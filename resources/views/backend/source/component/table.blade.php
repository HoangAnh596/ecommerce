<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên Nguồn khách</th>
            <th class="text-center">Từ khóa</th>
            <th class="text-center">Mô tả</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($sources) && is_object($sources))
        @foreach($sources as $source)
        <tr>
            <td>
                <input type="checkbox" value="{{ $source->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>{{ $source->name }}</td>
            <td>{{ $source->keyword }}</td>
            <td>{{ strip_tags(html_entity_decode($source->description)) }}</td>
            <td class="text-center js-switch-{{ $source->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $source->publish }}" data-modelId="{{ $source->id }}" {{ ($source->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'source.update')
                <a href="{{ route('source.edit', $source->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.destroy')
                <a href="{{ route('source.delete', $source->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $sources->links('pagination::bootstrap-4') }}