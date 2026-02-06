<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Sempozyum Gezisi');

// Sempozyum Gezisi içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_sempozyum_gezisi LIMIT 1");
    $sempozyum_gezisi = $stmt->fetch();
    if (!$sempozyum_gezisi || empty($sempozyum_gezisi['icerik'])) {
        $icerik = 'Sempozyum Gezisi içeriği henüz eklenmemiş.';
    } else {
        $icerik = $sempozyum_gezisi['icerik'];
    }
} catch (PDOException $e) {
    $icerik = 'Sempozyum Gezisi içeriği henüz eklenmemiş.';
}

require_once('../includes/header.php');
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-map-marked-alt"></i> Sempozyum Gezisi
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
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>

