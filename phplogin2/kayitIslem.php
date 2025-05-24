<?php
// MySQL veritabanı bilgilerini tanımlayalım;
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
// Veritabanına bağlanalım;
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
// Bağlantı hatası var mı kontrol edelim;
if (mysqli_connect_errno()) {
	// Veritabanına bağlanmada bir hata varsa, scripti çalıştırma, hatayı göster;
	echo"<script>alert('MySQL veritabanına bağlanılamadı: ' . mysqli_connect_error());
	window.location.href = 'kayit.php';</script>";
	exit;
}

// email adresinin geçerli olup olmadığını kontrol edelim
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	echo"<script>alert('Lütfen geçerli bir email adresi girin!');
	window.location.href = 'kayit.php';</script>";
	exit;
}

	// kullanıcı adının geçerli olup olmadığını kontrol edelim
	// Kullanıcı adı sadece harf ve rakamlardan oluşmalı
if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
	echo"<script>alert('Kullanıcı adı geçerli değil!');
	window.location.href = 'kayit.php';</script>";
	exit;
}

	// şifreyi uzunluğunu kontrol edelim;
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
	//exit('Şifreniz en az 5 en fazla 20 karakter uzunluğunda olmalı!');
	echo"<script>alert('Şifreniz en az 5 en fazla 20 karakter uzunluğunda olmalı!');
	window.location.href = 'kayit.php';</script>";
	exit;
}

// Checkbox seçimlerini kontrol et ve tam olarak 3 adet olup olmadığını doğrula
if (isset($_POST['ilgiAlani']) && count($_POST['ilgiAlani']) == 3) {
    // Checkbox larda seçilen ve dizi olarak alınan değerleri değişkenlere ayır
        list($ilgiAlani1, $ilgiAlani2, $ilgiAlani3) = $_POST['ilgiAlani'];
} else {
	// Seçim yapılmadıysa veya 3 adet seçim yapılmadıysa hata mesajı verelim;
	echo"<script>alert('Lütfen tam olarak 3 ilgi alanı seçin!');
	window.location.href = 'kayit.php';</script>";
	exit; 
}
	
// Kayıt işlemi için SQL sorgusunu hazırlayalım;
if ($stmt = $con->prepare('SELECT id, password FROM kullanicilar WHERE username = ?')) {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Sonuçları kaydedelim;
	$stmt->store_result();

	// Böyle bir kullanıcı zaten kayıtlı mı bakalım;
		if ($stmt->num_rows > 0) {
		// kullanıcı adı zaten kayıtlı ise;
		echo"<script>alert('Bu Kullanıcı Adı Kayıtlı! Lütfen Başka Bir Kullanıcı Adı Seçin!');
		window.location.href = 'kayit.php';</script>";
		exit;
		} 
		else 
		{
		// kullanıcı kayıtlı değil, yeni bir kullanıcı olarak ekleyebiliriz;
        // Değişkenleri tanımlayalım;
		$kayitTarihi = date('Y-m-d H:i:s');
		// Şifreyi gizleyerek kullanalım
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

		// Kullanıcıyı veritabanına ekleyelim;
			if ($stmt = $con->prepare('INSERT INTO kullanicilar (username, password, email, kayitTarihi, kayitOnayi, kullaniciTuru, ilgiAlani1, ilgiAlani2, ilgiAlani3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
	
				$kullaniciTuru = 'user'; // Kullanıcı türü default olarak 'user' olsun
				$kayitOnayi = 'no'; // Default 'no' olsun yönetici onayını beklesin
				$stmt->bind_param('sssssssss', $_POST['username'], $password, $_POST['email'], $kayitTarihi, $kayitOnayi,$kullaniciTuru, $ilgiAlani1, $ilgiAlani2, $ilgiAlani3);

				$stmt->execute();

				//Kullanıcı kaydedildiği bilgisini alert olarak verelim:
				echo"<script>alert('Yeni Üye Kaydı Başarılı! Yönetici Onayından Sonra Sisteme Giriş Yapabilirsiniz!');
				window.location.href = 'kayit.php';</script>";	

			} 
			else // Kayıt işlemi başarısız olduysa;
			{	
				echo"<script>alert('Kayıt Hatası! Yeniden Deneyin!');
				window.location.href = 'kayit.php';</script>";
			}
		}
			// komutu sonlandıralım;
			$stmt->close();
	} 
else 
{
	// SQL sorguda bir hata var
}
	// Bağlantıyı kapatalım;
	$con->close();

?>
	