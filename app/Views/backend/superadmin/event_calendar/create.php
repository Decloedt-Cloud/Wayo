<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('event_calendar/create'); ?>">
  <!-- Champ caché pour le jeton CSRF -->
  <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
  

    <input type="hidden" name="school_id" id="school_id" value="<?php echo session()->get('school_id'); ?>">
  <div class="form-row">
    <div class="form-group mb-1">
      <label for="title"><?php echo get_phrase('event_title'); ?><span class="required"> * </span></label>
      <input type="text" class="form-control" id="title" name = "title" >
      <small id="name_help" class="form-text"><?php echo get_phrase('provide_title_name'); ?></small>
    </div>
    <div class="form-group mb-1">
      <label for="starting_date"><?php echo get_phrase('event_starting_date'); ?><span class="required"> * </span></label>
      <input type="date" value="<?php echo date('m/d/Y'); ?>" class="form-control" id="starting_date" name = "starting_date" >
      <small id="name_help" class="form-text"><?php echo get_phrase('provide_starting_date'); ?></small>
    </div>

    <div class="form-group mb-1">
      <label for="starting_date"><?php echo get_phrase('event_ending_date'); ?><span class="required"> * </span></label>
      <input type="date" value="<?php echo date('m/d/Y'); ?>" class="form-control" id="ending_date" name = "ending_date" >
      <small id="name_help" class="form-text"><?php echo get_phrase('provide_ending_date'); ?></small>
    </div>

    <div class="form-group  col-md-12">
      <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-content-save"></i><?php echo get_phrase('save_event'); ?></button>
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
       CSRF TOKEN
    ============================ */
    function getCsrfToken() {
        var csrfName = $('input[name="<?=csrf_token();?>"]').attr('name');
        var csrfHash = $('input[name="<?=csrf_token();?>"]').val();
        return { csrfName, csrfHash };
    }

    /* ============================
       ELEMENTS & HELP TEXT
    ============================ */
    const titleInput = $('#title');
    const startDateInput = $('#starting_date');
    const endDateInput = $('#ending_date');

    const titleHelp = $('#name_help'); // tu peux créer des help séparés si tu veux
    const startDateHelp = $('#starting_date').next('small');
    const endDateHelp = $('#ending_date').next('small');

    const titleRegex = /^[a-zA-Z0-9 ]{3,100}$/; // Minimum 3 caractères, lettres, chiffres, espace

    function showError(el, msg) {
        el.addClass('text-danger').text(msg).show();
    }

    function hideError(el) {
        el.hide();
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */
    titleInput.on('input', function() {
        const value = $(this).val().trim();
        if (!titleRegex.test(value)) {
            showError(titleHelp, '<?php echo get_phrase('Invalid_title_(minimum_3_characters,_letters_and_numbers_allowed)'); ?>');
            titleInput.addClass('is-invalid');
        } else {
            hideError(titleHelp);
            titleInput.removeClass('is-invalid');
        }
    });

    startDateInput.on('change', function() {
        if (!$(this).val()) {
            showError(startDateHelp, '<?php echo get_phrase('Please_provide_a_start_date'); ?>');
            startDateInput.addClass('is-invalid');
        } else {
            hideError(startDateHelp);
            startDateInput.removeClass('is-invalid');
        }
    });

    endDateInput.on('change', function() {
        if (!$(this).val()) {
            showError(endDateHelp, '<?php echo get_phrase('Please_provide_an_end_date'); ?>');
            endDateInput.addClass('is-invalid');
        } else if (endDateInput.val() < startDateInput.val()) {
            showError(endDateHelp, '<?php echo get_phrase('The_end_date_must_be_after_the_start_date'); ?>');
            endDateInput.addClass('is-invalid');
        } else {
            hideError(endDateHelp);
            endDateInput.removeClass('is-invalid');
        }
    });

    /* ============================
       FORM SUBMISSION AJAX
    ============================ */
    let isSubmitting = false;

    $('.ajaxForm').on('submit', function(e) {
        e.preventDefault();
        if (isSubmitting) return;

        let isValid = true;

        const titleVal = titleInput.val().trim();
        const startVal = startDateInput.val();
        const endVal = endDateInput.val();

        // Validation avant soumission
        if (!titleRegex.test(titleVal)) {
            showError(titleHelp, '<?php echo get_phrase('Invalid_title_(minimum_3_characters,_letters_and_numbers_allowed)'); ?>');
            titleInput.addClass('is-invalid');
            isValid = false;
        }

        if (!startVal) {
            showError(startDateHelp, '<?php echo get_phrase('Please provide a start date'); ?>');
            startDateInput.addClass('is-invalid');
            isValid = false;
        }

        if (!endVal) {
            showError(endDateHelp, '<?php echo get_phrase('Please_provide_an_end_date'); ?>');
            endDateInput.addClass('is-invalid');
            isValid = false;
        } else if (endVal < startVal) {
            showError(endDateHelp, '<?php echo get_phrase('The_end_date_mus_be_after_the_start_date'); ?>');
            endDateInput.addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) return;

        // Soumission AJAX
        isSubmitting = true;
        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase('Registration'); ?> ...');

        const csrf = getCsrfToken();

        const formData = {
            title: titleVal,
            starting_date: startVal,
            ending_date: endVal,
            school_id: $('#school_id').val()
        };
        formData[csrf.csrfName] = csrf.csrfHash;

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).html('<i class="mdi mdi-content-save"></i> <?php echo get_phrase('Save'); ?>');

                if (response.status) {
                    toastr.success(response.message || '<?php echo get_phrase('Event_created_successfully.'); ?>');
                    $('.ajaxForm')[0].reset();
                } else {
                    toastr.error(response.message || '<?php echo get_phrase('Failed_to_create_the_event.'); ?>');
                }
            },
            error: function(xhr, status, error) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).html('<i class="mdi mdi-content-save"></i> <?php echo get_phrase('Save'); ?>');
                toastr.error('Une erreur est survenue : ' + xhr.status + ' ' + error);
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
        var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
       // Récupérer la valeur (hash) du token CSRF depuis le champ input caché
         var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
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

