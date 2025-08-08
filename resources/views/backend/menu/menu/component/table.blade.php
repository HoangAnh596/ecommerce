<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên Menu</th>
            <th class="text-center">Từ khóa</th>
            <th class="text-center">Ngày tạo</th>
            <th class="text-center">Người tạo</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($menus) && is_object($menus))
        @foreach($menus as $menu)
        <tr>
            <td>
                <input type="checkbox" value="{{ $menu->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <span class="image img-cover"><img src="https://storage.googleapis.com/dara-c1b52.appspot.com/daras_ai/media/2a9500aa-74f9-11ee-8902-02420a000165/gooey.ai%20-%20A%20beautiful%20anime%20drawing%20of%20a%20smilin...ibli%20ponyo%20anime%20excited%20anime%20saturated%20colorsn.png" alt=""></span>
            </td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td class="text-center">
                @can('modules', 'menu.update')
                <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.destroy')
                <a href="{{ route('menu.delete', $menu->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>