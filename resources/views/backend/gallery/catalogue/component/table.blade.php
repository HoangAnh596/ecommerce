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
        @if(isset($galleryCatalogues) && is_object($galleryCatalogues))
        @foreach($galleryCatalogues as $galleryCatalogue)
        <tr>
            <td>
                <input type="checkbox" value="{{ $galleryCatalogue->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                {{ str_repeat('|----', (($galleryCatalogue->level > 0) ? ($galleryCatalogue->level - 1) : 0)).$galleryCatalogue->name }}
            </td>
            @include('backend.dashboard.component.languageTd', ['model' => $galleryCatalogue, 'modeling' => 'GalleryCatalogue'])
            <td class="text-center js-switch-{{ $galleryCatalogue->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $galleryCatalogue->publish }}" data-modelId="{{ $galleryCatalogue->id }}" {{ ($galleryCatalogue->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'gallery.catalogue.update')
                <a href="{{ route('gallery.catalogue.edit', $galleryCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'gallery.catalogue.destroy')
                <a href="{{ route('gallery.catalogue.delete', $galleryCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $galleryCatalogues->links('pagination::bootstrap-4') }}