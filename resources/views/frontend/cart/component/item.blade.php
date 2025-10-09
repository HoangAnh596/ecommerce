<div class="panel-body">
    @if(count($carts) && !is_null($carts))
    <div class="cart-list">
        @foreach($carts as $keyCart => $cart)
        <div class="cart-item">
            <div class="uk-grid uk-grid-mdedium">
                <div class="uk-width-small-1-1 uk-width-medium-1-5">
                    <div class="cart-item-image">
                        <span class="image img-scaledown">
                            <img src="{{ asset($cart->image) }}" alt="">
                        </span>
                        <span class="cart-item-number">{{ $cart->qty }}</span>
                    </div>
                </div>
                <div class="uk-width-small-1-1 uk-width-medium-4-5">
                    <div class="cart-item-info">
                        <h3 class="title"><span>{{ $cart->name }}</span></h3>
                        <div class="cart-item-action uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="cart-item-qty">
                                <button type="button" class="btn-qty minus">-</button>
                                <input type="text" class="input-qty" value="{{ $cart->qty }}">
                                <input type="hidden" class="rowId" value="{{ $cart->rowId }}">
                                <button type="button" class="btn-qty plus">+</button>
                            </div>
                            <div class="cart-item-price">
                                <div class="uk-flex uk-flex-bottom">
                                    <!-- @if($cart->price != $cart->priceOriginal)
                                        <div class="cart-price-old mr10">{{ convert_price($cart->priceOriginal, true) }} đ</div>
                                    @endif -->
                                    <div class="cart-price-sale">{{ convert_price($cart->qty * $cart->price, true) }} đ</div>
                                </div>
                            </div>
                            <div class="cart-item-remove" data-row-id="{{ $cart->rowId }}">
                                <span>x</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>