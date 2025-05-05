@extends('panel-peserta.layouts.master')
@section('title', 'Seminar Saya')
@section('content')

    <div class="container-fluid">
        {{-- ... (pesan error) ... --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Seminar Yang Saya Ikuti</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Seminar</th>
                                        <th>Sesi</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Tempat</th>
                                        <th>Tanggal Pelaksanaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendaftaran as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->nama_seminar }}</td>
                                            <td>{{ $item->nama_sesi }}</td>
                                            <td>
                                                {{-- Status Badge --}}
                                                @if ($item->status == 'Approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($item->status == 'Waiting')
                                                    <span class="badge badge-warning">Waiting</span>
                                                @else
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y H:i') }}
                                            </td>
                                            <td>{{ $item->tempat_seminar }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pelaksanaan)->format('d M Y H:i') }}
                                            </td>
                                            <td>
                                                @if ($item->status == 'Approved')
                                                    <div class="btn-group" role="group">
                                                        {{-- Tombol Join Meeting --}}
                                                        @if ($item->link_gmeet)
                                                            <a href="{{ $item->link_gmeet }}" target="_blank"
                                                                class="btn btn-sm btn-info mr-1" data-toggle="tooltip"
                                                                title="Join Meeting">
                                                                <i class="fas fa-video"></i>
                                                            </a>
                                                        @endif

                                                        {{-- MODIFIKASI: Tombol untuk Buka Modal QR --}}
                                                        <button type="button"
                                                            class="btn btn-sm btn-secondary mr-1 btn-show-qr-modal"
                                                            data-toggle="modal" data-target="#qrCodeModal"
                                                            data-pendaftaran-id="{{ $item->id }}"
                                                            data-peserta-nama="{{ $item->nama_peserta ?? Auth::guard('panel-peserta')->user()->nama }}"
                                                            {{-- Ambil nama peserta jika ada --}} data-sesi-nama="{{ $item->nama_sesi }}"
                                                            data-download-url="{{ route('pendaftaran.qrcode.download.peserta', ['id' => $item->id]) }}"
                                                            title="Tampilkan QR Code">
                                                            <i class="fas fa-qrcode"></i>
                                                        </button>
                                                        {{-- AKHIR MODIFIKASI --}}

                                                        {{-- Tombol Sertifikat --}}
                                                        @if ($item->show_sertifikat == 'Yes' && $item->sudah_presensi)
                                                            <a href="{{ route('panel-peserta.sertifikat.download', ['pendaftaranId' => $item->id]) }}"
                                                                class="btn btn-sm btn-success" target="_blank"
                                                                data-toggle="tooltip" title="Lihat Sertifikat">
                                                                <i class="fas fa-certificate"></i>
                                                            </a>
                                                        @elseif ($item->show_sertifikat == 'Yes' && !$item->sudah_presensi)
                                                            <button class="btn btn-sm btn-outline-secondary" disabled
                                                                data-toggle="tooltip"
                                                                title="Sertifikat tersedia setelah presensi">
                                                                <i class="fas fa-certificate"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code Presensi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    {{-- Tempat untuk menampilkan QR Code --}}
                    <div id="qrCodeContainer"
                        style="min-height: 250px; display: flex; align-items: center; justify-content: center;">
                        {{-- Loading indicator atau QR code akan dimuat di sini oleh JS --}}
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <h5 id="qrPesertaName" class="mt-3">Nama Peserta</h5>
                    <p id="qrSesiName" class="text-muted">Nama Sesi</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    {{-- Tombol Download di dalam Modal --}}
                    <a href="#" id="qrDownloadBtn" class="btn btn-primary" download>
                        <i class="fas fa-download mr-1"></i> Download QR
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            try {
                $('#dataTable').DataTable({
                    "responsive": true,
                    "autoWidth": false
                });
                console.log('DataTable Initialized.'); // Log Sukses DataTable
            } catch (e) {
                console.error('Error initializing DataTable:', e); // Log Error DataTable
            }

            // Aktifkan tooltip Bootstrap
            $('[data-toggle="tooltip"]').tooltip();
            console.log('Tooltips Initialized.'); // Log Tooltip

            // --- Script untuk Modal QR Code ---
            console.log('Setting up QR Modal Button listener...'); // Log Setup Listener
            $('.btn-show-qr-modal').on('click', function() {
                console.log('Show QR Modal button clicked!'); // === Log 1 ===

                var pendaftaranId = $(this).data('pendaftaran-id');
                var pesertaNama = $(this).data('peserta-nama');
                var sesiNama = $(this).data('sesi-nama');
                var downloadUrl = $(this).data('download-url');
                var modal = $('#qrCodeModal');
                var qrContainer = modal.find('#qrCodeContainer');
                var downloadButton = modal.find('#qrDownloadBtn');

                console.log('Data retrieved: pendaftaranId=' + pendaftaranId + ', downloadUrl=' +
                    downloadUrl); // === Log 2 ===

                // Set judul dan nama di modal
                modal.find('#qrCodeModalLabel').text('QR Code Presensi');
                modal.find('#qrPesertaName').text(pesertaNama || 'Peserta');
                modal.find('#qrSesiName').text(sesiNama || 'Sesi Seminar');
                downloadButton.attr('href', downloadUrl);
                console.log('Modal content set.'); // === Log 3 ===

                // Tampilkan loading
                qrContainer.html(
                    '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                );

                // Definisikan URL untuk mengambil data SVG QR Code
                var generateUrl = "{{ url('/panel-peserta/generate-qrcode-data') }}/" + pendaftaranId;
                console.log('Requesting QR Data from URL:', generateUrl); // === Log 4 ===

                // Ambil data SVG menggunakan AJAX
                console.log('About to make AJAX call...'); // === Log 5 ===
                $.ajax({
                    url: generateUrl,
                    method: 'GET',
                    success: function(response) {
                        console.log('AJAX Success! Response:',
                        response); // Log 6: Respons sukses
                        if (response && response.svg) {
                            qrContainer.html(response.svg);
                            qrContainer.find('svg').css({
                                'width': '250px',
                                'height': '250px'
                            });
                            console.log('QR SVG injected.'); // Log SVG dimasukkan
                        } else {
                            qrContainer.html(
                                '<p class="text-danger">Gagal memuat data QR Code dari server.</p>'
                            );
                            console.error('Invalid response format:',
                            response); // Log Format Salah
                        }
                        if (response.nama_peserta) modal.find('#qrPesertaName').text(response
                            .nama_peserta);
                        if (response.nama_sesi) modal.find('#qrSesiName').text(response
                            .nama_sesi);
                        if (response.download_url) downloadButton.attr('href', response
                            .download_url);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error! Status:", status, "Error:", error, "XHR:",
                            xhr); // Log Error AJAX
                        qrContainer.html(
                            '<p class="text-danger">Gagal memuat QR Code. Error: ' +
                            status + '</p>');
                    }
                });
                console.log('AJAX call initiated (may run async).'); // === Log 8 ===
            });

            console.log('QR Modal Button listener setup complete.'); // Log Setup Listener Selesai

            // Reset konten modal saat ditutup (opsional, tapi baik)
            $('#qrCodeModal').on('hidden.bs.modal', function(e) {
                $(this).find('#qrCodeContainer').html(
                    '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                );
                $(this).find('#qrPesertaName').text('Nama Peserta');
                $(this).find('#qrSesiName').text('Nama Sesi');
                $(this).find('#qrDownloadBtn').attr('href', '#');
            });
            // --- Akhir Script Modal QR Code ---
        });
    </script>
@endpush
