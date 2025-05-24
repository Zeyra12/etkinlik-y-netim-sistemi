<?php
// Oturum başlat
session_start();

// Yönetici girişi kontrolü
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}

// MySQL veritabanı bağlantısı
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

// Bağlantıyı oluştur
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Bağlantı hatası kontrolü
if (mysqli_connect_errno()) {
    exit('MySQL Veritabanına bağlantı hatası: ' . mysqli_connect_error());
}

// Kullanıcı ilgi alanlarına göre etkinlikleri çek
$etkinlik_sorgu = "SELECT e.* 
    FROM etkinlikler e 
    JOIN kullanicilar k ON k.id = {$_SESSION['account_id']}
    WHERE e.ilgiAlani IN (k.ilgiAlani1, k.ilgiAlani2, k.ilgiAlani3) 
    ORDER BY e.etkinlikTarihi DESC";

$etkinlik_sonuc = $con->query($etkinlik_sorgu);
$con->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <title>Üye Profili</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .etkinlik-listesi {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .etkinlik-item {
            width: calc(33.33% - 20px);
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .etkinlik-resim-container {
            width: 100%;
            height: 350px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
        }
        .etkinlik-resim-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
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
        .bilet-sayisi {
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bilet-sayisi label {
            margin-right: 10px;
            font-weight: bold;
        }
        .bilet-sayisi input {
            width: 60px;
            text-align: center;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .sepet {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }
        .sepet:hover {
            background-color: #218838;
        }
    </style>

</head>
<body>



<header class="header">
    <div class="wrapper">
        <h1>Hoşgeldiniz <?= htmlspecialchars($_SESSION['account_name'], ENT_QUOTES) ?>!</h1>            
        <nav class="menu">
            <a href="duyurular.php">Duyurular</a>
            <a href="sifreDegistir.php">Şifrenizi Değiştirin</a>
            <a href="cikis.php">Çıkış</a>
            <a href="sepet.php" class="button sepet">Sepete Git</a>
        </nav>
    </div>
</header>

<div class="container">
    <div class="etkinlik-listesi">
        <?php
        if ($etkinlik_sonuc->num_rows > 0) {
            while ($row = $etkinlik_sonuc->fetch_assoc()) {
                echo "<div class='etkinlik-item'>";
                echo "<h4>" . htmlspecialchars($row["etkinlikAdi"]) . "</h4>";
                echo "<div class='etkinlik-resim-container'>";
                echo "<img src='" . htmlspecialchars($row["etkinlikResmi"]) . "' alt='Etkinlik Resmi'>";
                echo "</div>";
                echo "<p><b>Tarih:</b> " . htmlspecialchars($row["etkinlikTarihi"]) . "</p>";
                echo "<p><b>Kategori:</b> " . htmlspecialchars($row["ilgiAlani"]) . "</p>";
                echo "<p><b>Fiyat:</b> " . htmlspecialchars($row["biletFiyati"]) . " " . htmlspecialchars($row["paraBirimi"]) . "</p>";
                echo "<p><b>Kalan Kontenjan:</b> " . htmlspecialchars($row["kontenjan"]) . "</p>";
                echo "<p><b>Hava:</b> " . htmlspecialchars($row["havaKosulu"]) . "</p>";
                
                echo "<form action='sepet.php' method='post'>";
                
                
                echo "<input type='hidden' name='etkinlik_id' value='" . htmlspecialchars($row['id']) . "'>";

                echo "<input type='hidden' name='kullanici_id' value='" . htmlspecialchars($_SESSION['account_id']) . "'>";
                echo "<div class='bilet-sayisi'>";
                echo "<label for='biletSayisi'>Bilet Sayısı:</label>";
                
                echo "<input type='number' name='bilet_sayisi' data-id='" . htmlspecialchars($row['id']) . "' min='1' max='" . htmlspecialchars($row['kontenjan']) . "' value='1'>";
                
                echo "</div>";
                echo "<button type='button' class='button' onclick='sepetEkle(" . htmlspecialchars($row['id']) . "); alert(\"Sepete Eklendi!\")'>Sepete Ekle</button>";

                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>Henüz etkinlik eklenmemiş.</p>";
        }
        ?>
           </div>
</div>



<script>
function sepetEkle(etkinlikId) {
    var inputSelector = `input[name='bilet_sayisi'][data-id='${etkinlikId}']`;
    var biletSayisi = document.querySelector(inputSelector).value;
    
    console.log("Gönderilen etkinlikId:", etkinlikId);
    console.log("Gönderilen biletSayisi:", biletSayisi);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "sepetEkle.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            console.log("Sunucudan gelen yanıt:", xhr.responseText);
        }
    };

    xhr.send("etkinlik_id=" + encodeURIComponent(etkinlikId) + "&bilet_sayisi=" + encodeURIComponent(biletSayisi));
}
</script>

</body>
</html>