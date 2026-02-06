<style>
/* ================= COMMUNITY DETAILS PAGE REDESIGN ================= */
/* Modern & Premium Design with Animations */

/* CSS Variables for consistency */
:root {
    --primary-orange: #FC7B30;
    --primary-orange-light: #ff9a56;
    --dark-navy: #1a1a2e;
    --dark-navy-mid: #16213e;
    --dark-navy-light: #0f3460;
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.18);
    --shadow-soft: 0 8px 32px rgba(0, 0, 0, 0.08);
    --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
    --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Keyframe Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Hero Section - Enhanced */
.community-hero {
    position: relative;
    background: linear-gradient(135deg, #e56a1f 0%, #FC7B30 50%, #ff9a56 100%);
    padding: 5rem 0 6rem;
    overflow: hidden;
}

.community-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        /* Motif géométrique moderne */
        url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M40 0L80 40L40 80L0 40z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
        /* Glow lumineux en bas à droite */
        radial-gradient(ellipse at 100% 100%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        /* Glow subtil en haut à gauche */
        radial-gradient(ellipse at 0% 0%, rgba(255, 255, 255, 0.1) 0%, transparent 40%);
    opacity: 1;
}

/* Floating decorative elements */
.community-hero::after {
    content: '';
    position: absolute;
    top: -10%;
    right: -5%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 40%, transparent 70%);
    border-radius: 50%;
    animation: float 8s ease-in-out infinite;
    pointer-events: none;
}

.community-hero .hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    animation: fadeInUp 0.8s ease-out;
}

.community-hero h1 {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 1rem;
    letter-spacing: -0.5px;
    text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

.community-hero h1 span {
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.community-hero p {
    font-size: 1.15rem;
    color: rgba(255, 255, 255, 0.85);
    max-width: 600px;
    margin: 0 auto 2.5rem;
    line-height: 1.8;
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

/* Hero Stats */
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
    color: #fff;
    font-size: 1.1rem;
}

.hero-stat span {
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
    white-space: nowrap;
}


/* Main Content Section */
.community-main {
    background: linear-gradient(180deg, #f8f9fa 0%, #fff 50%, #f8f9fa 100%);
    padding: 3.5rem 0 5rem;
    min-height: 60vh;
    position: relative;
}

.community-main::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 200px;
    background: linear-gradient(180deg, rgba(26, 26, 46, 0.02) 0%, transparent 100%);
    pointer-events: none;
}

/* Main Card - Enhanced with animations */
.main-card {
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(0, 0, 0, 0.04);
    animation: fadeInScale 0.6s ease-out;
    transition: var(--transition-smooth);
}

.main-card:hover {
    box-shadow: var(--shadow-hover);
}

.main-card .cover-image {
    width: 100%;
    height: 380px;
    object-fit: cover;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: auto;
    transition: transform 0.6s ease;
}

.main-card:hover .cover-image {
    transform: scale(1.02);
}

.main-card .card-body {
    padding: 2.5rem;
    position: relative;
}

.main-card .community-header {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-bottom: 1.75rem;
    margin-top: -50px;
    position: relative;
    z-index: 10;
}

.main-card .community-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fff, #f8f9fa);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    overflow: hidden;
    border: 4px solid #fff;
    transition: var(--transition-smooth);
    flex-shrink: 0;
}

.main-card .community-logo:hover {
    transform: scale(1.1) rotate(5deg);
}

.main-card .community-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.main-card .community-name {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--dark-navy);
    margin: 0;
    letter-spacing: -0.3px;
    background: #fff;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

/* Info Tags - Modern Pills */
.info-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.info-tags .tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #555;
    background: linear-gradient(135deg, #f8f9fa, #fff);
    padding: 0.6rem 1rem;
    border-radius: 50px;
    border: 1px solid #eee;
    transition: var(--transition-smooth);
}

.info-tags .tag:hover {
    background: linear-gradient(135deg, rgba(252, 123, 48, 0.1), rgba(255, 154, 86, 0.1));
    border-color: rgba(252, 123, 48, 0.3);
    transform: translateY(-2px);
}

.info-tags .tag i {
    color: var(--primary-orange);
    font-size: 0.9rem;
}

/* Description */
.community-description {
    font-size: 1.05rem;
    color: #555;
    line-height: 1.9;
    margin-bottom: 2.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border-radius: 16px;
    border-left: 4px solid var(--primary-orange);
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
}

/* Section Title */
.section-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--dark-navy);
    margin-bottom: 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f1f1;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 60px;
    height: 2px;
    border-radius: 2px;
}

.section-title i {
    color: var(--primary-orange);
    font-size: 1.1rem;
}

/* Class Cards - Modern with Glassmorphism */
.class-card {
    background: linear-gradient(145deg, #ffffff 0%, #fafafa 100%);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    transition: var(--transition-smooth);
    border: 1px solid rgba(0, 0, 0, 0.04);
    height: 100%;
    position: relative;
}

.class-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-orange), var(--primary-orange-light));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.class-card:hover::before {
    opacity: 1;
}

.class-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(252, 123, 48, 0.15);
    border-color: rgba(252, 123, 48, 0.2);
}

.class-card .card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.class-card .class-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
}

.class-card .class-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--dark-navy);
    margin: 0;
    line-height: 1.4;
    transition: color 0.3s ease;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

.class-card:hover .class-title {
    color: var(--primary-orange);
}

.class-card .price-badge {
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-light));
    color: #fff !important;
    padding: 0.4rem 0.85rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    white-space: nowrap;
    border: none !important;
    box-shadow: 0 4px 12px rgba(252, 123, 48, 0.3);
    animation: pulse 3s ease-in-out infinite;
}

.class-card .fomo-badge {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, rgba(252, 123, 48, 0.12), rgba(255, 154, 86, 0.15));
    color: var(--primary-orange);
    padding: 0.4rem 0.85rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    width: fit-content;
    border: 1px solid rgba(252, 123, 48, 0.2);
}

.class-card .class-info {
    font-size: 0.88rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.5rem 0;
    border-bottom: 1px dashed #eee;
}

.class-card .class-info:last-of-type {
    border-bottom: none;
}

.class-card .class-info i {
    color: var(--primary-orange);
    font-size: 0.95rem;
    width: 20px;
    text-align: center;
}

.class-card .class-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: auto;
    padding-top: 1rem;
}

/* Sidebar Card - Premium Design */
.sidebar-card {
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(0, 0, 0, 0.04);
    position: sticky;
    top: 90px;
    animation: fadeInUp 0.6s ease-out 0.2s both;
    transition: var(--transition-smooth);
}

.sidebar-card:hover {
    box-shadow: var(--shadow-hover);
}

.sidebar-card .sidebar-header {
    background: linear-gradient(135deg, #e56a1f 0%, #FC7B30 50%, #ff9a56 100%);
    padding: 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.sidebar-card .sidebar-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
    border-radius: 50%;
    animation: float 5s ease-in-out infinite;
}

.sidebar-card .sidebar-header::after {
    content: '';
    position: absolute;
    bottom: -40%;
    left: -20%;
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    animation: float 4s ease-in-out infinite reverse;
}

.sidebar-card .logo-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 1;
    overflow: hidden;
    border: 4px solid rgba(255, 255, 255, 0.9);
    transition: var(--transition-smooth);
}

.sidebar-card .logo-circle:hover {
    transform: scale(1.1) rotate(-5deg);
    box-shadow: 0 16px 40px rgba(252, 123, 48, 0.3);
}

.sidebar-card .logo-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.sidebar-card .sidebar-body {
    padding: 1.75rem;
}

.sidebar-card .sidebar-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--dark-navy);
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar-card .sidebar-title::before {
    content: '';
    width: 4px;
    height: 20px;
    background: linear-gradient(180deg, var(--primary-orange), var(--primary-orange-light));
    border-radius: 2px;
}

.sidebar-card .info-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
}

.sidebar-card .info-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border-radius: 12px;
    font-size: 0.9rem;
    transition: var(--transition-smooth);
}

.sidebar-card .info-list li:hover {
    background: linear-gradient(135deg, rgba(252, 123, 48, 0.05) 0%, rgba(255, 154, 86, 0.08) 100%);
    transform: translateX(5px);
}

.sidebar-card .info-list li:last-child {
    margin-bottom: 0;
}

.sidebar-card .info-list .label {
    color: #666;
    display: flex;
    align-items: center;
}

.sidebar-card .info-list .value {
    font-weight: 700;
    color: var(--dark-navy);
}

.sidebar-card .info-list .value.price {
    color: var(--primary-orange);
    font-weight: 800;
    font-size: 1rem;
}

.sidebar-card .private-alert {
    background: linear-gradient(135deg, rgba(252, 123, 48, 0.1), rgba(255, 154, 86, 0.15));
    border: 1px solid rgba(252, 123, 48, 0.25);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    font-size: 0.88rem;
    color: #d35400;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Buttons - Modern with Shine Effect */
.btn-wayo {
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-light)) !important;
    color: #fff !important;
    border: none !important;
    padding: 0.8rem 1.75rem;
    border-radius: 14px;
    font-weight: 600;
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(252, 123, 48, 0.3);
}

.btn-wayo::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.btn-wayo:hover::before {
    left: 100%;
}

.btn-wayo:hover {
    background: linear-gradient(135deg, #e56a1f, var(--primary-orange)) !important;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 28px rgba(252, 123, 48, 0.4);
    color: #fff !important;
}

.btn-wayo-join {
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-light)) !important;
    color: #fff !important;
    border: none !important;
    padding: 1rem 1.75rem;
    border-radius: 14px;
    font-weight: 700;
    transition: var(--transition-smooth);
    width: 100%;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(252, 123, 48, 0.35);
}

.btn-wayo-join::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.btn-wayo-join:hover::before {
    left: 100%;
}

.btn-wayo-join:hover {
    background: linear-gradient(135deg, #e56a1f, var(--primary-orange)) !important;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 14px 32px rgba(252, 123, 48, 0.45);
    color: #fff !important;
}

.btn-outline-wayo {
    background: transparent !important;
    color: var(--primary-orange) !important;
    border: 2px solid var(--primary-orange) !important;
    padding: 0.6rem 1.25rem;
    border-radius: 12px;
    font-weight: 600;
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
}

.btn-outline-wayo::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-light));
    transition: width 0.3s ease;
    z-index: -1;
}

.btn-outline-wayo::after {
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

.btn-outline-wayo:hover::before {
    width: 100%;
}

.btn-outline-wayo:hover::after {
    left: 100%;
}

.btn-outline-wayo:hover {
    color: #fff !important;
    border-color: transparent !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(252, 123, 48, 0.3);
}

.btn-outline-wayo-join {
    background: transparent !important;
    color: var(--primary-orange) !important;
    border: 2px solid var(--primary-orange) !important;
    padding: 0.6rem 1.25rem;
    border-radius: 12px;
    font-weight: 600;
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
}

.btn-outline-wayo-join::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-light));
    transition: width 0.3s ease;
    z-index: -1;
}

.btn-outline-wayo-join::after {
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

.btn-outline-wayo-join:hover::before {
    width: 100%;
}

.btn-outline-wayo-join:hover::after {
    left: 100%;
}

.btn-outline-wayo-join:hover {
    color: #fff !important;
    border-color: transparent !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(252, 123, 48, 0.3);
}

/* Text colors */
.text-wayo {
    color: var(--primary-orange) !important;
}

.text-brand {
    color: var(--primary-orange) !important;
}

/* Scroll to top button - Floating */
#scrollTopBtn {
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-orange-light)) !important;
    box-shadow: 0 6px 24px rgba(252, 123, 48, 0.5);
    transition: var(--transition-smooth);
    z-index: 1000;
    animation: pulse 2s ease-in-out infinite;
}

#scrollTopBtn:hover {
    transform: translateY(-5px) scale(1.1);
    box-shadow: 0 12px 32px rgba(252, 123, 48, 0.6);
    animation: none;
}

/* Modal Styling - Premium */
.modal-content {
    border: none;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.2);
    animation: fadeInScale 0.3s ease-out;
}

.modal-content.rounded-4 {
    border-radius: 24px !important;
}

.modal-header {
    border-bottom: none;
    padding: 1.5rem 2rem 0.5rem;
    background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
    border-radius: 24px 24px 0 0;
}

.modal-header .modal-title {
    font-weight: 800;
    color: var(--dark-navy);
}

.modal-header .btn-close {
    background-color: #f1f1f1;
    border-radius: 50%;
    padding: 0.75rem;
    opacity: 1;
    transition: var(--transition-smooth);
}

.modal-header .btn-close:hover {
    background-color: var(--primary-orange);
    transform: rotate(90deg);
}

.modal-body {
    padding: 1.5rem 2rem;
}

.modal-footer {
    border-top: none;
    padding: 1rem 2rem 1.5rem;
    background: linear-gradient(0deg, #f8f9fa 0%, #fff 100%);
    border-radius: 0 0 24px 24px;
}

.img-card-dt-communitites {
    max-height: 380px;
    width: 100%;
    object-fit: cover;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

/* Text overflow handling for long words */
.modal-body p,
.modal-body .community-description {
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
}

/* Empty state - Modern */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #888;
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border-radius: 20px;
    border: 2px dashed #ddd;
}

.empty-state i {
    color: #ddd;
    margin-bottom: 1.5rem;
}

.empty-state p {
    font-size: 1.1rem;
    margin: 0;
}

/* Selection highlight */
::selection {
    background: rgba(252, 123, 48, 0.2);
    color: var(--dark-navy);
}

/* Global Shiny Effect for All Buttons */
.community-main .btn:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join),
.community-main button:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join) {
    position: relative;
    overflow: hidden;
}

.community-main .btn:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):not(.btn-wayo):not(.btn-wayo-join)::before,
.community-main button:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):not(.btn-wayo):not(.btn-wayo-join)::before {
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

.community-main .btn:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):not(.btn-wayo):not(.btn-wayo-join):hover::before,
.community-main button:not(.btn-outline-secondary):not(.btn-outline-wayo):not(.btn-outline-wayo-join):not(.btn-wayo):not(.btn-wayo-join):hover::before {
    left: 100%;
}

/* Focus states for accessibility */
.btn:focus,
button:focus,
a:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(252, 123, 48, 0.3);
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* RTL Support */
[dir="rtl"] .info-tags .tag i {
    margin-left: 0.5rem;
    margin-right: 0;
}

[dir="rtl"] .sidebar-card .sidebar-title::before {
    margin-left: 0.5rem;
    margin-right: 0;
}

[dir="rtl"] .community-description {
    border-left: none;
    border-right: 4px solid var(--primary-orange);
}

[dir="rtl"] .sidebar-card .info-list li:hover {
    transform: translateX(-5px);
}

/* Responsive */
@media (max-width: 991px) {
    .sidebar-card {
        position: relative;
        top: 0;
        margin-top: 2rem;
    }
    
    .main-card .community-header {
        margin-top: -40px;
    }
    
    .main-card .community-logo {
        width: 70px;
        height: 70px;
    }
}

@media (max-width: 768px) {
    .community-hero {
        padding: 3.5rem 0 4.5rem;
    }
    
    .community-hero h1 {
        font-size: 1.75rem;
    }
    
    .hero-stats {
        gap: 1rem;
    }
    
    .hero-stat {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
    }
    
    .main-card .cover-image {
        height: 220px;
    }
    
    .main-card .card-body {
        padding: 1.5rem;
    }
    
    .main-card .community-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-top: -35px;
    }
    
    .main-card .community-logo {
        width: 70px;
        height: 70px;
    }
    
    .main-card .community-name {
        font-size: 1.25rem;
        text-align: center;
    }
    
    .info-tags {
        justify-content: center;
    }
    
    .community-description {
        padding: 1.25rem;
        font-size: 0.95rem;
    }
    
    .class-card .class-actions {
        flex-direction: column;
    }
    
    .class-card .class-actions .btn,
    .class-card .class-actions form {
        width: 100%;
    }
    
    .class-card .class-actions form .btn {
        width: 100%;
    }
    
    .sidebar-card .sidebar-header {
        padding: 2rem 1.5rem;
    }
    
    .sidebar-card .logo-circle {
        width: 80px;
        height: 80px;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
    }
}

@media (max-width: 480px) {
    .community-hero {
        padding: 2.5rem 0 3.5rem;
    }
    
    .community-hero p {
        font-size: 0.95rem;
    }
    
    .hero-stats {
        gap: 0.75rem;
    }
    
    .hero-stat {
        padding: 0.5rem 0.85rem;
        font-size: 0.8rem;
    }
    
    .hero-stat i {
        font-size: 0.95rem;
        color: #fff;
    }
    
    .info-tags .tag {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }
    
    .class-card .card-body {
        padding: 1.25rem;
    }
    
    .class-card .class-title {
        font-size: 0.95rem;
    }
}
</style>

<?php if (get_common_settings('recaptcha_status')): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php
// --------- TVA / VAT CALCULATION ---------
// On récupère les réglages fiscaux de la communauté / école
$settings_school = $this->settings_model->get_settings_school_data($school_id);

$vat_applicable = isset($settings_school['vat']) && (int)$settings_school['vat'] === 1;
$tax_residence  = isset($settings_school['Tax_residence']) ? $settings_school['Tax_residence'] : null;

$vat_rate = 0; // en pourcentage
if ($vat_applicable) {
    if ($tax_residence === 'MA') {
        // 1 - Communauté au Maroc  => 20% de TVA
        $vat_rate = 20;
    } elseif ($tax_residence === 'UAE' || $tax_residence === 'AE') {
        // 2 - Communauté aux EAU => 5% de TVA (support both UAE and AE codes)
        $vat_rate = 5;
    }
}

// Calcul du prix avec TVA pour la communauté
$school_price_ht = (float)$school['price'];
$school_vat_amount = $school_price_ht * ($vat_rate / 100);
$school_price_ttc = $school_price_ht + $school_vat_amount;

// Calcul du prix avec TVA pour chaque classe
$classes_with_vat = [];
foreach ($classes as $key => $class) {
    $class_price_ht = isset($class['price']) ? (float)$class['price'] : 0;
    $class_vat_amount = $class_price_ht * ($vat_rate / 100);
    $class_price_ttc = $class_price_ht + $class_vat_amount;
    
    $classes_with_vat[$key] = $class;
    $classes_with_vat[$key]['price_ht'] = $class_price_ht;
    $classes_with_vat[$key]['vat_amount'] = $class_vat_amount;
    $classes_with_vat[$key]['price_ttc'] = $class_price_ttc;
}
?>

<!-- ===== HERO ===== -->
<section class="community-hero" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container hero-content">
    <h1><?php echo $school["name"] ?></h1>
    <p><?php echo get_phrase("The No. 1 community to learn, practice, and network!") ?></p>
    
    <!-- Stats in Hero -->
    <div class="hero-stats">
      <div class="hero-stat">
        <i class="fa-solid fa-users"></i>
        <span><?php echo $school["course_students_count"] ?> <?php echo get_phrase("Members") ?></span>
      </div>
      <div class="hero-stat">
        <i class="fa-solid fa-graduation-cap"></i>
        <span><?php echo $school["teachers_count"] ?> <?php echo get_phrase("Mentors") ?></span>
      </div>
      <div class="hero-stat">
        <i class="fa-solid fa-play-circle"></i>
        <span><?php echo $school['classes_count'] ?> <?php echo get_phrase("Classes") ?></span>
      </div>
    </div>
  </div>
</section>


<!-- ===== MAIN GRID ===== -->
<main class="community-main" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <div class="row g-4">
      <!-- Main card -->
      <div class="col-lg-8">
        <div class="main-card">
          <img class="cover-image" src="<?php echo $this->user_model->get_school_cover($school_id); ?>" alt="<?php echo htmlspecialchars($school["name"]); ?>">
          <div class="card-body">
            <div class="community-header">
              <div class="community-logo">
                <img src="<?php echo $this->user_model->get_school_image($school_id); ?>" alt="Logo communauté">
              </div>
              <h2 class="community-name"><?php echo $school["name"] ?></h2>
            </div>

            <div class="info-tags">
              <?php if ($school["access"] > 0) { ?>
                <span class="tag"><i class="fa-solid fa-lock"></i><?php echo get_phrase("Private") ?></span>
              <?php } else { ?>
                <span class="tag"><i class="fa-solid fa-lock-open"></i><?php echo get_phrase("Public") ?></span>
              <?php } ?>
              <span class="tag">
                <i class="fa-solid fa-th"></i>
                <?php echo get_phrase($school['category']); ?>
              </span>
              <span class="tag"><i class="fa-solid fa-tag"></i>
                <?php
                if ((float)$school['price'] > 0) {
                  echo number_format($school_price_ttc, 2) . " " . $settings_data['system_currency'];
                  if ($vat_rate > 0) {
                    echo " <small class='text-muted'>(TTC)</small>";
                  }
                } else {
                  echo get_phrase("Free");
                }
                ?>
              </span>
              <span class="tag"><i class="fa-solid fa-graduation-cap"></i><?php echo $school["course_students_count"] ?> <?php echo get_phrase("Members") ?></span>
            </div>

            <p class="community-description">
              <?php echo $school["description"] ?>
            </p>

            <h3 class="section-title"><i class="fa-solid fa-calendar-days"></i> <?php echo get_phrase("Class schedule") ?></h3>
            <!-- CLASSES GRID -->
             <?php
              $currencies = isset($settings_school['system_currency']) ? $settings_school['system_currency'] : 'USD';
              ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-3" id="classesGrid">
              <?php if (!empty($classes)): ?>
                <?php 
                  // Get community status once
                  $community_status = $this->user_model->check_student_status($school_id);
                  $is_logged_in = (bool)$this->session->userdata('user_id');

                  // Check if user is admin/teacher of THIS school or superadmin
                  $is_admin = $this->session->userdata('admin_login') == 1;
                  $is_teacher = $this->session->userdata('teacher_login') == 1;
                  $is_superadmin = $this->session->userdata('superadmin_login') == 1;
                  $session_school_id = $this->session->userdata('school_id');

                  // Check actual DB roles for this specific school (handle cross-role browsing)
                  $has_admin_rights = false;
                  if ($is_logged_in) {
                      $user_id = $this->session->userdata('user_id');
                      // Check for admin role
                      $admin_check = $this->db->get_where('user_schools', array(
                          'user_id' => $user_id, 
                          'school_id' => $school_id, 
                          'role' => 'admin'
                      ));
                      // Check for teacher role
                      $teacher_check = $this->db->get_where('user_schools', array(
                          'user_id' => $user_id, 
                          'school_id' => $school_id, 
                          'role' => 'teacher'
                      ));
                      
                      if ($admin_check->num_rows() > 0 || $teacher_check->num_rows() > 0) {
                          $has_admin_rights = true;
                      }
                  }

                  $is_authority = $is_superadmin || (($is_admin || $is_teacher) && $session_school_id == $school_id) || $has_admin_rights;

                  // Determine role to switch for modal/buttons
                  $role_to_switch_authority = '';
                  if ($is_authority) {
                      $is_teacher_role = (isset($teacher_check) && $teacher_check->num_rows() > 0) || ($is_teacher && $session_school_id == $school_id);
                      $role_to_switch_authority = $is_teacher_role ? 'teacher' : 'admin';
                  }

                ?>
                
                <?php foreach ($classes as $key => $class): ?>
                  <div class="col">
                    <div class="class-card"
                      data-title="<?php echo htmlspecialchars($class['name']); ?>"
                      data-photo="<?php echo htmlspecialchars($class['photo']); ?>"
                      data-desc="<?php echo isset($class['description']) ? htmlspecialchars($class['description']) : ''; ?>"
                      data-mentor="<?php echo htmlspecialchars($class['mentor']); ?>"
                      data-duration="<?php echo isset($class['duration']) ? htmlspecialchars($class['duration']) : ''; ?>"
                      data-level="<?php echo isset($class['level']) ? htmlspecialchars($class['level']) : ''; ?>"
                      data-start="<?php echo isset($class['date_debut']) ? htmlspecialchars($class['date_debut']) : ''; ?>"
                      data-end="<?php echo isset($class['date_fin']) ? htmlspecialchars($class['date_fin']) : ''; ?>"
                      data-price="<?php echo isset($class['price']) ? $class['price'] : 0; ?>"
                      data-currency="<?php echo $currencies; ?>"
                      data-cycle="<?php echo isset($class['cycle']) ? htmlspecialchars($class['cycle']) : ''; ?>"
                      data-free="<?php echo htmlspecialchars($class['nombre_max_membre']); ?>">

                      <div class="card-body">
                        <div class="class-header">
                          <h4 class="class-title"><?php echo htmlspecialchars($class['name']); ?></h4>
                          <span class="price-badge">
                            <?php
                            if (isset($class['price']) && $class['price'] > 0) {
                              $class_price_ttc = isset($classes_with_vat[$key]['price_ttc']) ? $classes_with_vat[$key]['price_ttc'] : $class['price'];
                              echo number_format($class_price_ttc, 2) . ' ' . (isset($class['currency']) && !empty($class['currency']) ? $class['currency'] : $currencies);
                              if ($vat_rate > 0) {
                                echo " <small>(TTC)</small>";
                              }
                              if (!empty($class['cycle'])) echo ' ' . $class['cycle'];
                            } else {
                              echo get_phrase('free');
                            }
                            ?>
                          </span>
                        </div>
                        <span class="fomo-badge">🔥 <?php echo get_phrase("Limited offer") ?></span>

                        <?php if (!empty($class['date_debut']) && !empty($class['date_fin']) && 
                                  $class['date_debut'] !== '0000-00-00' && $class['date_fin'] !== '0000-00-00'): ?>
                                <div class="class-info">
                                  <i class="fa-regular fa-calendar"></i>
                                  <span><strong><?php echo (new DateTime($class['date_debut']))->format('d M. Y'); ?></strong> → <strong><?php echo (new DateTime($class['date_fin']))->format('d M. Y'); ?></strong></span>
                                </div>
                        <?php else: ?>
                                  &nbsp;
                        <?php endif; ?>
                        
                        <div class="class-info">
                          <i class="fa-regular fa-circle-check"></i>
                          <span><?php echo htmlspecialchars($class['nombre_max_membre']); ?> <?php echo get_phrase("Maximum_number") ?></span>
                        </div>
                        <div class="class-actions">
                     <button
                        class="btn btn-outline-wayo btn-sm flex-fill openClassModal"
                        data-bs-toggle="modal"
                        data-bs-target="#classModal"
                        data-class-id="<?php echo $class['id']; ?>"
                        data-student-id="<?php echo $student_id; ?>"
                        data-currency="<?php echo $currencies; ?>"
                        data-class-price="<?php echo $class['price']; ?>"
                        data-class-enrolled="<?php
                            echo $this->db->get_where('enrols', [
                                'student_id' => $student_id,
                                'school_id' => $school_id,
                                'class_id' => $class['id']
                            ])->num_rows();
                        ?>"
                        data-school-id="<?php echo $school_id; ?>"
                        data-community-status="<?php echo $community_status; ?>"
                        data-school-price="<?php echo $school_price_ttc; ?>"
                        data-school-currency="<?php echo $settings_data['system_currency']; ?>"
                        data-is-logged-in="<?php echo $is_logged_in ? '1' : '0'; ?>"
                        data-user-role="<?php echo $this->session->userdata('admin_login') == 1 ? 'admin' : ($this->session->userdata('teacher_login') == 1 ? 'teacher' : ''); ?>"
                        data-is-authority="<?php echo $is_authority ? '1' : '0'; ?>"
                        data-role-to-switch="<?php echo $role_to_switch_authority; ?>"
                      >
                        <?php echo get_phrase("See more"); ?>
                      </button>
                          <?php
                            // Vérifier si déjà inscrit à la classe
                            $enrols_datas = 0;
                            if ($student_id > 0) {
                                $enrols_datas = $this->db->get_where('enrols', array(
                                    'student_id' => $student_id,
                                    'school_id' => $school_id,
                                    'class_id' => $class['id']
                                ))->num_rows();
                            }

                            $enrols_max = $this->db->get_where('enrols', array(
                                'school_id' => $school_id,
                                'class_id' => $class['id']
                            ))->num_rows();

                            $nombre_max = isset($class['nombre_max_membre']) ? (int)$class['nombre_max_membre'] : 0;
                            $is_max_reached = ($nombre_max > 0 && $enrols_max >= $nombre_max);

                            // Priority check: if authority, show dashboard link
                            if ($is_authority): 
                                $role_to_switch = ($is_teacher || (isset($teacher_check) && $teacher_check->num_rows() > 0)) ? 'teacher' : 'admin';
                            ?>
                              <form action="<?php echo site_url('login/logout'); ?>" method="post" style="display:inline;">
                                  <button type="button" class="btn btn-wayo fw-bold" onclick="switch_to_admin_role_for_community(<?php echo $school_id; ?>, '<?php echo $role_to_switch; ?>')">
                                      <?php echo get_phrase('dashboard'); ?>
                                  </button>
                              </form>
                            <?php 
                            // CASE 1 : déjà inscrit à la classe → Start course
                            elseif ($enrols_datas > 0): ?>
                              <button class="btn btn-outline-wayo-join fw-bold start-course-btn"
                                      data-class-id="<?php echo $class['id']; ?>">
                                <?php echo get_phrase("start_course"); ?>
                              </button>

                            <?php 
                            // CASE 2 : classe full → liste d’attente
                            elseif ($is_max_reached): ?>
                              <button type="button" class="btn btn-outline-secondary fw-bold" disabled>
                                <?php echo htmlspecialchars(get_phrase("class_is_full_no_more_space")); ?>
                              </button>

                            <?php 
                            // CASE 3 : pas encore inscrit
                            else:
                              $status = $this->user_model->check_student_status($school_id);

                              // CASE 3.1 : pas encore dans la communauté
                              if ($status == -1): ?>

                                <?php if (!$is_authority): ?>
                                <form action="<?php echo base_url('student/join_school/assigned/' . $school_id); ?>" method="post">
                                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                        value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                  <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
                                  <input type="hidden" name="price" value="<?php echo $school_price_ttc; ?>" />
                                  <input type="hidden" name="currency" value="<?php echo $settings_data['system_currency']; ?>" />

                                  <button type="submit" class="btn btn-wayo fw-bold <?php if(!$this->session->userdata('user_id')) echo 'join-community-login-popup'; ?>">
                                    <?php 
                                      if ($this->session->userdata('user_id')) {
                                          echo htmlspecialchars(get_phrase("join_as_member"));
                                      } else {
                                          echo htmlspecialchars(get_phrase("join_community"));
                                      }
                                    ?>
                                  </button>
                                </form>
                                <?php else: 
                                  $role_to_switch = ($is_teacher || (isset($teacher_check) && $teacher_check->num_rows() > 0)) ? 'teacher' : 'admin';
                                ?>
                                  <!-- User is authority (Admin/Teacher) of this school but viewing as student -->
                                  <form action="<?php echo site_url('login/logout'); ?>" method="post" style="display:inline;">
                                      <button type="button" class="btn btn-wayo fw-bold" onclick="switch_to_admin_role_for_community(<?php echo $school_id; ?>, '<?php echo $role_to_switch; ?>')">
                                          <?php echo get_phrase('dashboard'); ?>
                                      </button>
                                  </form>
                                <?php endif; ?>

                              <?php 
                              // CASE 3.2 : déjà dans la communauté (approuvé ou en attente)
                              elseif ($status == 0): 
                                // Status = 0 : Payé mais en attente d'approbation admin
                              ?>
                                <button type="button" class="btn btn-outline-secondary fw-bold" disabled>
                                  <?php echo htmlspecialchars(get_phrase("pending")); ?>
                                </button>

                              <?php 
                              // CASE 3.3 : Approuvé dans la communauté (status = 1)
                              else:

                                // NOUVELLE CONDITION : classe gratuite → Start course direct
                                if ((float)$class['price'] == 0): ?>
                                  
                                  <form action="<?php echo site_url('student/online_admission/assigned'); ?>" method="post">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                          value="<?php echo $this->security->get_csrf_hash(); ?>" />

                                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                                    <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
                                    <input type="hidden" name="class_id" id="class_id" value="<?php echo $class['id']; ?>">
                                    <input type="hidden" name="price" value="<?php echo $class['price']; ?>" />
                                    <input type="hidden" name="currency" value="<?php echo $currencies; ?>" />

                                    <button type="submit" class="btn btn-outline-wayo-join fw-bold">
                                      <?php echo htmlspecialchars(get_phrase("join_class")); ?>
                                    </button>
                                  </form>

                                <?php else: ?>

                                  <!-- Classe payante : inscription normale -->
                                  <form action="<?php echo site_url('student/online_admission/assigned'); ?>" method="post">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                          value="<?php echo $this->security->get_csrf_hash(); ?>" />

                                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                                    <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
                                    <input type="hidden" name="class_id" id="class_id" value="<?php echo $class['id']; ?>">
                                    <input type="hidden" name="price" value="<?php echo $class['price']; ?>" />
                                    <input type="hidden" name="currency" value="<?php echo $currencies; ?>" />

                                    <button type="submit" class="btn btn-wayo fw-bold">
                                      <?php echo htmlspecialchars(get_phrase("join_classe")); ?>
                                    </button>
                                  </form>

                                <?php endif; ?>

                              <?php endif; ?>

                            <?php endif; ?>

                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="col-12">
                  <div class="empty-state">
                    <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-muted"></i>
                    <p><?php echo get_phrase("Aucune classe disponible pour cette communauté.") ?></p>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <!-- Side card -->
      <aside class="col-lg-4">
        <div class="sidebar-card">
          <div class="sidebar-header">
            <div class="logo-circle">
              <img src="<?php echo $this->user_model->get_school_image($school_id); ?>" alt="Logo communauté">
            </div>
          </div>
          <div class="sidebar-body">
            <h3 class="sidebar-title"><?php echo get_phrase("Accès communauté") ?></h3>
            <ul class="info-list">
              <li>
                <span class="label"><i class="fa-solid fa-graduation-cap me-2 text-wayo"></i><?php echo get_phrase("Members:") ?></span>
                <span class="value"><?php echo $school["course_students_count"] ?></span>
              </li>
              <li>
                <span class="label"><i class="fa-solid fa-play-circle me-2 text-wayo"></i><?php echo get_phrase("Classes :") ?></span>
                <span class="value"><?php echo $school['classes_count'] ?></span>
              </li>
              <li>
                <span class="label"><i class="fa-solid fa-tag me-2 text-wayo"></i><?php echo get_phrase("Prix :") ?></span>
                <span class="value price">
                  <?php
                  if ((float)$school['price'] > 0) {
                    echo number_format($school_price_ttc, 2) . " " . $settings_data['system_currency'];
                    if ($vat_rate > 0) {
                      echo " <small class='text-muted'>(TTC)</small>";
                    }
                  } else {
                    echo get_phrase("Free");
                  }
                  ?>
                </span>
              </li>
            </ul>
            <?php if ((int)$school['access'] > 0): ?>
              <div class="private-alert"><i class="fa-solid fa-lock me-2"></i><?php echo htmlspecialchars(get_phrase("Private community - join request only")); ?></div>
            <?php endif; ?>
            <div class="community-app-button">
              <a id="dashboard-community-app-button" href="<?php echo route('dashboard'); ?>" class="btn btn-wayo-join w-100 text-center" style="display:none; text-decoration:none;"> <?php echo htmlspecialchars(get_phrase("community_app")); ?> </a>
            </div>

            <form action="<?php echo base_url('student/join_school/assigned/' . $school_id); ?>" method="post" id="join-community-form">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
              <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
              <input type="hidden" name="price" value="<?php echo $school_price_ttc; ?>" />
              <input type="hidden" name="currency" value="<?php echo $settings_data['system_currency']; ?>" />
              <input type="hidden" name="user_role" value="" id="user_role_hidden" />
              <button id="join-button" type="submit" class="btn btn-wayo-join w-100" style="display:none"> <?php echo htmlspecialchars(get_phrase("join_community")); ?> </button>
            </form>
            <button id="login-join-button" class="btn btn-wayo-join w-100" style="display:none; cursor:pointer;"> <?php echo htmlspecialchars(get_phrase("join_community")); ?> </button>
          </div>
        </div>
      </aside>

    </div>
  </div>
</main>

<!-- Scroll to top -->
<button id="scrollTopBtn" class="btn btn-wayo rounded-circle position-fixed d-flex align-items-center justify-content-center"
  style="width:52px;height:52px;right:24px;bottom:24px;display:none" aria-label="Retour en haut">
  <i class="fa-solid fa-arrow-up text-white"></i>
</button>


<!-- MODAL DETAILS CLASS -->
<div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="classModalLabel" style="color: #1a1a2e;"><?php echo get_phrase("Title") ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body pt-3">
        <div class="d-flex justify-content-center mb-4">
          <img id="classPhoto" src="" alt="Photo de classe" class="img-fluid img-card-dt-communitites" style="border-radius: 16px;">
        </div>

        <div class="info-tags mb-3">
          <span class="tag">
            <i class="fa-regular fa-calendar"></i>
            <span id="classDates"></span>
          </span>
          <span class="tag">
            <i class="fa-regular fa-user"></i>
            <span id="classMentor"></span>
          </span>
          <span class="tag">
            <i class="fa-regular fa-circle-check"></i>
            <span id="classFree"></span>
          </span>
        </div>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-brand fs-5" id="classPrice">—</span>

        <!-- CONTENEUR DU BOUTON : JS injectera Start/Join -->
        <div id="modalActionBtnContainer"></div>

      </div>
    </div>
  </div>
</div>





<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
const base_url = "<?php echo base_url(); ?>";
const csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>";
const csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
const currentSchoolId = "<?php echo $this->session->userdata('active_school_id'); ?>";
const currentRole = "<?php echo $this->session->userdata('role'); ?>";
const targetSchoolId = "<?php echo $school_id; ?>";


document.addEventListener("DOMContentLoaded", function() {

  // Quand on clique sur "See More"
  document.querySelectorAll('.openClassModal').forEach(btn => {
    btn.addEventListener('click', function() {

      const classId = this.dataset.classId;
      const classPrice = parseFloat(this.dataset.classPrice);
      const enrolled = parseInt(this.dataset.classEnrolled); // 0 = pas payé, 1 = déjà payé
      const studentId = this.dataset.studentId;
      const schoolId = this.dataset.schoolId;
      const csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>";
      const csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
      const currency = this.dataset.currency || "<?php echo $settings_data['system_currency']; ?>";

      const container = document.getElementById('modalActionBtnContainer');
      container.innerHTML = ""; // reset le bouton

      const communityStatus = parseInt(this.dataset.communityStatus);
      const schoolPrice = parseFloat(this.dataset.schoolPrice);
      const schoolCurrency = this.dataset.schoolCurrency;
      const isLoggedIn = this.dataset.isLoggedIn === '1';
      const userRole = this.dataset.userRole || "<?php echo $this->session->userdata('admin_login') == 1 ? 'admin' : ($this->session->userdata('teacher_login') == 1 ? 'teacher' : ''); ?>";
      const isAuthority = this.dataset.isAuthority === '1';
      const roleToSwitch = this.dataset.roleToSwitch || 'admin';

      if (isAuthority) {
          const dashboardBtn = document.createElement('button');
          dashboardBtn.className = 'btn btn-wayo fw-bold';
          dashboardBtn.textContent = "<?php echo get_phrase('dashboard'); ?>";
          dashboardBtn.onclick = function() {
              switch_to_admin_role_for_community(schoolId, roleToSwitch);
          };
          container.appendChild(dashboardBtn);
          return;
      }

          if (communityStatus === -1) {
          
          if (isAuthority) return;

          // Not a member -> Join Community
          // Toujours utiliser student/join_school (même pour admin/teacher qui rejoignent en tant que member)
          const form = document.createElement('form');
          let joinUrl = base_url + "student/join_school/assigned/" + schoolId;
          form.action = joinUrl;
          form.method = 'post';

          let btnClass = "btn btn-wayo fw-bold";
          if (!isLoggedIn) {
             btnClass += " join-community-login-popup";
          }
          // Changer le texte selon le rôle
          let buttonText = "<?php echo get_phrase('join_community'); ?>";
          if (isLoggedIn) {
            buttonText = "<?php echo get_phrase('join_as_member'); ?>";
          }

          form.innerHTML = `
            <input type="hidden" name="${csrfName}" value="${csrfHash}">
            <input type="hidden" name="school_id" value="${schoolId}">
            <input type="hidden" name="price" value="${schoolPrice}">
            <input type="hidden" name="currency" value="${schoolCurrency}">
            <input type="hidden" name="user_role" value="${userRole}">
            <button type="submit" class="${btnClass}">${buttonText}</button>
          `;
          container.appendChild(form);
          
            // Re-attach event listener for login popup if needed
            if (!isLoggedIn) {
                 const newBtn = form.querySelector('.join-community-login-popup');
                 if(newBtn){
                     newBtn.addEventListener("click", function(e) {
                      e.preventDefault();
                      
                      // Open login popup
                        const toggle = document.querySelector('.login-toggle');
                        if (toggle) {
                          toggle.click();
                        }

                        // Close modal
                        const applyModal = document.getElementById("classModal");
                        if (applyModal) {
                          const modalInstance = bootstrap.Modal.getInstance(applyModal);
                          if (modalInstance) {
                            modalInstance.hide();
                          }
                        }
                    });
                 }
            }

      } else if (communityStatus === 0) {
        // Status = 0: Paid but pending admin approval -> Show "En attente"
        const pendingBtn = document.createElement('button');
        pendingBtn.className = 'btn btn-outline-secondary fw-bold';
        pendingBtn.disabled = true;
        pendingBtn.textContent = "<?php echo get_phrase('pending'); ?>";
        container.appendChild(pendingBtn);
      } else if (enrolled > 0) {
        // Déjà inscrit -> Start Course (Redirect via JS handler)
        const startBtn = document.createElement('button');
        startBtn.className = 'btn btn-outline-wayo-join fw-bold start-course-btn';
        startBtn.dataset.classId = classId;
        startBtn.textContent = "<?php echo get_phrase('start_course'); ?>";
        container.appendChild(startBtn);
      } else if (classPrice === 0) {
        // Pas inscrit mais Gratuit -> Start Course (Enroll via Form)
        const form = document.createElement('form');
        form.action = base_url + "student/online_admission/assigned";
        form.method = 'post';
        form.innerHTML = `
          <input type="hidden" name="${csrfName}" value="${csrfHash}">
          <input type="hidden" name="student_id" value="${studentId}">
          <input type="hidden" name="school_id" value="${schoolId}">
          <input type="hidden" name="class_id" value="${classId}">
          <input type="hidden" name="price" value="${classPrice}">
          <input type="hidden" name="currency" value="${currency}">
          <button type="submit" class="btn btn-outline-wayo-join fw-bold"><?php echo get_phrase('join_class'); ?></button>
        `;
        container.appendChild(form);
      } else {
        // Classe payante → Join Class (paiement)
        const form = document.createElement('form');
        form.action = base_url + "student/online_admission/assigned";
        form.method = 'post';
        form.innerHTML = `
          <input type="hidden" name="${csrfName}" value="${csrfHash}">
          <input type="hidden" name="student_id" value="${studentId}">
          <input type="hidden" name="school_id" value="${schoolId}">
          <input type="hidden" name="class_id" value="${classId}">
          <input type="hidden" name="price" value="${classPrice}">
          <input type="hidden" name="currency" value="${currency}">
          <button type="submit" class="btn btn-wayo fw-bold"><?php echo get_phrase('join_class'); ?></button>
        `;
        container.appendChild(form);
      }
    });
  });

  // Start Course redirection
  document.addEventListener('click', function(e){
    if (!e.target.classList.contains('start-course-btn')) return;
    e.preventDefault();
    
    const classId = e.target.dataset.classId;
    if (!classId) return;

    const courseUrl = base_url + "student/courses/" + classId;

    // Check if we need to switch context (Role or School)
    if (currentRole !== 'student' || currentSchoolId !== targetSchoolId) {
        // Prepare form data for switch
        const formData = new FormData();
        formData.append('school_id', targetSchoolId);
        formData.append('role', 'student');
        formData.append(csrfName, csrfHash);

        fetch(base_url + 'home/switch_community_role_front', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Switch successful, redirect to the specific course
                window.location.href = courseUrl;
            } else {
                console.error('Switch failed:', data);
                // Fallback to direct link
                window.location.href = courseUrl; 
            }
        })
        .catch(error => {
            console.error('Error during switch:', error);
            window.location.href = courseUrl;
        });
    } else {
        // Already in correct context
        window.location.href = courseUrl;
    }
  });

});

</script>
<script>
document.addEventListener("DOMContentLoaded", function() {

  // Bouton login déjà existant
  const loginJoinBtn = document.getElementById("login-join-button");

  // Tous les boutons avec la classe join-community-login
  const joinCommunityBtns = document.querySelectorAll(".join-community-login");

  function openPopup() {
    const toggle = document.querySelector('.login-toggle');
    if (toggle) {
      toggle.click(); // ouvre le popup
    }
  }

  // Pour login-join-button
  if (loginJoinBtn) {
    loginJoinBtn.addEventListener("click", openPopup);
  }

  // Pour tous les boutons avec la classe join-community-login
  joinCommunityBtns.forEach(btn => {
    btn.addEventListener("click", function(e) {
      e.preventDefault();
      openPopup();
    });
  });

});

function switch_to_admin_role_for_community(targetSchoolId, role = 'admin') {
    // Prepare form data for switch
    const formData = new FormData();
    formData.append('school_id', targetSchoolId);
    formData.append('role', role); 
    formData.append(csrfName, csrfHash);

    fetch(base_url + 'home/switch_community_role_front', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = data.redirect_url ? data.redirect_url : base_url + role + '/dashboard';
        } else {
            console.error('Switch failed:', data);
            alert('Could not switch to ' + role + ' role.');
        }
    })
    .catch(error => {
        console.error('Error during switch:', error);
    });
}
</script>

<!-- script de button join community affichage poupup and hide modal -->
<script>
  document.addEventListener("DOMContentLoaded", function() {

  // ID de ton modal Bootstrap à fermer
  const applyModal = document.getElementById("classModal"); 

  //  boutons dans le footer avec cette classe
  const popupBtns = document.querySelectorAll(".join-community-login-popup");

  function openPopupAndCloseModal() {

    // Ouvrir le popup login
    const toggle = document.querySelector('.login-toggle');
    if (toggle) {
      toggle.click();
    }

    // Fermer le modal
    if (applyModal) {
      const modalInstance = bootstrap.Modal.getInstance(applyModal);
      if (modalInstance) {
        modalInstance.hide();
      }
    }
  }
  // l’événement sur tous les boutons
  popupBtns.forEach(btn => {
    btn.addEventListener("click", function(e) {
      e.preventDefault();      
      openPopupAndCloseModal();
    });
  });

});

</script>
<script>
  /* ===== Utilitaires prix ===== */
  function formatPrice(price, currency = '', cycle = '', vatRate = 0) {
    const priceNum = parseFloat(price) || 0;
    if (priceNum === 0) {
      return '<?php echo get_phrase("free"); ?>';
    }
    
    let formattedPrice = priceNum.toFixed(2);
    if (currency) {
      formattedPrice += ' ' + currency;
    }
    if (vatRate > 0) {
      formattedPrice += ' <small class="text-muted">(TTC)</small>';
    }
    if (cycle) {
      formattedPrice += ' ' + cycle;
    }
    return formattedPrice;
  }

  // Note: Les prix sont déjà formatés dans le PHP, cette fonction est disponible pour les mises à jour dynamiques
  // document.querySelectorAll('.class-card').forEach(card => {
  //   const badge = card.querySelector('.price-badge');
  //   if (badge && !badge.textContent.trim()) {
  //     const price = card.dataset.price || 0;
  //     const currency = card.dataset.currency || '';
  //     const cycle = card.dataset.cycle || '';
  //     badge.innerHTML = formatPrice(price, currency, cycle);
  //   }
  // });


  /* ===== Scroll-to-top ===== */
  const topBtn = document.getElementById('scrollTopBtn');
  window.addEventListener('scroll', () => {
    if (!topBtn) return;
    topBtn.style.display = window.scrollY > 600 ? 'flex' : 'none';
  });
  topBtn?.addEventListener('click', () => window.scrollTo({
    top: 0,
    behavior: 'smooth'
  }));
</script>


<script>
  if (document.getElementById("login-join-button")) {
    document.getElementById("login-join-button").addEventListener("click", function() {
      document.querySelector('.login-toggle').click();
    });
  }

  $(document).ready(function() {
    $('.courses-slider').slick({
      fade: true,
      autoplay: true,
      autoplaySpeed: 4000,
      arrows: false,
      infinite: true,
      pauseOnFocus: false,
      adaptiveHeight: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      centerMode: false,
      /* No centering to avoid gaps */
      variableWidth: false /* Consistent full width */
    });

    const descs = document.querySelectorAll(".course-slider-description");
    descs.forEach(desc => {
      const pars = desc.getElementsByTagName("p");
      Array.from(pars).forEach(par => {
        par.classList.add("text-white");
        par.classList.add("text-center");
      });
    });
  });
</script>

<script>
  $(document).ready(function() {
    // Variable pour stocker le rôle de l'utilisateur dans cette communauté
    var userRoleInThisSchool = null;
    
    function updateButton() {
      $.ajax({
        url: "<?php echo base_url('home/check_student_status_ajax/' . $school_id); ?>",
        method: "GET",
        dataType: "json",
        success: function(response) {
          var button = $("#join-button");
          var button_paye = $("#paye-button");

          var loginButton = $("#login-join-button");
          var dashboardCommunityAppButton = $("#dashboard-community-app-button");
          var form = $("#join-community-form");
          var userRoleHidden = $("#user_role_hidden");

          if (response.status === null) {
            loginButton.show();
            button.hide();
            dashboardCommunityAppButton.hide();
          } else {
            loginButton.hide();
            button.show();
            if (response.status == 1) {
              // L'utilisateur fait partie de cette communauté (admin, teacher ou membre approuvé)
              button.hide(); // Cacher le bouton join
              dashboardCommunityAppButton.show();
              
              // Stocker le rôle pour le clic sur Community App
              userRoleInThisSchool = response.role_in_this_school || response.user_role || 'student';
              
            } else {
              button.show();
              dashboardCommunityAppButton.hide();
              if (response.status == 0) {
                button.prop("disabled", true).text("<?php echo htmlspecialchars(get_phrase('pending')); ?>");
              } else if (response.status == 2) {
                // Si admin ou teacher d'une AUTRE communauté, montrer "join as a member"
                if (response.user_role === 'admin' || response.user_role === 'teacher') {
                  button.prop("disabled", false)
                        .text("<?php echo htmlspecialchars(get_phrase('join_as_member')); ?>")
                        .data("join-as-member", true);
                  // Garder l'action vers student/join_school
                  form.attr("action", base_url + "student/join_school/assigned/<?php echo $school_id; ?>");
                  userRoleHidden.val(response.user_role);
                } else {
                  button.prop("disabled", true).text("<?php echo htmlspecialchars(get_phrase('no_student_account')); ?>");
                }
              } else {
                button.prop("disabled", false).text("<?php echo htmlspecialchars(get_phrase('join_community')); ?>");

                $(".btn-outline-wayo-join, #paye-button").each(function() {
                  $(this).prop("disabled", false)
                    .text("<?php echo htmlspecialchars(get_phrase('join_community')); ?>")
                    .removeClass("btn-outline-wayo-join")
                    .addClass("btn-wayo");
                });

              }
            }
          }
        }
      });
    }
    
    // Gérer le clic sur le bouton Community App pour switcher vers cette communauté
    $("#dashboard-community-app-button").on("click", function(e) {
      e.preventDefault();
      
      var schoolId = "<?php echo $school_id; ?>";
      var role = userRoleInThisSchool || 'student';
      
      // Switcher vers cette communauté puis rediriger
      $.ajax({
        url: "<?php echo site_url('home/switch_community_role'); ?>",
        method: "POST",
        dataType: "json",
        data: {
          school_id: schoolId,
          role: role,
          <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>"
        },
        success: function(response) {
          if (response.status === 'success') {
            window.location.href = response.redirect_url;
          } else {
            // En cas d'erreur, rediriger quand même vers le dashboard
            window.location.href = "<?php echo route('dashboard'); ?>";
          }
        },
        error: function() {
          // En cas d'erreur, rediriger vers le dashboard
          window.location.href = "<?php echo route('dashboard'); ?>";
        }
      });
    });
    
    updateButton();
    setInterval(updateButton, 5000);
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const classCards = document.querySelectorAll('.class-card');
    const modal = document.getElementById('classModal');

    classCards.forEach(card => {
      card.querySelector('[data-bs-toggle="modal"]').addEventListener('click', function() {

        function formatDate(dateString) {
          const date = new Date(dateString);
          const options = {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          };
          return date.toLocaleDateString('fr-FR', options);
        }

        const mentorName = card.dataset.mentor || "—";
        // Mettre la première lettre de chaque mot en majuscule et le reste en minuscules
        const capitalizedMentor = mentorName
          .toLowerCase()
          .split(' ')
          .map(word => word.charAt(0).toUpperCase() + word.slice(1))
          .join(' ');

        document.getElementById('classMentor').textContent = capitalizedMentor;

        // Remplir les champs du modal
        modal.querySelector('.modal-title').textContent = card.dataset.title;
        
        // Utiliser une image par défaut si la photo n'existe pas
        const classPhoto = card.dataset.photo;
        if (classPhoto && classPhoto.trim() !== '' && classPhoto !== 'null') {
          modal.querySelector('#classPhoto').src = '<?php echo base_url("uploads/class/"); ?>' + classPhoto;
        } else {
          modal.querySelector('#classPhoto').src = '<?php echo base_url("uploads/communityCover/placeholder.jpg"); ?>';
        }

        // Dates
        const startText = '<?php echo get_phrase("From"); ?>';
        const endText = '<?php echo get_phrase("to"); ?>';

        const startDate = (card.dataset.start && card.dataset.start !== '0000-00-00' && card.dataset.start !== '') 
                  ? formatDate(card.dataset.start) 
                  : null;

        const endDate = (card.dataset.end && card.dataset.end !== '0000-00-00' && card.dataset.end !== '') 
                        ? formatDate(card.dataset.end) 
                        : null;

        if (startDate && endDate) {
          const startText = '<?php echo get_phrase("From"); ?>';
          const endText = '<?php echo get_phrase("to"); ?>';
          modal.querySelector('#classDates').parentElement.style.display = 'inline-block';
          modal.querySelector('#classDates').textContent = `${startText} ${startDate} ${endText} ${endDate}`;
        } else {
          modal.querySelector('#classDates').parentElement.style.display = 'none';
        }
        
        //modal.querySelector('#classDates').textContent = `${startText} ${startDate} ${endText} ${endDate}`;

        // Nombre max
        const nombre_max = card.dataset.free;
        modal.querySelector('#classFree').textContent = nombre_max + ' <?php echo get_phrase("Maximum_number"); ?>';

        // Prix avec TVA
        const classPrice = parseFloat(card.dataset.price) || 0;
        let displayPrice = 'Gratuit';
        if (classPrice > 0) {
          // Calculer le prix TTC pour la modal
          const vatRate = <?php echo $vat_rate; ?>;
          const classPriceTTC = vatRate > 0 ? classPrice * (1 + (vatRate / 100)) : classPrice;
          displayPrice = classPriceTTC.toFixed(2) + ' ' + card.dataset.currency + ' ' + card.dataset.cycle;
          if (vatRate > 0) {
            displayPrice += ' (TTC)';
          }
        }
        modal.querySelector('#classPrice').textContent = displayPrice;
      });
    });
  });
</script>
<script>
  // // CTA "S’inscrire" depuis la modal
  // modalApplyBtn?.addEventListener('click', () => {
  //   alert(`Inscription à “${modalTitle.textContent}” — ${modalPrice.textContent}`);
  //   bsModal?.hide();
  // });
</script>
