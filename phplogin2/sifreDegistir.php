<?php
session_start();
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;

}
$kullaniciTuru = isset($_SESSION['kullaniciTuru']) ? $_SESSION['kullaniciTuru'] : 'user';


$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('MySQL bağlantı hatası: ' . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $id = $_SESSION['account_id'];

    // Kullanıcının mevcut şifresini veritabanından alıyoruz;
    $stmt = $con->prepare('SELECT password FROM kullanicilar WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_password, $password)) {
        echo "<script>alert('Mevcut şifreniz yanlış!');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('Yeni şifreler uyuşmuyor!');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $con->prepare('UPDATE kullanicilar SET password = ? WHERE id = ?');
        $update_stmt->bind_param('si', $hashed_password, $id);
        if ($update_stmt->execute()) {           
            // Kullanıcının ilkGiris değerini güncelleyelim;
            $con->query("UPDATE kullanicilar SET ilkGiris = 1 WHERE id = $id");
            echo "<script>alert('Şifre başarıyla değiştirildi!');            
            window.location.href='cikis.php';</script>";       
        } else {
            echo "<script>alert('Şifre değiştirirken hata oluştu!');</script>";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <title>Şifre Değiştir</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
            body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 6vh;
        }
            .container {
            width: 300px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 230px;
            margin-right:20px;
        }
        .container h2 {            
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .link {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>  
</head>
<body>
    <header class="header">

            <div class="wrapper">

                <!--<h1>Üye Adı Buraya Gelsin</h1>-->
                <h1><?=htmlspecialchars($_SESSION['account_name'], ENT_QUOTES)?>!</p></h1>
                
                <nav class="menu">
                    
                    <a href="cikis.php">
                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
                        Çıkış
                    </a>
                </nav>

            </div>
        <div class="page-title">
        </header>

    <div class="container">
        <h2>Şifre Değiştir</h2>
        <form action="" method="post">
            <input type="password" name="current_password" placeholder="Mevcut Şifre" required>
            <input type="password" name="new_password" placeholder="Yeni Şifre" required>
            <input type="password" name="confirm_password" placeholder="Yeni Şifre (Tekrar)" required>
            <button type="submit">Şifreyi Güncelle</button>
        </form>
        <a class="link" href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') : 'index.php'; ?>">Geri Dön</a>    
    </div>
</body>
</html>