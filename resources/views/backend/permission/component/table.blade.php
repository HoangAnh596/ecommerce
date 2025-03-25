<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tiêu đề</th>
            <th class="text-center">Canonical</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($permissions) && is_object($permissions))
        @foreach($permissions as $permission)
        <tr>
            <td>
                <input type="checkbox" value="{{ $permission->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>{{ $permission->name }}</td>
            <td class="text-center">{{ $permission->canonical }}</td>
            <td class="text-center">
                @can('modules', 'permission.update')
                <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'permission.destroy')
                <a href="{{ route('permission.delete', $permission->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $permissions->links('pagination::bootstrap-4') }}