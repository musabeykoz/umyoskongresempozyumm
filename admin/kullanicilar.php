<?php
require_once('../includes/config.php');
require_once('../includes/auth.php');
require_once('../includes/database.php');

$page_title = 'Kullanıcı Yönetimi - ' . SITE_TITLE;

// Mesaj değişkenleri
$success_message = '';
$error_message = '';

// Kullanıcı ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    // CSRF token kontrolü
    $post_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($post_token)) {
        $error_message = 'Güvenlik hatası! Lütfen sayfayı yenileyip tekrar deneyin.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($username) || empty($password)) {
            $error_message = 'Kullanıcı adı ve şifre zorunludur.';
        } elseif (strlen($password) < 6) {
            $error_message = 'Şifre en az 6 karakter olmalıdır.';
        } else {
        try {
            // Kullanıcı adı kontrolü
            $check_stmt = $db->prepare("SELECT id FROM admin_users WHERE username = ?");
            $check_stmt->execute([$username]);
            if ($check_stmt->fetch()) {
                $error_message = 'Bu kullanıcı adı zaten kullanılıyor.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO admin_users (username, password, email, full_name, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $email ?: null, $full_name ?: null, $is_active]);
                $success_message = 'Kullanıcı başarıyla eklendi.';
            }
        } catch (PDOException $e) {
            $error_message = 'Hata: ' . $e->getMessage();
        }
        }
    }
}

// Mevcut kullanıcı ID'sini al (geriye dönük uyumluluk için)
$current_user_id = 0;
if (isset($_SESSION) && is_array($_SESSION)) {
    if (array_key_exists('admin_id', $_SESSION) && !empty($_SESSION['admin_id'])) {
        $current_user_id = intval($_SESSION['admin_id']);
    } elseif (array_key_exists('user_id', $_SESSION) && !empty($_SESSION['user_id'])) {
        $current_user_id = intval($_SESSION['user_id']);
    }
}

// Kullanıcı silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // CSRF token kontrolü
    $post_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($post_token)) {
        $error_message = 'Güvenlik hatası! Lütfen sayfayı yenileyip tekrar deneyin.';
    } else {
        $user_id = intval($_POST['user_id'] ?? 0);
        
        if ($user_id > 0) {
        try {
            // Kendi hesabını silmeyi engelle
            if ($user_id == $current_user_id) {
                $error_message = 'Kendi hesabınızı silemezsiniz.';
            } else {
                $stmt = $db->prepare("DELETE FROM admin_users WHERE id = ?");
                $stmt->execute([$user_id]);
                $success_message = 'Kullanıcı başarıyla silindi.';
            }
        } catch (PDOException $e) {
            $error_message = 'Hata: ' . $e->getMessage();
        }
        }
    }
}

// Şifre değiştirme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    // CSRF token kontrolü
    $post_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($post_token)) {
        $error_message = 'Güvenlik hatası! Lütfen sayfayı yenileyip tekrar deneyin.';
    } else {
        $user_id = intval($_POST['user_id'] ?? 0);
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($new_password) || empty($confirm_password)) {
            $error_message = 'Yeni şifre ve şifre tekrarı zorunludur.';
        } elseif (strlen($new_password) < 6) {
            $error_message = 'Şifre en az 6 karakter olmalıdır.';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'Şifreler eşleşmiyor.';
        } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
            $success_message = 'Şifre başarıyla güncellendi.';
        } catch (PDOException $e) {
            $error_message = 'Hata: ' . $e->getMessage();
        }
        }
    }
}

// Kullanıcı durumu değiştirme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    // CSRF token kontrolü
    $post_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($post_token)) {
        $error_message = 'Güvenlik hatası! Lütfen sayfayı yenileyip tekrar deneyin.';
    } else {
        $user_id = intval($_POST['user_id'] ?? 0);
        
        if ($user_id > 0) {
        try {
            // Kendi hesabını pasif yapmayı engelle
            if ($user_id == $current_user_id) {
                $error_message = 'Kendi hesabınızı pasif yapamazsınız.';
            } else {
                $stmt = $db->prepare("UPDATE admin_users SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$user_id]);
                $success_message = 'Kullanıcı durumu başarıyla güncellendi.';
            }
        } catch (PDOException $e) {
            $error_message = 'Hata: ' . $e->getMessage();
        }
        }
    }
}

// Tüm kullanıcıları çekme
try {
    $stmt = $db->query("SELECT id, username, email, full_name, is_active, last_login, created_at FROM admin_users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $error_message = 'Kullanıcılar yüklenirken hata oluştu: ' . $e->getMessage();
}
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
        .admin-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .form-section h2 {
            margin-top: 0;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        .form-group label .required {
            color: #dc3545;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        .form-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-primary:hover {
            background-color: #002244;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 0.9rem;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .users-table th,
        .users-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .users-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--primary-color);
        }
        .users-table tr:hover {
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h3 {
            margin: 0;
            color: var(--primary-color);
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .users-table {
                font-size: 0.9rem;
            }
            .users-table th,
            .users-table td {
                padding: 10px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <h1><i class="fas fa-users-cog"></i> Kullanıcı Yönetimi</h1>
            </div>
            <div class="admin-header-right">
                <a href="index.php" class="admin-site-link">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </div>
        
        <div class="admin-content">
            <?php if ($success_message): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Kullanıcı Ekleme Formu -->
            <div class="form-section">
                <h2><i class="fas fa-user-plus"></i> Yeni Kullanıcı Ekle</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Kullanıcı Adı <span class="required">*</span></label>
                            <input type="text" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Şifre <span class="required">*</span></label>
                            <input type="password" name="password" required minlength="6">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>E-posta</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>Ad Soyad</label>
                            <input type="text" name="full_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" checked>
                            Aktif
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Kullanıcı Ekle
                    </button>
                </form>
            </div>
            
            <!-- Kullanıcı Listesi -->
            <div class="form-section">
                <h2><i class="fas fa-list"></i> Kullanıcı Listesi</h2>
                <?php if (empty($users)): ?>
                    <p>Henüz kullanıcı bulunmamaktadır.</p>
                <?php else: ?>
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Kullanıcı Adı</th>
                                <th>Ad Soyad</th>
                                <th>E-posta</th>
                                <th>Durum</th>
                                <th>Son Giriş</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['full_name'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($user['email'] ?: '-'); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $user['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $user['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : '-'; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openPasswordModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" class="btn btn-secondary btn-small">
                                                <i class="fas fa-key"></i> Şifre Değiştir
                                            </button>
                                            <?php if ($user['id'] != $current_user_id): ?>
                                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Kullanıcı durumunu değiştirmek istediğinize emin misiniz?');">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-secondary btn-small">
                                                        <i class="fas fa-toggle-<?php echo $user['is_active'] ? 'on' : 'off'; ?>"></i> <?php echo $user['is_active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>
                                                    </button>
                                                </form>
                                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz? Bu işlem geri alınamaz!');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-small">
                                                        <i class="fas fa-trash"></i> Sil
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Şifre Değiştirme Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-key"></i> Şifre Değiştir</h3>
                <span class="close" onclick="closePasswordModal()">&times;</span>
            </div>
            <form method="POST" action="" id="passwordForm">
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                <input type="hidden" name="user_id" id="password_user_id">
                <div class="form-group">
                    <label>Kullanıcı: <strong id="password_username"></strong></label>
                </div>
                <div class="form-group">
                    <label>Yeni Şifre <span class="required">*</span></label>
                    <input type="password" name="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Şifre Tekrar <span class="required">*</span></label>
                    <input type="password" name="confirm_password" required minlength="6">
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closePasswordModal()" class="btn btn-secondary">İptal</button>
                    <button type="submit" class="btn btn-primary">Şifreyi Değiştir</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openPasswordModal(userId, username) {
            document.getElementById('password_user_id').value = userId;
            document.getElementById('password_username').textContent = username;
            document.getElementById('passwordModal').style.display = 'block';
        }
        
        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('passwordForm').reset();
        }
        
        // Modal dışına tıklanınca kapat
        window.onclick = function(event) {
            const modal = document.getElementById('passwordModal');
            if (event.target == modal) {
                closePasswordModal();
            }
        }
        
        // ESC tuşu ile kapat
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePasswordModal();
            }
        });
    </script>
</body>
</html>

