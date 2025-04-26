@extends('layouts.app')

@section('title', 'Detail Seminar')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-8 order-md-1 order-last">
                <h3>{{ $seminar->nama_seminar }}</h3>
                <p class="text-subtitle text-muted">Detail lengkap seminar</p>
            </div>
            <x-breadcrumb>
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('seminar.index') }}">Seminar</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </x-breadcrumb>
        </div>
    </div>

    <section class="section">
        <!-- Seminar Header -->
        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="{{ asset('storage/uploads/lampirans/' . $seminar->lampiran) }}"
                         class="img-fluid rounded-start h-100"
                         style="object-fit: cover; min-height: 250px;"
                         alt="{{ $seminar->nama_seminar }}">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="badge bg-{{ $seminar->is_active == 'Yes' ? 'success' : 'danger' }}">
                                    {{ $seminar->is_active }}
                                </span>
                                <h2 class="card-title mt-2">{{ $seminar->nama_seminar }}</h2>
                            </div>
                            <a href="{{ route('seminar.index') }}" class="btn btn-outline-secondary h-25">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <p class="card-text mt-3">{{ $seminar->deskripsi }}</p>

                        <div class="d-flex flex-wrap gap-4 mt-4">
                            <div>
                                <h6 class="text-muted">Pembicara</h6>
                                <p class="h5">{{ $pembicaras->count() }}</p>
                            </div>
                            <div>
                                <h6 class="text-muted">Sponsor</h6>
                                <p class="h5">{{ $sponsors->count() }}</p>
                            </div>
                            <div>
                                <h6 class="text-muted">Sesi</h6>
                                <p class="h5">{{ $sesiSeminars->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Pembicara Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            Daftar Pembicara
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($pembicaras->isEmpty())
                            <div class="alert alert-info">Belum ada pembicara</div>
                        @else
                            <div class="row">
                                @foreach($pembicaras as $pembicara)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="{{ $pembicara->photo ? asset('storage/uploads/pembicara/' . $pembicara->photo) : 'https://ui-avatars.com/api/?name='.urlencode($pembicara->nama_pembicara).'&background=random' }}"
                                                         class="rounded-circle"
                                                         width="80"
                                                         height="80"
                                                         style="object-fit: cover;"
                                                         alt="{{ $pembicara->nama_pembicara }}">
                                                </div>
                                                <div>
                                                    <h5 class="card-title">{{ $pembicara->nama_pembicara }}</h5>
                                                    <p class="card-text text-muted small">{{ Str::limit($pembicara->latar_belakang, 100) }}</p>
                                                    <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pembicaraModal{{ $pembicara->id }}">
                                                        Lihat Profil
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pembicara Modal -->
                                <div class="modal fade" id="pembicaraModal{{ $pembicara->id }}" tabindex="-1" aria-labelledby="pembicaraModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="pembicaraModalLabel">{{ $pembicara->nama_pembicara }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-4">
                                                    <img src="{{ $pembicara->photo ? asset('storage/uploads/pembicara/' . $pembicara->photo) : 'https://ui-avatars.com/api/?name='.urlencode($pembicara->nama_pembicara).'&background=random' }}"
                                                         class="rounded-circle"
                                                         width="150"
                                                         height="150"
                                                         style="object-fit: cover;"
                                                         alt="{{ $pembicara->nama_pembicara }}">
                                                </div>
                                                <h6 class="text-muted">Latar Belakang:</h6>
                                                <p>{{ $pembicara->latar_belakang }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sesi Seminar Section -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-calendar-event text-success me-2"></i>
                            Jadwal Sesi Seminar
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($sesiSeminars->isEmpty())
                            <div class="alert alert-info">Belum ada sesi seminar</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Sesi</th>
                                            <th>Tanggal</th>
                                            <th>Kuota</th>
                                            <th>Harga Tiket</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sesiSeminars as $sesi)
                                        <tr>
                                            <td>{{ $sesi->nama_sesi }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($sesi->tanggal_pelaksanaan)->translatedFormat('d F Y H:i') }}
                                            </td>
                                            <td>{{ $sesi->kuota }}</td>
                                            <td>Rp {{ number_format($sesi->harga_tiket, 0, ',', '.') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    @if($sesi->link_gmeet)
                                                    <a href="{{ $sesi->link_gmeet }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-camera-video"></i>
                                                    </a>
                                                    @endif
                                                    @if($sesi->lampiran)
                                                    <a href="{{ asset('storage/uploads/sesi/' . $sesi->lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Sponsor Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-award text-warning me-2"></i>
                            Sponsor Seminar
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($sponsors->isEmpty())
                            <div class="alert alert-info">Belum ada sponsor</div>
                        @else
                            <div class="row row-cols-2 g-3">
                                @foreach($sponsors as $sponsor)
                                <div class="col">
                                    <div class="card border-0 text-center p-3 h-100">
                                        <div class="card-body">
                                            <img src="{{ $sponsor->gambar ? asset('storage/uploads/sponsor/' . $sponsor->gambar) : 'https://via.placeholder.com/150?text=No+Image' }}"
                                                 class="img-fluid mb-2"
                                                 style="max-height: 80px; width: auto;"
                                                 alt="{{ $sponsor->nama_sponsor }}">
                                            <h6 class="card-title mb-0">{{ $sponsor->nama_sponsor }}</h6>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: none;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        padding: 1rem 1.5rem;
    }

    .card-title {
        margin-bottom: 0;
        font-weight: 600;
    }

    .table th {
        border-top: none;
        font-weight: 600;
    }

    .img-thumbnail {
        padding: 0.25rem;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        max-width: 100%;
        height: auto;
    }
</style>
@endsection
