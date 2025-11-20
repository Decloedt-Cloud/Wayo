<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/single-student-admission.css">

<?php $school_id = school_id(); ?>

<form method="POST" class="p-3 d-block ajaxForm" action="<?php echo route('student/create_single_student/submit'); ?>" id="student_admission_form" enctype="multipart/form-data" novalidate>
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

    <!-- Image -->
    <div class="image-upload-section">
        <h5><?php echo get_phrase('student_profile_image'); ?></h5>
        <div class="image-preview" id="student-image-preview">
            <img src="<?php echo $this->user_model->get_user_image($student['user_id']) . '?v=' . time(); ?>" alt="Student Profile Image" class="preview-image">
        </div>
        <div class="logo-overlay">
            <i class="fas fa-camera"></i>
        </div>
        <div class="logo-upload-btn">
            <label for="student_image">
                <i class="mdi mdi-cloud-upload"></i> <?php echo get_phrase('upload_an_image'); ?>
            </label>
            <input id="student_image" type="file" class="image-upload" name="student_image" accept="image/*" data-preview="student-image-preview">
        </div>
    </div>

    <!-- Formulaire -->
    <div class="form-grid">
        <div class="form-group">
            <label class="col-form-label" for="name"><?php echo get_phrase('Full_name'); ?> <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" placeholder="<?php echo get_phrase("Full_name") ?>" required>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="email"><?php echo get_phrase('email'); ?> <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control" placeholder="<?php echo get_phrase("email") ?>" required>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="class_id"><?php echo get_phrase('class'); ?> <span class="required">*</span></label>
            <select name="class_id" id="class_id_add" class="form-control" required>
                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                <?php $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array(); ?>
                <?php foreach ($classes as $class) { ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="gender"><?php echo get_phrase('gender'); ?> <span class="required">*</span></label>
            <select name="gender" id="gender" class="form-control" required>
                <option value=""><?php echo get_phrase('select_gender'); ?></option>
                <option value="Male"><?php echo get_phrase('male'); ?></option>
                <option value="Female"><?php echo get_phrase('female'); ?></option>
                <option value="Others"><?php echo get_phrase('others'); ?></option>
            </select>
        </div>

         <?php 
        // Calculer la date limite pour 18 ans
        $today = date('Y-m-d');
        $eighteen_years_ago = date('Y-m-d', strtotime('-18 years'));
        ?>
        <div class="form-group">
            <label class="col-form-label" for="birthday"><?php echo get_phrase('birthday'); ?> <span class="required">*</span></label>
            <input type="date"  max="<?php echo $eighteen_years_ago; ?>" id="birthday" name="birthday" class="form-control" data-provide="datepicker" data-date-autoclose="true" data-date-container="#datepicker4">
        </div>
       

        <div class="form-group">
            <label class="col-form-label" for="phone"><?php echo get_phrase('phone'); ?> <span class="required">*</span></label>
            <input type="text" id="phone" name="phone" class="form-control" placeholder="<?php echo get_phrase("phone") ?>" required>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="Street"><?php echo get_phrase('Rue'); ?> <span class="required">*</span></label>
            <input type="text" id="Street" name="Street" class="form-control" placeholder="<?php echo get_phrase('Rue'); ?>" required>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="communityNumber"><?php echo get_phrase('Numéro'); ?> <span class="required">*</span></label>
            <input type="text" id="communityNumber" name="number" class="form-control" placeholder="<?php echo get_phrase('Numéro'); ?>" required>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="communityCity"><?php echo get_phrase('Ville'); ?> <span class="required">*</span></label>
            <input type="text" id="communityCity" name="city" class="form-control" placeholder="<?php echo get_phrase('Ville'); ?>" required>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="communityPostalCode"><?php echo get_phrase('code_postal'); ?> <span class="required">*</span></label>
            <input type="text" id="communityPostalCode" name="postal_code" class="form-control" placeholder="<?php echo get_phrase('code_postal'); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="VAT_number"><?php echo get_phrase('numero_de_tva'); ?></label>
        <input type="text" id="VAT_number" name="VAT_number" class="form-control" placeholder="<?php echo get_phrase('VAT_number'); ?>">
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="action-btn btn btn-primary btn-modern col-md-4 col-sm-12">
            <i class="bi bi-check-circle mdi mdi-account-plus-outline"></i> <?php echo get_phrase('add_students'); ?>
        </button>
    </div>
</form>

<!-- Script -->
<script type="text/javascript">
$(document).ready(function() {

    const regex = {
        name: /^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        phone: /^(\+?\d{1,3}[- ]?)?\d{8,15}$/,
        street: /^[A-Za-z0-9À-ÖØ-öø-ÿ' -]{3,100}$/,
        number: /^\d{1,5}$/,
        city: /^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/,
        postal: /^\d{4,10}$/,
        vat: /^[A-Za-z0-9]{8,15}$/
    };

    // ======= Fonctions utilitaires =======
    function showError(input, msg) {
        $(input).addClass('is-invalid');
        $(input).siblings('.error-text').remove();
        $(input).after('<small class="error-text text-danger">' + msg + '</small>');
    }

    function clearError(input) {
        $(input).removeClass('is-invalid');
        $(input).siblings('.error-text').remove();
    }

    // ======= Validation de la date de naissance =======
    function validateBirthday(input) {
        const value = $(input).val();
        clearError(input);

       if (!value) {
            showError(input, "<?= get_phrase('please_enter_your_date_of_birth'); ?>");
            return false;
        }

        const birthDate = new Date(value);
        const today = new Date();

        if (birthDate > today) {
            showError(input, "<?= get_phrase('birth_date_cannot_be_in_the_future'); ?>");
            return false;
        }

        return true; // ok
    }

    // ======= Validation générique =======
    function validateField(input) {
        const id = $(input).attr('id');
        const val = $(input).val().trim();
        clearError(input);

        switch(id) {
            case 'name':
                if (!regex.name.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_name'); ?>"); return false; }
                break;
            case 'email':
                if (!regex.email.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_email_address'); ?>"); return false; }
                break;
            case 'phone':
                if (!regex.phone.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_phone_number'); ?>"); return false; }
                break;
            case 'Street':
                if (!regex.street.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_street'); ?>"); return false; }
                break;
            case 'communityNumber':
                if (!regex.number.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_number'); ?>"); return false; }
                break;
            case 'communityCity':
                if (!regex.city.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_city'); ?>"); return false; }
                break;
            case 'communityPostalCode':
                if (!regex.postal.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_postal_code'); ?>"); return false; }
                break;
            case 'VAT_number':
                if (val && !regex.vat.test(val)) { showError(input, "<?= get_phrase('invalid_vat_number'); ?>"); return false; }
                break;
            case 'birthday':
        return validateBirthday(input);
}
        return true;
    }

    // ======= Validation en direct =======
    $('input, select').on('input change', function() {
        validateField(this);
    });

    // ======= Vérification avant soumission =======
    function checkRequiredFields() {
        let valid = true;
        $('input[required], select[required], #birthday').each(function() {
            if (!validateField(this)) valid = false;
        });
        return valid;
    }

    // ======= Validation générique =======
    function validateField(input) {
        const id = $(input).attr('id');
        const val = $(input).val().trim();
        clearError(input);

        switch(id) {
            case 'name':
                if (!regex.name.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_name'); ?>"); return false; }
                break;

            case 'email':
                if (!regex.email.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_email_address'); ?>"); return false; }
                break;

            case 'phone':
                if (!regex.phone.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_phone_number'); ?>"); return false; }
                break;

            case 'Street':
                if (!regex.street.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_street'); ?>"); return false; }
                break;

            case 'communityNumber':
                if (!regex.number.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_number'); ?>"); return false; }
                break;

            case 'communityCity':
                if (!regex.city.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_city'); ?>"); return false; }
                break;

            case 'communityPostalCode':
                if (!regex.postal.test(val)) { showError(input, "<?= get_phrase('please_enter_a_valid_postal_code'); ?>"); return false; }
                break;

            case 'VAT_number':
                if (val && !regex.vat.test(val)) { showError(input, "<?= get_phrase('invalid_vat_number'); ?>"); return false; }
                break;

            case 'birthday':
                return validateBirthday(input);
}
        return true;
    }

    // ======= Déclenchement en direct =======
    $('input, select').on('input change', function() {
        validateField(this);
    });

    // ======= Vérifie tous les champs avant soumission =======
    function checkRequiredFields() {
        let valid = true;

        // ✅ on ajoute birthday même s'il n’a pas "required" dans le HTML
        $('input[required], select[required], #birthday').each(function() {
            if (!validateField(this)) valid = false;
        });

        return valid;
    }

    // ==================== AJAX SUBMIT ====================
    $(".ajaxForm").submit(function(e) {
        e.preventDefault();
        var form = $(this);

        if (!checkRequiredFields()) {
            error_notify("<?= get_phrase('please_fix_the_errors_before_submitting'); ?>");
            return;
        }

        var adding_text = "<?= get_phrase('adding'); ?>";
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> ' + adding_text);

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    success_notify(response.message);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    refreshForm(form);

                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 2000);

                } else {
                    error_notify(response.message);
                    submitBtn.prop('disabled', false).html('<?= get_phrase("add_students"); ?>');
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                error_notify('<?= get_phrase("request_failed"); ?>: ' + xhr.statusText);
                submitBtn.prop('disabled', false).html('<?= get_phrase("add_students"); ?>');
            }
        });
    });

});
</script>
