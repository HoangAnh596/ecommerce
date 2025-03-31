<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="width: 50px">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên Module</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($generates) && is_object($generates))
        @foreach($generates as $generate)
        <tr>
            <td>
                <input type="checkbox" value="{{ $generate->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>{{ $generate->name }}</td>
            <td class="text-center">
                @can('modules', 'generate.update')
                <a href="{{ route('generate.edit', $generate->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'generate.destroy')
                <a href="{{ route('generate.delete', $generate->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $generates->links('pagination::bootstrap-4') }}