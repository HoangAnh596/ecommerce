<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center" style="width: 100px">Ảnh</th>
            <th class="text-center">Tên ngôn ngữ</th>
            <th class="text-center">Canonical</th>
            <th class="text-center">Ghi chú</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($resLang) && is_object($resLang))
        @foreach($resLang as $language)
        <tr>
            <td>
                <input type="checkbox" value="{{ $language->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <span class="image img-cover"><img src="{{ asset(''.$language->image) }}" alt=""></span>
            </td>
            <td>{{ $language->name }}</td>
            <td class="text-center">{{ $language->canonical }}</td>
            <td>{{ $language->description }}</td>
            <td class="text-center js-switch-{{ $language->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $language->publish }}" data-modelId="{{ $language->id }}" {{ ($language->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'language.update')
                <a href="{{ route('language.edit', $language->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'language.destroy')
                <a href="{{ route('language.delete', $language->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $resLang->links('pagination::bootstrap-4') }}