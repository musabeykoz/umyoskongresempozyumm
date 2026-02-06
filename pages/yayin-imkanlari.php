<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Yayın İmkanları');
require_once('../includes/header.php');

// Yayın İmkanları içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_yayin_imkanlari LIMIT 1");
    $yayin_imkanlari = $stmt->fetch();
    if (!$yayin_imkanlari || empty($yayin_imkanlari['icerik'])) {
        // Varsayılan içerik
        $icerik = 'Yayın imkanları içeriği henüz eklenmemiş.';
    } else {
        $icerik = $yayin_imkanlari['icerik'];
    }
} catch (PDOException $e) {
    // Hata durumunda varsayılan içerik
    $icerik = 'Yayın imkanları içeriği henüz eklenmemiş.';
}
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="page-content">
                    <h2 class="news-title">
                        <i class="fas fa-book"></i> Yayın İmkanları
                    </h2>
                    <div class="news-content">
                        <div style="line-height: 1.8; word-wrap: break-word;">
                            <?php 
                            // Güvenli HTML tag'lerine izin ver
                            $allowed_tags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><span><div><blockquote><hr><table><tr><td><th><thead><tbody><tfoot>';
                            // HTML içeriğini temizle ve göster
                            $clean_content = strip_tags(trim($icerik), $allowed_tags);
                            // Eğer içerik HTML değilse (düz metin), satır sonlarını <br> ile değiştir
                            if (!preg_match('/<[^>]+>/', trim($icerik))) {
                                $clean_content = nl2br(htmlspecialchars(trim($icerik)));
                            }
                            echo $clean_content;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="all-news-link">
                <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                    <i class="fas fa-home"></i> Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>

