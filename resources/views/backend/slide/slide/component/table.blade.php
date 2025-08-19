<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên nhóm</th>
            <th class="text-center">Từ khóa</th>
            <th class="text-center">Danh sách Hình Ảnh</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($slides) && is_object($slides))
        @foreach($slides as $slide)
        <tr>
            <td>
                <input type="checkbox" value="{{ $slide->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <span class="image img-cover"><img src="https://storage.googleapis.com/dara-c1b52.appspot.com/daras_ai/media/2a9500aa-74f9-11ee-8902-02420a000165/gooey.ai%20-%20A%20beautiful%20anime%20drawing%20of%20a%20smilin...ibli%20ponyo%20anime%20excited%20anime%20saturated%20colorsn.png" alt=""></span>
            </td>
            <td>{{ $slide->name }}</td>
            <td>{{ $slide->keyword }}</td>
            <td></td>
            <td class="text-center js-switch-{{ $slide->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $slide->publish }}" data-modelId="{{ $slide->id }}" {{ ($slide->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'slide.update')
                <a href="{{ route('slide.edit', $slide->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.destroy')
                <a href="{{ route('slide.delete', $slide->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $slides->links('pagination::bootstrap-4') }}