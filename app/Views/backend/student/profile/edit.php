<?php
$profile_data = $this->user_model->get_profile_data();
?>

<style>
    .error-border {
        border-color: #dc3545 !important;
    }
</style>

<!-- Profile Update Card -->
<div class="modern-card">
    <div class="modern-card-header">
        <h3><i class="fas fa-user-edit"></i> <?php echo get_phrase('update_profile'); ?></h3>
    </div>
    <div class="modern-card-body">
        <form method="POST" class="profileAjaxForm" action="<?php echo route('profile/update_profile') ; ?>" id="profileAjaxForm" enctype="multipart/form-data">
            <!-- Champ caché pour le jeton CSRF -->
            <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
            
            <div class="row">
                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-label" for="name"><?php echo get_phrase('Full_name'); ?> <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="modern-input" value="<?php echo $profile_data['name']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-label" for="email"><?php echo get_phrase('email'); ?> <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="modern-input" value="<?php echo $profile_data['email']; ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-label" for="phone"><?php echo get_phrase('phone'); ?></label>
                        <input type="text" id="phone" name="phone" class="modern-input" value="<?php echo $profile_data['phone']; ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-label" for="Street"><?php echo get_phrase('Rue'); ?> <span class="required">*</span></label>
                        <input type="text" id="Street" name="Street" class="modern-input" placeholder="<?php echo get_phrase('Rue'); ?>" value="<?php echo $profile_data['Rue']; ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="modern-form-group">
                        <label class="modern-label" for="communityNumber"><?php echo get_phrase('Numéro'); ?> <span class="required">*</span></label>
                        <input id="communityNumber" type="text" name="number" class="modern-input" placeholder="<?php echo get_phrase("Numéro") ?>" value="<?php echo $profile_data['Numero']; ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="modern-form-group">
                        <label class="modern-label" for="communityCity"><?php echo get_phrase("Ville") ?> <span class="required">*</span></label>
                        <input id="communityCity" type="text" name="city" class="modern-input" placeholder="<?php echo get_phrase("Ville") ?>" value="<?php echo $profile_data['Ville']; ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="modern-form-group">
                        <label class="modern-label" for="communityPostalCode"><?php echo get_phrase('code_postal'); ?> <span class="required">*</span></label>
                        <input id="communityPostalCode" type="text" name="postal_code" class="modern-input" placeholder="<?php echo get_phrase("code_postal") ?>" value="<?php echo $profile_data['Codepostal']; ?>" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="modern-form-group">
                        <label class="modern-label" for="VAT_number"><?php echo get_phrase('numero_de_tva'); ?></label>
                        <input type="text" id="VAT_number" name="VAT_number" class="modern-input" value="<?php echo $profile_data['num_vat']; ?>" placeholder="<?php echo get_phrase("VAT_number") ?>">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="modern-form-group">
                        <label class="modern-label"><?php echo get_phrase('profile_image'); ?></label>
                        
                        <div class="d-flex align-items-center gap-4 mt-2">
                            <div class="position-relative" style="width: 100px; height: 100px;">
                                <div class="logo-preview" id="profile-image-preview" style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 3px solid var(--border-color); box-shadow: var(--shadow-sm);">
                                    <img src="<?php echo $this->user_model->get_user_image(session()->get('user_id')). '?v=' . time(); ?>" alt="Profile Image" class="preview-image" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                            
                            <div>
                                <label for="profile_image" class="modern-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: var(--bg-main); color: var(--text-dark); border: 1px solid var(--border-color); box-shadow: none;">
                                    <i class="mdi mdi-cloud-upload me-2" style="color: var(--primary);"></i> <?php echo get_phrase('upload_new_photo'); ?>
                                </label>
                                <input id="profile_image" type="file" class="image-upload d-none" name="profile_image" accept="image/*" data-preview="profile-image-preview">
                                <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem;"><?php echo get_phrase('allowed_files'); ?>: .png, .jpg, .jpeg</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="modern-btn" id="update-logos-btn">
                        <i class="mdi mdi-check-circle-outline"></i>
                        <?php echo get_phrase('save_changes'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Password Change Card -->
<div class="modern-card">
    <div class="modern-card-header">
        <h3><i class="fas fa-lock"></i> <?php echo get_phrase('change_password'); ?></h3>
    </div>
    <div class="modern-card-body">
        <form method="POST" class="changePasswordAjaxForm" action="<?php echo route('profile/update_password') ; ?>" id="changePasswordAjaxForm" enctype="multipart/form-data">
            <!-- Champ caché pour le jeton CSRF -->
            <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
            
            <div class="row">
                <div class="col-md-12">
                    <div class="modern-form-group">
                        <label class="modern-label" for="current_password"><?php echo get_phrase('current_password'); ?> <span class="required">*</span></label>
                        <div class="position-relative">
                            <input type="password" id="current_password" name="current_password" class="modern-input" required data-msg-required="<?php echo get_phrase('value_required'); ?>">
                            <i class="fas fa-eye password-toggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-label" for="new_password"><?php echo get_phrase('new_password'); ?> <span class="required">*</span></label>
                        <div class="position-relative">
                            <input type="password" id="new_password" name="new_password" class="modern-input" required data-msg-required="<?php echo get_phrase('value_required'); ?>" data-msg-minlength="<?php echo get_phrase('password_must_be_at_least_8_characters'); ?>">
                            <i class="fas fa-eye password-toggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-label" for="confirm_password"><?php echo get_phrase('confirm_password'); ?> <span class="required">*</span></label>
                        <div class="position-relative">
                            <input type="password" id="confirm_password" name="confirm_password" class="modern-input" required data-msg-required="<?php echo get_phrase('value_required'); ?>">
                            <i class="fas fa-eye password-toggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="modern-btn">
                        <i class="mdi mdi-lock-reset"></i>
                        <?php echo get_phrase('update_password'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    // Image preview handlers
    $('.image-upload').each(function() {
        const input = $(this);
        const previewId = input.data('preview');
        
        input.on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                const previewContainer = $('#' + previewId);
                const previewImage = previewContainer.find('.preview-image');
                
                reader.onload = function(e) {
                    previewImage.attr('src', e.target.result);
                };
                
                reader.readAsDataURL(file);
            }
        });
    });

    // Password toggle handler
    $('.password-toggle').on('click', function() {
        const input = $(this).siblings('input');
        const icon = $(this);
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Fonction pour récupérer et retourner le token CSRF
    function getCsrfToken() {
        var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
        return { csrfName: csrfName, csrfHash: csrfHash };
    }

    // Soumission AJAX du formulaire
    $('.profileAjaxForm, .changePasswordAjaxForm').submit(function(e) {
        e.preventDefault();
        
        // Validation check
        if (typeof $(this).valid === 'function') {
            if (!$(this).valid()) {
                return false;
            }
        } else {
            if (!this.checkValidity()) {
                if (typeof this.reportValidity === 'function') {
                    this.reportValidity();
                }
                return false;
            }
        }

        var submitButton = $(this).find('button[type="submit"]');
        var originalContent = submitButton.html();
        var updating_text = "<?php echo get_phrase('updating'); ?>...";
        
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> ' + updating_text);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                // Remove previous errors
                $('.input-error-message').remove();
                $('.modern-input').removeClass('error-border');

                if (response.status) {
                    // Mise à jour du token CSRF
                    if(response.csrf) {
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    }
                    
                    toastr.success('<?php echo get_phrase('updated_successfully'); ?>');
                    
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    if (response.field) {
                        var field = $('#' + response.field);
                        field.addClass('error-border');
                        field.closest('.modern-form-group').append('<small class="text-danger input-error-message">' + response.notification + '</small>');
                    } else {
                        toastr.error('<?php echo get_phrase('an_error_occurred'); ?>');
                    }
                    submitButton.prop('disabled', false).html(originalContent);
                }
            },
            error: function () {
                toastr.error('<?php echo get_phrase('an_error_occurred'); ?>');
                submitButton.prop('disabled', false).html(originalContent);
            }
        });
    });
});
</script>