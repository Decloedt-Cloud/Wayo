<?php
    $student = $this->db->get_where('students', array('id' => $param1))->row_array();

    // Récup classes (inchangé côté logique)
    $enrols = $this->db
        ->select('class_id')
        ->get_where('enrols', array('student_id' => $param1))
        ->result_array();
?>
<div class="container pb-4">
    <div class="row g-4 align-items-start">

        <!-- Profil Étudiant -->
        <div class="col-lg-4">
            <div class="card border-0 shadow profile-card overflow-hidden">
                <!-- Bandeau -->
                <div class="profile-cover left-side-prf">
                    
                </div>
                <div class="card-body text-center pt-5">
                    <div class="avatar-wrap">
                        <img
                            class="avatar-img"
                            width="120" height="120"
                            src="<?php echo $this->user_model->get_user_image($student['user_id']); ?>"
                            alt="Profile Image">
                    </div>

                    <h5 class="mt-3 mb-1 fw-semibold">
                        <?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?>
                    </h5>

                    <div class="mb-3 small text-muted">
                        <?php echo get_phrase('student_code'); ?> :
                        <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold ms-1">
                            <?php echo $student['code']; ?>
                        </span>
                    </div>

                    <div class="text-start mt-3">
                        <div class="section-title text-center small text-uppercase fw-bold text-muted mb-2">
                            <?php echo get_phrase('class'); ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($enrols as $enrol):
                                $class_name = $this->db->get_where('classes', array('id' => $enrol['class_id']))->row('name'); ?>
                                <span class="chip"><?php echo $class_name; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="row g-2 text-center small">

                        <div class="col-12">
                            <div class="mini-stat">
                                <div class="label text-muted"><?php echo get_phrase('phone'); ?></div>
                                <div class="value">
                                    <?php echo $this->user_model->get_user_details($student['user_id'], 'phone'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /card -->
        </div>

        <!-- Détails & Onglets -->
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="profile-cover bg-white border-0">
                    <h6 class="mb-0 fw-bold Banner-title">
                        <i class="bi bi-person-lines-fill me-2"></i> 
                        <?php echo strtoupper(get_phrase('Profile')); ?>
                    </h6>
                </div>


                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <!-- Profil -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('name'); ?></div>
                                        <div class="info-value">
                                            <?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('email'); ?></div>
                                        <div class="info-value">
                                            <?php echo $this->user_model->get_user_details($student['user_id'], 'email'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('student_code'); ?></div>
                                        <div class="info-value">
                                            <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold">
                                                <?php echo $student['code']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title"><?php echo get_phrase('Address'); ?></div>
                                        <div class="info-value">
                                            <?php
                                                echo $this->user_model->get_user_details($student['user_id'], 'Rue')
                                                     .', '.$this->user_model->get_user_details($student['user_id'], 'Numero')
                                                     .', '.$this->user_model->get_user_details($student['user_id'], 'Ville')
                                                     .', '.$this->user_model->get_user_details($student['user_id'], 'Codepostal');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="info-block">
                                        <div class="info-title mb-2"><?php echo get_phrase('class'); ?></div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($enrols as $enrol):
                                                $class_name = $this->db->get_where('classes', array('id' => $enrol['class_id']))->row('name'); ?>
                                                <span class="chip chip-outline">• <?php echo $class_name; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /row -->
                        </div><!-- /tab -->
                    </div><!-- /tab-content -->
                </div><!-- /card-body -->
            </div><!-- /card -->
        </div>
    </div>
</div>

<!-- CSS personnalisé (design uniquement) -->
<style>
/* Général */
.card { border-radius: 16px; }
.shadow { box-shadow: 0 10px 24px rgba(20, 20, 43, 0.06) !important; }

/* Profil: couverture + avatar */
.profile-card { position: relative; }
.profile-cover {
    height: 86px;
    background: linear-gradient(135deg, #e8f0ff 0%, #f7f7ff 100%);
    display: flex;
    justify-content: left;
    align-items: center;
    padding-left: 3em;
}
.profile-cover.left-side-prf {
    height: 124px;
}

.Banner-title{
        font-size: 14px;
        color: #536de6;
    }
.avatar-wrap {
    margin-top: -124px;
}
.avatar-img {
    border-radius: 50%;
    border: 4px solid #fff;
    outline: 3px solid var(--bs-primary);
    object-fit: cover;
}

/* Chips (classes) */
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

/* Mini stats en pied de carte */
.mini-stat .label { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; }
.mini-stat .value { font-weight: 600; }

/* Nav pills douces */
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

/* Blocs info */
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

/* Aides */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
