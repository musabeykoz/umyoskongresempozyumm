<?php
require_once('../includes/config.php');
require_once('../includes/database.php');

$page_title = getPageTitle('Geçmiş Sempozyumlar');
require_once('../includes/header.php');

// Aktif sempozyumları veritabanından çekme
try {
    $stmt = $db->prepare("SELECT * FROM umyos_gecmis_sempozyumlar WHERE aktif = 1 ORDER BY yil DESC");
    $stmt->execute();
    $sempozyumlar = $stmt->fetchAll();
} catch (PDOException $e) {
    $sempozyumlar = [];
}
?>

    <main class="main-content">
        <div class="container full-width">
            <div class="content-area">
                <div class="news-card">
                    <h2 class="news-title">
                        <i class="fas fa-history"></i> Geçmiş Sempozyumlar
                    </h2>
                    <div class="news-content">
                        <p>
                            Uluslararası Meslek Yüksekokulları Sempozyumu (UMYOS), 2013 yılından bu yana 
                            düzenli olarak gerçekleştirilen ve meslek yüksekokullarının akademik gelişimine 
                            katkı sağlayan önemli bir bilimsel platform olarak yerini almıştır.
                        </p>
                        
                        <?php if (!empty($sempozyumlar)): ?>
                            <h3 style="color: var(--primary-color); margin-top: 25px; margin-bottom: 15px;">
                                Önceki Sempozyumlar
                            </h3>
                            
                            <?php foreach ($sempozyumlar as $sempozyum): ?>
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 4px solid var(--secondary-color);">
                                    <h4 style="color: var(--secondary-color); margin-top: 0;">
                                        <?php echo htmlspecialchars($sempozyum['sempozyum_no']); ?>. UMYOS - <?php echo htmlspecialchars($sempozyum['yil']); ?>
                                    </h4>
                                    <?php if (!empty($sempozyum['ev_sahibi'])): ?>
                                        <p style="margin: 8px 0;">
                                            <strong>Ev Sahibi:</strong> <?php echo htmlspecialchars($sempozyum['ev_sahibi']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($sempozyum['tema'])): ?>
                                        <p style="margin: 8px 0;">
                                            <strong>Tema:</strong> <?php echo htmlspecialchars($sempozyum['tema']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($sempozyum['aciklama'])): ?>
                                        <p style="margin: 8px 0;">
                                            <?php echo nl2br(htmlspecialchars($sempozyum['aciklama'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <h3 style="color: var(--primary-color); margin-top: 25px; margin-bottom: 15px;">
                                Önceki Sempozyumlar
                            </h3>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; text-align: center; color: #6c757d;">
                                <p style="margin: 0;">
                                    <i class="fas fa-info-circle"></i> Henüz geçmiş sempozyum bilgisi eklenmemiş.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="<?php echo BASE_URL; ?>index.php" class="btn-all-news">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php require_once('../includes/footer.php'); ?>
