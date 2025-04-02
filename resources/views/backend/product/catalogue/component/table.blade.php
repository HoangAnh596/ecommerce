<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 30px">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">{{ __('messages.tableName') }}</th>
            @include('backend.dashboard.component.languageTh')
            <th class="text-center" style="width: 100px">{{ __('messages.tableStatus') }}</th>
            <th class="text-center" style="width: 100px">{{ __('messages.tableAction') }}</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($productCatalogues) && is_object($productCatalogues))
        @foreach($productCatalogues as $productCatalogue)
        <tr>
            <td>
                <input type="checkbox" value="{{ $productCatalogue->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                {{ str_repeat('|----', (($productCatalogue->level > 0) ? ($productCatalogue->level - 1) : 0)).$productCatalogue->name }}
            </td>
            @include('backend.dashboard.component.languageTd', ['model' => $productCatalogue, 'modeling' => 'ProductCatalogue'])
            <td class="text-center js-switch-{{ $productCatalogue->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $productCatalogue->publish }}" data-modelId="{{ $productCatalogue->id }}" {{ ($productCatalogue->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'product.catalogue.update')
                <a href="{{ route('product.catalogue.edit', $productCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'product.catalogue.destroy')
                <a href="{{ route('product.catalogue.delete', $productCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $productCatalogues->links('pagination::bootstrap-4') }}