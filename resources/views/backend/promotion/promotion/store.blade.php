@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
$url = ($config['method'] == 'create') ? route('promotion.store') : route('promotion.update', $promotion->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight promotion-wrapper">
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label">Tên chương trình <span class="text-danger">(*)</span></label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name', ($promotion->name) ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập vào tên khuyến mại"
                                        autocomplete="off"
                                        {{ isset($disabled) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label">Mã khuyến mại</label>
                                    <input
                                        type="text"
                                        name="code"
                                        value="{{ old('code', ($promotion->code) ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập vào mã khuyến mại"
                                        autocomplete="off"
                                        {{ isset($disabled) ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Mô tả khuyến mại</label>
                                    <textarea name="description" style="height: 100px;" class="form-control">{{ old('description', ($promotion->description) ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cài đặt thông tin chi tiết khuyến mãi</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-row">
                            <div class="fix-label" for="">Chọn hình thức khuyến mãi</div>
                            <select class="setupSelect2 promotionMethod">
                                <option value="">Chọn hình thức</option>
                                @foreach(__('module.promotion') as $key => $val)
                                <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="promotion-container">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thời gian áp dụng chương trình</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-row mb15">
                            <label class="control-label text-left">Ngày bắt đầu</label>
                            <div class="form-date">
                                <input
                                    type="text"
                                    name="startDate"
                                    value="{{ old('startDate', ($promotion->startDate) ?? '') }}"
                                    class="form-control datepicker"
                                    placeholder=""
                                    autocomplete="off"
                                    {{ isset($disabled) ? 'disabled' : '' }}>
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-row mb15">
                            <label class="control-label text-left">Ngày kết thúc</label>
                            <div class="form-date">
                                <input
                                    type="text"
                                    name="endDate"
                                    value="{{ old('endDate', ($promotion->endDate) ?? '') }}"
                                    class="form-control datepicker"
                                    placeholder=""
                                    autocomplete="off"
                                    {{ isset($disabled) ? 'disabled' : '' }}>
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="uk-flex uk-flex-middle">
                                <input type="checkbox"
                                    name=""
                                    value="accept"
                                    class=""
                                    id="neverEnd">
                                <label class="fix-label ml5" for="neverEnd">Không có ngày kết thúc</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Nguồn khách áp dụng</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="setting-value">
                            <div class="nav-setting-item uk-flex uk-flex-middle">
                                <input type="radio" class="chooseSource" name="source" id="allSource" value="all" checked="">
                                <label class="fix-label ml5" for="allSource">Áp dụng cho toàn bộ nguồn khách</label>
                            </div>
                            <div class="nav-setting-item uk-flex uk-flex-middle">
                                <input type="radio" class="chooseSource" name="source" id="chooseSource" value="choose">
                                <label class="fix-label ml5" for="chooseSource">Chọn nguồn khách áp dụng</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Đối tượng áp dụng</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="setting-value">
                            <div class="nav-setting-item uk-flex uk-flex-middle">
                                <input type="radio" class="chooseApply" name="apply" id="allApply" value="all" checked="">
                                <label class="fix-label ml5" for="allApply">Áp dụng cho toàn bộ khách hàng</label>
                            </div>
                            <div class="nav-setting-item uk-flex uk-flex-middle">
                                <input type="radio" class="chooseApply" name="apply" id="chooseApply" value="choose">
                                <label class="fix-label ml5" for="chooseApply">Chọn đối tượng khách hàng</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>
@include('backend.promotion.promotion.component.popup')
<input type="hidden" class="input-product-and-quantity" value="{{ json_encode(__('module.item')) }}">