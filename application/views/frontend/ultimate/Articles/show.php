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
                <img src="https://i.postimg.cc/W1GGVmqG/logo-icone-trans.png" alt="Author" class="author-avatar">
                <div>
                    <span class="author-name"><?php echo $article['author']; ?></span>
                    <span class="meta-item"><?php echo get_phrase('author'); ?></span>
                </div>
            </div>
            <div class="meta-item">
                <i class="far fa-calendar-alt"></i>
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
                    
                    <!-- <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                    
                    <h2>Pourquoi est-ce important ?</h2>
                    <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    
                    <blockquote>
                        "Wayo Academy change la façon dont nous apprenons et partageons nos connaissances au quotidien."
                    </blockquote>

                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p> -->
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
                    $share_title = urlencode($article['title']);
                    $share_description = urlencode($article['summary']);
                    
                    // Social share URLs
                    // Facebook with quote parameter to pre-fill the post
                    $facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($current_url) . '&quote=' . urlencode($article['title'] . ' - ' . $article['summary']);
                    
                    $twitter_share = 'https://twitter.com/intent/tweet?url=' . urlencode($current_url) . '&text=' . $share_title;
                    $linkedin_share = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($current_url);
                    $whatsapp_share = 'https://api.whatsapp.com/send?text=' . $share_title . '%20' . urlencode($current_url);
                    ?>
                    
                    <div class="share-btns d-flex align-items-center flex-wrap gap-2">
                        <span class="fw-bold" style="color: #666;"><?php echo get_phrase('share'); ?>:</span>
                        
                        <!-- Facebook Share -->
                        <a href="<?php echo $facebook_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center"
                           style="width: 40px; height: 40px; border-color: #1877f2; color: #1877f2;"
                           title="Share on Facebook"
                           onclick="window.open(this.href, 'facebook-share-dialog', 'width=626,height=436'); return false;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        
                        <!-- Twitter Share -->
                        <a href="<?php echo $twitter_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="btn btn-outline-info btn-sm rounded-circle d-flex align-items-center justify-content-center"
                           style="width: 40px; height: 40px; border-color: #1da1f2; color: #1da1f2;"
                           title="Share on Twitter"
                           onclick="window.open(this.href, 'twitter-share-dialog', 'width=626,height=436'); return false;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        
                        <!-- LinkedIn Share -->
                        <a href="<?php echo $linkedin_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center"
                           style="width: 40px; height: 40px; border-color: #0077b5; color: #0077b5;"
                           title="Share on LinkedIn"
                           onclick="window.open(this.href, 'linkedin-share-dialog', 'width=626,height=500'); return false;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        
                        <!-- WhatsApp Share -->
                        <a href="<?php echo $whatsapp_share; ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="btn btn-outline-success btn-sm rounded-circle d-flex align-items-center justify-content-center"
                           style="width: 40px; height: 40px; border-color: #25d366; color: #25d366;"
                           title="Share on WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        
                        <!-- Copy Link -->
                        <button onclick="copyToClipboard('<?php echo $current_url; ?>')"
                                class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;"
                                title="Copy link">
                            <i class="fas fa-link"></i>
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
