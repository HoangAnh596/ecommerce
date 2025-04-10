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
        @if(isset($attributeCatalogues) && is_object($attributeCatalogues))
        @foreach($attributeCatalogues as $attributeCatalogue)
        <tr>
            <td>
                <input type="checkbox" value="{{ $attributeCatalogue->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                {{ str_repeat('|----', (($attributeCatalogue->level > 0) ? ($attributeCatalogue->level - 1) : 0)).$attributeCatalogue->name }}
            </td>
            @include('backend.dashboard.component.languageTd', ['model' => $attributeCatalogue, 'modeling' => 'AttributeCatalogue'])
            <td class="text-center js-switch-{{ $attributeCatalogue->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $attributeCatalogue->publish }}" data-modelId="{{ $attributeCatalogue->id }}" {{ ($attributeCatalogue->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'attribute.catalogue.update')
                <a href="{{ route('attribute.catalogue.edit', $attributeCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'attribute.catalogue.destroy')
                <a href="{{ route('attribute.catalogue.delete', $attributeCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $attributeCatalogues->links('pagination::bootstrap-4') }}