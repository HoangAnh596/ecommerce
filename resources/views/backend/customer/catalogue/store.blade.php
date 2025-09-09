@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('customer.catalogue.store') : route('customer.catalogue.update', $customerCatalogue->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của nhóm khách hàng</p>
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
                                    <label for="" class="control-label">Tên nhóm khách hàng <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($customerCatalogue->name) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Ghi chú</label>
                                    <input type="text" name="description" value="{{ old('description', ($customerCatalogue->description) ?? '') }}" class="form-control" placeholder="">
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