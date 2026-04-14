<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/single-student-admission.min.css">

<?php $classes = db()->table('classes')->where('id', $param1)->get()->getResultArray(); ?>
<?php foreach($classes as $class){ ?>
<form method="POST" class="d-block ajaxForm" action="<?php echo route('manage_class/update/'.$param1); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
    
<div class="form-row">

    <div class="image-upload-section">
        <h5><?php echo get_phrase('class_image'); ?></h5>
        <div class="image-preview" id="student-image-preview">
            <?php if ($class['photo'] == '' || $class['photo'] == null): ?>
            <img src="<?php echo base_url() . 'uploads/class/placeholder.png'; ?>" alt="Class Image" class="preview-image">
            <?php else: ?>
            <img src="<?php echo base_url() . 'uploads/class/'.$class['photo']; ?>" alt="Class Image" class="preview-image">
            <?php endif; ?>
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
            <input type="text" class="form-control" value="<?php echo $class['name']; ?>" id="name" name = "name" required>
            <small id="name_help" class="form-text text-muted"><?php echo get_phrase('provide_class_name'); ?></small>
        </div>
        <div class="form-group mb-1 col-md-12">
            <label for="price"><?php echo get_phrase('class_price'); ?><span class="required"> * </span></label>
            <?php 
            $currencies = db()->table('settings_school')->where('school_id', school_id())->get()->getRowArray()['system_currency'] ?? '';
            $type = db()->table('settings_school')->where('school_id', school_id())->get()->getRowArray()['type'] ?? '';
            ?>

            <div class="form-inline">
                <input 
                    type="text" 
                    class="form-control col-md-10" 
                    value="<?php echo $class['price']; ?>" 
                    id="price" 
                    name="price" 
                    required 
                    oninput="checkPriceForParticulier(this)"
                >
                <input 
                    type="text" 
                    class="form-control currency-input col-md-2" 
                    value="<?php echo $currencies; ?>" 
                    disabled
                >
                <input 
                    type="hidden"  
                    id="currency" 
                    name="currency" 
                    value="<?php echo $currencies; ?>" 
                >
            </div>

            <!-- Message d'avertissement -->
            <small id="price-warning" class="text-danger" <?php if ($type != 'Particulier'): ?>   style="display:none;" <?php endif; ?>>
                <?php echo get_phrase('as_you_are_a_private_individual_the_price_will_be_automatically_set_to_0'); ?>
            </small>
        </div>




           
            <small id="price_help" class="form-text text-muted"><?php echo get_phrase('provide_class_price'); ?></small>
        </div>

          <!-- Date de début -->
      <div class="form-group mb-1 col-md-12">
          <label for="start_date"><?php echo get_phrase('start_date'); ?></label>
          <input type="date" class="form-control" id="start_date" name="start_date"   min="<?php echo date('Y-m-d'); ?>"  value="<?php echo (!empty($class['date_debut'])) ? date('Y-m-d', strtotime($class['date_debut'])) : ''; ?>">
      </div>

      <!-- Date de fin -->
      <div class="form-group mb-1 col-md-12">
          <label for="end_date"><?php echo get_phrase('end_date'); ?></label>
          <input type="date" class="form-control" id="end_date" name="end_date"   min="<?php echo date('Y-m-d'); ?>"  value="<?php echo (!empty($class['date_fin'])) ? date('Y-m-d', strtotime($class['date_fin'])) : ''; ?>">
      </div>

      <!-- Classe active/inactive -->
      <div class="form-group mb-1 col-md-12">
          <label for="status"><?php echo get_phrase('class_status'); ?></label>
          <select class="form-control" id="status_class" name="status">
              <option  value=""><?php echo get_phrase('class_status'); ?></option>
              <option <?php if ($class['statut'] == 'active'): ?> selected <?php endif; ?>  value="active"><?php echo get_phrase('active'); ?></option>
              <option   <?php if ($class['statut'] == 'inactive'): ?> selected <?php endif; ?>  value="inactive"><?php echo get_phrase('inactive'); ?></option>
          </select>
      </div>

      <!-- Nombre max de membres -->
      <div class="form-group mb-1 col-md-12">
          <label for="max_members"><?php echo get_phrase('maximum_number_of_members'); ?></label>
          <input type="number" class="form-control" id="max_members" name="max_members" min="1" value="<?php echo $class['nombre_max_membre']; ?>">
      </div>


        <div class="form-group  col-md-12">
            <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-account-check"></i><?php echo get_phrase('update_class'); ?></button>
        </div>
    </div>
</form>
<?php } ?>

<script>
$(document).ready(function() {

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
  $(".ajaxForm").validate({}); // Jquery form validation initialization
//   $(".ajaxForm").submit(function(e) {
//       var form = $(this);
    //   ajaxSubmit(e, form, showAllClasses);
      function getCsrfToken() {
         // Récupérer le nom du token CSRF depuis le champ input caché
          var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
         // Récupérer la valeur (hash) du token CSRF depuis le champ input caché
           var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
         // Retourner un objet contenant le nom du token et sa valeur
         return { csrfName: csrfName, csrfHash: csrfHash };
       }


 // Soumission du formulaire de logo
 $(".ajaxForm").submit(function(e) {
    e.preventDefault();

           // Cible uniquement le bouton de ce formulaire
        var submitButton = $(this).find('button[type="submit"]');
        var updating_text = "<?php echo get_phrase('updating'); ?>...";
        
        // Désactive et met à jour uniquement ce bouton
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>'+updating_text);
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
                // Met à jour le token CSRF
                $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                // Rafraîchissement de la page après un léger délai pour s'assurer que les modifications sont appliquées
                setTimeout(function() {
                  location.reload();
                  
                }, 500);// Attendre 3500ms avant de recharger la page
            } else {
              error_notify('<?= js_phrase(get_phrase('action_not_allowed')); ?>')
                
            }
        },
        error: function () {
          error_notify(<?= js_phrase(get_phrase('an_error_occurred_during_submission')); ?>)
        }
      });
    });
//   });
});

function checkPriceForParticulier(input) {
    const userType = "<?php echo $type; ?>";
    const price = parseFloat(input.value) || 0;
    const warning = document.getElementById('price-warning');

    // Autorise uniquement les chiffres et le point
    input.value = input.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

    if (userType === "Particulier" && price > 0) {
        warning.style.display = 'block';
    } else {
        warning.style.display = 'none';
    }
}
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



</style>