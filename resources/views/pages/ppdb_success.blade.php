<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMPIT NURUL AMANAH</title>
  <link rel="shortcut icon" href="{{ asset('images/pp/logo-smp.png') }}" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<body class="bg-green-600">
  <main class="p-4 bg-white rounded-lg shadow-md my-10 max-w-5xl mx-auto">
    <div class="flex justify-center"><img src="{{ asset('images/pp/logo-smp.png') }}" class="w-[75px]" alt=""></div>
    <h1 class="text-2xl text-center mt-5 font-bold">Pendaftaran Siswa {{ $calonSiswa->nama_lengkap }} Berhasil Dikirim</h1>
    <p class="text-gray-400 text-center mb-8">SMPIT Nurul Amanah</p>

    @if(session('success'))
      <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    <div class="flex gap-10 items-center justify-center"> {{-- Added items-center and justify-center for better alignment --}}
      <div class="w-40 text-center"> {{-- Added text-center for QR code caption --}}
        <h3>Nomor Pendaftaran:</h3>
        <p class="font-bold text-lg">{{ $calonSiswa->nomor_pendaftaran }}</p>
        <div class="mt-4">
            {{-- Generate QR Code --}}
            {!! QrCode::size(150)->generate($calonSiswa->nomor_pendaftaran) !!}
        </div>
        <p class="text-sm text-gray-600 mt-2">Scan QR ini untuk melihat detail pendaftaran.</p>
      </div>
      <div class="w-60">
        <p class="mb-2">Terima kasih telah mendaftar di SMPIT Nurul Amanah. Data pendaftaran Anda telah kami terima.</p>
        <p class="mb-2">Nomor pendaftaran Anda adalah: <span class="font-semibold">{{ $calonSiswa->nomor_pendaftaran }}</span>. Mohon simpan nomor ini untuk referensi lebih lanjut.</p>
        <p>Anda akan segera dihubungi oleh pihak sekolah untuk informasi mengenai tahapan seleksi selanjutnya. Pastikan nomor telepon yang Anda daftarkan aktif.</p>
      </div>
    </div>
  </main>

  <script>
    // The Select2 script for gelombang_id is not directly relevant for the success page,
    // but it's kept here as it was in your original code.
    $(document).ready(function() {
      $('#gelombang_id').select2({
        placeholder: 'Pilih Gelombang',
        ajax: {
          url: '{{ route("api.gelombang") }}',
          dataType: 'json',
          delay: 250,
          processResults: data => ({
            results: data.map(g => ({ id: g.id, text: g.nama }))
          }),
          cache: true
        }
      });
    });
  </script>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>