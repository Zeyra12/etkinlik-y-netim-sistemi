<?php
// Oturum başlatıyoruz;
session_start();
// Yönetici girişi yapılmadıysa index.php sayfasına yönlendiriyoruz;
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

// Formdan gelen verileri işle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $etkinlikAdi = $_POST["etkinlikAdi"];
    $etkinlikResmi = $_POST["etkinlikResmi"];
    $etkinlikTarihi = $_POST["etkinlikTarihi"];
    $ilgiAlani = $_POST["ilgiAlani"];
    $biletFiyati = $_POST["biletFiyati"];
    $paraBirimi = $_POST["paraBirimi"];
    $kontenjan = $_POST["kontenjan"];
    $havaKosulu = $_POST["havaKosulu"];

    $sql = "INSERT INTO etkinlikler (etkinlikAdi, etkinlikResmi, etkinlikTarihi, ilgiAlani, biletFiyati, paraBirimi, kontenjan, havaKosulu) VALUES ('$etkinlikAdi', '$etkinlikResmi', '$etkinlikTarihi', '$ilgiAlani', $biletFiyati, '$paraBirimi', '$kontenjan', '$havaKosulu')";

    if ($con->query($sql) === TRUE) {
        echo "<div class='success-msg'>Etkinlik başarıyla eklendi!</div>";
    } else {
        echo "<div class='error-msg'>Hata: " . $sql . "<br>" . $con->error . "</div>";
    }


}
// Etkinlikleri getirme sorgusu
$etkinlik_sorgu = "SELECT * FROM etkinlikler ORDER BY etkinlikTarihi DESC";
$etkinlik_sonuc = $con->query($etkinlik_sorgu);
$con->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>Etkinlikler</title>
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
        input, textarea {
            width: 30%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    
        .etkinlik-resim-container {
        width: 300px; /* Görsel alan genişliği */
        height: 200px; /* Görsel alan yüksekliği */
        overflow: hidden; /* Görselin belirtilen alanın dışına taşmasın*/
        display: flex;
        justify-content: center; /* Yatayda ortala */
        align-items: center; /* Dikeyde ortala */
        border-radius: 10px;
        }

        .etkinlik-resim-container img {
        max-width: 100%; /* resmin maksimum genişliği kabından büyük olmasın */
        max-height: 100%; /* resmin maksimum yüksekliği kabından büyük olmasın */
        object-fit: cover; /* resmi alanın içine sığdır */
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
               
        .form-group {
            margin-bottom: 15px;
            text-align: center;
        }
        label {
            font-weight: bold;
        }
    </style>

    <script>
        function etkinlikEkleFormuGoster() {
            document.getElementById("etkinlikFormu").style.display = "block";
        }
    </script>
    </head>

    <body>
        <header class="header">
            <div class="wrapper">
                <h1>ETKİNLİK YÖNETİM SİSTEMİ YÖNETİM SAYFASI</h1>
                
                <nav class="menu">
                    <a href="kayitOnay.php">Yeni Kayıt Onayı</a>
                    <a href="admin.php">Yönetim</a>
                    <a href="sifreDegistir.php">Şifre Değiştir</a>
                    <a href="cikis.php">
                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                        Çıkış
                    </a>
                </nav>

            </div>

        </header>

    <div class="container">
        <h2>Etkinlikler</h2>
        <button class="button" onclick="etkinlikEkleFormuGoster()">Yeni Etkinlik Ekle</button>

        <div id="etkinlikFormu" style="display: none;">
            
            <form method="post">
                <div class="form-group">
                    <label>Etkinlik Adı:</label>
                    <input type="text" name="etkinlikAdi" required>
                </div>
                <div class="form-group">
                    <label>Etkinlik Resmi:</label>
                    <textarea name="etkinlikResmi" required></textarea>
                </div>
                <div class="form-group">
                    <label>Tarihi:</label>
                    <input type="date" name="etkinlikTarihi" required>
                </div>
                
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" name="ilgiAlani" list="kategoriListesi" required>
                    <datalist id="kategoriListesi">
                    <option value="Teknoloji">
                    <option value="Sanat">
                    <option value="Spor">
                    <option value="Doğa">
                    <option value="Gastroloji">
                    </datalist>
                </div>

                <div class="form-group">
                    <label>Bilet Fiyatı</label>
                    <input type="decimal" name="biletFiyati" required>
                </div>
                <div class="form-group">
                    <label>Para Birimi</label>
                    <input type="text" name="paraBirimi" list="parabirimilistesi" required>
                    <datalist id="parabirimilistesi">
                    <option value="TRY">
                    <option value="USD">
                    <option value="EUR">
                    </datalist>
                </div>
                <div class="form-group">
                    <label>Kontenjan</label>
                    <input type="int" name="kontenjan" required>
                </div>
                <div class="form-group">
                    <label>Hava Koşulu</label>
                    <input type="text" name="havaKosulu" list="havaKosuluListesi" required>
                    <datalist id="havaKosuluListesi">
                    <option value="Güneşli">
                    <option value="Yağmurlu">
                    </datalist>
                </div>
                <button type="submit" class="button">Kaydet</button>
            </form>
        </div>
        </div>
    </div>

<div class="container">
    <!--<h2>Etkinlikler</h2>-->

    <div class="etkinlik-listesi">
        <?php
        if ($etkinlik_sonuc->num_rows > 0) {
            while($row = $etkinlik_sonuc->fetch_assoc()) {
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
                echo "</div>";
            }
        } else {
            echo "<p>Henüz etkinlik eklenmemiş.</p>";
        }
        ?>
        </div>
    </div>
</div>
</body>
</html>