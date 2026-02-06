<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Sergi Kuralları');

// Sergi Kuralları içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_sergi_kurallari LIMIT 1");
    $sergi_kurallari = $stmt->fetch();
    if (!$sergi_kurallari || empty($sergi_kurallari['icerik'])) {
        $icerik = 'Sergi Kuralları içeriği henüz eklenmemiş.';
    } else {
        $icerik = $sergi_kurallari['icerik'];
    }
} catch (PDOException $e) {
    $icerik = 'Sergi Kuralları içeriği henüz eklenmemiş.';
}

$extra_styles = '<style>
    .rules-section {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
    }
    .rules-section h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--secondary-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .rules-section ul {
        list-style-position: inside;
        line-height: 2;
    }
    .rules-section li {
        margin-bottom: 10px;
        padding-left: 10px;
    }
    .info-box {
        background: #e3f2fd;
        border-left: 4px solid var(--secondary-color);
        padding: 20px;
        margin: 20px 0;
        border-radius: 5px;
    }
    .warning-box {
        background: #fff3e0;
        border-left: 4px solid var(--accent-color);
        padding: 20px;
        margin: 20px 0;
        border-radius: 5px;
    }
    .success-box {
        background: #e8f5e9;
        border-left: 4px solid #4caf50;
        padding: 20px;
        margin: 20px 0;
        border-radius: 5px;
    }
</style>';
require_once('../includes/header.php');
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-file-alt"></i> Sergi Kuralları
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
                    <a href="sergi-hakkinda.php" class="btn-all-news" style="margin-right: 10px;">
                        <i class="fas fa-info-circle"></i> Sergi Hakkında
                    </a>
                    <a href="sergi-takvimi.php" class="btn-all-news" style="margin-right: 10px;">
                        <i class="fas fa-calendar-alt"></i> Sergi Takvimi
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>
