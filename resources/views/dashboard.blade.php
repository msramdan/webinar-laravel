@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="page-heading">
        <div class="heading-with-logo">

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
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="card text-center shadow-sm border-0"
                                    style="border-radius: 16px; background: #f9fafe; transition: transform 0.3s;">
                                    <div class="card-body d-flex align-items-center justify-content-center"
                                        style="height: 168px; overflow: hidden; ">
                                        <img src="{{ asset('kampus.png') }}" alt="" class="header-logo"
                                            style="width: auto; height: 90%; max-height: 140px; object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0"
                                    style="border-radius: 16px; background: #f9fafe; transition: transform 0.3s;">
                                    <div class="card-body d-flex align-items-center justify-content-center"
                                        style="height: 168px; overflow: hidden; ">
                                        <img src="{{ asset('uns.png') }}" alt="" class="header-logo"
                                            style="width: auto; height: 90%; max-height: 140px; object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0"
                                    style="border-radius: 16px; background: #f9fafe; transition: transform 0.3s;">
                                    <div class="card-body d-flex align-items-center justify-content-center"
                                        style="height: 168px; overflow: hidden; ">
                                        <img src="{{ asset('uns-biru.png') }}" alt="" class="header-logo"
                                            style="width: auto; height: 90%; max-height: 140px; object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body" style="min-height: 300px; max-height: 370px; overflow-y: hidden;">
                                <div class="gauge-container">
                                    <div class="gauge-header">
                                        <div class="gauge-title">Persentase Kehadiran Peserta Seminar</div>
                                    </div>
                                    <figure class="highcharts-figure">
                                        <div id="container"></div>
                                        <div class="gauge-value-display">
                                            <div class="gauge-current-value" id="current-value">{{ $persentaseKehadiran}} %</div>
                                            <div class="gauge-value-label">Skor Saat Ini</div>
                                        </div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body"
                                        style="min-height: 168px; max-height: 200px; overflow-y: hidden;">
                                        <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                                        <h6 class="card-title">Peserta Approved</h6>
                                        <p class="card-text fw-bold">{{ $approvedCount }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body"
                                        style="min-height: 168px; max-height: 200px; overflow-y: hidden;">
                                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                        <h6 class="card-title">Peserta Rejected</h6>
                                        <p class="card-text fw-bold">{{ $rejectedCount }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body"
                                        style="min-height: 168px; max-height: 200px; overflow-y: hidden;">
                                        <i class="fas fa-hourglass-half fa-2x text-warning mb-2"></i>
                                        <h6 class="card-title">Peserta Waiting</h6>
                                        <p class="card-text fw-bold">{{ $waitingCount }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body"
                                        style="min-height: 168px; max-height: 200px; overflow-y: hidden;">
                                        <i class="fas fa-ticket-alt fa-2x text-success mb-2"></i>
                                        <h6 class="card-title">Penjualan Tiket</h6>
                                        <p class="card-text fw-bold">{{ formatRupiah($totalPenjualan)  }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>
    </div>
@endsection


@push('css')
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

        .gauge-container {
            position: relative;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .gauge-header {
            margin-bottom: 1.25rem;
        }

        .gauge-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.3125rem;
            text-align: center;
        }

        .gauge-value-display {
            position: absolute;
            bottom: 30%;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 10;
        }

        .gauge-current-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.125rem;
            text-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
            line-height: 1.2;
        }

        .gauge-value-label {
            font-size: 0.875rem;
            /* 14px */
            color: #7f8c8d;
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

        .gauge-current-value.updated {
            animation: pulse 0.5s ease-in-out;
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

            .gauge-title {
                font-size: 1rem;
            }

            .gauge-current-value {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush

@push('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        var chartLevel3;
        document.addEventListener('DOMContentLoaded', function() {
            chartLevel3 = Highcharts.chart('container', {
                chart: {
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false,
                    height: '80%',
                    backgroundColor: 'transparent'
                },
                exporting: {
                    enabled: false
                },
                title: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                pane: {
                    startAngle: -90,
                    endAngle: 89.9,
                    background: null,
                    center: ['50%', '75%'],
                    size: '110%',
                    borderWidth: 0
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    tickPixelInterval: 72,
                    tickPosition: 'inside',
                    tickColor: '#f8f9fa',
                    tickLength: 15,
                    tickWidth: 2,
                    minorTickInterval: null,
                    labels: {
                        distance: 25,
                        style: {
                            fontSize: '12px',
                            color: '#95a5a6'
                        }
                    },
                    lineWidth: 0,
                    plotBands: [{
                            from: 0,
                            to: 25,
                            color: '#e74c3c',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 25,
                            to: 50,
                            color: '#f39c12',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 50,
                            to: 75,
                            color: '#3498db',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 75,
                            to: 100,
                            color: '#2ecc71',
                            thickness: 20,
                            borderRadius: 5
                        }
                    ]
                },
                series: [{
                    name: 'Skor',
                    data: [{{ $persentaseKehadiran }}],
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    dial: {
                        radius: '80%',
                        backgroundColor: '#34495e',
                        baseWidth: 12,
                        baseLength: '0%',
                        rearLength: '0%',
                        borderWidth: 1,
                        borderColor: '#fff'
                    },
                    pivot: {
                        backgroundColor: '#34495e',
                        radius: 6,
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }]
            });

        });
    </script>
@endpush
