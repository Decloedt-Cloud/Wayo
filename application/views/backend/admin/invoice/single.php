<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('invoice/single'); ?>">
  <!-- Champ caché pour le jeton CSRF -->
  <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
  
  <div class="form-row">
    <div class="form-group mb-2">
      <label for="class_id_on_create"><?php echo get_phrase('class'); ?><span class="required"> * </span></label>
      <select name="class_id" id="class_id_on_create" class="form-control"    onchange="classWiseStudentOnCreate(this.value)">
        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
        <?php $classes = $this->crud_model->get_classes()->result_array(); ?>
        <?php foreach($classes as $class): ?>
          <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group mb-2">
      <label for="student_id_on_create"><?php echo get_phrase('select_student'); ?><span class="required"> * </span></label>
      <div id = "student_content">
        <select name="student_id" id="student_id_on_create" class="form-control">
          <option value=""><?php echo get_phrase('select_a_student'); ?></option>
        </select>
      </div>
    </div>

    <div class="form-group mb-2">
      <label for="title"><?php echo get_phrase('invoice_title'); ?><span class="required"> * </span></label>
      <input type="text" class="form-control" id="title" name = "title" >
    </div>

    <div class="form-group mb-2">
      <label for="total_amount"><?php echo get_phrase('total_amount').' ('.currency_code_and_symbol('code').')'; ?><span class="required"> * </span></label>
      <input type="number" class="form-control" id="total_amount" name = "total_amount" >
    </div>

    <div class="form-group mb-2">
      <label for="paid_amount"><?php echo get_phrase('paid_amount').' ('.currency_code_and_symbol('code').')'; ?><span class="required"> * </span></label>
      <input type="number" class="form-control" id="paid_amount" name = "paid_amount" >
    </div>

    <div class="form-group mb-2">
      <label for="status"><?php echo get_phrase('status'); ?><span class="required"> * </span></label>
      <select name="status" id="status-pre" class="form-control"   >
        <option value=""><?php echo get_phrase('select_a_status'); ?></option>
        <option value="paid"><?php echo get_phrase('paid'); ?></option>
        <option value="unpaid"><?php echo get_phrase('unpaid'); ?></option>
      </select>
      <small id="status_help" class="text-danger" style="display:none;"></small>
    </div>
  </div>
  <div class="form-group mb-2">
    <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_invoice'); ?></button>
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
        let csrfField = $('input[type="hidden"][name*="csrf"]');
        return {
            csrfName: csrfField.attr('name'),
            csrfHash: csrfField.val()
        };
    }

    /* ============================
       FIELDS & HELP MESSAGES
    ============================ */
    const fields = {
        class_id:     {input: $('#class_id_on_create'), help: $('<small class="text-danger" style="display:none;"></small>').insertAfter('#class_id_on_create'), msg: "<?php echo get_phrase('Please_select_a_class'); ?>"},
        student_id:   {input: $('#student_id_on_create'), help: $('<small class="text-danger" style="display:none;"></small>').insertAfter('#student_id_on_create'), msg: "<?php echo get_phrase('Please_select_a_student'); ?>"},
        title:        {input: $('#title'), help: $('<small class="text-danger" style="display:none;"></small>').insertAfter('#title'), msg: "<?php echo get_phrase('Invoice_title_is_required'); ?>"},
        total_amount: {input: $('#total_amount'), help: $('<small class="text-danger" style="display:none;"></small>').insertAfter('#total_amount'), msg: "<?php echo get_phrase('Total_amount_must_be_a_number_≥_0'); ?>"},
        paid_amount:  {input: $('#paid_amount'), help: $('<small class="text-danger" style="display:none;"></small>').insertAfter('#paid_amount'), msg: "<?php echo get_phrase('Paid_amount_must_be_a_number_≥_0'); ?>"},
        status: {input: $('#status-pre'), help: $('#status_help'), msg: "<?php echo get_phrase('Please_select_a_status'); ?>"}
    };

    /* ============================
       SHOW / HIDE ERRORS
    ============================ */
    function showError(field, msg) {
        field.help.text(msg).show();
        field.input.addClass('is-invalid');
    }
    function hideError(field) {
        field.help.hide();
        field.input.removeClass('is-invalid');
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */
    fields.total_amount.input.on('input', function() {
        let v = parseFloat($(this).val());
        if (isNaN(v) || v < 0) showError(fields.total_amount, fields.total_amount.msg);
        else hideError(fields.total_amount);
    });

    fields.paid_amount.input.on('input', function() {
        let v = parseFloat($(this).val());
        if (isNaN(v) || v < 0) showError(fields.paid_amount, fields.paid_amount.msg);
        else hideError(fields.paid_amount);
    });

    fields.class_id.input.on('change', function() {
        if ($(this).val() === "") showError(fields.class_id, fields.class_id.msg);
        else hideError(fields.class_id);
        // Charger les étudiants pour la classe sélectionnée
        classWiseStudentOnCreate($(this).val());
    });

    fields.student_id.input.on('change', function() {
        if ($(this).val() === "") showError(fields.student_id, fields.student_id.msg);
        else hideError(fields.student_id);
    });

    fields.status.input.on('change', function() {
        if ($(this).val() === "") showError(fields.status, fields.status.msg);
        else hideError(fields.status);
    });

    fields.title.input.on('input', function() {
        if ($(this).val().trim() === "") showError(fields.title, fields.title.msg);
        else hideError(fields.title);
    });

    /* ============================
       AJAX FORM SUBMISSION
    ============================ */
    $(".ajaxForm").off("submit").on("submit", function (e) {
        e.preventDefault();

        let form = $(this);
        let submitButton = form.find('button[type="submit"]');

        // Reset all errors
        for (let key in fields) hideError(fields[key]);

        // Front-end validation
        let isValid = true;
        for (let key in fields) {
            let field = fields[key];
            let val = field.input.val().trim();
            if ((key === 'total_amount' || key === 'paid_amount') && (isNaN(parseFloat(val)) || val === "")) {
                showError(field, field.msg); isValid = false;
            } else if ((key === 'class_id' || key === 'student_id' || key === 'status' || key === 'title') && val === "") {
                showError(field, field.msg); isValid = false;
            }
        }

        if (!isValid) return;

        // Submit AJAX
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase('Creating'); ?>...');
        let formData = new FormData(this);
        let csrf = getCsrfToken();
        formData.append(csrf.csrfName, csrf.csrfHash);

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",

            success: function(response) {
                submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i> <?php echo get_phrase('Create_Invoice'); ?>');

                if (response.status) {
                    success_notify(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    error_notify(response.notification);
                }
            },
            error: function() {
                submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i> <?php echo get_phrase('Create_Invoice'); ?>');
                error_notify("<?php echo get_phrase('An_error_occurred_during_submission'); ?>");
            }
        });
    });

    /* ============================
       LOAD STUDENTS BY CLASS
    ============================ */
    function classWiseStudentOnCreate(classId) {
        fields.student_id.input.html('<option value=""><?php echo get_phrase('Loading'); ?>...</option>'); // message temporaire
        $.ajax({
            url: "<?php echo route('invoice/student/'); ?>" + classId,
            type: 'GET',
            success: function(response){
                fields.student_id.input.html(response);
                fields.student_id.input.trigger('change'); // pour réactiver validation
            },
            error: function() {
                fields.student_id.input.html('<option value=""><?php echo get_phrase('Error_loading_students'); ?></option>');
            }
        });
    }
});
</script>



<!-- <script>
$(document).ready(function () {
  $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); }); //initSelect2(['#class_id_on_create', '#student_id_on_create', '#status']);
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
              error_notify('<?= js_phrase(get_phrase('action_not_allowed')) ?>'.replace(/^"|"$/g, ''))
                
            }
        },
        error: function () {
          error_notify('<?= js_phrase(get_phrase('an_error_occurred_during_submission')) ?>'.replace(/^"|"$/g, ''))
        }
      });
    });
});

function classWiseStudentOnCreate(classId) {
  $.ajax({
    url: "<?php echo route('invoice/student/'); ?>"+classId,
    success: function(response){
      $('#student_id_on_create').html(response);
    }
  });
}
</script> -->
