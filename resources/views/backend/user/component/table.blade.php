<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Avatar</th>
            <th class="text-center">Họ và tên</th>
            <th class="text-center">Email</th>
            <th class="text-center">Số điện thoại</th>
            <th class="text-center">Địa chỉ</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($users) && is_object($users))
        @foreach($users as $user)
        <tr>
            <td>
                <input type="checkbox" value="" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <span class="image img-cover"><img src="https://storage.googleapis.com/dara-c1b52.appspot.com/daras_ai/media/2a9500aa-74f9-11ee-8902-02420a000165/gooey.ai%20-%20A%20beautiful%20anime%20drawing%20of%20a%20smilin...ibli%20ponyo%20anime%20excited%20anime%20saturated%20colorsn.png" alt=""></span>
            </td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->address }}</td>
            <td class="text-center">
                <input type="checkbox" class="js-switch" checked />
            </td>
            <td class="text-center">
                <a href="" class="btn btn-success"><i class="fa fa-edit"></i></a>
                <a href="" class="btn btn-danger"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $users->links('pagination::bootstrap-4') }}