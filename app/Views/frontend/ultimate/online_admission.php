<?php if (get_common_settings('recaptcha_status')): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<link rel="stylesheet" href="<?php echo base_url('assets/frontend/ultimate/css/flag-icons/flag-icons.min.css'); ?>">
<?php
$db = \Config\Database::connect();
?>

<style>
    /* ----------------- Hero ----------------- */

    .hero {
        position: relative;
        min-height: 35vh;
        display: grid;
        place-items: center;
        color: #fff;
        background-image: url('<?php echo base_url('uploads/images/decloedt/img/optimized/cover-wayo.webp'); ?>');
        background-size: cover;
        background-position: center;
    }
    
    /* AVIF pour navigateurs compatibles */
    @supports (background-image: url("test.avif")) {
        .hero {
            background-image: url('<?php echo base_url('uploads/images/decloedt/img/optimized/cover-wayo.avif'); ?>');
        }
    }

    .hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));
    }

    .hero .hero-content {
        position: relative;
        text-align: center;
    }

    .hero .lead {
        max-width: 760px;
        margin-inline: auto;
        color: #e9e9ef
    }

    .btn-pill {
        border-radius: 999px;
        font-weight: 700;
        padding: .8rem 1.2rem;
    }

    /* ====== Step 2 layout ====== */
    .panel--community .grid {
        display: grid;
        grid-template-columns: 1.15fr .85fr;
        gap: 24px;
    }

    @media (max-width: 992px) {
        .hero {
            position: relative;
            min-height: 50vh;
            display: grid;
            place-items: center;
            color: #fff;
            background-image: url('<?php echo base_url('uploads/images/decloedt/img/optimized/cover-wayo.webp'); ?>');
            background-size: cover;
            background-position: center;
        }
        
        @supports (background-image: url("test.avif")) {
            .hero {
                background-image: url('<?php echo base_url('uploads/images/decloedt/img/optimized/cover-wayo.avif'); ?>');
            }
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));
        }

        .hero .hero-content {
            position: relative;
            text-align: center;
        }

        .hero .lead {
            max-width: 760px;
            margin-inline: auto;
            color: #e9e9ef
        }

        .btn-pill {
            border-radius: 999px;
            font-weight: 700;
            padding: .8rem 1.2rem;
        }

        /* ====== Step 2 layout ====== */
        .panel--community .grid {
            grid-template-columns: 1fr;
        }
    }

    .panel--community fieldset {
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 18px;
        margin: 0 0 18px 0;
        background: #fff;
    }

    .panel--community legend {
        font-weight: 700;
        font-size: 14px;
        padding: 0 8px;
        color: #333;
    }

    .panel--community .stack>.field {
        margin-bottom: 14px;
    }

    #communityprice.bg-light {
        background-color: #f8f9fa;
        cursor: not-allowed;
        color: #777;
    }

    .alert {
        padding: 12px 14px;
        border-radius: 8px;
        font-size: .95rem;
    }

    .alert-warning {
        background: #fff7e6;
        border: 1px solid #ffe0a3;
        color: #7a4d00;
    }
     /* === NOUVEAU STYLE UNIFIÉ POUR TÉLÉPHONE === */
    .unified-phone-wrapper {
        display: flex;
        align-items: stretch;
        border: 1px solid #ECEEF3;
        border-radius: 12px;
        background: #f7f8fb;
        transition: all 0.3s ease;
        height: 52px; 
        /* overflow: hidden; Removed to allow dropdown visibility */
        position: relative;
    }

    .unified-phone-wrapper:focus-within {
        border-color: #ff6b35;
        background: #fff;
    }

    .unified-phone-wrapper.is-invalid {
        border-color: #dc3545 !important;
    }

    .unified-phone-wrapper .country-select-wrapper {
        width: 80px; 
        border-right: 1px solid #ECEEF3;
        background-color: rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        position: relative;
    }

    .custom-select-trigger {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.75rem;
        cursor: pointer;
        width: 100%;
    }

    .custom-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: auto;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 9999;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        margin-top: 4px;
        min-width: 80px;
    }

    .custom-options.open {
        display: block;
    }

    .custom-option {
        padding: 10px;
        cursor: pointer;
        text-align: center;
        transition: background 0.2s;
    }

    .custom-option:hover {
        background-color: #fff2ea;
        color: #ff6b35;
    }

    .custom-option.selected {
        background-color: #fff2ea;
        font-weight: bold;
    }

    .fi {
        font-size: 1.1em;
        line-height: 1em;
        border-radius: 3px;
    }

    .unified-phone-wrapper .form-control {
        border: none !important;
        box-shadow: none !important;
        background-color: transparent !important;
        height: 100% !important;
        padding: 10px 14px !important;
        font-size: 1rem;
    }

    .unified-phone-wrapper .form-control:focus {
        box-shadow: none !important;
        background-color: #fff !important;
    }

    /* Ajustement pour coller au style de la page online_admission */
    .unified-phone-wrapper {
        margin-top: 0;
    }

    /* onording popup */
     /* Overlay */
    .onb-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.5);
      backdrop-filter: blur(4px);
      opacity: 0;
      pointer-events: none;
      transition: opacity .3s ease;
      z-index: 99999;
    }
    .onb-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    /* Modal wrapper */
    .onb-modal {
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: none;
      z-index: 100000;
      padding: 1rem;
    }
    .onb-modal.active {
      pointer-events: auto;
    }

    /* Card */
    .onb-card {
      background: #fff;
      width: min(700px, 100%);
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      transform: translateY(20px) scale(0.98);
      opacity: 0;
      transition: all .3s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }
    .onb-modal.active .onb-card {
      transform: translateY(0) scale(1);
      opacity: 1;
    }

    /* Close */
    .onb-close {
      position: absolute;
      top: 16px;
      right: 16px;
      border: none;
      background: #f3f4f6;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      font-size: 16px;
      color: #374151;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background .2s, color .2s;
      z-index: 10;
    }
    .onb-close:hover {
      background: #e5e7eb;
      color: #111;
    }

    /* Header */
    .onb-header {
      padding: 1.5rem 2rem 1rem;
      border-bottom: 1px solid #f3f4f6;
    }
    .onb-brand {
      font-size: 1.25rem;
      font-weight: 800;
      color: #111827;
      margin-left: 0.5rem;
    }

    .onb-progress {
      height: 6px;
      background: #f3f4f6;
      border-radius: 10px;
      margin-top: 1.25rem;
      overflow: hidden;
    }
    .onb-progress-bar {
      height: 100%;
      width: 0%;
      background: #f47a1f;
      border-radius: 10px;
      transition: width .4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Body */
    .onb-body {
      padding: 2rem;
      min-height: 200px;
    }
    .onb-step {
      display: none;
      animation: fadeIn .4s ease;
    }
    .onb-step.is-active {
      display: block;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .onb-step h2 {
      font-size: 1.75rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1rem;
    }
    .onb-step p {
      font-size: 1.05rem;
      color: #4b5563;
      line-height: 1.6;
    }
    .onb-step ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        color: #4b5563;
    }

    /* Footer */
    .onb-footer {
      padding: 1.25rem 2rem;
      border-top: 1px solid #f3f4f6;
      background: #fff;
    }

    /* Buttons override */
    .btn-primary-custom, .btn-outline-primary-custom {
      padding: 0.75rem 1.5rem;
      font-weight: 700;
      font-size: 0.95rem;
      border-radius: 14px;
      transition: all 0.3s ease;
      letter-spacing: 0.01em;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    .btn-primary-custom {
      background: linear-gradient(135deg, #f47a1f, #ff9a56);
      border: none;
      color: white;
      box-shadow: 0 4px 15px rgba(244, 122, 31, 0.3);
    }
    
    /* Shiny effect for primary button */
    .btn-primary-custom::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.4s ease;
      pointer-events: none;
      z-index: 1;
    }
    .btn-primary-custom:hover::before {
      left: 100%;
    }
    
    .btn-primary-custom:hover {
      background: linear-gradient(135deg, #e06912, #f47a1f);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(244, 122, 31, 0.4);
      color:#fff;
    }

    .btn-outline-primary-custom {
      background: transparent;
      color: #f47a1f;
      border: 2px solid #f47a1f;
    }
    
    /* Shiny effect for outline button */
    .btn-outline-primary-custom::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: linear-gradient(135deg, #f47a1f, #ff9a56);
      transition: width 0.3s ease;
      z-index: -1;
    }
    .btn-outline-primary-custom:hover::before {
      width: 100%;
    }
    
    .btn-outline-primary-custom:hover {
      background: transparent;
      color: #fff;
      border-color: #f47a1f;
    }
    .btn-outline-primary-custom:disabled,
    .btn-primary-custom:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Responsive Mobile */
    @media (max-width: 576px) {
        .onb-card {
           width: 95%;
           margin: 10px;
        }
        
        .onb-header {
            padding: 1.25rem 1.5rem;
        }
        
        .onb-body {
            padding: 1.5rem;
        }

        .onb-footer {
            flex-direction: column-reverse; /* Ignorer en bas */
            gap: 12px;
            align-items: stretch !important; /* Pleine largeur */
        }

        .onb-footer .d-flex {
             width: 100%;
             display: flex;
             gap: 12px;
        }
        
        .onb-footer .d-flex .btn {
            flex: 1; /* Prev et Next prennent 50% chacun */
        }

        .onb-skip {
            width: 100%;
        }
    }
    /* ================= SUCCESS POPUP ================= */
.success-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(3, 7, 18, 0.6);
  backdrop-filter: blur(4px);
  z-index: 100001;

  display: flex;
  align-items: center;
  justify-content: center;

  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}

/* État visible */
.success-overlay.is-visible {
  opacity: 1;
  pointer-events: auto;
}

/* Card */
.success-card {
  background: #ffffff;
  width: min(500px, 90vw);
  padding: 3rem 2.5rem;
  border-radius: 30px;
  text-align: center;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);

  transform: scale(0.9) translateY(20px);
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Animation d’entrée */
.success-overlay.is-visible .success-card {
  transform: scale(1) translateY(0);
}

/* Icone */
.success-icon {
  width: 88px;
  height: 88px;
  margin: 0 auto 1.5rem;

  background: #fff5ec; /* light orange */
  color: #f47a1f;      /* orange */
  border-radius: 28px; /* Squircle */

  display: flex;
  align-items: center;
  justify-content: center;

  font-size: 2.5rem;
  transform: rotate(-10deg);
  animation: success-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

/* Titre */
.success-card h2 {
  margin: 0 0 0.75rem;
  font-size: 2rem;
  font-weight: 800;
  color: #1e1e4b;
}

/* Texte */
.success-card p {
  margin: 0 auto 2rem;
  font-size: 1.05rem;
  line-height: 1.5;
  color: #6b7280;
  max-width: 400px;
}

/* Info Box */
.success-info-box {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    text-align: left;
}

.success-info-icon {
    font-size: 1.5rem;
    color: #f47a1f;
    flex-shrink: 0;
    width: 24px;
    text-align: center;
}

.success-info-text {
    font-size: 0.95rem;
    color: #334155;
    line-height: 1.4;
    font-weight: 500;
}

.success-info-text strong {
    color: #0f172a;
    font-weight: 700;
}

/* Animation icône */
@keyframes success-pop {
  0% {
    transform: scale(0) rotate(-45deg);
    opacity: 0;
  }
  100% {
    transform: scale(1) rotate(-10deg);
    opacity: 1;
  }
}

/* Mobile */
@media (max-width: 480px) {
  .success-card {
    padding: 2rem 1.5rem;
    border-radius: 24px;
  }
  .success-card h2 {
    font-size: 1.75rem;
  }
}
    /* ================= SUMMARY REDESIGN ================= */
    .summary-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 1.5rem;
    }

    .summary-card {
        background: #fff;
        border: 1px solid #ECEEF3;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 1 1 300px; /* Allow growth, min-width 300px */
        min-width: 0; /* Prevent overflow from children */
        max-width: 100%;
    }

    .summary-card:hover {
        border-color: #f47a1f;
        box-shadow: 0 4px 12px rgba(244, 122, 31, 0.08);
    }

    .summary-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .summary-card-icon {
        width: 36px;
        height: 36px;
        background: #fff5ec;
        color: #f47a1f;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .summary-card-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e1e4b;
        margin: 0;
    }

    .summary-card-body {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #4b5563;
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        gap: 10px; /* Add gap to prevent label and value from Touching */
    }

    .summary-label {
        font-weight: 600;
        color: #64748b;
        margin-right: 12px;
        flex-shrink: 0; /* Prevent label from shrinking */
    }

    .summary-value {
        color: #1e293b;
        text-align: right;
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
        min-width: 0; /* Allow shrinking in flex */
    }

    .summary-media-row {
        display: flex;
        gap: 12px;
        margin-top: 0.5rem;
    }

    .summary-media-item {
        flex: 1;
        text-align: center;
    }

    .summary-media-item img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #f1f5f9;
    }

    .summary-media-item span {
        display: block;
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 4px;
    }

    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    /* ================= STEPPER PROGRESS ================= */
    .stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 40px;
        counter-reset: step;
    }

    .step-create-commaunaute {
        position: relative;
        flex: 1;
        text-align: center;
        z-index: 1;
    }

    /* Connecting Line (The "Border") */
    .step-create-commaunaute:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px; /* Half of circle height (40px) */
        left: 50%;
        width: 100%;
        height: 3px;
        background-color: #e0e0e0;
        z-index: -1;
        transform: translateY(-50%);
        transition: background-color 0.3s ease;
    }

    /* Circle */
    .step-create-commaunaute .num {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #e0e0e0;
        color: #9ca3af;
        font-weight: 700;
        transition: all 0.3s ease;
        position: relative; /* To sit on top of line */
        z-index: 2;
    }

    /* Label */
    .step-create-commaunaute .lbl {
        display: block;
        margin-top: 8px;
        font-size: 0.9rem;
        color: #9ca3af;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    /* ACTIVE STATE (Current Step) */
    .step-create-commaunaute.is-active .num {
        border-color: #f47a1f;
        color: #f47a1f;
        background-color: #fff5ec;
        box-shadow: 0 0 0 4px rgba(244, 122, 31, 0.1);
    }
    .step-create-commaunaute.is-active .lbl {
        color: #f47a1f;
        font-weight: 700;
    }

    /* COMPLETE STATE (Passed Steps) */
    .step-create-commaunaute.is-complete .num {
        background-color: #f47a1f;
        border-color: #f47a1f;
        color: #fff;
    }
    .step-create-commaunaute.is-complete .lbl {
        color: #f47a1f;
    }
    
    /* Coloring the line for completed steps */
    .step-create-commaunaute.is-complete::after {
        background-color: #f47a1f;
    }

    @media (max-width: 576px) {
        .step-create-commaunaute .lbl {
            font-size: 0.75rem;
        }
        .step-create-commaunaute .num {
            width: 32px;
            height: 32px;
            font-size: 0.9rem;
        }
        .step-create-commaunaute:not(:last-child)::after {
            top: 16px; /* Half of 32px */
        }
    }
    
    }
    /* ================= SUBSCRIPTION STEP ================= */
    .subscription-container {
        display: flex;
        justify-content: center;
        padding: 20px 0;
    }

    .subscription-card {
        background: #fff;
        border: 2px solid #f47a1f; /* Wayo Orange */
        border-radius: 20px;
        padding: 50px 30px 40px;
        max-width: 500px;
        width: 100%;
        text-align: center;
        position: relative;
        box-shadow: 0 10px 30px rgba(244, 122, 31, 0.1);
        margin-top: 15px;
    }
    
    .sub-header-badge {
        background-color: #111;
        color: #fff;
        padding: 10px 24px;
        border-radius: 99px;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .sub-title {
        font-size: 1.7rem;
        font-weight: 800;
        color: #111;
        margin-top: 15px;
        margin-bottom: 12px;
    }

    .sub-trial-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #e0fbf0; /* Light green */
        color: #0f9d58; /* Green */
        padding: 8px 18px;
        border-radius: 99px;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 20px;
    }

    .sub-price-big {
        font-size: 4rem;
        font-weight: 800;
        color: #f47a1f; /* Wayo Orange */
        line-height: 1;
        margin-bottom: 5px;
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 2px;
    }
    
    .sub-price-big small {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f47a1f;
    }

    .sub-price-sub {
        font-size: 1.2rem;
        font-weight: 800;
        color: #222;
        margin-bottom: 20px;
    }

    .sub-desc {
        font-size: 1rem;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
        max-width: 90%;
        margin-inline: auto;
    }

    .sub-features {
        text-align: left;
        list-style: none;
        padding: 0;
        margin: 0 0 30px 0;
    }

    .sub-features li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
        font-size: 1.05rem;
        color: #333;
        font-weight: 500;
    }

    .sub-features li i {
        color: #f47a1f;
        margin-top: 5px;
    }

    .sub-divider {
        height: 1px;
        background-color: #eee;
        margin: 25px 0;
        border: none;
    }

    .sub-footnotes {
        text-align: left;
        font-size: 0.85rem;
        color: #888;
        line-height: 1.5;
    }
    
    .sub-footnotes p {
        margin-bottom: 6px;
    }

    /* ================= LOADING OVERLAY ================= */
    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        z-index: 999999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .loading-overlay.is-visible {
        opacity: 1;
        visibility: visible;
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #f47a1f;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    .loading-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        text-align: center;
    }

    .loading-subtext {
        font-size: 0.9rem;
        color: #666;
        margin-top: -0.5rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* ================= UPLOADER DIFFERENTIATION ================= */
    /* Logo uploader - petit et carré */
    .uploader[data-kind="logo"] {
        max-width: 180px;
        height: 180px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        margin: 0 auto;
    }

    .uploader[data-kind="logo"] .uploader-content {
        gap: 6px;
    }

    .uploader[data-kind="logo"] .uploader-content i {
        font-size: 1.4rem;
    }

    .uploader[data-kind="logo"] .uploader-content p {
        font-size: 0.8rem;
    }

    .uploader[data-kind="logo"] .preview {
        inset: 8px;
    }

    .uploader[data-kind="logo"] .preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    /* Cover uploader - grand et rectangulaire */
    .uploader[data-kind="cover"] {
        min-height: 160px;
        padding: 1.5rem;
    }

    .uploader[data-kind="cover"] .preview img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>

<main class="bg-light">
    <!-- HERO -->
    <section class="hero">
        <div class="container hero-content py-5" data-animate>
            <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Create_a_community") ?></h1>
            <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Start_with_your_profile") ?></p>
        </div>
    </section>
    <section class="">
        <div class="container py-5 mb-5">
            <!-- Stepper -->
            <ol class="stepper" role="list" aria-label="<?php echo get_phrase('Steps'); ?>" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
                <li class="step-create-commaunaute is-active" data-stepnav="1"><span class="num">1</span><span class="lbl"><?php echo get_phrase("Profile") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="2"><span class="num">2</span><span class="lbl"><?php echo get_phrase("Community") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="3"><span class="num">3</span><span class="lbl"><?php echo get_phrase("subscription") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="4"><span class="num">4</span><span class="lbl"><?php echo get_phrase("Summary") ?></span></li>
            </ol>

            <form action="<?php echo site_url('admission/online_admission/submit/school'); ?>" method="post" id="schoolform"
                  class="js-validate studentform realtime-form container" enctype="multipart/form-data" novalidate>
                <!-- Champ caché pour le jeton CSRF et données manquantes -->
                <input type="hidden" id="schoolform_csrf" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />
                <input type="hidden" name="school_phone" id="school_phone_hidden">
                <input type="hidden" name="currency" id="currency_hidden" value="MAD">

                <!-- STEP 1 : PROFIL -->
                <section class="card panel step-pane is-visible" data-step="1" aria-labelledby="title-step1" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
                    <div class="panel-head">
                        <h2 id="title-step1"><?php echo get_phrase("Profile_creation") ?></h2>
                        <span class="legend-required"><span class="req">*</span> <?php echo get_phrase("Required_fields") ?></span>
                    </div>

                    <div class="grid-2 mt-4">
                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Name") ?> <span class="req">*</span></span>
                            <input id="profileName" type="text" placeholder="<?php echo get_phrase('Name'); ?>"
                                   class="form-control shadow-none rounded-end text-capitalize" name="name" required
                                   data-msg="<?php echo get_phrase("Please enter your name") ?>" data-error-class="u-has-error"
                                   data-success-class="u-has-success" aria-required="true" autocomplete="name">
                            <div class="error" data-for="profileName"></div>
                        </label>

                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Email") ?> <span class="req">*</span></span>
                            <input id="profileEmail" type="email" placeholder="<?php echo get_phrase('email'); ?>"
                                   class="form-control rounded-end shadow-none" name="email" required aria-required="true" autocomplete="email"
                                   data-msg="<?php echo get_phrase("Please enter a valid email address") ?>" data-error-class="u-has-error"
                                   data-success-class="u-has-success">
                            <div class="error" data-for="profileEmail"></div>
                        </label>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <span class="field-label"><?php echo get_phrase("Phone") ?> <span class="req">*</span></span>
                            <div class="unified-phone-wrapper">
                                <div class="country-select-wrapper">
                                   <?php include 'partials/countrySelect.php'; ?>
                                </div>
                                <input id="profilePhone" type="tel"
                                       class="form-control shadow-none" name="phone"
                                       data-msg="<?php echo get_phrase("Please enter a valid phone number") ?>"
                                       data-error-class="u-has-error" data-success-class="u-has-success"
                                       required aria-required="true" autocomplete="tel">
                            </div>
                            <div class="error" data-for="profilePhone"></div>
                        </div>
                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Primary_language") ?> <span class="req">*</span></span>
                            <?php $active_lang = get_user_language(); ?>
                            <select id="communityLang" name="communityLang" required aria-required="true"
                                    data-msg="<?php echo get_phrase('Please_select_a_language'); ?>">
                                <option value="french" <?php if($active_lang == 'french') echo 'selected'; ?>>Français</option>
                                <option value="english" <?php if($active_lang == 'english') echo 'selected'; ?>>English</option>
                                <option value="dutch" <?php if($active_lang == 'dutch') echo 'selected'; ?>>Nederlands</option>
                                <option value="arabe" <?php if($active_lang == 'arabic') echo 'selected'; ?>>العربية</option>
                                <option value="spanish" <?php if($active_lang == 'spanish') echo 'selected'; ?>>Español</option>
                            </select>
                            <div class="error" data-for="communityLang"></div>
                        </label>
                    </div>

                    <div class="grid-2">
                        <?php
                            $is_rtl = (get_user_language() === 'arabic');
                            $input_padding_style = $is_rtl 
                                ? 'width:100%; padding-left: 45px; box-sizing: border-box;' 
                                : 'width:100%; padding-right: 45px; box-sizing: border-box;';
                            
                            $icon_pos_style = $is_rtl
                                ? 'position:absolute; left:40px; top:18px; cursor:pointer; color:#6b7280; font-size:15px; transition: color 0.2s; z-index: 100; text-decoration: none; border: none; background: transparent;'
                                : 'position:absolute; right:40px; top:18px; cursor:pointer; color:#6b7280; font-size:15px; transition: color 0.2s; z-index: 100; text-decoration: none; border: none; background: transparent;';
                        ?>

                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Password") ?> <span class="req">*</span></span>
                            <div style="position:relative; width: 100%;">
                                <input id="profilePass" type="password" placeholder="********" required minlength="8" pattern=".{8,}"
                                    aria-required="true" autocomplete="new-password"
                                    class="form-control rounded-end shadow-none" name="password"
                                    data-msg="<?php echo get_phrase("Please enter a password with at least 8 characters") ?>"
                                    data-error-class="u-has-error" data-success-class="u-has-success"
                                    style="<?php echo $input_padding_style; ?>">
                                <i class="fa-regular fa-eye-slash" onclick="toggleAdmissionPassword('profilePass', this, event)" style="<?php echo $icon_pos_style; ?>"></i>
                            </div>
                            <div class="error" data-for="profilePass" style="margin-top: 45px;"></div>
                        </label>

                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Confirm_password") ?> <span class="req">*</span></span>
                            <div style="position:relative; width: 100%;">
                                <input id="profilePass2" type="password" placeholder="********" required minlength="8" pattern=".{8,}"
                                    aria-required="true" autocomplete="new-password" class="form-control rounded-end shadow-none"
                                    name="repeat-password"
                                    data-msg="<?php echo get_phrase("Please repeat your password (min. 8 characters)") ?>"
                                    data-error-class="u-has-error" data-success-class="u-has-success"
                                    style="<?php echo $input_padding_style; ?>">
                                <i class="fa-regular fa-eye-slash" onclick="toggleAdmissionPassword('profilePass2', this, event)" style="<?php echo $icon_pos_style; ?>"></i>
                            </div>
                            <div class="error" data-for="profilePass2" style="margin-top: 45px;"></div>
                        </label>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev" disabled><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next" disabled><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>

                <!-- STEP 2 : COMMUNAUTÉ -->
                <section class="card panel step-pane panel--community" data-step="2" aria-labelledby="title-step2" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
                    <div class="panel-head">
                        <h2 id="title-step2"><?php echo get_phrase("Community_details") ?></h2>
                        <span class="legend-required"><span class="req">*</span> <?php echo get_phrase("Required_fields") ?></span>
                    </div>
                    <div class="grid mt-4">
                        <!-- Colonne gauche -->
                        <div class="stack">

                            <!-- Identité -->
                            <fieldset>
                                <legend><?php echo get_phrase("Identity"); ?></legend>

                                <label class="field">
                                    <span class="field-label"><?php echo get_phrase("I_am") ?> <span class="req">*</span></span>
                                    <select id="i_am_id" name="i_am" class="form-control shadow-none" required
                                            data-msg="<?php echo get_phrase("Please select your status") ?>">
                                        <option value=""><?php echo get_phrase('select_a_status'); ?></option>
                                        <option value="Entreprise"><?php echo get_phrase("Entreprise") ?></option>
                                        <option value="Freelancer"><?php echo get_phrase("Freelancer") ?></option>
                                        <option value="Autoentrepreneur"><?php echo get_phrase("Autoentrepreneur") ?></option>
                                        <option value="Particulier"><?php echo get_phrase("Particulier") ?></option>
                                    </select>
                                    <div class="error" data-for="i_am_id"></div>
                                </label>

                                <div class="grid-2">
                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Tax_residence") ?> <span class="req">*</span></span>
                                        <select id="Tax_residence" name="Tax_residence" class="form-control shadow-none" required
                                                data-msg="<?php echo get_phrase("Please select your tax residence") ?>">
                                            <option value=""><?php echo get_phrase('select_a_Tax_residence'); ?></option>
                                            <option value="MA"><?php echo get_phrase("Morocco") ?></option>
                                            <option value="UAE"><?php echo get_phrase("United_Arab_Emirates") ?></option>
                                        </select>
                                        <div class="error" data-for="Tax_residence"></div>
                                    </label>

                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Category") ?> <span class="req">*</span></span>
                                        <select id="communityCat" name="category" class="form-control shadow-none" required
                                                data-msg="<?php echo get_phrase("please_select_a_category"); ?>">
                                            <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                                            <?php $categories = $db->table('categories')->get()->getResultArray(); ?>
                                            <?php foreach ($categories as $categorie): ?>
                                                <option value="<?php echo htmlspecialchars($categorie['name']); ?>"><?php echo get_phrase(htmlspecialchars($categorie['name'])); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="error" data-for="communityCat"></div>
                                    </label>
                                </div>
                            </fieldset>

                            <!-- Contenu -->
                            <fieldset>
                                <legend><?php echo get_phrase("Content"); ?></legend>

                                <label class="field">
                                    <span class="field-label"><?php echo get_phrase("Community_name") ?> <span class="req">*</span></span>
                                    <input id="communityName" type="text" maxlength="80"
                                           placeholder="<?php echo get_phrase("Ex._Digital_Marketing") ?>"
                                           class="form-control shadow-none rounded-end text-capitalize"
                                           name="school_name" required
                                           data-msg="<?php echo get_phrase("Please enter your first name") ?>">
                                    <small class="help"><?php echo get_phrase("Max._80_characters") ?></small>
                                    <div class="error" data-for="communityName"></div>
                                </label>

                                <label class="field">
                                    <span class="field-label">
                                        <?php echo get_phrase("Description") ?> <span class="req">*</span>
                                        <span class="info" data-tooltip="<?php echo get_phrase("Goal_in_one_sentence_•_2)_For_whom_•_3)_Benefits_(3–5)_•_4)_Pace/rules._Min._40_characters.") ?>">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </span>
                                    <textarea id="communityDesc" class="form-control shadow-none" rows="6" name="school_description"
                                              placeholder="<?php echo get_phrase("Describe_the_goal_and_the_value…") ?>" required
                                              data-msg="<?php echo get_phrase("Please enter a description") ?>"></textarea>
                                    <div style="margin-top: 10px; width: 100%;">
                                        <small class="help" style="margin: 0; color: #6b7280; font-size: 0.85rem; display: block;">
                                            <?php echo get_phrase("At_least_40_characters.") ?>
                                        </small>
                                        <div style="margin-top: 10px; display: flex; gap: 5px; width:100%;">
                                            <span id="charCount" style="font-weight: 800; color: #dc3545; font-size: 1rem; line-height: 1;">0</span>
                                            <small class="help" style="margin: 0; color: #9ca3af; font-size: 0.8rem;">
                                                <?php echo get_phrase("characters"); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="error" data-for="communityDesc"></div>
                                </label>
                            </fieldset>

                            <!-- Visibilité -->
                            <label class="switch emph">
                                <input type="hidden" name="visibility" value="0">
                                <input id="isPrivate_id" name="visibility" type="checkbox" value="1" />
                                <span class="switch-emph">
                                    <i class="fa-solid fa-lock"></i>
                                    <strong><?php echo get_phrase("Private_community") ?></strong>
                                    <em><?php echo get_phrase("(access_upon_approval)") ?></em>
                                    <span class="info field-label"
                                          data-tooltip="<?php echo get_phrase("(A_private_community_means_that_access_is_not_open._Interested_people_will_need_to_send_a_request_for_access._The_administrator_can_accept_or_reject_it.)") ?>">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                </span>
                            </label>
                        </div>

                        <!-- Colonne droite -->
                        <div class="stack">
                            <fieldset>
                                <legend><?php echo get_phrase("Media"); ?></legend>

                                <!-- Logo -->
                                <span class="field-label">
                                    <?php echo get_phrase("Logo (1:1)") ?>
                                    <span class="info"
                                          data-tooltip="<?php echo get_phrase("recommended_resolution"); ?>: 512×512 px • PNG/JPG/GIF/WebP • <?php echo get_phrase("animated_gifs_will_be_converted_to_static"); ?>">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                </span>
                                <div class="uploader" data-kind="logo" style="margin-bottom: 20px;">
                                    <input id="communityLogo" type="file" name="school_image" accept="image/*">
                                    <div class="uploader-content">
                                        <i class="fa-solid fa-upload"></i>
                                        <p><?php echo get_phrase("Upload_or_drag_a_logo") ?></p>
                                    </div>
                                    <div class="preview" id="logoPreview"></div>
                                    <div class="error" data-for="communityLogo"></div>
                                </div>

                                <!-- Cover -->
                                <span class="field-label">
                                    <?php echo get_phrase("cover_(16:5)") ?>
                                    <span class="info"
                                          data-tooltip="<?php echo get_phrase("recommended_resolution"); ?>: 1920×600 px • PNG/JPG/GIF/WebP • <?php echo get_phrase("animated_gifs_will_be_converted_to_static"); ?>">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                </span>
                                <div class="uploader" data-kind="cover">
                                    <input type="file" id="communityCover" name="communityCover" accept="image/*">
                                    <div class="uploader-content">
                                        <i class="fa-solid fa-image"></i>
                                        <p><?php echo get_phrase("Upload_or_drag_a_cover_photo") ?></p>
                                    </div>
                                    <div class="preview" id="coverPreview"></div>
                                    <div class="error" data-for="communityCover"></div>
                                </div>
                            </fieldset>

                            <!-- Adresse -->
                            <fieldset class="address-zone">
                                <legend><?php echo get_phrase("Address"); ?></legend>

                                <div class="grid-2" style="grid-template-columns: 66% 30%; gap: 14px;">
                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Rue") ?> <span class="req">*</span></span>
                                        <input id="communityStreet" type="text" placeholder="<?php echo get_phrase("Rue") ?>"
                                               class="form-control shadow-none" name="street" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer la rue") ?>">
                                        <div class="error" data-for="communityStreet"></div>
                                    </label>

                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Numéro") ?> <span class="req">*</span></span>
                                        <input id="communityNumber" type="text" placeholder="<?php echo get_phrase("Numéro") ?>"
                                               class="form-control shadow-none" name="number" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer le numéro") ?>">
                                        <div class="error" data-for="communityNumber"></div>
                                    </label>
                                </div>

                                <div class="grid-2" style="grid-template-columns: 50% 50%; gap: 14px;">
                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Ville") ?> <span class="req">*</span></span>
                                        <input id="communityCity" type="text" placeholder="<?php echo get_phrase("Ville") ?>"
                                               class="form-control shadow-none" name="city" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer la ville") ?>">
                                        <div class="error" data-for="communityCity"></div>
                                    </label>

                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("code_postal") ?> <span class="req">*</span></span>
                                        <input id="communityPostalCode" type="text" placeholder="<?php echo get_phrase("code_postal") ?>"
                                               class="form-control shadow-none" name="postal_code" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer le postal code") ?>">
                                        <div class="error" data-for="communityPostalCode"></div>
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next" disabled><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>

                <!-- STEP 3 : PRICE REMOVED -->

                <!-- STEP 3 : SUBSCRIPTION -->
                <!-- STEP 3 : SUBSCRIPTION -->
                <section class="card panel step-pane" data-step="3" aria-labelledby="title-step4" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
                    <div class="panel-head">
                        <h2 id="title-step4"><?php echo get_phrase("subscription") ?></h2>
                    </div>

                    <div class="subscription-container">
                        <div class="subscription-card">
                            <div class="sub-header-badge"><?php echo get_phrase('unique_plan_total_access'); ?></div>
                            
                            <h3 class="sub-title"><?php echo get_phrase('Wayo_Creator_Pro'); ?></h3>
                            
                            <?php if (community_billing_enabled()) : ?>
                            <div class="sub-trial-badge">
                                <i class="fa-solid fa-gift"></i> <?php echo get_phrase('14_day_free_trial'); ?>
                            </div>
                            
                            <div class="sub-price-big plan-price-split">
                                <span class="price-value"></span> <small><span class="price-currency"> </span><?php echo get_phrase('/_month'); ?></small>
                            </div>
                            
                            <p class="sub-desc">
                                <?php echo get_phrase('you_benefit_from_a_14_day_free_trial_to_test_all_features'); ?>
                            </p>
                            <?php else : ?>
                            <div class="sub-trial-badge">
                                <i class="fa-solid fa-gift"></i> <?php echo get_phrase('community_is_free_for_now'); ?>
                            </div>
                            <p class="sub-desc">
                                <?php echo get_phrase('no_payment_is_required_for_now'); ?>
                            </p>
                            <?php endif; ?>

                            <ul class="sub-features">
                                <li><i class="fa-solid fa-check"></i> <?php echo get_phrase('unlimited_classes_&_trainings'); ?></li>
                                <li><i class="fa-solid fa-check"></i> <?php echo get_phrase('unlimited_members'); ?></li>
                                <li><i class="fa-solid fa-check"></i> <?php echo get_phrase('monetize_your_community_now'); ?></li>
                                <li><i class="fa-solid fa-check"></i> <?php echo get_phrase('3%_commission_excluding_tax_the_cheapest_on_the_market*'); ?></li>
                                <li><i class="fa-solid fa-check"></i> <?php echo get_phrase('monetization_tools_via_cash_payment**'); ?></li>
                            </ul>
                            
                            <hr class="sub-divider">
                            
                            <div class="sub-footnotes">
                                <p><?php echo get_phrase('*_excluding_payment_processor_commission'); ?></p>
                                <p><?php echo get_phrase('**_integration_with_local_partner_for_cash_payment'); ?></p>
                            </div>

                            <!-- Selected plan hidden input -->
                            <input type="hidden" name="communityPriceType" value="oneoff">
                        </div>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next"><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>

                <!-- STEP 4 : RÉSUMÉ -->
                <section class="card panel step-pane" data-step="4" aria-labelledby="title-step5" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
                    <div class="panel-head">
                        <h2 id="title-step5"><?php echo get_phrase("Summary_&_publishing") ?></h2>
                    </div>

                    <div class="summary">
                        <p><?php echo get_phrase("Check_your_information_and_click_on") ?> <strong><?php echo get_phrase("Publish") ?></strong>.</p>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="submit" id="submitBtnSchool"
                                class="btn btn btn-primary text-uppercase submit-button"><?php echo get_phrase('Submit'); ?></button>
                        <button type="reset" id="resetBtn" style="display: none;"></button>
                    </div>
                </section>
            </form>
        </div>
    </section>
    <!-- Overlay -->
    <?php include 'partials/onboarding_community.php'; ?>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" aria-hidden="true">
        <div class="loading-spinner"></div>
        <div class="loading-text"><?php echo get_phrase("Processing_your_request"); ?>...</div>
        <div class="loading-subtext"><?php echo get_phrase("Please_wait"); ?></div>
    </div>

    <!--success overlay-->
<div id="successOverlay" class="success-overlay" aria-hidden="true">
        <div class="success-card">
            <div class="success-icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h2><?php echo get_phrase("Request_sent") ?>!</h2>
            <p><?php echo get_phrase("Your_profile_and_your_community_have_been_successfully_created_on_Wayo.") ?></p>

            <div class="success-info-box">
                <div class="success-info-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="success-info-text">
                    <?php echo get_phrase("You_will_receive_a_validation_email_within_a_maximum_of") ?> <strong><?php echo get_phrase("24 hours.") ?></strong> <?php echo get_phrase("to confirm your registration.") ?>
                </div>
            </div>

            <button id="successBtn" type="button" class="btn btn-primary-custom w-100"><?php echo get_phrase("I_understand") ?></button>
        </div>
    </div>
    
</main>

<script src="<?php echo base_url('assets/frontend/ultimate/vendor/jquery/dist/jquery.min.js'); ?>"></script>

<script>
       // Mettre à jour les prix affichés
  async function updatePrices() {
    try {
      // Détection du pays de l'utilisateur
      const response = await fetch('https://api.country.is/');
      const data = await response.json();
      const country = data.country;

      // Définition des prix fixes
      const prices = {
        'MA': {
          value: 790,
          currency: 'DH'
        },
        'AE': {
          value: 299, 
          currency: 'AED'
        }
      };

      // Fallback par défaut (Maroc)
      const priceData = prices[country] || prices['MA'];

      // Mise à jour des éléments prix
      const priceElements = document.querySelectorAll('.plan-price-split');

      priceElements.forEach(element => {
        const priceValue = element.querySelector('.price-value');
        const priceCurrency = element.querySelector('.price-currency');

        if (priceValue) priceValue.textContent = priceData.value;
        if (priceCurrency) priceCurrency.textContent = priceData.currency;
      });

    } catch (error) {
      console.error('Error updating prices:', error);
    }
  }
</script>

<!-- Script principal unifié & optimisé -->
<script>
function toggleAdmissionPassword(inputId, icon, e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const input = document.getElementById(inputId);
  if (input && icon) {
    const isPassword = input.getAttribute("type") === "password";
    input.setAttribute("type", isPassword ? "text" : "password");
    if (isPassword) {
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
      icon.style.color = "#F47A1F"; // Orange
    } else {
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
      icon.style.color = "#6b7280"; // Gray
    }
  }
}

    
document.addEventListener('DOMContentLoaded', function() {
    // Appeler la fonction de mise à jour des prix
    updatePrices();

    // ========================
    // Helpers
    // ========================
    const $  = (s, ctx = document) => ctx.querySelector(s);
    const $$ = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));

    if (window.toastr) {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000
        };
    }

    const form            = $('#schoolform');
    const panes           = $$('.step-pane');
    const steps           = $$('.step-create-commaunaute');
    const iAmSelect       = $('#i_am_id');
    const taxResSelect    = $('#Tax_residence');
    const currencyHint    = $('#currencyHint');
    const currencyCodeEl  = $('#currencyCode');
    const logoInput       = $('#communityLogo');
    const coverInput      = $('#communityCover');
    const logoPreview     = $('#logoPreview');
    const coverPreview    = $('#coverPreview');
    const privateToggle   = $('#isPrivate_id');

    let currentStep = 0;
    // lastEmail and lastSchool removed to force re-check

    // ========================
    // Config images (recommandations uniquement, pas de restrictions)
    // La compression est gérée côté serveur
    // ========================
    async function validateImage(file, type) {
        if (!file) return { valid: true };

        // Vérifier uniquement que c'est une image valide
        return new Promise(resolve => {
            const img = new Image();
            img.onload = () => {
                URL.revokeObjectURL(img.src);
                resolve({ valid: true });
            };
            img.onerror = () => {
                URL.revokeObjectURL(img.src);
                resolve({ valid: false, msg: '<?php echo get_phrase("image_corrupted_or_invalid_format"); ?>' });
            };
            img.src = URL.createObjectURL(file);
        });
    }

    function showImageError(previewIdOrEl, msg) {
        let el = previewIdOrEl;
        if (typeof previewIdOrEl === 'string') {
            el = document.getElementById(previewIdOrEl);
        }
        if (!el) return;
        el.innerHTML = `<div class="text-danger small p-3 text-center bg-light border rounded">${msg}</div>`;
    }

    // ========================
    // Duplication email / nom communauté
    // ========================
    function getSchoolFormCsrfInput() {
        return document.getElementById('schoolform_csrf');
    }

    function checkDuplication(type, value, input) {
        if (!value.trim()) return;

        const formData = new FormData();
        formData.append('type',  type);
        formData.append('value', value);
        const csrfInput = getSchoolFormCsrfInput();
        if (csrfInput) {
            formData.append(csrfInput.name, csrfInput.value);
        }

        fetch('<?= site_url('admission/post-check-duplication-ajax'); ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(async r => {
            const raw = await r.text();
            if (!raw) return { available: true };
            try { return JSON.parse(raw); } 
            catch (err) { console.warn('JSON Error', raw); return { available: true }; }
        })
        .then(data => {
            if (data && data.csrf) {
                const csrfInput = getSchoolFormCsrfInput();
                if (csrfInput) {
                    csrfInput.name = data.csrf.csrfName;
                    csrfInput.value = data.csrf.csrfHash;
                }
            }
            const errorEl = $(`.error[data-for="${input.id}"]`);
            
            if (!data.available) {
                // Email is taken
                if (errorEl) {
                    errorEl.textContent = data.message;
                    errorEl.style.display = 'block'; 
                }
                input.classList.add('is-invalid');
                input.setAttribute('aria-invalid', 'true');
                input.setAttribute('data-duplicate', 'true');
            } else {
                // Email is free
                // Only clear if no other validation errors exist (e.g. format)
                if (input.validity.valid) {
                     input.classList.remove('is-invalid');
                     input.setAttribute('aria-invalid', 'false');
                     if (errorEl) errorEl.style.display = 'none';
                }
                input.removeAttribute('data-duplicate');
            }
            updateContinueButton();
        })
        .catch(err => {
            console.error(err);
            input.removeAttribute('data-duplicate');
            updateContinueButton();
        });
    }

    $('#profileEmail')?.addEventListener('blur', () => {
        checkDuplication('email', $('#profileEmail').value, $('#profileEmail'));
    });
    $('#communityName')?.addEventListener('blur', () => {
        checkDuplication('school_name', $('#communityName').value, $('#communityName'));
    });

    // ========================
    // Currency (Tax residence)
    // ========================
    function updateCurrencyUI() {
        if (!taxResSelect) return;
        const sel  = taxResSelect.value || '';
        const code = sel === 'UAE' ? 'AED' : sel === 'EUR' ? 'EUR' : 'MAD';

        if (currencyHint)   currencyHint.textContent = code;
        if (currencyCodeEl) currencyCodeEl.value     = code;
    }

    taxResSelect?.addEventListener('change', () => {
        updateCurrencyUI();
        updateContinueButton();
    });

    // ========================
    // Validation de base des champs required
    // ========================
    function setInvalid(el, msg) {
        if (!el) return;
        el.classList.add('is-invalid');
        // Handle phone wrapper
        if (el.id === 'profilePhone') {
            el.closest('.unified-phone-wrapper')?.classList.add('is-invalid');
        }
        const err = $(`.error[data-for="${el.id}"]`);
        if (err) {
            err.textContent = msg || el.dataset.msg || '';
            err.style.setProperty('display', 'block', 'important');
        }
    }

    function clearInvalid(el) {
        if (!el) return;
        el.classList.remove('is-invalid');
         // Handle phone wrapper
        if (el.id === 'profilePhone') {
            el.closest('.unified-phone-wrapper')?.classList.remove('is-invalid');
        }
        const err = $(`.error[data-for="${el.id}"]`);
        if (err) {
            err.textContent = '';
            err.style.setProperty('display', 'none', 'important');
        }
    }

    $$('input[required], select[required], textarea[required]').forEach(el => {
        const update = () => {
            const isEmpty = !el.value.trim();
            if (isEmpty) {
                setInvalid(el);
            } else {
                clearInvalid(el);
            }
            updateContinueButton();
        };

        el.addEventListener('blur', update);
        if (el.tagName === 'SELECT') {
            el.addEventListener('change', update);
        }
        
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            el.addEventListener('input', () => {
                if (el.classList.contains('is-invalid') && el.value.trim()) {
                    clearInvalid(el);
                }
                updateContinueButton();
            });
        }
    });

    // Private toggle listener to update summary/buttons
    privateToggle?.addEventListener('change', () => {
        updateContinueButton();
        updateSummary();
    });

    // Character counter logic
    const descInput = $('#communityDesc');
    const countSpan = $('#charCount');
    if (descInput && countSpan) {
        const updateCharCount = () => {
            const count = descInput.value.length;
            countSpan.textContent = count;
            if (count < 40) {
                countSpan.style.color = '#dc3545';
            } else {
                countSpan.style.color = '#28a745';
            }
        };
        ['input', 'change', 'keyup', 'focus'].forEach(ev => {
            descInput.addEventListener(ev, updateCharCount);
        });
        updateCharCount(); // Initial count
    }

    // ========================
    // Validation step par step
    // ========================
    function validateStep(index) {
        const pane = panes[index];
        if (!pane) return true;

        let ok = true;

        // Blocage si doublons
        if ($$('[data-duplicate="true"]').length > 0) {
            toastr?.warning('<?= get_phrase("please_correct_duplicate_fields"); ?>');
            return false;
        }

        // Required
        pane.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                setInvalid(field);
                ok = false;
            }
        });

        const stepId = pane.dataset.step;

        // Step 1
        if (stepId === '1') {
            const name  = $('#profileName');
            const email = $('#profileEmail');
            const phone = $('#profilePhone');
            const pass  = $('#profilePass');
            const pass2 = $('#profilePass2');

            if (!name.value.trim() || name.value.trim().length < 2) {
                setInvalid(name); ok = false;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                setInvalid(email); ok = false;
            }

            if ((phone.value || '').replace(/\D/g, '').length < 8) {
                setInvalid(phone); ok = false;
            }

            if ((pass.value || '').length < 8) {
                setInvalid(pass); ok = false;
            }

            if (pass.value && !/[A-Z]/.test(pass.value)) {
                setInvalid(pass, '<?php echo get_phrase("password_must_contain_at_least_one_uppercase_letter"); ?>'); ok = false;
            }

            if (pass.value && !/[a-z]/.test(pass.value)) {
                setInvalid(pass, '<?php echo get_phrase("password_must_contain_at_least_one_lowercase_letter"); ?>'); ok = false;
            }

            if (pass.value && !/[0-9]/.test(pass.value)) {
                setInvalid(pass, '<?php echo get_phrase("password_must_contain_at_least_one_number"); ?>'); ok = false;
            }

            if (pass.value && !/[!@#$%^&*(),.?":{}|<>]/.test(pass.value)) {
                setInvalid(pass, '<?php echo get_phrase("password_must_contain_at_least_one_special_character"); ?>'); ok = false;
            }

            if (pass.value !== pass2.value) {
                setInvalid(pass2); ok = false;
            }
        }

        // Step 2
        if (stepId === '2') {
            const desc   = $('#communityDesc');
            const number = $('#communityNumber');
            const postal = $('#communityPostalCode');

            if (desc.value.trim().length < 40) {
                setInvalid(desc); ok = false;
            }

            if (!/^\d+$/.test(number.value.trim())) {
                setInvalid(number); ok = false;
            }

            if (!/^\d{4,5}$/.test(postal.value.trim())) {
                setInvalid(postal); ok = false;
            }

            // Images
            const logoFile  = logoInput?.files[0];
            const coverFile = coverInput?.files[0];

            if (logoFile && logoInput.dataset.valid !== 'true') {
                ok = false;
                showImageError(logoPreview, '<?php echo get_phrase("Invalid_logo_size_or_ratio"); ?>');
                toastr?.error('<?php echo get_phrase("Please_upload_a_valid_logo_before_continuing"); ?>');
            }

            if (coverFile && coverInput.dataset.valid !== 'true') {
                ok = false;
                showImageError(coverPreview, '<?php echo get_phrase("Invalid_cover_size_or_ratio"); ?>');
                toastr?.error('<?php echo get_phrase("Please_upload_a_valid_cover_before_continuing"); ?>');
            }
        }

        if (stepId === '3') {
           // Step 3 is now Subscription - no custom validation logic needed yet
        }

        if (!ok) {
            toastr?.warning('<?= get_phrase("Please_correct_the_errors"); ?>');
        }

        return ok;
    }

    // ========================
    // Continue button
    // ========================
    function updateContinueButton() {
        const pane   = $('.step-pane.is-visible');
        if (!pane) return;
        const nextBtn = pane.querySelector('.next');
        if (!nextBtn) return;

        const hasDup        = $$('[data-duplicate="true"]').length > 0;
        const invalidInPane = pane.querySelectorAll('.is-invalid').length > 0;

        let imagesOk = true;
        if (pane.dataset.step === '2') {
            const logoFile  = logoInput?.files[0];
            const coverFile = coverInput?.files[0];

            if (logoFile  && logoInput.dataset.valid  !== 'true') imagesOk = false;
            if (coverFile && coverInput.dataset.valid !== 'true') imagesOk = false;
        }

        let priceOk = true;
        // Step 3 price check removed

        const disabled = hasDup || invalidInPane || !imagesOk || !priceOk;
        nextBtn.disabled = disabled;
        nextBtn.classList.toggle('opacity-50', disabled);
    }

    window.updateContinueButton = updateContinueButton;

    // ========================
    // Gestion upload images avec validation
    // ========================
    ['logo', 'cover'].forEach(type => {
        const input   = type === 'logo' ? logoInput : coverInput;
        const preview = type === 'logo' ? logoPreview : coverPreview;
        if (!input || !preview) return;

        input.addEventListener('change', async () => {
            const file   = input.files[0];
            const $result = file ? await validateImage(file, type) : { valid: true };

            if (result.valid) {
                input.dataset.valid = 'true';
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `
                        <img src="${e.target.result}">
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                input.removeAttribute('data-valid');
                input.value = '';
                showImageError(preview, result.msg);
                if (window.toastr) {
                    toastr.error(result.msg);
                }
            }
            updateContinueButton();
        });
    });

    // ========================
    // Stepper
    // ========================
    function goTo(index) {
        if (index < 0 || index >= panes.length) return;

        if (index > currentStep && !validateStep(currentStep)) return;

        panes.forEach(p => p.classList.remove('is-visible'));
        steps.forEach((s, idx) => {
            s.classList.toggle('is-active',   idx === index);
            s.classList.toggle('is-complete', idx < index);
        });

        panes[index].classList.add('is-visible');
        currentStep = index;

        updateContinueButton();
        updateSummary();
        if (typeof saveFormState === 'function') saveFormState();
        // window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    window.goTo = goTo; // Expose for persistence manager

    $$('.next').forEach(btn => btn.addEventListener('click', () => {
        if (validateStep(currentStep)) goTo(currentStep + 1);
    }));

    $$('.prev').forEach(btn => btn.addEventListener('click', () => {
        if (currentStep > 0) goTo(currentStep - 1);
    }));

    steps.forEach((s, idx) => {
        s.style.cursor = 'pointer';
        s.addEventListener('click', () => {
            if (idx <= currentStep) {
                goTo(idx);
            } else if (idx === currentStep + 1) {
                if (validateStep(currentStep)) goTo(idx);
            }
        });
    });

    // ========================
    // Résumé
    // ========================
    function updateSummary() {
        const summaryDiv = $('.summary');
        if (!summaryDiv) return;

        const profileName   = $('#profileName')?.value || '—';
        const profileEmail  = $('#profileEmail')?.value || '—';
        const phone         = $('#profilePhone')?.value || '—';
        const langValue     = $('#communityLang')?.value || '—';
        
        // Map language values to native names
        const langMap = {
            'french': 'Français',
            'english': 'English',
            'deutsch': 'Nederlands',
            'arabe': 'العربية',
            'spanish': 'Español'
        };
        const lang = langMap[langValue] || langValue;
        
        const i_am          = $('#i_am_id')?.value || '—';
        const communityName = $('#communityName')?.value || '—';
        const desc          = $('#communityDesc')?.value || '—';
        const cat           = $('#communityCat')?.value || '—';
        const street        = $('#communityStreet')?.value || '—';
        const number        = $('#communityNumber')?.value || '—';
        const city          = $('#communityCity')?.value || '—';
        const postal        = $('#communityPostalCode')?.value || '—';

        // Get the DISPLAYED TEXT from Tax_residence dropdown
        const taxEl = document.getElementById('Tax_residence');
        let tax = '—';
        let taxValue = taxEl ? taxEl.value : '';
        
        
        // If DOM value is empty, try to get from sessionStorage
        if (!taxValue) {
            try {
                const saved = sessionStorage.getItem('wayo_admission_form_state');
                if (saved) {
                    const savedData = JSON.parse(saved);
                    taxValue = savedData.Tax_residence || savedData._taxResidence || '';
                }
            } catch (e) {
                console.error('Error reading sessionStorage:', e);
            }
        }
        
        if (taxValue) {
            // Map value to display text
            const taxTextMap = {
                'MA': '<?php echo get_phrase("Morocco"); ?>',
                'UAE': '<?php echo get_phrase("United_Arab_Emirates"); ?>'
            };
            tax = taxTextMap[taxValue] || taxValue;
        }
        const currency      = $('#currencyCode')?.value || 'MAD';
        const isPrivate     = privateToggle?.checked;
        const visibilityText = isPrivate
            ? '<?php echo get_phrase("Private"); ?>'
            : '<?php echo get_phrase("Public"); ?>';

        const logoImg  = $('#logoPreview img');
        const coverImg = $('#coverPreview img');
        

        summaryDiv.innerHTML = `
            <div class="summary-grid">
                <!-- Profile Card -->
                <div class="summary-card" style="cursor: pointer;" onclick="goTo(0)">
                    <div class="summary-card-header">
                        <div class="summary-card-icon"><i class="fa-solid fa-user"></i></div>
                        <h3><?php echo get_phrase("Profile"); ?></h3>
                    </div>
                    <div class="summary-card-body">
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Name"); ?>:</span><span class="summary-value">${profileName}</span></div>
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Email"); ?>:</span><span class="summary-value">${profileEmail}</span></div>
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Phone"); ?>:</span><span class="summary-value">${phone}</span></div>
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Language"); ?>:</span><span class="summary-value">${lang}</span></div>
                    </div>
                </div>

                <!-- Community Card -->
                <div class="summary-card" style="cursor: pointer;" onclick="goTo(1)">
                    <div class="summary-card-header">
                        <div class="summary-card-icon"><i class="fa-solid fa-users"></i></div>
                        <h3><?php echo get_phrase("Community"); ?></h3>
                    </div>
                    <div class="summary-card-body">
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Type"); ?>:</span><span class="summary-value">${i_am}</span></div>
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Name"); ?>:</span><span class="summary-value">${communityName}</span></div>
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Tax_residence"); ?>:</span><span class="summary-value">${tax}</span></div>
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Category"); ?>:</span><span class="summary-value">${cat}</span></div>
                        <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Visibility"); ?>:</span><span class="summary-value">${visibilityText}</span></div>
                    </div>
                </div>

                <!-- Price Card Removed -->

                <!-- Address Card -->
                <div class="summary-card" style="cursor: pointer;" onclick="goTo(1)">
                    <div class="summary-card-header">
                        <div class="summary-card-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <h3><?php echo get_phrase("Address"); ?></h3>
                    </div>
                    <div class="summary-card-body">
                        <div class="summary-item"><span class="summary-value">${street}, ${number}</span></div>
                        <div class="summary-item"><span class="summary-value">${city}, ${postal}</span></div>
                    </div>
                </div>

                <!-- Media Card -->
                <div class="summary-card" style="cursor: pointer;" onclick="goTo(1)">
                    <div class="summary-card-header">
                        <div class="summary-card-icon"><i class="fa-solid fa-image"></i></div>
                        <h3><?php echo get_phrase("Media"); ?></h3>
                    </div>
                    <div class="summary-card-body">
                        <div class="summary-media-row">
                            <div class="summary-media-item">
                                ${logoImg ? `<img src="${logoImg.src}" alt="Logo">` : '<div style="height:140px; background:#f8fafc; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#cbd5e1;"><i class="fa-solid fa-plus"></i></div>'}
                                <span><?php echo get_phrase("Logo"); ?></span>
                            </div>
                            <div class="summary-media-item">
                                ${coverImg ? `<img src="${coverImg.src}" alt="Cover">` : '<div style="height:140px; background:#f8fafc; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#cbd5e1;"><i class="fa-solid fa-plus"></i></div>'}
                                <span><?php echo get_phrase("Cover"); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="summary-card" style="cursor: pointer;" onclick="goTo(1)">
                    <div class="summary-card-header">
                        <div class="summary-card-icon"><i class="fa-solid fa-align-left"></i></div>
                        <h3><?php echo get_phrase("Description"); ?></h3>
                    </div>
                    <div class="summary-card-body">
                        <p style="margin:0; font-style: italic; color: #64748b; overflow-wrap: break-word; word-break: break-word;">"${desc}"</p>
                    </div>
                </div>
            </div>
        `;
    }

    window.updateSummary = updateSummary;

    // ========================
    // Submit AJAX
    // ========================
    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!validateStep(currentStep)) return;

        // Sync hidden fields if needed
        const phone = document.getElementById('profilePhone')?.value;
        const schoolPhoneHidden = document.getElementById('school_phone_hidden');
        if (schoolPhoneHidden) schoolPhoneHidden.value = phone;

        const formData = new FormData(this);
        const csrfInput = getSchoolFormCsrfInput();
        if (csrfInput && !formData.has(csrfInput.name)) {
            formData.append(csrfInput.name, csrfInput.value);
        }
        const submitBtn = document.getElementById('submitBtnSchool');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        // Show loading overlay
        if (loadingOverlay) {
            loadingOverlay.classList.add('is-visible');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
        if (submitBtn) submitBtn.disabled = true;

        // Use direct endpoint to bypass URL rewriting
        const submitUrl = '<?= base_url("register/community"); ?>';
        
        fetch(submitUrl, {
            method: 'POST',
            body: formData
        })
        .then(async r => {
            const raw = await r.text();
            if (!raw) {
                throw new Error('Server returned an empty response.');
            }
            try {
                return JSON.parse(raw);
            } catch (err) {
                console.error('Server response is not valid JSON:', raw);
                throw new Error('Server returned invalid JSON. Check console.');
            }
        })
        .then(data => {
            if (data.csrf) {
                const csrfInput = getSchoolFormCsrfInput();
                if (csrfInput) {
                    csrfInput.name = data.csrf.csrfName;
                    csrfInput.value = data.csrf.csrfHash;
                }
            }

            if (data.status) {
                if (typeof clearSavedState === 'function') clearSavedState();
                $('#resetBtn')?.click();
                const overlay = document.getElementById('successOverlay');
                if (overlay) overlay.classList.add('is-visible');
            } else {
                // Handle specific image errors
                if (data.error_type === 'logo') {
                    showImageError(logoPreview, data.message);
                    goTo(1); // Go back to step 2 (community)
                } else if (data.error_type === 'cover') {
                    showImageError(coverPreview, data.message);
                    goTo(1); // Go back to step 2 (community)
                } else {
                    toastr?.error(data.message || 'Error');
                }
            }
        })
        .catch((err) => {
            console.error('Submission error:', err);
            toastr?.error(err.message || 'An error occurred during submission.');
        })
        .finally(() => {
            // Hide loading overlay
            if (loadingOverlay) {
                loadingOverlay.classList.remove('is-visible');
                document.body.style.overflow = ''; // Restore scrolling
            }
            if (submitBtn) submitBtn.disabled = false;
        });
    });
     // ========================
    // GESTION DU CODE PAYS CUSTOM ET AUTOMATIQUE
    // ========================
    const phoneInput = $('#profilePhone');
    const countryInput = $('#countrySelect'); // Hidden input
    const countryTrigger = $('#countryTrigger');
    const countryOptions = $('#countryOptions');
    const selectedFlag = $('#selectedFlag');
    const options = $$('.custom-option');

    let previousCode = countryInput ? countryInput.value : '+212'; 

    if (countryTrigger) {
        // Toggle Dropdown
        countryTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            countryOptions.classList.toggle('open');
        });

        // Close when clicking outside
        document.addEventListener('click', () => {
            countryOptions.classList.remove('open');
        });

        // Select Option Logic
        options.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                const value = this.getAttribute('data-value');
                const flag = this.getAttribute('data-flag');

                // Update UI
                selectedFlag.className = `fi fi-${flag}`;
                countryInput.value = value;

                // Update selection state
                options.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');

                countryOptions.classList.remove('open');

                // Trigger logic to update phone input
                updatePhoneCode();
            });
        });
    }

    function updatePhoneCode() {
        if (!phoneInput || !countryInput) return;
        const newCode = countryInput.value;
        let currentVal = phoneInput.value;

        if (currentVal.startsWith(previousCode)) {
            phoneInput.value = newCode + currentVal.substring(previousCode.length);
        } 
        else if (!currentVal.trim() || !currentVal.startsWith('+')) {
            phoneInput.value = newCode;
        }
        else {
            phoneInput.value = newCode + currentVal.replace(/^\+\d+\s*/, '');
        }

        previousCode = newCode; 
        updateContinueButton();
    }

    // Init phone code
    if (phoneInput && countryInput) {
        if (!phoneInput.value.trim()) {
            phoneInput.value = countryInput.value;
        }
        previousCode = countryInput.value;

        const initialOpt = document.querySelector(`.custom-option[data-value="${countryInput.value}"]`);
        if(initialOpt && selectedFlag) {
           const flag = initialOpt.getAttribute('data-flag'); 
           selectedFlag.className = `fi fi-${flag}`;
        }
    }

    // Reverse lookup logic: Auto-select flag when typing code
    if (phoneInput && countryInput) {
        phoneInput.addEventListener('input', () => {
            const val = phoneInput.value.trim();
            
            // Allow user to clear input or type anything, but if it looks like a code, try to match
            if (!val.startsWith('+')) return;

            // Find matching option
            let bestMatch = null;
            let bestLen = 0;

            options.forEach(opt => {
                const code = opt.getAttribute('data-value');
                if (val.startsWith(code)) {
                    if (code.length > bestLen) {
                        bestLen = code.length;
                        bestMatch = opt;
                    }
                }
            });

            if (bestMatch) {
                const code = bestMatch.getAttribute('data-value');
                const flag = bestMatch.getAttribute('data-flag');
                
                // Only update if changed
                if (countryInput.value !== code) {
                     countryInput.value = code;
                     selectedFlag.className = `fi fi-${flag}`;
                     previousCode = code; // Sync previousCode

                     // Update selection in dropdown
                     options.forEach(o => o.classList.remove('selected'));
                     bestMatch.classList.add('selected');
                }
            }
        });
    }

    // ========================
    // Init
    // ========================
    // Init
    // applyPriceRules(); // REMOVED
    updateCurrencyUI();
    
    // Check if we should restore step or start at 0
    const saved = sessionStorage.getItem('wayo_admission_form_state');
    if (!saved) {
        goTo(0);
    }

    // Success button redirect
    const successBtn = document.getElementById('successBtn');
    if (successBtn) {
        successBtn.addEventListener('click', () => {
             location.href = '<?= site_url('/home/communities'); ?>';
        });
    }

    // Move overlays to body to prevent z-index/clipping issues
    const successOverlay = document.getElementById('successOverlay');
    if (successOverlay) {
        document.body.appendChild(successOverlay);
    }
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        document.body.appendChild(loadingOverlay);
    }
});
</script>

<!-- ==========================================
     PERSISTENCE MANAGER (Non-invasive)
     ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'wayo_admission_form_state';
    const form = document.getElementById('schoolform');
    if (!form) return;

    window.saveFormState = function() {
        const formData = new FormData(form);
        const data = {};
        
        formData.forEach((value, key) => {
            // Exclude files and CSRF (Passwords included per user request)
            if (!(value instanceof File) && !key.includes('csrf')) {
                data[key] = value;
            }
        });

        // Save current step by reading from DOM
        const visiblePane = document.querySelector('.step-pane.is-visible');
        if (visiblePane) {
            data._step = parseInt(visiblePane.dataset.step) - 1;
        }

        // Custom phone/country sync
        const countryInput = document.getElementById('countrySelect');
        if (countryInput) data._country = countryInput.value;

        // Explicit Tax_residence save
        const taxResEl = document.getElementById('Tax_residence');
        if (taxResEl && taxResEl.value) {
            data._taxResidence = taxResEl.value;
        }

        // Visual previews (Base64)
        const logoImg = document.querySelector('#logoPreview img');
        const coverImg = document.querySelector('#coverPreview img');
        if (logoImg) data._logoPre = logoImg.src;
        if (coverImg) data._coverPre = coverImg.src;

        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    };

    window.restoreFormState = function() {
        const saved = sessionStorage.getItem(STORAGE_KEY);
        if (!saved) return;

        try {
            const data = JSON.parse(saved);
            
            // Restore inputs
            Object.keys(data).forEach(key => {
                if (key.startsWith('_')) return;
                const input = form.elements[key];
                if (input) {
                    if (input.type === 'checkbox') input.checked = !!data[key];
                    else if (input.type === 'radio') {
                        const r = form.querySelector(`input[name="${key}"][value="${data[key]}"]`);
                        if (r) r.checked = true;
                    } else {
                        input.value = data[key];
                    }
                }
            });

            // Restore Phone/Country UI
            if (data._country) {
                const ci = document.getElementById('countrySelect');
                const sf = document.getElementById('selectedFlag');
                if (ci) ci.value = data._country;
                const opt = document.querySelector(`.custom-option[data-value="${data._country}"]`);
                if (opt && sf) {
                    sf.className = `fi fi-${opt.getAttribute('data-flag')}`;
                    document.querySelectorAll('.custom-option').forEach(o => o.classList.remove('selected'));
                    opt.classList.add('selected');
                }
                // Update internal logic if exposed (optional but safer)
                if (typeof previousCode !== 'undefined') window.previousCode = data._country;
            }

            // Restore Tax_residence explicitly
            if (data._taxResidence) {
                const taxResEl = document.getElementById('Tax_residence');
                if (taxResEl) {
                    taxResEl.value = data._taxResidence;
                }
            }

            // Restore Previews
            if (data._logoPre) {
                const lp = document.getElementById('logoPreview');
                if (lp) {
                    lp.innerHTML = `<img src="${data._logoPre}" style="max-width:100%; border-radius:8px;">`;
                    const li = document.getElementById('communityLogo');
                    if (li) li.dataset.valid = 'true';
                }
            }
            if (data._coverPre) {
                const cp = document.getElementById('coverPreview');
                if (cp) {
                    cp.innerHTML = `<img src="${data._coverPre}" style="max-width:100%; border-radius:8px; object-fit:cover;">`;
                    const ci = document.getElementById('communityCover');
                    if (ci) ci.dataset.valid = 'true';
                }
            }

            // Restore Step
            if (typeof data._step !== 'undefined' && typeof window.goTo === 'function') {
                setTimeout(() => window.goTo(data._step), 100);
            }

            // Trigger existing UI updates
            if (typeof applyPriceRules === 'function') applyPriceRules();
            if (typeof updateCurrencyUI === 'function') updateCurrencyUI();
            if (typeof updateSummary === 'function') updateSummary();
            if (typeof updateContinueButton === 'function') updateContinueButton();

        } catch (e) {
            console.error("Persistence error:", e);
        }
    };

    window.clearSavedState = function() {
        sessionStorage.removeItem(STORAGE_KEY);
    };

    // Auto-save triggers
    form.addEventListener('input', saveFormState);
    form.addEventListener('change', saveFormState);

    // Initial restoration
    restoreFormState();
});

        
</script>
