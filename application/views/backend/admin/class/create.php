<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/single-student-admission.css">


<form method="POST" class="d-block ajaxForm" action="<?php echo route('manage_class/create'); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <div class="image-upload-section">
        <h5><?php echo get_phrase('class_image'); ?></h5>
        <div class="image-preview" id="student-image-preview">

            <img src="<?php echo base_url() . 'uploads/class/placeholder.png'; ?>" alt="classs Image" class="preview-image">

        </div>
        <div class="logo-overlay">
            <i class="fas fa-camera"></i>
        </div>
        <div class="logo-upload-btn">
            <label for="student_image">
                <i class="mdi mdi-cloud-upload"></i> <?php echo get_phrase('upload_an_image'); ?>
            </label>
            <input id="student_image" type="file" class="image-upload" name="photo" accept="image/*" data-preview="student-image-preview">
        </div>

    </div>
    
    <div class="form-group mb-1 col-md-12">
        <label for="name"><?php echo get_phrase('class_name'); ?><span class="required"> * </span></label>
        <input type="text" class="form-control" id="name" name="name">
    </div>
    
    <div class="form-group mb-1 col-md-12">
        <label for="price"><?php echo get_phrase('Price_including_VAT'); ?><span class="required"> * </span></label>
        <?php

         $currencies = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('system_currency'); 
         $type = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('type'); 
         
         ?>
        
        <div class="form-inline">
            <input type="text" <?php if ($type == 'Particulier'): ?> readonly value="0" <?php endif; ?> class="form-control col-md-10" id="price" name="price" >
            <input type="text" class="form-control currency-input col-md-2" value="<?php echo $currencies; ?>" disabled>
            <input type="hidden"  id = "currency" name="currency" value="<?php echo $currencies; ?>" >
        </div>
        
    </div>

     <!-- Date de début -->
    <div class="form-group mb-1 col-md-12">
        <label for="start_date"><?php echo get_phrase('start_date'); ?></label>
        <input type="date" class="form-control" id="start_date" name="start_date"  min="<?php echo date('Y-m-d'); ?>">
    </div>

    <!-- Date de fin -->
    <div class="form-group mb-1 col-md-12">
        <label for="end_date"><?php echo get_phrase('end_date'); ?></label>
        <input type="date" class="form-control" id="end_date" name="end_date"  min="<?php echo date('Y-m-d'); ?>">
    </div>

    <!-- Classe active/inactive -->
    <div class="form-group mb-1 col-md-12">
        <label for="status"><?php echo get_phrase('class_status'); ?></label>
        <select class="form-control" id="status_class" name="status">
            <option value=""><?php echo get_phrase('class_status'); ?></option>
            <option value="active"><?php echo get_phrase('active'); ?></option>
            <option value="inactive"><?php echo get_phrase('inactive'); ?></option>
        </select>
    </div>

    <!-- Nombre max de membres -->
    <div class="form-group mb-1 col-md-12">
        <label for="max_members"><?php echo get_phrase('maximum_number_of_members'); ?></label>
        <input type="number" class="form-control" id="max_members" name="max_members" min="1">
    </div>


    
    <div class="form-group col-md-12">
        <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_class'); ?></button>
    </div>
</div>
</form>

<script>
$(document).ready(function() {

    /** =============================
     * VALIDATION EN TEMPS RÉEL — NOM DE LA CLASSE
     * ============================= */
    const classNameInput = $('#name');
    const classNameError = $('<small id="class-name-error" class="text-danger d-block mt-1" style="display:none;"></small>');
    classNameInput.after(classNameError);

    classNameInput.on('input', function() {
        const value = $(this).val().trim();
        if (value.length < 3) {
            classNameError.text('<?php echo get_phrase('The_name_of_the_class_must_contain_at_least_3_characters'); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            classNameError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    /** =============================
     * VALIDATION EN TEMPS RÉEL — PRIX TTC
     * ============================= */
    const priceInput = $('#price');
    const priceError = $('<small id="price-error" class="text-danger d-block mt-1" style="display:none;"></small>');
    priceInput.closest('.form-inline').after(priceError);

    priceInput.on('input', function() {
        const value = $(this).val().trim();

        if (value === '' || isNaN(value) || parseFloat(value) < 0) {
            priceError.text('<?php echo get_phrase('Invalid_price_(must_be_a_positive_number)'); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            priceError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    /** =============================
     * VALIDATION ET SOUMISSION AJAX
     * ============================= */
    $('.ajaxForm').on('submit', function(e) {
        e.preventDefault(); // Empêche le rechargement immédiat

        let isValid = true;

        // Vérification du nom
        const classValue = classNameInput.val().trim();
        if (classValue.length < 3) {
            classNameError.text('<?php echo get_phrase('The_name_of_the_class_must_contain_at_least_3_characters'); ?>').show();
            classNameInput.addClass('is-invalid');
            isValid = false;
        } else {
            classNameError.hide();
            classNameInput.removeClass('is-invalid');
        }

        // Vérification du prix
        const priceValue = priceInput.val().trim();
        if (priceValue === '' || isNaN(priceValue) || parseFloat(priceValue) < 0) {
            priceError.text('<?php echo get_phrase('Invalid_price_(must_be_a_positive_number)'); ?>').show();
            priceInput.addClass('is-invalid');
            isValid = false;
        } else {
            priceError.hide();
            priceInput.removeClass('is-invalid');
        }

        // ✅ Si erreurs → on bloque complètement la requête AJAX
        if (!isValid) {
            error_notify('<?php echo get_phrase('Please_correct_the_errors_before_submitting'); ?>');
            return false;
        }

        /** =============================
         * Envoi AJAX uniquement si tout est valide
         * ============================= */
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        const adding_text = "<?php echo get_phrase('creating'); ?>...";
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> ' + adding_text);

        // Fonction CSRF
        function getCsrfToken() {
            var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
            var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
            return { csrfName: csrfName, csrfHash: csrfHash };
        }

        const csrf = getCsrfToken();
        const formData = new FormData(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                submitButton.prop('disabled', false).html('<?php echo get_phrase('Submit'); ?>');

                if (response.status) {
                    success_notify(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(function() {
                        location.reload();
                    }, 600);
                } else {
                    error_notify('<?= js_phrase(get_phrase('action_not_allowed')); ?>');
                }
            },
            error: function() {
                submitButton.prop('disabled', false).html('<?php echo get_phrase('Submit'); ?>');
                error_notify('<?= js_phrase(get_phrase('an_error_occurred_during_submission')); ?>');
            }
        });
    });

    /** =============================
     * PRÉVISUALISATION IMAGE
     * ============================= */
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
                    previewContainer.addClass('upload-highlight');
                    setTimeout(function() {
                        previewContainer.removeClass('upload-highlight');
                    }, 1500);
                };

                reader.readAsDataURL(file);
            }
        });
    });

});
</script>

<style>
    .form-inline .form-control {
        display: inline-block;
        width: 75%;
        vertical-align: middle;
    }
    .form-inline .currency-input {
        max-width: 80px;
    }

      .preview-image_style {
      width: 150px;       /* taille personnalisable */
      height: 150px;      /* même hauteur que largeur */
      object-fit: cover;  /* pour garder les proportions sans déformation */
      border-radius: 10%; /* cercle parfait */
      border: 3px solid #007bff; /* optionnel: bordure bleue */
  }
  .image-preview_ci {
    display: flex;
    justify-content: center; /* centre horizontal */
    align-items: center;     /* centre vertical si tu veux */
    margin: 15px 0;          /* un peu d’espace autour */
}
</style>