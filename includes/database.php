<?php
// Veritabanı Bağlantı Dosyası

// .env dosyasını yükle (sadece bir kez)
if (file_exists(__DIR__ . '/../.env')) {
    $envFile = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        // Yorum satırlarını ve boş satırları atla
        if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
            continue;
        }
        // KEY=VALUE formatını parse et
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Tırnak işaretlerini kaldır
            $value = trim($value, '"\'');
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }
}

// Environment variable'dan veya .env'den oku, yoksa varsayılan değerleri kullan
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') ?: '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: 'ysuniono_umyos');
}

// Veritabanı bağlantısı
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    // Hata durumunda exception fırlat (login.php'de yakalanacak)
    // Production'da detaylı hata mesajı gösterme
    $errorMsg = (ini_get('display_errors')) 
        ? "Veritabanı bağlantı hatası: " . $e->getMessage()
        : "Veritabanı bağlantı hatası!";
    throw new Exception($errorMsg);
}
?>
