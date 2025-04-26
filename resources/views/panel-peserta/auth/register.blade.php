<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pendaftaran Member</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet"
        href="{{asset('auth')}}/template/assets/plugins/animation/css/animate.min.css">
    <link rel="stylesheet" href="{{asset('auth')}}/template/assets/css/style.css">
    <link rel="stylesheet"
        href="{{asset('auth')}}/template/assets/plugins/notification/css/notification.min.css">
    <style>
        .alert span {
            cursor: pointer;
            padding-right: 5px;
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #5e72e4;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-link:hover {
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
                            <form method="POST" action="{{ route('panel-peserta.register') }}">
                                @csrf
                                <h4 class="mb-3 f-w-400">Daftar Peserta Baru</h4>

                                <div class="form-group">
                                    <label for="nama_mhs">Nama Lengkap</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input placeholder="Nama Lengkap" id="nama" type="text"
                                            class="form-control @error('nama') is-invalid @enderror" name="nama"
                                            value="{{ old('nama') }}" required autocomplete="name" autofocus>
                                        @error('nama')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="no_telp">Nomor Telepon</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input placeholder="Nomor Telepon" id="no_telepon" type="text"
                                            class="form-control @error('no_telepon') is-invalid @enderror"
                                            name="no_telepon" value="{{ old('no_telepon') }}" required>
                                        @error('no_telepon')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input placeholder="Email" id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <input placeholder="Alamat" id="alamat" type="text"
                                            class="form-control @error('alamat') is-invalid @enderror" name="alamat"
                                            value="{{ old('alamat') }}" required autocomplete="alamat">
                                        @error('alamat')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input placeholder="Password" id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirm">Konfirmasi Password</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input placeholder="Konfirmasi Password" id="password-confirm" type="password"
                                            class="form-control" name="password_confirmation" required
                                            autocomplete="new-password">
                                    </div>
                                </div>

                                <input type="submit" name="submit" value="Daftar" class="btn btn-primary mb-4" />

                                <a href="{{ route('panel-peserta.login') }}" class="login-link">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Sudah punya akun? Login disini
                                </a>
                            </form>
                        </div>

                    </div>
                    <div class="col-md-6 d-none d-md-block">
                        <div id="carouselExampleCaptions" class="carousel slide auth-slider" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                                <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                                <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="auth-prod-slidebg bg-1"></div>
                                    <div class="carousel-caption d-none d-md-block">
                                        <img src="{{asset('auth')}}/template/assets/images/product/c1.png"
                                            alt="product images" class="img-fluid mb-5">
                                        <h5>Aplikasi Pendaftaran Webinar (Seminar)</h5>
                                        <p class="mb-5">Sistem pendaftaran peserta webinar (seminar) menggunakan QR
                                            Code</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="auth-prod-slidebg bg-2"></div>
                                    <div class="carousel-caption d-none d-md-block">
                                        <img src="{{asset('auth')}}/template/assets/images/product/c2.jpg"
                                            alt="product images" class="img-fluid mb-5">
                                        <h5>Daftarkan diri Anda sekarang</h5>
                                        <p class="mb-5">Bergabung dengan komunitas webinar kami</p>
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
    <script src="{{asset('auth')}}/template/assets/js/vendor-all.min.js"></script>
    <script src="{{asset('auth')}}/template/assets/plugins/bootstrap/js/bootstrap.min.js">
    </script>
    <script
        src="{{asset('auth')}}/template/assets/plugins/notification/js/bootstrap-growl.min.js">
    </script>
</body>

</html>
