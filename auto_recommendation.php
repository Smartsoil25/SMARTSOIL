<?php
function predict_crop($temperature, $humidity, $moisture, $ph, $n, $p, $k) {
    $data = [
        "temperature" => (float)$temperature,
        "humidity" => (float)$humidity,
        "moisture" => (float)$moisture,
        "pH" => (float)$ph,
        "N" => (float)$n,
        "P" => (float)$p,
        "K" => (float)$k
    ];

    $json_data = json_encode($data);
    $url = "http://localhost:5000/predict";  // AI Server URL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['recommended_crop'] ?? "N/A";
}
?>
