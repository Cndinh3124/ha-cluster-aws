<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$slides = get_slides($pdo);
$latestPosts = get_latest_posts($pdo, 7);
$featured = $latestPosts[0] ?? null;
$sidePosts = array_slice($latestPosts, 1);
$volunteerPosts = array_slice(get_posts_by_category_slug($pdo, 'hoi-chu-thap-do'), 0, 3);

$pageTitle = 'Trang chủ TDC';

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <?php if ($slides): ?>
        <div class="hero-slider" data-slider>
            <?php foreach ($slides as $index => $slide): ?>
                <div class="hero-card<?= $index === 0 ? ' is-active' : '' ?>" style="background-image: linear-gradient(120deg, rgba(22,120,233,0.65), rgba(10,34,94,0.85)), url('<?= e($slide['image_url']) ?>');">
                    <div class="hero-content">
                        <h1><?= e($slide['title']) ?></h1>
                        <p><?= e($slide['subtitle']) ?></p>
                        <?php if (!empty($slide['link_url'])): ?>
                            <a class="btn" href="<?= e($slide['link_url']) ?>">Xem chi tiết</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="hero-dots">
                <?php foreach ($slides as $index => $slide): ?>
                    <button type="button" class="hero-dot<?= $index === 0 ? ' is-active' : '' ?>" data-slide-to="<?= $index ?>" aria-label="Chuyển banner <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-card fallback">
            <div class="hero-content">
                <h1>Chiến dịch Sinh viên TDC</h1>
                <p>Nơi cập nhật thông tin, sự kiện và văn bản mới nhất dành cho sinh viên.</p>
            </div>
        </div>
    <?php endif; ?>
</section>

<script>
    (function () {
        const slider = document.querySelector('[data-slider]');
        if (!slider) return;

        const slides = Array.from(slider.querySelectorAll('.hero-card'));
        const dots = Array.from(slider.querySelectorAll('.hero-dot'));
        
        let current = 0;
        let timer = null;

        const goTo = (index) => {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, idx) => slide.classList.toggle('is-active', idx === current));
            dots.forEach((dot, idx) => dot.classList.toggle('is-active', idx === current));
        };

        const start = () => {
            if (slides.length <= 1) return;
            timer = window.setInterval(() => goTo(current + 1), 15000);
        };

        const stop = () => {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        };

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                goTo(idx);
                stop();
                start();
            });
        });

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);

        start();
    })();
</script>

<section class="section-news">
    <div class="container">
        <div class="section-head">
            <h2>Tin tức - Sự kiện</h2>
        </div>
        <div class="news-grid">
            <?php if ($featured): ?>
                <article class="news-featured">
                    <img src="<?= e(post_cover_image($featured)) ?>" alt="<?= e($featured['title']) ?>">
                    <div class="card-body">
                        <div class="meta"><?= e(format_date($featured['published_at'])) ?></div>
                        <h3><a href="<?= e(base_url('news.php?slug=' . $featured['slug'])) ?>"><?= e($featured['title']) ?></a></h3>
                    </div>
                </article>
            <?php endif; ?>

            <div class="news-list">
                <?php foreach ($sidePosts as $item): ?>
                    <article class="news-item">
                        <img src="<?= e(post_cover_image($item)) ?>" alt="<?= e($item['title']) ?>">
                        <div>
                            <h4><a href="<?= e(base_url('news.php?slug=' . $item['slug'])) ?>"><?= e($item['title']) ?></a></h4>
                            <div class="meta"><?= e(format_date($item['published_at'])) ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section-volunteer">
    <div class="container">
        <div class="section-head center">
            <h2>Hội Chữ Thập Đỏ</h2>
        </div>
        <div class="volunteer-carousel" data-volunteer-carousel>
            <div class="volunteer-track" data-volunteer-track>
                <?php foreach ($volunteerPosts as $index => $item): ?>
                    <?php
                    $time = strtotime((string) $item['published_at']);
                    $isFresh = $index < 2;
                    ?>
                    <article class="volunteer-card">
                        <a class="volunteer-thumb" href="<?= e(base_url('news.php?slug=' . $item['slug'])) ?>">
                            <img src="<?= e(post_cover_image($item)) ?>" alt="<?= e($item['title']) ?>">
                            <span class="volunteer-thumb-overlay"></span>
                        </a>
                        <div class="volunteer-content">
                            <div class="volunteer-date">
                                <strong><?= date('d', $time) ?></strong>
                                <span>Tháng <?= date('m', $time) ?></span>
                            </div>
                            <h3>
                                <a href="<?= e(base_url('news.php?slug=' . $item['slug'])) ?>"><?= e($item['title']) ?></a>
                                <?php if ($isFresh): ?>
                                    <span class="volunteer-badge-new">new</span>
                                <?php endif; ?>
                            </h3>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="volunteer-dots" data-volunteer-dots></div>
        </div>
    </div>
</section>

<script>
    (function () {
        const carousel = document.querySelector('[data-volunteer-carousel]');
        if (!carousel) return;

        const track = carousel.querySelector('[data-volunteer-track]');
        const cards = Array.from(track.querySelectorAll('.volunteer-card'));
        const dotsWrap = carousel.querySelector('[data-volunteer-dots]');

        if (cards.length === 0) return;

        let page = 0;
        let timer = null;

        const getPerView = () => (window.innerWidth <= 900 ? 1 : 3);

        const getPageCount = () => {
            const perView = getPerView();
            return Math.max(1, Math.ceil(cards.length / perView));
        };

        const renderDots = () => {
            const pages = getPageCount();
            dotsWrap.innerHTML = '';

            for (let i = 0; i < pages; i += 1) {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'volunteer-dot';
                dot.setAttribute('aria-label', 'Chuyển trang ' + (i + 1));
                dot.addEventListener('click', () => {
                    goTo(i);
                    stop();
                    start();
                });
                dotsWrap.appendChild(dot);
            }

            dotsWrap.style.display = pages > 1 ? 'flex' : 'none';
        };

        const goTo = (nextPage) => {
            const pages = getPageCount();
            page = (nextPage + pages) % pages;
            track.style.transform = 'translateX(' + (page * -100) + '%)';

            const dots = dotsWrap.querySelectorAll('.volunteer-dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('is-active', i === page);
            });
        };

        const start = () => {
            if (getPageCount() <= 1) return;
            timer = window.setInterval(() => goTo(page + 1), 15000);
        };

        const stop = () => {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        };

        window.addEventListener('resize', () => {
            renderDots();
            goTo(0);
            stop();
            start();
        });

        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);

        renderDots();
        goTo(0);
        start();
    })();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
