<!DOCTYPE html>
<html lang="en">
<head>
{{-- meta --}}
@include('panel-peserta.layouts._dashboard.meta')
{{-- style --}}
@include('panel-peserta.layouts._dashboard.style')
@stack('css')
</head>
<body id="page-top">
    <div id="wrapper">
        {{-- sidebar --}}
        @include('panel-peserta.layouts._dashboard.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                {{-- topbar --}}
                @include('panel-peserta.layouts._dashboard.topbar')
                @yield('content')
            </div>
            {{-- footer --}}
            @include('panel-peserta.layouts._dashboard.footer')
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    {{-- logout modal --}}
    @include('panel-peserta.layouts._dashboard.logoutModal')
    {{-- script --}}
    @include('panel-peserta.layouts._dashboard.script')
    @stack('js')
    @include('sweetalert::alert')
</body>

</html>
