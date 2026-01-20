<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- App css -->
<link href="<?php echo base_url(); ?>assets/backend/css/icons.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/app-modern.min.css" rel="stylesheet" type="text/css" id="light-style" />
<link href="<?php echo base_url(); ?>assets/backend/css/app-modern-dark.min.css" rel="stylesheet" type="text/css" id="dark-style" />
<!-- App css End-->

<!-- third party css -->
<link href="<?php echo base_url(); ?>assets/backend/css/vendor/fullcalendar.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/vendor/buttons.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/vendor/select.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/vendor/summernote-bs4.css" rel="stylesheet" type="text/css" />
<!-- third party css end -->

<link href="<?php echo base_url(); ?>assets/backend/css/custom.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/backend/css/content-placeholder.css" rel="stylesheet" type="text/css" />

<style>
/* ========== MODERN LESSONS GLOBAL STYLES ========== */
:root {
    /* MONOCHROMATIC THEME (INDIGO) */
    --primary: #6366f1;
    --primary-light: #818cf8;
    --primary-lighter: #e0e7ff;
    --primary-dark: #4338ca;
    --secondary: #10b981;
    --bg-main: #f8fafc;
    --bg-card: #ffffff;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
    --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    --font-body: 'DM Sans', sans-serif;
    --font-header: 'Outfit', sans-serif;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
}

body {
    font-family: var(--font-body) !important;
    background: var(--bg-main) !important;
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-header) !important;
}

/* Modern Scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: var(--bg-main);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

/* Animations */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
}

.fade-up {
    animation: fadeUp 0.5s ease forwards;
}

.slide-in {
    animation: slideIn 0.3s ease forwards;
}

/* Selection */
::selection {
    background: var(--primary-lighter);
    color: var(--primary-dark);
}
</style>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/jquery-3.6.0.min.js"></script>
