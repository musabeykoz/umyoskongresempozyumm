TARİH: OCAK 2026
 
 🎥 Tanıtım Videosu

Projeyi 2 dakikada tanımak için videoyu izleyebilirsin:

▶️ YouTube: https://youtu.be/Nv1Re8MwxbA

Genel Bakış
UMYOS (Uluslararası Meslek Yüksekokulları Sempozyumu) 2025 Web Sitesi, akademik bir sempozyum etkinliğinin tüm yönetimini ve tanıtımını gerçekleştirmek için geliştirilmiş kapsamlı bir İçerik Yönetim Sistemi (CMS) projesidir.

🎯 Proje Amacı:
Bu sistem, bir sempozyum web sitesinin ihtiyaç duyduğu tüm özellikleri barındırır:

-Etkinlik bilgilerinin profesyonel sunumu
-İçeriklerin dinamik yönetimi
-Ziyaretçi bilgilendirme ve iletişim
-Belge ve dosya paylaşımı
-Haber/duyuru sistemi

🔧 Temel Mekanikler ve Özellikler
1. Güvenli Yönetici Paneli (Admin Dashboard):
   
  Sistem, gelişmiş güvenlik özellikleriyle donatılmış bir admin paneli içerir:
 
-Kimlik Doğrulama Sistemi:

-Güvenli giriş/çıkış mekanizması

-Şifrelerin hash'lenerek saklanması (PHP password_hash)

-Session yönetimi ve güvenlik kontrolleri

-Brute Force Koruması:

-IP bazlı deneme sayısı takibi

-15 dakika içinde 5 başarısız denemede hesap geçici kilitleme

-Login denemeleri veritabanında kayıt altına alınır

-CSRF (Cross-Site Request Forgery) Koruması:

-Token tabanlı form güvenliği

-Her oturum için benzersiz güvenlik token'ı

-Session Hijacking Önleme:

-IP adresi kontrolü

-Session token doğrulama


2. Gelişmiş Haber/Duyuru Sistemi
-Ana sayfada en son haber otomatik gösterim
-Haber listesi ve arşiv yönetimi
-Haber ekleme, düzenleme, silme, aktif/pasif yapma
-Modal Pop-up ile haber detay gösterimi

3. Dosya Yönetim Sistemi
-Güvenli ve güçlü bir dosya yükleme altyapısı:
-Güvenlik Kontrolleri:
-MIME type doğrulama
-Dosya uzantısı kontrolü
-Dosya boyutu sınırlandırması
-Tehlikeli dosya türlerinin engellenmesi (php, exe, sh vb.)
-Desteklenen Dosya Türleri:
-Resimler: JPG, PNG, GIF, WebP
-Dökümanlar: PDF, DOC, DOCX, XLS, XLSX
-Arşivler: ZIP, RAR


4. Güvenlik Mekanikleri
Çok katmanlı güvenlik yaklaşımı:
Input Validation: Tüm kullanıcı girdileri doğrulanır
Output Escaping: XSS saldırılarına karşı koruma
PDO Prepared Statements: SQL injection koruması
CSRF Token: Form güvenliği
Session Security: Güvenli oturum yönetimi
File Upload Security: Güvenli dosya yükleme
.htaccess Koruması: Kritik dosyaların korunması
Error Handling: Güvenli hata yönetimi
14. Kolay Kurulum Sistemi
Kullanıcı dostu kurulum süreci:
Tek SQL dosyası ile tüm veritabanı kurulumu
.env dosyası ile kolay yapılandırma
Otomatik admin kullanıcı oluşturma
Detaylı kurulum dökümanları (TR/EN)
Adım adım talimatlar

🛠️ Teknik Altyapı
Backend:
PHP 7.4+ (Object-oriented programlama)
PDO (PHP Data Objects) veritabanı soyutlaması
Session yönetimi
File system operasyonları
Frontend:
Modern HTML5
CSS3 (Custom properties, Flexbox, Grid)
Vanilla JavaScript (ES6+)
Font Awesome icons
Responsive design patterns
Veritabanı:
MySQL/MariaDB
UTF-8 encoding
İlişkisel veri modeli
Güvenlik:
HTTPS desteği
CSRF koruması
XSS filtreleme
SQL injection önleme
Brute force koruması

🚀 Performans ve Optimizasyon
Veritabanı sorgu optimizasyonu
Lazy loading görseller
Minified CSS/JS (production için)
Caching stratejileri
Optimize edilmiş dosya yapısı


🎓 Kullanım Alanları
Bu sistem sadece sempozyumlar için değil, aşağıdaki etkinlikler için de kullanılabilir:
Konferanslar
Kongreler
Workshoplar
Akademik toplantılar
Kurs ve eğitim programları
Fuarlar ve sergiler



🤝 Katkıda Bulunanlar

 👤Öğretim üyesi-Dr.Tuğrul aktaş-
 
 👤Geliştirici-Musa Beykoz-

------------------------------------------

GitHub: https://github.com/musabeykoz

LinkedIn: https://linkedin.com/in/musabeykoz

Web: https://musabeykoz.com

⭐ Destek Ol

Projeyi beğendiysen yıldız atmayı unutma ⭐ Bu, projeyi geliştirmem için büyük motivasyon sağlar!

