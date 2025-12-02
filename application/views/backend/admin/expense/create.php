<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('expense/create'); ?>">
  <!-- Champ caché pour le jeton CSRF -->
  <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
  
  <div class="form-row">
    <div class="form-group mb-1">
      <label for="date"><?php echo get_phrase('date'); ?><span class="required"> * </span></label>
      <input type="text" class="form-control date" id="date" data-bs-toggle="date-picker" data-single-date-picker="true" name = "date" value="" >
       <small id="date_help" class="text-danger d-none"></small>
    </div>

    <div class="form-group mb-1">
      <label for="amount"><?php echo get_phrase('amount').' ('.currency_code_and_symbol('code').')'; ?><span class="required"> * </span></label>
      <input type="text" class="form-control" id="amount" name = "amount" >
      <small id="amount_help" class="text-danger d-none"></small>
    </div>

    <div class="form-group mb-1">
      <label for="expense_category_id"><?php echo get_phrase('expense_category'); ?><span class="required"> * </span></label>
      <select class="form-control"  name="expense_category_id" id = "expense_category_id_on_create" >
        <option value=""><?php echo get_phrase('select_an_expense_category'); ?></option>
        <?php
        $expense_categories = $this->crud_model->get_expense_categories()->result_array();
        foreach ($expense_categories as $expense_category): ?>
        <option value="<?php echo $expense_category['id']; ?>"><?php echo $expense_category['name']; ?></option>
      <?php endforeach; ?>
    </select>
     <small id="category_help" class="text-danger d-none" ></small>
  </div>

  <div class="form-group  col-md-12">
    <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_expense'); ?></button>
  </div>
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
    const dateInput     = $('#date');
    const dateHelp      = $('#date_help');

    const amountInput   = $('#amount');
    const amountHelp    = $('#amount_help');

    const categoryInput = $('#expense_category_id_on_create');
    const categoryHelp  = $('#category_help');

    const amountRegex = /^[0-9]+(\.[0-9]{1,2})?$/;

   function showError(el, msg) {
    el.text(msg)
      .removeClass('d-none')
      .removeClass('invisible')
      .show();   // force display:block
}

  function hideError(el) {
      el.text('')
        .addClass('d-none')
        .hide();  
  }

    /* ============================
       VALIDATION TEMPS RÉEL
    ============================ */

    dateInput.on('change', function () {
        if ($(this).val().trim() === '') {
            showError(dateHelp, "<?= addslashes(get_phrase('please_provide_date')); ?>");
            dateInput.addClass('is-invalid');
        } else {
            hideError(dateHelp);
            dateInput.removeClass('is-invalid');
        }
    });

    amountInput.on('input', function () {
        const val = $(this).val().trim();
        if (!amountRegex.test(val)) {
            showError(amountHelp, "<?= addslashes(get_phrase('invalid_amount')); ?>");
            amountInput.addClass('is-invalid');
        } else {
            hideError(amountHelp);
            amountInput.removeClass('is-invalid');
        }
    });

    categoryInput.on('change', function () {
        if ($(this).val() === '') {
            showError(categoryHelp, "<?= addslashes(get_phrase('please_select_expense_category')); ?>");
            categoryInput.addClass('is-invalid');
        } else {
            hideError(categoryHelp);
            categoryInput.removeClass('is-invalid');
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

        if (dateInput.val().trim() === '') {
            showError(dateHelp, "<?= addslashes(get_phrase('please_provide_date')); ?>");
            dateInput.addClass('is-invalid');
            isValid = false;
        }

        const amountVal = amountInput.val().trim();
        if (!amountRegex.test(amountVal)) {
            showError(amountHelp, "<?= addslashes(get_phrase('invalid_amount')); ?>");
            amountInput.addClass('is-invalid');
            isValid = false;
        }

        if (categoryInput.val() === '') {
            showError(categoryHelp, "<?= addslashes(get_phrase('please_select_expense_category')); ?>");
            categoryInput.addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) return;

        isSubmitting = true;

        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true)
            .html('<i class="mdi mdi-loading mdi-spin"></i> <?= addslashes(get_phrase('creating')); ?>...');

        var csrf = getCsrfToken();
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
                    .html('<i class="mdi mdi-plus"></i> <?= addslashes(get_phrase('create_expense')); ?>');

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
                    .html('<i class="mdi mdi-plus"></i> <?= addslashes(get_phrase('create_expense')); ?>');
                error_notify("<?= addslashes(get_phrase('an_error_occurred_during_submission')); ?>");
            }
        });
    });

});
</script>




<!-- <script>
$(document).ready(function() {
  $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); }); //initSelect2(['#expense_category_id_on_create']);
  $('#date').daterangepicker();
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


