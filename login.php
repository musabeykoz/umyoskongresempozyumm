<?php
require_once('includes/config.php');

$error = '';
$csrf_token = '';
$db = null;

// Veritabanı bağlantısını kontrol et
try {
    require_once('includes/database.php');
    require_once('includes/functions.php');
    $csrf_token = generateCSRFToken();
} catch(Exception $e) {
    $error = 'Veritabanı bağlantısı bulunamadı! Lütfen <a href="install/install_database.php" style="color: #721c24; text-decoration: underline;">kurulum sayfasını</a> çalıştırın.';
}

// Logout işlemi
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    if (function_exists('secureLogout')) {
        secureLogout();
    } else {
        session_destroy();
    }
    header('Location: login.php');
    exit;
}

// Eğer zaten giriş yapılmışsa admin paneline yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && $db) {
    if (function_exists('validateSession') && validateSession($db)) {
        header('Location: admin/index.php');
        exit;
    } else {
        if (function_exists('secureLogout')) {
            secureLogout();
        } else {
            session_destroy();
        }
    }
}

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($db)) {
    // CSRF token kontrolü
    $post_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($post_token)) {
        $error = 'Güvenlik hatası! Lütfen sayfayı yenileyip tekrar deneyin.';
    } else {
        $username = cleanInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip_address = getClientIP();
        
        // Brute force kontrolü
        if (checkBruteForce($db, $username, $ip_address)) {
            $error = 'Çok fazla başarısız giriş denemesi! Lütfen 15 dakika sonra tekrar deneyin.';
            logLoginAttempt($db, $username, $ip_address, false);
        } else {
            // Kullanıcı kontrolü
            try {
                $stmt = $db->prepare("SELECT id, username, password, email, full_name, is_active FROM admin_users WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && $user['is_active'] == 1 && password_verify($password, $user['password'])) {
                    // Başarılı giriş
                    session_regenerate_id(true);
                    
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['user_id'] = $user['id']; // Geriye dönük uyumluluk için
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_full_name'] = $user['full_name'];
                    $_SESSION['session_token'] = generateSessionToken();
                    $_SESSION['ip_address'] = $ip_address;
                    $_SESSION['login_time'] = time();
                    
                    // Son giriş zamanını güncelle
                    $stmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // Başarılı login denemesini kaydet
                    logLoginAttempt($db, $username, $ip_address, true);
                    
                    header('Location: admin/index.php');
                    exit;
                } else {
                    // Başarısız giriş
                    $error = 'Kullanıcı adı veya şifre hatalı!';
                    logLoginAttempt($db, $username, $ip_address, false);
                }
            } catch(PDOException $e) {
                $error = 'Bir hata oluştu! Lütfen daha sonra tekrar deneyin.';
                // Hata loglama (production'da log dosyasına yazılmalı)
                error_log("Login error: " . $e->getMessage());
            }
        }
    }
}

$page_title = getPageTitle('Yönetim Paneli Girişi');
require_once('includes/header.php');
?>

    <style>
        .login-wrapper {
            min-height: calc(100vh - 300px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .login-card {
            max-width: 500px;
            width: 100%;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo img {
            max-width: 120px;
            height: auto;
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .login-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .login-title i {
            margin-right: 10px;
        }
        
        .login-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .login-form-group {
            margin-bottom: 20px;
        }
        
        .login-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .login-label i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        .login-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .login-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        .login-button {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .login-button:hover {
            background-color: #002244;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .login-back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-back-link a {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .login-back-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .login-wrapper {
                padding: 20px 15px;
                min-height: calc(100vh - 250px);
            }
            
            .login-card {
                padding: 30px 20px;
            }
            
            .login-logo img {
                max-width: 100px;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 25px 15px;
            }
            
            .login-logo {
                margin-bottom: 20px;
            }
            
            .login-logo img {
                max-width: 80px;
                padding: 8px;
            }
            
            .login-title {
                font-size: 1.3rem;
            }
            
            .login-input {
                padding: 10px 12px;
                font-size: 0.95rem;
            }
            
            .login-button {
                padding: 12px;
                font-size: 1rem;
            }
        }
    </style>

    <main class="main-content">
        <div class="login-wrapper">
            <div class="login-card">
                <div class="login-logo">
                    <img src="<?php echo SITE_LOGO; ?>" alt="UMYOS Logo">
                </div>
                <h2 class="login-title">
                    <i class="fas fa-lock"></i> Yönetim Paneli Girişi
                </h2>
                
                <?php if ($error): ?>
                    <div class="login-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['expired']) && $_GET['expired'] == 1): ?>
                    <div class="login-error">
                        <i class="fas fa-clock"></i>
                        <span>Oturum süreniz doldu. Lütfen tekrar giriş yapın.</span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="loginForm"<?php echo $db ? '' : ' style="opacity: 0.5; pointer-events: none;"'; ?>>
                    <?php if ($csrf_token): ?>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <?php endif; ?>
                    
                    <div class="login-form-group">
                        <label for="username" class="login-label">
                            <i class="fas fa-user"></i> Kullanıcı Adı
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="login-input"
                            required 
                            placeholder="Kullanıcı adınızı girin"
                            autocomplete="username"
                            maxlength="50"
                        >
                    </div>
                    
                    <div class="login-form-group">
                        <label for="password" class="login-label">
                            <i class="fas fa-key"></i> Şifre
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="login-input"
                            required 
                            placeholder="Şifrenizi girin"
                            autocomplete="current-password"
                            maxlength="255"
                        >
                    </div>
                    
                    <button type="submit" class="login-button" id="loginButton">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Giriş Yap</span>
                    </button>
                </form>
                
                <script>
                    // Form gönderildiğinde butonu devre dışı bırak (çift gönderimi önle)
                    document.getElementById('loginForm').addEventListener('submit', function() {
                        const button = document.getElementById('loginButton');
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Giriş yapılıyor...</span>';
                    });
                </script>
                
                <div class="login-back-link">
                    <a href="<?php echo BASE_URL; ?>index.php">
                        <i class="fas fa-arrow-left"></i>
                        <span>Ana Sayfaya Dön</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once('includes/footer.php'); ?>

