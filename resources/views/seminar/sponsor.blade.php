@extends('layouts.app')

@section('title', 'Sponsor Seminar')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>Sponsor Seminar</h3>
                    <p class="text-subtitle text-muted">
                        Kelola sponsor untuk seminar: <strong>{{ $seminar->nama_seminar }}</strong>
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('seminar.index') }}">Seminar</a></li>
                    <li class="breadcrumb-item active">Sponsor</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title">Daftar Sponsor</h4>
                                <div class="col-md-6 text-end">
                                    @can('sponsor create')
                                        <a href="{{ route('seminar.index') }}" class="btn btn-secondary me-2">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sponsorModal">
                                            <i class="fas fa-plus"></i> Tambah Sponsor
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive p-1">
                                <table class="table table-hover" id="sponsor-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Logo</th>
                                            <th>Nama Sponsor</th>
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
    <div class="modal fade" id="sponsorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Sponsor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="sponsorForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="sponsorId" name="id">
                        <div class="form-group">
                            <label for="nama_sponsor">Nama Sponsor</label>
                            <input type="text" class="form-control" id="nama_sponsor" name="nama_sponsor" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="gambar">Logo Sponsor</label>
                            <div class="text-center mb-3">
                                <img id="gambarPreview" src="https://via.placeholder.com/200?text=Upload+Logo"
                                    class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <small class="text-muted">Format: JPEG, PNG, JPG (Max: 2MB)</small>
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
                    <p>Apakah Anda yakin ingin menghapus sponsor ini?</p>
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
            object-fit: contain;
            background-color: #f8f9fa;
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
            let table = $('#sponsor-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sponsor.data', ['seminar' => $seminar->id]) }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'gambar',
                        name: 'gambar',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_sponsor',
                        name: 'nama_sponsor'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Reset form and open modal for create
            $('#sponsorModal').on('show.bs.modal', function(e) {
                $('#sponsorForm')[0].reset();
                $('#gambarPreview').attr('src', 'https://via.placeholder.com/200?text=Upload+Logo');
                $('#modalTitle').text('Tambah Sponsor');
                $('#sponsorId').val('');
                $('#gambar').attr('required', true);
            });

            // Preview image before upload
            $('#gambar').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#gambarPreview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Edit button click
            // Edit button click - Updated version
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                // Show loading state
                $('#modalTitle').text('Memuat...');
                $('#sponsorModal').modal('show');

                $.ajax({
                    url: `/seminar/${seminarId}/sponsor/${id}`,
                    type: 'GET',
                    success: function(response) {
                        $('#modalTitle').text('Edit Sponsor');
                        $('#sponsorId').val(response.id);
                        $('#nama_sponsor').val(response.nama_sponsor);

                        // Set the image preview
                        if (response.gambar) {
                            $('#gambarPreview').attr('src',
                                `/storage/uploads/sponsor/${response.gambar}`);
                        } else {
                            $('#gambarPreview').attr('src',
                                'https://via.placeholder.com/200?text=No+Logo');
                        }

                        // Make image field optional for edits
                        $('#gambar').removeAttr('required');
                    },
                    error: function(xhr) {
                        $('#sponsorModal').modal('hide');
                        if (xhr.status === 404) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Sponsor tidak ditemukan'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal memuat data sponsor'
                            });
                        }
                        table.ajax.reload();
                    }
                });
            });

            // Form submission
            $('#sponsorForm').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const id = $('#sponsorId').val();
                const isEdit = !!id;
                const url = isEdit ?
                    `/seminar/${seminarId}/sponsor/${id}` :
                    `/seminar/${seminarId}/sponsor`;

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
                        $('#sponsorModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: isEdit ? 'Sponsor berhasil diperbarui' :
                                'Sponsor berhasil ditambahkan'
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
                    url: `/seminar/${seminarId}/sponsor/${deleteId}`,
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
                            text: 'Gagal menghapus sponsor'
                        });
                    }
                });
            });
        });
    </script>
@endpush
