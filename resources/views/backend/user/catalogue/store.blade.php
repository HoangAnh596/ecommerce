@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url = ($config['method'] == 'create') ? route('user.catalogue.store') : route('user.catalogue.update', $userCatalogue->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của nhóm thành viên</p>
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
                                    <label for="" class="control-label">Tên nhóm thành viên <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($userCatalogue->name) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Ghi chú</label>
                                    <input type="text" name="description" value="{{ old('description', ($userCatalogue->description) ?? '') }}" class="form-control" placeholder="">
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
    var provinceId = '{{ (isset($user->province_id)) ? $user->province_id : old('province_id') }}';
    var districtId = '{{ (isset($user->district_id)) ? $user->district_id : old('district_id') }}';
    var wardId = '{{ (isset($user->ward_id)) ? $user->ward_id : old('ward_id') }}';
</script>