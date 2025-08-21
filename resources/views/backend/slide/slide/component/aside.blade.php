<div class="ibox slide-setting slide-normal">
    <div class="ibox-title">
        <h5>Cài đặt cơ bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mb10">
                <div class="form-row">
                    <label for="" class="control-label">Tên Slide <span class="text-danger">(*)</span></label>
                    <input type="text"
                        name="name"
                        value="{{ old('name', ($slide->name) ?? '') }}"
                        class="form-control"
                        placeholder="">
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label">Từ khóa Slide <span class="text-danger">(*)</span></label>
                    <input type="text"
                        name="keyword"
                        value="{{ old('keyword', ($slide->keyword) ?? '') }}"
                        class="form-control"
                        placeholder="">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="setting-item">
                    <div class="uk-flex uk-flex-middle">
                        <span class="setting-text">Chiều rộng</span>
                        <div class="setting-value">
                            <input type="text"
                                class="form-control int"
                                name="setting[width]"
                                value="{{ old('setting.width', ($slide->setting['width']) ?? 0) }}">
                            <span class="px">px</span>
                        </div>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="uk-flex uk-flex-middle">
                        <span class="setting-text">Chiều cao</span>
                        <div class="setting-value">
                            <input type="text"
                                class="form-control int"
                                name="setting[height]"
                                value="{{ old('setting.height', ($slide->setting['height']) ?? 0) }}">
                            <span class="px">px</span>
                        </div>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="uk-flex uk-flex-middle">
                        <span class="setting-text">Hiệu ứng</span>
                        <div class="setting-value">
                            <select class="form-control setupSelect2" name="setting[animation]" id="">
                                @foreach(__('module.effect') as $key => $val)
                                <option value="{{ $key }}" {{ $key == old('setting.animation', ($slide->setting['animation']) ?? null) ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @include('backend.slide.slide.component.checkbox', ['key' => 'arrow', 'label' => 'Mũi tên'])
                <div class="setting-item">
                    <div class="uk-flex uk-flex-middle">
                        <span class="setting-text">Điều hướng</span>
                        <div class="setting-value">
                            @php
                                // Lấy giá trị navigate theo thứ tự: old() > DB > mặc định = 'dots'
                                $navigate = old('setting.navigate', $slide->setting['navigate'] ?? 'dots');
                            @endphp

                            @foreach(__('module.navigate') as $key => $val)
                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                    <input type="radio"
                                        name="setting[navigate]"
                                        id="navigate_{{ $key }}"
                                        value="{{ $key }}"
                                        @if($navigate == $key) checked @endif>
                                    <label for="navigate_{{ $key }}">{{ $val }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox slide-setting slide-advance">
    <div class="ibox-title uk-flex uk-flex-middle uk-flex-space-between">
        <h5>Cài đặt nâng cao</h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        @include('backend.slide.slide.component.checkbox', ['key' => 'autoplay', 'label' => 'Tự động chạy'])
        @include('backend.slide.slide.component.checkbox', ['key' => 'pauseHover', 'label' => 'Dừng khi <br> di chuột'])
        <div class="setting-item">
            <div class="uk-flex uk-flex-middle">
                <span class="setting-text">Chuyển ảnh</span>
                <div class="setting-value">
                    <input type="text"
                        name="setting[animationDelay]"
                        class="form-control int"
                        value="{{ old('setting.animationDelay', ($slide->setting['animationDelay']) ?? 0) }}">
                    <span class="px">ms</span>
                </div>
            </div>
        </div>
        <div class="setting-item">
            <div class="uk-flex uk-flex-middle">
                <span class="setting-text">Tốc độ <br> hiệu ứng</span>
                <div class="setting-value">
                    <input type="text"
                        name="setting[animationSpeed]"
                        class="form-control int"
                        value="{{ old('setting.animationSpeed', ($slide->setting['animationSpeed']) ?? 0) }}">
                    <span class="px">ms</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox short-code">
    <div class="ibox-title">
        <h5>Short code</h5>
    </div>
    <div class="ibox-content">
        <textarea class="textarea form-control" name="short_code" id="">{{ old('short_code', ($slide->short_code) ?? null) }}</textarea>
    </div>
</div>