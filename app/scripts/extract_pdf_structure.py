#!/usr/bin/env python3
import sys
import json
try:
    import fitz  # PyMuPDF
except ImportError:
    print(json.dumps({"error": "PyMuPDF not installed. Run: pip install PyMuPDF"}))
    sys.exit(1)

def extract_pdf_structure(pdf_path):
    try:
        doc = fitz.open(pdf_path)
        
        structure = {
            "title": "",
            "chapters": [],
            "total_pages": len(doc)
        }
        
        for page in doc:
            blocks = page.get_text("blocks")
            for block in blocks:
                if block[6] == 0:
                    text = block[4].strip()
                    
                    if len(text) < 5:
                        continue
                    
                    try:
                        spans = page.search_for(text)
                        if len(spans) > 0:
                            rect = spans[0]
                            font_size = rect.y1 - rect.y0
                        else:
                            font_size = 12
                    except:
                        font_size = 12
                        
                    if font_size >= 18:
                        structure["title"] = text
                    elif font_size >= 14:
                        structure["chapters"].append({
                            "title": text,
                            "page": page.number + 1,
                            "type": "chapter"
                        })
                    elif font_size >= 12:
                        structure["chapters"].append({
                            "title": text,
                            "page": page.number + 1,
                            "type": "section"
                        })
        
        doc.close()
        return structure
        
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    if len(sys.argv) < 2:
        sys.stdout.buffer.write(json.dumps({"error": "No PDF path provided"}).encode('utf-8'))
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    result = extract_pdf_structure(pdf_path)
    json_output = json.dumps(result, ensure_ascii=False, indent=2)
    sys.stdout.buffer.write(json_output.encode('utf-8'))
