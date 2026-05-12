<?php $site = get_site_settings($pdo); ?>
</main>
<footer class="site-footer">
    <div class="footer-overlay">
        <div class="container footer-grid">
            <div>
                <h3><?= e($site['footer_title_left']) ?></h3>
                <p><?= e($site['footer_address']) ?></p>
                <p><?= e($site['footer_phone']) ?></p>
                <p><?= e($site['footer_email']) ?></p>
            </div>
            <div>
                <h3><?= e($site['footer_title_right']) ?></h3>
                <p><?= e($site['footer_hotline_1']) ?></p>
                <p><?= e($site['footer_hotline_2']) ?></p>
                <p><?= e($site['footer_hotline_3']) ?></p>
            </div>
        </div>
    </div>
    <div class="copyright">&copy; <?= date('Y') ?> <?= e($site['footer_copyright']) ?></div>
</footer>
</body>
</html>
