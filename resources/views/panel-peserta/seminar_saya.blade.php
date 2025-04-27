@extends('panel-peserta.layouts.master')
@section('title', 'Seminar Saya')
@section('content')

    <div class="container-fluid">
        {{-- Tambahkan ini untuk menampilkan error dari redirect --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        {{-- Akhir tambahan --}}

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
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pelaksanaan)->format('d M Y H:i') }}
                                            </td>
                                            <td>
                                                {{-- --- MODIFIKASI BAGIAN AKSI --- --}}
                                                @if ($item->status == 'Approved')
                                                    <div class="btn-group" role="group">
                                                        {{-- Tombol Join Meeting (jika ada link) --}}
                                                        @if ($item->link_gmeet)
                                                            <a href="{{ $item->link_gmeet }}" target="_blank"
                                                                class="btn btn-sm btn-info mr-1" data-toggle="tooltip"
                                                                title="Join Meeting">
                                                                <i class="fas fa-video"></i>
                                                            </a>
                                                        @endif

                                                        {{-- Tombol Download QR --}}
                                                        <a href="{{ route('pendaftaran.qrcode.download.peserta', ['id' => $item->id]) }}"
                                                            class="btn btn-sm btn-secondary mr-1" data-toggle="tooltip"
                                                            title="Download QR Code">
                                                            <i class="fas fa-qrcode"></i>
                                                        </a>

                                                        {{-- Tombol Download Sertifikat (KONDISIONAL) --}}
                                                        @if ($item->show_sertifikat == 'Yes' && $item->sudah_presensi)
                                                            <a href="{{ route('panel-peserta.sertifikat.download', ['pendaftaranId' => $item->id]) }}"
                                                                class="btn btn-sm btn-success" target="_blank"
                                                                data-toggle="tooltip" title="Lihat Sertifikat">
                                                                <i class="fas fa-certificate"></i>
                                                            </a>
                                                        @elseif ($item->show_sertifikat == 'Yes' && !$item->sudah_presensi)
                                                            {{-- Tombol disabled jika belum presensi --}}
                                                            <button class="btn btn-sm btn-outline-secondary" disabled
                                                                data-toggle="tooltip"
                                                                title="Sertifikat tersedia setelah presensi">
                                                                <i class="fas fa-certificate"></i>
                                                            </button>
                                                        @endif
                                                        {{-- Akhir Tombol Sertifikat --}}

                                                    </div>
                                                @else
                                                    -
                                                @endif
                                                {{-- --- AKHIR MODIFIKASI AKSI --- --}}
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

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "responsive": true,
                "autoWidth": false
            });
            // Aktifkan tooltip
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
