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
        @if(isset($postCatalogues) && is_object($postCatalogues))
        @foreach($postCatalogues as $postCatalogue)
        <tr>
            <td>
                <input type="checkbox" value="{{ $postCatalogue->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                {{ str_repeat('|----', (($postCatalogue->level > 0) ? ($postCatalogue->level - 1) : 0)).$postCatalogue->name }}
            </td>
            @include('backend.dashboard.component.languageTd', ['model' => $postCatalogue, 'modeling' => 'PostCatalogue'])
            <td class="text-center js-switch-{{ $postCatalogue->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $postCatalogue->publish }}" data-modelId="{{ $postCatalogue->id }}" {{ ($postCatalogue->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'post.catalogue.update')
                <a href="{{ route('post.catalogue.edit', $postCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.catalogue.destroy')
                <a href="{{ route('post.catalogue.delete', $postCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $postCatalogues->links('pagination::bootstrap-4') }}