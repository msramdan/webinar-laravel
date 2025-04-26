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
                                        <th>Jumlah Sesi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seminars as $index => $seminar)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $seminar->nama_seminar }}</td>
                                            <td>{{ $seminar->jumlah_sesi }} Sesi</td>
                                            <td>
                                                <a href="{{ route('panel-peserta.lihatSesi', $seminar->id) }}" class="btn btn-primary btn-sm">Lihat Sesi</a>
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
