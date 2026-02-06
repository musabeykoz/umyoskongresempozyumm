<?php
require_once('includes/config.php');
require_once('includes/database.php');

$page_title = getPageTitle();
require_once('includes/header.php');

// En son aktif haberi veritabanından çekme
try {
    $stmt = $db->query("SELECT id, baslik, icerik, created_at FROM umyos_haberler WHERE aktif = 1 ORDER BY created_at DESC LIMIT 1");
    $haber = $stmt->fetch();
    
    if (!$haber) {
        // Varsayılan içerik
        $haber = [
            'baslik' => 'UMYOS 2025',
            'icerik' => '2025 yılında 13.sü düzenlenecek olan Uluslararası Meslek Yüksekokulları Sempozyumu Bursa Uludağ Üniversitesi\'nde Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde gerçekleştirilecektir.',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
} catch (PDOException $e) {
    // Hata durumunda varsayılan içerik
    $haber = [
        'baslik' => 'UMYOS 2025',
        'icerik' => '2025 yılında 13.sü düzenlenecek olan Uluslararası Meslek Yüksekokulları Sempozyumu Bursa Uludağ Üniversitesi\'nde Teknik Bilimler Meslek Yüksekokulu ev sahipliğinde gerçekleştirilecektir.',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Tarih formatla (d.m.Y H:i:s)
$eklenme_tarihi = date('d.m.Y H:i:s', strtotime($haber['created_at']));
?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?php include('includes/sidebar.php'); ?>

            <!-- Content Area -->
            <section class="content-area">
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
                        // HTML içeriğini temizle
                        $clean_content = strip_tags($haber['icerik'], $allowed_tags);
                        // Eğer içerik HTML değilse (düz metin), satır sonlarını <br> ile değiştir
                        if (!preg_match('/<[^>]+>/', trim($haber['icerik']))) {
                            $clean_content = nl2br(htmlspecialchars(trim($haber['icerik'])));
                        }
                        // İçeriği kısalt (özet)
                        $ozet_icerik = mb_strlen($clean_content, 'UTF-8') > 200 ? mb_substr($clean_content, 0, 200, 'UTF-8') . '...' : $clean_content;
                        echo $ozet_icerik;
                        ?>
                    </div>
                    <div class="news-footer">
                        <div class="share-section">
                            <span class="share-label"><i class="fas fa-share-alt"></i> Paylaş:</span>
                            <div class="share-buttons">
                                <a href="#" class="share-btn facebook" title="Facebook'ta Paylaş">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="share-btn twitter" title="Twitter'da Paylaş">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="share-btn linkedin" title="LinkedIn'de Paylaş">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="share-btn whatsapp" title="WhatsApp'ta Paylaş">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="#" class="share-btn email" title="E-posta ile Paylaş">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                <button type="button" class="share-btn copy-link" title="Bağlantıyı Kopyala" onclick="copyHaberLink()">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php if (mb_strlen($clean_content, 'UTF-8') > 200): ?>
                    <button type="button" class="btn-all-news haber-modal-btn" 
                            data-haber-id="<?php echo htmlspecialchars($haber['id'] ?? 0); ?>" 
                            data-haber-baslik="<?php echo htmlspecialchars($haber['baslik'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-haber-icerik="<?php echo htmlspecialchars(base64_encode($haber['icerik']), ENT_QUOTES, 'UTF-8'); ?>" 
                            data-haber-tarih="<?php echo htmlspecialchars($eklenme_tarihi); ?>">
                        <i class="fas fa-arrow-right"></i> Devamını Oku
                    </button>
                    <?php endif; ?>
                </div>

                <div class="all-news-link">
                    <a href="pages/tum-haberler.php" class="btn-all-news">
                        <i class="fas fa-list"></i> Tüm Haberler
                    </a>
                </div>
            </section>
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
        
        /* Paylaş butonlarını küçült */
        .news-card .share-btn {
            width: 32px;
            height: 32px;
            font-size: 0.85rem;
        }
        
        .news-card .share-buttons {
            gap: 8px;
        }
        
        .news-card .share-label {
            font-size: 0.9rem;
        }
        
        .share-btn.copy-link {
            background-color: #6c757d;
        }
        
        .share-btn.copy-link:hover {
            background-color: #5a6268;
        }
        
        .copy-link-success {
            background-color: #28a745 !important;
        }
        
        .copy-link-success:hover {
            background-color: #218838 !important;
        }
        
        /* Paylaş bölümü ile Devamını Oku butonu arası boşluk */
        .news-card .news-footer {
            margin-bottom: 20px;
        }
        
        .news-card .haber-modal-btn {
            margin-top: 20px;
        }
    </style>

    <script>
        // "Devamını Oku" butonuna tıklama eventi ekle
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
        
        // Haber bağlantısını kopyala
        function copyHaberLink() {
            const currentUrl = window.location.href;
            const copyBtn = document.querySelector('.share-btn.copy-link');
            
            // Clipboard API kullan
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(currentUrl).then(function() {
                    // Başarılı mesajı göster
                    const originalClass = copyBtn.className;
                    copyBtn.classList.add('copy-link-success');
                    copyBtn.querySelector('i').className = 'fas fa-check';
                    copyBtn.title = 'Bağlantı kopyalandı!';
                    
                    setTimeout(function() {
                        copyBtn.className = originalClass;
                        copyBtn.querySelector('i').className = 'fas fa-link';
                        copyBtn.title = 'Bağlantıyı Kopyala';
                    }, 2000);
                }).catch(function(err) {
                    console.error('Kopyalama hatası:', err);
                    fallbackCopyTextToClipboard(currentUrl, copyBtn);
                });
            } else {
                // Fallback yöntem
                fallbackCopyTextToClipboard(currentUrl, copyBtn);
            }
        }
        
        // Fallback kopyalama yöntemi
        function fallbackCopyTextToClipboard(text, btn) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    const originalClass = btn.className;
                    btn.classList.add('copy-link-success');
                    btn.querySelector('i').className = 'fas fa-check';
                    btn.title = 'Bağlantı kopyalandı!';
                    
                    setTimeout(function() {
                        btn.className = originalClass;
                        btn.querySelector('i').className = 'fas fa-link';
                        btn.title = 'Bağlantıyı Kopyala';
                    }, 2000);
                }
            } catch (err) {
                console.error('Kopyalama hatası:', err);
                alert('Bağlantı kopyalanamadı. Lütfen manuel olarak kopyalayın: ' + text);
            }
            
            document.body.removeChild(textArea);
        }
    </script>

<?php require_once('includes/footer.php'); ?>

