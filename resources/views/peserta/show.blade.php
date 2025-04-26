@extends('layouts.app')

@section('title', __('Detail of Peserta'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Peserta') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of peserta.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('peserta.index') }}">{{ __('Peserta') }}</a>
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
                    <td>{{ $peserta->nama }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('No Telepon') }}</td>
                    <td>{{ $peserta->no_telepon }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Email') }}</td>
                    <td>{{ $peserta->email }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Alamat') }}</td>
                    <td>{{ $peserta->alamat }}</td>
                </tr>

                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $peserta->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $peserta->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('peserta.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
