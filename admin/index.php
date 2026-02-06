<?php
require_once('../includes/config.php');
require_once('../includes/auth.php');

$page_title = 'Yönetim Paneli - ' . SITE_TITLE;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), #002244);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .admin-header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .admin-logo {
            width: 60px;
            height: 60px;
            background: white;
            padding: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-logo img {
            max-width: 100%;
            height: auto;
        }
        .admin-header h1 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .admin-site-link {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            white-space: nowrap;
        }
        .admin-site-link:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .admin-logout {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            white-space: nowrap;
        }
        .admin-logout:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .admin-search-container {
            margin-bottom: 30px;
        }
        .admin-search-box {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        .admin-search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .admin-search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0, 51, 102, 0.1);
        }
        .admin-search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2rem;
            pointer-events: none;
        }
        .admin-search-input:focus + .admin-search-icon {
            color: var(--primary-color);
        }
        .admin-search-clear {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1rem;
            cursor: pointer;
            display: none;
            transition: color 0.3s;
        }
        .admin-search-clear:hover {
            color: var(--primary-color);
        }
        .admin-search-clear.show {
            display: block;
        }
        .admin-search-results {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 0.9rem;
        }
        .admin-card.hidden {
            display: none;
        }
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .admin-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--text-color);
            display: block;
            position: relative;
        }
        .admin-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .admin-card-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .admin-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        .admin-card-desc {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .admin-card-page {
            position: absolute;
            bottom: 8px;
            left: 12px;
            font-size: 0.75rem;
            color: #999;
            font-family: 'Courier New', monospace;
            opacity: 0.7;
            transition: opacity 0.3s;
        }
        .admin-card:hover .admin-card-page {
            opacity: 1;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                text-align: center;
            }
            .admin-header-left {
                flex-direction: column;
                width: 100%;
            }
            .admin-header-right {
                width: 100%;
                justify-content: center;
            }
            .admin-logo {
                margin: 0 auto;
            }
            .admin-header h1 {
                font-size: 1.2rem;
            }
            .admin-search-box {
                max-width: 100%;
            }
            .admin-search-input {
                padding: 12px 45px 12px 15px;
                font-size: 0.95rem;
            }
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .admin-container {
                padding: 15px;
            }
            .admin-header {
                padding: 15px;
            }
            .admin-header h1 {
                font-size: 1rem;
            }
            .admin-logo {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <div class="admin-logo">
                    <img src="<?php echo SITE_LOGO; ?>" alt="UMYOS Logo">
                </div>
                <h1><i class="fas fa-cog"></i> Yönetim Paneli</h1>
            </div>
            <div class="admin-header-right">
                <a href="kullanicilar.php" class="admin-site-link">
                    <i class="fas fa-users-cog"></i> Kullanıcı Yönetimi
                </a>
                <a href="<?php echo BASE_URL; ?>index.php" class="admin-site-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Siteye Git
                </a>
                <a href="../login.php?logout=1" class="admin-logout">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </div>
        </div>
        
        <div class="admin-search-container">
            <div class="admin-search-box">
                <input 
                    type="text" 
                    id="adminSearch" 
                    class="admin-search-input" 
                    placeholder="Bölüm ara... (örn: Hakkımızda, İletişim, Kurul)"
                    autocomplete="off"
                >
                <i class="fas fa-search admin-search-icon"></i>
                <span class="admin-search-clear" id="adminSearchClear" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                </span>
            </div>
            <div class="admin-search-results" id="adminSearchResults"></div>
        </div>
        
        <div class="admin-grid" id="adminGrid">
            <a href="hakkimizda.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-info-circle"></i></div>
                <div class="admin-card-title">Hakkımızda Yönetimi</div>
                <div class="admin-card-desc">Hakkımızda sayfasını yönetin</div>
                <div class="admin-card-page">pages/hakkimizda.php</div>
            </a>
            
            <a href="iletisim.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-envelope"></i></div>
                <div class="admin-card-title">İletişim Yönetimi</div>
                <div class="admin-card-desc">İletişim sayfasını yönetin</div>
                <div class="admin-card-page">pages/iletisim.php</div>
            </a>
            
            <a href="duzenleme-kurulu.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-users"></i></div>
                <div class="admin-card-title">Düzenleme Kurulu Yönetimi</div>
                <div class="admin-card-desc">Düzenleme Kurulu sayfasını yönetin</div>
                <div class="admin-card-page">pages/duzenleme-kurulu.php</div>
            </a>
            
            <a href="danisma-kurulu.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-user-tie"></i></div>
                <div class="admin-card-title">Danışma Kurulu Yönetimi</div>
                <div class="admin-card-desc">Danışma Kurulu sayfasını yönetin</div>
                <div class="admin-card-page">pages/danisma-kurulu.php</div>
            </a>
            
            <a href="bilim-kurulu.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="admin-card-title">Bilim Kurulu Yönetimi</div>
                <div class="admin-card-desc">Bilim Kurulu sayfasını yönetin</div>
                <div class="admin-card-page">pages/bilim-kurulu.php</div>
            </a>
            
            <a href="ev-sahibi.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-building"></i></div>
                <div class="admin-card-title">Ev Sahibi Yönetimi</div>
                <div class="admin-card-desc">Ev Sahibi sayfasını yönetin</div>
                <div class="admin-card-page">pages/ev-sahibi.php</div>
            </a>
            
            <a href="davet-metni.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-file-alt"></i></div>
                <div class="admin-card-title">Davet Metni Yönetimi</div>
                <div class="admin-card-desc">Davet Metni sayfasını yönetin</div>
                <div class="admin-card-page">pages/davet-metni.php</div>
            </a>
            
            <a href="yayin-imkanlari.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-book"></i></div>
                <div class="admin-card-title">Yayın İmkanları Yönetimi</div>
                <div class="admin-card-desc">Yayın İmkanları sayfasını yönetin</div>
                <div class="admin-card-page">pages/yayin-imkanlari.php</div>
            </a>
            
            <a href="sempozyum-programi.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="admin-card-title">Sempozyum Programı Yönetimi</div>
                <div class="admin-card-desc">Sempozyum Programı sayfasını yönetin</div>
                <div class="admin-card-page">pages/sempozyum-programi.php</div>
            </a>
            
            <a href="sempozyum-gezisi.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-map-marked-alt"></i></div>
                <div class="admin-card-title">Sempozyum Gezisi Yönetimi</div>
                <div class="admin-card-desc">Sempozyum Gezisi sayfasını yönetin</div>
                <div class="admin-card-page">pages/sempozyum-gezisi.php</div>
            </a>
            
            <a href="konaklama.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-hotel"></i></div>
                <div class="admin-card-title">Konaklama Yönetimi</div>
                <div class="admin-card-desc">Konaklama sayfasını yönetin</div>
                <div class="admin-card-page">pages/konaklama.php</div>
            </a>
            
            <a href="ulasim.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-route"></i></div>
                <div class="admin-card-title">Ulaşım Yönetimi</div>
                <div class="admin-card-desc">Ulaşım sayfasını yönetin</div>
                <div class="admin-card-page">pages/ulasim.php</div>
            </a>
            
            <a href="egitimler.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="admin-card-title">Eğitimler Yönetimi</div>
                <div class="admin-card-desc">Eğitimler sayfasını yönetin</div>
                <div class="admin-card-page">pages/egitimler.php</div>
            </a>
            
            <a href="onemli-tarihler.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-clock"></i></div>
                <div class="admin-card-title">Önemli Tarihler Yönetimi</div>
                <div class="admin-card-desc">Önemli Tarihler sayfasını yönetin</div>
                <div class="admin-card-page">pages/onemli-tarihler.php</div>
            </a>
            
            <a href="bildiri-poster-sablonlari.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-file-powerpoint"></i></div>
                <div class="admin-card-title">Bildiri/Poster Şablonları Yönetimi</div>
                <div class="admin-card-desc">Bildiri/Poster Şablonları sayfasını yönetin</div>
                <div class="admin-card-page">pages/bildiri-poster-sablonlari.php</div>
            </a>
            
            <a href="sergi-hakkinda.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-images"></i></div>
                <div class="admin-card-title">Sergi Hakkında Yönetimi</div>
                <div class="admin-card-desc">Sergi Hakkında sayfasını yönetin</div>
                <div class="admin-card-page">pages/sergi-hakkinda.php</div>
            </a>
            
            <a href="sergi-kurallari.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-gavel"></i></div>
                <div class="admin-card-title">Sergi Kuralları Yönetimi</div>
                <div class="admin-card-desc">Sergi Kuralları sayfasını yönetin</div>
                <div class="admin-card-page">pages/sergi-kurallari.php</div>
            </a>
            
            <a href="kayit-ucretleri.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="admin-card-title">Kayıt Ücretleri Yönetimi</div>
                <div class="admin-card-desc">Kayıt Ücretleri sayfasını yönetin</div>
                <div class="admin-card-page">pages/kayit-ucretleri.php</div>
            </a>
            
            <a href="gecmis-sempozyumlar.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-history"></i></div>
                <div class="admin-card-title">Geçmiş Sempozyumlar Yönetimi</div>
                <div class="admin-card-desc">Geçmiş Sempozyumlar sayfasını yönetin</div>
                <div class="admin-card-page">pages/gecmis-sempozyumlar.php</div>
            </a>
            
            <a href="tum-haberler.php" class="admin-card">
                <div class="admin-card-icon"><i class="fas fa-newspaper"></i></div>
                <div class="admin-card-title">Tüm Haberler Yönetimi</div>
                <div class="admin-card-desc">Tüm Haberler sayfasını yönetin</div>
                <div class="admin-card-page">pages/tum-haberler.php</div>
            </a>
        </div>
    </div>
    
    <script>
        const searchInput = document.getElementById('adminSearch');
        const searchClear = document.getElementById('adminSearchClear');
        const searchResults = document.getElementById('adminSearchResults');
        const adminCards = document.querySelectorAll('.admin-card');
        
        // Arama fonksiyonu
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            
            adminCards.forEach(card => {
                const title = card.querySelector('.admin-card-title').textContent.toLowerCase();
                const desc = card.querySelector('.admin-card-desc').textContent.toLowerCase();
                const page = card.querySelector('.admin-card-page').textContent.toLowerCase();
                
                const matches = title.includes(searchTerm) || 
                               desc.includes(searchTerm) || 
                               page.includes(searchTerm);
                
                if (matches || searchTerm === '') {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Temizle butonunu göster/gizle
            if (searchTerm !== '') {
                searchClear.classList.add('show');
                searchResults.textContent = `${visibleCount} bölüm bulundu`;
            } else {
                searchClear.classList.remove('show');
                searchResults.textContent = '';
            }
            
            // Sonuç yoksa mesaj göster
            if (searchTerm !== '' && visibleCount === 0) {
                searchResults.textContent = 'Aradığınız bölüm bulunamadı';
                searchResults.style.color = '#e74c3c';
            } else {
                searchResults.style.color = '#666';
            }
        }
        
        // Arama temizleme fonksiyonu
        function clearSearch() {
            searchInput.value = '';
            searchInput.focus();
            performSearch();
        }
        
        // Event listeners
        searchInput.addEventListener('input', performSearch);
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });
        
        // Sayfa yüklendiğinde focus
        window.addEventListener('load', function() {
            // İsteğe bağlı: sayfa yüklendiğinde arama kutusuna odaklan
            // searchInput.focus();
        });
    </script>
</body>
</html>

