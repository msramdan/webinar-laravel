<td>
    @can('pendaftaran view')
        <a href="{{ route('pendaftaran.peserta.sesi', $row->id) }}" class="btn btn-success btn-sm">
            <i class="fa fa-eye"></i> Daftar Peserta
        </a>
    @endcan
</td>
