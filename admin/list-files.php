<?php
require_once('../includes/config.php');
require_once('../includes/auth.php');
require_once('../includes/database.php');

header('Content-Type: application/json');

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id']) || !validateSession($db)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
    exit;
}

// Upload klasörünü kontrol et
$upload_dir = dirname(__DIR__) . '/uploads/';
if (!file_exists($upload_dir)) {
    echo json_encode(['success' => true, 'files' => []]);
    exit;
}

// Dosyaları listele
$files = [];
$items = scandir($upload_dir);

foreach ($items as $item) {
    if ($item === '.' || $item === '..') {
        continue;
    }
    
    // .htaccess ve gizli dosyaları gizle
    if ($item === '.htaccess' || strpos($item, '.') === 0) {
        continue;
    }
    
    $file_path = $upload_dir . $item;
    if (is_file($file_path)) {
        $file_info = [
            'name' => $item,
            'url' => BASE_URL . 'uploads/' . $item,
            'size' => filesize($file_path),
            'modified' => filemtime($file_path)
        ];
        $files[] = $file_info;
    }
}

// Tarihe göre sırala (en yeni önce)
usort($files, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

echo json_encode(['success' => true, 'files' => $files]);
?>

