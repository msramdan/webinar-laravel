@extends('layouts.app')

@section('title', __('Daftar Peserta Seminar'))

@section('content')
    <style>
        /* Add to your CSS */
        #qrCodeContainer {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #qrCodeContainer svg {
            max-width: 100%;
            height: auto;
        }

        #qrParticipantName {
            font-weight: 600;
            margin-top: 1rem;
        }

        #qrSessionName {
            color: #6c757d;
            margin-bottom: 1rem;
        }
    </style>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Rekap Laporan') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Daftar Peserta Seminar.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/dashboard">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('laporan.index') }}">{{ __('Laporan Presensi') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Rekap Presensi') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <x-alert></x-alert>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Filter Peserta</h4>
                                    <div class="form-group">
                                        <label for="session_filter">Sesi Seminar</label>
                                        <select class="form-select" id="filterSession">
                                            <option value="all" {{ $selectedSession == 'all' ? 'selected' : '' }}>Semua
                                                Sesi</option>
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session->id }}"
                                                    {{ $selectedSession == $session->id ? 'selected' : '' }}>
                                                    {{ $session->nama_sesi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a class="btn btn-primary" href="#" target="_blank"><i
                                            class="fas fa-file-pdf"></i> PDF</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-rekap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Peserta</th>
                                            <th>Email</th>
                                            <th>No Telepon</th>
                                            <th>Sesi</th>
                                            <th>Status</th>
                                            <th>Tanggal Pendaftaran</th>
                                            <th>Presensi</th>
                                        </tr>
                                    </thead>

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let dataTableRekap;

        $(document).ready(function() {
            const currentUrl = window.location.href;
            const seminarID = currentUrl.split("/").pop();

            let dataTableRekap = $('#table-rekap').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                scrollX: false,
                dom: "<'row'" +
                    "<'col-sm-6 d-flex align-items-center justify-conten-start'>" +
                    "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
                    ">" +

                    "<'table-responsive'tr>" +

                    "<'row'" +
                    "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                    "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                    ">",
                oLanguage: {
                    sSearch: "Search:",
                    sLengthMenu: "_MENU_"
                },
                initComplete: function() {
                    $('.dataTables_filter input')
                        .removeClass('form-control-solid')
                        .addClass('form-control form-control-sm');
                    $('.dataTables_length select')
                        .removeClass('form-select-solid')
                        .addClass('form-select form-select-sm');
                },
                ajax: {
                    url: "{{ route('laporan.rekap-fetchData') }}",
                    data: function(data) {
                        data.id = $('#filterSession').val();
                        data.seminar_id = seminarID;
                        return data;
                    }
                },
                columns: [{
                        data: 'no',
                        name: 'no',
                    },
                    {
                        data: 'nama_peserta',
                        name: 'nama_peserta'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'no_telepon',
                        name: 'no_telepon'
                    },
                    {
                        data: 'sesi',
                        name: 'sesi'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'tanggal_pendaftaran',
                        name: 'tanggal_pendaftaran',
                    },
                    {
                        data: 'presensi',
                        name: 'presensi'
                    },
                ]
            });

            $('#filterSession').change(function(e) {
                e.preventDefault();
                dataTableRekap.ajax.reload(null, false); 
            });
        });
    </script>
@endpush
