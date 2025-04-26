@extends('layouts.app')

@section('title', 'Pembicara Seminar')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>Pembicara Seminar</h3>
                    <p class="text-subtitle text-muted">
                        Kelola pembicara untuk seminar: <strong>{{ $seminar->nama_seminar }}</strong>
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('seminar.index') }}">Seminar</a></li>
                    <li class="breadcrumb-item active">Pembicara</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title">Daftar Pembicara</h4>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pembicaraModal">
                                    <i class="fas fa-plus"></i> Tambah Pembicara
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive p-1">
                                <table class="table table-hover" id="pembicara-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Foto</th>
                                            <th>Nama Pembicara</th>
                                            <th>Latar Belakang</th>
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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="pembicaraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Pembicara</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="pembicaraForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="pembicaraId" name="id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="photo">Foto Pembicara</label>
                                    <div class="text-center mb-3">
                                        <img id="photoPreview" src="https://via.placeholder.com/150"
                                            class="img-thumbnail rounded-circle"
                                            style="width: 150px; height: 150px; object-fit: cover;">
                                    </div>
                                    <input type="file" class="form-control" id="photo" name="photo"
                                        accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nama_pembicara">Nama Pembicara</label>
                                    <input type="text" class="form-control" id="nama_pembicara" name="nama_pembicara"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="latar_belakang">Latar Belakang</label>
                                    <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="5" required></textarea>
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
                    <p>Apakah Anda yakin ingin menghapus pembicara ini?</p>
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
        .img-thumbnail {
            object-fit: cover;
        }

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
            let table = $('#pembicara-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pembicara.data', ['seminar' => $seminar->id]) }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_pembicara',
                        name: 'nama_pembicara'
                    },
                    {
                        data: 'latar_belakang',
                        name: 'latar_belakang'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Edit button click handler
            $(document).on('click', '.btn-edit', function() {
                const pembicaraId = $(this).data('id');

                // Show loading state
                $('#modalTitle').text('Memuat...');
                $('#pembicaraModal').modal('show');

                $.ajax({
                    url: `/seminar/${seminarId}/pembicara/${pembicaraId}`,
                    type: 'GET',
                    success: function(response) {
                        $('#modalTitle').text('Edit Pembicara');
                        $('#pembicaraId').val(response.id);
                        $('#nama_pembicara').val(response.nama_pembicara);
                        $('#latar_belakang').val(response.latar_belakang);

                        if (response.photo) {
                            $('#photoPreview').attr('src',
                                `/storage/uploads/pembicara/${response.photo}`);
                        } else {
                            $('#photoPreview').attr('src', 'https://via.placeholder.com/150');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data pembicara'
                        });
                        $('#pembicaraModal').modal('hide');
                    }
                });
            });

            // Form submission handler
            $('#pembicaraForm').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const pembicaraId = $('#pembicaraId').val();
                const isEdit = !!pembicaraId;

                // Set the correct URL based on whether we're editing or creating
                const url = isEdit ?
                    `/seminar/${seminarId}/pembicara/${pembicaraId}` :
                    `/seminar/${seminarId}/pembicara`;

                // Add CSRF token
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // For edit requests, add _method field
                if (isEdit) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST', // Always use POST, Laravel will handle PUT via _method
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#pembicaraModal').modal('hide');
                        table.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: isEdit ?
                                'Pembicara berhasil diperbarui' :
                                'Pembicara berhasil ditambahkan'
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

            // Delete button handler
            let deleteId;
            $(document).on('click', '.btn-delete', function() {
                deleteId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').click(function() {
                $.ajax({
                    url: `/seminar/${seminarId}/pembicara/${deleteId}`,
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
                            text: 'Gagal menghapus pembicara'
                        });
                    }
                });
            });
        });
    </script>
@endpush
