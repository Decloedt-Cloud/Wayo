<?php
    // Fetch teacher data based on the provided teacher ID
    $teacher = $this->db->get_where('teachers', array('id' => $param1))->row_array();
    if (empty($teacher)) {
        // Handle case where teacher is not found
        include APPPATH . 'views/backend/empty.php';
        return;
    }
?>
<div class="container py-4">
    <div class="row g-4 align-items-start">

        <!-- Teacher Profile -->
        <div class="col-lg-4">
            <div class="card border-0 shadow profile-card overflow-hidden">
                <!-- Banner -->
                <div class="profile-cover"></div>

                <div class="card-body text-center pt-5">
                    <div class="avatar-wrap">
                        <img
                            class="avatar-img"
                            width="120" height="120"
                            src="<?php echo $this->user_model->get_user_image($teacher['user_id']); ?>"
                            alt="Profile Image">
                    </div>

                    <h5 class="mt-3 mb-1 fw-semibold">
                        <?php echo $this->user_model->get_user_details($teacher['user_id'], 'name'); ?>
                    </h5>

                    <div class="mb-3 small text-muted">
                        <?php echo get_phrase('designation'); ?> :
                        <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold ms-1">
                            <?php echo $teacher['designation']; ?>
                        </span>
                    </div>
                    <!-- Commenter "Visibility" Temporarely -->

                    <!-- <div class="text-start mt-3">
                        <div class="section-title text-center small text-uppercase fw-bold text-muted mb-2">
                            <?php echo get_phrase('visibility'); ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="chip">
                                <?php echo $teacher['show_on_website'] ? get_phrase('visible_on_website') : get_phrase('not_visible_on_website'); ?>
                            </span>
                        </div>
                    </div> -->
                    
                    <!--------------------------------------->

                </div>

                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="row g-2 text-center small">
                        <div class="col-12">
                            <div class="mini-stat">
                                <div class="label text-muted"><?php echo get_phrase('phone'); ?></div>
                                <div class="value">
                                    <?php echo $this->user_model->get_user_details($teacher['user_id'], 'phone'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /card -->
        </div>

        <!-- Details & Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="profile-cover bg-white border-0">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-person-lines-fill me-2"></i> 
                        <?php echo get_phrase('profile'); ?>
                    </h6>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <!-- Profile -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('name'); ?></div>
                                        <div class="info-value">
                                            <?php echo $this->user_model->get_user_details($teacher['user_id'], 'name'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('email'); ?></div>
                                        <div class="info-value">
                                            <?php echo $this->user_model->get_user_details($teacher['user_id'], 'email'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('designation'); ?></div>
                                        <div class="info-value">
                                            <?php echo $teacher['designation']; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('Address'); ?></div>
                                        <div class="info-value">
                                            <?php
                                                echo $this->user_model->get_user_details($teacher['user_id'], 'Rue')
                                                     .', '.$this->user_model->get_user_details($teacher['user_id'], 'Numero')
                                                     .', '.$this->user_model->get_user_details($teacher['user_id'], 'Ville')
                                                     .', '.$this->user_model->get_user_details($teacher['user_id'], 'Codepostal');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('about'); ?></div>
                                        <div class="info-value">
                                            <?php echo $teacher['about'] ? htmlspecialchars($teacher['about']) : get_phrase('no_information_provided'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title mb-2"><?php echo get_phrase('social_links'); ?></div>
                                        <div class="info-value">
                                            <?php if ($teacher['social_links']): ?>
                                                <?php 
                                                    // Assuming social_links is stored as JSON or a serialized format
                                                    $social_links = json_decode($teacher['social_links'], true);
                                                    if (is_array($social_links) && !empty($social_links)): ?>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php foreach ($social_links as $platform => $url): ?>
                                                                <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="chip chip-outline">
                                                                    <?php echo ucfirst($platform); ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <?php echo get_phrase('no_social_links'); ?>
                                                    <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo get_phrase('no_social_links'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>


                                <!-- Commenter "website_visibility" Temporarely -->

                                <!-- <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('website_visibility'); ?></div>
                                        <div class="info-value">
                                            <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold">
                                                <?php echo $teacher['show_on_website'] ? get_phrase('visible') : get_phrase('not_visible'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div> -->

                                <!------------end-------------------------------------->
                                 
                            </div><!-- /row -->
                        </div><!-- /tab -->
                    </div><!-- /tab-content -->
                </div><!-- /card-body -->
            </div><!-- /card -->
        </div>
    </div>
</div>

<!-- Custom CSS (unchanged from original) -->
<style>
/* General */
.card { border-radius: 16px; }
.shadow { box-shadow: 0 10px 24px rgba(20, 20, 43, 0.06) !important; }

/* Profile: cover + avatar */
.profile-card { position: relative; }
.profile-cover {
    height: 86px;
    background: linear-gradient(135deg, #e8f0ff 0%, #f7f7ff 100%);
}
.avatar-wrap {
    margin-top: -60px;
}
.avatar-img {
    border-radius: 50%;
    border: 4px solid #fff;
    outline: 3px solid var(--bs-primary);
    object-fit: cover;
}

/* Chips (social links, visibility) */
.chip {
    display: inline-block;
    padding: .35rem .65rem;
    border-radius: 999px;
    background: #f5f7fb;
    border: 1px solid #eef1f7;
    font-size: .8125rem;
    line-height: 1;
}
.chip-outline {
    background: transparent;
    border-color: #dfe6f3;
}

/* Mini stats in card footer */
.mini-stat .label { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; }
.mini-stat .value { font-weight: 600; }

/* Soft pills */
.soft-pills .nav-link {
    border-radius: 10px;
    background: #f6f8fb;
    color: #4d5a75;
    margin-right: .5rem;
}
.soft-pills .nav-link.active {
    background: var(--bs-primary);
    color: #fff;
    box-shadow: 0 6px 14px rgba(35, 99, 255, .2);
}

/* Info blocks */
.info-block {
    padding: 1rem 1.125rem;
    border: 1px solid #eef1f7;
    border-radius: 12px;
    background: #ffffffcc;
    backdrop-filter: blur(6px);
}
.info-title {
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #6b7280;
    margin-bottom: .25rem;
}
.info-value {
    font-size: 1rem;
    color: #0f172a;
}

/* Helpers */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>