#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
PDF Structure Extractor (PyMuPDF) - Better H1/H2 extraction + noise filtering + visual TOC detection

Usage:
  python extract_pdf_structure.py input.pdf > output.json
Optional:
  python extract_pdf_structure.py input.pdf --debug-sizes
"""

import sys, json, re, io, argparse, datetime
from collections import Counter
import fitz  # PyMuPDF

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8")
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding="utf-8")

# ---------------------------
# Heuristics / Filters
# ---------------------------

HEADING_PATTERNS = [
    r"^(\d+\.)+\s+\S+",                 # 1. or 1.1. Title
    r"^(\d+)\s+[A-ZÀ-Ü]",               # 1 Title
    r"^[IVXLCDM]+\.\s+\S+",             # Roman numerals I. Title
    r"^[A-Z]\.\s+\S+",                  # A. Title
    r"^(Chapter|Chapitre|Section|Partie|Part)\s+\d+",
    r"^(Introduction|Conclusion|Annexe|Appendix|Préface|Sommaire|Table des matières|Contents)",
]

NOISE_REGEX = [
    r"^\s*curl\b",
    r'^\s*-H\s+"[^"]+"\s*\\?\s*$',
    r"^\s*-H\s+'[^']+'\s*\\?\s*$",
    r"Authorization:\s*Bearer",
    r"Content-Type:\s*application\/json",
    r"^\s*GET\s+\/|^\s*POST\s+\/|^\s*PUT\s+\/|^\s*DELETE\s+\/",
    r"^\s*{\s*$|^\s*}\s*$",
    r"^\s*\[\s*$|^\s*\]\s*$",
]

TOC_KEYWORDS = ("table des matières", "sommaire", "contents", "content")

TOC_LINE_REGEXES = [
    # "Title ....... 12"
    re.compile(r"^(?P<title>.+?)\s*(\.{2,}\s*)?(?P<page>\d{1,4})\s*$"),
    # "Title 12" (no dots)
    re.compile(r"^(?P<title>.+?)\s+(?P<page>\d{1,4})\s*$"),
]

def normalize_text(t: str) -> str:
    t = t.replace("\u00A0", " ").strip()
    t = re.sub(r"\s+", " ", t)
    t = re.sub(r"^[•\-\–\—]\s*", "", t).strip()
    return t

def looks_like_heading(text: str) -> bool:
    s = text.strip()
    if not s:
        return False
    return any(re.match(p, s, re.IGNORECASE) for p in HEADING_PATTERNS)

def is_noise(text: str) -> bool:
    s = text.strip()
    if len(s) <= 2:
        return True
    if any(re.search(p, s, re.IGNORECASE) for p in NOISE_REGEX):
        return True
    if s.count("{") + s.count("}") >= 2:
        return True
    if re.match(r"^(Path|Query|Header)\s+Parameters?\s*:?", s, re.IGNORECASE):
        return True
    return False

def is_sentence_like(text: str) -> bool:
    if len(text) > 140:
        return True
    if text.endswith(".") and len(text) > 60:
        return True
    return False

def bold_from_span(span: dict) -> bool:
    font = (span.get("font") or "").lower()
    flags = span.get("flags", 0)
    return bool(flags & 16) or ("bold" in font) or ("black" in font) or ("semibold" in font)

# ---------------------------
# Visual TOC Detection
# ---------------------------

def infer_toc_level(title: str) -> int:
    """
    Heuristic:
    - "1." or "I." => level 1
    - "1.1" => level 2
    - indentation can also hint nesting
    """
    t = title.strip()
    if re.match(r"^\d+\.\d+(\.\d+)?\s+", t):
        return 2
    if re.match(r"^\d+\.\s+", t) or re.match(r"^[IVXLCDM]+\.\s+", t):
        return 1
    # fallback: try indentation (kept minimal since we normalized spaces)
    return 1

def detect_visual_toc(doc: fitz.Document, max_scan_pages: int = 8):
    """
    Find a TOC page by keyword, then parse TOC lines into items.
    Works best with dot leaders ".... 12" or "Title 12".
    """
    toc_items = []

    # scan first pages for "sommaire/table des matières/contents"
    toc_page_indexes = []
    for i in range(min(len(doc), max_scan_pages)):
        page_text = (doc[i].get_text("text") or "").lower()
        if any(k in page_text for k in TOC_KEYWORDS):
            toc_page_indexes.append(i)

    if not toc_page_indexes:
        return []

    # parse lines of each toc page found
    for i in toc_page_indexes:
        raw_lines = (doc[i].get_text("text") or "").splitlines()
        for line in raw_lines:
            line = normalize_text(line)
            if not line:
                continue
            low = line.lower()
            # ignore the toc title line itself
            if any(k == low for k in TOC_KEYWORDS):
                continue
            # ignore obvious noise
            if is_noise(line):
                continue

            m = None
            for rgx in TOC_LINE_REGEXES:
                m = rgx.match(line)
                if m:
                    break
            if not m:
                continue

            title = normalize_text(m.group("title"))
            page_str = m.group("page")

            # Basic sanity checks
            if not title or not page_str.isdigit():
                continue
            page_num = int(page_str)
            if page_num <= 0 or page_num > len(doc):
                continue
            # discard lines that look like paragraph sentences
            if is_sentence_like(title):
                continue

            toc_items.append({
                "level": infer_toc_level(title),
                "title": title,
                "page": page_num
            })

    # dedupe consecutive duplicates
    deduped = []
    seen = set()
    for it in toc_items:
        key = (it["title"].lower(), it["page"])
        if key in seen:
            continue
        seen.add(key)
        deduped.append(it)

    # keep only first ~200 items max
    return deduped[:200]

# ---------------------------
# Extraction core
# ---------------------------

def collect_lines(doc: fitz.Document, top_margin=0.08, bottom_margin=0.08):
    rows = []
    for page_index in range(len(doc)):
        page = doc[page_index]
        page_num = page_index + 1
        d = page.get_text("dict")
        height = page.rect.height

        for block in d.get("blocks", []):
            for line in block.get("lines", []):
                spans = sorted(line.get("spans", []), key=lambda s: s.get("bbox", [0, 0, 0, 0])[0])
                raw = " ".join(s.get("text", "").strip() for s in spans if s.get("text", "").strip())
                text = normalize_text(raw)
                if not text:
                    continue

                y0 = line.get("bbox", [0, 0, 0, 0])[1]
                y1 = line.get("bbox", [0, 0, 0, 0])[3]
                y_center = (y0 + y1) / 2.0

                if y_center < top_margin * height or y_center > (1.0 - bottom_margin) * height:
                    continue

                max_size = max((s.get("size", 0) for s in spans), default=0)
                avg_size = sum((s.get("size", 0) for s in spans)) / max(len(spans), 1)
                is_bold = any(bold_from_span(s) for s in spans)

                rows.append({
                    "text": text,
                    "page": page_num,
                    "max_size": float(max_size),
                    "avg_size": float(avg_size),
                    "bold": bool(is_bold),
                    "y": y_center
                })
    return rows

def infer_body_size(lines):
    candidates = []
    for r in lines:
        t = r["text"]
        if len(t) >= 35 and not looks_like_heading(t) and not is_noise(t) and not is_sentence_like(t):
            candidates.append(round(r["avg_size"] * 2) / 2)
    if not candidates:
        all_sizes = [round(r["avg_size"] * 2) / 2 for r in lines]
        return Counter(all_sizes).most_common(1)[0][0] if all_sizes else 12.0
    return Counter(candidates).most_common(1)[0][0]

def infer_heading_sizes(lines, body_size, min_freq=3):
    size_counts = Counter(round(r["max_size"] * 2) / 2 for r in lines if not is_noise(r["text"]))
    above = [(s, n) for s, n in size_counts.items() if s >= body_size + 1.0 and n >= min_freq]
    above.sort(key=lambda x: x[0], reverse=True)
    if not above:
        above = [(s, n) for s, n in size_counts.items() if s >= body_size + 1.0]
        above.sort(key=lambda x: x[0], reverse=True)

    sizes = [s for s, _ in above]
    h1 = sizes[0] if sizes else None
    h2 = sizes[1] if len(sizes) > 1 else None

    if h1 and size_counts[h1] <= 2 and h2 and size_counts[h2] >= 3:
        h1 = h2
        h2 = sizes[2] if len(sizes) > 2 else None

    return h1, h2, size_counts

def add_end_page_for_h1(headings, total_pages):
    # headings: list of {"level": int, "title": str, "page": int}
    h1 = [h for h in headings if h.get("level") == 1]
    h1 = sorted(h1, key=lambda x: x["page"])

    # map (title,page) -> end_page
    end_map = {}

    for i, cur in enumerate(h1):
        start = int(cur["page"])
        if i + 1 < len(h1):
            next_start = int(h1[i + 1]["page"])
            end = next_start - 1
        else:
            end = total_pages

        end = max(start, end)
        end_map[(cur["title"], start)] = end

    # inject end_page into headings for H1
    for h in headings:
        if h.get("level") == 1:
            key = (h["title"], int(h["page"]))
            h["end_page"] = int(end_map.get(key, total_pages))

    return headings

def merge_multiline_headings(candidates, y_gap=10):
    merged = []
    candidates = sorted(candidates, key=lambda r: (r["page"], r["y"]))
    i = 0
    while i < len(candidates):
        cur = candidates[i].copy()
        j = i + 1
        while j < len(candidates):
            nxt = candidates[j]
            if nxt["page"] != cur["page"]:
                break
            if abs(nxt["y"] - cur["y"]) > y_gap:
                break
            if nxt["level"] == cur["level"] and (nxt["bold"] or cur["bold"]):
                cur["title"] = normalize_text(cur["title"] + " " + nxt["title"])
                cur["y"] = (cur["y"] + nxt["y"]) / 2.0
                j += 1
                continue
            break
        merged.append(cur)
        i = j
    return merged

def extract_structure(pdf_path: str, top_margin=0.08, bottom_margin=0.08, debug_sizes=False):
    doc = fitz.open(pdf_path)

    result = {
        "title": doc.metadata.get("title", "") if doc.metadata else "",
        "total_pages": len(doc),
        "toc": [],
        "headings": [],
        "metadata": {k: doc.metadata.get(k, "") for k in ["title", "author", "creator", "creationDate"]} if doc.metadata else {},
        "source_file": pdf_path.split("/")[-1],
        "extracted_at": datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
    }

    # 1) Native TOC
    try:
        toc = doc.get_toc()
        for level, title, page in toc:
            title = normalize_text(title)
            if level <= 2 and title and not is_noise(title) and title.lower() not in ("table des matières", "sommaire", "contents"):
                result["toc"].append({"level": level, "title": title, "page": int(page)})
    except Exception:
        pass

    # 1b) Visual TOC fallback if empty
    if not result["toc"]:
        result["toc"] = detect_visual_toc(doc, max_scan_pages=10)

    # 2) Collect lines
    lines = collect_lines(doc, top_margin=top_margin, bottom_margin=bottom_margin)
    if not lines:
        doc.close()
        return result

    body_size = infer_body_size(lines)
    h1_size, h2_size, size_counts = infer_heading_sizes(lines, body_size)

    if debug_sizes:
        result["_debug"] = {
            "body_size": body_size,
            "h1_size": h1_size,
            "h2_size": h2_size,
            "size_counts_top": size_counts.most_common(10),
            "visual_toc_detected": bool(result["toc"])
        }

    seen = set()
    candidates = []

    for r in lines:
        text = r["text"]
        if is_noise(text) or is_sentence_like(text):
            continue

        size = round(r["max_size"] * 2) / 2
        bold = r["bold"]
        if len(text) < 3:
            continue

        key = (text.lower()[:80], r["page"])
        if key in seen:
            continue

        level = None
        if h1_size and size >= h1_size and (bold or looks_like_heading(text) or len(text) <= 80):
            level = 1
        elif h2_size and size >= h2_size and (bold or looks_like_heading(text)):
            level = 2
        else:
            if looks_like_heading(text) and (bold or size >= body_size + 0.5):
                level = 2

        if level and text.lower() in ("table des matières", "sommaire", "contents"):
            continue

        if level:
            seen.add(key)
            candidates.append({
                "level": level,
                "title": text,
                "page": r["page"],
                "bold": bold,
                "y": r["y"]
            })

    merged = merge_multiline_headings(candidates)

    seen_title = set()
    for h in merged:
        tkey = h["title"].lower()
        if tkey in seen_title:
            continue
        seen_title.add(tkey)
        result["headings"].append({
            "level": int(h["level"]),
            "title": h["title"],
            "page": int(h["page"])
        })

    result["headings"] = add_end_page_for_h1(result["headings"], result["total_pages"])

    # Title fallback
    if not result["title"]:
        if result["toc"]:
            result["title"] = result["toc"][0]["title"]
        else:
            h1_p1 = next((h for h in result["headings"] if h["level"] == 1 and h["page"] == 1), None)
            h2_p1 = next((h for h in result["headings"] if h["level"] == 2 and h["page"] == 1), None)
            result["title"] = (h1_p1["title"] if h1_p1 else (h2_p1["title"] if h2_p1 else ""))

    doc.close()
    return result

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("pdf_path", help="Path to PDF file")
    ap.add_argument("--top-margin", type=float, default=0.08, help="Header exclusion ratio (default 0.08)")
    ap.add_argument("--bottom-margin", type=float, default=0.08, help="Footer exclusion ratio (default 0.08)")
    ap.add_argument("--debug-sizes", action="store_true", help="Include debug info about detected font sizes")
    args = ap.parse_args()

    try:
        data = extract_structure(
            args.pdf_path,
            top_margin=args.top_margin,
            bottom_margin=args.bottom_margin,
            debug_sizes=args.debug_sizes,
        )
        print(json.dumps(data, ensure_ascii=False, indent=2))
    except FileNotFoundError:
        print(json.dumps({"error": f"File not found: {args.pdf_path}"}))
        sys.exit(1)
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()