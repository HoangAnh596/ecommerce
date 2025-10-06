<table class="table table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Tên chương trình</th>
            <th class="text-center">Chiết khấu</th>
            <th class="text-center">Thông tin</th>
            <th class="text-center">Ngày bắt đầu</th>
            <th class="text-center">Ngày kết thúc</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($promotions) && is_object($promotions))
        @foreach($promotions as $promotion)
        @php
            $startDate = convertDateTime($promotion->startDate);
            $endDate = $promotion->endDate ? convertDateTime($promotion->endDate) : '';
            $status = '';
            if ($promotion->endDate !== null && strtotime($promotion->endDate) <= time()) {
                $status = '<span class="text-danger text-small">- Hết Hạn</span>';
            }
        @endphp
        <tr>
            <td>
                <input type="checkbox" value="{{ $promotion->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <div>{{ $promotion->name }} {!! $status !!}</div>
                <div class="text-small text-success">Mã: {{ $promotion->code }}</div>
            </td>
            <td>
                <div class="discount-information text-center">
                    {!! renderDiscountInformation($promotion) !!}
                </div>
            </td>
            <td>
                <div>{{ __('module.promotion')[$promotion->method] }}</div>
            </td>
            <td>{{ $startDate }}</td>
            <td>{{ ($promotion->neverEndDate === 'accept') ? 'Không giới hạn' : $endDate }}</td>
            <td class="text-center js-switch-{{ $promotion->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $promotion->publish }}" data-modelId="{{ $promotion->id }}" {{ ($promotion->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'promotion.update')
                <a href="{{ route('promotion.edit', $promotion->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.destroy')
                <a href="{{ route('promotion.delete', $promotion->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $promotions->links('pagination::bootstrap-4') }}