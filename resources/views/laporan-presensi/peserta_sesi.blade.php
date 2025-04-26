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
                    <h3>{{ __('Pendaftaran Seminar') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Daftar Peserta Seminar.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/dashboard">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('pendaftaran.index') }}">{{ __('Pendaftaran') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Daftar Peserta') }}
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
                                        <select class="form-select" id="session_filter">
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
                                    <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button class="btn btn-primary" id="btnCreate">
                                        <i class="fas fa-plus"></i> Tambah Pendaftaran
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-peserta">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Peserta</th>
                                            <th>Email</th>
                                            <th>No Telepon</th>
                                            <th>Sesi</th>
                                            <th>Status</th>
                                            <th>Tanggal Pendaftaran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($participants as $key => $participant)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $participant->nama }}</td>
                                                <td>{{ $participant->email }}</td>
                                                <td>{{ $participant->no_telepon }}</td>
                                                <td>{{ $participant->nama_sesi }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $participant->status == 'Approved' ? 'success' : ($participant->status == 'Rejected' ? 'danger' : 'warning') }}">
                                                        {{ $participant->status }}
                                                    </span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($participant->tanggal_pengajuan)->format('d/m/Y H:i') }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary edit-peserta"
                                                        data-id="{{ $participant->pendaftaran_id }}"
                                                        data-peserta-id="{{ $participant->id }}"
                                                        data-nama="{{ $participant->nama }}"
                                                        data-sesi="{{ $participant->sesi_id }}"
                                                        data-status="{{ $participant->status }}">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-peserta"
                                                        data-id="{{ $participant->pendaftaran_id }}"
                                                        data-nama="{{ $participant->nama }}">
                                                        <i class="ace-icon fa fa-trash-alt"></i>
                                                    </button>
                                                    @if ($participant->status == 'Approved')
                                                        <button class="btn btn-sm btn-success show-qrcode"
                                                            data-id="{{ $participant->pendaftaran_id }}"
                                                            data-url="{{ route('pendaftaran.qrcode.generate', ['id' => $participant->pendaftaran_id]) }}"
                                                            title="Lihat QR Code">
                                                            <i class="fas fa-qrcode"></i>
                                                        </button>
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
        </section>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QR Code Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer"></div>
                    <h5 id="qrParticipantName" class="mt-3"></h5>
                    <p id="qrSessionName" class="text-muted"></p>
                </div>
                <div class="modal-footer">
                    <a id="qrDownloadBtn" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createForm" action="{{ route('pendaftaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="seminar_id" value="{{ $id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="peserta_id">Peserta</label>
                            <select class="form-select" id="peserta_id" name="peserta_id" required>
                                <option value="">Pilih Peserta</option>
                                @foreach ($allPeserta as $peserta)
                                    <option value="{{ $peserta->id }}">{{ $peserta->nama }} ({{ $peserta->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sesi_id">Sesi Seminar</label>
                            <select class="form-select" id="sesi_id" name="sesi_id" required>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->nama_sesi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Waiting">Waiting</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Peserta</label>
                            <input type="text" class="form-control" id="edit_peserta_nama" readonly>
                            <input type="hidden" id="edit_peserta_id" name="peserta_id">
                        </div>
                        <div class="form-group">
                            <label for="edit_sesi_id">Sesi Seminar</label>
                            <select class="form-select" id="edit_sesi_id" name="sesi_id" required>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->nama_sesi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Waiting">Waiting</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Hapus Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus pendaftaran untuk peserta <strong id="delete_nama"></strong>?
                        </p>
                        <p class="text-danger">Data yang dihapus tidak dapat dikembalikan!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
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
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#table-peserta').DataTable({
                responsive: true,
                stateSave: true
            });

            // Filter session - will reload the page with new parameter
            $('#session_filter').change(function() {
                const sessionId = $(this).val();
                const url = new URL(window.location.href);

                if (sessionId === 'all') {
                    url.searchParams.delete('sesi_id');
                } else {
                    url.searchParams.set('sesi_id', sessionId);
                }

                // Reload the page with new filter
                window.location.href = url.toString();
            });

            // Edit button click
            $(document).on('click', '.edit-peserta', function() {
                const id = $(this).data('id');
                const pesertaId = $(this).data('peserta-id');
                const pesertaNama = $(this).data('nama');
                const url = "{{ route('pendaftaran.update', ':id') }}".replace(':id', id);

                $('#editForm').attr('action', url);
                $('#edit_peserta_nama').val(pesertaNama);
                $('#edit_peserta_id').val(pesertaId);
                $('#edit_sesi_id').val($(this).data('sesi'));
                $('#edit_status').val($(this).data('status'));

                $('#editModal').modal('show');
            });

            // Delete button click
            $(document).on('click', '.delete-peserta', function() {
                const id = $(this).data('id');
                const url = "{{ route('pendaftaran.destroy', ':id') }}".replace(':id', id);

                $('#deleteForm').attr('action', url);
                $('#delete_nama').text($(this).data('nama'));

                $('#deleteModal').modal('show');
            });

            // Show create modal
            $('#btnCreate').click(function() {
                $('#createModal').modal('show');
            });
        });
    </script>
    <script>
        $(document).on('click', '.show-qrcode', function() {
            const url = $(this).data('url');
            const modal = $('#qrCodeModal');

            $('#qrCodeContainer').html(
                '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
            $('#qrParticipantName').text('');
            $('#qrSessionName').text('');

            $.get(url, function(response) {
                $('#qrCodeContainer').html(response.svg);
                $('#qrParticipantName').text(response.nama);
                $('#qrSessionName').text(response.sesi);
                $('#qrDownloadBtn').attr('href', response.download_url);
                modal.modal('show');
            }).fail(function() {
                alert('Gagal memuat QR Code');
            });
        });
    </script>
@endpush
