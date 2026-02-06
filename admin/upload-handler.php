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

// Upload klasörünü oluştur
$upload_dir = dirname(__DIR__) . '/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Dosya yükleme kontrolü
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dosya yükleme hatası.']);
    exit;
}

$file = $_FILES['file'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_error = $file['error'];

// Dosya boyutu kontrolü (10MB limit)
$max_size = 10 * 1024 * 1024; // 10MB
if ($file_size > $max_size) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dosya boyutu çok büyük. Maksimum 10MB olabilir.']);
    exit;
}

// İzin verilen dosya uzantıları (sadece evrak/dosya tipleri)
$allowed_extensions = [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
    'zip', 'rar', '7z',
    'jpg', 'jpeg', 'png', 'gif',
    'txt', 'rtf'
];

// Zararlı dosya uzantıları (kesinlikle engelle)
$dangerous_extensions = [
    'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
    'exe', 'bat', 'cmd', 'com', 'scr', 'vbs', 'js',
    'sh', 'py', 'pl', 'rb', 'jar', 'war',
    'asp', 'aspx', 'jsp', 'jspx', 'cgi',
    'htaccess', 'htpasswd', 'ini', 'conf'
];

// Dosya uzantısını kontrol et
$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Zararlı uzantı kontrolü
if (in_array($file_extension, $dangerous_extensions)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Güvenlik nedeniyle bu dosya tipi yüklenemez.']);
    exit;
}

// İzin verilen uzantı kontrolü
if (!in_array($file_extension, $allowed_extensions)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Sadece belge, arşiv ve resim dosyaları yüklenebilir.']);
    exit;
}

// MIME type kontrolü (ek güvenlik)
$allowed_mime_types = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/zip',
    'application/x-rar-compressed',
    'application/x-rar',
    'application/x-7z-compressed',
    'application/x-zip-compressed',
    'image/jpeg',
    'image/png',
    'image/gif',
    'text/plain',
    'application/rtf'
];

$file_type = mime_content_type($file_tmp);
if (!in_array($file_type, $allowed_mime_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'İzin verilmeyen dosya tipi.']);
    exit;
}

// Dosya içeriği kontrolü (PHP kod içeriyor mu?)
if ($file_extension === 'txt' || $file_extension === 'html') {
    $file_content = file_get_contents($file_tmp);
    // PHP tag kontrolü
    if (preg_match('/<\?php|<\?=|<\?/i', $file_content)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dosya içeriği güvenlik nedeniyle reddedildi.']);
        exit;
    }
}

// Güvenli dosya adı oluştur (uzantı zaten kontrol edildi)
$safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
$safe_name = substr($safe_name, 0, 100); // Maksimum 100 karakter
$new_file_name = $safe_name . '_' . time() . '.' . $file_extension;
$target_path = $upload_dir . $new_file_name;

// Dosyayı yükle
if (move_uploaded_file($file_tmp, $target_path)) {
    $file_url = BASE_URL . 'uploads/' . $new_file_name;
    echo json_encode([
        'success' => true,
        'message' => 'Dosya başarıyla yüklendi.',
        'file_name' => $new_file_name,
        'original_name' => $file_name,
        'file_url' => $file_url,
        'file_size' => $file_size
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Dosya yükleme başarısız.']);
}
?>

