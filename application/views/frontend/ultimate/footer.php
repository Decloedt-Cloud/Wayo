
<!-- ========== FOOTER ========== -->
<?php 
$current1 = $this->uri->segment(1);   // "home"
$current2 = $this->uri->segment(2);   // communities, tutorial, contact, etc.
?>
 <footer class="site-footer" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <div class="container footer-top">
      <a href="#" class="footer-logo"><img class="logo-img footer" src="https://i.postimg.cc/W1GGVmqG/logo-icone-trans.png" alt="Wayo"/></a>
      <nav class="footer-nav" aria-label="Liens pied de page">
        <ul>
            <li>
                <a href="<?= base_url('home'); ?>"
                  class="<?= ($current1 == 'home' && empty($current2)) ? 'active' : '' ?>">
                  <?php echo get_phrase('Home');?>
                  
                </a>
            </li>

            <li>
                <a href="<?= site_url('home/communities'); ?>"
                  class="<?= ($current2 == 'communities') ? 'active' : '' ?>">
                  <?php echo get_phrase('Communities');?>
                </a>
            </li>

            <li>
                <a href="<?= site_url('home/tutorial'); ?>"
                  class="<?= ($current2 == 'tutorial') ? 'active' : '' ?>"> 
                  <?php echo get_phrase('How it Works');?>
                </a>
            </li>

            <li>
                <a href="<?= site_url('home/contact'); ?>"
                  class="<?= ($current2 == 'contact') ? 'active' : '' ?>">
                  
                  <?php echo get_phrase('Contact');?>
                </a>
            </li>
      </ul>
      </nav>
      <div class="footer-social d-flex justify-content-center gap-2">
        <a href="https://www.facebook.com/people/Wayo-Academy/61572524656807/" target="_blank" class="social-btn">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
        <a href="https://www.instagram.com/wayo_academy/" target="_blank" class="social-btn">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="https://www.linkedin.com/company/wayoacademy/" target="_blank" class="social-btn">
            <i class="fa-brands fa-linkedin-in"></i>
        </a>
        <a href="https://www.youtube.com/@Wayo-ma" target="_blank" class="social-btn">
            <i class="fa-brands fa-youtube"></i>
        </a>
      </div>
    </div>
    <div class="container footer-bottom">
      <div class="footer-contact">
        <p>
          <strong><?php echo get_phrase('Contact'); ?></strong>
          <div class="infoContact">
                <a href="tel:+971501548923">+971 50 154 8923</a>
                <a href="mailto:info@wayo.cloud">info@wayo.cloud</a>
                <a href="https://maps.google.com/?q=R320+Um+Hurair+2,+Dubai,+UAE" target="_blank">
                    R320 Um Hurair 2, Dubai, EAU
                </a>
          </div>
        </p>
      </div>
      <div class="footer-links">
        <p><strong><?php echo get_phrase('Useful Links'); ?></strong></p>
          <div class="infolinks">
              <a href="<?php echo site_url('home/faq'); ?>"><?php echo get_phrase('FAQ'); ?></a>
              <a href="<?php echo site_url('home/terms_conditions'); ?>"><?php echo get_phrase('CGU'); ?></a>
              <a href="<?php echo site_url('home/privacy_policy'); ?>"><?php echo get_phrase('Privacy Policy'); ?></a>
              <a href="<?php echo site_url('home/contact'); ?>"><?php echo get_phrase('Support');?></a>
          </div>
      </div>

      <div class="footer-newsletter">
        <p> <strong><?php echo get_phrase('Newsletter'); ?></strong></p>
       
        <form action="#" method="post" class="newsletter-form">
          <input type="email" name="email" placeholder="<?php echo get_phrase('Your email');?>" required/>
          <button type="submit" class="btn accent"><?php echo get_phrase('Subscribe');?></button>
        </form>
      </div>
    </div>
    <div class="container footer-credits">
      <p><?php echo get_phrase('© 2025 Wayo Academy. All rights reserved.');?></p>
      <p><?php echo get_phrase('Developed by the Wayo team')?></p>
    </div>
  </footer>
<!-- Go to Top -->
<a class="js-go-to u-go-to" href="#" data-position='{"bottom": 25, "right": 15 }' data-type="fixed"
  data-offset-top="400" data-compensation="#header" data-show-effect="slideInUp" data-hide-effect="slideOutDown">
  <span class="fas fa-arrow-up u-go-to__inner"></span>
</a>
<!-- End Go to Top -->