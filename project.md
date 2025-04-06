# Proje Yönetim Sistemi Dokümantasyonu

## Proje Özeti
Modern ve kullanıcı dostu bir proje yönetim sistemi. Codeigniter 4 ve MySQL kullanılarak geliştirilmiş, Bootstrap 5 ile tasarlanmış web tabanlı bir uygulama. Tüm veri işlemleri AJAX teknolojisi kullanılarak gerçekleştirilecektir.

## Teknik Özellikler
- Codeigniter 4 (composer kullanmadan )
- Veritabanı: MySQL
- Frontend Framework: Bootstrap 5
- Grafik Kütüphanesi: Chart.js
- AJAX Framework: jQuery AJAX
- Tarih Formatı: DD/MM/YYYY
- Para Birimi: TL (₺)

## Veritabanı Yapısı

### users
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### customers
```sql
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### categories
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### projects
```sql
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    task TEXT,
    priority ENUM('Düşük', 'Orta', 'Yüksek', 'Acil') NOT NULL,
    status ENUM('Ödeme Bekliyor', 'Başlamadı', 'Devam Ediyor', 'Tamamlandı', 'Beklemede') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### payments
```sql
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);
```

### project_notes
```sql
CREATE TABLE project_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);
```

## Modüller ve Özellikleri

### 1. Dashboard
- Son 5 proje listesi
- En çok projeye sahip müşteriler
- Metrik kartları:
  - Toplam proje ücreti
  - Alınan proje ücretleri
  - Kalan proje ücretleri
  - Toplam proje sayısı
  - Tamamlanan proje sayısı
  - Devam eden proje sayısı
  - Bekleyen proje sayısı
  - Ödeme bekleyen proje sayısı
- Aylık istatistik grafikleri
- Yıllık/Aylık filtreleme

### 2. Projeler
- Proje listesi
- Detaylı proje bilgileri
- Düzenleme ve not ekleme özellikleri
- Filtreleme ve arama
- Kategori bazlı filtreleme

### 3. Proje Ekleme/Düzenleme
- Müşteri seçimi
- Kategori seçimi
- Görev atama
- Ücret belirleme
- Tarih seçimi
- Durum ve öncelik belirleme

### 4. Proje Notları
- Not ekleme/düzenleme
- Not geçmişi
- Tarih bazlı filtreleme

### 5. Raporlama
- Aylık/yıllık raporlar
- Grafiksel analizler
- PDF export
- Excel export

#### Gelişmiş Raporlama Özellikleri

##### 1. Kategori Bazlı Analizler
- **Kategori Dağılımı**: Projelerin kategorilere göre dağılımını gösteren pasta grafik
- **Kategori Performansı**: Her kategorinin ortalama tamamlanma süresi, ortalama bütçe aşımı gibi metrikleri
- **Kategori Karlılığı**: Kategori bazında kâr marjı analizi

##### 2. Müşteri Bazlı Analizler
- **Müşteri Segmentasyonu**: Müşterileri proje sayısı, bütçe büyüklüğü veya ödeme düzenliliğine göre segmentasyonu
- **Müşteri Sadakati**: Tekrarlayan müşterilerin analizi ve sadakat programı önerileri
- **Müşteri Karlılığı**: Müşteri bazında kârlılık analizi (hangi müşteriler daha kârlı)

##### 3. Zaman Bazlı Analizler
- **Proje Süre Analizi**: Planlanan süre ile gerçekleşen süre arasındaki farkların analizi
- **Gecikme Analizi**: Geciken projelerin nedenleri ve etkileri
- **Sezonsal Trendler**: Yıl içindeki sezonsal proje artış/azalış trendleri

##### 4. Finansal Analizler
- **Nakit Akışı Tahmini**: Gelecek aylar için beklenen nakit akışı tahmini
- **Bütçe Aşımı Analizi**: Bütçe aşımı yaşanan projelerin analizi
- **Kâr Marjı Analizi**: Proje bazında ve genel kâr marjı analizi
- **Ödeme Performansı**: Müşterilerin ödeme performansı ve gecikme analizi

##### 5. Performans Göstergeleri (KPI)
- **Proje Başarı Oranı**: Zamanında ve bütçe dahilinde tamamlanan projelerin oranı
- **Müşteri Memnuniyet Skoru**: Müşteri geri bildirimlerine dayalı memnuniyet skoru
- **Kaynak Kullanım Verimliliği**: Ekip üyelerinin proje bazında verimlilik analizi
- **Proje Dönüşüm Oranı**: Teklif aşamasından proje aşamasına geçiş oranı

##### 6. Karşılaştırmalı Analizler
- **Yıl Bazında Karşılaştırma**: Mevcut yıl ile önceki yılların karşılaştırması
- **Hedef-Gerçekleşen Karşılaştırması**: Yıllık hedefler ile gerçekleşen değerlerin karşılaştırması
- **Sektör Ortalamaları**: Sektör ortalamaları ile karşılaştırma (eğer veri mevcutsa)

##### 7. İnteraktif Raporlama Araçları
- **Filtreleme Seçenekleri**: Tarih aralığı, kategori, müşteri, durum gibi çeşitli filtreleme seçenekleri

##### 8. Tahminsel Analizler
- **Proje Tamamlanma Tahmini**: Devam eden projelerin tamamlanma tarihi tahmini
- **Gelir Tahmini**: Gelecek dönemler için gelir tahmini
- **Risk Analizi**: Proje risklerinin tahmin edilmesi ve erken uyarı sistemi

##### 9. Dışa Aktarma ve Paylaşım Özellikleri
- **PDF/Excel Raporları**: Detaylı raporların PDF veya Excel formatında dışa aktarılması
- **Otomatik Rapor Gönderimi**: Belirli aralıklarla otomatik rapor gönderimi
- **Rapor Arşivi**: Geçmiş raporların arşivlenmesi ve karşılaştırılması

##### 10. Görselleştirme İyileştirmeleri
- **Heat Map**: Proje yoğunluğunu gösteren ısı haritası
- **Gantt Chart**: Proje zaman çizelgelerini gösteren Gantt şeması
- **Bubble Chart**: Çok boyutlu veri görselleştirme (örn: büyüklük, süre, kâr)
- **Gauge Charts**: Hedef-gerçekleşen karşılaştırması için gösterge grafikleri

### 6. İstatistikler
- Detaylı metrikler
- Grafiksel analizler
- Performans göstergeleri

## AJAX İmplementasyonu

### Genel AJAX Yapısı
```javascript
$.ajax({
    url: 'modules/[modül_adı]/[işlem].php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
        // Başarılı işlem
    },
    error: function(xhr, status, error) {
        // Hata durumu
    }
});
```

### AJAX ile Gerçekleştirilecek İşlemler

#### Dashboard
- Metrik kartlarının güncellenmesi
- Grafik verilerinin yüklenmesi
- Filtreleme işlemleri

#### Projeler
- Proje listesinin yüklenmesi
- Proje ekleme/düzenleme
- Proje silme
- Proje durumu güncelleme
- Proje filtreleme ve arama

#### Müşteriler
- Müşteri listesinin yüklenmesi
- Müşteri ekleme/düzenleme
- Müşteri silme
- Müşteri arama

#### Ödemeler
- Ödeme kayıtlarının yüklenmesi
- Ödeme ekleme
- Ödeme düzenleme
- Ödeme silme

#### Notlar
- Not ekleme/düzenleme
- Not silme
- Not listesinin yüklenmesi

#### Raporlar
- Rapor verilerinin yüklenmesi
- Filtreleme işlemleri
- PDF/Excel export işlemleri

### AJAX Güvenlik Önlemleri
- CSRF token kontrolü
- Input validasyonu
- Rate limiting
- Session kontrolü
- XSS koruması

### AJAX Response Format
```json
{
    "status": "success|error",
    "message": "İşlem mesajı",
    "data": {
        // İşlem sonucu verileri
    }
}
```

## Güvenlik Önlemleri
- Şifre hashleme (password_hash)
- SQL injection koruması
- XSS koruması
- CSRF token kullanımı
- Session yönetimi
- Input validasyonu

## Kurulum
1. Dosyaları web sunucusuna yükleyin
2. Veritabanını oluşturun
3. config/database.php dosyasını düzenleyin
4. Gerekli tabloları oluşturun
5. Admin kullanıcısı oluşturun

## Test Senaryoları
1. Kullanıcı girişi
2. Proje ekleme/düzenleme
3. Ödeme kayıtları
4. Rapor oluşturma
5. Not ekleme/düzenleme

## Performans Optimizasyonları
- Veritabanı indeksleme
- Sayfalama sistemi
- Önbellek mekanizması
- Resim optimizasyonu
- CSS/JS minification

## Görsel Tasarım Prensipleri
- Modern ve minimalist arayüz
- Responsive tasarım
- Kullanıcı dostu navigasyon
- Tutarlı renk şeması
- Okunabilir tipografi
- Görsel hiyerarşi
- Beyaz alan kullanımı
- Görsel geri bildirimler

## Renk Paleti
- Ana Renk: #2c3e50
- İkincil Renk: #3498db
- Başarı: #2ecc71
- Uyarı: #f1c40f
- Tehlike: #e74c3c
- Arka Plan: #f8f9fa
- Metin: #2c3e50
- Açık Metin: #7f8c8d 

## Örnek Veriler (Dummy Data)

Aşağıdaki SQL sorguları, veritabanı tablolarını doldurmak için kullanılabilecek örnek kayıtları içerir:

### Kullanıcılar (users)
```sql
INSERT INTO users (username, password, email, created_at) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', '2023-01-01 10:00:00'),
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user1@example.com', '2023-01-02 11:30:00'),
('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user2@example.com', '2023-01-03 09:15:00');
```
Not: Tüm kullanıcıların şifresi "password" olarak ayarlanmıştır (hash edilmiş hali).

### Müşteriler (customers)
```sql
INSERT INTO customers (name, email, phone, address, created_at) VALUES
('ABC Şirketi', 'info@abc.com', '0212 555 1234', 'İstanbul, Türkiye', '2023-01-05 14:20:00'),
('XYZ Limited', 'contact@xyz.com', '0216 444 5678', 'Ankara, Türkiye', '2023-01-10 16:45:00'),
('Tech Solutions', 'info@techsolutions.com', '0232 333 9012', 'İzmir, Türkiye', '2023-01-15 13:10:00'),
('Global Trading', 'info@globaltrading.com', '0242 222 3456', 'Antalya, Türkiye', '2023-01-20 11:30:00'),
('Digital Systems', 'contact@digitalsystems.com', '0352 111 7890', 'Kayseri, Türkiye', '2023-01-25 15:40:00'),
('Smart Innovations', 'info@smartinnovations.com', '0312 999 2345', 'İstanbul, Türkiye', '2023-02-01 10:15:00'),
('Future Technologies', 'contact@futuretech.com', '0224 888 6789', 'Bursa, Türkiye', '2023-02-05 14:50:00'),
('Data Analytics', 'info@dataanalytics.com', '0322 777 1234', 'Konya, Türkiye', '2023-02-10 09:25:00'),
('Cloud Services', 'contact@cloudservices.com', '0218 666 5678', 'İstanbul, Türkiye', '2023-02-15 16:30:00'),
('Mobile Apps', 'info@mobileapps.com', '0236 555 9012', 'Samsun, Türkiye', '2023-02-20 12:40:00');
```

### Kategoriler (categories)
```sql
INSERT INTO categories (name, description, created_at) VALUES
('Web Geliştirme', 'Web siteleri ve web uygulamaları geliştirme projeleri', '2023-01-01 10:00:00'),
('Mobil Uygulama', 'iOS ve Android mobil uygulamalar geliştirme projeleri', '2023-01-01 10:00:00'),
('E-Ticaret', 'E-ticaret platformları ve çözümleri', '2023-01-01 10:00:00'),
('Yazılım Entegrasyonu', 'Farklı yazılım sistemlerinin entegrasyonu', '2023-01-01 10:00:00'),
('Veri Analizi', 'Veri analizi ve raporlama projeleri', '2023-01-01 10:00:00'),
('Siber Güvenlik', 'Güvenlik çözümleri ve sistemleri', '2023-01-01 10:00:00'),
('Bulut Hizmetleri', 'Bulut tabanlı çözümler ve hizmetler', '2023-01-01 10:00:00'),
('Yapay Zeka', 'Yapay zeka ve makine öğrenimi projeleri', '2023-01-01 10:00:00'),
('IoT', 'Nesnelerin İnterneti projeleri', '2023-01-01 10:00:00'),
('Dijital Pazarlama', 'Dijital pazarlama ve sosyal medya projeleri', '2023-01-01 10:00:00');
```

### Projeler (projects)
```sql
INSERT INTO projects (customer_id, category_id, name, description, task, priority, status, start_date, end_date, total_amount, paid_amount, created_at) VALUES
(1, 1, 'E-ticaret Web Sitesi', 'ABC Şirketi için e-ticaret web sitesi geliştirme projesi', 'Responsive tasarım, ürün yönetimi, ödeme sistemi entegrasyonu', 'Yüksek', 'Tamamlandı', '2023-01-10', '2023-02-15', 25000.00, 25000.00, '2023-01-10 09:00:00'),
(1, 2, 'Mobil Uygulama', 'ABC Şirketi için iOS ve Android mobil uygulama geliştirme', 'Kullanıcı arayüzü tasarımı, API entegrasyonu, push bildirimler', 'Orta', 'Devam Ediyor', '2023-02-01', '2023-04-30', 35000.00, 17500.00, '2023-02-01 10:30:00'),
(2, 1, 'Kurumsal Web Sitesi', 'XYZ Limited için kurumsal web sitesi yenileme projesi', 'Modern tasarım, içerik yönetim sistemi, SEO optimizasyonu', 'Düşük', 'Tamamlandı', '2023-01-15', '2023-02-28', 15000.00, 15000.00, '2023-01-15 11:45:00'),
(2, 5, 'Veri Analiz Sistemi', 'XYZ Limited için veri analiz ve raporlama sistemi', 'Veri görselleştirme, raporlama araçları, dashboard tasarımı', 'Yüksek', 'Devam Ediyor', '2023-03-01', '2023-05-15', 40000.00, 20000.00, '2023-03-01 14:20:00'),
(3, 4, 'CRM Sistemi', 'Tech Solutions için müşteri ilişkileri yönetim sistemi', 'Müşteri takibi, satış yönetimi, raporlama modülleri', 'Acil', 'Ödeme Bekliyor', '2023-02-10', '2023-04-30', 30000.00, 0.00, '2023-02-10 16:10:00'),
(3, 10, 'E-posta Pazarlama Kampanyası', 'Tech Solutions için e-posta pazarlama kampanyası', 'E-posta şablonları, abonelik sistemi, analitik raporlar', 'Orta', 'Başlamadı', '2023-04-01', '2023-05-15', 8000.00, 0.00, '2023-03-15 09:30:00'),
(4, 4, 'Lojistik Takip Sistemi', 'Global Trading için lojistik takip sistemi', 'Araç takibi, rota optimizasyonu, teslimat yönetimi', 'Yüksek', 'Devam Ediyor', '2023-02-15', '2023-06-30', 45000.00, 22500.00, '2023-02-15 13:40:00'),
(4, 4, 'Muhasebe Yazılımı', 'Global Trading için muhasebe yazılımı entegrasyonu', 'Fatura yönetimi, raporlama, vergi hesaplamaları', 'Orta', 'Beklemede', '2023-05-01', '2023-07-15', 20000.00, 5000.00, '2023-04-10 11:20:00'),
(5, 7, 'Eğitim Platformu', 'Digital Systems için online eğitim platformu', 'Video içerik yönetimi, quiz sistemi, sertifika oluşturma', 'Yüksek', 'Devam Ediyor', '2023-03-01', '2023-08-31', 60000.00, 30000.00, '2023-03-01 15:50:00'),
(5, 2, 'Mobil Oyun', 'Digital Systems için mobil oyun geliştirme', 'Oyun mekanikleri, grafik tasarımı, ses efektleri', 'Orta', 'Başlamadı', '2023-06-01', '2023-09-30', 35000.00, 0.00, '2023-05-15 10:30:00'),
(6, 10, 'Sosyal Medya Yönetim Aracı', 'Smart Innovations için sosyal medya yönetim aracı', 'İçerik planlama, analitik raporlar, otomatik paylaşım', 'Yüksek', 'Devam Ediyor', '2023-03-10', '2023-07-15', 40000.00, 20000.00, '2023-03-10 14:20:00'),
(6, 7, 'Webinar Platformu', 'Smart Innovations için webinar platformu', 'Canlı yayın, kayıt yönetimi, etkileşim araçları', 'Orta', 'Beklemede', '2023-07-01', '2023-09-30', 25000.00, 5000.00, '2023-06-10 09:40:00'),
(7, 9, 'IoT Sensör Ağı', 'Future Technologies için IoT sensör ağı kurulumu', 'Sensör entegrasyonu, veri toplama, alarm sistemi', 'Acil', 'Devam Ediyor', '2023-04-01', '2023-08-15', 55000.00, 27500.00, '2023-04-01 11:30:00'),
(7, 8, 'Yapay Zeka Analiz Sistemi', 'Future Technologies için yapay zeka analiz sistemi', 'Veri işleme, model eğitimi, tahmin algoritmaları', 'Yüksek', 'Başlamadı', '2023-08-01', '2023-12-31', 70000.00, 0.00, '2023-07-15 16:20:00'),
(8, 6, 'Veri Merkezi Güvenlik Sistemi', 'Data Analytics için veri merkezi güvenlik sistemi', 'Erişim kontrolü, izleme sistemi, alarm entegrasyonu', 'Acil', 'Devam Ediyor', '2023-05-01', '2023-09-30', 48000.00, 24000.00, '2023-05-01 13:10:00'),
(8, 5, 'Büyük Veri Analiz Platformu', 'Data Analytics için büyük veri analiz platformu', 'Veri işleme, görselleştirme, raporlama araçları', 'Yüksek', 'Beklemede', '2023-09-01', '2023-12-15', 65000.00, 10000.00, '2023-08-10 10:50:00'),
(9, 7, 'Bulut Depolama Çözümü', 'Cloud Services için bulut depolama çözümü', 'Dosya yönetimi, paylaşım sistemi, yedekleme', 'Orta', 'Devam Ediyor', '2023-06-01', '2023-10-15', 35000.00, 17500.00, '2023-06-01 14:30:00'),
(9, 7, 'Sanal Sunucu Altyapısı', 'Cloud Services için sanal sunucu altyapısı', 'Sunucu yönetimi, ölçeklendirme, izleme sistemi', 'Yüksek', 'Başlamadı', '2023-10-01', '2023-12-31', 42000.00, 0.00, '2023-09-15 11:40:00'),
(10, 2, 'Mobil Uygulama Geliştirme', 'Mobile Apps için mobil uygulama geliştirme', 'UI/UX tasarımı, geliştirme, test ve dağıtım', 'Orta', 'Devam Ediyor', '2023-07-01', '2023-11-30', 45000.00, 22500.00, '2023-07-01 15:20:00'),
(10, 2, 'Oyun Geliştirme Projesi', 'Mobile Apps için oyun geliştirme projesi', 'Oyun tasarımı, geliştirme, test ve dağıtım', 'Yüksek', 'Beklemede', '2023-11-01', '2024-02-28', 55000.00, 5000.00, '2023-10-10 09:30:00');
```

### Ödemeler (payments)
```sql
INSERT INTO payments (project_id, amount, payment_date, description, created_at) VALUES
(1, 10000.00, '2023-01-15', 'Proje başlangıç ödemesi', '2023-01-15 10:30:00'),
(1, 15000.00, '2023-02-15', 'Proje tamamlanma ödemesi', '2023-02-15 14:20:00'),
(2, 17500.00, '2023-02-15', 'Proje ilerleme ödemesi', '2023-02-15 16:40:00'),
(3, 7500.00, '2023-01-20', 'Proje başlangıç ödemesi', '2023-01-20 11:10:00'),
(3, 7500.00, '2023-02-28', 'Proje tamamlanma ödemesi', '2023-02-28 15:30:00'),
(4, 20000.00, '2023-03-15', 'Proje ilerleme ödemesi', '2023-03-15 13:50:00'),
(6, 5000.00, '2023-04-01', 'Proje başlangıç ödemesi', '2023-04-01 10:20:00'),
(7, 22500.00, '2023-03-15', 'Proje ilerleme ödemesi', '2023-03-15 16:10:00'),
(8, 5000.00, '2023-05-01', 'Proje başlangıç ödemesi', '2023-05-01 09:40:00'),
(9, 30000.00, '2023-04-15', 'Proje ilerleme ödemesi', '2023-04-15 14:30:00'),
(11, 20000.00, '2023-04-15', 'Proje ilerleme ödemesi', '2023-04-15 11:20:00'),
(12, 5000.00, '2023-07-01', 'Proje başlangıç ödemesi', '2023-07-01 15:40:00'),
(13, 27500.00, '2023-05-15', 'Proje ilerleme ödemesi', '2023-05-15 10:50:00'),
(15, 5000.00, '2023-08-01', 'Proje başlangıç ödemesi', '2023-08-01 13:30:00'),
(16, 24000.00, '2023-06-15', 'Proje ilerleme ödemesi', '2023-06-15 16:20:00'),
(17, 10000.00, '2023-09-01', 'Proje başlangıç ödemesi', '2023-09-01 09:10:00'),
(18, 17500.00, '2023-07-15', 'Proje ilerleme ödemesi', '2023-07-15 14:40:00'),
(19, 5000.00, '2023-10-01', 'Proje başlangıç ödemesi', '2023-10-01 11:30:00'),
(20, 22500.00, '2023-08-15', 'Proje ilerleme ödemesi', '2023-08-15 15:50:00'),
(21, 5000.00, '2023-11-01', 'Proje başlangıç ödemesi', '2023-11-01 10:20:00');
```

### Proje Notları (project_notes)
```sql
INSERT INTO project_notes (project_id, note, created_at) VALUES
(1, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-01-10 09:30:00'),
(1, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-01-20 14:20:00'),
(1, 'Geliştirme aşaması tamamlandı. Test aşamasına geçildi.', '2023-02-05 11:40:00'),
(1, 'Test aşaması tamamlandı. Proje teslim edildi.', '2023-02-15 16:30:00'),
(2, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-02-01 10:15:00'),
(2, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-02-15 13:50:00'),
(2, 'iOS uygulaması geliştirme aşaması tamamlandı. Android geliştirmeye başlandı.', '2023-03-15 15:20:00'),
(3, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-01-15 11:30:00'),
(3, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-01-25 14:40:00'),
(3, 'Geliştirme aşaması tamamlandı. Test aşamasına geçildi.', '2023-02-10 09:50:00'),
(3, 'Test aşaması tamamlandı. Proje teslim edildi.', '2023-02-28 16:10:00'),
(4, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-03-01 10:40:00'),
(4, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-03-15 13:20:00'),
(4, 'Veri görselleştirme modülü tamamlandı. Raporlama modülüne geçildi.', '2023-04-15 15:50:00'),
(5, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-02-10 11:20:00'),
(5, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-02-20 14:30:00'),
(6, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-03-15 10:50:00'),
(6, 'E-posta şablonları hazırlandı. Abonelik sistemi geliştiriliyor.', '2023-03-25 13:40:00'),
(7, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-02-15 11:10:00'),
(7, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-03-01 14:20:00'),
(7, 'Sensör entegrasyonu tamamlandı. Veri toplama sistemine geçildi.', '2023-04-15 16:30:00'),
(8, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-05-01 10:30:00'),
(8, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-05-15 13:50:00'),
(9, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-03-01 11:40:00'),
(9, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-03-15 14:20:00'),
(9, 'Video içerik yönetimi modülü tamamlandı. Quiz sistemine geçildi.', '2023-04-15 15:50:00'),
(10, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-05-15 10:20:00'),
(10, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-05-30 13:40:00'),
(11, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-03-10 11:30:00'),
(11, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-03-25 14:50:00'),
(11, 'İçerik planlama modülü tamamlandı. Analitik raporlara geçildi.', '2023-04-25 16:20:00'),
(12, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-06-10 10:40:00'),
(12, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-06-25 13:30:00'),
(13, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-04-01 11:20:00'),
(13, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-04-15 14:40:00'),
(13, 'Sensör entegrasyonu tamamlandı. Veri toplama sistemine geçildi.', '2023-05-15 15:50:00'),
(14, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-07-15 10:30:00'),
(14, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-07-30 13:50:00'),
(15, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-05-01 11:40:00'),
(15, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-05-15 14:20:00'),
(15, 'Erişim kontrolü modülü tamamlandı. İzleme sistemine geçildi.', '2023-06-15 16:30:00'),
(16, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-08-10 10:50:00'),
(16, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-08-25 13:40:00'),
(17, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-06-01 11:30:00'),
(17, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-06-15 14:50:00'),
(17, 'Dosya yönetimi modülü tamamlandı. Paylaşım sistemine geçildi.', '2023-07-15 15:20:00'),
(18, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-09-15 10:40:00'),
(18, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-09-30 13:30:00'),
(19, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-07-01 11:20:00'),
(19, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-07-15 14:40:00'),
(19, 'UI/UX tasarımı tamamlandı. Geliştirme aşamasına geçildi.', '2023-08-15 15:50:00'),
(20, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-10-10 10:30:00'),
(20, 'Tasarım aşaması tamamlandı. Müşteri onayı bekleniyor.', '2023-10-25 13:50:00'),
(21, 'Müşteri ile ilk toplantı yapıldı. Proje gereksinimleri belirlendi.', '2023-08-01 11:40:00'),
(21, 'Tasarım aşaması tamamlandı. Müşteri onayı alındı.', '2023-08-15 14:20:00'),
(21, 'Oyun mekanikleri tamamlandı. Grafik tasarımına geçildi.', '2023-09-15 16:30:00');
``` 

## Proje Dosya Yapısı

```
project/
├── app/
│   ├── Controllers/
│   │   ├── Home.php
│   │   ├── Auth.php
│   │   ├── Projects.php
│   │   ├── Customers.php
│   │   ├── Payments.php
│   │   ├── Categories.php
│   │   └── Reports.php
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── CustomerModel.php
│   │   ├── ProjectModel.php
│   │   ├── PaymentModel.php
│   │   ├── CategoryModel.php
│   │   └── ProjectNoteModel.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   └── main.php
│   │   ├── auth/
│   │   │   └── login.php
│   │   ├── projects/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   ├── customers/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   ├── categories/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   ├── payments/
│   │   │   ├── index.php
│   │   │   └── create.php
│   │   └── reports/
│   │       └── index.php
│   ├── Helpers/
│   │   ├── auth_helper.php
│   │   ├── project_helper.php
│   │   ├── payment_helper.php
│   │   └── report_helper.php
│   ├── Config/
│   └── Database/
├── public/
│   └── assets/
│       ├── css/
│       ├── js/
│       └── img/
└── project.md 