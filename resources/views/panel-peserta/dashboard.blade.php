@extends('panel-peserta.layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
<div class="card">
    <div class="card-header">Dashboard Peserta</div>
    <div class="card-body">
        <h4>Selamat datang, {{ Auth::guard('panel-peserta')->user()->nama }}!</h4>
        <p>Email: {{ Auth::guard('panel-peserta')->user()->email }}</p>
        <p>No. Telepon: {{ Auth::guard('panel-peserta')->user()->no_telepon }}</p>
        <p>Alamat: {{ Auth::guard('panel-peserta')->user()->alamat }}</p>
    </div>
</div>
@endsection
