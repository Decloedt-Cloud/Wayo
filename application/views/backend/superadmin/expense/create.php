<style>
/* ============================================================================
   EXPENSE CREATE - MODERN FORM DESIGN
   ============================================================================ */
:root {
    --edit-primary: #6366f1;
    --edit-primary-light: #eef2ff;
    --edit-success: #059669;
    --edit-success-light: #d1fae5;
    --edit-danger: #dc2626;
    --edit-danger-light: #fee2e2;
    --edit-warning: #d97706;
    --edit-dark: #1e293b;
    --edit-gray: #64748b;
    --edit-light: #f8fafc;
    --edit-border: #e2e8f0;
}

.edit-form-container {
    padding: 0;
}

/* Header */
.edit-header {
    background: linear-gradient(135deg, var(--edit-primary) 0%, #4f46e5 100%);
    padding: 1.25rem 1.5rem;
    margin: -1rem -1rem 1.5rem -1rem;
    border-radius: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.edit-header-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.edit-header-text h5 {
    margin: 0;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
}

.edit-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.8);
    font-size: 0.8rem;
}

/* Form Sections */
.edit-section {
    margin-bottom: 1.5rem;
}

.edit-section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--edit-dark);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--edit-border);
}

.edit-section-title i {
    color: var(--edit-primary);
    font-size: 1rem;
}

/* Form Grid */
.edit-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (max-width: 576px) {
    .edit-form-grid {
        grid-template-columns: 1fr;
    }
}

.edit-form-grid.full {
    grid-template-columns: 1fr;
}

/* Form Group */
.edit-form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    margin-bottom: 1rem;
}

.edit-form-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--edit-dark);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.edit-form-label i {
    color: var(--edit-primary);
}

.edit-form-label .required {
    color: var(--edit-danger);
    margin-left: 0.2rem;
}

.edit-form-control {
    padding: 0.6rem 0.85rem;
    border: 1px solid var(--edit-border);
    border-radius: 8px;
    font-size: 0.9rem;
    color: var(--edit-dark);
    transition: all 0.2s;
    background: white;
}

.edit-form-control:focus {
    border-color: var(--edit-primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.edit-form-control::placeholder {
    color: #cbd5e1;
}

/* Buttons */
.edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--edit-border);
}

.edit-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.edit-btn-primary {
    background: linear-gradient(135deg, var(--edit-primary), #4f46e5);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.edit-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
}

.edit-btn-secondary {
    background: white;
    color: var(--edit-gray);
    border: 1px solid var(--edit-border);
}

.edit-btn-secondary:hover {
    background: var(--edit-light);
    color: var(--edit-dark);
}
</style>

<div class="edit-form-container">
    <!-- Header -->
    <div class="edit-header">
        <div class="edit-header-icon">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div class="edit-header-text">
            <h5><?php echo get_phrase('add_expense'); ?></h5>
            <p><?php echo get_phrase('create_new_expense_record'); ?></p>
        </div>
    </div>

    <form method="POST" class="d-block ajaxForm" action="<?php echo route('expense/create'); ?>">
        <!-- Champ caché pour le jeton CSRF -->
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        
        <div class="edit-section">
            <div class="edit-section-title">
                <i class="fa-solid fa-circle-info"></i> <?php echo get_phrase('expense_details'); ?>
            </div>

            <div class="edit-form-grid">
                <div class="edit-form-group">
                    <label class="edit-form-label" for="date">
                        <i class="fa-solid fa-calendar-days"></i> <?php echo get_phrase('date'); ?>
                        <span class="required"> * </span>
                    </label>
                    <input type="text" class="edit-form-control date" id="date" data-bs-toggle="date-picker" data-single-date-picker="true" name="date" value="" required>
                </div>

                <div class="edit-form-group">
                    <label class="edit-form-label" for="amount">
                        <i class="fa-solid fa-money-bill"></i> <?php echo get_phrase('amount'); ?> (<?php echo currency_code_and_symbol('code'); ?>)
                        <span class="required"> * </span>
                    </label>
                    <input type="text" class="edit-form-control" id="amount" name="amount" required>
                </div>
            </div>

            <div class="edit-form-grid full">
                <div class="edit-form-group">
                    <label class="edit-form-label" for="expense_category_id_on_create">
                        <i class="fa-solid fa-tag"></i> <?php echo get_phrase('expense_category'); ?>
                        <span class="required"> * </span>
                    </label>
                    <select class="edit-form-control select2" name="expense_category_id" id="expense_category_id_on_create" required>
                        <option value=""><?php echo get_phrase('select_an_expense_category'); ?></option>
                        <?php
                        $expense_categories = $this->crud_model->get_expense_categories()->result_array();
                        foreach ($expense_categories as $expense_category): ?>
                            <option value="<?php echo $expense_category['id']; ?>"><?php echo $expense_category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="edit-actions">
            <button type="button" class="edit-btn edit-btn-secondary" data-bs-dismiss="modal"><?php echo get_phrase('cancel'); ?></button>
            <button class="edit-btn edit-btn-primary" id="update-btn" type="submit">
                <i class="fa-solid fa-plus"></i> <?php echo get_phrase('create_expense'); ?>
            </button>
        </div>
    </form>
</div>

<script>
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
        submitButton.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> '+adding_text);
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
</script>



