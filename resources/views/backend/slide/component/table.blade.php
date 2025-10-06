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
            <td>{{ $slide->name }}</td>
            <td>{{ $slide->keyword }}</td>
            <!-- <td>
                <span class="image img-cover"><img src="" alt=""></span>
            </td> -->
            <td>
                @php
                    $items = is_array($slide->item) ? $slide->item : json_decode($slide->item, true);
                    $langId = $language; // đổi theo ngôn ngữ hiện tại
                @endphp

                @if(!empty($items[$langId]))
                    @foreach($items[$langId] as $it)
                        <span class="image img-cover" style="display:inline-block; margin-right:5px;">
                            <img src="{{ asset(ltrim($it['image'], '/')) }}" alt="">
                        </span>
                    @endforeach
                @else
                    <img src="{{ asset('images/no-image.png') }}" alt="">
                @endif
            </td>
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