<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Pelajaran Kelas {{ $kelas->nama ?? 'Contoh' }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; }
        .schedule-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Center items if they don't fill a row */
            gap: 20px;
            /* Untuk Dompdf, flexbox mungkin perlu fallback atau penyesuaian */
            /* Alternatif jika flexbox bermasalah di Dompdf: */
            /* display: block; clear: both; */
        }
        .day-box {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            width: 100%; /* Approx 3 columns, adjust as needed */
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            page-break-inside: avoid; /* Prevent breaking a box across pages */
            box-sizing: border-box; /* Include padding and border in the width */
            margin-bottom: 20px; /* Space between rows */
            /* Jika menggunakan float sebagai fallback */
            /* float: left; margin-right: 2.33%; */
        }
        .day-box h3 {
            background-color: #f0f0f0;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
        }
        .schedule-item {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #eee;
        }
        .schedule-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .time { font-weight: bold; color: #555; }
        .subject { margin-left: 10px; }
        .no-schedule {
            color: #888;
            font-style: italic;
            text-align: center;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Jadwal Pelajaran</h1>
        <h2>Kelas: {{ $kelas->nama ?? 'Contoh Kelas 1A' }}</h2>
        <p>Semester: {{ $semester->nama ?? 'Ganjil 2024/2025' }}</p>
    </div>

    <div class="schedule-container">
        @php
            // Mapping angka hari ke nama hari
            $dayNames = [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Minggu', // Tambahkan Minggu jika Anda mungkin memerlukannya di masa depan
            ];

            // Hari-hari statis yang ingin ditampilkan
            $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            // Ubah group key dari angka hari menjadi nama hari
            $groupedJadwal = collect($jadwal)->groupBy(function($item) use ($dayNames) {
                return $dayNames[$item->hari] ?? 'Tidak Dikenal'; // Menggunakan $item->hari karena itu objek
            });
        @endphp

        @foreach($hariOrder as $hari)
            <div class="day-box">
                <h3>{{ $hari }}</h3>
                @if(isset($groupedJadwal[$hari]) && $groupedJadwal[$hari]->isNotEmpty())
                    @php
                        $sortedItems = $groupedJadwal[$hari]->sortBy('jam_mulai');
                    @endphp
                    @foreach($sortedItems as $item)
                        <div class="schedule-item">
                            <span class="time">{{ $item->jam_mulai ?? 'N/A' }} - {{ $item->jam_selesai ?? 'N/A' }}</span>
                            <span class="subject">{{ $item->mataPelajaran->nama ?? 'Mata Pelajaran Tidak Dikenal' }}</span><br>
                        </div>
                    @endforeach
                @else
                    <div class="no-schedule">
                        Tidak ada jadwal untuk hari ini.
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>

</html>