# Proje Yönetim Sistemi

<div align="center">
  <img src="https://img.shields.io/badge/Proje_Yönetim_Sistemi-1.0-blue?style=for-the-badge&logo=codeigniter&logoColor=white" alt="Proje Yönetim Sistemi Logo">
  
  [![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
  [![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.0-orange.svg)](https://codeigniter.com)
  [![MySQL](https://img.shields.io/badge/MySQL-5.7+-blue.svg)](https://www.mysql.com)
  [![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple.svg)](https://getbootstrap.com)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
</div>

## 📋 İçindekiler
- [Genel Bakış](#genel-bakış)
- [Özellikler](#özellikler)
- [Ekran Görüntüleri](#ekran-görüntüleri)
- [Teknik Özellikler](#teknik-özellikler)
- [Kurulum](#kurulum)
- [Sistem Gereksinimleri](#sistem-gereksinimleri)
- [Güvenlik Önlemleri](#güvenlik-önlemleri)
- [Katkıda Bulunma](#katkıda-bulunma)
- [Lisans](#lisans)

## 🔍 Genel Bakış

Modern ve kullanıcı dostu bir proje yönetim sistemi. Codeigniter 4 ve MySQL kullanılarak geliştirilmiş, Bootstrap 5 ile tasarlanmış web tabanlı bir uygulama. Bu sistem, projelerinizi, müşterilerinizi ve ödemelerinizi tek bir platformda yönetmenizi sağlar.

## ✨ Özellikler

### 1. 📊 Dashboard
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

### 2. 📋 Projeler
- Proje listesi
- Detaylı proje bilgileri
- Düzenleme ve not ekleme özellikleri
- Filtreleme ve arama
- Kategori bazlı filtreleme

### 3. ➕ Proje Ekleme/Düzenleme
- Müşteri seçimi
- Kategori seçimi
- Görev atama
- Ücret belirleme
- Tarih seçimi
- Durum ve öncelik belirleme

### 4. 📝 Proje Notları
- Not ekleme/düzenleme
- Not geçmişi
- Tarih bazlı filtreleme

### 5. 📈 Raporlama
- Aylık/yıllık raporlar
- Grafiksel analizler
- PDF export
- Excel export

#### 📊 Gelişmiş Raporlama Özellikleri
- **Kategori Bazlı Analizler**: Projelerin kategorilere göre dağılımı ve performansı
- **Müşteri Bazlı Analizler**: Müşteri segmentasyonu, sadakati ve karlılığı
- **Zaman Bazlı Analizler**: Proje süre analizi, gecikme analizi ve sezonsal trendler
- **Finansal Analizler**: Nakit akışı tahmini, bütçe aşımı ve kâr marjı analizi
- **Performans Göstergeleri (KPI)**: Proje başarı oranı, müşteri memnuniyeti ve kaynak verimliliği
- **Karşılaştırmalı Analizler**: Yıl bazında karşılaştırma ve hedef-gerçekleşen analizi
- **İnteraktif Raporlama Araçları**: Çeşitli filtreleme seçenekleri
- **Tahminsel Analizler**: Proje tamamlanma tahmini, gelir tahmini ve risk analizi
- **Dışa Aktarma ve Paylaşım Özellikleri**: PDF/Excel raporları ve otomatik rapor gönderimi
- **Görselleştirme İyileştirmeleri**: Heat Map, Gantt Chart, Bubble Chart ve Gauge Charts

## 🖼️ Ekran Görüntüleri

<div align="center">
  <img src="https://img.shields.io/badge/Dashboard-1.0-blue?style=for-the-badge&logo=chart.js&logoColor=white" alt="Dashboard" width="200">
  <img src="https://img.shields.io/badge/Projeler-1.0-blue?style=for-the-badge&logo=codeigniter&logoColor=white" alt="Projeler" width="200">
  <img src="https://img.shields.io/badge/Raporlar-1.0-blue?style=for-the-badge&logo=chart.js&logoColor=white" alt="Raporlar" width="200">
  <img src="https://img.shields.io/badge/Müşteriler-1.0-blue?style=for-the-badge&logo=codeigniter&logoColor=white" alt="Müşteriler" width="200">
</div>

## 🛠️ Teknik Özellikler
- **Backend**: Codeigniter 4 (composer kullanmadan)
- **Veritabanı**: MySQL
- **Frontend Framework**: Bootstrap 5
- **Grafik Kütüphanesi**: Chart.js
- **AJAX Framework**: jQuery AJAX
- **Tarih Formatı**: DD/MM/YYYY
- **Para Birimi**: TL (₺)

## ⚙️ Kurulum

### 1. Dosyaları web sunucusuna yükleyin

### 2. Veritabanını oluşturun

### 3. `app/Config/Database.php` dosyasını düzenleyin:
```php
public $default = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'your_database',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => true,
    'charset'  => 'utf8',
    'DBCollat' => 'utf8_general_ci',
    'swapPre'  => '',
    'encrypt'  => false,
    'compress' => false,
    'strictOn' => false,
    'failover' => [],
    'port'     => 3306,
];
```

### 4. Gerekli tabloları oluşturun:
```sql
-- users tablosu
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- customers tablosu
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- categories tablosu
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- projects tablosu
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

-- payments tablosu
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

-- project_notes tablosu
CREATE TABLE project_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);
```

### 5. Admin kullanıcısı oluşturun:
```sql
INSERT INTO users (username, password, email, created_at) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', '2023-01-01 10:00:00');
```
> **Not**: Şifre "password" olarak ayarlanmıştır. Güvenlik için ilk girişten sonra değiştirmeniz önerilir.

## 💻 Sistem Gereksinimleri

- **PHP**: 8.1 veya üzeri
- **MySQL**: 5.7 veya üzeri
- **Web Sunucusu**: Apache/Nginx
- **Apache Modülleri**: mod_rewrite (Apache için)

## 🔒 Güvenlik Önlemleri
- **Şifre Güvenliği**: Şifre hashleme (password_hash)
- **Veritabanı Güvenliği**: SQL injection koruması
- **Web Güvenliği**: XSS koruması
- **Form Güvenliği**: CSRF token kullanımı
- **Oturum Yönetimi**: Güvenli session yönetimi
- **Veri Doğrulama**: Input validasyonu

## 🤝 Katkıda Bulunma

Projeye katkıda bulunmak isterseniz:

1. Bu depoyu fork edin
2. Yeni bir branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Bir Pull Request oluşturun

## 📄 Lisans
Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

---

<div align="center">
  <p>Proje Yönetim Sistemi &copy; 2025</p>
</div>
