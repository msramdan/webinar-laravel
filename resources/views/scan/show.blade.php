@extends('layouts.app')

@section('title', 'Scan QR Code Presensi')

@push('css')
    <style>
        .scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        #qr-scanner {
            width: 100%;
            height: auto;
            background: #000;
        }

        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }

        .scan-frame {
            width: 70%;
            height: 200px;
            border: 4px solid rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            position: relative;
        }

        .scan-line {
            width: 100%;
            height: 2px;
            background: rgba(0, 255, 0, 0.7);
            position: absolute;
            animation: scan 2s infinite linear;
        }

        @keyframes scan {
            0% {
                top: 0;
            }

            100% {
                top: 100%;
            }
        }

        .session-info {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        /* Perbesar tampilan notifikasi */
        .result-container {
            display: none;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
        }

        .result-title {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .result-content {
            font-size: 20px;
        }

        .alert-success,
        .alert-danger {
            font-size: 18px;
            padding: 20px;
        }

        .btn-scan {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            border: none;
            padding: 12px 30px;
            color: white;
            border-radius: 50px;
            font-weight: 600;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(78, 84, 200, 0.3);
            transition: all 0.3s ease;
        }

        .btn-scan:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 84, 200, 0.4);
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <section class="section">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="session-info">
                        <h4 class="text-white">{{ $sesi->nama_sesi }}</h4>
                        <p class="mb-1"><i class="bi bi-calendar-event"></i>
                            {{ $sesi->tanggal_pelaksanaan }}</p>
                        <p class="mb-1"><i class="bi bi-building"></i> {{ $sesi->nama_seminar }}</p>
                        @if ($sesi->link_gmeet)
                            <p class="mb-0"><i class="bi bi-link-45deg"></i> <a href="{{ $sesi->link_gmeet }}"
                                    class="text-white" target="_blank">Join Online</a></p>
                        @endif
                    </div>

                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="card-title">Scan QR Code Peserta</h4>
                            <p class="text-muted">Arahkan kamera ke QR code yang ditampilkan peserta</p>

                            <div class="scanner-container mb-4">
                                <video id="qr-scanner"></video>
                                <div class="scanner-overlay">
                                    <div class="scan-frame">
                                        <div class="scan-line"></div>
                                    </div>
                                    <p class="text-white mt-2">Scan QR Code di dalam frame</p>
                                </div>
                            </div>

                            <button id="toggle-scanner" class="btn btn-scan mb-3">
                                <i class="bi bi-camera"></i> Mulai Scan
                            </button>

                            <div class="result-container" id="result-container">
                                <h3 id="result-title" class="result-title">Hasil Scan</h3>
                                <div id="result-content" class="result-content"></div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('scan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let scanner = null;
            let videoElem = document.getElementById('qr-scanner');

            $('#toggle-scanner').click(function() {
                if (scanner) {
                    // Stop scanner
                    scanner.stop();
                    scanner = null;
                    videoElem.srcObject = null;
                    $(this).html('<i class="bi bi-camera"></i> Mulai Scan');
                } else {
                    // Start scanner
                    scanner = new Instascan.Scanner({
                        video: videoElem,
                        mirror: false
                    });

                    scanner.addListener('scan', function(content) {
                        if (scanner._active !== false) {
                            processQR(content);
                        }
                    });

                    Instascan.Camera.getCameras().then(function(cameras) {
                        if (cameras.length > 0) {
                            scanner.start(cameras[0]);
                            $('#toggle-scanner').html(
                                '<i class="bi bi-stop-circle"></i> Stop Scan');
                        } else {
                            Swal.fire({
                                title: 'Kamera Tidak Ditemukan',
                                text: 'Tidak ada kamera yang terdeteksi di perangkat Anda',
                                icon: 'error'
                            });
                        }
                    }).catch(function(e) {
                        console.error(e);
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal mengakses kamera: ' + e,
                            icon: 'error'
                        });
                    });
                }
            });

            function processQR(qrData) {
                $.ajax({
                    url: '{{ route('scan.process') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        qr_data: qrData,
                        sesi_id: '{{ $sesi->id }}'
                    },
                    beforeSend: function() {
                        $('#result-container').hide();
                        $('#toggle-scanner').prop('disabled', true).html(
                            '<i class="bi bi-hourglass"></i> Memproses...');
                    },
                    success: function(response) {
                        if (response.sukses) {
                            Swal.fire({
                                title: 'Absen Berhasil!',
                                html: `<div style="font-size:24px">Peserta: <strong>${response.peserta}</strong></div>
           <div style="font-size:18px;margin-top:10px">Waktu: ${new Date().toLocaleString()}</div>`,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'swal-wide',
                                    title: 'swal-title-large'
                                }
                            });

                            $('#result-title').html(
                                '<span class="text-success"><i class="bi bi-check-circle"></i> Berhasil</span>'
                            );
                            $('#result-content').html(`
        <div class="alert alert-success">
            <h4>${response.peserta}</h4>
            <p class="mb-0" style="font-size:18px">Absen berhasil direkam pada: ${new Date().toLocaleString()}</p>
        </div>
    `);
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: response.pesan,
                                icon: 'error'
                            });

                            $('#result-title').html(
                                '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Gagal</span>'
                            );
                            $('#result-content').html(`
        <div class="alert alert-danger">
            <h4>${response.pesan}</h4>
            <p class="mb-0" style="font-size:18px">Silahkan coba lagi atau hubungi panitia</p>
        </div>
    `);
                        }

                        $('#result-container').show();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan saat memproses QR code';
                        if (xhr.responseJSON && xhr.responseJSON.pesan) {
                            errorMsg = xhr.responseJSON.pesan;
                        }

                        Swal.fire({
                            title: 'Error',
                            text: errorMsg,
                            icon: 'error'
                        });

                        $('#result-title').html(
                            '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Error</span>'
                        );
                        $('#result-content').html(`
    <div class="alert alert-danger">
        <h4>${errorMsg}</h4>
    </div>
`);
                        $('#result-container').show();
                    },
                    complete: function() {
                        $('#toggle-scanner').prop('disabled', false).html(
                            '<i class="bi bi-camera"></i> Scanner Aktif');

                        // Tidak menghentikan scanner, hanya menonaktifkan sementara
                        if (scanner) {
                            // Nonaktifkan scanner sementara selama 2 detik
                            scanner._active = false;
                            setTimeout(() => {
                                scanner._active = true;
                            }, 2000);
                        }
                    }
                });
            }

        });
    </script>

    <style>
        /* Perbesar ukuran SweetAlert */
        .swal-wide {
            width: 600px !important;
        }

        .swal-title-large {
            font-size: 28px !important;
        }

        .swal2-popup {
            font-size: 1.6rem !important;
        }
    </style>
@endpush
