<?php
// Oturum başlatıyoruz;
session_start();
// giriş yapılmadıysa index.php sayfasına yönlendiriyoruz;
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
if (mysqli_connect_errno()) 
{
    // Bağlantıda bir hata varsa script leri çalıştırmasın ve bir hata alert mesajı versin;
    exit('MySQL Veritabanına bağlantı hatası: ' . mysqli_connect_error());
}

// Güncel duyuruları çekelim;
$duyuru_sorgu = "SELECT * FROM duyurular WHERE NOW() BETWEEN baslangicTarihi AND bitisTarihi ORDER BY baslangicTarihi DESC";
$duyuru_sonuc = $con->query($duyuru_sorgu);
$con->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>Duyurular</title>
        <link href="style.css" rel="stylesheet" type="text/css">
        <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .success-msg, .error-msg {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .success-msg {
            background-color: #d4edda;
            color: #155724;
        }
        .error-msg {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
    </head>

    <body>

        <header class="header">

            <div class="wrapper">

                <h1>ETKİNLİK YÖNETİM SİSTEMİ DUYURULAR</h1>
                
                <nav class="menu">
                    
                    <a href="uyeProfil.php">Uye Profil Sayfası</a>
                    <a href="sifreDegistir.php">Şifre Değiştir</a>
                    <a href="cikis.php">
                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                        Çıkış
                    </a>
                </nav>

            </div>

        </header>
    <div class="container">
        <h2><span style="border: 3px solid black; padding: 5px;">GÜNCEL DUYURULAR</span></h2>
        <!--<h2>GÜNCEL DUYURULAR</h2>-->
        <div class="duyuru-listesi">           
            <?php
            if ($duyuru_sonuc->num_rows > 0) {
                while($row = $duyuru_sonuc->fetch_assoc()) {
                    echo "<br><br><div class='duyuru-item'></u>";
                    echo "<h4><u>" . htmlspecialchars($row["duyuruAdi"]) . "</u></h4>";
                    echo "<p>" . htmlspecialchars($row["duyuruMetni"]) . "</p>";
                    echo "<small><b>Başlangıç:</b> " . $row["baslangicTarihi"] . " | <b>Bitiş:</b> " . $row["bitisTarihi"] . "</small>";
                    echo "</div>";
                }
            } else {
                echo "<p>Henüz duyuru eklenmemiş.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>