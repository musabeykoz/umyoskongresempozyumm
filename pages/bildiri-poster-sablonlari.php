<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Bildiri/Poster Şablonları');

// Bildiri/Poster Şablonları içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_bildiri_poster_sablonlari LIMIT 1");
    $bildiri_poster = $stmt->fetch();
    if (!$bildiri_poster || empty($bildiri_poster['icerik'])) {
        $icerik = 'Bildiri/Poster Şablonları içeriği henüz eklenmemiş.';
    } else {
        $icerik = $bildiri_poster['icerik'];
    }
} catch (PDOException $e) {
    $icerik = 'Bildiri/Poster Şablonları içeriği henüz eklenmemiş.';
}

require_once('../includes/header.php');
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-file-download"></i> Bildiri/Poster Şablonları
                    </h2>
                    <div class="news-content">
                        <div style="line-height: 1.8; word-wrap: break-word;">
                            <?php 
                            $allowed_tags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><span><div><blockquote><hr><table><tr><td><th><thead><tbody><tfoot>';
                            $clean_content = strip_tags(trim($icerik), $allowed_tags);
                            if (!preg_match('/<[^>]+>/', trim($icerik))) {
                                $clean_content = nl2br(htmlspecialchars(trim($icerik)));
                            }
                            echo $clean_content;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="all-news-link">
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>
