<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $title ?> - <?= $year ?>/<?= $month ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        h3 {
            font-size: 16px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .summary-box h3 {
            margin-top: 0;
        }
        .summary-item {
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
        }
        .chart-container {
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <h1>Proje Yönetim Sistemi - Rapor</h1>
    <p style="text-align: center;"><?= $year ?> Yılı <?= $month ?>. Ay</p>
    
    <div class="summary-box">
        <h3>Özet Bilgiler</h3>
        <div class="summary-item">
            <span class="summary-label">Toplam Proje:</span> <?= $monthlyStats['totalProjects'] ?>
        </div>
        <div class="summary-item">
            <span class="summary-label">Tamamlanan Proje:</span> <?= $monthlyStats['completedProjects'] ?>
        </div>
        <div class="summary-item">
            <span class="summary-label">Toplam Tutar:</span> <?= number_format($monthlyStats['totalAmount'], 2, ',', '.') ?> ₺
        </div>
        <div class="summary-item">
            <span class="summary-label">Ödenen Tutar:</span> <?= number_format($monthlyStats['paidAmount'], 2, ',', '.') ?> ₺
        </div>
        <div class="summary-item">
            <span class="summary-label">Bekleyen Tutar:</span> <?= number_format($monthlyStats['pendingAmount'], 2, ',', '.') ?> ₺
        </div>
    </div>
    
    <h2>Kategori Bazlı İstatistikler</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Toplam Proje</th>
                <th>Tamamlanan Proje</th>
                <th>Toplam Tutar</th>
                <th>Ödenen Tutar</th>
                <th>Bekleyen Tutar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categoryStats as $category): ?>
            <tr>
                <td><?= $category['name'] ?></td>
                <td><?= $category['totalProjects'] ?></td>
                <td><?= $category['completedProjects'] ?></td>
                <td><?= number_format($category['totalAmount'], 2, ',', '.') ?> ₺</td>
                <td><?= number_format($category['paidAmount'], 2, ',', '.') ?> ₺</td>
                <td><?= number_format($category['pendingAmount'], 2, ',', '.') ?> ₺</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="page-break"></div>
    
    <h2>Tahminsel Analizler</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Tamamlanma Süresi (Gün)</th>
                <th>Bütçe Aşımı (%)</th>
                <th>Başarı Oranı (%)</th>
                <th>Günlük Kaynak İhtiyacı (₺)</th>
                <th>Toplam Kaynak İhtiyacı (₺)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($predictive_analysis as $category => $predictions): ?>
            <tr>
                <td><?= $category ?></td>
                <td><?= $predictions['completion_time'] ?></td>
                <td><?= number_format($predictions['budget_overrun']['overrun_percentage'], 2, ',', '.') ?></td>
                <td><?= number_format($predictions['success_rate']['success_rate'], 2, ',', '.') ?></td>
                <td><?= number_format($predictions['resource_need']['daily_cost'], 2, ',', '.') ?></td>
                <td><?= number_format($predictions['resource_need']['total_cost'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="page-break"></div>
    
    <h2>Müşteri Analizi</h2>
    <table>
        <thead>
            <tr>
                <th>Müşteri</th>
                <th>Toplam Proje</th>
                <th>Tamamlanan Proje</th>
                <th>Toplam Tutar</th>
                <th>Ödenen Tutar</th>
                <th>Bekleyen Tutar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customerAnalysis['customers'] as $customer): ?>
            <tr>
                <td><?= $customer['name'] ?></td>
                <td><?= $customer['totalProjects'] ?></td>
                <td><?= $customer['completedProjects'] ?></td>
                <td><?= number_format($customer['totalAmount'], 2, ',', '.') ?> ₺</td>
                <td><?= number_format($customer['paidAmount'], 2, ',', '.') ?> ₺</td>
                <td><?= number_format($customer['pendingAmount'], 2, ',', '.') ?> ₺</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Bu rapor <?= date('d.m.Y H:i:s') ?> tarihinde oluşturulmuştur.</p>
        <p>Proje Yönetim Sistemi &copy; <?= date('Y') ?></p>
    </div>
</body>
</html> 