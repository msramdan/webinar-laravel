<!DOCTYPE html>
<html lang="en">

<head>
    <title>Halaman Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('auth') }}/template/assets/plugins/animation/css/animate.min.css">
    <link rel="stylesheet" href="{{ asset('auth') }}/template/assets/css/style.css">
    <link rel="stylesheet" href="{{ asset('auth') }}/template/assets/plugins/notification/css/notification.min.css">
    {!! NoCaptcha::renderJs('id', false, 'recaptchaCallback') !!}
    <style>
        .invalid-feedback-captcha {
            display: block;
            /* Pastikan pesan error terlihat */
            width: 100%;
            margin-top: .25rem;
            font-size: 80%;
            color: #dc3545;
            /* Warna error standar Bootstrap */
        }

        .alert span {
            cursor: pointer;
            padding-right: 5px;
        }

        .alert span {
            cursor: pointer;
            padding-right: 5px;
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #5e72e4;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-link:hover {
            color: #233dd2;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .auth-prod-slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-body {
            padding: 2.5rem;
        }

        .btn-primary {
            background: linear-gradient(to right, #5e72e4, #825ee4);
            border: none;
            width: 100%;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(94, 114, 228, 0.4);
        }

        .input-group-text {
            background-color: #f8f9fe;
        }

        .form-control {
            border-left: 0;
            background-color: #f8f9fe;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .auth-slider .carousel-item {
            height: 100%;
        }

        .auth-slider img {
            max-height: 200px;
            object-fit: contain;
        }

        .carousel-caption {
            bottom: 30%;
        }

        .login-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo-img {
            max-height: 60px;
            width: auto;
            object-fit: contain;
            filter: grayscale(20%);
            opacity: 0.9;
            transition: all 0.3s ease;
        }

        .logo-img:hover {
            opacity: 1;
            filter: grayscale(0%);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        .register-link {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s;
        }

        .register-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .btn-primary {
            padding: 10px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body class="auth-prod-slider">
    <div class="blur-bg-images"></div>
    <div class="auth-wrapper">
        <div class="auth-content container">
            <div class="card">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-5">
                        <div class="card-body">
                            <form method="POST" action="{{ route('panel-peserta.login') }}" class="login-form">
                                @csrf
                                <div class="logo-container mb-4">
                                    <img src="{{ asset('kampus.png') }}" alt="Logo Kampus" class="logo-img">
                                    <img src="{{ asset('uns.png') }}" alt="Logo UNS" class="logo-img">
                                    <img src="{{ asset('uns-biru.png') }}" alt="Logo UNS Biru" class="logo-img">
                                </div>

                                <h4 class="mb-3 f-w-400 text-center">Silahkan login!</h4>

                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('status') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @error('verification')
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ $message }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @enderror


                                <div class="form-group">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email" autofocus
                                            placeholder="Alamat Email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        </div>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="current-password" placeholder="Password">
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! NoCaptcha::display(['data-theme' => 'light']) !!} @error('g-recaptcha-response')
                                        <span class="invalid-feedback-captcha" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mb-4">Login</button>

                                <div class="text-center">
                                    <a href="{{ route('panel-peserta.register') }}" class="register-link">
                                        <i class="fas fa-user-plus mr-2"></i>Daftar sebagai Peserta
                                    </a>
                                </div>
                            </form>

                            <div class="sponsor-carousel mt-5">
                                <h5 class="text-center mb-3">Didukung oleh:</h5>
                                <div id="sponsorCarousel" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        @for ($i = 0; $i < count($sponsors); $i += 2)
                                            <div class="carousel-item @if ($i == 0) active @endif">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="sponsor-item mx-3">
                                                        @if ($sponsors[$i]->gambar)
                                                            <img src="{{ asset('storage/uploads/sponsor/' . $sponsors[$i]->gambar) }}"
                                                                class="img-fluid" style="max-height: 60px;">
                                                        @else
                                                            <div class="sponsor-placeholder">
                                                                <i class="fas fa-image fa-2x"></i>
                                                                <p class="mt-2">{{ $sponsors[$i]->nama }}</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if ($i + 1 < count($sponsors))
                                                        <div class="sponsor-item mx-3">
                                                            @if ($sponsors[$i + 1]->gambar)
                                                                <img src="{{ asset('storage/uploads/sponsor/' . $sponsors[$i + 1]->gambar) }}"
                                                                    class="img-fluid" style="max-height: 60px;">
                                                            @else
                                                                <div class="sponsor-placeholder">
                                                                    <i class="fas fa-image fa-2x"></i>
                                                                    <p class="mt-2">{{ $sponsors[$i + 1]->nama }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <a class="carousel-control-prev" href="#sponsorCarousel" role="button"
                                        data-slide="prev">
                                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"
                                            aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#sponsorCarousel" role="button"
                                        data-slide="next">
                                        <span class="carousel-control-next-icon bg-dark rounded-circle p-2"
                                            aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-none d-md-block">
                        <div id="carouselExampleCaptions" class="carousel slide auth-slider" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                                <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                                <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
                                <li data-target="#carouselExampleCaptions" data-slide-to="3"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="auth-prod-slidebg bg-1"></div>
                                    <div class="carousel-caption d-none d-md-block">
                                        <img src="{{ asset('auth') }}/template/assets/images/product/c1.png"
                                            alt="product images" class="img-fluid mb-5">
                                        <h5>Aplikasi Pendaftaran Webinar (Seminar)</h5>
                                        <p class="mb-5">Sistem pendaftaran peserta webinar (seminar) menggunakan QR
                                            Code</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="auth-prod-slidebg bg-2"></div>
                                    <div class="carousel-caption d-none d-md-block">
                                        <img src="{{ asset('auth') }}/template/assets/images/product/c2.jpg"
                                            alt="product images" class="img-fluid mb-5">
                                        <h5>Memudahkan pengguna untuk melakukan pendaftaran webinar (seminar)</h5>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="auth-prod-slidebg bg-3"></div>
                                    <div class="carousel-caption d-none d-md-block">
                                        <img src="{{ asset('auth') }}/template/assets/images/product/c3.jpg"
                                            alt="product images" class="img-fluid mb-5">
                                        <h5>Cukup melakukan scan QR</h5>
                                    </div>
                                </div>
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button"
                                data-slide="prev"><span class="carousel-control-prev-icon"
                                    aria-hidden="true"></span></a>
                            <a class="carousel-control-next" href="#carouselExampleCaptions" role="button"
                                data-slide="next"><span class="carousel-control-next-icon"
                                    aria-hidden="true"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('auth') }}/template/assets/js/vendor-all.min.js"></script>
    <script src="{{ asset('auth') }}/template/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="{{ asset('auth') }}/template/assets/plugins/notification/js/bootstrap-growl.min.js"></script>
    <script>
        // Auto-rotate carousel setiap 3 detik
        $(document).ready(function() {
            $('#sponsorCarousel').carousel({
                interval: 3000
            });
        });

        // Callback function (opsional, bisa kosong jika tidak ada aksi khusus)
        var recaptchaCallback = function() {
            console.log('reCAPTCHA ter-render.');
        };
    </script>

</body>

</html>
