<?php
    // Fetch class data based on the provided class ID
    $class = db()->table('classes')->where('id', $param1)->get()->getRowArray();
    if (empty($class)) {
        // Handle case where class is not found
        include APPPATH . 'Views/backend/empty.php';
        return;
    }
     // Fetch currency
    $currencies = db()->table('settings_school')->where('school_id', school_id())->get()->getRowArray()['system_currency'] ?? '';
?>
<div class="container pb-4">
    <div class="row g-4 align-items-stretch">

        <!-- class Profile -->
        <div class="col-lg-4">
            <div class="card border-0 shadow profile-card overflow-hidden h-100">
                <!-- Banner -->
                <div class="profile-cover">
                    <h6 class="mb-0 fw-bold Banner-title">
                        <?php echo strtoupper(get_phrase('Class')); ?>
                    </h6>
                </div>

                <div class="card-body text-center pt-5">
                    <div class="avatar-wrap">
                        <div class="avatar-wrap">
                            <?php if ($class['photo'] == '' || $class['photo'] == null): ?>
                                <img
                                    class="avatar-img"
                                    width="120" height="120"
                                    src="<?php echo base_url() . 'uploads/class/placeholder.png'; ?>"
                                    alt="Class Image">
                            <?php else: ?>
                                <img
                                    class="avatar-img"
                                    width="120" height="120"
                                    src="<?php echo base_url() . 'uploads/class/' . $class['photo']; ?>"
                                    alt="Class Image">
                            <?php endif; ?>
                        </div>
                    </div>

                    <h5 class="mt-3 mb-1 fw-semibold">
                        <?php echo $class['name']; ?>
                    </h5>

                    <!-- <div class="mb-3 small text-muted">
                        <?php echo get_phrase('designation'); ?> :
                        <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold ms-1">
                            <?php echo $class['designation']; ?>
                        </span>
                    </div> -->

                    <!-- <div class="text-start mt-3">
                        <div class="section-title text-center small text-uppercase fw-bold text-muted mb-2">
                            <?php echo get_phrase('visibility'); ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="chip">
                                <?php echo $class['show_on_website'] ? get_phrase('visible_on_website') : get_phrase('not_visible_on_website'); ?>
                            </span>
                        </div>
                    </div> -->
                </div>

                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="row g-2 text-center small">
                        <div class="col-12">
                            <div class="mini-stat">
                                <div class="label text-muted"><?php echo get_phrase('status'); ?></div>
                                <div class="value">
                                    <?php if ($class['statut'] == 'active'): ?>
                                        <span class="badge rounded-pill bg-success"><?php echo get_phrase('active'); ?></span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-danger"><?php echo get_phrase('inactive'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 text-center small pt-2">
                            <div class="col-12">
                                <div class="mini-stat">
                                    <div class="label text-muted"><?php echo get_phrase('maximum_members'); ?></div>
                                    <div class="value">
                                        <?php echo !empty($class['nombre_max_membre']) ? $class['nombre_max_membre'] : get_phrase('not_specified'); ?>
                                    </div>
                                </div>
                            </div>
                    </div>
                    
                </div>

                <div class="card-footer bg-transparent border-0 pt-0">
                    <!-- <div class="row g-2 text-center small">
                        <div class="col-12">
                            <div class="mini-stat">
                                <div class="label text-muted"><?php echo get_phrase('phone'); ?></div>
                                <div class="value">
                                    <?php echo $this->user_model->get_user_details($class['user_id'], 'phone'); ?>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div><!-- /card -->
        </div>

        <!-- Details & Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow profile-card overflow-hidden h-100">
                <div class="profile-cover bg-white border-0">
                    
                    <h6 class="mb-0 fw-bold Banner-title">
                        <?php echo strtoupper(get_phrase('Details')); ?>
                    </h6>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <!-- Profile -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('class name'); ?></div>
                                        <div class="info-value">
                                            <?php echo $class['name']; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('price'); ?></div>
                                        <div class="info-value">
                                            <?php echo $class['price'].' '.$currencies; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-md-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('designation'); ?></div>
                                        <div class="info-value">
                                            <?php echo $class['designation']; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('Address'); ?></div>
                                        <div class="info-value">
                                            <?php
                                                echo $this->user_model->get_user_details($class['user_id'], 'Rue')
                                                     .', '.$this->user_model->get_user_details($class['user_id'], 'Numero')
                                                     .', '.$this->user_model->get_user_details($class['user_id'], 'Ville')
                                                     .', '.$this->user_model->get_user_details($class['user_id'], 'Codepostal');
                                            ?>
                                        </div>
                                    </div>
                                </div> -->

                                <?php if (!empty($class['date_debut']) && $class['date_debut'] != '0000-00-00'): ?>
                                    <div class="col-12">
                                        <div class="info-block">
                                            <div class="info-title"><?php echo get_phrase('start_date'); ?></div>
                                            <div class="info-value">
                                                <?php echo date('d/m/Y', strtotime($class['date_debut'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if(!empty($class['date_fin'] && $class['date_fin'] != '0000-00-00')): ?>
                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('end_date'); ?></div>
                                        <div class="info-value">
                                            <?php echo date('d/m/Y', strtotime($class['date_fin'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Social Links -->

                                <!-- <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title mb-2"><?php echo get_phrase('social_links'); ?></div>
                                        <div class="info-value">
                                            <?php if ($class['social_links']): ?>
                                                <?php 
                                                    // Assuming social_links is stored as JSON or a serialized format
                                                    $social_links = json_decode($class['social_links'], true);
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
                                </div> -->

                                <!-- <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('website_visibility'); ?></div>
                                        <div class="info-value">
                                            <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold">
                                                <?php echo $class['show_on_website'] ? get_phrase('visible') : get_phrase('not_visible'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div> -->

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
.Banner-title{
        font-size: 14px;
        color: #536de6;
    }
.card { border-radius: 16px; }
.shadow { box-shadow: 0 10px 24px rgba(20, 20, 43, 0.06) !important; }

/* Profile: cover + avatar */
.profile-card { position: relative; }
.profile-cover {
    height: 86px;
    background: linear-gradient(135deg, #e8f0ff 0%, #f7f7ff 100%);
    display: flex;
    justify-content: center;
    align-items: center;
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

@media (min-width: 992px) {
    .modal-lg {
        max-width: 900px !important;
    }
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