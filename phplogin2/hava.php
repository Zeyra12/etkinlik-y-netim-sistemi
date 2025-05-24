<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $apiKey = "b3e2b3aa27941413f4e6fcbc081b2ac3";
    $city = "Erzurum";
    $selectedDate = $_POST["date"];
    $url = "https://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$apiKey}&units=metric";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data && isset($data['list'])) {
        $found = false;

        foreach ($data['list'] as $forecast) {
            $date = substr($forecast['dt_txt'], 0, 10);
            $description = strtolower($forecast['weather'][0]['main']); // "Rain" veya "Clear"

            if ($date == $selectedDate) {
                if ($description == "rain") {
                    echo "<p><strong>Tarih:</strong> $date</p>";
                    echo "<p><strong>Hava Durumu:</strong> 🌧 Yağmurlu</p>";
                } elseif ($description == "clear") {
                    echo "<p><strong>Tarih:</strong> $date</p>";
                    echo "<p><strong>Hava Durumu:</strong> ☀ Açık</p>";
                } else {
                    echo "<p><strong>Tarih:</strong> $date</p>";
                    echo "<p>Hava durumu açık veya yağmurlu değil.</p>";
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            echo "<p>Bu tarihe ait hava durumu verisi bulunamadı.</p>";
        }
    } else {
        echo "<p>Hata: Veri alınamadı.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hava Durumu Sorgulama</title>
</head>
<body>
    <form method="POST">
        <label for="date">Tarih Seç (YYYY-MM-DD):</label>
        <input type="date" id="date" name="date" required>
        <button type="submit">Hava Durumu Sorgula</button>
    </form>
</body>
</html>
