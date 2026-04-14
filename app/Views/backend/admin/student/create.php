<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ============================================================================
   PREMIUM FORM DESIGN
   ============================================================================ */

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

.ec-form-container {
    padding: 0.5rem;
}

/* Form Header */
.ec-form-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--form-border);
}

.ec-form-icon {
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

.ec-form-title {
    flex: 1;
}

.ec-form-title h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--form-dark);
}

.ec-form-title p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--form-gray);
}

/* Form Groups */
.ec-form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.ec-form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--form-dark);
}

.ec-form-label i {
    color: var(--form-primary);
    font-size: 1rem;
}

.ec-form-label .ec-required {
    color: var(--form-danger);
    font-weight: 700;
}

.ec-form-hint {
    font-size: 0.75rem;
    color: var(--form-gray);
    font-weight: 400;
    margin-left: auto;
}

/* Input Wrapper */
.ec-input-wrapper {
    position: relative;
}

.ec-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--form-gray);
    font-size: 1.25rem;
    z-index: 1;
    transition: all 0.2s;
}

.ec-form-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--form-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--form-light);
    color: var(--form-dark);
    transition: all 0.2s;
}

.ec-form-input:hover {
    border-color: #cbd5e1;
}

.ec-form-input:focus {
    outline: none;
    border-color: var(--form-primary);
    background: var(--form-white);
    box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1);
}

.ec-form-input:focus + .ec-input-icon,
.ec-form-input:not(:placeholder-shown) + .ec-input-icon {
    color: var(--form-primary);
}

.ec-form-input.is-invalid {
    border-color: var(--form-danger);
    background: #fef2f2;
}

.ec-form-input.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.ec-form-input.is-valid {
    border-color: var(--form-success);
    background: #f0fdf4;
}

/* Validation Icons */
.ec-validation-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    opacity: 0;
    transition: all 0.2s;
}

.ec-validation-icon.success {
    color: var(--form-success);
}

.ec-validation-icon.error {
    color: var(--form-danger);
}

.ec-form-input.is-valid ~ .ec-validation-icon.success,
.ec-form-input.is-invalid ~ .ec-validation-icon.error {
    opacity: 1;
}

/* Error Messages */
.ec-error-message {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #fef2f2;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--form-danger);
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.2s;
}

.ec-error-message.show {
    opacity: 1;
    transform: translateY(0);
}

.ec-error-message i {
    font-size: 1rem;
}

/* Form Actions */
.ec-form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--form-border);
}

.ec-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    min-width: 140px;
}

.ec-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.ec-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4);
}

.ec-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.ec-btn-secondary {
    background: var(--form-light);
    color: var(--form-gray);
    border: 2px solid var(--form-border);
}

.ec-btn-secondary:hover {
    background: var(--form-border);
    color: var(--form-dark);
}

.ec-btn-success {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* Loading Animation */
.ec-btn .mdi-loading {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Shake Animation */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.shake {
    animation: shake 0.5s;
}



/* Image Upload Section Styles */
.image-upload-section {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--form-light);
    border-radius: 16px;
    border: 2px dashed var(--form-border);
    transition: all 0.2s;
}

.image-upload-section:hover {
    border-color: var(--form-primary);
    background: rgba(var(--form-primary-rgb), 0.05);
}

.image-preview {
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    position: relative;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo-upload-btn label {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid var(--form-border);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--form-dark);
    transition: all 0.2s;
}

.logo-upload-btn label:hover {
    border-color: var(--form-primary);
    color: var(--form-primary);
}

.image-upload {
    display: none;
}

/* Info Card */
.ec-info-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--form-light);
    border-radius: 12px;
    border: 1px solid var(--form-border);
    margin-bottom: 1.5rem;
}

.ec-info-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(var(--form-primary-rgb), 0.1);
    color: var(--form-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.ec-info-card-content h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: var(--form-dark);
}

.ec-info-card-content p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--form-gray);
}
</style>

<div class="ec-form-container">
    <!-- Form Header -->
    <div class="ec-form-header">
        <div class="ec-form-icon">
            <i class="mdi mdi-account-plus"></i>
        </div>
        <div class="ec-form-title">
            <h3><?php echo get_phrase('student_admission_form'); ?></h3>
            <p><?php echo get_phrase('manage_student_admissions'); ?></p>
        </div>
    </div>

    <?php include 'single_student_admission.php'; ?>
</div>
