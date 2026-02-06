<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/schoolSettings.min.css">

<?php 
$school_data = $this->settings_model->get_current_school_data();
$settings_school = $this->settings_model->get_current_settings_school_data();
?>

<style>
:root {
    --set-primary: #6366f1;
    --set-primary-rgb: 99, 102, 241;
    --set-success: #10b981;
    --set-danger: #ef4444;
    --set-dark: #1e293b;
    --set-gray: #64748b;
    --set-light: #f8fafc;
    --set-border: #e2e8f0;
    --set-white: #ffffff;
}

.set-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
.set-header-icon { width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
.set-header-text h4 { margin: 0; color: white; font-size: 1.35rem; font-weight: 700; }
.set-header-text p { margin: 0.25rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem; }

.set-card { background: var(--set-white); border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid var(--set-border); overflow: hidden; margin-bottom: 1.5rem; }
.set-card-header { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-bottom: 1px solid var(--set-border); display: flex; align-items: center; gap: 0.75rem; }
.set-card-header i { color: var(--set-primary); font-size: 1.25rem; }
.set-card-header h5 { margin: 0; font-size: 1rem; font-weight: 600; color: var(--set-dark); }
.set-card-body { padding: 1.5rem; }

.set-form-group { margin-bottom: 1.25rem; }
.set-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.set-form-label { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: var(--set-dark); }
.set-form-label i { color: var(--set-primary); font-size: 1rem; }
.set-form-label .required { color: var(--set-danger); margin-left: 0.25rem; }

.set-input { width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--set-border); border-radius: 10px; font-size: 0.9375rem; background: var(--set-light); color: var(--set-dark); transition: all 0.2s; }
.set-input:focus { outline: none; border-color: var(--set-primary); background: var(--set-white); box-shadow: 0 0 0 4px rgba(var(--set-primary-rgb), 0.1); }
.set-input.is-invalid { border-color: var(--set-danger); }
.set-textarea { min-height: 100px; resize: vertical; }

.set-form-hint { font-size: 0.75rem; color: var(--set-gray); margin-top: 0.375rem; }
.set-form-error { font-size: 0.8rem; color: var(--set-danger); margin-top: 0.5rem; display: none; padding: 0.5rem 0.75rem; background: rgba(239, 68, 68, 0.1); border-radius: 6px; border-left: 3px solid var(--set-danger); }

.set-upload-card { border: 2px dashed var(--set-border); border-radius: 12px; padding: 1.5rem; text-align: center; transition: all 0.2s; cursor: pointer; position: relative; }
.set-upload-card:hover { border-color: var(--set-primary); background: rgba(var(--set-primary-rgb), 0.02); }
.set-upload-card.is-dragover { border-color: var(--set-primary); background: rgba(var(--set-primary-rgb), 0.08); transform: scale(1.01); }
.set-upload-card .drag-text { font-size: 0.8rem; color: var(--set-gray); margin-top: 0.5rem; }
.set-upload-card .drag-text i { margin-right: 0.25rem; }
.set-upload-card.is-logo { max-width: 200px; margin: 0 auto; padding: 1rem; }
.set-upload-preview { width: 120px; height: 120px; border-radius: 12px; overflow: hidden; margin: 0 auto 1rem; border: 2px solid var(--set-border); background: var(--set-light); }
.set-upload-preview img { width: 100%; height: 100%; object-fit: cover; }
.set-upload-preview.cover { width: 100%; max-width: 300px; height: 100px; border-radius: 8px; }
.set-upload-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: var(--set-light); border: 2px solid var(--set-border); border-radius: 10px; font-size: 0.875rem; font-weight: 600; color: var(--set-dark); cursor: pointer; transition: all 0.2s; }
.set-upload-btn:hover { border-color: var(--set-primary); color: var(--set-primary); }
.set-upload-btn i { font-size: 1.125rem; }

.set-doc-card { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(var(--set-primary-rgb), 0.05); border: 1px solid rgba(var(--set-primary-rgb), 0.2); border-radius: 12px; }
.set-doc-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--set-success); display: flex; align-items: center; justify-content: center; color: white; }
.set-doc-info { flex: 1; }
.set-doc-name { font-weight: 600; color: var(--set-dark); font-size: 0.875rem; }
.set-doc-meta { font-size: 0.75rem; color: var(--set-gray); }
.set-doc-actions { display: flex; gap: 0.5rem; }
.set-doc-btn { padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; }
.set-doc-btn.view { background: var(--set-light); color: var(--set-primary); }
.set-doc-btn.replace { background: var(--set-light); color: var(--set-dark); border: 1px solid var(--set-border); }
.set-doc-btn.delete { background: rgba(239, 68, 68, 0.1); color: var(--set-danger); }

.set-form-actions { margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid var(--set-border); text-align: center; }
.set-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.875rem 2rem; border-radius: 12px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
.set-btn-primary { background: linear-gradient(135deg, var(--set-primary), #8b5cf6); color: white; box-shadow: 0 4px 12px rgba(var(--set-primary-rgb), 0.3); }
.set-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(var(--set-primary-rgb), 0.4); }
.set-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

@media (max-width: 768px) {
    .set-form-row { grid-template-columns: 1fr; }
}
</style>

<!-- Header -->
<div class="set-header">
    <div class="set-header-icon">
        <i class="mdi mdi-cog"></i>
    </div>
    <div class="set-header-text">
        <h4><?php echo get_phrase('school_settings'); ?></h4>
        <p><?php echo get_phrase('manage_your_community_settings'); ?></p>
    </div>
</div>

<form method="POST" class="schoolForm" action="<?php echo route('school_settings/update'); ?>" id="schoolForm" enctype="multipart/form-data">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

    <!-- General Information Card -->
    <div class="set-card">
        <div class="set-card-header">
            <i class="mdi mdi-information-outline"></i>
            <h5><?php echo get_phrase('general_information'); ?></h5>
        </div>
        <div class="set-card-body">
            <div class="set-form-group">
                <label class="set-form-label">
                    <i class="mdi mdi-office-building"></i>
                    <span><?php echo get_phrase('school_name'); ?></span>
                    <span class="required">*</span>
                </label>
                <input type="text" id="school_name" name="school_name" class="set-input" value="<?php echo $school_data['name']; ?>" required>
                <small id="school-name-error" class="set-form-error"></small>
            </div>

            <div class="set-form-group">
                <label class="set-form-label">
                    <i class="mdi mdi-text"></i>
                    <span><?php echo get_phrase('description'); ?></span>
                    <span class="required">*</span>
                </label>
                <textarea id="description" name="description" class="set-input set-textarea" required><?php echo $school_data['description']; ?></textarea>
                <div class="set-form-hint"><?php echo get_phrase('provide_admin_description'); ?></div>
            </div>

            <div class="set-form-row">
                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-phone"></i>
                        <span><?php echo get_phrase('phone'); ?></span>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="phone" name="phone" class="set-input" value="<?php echo $school_data['phone']; ?>" required>
                    <small id="phone-error" class="set-form-error"></small>
                </div>

                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-tag"></i>
                        <span><?php echo get_phrase('Category'); ?></span>
                        <span class="required">*</span>
                    </label>
                    <select name="category" id="category" class="set-input" required>
                        <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                        <?php $categories = $this->db->get_where('categories', array())->result_array();
                        foreach ($categories as $categorie): ?>
                        <option <?php if ($school_data['category'] == $categorie['name']): ?> selected <?php endif; ?> value="<?php echo $categorie['name']; ?>"><?php echo $categorie['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="set-form-group">
                <label class="set-form-label">
                    <i class="mdi mdi-eye"></i>
                    <span><?php echo get_phrase('Access'); ?></span>
                    <span class="required">*</span>
                </label>
                <select name="access" id="access" class="set-input" required>
                    <option value=""><?php echo get_phrase('select_a_access'); ?></option>
                    <option <?php if ($school_data['access'] == 1): ?> selected <?php endif; ?> value="1"><?php echo get_phrase('public'); ?></option>
                    <option <?php if ($school_data['access'] == 0): ?> selected <?php endif; ?> value="0"><?php echo get_phrase('privé'); ?></option>
                </select>
                <div class="set-form-hint"><?php echo get_phrase('provide_admin_access'); ?></div>
            </div>
        </div>
    </div>

    <!-- Address Card -->
    <div class="set-card">
        <div class="set-card-header">
            <i class="mdi mdi-map-marker"></i>
            <h5><?php echo get_phrase('address'); ?></h5>
        </div>
        <div class="set-card-body">
            <div class="set-form-row">
                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-road"></i>
                        <span><?php echo get_phrase("Rue") ?></span>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="communityStreet" name="communityStreet" class="set-input" value="<?php echo $school_data['Rue']; ?>" required>
                    <small id="communityStreet-error" class="set-form-error"></small>
                </div>

                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-numeric"></i>
                        <span><?php echo get_phrase("Numéro") ?></span>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="communityNumber" name="communityNumber" class="set-input" value="<?php echo $school_data['Numero']; ?>" required>
                    <small id="communityNumber-error" class="set-form-error"></small>
                </div>
            </div>

            <div class="set-form-row">
                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-city"></i>
                        <span><?php echo get_phrase("Ville") ?></span>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="communityCity" name="communityCity" class="set-input" value="<?php echo $school_data['Ville']; ?>" required>
                    <small id="communityCity-error" class="set-form-error"></small>
                </div>

                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-mailbox"></i>
                        <span><?php echo get_phrase("code_postal") ?></span>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="communityPostalCode" name="communityPostalCode" class="set-input" value="<?php echo $school_data['Codepostal']; ?>" required>
                    <small id="communityPostalCode-error" class="set-form-error"></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Information Card -->
    <div class="set-card">
        <div class="set-card-header">
            <i class="mdi mdi-briefcase"></i>
            <h5><?php echo get_phrase('business_information'); ?></h5>
        </div>
        <div class="set-card-body">
            <div class="set-form-row">
                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-account-tie"></i>
                        <span><?php echo get_phrase("I_am") ?></span>
                        <span class="required">*</span>
                    </label>
                    <select id="i_am" name="i_am" class="set-input" required>
                        <option value=""><?php echo get_phrase('select_a_status'); ?></option>
                        <option value="Entreprise" <?php if (($settings_school['type'] ?? '') == 'Entreprise'): ?> selected <?php endif; ?>><?php echo get_phrase("Entreprise") ?></option>
                        <option value="Freelancer" <?php if (($settings_school['type'] ?? '') == 'Freelancer'): ?> selected <?php endif; ?>><?php echo get_phrase("Freelancer") ?></option>
                        <option value="Autoentrepreneur" <?php if (($settings_school['type'] ?? '') == 'Autoentrepreneur'): ?> selected <?php endif; ?>><?php echo get_phrase("Autoentrepreneur") ?></option>
                        <option value="Particulier" <?php if (($settings_school['type'] ?? '') == 'Particulier'): ?> selected <?php endif; ?>><?php echo get_phrase("Particulier") ?></option>
                    </select>
                </div>

                <div class="set-form-group" id="row_vat_number">
                    <label class="set-form-label">
                        <i class="mdi mdi-file-document"></i>
                        <span><?php echo get_phrase("Numero_de_TVA") ?></span>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="vat_number" name="num_vat" class="set-input" value="<?php echo $settings_school['num_vat'] ?? ''; ?>">
                    <small id="vat_number-error" class="set-form-error"></small>
                </div>
            </div>

            <?php 
            $tax_residence = $school_data['country'] ?? '';
            if ($tax_residence === 'AE') $tax_residence = 'UAE';
            ?>

            <div class="set-form-group">
                <label class="set-form-label">
                    <i class="mdi mdi-earth"></i>
                    <span><?php echo get_phrase("Pays_de_résidence_fiscale") ?></span>
                    <span class="required">*</span>
                </label>
                <select name="tax_residence" id="tax_residence" class="set-input" required onchange="handleTaxResidenceChange(this.value)">
                    <option value=""><?php echo get_phrase("Sélectionnez_un_pays") ?></option>
                    <option value="MA" <?php if ($tax_residence == 'MA'): ?> selected <?php endif; ?>><?php echo get_phrase("Morocco") ?></option>
                    <option value="AE" <?php if ($tax_residence == 'UAE'): ?> selected <?php endif; ?>><?php echo get_phrase("United_Arab_Emirates") ?></option>
                </select>
            </div>

            <!-- Document Upload Section -->
            <div id="document_upload" style="display: <?php echo ($tax_residence == 'MA' || $tax_residence == 'UAE') ? 'block' : 'none'; ?>;">
                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-file-upload"></i>
                        <span><?php echo get_phrase("Document_justificatif") ?></span>
                        <span class="required">*</span>
                    </label>
                    
                    <?php if (!empty($settings_school['file']) && file_exists('uploads/community_tax/' . $settings_school['file'])): 
                        $docPath = 'uploads/community_tax/' . $settings_school['file'];
                        $fileUrl = base_url($docPath);
                        $fileName = basename($docPath);
                        $fileSizeK = file_exists($docPath) ? round(filesize($docPath) / 1024) : 0;
                    ?>
                    <div id="document-loaded-state">
                        <div class="set-doc-card">
                            <div class="set-doc-icon"><i class="mdi mdi-check"></i></div>
                            <div class="set-doc-info">
                                <div class="set-doc-name"><?php echo htmlspecialchars($fileName); ?></div>
                                <div class="set-doc-meta"><?php echo $fileSizeK; ?> Ko</div>
                            </div>
                            <div class="set-doc-actions">
                                <a href="<?php echo $fileUrl; ?>" target="_blank" class="set-doc-btn view"><?php echo get_phrase("Voir"); ?></a>
                                <button type="button" id="replace-document-btn" class="set-doc-btn replace"><?php echo get_phrase("Remplacer"); ?></button>
                                <button type="button" id="delete-document-btn" class="set-doc-btn delete"><?php echo get_phrase("Supprimer"); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div id="document-upload-section" style="<?php echo (!empty($settings_school['file']) && file_exists('uploads/community_tax/' . $settings_school['file'])) ? 'display:none;' : ''; ?>">
                        <input type="file" id="tax_document" name="tax_document" class="set-input" accept=".pdf,.png,.jpg,.jpeg">
                        <div class="set-form-hint" id="document_hint">
                            <?php if ($tax_residence == 'MA'): ?>
                                <?php echo get_phrase("Veuillez_télécharger_une_attestation_fiscale_marocaine.") ?>
                            <?php elseif ($tax_residence == 'UAE'): ?>
                                <?php echo get_phrase("Veuillez_télécharger_une_licence_commerciale.") ?>
                            <?php endif; ?>
                        </div>
                        <div class="set-form-hint"><i class="mdi mdi-information-outline"></i> PDF, PNG, JPG (max 4 Mo)</div>
                        <div id="tax-document-error" class="set-form-error"></div>
                    </div>
                    <input type="hidden" id="delete_tax_document" name="delete_tax_document" value="0">
                </div>
            </div>
        </div>
    </div>

    <!-- Images Card -->
    <div class="set-card">
        <div class="set-card-header">
            <i class="mdi mdi-image-multiple"></i>
            <h5><?php echo get_phrase('images'); ?></h5>
        </div>
        <div class="set-card-body">
            <div class="set-form-row">
                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-image"></i>
                        <span><?php echo get_phrase('Community_profile_logo'); ?></span>
                    </label>
                    <div class="set-upload-card is-logo" data-upload="school_image">
                        <div class="set-upload-preview" id="school-image-preview">
                            <img src="<?php echo $this->user_model->get_school_image($school_data['id']) . '?v=' . time(); ?>" class="preview-image" alt="Logo">
                        </div>
                        <label for="school_image" class="set-upload-btn">
                            <i class="mdi mdi-cloud-upload"></i>
                            <?php echo get_phrase('upload_an_image'); ?>
                        </label>
                        <div class="drag-text"><i class="mdi mdi-cursor-move"></i><?php echo get_phrase('or_drag_and_drop'); ?></div>
                        <input id="school_image" type="file" class="d-none image-upload" name="school_image" accept="image/*" data-preview="school-image-preview">
                        <div class="set-form-hint"><?php echo get_phrase('recommended_resolution'); ?>: 512×512 px • <?php echo get_phrase("animated_gifs_will_be_converted_to_static"); ?></div>
                        <div id="image-error" class="set-form-error"></div>
                    </div>
                </div>

                <div class="set-form-group">
                    <label class="set-form-label">
                        <i class="mdi mdi-panorama"></i>
                        <span><?php echo get_phrase('Community_cover_image'); ?></span>
                    </label>
                    <div class="set-upload-card" data-upload="school_cover">
                        <div class="set-upload-preview cover" id="school-cover-preview">
                            <img src="<?php echo $this->user_model->get_school_cover($school_data['id']) . '?v=' . time(); ?>" class="preview-image" alt="Cover">
                        </div>
                        <label for="school_cover" class="set-upload-btn">
                            <i class="mdi mdi-cloud-upload"></i>
                            <?php echo get_phrase('upload_an_image'); ?>
                        </label>
                        <div class="drag-text"><i class="mdi mdi-cursor-move"></i><?php echo get_phrase('or_drag_and_drop'); ?></div>
                        <input id="school_cover" type="file" class="d-none image-upload" name="school_cover" accept="image/*" data-preview="school-cover-preview">
                        <div class="set-form-hint"><?php echo get_phrase('recommended_resolution'); ?>: 1920×600 px • <?php echo get_phrase("animated_gifs_will_be_converted_to_static"); ?></div>
                        <div id="cover-error" class="set-form-error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="set-form-actions">
        <button type="submit" class="set-btn set-btn-primary" id="update-logos-btn">
            <i class="mdi mdi-content-save"></i>
            <?php echo get_phrase('update_settings'); ?>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Validation patterns
    const phoneRegex = /^(\+?\d{1,3}[- ]?)?\d{9,10}$/;
    const validations = {
        school_name: { min: 3, msg: '<?php echo get_phrase('The_name_of_the_school_must_contain_at_least_3_characters'); ?>' },
        description: { min: 10, msg: '<?php echo get_phrase('The_description_must_contain_at_least_10_characters'); ?>' },
        communityStreet: { min: 3, msg: '<?php echo get_phrase('Invalid_street_(minimum_3_characters)'); ?>' },
        communityCity: { min: 2, msg: '<?php echo get_phrase('Invalid_city_(minimum_2_characters)'); ?>' }
    };

    // Real-time validation
    Object.keys(validations).forEach(id => {
        $(`#${id}`).on('input', function() {
            const val = $(this).val().trim();
            const $error = $(`#${id.replace('community', '').toLowerCase()}-error, #${id}-error`);
            if (val.length < validations[id].min) {
                $(this).addClass('is-invalid');
                $error.text(validations[id].msg).show();
            } else {
                $(this).removeClass('is-invalid');
                $error.hide();
            }
        });
    });

    $('#phone').on('input', function() {
        const val = $(this).val().trim();
        if (!phoneRegex.test(val)) {
            $(this).addClass('is-invalid');
            $('#phone-error').text('<?php echo get_phrase('Numéro_invalide'); ?>').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#phone-error').hide();
        }
    });

    $('#communityNumber').on('input', function() {
        if (!/^[0-9]+$/.test($(this).val().trim())) {
            $(this).addClass('is-invalid');
            $('#communityNumber-error').text('<?php echo get_phrase('Invalid_number_(digits_only)'); ?>').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#communityNumber-error').hide();
        }
    });

    $('#communityPostalCode').on('input', function() {
        if (!/^[0-9]{4,5}$/.test($(this).val().trim())) {
            $(this).addClass('is-invalid');
            $('#communityPostalCode-error').text('<?php echo get_phrase('Invalid_postal_code_(4_or_5_digits)'); ?>').show();
        } else {
            $(this).removeClass('is-invalid');
            $('#communityPostalCode-error').hide();
        }
    });

    // Toggle VAT field
    function toggleVat() {
        const isParticulier = $('#i_am').val() === 'Particulier';
        $('#row_vat_number').toggleClass('d-none', isParticulier);
        if (isParticulier) {
            $('#vat_number').val('').prop('disabled', true).removeAttr('required');
        } else {
            $('#vat_number').prop('disabled', false);
        }
    }
    toggleVat();
    $('#i_am').on('change', toggleVat);

    // Image preview
    $('.image-upload').each(function() {
        $(this).on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                const previewId = $(this).data('preview');
                reader.onload = e => $(`#${previewId} .preview-image`).attr('src', e.target.result);
                reader.readAsDataURL(file);
            }
        });
    });

    // Clear image errors when selecting a new file
    $('#school_image').on('change', function() {
        $('#image-error').text('').hide();
    });
    $('#school_cover').on('change', function() {
        $('#cover-error').text('').hide();
    });

    // Drag and Drop functionality
    $('.set-upload-card').each(function() {
        const card = $(this);
        const inputId = card.data('upload');
        const input = $(`#${inputId}`);
        
        // Click on card triggers file input
        card.on('click', function(e) {
            if (!$(e.target).is('label, label *, input')) {
                input.trigger('click');
            }
        });

        // Drag events
        card.on('dragenter dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            card.addClass('is-dragover');
        });

        card.on('dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            card.removeClass('is-dragover');
        });

        card.on('drop', function(e) {
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                // Set file to input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(files[0]);
                input[0].files = dataTransfer.files;
                input.trigger('change');
            }
        });
    });

    // Form submission
    $('#schoolForm').submit(function(e) {
        e.preventDefault();
        
        // Clear previous image errors
        $('#image-error, #cover-error').text('').hide();
        
        const $btn = $('#update-logos-btn');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase('updating'); ?>...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $btn.css({'background': 'linear-gradient(135deg, #10b981, #34d399)'})
                        .html('<i class="mdi mdi-check-circle"></i> <?php echo get_phrase('updated'); ?>!');
                    if (response.csrf) {
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save"></i> <?php echo get_phrase('update_settings'); ?>');
                    
                    // Handle specific image errors
                    if (response.error_type === 'logo') {
                        $('#image-error').text(response.error_message).show();
                        // Scroll to the error
                        $('html, body').animate({
                            scrollTop: $('#image-error').offset().top - 100
                        }, 300);
                        // Clear the file input
                        $('#school_image').val('');
                    } else if (response.error_type === 'cover') {
                        $('#cover-error').text(response.error_message).show();
                        // Scroll to the error
                        $('html, body').animate({
                            scrollTop: $('#cover-error').offset().top - 100
                        }, 300);
                        // Clear the file input
                        $('#school_cover').val('');
                    } else {
                        // Generic error
                        toastr.error(response.notification || '<?php echo get_phrase('action_not_allowed'); ?>');
                    }
                    
                    // Update CSRF token if provided
                    if (response.csrf) {
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    }
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="mdi mdi-content-save"></i> <?php echo get_phrase('update_settings'); ?>');
                toastr.error('<?php echo get_phrase('an_error_occurred'); ?>');
            }
        });
    });
});

function handleTaxResidenceChange(country) {
    const documentUpload = document.getElementById('document_upload');
    const documentHint = document.getElementById('document_hint');
    if (country === 'MA' || country === 'UAE') {
        documentUpload.style.display = 'block';
        if (documentHint) {
            documentHint.textContent = country === 'MA' 
                ? '<?php echo get_phrase("Veuillez_télécharger_une_attestation_fiscale_marocaine."); ?>'
                : '<?php echo get_phrase("Veuillez_télécharger_une_licence_commerciale."); ?>';
        }
    } else {
        documentUpload.style.display = 'none';
    }
}

// Document management
$('#replace-document-btn').on('click', function() {
    $('#document-loaded-state').hide();
    $('#document-upload-section').show();
});

$('#delete-document-btn').on('click', function() {
    if (confirm('<?php echo get_phrase("Êtes-vous sûr de vouloir supprimer ce document ?"); ?>')) {
        $.ajax({
            url: '<?php echo base_url("admin/school_settings/delete_tax_document"); ?>',
            type: 'POST',
            data: { <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>' },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#document-loaded-state').hide();
                    $('#document-upload-section').show();
                    toastr.success('<?php echo get_phrase("Document supprimé avec succès"); ?>');
                }
            }
        });
    }
});
</script>