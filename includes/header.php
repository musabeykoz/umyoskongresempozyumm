<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/config.php');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="UMYOS 2025 - 13. Uluslararası Meslek Yüksekokulları Sempozyumu">
    <meta name="keywords" content="UMYOS, Meslek Yüksekokulu, Sempozyum, Bursa, Uludağ Üniversitesi">
    <meta name="theme-color" content="#003366">
    <title><?php echo isset($page_title) ? $page_title : getPageTitle(); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (isset($extra_styles)) echo $extra_styles; ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>index.php" class="header-link">
                <div class="header-top">
                    <div class="logo-section">
                        <img src="<?php echo SITE_LOGO; ?>" alt="UMYOS Logo" class="main-logo">
                    </div>
                    <div class="header-title">
                        <h1><?php echo SITE_TITLE; ?></h1>
                        <p><?php echo SITE_SUBTITLE; ?></p>
                    </div>
                </div>
            </a>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link">
                        Genel Bilgiler <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo BASE_URL; ?>pages/gecmis-sempozyumlar.php">Geçmiş Sempozyumlar</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/hakkimizda.php">Hakkımızda</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/ev-sahibi.php">Ev Sahibi</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/davet-metni.php">Davet Metni</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/yayin-imkanlari.php">Yayın İmkanları</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/sempozyum-programi.php">Sempozyum Programı</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/iletisim.php">İletişim</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/sempozyum-gezisi.php">Sempozyum Gezisi</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/konaklama.php">Konaklama</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/ulasim.php">Ulaşım</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link">
                        Kurullar <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo BASE_URL; ?>pages/duzenleme-kurulu.php">Düzenleme Kurulu</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/danisma-kurulu.php">Danışma Kurulu</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/bilim-kurulu.php">Bilim Kurulu</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link">
                        Eğitim/Atölye <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo BASE_URL; ?>pages/egitimler.php">Eğitimler</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link">
                        Başvuru ve Kayıt <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="https://openconf.com" target="_blank">Eser Gönderme</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/onemli-tarihler.php">Önemli Tarihler</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/bildiri-poster-sablonlari.php">Bildiri/Poster Şablonları</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/sergi-hakkinda.php">Sergi Hakkında</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/sergi-kurallari.php">Sergi Kuralları</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/kayit-ucretleri.php">Kayıt Ücretleri</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

