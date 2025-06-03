<?php
// data.php
header("Content-Type: application/json");
date_default_timezone_set("Asia/Jakarta");

$supabase_url = "https://ctggbrmvubjggyxmmbse.supabase.co";
$supabase_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImN0Z2dicm12dWJqZ2d5eG1tYnNlIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc0ODI1MTg0NywiZXhwIjoyMDYzODI3ODQ3fQ.6rVGqPTOCkhI14R12cRVSQfH0uF7ywzQIC7Dm-vSrZA"; // Jaga keamanan!

$headers = [
    "apikey: $supabase_key",
    "Authorization: Bearer $supabase_key"
];

// Ambil data kelembapan terakhir
$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, "$supabase_url/rest/v1/sensor_data?select=kelembapan,waktu&order=waktu.desc&limit=5");
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
$response1 = curl_exec($ch1);
curl_close($ch1);
$kelembapan = json_decode($response1, true);

if ($kelembapan) {
    foreach ($kelembapan as &$row) {
        $row['waktu'] = date('Y-m-d H:i:s', strtotime($row['waktu']));
    }
    $kelembapan = array_reverse($kelembapan);
} else {
    $kelembapan = [];
}

// Ambil penyiraman terakhir
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, "$supabase_url/rest/v1/penyiraman?select=*&order=waktu.desc&limit=1");
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
$response2 = curl_exec($ch2);
curl_close($ch2);
$penyiraman = json_decode($response2, true);

$last_penyiraman = isset($penyiraman[0]) ? $penyiraman[0] : null;
if ($last_penyiraman) {
    $last_penyiraman['waktu'] = date('Y-m-d H:i:s', strtotime($last_penyiraman['waktu']));
}

echo json_encode([
    "kelembapan" => $kelembapan,
    "penyiraman" => $last_penyiraman
]);
