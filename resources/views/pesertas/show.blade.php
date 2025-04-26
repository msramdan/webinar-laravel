@extends('layouts.app')

@section('title', __('Detail of Pesertas'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Pesertas') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of pesertum.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('pesertas.index') }}">{{ __('Pesertas') }}</a>
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
                    <td class="fw-bold">{{ __('Nama') }}</td>
                    <td>{{ $pesertum->nama }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('No Telepon') }}</td>
                    <td>{{ $pesertum->no_telepon }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Email') }}</td>
                    <td>{{ $pesertum->email }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Alamat') }}</td>
                    <td>{{ $pesertum->alamat }}</td>
                </tr>

                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $pesertum->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $pesertum->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('pesertas.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
