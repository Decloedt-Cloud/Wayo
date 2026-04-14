<style>
    .article-header {
        padding: 5rem 0;
        background: #fff;
    }
    .article-header .breadcrumb {
        margin-bottom: 2rem;
        background: none;
        padding: 0;
    }
    .article-header .breadcrumb-item a {
        color: #F06423;
        text-decoration: none;
        font-weight: 600;
    }
    .article-header h1 {
        font-family: 'Urbanist', sans-serif;
        font-weight: 800;
        font-size: 3rem;
        color: #1A1A1A;
        line-height: 1.2;
        margin-bottom: 2rem;
    }
    .article-main-meta {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding-bottom: 3rem;
        border-bottom: 1px solid #eee;
    }
    .author-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .author-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    .author-name {
        font-weight: 700;
        color: #1A1A1A;
        display: block;
    }
    .meta-item {
        color: #999;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .article-content-wrapper {
        padding: 4rem 0;
        background: #fafafa;
    }
    .featured-image-wrapper {
        margin-top: -4rem;
        margin-bottom: 4rem;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .featured-image {
        width: 100%;
        height: auto;
        display: block;
    }

    .article-body-content {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #333;
        background: #fff;
        padding: 4rem;
        border-radius: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03);
    }
    .article-body-content p {
        margin-bottom: 2rem;
    }
    .article-body-content h2 {
        font-family: 'Urbanist', sans-serif;
        font-weight: 800;
        margin: 3rem 0 1.5rem;
        color: #1A1A1A;
    }

    /* Sidebar Styles */
    .blog-sidebar {
        position: sticky;
        top: 100px;
    }
    .sidebar-widget {
        background: #fff;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.02);
    }
    .widget-title {
        font-family: 'Urbanist', sans-serif;
        font-weight: 700;
        font-size: 1.25rem;
        color: #1A1A1A;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #F06423;
        display: inline-block;
    }
    .search-form {
        position: relative;
    }
    .search-input {
        width: 100%;
        padding: 12px 20px;
        border: 1px solid #eee;
        border-radius: 12px;
        outline: none;
        transition: border-color 0.2s;
    }
    .search-input:focus {
        border-color: #F06423;
    }
    .search-btn {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #F06423;
    }

    .recent-post-item {
        display: flex;
        gap: 15px;
        margin-bottom: 1.5rem;
        text-decoration: none;
    }
    .recent-post-img {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
    }
    .recent-post-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1A1A1A;
        line-height: 1.4;
        transition: color 0.2s;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .recent-post-item:hover .recent-post-title {
        color: #F06423;
    }

    .tag-cloud {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .tag-item {
        padding: 6px 15px;
        background: #f8f9fa;
        color: #666;
        border-radius: 99px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    .tag-item:hover {
        background: #F06423;
        color: #fff;
    }

    @media (max-width: 991px) {
        .article-body-content {
            padding: 2rem;
        }
        .article-header h1 {
            font-size: 2.25rem;
        }
    }

    /* Social Share Buttons */
    .share-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        text-decoration: none;
        border: 2px solid;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .share-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .share-btn:hover::before {
        left: 100%;
    }
    .share-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Facebook */
    .share-btn--facebook {
        color: #1877f2;
        border-color: #1877f2;
    }
    .share-btn--facebook:hover {
        background: #1877f2;
        color: #fff;
        box-shadow: 0 6px 20px rgba(24, 119, 242, 0.4);
    }
    
    /* X (Twitter) */
    .share-btn--x {
        color: #14171a;
        border-color: #14171a;
    }
    .share-btn--x:hover {
        background: #14171a;
        color: #fff;
        box-shadow: 0 6px 20px rgba(20, 23, 26, 0.4);
    }
    
    /* LinkedIn */
    .share-btn--linkedin {
        color: #0a66c2;
        border-color: #0a66c2;
    }
    .share-btn--linkedin:hover {
        background: #0a66c2;
        color: #fff;
        box-shadow: 0 6px 20px rgba(10, 102, 194, 0.4);
    }
    
    /* WhatsApp */
    .share-btn--whatsapp {
        color: #25d366;
        border-color: #25d366;
    }
    .share-btn--whatsapp:hover {
        background: #25d366;
        color: #fff;
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
    }
    
    /* Copy Link */
    .share-btn--copy {
        color: #6c757d;
        border-color: #dee2e6;
    }
    .share-btn--copy:hover {
        background: linear-gradient(135deg, #F06423, #ff8a50);
        color: #fff;
        border-color: #F06423;
        box-shadow: 0 6px 20px rgba(240, 100, 35, 0.4);
    }
</style>

<div class="article-header">
    <div class="container">
        <!-- <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo site_url('trends'); ?>"><?php echo get_phrase('trends'); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $article['category']; ?></li>
            </ol>
        </nav> -->
        <h1><?php echo $article['title']; ?></h1>
        
        <div class="article-main-meta">
            <div class="author-info">
                <img alt="Author" class="author-avatar" src="<?php echo $logo_light; ?>" alt="<?php echo $system_name; ?>">
                <div>
                    <span class="author-name"><?php echo $article['author']; ?></span>
                    <span class="meta-item"><?php echo get_phrase('author'); ?></span>
                </div>
            </div>
            <div class="meta-item">
                <i class="fa-regular fa-calendar-days"></i>
                <?php echo date('d M, Y', strtotime($article['created_at'])); ?>
            </div>
            <!-- <div class="meta-item">
                <i class="far fa-clock"></i>
                5 min read
            </div> -->
        </div>
    </div>
</div>

<div class="article-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="featured-image-wrapper">
                    <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>" class="featured-image">
                </div>

                <div class="article-body-content">
                    <?php echo $article['content']; ?>

                </div>

                <div class="mt-5 d-flex justify-content-between align-items-center">
                    <!-- <div class="tag-cloud">
                        <?php 
                        $tags = explode(',', $article['tags']);
                        foreach($tags as $tag): 
                        ?>
                            <a href="<?php echo site_url('blog/tag/'.trim($tag)); ?>" class="tag-item">#<?php echo trim($tag); ?></a>
                        <?php endforeach; ?>
                    </div> -->
                    
                    <?php 
                    // Prepare share data
                    $current_url = current_url();
                    $share_title = $article['title'];
                    $share_text = $article['title'] . ' - ' . (isset($article['summary']) ? $article['summary'] : '');
                    
                    // Social share URLs
                    $facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($current_url);
                    $twitter_share = 'https://x.com/intent/post?url=' . urlencode($current_url) . '&text=' . urlencode($share_title);
                    $linkedin_share = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($current_url);
                    $whatsapp_share = 'https://api.whatsapp.com/send?text=' . urlencode($share_text . ' ' . $current_url);
                    ?>
                    
                    <div class="share-btns d-flex align-items-center flex-wrap gap-3">
                        <span class="fw-bold" style="color: #888;"><?php echo get_phrase('share'); ?>:</span>
                        
                        <!-- Facebook Share -->
                        <a href="<?php echo $facebook_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="share-btn share-btn--facebook"
                           title="<?php echo get_phrase('share_on_facebook'); ?>"
                           onclick="window.open(this.href, 'facebook-share-dialog', 'width=626,height=436'); return false;">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        
                        <!-- X (Twitter) Share -->
                        <a href="<?php echo $twitter_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="share-btn share-btn--x"
                           title="<?php echo get_phrase('share_on_x'); ?>"
                           onclick="window.open(this.href, 'twitter-share-dialog', 'width=626,height=436'); return false;">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        
                        <!-- LinkedIn Share -->
                        <a href="<?php echo $linkedin_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="share-btn share-btn--linkedin"
                           title="<?php echo get_phrase('share_on_linkedin'); ?>"
                           onclick="window.open(this.href, 'linkedin-share-dialog', 'width=626,height=500'); return false;">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        
                        <!-- WhatsApp Share -->
                        <a href="<?php echo $whatsapp_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="share-btn share-btn--whatsapp"
                           title="<?php echo get_phrase('share_on_whatsapp'); ?>">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        
                        <!-- Copy Link -->
                        <button onclick="copyToClipboard('<?php echo $current_url; ?>')"
                                class="share-btn share-btn--copy"
                                title="<?php echo get_phrase('copy_link'); ?>">
                            <i class="fa-solid fa-link"></i>
                        </button>
                    </div>
                    
                    <script>
                    function copyToClipboard(text) {
                        navigator.clipboard.writeText(text).then(function() {
                            // Show success message with better styling
                            const message = document.createElement('div');
                            message.textContent = '<?php echo get_phrase("link_copied"); ?> ✓';
                            message.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 15px 25px; border-radius: 10px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999;';
                            document.body.appendChild(message);
                            setTimeout(() => message.remove(), 3000);
                        }, function(err) {
                            console.error('Could not copy text: ', err);
                            alert('<?php echo get_phrase("error_copying_link"); ?>');
                        });
                    }
                    </script>
                </div>
            </div>

            <div class="col-lg-4">
                <?php include 'partials/sidebar.php'; ?>
            </div>
        </div>
    </div>
</div>
