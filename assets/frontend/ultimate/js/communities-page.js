document.addEventListener("DOMContentLoaded", () => {
  const pageRoot = document.getElementById("communitiesPage");
  const form = document.getElementById("searchForm");
  const searchInput = document.getElementById("searchInput");
  const clearButton = document.getElementById("clearSearch");
  const catSelect = document.getElementById("catSelect");
  const container = document.getElementById("communitiesContainer");

  if (!form || !searchInput || !catSelect || !container) {
    return;
  }

  const ds = pageRoot?.dataset || {};
  const paginationNavLabel = (ds.paginationNavLabel || "").trim();
  const paginationPagePrefix = (ds.paginationPagePrefix || "").trim();

  const searchButton = form.querySelector("button[type='submit']");
  const STORAGE_KEY = "communities_page_state";
  let activeRequestController = null;
  let latestRequestId = 0;

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function emptyResultsHtml() {
    const t = (ds.noResults || "").trim();
    const inner = t ? `<p class="text-muted">${escapeHtml(t)}</p>` : `<p class="text-muted"></p>`;
    return `<div class="empty-state">${inner}</div>`;
  }

  function loadingErrorHtml() {
    const t = (ds.loadingError || "").trim();
    const inner = t ? `<p class="text-danger">${escapeHtml(t)}</p>` : `<p class="text-danger"></p>`;
    return `<div class="empty-state">${inner}</div>`;
  }

  function getSearchActionHref() {
    try {
      return new URL(form.getAttribute("action") || "", window.location.origin).href;
    } catch (_) {
      return window.location.href;
    }
  }

  function isSearchEndpoint(absoluteUrl) {
    try {
      const u = new URL(absoluteUrl, window.location.origin);
      const b = new URL(form.getAttribute("action") || "", window.location.origin);
      return u.pathname === b.pathname;
    } catch (_) {
      return false;
    }
  }

  function normalizeFetchUrl(url) {
    if (!url) return getSearchActionHref();
    if (url.startsWith("http://") || url.startsWith("https://")) return url;
    if (url.startsWith("/")) return `${window.location.origin}${url}`;
    return `${window.location.origin}/${String(url).replace(/^\/+/, "")}`;
  }

  function createCustomDropdown(selectElement) {
    const filterSelect = selectElement.closest(".filter-select");
    if (!filterSelect) return;

    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const selectedText = selectedOption ? selectedOption.textContent : "";
    const baseId = selectElement.id || "communities-category";
    const dropdownId = `${baseId}-listbox`;
    const buttonId = `${baseId}-combobox`;
    let activeOptionIndex = selectElement.selectedIndex >= 0 ? selectElement.selectedIndex : 0;

    const customButton = document.createElement("div");
    customButton.className = "custom-select-button";
    customButton.innerHTML = `
      <span class="selected-text">${selectedText}</span>
      <i class="fa-solid fa-chevron-down icon-right"></i>
    `;
    customButton.id = buttonId;
    customButton.setAttribute("role", "combobox");
    customButton.setAttribute("aria-haspopup", "listbox");
    customButton.setAttribute("aria-expanded", "false");
    customButton.setAttribute("aria-controls", dropdownId);
    customButton.setAttribute("aria-label", selectElement.getAttribute("aria-label") || "Select category");
    customButton.tabIndex = 0;

    const customDropdown = document.createElement("div");
    customDropdown.className = "custom-dropdown";
    customDropdown.id = dropdownId;
    customDropdown.setAttribute("role", "listbox");
    customDropdown.setAttribute("aria-labelledby", buttonId);
    customDropdown.tabIndex = -1;

    const customOptions = [];

    const closeDropdown = () => {
      customButton.classList.remove("active");
      customDropdown.classList.remove("active");
      customButton.setAttribute("aria-expanded", "false");
    };

    const closeAllDropdowns = () => {
      document.querySelectorAll(".custom-select-button").forEach((btn) => {
        btn.classList.remove("active");
        btn.setAttribute("aria-expanded", "false");
      });
      document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
        dropdown.classList.remove("active");
      });
    };

    const setActiveOption = (index, focusOption = false) => {
      if (!customOptions.length) return;
      const boundedIndex = Math.max(0, Math.min(index, customOptions.length - 1));
      activeOptionIndex = boundedIndex;

      customOptions.forEach((opt, optIndex) => {
        opt.classList.toggle("is-active", optIndex === boundedIndex);
      });
      customButton.setAttribute("aria-activedescendant", customOptions[boundedIndex].id);
      if (focusOption) {
        customOptions[boundedIndex].focus();
      }
    };

    const openDropdown = (focusOption = false) => {
      closeAllDropdowns();
      customButton.classList.add("active");
      customDropdown.classList.add("active");
      customButton.setAttribute("aria-expanded", "true");
      setActiveOption(activeOptionIndex, focusOption);
    };

    const selectOptionAt = (index, dispatchChange = true) => {
      if (!customOptions[index]) return;
      const option = selectElement.options[index];
      if (!option) return;

      selectElement.selectedIndex = index;
      if (dispatchChange) {
        selectElement.dispatchEvent(new Event("change", { bubbles: true }));
      }
      customButton.querySelector(".selected-text").textContent = option.textContent;
      customOptions.forEach((opt, optIndex) => {
        const isSelected = optIndex === index;
        opt.classList.toggle("selected", isSelected);
        opt.setAttribute("aria-selected", String(isSelected));
      });
      activeOptionIndex = index;
      customButton.setAttribute("aria-activedescendant", customOptions[index].id);
    };

    Array.from(selectElement.options).forEach((option, index) => {
      const customOption = document.createElement("div");
      customOption.className = "custom-option";
      if (index === selectElement.selectedIndex) customOption.classList.add("selected");
      customOption.textContent = option.textContent;
      customOption.dataset.value = option.value;
      customOption.dataset.index = String(index);
      customOption.id = `${dropdownId}-option-${index}`;
      customOption.setAttribute("role", "option");
      customOption.setAttribute("aria-selected", String(index === selectElement.selectedIndex));
      customOption.tabIndex = -1;

      customOption.addEventListener("click", () => {
        selectOptionAt(index, true);
        closeDropdown();
        customButton.focus();
      });

      customOption.addEventListener("keydown", (event) => {
        if (event.key === "ArrowDown") {
          event.preventDefault();
          setActiveOption(activeOptionIndex + 1, true);
        } else if (event.key === "ArrowUp") {
          event.preventDefault();
          setActiveOption(activeOptionIndex - 1, true);
        } else if (event.key === "Home") {
          event.preventDefault();
          setActiveOption(0, true);
        } else if (event.key === "End") {
          event.preventDefault();
          setActiveOption(customOptions.length - 1, true);
        } else if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          selectOptionAt(activeOptionIndex, true);
          closeDropdown();
          customButton.focus();
        } else if (event.key === "Escape") {
          event.preventDefault();
          closeDropdown();
          customButton.focus();
        } else if (event.key === "Tab") {
          closeDropdown();
        }
      });

      customOptions.push(customOption);
      customDropdown.appendChild(customOption);
    });

    customButton.addEventListener("click", (e) => {
      e.stopPropagation();
      if (customButton.classList.contains("active")) {
        closeDropdown();
      } else {
        openDropdown(false);
      }
    });

    customButton.addEventListener("keydown", (event) => {
      if (event.key === "ArrowDown") {
        event.preventDefault();
        openDropdown(true);
      } else if (event.key === "ArrowUp") {
        event.preventDefault();
        openDropdown(true);
        setActiveOption(activeOptionIndex - 1, true);
      } else if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        if (customButton.classList.contains("active")) {
          selectOptionAt(activeOptionIndex, true);
          closeDropdown();
        } else {
          openDropdown(true);
        }
      } else if (event.key === "Escape") {
        event.preventDefault();
        closeDropdown();
      } else if (event.key === "Tab") {
        closeDropdown();
      }
    });

    document.addEventListener("click", (e) => {
      if (!filterSelect.contains(e.target)) {
        closeDropdown();
      }
    });

    const iconLeft = filterSelect.querySelector(".icon-left");
    const iconRight = filterSelect.querySelector(".icon-right");
    selectElement.insertAdjacentElement("afterend", customButton);
    customButton.insertAdjacentElement("afterend", customDropdown);

    if (iconLeft) {
      const clonedIconLeft = iconLeft.cloneNode(true);
      clonedIconLeft.style.position = "static";
      clonedIconLeft.style.transform = "none";
      clonedIconLeft.style.top = "auto";
      clonedIconLeft.style.left = "auto";
      customButton.insertBefore(clonedIconLeft, customButton.querySelector(".selected-text"));
      iconLeft.style.display = "none";
    }
    if (iconRight) {
      iconRight.style.display = "none";
    }

    selectOptionAt(activeOptionIndex, false);
  }

  function updateClearButton() {
    if (!clearButton) return;
    clearButton.classList.toggle("visible", searchInput.value.trim().length > 0);
  }

  function savePageState(url = null) {
    const state = {
      url: url || window.location.href,
      search: searchInput.value.trim(),
      category: catSelect.value,
      page: extractPageNumber(url || window.location.href),
      timestamp: Date.now(),
    };
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  }

  function loadPageState() {
    try {
      const saved = sessionStorage.getItem(STORAGE_KEY);
      if (saved) return JSON.parse(saved);
    } catch (e) {
      console.error("Error loading page state:", e);
    }
    return null;
  }

  function extractPageNumber(url) {
    if (!url) return 1;
    try {
      const parsedUrl = new URL(url, window.location.origin);
      const pageFromQuery = parseInt(parsedUrl.searchParams.get("page"), 10);
      if (!Number.isNaN(pageFromQuery) && pageFromQuery > 0) {
        return pageFromQuery;
      }

      const urlParts = parsedUrl.pathname.split("/");
      for (let i = urlParts.length - 1; i >= 0; i--) {
        const part = urlParts[i];
        if (!Number.isNaN(Number(part)) && part !== "") {
          return parseInt(part, 10);
        }
      }
    } catch (_) {}
    return 1;
  }

  function replaceHistoryUrl(absoluteUrl) {
    try {
      const parsedUrl = new URL(absoluteUrl, window.location.origin);
      if (window.history.replaceState) {
        window.history.replaceState(
          { communitiesUrl: parsedUrl.href },
          "",
          `${parsedUrl.pathname}${parsedUrl.search}`
        );
      }
    } catch (_) {
      if (window.history.replaceState) {
        window.history.replaceState(
          { communitiesUrl: absoluteUrl },
          "",
          `${window.location.pathname}${window.location.search}`
        );
      }
    }
  }

  function getPaginationElement() {
    return document.getElementById("pagination");
  }

  function applyPaginationAccessibility() {
    const pagination = getPaginationElement();
    if (!pagination) return;
    const nav = pagination.querySelector("nav");
    if (nav && paginationNavLabel) {
      nav.setAttribute("aria-label", paginationNavLabel);
    }

    pagination.querySelectorAll(".page-link").forEach((link) => {
      const label = link.textContent.trim();
      if (paginationPagePrefix && /^\d+$/.test(label)) {
        link.setAttribute("aria-label", `${paginationPagePrefix} ${label}`);
      }
    });
  }

  function updateActivePagination(url) {
    const pagination = getPaginationElement();
    if (!pagination) return;
    const currentPage = extractPageNumber(url);

    pagination.querySelectorAll(".page-item").forEach((item) => item.classList.remove("active"));
    pagination.querySelectorAll(".page-link").forEach((link) => {
      link.removeAttribute("aria-current");
      const pageText = link.textContent.trim();
      if (!Number.isNaN(parseInt(pageText, 10)) && parseInt(pageText, 10) === currentPage) {
        link.closest(".page-item")?.classList.add("active");
        link.setAttribute("aria-current", "page");
      }
    });
  }

  function truncateDescriptions() {
    const descriptions = document.querySelectorAll(".community-card .card-description");
    descriptions.forEach((desc) => {
      if (!desc.dataset.originalText) desc.dataset.originalText = desc.textContent;
      const originalText = desc.dataset.originalText;
      const lineHeight = parseFloat(window.getComputedStyle(desc).lineHeight);
      const maxHeight = lineHeight * 2;
      desc.textContent = originalText;

      if (desc.scrollHeight <= maxHeight) return;
      const words = originalText.split(/\s+/).filter((w) => w.length > 0);

      if (words.length === 0 || (words.length === 1 && words[0].length > 50)) {
        let testText = originalText;
        while (desc.scrollHeight > maxHeight && testText.length > 0) {
          testText = testText.slice(0, -1);
          desc.textContent = `${testText}...`;
        }
        return;
      }

      let low = 0;
      let high = words.length;
      let bestFit = words.length;
      while (low <= high) {
        const mid = Math.floor((low + high) / 2);
        const testText = words.slice(0, mid).join(" ") + (mid < words.length ? "..." : "");
        desc.textContent = testText;
        if (desc.scrollHeight <= maxHeight) {
          bestFit = mid;
          low = mid + 1;
        } else {
          high = mid - 1;
        }
      }
      desc.textContent = bestFit < words.length ? `${words.slice(0, bestFit).join(" ")}...` : originalText;
    });
  }

  const loadingSkeleton = `
    <div id="cardsGrid" class="row g-4">
      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
        <div class="loading-card"><div class="shimmer" style="height:180px"></div><div style="padding:1.5rem"><div class="shimmer" style="height:20px;width:70%;border-radius:8px;margin-bottom:10px"></div><div class="shimmer" style="height:14px;width:100%;border-radius:6px;margin-bottom:6px"></div><div class="shimmer" style="height:14px;width:80%;border-radius:6px;margin-bottom:20px"></div><div class="shimmer" style="height:44px;border-radius:12px"></div></div></div>
      </div>
      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
        <div class="loading-card"><div class="shimmer" style="height:180px"></div><div style="padding:1.5rem"><div class="shimmer" style="height:20px;width:70%;border-radius:8px;margin-bottom:10px"></div><div class="shimmer" style="height:14px;width:100%;border-radius:6px;margin-bottom:6px"></div><div class="shimmer" style="height:14px;width:80%;border-radius:6px;margin-bottom:20px"></div><div class="shimmer" style="height:44px;border-radius:12px"></div></div></div>
      </div>
      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 d-none d-lg-block">
        <div class="loading-card"><div class="shimmer" style="height:180px"></div><div style="padding:1.5rem"><div class="shimmer" style="height:20px;width:70%;border-radius:8px;margin-bottom:10px"></div><div class="shimmer" style="height:14px;width:100%;border-radius:6px;margin-bottom:6px"></div><div class="shimmer" style="height:14px;width:80%;border-radius:6px;margin-bottom:20px"></div><div class="shimmer" style="height:44px;border-radius:12px"></div></div></div>
      </div>
      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 d-none d-xxl-block">
        <div class="loading-card"><div class="shimmer" style="height:180px"></div><div style="padding:1.5rem"><div class="shimmer" style="height:20px;width:70%;border-radius:8px;margin-bottom:10px"></div><div class="shimmer" style="height:14px;width:100%;border-radius:6px;margin-bottom:6px"></div><div class="shimmer" style="height:14px;width:80%;border-radius:6px;margin-bottom:20px"></div><div class="shimmer" style="height:44px;border-radius:12px"></div></div></div>
      </div>
    </div>
    <div id="pagination" class="pagination-wrapper"></div>
  `;

  function sendAjax(pageUrl = null, shouldScroll = false) {
    const searchValue = searchInput.value.trim();
    let url = pageUrl ? normalizeFetchUrl(pageUrl) : getSearchActionHref();
    const requestId = ++latestRequestId;

    if (searchValue && isSearchEndpoint(url)) {
      const sep = url.includes("?") ? "&" : "?";
      url += `${sep}search=${encodeURIComponent(searchValue)}`;
    } else if (searchValue && !isSearchEndpoint(url)) {
      const base = getSearchActionHref();
      url = `${base}${base.includes("?") ? "&" : "?"}search=${encodeURIComponent(searchValue)}`;
    }

    savePageState(url);
    container.innerHTML = loadingSkeleton;

    if (activeRequestController) {
      activeRequestController.abort();
    }
    activeRequestController = new AbortController();

    if (shouldScroll) {
      const communitiesSection = document.querySelector(".communities-section");
      if (communitiesSection) {
        const navbarHeight = 190;
        const elementPosition = communitiesSection.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - navbarHeight;
        window.scrollTo({ top: offsetPosition, behavior: "smooth" });
      } else {
        container.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }

    fetch(url, {
      headers: { "X-Requested-With": "XMLHttpRequest", Accept: "text/html" },
      credentials: "same-origin",
      signal: activeRequestController.signal,
    })
      .then((res) => {
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return res.text();
      })
      .then((html) => {
        if (requestId !== latestRequestId) {
          return;
        }
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");
        let newContent = doc.querySelector("#communitiesContainer");

        if (newContent && newContent.innerHTML.trim() !== "") {
          container.innerHTML = newContent.innerHTML;
        } else {
          const resultsHeader = doc.querySelector(".results-header");
          const cardsGrid = doc.querySelector("#cardsGrid");
          const pagination = doc.querySelector("#pagination");
          const emptyState = doc.querySelector(".empty-state");

          let partialContent = "";
          if (resultsHeader) partialContent += resultsHeader.outerHTML;
          if (cardsGrid) {
            partialContent += cardsGrid.outerHTML;
          } else if (emptyState) {
            partialContent += `<div id="cardsGrid" class="row g-4"><div class="col-12">${emptyState.outerHTML}</div></div>`;
          }
          if (pagination) partialContent += pagination.outerHTML;

          if (partialContent.trim() !== "") {
            container.innerHTML = partialContent;
          } else {
            const hasCards = doc.querySelectorAll(".community-card").length > 0;
            if (hasCards) {
              const allCards = doc.querySelectorAll(".community-card");
              let cardsHTML = '<div id="cardsGrid" class="row g-4">';
              allCards.forEach((card) => {
                cardsHTML += `<div class="col-12 col-sm-6 col-lg-4 col-xxl-3">${card.outerHTML}</div>`;
              });
              cardsHTML += "</div>";
              if (pagination) cardsHTML += pagination.outerHTML;
              container.innerHTML = cardsHTML;
            } else {
              container.innerHTML = emptyResultsHtml();
            }
          }
        }

        if (pageUrl) {
          replaceHistoryUrl(url);
        }

        updateActivePagination(url);
        applyPaginationAccessibility();
        setTimeout(truncateDescriptions, 100);
      })
      .catch((err) => {
        if (err && err.name === "AbortError") {
          return;
        }
        if (requestId !== latestRequestId) {
          return;
        }
        console.error("Erreur AJAX :", err);
        container.innerHTML = loadingErrorHtml();
      });
  }

  function restorePageState() {
    const state = loadPageState();
    if (!state) return;
    const navEntries = performance.getEntriesByType("navigation");
    const isBackNavigation = navEntries.length > 0 && navEntries[0].type === "back_forward";
    const referrer = document.referrer;
    const isFromDetail = referrer.includes("community_details") || referrer.includes("/community/");
    if ((isBackNavigation || isFromDetail) && state.page > 1) {
      if (state.search) {
        searchInput.value = state.search;
        updateClearButton();
      }
      if (state.url) sendAjax(state.url, false);
    }
  }

  createCustomDropdown(catSelect);
  truncateDescriptions();
  updateClearButton();
  updateActivePagination(window.location.href);
  applyPaginationAccessibility();
  restorePageState();
  savePageState();

  searchInput.addEventListener("input", updateClearButton);
  if (clearButton) {
    clearButton.addEventListener("click", () => {
      searchInput.value = "";
      updateClearButton();
      searchInput.focus();
    });
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    sendAjax();
  });

  if (searchButton) {
    searchButton.addEventListener("click", (e) => {
      e.preventDefault();
      sendAjax();
    });
  }

  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      sendAjax();
    }
  });

  catSelect.addEventListener("change", (e) => {
    e.preventDefault();
    sendAjax(catSelect.value, true);
  });

  container.addEventListener("click", (e) => {
    const link = e.target.closest("#pagination a");
    if (!link) return;
    e.preventDefault();
    if (link.href) sendAjax(link.href, true);
  });
});
