<?php
header("Content-Type: text/xml");

$baseURL = 'http://graphspot.dk/';

$pages = [
    'home',
    'browse',
    'profile',
    'settings',
    'adminPanel',
    'singlePost',
    'userProfile',
    'followers',
    'following'
];

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($pages as $page): ?>
        <url>
            <loc><?= htmlspecialchars($baseURL . "index.php?page=" . $page) ?></loc>
            <lastmod><?= date('c') ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    <?php endforeach; ?>
</urlset>
