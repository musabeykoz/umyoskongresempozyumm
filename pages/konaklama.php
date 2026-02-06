<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Konaklama');

// Konaklama içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_konaklama LIMIT 1");
    $konaklama = $stmt->fetch();
    if (!$konaklama || empty($konaklama['icerik'])) {
        $icerik = 'Konaklama içeriği henüz eklenmemiş.';
    } else {
        $icerik = $konaklama['icerik'];
    }
} catch (PDOException $e) {
    $icerik = 'Konaklama içeriği henüz eklenmemiş.';
}

$extra_styles = '<style>
    .hotel-card {
        background: white;
        padding: 25px;
        margin-bottom: 25px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        border-left: 5px solid var(--secondary-color);
        transition: all 0.3s ease;
    }
    .hotel-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }
    .hotel-name {
        color: var(--primary-color);
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .hotel-stars {
        color: #ffc107;
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    .hotel-info {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }
    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-color);
    }
    .price-tag {
        background: var(--accent-color);
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: bold;
        display: inline-block;
        margin-top: 15px;
    }
</style>';
require_once('../includes/header.php');
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-hotel"></i> Konaklama Bilgileri
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

