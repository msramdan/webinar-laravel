@extends('layouts.app')

@section('title', __('Detail of Seminar'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Seminar') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of seminar.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('seminar.index') }}">{{ __('Seminar') }}</a>
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
                                        <td class="fw-bold">{{ __('Nama Seminar') }}</td>
                                        <td>{{ $seminar->nama_seminar }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Deskripsi') }}</td>
                                        <td>{{ $seminar->deskripsi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Lampiran') }}</td>
                                        <td>
                                            @if (!$seminar->lampiran)
                                                <img src="https://via.placeholder.com/350?text=No+Image+Avaiable"
                                                    alt="Lampiran" class="rounded img-fluid">
                                            @else
                                                <img src="{{ asset('storage/uploads/lampirans/' . $seminar->lampiran) }}"
                                                    alt="Lampiran" style="width: 200px">
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Is Active') }}</td>
                                        <td>{{ $seminar->is_active }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('seminar.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
