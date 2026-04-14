<div id="createCommunityModal" class="modal-saas">
    <div class="modal-content-saas">
        <span class="close-button-saas">&times;</span>

        <!-- En-tête avec Stepper -->
        <div class="modal-header-community">
            <h3><?php echo get_phrase("create_a_new_community"); ?></h3>
            <div class="stepper-community">
                <div class="step-community active" data-step="1">
                    <span class="step-number">1</span>
                    <span class="step-label"><?php echo get_phrase("details"); ?></span>
                </div>
                <div class="step-divider"></div>
                <div class="step-community" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-label"><?php echo get_phrase("subscription"); ?></span>
                </div>
            </div>
        </div>

        <!-- FORMULAIRE GLOBAL (englobe les 2 étapes) -->
        <form id="create-community-form"
            action="<?php echo site_url('home/online_admission_school'); ?>"
            method="post"
            enctype="multipart/form-data"
            novalidate>

            <input type="hidden" id="community_csrf_token" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />

            <!-- ÉTAPE 1 -->
            <div class="modal-body-community" id="modal-step-1">
                <!-- Tous les champs (i_am, Tax_residence, etc.) -->
                <div class="form-group-community">
                    <label for="i_am"><?php echo get_phrase("I_am") ?> <span class="required-star">*</span></label>
                    <select id="i_am" name="i_am" class="form-input-community" required data-msg="<?php echo get_phrase("Please select your status") ?>">
                        <option value=""><?php echo get_phrase('select_a_status'); ?></option>
                        <option value="Entreprise"><?php echo get_phrase("Entreprise") ?></option>
                        <option value="Freelancer"><?php echo get_phrase("Freelancer") ?></option>
                        <option value="Autoentrepreneur"><?php echo get_phrase("Autoentrepreneur") ?></option>
                        <option value="Particulier"><?php echo get_phrase("Particulier") ?></option>
                    </select>
                </div>

                <div class="form-group-community">
                    <label for="Tax_residence"><?php echo get_phrase("Tax_residence") ?> <span class="required-star">*</span></label>
                    <select id="Tax_residence" name="Tax_residence" class="form-input-community" required data-msg="<?php echo get_phrase("Please select your tax residence") ?>">
                        <option value=""><?php echo get_phrase('select_a_Tax_residence'); ?></option>
                        <option value="MA"><?php echo get_phrase("Morocco") ?></option>
                        <option value="UAE"><?php echo get_phrase("United_Arab_Emirates") ?></option>
                    </select>
                </div>

                <div class="form-group-community">
                    <label for="category"><?php echo get_phrase("Category") ?><span class="required-star">*</span></label>
                    <select id="category" name="category" class="form-input-community" required data-msg="<?php echo get_phrase("please_select_a_category"); ?>">
                        <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                        <?php $categories = $this->db->table('categories')->get()->getResultArray(); ?>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo $categorie['name']; ?>"><?php echo get_phrase($categorie['name']) ; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group-community">
                    <label for="community-name"><?php echo get_phrase("community_name") ?> <span class="required-star">*</span></label>
                    <input type="text" id="community-name" name="school_name" class="form-input-community"
                        placeholder="Ex: Les As du Marketing" maxlength="80" required
                        data-msg="<?php echo get_phrase("Please enter your community name") ?>">
                    <small class="form-text"><?php echo get_phrase("Max._80_characters") ?></small>
                </div>

                <div class="form-group-community">
                    <label for="community-description">
                        <?php echo get_phrase("description") ?> <span class="required-star">*</span>
                        <span class="info" data-tooltip="<?php echo get_phrase("Goal_in_one_sentence_•_2)_For_whom_•_3)_Benefits_(3–5)_•_4)_Pace/rules._Min._40_characters.") ?>">
                            <i class="fa-solid fa-circle-info"></i>
                        </span>
                    </label>
                    <textarea id="community-description" name="school_description" class="form-input-community" rows="4"
                        placeholder="Describe your community..." required
                        data-msg="<?php echo get_phrase("Please enter a description (min 40 characters)") ?>"></textarea>
                    <small class="form-text"><?php echo get_phrase("At_least_40_characters.") ?></small>
                </div>

                <div class="form-group-community">
                    <label for="street"><?php echo get_phrase("Rue") ?> <span class="required-star">*</span></label>
                    <input type="text" id="street" name="street" class="form-input-community" placeholder="<?php echo get_phrase("Rue") ?>" required>
                </div>
                <div class="form-group-community">
                    <label for="number"><?php echo get_phrase("Numéro") ?> <span class="required-star">*</span></label>
                    <input type="text" id="number" name="number" class="form-input-community" placeholder="<?php echo get_phrase("Numéro") ?>" required>
                </div>
                <div class="form-group-community">
                    <label for="city"><?php echo get_phrase("Ville") ?> <span class="required-star">*</span></label>
                    <input type="text" id="city" name="city" class="form-input-community" placeholder="<?php echo get_phrase("Ville") ?>" required>
                </div>
                <div class="form-group-community">
                    <label for="code_postal"><?php echo get_phrase("code_postal") ?> <span class="required-star">*</span></label>
                    <input type="text" id="code_postal" name="postal_code" class="form-input-community" placeholder="<?php echo get_phrase("code_postal") ?>" required>
                </div>             
                <label class="switch emph">
                    <input type="hidden" name="visibility" value="0">
                    <input id="isPrivate" name="visibility" type="checkbox" value="1" />
                    <span class="switch-emph">
                        <i class="fa-solid fa-lock"></i>
                        <strong><?php echo get_phrase("Private_community") ?></strong>
                        <em><?php echo get_phrase("(access_upon_approval)") ?></em>
                        <span class="info field-label" data-tooltip="<?php echo get_phrase("(A_private_community_means_that_access_is_not_open._Interested_people_will_need_to_send_a_request_for_access._The_administrator_can_accept_or_reject_it.)") ?>">
                            <i class="fa-solid fa-circle-info"></i>
                        </span>
                    </span>
                </label>




                <label><?php echo get_phrase("media") ?></label>
                <div class="media-upload-grid">
                    <div class="file-upload-saas" id="logo-upload-box">
                        <div class="upload-placeholder-saas"><i class="fas fa-camera"></i><span><?php echo get_phrase("logo_(1:1)") ?></span></div>
                        <div class="image-preview-saas" id="logo-preview"></div>
                        <input type="file" class="file-input-saas" id="logo-upload" name="school_image" accept="image/*">
                    </div>
                    <div class="file-upload-saas wide" id="cover-upload-box">
                        <div class="upload-placeholder-saas"><i class="fas fa-image"></i><span><?php echo get_phrase("cover_(16:5)") ?></span></div>
                        <div class="image-preview-saas" id="cover-preview"></div>
                        <input type="file" class="file-input-saas" id="cover-upload" name="communityCover" accept="image/*">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-saas btn-community" id="modal-continue-btn">
                        <?php echo get_phrase("Continue") ?>
                    </button>
                </div>
            </div>

            <!-- ÉTAPE 2 -->
            <div class="modal-body-community" id="modal-step-2" style="display:none;">
                <div class="subscription-plan">
                    <div class="plan-header">
                        <h4><?php echo get_phrase("creator_plan") ?></h4>
                        <div class="plan-price">
                            790 <span id="planCurrency">MAD</span>
                            <span>/ <?php echo get_phrase("month,_no_commitment") ?></span>
                        </div>
                    </div>
                    <div class="plan-body">
                        <p><?php echo get_phrase("access_all_the_features_to_publish_your_community:") ?></p>
                        <ul class="plan-features">
                            <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("online_course_hosting") ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("integrated_video_conferencing") ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("social_network") ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("discussion_space") ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("certification") ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("multi-class") ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("multi-coach_management") ?></li>
                        </ul>
                    </div>
                </div>

                <div class="trial-callout">
                    <i class="fas fa-calendar-check"></i>
                    <div class="trial-text">
                        <strong><?php echo get_phrase("enjoy_a_14-day_free_trial.") ?></strong>
                        <span><?php echo get_phrase("no_payment_will_be_required_before_the_end_of_your_trial.") ?></span>
                    </div>
                </div>

                <div class="modal-actions space-between">
                    <button type="button" class="btn-saas btn-community-2" id="modal-back-btn"><?php echo get_phrase("back") ?></button>
                    <button type="submit" class="btn-saas btn-community">
                        <?php echo get_phrase("start_the_14_day_trial") ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>