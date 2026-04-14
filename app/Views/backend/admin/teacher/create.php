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
    background: #e2e8f0;
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

#email_select {
    font-weight: bold;
    color: var(--form-primary) !important;
    background-color: var(--form-light) !important;
}
</style>

<div class="ec-form-container">
    <!-- Form Header -->
    <div class="ec-form-header">
        <div class="ec-form-icon">
            <i class="mdi mdi-account-tie"></i>
        </div>
        <div class="ec-form-title">
            <h3><?php echo get_phrase('create_teacher'); ?></h3>
            <p><?php echo get_phrase('add_new_teacher_details'); ?></p>
        </div>
    </div>

    <form method="POST" class="d-block ajaxForm" action="<?php echo site_url('app/mentor/create'); ?>" enctype="multipart/form-data">
        <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />
        <input type="hidden" name="existing_user_id" id="existing_user_id" value="">

        <!-- Image Upload Section -->
        <div class="image-upload-section">
            <div class="image-preview" id="image-preview">
                <img src="<?php echo base_url('uploads/users/placeholder.jpg'); ?>" alt="Profile Image">
            </div>
            <div class="logo-upload-btn">
                <label for="image_file">
                    <i class="mdi mdi-camera"></i> <?php echo get_phrase('upload_profile_photo'); ?>
                </label>
                <input type="file" class="image-upload" id="image_file" name="image_file" accept="image/*">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Email -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="email">
                        <i class="mdi mdi-email"></i>
                        <span><?php echo get_phrase("email"); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper" id="email-container">
                        <input type="email" class="ec-form-input" id="email" name="email" required autocomplete="off" placeholder="<?php echo get_phrase("enter_email"); ?>">
                        <i class="mdi mdi-at ec-input-icon"></i>
                    </div>
                    <small class="form-text text-muted email-status" style="margin-top: 5px; display: block;"></small>
                </div>
            </div>
        </div>

        <div class="row regular-fields">
            <div class="col-md-6">
                <!-- Password -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="password">
                        <i class="mdi mdi-lock"></i>
                        <span><?php echo get_phrase("password"); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="password" class="ec-form-input" id="password" name="password">
                        <i class="mdi mdi-key ec-input-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Name -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="name">
                        <i class="mdi mdi-account"></i>
                        <span><?php echo get_phrase("name"); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="text" class="ec-form-input" id="name" name="name">
                        <i class="mdi mdi-format-letter-case ec-input-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row regular-fields">
            <div class="col-md-6">
                <!-- Phone -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="phone">
                        <i class="mdi mdi-phone"></i>
                        <span><?php echo get_phrase("phone_number"); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="text" class="ec-form-input" id="phone" name="phone">
                        <i class="mdi mdi-phone ec-input-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Gender -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="gender">
                        <i class="mdi mdi-gender-male-female"></i>
                        <span><?php echo get_phrase("gender"); ?></span>
                    </label>
                    <div class="ec-input-wrapper">
                        <select name="gender" id="gender" class="ec-form-input">
                            <option value=""><?php echo get_phrase("select_a_gender"); ?></option>
                            <option value="Male"><?php echo get_phrase("male"); ?></option>
                            <option value="Female"><?php echo get_phrase("female"); ?></option>
                            <option value="Others"><?php echo get_phrase("others"); ?></option>
                        </select>
                        <i class="mdi mdi-gender-transgender ec-input-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row regular-fields">
            <div class="col-md-12">
                <!-- Address -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="address">
                        <i class="mdi mdi-map-marker"></i>
                        <span><?php echo get_phrase("address"); ?></span>
                    </label>
                    <div class="ec-input-wrapper">
                        <textarea class="ec-form-input" id="address" name="address" rows="1"></textarea>
                        <i class="mdi mdi-map-marker-radius ec-input-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="always-visible">
            <div class="row">
                <div class="col-md-6">
                    <!-- Designation -->
                    <div class="ec-form-group">
                        <label class="ec-form-label" for="designation">
                            <i class="mdi mdi-briefcase"></i>
                            <span><?php echo get_phrase("designation"); ?></span>
                            <span class="ec-required">*</span>
                        </label>
                        <div class="ec-input-wrapper">
                            <input type="text" class="ec-form-input" id="designation" name="designation" required>
                            <i class="mdi mdi-badge-account-horizontal ec-input-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- LinkedIn -->
                    <div class="ec-form-group">
                        <label class="ec-form-label">
                            <i class="mdi mdi-linkedin"></i>
                            <span><?php echo get_phrase("linkedin_profile_link"); ?></span>
                        </label>
                        <div class="ec-input-wrapper">
                            <input type="text" class="ec-form-input" name="linkedin_link">
                            <i class="mdi mdi-link ec-input-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- About -->
            <div class="ec-form-group">
                <label class="ec-form-label" for="about">
                    <i class="mdi mdi-information"></i>
                    <span><?php echo get_phrase("about"); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <textarea class="ec-form-input" id="about" name="about" rows="3" required></textarea>
                    <i class="mdi mdi-text ec-input-icon"></i>
                </div>
            </div>

            <div class="ec-form-actions">
                <button class="ec-btn ec-btn-primary" id="submit-btn" type="submit" disabled>
                    <i class="mdi mdi-plus"></i> <?php echo get_phrase("create_teacher"); ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const phrases = {
        enter_email: "<?= get_phrase("enter_email") ?>",
        or_enter_new_email: "<?= get_phrase("or_enter_new_email") ?>",
        email_available: "<?= get_phrase("email_available") ?>",
        user_found_click_to_select: "<?= get_phrase("user_found_click_to_select") ?>",
        creating: "<?= get_phrase("creating") ?>",
        create_teacher: "<?= get_phrase("create_teacher") ?>",
        an_error_occurred_during_submission: "<?= get_phrase("an_error_occurred_during_submission") ?>"
    };
    const checkEmailUrl = "<?= site_url('app/check_teacher_email') ?>";

    $(document).ready(function() {
        let emailTimer;
        let isEmailDropdown = false;

        // Image Preview
        $('#image_file').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image-preview img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        function resetToInput() {
            $('#email-container').html(
                `<input type="email" class="ec-form-input" id="email" name="email" required autocomplete="off" placeholder="${phrases.enter_email}">
                 <i class="mdi mdi-at ec-input-icon"></i>`
            );
            $('#existing_user_id').val('');
            $('.regular-fields').slideDown();
            $('#submit-btn').prop('disabled', true);
            isEmailDropdown = false;
            rebindEmailKeyup();
            toggleRequiredFields();
        }

        function toggleRequiredFields() {
            if (isEmailDropdown) {
                $('#name, #password, #phone, #gender, #address').removeAttr('required');
            } else {
                $('#name, #password, #phone').attr('required', 'required');
            }
        }

        function transformToDropdown(user) {
            $('#email-container').html(
                `<select class="ec-form-input" id="email_select" name="email_select" required>
                    <option value="${user.id}">${user.name} (${user.email})</option>
                </select>
                <i class="mdi mdi-account-search ec-input-icon"></i>`
            );
            $('#existing_user_id').val(user.id);
            $('.regular-fields').slideUp();
            $('.always-visible').show();
            $('#submit-btn').prop('disabled', false);
            isEmailDropdown = true;

            $('#email_select').prepend(`<option value="">${phrases.or_enter_new_email}</option>`);
            toggleRequiredFields();
        }

        function rebindEmailKeyup() {
            $(document).off('keyup', '#email');
            $(document).on('keyup', '#email', function() {
                clearTimeout(emailTimer);
                const email = $(this).val().trim();
                const $status = $('.email-status');

                $status.html('').removeClass('text-success text-danger text-warning');
                $('#existing_user_id').val('');
                $('#submit-btn').prop('disabled', true);

                if (email.length < 5) return;

                emailTimer = setTimeout(function() {
                    var csrfName = '<?php echo csrf_token(); ?>';
                    var csrfHash = $('input[name="'+csrfName+'"]').val();

                    $.ajax({
                        url: checkEmailUrl,
                        type: 'POST',
                        data: {
                            email: email,
                            [csrfName]: csrfHash
                        },
                        success: function(res) {
                            let response = typeof res === 'object' ? res : JSON.parse(res);
                            
                            // Update CSRF
                            if(response.csrf) {
                                $('input[name="'+response.csrf.csrfName+'"]').val(response.csrf.csrfHash);
                            }

                            $status.html('').removeClass('text-success text-danger text-warning');
                            $('#existing_user_id').val('');
                            $('#submit-btn').prop('disabled', true);

                            if (response.status === 'new') {
                                $status.html('<span class="text-success"><i class="mdi mdi-check-circle"></i> ' + phrases.email_available + '</span>');
                                $('.regular-fields').slideDown();
                                $('#submit-btn').prop('disabled', false);

                            } else if (response.status === 'exists') {
                                $status.html('<span class="text-warning"><i class="mdi mdi-alert-circle"></i> ' + phrases.user_found_click_to_select + '</span>');
                                transformToDropdown(response.user);

                            } else if (response.status === 'exists_in_school') {
                                $status.html('<span class="text-danger"><i class="mdi mdi-alert-circle"></i> ' + response.message + '</span>');
                                resetToInput();
                                $('#submit-btn').prop('disabled', true);
                            }
                        }
                    });
                }, 600);
            });
        }

        rebindEmailKeyup();

        $(document).on('change', '#email_select', function() {
            if ($(this).val() === '') {
                resetToInput();
                $('.email-status').html('');
            } else {
                $('#existing_user_id').val($(this).val());
                $('#submit-btn').prop('disabled', false);
            }
        });

        $(document).on('blur', '#email', function() {
            if ($(this).val().trim() === '' && !isEmailDropdown) {
                $('.email-status').html('');
                $('#submit-btn').prop('disabled', true);
            }
        });

        $(".ajaxForm").submit(function(e) {
            e.preventDefault();
            const $btn = $('#submit-btn');
            const originalBtnHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> ' + phrases.creating + '...');

            const formData = new FormData(this);

            if (isEmailDropdown && $('#email_select').length) {
                formData.set('existing_user_id', $('#existing_user_id').val());
                formData.delete('email');
                formData.delete('password');
                formData.delete('name');
                formData.delete('phone');
                formData.delete('gender');
                formData.delete('address');
                formData.delete('image_file');
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        if(typeof showNotification === 'function') {
                             showNotification('success', response.notification);
                        } else if(typeof toastr !== 'undefined') {
                             toastr.success(response.notification);
                        }
                        
                        if (response.csrf) {
                            $('input[name="' + response.csrf.csrfName + '"]').val(response.csrf.csrfHash);
                        }
                        
                        // Close modal or reload
                        setTimeout(() => {
                             // Close modal first
                             $('#right-modal').modal('hide');
                             $('.modal').modal('hide');

                             // Reload the list
                             if(typeof showAllTeachers === 'function') {
                                 showAllTeachers();
                             } else {
                                 location.reload();
                             }
                        }, 1000);
                    } else {
                        if(typeof showNotification === 'function') {
                             showNotification('error', response.notification || 'Erreur');
                        } else if(typeof toastr !== 'undefined') {
                             toastr.error(response.notification || 'Erreur');
                        }
                        $btn.prop('disabled', false).html(originalBtnHtml);
                        
                        if(response.csrf) {
                            $('input[name="' + response.csrf.csrfName + '"]').val(response.csrf.csrfHash);
                        }
                    }
                },
                error: function() {
                    if(typeof showNotification === 'function') {
                         showNotification('error', phrases.an_error_occurred_during_submission);
                    } else if(typeof toastr !== 'undefined') {
                         toastr.error(phrases.an_error_occurred_during_submission);
                    }
                    $btn.prop('disabled', false).html(originalBtnHtml);
                }
            });
        });
    });
</script>
