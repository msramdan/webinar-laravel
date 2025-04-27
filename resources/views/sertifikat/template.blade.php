<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {{-- Judul bisa disesuaikan --}}
    <title>Sertifikat {{ $namaPeserta }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("{{ $templatePath }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* --- SESUAIKAN POSISI DAN STYLE DI BAWAH INI --- */

        .nomor-sertifikat {
            position: absolute;
            /* Coba geser sedikit ke bawah dan ke kanan */
            top: 192px;
            /* Coba nilai baru (misal: tambah 5px) */
            left: 490px;
            /* Coba nilai baru (misal: tambah 90px) */
            font-size: 16px;
            font-weight: bold;
            color: #333;
            /* Mungkin tidak perlu width atau text-align jika posisinya spesifik */
        }

        .nama-peserta {
            position: absolute;
            /* Coba geser sedikit ke bawah */
            top: 305px;
            /* Coba nilai baru (misal: tambah 15px) */
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        /* Hapus class CSS lain seperti .nama-kegiatan jika sudah tidak dipakai */
    </style>
</head>

<body>
    {{-- Nomor Sertifikat --}}
    <div class="nomor-sertifikat">
        {{ $nomorSertifikat }}
    </div>

    {{-- Nama Peserta --}}
    <div class="nama-peserta">
        {{ $namaPeserta }}
    </div>
</body>

</html>
