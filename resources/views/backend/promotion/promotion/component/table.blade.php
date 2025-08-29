<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên Widget</th>
            <th class="text-center">Từ khóa</th>
            <th class="text-center">Short code</th>
            @include('backend.dashboard.component.languageTh')
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($widgets) && is_object($widgets))
        @foreach($widgets as $widget)
        <tr>
            <td>
                <input type="checkbox" value="{{ $widget->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>{{ $widget->name }}</td>
            <td>{{ $widget->keyword }}</td>
            <td>{{ $widget->short_code }}</td>
            @foreach($languages as $language)
            @if(session('app_locale') === $language->canonical) @continue @endif
            @php
                $translated = (isset($widget->description[$language->id])) ? 1 : 0;
            @endphp
            <td class="text-center">
                <a class="{{ ($translated == 1) ? '' : 'text-danger' }}" 
                    href="{{ route('widget.translate', ['languageId' => $language->id, 'id' => $widget->id]) }}">
                    {{ ($translated == 1) ? 'Đã dịch' : 'Chưa dịch' }}
                </a>
            </td>
            @endforeach
            <td class="text-center js-switch-{{ $widget->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $widget->publish }}" data-modelId="{{ $widget->id }}" {{ ($widget->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'widget.update')
                <a href="{{ route('widget.edit', $widget->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.destroy')
                <a href="{{ route('widget.delete', $widget->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $widgets->links('pagination::bootstrap-4') }}