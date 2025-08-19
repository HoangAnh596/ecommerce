@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
$url = ($config['method'] == 'create') ? route('slide.store') : route('slide.update', $slide->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của người sử dụng</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là trường bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cài đặt nâng cao</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label">Tên Slide <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($slide->name) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label">Từ khóa Slide <span class="text-danger">(*)</span></label>
                                    <input type="text" name="keyword" value="{{ old('keyword', ($slide->keyword) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="slide-setting">
                                    <div class="slide-setting-item">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="setting-text">Chiều rộng</span>
                                            <div class="setting-value">
                                                <input type="text" class="form-control" name="">
                                                <span>px</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slide-setting-item">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="setting-text">Chiều cao</span>
                                            <div class="setting-value">
                                                <input type="text" class="form-control" name="">
                                                <span>px</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slide-setting-item">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="setting-text">Hiệu ứng</span>
                                            <div class="setting-value">
                                                <select name="" id="">
                                                    <option value="">Fade</option>
                                                    <option value="">...</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slide-setting-item">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="setting-text">Mũi tên</span>
                                            <div class="setting-value">
                                                <input type="checkbox" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slide-setting-item">
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="setting-text">Điều hướng</span>
                                            <div class="setting-value">
                                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                                    <input type="radio" name="" id="item-1">
                                                    <label for="item-1">Ẩn thanh điều hướng</label>
                                                </div>
                                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                                    <input type="radio" name="" id="item-2">
                                                    <label for="item-2">Hiển thị thanh dấu chấm</label>
                                                </div>
                                                <div class="nav-setting-item uk-flex uk-flex-middle">
                                                    <input type="radio" name="" id="item-3">
                                                    <label for="item-3">Hiển thị dạng ảnh thumbnails</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label">Mô tả ngắn</label>
                                    <textarea name="description" class="form-control" id="">{{ old('description', ($slide->description) ?? '') }}</textarea>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>