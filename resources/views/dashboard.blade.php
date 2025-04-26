@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <style>
        .heading-with-logo {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1rem 0;
        }

        .header-logo {
            height: 4.375rem;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 0.125rem 0.25rem rgba(0, 0, 0, 0.1));
        }

        .heading-text {
            border-left: 0.125rem solid #e74c3c;
            padding-left: 1.25rem;
        }

        .page-heading h5 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.03125rem;
        }

        .page-heading p {
            margin: 0.3125rem 0 0;
            color: #7f8c8d;
            font-size: 0.9375rem;
            font-weight: 500;
        }


        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 48rem) {
            .heading-with-logo {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.9375rem;
            }

            .header-logo {
                height: 3.75rem;
            }

            .heading-text {
                border-left: none;
                padding-left: 0;
                border-top: 0.125rem solid #e74c3c;
                padding-top: 0.9375rem;
                width: 100%;
            }

        }
    </style>


    <div class="page-heading">
        <div class="heading-with-logo">
            <img src="{{ asset('uns.png') }}" alt="BPKP Logo" class="header-logo" style="width: 140px">
            <div class="heading-text">
                <h6>Aplikasi Manajemen Seminar</h6>
                <p>Sistem Terintegrasi untuk Pengelolaan dan Administrasi Seminar Secara Efektif.</p>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                                        <h6 class="card-title">Peserta Approved</h6>
                                        <p class="card-text fw-bold">10</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                        <h6 class="card-title">Peserta Rejected</h6>
                                        <p class="card-text fw-bold">10</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-hourglass-half fa-2x text-warning mb-2"></i>
                                        <h6 class="card-title">Peserta Waiting</h6>
                                        <p class="card-text fw-bold">10</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-ticket-alt fa-2x text-success mb-2"></i>
                                        <h6 class="card-title">Penjualan Tiket</h6>
                                        <p class="card-text fw-bold">10</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="card text-center">
                            <div class="card-body" style="min-height: 300px; max-height: 440px; overflow-y: hidden;">
                                <div class="gauge-container">
                                    <div class="gauge-header">
                                        <div class="gauge-title">Grafik Perbandingan Peserta Absen & Tidak Absen</div>
                                    </div>
                                    <figure class="highcharts-figure">
                                        <div id="container"></div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>
    </div>
@endsection
