<?php
require_once('../includes/config.php');
require_once('../includes/auth.php');
require_once('../includes/database.php');

header('Content-Type: application/json');

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Sadece POST istekleri kabul edilir.']);
    exit;
}

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id']) || !validateSession($db)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
    exit;
}

// CSRF token kontrolü
$post_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($post_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Güvenlik hatası!']);
    exit;
}

// Dosya adı kontrolü
$file_name = $_POST['file_name'] ?? '';
if (empty($file_name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dosya adı belirtilmedi.']);
    exit;
}

// Güvenlik: Sadece dosya adı, path traversal saldırılarını engelle
$file_name = basename($file_name);
if (strpos($file_name, '..') !== false || strpos($file_name, '/') !== false || strpos($file_name, '\\') !== false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz dosya adı.']);
    exit;
}

// .htaccess ve sistem dosyalarını silmeyi engelle
if ($file_name === '.htaccess' || strpos($file_name, '.') === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bu dosya silinemez.']);
    exit;
}

// Upload klasörü
$upload_dir = dirname(__DIR__) . '/uploads/';
$file_path = $upload_dir . $file_name;

// Dosya var mı kontrol et
if (!file_exists($file_path)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Dosya bulunamadı.']);
    exit;
}

// Dosya klasör içinde mi kontrol et (güvenlik)
$real_upload_dir = realpath($upload_dir);
$real_file_path = realpath($file_path);
if (!$real_file_path || strpos($real_file_path, $real_upload_dir) !== 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz dosya yolu.']);
    exit;
}

// Dosyayı sil
if (unlink($file_path)) {
    echo json_encode([
        'success' => true,
        'message' => 'Dosya başarıyla silindi.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Dosya silinirken hata oluştu.']);
}
?>

