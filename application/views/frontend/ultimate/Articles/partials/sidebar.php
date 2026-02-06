<aside class="blog-sidebar">
    <div class="sidebar-widget">
        <h3 class="widget-title"><?php echo get_phrase('search'); ?></h3>
        <form action="<?php echo site_url('trends/search'); ?>" class="search-form">
            <input type="text" name="q" placeholder="<?php echo get_phrase('search_trends'); ?>..." class="search-input">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="sidebar-widget">
        <h3 class="widget-title"><?php echo get_phrase('Recent_trends'); ?></h3>
        <!-- Ces données devraient idéalement venir du contrôleur -->
        <a href="#" class="recent-post-item">
            <picture>
                <source type="image/avif" srcset="<?php echo base_url('uploads/images/trends/optimized/clawdBot.avif'); ?>">
                <source type="image/webp" srcset="<?php echo base_url('uploads/images/trends/optimized/clawdBot.webp'); ?>">
                <img src="<?php echo base_url('uploads/images/trends/clawdBot.webp'); ?>" alt="<?php echo get_phrase('how_clawdbot_is_changing_digital_content'); ?>" class="recent-post-img" loading="lazy">
            </picture>
            <div>
                <h4 class="recent-post-title"><?php echo get_phrase('how_clawdbot_is_changing_digital_content'); ?></h4>
                <span class="meta-item"><?php echo date('d M, Y', strtotime($article['created_at'])); ?></span>
            </div>
        </a>
    </div>

    <!-- <div class="sidebar-widget">
        <h3 class="widget-title"><?php echo get_phrase('categories'); ?></h3>
        <ul class="list-unstyled">
            <li class="mb-2">
                <a href="<?php echo site_url('trends/category/actualites'); ?>" class="text-decoration-none text-dark d-flex justify-content-between hover-orange">
                    Actualités <span class="badge bg-light text-muted">5</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="<?php echo site_url('trends/category/conseils'); ?>" class="text-decoration-none text-dark d-flex justify-content-between hover-orange">
                    Conseils <span class="badge bg-light text-muted">12</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="<?php echo site_url('trends/category/tutoriels'); ?>" class="text-decoration-none text-dark d-flex justify-content-between hover-orange">
                    Tutoriels <span class="badge bg-light text-muted">8</span>
                </a>
            </li>
        </ul>
    </div> -->

    <!-- <div class="sidebar-widget">
        <h3 class="widget-title"><?php echo get_phrase('tags'); ?></h3>
        <div class="tag-cloud">
            <a href="#" class="tag-item">Academy</a>
            <a href="#" class="tag-item">E-learning</a>
            <a href="#" class="tag-item">LMS</a>
            <a href="#" class="tag-item">Community</a>
        </div>
    </div> -->
</aside>

<style>
    .hover-orange:hover {
        color: #F06423 !important;
    }
</style>
