<div class="ibox slide-setting slide-normal">
    <div class="ibox-title">
        <h5>Cài đặt cơ bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12 mb10">
                <div class="form-row">
                    <label for="" class="control-label">Tên Widget <span class="text-danger">(*)</span></label>
                    <input type="text"
                        name="name"
                        value="{{ old('name', ($widget->name) ?? '') }}"
                        class="form-control"
                        placeholder="">
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label">Từ khóa Widget <span class="text-danger">(*)</span></label>
                    <input type="text"
                        name="keyword"
                        value="{{ old('keyword', ($widget->keyword) ?? '') }}"
                        class="form-control"
                        placeholder="">
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
        <textarea class="textarea form-control" name="short_code" id="">{{ old('short_code', ($widget->short_code) ?? null) }}</textarea>
    </div>
</div>