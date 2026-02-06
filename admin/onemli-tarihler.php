<?php
require_once('../includes/config.php');
require_once('../includes/auth.php');
require_once('../includes/database.php');

$page_title = 'Önemli Tarihler Yönetimi - ' . SITE_TITLE;

// Mesaj değişkenleri
$success_message = '';
$error_message = '';

// Form işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token kontrolü
    $post_token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($post_token)) {
        $error_message = 'Güvenlik hatası! Lütfen sayfayı yenileyip tekrar deneyin.';
    } else {
        $icerik = $_POST['icerik'] ?? '';
        
        // Sadece tamamen boş olup olmadığını kontrol et (sadece boşluk karakterleri varsa da kabul et)
        if (strlen(trim($icerik)) > 0) {
        try {
            // Önce kayıt var mı kontrol et
            $check_stmt = $db->query("SELECT id FROM umyos_onemli_tarihler LIMIT 1");
            $existing = $check_stmt->fetch();
            
            if ($existing) {
                // Varsa güncelle
                $stmt = $db->prepare("UPDATE umyos_onemli_tarihler SET icerik = ? WHERE id = ?");
                $stmt->execute([$icerik, $existing['id']]);
            } else {
                // Yoksa ekle
                $stmt = $db->prepare("INSERT INTO umyos_onemli_tarihler (icerik) VALUES (?)");
                $stmt->execute([$icerik]);
            }
            
            $success_message = 'Önemli Tarihler içeriği başarıyla güncellendi.';
        } catch (PDOException $e) {
            $error_message = 'Hata: ' . $e->getMessage();
        }
        } else {
            $error_message = 'Lütfen içerik alanını doldurun.';
        }
    }
}

// Mevcut içeriği çekme
try {
    $stmt = $db->query("SELECT * FROM umyos_onemli_tarihler LIMIT 1");
    $onemli_tarihler = $stmt->fetch();
    if (!$onemli_tarihler) {
        $onemli_tarihler = ['icerik' => ''];
    }
} catch (PDOException $e) {
    $onemli_tarihler = ['icerik' => ''];
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
    <?php require_once('../includes/rich-text-editor.php'); ?>
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
            flex: 1;
        }
        .admin-header h1 {
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
        }
        .admin-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            opacity: 0.9;
            flex-wrap: wrap;
        }
        .admin-breadcrumb a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        .admin-breadcrumb a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
        .admin-breadcrumb .separator {
            opacity: 0.7;
        }
        .admin-breadcrumb .current {
            opacity: 1;
            font-weight: 600;
        }
        .admin-back {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            white-space: nowrap;
        }
        .admin-back:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .admin-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            min-height: 400px;
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
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
            min-height: 300px;
            resize: vertical;
            line-height: 1.6;
        }
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 0.9rem;
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
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .preview-section {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin-top: 30px;
        }
        .preview-section h2 {
            margin-top: 0;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .preview-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            line-height: 1.8;
            word-wrap: break-word;
        }
        
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .admin-header h1 {
                font-size: 1.2rem;
            }
            .admin-breadcrumb {
                font-size: 0.85rem;
            }
            .form-group textarea {
                min-height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <h1><i class="fas fa-calendar-alt"></i> Önemli Tarihler Yönetimi</h1>
                <div class="admin-breadcrumb">
                    <a href="index.php"><i class="fas fa-home"></i> Yönetim Paneli</a>
                    <span class="separator">/</span>
                    <span class="current">Önemli Tarihler</span>
                </div>
            </div>
            <a href="index.php" class="admin-back">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
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
            
            <!-- Form Bölümü -->
            <div class="form-section">
                <h2>
                    <i class="fas fa-edit"></i> Önemli Tarihler İçeriğini Düzenle
                </h2>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                    <div class="form-group">
                        <label>İçerik <span class="required">*</span></label>
                        <textarea id="icerik" name="icerik" required><?php echo htmlspecialchars($onemli_tarihler['icerik']); ?></textarea>
                        <small>Metin düzenleyiciyi kullanarak içeriğinizi biçimlendirebilirsiniz. Satır boşlukları otomatik olarak korunacaktır.</small>
                    </div>
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="<?php echo BASE_URL; ?>pages/onemli-tarihler.php" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-eye"></i> Sayfayı Görüntüle
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Önizleme Bölümü -->
            <?php if (!empty($onemli_tarihler['icerik'])): ?>
                <div class="preview-section">
                    <h2><i class="fas fa-eye"></i> Önizleme</h2>
                    <div class="preview-content">
                        <?php 
                        // Güvenli HTML tag'lerine izin ver
                        $allowed_tags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><span><div><blockquote><hr><table><tr><td><th><thead><tbody><tfoot>';
                        // HTML içeriğini temizle ve göster
                        $preview_content = strip_tags(trim($onemli_tarihler['icerik']), $allowed_tags);
                        // Eğer içerik HTML değilse (düz metin), satır sonlarını <br> ile değiştir
                        if (!preg_match('/<[^>]+>/', trim($onemli_tarihler['icerik']))) {
                            $preview_content = nl2br(htmlspecialchars(trim($onemli_tarihler['icerik'])));
                        }
                        echo $preview_content;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
