<style>
    .footer-info {
    display: flex;
    gap: 10px;
}
body[dir="rtl"] .footer-info {
    flex-direction: row-reverse;
}
@media (max-width: 768px) {
    .row1 {
        position: relative;
        top: 12px;
    }
    body[dir="rtl"] .footer{
       bottom: -4px;
}
}
</style>
<!-- Footer Start -->
<footer class="footer">
    <div class="container-fluid">
        <div class="row1">
           <div class="col-md-6">
                ©  <a href="<?php echo get_settings('footer_link'); ?>" target="_blank"><?php echo get_phrase('By Wayo Academy'); ?> V.1.0.0</a>
            </div>
        </div>
    </div>
</footer>
<!-- end Footer -->
