<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/schoolSettings.css">


<?php $school_data = $this->settings_model->get_current_school_data(); ?>



<div class="mb-3">
    <div class="main-card">
        <div class="card-body">
            <h4 class="header-title"><?php echo get_phrase('school_settings'); ?></h4>
            <form method="POST" class="col-12 schoolForm" action="<?php echo route('school_settings/update'); ?>" id="schoolForm">
                <!-- Champ caché pour le jeton CSRF -->
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                <div class="col-12">
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="school_name"> <?php echo get_phrase('school_name'); ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="school_name" name="school_name" class="form-control" value="<?php echo $school_data['name']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="description"><?php echo get_phrase('description'); ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $school_data['description']; ?></textarea>
                            <small id="" class="form-text text-muted"><?php echo get_phrase('provide_admin_description'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="phone"><?php echo get_phrase('phone'); ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo $school_data['phone']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="access"><?php echo get_phrase('Access'); ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <select name="access" id="access" class="form-control" required>
                                <option value=""><?php echo get_phrase('select_a_access'); ?></option>
                                <option <?php if ($school_data['access'] == 1): ?> selected <?php endif; ?> value="1"><?php echo get_phrase('public'); ?></option>
                                <option <?php if ($school_data['access'] == 0): ?> selected <?php endif; ?> value="0"><?php echo get_phrase('privé'); ?></option>

                            </select>
                            <small id="" class="form-text text-muted"><?php echo get_phrase('provide_admin_access'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="address"> <?php echo get_phrase('address'); ?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" id="address" name="address" rows="5" required><?php echo $school_data['address']; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="access"><?php echo get_phrase('Category'); ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <select name="category" id="category" class="form-control" required>
                                <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                                <?php $categories = $this->db->get_where('categories', array())->result_array(); ?>
                                <?php foreach ($categories as $categorie): ?>
                                    <option <?php if ($school_data['category'] == $categorie['name']): ?> selected <?php endif; ?> value="<?php echo $categorie['name']; ?>"><?php echo $categorie['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small id="" class="form-text text-muted"><?php echo get_phrase('provide_admin_category'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="example-fileinput">
                            <?php echo get_phrase('Community_profile_logo'); ?>
                        </label>
                        <div class="col-md-5 logo-upload-container">
                            <div class="logo-card">
                                <div class="logo-header">
                                    <h5><?php echo get_phrase('Community_profile_logo'); ?></h5>
                                </div>
                                <div class="logo-preview" id="school-image-preview">
                                <img 
                                src="<?php echo $this->user_model->get_school_image($school_data['id']) . '?v=' . time(); ?>" 
                                alt="Community profile logo" 
                                class="preview-image"
                                >
                            </div>
                                    <div class="logo-upload-btn mt-2">
                                <label 
                                for="school_image" 
                                class="btn btn-outline-primary"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="<?php echo get_phrase('Upload_a square_image_(512×512_recommended),_PNG_or_JPEG,_max_size_2_MB'); ?>"
                                >
                                <i class="mdi mdi-cloud-upload" style="pointer-events: none;"></i> 
                                <?php echo get_phrase('upload_an_image'); ?>
                                </label>

                                <input 
                                id="school_image" 
                                type="file" 
                                class="image-upload d-none" 
                                name="school_image" 
                                accept="image/*" 
                                data-preview="school-image-preview"
                                >
                                <!-- 🔹 Zone d’erreur -->
                                <div id="image-error" class="text-danger mt-2 small fw-bold"></div>
                            </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                    <label class="col-md-3 col-form-label" for="school_cover">
                        <?php echo get_phrase('Community_cover_image'); ?>
                    </label>

                    <div class="col-md-5 logo-upload-container">
                        <div class="logo-card">
                        <div class="logo-header">
                            <h5><?php echo get_phrase('Community_cover_image'); ?></h5>
                        </div>

                        <div class="logo-preview" id="school-cover-preview">
                            <img 
                            src="<?php echo $this->user_model->get_school_cover($school_data['id']) . '?v=' . time(); ?>" 
                            alt="Community_cover_image" 
                            class="preview-image"
                            >
                            <div class="logo-overlay">
                            <i class="fas fa-camera"></i>
                            </div>
                        </div>

                        <div class="logo-upload-btn mt-2">
                            <label 
                            for="school_cover" 
                            class="btn btn-outline-primary tooltip-label"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="<?php echo get_phrase('Upload_an_image_(1920×600_recommended),_PNG_or_JPEG,_max_size_2_MB'); ?>"
                            >
                            <i class="mdi mdi-cloud-upload" style="pointer-events: none;"></i> 
                            <?php echo get_phrase('upload_an_image'); ?>
                            </label>

                            <input 
                            id="school_cover" 
                            type="file" 
                            class="image-upload d-none" 
                            name="school_cover" 
                            accept="image/png, image/jpeg" 
                            data-preview="school-cover-preview"
                            >

                            <!-- 🔹 Zone d’erreur -->
                            <div id="cover-error" class="text-danger mt-2 small fw-bold"></div>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-l px-4" id="update-logos-btn" id="update-logos-btn" onclick="updateSchoolInfo()">
                            <i class="mdi mdi-account-check"></i>
                            <?php echo get_phrase('update_settings'); ?>
                        </button>
                    </div>

            </form>

        </div> <!-- end card body-->
    </div> <!-- end card -->
</div>



<script type="text/javascript">
    $(document).ready(function() {
        // Initialisation Select2
        $('select.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: '#right-modal'
            });
        });

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
            var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
            var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
            return {
                csrfName: csrfName,
                csrfHash: csrfHash
            };
        }

        // Soumission AJAX du formulaire
        $('#schoolForm').submit(function(e) {
            e.preventDefault();

            // Obtenez le texte de mise à jour traduit
            var updating_text = "<?php echo get_phrase('updating'); ?>...";
            // Afficher un indicateur de chargement
            $('button[type="submit"]').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>' + updating_text);
            // Récupérer le token CSRF avant l'envoi
            var csrf = getCsrfToken(); // Appel de la fonction pour obtenir le token
            const formData = new FormData(this); // Crée une nouvelle instance de FormData en passant l'élément du formulaire courant

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',

                success: function(response) {
                    if (response.status) {
                        //success_notify(response.notification);
                        // Mise à jour du token CSRF
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                        // Mise à jour dynamique des images avec cache-buster (pour forcer le rechargement des images)
                        $('.preview-image').each(function() {
                            var originalSrc = $(this).attr('src').split('?')[0]; // Récupère l'URL d'origine de l'image sans la chaîne de requête
                            // $(this).attr('src', originalSrc + '?v=' + Date.now()); // Ajoute un timestamp pour éviter la mise en cache cette ligne affiche alt du l'image avant affichage du l'image apres update
                        });

                        // Rafraîchissement de la page après un léger délai pour s'assurer que les modifications sont appliquées
                        setTimeout(function() {
                            location.reload();
                        }, 3500); // Attendre 3500ms avant de recharger la page
                    } else {
                        error_notify('<?= js_phrase(get_phrase('action_not_allowed')); ?>')

                    }
                },
                error: function() {
                    error_notify(<?= js_phrase('an_error_occurred_during_submission'); ?>)
                }
            });
        });
    });
</script>

</script>
</script>
<!-- condition logo photo -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("school_image");
  const errorDiv = document.getElementById("image-error");
  const maxWidth = 512;
  const maxHeight = 512;
  const maxSizeMB = 2;

  fileInput.addEventListener("change", function (e) {
    errorDiv.textContent = ""; // réinitialiser le message
    const file = e.target.files[0];
    if (!file) return;

    // Vérification du poids
    const fileSizeMB = file.size / 1024 / 1024;
    if (fileSizeMB > maxSizeMB) {
      errorDiv.textContent = `⚠️ <?php echo get_phrase("The_file_is_too_large!_Maximum") ?> ${maxSizeMB} MB <?php echo get_phrase("allowed") ?>.`;
      fileInput.value = "";
      return;
    }

    // Vérification des dimensions
    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = function () {
      if (img.width > maxWidth || img.height > maxHeight) {
        errorDiv.textContent = `⚠️ <?php echo get_phrase("The_logo_is_too_large!_Maximum") ?> ${maxWidth}×${maxHeight} pixels.`;
        fileInput.value = "";
      } else {
        errorDiv.textContent = ""; // OK
      }
      URL.revokeObjectURL(objectUrl);
    };

    img.onerror = function() {
      errorDiv.textContent = "⚠️ <?php echo get_phrase("Unable_to_upload_this_image._Check_the_format_(PNG/JPEG)..") ?>";
      fileInput.value = "";
      URL.revokeObjectURL(objectUrl);
    }

    img.src = objectUrl;
  });

  // Tooltip Bootstrap
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (el) {
    const tooltipInstance = bootstrap.Tooltip.getInstance(el);
    if (tooltipInstance) tooltipInstance.dispose();
    new bootstrap.Tooltip(el, { trigger: 'hover', delay: { show: 200, hide: 0 } });
  });
});
</script>


<!-- condition cover photo -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("school_cover");
  const errorDiv = document.getElementById("cover-error");

  const maxWidth = 1600;   // largeur max
  const maxHeight = 900;   // hauteur max
  const maxSizeMB = 2;     // poids max

  fileInput.addEventListener("change", function (e) {
    errorDiv.textContent = "";
    errorDiv.style.display = "none";

    const file = e.target.files[0];
    if (!file) return;

    // Vérification du poids
    const fileSizeMB = file.size / 1024 / 1024;
    if (fileSizeMB > maxSizeMB) {
      errorDiv.textContent = `⚠️ <?php echo get_phrase("The file is too large! Maximum") ?> ${maxSizeMB} MB <?php echo get_phrase("allowed") ?>.`;
      errorDiv.style.display = "block";
      fileInput.value = "";
      return;
    }

    // Vérification des dimensions
    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = function () {
      if (img.width > maxWidth || img.height > maxHeight) {
        errorDiv.textContent = `⚠️ <?php echo get_phrase("Cover_image_is_too_large!_Maximum") ?> ${maxWidth}×${maxHeight} pixels.`;
        errorDiv.style.display = "block";
        fileInput.value = "";
      } else {
        errorDiv.textContent = "";
        errorDiv.style.display = "none";
      }
      URL.revokeObjectURL(objectUrl);
    };

    img.onerror = function() {
      errorDiv.textContent = "⚠️ <?php echo get_phrase("Unable_to_upload_this_image._Please_check_the_format_(PNG/JPEG).") ?>";
      errorDiv.style.display = "block";
      fileInput.value = "";
      URL.revokeObjectURL(objectUrl);
    }

    img.src = objectUrl;
  });

  // Initialisation tooltip Bootstrap
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('.tooltip-label'));
  tooltipTriggerList.forEach(function(el){
    const tooltipInstance = bootstrap.Tooltip.getInstance(el);
    if (tooltipInstance) tooltipInstance.dispose();
    new bootstrap.Tooltip(el, { trigger: 'hover', delay: { show: 200, hide: 0 } });
  });
});
</script>