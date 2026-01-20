<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive-profile.css">

<style>
/* ========== MODERN DASHBOARD STYLES ========== */
.modern-dashboard {
  /* MONOCHROMATIC THEME (INDIGO) */
  --primary: #6366f1;
  --primary-light: #818cf8;
  --primary-lighter: #e0e7ff;
  --primary-dark: #4338ca;
  --secondary: #10b981; /* Green for success */
  --bg-main: #f8fafc;
  --bg-card: #ffffff;
  --text-dark: #1e293b;
  --text-muted: #64748b;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
  --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
  
  font-family: 'DM Sans', sans-serif;
  background: var(--bg-main);
  min-height: 100vh;
  padding: 1.5rem;
  margin: -15px -15px 0 -15px;
}

/* Header */
.dash-header {
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.dash-header h1 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.dash-header h1 .icon-box {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
}

/* Modern Cards */
.modern-card {
  background: var(--bg-card);
  border-radius: 20px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
  transition: all 0.3s ease;
  margin-bottom: 2rem;
}

.modern-card:hover {
  box-shadow: var(--shadow-lg);
}

.modern-card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(180deg, #fafbfc, transparent);
}

.modern-card-header h3 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.0625rem;
  font-weight: 600;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.modern-card-header h3 i {
  color: var(--primary);
  font-size: 1.125rem;
}

.modern-card-body {
  padding: 1.5rem;
}

/* Modern Form Elements */
.modern-form-group {
    margin-bottom: 1.5rem;
}

.modern-label {
    display: block;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.modern-label span.required {
    color: #ef4444;
}

.modern-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    font-size: 0.95rem;
    color: var(--text-dark);
    transition: all 0.2s;
    background-color: #fcfcfc;
}

.modern-input:focus {
    border-color: var(--primary);
    background-color: #ffffff;
    box-shadow: 0 0 0 4px var(--primary-lighter);
    outline: none;
}

.modern-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    transition: all 0.2s ease;
    gap: 0.5rem;
    cursor: pointer;
    background: var(--primary);
    color: white;
    box-shadow: 0 4px 6px rgba(99, 102, 241, 0.25);
}

.modern-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(99, 102, 241, 0.3);
    color: white;
}

.modern-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
</style>

<div class="modern-dashboard">
    <!-- Header -->
    <div class="dash-header">
        <h1>
            <div class="icon-box">
                <i class="fas fa-user-cog"></i>
            </div>
            <?php echo get_phrase('manage_profile'); ?>
        </h1>
    </div>

    <div class="row justify-content-center">
        <div id="profile_content" class="col-xl-10 col-lg-12">
            <?php include 'edit.php'; ?>
        </div>
    </div>
</div>

<script>
    function updateProfileInfo() {
        $(".profileAjaxForm").validate({});
        $(".profileAjaxForm").submit(function(e) {
            var form = $(this);
            ajaxSubmit(e, form, reload);
        });
    }

    function changePassword() {
        $(".changePasswordAjaxForm").validate({});
        $(".changePasswordAjaxForm").submit(function(e) {
            var form = $(this);
            ajaxSubmit(e, form, reload);
        });
    }

    function reload() {
        setTimeout(
            function() {
                location.reload();
            }, 1000);
    }
</script>