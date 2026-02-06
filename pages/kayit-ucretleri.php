<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Kayıt Ücretleri');

// Kayıt Ücretleri içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_kayit_ucretleri LIMIT 1");
    $kayit_ucretleri = $stmt->fetch();
    if (!$kayit_ucretleri || empty($kayit_ucretleri['icerik'])) {
        $icerik = 'Kayıt Ücretleri içeriği henüz eklenmemiş.';
    } else {
        $icerik = $kayit_ucretleri['icerik'];
    }
} catch (PDOException $e) {
    $icerik = 'Kayıt Ücretleri içeriği henüz eklenmemiş.';
}

$extra_styles = '<style>
    .price-table {
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
    }
    .price-table th {
        background: var(--primary-color);
        color: white;
        padding: 20px;
        text-align: left;
        font-size: 1.1rem;
    }
    .price-table td {
        padding: 18px 20px;
        border-bottom: 1px solid #eee;
    }
    .price-table tr:last-child td {
        border-bottom: none;
    }
    .price-table tr:hover {
        background: #f8f9fa;
    }
    .price-amount {
        color: var(--secondary-color);
        font-weight: bold;
        font-size: 1.2rem;
    }
    .discount-badge {
        background: #4caf50;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 10px;
    }
</style>';
require_once('../includes/header.php');
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-money-bill-wave"></i> Kayıt Ücretleri
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
