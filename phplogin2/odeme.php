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

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}

$kullanici_id = $_SESSION['account_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['odeme_tamamla'])) {
    $odeme_yontemi = $_POST['odeme_yontemi'];

    if ($odeme_yontemi === "Kredi Kartı") {
        $kart_no = $_POST['kart_no'] ?? '';
        $son_kullanma = $_POST['son_kullanma'] ?? '';
        $cvv = $_POST['cvv'] ?? '';

        // Gerçek ödeme entegrasyonu burada olurdu
        if (empty($kart_no) || empty($son_kullanma) || empty($cvv)) {
            echo "<p style='color: red;'>Lütfen tüm kart bilgilerini doldurun.</p>";
            $odeme_basari = 0;
        } else {
            $odeme_basari = 1; // Başarılı
        }
    } else {
        $odeme_basari = 1;
    }

    if ($odeme_basari) {
        $stmt = $con->prepare("DELETE FROM sepet WHERE uye_id = ?");
        $stmt->bind_param("i", $kullanici_id);
        $stmt->execute();
        $stmt->close();

        echo "<p style='color: green;'>Ödeme başarılı! 🎉 Seçilen yöntem: " . htmlspecialchars($odeme_yontemi) . "</p>";
    } else {
        echo "<p style='color: red;'>Ödeme başarısız! Lütfen tekrar deneyin. 🚨</p>";
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ödeme Sayfası</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
            font-size: 16px;
        }
        .button:hover {
            background-color: #218838;
        }
        .cancel-button {
            background-color: #dc3545;
        }
        .cancel-button:hover {
            background-color: #c82333;
        }
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 20px 0;
        }
        .credit-card-info {
            display: none;
            margin-top: 20px;
            text-align: left;
        }
        .credit-card-info input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
    </style>
    <script>
        function toggleCardFields() {
            const krediKartiSecili = document.querySelector('input[name="odeme_yontemi"]:checked').value === "Kredi Kartı";
            document.getElementById("card-fields").style.display = krediKartiSecili ? "block" : "none";
        }
        document.addEventListener("DOMContentLoaded", function () {
            const radios = document.querySelectorAll('input[name="odeme_yontemi"]');
            radios.forEach(radio => radio.addEventListener('change', toggleCardFields));
        });
    </script>
</head>
<body>

<div class="container">
    <h2>Ödeme Sayfası</h2>
    <p>Lütfen bir ödeme yöntemi seçin:</p>

    <form method="POST">
        <div class="payment-options">
            <label><input type="radio" name="odeme_yontemi" value="Kredi Kartı" required> Kredi Kartı</label>
            <label><input type="radio" name="odeme_yontemi" value="Banka Havalesi" required> Banka Havalesi</label>
            <label><input type="radio" name="odeme_yontemi" value="Kapıda Ödeme" required> Kapıda Ödeme</label>
        </div>

        <div id="card-fields" class="credit-card-info">
            <label>Kart Numarası:
                <input type="text" name="kart_no" maxlength="19" placeholder="1111 2222 3333 4444">
            </label>
            <label>Son Kullanma Tarihi:
                <input type="text" name="son_kullanma" placeholder="MM/YY">
            </label>
            <label>CVV:
                <input type="text" name="cvv" maxlength="4" placeholder="123">
            </label>
        </div>

        <button type="submit" name="odeme_tamamla" class="button">Ödeme Yap</button>
    </form>

    <a href="sepet.php" class="button cancel-button">Sepete Geri Dön</a>
</div>

</body>
</html>
