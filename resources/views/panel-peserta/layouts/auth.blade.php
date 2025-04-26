<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@yield('title') - {{ config('app.name') }}</title>

    <link href="{{ asset('peserta/temp/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('peserta/temp/css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-xl-6 col-lg-6 col-md-6">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Selamat Datang Kembali!</h1>
                            </div>
                            @yield('content')
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <script src="{{ asset('peserta/temp/vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('peserta/temp/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('peserta/temp/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
        <script src="{{ asset('peserta/temp/js/sb-admin-2.min.js') }}"></script>
        @stack('js')
</body>

</html>
