<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('session_manager/create'); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <div class="form-row">
        <input type="hidden" name="school_id" value="<?php echo school_id(); ?>">
        <div class="form-group mb-1">
            <label for="name"><?php echo get_phrase('session_title'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="name" name = "session_title">
            <small id="name_help" class="text-danger d-none"></small>
        </div>

        <div class="form-group  col-md-12">
            <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_session'); ?></button>
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
        timeOut: 4000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };

    /* ============================
       CSRF TOKEN
    ============================ */
    function getCsrfToken() {
        var csrfName = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
        return { csrfName, csrfHash };
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */

    const nameInput = $('#name');
    const nameHelp = $('#name_help');

    const nameRegex = /^[a-zA-Z0-9 ]{3,}$/;

    function showError(el, msg) {
        el.removeClass('d-none').addClass('text-danger').text(msg);
    }

    function hideError(el) {
        el.addClass('d-none').text('');
    }

    // Validation en temps réel
    nameInput.on('input', function () {
        const value = $(this).val().trim();

        if (!nameRegex.test(value)) {
            showError(nameHelp, '<?php echo addslashes(get_phrase("Invalid_title_(min_3_characters)")); ?>');
            nameInput.addClass('is-invalid');
        } else {
            hideError(nameHelp);
            nameInput.removeClass('is-invalid');
        }
    });

    /* ============================
       FORM SUBMIT AJAX
    ============================ */
    $(".ajaxForm").off("submit").on("submit", function(e) {
        e.preventDefault();

        let isValid = true;
        const nameValue = nameInput.val().trim();

        // Validation finale avant submit
        if (!nameRegex.test(nameValue)) {
            showError(nameHelp, '<?php echo addslashes(get_phrase("Invalid_title_(min_3_characters)")); ?>');
            nameInput.addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) return;

        // Submit button animation
        const submitButton = $(this).find('button[type="submit"]');
        const creatingText = '<?php echo addslashes(get_phrase("creating")); ?>...';
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>' + creatingText);

        const csrf = getCsrfToken();
        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',

            success: function(response) {
                if (response.status) {
                    success_notify(response.notification);

                    // CSRF update
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                    setTimeout(() => location.reload(), 3000);
                } else {
                    error_notify('<?php echo addslashes(get_phrase("action_not_allowed")); ?>');
                }

                submitButton.prop("disabled", false).html('<i class="mdi mdi-plus"></i><?php echo get_phrase("create_session"); ?>');
            },

            error: function() {
                error_notify('<?php echo addslashes(get_phrase("an_error_occurred_during_submission")); ?>');
                submitButton.prop("disabled", false).html('<i class="mdi mdi-plus"></i><?php echo get_phrase("create_session"); ?>');
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
