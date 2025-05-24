<?php
// Oturum başlatıyoruz;
session_start();
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

// Giriş formundaki kullanıcı adı ve şifre yazılıp kaydet butonuna tıklandı mı;
if (!isset($_POST['username'], $_POST['password'])) 
{
    // VT ye gönderilecek değişkenler alınamadıysa;
    exit('Lütfen kullanıcı adı ve şifrenizi giriniz!');
}

//  SQL i oluşturalım; (SQL injection ı önlemek için)
if ($stmt = $con->prepare('SELECT id, password, kullaniciTuru, kayitOnayi, ilkGiris FROM kullanicilar WHERE username = ?')) 
{
    // parametrleri bağlayalım (s = string, i = int, b = blob, vs), kullanıcı adı string olduğu için 's' kullanıyoruz;
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    // Sonucu saklayalım ki hesabın veritabanında olup olmadığını kontrol edebilelim;
    $stmt->store_result();

    // Girilen kullanıcı adı ile veritabanında kayıtlı bir hesap var mı kontrol edelim;
    if ($stmt->num_rows > 0) 
    {
    // Hesap var, sonuçları değişkenlere alalım;
    $stmt->bind_result($id, $password, $kullaniciTuru, $kayitOnayi, $ilkGiris);
    $stmt->fetch();
    //  hashed (şifrelenmiş) şifreyi kontrol edelim;
        if (password_verify($_POST['password'], $password)) 
        {
/****************************************************** */  
            // Şifre doğru! Giriş başarılı,Oturumu yenileyelim (güvenlik için);
            session_regenerate_id();
            // Oturum açmak için gerekli değişkenleri tanımlayalım;
            $_SESSION['account_loggedin'] = TRUE;
            $_SESSION['account_name'] = $_POST['username'];
            $_SESSION['account_id'] = $id;

            // İlk kez giriş yapmışsa şifre değiştirmesi için uyarı ver
            if (!$ilkGiris) {
                echo "<script>alert('İlk kez giriş yapıyorsunuz, lütfen şifrenizi değiştiriniz!');
                window.location.href = 'sifreDegistir.php';</script>";
            exit;
            }

            

            if ($kullaniciTuru == 'user' && $kayitOnayi == 'ok')
            {   
                echo 'Hoşgeldiniz!, ' . htmlspecialchars($_SESSION['account_name'], ENT_QUOTES) . '!';
                header('Location: uyeProfil.php');
                exit;
            }
            else if ($kullaniciTuru =='admin' && $kayitOnayi == 'ok') 
            {
                header('Location: admin.php');
                exit;
            }

            else  
            {
               echo"<script>alert('Yönetici Onayı Bekleniyor!');
               window.location.href = 'index.php';</script>";
            }
        } 
        else // Şifre yanlış ise;
        {
            echo"<script>alert('Şifre yanlış!');
		    window.location.href = 'index.php';</script>";
        }
    } 
    else 
    {
    // Kullanıcı adı yanlış ise;
    echo 'Kullanıcı Adı hatalı!';
    echo"<script>alert('Kullanıcı Kayıtlı Değil!');
		window.location.href = 'index.php';</script>";
    }
    // kapatıyoruz;
    $stmt->close();
}
?>

