@extends('layouts.app')

@section('title', 'Laporan Presensi Sesi Seminar')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>Sesi Seminar</h3>
                    <p class="text-subtitle text-muted">
                        Kelola laporan presensi sesi untuk seminar: <strong>{{ $seminar->nama_seminar }}</strong>
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan Presensi</a></li>
                    <li class="breadcrumb-item active">Sesi Seminar</li>
                </x-breadcrumb>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title">Daftar Sesi Seminar</h4>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive p-1">
                            <table class="table table-hover" id="sesi-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sesi</th>
                                        <th>Tempat Seminar</th>
                                        <th>Kuota</th>
                                        <th>Harga Tiket</th>
                                        <th>Tanggal Pelaksanaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>

@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const seminarId = {{ $seminar->id }};
            let table = $('#sesi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan.sesi.data', ['seminar' => $seminar->id]) }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_sesi',
                        name: 'nama_sesi'
                    },
                    {
                        data: 'tempat_seminar',
                        name: 'tempat_seminar'
                    },
                    {
                        data: 'kuota',
                        name: 'kuota'
                    },
                    {
                        data: 'harga',
                        name: 'harga_tiket',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal_pelaksanaan',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [4, 'asc']
                ]
            });
        });
    </script>
@endpush
