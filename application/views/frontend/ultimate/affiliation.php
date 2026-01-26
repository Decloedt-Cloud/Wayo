<div class="affiliation-container" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <!-- Hero Section -->
    <section class="text-center aff-section">
        <h1><span class="aff-accent"><?php echo get_phrase('Become a partner wayo'); ?></span> <?php echo get_phrase('and monetize your community'); ?>.</h1>
        <p class="aff-subtitle mb-5"><?php echo get_phrase('Offer 1 month of full access for 10 DH. Earn 100 DH per signup. Simple'); ?>.</p>
        <button class="aff-cta-primary" onclick="handleCTA()"><?php echo get_phrase('Become a Partner'); ?></button>
    </section>

    <!-- Steps Section -->
    <section class="aff-section">
        <h2 class="aff-section-title text-center"><?php echo get_phrase('How it works in 3 steps'); ?></h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="aff-step-item">
                    <div class="aff-step-number">1</div>
                    <h5><?php echo get_phrase('Share your link'); ?></h5>
                    <p><?php echo get_phrase('Instantly get your unique referral link'); ?>.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="aff-step-item">
                    <div class="aff-step-number">2</div>
                    <h5><?php echo get_phrase('Your community benefits'); ?></h5>
                    <p><?php echo get_phrase('They access the full platform for only 10 DH for the first month'); ?>.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="aff-step-item">
                    <div class="aff-step-number">3</div>
                    <h5><?php echo get_phrase('You earn 100 DH'); ?></h5>
                    <p><?php echo get_phrase('Commission validated after the first month of customer activity'); ?>.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Arguments Section -->
    <section class="aff-section">
        <h2 class="aff-section-title text-center"><?php echo get_phrase('Why it\'s easy to promote'); ?></h2>
        <div class="aff-argument-list">
            <div class="aff-argument-item">
                <div class="aff-check-icon"><i class="fas fa-check"></i></div>
                <div>
                    <h6><?php echo get_phrase('No entry barrier'); ?></h6>
                    <p><?php echo get_phrase('At 10 DH for the first month, there is no resistance. Your community can test without risk'); ?>.</p>
                </div>
            </div>
            <div class="aff-argument-item">
                <div class="aff-check-icon"><i class="fas fa-check"></i></div>
                <div>
                    <h6><?php echo get_phrase('High value-added product'); ?></h6>
                    <p><?php echo get_phrase('A complete SaaS that solves real problems. Your recommendations stay authentic'); ?>.</p>
                </div>
            </div>
            <div class="aff-argument-item">
                <div class="aff-check-icon"><i class="fas fa-check"></i></div>
                <div>
                    <h6><?php echo get_phrase('Fair and transparent compensation'); ?></h6>
                    <p><?php echo get_phrase('100 DH per qualified customer. No hidden fees, no complex conditions'); ?>.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Calculator Section -->
    <section class="aff-section">
        <h2 class="aff-section-title text-center"><?php echo get_phrase('Calculate your potential'); ?></h2>
        <div class="aff-calculator-card">
            <h5><?php echo get_phrase('Quick simulation'); ?></h5>
            <div class="aff-calc-label"><?php echo get_phrase('Estimated number of signups'); ?></div>
            <input type="number" id="signups" class="aff-calc-input" value="10" min="0" oninput="calculateCommission()">
            <div class="aff-calc-result" id="result">1 000 <span style="font-size: 0.6em; font-weight: 900; margin-left: 5px; vertical-align: middle;">DH</span></div>
            <div class="aff-calc-footer"><?php echo get_phrase('of potential commissions'); ?></div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="aff-section">
        <h2 class="aff-section-title text-center"><?php echo get_phrase('Frequently Asked Questions'); ?></h2>
        <div class="aff-faq-list">
            <div class="aff-faq-card">
                <h5><?php echo get_phrase('When am I paid?'); ?></h5>
                <p><?php echo get_phrase('Commissions are validated after the first month of customer activity to ensure quality. You receive your payments monthly'); ?>.</p>
            </div>
            <div class="aff-faq-card">
                <h5><?php echo get_phrase('Is there a limit?'); ?></h5>
                <p><?php echo get_phrase('An initial quota is set up to assess the quality of the traffic. Once your reliability is proven, the limits are lifted'); ?>.</p>
            </div>
        </div>
    </section>

    <!-- Footer CTA -->
    <hr class="aff-divider">
    <section class="aff-footer-cta aff-section">
        <h2><?php echo get_phrase('Ready to start?'); ?></h2>
        <button class="aff-cta-primary" onclick="handleCTA()"><?php echo get_phrase('Become a Partner'); ?></button>
    </section>
</div>

<script>
    function calculateCommission() {
        let signups = parseInt(document.getElementById('signups').value) || 0;
        if (signups < 0) {
            signups = 0;
            document.getElementById('signups').value = 0;
        }
        const total = (signups * 100).toLocaleString('en-US').replace(/,/g, ' ');
        document.getElementById('result').innerHTML = total + ' <span style="font-size: 0.6em; font-weight: 900; margin-left: 5px; vertical-align: middle;">DH</span>';
    }

    function handleCTA() {
        window.location.href = "<?php echo site_url('home/contact'); ?>";
    }

    calculateCommission();
</script>
