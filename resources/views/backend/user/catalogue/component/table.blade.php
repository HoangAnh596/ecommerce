<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên nhóm thành viên</th>
            <th class="text-center">Số thành viên</th>
            <th class="text-center">Ghi chú</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($userCatalogues) && is_object($userCatalogues))
        @foreach($userCatalogues as $userCatalogue)
        <tr>
            <td>
                <input type="checkbox" value="{{ $userCatalogue->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>{{ $userCatalogue->name }}</td>
            <td class="text-center">@if($userCatalogue->users_count > 0) {{ $userCatalogue->users_count }} người @endif</td>
            <td>{{ $userCatalogue->description }}</td>
            <td class="text-center js-switch-{{ $userCatalogue->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $userCatalogue->publish }}" data-modelId="{{ $userCatalogue->id }}" {{ ($userCatalogue->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'user.catalogue.update')
                <a href="{{ route('user.catalogue.edit', $userCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'user.catalogue.destroy')
                <a href="{{ route('user.catalogue.delete', $userCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $userCatalogues->links('pagination::bootstrap-4') }}