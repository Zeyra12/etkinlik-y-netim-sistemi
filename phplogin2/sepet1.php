<?php
// Oturum başlat
session_start();

// Kullanıcı giriş yapmamışsa yönlendir
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}

// MySQL veritabanı bağlantısı
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Veritabanına bağlantı hatası: ' . mysqli_connect_error());
}

// POST ile gelen verileri al
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["etkinlik_id"], $_POST["uye_id"], $_POST["bilet_sayisi"])) {
    $etkinlik_id = intval($_POST["etkinlik_id"]);
    $kullanici_id = mysqli_real_escape_string($con, $_POST["account_id"]);
    $bilet_sayisi = intval($_POST["bilet_sayisi"]);

    // Sepete ekleme sorgusu
    $ekle_sorgu = "INSERT INTO sepet (uye_id, etkinlik_id, bilet_sayisi) VALUES ($kullanici_id, $etkinlik_id, $bilet_sayisi)";
      
    if ($con->query($ekle_sorgu) === TRUE) {
        echo "Etkinlik başarıyla sepete eklendi.";
    } else {
        echo "Sepete ekleme hatası: " . $con->error;
    }
}

// Kullanıcının sepetindeki etkinlikleri çekelim;
$sepet_sorgu = "SELECT e.etkinlikAdi, e.biletFiyati, e.paraBirimi, s.bilet_sayisi, e.id 
                FROM sepet s 
                JOIN etkinlikler e ON s.etkinlik_id = e.id 
                WHERE s.uye_id = ($_SESSION[account_id])";
                

$sepet_sonuc = $con->query($sepet_sorgu);

// Toplam tutarı hesapla
$toplam_tutar = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <title>Sepetiniz</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .etkinlik-item {
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .toplam {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }
        .odeme {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .odeme:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<header class="header">
    <h1>Sepetiniz</h1>
</header>

<div class="container">
    <?php
    $paraBirimi = "TL"; // Varsayılan para birimi
    if ($sepet_sonuc->num_rows > 0) {
        while ($row = $sepet_sonuc->fetch_assoc()) {
            $etkinlik_tutari = $row["biletFiyati"] * $row["bilet_sayisi"];
            $toplam_tutar += $etkinlik_tutari;

            echo "<div class='etkinlik-item'>";
            echo "<h4>" . htmlspecialchars($row["etkinlikAdi"]) . "</h4>";
            echo "<p><b>Bilet Fiyatı:</b> " . htmlspecialchars($row["biletFiyati"]) . " " . htmlspecialchars($row["paraBirimi"]) . "</p>";
            echo "<p><b>Bilet Sayısı:</b> " . htmlspecialchars($row["bilet_sayisi"]) . "</p>";
            echo "<p><b>Etkinlik Tutarı:</b> " . $etkinlik_tutari . " " . htmlspecialchars($row["paraBirimi"]) . "</p>";
            echo "</div>";
        }
        echo "<p class='toplam'>Toplam Tutar: " . $toplam_tutar . " " . $paraBirimi . "</p>";
    } else {
        echo "<p>Sepetinizde etkinlik bulunmamaktadır.</p>";
    }
    ?>
    
    <!-- Ödeme Yap Butonu -->
    <form action="odeme.php" method="post">
        <input type="hidden" name="toplam_tutar" value="<?= $toplam_tutar ?>">
        <button type="submit" class="odeme">Ödeme Yap</button>
    </form>
</div>

</body>
</html>

<?php
$con->close();
?>