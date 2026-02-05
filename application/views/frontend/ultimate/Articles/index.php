<style>
    .blog-hero {
        background: linear-gradient(135deg, #FFF6F0 0%, #FFFFFF 100%);
        padding: 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .blog-hero h1 {
        font-family: 'Urbanist', sans-serif;
        font-weight: 800;
        font-size: 3.5rem;
        color: #1A1A1A;
        margin-bottom: 1.5rem;
    }
    .blog-hero p {
        font-size: 1.25rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }
    .blog-hero::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(240, 100, 35, 0.05) 0%, transparent 70%);
        top: -150px;
        right: -150px;
        border-radius: 50%;
    }

    .article-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #fff;
        height: 100%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .article-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(240, 100, 35, 0.1);
    }
    .article-img-wrapper {
        position: relative;
        height: 240px;
        overflow: hidden;
    }
    .article-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .article-card:hover .article-img {
        transform: scale(1.1);
    }
    .article-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        padding: 6px 15px;
        border-radius: 99px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #F06423;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .article-body {
        padding: 2rem;
    }
    .article-meta {
        font-size: 0.85rem;
        color: #999;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .article-title {
        font-family: 'Urbanist', sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        color: #1A1A1A;
        line-height: 1.3;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-decoration: none;
        transition: color 0.2s;
    }
    .article-title:hover {
        color: #F06423;
    }
    .article-excerpt {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .read-more {
        color: #F06423;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: gap 0.2s;
    }
    .read-more:hover {
        gap: 12px;
    }

    .blog-container {
        padding: 5rem 0;
        background: #fafafa;
    }

    .category-filter {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 4rem;
        flex-wrap: wrap;
    }
    .cat-btn {
        padding: 10px 25px;
        border-radius: 99px;
        border: 1px solid #eee;
        background: #fff;
        color: #666;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
    }
    .cat-btn:hover, .cat-btn.active {
        background: #F06423;
        color: #fff;
        border-color: #F06423;
        box-shadow: 0 5px 15px rgba(240, 100, 35, 0.3);
    }

    [dir="rtl"] .article-badge {
        left: auto;
        right: 20px;
    }
    [dir="rtl"] .read-more i {
        transform: rotate(180deg);
    }
</style>

<section class="blog-hero">
    <div class="container">
        <h1 class="animate__animated animate__fadeInDown"><?php echo get_phrase('our_trends'); ?></h1>
        <p class="animate__animated animate__fadeInUp animate__delay-1s">
            <?php echo get_phrase('Discover the key trends of the moment.'); ?>
        </p>
    </div>
</section>

<section class="blog-container">
    <div class="container">
        <!-- Category Filter -->
        <!-- <div class="category-filter animate__animated animate__fadeIn">
            <a href="<?php echo site_url('trends'); ?>" class="cat-btn <?php echo !isset($selected_category) ? 'active' : ''; ?>">
                <?php echo get_phrase('all'); ?>
            </a>
            <a href="<?php echo site_url('trends/category/actualites'); ?>" class="cat-btn <?php echo (isset($selected_category) && $selected_category == 'actualites') ? 'active' : ''; ?>">
                <?php echo get_phrase('news'); ?>
            </a>
            <a href="<?php echo site_url('trends/category/conseils'); ?>" class="cat-btn <?php echo (isset($selected_category) && $selected_category == 'conseils') ? 'active' : ''; ?>">
                <?php echo get_phrase('tips'); ?>
            </a>
            <a href="<?php echo site_url('trends/category/tutoriels'); ?>" class="cat-btn <?php echo (isset($selected_category) && $selected_category == 'tutoriels') ? 'active' : ''; ?>">
                <?php echo get_phrase('tutorials'); ?>
            </a>
        </div> -->

        <div class="row g-4">
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="col-lg-4 col-md-6">
                        <article class="article-card animate__animated animate__fadeInUp">
                            <div class="article-img-wrapper">
                                <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>" class="article-img">
                                <!-- <span class="article-badge"><?php echo $article['category']; ?></span> -->
                            </div>
                            <div class="article-body">
                                <div class="article-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo date('d M, Y', strtotime($article['created_at'])); ?></span>
                                    <span><i class="far fa-user"></i> <?php echo $article['author']; ?></span>
                                </div>
                                <a href="<?php echo site_url('trends/'.$article['slug']); ?>" class="article-title">
                                    <?php echo $article['title']; ?>
                                </a>
                                <p class="article-excerpt">
                                    <?php echo $article['summary']; ?>
                                </p>
                                <a href="<?php echo site_url('trends/'.$article['slug']); ?>" class="read-more">
                                    <?php echo get_phrase('read_more'); ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="mt-4 text-muted"><?php echo get_phrase('no_articles_found'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
