<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Davet Metni');
require_once('../includes/header.php');

// Davet Metni içeriğini veritabanından çekme
try {
    $stmt = $db->query("SELECT icerik FROM umyos_davet_metni LIMIT 1");
    $davet_metni = $stmt->fetch();
    if (!$davet_metni || empty($davet_metni['icerik'])) {
        // Varsayılan içerik
        $icerik = 'Değerli Akademisyenler, Araştırmacılar ve Sektör Temsilcileri,

Bursa Uludağ Üniversitesi Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde, 22-24 Mayıs 2025 tarihleri arasında gerçekleştirilecek olan 13. Uluslararası Meslek Yüksekokulları Sempozyumu (UMYOS 2025)\'na sizleri davet etmekten büyük mutluluk duyuyoruz.

Meslek yüksekokullarının eğitim-öğretim kalitesinin artırılması, akademik gelişmelerin paylaşılması ve sektör-üniversite iş birliğinin güçlendirilmesi amacıyla düzenlenen sempozyumumuz, ulusal ve uluslararası platformda önemli bir buluşma noktası olma özelliğini sürdürmektedir.

Sempozyumumuzda, akademik çalışmalarınızı sunma, deneyimlerinizi paylaşma ve meslektaşlarınızla bir araya gelme fırsatı bulacaksınız. Bildiri sunumlarının yanı sıra, panel oturumları, atölye çalışmaları ve networking etkinlikleri düzenlenecektir.

Tarihi ve kültürel zenginlikleriyle dikkat çeken Bursa\'da gerçekleştirilecek olan sempozyumumuza katılımınızı bekliyoruz.

Saygılarımızla,
UMYOS 2025 Düzenleme Kurulu
Bursa Uludağ Üniversitesi
Teknik Bilimler Meslek Yüksekokulu';
    } else {
        $icerik = $davet_metni['icerik'];
    }
} catch (PDOException $e) {
    // Hata durumunda varsayılan içerik
    $icerik = 'Değerli Akademisyenler, Araştırmacılar ve Sektör Temsilcileri,

Bursa Uludağ Üniversitesi Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde, 22-24 Mayıs 2025 tarihleri arasında gerçekleştirilecek olan 13. Uluslararası Meslek Yüksekokulları Sempozyumu (UMYOS 2025)\'na sizleri davet etmekten büyük mutluluk duyuyoruz.

Meslek yüksekokullarının eğitim-öğretim kalitesinin artırılması, akademik gelişmelerin paylaşılması ve sektör-üniversite iş birliğinin güçlendirilmesi amacıyla düzenlenen sempozyumumuz, ulusal ve uluslararası platformda önemli bir buluşma noktası olma özelliğini sürdürmektedir.

Sempozyumumuzda, akademik çalışmalarınızı sunma, deneyimlerinizi paylaşma ve meslektaşlarınızla bir araya gelme fırsatı bulacaksınız. Bildiri sunumlarının yanı sıra, panel oturumları, atölye çalışmaları ve networking etkinlikleri düzenlenecektir.

Tarihi ve kültürel zenginlikleriyle dikkat çeken Bursa\'da gerçekleştirilecek olan sempozyumumuza katılımınızı bekliyoruz.

Saygılarımızla,
UMYOS 2025 Düzenleme Kurulu
Bursa Uludağ Üniversitesi
Teknik Bilimler Meslek Yüksekokulu';
}
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="page-content">
                    <h2 class="news-title">
                        <i class="fas fa-envelope-open-text"></i> Davet Metni
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

                <div style="text-align: center; margin-top: 30px;">
                    <a href="https://openconf.com" class="btn-all-news" style="margin-right: 10px;" target="_blank">
                        <i class="fas fa-file-alt"></i> Eser Gönderme
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>
