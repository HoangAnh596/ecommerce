<div class="cart-information">
    <div class="uk-grid uk-grid-medium">
        <div class="uk-width-large-1-2">
            <div class="form-row mb20">
                <input type="text"
                    name="fullname"
                    class="input-text"
                    value="{{ old('fullname') }}"
                    placeholder="Nhập vào Họ Tên">
            </div>
        </div>
        <div class="uk-width-large-1-2">
            <div class="form-row mb20">
                <input type="text"
                    name="phone"
                    class="input-text"
                    value="{{ old('phone') }}"
                    placeholder="Nhập vào Số điện thoại">
            </div>
        </div>
        <div class="form-row mb20">
            <input type="text"
                name="email"
                class="input-text"
                value="{{ old('email') }}"
                placeholder="Nhập vào Email">
        </div>
    </div>
    <div class="uk-grid uk-grid-medium mb20">
        <div class="uk-width-large-1-3">
            <select name="province_id" id="province_id" class="setupSelect2 provinces location" data-target="districts">
                <option value="0">Chọn Thành Phố</option>
                @foreach($provinces as $key => $val)
                <option value="{{ $val->code }}">{{ $val->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="uk-width-large-1-3">
            <select name="district_id" id="district_id" class="setupSelect2 districts location" data-target="wards">
                <option value="0">Chọn Quận Huyện</option>
            </select>
        </div>
        <div class="uk-width-large-1-3">
            <select name="ward_id" id="" class="setupSelect2 wards">
                <option value="0">Chọn Phường Xã</option>
            </select>
        </div>
    </div>
    <div class="form-row mb20">
        <input type="text"
            name="address"
            class="input-text"
            value="{{ old('address') }}"
            placeholder="Nhập vào Địa chỉ">
    </div>
    <div class="form-row mb20">
        <input type="text"
            name="description"
            class="input-text"
            value="{{ old('description') }}"
            placeholder="Ghi chú thêm (Ví dụ: Giao hàng vào lúc 15h00,...)">
    </div>
</div>