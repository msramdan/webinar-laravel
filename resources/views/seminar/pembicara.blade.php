@extends('layouts.app')

@section('title', __('Seminar'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Seminar') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar pembicara dari Seminar.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('seminar.index') }}">{{ __('Seminar') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Seminar') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <!-- Card Pertama: Informasi Sieminar -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama Seminar</strong></td>
                                    <td>: </td>
                                </tr>
                                {{-- dan lain lain --}}
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <a href="{{ route('seminar.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                                </a>
                            </div>

                            <div class="table-responsive p-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush
