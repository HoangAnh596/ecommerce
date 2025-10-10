@extends('frontend.homepage.layout')
@section('content')
<div class="cart-success">
    <div class="panel-head">
        <h2 class="cart-heading"><span>Đặt hàng thành công</span></h2>
        <div class="discover-text">
            <a href="{{ write_url('san-pham') }}">Khám phá thêm các sản phẩm khác tại đây</a>
        </div>
    </div>
    <div class="panel-body">
        <h2 class="cart-heading"><span>Thông tin đơn hàng</span></h2>
        <div class="checkout-box">
            <div class="checkout-box-head">
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                    <div class="uk-width-large-1-3"></div>
                    <div class="uk-width-large-1-3">
                        <div class="order-title uk-text-center">ĐƠN HÀNG #{{ $order->code }}</div>
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="order-date">{{ convertDateTime($order->created_at) }}</div>
                    </div>
                </div>
            </div>
            <div class="checkout-box-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá bán</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $carts = $order->cart['detail'];
                        @endphp
                        @foreach($carts as $key => $val)
                        @php
                            $name = $val['name'];
                            $qty = $val['qty'];
                            $price = convert_price($val['price'], true);
                            $subTotal = convert_price($val['price'] * $qty, true);
                        @endphp
                        <tr>
                            <td>{{ $name }}</td>
                            <td>{{ $qty }}</td>
                            <td>{{ $price }} đ</td>
                            <td>{{ $subTotal }} đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Mã giảm giá</td>
                            <td>{{ $order->promotion['code'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">Tổng giá trị sản phẩm</td>
                            <td>{{ convert_price($order->promotion['discount'] + $order->cart['cartTotal'], true) }} đ</td>
                        </tr>
                        <tr>
                            <td colspan="3">Tổng giá trị khuyến mãi</td>
                            <td>{{ convert_price($order->promotion['discount'], true) }} đ</td>
                        </tr>
                        <tr>
                            <td colspan="3">Phí giao hàng</td>
                            <td>0 đ</td>
                        </tr>
                        <tr class="total_payment">
                            <td colspan="3"><span>Tổng thanh toán</span></td>
                            <td>{{ convert_price($order->promotion['discount'] + $order->cart['cartTotal'], true) }} đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="panel-foot">
        <h2 class="cart-heading"><span>Thông tin nhận hàng</span></h2>
        <div class="checkout-box">
            <div>Tên người nhận: <span>{{ $order->fullname }}</span></div>
            <div>Email: <span>{{ $order->email }}</span></div>
            <div>Địa chỉ: <span>{{ $order->address }}</span></div>
            <div>Số điện thoại: <span>{{ $order->phone }}</span></div>
            <div>Hình thức thanh toán: <span>{{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-'}}</span></div>
        </div>
    </div>
</div>
@endsection