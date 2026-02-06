<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Sempozyum Programı');
require_once('../includes/header.php');

// Sempozyum Programı içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_sempozyum_programi LIMIT 1");
    $sempozyum_programi = $stmt->fetch();
    if (!$sempozyum_programi || empty($sempozyum_programi['icerik'])) {
        // Varsayılan içerik
        $icerik = 'Sempozyum programı içeriği henüz eklenmemiş.';
    } else {
        $icerik = $sempozyum_programi['icerik'];
    }
} catch (PDOException $e) {
    // Hata durumunda varsayılan içerik
    $icerik = 'Sempozyum programı içeriği henüz eklenmemiş.';
}
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="page-content">
                    <h2 class="news-title">
                        <i class="fas fa-calendar-alt"></i> Sempozyum Programı
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

