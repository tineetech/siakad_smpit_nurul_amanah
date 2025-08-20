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
    <table style="width: 100%;border:none !important;" border="0">
        <tr>
            <td style="border:none !important;width: 60px; text-align: center;">
                <img src="{{ public_path('images/pp/logo-smp.png') }}" style="width: 60px; height: 60px;">
            </td>
            <td style="text-align: center;border:none !important;">
                <h3>Pesantren Terpadu Nurul Amanah</h3>
                <h4>LAPORAN HASIL BELAJAR</h4>
            </td>
        </tr>
    </table>
    
    {{-- <div style="display: flex;background:red">
        <img src="{{ public_path('images/pp/logo-smp.png') }}" style="width: 60px; height: 60px;background:yellow">
        <div style="width: 200px;background:blue">
            <h3>Pesantren Terpadu Nurul Amanah</h3>
            <h4>LAPORAN HASIL BELAJAR</h4>
        </div>
    </div> --}}

    <div style="display: flex;">
        <div style="width: 75%; display: inline-block; vertical-align: top;">
            <p>Nama : {{ $siswa->nama_lengkap }}</p>
            <p>TTL : {{ $siswa->tempat_lahir . ' / ' . \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</p>
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
                <th>KKM</th>
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
                <td>{{ $nilai->nilai_kkm ?? '-' }}</td>
                <td>{{ $nilai->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br><br><br>
    <table style="width: 100%; border: none; text-align: start;">
        <tr>
            <td style="border:none;">
                <p>Orang Tua/Wali</p>
                <br><br><br><br>
                <p style="text-decoration: underline; margin: 0;">Nama Orang Tua</p>
                {{-- <p>Orang tua dari: {{ $siswa->nama_lengkap ?? '-' }}</p> --}}
            </td>
            <td style="border:none;">
                <p>Wali Kelas</p>
                <br><br><br><br>
                <p style="text-decoration: underline; margin: 0;">{{ $waliKelas->nama ?? 'Nama Wali Kelas' }}</p>
                <p>NIP: {{ $waliKelas->nip ?? '-' }}</p>
            </td>
            <td style="border:none;">
                <p>Tasik Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p>Kepala Sekolah</p>
                <br><br><br><br>
                <p style="text-decoration: underline; margin: 0;">{{ $kepalaSekolah->nama ?? 'Nama Kepala Sekolah' }}</p>
                <p>NUPTK: {{ $kepalaSekolah->nip ?? '4137771672130233' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
