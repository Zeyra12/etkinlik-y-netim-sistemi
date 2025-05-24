<?php
session_start();

// Veritabanı bağlantısı
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('MySQL bağlantı hatası: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etkinlik_id = $_POST['etkinlik_id'];
    $bilet_sayisi = $_POST['bilet_sayisi'];
    $kullanici_id = $_SESSION['account_id'];

    // Gelen verileri ekrana yazdır
    var_dump($etkinlik_id, $bilet_sayisi, $kullanici_id);

    // SQL sorgusunu hazırla
    $stmt = $con->prepare("INSERT INTO sepet (uye_id, etkinlik_id, bilet_sayisi) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        exit('SQL sorgusu hazırlanamadı: ' . $con->error);
    }

    $stmt->bind_param("iii", $kullanici_id, $etkinlik_id, $bilet_sayisi);

    if ($stmt->execute()) {
        echo "Başarıyla eklendi!";
    } else {
        echo "SQL hatası: " . $stmt->error;
    }

    $stmt->close();
}

$con->close();
?>














