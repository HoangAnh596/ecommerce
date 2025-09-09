<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Họ và tên</th>
            <th class="text-center">Email</th>
            <th class="text-center">Số điện thoại</th>
            <th class="text-center">Địa chỉ</th>
            <th class="text-center">Nhóm thành viên</th>
            <th class="text-center">Nguồn khách</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($customers) && is_object($customers))
        @foreach($customers as $customer)
        <tr>
            <td>
                <input type="checkbox" value="{{ $customer->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->address }}</td>
            <td class="text-center">{{ $customer->customer_catalogues->name }}</td>
            <td class="text-center">{{ $customer->sources->name }}</td>
            <td class="text-center js-switch-{{ $customer->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $customer->publish }}" data-modelId="{{ $customer->id }}" {{ ($customer->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'customer.update')
                <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.destroy')
                <a href="{{ route('customer.delete', $customer->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $customers->links('pagination::bootstrap-4') }}