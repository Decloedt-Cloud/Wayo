<?php
// Fetch class data based on the provided class ID
$class = $this->db->get_where('classes', array('id' => $param1))->row_array();
if (empty($class)) {
    // Handle case where class is not found
    include APPPATH . 'views/backend/empty.php';
    return;
}
// Fetch currency
$currencies = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('system_currency');
?>

<style>
/* ============================================================================
   CLASS READ VIEW - PREMIUM DESIGN
   ============================================================================ */

:root {
    --read-primary: #6366f1;
    --read-primary-rgb: 99, 102, 241;
    --read-success: #10b981;
    --read-danger: #ef4444;
    --read-warning: #f59e0b;
    --read-dark: #1e293b;
    --read-gray: #64748b;
    --read-light: #f8fafc;
    --read-border: #e2e8f0;
    --read-white: #ffffff;
}

/* Header Card */
.read-header {
    background: linear-gradient(135deg, var(--read-primary), #8b5cf6);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(var(--read-primary-rgb), 0.3);
}

.read-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.read-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.read-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.read-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.read-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.read-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.read-btn-primary {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: white;
}

.read-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 41, 59, 0.4);
    color: white;
}

/* Profile Cards */
.profile-card {
    background: var(--read-white);
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--read-border);
    overflow: hidden;
    height: 100%;
}

.profile-cover {
    height: 86px;
    background: linear-gradient(135deg, #e8f0ff 0%, #f7f7ff 100%);
    display: flex;
    justify-content: center;
    align-items: center;
}

.Banner-title {
    font-size: 14px;
    color: #536de6;
}

/* Avatar */
.avatar-wrap {
    margin-top: -60px;
}

.avatar-img {
    border-radius: 50%;
    border: 4px solid white;
    outline: 3px solid var(--read-primary);
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Status Badges */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8125rem;
}

/* Info Blocks */
.info-block {
    padding: 1rem 1.125rem;
    border: 1px solid var(--read-border);
    border-radius: 12px;
    background: #ffffffcc;
    backdrop-filter: blur(6px);
    margin-bottom: 1rem;
}

.info-title {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--read-gray);
    margin-bottom: .25rem;
    font-weight: 600;
}

.info-value {
    font-size: 1rem;
    color: var(--read-dark);
    font-weight: 500;
}

/* Price Display */
.price-free {
    color: var(--read-success);
    font-weight: 700;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    padding: 0.5rem 1rem;
    border-radius: 10px;
    display: inline-block;
}

.price-paid {
    color: var(--read-success);
    font-weight: 700;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    padding: 0.5rem 1rem;
    border-radius: 10px;
    display: inline-block;
}

/* Responsive */
@media (max-width: 768px) {
    .read-header {
        flex-direction: column;
        text-align: center;
    }

    .profile-card {
        margin-bottom: 2rem;
    }
}
</style>

<!-- Header -->
<div class="read-header">
    <div class="read-header-left">
        <div class="read-header-icon">
            <i class="fas fa-eye"></i>
        </div>
        <div class="read-header-text">
            <h4><?php echo get_phrase('class_details'); ?></h4>
            <p><?php echo get_phrase('view_complete_class_information'); ?></p>
        </div>
    </div>
    <div class="read-header-actions">
        <button type="button" class="read-btn read-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/class/edit/'.$class['id'])?>', '<?php echo get_phrase('update_class'); ?>')">
            <i class="mdi mdi-pencil-outline"></i> <?php echo get_phrase('edit'); ?>
        </button>
    </div>
</div>

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

                    <h5 class="mt-4 fw-semibold lh-sm">
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
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title">
                                            <i class="mdi mdi-account-outline" style="color: var(--read-primary); margin-right: 0.25rem;"></i>
                                            <?php echo get_phrase('class_name'); ?>
                                        </div>
                                        <div class="info-value">
                                            <strong><?php echo $class['name']; ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="info-block">
                                        <div class="info-title">
                                            <i class="mdi mdi-cash-multiple" style="color: var(--read-success); margin-right: 0.25rem;"></i>
                                            <?php echo get_phrase('price'); ?>
                                        </div>
                                        <div class="info-value">
                                            <?php if ($class['price'] == 0): ?>
                                                <span class="price-free">
                                                    <i class="mdi mdi-gift-outline" style="margin-right: 0.25rem;"></i>
                                                    <?php echo get_phrase('free'); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="price-paid">
                                                    <?php echo number_format($class['price'], 2) . ' ' . $currencies; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
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
                                                . ', ' . $this->user_model->get_user_details($class['user_id'], 'Numero')
                                                . ', ' . $this->user_model->get_user_details($class['user_id'], 'Ville')
                                                . ', ' . $this->user_model->get_user_details($class['user_id'], 'Codepostal');
                                            ?>
                                        </div>
                                    </div>
                                </div> -->


                                <?php if (!empty($class['date_debut']) && $class['date_debut'] != '0000-00-00'): ?>
                                    <div class="col-12">
                                        <div class="info-block">
                                            <div class="info-title">
                                                <i class="mdi mdi-calendar-start" style="color: var(--read-primary); margin-right: 0.25rem;"></i>
                                                <?php echo get_phrase('start_date'); ?>
                                            </div>
                                            <div class="info-value">
                                                <i class="mdi mdi-calendar" style="color: var(--read-gray); margin-right: 0.25rem;"></i>
                                                <?php echo date('d/m/Y', strtotime($class['date_debut'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if(!empty($class['date_fin']) && $class['date_fin'] != '0000-00-00'): ?>
                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title">
                                            <i class="mdi mdi-calendar-end" style="color: var(--read-primary); margin-right: 0.25rem;"></i>
                                            <?php echo get_phrase('end_date'); ?>
                                        </div>
                                        <div class="info-value">
                                            <i class="mdi mdi-calendar" style="color: var(--read-gray); margin-right: 0.25rem;"></i>
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
    .card {
        border-radius: 16px;
    }

    .shadow {
        box-shadow: 0 10px 24px rgba(20, 20, 43, 0.06) !important;
    }

    /* Profile: cover + avatar */
    .profile-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        
    }
    .profile-card .card-body{
        flex-grow: 0; /* Don't grow, stay compact */
        padding-bottom: 0;
    }
    .profile-card .card-footer{
         margin-top: auto; /* Push footer to bottom if needed, or remove this line */
    }

    .profile-cover {
        height: 86px;
        background: linear-gradient(135deg, #e8f0ff 0%, #f7f7ff 100%);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    @media (min-width: 992px) {
    .modal-lg {
        max-width: 900px !important;
    }
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
    .mini-stat .label {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        line-height: 1.6;
        margin-bottom: 0.25rem;
    }

    .mini-stat .value {
        font-weight: 600;
        font-size: 1rem; /* 16px */
        line-height: 1.8;
    }

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