<style>
    .custom-btn {
        background-color: #4F46E5 !important;
        border-radius: 8px !important;
    }
    .custom-card {
        border-radius: 20px !important;
        overflow: hidden;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="header-title"><?php echo get_phrase('access_denied'); ?></h4>
                        <p class="text-muted"><?php echo get_phrase('you_are_not_currently_enrolled_in_any_community'); ?></p>
                        <p class="text-muted"><?php echo get_phrase('please_join_a_community_to_access_this_page'); ?></p>
                        <a href="<?php echo site_url('student/dashboard'); ?>" class="btn btn-primary custom-btn"><?php echo get_phrase('go_to_dashboard'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
