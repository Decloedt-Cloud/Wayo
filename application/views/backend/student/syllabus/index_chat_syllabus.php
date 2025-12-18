<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h4 class="page-title d-inline-block">
                <i class="fas fa-wand-magic-sparkles fa-fw"></i> <?php echo get_phrase('chat_ai'); ?> - <?php echo $syllabus['title']; ?>
            </h4>
            <a href="<?php echo site_url('student/syllabus'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?php echo get_phrase('back_to_syllabus'); ?>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <div class="main-card">
                <div class="card-body">
                    <div class="card-body class_content">
                        <?php include 'chat_ai_syllabus.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
