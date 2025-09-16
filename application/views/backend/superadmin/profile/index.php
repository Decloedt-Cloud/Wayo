<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive-profile.css">
<!-- start page title -->
<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body">
            <h4 class="page-title d-inline-block"><i class="mdi mdi-account-cog title_icon"></i><?php echo get_phrase('manage_profile'); ?></h4>
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end page title -->

<div class="mb-3">
    <div class="main-card">
        <div id="profile_content" class="card-body">
            <?php include 'edit.php'; ?>
        </div>
    </div>
</div>

<script>
    function updateProfileInfo() {
        $(".profileAjaxForm").validate({});
        $(".profileAjaxForm").submit(function(e) {
            var form = $(this);
            ajaxSubmit(e, form, reload);
        });
    }

    function changePassword() {
        $(".changePasswordAjaxForm").validate({});
        $(".changePasswordAjaxForm").submit(function(e) {
            var form = $(this);
            ajaxSubmit(e, form, reload);
        });
    }

    function reload() {
        setTimeout(
            function() {
                location.reload();
            }, 1000);
    }
</script>