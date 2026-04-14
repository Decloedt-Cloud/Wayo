<?php
$users = db()->table('users')->where('id', $param1)->get()->getResultArray();
foreach($users as $user):
?>

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
    text-decoration: none;
}

.ec-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.ec-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4);
    color: white;
}

.ec-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
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
            <i class="mdi mdi-account-edit"></i>
        </div>
        <div class="ec-form-title">
            <h3><?php echo get_phrase('update_accountant'); ?></h3>
            <p><?php echo get_phrase('update_accountant_information'); ?></p>
        </div>
    </div>

    <form method="POST" class="d-block ajaxForm" action="<?php echo route('accountant/update/'.$param1); ?>" enctype="multipart/form-data">
        <!-- Champ caché pour le jeton CSRF -->
        <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
        
        <!-- Image Upload Section -->
        <div class="image-upload-section">
            <div class="image-preview" id="image-preview">
                <img src="<?php echo $this->user_model->get_user_image($user['id']); ?>" alt="Profile Image">
            </div>
            <div class="logo-upload-btn">
                <label for="image_file">
                    <i class="mdi mdi-camera"></i> <?php echo get_phrase('change_profile_photo'); ?>
                </label>
                <input type="file" class="image-upload" id="image_file" name="image_file" accept="image/*">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Name -->
                <div class="ec-form-group">
                    <label class="ec-form-label">
                        <i class="mdi mdi-account"></i>
                        <span><?php echo get_phrase('name'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="text" value="<?php echo $user['name']; ?>" class="ec-form-input" id="name" name="name" required>
                        <i class="mdi mdi-format-letter-case ec-input-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Email -->
                <div class="ec-form-group">
                    <label class="ec-form-label">
                        <i class="mdi mdi-email"></i>
                        <span><?php echo get_phrase('email'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="email" value="<?php echo $user['email']; ?>" class="ec-form-input" id="email" name="email" required>
                        <i class="mdi mdi-at ec-input-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Phone -->
                <div class="ec-form-group">
                    <label class="ec-form-label">
                        <i class="mdi mdi-phone"></i>
                        <span><?php echo get_phrase('phone_number'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="text" value="<?php echo $user['phone']; ?>" class="ec-form-input" id="phone" name="phone" required>
                        <i class="mdi mdi-cellphone ec-input-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Gender -->
                <div class="ec-form-group">
                    <label class="ec-form-label">
                        <i class="mdi mdi-gender-male-female"></i>
                        <span><?php echo get_phrase('gender'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <select name="gender" id="gender" class="ec-form-input" required>
                            <option value=""><?php echo get_phrase('select_a_gender'); ?></option>
                            <option value="Male" <?php if($user['gender'] == 'Male') echo 'selected'; ?>><?php echo get_phrase('male'); ?></option>
                            <option value="Female" <?php if($user['gender'] == 'Female') echo 'selected'; ?>><?php echo get_phrase('female'); ?></option>
                            <option value="Others" <?php if($user['gender'] == 'Others') echo 'selected'; ?>><?php echo get_phrase('others'); ?></option>
                        </select>
                        <i class="mdi mdi-gender-male-female ec-input-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Info Card -->
        <div class="ec-info-card mt-3">
            <div class="ec-info-card-icon">
                <i class="mdi mdi-map-marker-radius"></i>
            </div>
            <div class="ec-info-card-content">
                <h4><?php echo get_phrase('address_details'); ?></h4>
                <p><?php echo get_phrase('provide_accountant_address_information'); ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Address -->
                <div class="ec-form-group">
                    <label class="ec-form-label">
                        <i class="mdi mdi-map-marker"></i>
                        <span><?php echo get_phrase('address'); ?></span>
                    </label>
                    <div class="ec-input-wrapper">
                        <textarea class="ec-form-input" id="address" name="address" rows="3" style="height: auto;"><?php echo $user['address']; ?></textarea>
                        <i class="mdi mdi-map-marker-outline ec-input-icon" style="top: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="ec-form-actions">
            <button class="ec-btn ec-btn-primary" id="update-btn" type="submit">
                <i class="mdi mdi-account-check"></i>
                <?php echo get_phrase('update_accountant'); ?>
            </button>
        </div>
    </form>
</div>

<?php endforeach; ?>

<script>
$(document).ready(function () {
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });
});

// Image preview
$('#image_file').change(function(){
    let reader = new FileReader();
    reader.onload = (e) => {
        $('#image-preview img').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});

$(".ajaxForm").submit(function(e) {
    e.preventDefault();
    var form = $(this);
    var submitButton = form.find('button[type="submit"]');
    var originalBtnHtml = submitButton.html();
    var updating_text = "<?php echo get_phrase('updating'); ?>...";
    
    submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> '+updating_text);
    
    const formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response.status) {
                if(typeof showNotification === 'function') {
                    showNotification('success', response.notification);
                } else if(typeof toastr !== 'undefined') {
                    toastr.success(response.notification);
                }
                
                // Update CSRF token
                if(response.csrf) {
                    $('input[name="' + response.csrf.csrfName + '"]').val(response.csrf.csrfHash);
                }

                setTimeout(function() {
                    // Close modal first to ensure UI feedback
                    $('#right-modal').modal('hide');
                    $('.modal').modal('hide');
                    
                    if(typeof showAllAccountants === 'function') {
                        showAllAccountants();
                    } else {
                        location.reload();
                    }
                }, 1000);
            } else {
                if(typeof showNotification === 'function') {
                    showNotification('error', response.notification || '<?php echo get_phrase('action_not_allowed'); ?>');
                } else if(typeof toastr !== 'undefined') {
                    toastr.error(response.notification || '<?php echo get_phrase('action_not_allowed'); ?>');
                }
                
                if(response.csrf) {
                    $('input[name="' + response.csrf.csrfName + '"]').val(response.csrf.csrfHash);
                }
                submitButton.prop('disabled', false).html(originalBtnHtml);
            }
        },
        error: function () {
            if(typeof showNotification === 'function') {
                showNotification('error', '<?php echo get_phrase('an_error_occurred_during_submission'); ?>');
            } else if(typeof toastr !== 'undefined') {
                toastr.error('<?php echo get_phrase('an_error_occurred_during_submission'); ?>');
            }
            submitButton.prop('disabled', false).html(originalBtnHtml);
        }
    });
});
</script>

