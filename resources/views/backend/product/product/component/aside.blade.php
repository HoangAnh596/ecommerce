<div class="ibox">
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label">Chọn danh mục cha <span class="text-danger">(*)</span></label>
                    <span class="text-danger notice">Chọn root nếu không có danh mục cha</span>
                    <select name="product_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('product_catalogue_id', (isset($product->product_catalogue_id)) ? $product->product_catalogue_id : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset($product)) {
                foreach($product->product_catalogues as $key => $val) {
                    $catalogue[] = $val->id;
                }
            }
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="form-label">Chọn danh mục phụ</label>
                    <select name="catalogue[]" class="form-control setupSelect2" multiple>
                        @foreach($dropdown as $key => $val)
                        <option value="{{ $key }}"
                            @if(is_array(old('catalogue', (
                                isset($catalogue) && count($catalogue)) ? $catalogue : [])) 
                                && isset($product->product_catalogue_id) && ($key !== $product->product_catalogue_id) 
                                && in_array($key, old('catalogue', (isset($catalogue)
                                ) ? $catalogue : [])))
                            selected @endif >
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.product.information') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.code') }}</label>
                    <input 
                        type="text"
                        name="code"
                        value="{{ old('code', ($product->code) ?? time()) }}"
                        class="form-control"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.made_in') }}</label>
                    <input 
                        type="text"
                        name="made_in"
                        value="{{ old('made_in', ($product->made_in) ?? null) }}"
                        class="form-control "
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.price') }}</label>
                    <input 
                        type="text"
                        name="price"
                        value="{{ old('price', (isset($product)) ? number_format($product->price, 0 , ',', '.') : '') }}"
                        class="form-control int" id="priceInput"
                    >
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>Chọn ảnh đại diện</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target"><img src="{{ old('image', ($product->image ?? asset(''.'/backend/img/not-found.jpg'))) }}" alt="Image not found"></span>
                    <input type="hidden" name="image" value="{{ old('image', ($product->image) ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>Cấu hình nâng cao</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach(config('apps.general.publish') as $key => $val)
                            <option value="{{ $key }}" {{ $key == old('publish', (isset($product->publish)) ? $product->publish : '') ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" class="form-control setupSelect2" id="">
                        @foreach(config('apps.general.follow') as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('follow', (isset($product->follow)) ? $product->follow : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>