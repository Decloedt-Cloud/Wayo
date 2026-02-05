<style>
/* ================= COMMUNITIES PAGE REDESIGN ================= */

/* Hero Section */
.communities-hero {
    position: relative;
    background-image: url("<?php echo base_url('uploads/images/decloedt/img/optimized/bg-communities.avif'); ?>"), url("<?php echo base_url('uploads/images/decloedt/img/optimized/bg-communities.webp'); ?>");
    background-size: 100% auto;
    background-position: center -119px;
    background-repeat: no-repeat;
    padding: 5rem 0 6rem;
    min-height: 472px;
    overflow: visible;
}

.communities-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(26, 26, 46, 0.6);
    z-index: 0;
}

.communities-hero::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
    z-index: 0;
}

.communities-hero .hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
}

.communities-hero .filter-wrapper {
    margin-top: 2.5rem;
    position: relative;
    z-index: 2;
}

/* Search box styling in hero */
.communities-hero .search-box input {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-color: rgba(255, 255, 255, 0.3);
}

.communities-hero .search-box input:focus {
    background: #fff;
    border-color: #FC7B30;
    box-shadow: 0 0 0 4px rgba(252, 123, 48, 0.15);
}

/* Ensure search icon is visible in hero */
.communities-hero .search-box i.fa-magnifying-glass {
    color: #666 !important;
    z-index: 10;
}

.communities-hero .search-box .clear-btn i {
    color: #333 !important;
}

.communities-hero .filter-select .custom-select-button {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-color: rgba(255, 255, 255, 0.3);
}

.communities-hero .filter-select .custom-select-button:hover {
    background: #fff;
    border-color: rgba(255, 255, 255, 0.5);
}

.communities-hero .filter-select .custom-select-button.active {
    background: #fff;
    border-color: #FC7B30;
}

.communities-hero h1 {
    font-size: clamp(2rem, 5vw, 3.2rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 1rem;
    letter-spacing: -0.5px;
}

.communities-hero h1 span {
    background: linear-gradient(135deg, #FC7B30, #ff9a56);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.communities-hero p {
    font-size: 1.15rem;
    color: rgba(255, 255, 255, 0.8);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.7;
}

/* Stats badges in hero */
.hero-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 2.5rem;
    flex-wrap: wrap;
}

.hero-stat {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1.25rem;
    border-radius: 50px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.hero-stat i {
    color: #FC7B30;
    font-size: 1.1rem;
}

.hero-stat span {
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
}

/* Search & Filter Section - Now inside hero */
.filter-wrapper {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

.search-box {
    flex: 1;
    min-width: 280px;
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 0.9rem 1.25rem 0.9rem 3rem;
    border: 2px solid #eee;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
    color: #333;
}

.search-box input::placeholder {
    color: #999;
}

.search-box input:focus {
    outline: none;
    border-color: #FC7B30;
    background: #fff;
    color: #333;
    box-shadow: 0 0 0 4px rgba(252, 123, 48, 0.1);
}

.search-box i {
    position: absolute;
    left: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 1rem;
}

.search-box button {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #FC7B30, #ff9a56);
    border: none;
    color: #fff;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(252, 123, 48, 0.3);
}

.search-box button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    pointer-events: none;
}

.search-box button:hover::before {
    left: 100%;
}

.search-box button i {
    color: #fff;
    font-size: 0.9rem;
    line-height: 1;
    margin: 0;
    padding: 0;
}

.search-box button:hover {
    transform: translateY(-50%) scale(1.05);
    box-shadow: 0 6px 18px rgba(252, 123, 48, 0.5);
}

/* Clear button */
.search-box .clear-btn {
    position: absolute;
    right: 3.2rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #333;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    padding: 0;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    box-shadow: none;
}

.search-box .clear-btn i {
    color: #333 !important;
    font-size: 0.9rem;
    line-height: 1;
}

.search-box .clear-btn:hover {
    transform: translateY(-50%);
    box-shadow: none;
    color: #000;
}

.search-box .clear-btn:hover i {
    color: #000 !important;
}

.search-box .clear-btn.visible {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.filter-select {
    position: relative;
    min-width: 180px;
}

.filter-select select {
    position: absolute;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
}

/* Custom dropdown button */
.filter-select .custom-select-button {
    width: 100%;
    padding: 0.9rem 2.5rem 0.9rem 2.8rem;
    border: 2px solid #eee;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 500;
    background: #f8f9fa;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    user-select: none;
}

.filter-select .custom-select-button .icon-left {
    position: static !important;
    color: #FC7B30;
    font-size: 0.95rem;
    flex-shrink: 0;
    transform: none !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    margin: 0;
}

.filter-select .custom-select-button i.icon-right {
    position: static !important;
    transform: none;
    top: auto !important;
    right: auto !important;
    left: auto !important;
    margin: 0;
    flex-shrink: 0;
}

.filter-select .custom-select-button:hover {
    border-color: #ddd;
    background: #fff;
}

.filter-select .custom-select-button.active {
    border-color: #FC7B30;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(252, 123, 48, 0.1), 0 4px 12px rgba(0, 0, 0, 0.08);
}

.filter-select .custom-select-button .selected-text {
    flex: 1;
    text-align: left;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.filter-select .custom-select-button.active i.icon-right {
    transform: rotate(180deg) !important;
}

/* Custom dropdown menu */
.filter-select .custom-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: #fff;
    border: 2px solid #eee;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Ensure dropdown appears above hero overlay */
.communities-hero .filter-select .custom-dropdown {
    z-index: 1001;
}

.filter-select .custom-dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.filter-select .custom-dropdown::-webkit-scrollbar {
    width: 6px;
}

.filter-select .custom-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.filter-select .custom-dropdown::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

.filter-select .custom-dropdown::-webkit-scrollbar-thumb:hover {
    background: #bbb;
}

/* Dropdown options */
.filter-select .custom-option {
    padding: 0.85rem 1.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #333;
    font-weight: 500;
    font-size: 0.95rem;
    border-bottom: 1px solid #f5f5f5;
    display: block;
    text-decoration: none;
}

.filter-select .custom-option:first-child {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.filter-select .custom-option:last-child {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    border-bottom: none;
}

.filter-select .custom-option:hover {
    background: linear-gradient(135deg, rgba(252, 123, 48, 0.1), rgba(255, 154, 86, 0.1));
    color: #FC7B30;
    padding-left: 1.5rem;
}

.filter-select .custom-option.selected {
    background: linear-gradient(135deg, #FC7B30, #ff9a56);
    color: #fff;
    font-weight: 600;
}

.filter-select .custom-option.selected:hover {
    background: linear-gradient(135deg, #e56a1f, #FC7B30);
    padding-left: 1.25rem;
}

.filter-select i.icon-left {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #FC7B30;
    font-size: 0.95rem;
    pointer-events: none;
}

.filter-select i.icon-right {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 0.8rem;
    pointer-events: none;
}

/* Communities Grid Section */
.communities-section {
    background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
    padding: 1rem 0 4rem;
    min-height: 60vh;
}

/* Results count */
.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.results-count {
    font-size: 1rem;
    color: #666;
}

.results-count strong {
    color: #1a1a2e;
    font-weight: 700;
}

/* Community Cards */
.community-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.community-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.community-card .card-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.community-card .card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: auto;
    -ms-interpolation-mode: bicubic;
    backface-visibility: hidden;
    transform: translateZ(0);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.community-card:hover .card-image img {
    transform: scale(1.08);
}

.community-card .card-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.4rem 0.9rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.community-card .card-badge.public {
    background: linear-gradient(135deg, #FC7B30, #ff9a56);
    color: #fff;
}

.community-card .card-badge.private {
    background: linear-gradient(135deg, #FC7B30, #ff9a56);
    color: #fff;
}

.community-card .card-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.community-card .card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.community-card .card-description {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 1.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-grow: 1;
    word-wrap: break-word;
    max-height: calc(0.9rem * 1.6 * 2);
    min-height: calc(0.9rem * 1.6 * 2);
}

.community-card .card-stats {
    display: flex;
    gap: 1.25rem;
    padding-top: 1rem;
    border-top: 1px solid #f1f1f1;
    margin-bottom: 1.25rem;
}

.community-card .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #666;
}

.community-card .stat-item i {
    color: #FC7B30;
    font-size: 0.9rem;
}

.community-card .stat-item strong {
    color: #1a1a2e;
    font-weight: 700;
}

.community-card .card-action {
    margin-top: auto;
}

.community-card .btn-details {
    display: block;
    width: 100%;
    padding: 0.9rem;
    background: linear-gradient(135deg, #FC7B30, #ff9a56);
    color: #fff;
    text-align: center;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(252, 123, 48, 0.3);
}

.community-card .btn-details::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.community-card .btn-details:hover::before {
    left: 100%;
}

.community-card .btn-details:hover {
    background: linear-gradient(135deg, #e56a1f, #FC7B30);
    box-shadow: 0 12px 28px rgba(252, 123, 48, 0.4);
    transform: translateY(-3px) scale(1.02);
    color: #fff;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state img {
    max-width: 150px;
    opacity: 0.6;
    margin-bottom: 1.5rem;
}

.empty-state p {
    color: #888;
    font-size: 1.1rem;
}

/* CTA Section */
.cta-section {
    background: #fff;
    padding: 4rem 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.cta-card {
    background: linear-gradient(135deg, #FC7B30 0%, #ff9a56 100%);
    border-radius: 24px;
    padding: 3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 2rem;
    position: relative;
    overflow: hidden;
}

.cta-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.cta-content {
    position: relative;
    z-index: 1;
    flex: 1;
    min-width: 280px;
}

.cta-content h2 {
    color: #fff;
    font-size: 1.75rem;
    font-weight: 800;
    margin-bottom: 0.75rem;
}

.cta-content p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.05rem;
    margin: 0;
    max-width: 500px;
}

.cta-btn {
    position: relative;
    z-index: 1;
    background: #fff;
    color: #FC7B30;
    padding: 1rem 2rem;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.cta-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    pointer-events: none;
    z-index: 1;
}

.cta-btn:hover::before {
    left: 100%;
}

.cta-btn:hover {
    background: linear-gradient(135deg, #e56a1f, #FC7B30);
    color: #fff;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 28px rgba(252, 123, 48, 0.4);
}

/* Pagination */
.pagination-wrapper {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    gap: 0.5rem;
}

.pagination-wrapper .page-link {
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    color: #666;
    font-weight: 600;
    transition: all 0.3s ease;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
}

.pagination-wrapper .page-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    pointer-events: none;
    z-index: 1;
}

.pagination-wrapper .page-link:hover::before {
    left: 100%;
}

.pagination-wrapper .page-link:hover {
    background: #FC7B30;
    color: #fff;
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #FC7B30, #ff9a56);
    color: #fff;
    box-shadow: 0 4px 12px rgba(252, 123, 48, 0.4);
    position: relative;
    overflow: hidden;
}

.pagination-wrapper .page-item.active .page-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    pointer-events: none;
    z-index: 1;
}

.pagination-wrapper .page-item.active .page-link:hover::before {
    left: 100%;
}

/* Loading State */
.loading-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    height: 380px;
}

.loading-card .shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* RTL Support */
[dir="rtl"] .search-box i {
    left: auto;
    right: 1rem;
}

[dir="rtl"] .search-box input {
    padding: 0.9rem 3rem 0.9rem 4rem;
}

[dir="rtl"] .search-box button {
    right: auto;
    left: 0.5rem;
}

[dir="rtl"] .search-box .clear-btn {
    right: auto;
    left: 3.2rem;
}

[dir="rtl"] .filter-select i.icon-left {
    left: auto;
    right: 1rem;
}

[dir="rtl"] .filter-select i.icon-right {
    right: auto;
    left: 1rem;
}

[dir="rtl"] .filter-select select {
    padding: 0.9rem 2.8rem 0.9rem 2.5rem;
}

[dir="rtl"] .filter-select .custom-select-button {
    padding: 0.9rem 2.8rem 0.9rem 2.5rem;
}

[dir="rtl"] .filter-select .custom-select-button .selected-text {
    text-align: right;
}

[dir="rtl"] .filter-select .custom-option {
    text-align: right;
}

[dir="rtl"] .filter-select .custom-option:hover {
    padding-right: 1.5rem;
    padding-left: 1.25rem;
}

[dir="rtl"] .filter-select .custom-option.selected:hover {
    padding-right: 1.25rem;
    padding-left: 1.25rem;
}

[dir="rtl"] .community-card .card-badge {
    right: auto;
    left: 1rem;
}

/* Global Shiny Effect for All Buttons */
.communities-section .btn,
.communities-section button:not(.clear-btn):not(.custom-select-button),
.cta-section .btn,
.cta-section button {
    position: relative;
    overflow: hidden;
}

.communities-section .btn:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join)::before,
.communities-section button:not(.clear-btn):not(.custom-select-button):not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join)::before,
.cta-section .btn:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join)::before,
.cta-section button:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join)::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    pointer-events: none;
    z-index: 1;
}

.communities-section .btn:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):hover::before,
.communities-section button:not(.clear-btn):not(.custom-select-button):not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):hover::before,
.cta-section .btn:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):hover::before,
.cta-section button:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):hover::before {
    left: 100%;
}

/* Shiny effect for outline buttons */
.communities-section .btn-outline-wayo::after,
.communities-section .btn-outline-wayo-join::after,
.cta-section .btn-outline-wayo::after,
.cta-section .btn-outline-wayo-join::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s ease;
    z-index: 1;
    pointer-events: none;
}

.communities-section .btn-outline-wayo:hover::after,
.communities-section .btn-outline-wayo-join:hover::after,
.cta-section .btn-outline-wayo:hover::after,
.cta-section .btn-outline-wayo-join:hover::after {
    left: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .communities-hero {
        padding: 3rem 0 4rem;
    }
    
    .hero-stats {
        gap: 1rem;
    }
    
    .hero-stat {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
    }
    
    .communities-hero .filter-wrapper {
        flex-direction: column;
        margin-top: 2rem;
    }
    
    .search-box, .filter-select {
        width: 100%;
        min-width: unset;
    }
    
    .cta-card {
        padding: 2rem;
        text-align: center;
        justify-content: center;
    }
    
    .cta-content {
        text-align: center;
    }
    
    .cta-btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<main>
  <!-- ===== HERO ===== -->
  <section class="communities-hero">
    <div class="container hero-content" data-animate>
      <h1><?php echo get_phrase("Discover our") ?> <span><?php echo get_phrase("communities") ?></span></h1>
      <p><?php echo get_phrase("Engaged_communities_and_high-quality_courses_for_sustainable_progress.") ?></p>
      
      <div class="hero-stats">
        <div class="hero-stat">
          <i class="fa-solid fa-users"></i>
          <span><?php echo $this->db->count_all('schools'); ?>+ <?php echo get_phrase("communities"); ?></span>
        </div>
        <div class="hero-stat">
          <i class="fa-solid fa-graduation-cap"></i>
          <span><?php echo $this->db->count_all('students'); ?>+ <?php echo get_phrase("members"); ?></span>
        </div>
        <div class="hero-stat">
          <i class="fa-solid fa-star"></i>
          <span><?php echo get_phrase("Quality_content"); ?></span>
        </div>
      </div>

      <!-- ===== FILTER SECTION ===== -->
      <form id="searchForm" action="<?php echo site_url('home/communities_search'); ?>" method="get" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <div class="filter-wrapper">
          
          <!-- Search Box -->
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input id="searchInput"
              type="search"
              name="search"
              placeholder="<?php echo get_phrase('Search_communities'); ?>..."
              value="<?php if (isset($input_search) && $input_search) echo ($input_search); ?>">
            <button type="button" class="clear-btn" id="clearSearch" title="<?php echo get_phrase('Clear'); ?>">
              <i class="fa-solid fa-xmark"></i>
            </button>
            <button type="submit">
              <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>
          
          <!-- Category Filter -->
          <div class="filter-select">
            <i class="fa-solid fa-th icon-left"></i>
            <select name="categories" id="catSelect">
              <option value="<?php echo lang_route('communities'); ?>"><?php echo get_phrase('All_categorie'); ?></option>
              <?php foreach ($categories as $category): ?>
                <?php $cat_formated = $this->frontend_model->get_category_formated($category['name']); ?>
                <option value="<?php echo lang_route('communities', $cat_formated); ?>">
                  <?php echo get_phrase($category['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <i class="fa-solid fa-chevron-down icon-right"></i>
          </div>
          
          <!-- Language Filter -->
         <!--  <div class="filter-select">
            <i class="fa-solid fa-language icon-left"></i>
            <select id="langSelect">
              <option value="all"><?php echo get_phrase("All_languages") ?></option>
              <option value="fr">Français</option>
              <option value="ar">العربية</option>
              <option value="en">English</option>
            </select>
            <i class="fa-solid fa-chevron-down icon-right"></i>
          </div> -->
          
        </div>
      </form>
    </div>
  </section>



  <!-- ===== GRID DES COMMUNAUTÉS ===== -->
  <section class="communities-section">
    <div class="container" id="communitiesContainer">
      <?php include 'partials/communities_grid.php'; ?>
    </div>
  </section>

  <!-- ===== CTA ===== -->
  <?php if (!$this->session->userdata('user_id')): ?>
    <section class="cta-section" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <div class="container">
        <div class="cta-card">
          <div class="cta-content">
            <h2><i class="fa-solid fa-rocket <?php echo (get_user_language() === 'arabic') ? 'ms-2' : 'me-2'; ?>"></i><?php echo get_phrase("Launch your own community in minutes") ?></h2>
            <p><?php echo get_phrase("Monetize your expertise, engage your members, and enjoy the power of the Wayo platform") ?></p>
          </div>
          <a href="<?php echo site_url('admission/online_admission'); ?>" class="cta-btn"><?php echo get_phrase("Create my community") ?> <i class="fa-solid fa-arrow-right <?php echo (get_user_language() === 'arabic') ? 'me-2 fa-flip-horizontal' : 'ms-2'; ?>"></i></a>
        </div>
      </div>
    </section>
  <?php endif; ?>



  <!-- Communities AJAX Search & Filter -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("searchForm");
      const searchInput = document.getElementById("searchInput");
      const searchButton = form.querySelector("button[type='submit']");
      const clearButton = document.getElementById("clearSearch");
      const catSelect = document.getElementById("catSelect");
      const container = document.getElementById("communitiesContainer");
      
      // Storage key for pagination state
      const STORAGE_KEY = 'communities_page_state';

      // ===== CUSTOM DROPDOWN CREATION =====
      function createCustomDropdown(selectElement) {
        const filterSelect = selectElement.closest('.filter-select');
        if (!filterSelect) return;

        // Get selected option text
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const selectedText = selectedOption ? selectedOption.textContent : '';

        // Create custom button
        const customButton = document.createElement('div');
        customButton.className = 'custom-select-button';
        customButton.innerHTML = `
          <span class="selected-text">${selectedText}</span>
          <i class="fa-solid fa-chevron-down icon-right"></i>
        `;

        // Create dropdown menu
        const customDropdown = document.createElement('div');
        customDropdown.className = 'custom-dropdown';

        // Create options
        Array.from(selectElement.options).forEach((option, index) => {
          const customOption = document.createElement('div');
          customOption.className = 'custom-option';
          if (index === selectElement.selectedIndex) {
            customOption.classList.add('selected');
          }
          customOption.textContent = option.textContent;
          customOption.dataset.value = option.value;
          customOption.dataset.index = index;

          customOption.addEventListener('click', () => {
            // Update select value
            selectElement.selectedIndex = index;
            selectElement.dispatchEvent(new Event('change', { bubbles: true }));

            // Update button text
            customButton.querySelector('.selected-text').textContent = option.textContent;

            // Update selected state
            customDropdown.querySelectorAll('.custom-option').forEach(opt => {
              opt.classList.remove('selected');
            });
            customOption.classList.add('selected');

            // Close dropdown
            customButton.classList.remove('active');
            customDropdown.classList.remove('active');
          });

          customDropdown.appendChild(customOption);
        });

        // Toggle dropdown on button click
        customButton.addEventListener('click', (e) => {
          e.stopPropagation();
          const isActive = customButton.classList.contains('active');
          
          // Close all other dropdowns
          document.querySelectorAll('.custom-select-button').forEach(btn => {
            if (btn !== customButton) {
              btn.classList.remove('active');
              btn.nextElementSibling?.classList.remove('active');
            }
          });

          // Toggle current dropdown
          customButton.classList.toggle('active');
          customDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
          if (!filterSelect.contains(e.target)) {
            customButton.classList.remove('active');
            customDropdown.classList.remove('active');
          }
        });

        // Insert custom elements
        const iconLeft = filterSelect.querySelector('.icon-left');
        const iconRight = filterSelect.querySelector('.icon-right');
        
        // Insert button after select
        selectElement.insertAdjacentElement('afterend', customButton);
        
        // Insert dropdown after button
        customButton.insertAdjacentElement('afterend', customDropdown);
        
        // Keep icons visible - insert left icon at the beginning
        if (iconLeft) {
          const clonedIconLeft = iconLeft.cloneNode(true);
          clonedIconLeft.style.position = 'static';
          clonedIconLeft.style.transform = 'none';
          clonedIconLeft.style.top = 'auto';
          clonedIconLeft.style.left = 'auto';
          customButton.insertBefore(clonedIconLeft, customButton.querySelector('.selected-text'));
          iconLeft.style.display = 'none';
        }
        
        // Hide original right icon (already in custom button)
        if (iconRight) {
          iconRight.style.display = 'none';
        }
      }

      // Initialize custom dropdowns
      if (catSelect) createCustomDropdown(catSelect);

      // ===== TRUNCATE DESCRIPTIONS =====
      function truncateDescriptions() {
        const descriptions = document.querySelectorAll('.community-card .card-description');
        descriptions.forEach(desc => {
          // Store original text if not already stored
          if (!desc.dataset.originalText) {
            desc.dataset.originalText = desc.textContent;
          }
          
          const originalText = desc.dataset.originalText;
          const lineHeight = parseFloat(window.getComputedStyle(desc).lineHeight);
          const maxHeight = lineHeight * 2; // 2 lines
          
          // Reset to original text
          desc.textContent = originalText;
          
          // Check if text exceeds 2 lines
          if (desc.scrollHeight > maxHeight) {
            let words = originalText.split(/\s+/).filter(w => w.length > 0);
            
            // If no spaces or single very long word, truncate by characters
            if (words.length === 0 || (words.length === 1 && words[0].length > 50)) {
              // Truncate character by character
              let truncated = false;
              let testText = originalText;
              
              while (desc.scrollHeight > maxHeight && testText.length > 0) {
                testText = testText.slice(0, -1);
                desc.textContent = testText + '...';
                truncated = true;
              }
              
              if (!truncated) {
                desc.textContent = originalText;
              }
            } else {
              // Truncate by words using binary search
              let low = 0;
              let high = words.length;
              let bestFit = words.length;
              
              while (low <= high) {
                const mid = Math.floor((low + high) / 2);
                const testText = words.slice(0, mid).join(' ') + (mid < words.length ? '...' : '');
                desc.textContent = testText;
                
                if (desc.scrollHeight <= maxHeight) {
                  bestFit = mid;
                  low = mid + 1;
                } else {
                  high = mid - 1;
                }
              }
              
              // Apply truncation
              if (bestFit < words.length) {
                desc.textContent = words.slice(0, bestFit).join(' ') + '...';
              } else {
                desc.textContent = originalText;
              }
            }
          }
        });
      }

      // Truncate descriptions on page load
      truncateDescriptions();

      // Loading skeleton template
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

      // ===== CLEAR BUTTON LOGIC =====
      function updateClearButton() {
        if (searchInput.value.trim().length > 0) {
          clearButton.classList.add('visible');
        } else {
          clearButton.classList.remove('visible');
        }
      }
      
      // Show/hide clear button on input
      searchInput.addEventListener('input', updateClearButton);
      
      // Initial check for pre-filled search
      updateClearButton();
      
      // Clear button click
      clearButton.addEventListener('click', () => {
        searchInput.value = '';
        updateClearButton();
        searchInput.focus();
        // Don't trigger search automatically - user can press Enter or click search button
      });

      // Search on button click
      searchButton.addEventListener("click", e => {
        e.preventDefault();
        sendAjax();
      });

      // Search on Enter key
      searchInput.addEventListener("keypress", e => {
        if (e.key === "Enter") {
          e.preventDefault();
          sendAjax();
        }
      });

      // Filter by category
      catSelect.addEventListener("change", e => {
        e.preventDefault();
        const categoryUrl = catSelect.value;
        sendAjax(categoryUrl, true); // Enable scroll for category change
      });

      // ===== PAGE STATE MANAGEMENT =====
      function savePageState(url = null) {
        const state = {
          url: url || window.location.href,
          search: searchInput.value.trim(),
          category: catSelect.value,
          page: extractPageNumber(url || window.location.href),
          timestamp: Date.now()
        };
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
      }
      
      function loadPageState() {
        try {
          const saved = sessionStorage.getItem(STORAGE_KEY);
          if (saved) {
            return JSON.parse(saved);
          }
        } catch (e) {
          console.error('Error loading page state:', e);
        }
        return null;
      }
      
      function extractPageNumber(url) {
        if (!url) return 1;
        const urlParts = url.split('/');
        for (let i = urlParts.length - 1; i >= 0; i--) {
          const part = urlParts[i].split('?')[0];
          if (!isNaN(part) && part !== '') {
            return parseInt(part);
          }
        }
        return 1;
      }
      
      // Restore state on page load (when coming back from detail page)
      function restorePageState() {
        const state = loadPageState();
        if (!state) return;
        
        // Check if we're navigating back (using performance API)
        const navEntries = performance.getEntriesByType('navigation');
        const isBackNavigation = navEntries.length > 0 && navEntries[0].type === 'back_forward';
        
        // Also check if the referrer suggests we came from a detail page
        const referrer = document.referrer;
        const isFromDetail = referrer.includes('community_details') || referrer.includes('/community/');
        
        if ((isBackNavigation || isFromDetail) && state.page > 1) {
          // Restore search input
          if (state.search) {
            searchInput.value = state.search;
            updateClearButton();
          }
          
          // Restore category and page
          if (state.url) {
            sendAjax(state.url, false); // Don't scroll on restore
          }
        }
      }

      function sendAjax(pageUrl = null, shouldScroll = false) {
        const searchValue = searchInput.value.trim();
        let url = pageUrl || "<?php echo site_url('home/communities_search'); ?>";

        // Ensure URL is absolute
        if (url && !url.startsWith('http') && !url.startsWith('/')) {
          url = window.location.origin + '/' + url;
        } else if (url && url.startsWith('/')) {
          url = window.location.origin + url;
        }

        // Only add search parameter if we're using communities_search endpoint
        // For category URLs, don't add search parameter as it's handled by the route
        if (searchValue && url.includes('communities_search')) {
          const sep = url.includes("?") ? "&" : "?";
          url += sep + "search=" + encodeURIComponent(searchValue);
        } else if (searchValue && !url.includes('communities_search')) {
          // If we have search and it's a category URL, we need to use communities_search instead
          url = "<?php echo site_url('home/communities_search'); ?>";
          url += "?search=" + encodeURIComponent(searchValue);
        }

        // Save current state before loading new content
        savePageState(url);

        // Show loading skeleton
        container.innerHTML = loadingSkeleton;
        
        // Scroll to top of results only if explicitly requested
        if (shouldScroll) {
          const communitiesSection = document.querySelector('.communities-section');
          if (communitiesSection) {
            const navbarHeight = 190;
            const elementPosition = communitiesSection.getBoundingClientRect().top + window.pageYOffset;
            const offsetPosition = elementPosition - navbarHeight;
            
            window.scrollTo({
              top: offsetPosition,
              behavior: 'smooth'
            });
          } else {
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }

        fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
          },
          credentials: 'same-origin'
        })
          .then(res => {
            if (!res.ok) {
              throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.text();
          })
          .then(html => {
            // Check if response is HTML or just partial content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            
            // Try to find #communitiesContainer first (for full page responses)
            let newContent = doc.querySelector("#communitiesContainer");
            
            if (newContent && newContent.innerHTML.trim() !== '') {
              container.innerHTML = newContent.innerHTML;
            } else {
              // For AJAX partial responses, extract content directly
              const resultsHeader = doc.querySelector(".results-header");
              const cardsGrid = doc.querySelector("#cardsGrid");
              const pagination = doc.querySelector("#pagination");
              
              // Also check for empty state in the response
              const emptyState = doc.querySelector(".empty-state");
              
              let partialContent = '';
              if (resultsHeader) partialContent += resultsHeader.outerHTML;
              if (cardsGrid) {
                partialContent += cardsGrid.outerHTML;
              } else if (emptyState) {
                // If we have empty state but no grid, wrap it properly
                partialContent += '<div id="cardsGrid" class="row g-4"><div class="col-12">' + emptyState.outerHTML + '</div></div>';
              }
              if (pagination) partialContent += pagination.outerHTML;
              
              if (partialContent.trim() !== '') {
                container.innerHTML = partialContent;
              } else {
                // If we still have no content, check if HTML contains any card elements
                const hasCards = doc.querySelectorAll('.community-card').length > 0;
                if (hasCards) {
                  // Extract all cards and wrap them
                  const allCards = doc.querySelectorAll('.community-card');
                  let cardsHTML = '<div id="cardsGrid" class="row g-4">';
                  allCards.forEach(card => {
                    cardsHTML += '<div class="col-12 col-sm-6 col-lg-4 col-xxl-3">' + card.outerHTML + '</div>';
                  });
                  cardsHTML += '</div>';
                  if (pagination) cardsHTML += pagination.outerHTML;
                  container.innerHTML = cardsHTML;
                } else {
                  container.innerHTML = `
                    <div class="empty-state">
                      <p class="text-muted"><?php echo get_phrase('No_results_found'); ?></p>
                    </div>
                  `;
                }
              }
            }
            
            // Update URL without page reload (for back button support)
            if (pageUrl && window.history.pushState) {
              window.history.replaceState({ communitiesUrl: url }, '', window.location.pathname + window.location.search);
            }
            
            updateActivePagination(url);
            attachPaginationEvents();
            
            // Truncate descriptions after content load
            setTimeout(truncateDescriptions, 100);
          })
          .catch(err => {
            console.error("Erreur AJAX :", err);
            container.innerHTML = `
              <div class="empty-state">
                <p class="text-danger"><?php echo get_phrase('Loading_error'); ?></p>
              </div>
            `;
          });
      }

      function updateActivePagination(url) {
        const pagination = document.getElementById("pagination");
        if (!pagination) return;
        
        const currentPage = extractPageNumber(url);

        pagination.querySelectorAll(".page-item").forEach(item => {
          item.classList.remove("active");
        });

        pagination.querySelectorAll(".page-link").forEach(link => {
          const pageText = link.textContent.trim();
          if (!isNaN(pageText) && parseInt(pageText) === currentPage) {
            link.closest(".page-item")?.classList.add("active");
          }
        });
      }

      function attachPaginationEvents() {
        const pagination = document.getElementById("pagination");
        if (!pagination) return;
        
        pagination.querySelectorAll("a").forEach(a => {
          a.addEventListener("click", e => {
            e.preventDefault();
            sendAjax(a.href, true); // Enable scroll for pagination
          });
        });
      }
      
      // Initialize
      attachPaginationEvents();
      
      // Try to restore state if coming back from detail page
      restorePageState();
      
      // Save initial state
      savePageState();
    });
  </script>