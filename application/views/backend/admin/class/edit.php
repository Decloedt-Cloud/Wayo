<style>
/* ============================================================================
   PREMIUM FORM DESIGN - CLASS EDIT
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

.exp-form-container {
    padding: 0.5rem;
}

/* Form Header */
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

/* Expense Preview Card */
.exp-expense-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, var(--form-light), #f1f5f9);
    border-radius: 16px;
    margin-bottom: 2rem;
    border: 1px solid var(--form-border);
}

.exp-preview-avatar {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.125rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.exp-preview-info {
    flex: 1;
}

.exp-preview-amount {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--form-success);
    margin: 0 0 0.25rem;
}

.exp-preview-id {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--form-gray);
    background: var(--form-white);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

.exp-preview-category {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: linear-gradient(135deg, rgba(var(--form-primary-rgb), 0.1), rgba(var(--form-primary-rgb), 0.2));
    color: var(--form-primary);
}

/* Change Indicator */
.exp-change-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, rgba(var(--form-primary-rgb), 0.05), rgba(var(--form-primary-rgb), 0.1));
    border-radius: 10px;
    margin-bottom: 1.5rem;
    border: 1px dashed rgba(var(--form-primary-rgb), 0.3);
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s;
}

.exp-change-indicator.show {
    opacity: 1;
    transform: translateY(0);
}

.exp-change-indicator i {
    color: var(--form-primary);
    font-size: 1.25rem;
}

.exp-change-indicator span {
    font-size: 0.875rem;
    color: var(--form-dark);
}

/* Form Groups */
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

.exp-form-label .exp-required {
    color: var(--form-danger);
    font-weight: 700;
}

.exp-form-hint {
    font-size: 0.75rem;
    color: var(--form-gray);
    font-weight: 400;
    margin-left: auto;
}

/* Input Wrapper */
.exp-input-wrapper {
    position: relative;
}

.exp-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--form-gray);
    font-size: 1.25rem;
    z-index: 1;
    transition: all 0.2s;
}

.exp-form-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
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

.exp-form-input:focus + .exp-input-icon,
.exp-form-input:not(:placeholder-shown) + .exp-input-icon {
    color: var(--form-primary);
}

.exp-form-input.is-invalid {
    border-color: var(--form-danger);
    background: #fef2f2;
}

.exp-form-input.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.exp-form-input.is-valid {
    border-color: var(--form-success);
    background: #f0fdf4;
}

/* Select Styling */
.exp-form-select {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--form-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--form-light);
    color: var(--form-dark);
    transition: all 0.2s;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
}

.exp-form-select:hover {
    border-color: #cbd5e1;
}

.exp-form-select:focus {
    outline: none;
    border-color: var(--form-primary);
    background-color: var(--form-white);
    box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1);
}

.exp-form-select.is-invalid {
    border-color: var(--form-danger);
    background-color: #fef2f2;
}

.exp-form-select.is-valid {
    border-color: var(--form-success);
    background-color: #f0fdf4;
}

/* Validation Icons */
.exp-validation-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    opacity: 0;
    transition: all 0.2s;
}

.exp-validation-icon.success {
    color: var(--form-success);
}

.exp-validation-icon.error {
    color: var(--form-danger);
}

.exp-form-input.is-valid ~ .exp-validation-icon.success,
.exp-form-input.is-invalid ~ .exp-validation-icon.error,
.exp-form-select.is-valid ~ .exp-validation-icon.success,
.exp-form-select.is-invalid ~ .exp-validation-icon.error {
    opacity: 1;
}

/* Error Messages */
.exp-error-message {
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

.exp-error-message.show {
    opacity: 1;
    transform: translateY(0);
}

.exp-error-message i {
    font-size: 1rem;
}

/* Form Inline */
.exp-form-inline {
    display: flex;
    gap: 1rem;
    align-items: end;
}

.exp-form-inline .exp-form-input {
    flex: 1;
}

.exp-currency-input {
    width: 80px !important;
    text-align: center;
    background: var(--form-light) !important;
    font-weight: 600;
}

/* Checkbox */
.exp-form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--form-light);
    border-radius: 12px;
    border: 1px solid var(--form-border);
}

.exp-form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--form-border);
    border-radius: 6px;
    background: var(--form-white);
}

.exp-form-check-input:checked {
    background: var(--form-primary);
    border-color: var(--form-primary);
}

.exp-form-check-label {
    font-weight: 500;
    color: var(--form-dark);
    cursor: pointer;
}

.exp-form-check-label i {
    color: var(--form-warning);
    margin-right: 0.25rem;
}

/* Form Actions */
.exp-form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--form-border);
}

.exp-btn {
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

.exp-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.exp-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4);
}

.exp-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.exp-btn-secondary {
    background: var(--form-light);
    color: var(--form-gray);
    border: 2px solid var(--form-border);
}

.exp-btn-secondary:hover {
    background: var(--form-border);
    color: var(--form-dark);
}

/* Loading Animation */
.exp-btn .mdi-loading {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Shake Animation */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.shake {
    animation: shake 0.3s ease-in-out;
}

/* Image Upload Section */
.exp-image-upload-section {
    background: linear-gradient(135deg, var(--form-light) 0%, #f1f5f9 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 2px dashed var(--form-border);
    transition: all 0.3s ease;
}

.exp-image-upload-section:hover {
    border-color: var(--form-primary);
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);
}

.exp-image-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    overflow: hidden;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    position: relative;
}

.exp-preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.exp-image-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(99, 102, 241, 0.9);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    opacity: 0;
    transition: all 0.3s ease;
}

.exp-image-preview:hover .exp-image-overlay {
    opacity: 1;
}

.exp-image-upload-btn {
    text-align: center;
}

.exp-image-upload-btn label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
    border: none;
}

.exp-image-upload-btn label:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.exp-image-upload {
    display: none;
}

/* Upload highlight effect */
.upload-highlight {
    animation: uploadPulse 1.5s ease-in-out;
}

@keyframes uploadPulse {
    0%, 100% {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    50% {
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    }
}

/* Responsive */
@media (max-width: 480px) {
    .exp-form-header {
        flex-direction: column;
        text-align: center;
    }

    .exp-expense-preview {
        flex-direction: column;
        text-align: center;
    }

    .exp-form-actions {
        flex-direction: column;
    }

    .exp-btn {
        width: 100%;
    }
}
</style>

<?php $classes = $this->db->get_where('classes', array('id' => $param1))->result_array(); 
$currencies = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('system_currency');
?>
<?php foreach($classes as $class){ ?>

<div class="exp-form-container">
    <!-- Form Header -->
    <div class="exp-form-header">
        <div class="exp-form-icon">
            <i class="mdi mdi-school-edit"></i>
        </div>
        <div class="exp-form-title">
            <h3><?php echo get_phrase('update_class'); ?></h3>
            <p><?php echo get_phrase('modify_class_details'); ?></p>
        </div>
    </div>

    <!-- Class Preview -->
    <div class="exp-expense-preview">
        <div class="exp-preview-avatar">
            <?php echo strtoupper(substr($class['name'] ?? 'CL', 0, 2)); ?>
        </div>
        <div class="exp-preview-info">
            <h4 class="exp-preview-amount"><?php echo $class['price'].' '.$currencies; ?></h4>
            <span class="exp-preview-id">
                <i class="mdi mdi-identifier"></i>
                CLASS-<?php echo str_pad($class['id'], 4, '0', STR_PAD_LEFT); ?>
            </span>
        </div>
        <span class="exp-preview-category">
            <i class="mdi mdi-tag"></i>
            <?php echo $class['name'] ?? get_phrase('unknown'); ?>
        </span>
    </div>

    <!-- Change Indicator -->
    <div class="exp-change-indicator" id="change-indicator">
        <i class="mdi mdi-pencil-circle"></i>
        <span><?php echo get_phrase('you_have_unsaved_changes'); ?></span>
    </div>

    <form method="POST" class="d-block ajaxForm" action="<?php echo route('manage_class/update/'.$param1); ?>">
        <!-- CSRF Token -->
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

        <!-- Original values for change detection -->
        <input type="hidden" id="original_name" value="<?php echo $class['name']; ?>">
        <input type="hidden" id="original_price" value="<?php echo $class['price']; ?>">
        <input type="hidden" id="original_status" value="<?php echo $class['statut']; ?>">
    
<div class="form-row">

    <!-- Image Upload Section -->
    <div class="exp-image-upload-section">
        <h5 style="text-align: center; color: var(--form-dark); margin-bottom: 1.5rem; font-weight: 600;">
            <i class="mdi mdi-camera-outline" style="color: var(--form-primary); margin-right: 0.5rem;"></i>
            <?php echo get_phrase('class_image'); ?>
            <small style="display: block; font-size: 0.75rem; color: var(--form-gray); font-weight: 400; margin-top: 0.25rem;">
                <?php echo get_phrase('optional'); ?>
            </small>
        </h5>
        <div class="exp-image-preview" id="student-image-preview" style="position: relative;">
            <?php if ($class['photo'] == '' || $class['photo'] == null): ?>
            <img src="<?php echo base_url() . 'uploads/class/placeholder.png'; ?>" alt="Class Image" class="exp-preview-image">
            <?php else: ?>
            <img src="<?php echo base_url() . 'uploads/class/'.$class['photo']; ?>" alt="Class Image" class="exp-preview-image">
            <?php endif; ?>
            <div class="exp-image-overlay">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        <div class="exp-image-upload-btn">
            <label for="student_image">
                <i class="mdi mdi-cloud-upload"></i> <?php echo get_phrase('upload_an_image'); ?>
            </label>
            <input id="student_image" type="file" class="exp-image-upload" name="photo" accept="image/*" data-preview="student-image-preview">
        </div>
    </div>
        <!-- Class Name -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-account-outline"></i>
                <span><?php echo get_phrase('class_name'); ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="text"
                       class="exp-form-input"
                       id="name"
                       name="name"
                       value="<?php echo $class['name']; ?>"
                       placeholder="<?php echo get_phrase('enter_class_name'); ?>"
                       required>
                <i class="mdi mdi-account exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="name_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('please_provide_class_name'); ?></span>
            </div>
        </div>
        <!-- Price -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-currency-usd"></i>
                <span><?php echo get_phrase('price_including_vat'); ?> (<?php echo currency_code_and_symbol('code'); ?>)</span>
                <span class="exp-required">*</span>
            </label>
            <?php
            $currencies = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('system_currency');
            $type = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('type');
            ?>
            <div class="exp-form-inline">
                <div class="exp-input-wrapper" style="flex: 1;">
                    <input type="text"
                           class="exp-form-input"
                           id="price"
                           name="price"
                           value="<?php echo $class['price']; ?>"
                           placeholder="0.00"
                           autocomplete="off"
                           oninput="checkPriceForParticulier(this)"
                           required>
                    <i class="mdi mdi-cash exp-input-icon"></i>
                    <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                    <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
                </div>
                <input type="text" class="exp-form-input exp-currency-input" value="<?php echo $currencies; ?>" disabled>
                <input type="hidden" id="currency" name="currency" value="<?php echo $currencies; ?>">
            </div>
            <div class="exp-error-message" id="price_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('invalid_price'); ?></span>
            </div>

            <!-- Free Class Option -->
            <div class="mt-1">
                <div class="exp-form-check">
                    <input class="exp-form-check-input"
                           type="checkbox"
                           id="is_free"
                           name="is_free"
                           value="1"
                           <?php if ((float)$class['price'] == 0): ?> checked <?php endif; ?>>
                    <label class="exp-form-check-label" for="is_free">
                        <i class="mdi mdi-gift-outline"></i>
                        <?php echo get_phrase('free_class_set_price_to_0'); ?>
                    </label>
                </div>
            </div>

            <!-- Warning Message -->
            <small id="price-warning" class="text-danger" <?php if ($type != 'Particulier'): ?> style="display:none;" <?php endif; ?>>
                <?php echo get_phrase('as_you_are_a_private_individual_the_price_will_be_automatically_set_to_0'); ?>
            </small>
        </div>




           
            <small id="price_help" class="form-text text-muted"><?php echo get_phrase('provide_class_price'); ?></small>
        </div>

        <!-- Start Date -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-calendar-start"></i>
                <span><?php echo get_phrase('start_date'); ?></span>
            </label>
            <div class="exp-input-wrapper">
                <input type="date"
                       class="exp-form-input"
                       id="start_date"
                       name="start_date"
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo (!empty($class['date_debut'])) ? date('Y-m-d', strtotime($class['date_debut'])) : ''; ?>">
                <i class="mdi mdi-calendar-month exp-input-icon"></i>
            </div>
        </div>

        <!-- End Date -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-calendar-end"></i>
                <span><?php echo get_phrase('end_date'); ?></span>
            </label>
            <div class="exp-input-wrapper">
                <input type="date"
                       class="exp-form-input"
                       id="end_date"
                       name="end_date"
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo (!empty($class['date_fin'])) ? date('Y-m-d', strtotime($class['date_fin'])) : ''; ?>">
                <i class="mdi mdi-calendar-month exp-input-icon"></i>
            </div>
        </div>

        <!-- Class Status -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-information-outline"></i>
                <span><?php echo get_phrase('class_status'); ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <select class="exp-form-select" name="status" id="status_class" required>
                    <option value=""><?php echo get_phrase('select_status'); ?></option>
                    <option value="active" <?php if ($class['statut'] == 'active'): ?> selected <?php endif; ?>><?php echo get_phrase('active'); ?></option>
                    <option value="inactive" <?php if ($class['statut'] == 'inactive'): ?> selected <?php endif; ?>><?php echo get_phrase('inactive'); ?></option>
                </select>
                <i class="mdi mdi-information exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="status_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('please_select_class_status'); ?></span>
            </div>
        </div>

        <!-- Maximum Members -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-account-group"></i>
                <span><?php echo get_phrase('maximum_number_of_members'); ?></span>
            </label>
            <div class="exp-input-wrapper">
                <input type="number"
                       class="exp-form-input"
                       id="max_members"
                       name="max_members"
                       min="1"
                       value="<?php echo $class['nombre_max_membre']; ?>"
                       placeholder="10">
                <i class="mdi mdi-account-multiple exp-input-icon"></i>
            </div>
        </div>


        <!-- Form Actions -->
        <div class="exp-form-actions">
            <button type="submit" class="exp-btn exp-btn-primary" id="submit-btn">
                <i class="mdi mdi-content-save-edit"></i>
                <span><?php echo get_phrase('update_class'); ?></span>
            </button>
        </div>
    </form>
</div>
<?php } ?>

<script>
$(document).ready(function() {

    // Initialize date picker
    $('#date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true
    });

    // Elements
    const nameInput = $('#name');
    const priceInput = $('#price');
    const statusInput = $('#status_class');
    const isFreeCheckbox = $('#is_free');
    const nameError = $('#name_error');
    const priceError = $('#price_error');
    const statusError = $('#status_error');
    const submitBtn = $('#submit-btn');
    const changeIndicator = $('#change-indicator');

    // Original values
    const originalName = $('#original_name').val();
    const originalPrice = $('#original_price').val();
    const originalStatus = $('#original_status').val();

    // Validation pattern
    const priceRegex = /^[0-9]+(\.[0-9]{1,2})?$/;

    // CSRF Token
    function getCsrfToken() {
        return {
            csrfName: $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name'),
            csrfHash: $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
        };
    }

    // Check for changes
    function checkChanges() {
        const hasChanges = nameInput.val() !== originalName ||
                          priceInput.val() !== originalPrice ||
                          statusInput.val() !== originalStatus;

        if (hasChanges) {
            changeIndicator.addClass('show');
        } else {
            changeIndicator.removeClass('show');
        }
    }

    // Validation functions
    function validateName() {
        const val = nameInput.val().trim();
        if (val.length < 3) {
            nameInput.removeClass('is-valid').addClass('is-invalid');
            nameError.addClass('show');
            return false;
        }
        nameInput.removeClass('is-invalid').addClass('is-valid');
        nameError.removeClass('show');
        return true;
    }

    function validatePrice() {
        // If free class is checked, skip price validation
        if (isFreeCheckbox.is(':checked')) {
            priceInput.removeClass('is-invalid is-valid');
            priceError.removeClass('show');
            return true;
        }

        const val = priceInput.val().trim();
        if (val.length === 0) {
            priceInput.removeClass('is-valid is-invalid');
            priceError.removeClass('show');
            return false;
        }
        if (!priceRegex.test(val)) {
            priceInput.addClass('is-invalid').removeClass('is-valid');
            priceError.addClass('show');
            return false;
        }
        priceInput.addClass('is-valid').removeClass('is-invalid');
        priceError.removeClass('show');
        return true;
    }

    function validateStatus() {
        const val = statusInput.val();
        if (val === '' || val === null) {
            statusInput.addClass('is-invalid').removeClass('is-valid');
            statusError.addClass('show');
            return false;
        }
        statusInput.addClass('is-valid').removeClass('is-invalid');
        statusError.removeClass('show');
        return true;
    }

    // Real-time validation and change detection
    nameInput.on('input', function() {
        validateName();
        checkChanges();
    });

    priceInput.on('input', function() {
        validatePrice();
        checkChanges();
    });

    statusInput.on('change', function() {
        validateStatus();
        checkChanges();
    });

    // Free class checkbox handler
    isFreeCheckbox.on('change', function() {
        if ($(this).is(':checked')) {
            priceInput.val('0').prop('readonly', true).removeClass('is-invalid is-valid');
            priceError.removeClass('show');
        } else {
            <?php if ($type == 'Particulier'): ?>
            priceInput.val('0').prop('readonly', true);
            <?php else: ?>
            priceInput.prop('readonly', false);
            <?php endif; ?>
            validatePrice();
        }
        checkChanges();
    });

    // Initial validation state (mark existing values as valid)
    if (nameInput.val()) {
        validateName();
    }
    if (priceInput.val()) {
        validatePrice();
    }
    if (statusInput.val()) {
        validateStatus();
    }

    // Form submission
    let isSubmitting = false;

    $(".ajaxForm").on("submit", function(e) {
        e.preventDefault();

        if (isSubmitting) return;

        const isNameValid = validateName();
        const isPriceValid = validatePrice();
        const isStatusValid = validateStatus();

        if (!isNameValid || !isPriceValid || !isStatusValid) {
            // Shake animation on invalid fields
            if (!isNameValid) {
                nameInput.addClass('shake');
                setTimeout(() => nameInput.removeClass('shake'), 500);
            }
            if (!isPriceValid) {
                priceInput.addClass('shake');
                setTimeout(() => priceInput.removeClass('shake'), 500);
            }
            if (!isStatusValid) {
                statusInput.addClass('shake');
                setTimeout(() => statusInput.removeClass('shake'), 500);
            }
            return;
        }

        isSubmitting = true;

        // Update button state
        submitBtn.prop('disabled', true)
            .html('<i class="mdi mdi-loading mdi-spin"></i> <span><?php echo get_phrase('updating'); ?>...</span>');

        // Force price to 0 if free class is checked
        if (isFreeCheckbox.is(':checked')) {
            priceInput.val('0');
        }

        const formData = new FormData(this);

        // Ensure file is included in FormData if selected
        const fileInput = $('#student_image')[0];
        if (fileInput && fileInput.files && fileInput.files[0]) {
            formData.set('photo', fileInput.files[0]);
            console.log('File included in FormData:', fileInput.files[0].name);
        }

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                isSubmitting = false;

                if (response.status) {
                    // Success state
                    submitBtn.html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('updated'); ?>!</span>')
                        .css({
                            'background': 'linear-gradient(135deg, #10b981, #34d399)',
                            'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.3)'
                        });

                    changeIndicator.removeClass('show');

                    success_notify(response.notification || '<?php echo get_phrase('class_updated_successfully'); ?>');

                    // Update CSRF
                    if (response.csrf) {
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    }

                    // Reload after delay
                    setTimeout(() => location.reload(), 2000);
                } else {
                    resetButton();
                    error_notify("<?php echo get_phrase('action_not_allowed'); ?>");
                }
            },
            error: function() {
                isSubmitting = false;
                resetButton();
                error_notify("<?php echo get_phrase('an_error_occurred'); ?>");
            }
        });
    });

    function resetButton() {
        submitBtn.prop('disabled', false)
            .html('<i class="mdi mdi-content-save-edit"></i> <span><?php echo get_phrase('update_class'); ?></span>')
            .css({
                'background': '',
                'box-shadow': ''
            });
    }

    /** =============================
     * IMAGE PREVIEW
     * ============================= */
    $('.exp-image-upload').each(function() {
        const input = $(this);
        const previewId = input.data('preview');

        input.on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                const previewContainer = $('#' + previewId);
                const previewImage = previewContainer.find('.exp-preview-image');

                reader.onload = function(e) {
                    previewImage.attr('src', e.target.result);
                    // Add a subtle animation effect
                    previewImage.css('transform', 'scale(1.05)');
                    setTimeout(function() {
                        previewImage.css('transform', 'scale(1)');
                    }, 200);
                };

                reader.readAsDataURL(file);
            }
        });
    });

    function checkPriceForParticulier(input) {
        const userType = "<?php echo $type; ?>";
        const isFreeCheckbox = document.getElementById('is_free');
        const warning = document.getElementById('price-warning');

        // Si "classe gratuite" est cochée, on force 0 et on ignore le reste
        if (isFreeCheckbox && isFreeCheckbox.checked) {
            input.value = '0';
            if (warning) warning.style.display = 'none';
            return;
        }

        const price = parseFloat(input.value) || 0;

        // Autorise uniquement les chiffres et le point
        input.value = input.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

        if (userType === "Particulier" && price > 0) {
            if (warning) warning.style.display = 'block';
        } else {
            if (warning) warning.style.display = 'none';
        }
    }
});
</script>