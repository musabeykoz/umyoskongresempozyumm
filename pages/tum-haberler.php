<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Tüm Haberler');
require_once('../includes/header.php');

// Tüm aktif haberleri veritabanından çekme
try {
    $stmt = $db->query("SELECT id, baslik, icerik, created_at FROM umyos_haberler WHERE aktif = 1 ORDER BY created_at DESC");
    $haberler = $stmt->fetchAll();
    
    if (!$haberler || count($haberler) == 0) {
        // Varsayılan haber
        $haberler = [[
            'id' => 1,
            'baslik' => 'UMYOS 2025',
            'icerik' => '2025 yılında 13.sü düzenlenecek olan Uluslararası Meslek Yüksekokulları Sempozyumu Bursa Uludağ Üniversitesi\'nde Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde gerçekleştirilecektir.',
            'created_at' => date('Y-m-d H:i:s')
        ]];
    }
} catch (PDOException $e) {
    // Hata durumunda varsayılan haber
    $haberler = [[
        'id' => 1,
        'baslik' => 'UMYOS 2025',
        'icerik' => '2025 yılında 13.sü düzenlenecek olan Uluslararası Meslek Yüksekokulları Sempozyumu Bursa Uludağ Üniversitesi\'nde Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde gerçekleştirilecektir.',
        'created_at' => date('Y-m-d H:i:s')
    ]];
}
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <h2 style="color: var(--primary-color); margin-bottom: 30px;">
                    <i class="fas fa-newspaper"></i> Tüm Haberler
                </h2>

                <?php foreach ($haberler as $haber): 
                    // Tarih formatla (d.m.Y H:i:s)
                    $eklenme_tarihi = date('d.m.Y H:i:s', strtotime($haber['created_at']));
                ?>
                <div class="news-card">
                    <div class="news-header">
                        <span class="news-category">Haber</span>
                        <span class="news-date"><?php echo htmlspecialchars($eklenme_tarihi); ?></span>
                    </div>
                    <h2 class="news-title"><?php echo htmlspecialchars($haber['baslik']); ?></h2>
                    <div class="news-content">
                        <?php 
                        // Güvenli HTML tag'lerine izin ver
                        $allowed_tags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><span><div><blockquote><hr>';
                        // HTML içeriğini temizle ve göster
                        $clean_content = strip_tags($haber['icerik'], $allowed_tags);
                        // Eğer içerik HTML değilse (düz metin), satır sonlarını <br> ile değiştir
                        if (!preg_match('/<[^>]+>/', trim($haber['icerik']))) {
                            $clean_content = nl2br(htmlspecialchars(trim($haber['icerik'])));
                        }
                        // İçeriği kısalt (özet)
                        $ozet_icerik = strlen($clean_content) > 200 ? substr($clean_content, 0, 200) . '...' : $clean_content;
                        echo $ozet_icerik;
                        ?>
                    </div>
                    <button type="button" class="btn-all-news haber-modal-btn" 
                            data-haber-id="<?php echo htmlspecialchars($haber['id']); ?>" 
                            data-haber-baslik="<?php echo htmlspecialchars($haber['baslik'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-haber-icerik="<?php echo htmlspecialchars(base64_encode($haber['icerik']), ENT_QUOTES, 'UTF-8'); ?>" 
                            data-haber-tarih="<?php echo htmlspecialchars($eklenme_tarihi); ?>">
                        <i class="fas fa-arrow-right"></i> Devamını Oku
                    </button>
                </div>
                <?php endforeach; ?>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Haber Detay Modal -->
    <div id="haberModal" class="haber-modal" style="display: none;">
        <div class="haber-modal-overlay" onclick="closeHaberModal()"></div>
        <div class="haber-modal-content">
            <div class="haber-modal-header">
                <h2 id="modalBaslik"></h2>
                <button class="haber-modal-close" onclick="closeHaberModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="haber-modal-body">
                <div class="haber-modal-date">
                    <i class="fas fa-calendar-plus"></i> Eklenme Tarihi: <strong id="modalTarih"></strong>
                </div>
                <div id="modalIcerik" class="haber-modal-icerik"></div>
            </div>
        </div>
    </div>

    <style>
        .haber-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .haber-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }
        .haber-modal-content {
            position: relative;
            background: white;
            border-radius: 15px;
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .haber-modal-header {
            padding: 25px 30px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary-color), #002244);
            color: white;
        }
        .haber-modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            flex: 1;
            padding-right: 20px;
        }
        .haber-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .haber-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        .haber-modal-body {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }
        .haber-modal-date {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            color: var(--text-color);
            font-size: 0.95rem;
        }
        .haber-modal-date i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        .haber-modal-icerik {
            line-height: 1.8;
            color: var(--text-color);
            word-wrap: break-word;
        }
        .haber-modal-icerik p {
            margin-bottom: 15px;
        }
        .haber-modal-icerik h1, .haber-modal-icerik h2, .haber-modal-icerik h3 {
            margin-top: 20px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        .haber-modal-icerik ul, .haber-modal-icerik ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        .haber-modal-icerik li {
            margin-bottom: 8px;
        }
        .haber-modal-icerik a {
            color: var(--primary-color);
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @media (max-width: 768px) {
            .haber-modal-content {
                width: 95%;
                max-height: 95vh;
            }
            .haber-modal-header {
                padding: 20px;
            }
            .haber-modal-header h2 {
                font-size: 1.2rem;
            }
            .haber-modal-body {
                padding: 20px;
            }
        }
    </style>

    <script>
        // Tüm "Devamını Oku" butonlarına tıklama eventi ekle
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.haber-modal-btn');
            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-haber-id');
                    const baslik = this.getAttribute('data-haber-baslik');
                    const icerikEncoded = this.getAttribute('data-haber-icerik');
                    const tarih = this.getAttribute('data-haber-tarih');
                    
                    // Base64 decode (UTF-8 uyumlu)
                    let icerik = '';
                    try {
                        // UTF-8 uyumlu decode
                        icerik = decodeURIComponent(escape(atob(icerikEncoded)));
                    } catch(e) {
                        try {
                            // Fallback: normal decode
                            icerik = atob(icerikEncoded);
                        } catch(e2) {
                            icerik = icerikEncoded; // Eğer decode edilemezse olduğu gibi kullan
                        }
                    }
                    
                    openHaberModal(id, baslik, icerik, tarih);
                });
            });
        });
        
        function openHaberModal(id, baslik, icerik, tarih) {
            const modal = document.getElementById('haberModal');
            const modalBaslik = document.getElementById('modalBaslik');
            const modalIcerik = document.getElementById('modalIcerik');
            const modalTarih = document.getElementById('modalTarih');
            
            modalBaslik.textContent = baslik;
            modalTarih.textContent = tarih;
            
            // İçeriği göster (HTML içeriği PHP'den zaten güvenli şekilde geliyor)
            // Eğer düz metin ise satır sonlarını <br> ile değiştir
            let cleanContent = icerik;
            if (!cleanContent.includes('<')) {
                cleanContent = cleanContent.replace(/\n/g, '<br>');
            }
            
            modalIcerik.innerHTML = cleanContent;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Sayfa kaydırmayı engelle
        }
        
        function closeHaberModal() {
            const modal = document.getElementById('haberModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Sayfa kaydırmayı geri getir
        }
        
        // ESC tuşu ile modal'ı kapat
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeHaberModal();
            }
        });
    </script>

<?php require_once('../includes/footer.php'); ?>

