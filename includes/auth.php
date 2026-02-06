<?php
// Admin yetkilendirme kontrolü
// Bu dosya admin sayfalarında kullanılacak

require_once(__DIR__ . '/database.php');
require_once(__DIR__ . '/functions.php');

// Session kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    secureLogout();
    header('Location: ../login.php');
    exit;
}

// Session doğrulama
if (!validateSession($db)) {
    secureLogout();
    header('Location: ../login.php?expired=1');
    exit;
}

// Session timeout kontrolü (1 saat)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 3600) {
    secureLogout();
    header('Location: ../login.php?expired=1');
    exit;
}

// Session aktivitesini güncelle (her sayfa yüklemesinde)
$_SESSION['last_activity'] = time();

// Session ID'yi periyodik olarak yenile (her 30 dakikada bir)
if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
?>

