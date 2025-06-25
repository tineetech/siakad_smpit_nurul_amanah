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
    <h1 class="text-2xl text-center mt-5 font-bold">Formulir Pendaftaran Siswa Baru</h1>
    <p class="text-gray-400 text-center mb-8">SMPIT Nurul Amanah</p>

    @if(session('success'))
      <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('ppdb.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Gelombang --}}
        <div>
          <label class="block text-sm font-medium">Gelombang <span class="text-red-500">*</span></label>
          <select id="gelombang_id" name="gelombang_id" required class="w-full bg-white shadow-sm border p-2 rounded"></select>
        </div>

        {{-- NISN --}}
        <div>
          <label class="block text-sm font-medium">NISN <span class="text-red-500">*</span></label>
          <input type="number" name="nisn" required placeholder="NISN" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Nama Lengkap --}}
        <div>
          <label class="block text-sm font-medium">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" name="nama_lengkap" required placeholder="Nama Lengkap" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Jenis Kelamin --}}
        <div>
          <label class="block text-sm font-medium">Jenis Kelamin <span class="text-red-500">*</span></label>
          <select name="jenis_kelamin" required class="w-full bg-white shadow-sm border p-2 rounded">
            <option value="" disabled selected>Pilih Jenis Kelamin</option>
            <option value="laki-laki">Laki-laki</option>
            <option value="perempuan">Perempuan</option>
          </select>
        </div>

        {{-- Tempat Lahir --}}
        <div>
          <label class="block text-sm font-medium">Tempat Lahir <span class="text-red-500">*</span></label>
          <input type="text" name="tempat_lahir" required placeholder="Tempat Lahir" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Tanggal Lahir --}}
        <div>
          <label class="block text-sm font-medium">Tanggal Lahir <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_lahir" required class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Alamat --}}
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">Alamat Lengkap <span class="text-red-500">*</span></label>
          <textarea name="alamat" required placeholder="Alamat Lengkap" class="w-full bg-white shadow-sm border p-2 rounded"></textarea>
        </div>

        {{-- Nomor HP --}}
        <div>
          <label class="block text-sm font-medium">Nomor HP Siswa <span class="text-red-500">*</span></label>
          <input type="number" name="nomor_hp_siswa" required placeholder="Nomor HP Siswa" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Asal Sekolah --}}
        <div>
          <label class="block text-sm font-medium">Asal Sekolah <span class="text-red-500">*</span></label>
          <input type="text" name="asal_sekolah" required placeholder="Asal Sekolah" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Anak Ke --}}
        <div>
          <label class="block text-sm font-medium">Anak Ke- <span class="text-red-500">*</span></label>
          <input type="number" name="anak_ke" required placeholder="Anak Ke-" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Jumlah Saudara --}}
        <div>
          <label class="block text-sm font-medium">Jumlah Saudara Kandung <span class="text-red-500">*</span></label>
          <input type="number" name="jumlah_saudara" required placeholder="Jumlah Saudara Kandung" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Cita-cita --}}
        <div>
          <label class="block text-sm font-medium">Cita-Cita</label>
          <input type="text" name="cita_cita" placeholder="Cita-Cita" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Hobi --}}
        <div>
          <label class="block text-sm font-medium">Hobi</label>
          <input type="text" name="hobi" placeholder="Hobi" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Berat --}}
        <div>
          <label class="block text-sm font-medium">Berat Badan (kg)</label>
          <input type="number" step="0.1" name="berat_badan" placeholder="Berat Badan" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Tinggi --}}
        <div>
          <label class="block text-sm font-medium">Tinggi Badan (cm)</label>
          <input type="number" step="0.1" name="tinggi_badan" placeholder="Tinggi Badan" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>

        {{-- Riwayat Penyakit --}}
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">Riwayat Penyakit</label>
          <textarea name="riwayat_penyakit" placeholder="Riwayat Penyakit yang Pernah Diderita (kosongkan kalau tidak ada)" class="w-full bg-white shadow-sm border p-2 rounded"></textarea>
        </div>
      </div>

      <h2 class="text-lg font-semibold mt-8">Informasi Ayah</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm">Nama Ayah <span class="text-red-500">*</span></label>
          <input type="text" name="nama_ayah" required placeholder="Nama Ayah" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Status Ayah <span class="text-red-500">*</span></label>
          <select name="status_ayah" required class="w-full bg-white shadow-sm border p-2 rounded">
            <option value="" disabled selected>Pilih Status</option>
            <option value="hidup">Hidup</option>
            <option value="meninggal">Meninggal</option>
          </select>
        </div>
        <div>
          <label class="block text-sm">Tempat Lahir Ayah <span class="text-red-500">*</span></label>
          <input type="text" name="tempat_lahir_ayah" required placeholder="Tempat Lahir Ayah" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Tanggal Lahir Ayah <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_lahir_ayah" required class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Pendidikan Terakhir Ayah <span class="text-red-500">*</span></label>
          <select name="pendidikan_ayah" required class="w-full bg-white shadow-sm border p-2 rounded">
            <option value="" disabled selected>Pilih Pendidikan</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA/SMK">SMA/SMK</option>
            <option value="D1">D1</option>
            <option value="D2">D2</option>
            <option value="D3">D3</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
          </select>
        </div>
        <div>
          <label class="block text-sm">Pekerjaan Ayah <span class="text-red-500">*</span></label>
          <input type="text" name="pekerjaan_ayah" required placeholder="Pekerjaan Ayah" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Penghasilan Bulanan Ayah <span class="text-red-500">*</span></label>
          <select name="penghasilan_ayah" required class="w-full bg-white shadow-sm border p-2 rounded">
            <option value="" disabled selected>Pilih Penghasilan</option>
            <option value="< 1 Juta">&lt; 1 Juta</option>
            <option value="1 Juta - 2 Juta">1 Juta - 2 Juta</option>
            <option value="2 Juta - 5 Juta">2 Juta - 5 Juta</option>
            <option value="5 Juta - 10 Juta">5 Juta - 10 Juta</option>
            <option value="> 10 Juta">&gt; 10 Juta</option>
          </select>
        </div>
        <div>
          <label class="block text-sm">Nomor HP Ayah <span class="text-red-500">*</span></label>
          <input type="number" name="nomor_hp_ayah" required placeholder="Nomor HP Ayah" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
      </div>

      <h2 class="text-lg font-semibold mt-8">Informasi Ibu</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm">Nama Ibu <span class="text-red-500">*</span></label>
          <input type="text" name="nama_ibu" required placeholder="Nama Ibu" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Status Ibu <span class="text-red-500">*</span></label>
          <select name="status_ibu" required class="w-full bg-white shadow-sm border p-2 rounded">
            <option value="" disabled selected>Pilih Status</option>
            <option value="hidup">Hidup</option>
            <option value="meninggal">Meninggal</option>
          </select>
        </div>
        <div>
          <label class="block text-sm">Tempat Lahir Ibu <span class="text-red-500">*</span></label>
          <input type="text" name="tempat_lahir_ibu" required placeholder="Tempat Lahir Ibu" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Tanggal Lahir Ibu <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_lahir_ibu" required class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Pendidikan Terakhir Ibu <span class="text-red-500">*</span></label>
          <select name="pendidikan_ibu" required class="w-full bg-white shadow-sm border p-2 rounded">
            <option value="" disabled selected>Pilih Pendidikan</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA/SMK">SMA/SMK</option>
            <option value="D1">D1</option>
            <option value="D2">D2</option>
            <option value="D3">D3</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
          </select>
        </div>
        <div>
          <label class="block text-sm">Pekerjaan Ibu <span class="text-red-500">*</span></label>
          <input type="text" name="pekerjaan_ibu" required placeholder="Pekerjaan Ibu" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm">Penghasilan Bulanan Ibu <span class="text-red-500">*</span></label>
          <select name="penghasilan_ibu" required class="w-full bg-white shadow-sm border p-2 rounded">
            <option value="" disabled selected>Pilih Penghasilan</option>
            <option value="< 1 Juta">&lt; 1 Juta</option>
            <option value="1 Juta - 2 Juta">1 Juta - 2 Juta</option>
            <option value="2 Juta - 5 Juta">2 Juta - 5 Juta</option>
            <option value="5 Juta - 10 Juta">5 Juta - 10 Juta</option>
            <option value="> 10 Juta">&gt; 10 Juta</option>
          </select>
        </div>
        <div>
          <label class="block text-sm">Nomor HP Ibu <span class="text-red-500">*</span></label>
          <input type="number" name="nomor_hp_ibu" required placeholder="Nomor HP Ibu" class="w-full bg-white shadow-sm border p-2 rounded">
        </div>
      </div>

      <button type="submit" class="w-full mt-6 bg-yellow-500 text-white px-4 py-3 rounded hover:bg-yellow-600">
        Kirim Pendaftaran
      </button>
    </form>
  </main>

  <script>
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