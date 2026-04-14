<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/single-student-admission.min.css">

<?php $classes = isset($student_create_classes) && is_array($student_create_classes) ? $student_create_classes : []; ?>

<form method="POST" class="p-3 d-block ajaxForm" action="<?php echo route('student/create_single_student/submit'); ?>" id="student_admission_form" enctype="multipart/form-data">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />

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

    <div class="form-grid">
        <div class="form-group ">
            <label class="col-form-label" for="name"><?php echo get_phrase('Full_name'); ?><span class="required"> * </span></label>
            <div class="input-wrapper">
                <input type="text" id="name" name="name" class="form-control" placeholder="<?php echo get_phrase("Full_name") ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="email"><?php echo get_phrase('email'); ?><span class="required"> * </span></label>
            <div class="input-wrapper">
                <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo get_phrase("email") ?>" >
            </div>
        </div>



        <div class="form-group ">
            <label class="col-form-label" for="class_id"><?php echo get_phrase('class'); ?><span class="required"> *</span></label>
            <select name="class_id" id="class_id_add" class="form-control" >
                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                <?php foreach ($classes as $class) { ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="col-form-label" for="gender"><?php echo get_phrase('gender'); ?><span class="required"> *</span></label>
            <select name="gender" id="gender" class="form-control" >
                <option value=""><?php echo get_phrase('select_gender'); ?></option>
                <option value="Male"><?php echo get_phrase('male'); ?></option>
                <option value="Female"><?php echo get_phrase('female'); ?></option>
                <option value="Others"><?php echo get_phrase('others'); ?></option>
            </select>

        </div>


        <div class="form-group ">
            <label class="col-form-label" for="birthday"><?php echo get_phrase('birthday'); ?><span class="required"> *</span></label>
            <input type="text" class="form-control" data-provide="datepicker" id="birthday"  data-date-autoclose="true" data-date-container="#datepicker4" name="birthday" value="">
        </div>
        <div class="form-group ">
            <label class="col-form-label" for="phone"><?php echo get_phrase('phone'); ?><span class="required"> *</span></label>
            <input type="text" id="phone" name="phone" class="form-control" placeholder="<?php echo get_phrase("phone") ?>" >
        </div>
        <div class="form-group ">
            <label class="col-form-label" for="Rue"><?php echo get_phrase('Rue'); ?><span class="required"> *</span></label>
            <input type="text" class="form-control" id="Street" placeholder="<?php echo get_phrase('Rue'); ?>"  name="Street"  >
        </div>
        <div class="form-group ">
            <label class="col-form-label" for="phone"><?php echo get_phrase('Numéro'); ?><span class="required"> *</span></label>
            <input id="communityNumber" type="text" placeholder="<?php echo get_phrase("Numéro") ?>" class="form-control shadow-none" name="number" 
                          data-msg="<?php echo get_phrase("Veuillez entrer le numéro") ?>" data-error-class="u-has-error" data-success-class="u-has-success">
        </div>
        <div class="form-group ">                 
                 
            <label class="col-form-label" for="Ville"><?php echo get_phrase("Ville") ?><span class="required"> *</span></label>
             <input id="communityCity" type="text" placeholder="<?php echo get_phrase("Ville") ?>" class="form-control shadow-none" name="city" 
                    data-msg="<?php echo get_phrase("Veuillez entrer la ville") ?>" data-error-class="u-has-error" data-success-class="u-has-success">
        </div>
        <div class="form-group ">
            <label class="col-form-label" for="code_postal"><?php echo get_phrase('code_postal'); ?><span class="required"> *</span></label>
            <input id="communityPostalCode" type="text" placeholder="<?php echo get_phrase("code_postal") ?>" class="form-control shadow-none" name="postal_code" 
            data-msg="<?php echo get_phrase("Veuillez entrer le postal code") ?>" data-error-class="u-has-error" data-success-class="u-has-success">
        </div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="VAT number"><?php echo get_phrase('numero_de_tva'); ?></label>
        <input type="text" id="VAT_number" name="VAT_number" class="form-control" placeholder="<?php echo get_phrase("VAT_number") ?>" >

    </div>

        <div class="text-center mt-4">
                <button type="submit" class="action-btn btn btn-primary btn-modern col-md-4 col-sm-12">
                    <i class="bi bi-check-circle mdi mdi-account-plus-outline "></i> <?php echo get_phrase('add_students'); ?>
                </button>
        </div>

</form>

<script>
$(document).ready(function() {

    /** =============================
     * CRÉATION DES MESSAGES D'ERREUR <small>
     * ============================= */
    const nameInput = $('#name');
    const nameError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    nameInput.after(nameError);

    const emailInput = $('#email');
    const emailError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    emailInput.after(emailError);

    const phoneInput = $('#phone');
    const phoneError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    phoneInput.after(phoneError);

    const streetInput = $('#Street');
    const streetError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    streetInput.after(streetError);

    const numberInput = $('#communityNumber');
    const numberError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    numberInput.after(numberError);

    const cityInput = $('#communityCity');
    const cityError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    cityInput.after(cityError);

    const postalInput = $('#communityPostalCode');
    const postalError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    postalInput.after(postalError);

    const classInput = $('#class_id_add');
    const classError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    classInput.after(classError);

    const genderInput = $('#gender');
    const genderError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    genderInput.after(genderError);

    const birthdayInput = $('#birthday');
    const birthdayError = $('<small class="text-danger d-block mt-1" style="display:none;"></small>');
    birthdayInput.after(birthdayError);

    /** =============================
     * VALIDATION EN TEMPS RÉEL
     * ============================= */
    nameInput.on('input', function() {
        const value = $(this).val().trim();
        if (value.length < 3) {
            nameError.text('<?php echo get_phrase("Full_name_must_contain_at_least_3_characters"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            nameError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    emailInput.on('input', function() {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test($(this).val().trim())) {
            emailError.text('<?php echo get_phrase("Please_enter_a_valid_email"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            emailError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    phoneInput.on('input', function() {
        const regex = /^[0-9]{8,15}$/;
        if (!regex.test($(this).val().trim())) {
            phoneError.text('<?php echo get_phrase("Please_enter_a_valid_phone_number"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            phoneError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    streetInput.on('input', function() {
        if ($(this).val().trim() === "") {
            streetError.text('<?php echo get_phrase("Street_cannot_be_empty"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            streetError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    numberInput.on('input', function() {
        if ($(this).val().trim() === "") {
            numberError.text('<?php echo get_phrase("Number_cannot_be_empty"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            numberError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    cityInput.on('input', function() {
        if ($(this).val().trim() === "") {
            cityError.text('<?php echo get_phrase("City_cannot_be_empty"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            cityError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    postalInput.on('input', function() {
        const regex = /^[0-9]{4,10}$/;
        if (!regex.test($(this).val().trim())) {
            postalError.text('<?php echo get_phrase("Please_enter_a_valid_postal_code"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            postalError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    classInput.on('change', function() {
        if ($(this).val() === "") {
            classError.text('<?php echo get_phrase("Please_select_a_class"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            classError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    genderInput.on('change', function() {
        if ($(this).val() === "") {
            genderError.text('<?php echo get_phrase("Please_select_gender"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            genderError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    birthdayInput.on('change', function() {
        if ($(this).val().trim() === "") {
            birthdayError.text('<?php echo get_phrase("Please_select_birthday"); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            birthdayError.hide();
            $(this).removeClass('is-invalid');
        }
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

    /** =============================
     * SOUMISSION AJAX AVEC VALIDATION
     * ============================= */
    $('#student_admission_form').on('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        // Champs texte
        if (!/^[a-zA-Z\s]{3,}$/.test(nameInput.val().trim())) { isValid=false; nameInput.addClass('is-invalid'); nameError.show(); } else { nameInput.removeClass('is-invalid'); nameError.hide(); }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.val().trim())) { isValid=false; emailInput.addClass('is-invalid'); emailError.show(); } else { emailInput.removeClass('is-invalid'); emailError.hide(); }
        if (!/^[0-9]{8,15}$/.test(phoneInput.val().trim())) { isValid=false; phoneInput.addClass('is-invalid'); phoneError.show(); } else { phoneInput.removeClass('is-invalid'); phoneError.hide(); }
        if (streetInput.val().trim()==="") { isValid=false; streetInput.addClass('is-invalid'); streetError.show(); } else { streetInput.removeClass('is-invalid'); streetError.hide(); }
        if (numberInput.val().trim()==="") { isValid=false; numberInput.addClass('is-invalid'); numberError.show(); } else { numberInput.removeClass('is-invalid'); numberError.hide(); }
        if (cityInput.val().trim()==="") { isValid=false; cityInput.addClass('is-invalid'); cityError.show(); } else { cityInput.removeClass('is-invalid'); cityError.hide(); }
        if (!/^[0-9]{4,10}$/.test(postalInput.val().trim())) { isValid=false; postalInput.addClass('is-invalid'); postalError.show(); } else { postalInput.removeClass('is-invalid'); postalError.hide(); }

        // Champs select / date
        if (classInput.val() === "") { isValid=false; classInput.addClass('is-invalid'); classError.show(); } else { classInput.removeClass('is-invalid'); classError.hide(); }
        if (genderInput.val() === "") { isValid=false; genderInput.addClass('is-invalid'); genderError.show(); } else { genderInput.removeClass('is-invalid'); genderError.hide(); }
        if (birthdayInput.val().trim() === "") { isValid=false; birthdayInput.addClass('is-invalid'); birthdayError.show(); } else { birthdayInput.removeClass('is-invalid'); birthdayError.hide(); }

        if (!isValid) {
            error_notify('<?php echo get_phrase("Please_correct_the_errors_before_submitting"); ?>');
            return false;
        }

        // Soumission AJAX
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        const adding_text = "<?php echo get_phrase('adding'); ?>...";
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> ' + adding_text);

        const formData = new FormData(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                submitButton.prop('disabled', false).html('<?php echo get_phrase("add_students"); ?>');
                if (response.status) {
                    success_notify(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 600);
                } else {
                    error_notify('<?= js_phrase(get_phrase("action_not_allowed")); ?>');
                }
            },
            error: function() {
                submitButton.prop('disabled', false).html('<?php echo get_phrase("add_students"); ?>');
                error_notify('<?= js_phrase(get_phrase("an_error_occurred_during_submission")); ?>');
            }
        });
    });

});
</script>


<!-- <script type="text/javascript">
    $(document).ready(function() {
        $(".ajaxForm").validate();

        // Gestionnaire de prévisualisation d'image
        // Image preview handlers
        $('.image-upload').each(function() {
            const input = $(this); // Sélectionne chaque champ d'upload d'image individuellement
            const previewId = input.data('preview'); // Récupère l'ID du conteneur de prévisualisation depuis l'attribut data-preview

            input.on('change', function() { // Ajoute un événement de changement lorsque l'utilisateur sélectionne un fichier
                const file = this.files[0]; // Récupère le premier fichier sélectionné
                if (file) {
                    const reader = new FileReader(); // Crée un objet FileReader pour lire le fichier
                    const previewContainer = $('#' + previewId); // Sélectionne le conteneur de prévisualisation
                    const previewImage = previewContainer.find('.preview-image'); // Sélectionne l'image de prévisualisation à l'intérieur du conteneur

                    reader.onload = function(e) { // Exécute cette fonction lorsque le fichier est lu avec succès
                        previewImage.attr('src', e.target.result); // Met à jour la source de l'image avec l'URL du fichier chargé

                        // Ajoute un effet d'animation visuelle pour signaler l'upload
                        previewContainer.addClass('upload-highlight');
                        setTimeout(function() {
                            previewContainer.removeClass('upload-highlight'); // Supprime l'effet après 1,5 seconde
                        }, 1500);
                    };

                    reader.readAsDataURL(file); // Lit le fichier sous forme d'URL de données (base64)
                }
            });
        });


        // Fonction pour récupérer et retourner le token CSRF
        function getCsrfToken() {
            // Récupérer le nom du token CSRF depuis le champ input caché
            var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
            // Récupérer la valeur (hash) du token CSRF depuis le champ input caché
            var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
            // Retourner un objet contenant le nom du token et sa valeur
            return {
                csrfName: csrfName,
                csrfHash: csrfHash
            };
        }
        // Fonction de réinitialisation du formulaire

        var refreshForm = function(form) {
            form.trigger("reset");
        };


        // Soumission du formulaire de logo
        $(".ajaxForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            // Appel de la fonction check() pour valider les champs
            if (!check()) { // Si check() renvoie false, on arrête la soumission
                return;
            }

            // Obtenez le texte de mise à jour traduit
            var adding_text = "<?php echo get_phrase('adding'); ?>";
            // Afficher un indicateur de chargement
            $('button[type="submit"]').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>' + adding_text);
            // Récupérer le token CSRF avant l'envoi
            var csrf = getCsrfToken(); // Appel de la fonction pour obtenir le token
            const formData = new FormData(this); // Crée une nouvelle instance de FormData en passant l'élément du formulaire courant

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    // console.log(response);  // Ajoutez cette ligne pour déboguer
                    if (response.status) { // Vérifie si la mise à jour a réussi

                        //console.log("Étudiant ajouté avec succès."); pour testé

                        //affiché notification success
                        success_notify(response.message);

                        // Met à jour le token CSRF
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                        // Mise à jour dynamique des images avec cache-buster (pour forcer le rechargement des images)
                        $('.preview-image').each(function() {
                            var originalSrc = $(this).attr('src').split('?')[0]; // Récupère l'URL d'origine de l'image sans la chaîne de requête
                            // $(this).attr('src', originalSrc + '?v=' + Date.now());// Ajoute un timestamp pour éviter la mise en cache cette ligne affiche alt du l'image avant affichage du l'image apres update
                        });
                        // Réinitialiser le formulaire
                        refreshForm(form);

                        // Redirige après 2 secondes
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 4000); //permet à la notification de succès d'être visible pendant 4 secondes

                    } else {
                        error_notify(response.message);

                        // Re-enable submit button
                        $('button[type="submit"]').prop('disabled', false)
                            .html('<?= get_phrase("add_student"); ?>');
                    }
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                },
                error: function(xhr) {
                    error_notify('<?= get_phrase("request_failed"); ?>: ' + xhr.statusText);
                    $('button[type="submit"]').prop('disabled', false)
                        .html('<?= get_phrase("add_student"); ?>');
                }

            });
        });
    });

    function check() {
        var name = $("#name").val();
        var email = $("#email").val();
        //var password = $("#password").val();
        var parent_id = $("#parent_id").val();
        var class_id = $("#class_id").val();
        var birthday = $("#birthday").val();
        var gender = $("#gender").val();
        var address = $("#address").val();
        var phone = $("#phone").val();
        if (name == "" || email == "" || class_id == "" ||
            birthday == "" || gender == "" || address == "" || phone == "") {
            error_notify('<?php echo get_phrase('please_select_in_all_fields !'); ?>');
            return false;
        }
        return true;
    }
</script> -->