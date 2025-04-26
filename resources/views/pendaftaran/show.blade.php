@extends('layouts.app')

@section('title', __('Detail of Pendaftaran'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Pendaftaran') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of Pendaftaran.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('pendaftaran.index') }}">{{ __('Pendaftaran') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Detail') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <tr>
                    <td class="fw-bold">{{ __('Sesi') }}</td>
                    <td>{{ $pendaftaran->sesi ? $pendaftaran->sesi->id : '' }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Pesertum') }}</td>
                    <td>{{ $pendaftaran->pesertum ? $pendaftaran->pesertum->id : '' }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Status') }}</td>
                    <td>{{ $pendaftaran->status }}</td>
                </tr>
<tr>
                                <td class="fw-bold">{{ __('Tanggal Pengajuan') }}</td>
                                <td>{{ isset($pendaftaran->tanggal_pengajuan) ? $pendaftaran->tanggal_pengajuan?->format("Y-m-d H:i:s") : '' }}</td>
                               </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $pendaftaran->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $pendaftaran->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
