    <?php
    $publicPage = basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
    if (isset($pdo) && in_array($publicPage, ['index', 'search', 'contact', 'privacy', 'terms'], true)) {
        $publicAds = active_content_ad_groups($pdo)['sitewide'] ?? [];
        if ($publicAds) {
            echo '<section class="library-shell public-ad-strip" aria-label="Sponsored placements">'
                . render_ad_group($publicAds, 'Public page sponsor') . '</section>';
        }
    }
    ?>
    </main>
    <footer class="nectra-footer">
        <div class="library-shell">
            <div class="footer-layout">
                <div>
                    <a href="<?php echo h(lang_href('index')); ?>" class="text-2xl font-bold text-white">Learn<span class="brand-accent">.Nectra</span></a>
                    <p class="mt-2"><?php echo h(t('footer.tagline')); ?></p>
                </div>
                <nav aria-label="<?php echo h(t('footer.resources')); ?>">
                    <h2><?php echo h(t('footer.learn')); ?></h2>
                    <ul>
                        <li><a href="<?php echo h(lang_href('index#courses')); ?>"><?php echo h(t('footer.library')); ?></a></li>
                        <li><a href="<?php echo h(lang_href('search')); ?>"><?php echo h(t('footer.search')); ?></a></li>
                        <li><a href="<?php echo h(lang_href(isset($_SESSION['student_logged_in']) ? 'student' : 'login')); ?>"><?php echo h(t('footer.my')); ?></a></li>
                    </ul>
                </nav>
                <nav aria-label="<?php echo h(t('footer.policies')); ?>">
                    <h2><?php echo h(t('footer.help')); ?></h2>
                    <ul>
                        <li><a href="<?php echo h(lang_href('contact')); ?>"><?php echo h(t('footer.contact')); ?></a></li>
                        <li><a href="<?php echo h(lang_href('billing')); ?>"><?php echo h(t('footer.plans')); ?></a></li>
                        <li><a href="<?php echo h(lang_href('privacy')); ?>"><?php echo h(t('footer.privacy')); ?></a></li>
                        <li><a href="<?php echo h(lang_href('terms')); ?>"><?php echo h(t('footer.terms')); ?></a></li>
                    </ul>
                </nav>
            </div>
            <p class="footer-legal">&copy; <?php echo date('Y'); ?> Learn.Nectra</p>
        </div>
    </footer>
    <script src="<?php echo h(protected_asset_url('assets/js/tryit-runners.js')); ?>"></script>
    <script src="<?php echo h(protected_asset_url('assets/js/tryit.js')); ?>"></script>
    <script src="<?php echo h(protected_asset_url('assets/js/main.js')); ?>"></script>
</body>
</html>
