<div class="ibox">
    <div class="ibox-title">
        <h5>Thông tin chung</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            @if(!isset($offTitle))
            <div class="col-lg-6">
                <div class="form-row">
                    <label class="control-label">Tên chương trình <span class="text-danger">(*)</span></label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', ($model->name) ?? '') }}"
                        class="form-control"
                        placeholder="Nhập vào tên khuyến mại"
                        autocomplete="off">
                </div>
            </div>
            @endif
            <div class="col-lg-6">
                <div class="form-row">
                    <label class="control-label">Mã khuyến mại <span class="text-danger">(*)</span></label>
                    <input
                        type="text"
                        name="code"
                        value="{{ old('code', ($model->code) ?? '') }}"
                        class="form-control"
                        placeholder="Nếu mã khuyến mại để trống hệ thống sẽ tự động tạo"
                        autocomplete="off">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Mô tả khuyến mại</label>
                    <textarea name="description" style="height: 100px;" class="form-control">{{ old('description', ($model->description) ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>