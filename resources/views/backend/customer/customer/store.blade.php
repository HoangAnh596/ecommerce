@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('customer.store') : route('customer.update', $customer->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của người sử dụng</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là trường bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Họ và tên <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($customer->name) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Email <span class="text-danger">(*)</span></label>
                                    <input type="text" name="email" value="{{ old('email', ($customer->email) ?? '') }}" class="form-control" placeholder="@gmail.com">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Nhóm thành viên <span class="text-danger">(*)</span></label>
                                    <select name="customer_catalogue_id" class="form-control setupSelect2">
                                        <option value="0">[Chọn nhóm thành viên]</option>
                                        @foreach($customerCatalogues as $key => $item)
                                        <option value="{{ $item->id }}" 
                                            {{ $item->id == old('customer_catalogue_id', isset($customer->customer_catalogue_id) ? $customer->customer_catalogue_id : '') ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Nguồn khách <span class="text-danger">(*)</span></label>
                                    <select name="source_id" class="form-control setupSelect2">
                                        <option value="0">[Chọn nguồn khách]</option>
                                        @foreach($sources as $key => $item)
                                        <option value="{{ $item->id }}" 
                                            {{ $item->id == old('source_id', isset($customer->source_id) ? $customer->source_id : '') ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Ngày sinh <span class="text-danger">(*)</span></label>
                                    <input type="date" name="birthday" value="{{ old('birthday', (isset($customer->birthday)) ? date('Y-m-d', strtotime($customer->birthday)) : '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                        @if($config['method'] == 'create')
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Mật khẩu <span class="text-danger">(*)</span></label>
                                    <input type="password" name="password" value="" class="form-control" placeholder="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Nhập lại mật khẩu <span class="text-danger">(*)</span></label>
                                    <input type="password" name="re_password" value="" class="form-control" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label">Ảnh đại diện</label>
                                    <input type="text" name="image" value="{{ old('image', ($customer->image) ?? '') }}" class="form-control upload-image" data-upload="Images" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin liên hệ</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin liên hệ của người sử dụng</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Thành phố</label>
                                    <select name="province_id" class="form-control setupSelect2 provinces location" data-target="districts">
                                        <option value="0">[Chọn Thành phố]</option>
                                        @if(isset($provinces))
                                        @foreach($provinces as $province)
                                        <option value="{{ $province->code }}" @if(old('province_id') == $province->code) selected @endif>{{ $province->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Quận/Huyện</label>
                                    <select name="district_id" class="form-control setupSelect2 districts location" data-target="wards">  
                                        <option value="0">[Chọn Quận/Huyện]</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Phường/Xã</label>
                                    <select name="ward_id" class="form-control setupSelect2 wards">
                                        <option value="0">[Chọn Phường/Xã]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Địa chỉ</label>
                                    <input type="text" name="address" value="{{ old('address', ($customer->address) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Số điện thoại <span class="text-danger">(*)</span></label>
                                    <input type="text" name="phone" value="{{ old('phone', ($customer->phone) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Ghi chú <span class="text-danger">(*)</span></label>
                                    <input type="text" name="description" value="{{ old('description', ($customer->description) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>

<script>
    var provinceId = '{{ (isset($customer->province_id)) ? $customer->province_id : old('province_id') }}';
    var districtId = '{{ (isset($customer->district_id)) ? $customer->district_id : old('district_id') }}';
    var wardId = '{{ (isset($customer->ward_id)) ? $customer->ward_id : old('ward_id') }}';
</script>