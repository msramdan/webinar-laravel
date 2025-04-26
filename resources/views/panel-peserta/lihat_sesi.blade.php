@extends('panel-peserta.layouts.master')
@section('title', 'Seminar Saya')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Sesi Seminar: {{ $seminar->nama_seminar }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sesi</th>
                                        <th>Kuota</th>
                                        <th>Harga Tiket</th>
                                        <th>Lampiran</th>
                                        <th>Tanggal Pelaksanaan</th>
                                        <th>Registrasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sesi as $index => $session)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $session->nama_sesi }}</td>
                                            <td>{{ $session->kuota }} - {{ $session->kuota - $session->filled_kuota }} spots
                                                left</td>
                                            <td>{{ $session->harga_tiket }}</td>
                                            <td><a href="{{ asset('path/to/lampiran/' . $session->lampiran) }}"
                                                    target="_blank">Download</a></td>
                                            <td>{{ \Carbon\Carbon::parse($session->tanggal_pelaksanaan)->format('d-m-Y H:i') }}
                                            </td>
                                            <td>
                                                @if ($session->filled_kuota < $session->kuota)
                                                    <button class="btn btn-success btn-sm register-btn"
                                                        data-seminar-id="{{ $seminar->id }}"
                                                        data-sesi-id="{{ $session->id }}">
                                                        Daftar
                                                    </button>
                                                @else
                                                    <button class="btn btn-secondary btn-sm" disabled>Kuota Penuh</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "responsive": true,
                "autoWidth": false
            });
        });

        $(document).on('click', '.register-btn', function() {
    // Retrieve sesi_id from the button's data attributes
    var sesi_id = $(this).data('sesi-id');

    // Show SweetAlert confirmation
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan mendaftar untuk sesi ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Daftar',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Send the registration request using POST method
            $.ajax({
                url: '{{ route('panel-peserta.register') }}', // Simplified URL
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sesi_id: sesi_id  // Only send sesi_id
                },
                success: function(response) {
                    Swal.fire(
                        'Pendaftaran Berhasil!',
                        'Menunggu konfirmasi dari admin.',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Terjadi kesalahan!',
                        'Silakan coba lagi.',
                        'error'
                    );
                }
            });
        }
    });
});
    </script>
@endpush
