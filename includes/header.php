<?php
$categories = get_categories($pdo);
$site = get_site_settings($pdo);
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')) ?>">
</head>
<body>
<header>
    <div class="topbar">
        <div class="container topbar-inner">
            <a href="<?= e(base_url()) ?>">TRANG CHỦ</a>
            <a href="#">TUYỂN SINH</a>
            <a href="<?= e(base_url('lien-he.php')) ?>">LIÊN HỆ</a>
        </div>
    </div>
    <div class="navbar-wrap">
        <div class="container navbar">
            <div class="logo-group" id="site-logo-trigger" role="button" tabindex="0">
                <?php if (!empty($site['logo_image_url'])): ?>
                    <img class="logo-image" src="<?= e($site['logo_image_url']) ?>" alt="Logo trường">
                <?php else: ?>
                    <div class="logo-badge">TDC</div>
                <?php endif; ?>
                <div>
                    <div class="logo-title"><?= e($site['school_name']) ?></div>
                    <div class="logo-sub"><?= e($site['school_subtitle']) ?></div>
                </div>
            </div>
            <nav>
                <ul class="main-menu">
                    <li><a href="<?= e(base_url('gioi-thieu.php')) ?>">Giới thiệu</a></li>
                    <li><a href="<?= e(base_url('thong-bao.php')) ?>">Thông báo</a></li>
                    <li><a href="<?= e(base_url('van-ban.php')) ?>">Văn bản</a></li>
                    <li class="has-sub">
                        <a href="<?= e(base_url('category.php?slug=tin-tuc-su-kien')) ?>">Tin tức</a>
                        <ul class="sub-menu">
                            <?php foreach ($categories as $cat): ?>
                                <li><a href="<?= e(base_url('category.php?slug=' . $cat['slug'])) ?>"><?= e($cat['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
<script>
// Easter-egg: nhấn 7 lần logo để vào login admin
(function(){
    var el = document.getElementById('site-logo-trigger');
    if (!el) return;
    var count = 0;
    var timeout = null;
    el.addEventListener('click', function(e){
        count++;
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(function(){ count = 0; }, 2000);
        if (count >= 7) {
            window.location.href = '<?= e(base_url('admin/login.php')) ?>';
        }
    }, false);
})();
</script>
<main>
