<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raport</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        h3, h4 { text-align: center; margin: 0; }
    </style>
</head>
<body>
    <h3>Pesantren Terpadu Nurul Amanah</h3>
    <h4>LAPORAN HASIL BELAJAR</h4>

    <div style="display: flex;">
        <div style="width: 75%; display: inline-block; vertical-align: top;">
            <p>Nama : {{ $siswa->nama_lengkap }}</p>
            <p>TTL : {{ $siswa->tempat_lahir . "/" . $siswa->tanggal_lahir ?? '-' }}</p>
            <p>Jenis Kelamin : {{ $siswa->jenis_kelamin ?? '-' }}</p>
            <p>NISN : {{ $siswa->nisn ?? '-' }}</p>
        </div>
        <div style="width: 20%; display: inline-block; vertical-align: top;">
            <p>Kelas : {{ $siswa->kelas->nama ?? '-' }}</p>
            <p>Semester : {{ $semester->nama ?? '-' }}</p>
        </div>
    </div>
    {{-- <table>
        <tr>
            <td>Nama</td><td>{{ $siswa->nama_lengkap }}</td>
            <td>Semester</td><td>{{ $semester->nama }}</td>
        </tr>
        <tr>
            <td>Kelas</td><td>{{ $siswa->kelas->nama ?? '-' }}</td>
            <td>NIS</td><td>{{ $siswa->nis }}</td>
        </tr>
    </table> --}}

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>NH</th>
                <th>PAS</th>
                <th>NA</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiList as $i => $nilai)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $nilai->mataPelajaran->nama }}</td>
                <td>{{ $nilai->nilai_harian ?? '-' }}</td>
                <td>{{ $nilai->nilai_pas ?? '-' }}</td>
                <td>{{ $nilai->nilai_akhir ?? '-' }}</td>
                <td>{{ $nilai->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
