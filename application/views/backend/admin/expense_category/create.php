<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('expense_category/create'); ?>">
  <!-- Champ caché pour le jeton CSRF -->
  <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
  
  <div class="form-group mb-2">
    <label for="name"><?php echo get_phrase('expense_category_name'); ?><span class="required"> * </span></label>
    <input type="text" class="form-control" id="name" name = "name">
    <small id="name_help" class="text-danger d-none"></small>
  </div>

  <div class="form-group">
    <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-content-save"></i><?php echo get_phrase('save_expense_category'); ?></button>
  </div>
</form>

<script>
$(document).ready(function () {

    /* ============================
       CSRF TOKEN
    ============================ */
    function getCsrfToken() {
        var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
        return { csrfName: csrfName, csrfHash: csrfHash };
    }

    /* ============================
       FIELD ELEMENTS
    ============================ */
    const nameInput = $('#name');
    const nameHelp  = $('#name_help');

    const nameRegex = /^[a-zA-Z0-9 ]{3,}$/; // minimum 3 caractères, lettres et chiffres

    function showError(el, msg) {
        el.text(msg).removeClass('d-none').show();
    }

    function hideError(el) {
        el.text('').addClass('d-none').hide();
    }

    /* ============================
       VALIDATION EN TEMPS RÉEL
    ============================ */
    nameInput.on('input', function () {
        const val = $(this).val().trim();
        if (!nameRegex.test(val)) {
            showError(nameHelp, "<?= addslashes(get_phrase('invalid_name_(minimum_3_characters)')); ?>");
            nameInput.addClass('is-invalid');
        } else {
            hideError(nameHelp);
            nameInput.removeClass('is-invalid');
        }
    });

    /* ============================
       AJAX SUBMIT
    ============================ */
    let isSubmitting = false;

    $(".ajaxForm").on("submit", function(e) {
        e.preventDefault();
        if (isSubmitting) return;

        let isValid = true;
        const nameVal = nameInput.val().trim();

        if (!nameRegex.test(nameVal)) {
            showError(nameHelp, "<?= addslashes(get_phrase('invalid_name_(minimum_3_characters)')); ?>");
            nameInput.addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) return;

        isSubmitting = true;

        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true)
            .html('<i class="mdi mdi-loading mdi-spin"></i> <?= addslashes(get_phrase('saving')); ?>...');

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',

            success: function(response) {
                isSubmitting = false;
                submitButton.prop('disabled', false)
                    .html('<i class="mdi mdi-content-save"></i> <?= addslashes(get_phrase('save_expense_category')); ?>');

                if (response.status) {
                    success_notify(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 3000);
                } else {
                    error_notify("<?= addslashes(get_phrase('action_not_allowed')); ?>");
                }
            },

            error: function() {
                isSubmitting = false;
                submitButton.prop('disabled', false)
                    .html('<i class="mdi mdi-content-save"></i> <?= addslashes(get_phrase('save_expense_category')); ?>');
                error_notify("<?= addslashes(get_phrase('an_error_occurred_during_submission')); ?>");
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
        var adding_text = "<?php echo get_phrase('saving'); ?>...";
        
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
