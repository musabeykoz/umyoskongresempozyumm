<?php
// Temel Yapılandırma Dosyası
// Bu dosya tüm sayfalarda kullanılacak temel ayarları içerir

// Hata raporlama ayarları (production'da kapatılmalı)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session başlatma ve güvenlik ayarları
if (session_status() === PHP_SESSION_NONE) {
    // Güvenli session ayarları
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // HTTPS kullanıyorsanız 1 yapın
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600); // 1 saat
    
    session_start();
    
    // Session fixation koruması
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

// Sayfa klasör yapısına göre base URL belirleme
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$page_level = ($current_dir === 'pages' || $current_dir === 'admin') ? '../' : '';
define('BASE_URL', $page_level);

// Site bilgileri
define('SITE_TITLE', 'UMYOS 2025');
define('SITE_SUBTITLE', '13. Uluslararası Meslek Yüksekokulları Sempozyumu');
define('SITE_LOGO', 'https://pbs.twimg.com/profile_images/1726500126032556033/VnQfpW-I_400x400.jpg');

// Aktif sayfa belirleme fonksiyonu
function isActivePage($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page === $page) ? 'active' : '';
}

// Sayfa başlığı oluşturma fonksiyonu
function getPageTitle($page_name = '') {
    if ($page_name) {
        return $page_name . ' - ' . SITE_TITLE;
    }
    return SITE_TITLE . ' - ' . SITE_SUBTITLE;
}
?>

