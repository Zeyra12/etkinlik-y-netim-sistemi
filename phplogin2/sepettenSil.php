<?php
session_start();

// Veritabanı bağlantısı
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Veritabanına bağlantı hatası: ' . mysqli_connect_error());
}

// Silme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["etkinlik_id"])) {
    $etkinlik_id = intval($_POST["etkinlik_id"]);
    $kullanici_id = $_SESSION['account_id'];

    // Silmeden önce bilet sayısını çek
    $sorgu = $con->prepare("SELECT bilet_sayisi FROM sepet WHERE etkinlik_id = ? AND uye_id = ?");
    $sorgu->bind_param("ii", $etkinlik_id, $kullanici_id);
    $sorgu->execute();
    $sorgu->bind_result($bilet_sayisi);
    $sorgu->fetch();
    $sorgu->close();

    if ($bilet_sayisi) {
        // Kontenjanı güncelle
        $stmt = $con->prepare("UPDATE etkinlikler SET kontenjan = kontenjan + ? WHERE id = ?");
        $stmt->bind_param("ii", $bilet_sayisi, $etkinlik_id);
        $stmt->execute();
        $stmt->close();

        // Etkinliği sepetten sil
        $stmt = $con->prepare("DELETE FROM sepet WHERE etkinlik_id = ? AND uye_id = ?");
        $stmt->bind_param("ii", $etkinlik_id, $kullanici_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Sepete geri dön
header('Location: sepet.php');
exit;
?>