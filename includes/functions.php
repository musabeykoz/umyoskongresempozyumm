<?php
// Güvenlik ve Yardımcı Fonksiyonlar

/**
 * CSRF Token oluştur
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token doğrula
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * IP adresini al
 */
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * Brute force kontrolü
 */
function checkBruteForce($db, $username, $ip_address) {
    // Son 15 dakikadaki başarısız denemeleri kontrol et
    $time_limit = date('Y-m-d H:i:s', strtotime('-15 minutes'));
    
    $stmt = $db->prepare("
        SELECT COUNT(*) as attempts 
        FROM login_attempts 
        WHERE username = ? 
        AND ip_address = ? 
        AND success = 0 
        AND attempt_time > ?
    ");
    $stmt->execute([$username, $ip_address, $time_limit]);
    $result = $stmt->fetch();
    
    // 5 başarısız denemeden fazla ise engelle
    return $result['attempts'] >= 5;
}

/**
 * Login denemesini kaydet
 */
function logLoginAttempt($db, $username, $ip_address, $success) {
    $stmt = $db->prepare("INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, ?)");
    $stmt->execute([$username, $ip_address, $success ? 1 : 0]);
}

/**
 * Session güvenliği için token oluştur
 */
function generateSessionToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Session hijacking kontrolü
 */
function validateSession($db) {
    // Session ID ve IP kontrolü
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
        return false;
    }
    
    // IP adresi kontrolü (opsiyonel - çok katı olabilir)
    // if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== getClientIP()) {
    //     return false;
    // }
    
    // Kullanıcı aktif mi kontrol et
    $stmt = $db->prepare("SELECT is_active FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user || !$user['is_active']) {
        return false;
    }
    
    return true;
}

/**
 * Güvenli çıkış
 */
function secureLogout() {
    // Session verilerini temizle
    $_SESSION = array();
    
    // Session cookie'sini sil
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Session'ı yok et
    session_destroy();
}

/**
 * XSS koruması için temizleme
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>

