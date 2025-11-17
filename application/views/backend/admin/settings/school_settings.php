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
                             <small id="school-name-error" class="text-danger" style="display:none;"></small>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="description"><?php echo get_phrase('description'); ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $school_data['description']; ?></textarea>
                            <small id="description-error" class="form-text text-muted"><?php echo get_phrase('provide_admin_description'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="phone"><?php echo get_phrase('phone'); ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo $school_data['phone']; ?>" required>
                             <small id="phone-error" class="text-danger" style="display:none;"></small>
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
                            <small id="communityStreet-error" class="text-danger" style="display:none;"></small>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="communityNumber"><?php echo get_phrase("Numéro") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="communityNumber" name="communityNumber" class="form-control" value="<?php echo $school_data['Numero']; ?>" required>
                            <small id="communityNumber-error" class="text-danger" style="display:none;"></small>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="communityCity"><?php echo get_phrase("Ville") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="communityCity" name="communityCity" class="form-control" value="<?php echo $school_data['Ville']; ?>" required>
                            <small id="communityCity-error" class="text-danger" style="display:none;"></small>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="communityPostalCode"><?php echo get_phrase("code_postal") ?><span class="required"> * </span></label>
                        <div class="col-md-9">
                            <input type="text" id="communityPostalCode" name="communityPostalCode" class="form-control" value="<?php echo $school_data['Codepostal']; ?>" required>
                            <small id="communityPostalCode-error" class="text-danger" style="display:none;"></small>
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
                            <small id="vat_number-error" class="text-danger" style="display:none;"></small>
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
                        <div class="form-group row mb-3">
                            <label class="col-md-3 col-form-label" for="tax_document">
                                <i class="mdi mdi-file-document-outline"></i> <?php echo get_phrase("Document_justificatif") ?><span class="required"> * </span>
                            </label>
                            <div class="col-md-9">
                                <!-- État: Document chargé -->
                                <?php if (!empty($settings_school['file']) && file_exists('uploads/community_tax/' . $settings_school['file'])): ?>
                                    <?php
                                        $docPath   = 'uploads/community_tax/' . $settings_school['file'];
                                        $fileUrl   = base_url($docPath);
                                        $fileName  = basename($docPath);
                                        $fileSizeK = file_exists($docPath) ? round(filesize($docPath) / 1024) : 0;
                                        $fileMTime = file_exists($docPath) ? filemtime($docPath) : 0;
                                    ?>
                                    <div id="document-loaded-state" class="mb-3">
                                        <div class="doc-card border rounded p-3">
                                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-cloud-check-outline text-success me-2 fs-4"></i>
                                                    <div class="fw-semibold"><?php echo get_phrase("Document chargé"); ?></div>
                                                </div>
                                                <a href="<?php echo $fileUrl; ?>" target="_blank" class="text-primary fw-semibold small" title="<?php echo get_phrase("Voir le document"); ?>"><?php echo get_phrase("Voir le document"); ?></a>
                                            </div>

                                            <div class="text-muted small mt-1">
                                                <span class="me-2"><?php echo htmlspecialchars($fileName); ?></span>
                                                <?php if ($fileSizeK): ?><span class="me-2"><?php echo $fileSizeK; ?> Ko</span><?php endif; ?>
                                                <?php if ($fileMTime): ?><span>maj <?php echo date('d M. Y', $fileMTime); ?></span><?php endif; ?>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2 mt-3">
                                                <button type="button" id="replace-document-btn" class="btn btn-sm btn-light border" title="<?php echo get_phrase("Remplacer"); ?>">
                                                    <?php echo get_phrase("Remplacer"); ?>
                                                </button>
                                                <button type="button" id="delete-document-btn" class="btn btn-sm btn-outline-danger" title="<?php echo get_phrase("Supprimer"); ?>">
                                                    <?php echo get_phrase("Supprimer"); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <style>
                                      .doc-card{background:#fff}
                                      @media (prefers-color-scheme:dark){.doc-card{background:var(--bs-body-bg)}}
                                    </style>
                                    <!-- Input file caché quand document chargé -->
                                    <div id="document-upload-section" style="display: none;">
                                        <input type="file" id="tax_document" name="tax_document" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                                        <small id="document_hint" class="form-text text-muted mt-1">
                                            <?php if ($settings_school['Tax_residence'] == 'MA'): ?>
                                                <?php echo get_phrase("Veuillez_télécharger_une_attestation_fiscale_marocaine.") ?>
                                            <?php elseif ($settings_school['Tax_residence'] == 'UAE'): ?>
                                                <?php echo get_phrase("Veuillez_télécharger_une_licence_commerciale.") ?>
                                            <?php endif; ?>
                                        </small>
                                        <small id="document-help" class="form-text text-muted">
                                            <i class="mdi mdi-information-outline"></i> PDF, PNG, JPG (max 4 Mo)
                                        </small>
                                        <!-- Zone d'erreur -->
                                        <div id="tax-document-error" class="text-danger mt-2 small fw-bold" style="display: none;"></div>
                                    </div>
                                    <input type="hidden" id="delete_tax_document" name="delete_tax_document" value="0">
                                <?php else: ?>
                                    <!-- État: Pas de document -->
                                    <div id="document-upload-section">
                                        <input type="file" id="tax_document" name="tax_document" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                                        <small id="document_hint" class="form-text text-muted mt-1">
                                            <?php if ($settings_school['Tax_residence'] == 'MA'): ?>
                                                <?php echo get_phrase("Veuillez_télécharger_une_attestation_fiscale_marocaine.") ?>
                                            <?php elseif ($settings_school['Tax_residence'] == 'UAE'): ?>
                                                <?php echo get_phrase("Veuillez_télécharger_une_licence_commerciale.") ?>
                                            <?php endif; ?>
                                        </small>
                                        <small id="document-help" class="form-text text-muted">
                                            <i class="mdi mdi-information-outline"></i> PDF, PNG, JPG (max 4 Mo)
                                        </small>
                                        <!-- Zone d'erreur -->
                                        <div id="tax-document-error" class="text-danger mt-2 small fw-bold" style="display: none;"></div>
                                    </div>
                                    <input type="hidden" id="delete_tax_document" name="delete_tax_document" value="0">
                                <?php endif; ?>
                            </div>
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
                        <button type="submit" class="btn btn-primary btn-l px-4" id="update-logos-btn" id="update-logos-btn" >
                            <i class="mdi mdi-account-check"></i>
                            <?php echo get_phrase('update_settings'); ?>
                        </button>
                    </div>

            </form>

        </div> <!-- end card body-->
    </div> <!-- end card -->
</div>


<script>
$(document).ready(function() {
    const phoneInput = $('#phone');
    const phoneError = $('#phone-error');
    const phoneRegex = /^(\+?\d{1,3}[- ]?)?\d{9,10}$/;

    const schoolNameInput = $('#school_name');
    const schoolNameError = $('#school-name-error');

    const descriptionInput = $('#description');
    const descriptionError = $('#description-error');

    const streetInput = $('#communityStreet');
    const streetError = $('#communityStreet-error');

    const numberInput = $('#communityNumber');
    const numberError = $('#communityNumber-error');

    const cityInput = $('#communityCity');
    const cityError = $('#communityCity-error');

    const postalCodeInput = $('#communityPostalCode');
    const postalCodeError = $('#communityPostalCode-error');

    const vatInput = $('#vat_number');
    const vatError = $('#vat_number-error');

    const selectStatus = $('#i_am'); // Particulier ou Entreprise

    // Fonction pour activer/désactiver VAT
    function toggleVat() {
        const isParticulier = (selectStatus.val() === 'Particulier');
        const rowVat = $('#row_vat_number');

        rowVat.toggleClass('d-none', isParticulier);

        if (isParticulier) {
            vatInput.val('');
            vatInput.prop('disabled', true);
            vatInput.removeAttr('required');
        } else {
            vatInput.prop('disabled', false);
        }
    }

    toggleVat();
    selectStatus.on('change', toggleVat);

    // Validation en temps réel
    phoneInput.on('input', function() {
        const value = $(this).val().trim();
        if (!phoneRegex.test(value)) {
            phoneError.text('<?php echo get_phrase('Numéro_invalide'); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            phoneError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    schoolNameInput.on('input', function() {
        const value = $(this).val().trim();
        if (value.length < 3) {
            schoolNameError.text('<?php echo get_phrase('The_name_of_the_school_must_contain_at_least_3_characters'); ?>').show();
            $(this).addClass('is-invalid');
        } else {
            schoolNameError.hide();
            $(this).removeClass('is-invalid');
        }
    });

    descriptionInput.on('input', function() {
    const value = $(this).val().trim();
    if (value.length < 10) {
        descriptionError.text('<?php echo get_phrase('The_description_must_contain_at_least_10_characters'); ?>').show();
        $(this).addClass('is-invalid');
    } else {
        descriptionError.hide();
        $(this).removeClass('is-invalid');
    }
});

streetInput.on('input', function() {
    const value = $(this).val().trim();
    if (value.length < 3) {
        streetError.text('<?php echo get_phrase('Invalid_street_(minimum_3_characters)'); ?>').show();
        $(this).addClass('is-invalid');
    } else {
        streetError.hide();
        $(this).removeClass('is-invalid');
    }
});

numberInput.on('input', function() {
    const value = $(this).val().trim();
    if (!/^[0-9]+$/.test(value)) {
        numberError.text('<?php echo get_phrase('Invalid_number_(digits_only)'); ?>').show();
        $(this).addClass('is-invalid');
    } else {
        numberError.hide();
        $(this).removeClass('is-invalid');
    }
});

cityInput.on('input', function() {
    const value = $(this).val().trim();
    if (value.length < 2) {
        cityError.text('<?php echo get_phrase('Invalid_city_(minimum_2_characters)'); ?>').show();
        $(this).addClass('is-invalid');
    } else {
        cityError.hide();
        $(this).removeClass('is-invalid');
    }
});

postalCodeInput.on('input', function() {
    const value = $(this).val().trim();
    if (!/^[0-9]{4,5}$/.test(value)) {
        postalCodeError.text('<?php echo get_phrase('Invalid_postal_code_(4_or_5_digits)'); ?>').show();
        $(this).addClass('is-invalid');
    } else {
        postalCodeError.hide();
        $(this).removeClass('is-invalid');
    }
});

vatInput.on('input', function() {
    const value = $(this).val().trim();
    if (!vatInput.prop('disabled') && value.length < 5) {
        vatError.text('<?php echo get_phrase('Invalid_VAT_number_(minimum_5_characters)'); ?>').show();
        $(this).addClass('is-invalid');
    } else {
        vatError.hide();
        $(this).removeClass('is-invalid');
    }
});

// Validation avant submit
$('#schoolForm').submit(function(e) {
    let isValid = true;

    if (!phoneRegex.test(phoneInput.val().trim())) {
        phoneError.text('Invalid_number').show();
        phoneInput.addClass('is-invalid');
        isValid = false;
    } else phoneError.hide(), phoneInput.removeClass('is-invalid');

    if (schoolNameInput.val().trim().length < 3) {
        schoolNameError.text('<?php echo get_phrase('The_name_of_the_school_must_contain_at_least_3_characters'); ?>').show();
        schoolNameInput.addClass('is-invalid');
        isValid = false;
    } else schoolNameError.hide(), schoolNameInput.removeClass('is-invalid');

    if (descriptionInput.val().trim().length < 10) {
        descriptionError.text('<?php echo get_phrase('The_description_must_contain_at_least_10_characters'); ?>').show();
        descriptionInput.addClass('is-invalid');
        isValid = false;
    } else descriptionError.hide(), descriptionInput.removeClass('is-invalid');

    if (streetInput.val().trim().length < 3) {
        streetError.text('<?php echo get_phrase('Invalid_street_(minimum_3_characters)'); ?>').show();
        streetInput.addClass('is-invalid');
        isValid = false;
    } else streetError.hide(), streetInput.removeClass('is-invalid');

    if (!/^[0-9]+$/.test(numberInput.val().trim())) {
        numberError.text('<?php echo get_phrase('Invalid_number_(digits_only)'); ?>').show();
        numberInput.addClass('is-invalid');
        isValid = false;
    } else numberError.hide(), numberInput.removeClass('is-invalid');

    if (cityInput.val().trim().length < 2) {
        cityError.text('<?php echo get_phrase('Invalid_city_(minimum_2_characters)'); ?>').show();
        cityInput.addClass('is-invalid');
        isValid = false;
    } else cityError.hide(), cityInput.removeClass('is-invalid');

    if (!/^[0-9]{4,5}$/.test(postalCodeInput.val().trim())) {
        postalCodeError.text('<?php echo get_phrase('Invalid_postal_code_(4_or_5_digits)'); ?>').show();
        postalCodeInput.addClass('is-invalid');
        isValid = false;
    } else postalCodeError.hide(), postalCodeInput.removeClass('is-invalid');

    if (!vatInput.prop('disabled') && vatInput.val().trim().length < 5) {
        vatError.text('<?php echo get_phrase('Invalid_VAT_number_(minimum_5_characters)'); ?>').show();
        vatInput.addClass('is-invalid');
        isValid = false;
    } else vatError.hide(), vatInput.removeClass('is-invalid');
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
});
</script>



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
            if (documentHint) {
                documentHint.textContent = 'Veuillez télécharger une attestation fiscale marocaine.';
            }
        } else if (country === 'UAE') {
            documentUpload.style.display = 'block';
            if (documentHint) {
                documentHint.textContent = 'Veuillez télécharger une licence commerciale.';
            }
        } else {
            documentUpload.style.display = 'none';
            if (documentHint) {
                documentHint.textContent = '';
            }
        }
    }
</script>

<!-- Script de gestion du document justificatif -->
<script>
$(document).ready(function() {
    const maxFileSizeMB = 4;
    const maxFileSizeBytes = maxFileSizeMB * 1024 * 1024;
    const allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
    const errorDiv = $('#tax-document-error');
    const fileInput = $('#tax_document');
    const deleteInput = $('#delete_tax_document');
    const documentUploadSection = $('#document-upload-section');
    const documentLoadedState = $('#document-loaded-state');
    const replaceBtn = $('#replace-document-btn');
    const deleteBtn = $('#delete-document-btn');

    // Fonction de validation du fichier
    function validateTaxDocument(file) {
        errorDiv.hide().text('');

        if (!file) {
            return false;
        }

        // Vérification de la taille
        if (file.size > maxFileSizeBytes) {
            errorDiv.text('⚠️ <?php echo get_phrase("Fichier trop volumineux (4Mo max)"); ?>').show();
            fileInput.val('');
            return false;
        }

        // Vérification de l'extension
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            errorDiv.text('⚠️ <?php echo get_phrase("Format non supporté"); ?>').show();
            fileInput.val('');
            return false;
        }

        return true;
    }

    // Validation lors de la sélection du fichier
    fileInput.on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (validateTaxDocument(file)) {
                errorDiv.hide();
            }
        }
    });

    // Bouton "Remplacer"
    if (replaceBtn.length) {
        replaceBtn.on('click', function() {
            documentLoadedState.hide();
            documentUploadSection.show();
            fileInput.val('');
            deleteInput.val('0');
        });
    }

    // Bouton "Supprimer"
    if (deleteBtn.length) {
        deleteBtn.on('click', function() {
            if (confirm('<?php echo get_phrase("Êtes-vous sûr de vouloir supprimer ce document ?"); ?>')) {
                deleteTaxDocument();
            }
        });
    }

    // Fonction de suppression du document via AJAX
    function deleteTaxDocument() {
        const btn = deleteBtn;
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i><?php echo get_phrase("Suppression..."); ?>');

        $.ajax({
            url: '<?php echo base_url("admin/school_settings/delete_tax_document"); ?>',
            type: 'POST',
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    // Mise à jour du token CSRF
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    
                    // Masquer l'état "Document chargé" et afficher l'input file
                    documentLoadedState.hide();
                    documentUploadSection.show();
                    fileInput.val('');
                    deleteInput.val('0');
                    
                    success_notify(response.notification || '<?php echo get_phrase("Document supprimé avec succès"); ?>');
                } else {
                    error_notify(response.notification || '<?php echo get_phrase("Suppression impossible"); ?>');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                error_notify('<?php echo get_phrase("Suppression impossible"); ?>');
                btn.prop('disabled', false).html(originalText);
            }
        });
    }

    // Validation avant soumission du formulaire
    $('#schoolForm').on('submit', function(e) {
        const taxResidence = $('#tax_residence').val();
        const hasDocument = documentLoadedState.is(':visible');
        const hasFileSelected = fileInput[0] && fileInput[0].files.length > 0;
        const isDeleteRequested = deleteInput.val() === '1';

        // Si résidence fiscale requiert un document
        if ((taxResidence === 'MA' || taxResidence === 'UAE')) {
            // Vérifier qu'un document existe OU qu'un nouveau fichier est sélectionné
            if (!hasDocument && !hasFileSelected && !isDeleteRequested) {
                error_notify('<?php echo get_phrase("Veuillez télécharger un document justificatif"); ?>');
                e.preventDefault();
                return false;
            }

            // Si un fichier est sélectionné, le valider
            if (hasFileSelected) {
                const file = fileInput[0].files[0];
                if (!validateTaxDocument(file)) {
                    e.preventDefault();
                    return false;
                }
            }
        }
    });

    // Réinitialiser le champ delete_tax_document si un nouveau fichier est sélectionné
    fileInput.on('change', function() {
        if (this.files.length > 0) {
            deleteInput.val('0');
        }
    });
});
</script>

<script>
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