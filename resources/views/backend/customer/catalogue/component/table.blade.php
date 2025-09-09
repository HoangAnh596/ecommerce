<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên nhóm khách hàng</th>
            <th class="text-center">Số khách hàng</th>
            <th class="text-center">Ghi chú</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($customerCatalogues) && is_object($customerCatalogues))
        @foreach($customerCatalogues as $customerCatalogue)
        <tr>
            <td>
                <input type="checkbox" value="{{ $customerCatalogue->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>{{ $customerCatalogue->name }}</td>
            <td class="text-center">@if($customerCatalogue->customers_count > 0) {{ $customerCatalogue->customers_count }} người @else 0 người @endif</td>
            <td>{{ $customerCatalogue->description }}</td>
            <td class="text-center js-switch-{{ $customerCatalogue->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $customerCatalogue->publish }}" data-modelId="{{ $customerCatalogue->id }}" {{ ($customerCatalogue->publish == 1) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'customer.catalogue.update')
                <a href="{{ route('customer.catalogue.edit', $customerCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'customer.catalogue.destroy')
                <a href="{{ route('customer.catalogue.delete', $customerCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $customerCatalogues->links('pagination::bootstrap-4') }}