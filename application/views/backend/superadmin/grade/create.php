<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">
<form method="POST" class="d-block ajaxForm" action="<?php echo route('grade/create'); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <div class="form-row">
        <div class="form-group mb-2">
            <label for="grade"><?php echo get_phrase('grade'); ?></label>
            <input type="text" class="form-control" id="grade" name = "grade" placeholder="<?php echo get_phrase('grade'); ?>" >
        </div>

        <div class="form-group mb-2">
            <label for="grade_point"><?php echo get_phrase('grade_point'); ?></label>
            <input type="number" class="form-control" id="grade_point" name = "grade_point" placeholder="<?php echo get_phrase('grade_point'); ?>" >
        </div>

        <div class="form-group mb-2">
            <label for="mark_from"><?php echo get_phrase('mark_from'); ?></label>
            <input type="number" class="form-control" id="mark_from" name = "mark_from" placeholder="<?php echo get_phrase('mark_from'); ?>" >
        </div>

        <div class="form-group mb-2">
            <label for="mark_upto"><?php echo get_phrase('mark_upto'); ?></label>
            <input type="number" class="form-control" id="mark_upto" name = "mark_upto" placeholder="<?php echo get_phrase('mark_upto'); ?>" >
        </div>

        <div class="form-group mb-2">
            <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_grade'); ?></button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {

    /* ============================
       TOASTR CONFIG
    ============================ */
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };

    /* ============================
       INPUTS & VALIDATION
    ============================ */
    const gradeInput = $('#grade');
    const gradePointInput = $('#grade_point');
    const markFromInput = $('#mark_from');
    const markUptoInput = $('#mark_upto');

    // Helper functions
    function showError(input, message) {
        input.addClass('is-invalid');
        if (input.next('.invalid-feedback').length === 0) {
            input.after('<div class="invalid-feedback">' + message + '</div>');
        } else {
            input.next('.invalid-feedback').text(message);
        }
    }

    function hideError(input) {
        input.removeClass('is-invalid');
        input.next('.invalid-feedback').remove();
    }

    // Regex rules
    const gradeRegex = /^[a-zA-Z0-9 ]+$/; // lettres, chiffres, espaces

    // Validation en temps réel
    gradeInput.on('input', function() {
        const val = $(this).val().trim();
        if (val.length < 1 || !gradeRegex.test(val)) {
            showError(gradeInput, '<?php echo addslashes(get_phrase("Invalid_grade")); ?>');
        } else {
            hideError(gradeInput);
        }
    });

    gradePointInput.on('input', function() {
        const val = parseFloat($(this).val());
        if (isNaN(val) || val < 0) {
            showError(gradePointInput, '<?php echo addslashes(get_phrase("Invalid_grade_point")); ?>');
        } else {
            hideError(gradePointInput);
        }
    });

    markFromInput.on('input', function() {
        const fromVal = parseInt(markFromInput.val());
        const uptoVal = parseInt(markUptoInput.val());
        if (isNaN(fromVal) || fromVal < 0 || (markUptoInput.val() && fromVal >= uptoVal)) {
            showError(markFromInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>');
        } else {
            hideError(markFromInput);
        }
    });

    markUptoInput.on('input', function() {
        const fromVal = parseInt(markFromInput.val());
        const uptoVal = parseInt(markUptoInput.val());
        if (isNaN(uptoVal) || uptoVal <= fromVal) {
            showError(markUptoInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>');
        } else {
            hideError(markUptoInput);
        }
    });

    /* ============================
       FORM SUBMISSION AJAX
    ============================ */
    $(".ajaxForm").on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);

        let isValid = true;

        // Validation avant submission
        const gradeVal = gradeInput.val().trim();
        const gradePointVal = parseFloat(gradePointInput.val());
        const markFromVal = parseInt(markFromInput.val());
        const markUptoVal = parseInt(markUptoInput.val());

        if (!gradeVal || !gradeRegex.test(gradeVal)) {
            showError(gradeInput, '<?php echo addslashes(get_phrase("Invalid_grade")); ?>');
            isValid = false;
        } else { hideError(gradeInput); }

        if (isNaN(gradePointVal) || gradePointVal < 0) {
            showError(gradePointInput, '<?php echo addslashes(get_phrase("Invalid_grade_point")); ?>');
            isValid = false;
        } else { hideError(gradePointInput); }

        if (isNaN(markFromVal) || markFromVal < 0 || markFromVal >= markUptoVal) {
            showError(markFromInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>');
            isValid = false;
        } else { hideError(markFromInput); }

        if (isNaN(markUptoVal) || markUptoVal <= markFromVal) {
            showError(markUptoInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>');
            isValid = false;
        } else { hideError(markUptoInput); }

        if (!isValid) return;

        // Envoi AJAX
        const $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo addslashes(get_phrase("creating")); ?>...');

        const formData = new FormData(this);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
           success: function(response) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-plus"></i> <?php echo addslashes(get_phrase("create_grade")); ?>');
                if (response.status) {
                    toastr.success(response.notification || '<?php echo addslashes(get_phrase("grade_created_successfully")); ?>');

                    // Reset du formulaire
                    $form[0].reset();

                    // Optionnel : mettre à jour table via fonction JS si existante
                    if (typeof window.updateGradeTable === 'function') {
                        window.updateGradeTable();
                    }

                    // Rafraîchissement de la page après 1,5s pour voir la nouvelle entrée
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(response.notification || '<?php echo addslashes(get_phrase("action_not_allowed")); ?>');
                }
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-plus"></i> <?php echo addslashes(get_phrase("create_grade")); ?>');
                toastr.error('<?php echo addslashes(get_phrase("an_error_occurred_during_submission")); ?>: ' + xhr.status + ' ' + error);
            }
        });
    });

});
</script>

<!-- <script>
    $(document).ready(function() {
    $(".ajaxForm").validate({}); // Jquery form validation initialization
    $(".ajaxForm").submit(function(e) {
        
        e.preventDefault(); // Bloque le comportement normal
        var form = $(this);
        //ajaxSubmit(e, form, showAllGrades);
        function getCsrfToken() {
         // Récupérer le nom du token CSRF depuis le champ input caché
          var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
         // Récupérer la valeur (hash) du token CSRF depuis le champ input caché
           var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
         // Retourner un objet contenant le nom du token et sa valeur
         return { csrfName: csrfName, csrfHash: csrfHash };
      }
           // Cible uniquement le bouton de ce formulaire
        var submitButton = $(this).find('button[type="submit"]');
        var adding_text = "<?php echo get_phrase('creating'); ?>...";
        
        // Désactive et met à jour uniquement ce bouton
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>'+adding_text);
         // Récupérer le token CSRF avant l'envoi
         var csrf = getCsrfToken(); // Appel de la fonction pour obtenir le token
         const formData = new FormData(this);// Crée une nouvelle instance de FormData en passant l'élément du formulaire courant

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response.status) { // Vérifie si la mise à jour a réussi
                success_notify(response.notification);
                // Met à jour le token CSRF
                $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                // Rafraîchissement de la page après un léger délai pour s'assurer que les modifications sont appliquées
                setTimeout(function() {
                  location.reload();
                }, 3500);// Attendre 3500ms avant de recharger la page
            } else {
              error_notify('<?= js_phrase(get_phrase('action_not_allowed')); ?>')
                
            }
        },
        error: function () {
          error_notify(<?= js_phrase(get_phrase('an_error_occurred_during_submission')); ?>)
        }
      });
    });
});
</script> -->


