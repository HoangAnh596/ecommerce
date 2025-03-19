<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 30px">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên nhóm</th>
            <th class="text-center" style="width: 100px">Tình trạng</th>
            <th class="text-center" style="width: 100px">Thao tác</th>
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
            <td class="text-center js-switch-{{ $postCatalogue->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="PostCatalogue" value="{{ $postCatalogue->publish }}" data-modelId="{{ $postCatalogue->id }}" {{ ($postCatalogue->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                <a href="{{ route('post.catalogue.edit', $postCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                <a href="{{ route('post.catalogue.delete', $postCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $postCatalogues->links('pagination::bootstrap-4') }}