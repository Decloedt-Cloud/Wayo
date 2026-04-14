<style>
/* Reusing Premium Form Design Styles for Consistency */
:root {
    --form-primary: #6366f1;
    --form-primary-rgb: 99, 102, 241;
    --form-success: #10b981;
    --form-danger: #ef4444;
    --form-warning: #f59e0b;
    --form-dark: #1e293b;
    --form-gray: #64748b;
    --form-light: #f8fafc;
    --form-border: #e2e8f0;
    --form-white: #ffffff;
}

.exp-form-container {
    padding: 0.5rem;
}

.exp-form-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--form-border);
}

.exp-form-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.3);
}

.exp-form-title {
    flex: 1;
}

.exp-form-title h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--form-dark);
}

.exp-form-title p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--form-gray);
}

.exp-form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.exp-form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--form-dark);
}

.exp-form-label i {
    color: var(--form-primary);
    font-size: 1rem;
}

.exp-input-wrapper {
    position: relative;
}

.exp-form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--form-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--form-light);
    color: var(--form-dark);
    transition: all 0.2s;
}

.exp-form-input:hover {
    border-color: #cbd5e1;
}

.exp-form-input:focus {
    outline: none;
    border-color: var(--form-primary);
    background: var(--form-white);
    box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1);
}

/* File Input Specific */
.exp-file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 100%;
}

.exp-file-input-wrapper input[type=file] {
    font-size: 100px;
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
}

.exp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    width: 100%;
}

.exp-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.exp-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}
</style>

<?php $school_id = school_id(); ?>

<div class="exp-form-container">
    <div class="exp-form-header">
        <div class="exp-form-icon">
            <i class="mdi mdi-fingerprint"></i>
        </div>
        <div class="exp-form-title">
            <h3><?php echo get_phrase('biometric_attendance'); ?></h3>
            <p><?php echo get_phrase('upload_csv_file_for_attendance'); ?></p>
        </div>
    </div>

    <form method="POST" class="d-block ajaxForm responsive_media_query" action="<?php echo site_url('addons/biometric_attendance/biometric_attendance'); ?>" enctype="multipart/form-data">
        <!-- Champ caché pour le jeton CSRF -->
        <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
        
        <div class="exp-form-group">
            <label class="exp-form-label" for="biometric_attendance_file">
                <i class="mdi mdi-file-upload"></i>
                <?php echo get_phrase('choose_biometric_attendance_file'); ?>
            </label>
            <div class="exp-input-wrapper">
                <input type="file" class="exp-form-input" id="biometric_attendance_file" name="biometric_attendance_file" required>
            </div>
        </div>

        <div class="exp-form-group mt-4" id="updateAttendanceDiv">
            <button class="exp-btn exp-btn-primary" type="submit">
                <i class="mdi mdi-cloud-upload"></i> <?php echo get_phrase('upload_file'); ?>
            </button>
        </div>
    </form>
</div>

<script>
initCustomFileUploader();

$(".ajaxForm").validate({}); // Jquery form validation initialization
$(".ajaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, getDailtyAttendance);
});
</script>