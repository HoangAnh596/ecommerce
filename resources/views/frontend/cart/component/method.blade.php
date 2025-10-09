<div class="cart-foot">
    <h2 class="cart-heading"><span>Hình Thức Thanh Toán</span></h2>
    <div class="cart-method">
        @foreach(__('payment.method') as $key => $val)
        <label for="{{ $val['name'] }}" class="uk-flex uk-flex-middle method-item">
            <input type="radio"
                name="method"
                value="{{ $val['name'] }}"
                id="{{ $val['name'] }}"
                {{ ($key == 0) ? 'checked' : '' }}>
            <span class="image"><img src="{{ asset($val['image']) }}" alt=""></span>
            <span class="title">{{ $val['title'] }}</span>
        </label>
        @endforeach
    </div>
    <div class="cart-return mb20">
        <span>{!! __('payment.return') !!}</span>
    </div>
</div>