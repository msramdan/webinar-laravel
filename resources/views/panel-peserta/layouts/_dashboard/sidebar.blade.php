<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-desktop"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Seminar</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link" href="{{route('panel-peserta.seminarSaya')}}">
            <i class="fas fa-fw fa-list"></i>
            <span>Seminar Saya</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('panel-peserta.semuaSeminar')}}">
            <i class="fas fa-fw fa-table"></i>
            <span>Semua Seminar</span></a>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
