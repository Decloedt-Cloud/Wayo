<?php 
/**
 * Invoice Edit - Modern Design (Accountant)
 */
$invoice_details = $this->crud_model->get_invoice_by_id($param1); 
$student = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
$is_paid = strtolower($invoice_details['status']) == 'paid';
?>

<style>
/* ============================================================================
   INVOICE EDIT - MODERN FORM DESIGN
   ============================================================================ */
:root {
    --edit-primary: #4f46e5;
    --edit-primary-light: #eef2ff;
    --edit-success: #059669;
    --edit-success-light: #d1fae5;
    --edit-danger: #dc2626;
    --edit-danger-light: #fee2e2;
    --edit-warning: #d97706;
    --edit-dark: #1e293b;
    --edit-gray: #64748b;
    --edit-light: #f8fafc;
    --edit-border: #e2e8f0;
}

.edit-form-container {
    padding: 0;
}

/* Header */
.edit-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    padding: 1.25rem 1.5rem;
    margin: -1rem -1rem 1.5rem -1rem;
    border-radius: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.edit-header-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.edit-header-text h5 {
    margin: 0;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
}

.edit-header-text p {
    margin: 0.25rem 0 0;
    color: #94a3b8;
    font-size: 0.8rem;
}

/* Invoice Summary Card */
.edit-summary {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--edit-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.edit-summary-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.edit-summary-avatar {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #4f46e5;
    font-size: 0.9rem;
}

.edit-summary-info {
    display: flex;
    flex-direction: column;
}

.edit-summary-name {
    font-weight: 600;
    color: var(--edit-dark);
    font-size: 0.95rem;
}

.edit-summary-id {
    font-size: 0.75rem;
    color: var(--edit-gray);
    font-family: monospace;
}

.edit-summary-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.85rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.edit-summary-status.paid {
    background: var(--edit-success-light);
    color: var(--edit-success);
}

.edit-summary-status.unpaid {
    background: var(--edit-danger-light);
    color: var(--edit-danger);
}

.edit-summary-status i {
    font-size: 0.9rem;
}

/* Form Sections */
.edit-section {
    margin-bottom: 1.5rem;
}

.edit-section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--edit-dark);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--edit-border);
}

.edit-section-title i {
    color: var(--edit-primary);
    font-size: 1rem;
}

/* Form Grid */
.edit-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (max-width: 576px) {
    .edit-form-grid {
        grid-template-columns: 1fr;
    }
}

.edit-form-grid.full {
    grid-template-columns: 1fr;
}

/* Form Group */
.edit-form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.edit-form-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--edit-dark);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.edit-form-label i {
    color: var(--edit-primary);
    font-size: 0.9rem;
}

.edit-form-label .required {
    color: var(--edit-danger);
    font-weight: 700;
}

.edit-form-input,
.edit-form-select {
    padding: 0.7rem 1rem;
    border: 2px solid var(--edit-border);
    border-radius: 10px;
    font-size: 0.9rem;
    color: var(--edit-dark);
    background: white;
    transition: all 0.2s;
}

.edit-form-input:focus,
.edit-form-select:focus {
    outline: none;
    border-color: var(--edit-primary);
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.edit-form-select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234f46e5' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
}

/* Amount Inputs */
.edit-amount-input {
    position: relative;
}

.edit-amount-input input {
    padding-left: 3rem;
}

.edit-amount-prefix {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--edit-gray);
    background: var(--edit-light);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

/* Status Toggle */
.edit-status-toggle {
    display: flex;
    gap: 0.5rem;
}

.edit-status-option {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border: 2px solid var(--edit-border);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--edit-gray);
    background: white;
}

.edit-status-option:hover {
    border-color: var(--edit-primary);
    background: var(--edit-primary-light);
}

.edit-status-option.active-paid {
    border-color: var(--edit-success);
    background: var(--edit-success-light);
    color: var(--edit-success);
}

.edit-status-option.active-unpaid {
    border-color: var(--edit-danger);
    background: var(--edit-danger-light);
    color: var(--edit-danger);
}

.edit-status-option input {
    display: none;
}

/* Submit Button */
.edit-submit-area {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--edit-border);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.edit-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.edit-btn-cancel {
    background: var(--edit-light);
    color: var(--edit-gray);
    border: 1px solid var(--edit-border);
}

.edit-btn-cancel:hover {
    background: #e2e8f0;
    color: var(--edit-dark);
}

.edit-btn-submit {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
    min-width: 160px;
}

.edit-btn-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.edit-btn-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.edit-btn-submit i {
    font-size: 1rem;
}
</style>

<div class="edit-form-container">
    <!-- Header -->
    <div class="edit-header">
        <div class="edit-header-icon">
            <i class="mdi mdi-file-document-edit"></i>
        </div>
        <div class="edit-header-text">
            <h5><?php echo get_phrase('edit_invoice'); ?></h5>
            <p>#<?php echo sprintf('%06d', $invoice_details['id']); ?> • <?php echo date('d M Y', $invoice_details['created_at']); ?></p>
        </div>
    </div>
    
    <!-- Summary Card -->
    <div class="edit-summary">
        <div class="edit-summary-left">
            <?php $initials = isset($student['name']) ? strtoupper(substr($student['name'], 0, 2)) : '??'; ?>
            <div class="edit-summary-avatar"><?php echo $initials; ?></div>
            <div class="edit-summary-info">
                <span class="edit-summary-name"><?php echo isset($student['name']) ? htmlspecialchars($student['name']) : get_phrase('unknown'); ?></span>
                <span class="edit-summary-id">INV-<?php echo sprintf('%06d', $invoice_details['id']); ?></span>
            </div>
        </div>
        <span class="edit-summary-status <?php echo $is_paid ? 'paid' : 'unpaid'; ?>">
            <i class="mdi mdi-<?php echo $is_paid ? 'check-circle' : 'clock-outline'; ?>"></i>
            <?php echo $is_paid ? get_phrase('paid') : get_phrase('unpaid'); ?>
        </span>
    </div>
    
    <!-- Form -->
    <form method="POST" class="ajaxForm" id="editInvoiceForm" action="<?php echo route('invoice/update/'.$param1); ?>">
        <input type="hidden" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>" id="csrf_edit" />
        
        <!-- Student Selection -->
        <div class="edit-section">
            <div class="edit-section-title">
                <i class="mdi mdi-account-school"></i>
                <?php echo get_phrase('student_information'); ?>
            </div>
            <div class="edit-form-grid">
                <div class="edit-form-group">
                    <label class="edit-form-label">
                        <i class="mdi mdi-school"></i>
                        <?php echo get_phrase('class'); ?>
                        <span class="required">*</span>
                    </label>
                    <select name="class_id" id="class_id_on_edit" class="edit-form-select select2" required onchange="classWiseStudentOnEdit(this.value)">
                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                <?php $classes = $this->crud_model->get_classes(); ?>
                <?php foreach($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" <?php if ($class['id'] == $invoice_details['class_id']): ?>selected<?php endif; ?>>
                                <?php echo htmlspecialchars($class['name']); ?>
                            </option>
                <?php endforeach; ?>
            </select>
        </div>

                <div class="edit-form-group">
                    <label class="edit-form-label">
                        <i class="mdi mdi-account"></i>
                        <?php echo get_phrase('student'); ?>
                        <span class="required">*</span>
                    </label>
                    <div id="student_content_edit">
                        <select name="student_id" id="student_id_on_edit" class="edit-form-select select2" required>
                    <option value=""><?php echo get_phrase('select_a_student'); ?></option>
                            <?php 
                            $enrolments = $this->user_model->get_student_details_by_id('class', $invoice_details['class_id']);
                    foreach ($enrolments as $enrolment): ?>
                                <option value="<?php echo $enrolment['student_id']; ?>" <?php if ($invoice_details['student_id'] == $enrolment['student_id']): ?>selected<?php endif; ?>>
                                    <?php echo htmlspecialchars($enrolment['name']); ?>
                                </option>
                    <?php endforeach; ?>
                </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="edit-section">
            <div class="edit-section-title">
                <i class="mdi mdi-file-document"></i>
                <?php echo get_phrase('invoice_details'); ?>
            </div>
            <div class="edit-form-grid full">
                <div class="edit-form-group">
                    <label class="edit-form-label">
                        <i class="mdi mdi-text"></i>
                        <?php echo get_phrase('invoice_title'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="title" id="title" class="edit-form-input" value="<?php echo htmlspecialchars($invoice_details['title']); ?>" required placeholder="<?php echo get_phrase('enter_invoice_title'); ?>">
                </div>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="edit-section">
            <div class="edit-section-title">
                <i class="mdi mdi-cash-multiple"></i>
                <?php echo get_phrase('amounts'); ?>
            </div>
            <div class="edit-form-grid">
                <div class="edit-form-group">
                    <label class="edit-form-label">
                        <i class="mdi mdi-currency-usd"></i>
                        <?php echo get_phrase('total_amount'); ?>
                        <span class="required">*</span>
                    </label>
                    <div class="edit-amount-input">
                        <span class="edit-amount-prefix"><?php echo currency_code_and_symbol('code'); ?></span>
                        <input type="number" step="0.01" min="0" name="total_amount" id="total_amount" class="edit-form-input" value="<?php echo $invoice_details['total_amount']; ?>" required style="padding-left: 4rem;">
                    </div>
                </div>
                
                <div class="edit-form-group">
                    <label class="edit-form-label">
                        <i class="mdi mdi-check-circle"></i>
                        <?php echo get_phrase('paid_amount'); ?>
                        <span class="required">*</span>
                    </label>
                    <div class="edit-amount-input">
                        <span class="edit-amount-prefix"><?php echo currency_code_and_symbol('code'); ?></span>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" class="edit-form-input" value="<?php echo $invoice_details['paid_amount']; ?>" required style="padding-left: 4rem;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="edit-section">
            <div class="edit-section-title">
                <i class="mdi mdi-flag"></i>
                <?php echo get_phrase('status'); ?>
            </div>
            <div class="edit-status-toggle">
                <label class="edit-status-option <?php echo $is_paid ? 'active-paid' : ''; ?>" onclick="setStatus('paid')">
                    <input type="radio" name="status" value="paid" <?php echo $is_paid ? 'checked' : ''; ?>>
                    <i class="mdi mdi-check-circle"></i>
                    <?php echo get_phrase('paid'); ?>
                </label>
                <label class="edit-status-option <?php echo !$is_paid ? 'active-unpaid' : ''; ?>" onclick="setStatus('unpaid')">
                    <input type="radio" name="status" value="unpaid" <?php echo !$is_paid ? 'checked' : ''; ?>>
                    <i class="mdi mdi-clock-outline"></i>
                    <?php echo get_phrase('unpaid'); ?>
                </label>
            </div>
        </div>

        <!-- Submit -->
        <div class="edit-submit-area">
            <button type="button" class="edit-btn edit-btn-cancel" onclick="$('#right-modal').modal('hide');">
                <i class="mdi mdi-close"></i>
                <?php echo get_phrase('cancel'); ?>
            </button>
            <button type="submit" class="edit-btn edit-btn-submit" id="submitBtn">
                <i class="mdi mdi-content-save"></i>
                <?php echo get_phrase('update_invoice'); ?>
            </button>
        </div>
    </form>
    </div>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('select.select2').each(function() {
        $(this).select2({ dropdownParent: '#right-modal' });
    });
    
    // Form validation
    $("#editInvoiceForm").validate({});
});

// Status toggle
function setStatus(status) {
    $('.edit-status-option').removeClass('active-paid active-unpaid');
    if (status === 'paid') {
        $('input[value="paid"]').prop('checked', true).closest('.edit-status-option').addClass('active-paid');
    } else {
        $('input[value="unpaid"]').prop('checked', true).closest('.edit-status-option').addClass('active-unpaid');
    }
}

// Load students by class
function classWiseStudentOnEdit(classId) {
    if (!classId) return;
    
    $('#student_id_on_edit').prop('disabled', true);
    
    $.ajax({
        url: "<?php echo route('invoice/student/'); ?>" + classId,
        success: function(response) {
            $('#student_id_on_edit').html(response).prop('disabled', false);
            if ($('#student_id_on_edit').hasClass('select2-hidden-accessible')) {
                $('#student_id_on_edit').select2('destroy');
            }
            $('#student_id_on_edit').select2({ dropdownParent: '#right-modal' });
        }
    });
}

// Form submission
$("#editInvoiceForm").on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var submitBtn = $('#submitBtn');
    var originalText = submitBtn.html();
    
    // Disable button and show loading
    submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase('updating'); ?>...');
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                // Update CSRF token
                if (response.csrf) {
                    $('#csrf_edit').val(response.csrf.hash);
                }
                
                success_notify('<?php echo get_phrase('invoice_updated_successfully'); ?>');
                
                // Refresh invoice list
                if (typeof showAllInvoices === 'function') {
                    showAllInvoices();
                }
                
                // Close modal
                setTimeout(function() {
                    $('#right-modal').modal('hide');
                }, 1000);
            } else {
                error_notify(response.message || '<?php echo get_phrase('action_not_allowed'); ?>');
                submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', status, error);
            error_notify('<?php echo get_phrase('an_error_occurred'); ?>');
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});
</script>
