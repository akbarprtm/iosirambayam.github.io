<?php
header('Content-Type: application/json');
date_default_timezone_set("Asia/Jakarta");

// Supabase config
$supabase_url = "https://ctggbrmvubjggyxmmbse.supabase.co";
$supabase_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImN0Z2dicm12dWJqZ2d5eG1tYnNlIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc0ODI1MTg0NywiZXhwIjoyMDYzODI3ODQ3fQ.6rVGqPTOCkhI14R12cRVSQfH0uF7ywzQIC7Dm-vSrZA"; // rahasiakan

// Ambil data kelembapan dari query parameter
if (!isset($_GET['kelembapan'])) {
    echo json_encode(["error" => "Parameter kelembapan tidak ditemukan"]);
    exit;
}

$kelembaban = intval($_GET['kelembapan']);
$waktu = date('Y-m-d H:i:s');

// Kirim data ke Supabase via REST API
$ch = curl_init();

$data = json_encode([
    "kelembapan" => $kelembaban,
    "waktu" => $waktu
]);

curl_setopt($ch, CURLOPT_URL, $supabase_url . "/rest/v1/sensor_data");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: $supabase_key",
    "Authorization: Bearer $supabase_key",
    "Content-Type: application/json",
    "Prefer: return=representation"
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(["error" => "Curl Error: $err"]);
} else {
    echo $response;
}
?>
