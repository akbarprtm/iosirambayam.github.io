<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Kelembaban & Penyiraman Tanah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f6f8; }
        .container { margin-top: 50px; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">📈 Monitoring Kelembaban & Penyiraman Tanah</h2>

    <div class="row">
        <!-- Data Kelembaban -->
        <div class="col-md-6">
            <div class="card shadow p-3 mb-4">
                <h5 class="card-title text-center">Data Kelembaban Terbaru</h5>
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kelembaban (%)</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="kelembaban-tbody">
                        <tr><td colspan="2" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grafik -->
        <div class="col-md-6">
            <div class="card shadow p-3 mb-4">
                <h5 class="card-title text-center">Grafik Kelembaban</h5>
                <canvas id="kelembabanChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Informasi Penyiraman Terakhir -->
    <div class="card shadow p-3">
        <h5 class="card-title text-center mb-3">Info Penyiraman Terakhir</h5>
        <p id="info-penyiraman" class="text-center">Memuat...</p>
    </div>
</div>

<script>
    
  // Format 'YYYY-MM-DD HH:mm:ss' ke 'DD/MM/YYYY HH:mm:ss' tanpa parsing Date
  function formatWaktu(waktuString) {
      // contoh input: "2025-05-27 02:33:00"
      const [tanggal, jam] = waktuString.split(' ');
      const [tahun, bulan, hari] = tanggal.split('-');
      return `${hari}/${bulan}/${tahun} ${jam}`;
  }


    const ctx = document.getElementById('kelembabanChart').getContext('2d');
    const kelembabanChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Kelembapan (%)',
                data: [],
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    function updateData() {
        fetch('data.php')
            .then(res => res.json())
            .then(data => {
                // Update Tabel
                if (data.kelembapan && Array.isArray(data.kelembapan)) {
                    const tableRows = data.kelembapan.map(row =>
                        `<tr>
                            <td>${row.kelembapan}</td>
                            <td>${formatWaktu(row.waktu)}</td>
                        </tr>`
                    ).join('');
                    document.querySelector('#kelembaban-tbody').innerHTML = tableRows;

                    // Update Chart
                    kelembabanChart.data.labels = data.kelembapan.map(row => formatWaktu(row.waktu));
                    kelembabanChart.data.datasets[0].data = data.kelembapan.map(row => row.kelembapan);
                    kelembabanChart.update();
                }

                // Info Penyiraman
              if (data.penyiraman) {
                  document.querySelector('#info-penyiraman').innerHTML = `
                      <strong>Waktu Penyiraman:</strong> ${formatWaktu(data.penyiraman.waktu)}<br>
                      <strong>Metode:</strong> ${data.penyiraman.metode}<br>
                      <strong>Durasi:</strong> ${data.penyiraman.durasi_detik} detik
                  `;
              } else {
                  document.querySelector('#info-penyiraman').innerText = "Belum ada data penyiraman.";
              }

            })
            .catch(error => {
                document.querySelector('#kelembaban-tbody').innerHTML = `<tr><td colspan="2">Gagal memuat data</td></tr>`;
                document.querySelector('#info-penyiraman').innerText = "Gagal memuat info penyiraman.";
                console.error("Fetch error:", error);
            });
    }

    // Pertama kali load
    updateData();

    // Update setiap 5 detik
    setInterval(updateData, 5000);
</script>
</body>
</html>
