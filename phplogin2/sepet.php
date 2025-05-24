<?php
session_start();

// Kullanıcı giriş yapmamışsa yönlendir
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}

// Veritabanı bağlantısı
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Veritabanına bağlantı hatası: ' . mysqli_connect_error());
}

// Kullanıcının sepetindeki etkinlikleri çek
$sepet_sorgu = "SELECT e.etkinlikAdi, e.biletFiyati, e.paraBirimi, s.bilet_sayisi, e.id 
                FROM sepet s 
                JOIN etkinlikler e ON s.etkinlik_id = e.id 
                WHERE s.uye_id = {$_SESSION['account_id']}";

$sepet_sonuc = $con->query($sepet_sorgu);
$toplam_tutar = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sepetiniz</title>
    <style>
        body { text-align: center; background-color: #f4f4f4; font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        .button { padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; margin: 10px; font-size: 16px; }
        .delete-button { background-color: #dc3545; color: white; }
        .delete-button:hover { background-color: #c82333; }
    </style>
</head>
<body>

<div class="container">
    <h1>Sepetiniz</h1>
    <?php
    if ($sepet_sonuc->num_rows > 0) {
        while ($row = $sepet_sonuc->fetch_assoc()) {
            $etkinlik_tutari = $row["biletFiyati"] * $row["bilet_sayisi"];
            $toplam_tutar += $etkinlik_tutari;

            echo "<div class='etkinlik-item'>";
            echo "<h4>" . htmlspecialchars($row["etkinlikAdi"]) . "</h4>";
            echo "<p><b>Bilet Fiyatı:</b> " . htmlspecialchars($row["biletFiyati"]) . " " . htmlspecialchars($row["paraBirimi"]) . "</p>";
            echo "<p><b>Bilet Sayısı:</b> " . htmlspecialchars($row["bilet_sayisi"]) . "</p>";
            echo "<p><b>Toplam:</b> " . $etkinlik_tutari . " " . htmlspecialchars($row["paraBirimi"]) . "</p>";
            
            // Silme butonu ekleme
            echo "<form action='sepettenSil.php' method='post'>";
            echo "<input type='hidden' name='etkinlik_id' value='" . htmlspecialchars($row["id"]) . "'>";
            echo "<button type='submit' class='button delete-button'>Sepetten Çıkar</button>";
            echo "</form>";

            echo "</div>";
        }
        echo "<p><b>Toplam Tutar:</b> " . $toplam_tutar . " TL</p>";
    } else {
        echo "<p>Sepetinizde etkinlik bulunmamaktadır.</p>";
    }
    ?>
    
    <form action="odeme.php" method="post">
        <input type="hidden" name="toplam_tutar" value="<?= $toplam_tutar ?>">
        <button type="submit" class="button">Ödeme Yap</button>
    </form>
</div>

</body>
</html>

<?php
$con->close();
?>