<td>
    @can('pendaftaran view')
        <a href="{{ route('pendaftaran.show', $row->id) }}" class="btn btn-success btn-sm">
            <i class="fa fa-eye"></i> Daftar Peserta
        </a>
    @endcan
</td>
