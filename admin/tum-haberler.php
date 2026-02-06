<?php
require_once('../includes/config.php');
require_once('../includes/auth.php');
require_once('../includes/database.php');

$page_title = 'Tüm Haberler Yönetimi - ' . SITE_TITLE;

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
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'add' || $action === 'edit') {
                $baslik = trim($_POST['baslik'] ?? '');
                $icerik = $_POST['icerik'] ?? '';
                $aktif = isset($_POST['aktif']) ? 1 : 0;
                
                if (!empty($baslik) && !empty($icerik)) {
                    try {
                        if ($action === 'add') {
                            $stmt = $db->prepare("INSERT INTO umyos_haberler (baslik, icerik, aktif) VALUES (?, ?, ?)");
                            $stmt->execute([$baslik, $icerik, $aktif]);
                            $success_message = 'Haber başarıyla eklendi.';
                        } else {
                            $id = intval($_POST['id'] ?? 0);
                            $stmt = $db->prepare("UPDATE umyos_haberler SET baslik = ?, icerik = ?, aktif = ? WHERE id = ?");
                            $stmt->execute([$baslik, $icerik, $aktif, $id]);
                            $success_message = 'Haber başarıyla güncellendi.';
                        }
                    } catch (PDOException $e) {
                        $error_message = 'Hata: ' . $e->getMessage();
                    }
                } else {
                    $error_message = 'Lütfen başlık ve içerik alanlarını doldurun.';
                }
            } elseif ($action === 'delete') {
                $id = intval($_POST['id'] ?? 0);
                if ($id > 0) {
                    try {
                        $stmt = $db->prepare("DELETE FROM umyos_haberler WHERE id = ?");
                        $stmt->execute([$id]);
                        $success_message = 'Haber başarıyla silindi.';
                    } catch (PDOException $e) {
                        $error_message = 'Hata: ' . $e->getMessage();
                    }
                }
            }
        }
    }
}

// Düzenleme için veri çekme
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM umyos_haberler WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch();
}

// Tüm haberleri çekme
$stmt = $db->query("SELECT * FROM umyos_haberler ORDER BY created_at DESC");
$haberler = $stmt->fetchAll();
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
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 200px;
            resize: vertical;
        }
        .form-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 8px;
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
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        table tr:hover {
            background-color: #f8f9fa;
        }
        table td {
            max-width: 400px;
            word-wrap: break-word;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .action-buttons .btn {
            padding: 6px 12px;
            font-size: 0.9rem;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
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
            .table-container {
                overflow-x: scroll;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-left">
                <h1><i class="fas fa-newspaper"></i> Tüm Haberler Yönetimi</h1>
                <div class="admin-breadcrumb">
                    <a href="index.php"><i class="fas fa-home"></i> Yönetim Paneli</a>
                    <span class="separator">/</span>
                    <span class="current">Tüm Haberler</span>
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
                    <i class="fas fa-<?php echo $edit_data ? 'edit' : 'plus'; ?>"></i>
                    <?php echo $edit_data ? 'Haber Düzenle' : 'Yeni Haber Ekle'; ?>
                </h2>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                    <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Başlık <span class="required">*</span></label>
                        <input type="text" name="baslik" value="<?php echo htmlspecialchars($edit_data['baslik'] ?? ''); ?>" required maxlength="500">
                    </div>
                    
                    <div class="form-group">
                        <label>İçerik <span class="required">*</span></label>
                        <textarea id="icerik" name="icerik" required><?php echo htmlspecialchars($edit_data['icerik'] ?? ''); ?></textarea>
                        <small>Metin düzenleyiciyi kullanarak içeriğinizi biçimlendirebilirsiniz.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="aktif" <?php echo ($edit_data['aktif'] ?? 1) ? 'checked' : ''; ?>>
                            Aktif
                        </label>
                    </div>
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_data ? 'Güncelle' : 'Kaydet'; ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="tum-haberler.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> İptal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Liste Bölümü -->
            <div class="form-section">
                <h2><i class="fas fa-list"></i> Haber Listesi</h2>
                <?php if (empty($haberler)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Henüz haber eklenmemiş.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Başlık</th>
                                    <th>İçerik (Özet)</th>
                                    <th>Eklenme Tarihi</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($haberler as $haber): 
                                    $eklenme_tarihi = date('d.m.Y H:i:s', strtotime($haber['created_at']));
                                    // İçeriği temizle ve özet oluştur
                                    $allowed_tags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><span><div><blockquote><hr>';
                                    $clean_content = strip_tags($haber['icerik'], $allowed_tags);
                                    if (!preg_match('/<[^>]+>/', trim($haber['icerik']))) {
                                        $clean_content = htmlspecialchars(trim($haber['icerik']));
                                    }
                                    $ozet_icerik = mb_substr($clean_content, 0, 100, 'UTF-8');
                                    if (mb_strlen($clean_content, 'UTF-8') > 100) {
                                        $ozet_icerik .= '...';
                                    }
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($haber['baslik']); ?></strong></td>
                                        <td><?php echo $ozet_icerik; ?></td>
                                        <td><?php echo htmlspecialchars($eklenme_tarihi); ?></td>
                                        <td>
                                            <?php if ($haber['aktif']): ?>
                                                <span class="badge badge-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?edit=<?php echo $haber['id']; ?>" class="btn btn-success">
                                                    <i class="fas fa-edit"></i> Düzenle
                                                </a>
                                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Bu haberi silmek istediğinize emin misiniz?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $haber['id']; ?>">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i> Sil
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php require_once('../includes/rich-text-editor.php'); ?>
</body>
</html>
