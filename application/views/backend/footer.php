<style>
    .footer-info {
    display: flex;
    gap: 10px;
}
body[dir="rtl"] .footer-info {
    flex-direction: row-reverse;
}
</style>
<!-- Footer Start -->
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
           <div class="col-md-6">
                ©  <a href="<?php echo get_settings('footer_link'); ?>" target="_blank"><?php echo get_phrase('By Wayo Academy'); ?> V.1.0.0</a>
            </div>
        </div>
    </div>
</footer>
<!-- end Footer -->
