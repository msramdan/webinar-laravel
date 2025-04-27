<td>
    @can('laporan presensi view')
        <a href="{{ route('laporan.sesi.seminar', $row->id) }}" class="btn btn-success btn-sm">
            <i class="fa fa-book"></i> Rekap Presensi
        </a>
    @endcan
</td>
