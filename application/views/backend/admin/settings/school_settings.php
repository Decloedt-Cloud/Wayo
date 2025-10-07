<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/schoolSettings.css">


<?php 
$school_data = $this->settings_model->get_current_school_data();
$settings_school = $this->settings_model->get_current_settings_school_data();

?>



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
                        <label class="col-md-3 col-form-label" for="communityStreet"><?php echo get_phrase("Rue") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="communityStreet" name="communityStreet" class="form-control" value="<?php echo $school_data['Rue']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="communityNumber"><?php echo get_phrase("Numéro") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="communityNumber" name="communityNumber" class="form-control" value="<?php echo $school_data['Numero']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="communityCity"><?php echo get_phrase("Ville") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="communityCity" name="communityCity" class="form-control" value="<?php echo $school_data['Ville']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="communityPostalCode"><?php echo get_phrase("code_postal") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="communityPostalCode" name="communityPostalCode" class="form-control" value="<?php echo $school_data['Codepostal']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="communityPostalCode"><?php echo get_phrase("I_am") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <select id="i_am" aria-required="true" name="i_am" class="form-control shadow-none" required
                            data-msg="Please select your tax residence." data-error-class="u-has-error" data-success-class="u-has-success">
                            <option value=""><?php echo get_phrase('select_a_status'); ?></option>

                            <option value="Entreprise" <?php if ($settings_school['type'] == 'Entreprise'): ?> selected <?php endif; ?>> <?php echo get_phrase("Entreprise") ?></option>
                            <option value="Freelancer" <?php if ($settings_school['type'] == 'Freelancer'): ?> selected <?php endif; ?>> <?php echo get_phrase("Freelancer") ?> </option>
                            <option value="Autoentrepreneur" <?php if ($settings_school['type'] == 'Autoentrepreneur'): ?> selected <?php endif; ?>> <?php echo get_phrase("Autoentrepreneur") ?> </option>
                            <option value="Particulier" <?php if ($settings_school['type'] == 'Particulier'): ?> selected <?php endif; ?>> <?php echo get_phrase("Particulier") ?> </option>
                        
                            </select>                       
                        </div>
                    </div>
                    
                    

                    <div class="form-group row mb-3" id="row_vat_number">
                        <label class="col-md-3 col-form-label" for="vat_number"><?php echo get_phrase("Numero_de_TVA") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="vat_number" name="vat_number" value="<?php echo $settings_school['num_vat']; ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="tax_residence"><?php echo get_phrase("Pays_de_résidence_fiscale") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <select name="tax_residence" id="tax_residence" class="form-control" required onchange="handleTaxResidenceChange(this.value)">
                                <option value=""><?php echo get_phrase("Sélectionnez_un_pays") ?></option>
                                <option value="MA" <?php if ($settings_school['Tax_residence'] == 'MA'): ?> selected <?php endif; ?>><?php echo get_phrase("Morocco") ?></option>
                                <option value="UAE" <?php if ($settings_school['Tax_residence'] == 'UAE'): ?> selected <?php endif; ?>><?php echo get_phrase("United_Arab_Emirates") ?></option>
                            </select>
                        </div>
                    </div>
                    <div id="document_upload" style="display: <?php echo ($settings_school['Tax_residence'] == 'MA' || $settings_school['Tax_residence'] == 'UAE') ? 'block' : 'none'; ?>;">
                        <div class="form-group row mb-3" >
                            <label class="col-md-3 col-form-label" for="tax_document">
                                <i class="mdi mdi-file-document-outline"></i> <?php echo get_phrase("Document_justificatif") ?><span class="required"> * </span>
                            </label>
                            <div class="col-md-9">
                                <input type="file" id="tax_document" name="tax_document" class="form-control" accept="application/pdf,image/*">
                                <small id="document_hint" class="form-text text-muted">
                                    <?php if ($settings_school['Tax_residence'] == 'MA'): ?>
                                       
                                        <?php echo get_phrase("Veuillez_télécharger_une_attestation_fiscale_marocaine.") ?>
                                    <?php elseif ($settings_school['Tax_residence'] == 'UAE'): ?>
                                        
                                        <?php echo get_phrase("Veuillez_télécharger_une_licence_commerciale.") ?>
                                    <?php endif; ?>
                                </small>
                            </div>

                        </div>

                    </div>
                     <div class="form-group row mb-3" id="document_upload">
                                <?php if (!empty($settings_school['file']) && file_exists('uploads/community_tax/' . $settings_school['file'])): ?>
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <i class="mdi mdi-check-circle-outline me-2"></i>
                                        <div>
                                            <?php echo get_phrase("Un_document_est_déjà_téléchargé.") ?>
                                           
                                            <a href="<?php echo base_url('uploads/community_tax/' . $settings_school['file']); ?>" target="_blank" class="btn btn-link btn-sm p-0">
                                               <?php echo get_phrase("Voir_le_document") ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                    </div>




                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="example-fileinput"><?php echo get_phrase('school_profile_image'); ?></label>
                        <div class="col-md-5 logo-upload-container">
                            <div class="logo-card">
                                <div class="logo-header">
                                    <h5><?php echo get_phrase('school_profile_image'); ?></h5>
                                </div>
                                <div class="logo-preview" id="school-image-preview">
                                    <img src="<?php echo $this->user_model->get_school_image($school_data['id']) . '?v=' . time(); ?>" alt="School Profile Image" class="preview-image">
                                    <div class="logo-overlay">
                                        <i class="fas fa-camera"></i>
                                    </div>

                                </div>
                                <div class="logo-upload-btn">
                                    <label for="school_image">
                                        <i class="mdi mdi-cloud-upload"></i> <?php echo get_phrase('upload_an_image'); ?>
                                    </label>
                                    <input id="school_image" type="file" class="image-upload" name="school_image" accept="image/*" data-preview="school-image-preview">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-l px-4" id="update-logos-btn" id="update-logos-btn" >
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
        // Fonction pour récupérer and retourner le token CSRF
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

    function handleTaxResidenceChange(country) {
        const documentUpload = document.getElementById('document_upload');
        const documentHint = document.getElementById('document_hint');

        if (country === 'MA') {
            documentUpload.style.display = 'block';
            documentHint.textContent = 'Veuillez télécharger une attestation fiscale marocaine.';
        } else if (country === 'UAE') {
            documentUpload.style.display = 'block';
            documentHint.textContent = 'Veuillez télécharger une licence commerciale.';
        } else {
            documentUpload.style.display = 'none';
            documentHint.textContent = '';
        }
    }
</script>

<script>
$(document).ready(function() {
    $('#schoolForm').submit(function(e) {
        let isValid = true;

        // Validate Rue
        const street = $('#communityStreet');
        if (!street.val().trim() || street.val().trim().length < 3) {
            isValid = false;
            street.addClass('is-invalid');
            if (!street.next('.invalid-feedback').length) {
                street.after('<div class="invalid-feedback">Rue invalide (3 caractères min).</div>');
            }
        } else {
            street.removeClass('is-invalid');
            street.next('.invalid-feedback').remove();
        }

        // Validate Numéro
        const number = $('#communityNumber');
        if (!/^[0-9]+$/.test(number.val().trim())) {
            isValid = false;
            number.addClass('is-invalid');
            if (!number.next('.invalid-feedback').length) {
                number.after('<div class="invalid-feedback">Numéro invalide (uniquement des chiffres).</div>');
            }
        } else {
            number.removeClass('is-invalid');
            number.next('.invalid-feedback').remove();
        }

        // Validate Ville
        const city = $('#communityCity');
        if (!city.val().trim() || city.val().trim().length < 2) {
            isValid = false;
            city.addClass('is-invalid');
            if (!city.next('.invalid-feedback').length) {
                city.after('<div class="invalid-feedback">Ville invalide (2 caractères min).</div>');
            }
        } else {
            city.removeClass('is-invalid');
            city.next('.invalid-feedback').remove();
        }

        // Validate Code postal
        const postalCode = $('#communityPostalCode');
        if (!/^[0-9]{4,5}$/.test(postalCode.val().trim())) {
            isValid = false;
            postalCode.addClass('is-invalid');
            if (!postalCode.next('.invalid-feedback').length) {
                postalCode.after('<div class="invalid-feedback">Code postal invalide (4 à 5 chiffres).</div>');
            }
        } else {
            postalCode.removeClass('is-invalid');
            postalCode.next('.invalid-feedback').remove();
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
  const selectStatus = document.getElementById('i_am');
  const rowVat = document.getElementById('row_vat_number');
  const vatInput = document.getElementById('vat_number');

  function toggleVat() {
    const isParticulier = (selectStatus.value === 'Particulier');
    // Cache/affiche le bloc
    rowVat.classList.toggle('d-none', isParticulier);

    // Gère les contraintes du champ
    if (isParticulier) {
      vatInput.value = '';
      vatInput.setAttribute('disabled', 'disabled');
      vatInput.removeAttribute('required');
      vatInput.setAttribute('aria-required', 'false');
    } else {
      vatInput.removeAttribute('disabled');
      // Décommentez si le champ doit être obligatoire pour non-Particulier
      // vatInput.setAttribute('required', 'required');
      // vatInput.setAttribute('aria-required', 'true');
    }
  }

  toggleVat();                   // État initial (pré-sélection serveur)
  selectStatus.addEventListener('change', toggleVat);
});
</script>