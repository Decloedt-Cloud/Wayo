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
            <img src="<?php echo base_url('uploads/images/trends/clawdBot.webp'); ?>" alt="" class="recent-post-img">
            <div>
                <h4 class="recent-post-title"><?php echo get_phrase('how_clawdbot_is_changing_digital_content'); ?></h4>
                <span class="meta-item">22 Jan, 2025</span>
            </div>
        </a>
        <!-- <a href="#" class="recent-post-item">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=150&q=80" alt="" class="recent-post-img">
            <div>
                <h4 class="recent-post-title">Comment créer une communauté engagée</h4>
                <span class="meta-item">21 Jan, 2025</span>
            </div>
        </a> -->
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
