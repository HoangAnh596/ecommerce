@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('generate.store') : route('generate.update', $generate->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của Module</p>
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
                                    <label class="control-label">Tên Model <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($generate->name) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label">Tên chức năng <span class="text-danger">(*)</span></label>
                                    <input type="text" name="module" value="{{ old('module', ($generate->module) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label">Loại Module <span class="text-danger">(*)</span></label>
                                    <select name="module_type" id="" class="form-control setupSelect2">
                                        <option value="0">Chọn loại Module</option>
                                        <option value="catalogue">Module danh mục</option>
                                        <option value="detail">Module chi tiết</option>
                                        <option value="difference">Module khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label">Đường dẫn <span class="text-danger">(*)</span></label>
                                    <input type="text" name="path" value="{{ old('path', ($generate->path) ?? '') }}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin Schema</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của Schema</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là trường bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label">Schema <span class="text-danger">(*)</span></label>
                                    <textarea name="schema" class="form-control schema">{{ old('schema', ($generate->schema) ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>