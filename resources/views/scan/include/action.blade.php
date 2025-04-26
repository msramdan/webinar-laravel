<td>
    @can('scan view')
        <a href="{{ route('scan.show', $model->id) }}" class="btn btn-danger btn-sm">
            <i class="fa fa-camera"></i> OpenCam
        </a>
    @endcan
</td>
