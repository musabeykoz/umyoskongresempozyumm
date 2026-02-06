<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Ev Sahibi');
require_once('../includes/header.php');

// Ev Sahibi içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_ev_sahibi LIMIT 1");
    $ev_sahibi = $stmt->fetch();
    if (!$ev_sahibi || empty($ev_sahibi['icerik'])) {
        // Varsayılan içerik
        $icerik = 'UMYOS 2025, Bursa Uludağ Üniversitesi Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde gerçekleştirilecektir. Yüksekokulumuz, modern eğitim anlayışı ve kaliteli öğretim kadrosuyla bölgenin önde gelen mesleki eğitim kurumlarından biridir.';
    } else {
        $icerik = $ev_sahibi['icerik'];
    }
} catch (PDOException $e) {
    // Hata durumunda varsayılan içerik
    $icerik = 'UMYOS 2025, Bursa Uludağ Üniversitesi Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde gerçekleştirilecektir. Yüksekokulumuz, modern eğitim anlayışı ve kaliteli öğretim kadrosuyla bölgenin önde gelen mesleki eğitim kurumlarından biridir.';
}
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-building"></i> Ev Sahibi
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

                <div class="all-news-link">
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>
