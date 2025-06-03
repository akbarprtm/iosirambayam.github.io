<?php
// Konfigurasi Supabase
$supabase_url = "https://ctggbrmvubjggyxmmbse.supabase.co";
$supabase_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImN0Z2dicm12dWJqZ2d5eG1tYnNlIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc0ODI1MTg0NywiZXhwIjoyMDYzODI3ODQ3fQ.6rVGqPTOCkhI14R12cRVSQfH0uF7ywzQIC7Dm-vSrZA"; // Ganti dengan anon key atau service role key

// Ambil data dari parameter GET
$metode = $_GET['metode'] ?? null;
$durasi = $_GET['durasi'] ?? null;
$waktu = $_GET['waktu'] ?? null;

if (!$metode || !$durasi || !$waktu) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Parameter tidak lengkap"]);
    exit;
}

// Data yang akan dikirim ke Supabase
$data = [
    "metode" => $metode,
    "durasi_detik" => (int)$durasi,
    "waktu" => $waktu
];

// Inisialisasi CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$supabase_url/rest/v1/penyiraman");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: $supabase_key",
    "Authorization: Bearer $supabase_key",
    "Content-Type: application/json",
    "Prefer: return=representation"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Eksekusi request
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Tanggapi hasil
if ($httpcode >= 200 && $httpcode < 300) {
    echo json_encode(["status" => "success", "message" => "Data penyiraman berhasil disimpan", "data" => json_decode($response)]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan data ke Supabase", "http_code" => $httpcode, "response" => $response]);
}
?>
