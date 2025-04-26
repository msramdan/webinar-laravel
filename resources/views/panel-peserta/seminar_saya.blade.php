@extends('panel-peserta.layouts.master')
@section('title', 'Seminar Saya')
@section('content')

    <div class="container-fluid">
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
                                                @if ($item->status == 'Approved')
                                                    <div class="btn-group" role="group">
                                                        @if ($item->link_gmeet)
                                                            <a href="{{ $item->link_gmeet }}" target="_blank"
                                                                class="btn btn-sm btn-primary mr-2">
                                                                <i class="fas fa-video"></i> Join Meeting
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('pendaftaran.qrcode.download.peserta', ['id' => $item->id]) }}"
                                                            class="btn btn-sm btn-secondary mr-2">
                                                            <i class="fas fa-qrcode"></i> Download QR
                                                        </a>
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

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "responsive": true,
                "autoWidth": false
            });
        });
    </script>
@endpush
