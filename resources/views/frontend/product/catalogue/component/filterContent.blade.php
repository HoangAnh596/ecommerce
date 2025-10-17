<div class="filter-content">
    <div class="filter-overlay">
        <div class="filter-close">
            <i class="fi fi-rs-cross"></i>
        </div>
        <div class="filter-content-contailer">
            @if(!is_null($filters))
            @foreach($filters as $key => $val)
            @php
                $catName = $val->languages->first()->pivot->name;
            @endphp
            <div class="filter-item">
                <div class="filter-heading">{{ $catName }}</div>
                @if(count($val->attributes))
                <div class="filter-body">
                    @foreach($val->attributes as $item)
                    @php
                        $attributeName = $item->languages->first()->pivot->name;
                        $id = $item->id;
                    @endphp
                    <div class="filter-choose">
                        <input type="checkbox" class="input-checkbox filtering" value="{{ $id }}" id="attribute-{{ $id }}">
                        <label for="attribute-{{ $id }}">{{ $attributeName }}</label>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
            @endif
            <div class="filter-item">
                <div class="filter-heading" for="priceRange">Lọc Theo Giá: </div>
                <div class="filter-price-content">
                    <input type="text" id="priceRange" readonly="" class="uk-hidden">
                    <div id="price-range" class="slider ui-slider ui-slider-horizontal ui-widget"></div>
                </div>
                <div class="filter-input-value mt5">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <input type="text" class="min-value input-value" value="0đ">
                        <input type="text" class="max-value input-value" value="10.000.000đ">
                    </div>
                </div>
            </div>
            <div class="filter-item filter-category">
                <div class="filter-heading">Tình trạng sản phẩm</div>
                <div class="filter-body">
                    <div class="filter-choose">
                        <input type="checkbox" id="input-availble" name="stock[]" value="1">
                        <label for="input-availble">Còn hàng</label>
                    </div>
                    <div class="filter-choose">
                        <input type="checkbox" id="input-outstock" name="stock[]" value="0">
                        <label for="input-outstock">Hết hàng</label>
                    </div>
                </div>
            </div>
            <div class="filter-review">
                <div class="filter-heading">Lọc theo đánh giá</div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" id="input-rate-5" name="rate[]" value="5">
                    <label for="input-rate-5">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(5)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" id="input-rate-4" name="rate[]" value="4">
                    <label for="input-rate-4">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(4)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" id="input-rate-3" name="rate[]" value="3">
                    <label for="input-rate-3">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(3)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" id="input-rate-2" name="rate[]" value="2">
                    <label for="input-rate-2">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(2)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" id="input-rate-1" name="rate[]" value="1">
                    <label for="input-rate-1">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(1)</span>
                </div>
            </div>
        </div>
    </div>
</div>