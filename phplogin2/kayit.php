<?php
// Oturumu başlat
session_start();
// Kullanıcı giriş yapmış ise anasayfaya yönlendiriyoruz
if (isset($_SESSION['account_loggedin'])) {
    header('Location: uyeProfil.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>Yeni Üye Kaydı</title>
    <style>
        .checkbox-group {
            display: flex;
            flex-direction: row;
            gap: 20px;
        }
    </style>

<!--ilgi alanlarından 3 adet seçim yapıldığını garantileyelim-->
    <script>
        function validateSelection() {
            let checkboxes = document.querySelectorAll('input[name="ilgiAlani[]"]:checked');
            if (checkboxes.length !== 3) {
                alert("Lütfen 3 adet ilgi alanı seçin!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="login">
        <h1>Etkinlik Yönetim Sistemi <br> Yeni Üye Kaydı</h1>
        <form action="kayitIslem.php" method="post" class="form login-form" onsubmit="return validateSelection()">
            
            <label class="form-label" for="username">Kullanıcı Adı</label>
            <input class="form-input" type="text" name="username" id="username" required>

            <label class="form-label" for="email">Email</label>
            <input class="form-input" type="email" name="email" id="email" required>

            <label class="form-label" for="password">Şifre</label>
            <input class="form-input" type="password" name="password" id="password" required>

            <label class="form-label">İlgi Alanları (3 adet seçim yapınız):</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="ilgiAlani[]" value="Teknoloji"> Teknoloji</label>
                <label><input type="checkbox" name="ilgiAlani[]" value="Sanat"> Sanat</label>
                <label><input type="checkbox" name="ilgiAlani[]" value="Spor"> Spor</label>
                <label><input type="checkbox" name="ilgiAlani[]" value="Doğa"> Doğa</label>
                <label><input type="checkbox" name="ilgiAlani[]" value="Gastroloji"> Gastroloji</label>
            </div>

            <br>
            <button class="btn blue" type="submit">Kaydet</button>
            <p class="register-link">Kayıtlı Üye misiniz? => <a href="index.php" class="form-link">Giriş Yap</a></p>
        </form>
    </div>
</body>
</html>