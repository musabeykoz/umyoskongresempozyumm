<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Ulaşım');

// Ulaşım içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_ulasim LIMIT 1");
    $ulasim = $stmt->fetch();
    if (!$ulasim || empty($ulasim['icerik'])) {
        $icerik = 'Ulaşım içeriği henüz eklenmemiş.';
    } else {
        $icerik = $ulasim['icerik'];
    }
} catch (PDOException $e) {
    $icerik = 'Ulaşım içeriği henüz eklenmemiş.';
}

$extra_styles = '<style>
    .transport-card {
        background: white;
        padding: 25px;
        margin-bottom: 25px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        border-left: 5px solid var(--secondary-color);
        transition: all 0.3s ease;
    }
    .transport-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }
    .transport-title {
        color: var(--primary-color);
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .transport-info {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 15px;
    }
    .transport-info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .transport-info-item i {
        color: var(--secondary-color);
        font-size: 1.2rem;
        margin-top: 3px;
        min-width: 20px;
    }
    .transport-info-item span {
        line-height: 1.6;
    }
    .map-container {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin: 20px 0;
        text-align: center;
    }
    .route-box {
        background: #e3f2fd;
        padding: 20px;
        border-radius: 8px;
        margin: 15px 0;
        border-left: 4px solid var(--secondary-color);
    }
    .route-box h4 {
        color: var(--primary-color);
        margin-bottom: 10px;
    }
    @media (max-width: 768px) {
        .transport-card {
            padding: 20px;
        }
        .transport-title {
            font-size: 1.2rem;
        }
    }
</style>';
require_once('../includes/header.php');
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-route"></i> Ulaşım Bilgileri
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
                    <a href="<?php echo BASE_URL; ?>pages/konaklama.php" class="btn-all-news" style="margin-right: 10px;">
                        <i class="fas fa-hotel"></i> Konaklama Bilgileri
                    </a>
                    <a href="<?php echo BASE_URL; ?>pages/iletisim.php" class="btn-all-news" style="margin-right: 10px;">
                        <i class="fas fa-envelope"></i> İletişim
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>
