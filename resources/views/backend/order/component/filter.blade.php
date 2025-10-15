<form action="{{ route('order.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="uk-flex uk-flex-middle">
                @include('backend.dashboard.component.perpage')
                <div class="date-item-box">
                    <input type="type" name="created_at" class="form-control rangepicker" value="">
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @foreach(__('cart') as $key => $val)
                    @php
                        ${$key} = request($key) ?: old($key);
                    @endphp
                    <select name="{{ $key }}" class="form-control setupSelect2 ml10">
                        @foreach($val as $index => $item)
                        <option {{ (${$key} == $index)  ? 'selected' : '' }} value="{{ $index }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @endforeach
                    @include('backend.dashboard.component.keyword')
                </div>
            </div>
        </div>
    </div>
</form>