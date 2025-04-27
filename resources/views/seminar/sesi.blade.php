@extends('layouts.app')

@section('title', 'Sesi Seminar')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>Sesi Seminar</h3>
                    <p class="text-subtitle text-muted">
                        Kelola sesi untuk seminar: <strong>{{ $seminar->nama_seminar }}</strong>
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('seminar.index') }}">Seminar</a></li>
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
                                    @can('sesi create')
                                        <a href="{{ route('seminar.index') }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sesiModal">
                                            <i class="fas fa-plus"></i> Tambah Sesi
                                        </button>
                                    @endcan
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
                                        <th>Link GMeet</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="sesiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Sesi Seminar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="sesiForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="sesiId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_sesi">Nama Sesi</label>
                                    <input type="text" class="form-control" id="nama_sesi" name="nama_sesi" required>
                                </div>
                                <div class="form-group">
                                    <label for="kuota">Kuota Peserta</label>
                                    <input type="number" class="form-control" id="kuota" name="kuota" min="1"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="harga_tiket">Harga Tiket (Rp)</label>
                                    <input type="number" class="form-control" id="harga_tiket" name="harga_tiket"
                                        min="0" step="1000" required>
                                </div>
                                <div class="form-group">
                                    <label for="tempat_seminar">Tempat Seminar</label>
                                    <input type="text" class="form-control" id="tempat_seminar" name="tempat_seminar" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_pelaksanaan">Tanggal Pelaksanaan</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_pelaksanaan"
                                        name="tanggal_pelaksanaan" required>
                                </div>
                                <div class="form-group">
                                    <label for="link_gmeet">Link Google Meet</label>
                                    <input type="url" class="form-control" id="link_gmeet" name="link_gmeet"
                                        placeholder="https://meet.google.com/xxx-yyyy-zzz">
                                </div>
                                <div class="form-group">
                                    <label for="lampiran">Lampiran (PDF/DOC)</label>
                                    <input type="file" class="form-control" id="lampiran" name="lampiran"
                                        accept=".pdf,.doc,.docx">
                                    <small class="text-muted">Maksimal 2MB</small>
                                    <div id="lampiranPreview" class="mt-2"></div>
                                </div>
                            </div>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus sesi ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
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
                    url: "{{ route('sesi.data', ['seminar' => $seminar->id]) }}",
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
                        data: 'link_gmeet',
                        name: 'link_gmeet',
                        render: function(data) {
                            return data ? `<a href="${data}" target="_blank">Join Meeting</a>` :
                                '-';
                        }
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
                ] // Default order by tanggal_pelaksanaan
            });

            // Reset form and open modal for create
            $('#sesiModal').on('show.bs.modal', function(e) {
                $('#sesiForm')[0].reset();
                $('#modalTitle').text('Tambah Sesi Seminar');
                $('#sesiId').val('');
                $('#lampiranPreview').empty();
                $('#lampiran').attr('required', false);
            });

            // Edit button click
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                // Show loading state
                $('#modalTitle').text('Memuat...');
                $('#sesiModal').modal('show');

                $.ajax({
                    url: `/seminar/${seminarId}/sesi/${id}`,
                    type: 'GET',
                    success: function(response) {
                        $('#modalTitle').text('Edit Sesi Seminar');
                        $('#sesiId').val(response.id);
                        $('#nama_sesi').val(response.nama_sesi);
                        $('#kuota').val(response.kuota);
                        $('#harga_tiket').val(response.harga_tiket);
                        $('#link_gmeet').val(response.link_gmeet);
                        $('#tempat_seminar').val(response.tempat_seminar);

                        // Format datetime for input
                        const dt = new Date(response.tanggal_pelaksanaan);
                        const dtLocal = dt.toISOString().slice(0, 16);
                        $('#tanggal_pelaksanaan').val(dtLocal);

                        // Show attachment if exists
                        $('#lampiranPreview').empty();
                        if (response.lampiran) {
                            $('#lampiranPreview').html(`
                            <div class="alert alert-info p-2">
                                <i class="fas fa-paperclip me-2"></i>
                                <a href="/storage/uploads/sesi/${response.lampiran}" target="_blank">Lihat Lampiran</a>
                            </div>
                        `);
                        }

                        $('#lampiran').removeAttr('required');
                    },
                    error: function(xhr) {
                        $('#sesiModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.status === 404 ? 'Sesi tidak ditemukan' :
                                'Gagal memuat data sesi'
                        });
                    }
                });
            });

            // Form submission
            $('#sesiForm').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const id = $('#sesiId').val();
                const isEdit = !!id;
                const url = isEdit ?
                    `/seminar/${seminarId}/sesi/${id}` :
                    `/seminar/${seminarId}/sesi`;

                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                if (isEdit) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#sesiModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: isEdit ?
                                'Sesi seminar berhasil diperbarui' :
                                'Sesi seminar berhasil ditambahkan'
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessage += value + '<br>';
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage
                        });
                    }
                });
            });

            // Delete button click
            let deleteId;
            $(document).on('click', '.btn-delete', function() {
                deleteId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            // Confirm delete
            $('#confirmDelete').click(function() {
                $.ajax({
                    url: `/seminar/${seminarId}/sesi/${deleteId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.success
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus sesi'
                        });
                    }
                });
            });
        });
    </script>
@endpush
