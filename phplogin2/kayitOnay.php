<?php
// Oturum başlatıyoruz;
session_start();
// Kullanıcı giriş yapmadıysa index.php sayfasına yönlendiriyoruz;
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}
// MySQL veritabanı bilgilerini tanımlıyoruz;
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
// VT ye bağlanıyoruz;
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
// Bağlantı hatası var mı kontrol ediyoruz;
if (mysqli_connect_errno()) {
    exit('MySQL bağlantı hatası: ' . mysqli_connect_error());
}

// Kullanıcı listesini çek
$result = $con->query("SELECT id, username, kullaniciTuru, kayitTarihi, kayitOnayi,kullaniciTuru,ilgiAlani1,ilgiAlani2,ilgiAlani3 FROM kullanicilar");

// Kayıt silelim:
if(isset($_GET['id'])&& isset($_GET['uyeSil'])) {
    $id = $_GET['id'];
$update = $con->query("DELETE FROM kullanicilar WHERE id = $id");
    echo "<script>alert('Kayıt silindi!'); 
	window.location.href='kayitOnay.php';</script>";
}

// Kayıt onay veya red edelim:
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = ($_GET['status'] == 'ok') ? 'ok' : 'no';

    // Güncelleme sorgusu
    $update = $con->query("UPDATE kullanicilar SET kayitOnayi='$status' WHERE id=$id");

    if ($update) {
        echo "<script>alert('Kayıt durumu güncellendi!'); 
		window.location.href='kayitOnay.php';</script>";
		
    } else {
        echo "<script>alert('Güncelleme hatası!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <title>Onay İşlemleri</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
        table { width: 90%; border-collapse: collapse; margin:0 auto;}
        th, td { padding: 2px; border: 3px solid black; text-align: center; }
        th { background-color: #f2f2f2; }
        .btn { padding: 3px 15px; cursor: pointer; }
    </style>
</head>
<body>
	<header class="header">

            <div class="wrapper">
                
                <h1>ETKİNLİK YÖNETİM SİSTEMİ KULLANICI ONAY İŞLEMLERİ</h1>
                <nav class="menu">
                    
                    <a href="etkinlikler.php">Etkinlikler</a>
                    <a href="admin.php">Yönetim</a>
                    <a href="cikis.php">
                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                        Çıkış
                    </a>
                </nav>

            </div>

        </header>
    <br>
    <table>
        <tr>
            <th>Kullanıcı Adı</th>
            <th>Kullanıcı Türü</th>
            <th>Kayıt Tarihi</th>
			<th>İlgiAlani-1</th>
			<th>İlgiAlani-2</th>
			<th>İlgiAlani-3</th>
            <th>Onay Durumu</th>			
            <th>İşlem</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['username'], ENT_QUOTES) ?></td>
            <td><?= $row['kullaniciTuru'] ?></td>
            <td><?= $row['kayitTarihi'] ?></td>
            <td><?= $row['ilgiAlani1'] ?></td>
			<td><?= $row['ilgiAlani2'] ?></td>
			<td><?= $row['ilgiAlani3'] ?></td>
			<td><?= $row['kayitOnayi'] ?></td>
            <td>
                <a class="btn" href="?id=<?= $row['id'] ?>&status=ok">Onayla</a>
                <a class="btn" href="?id=<?= $row['id'] ?>&status=no">Reddet</a>
                <a class="btn" href="?id=<?= $row['id'] ?>&uyeSil">Üye Sil</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
