<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label">Tiêu đề bài viết <span class="text-danger">(*)</span></label>
            <input type="text" name="name" value="{{ old('name', ($postCatalogue->name) ?? '') }}" class="form-control" placeholder="" autocomplete="off">
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label">Mô tả ngắn</label>
            <textarea name="description" id="ckDescription" class="form-control ck-editor" data-height="100">{{ old('description', ($postCatalogue->description) ?? '') }}</textarea>
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label">Nội dung</label>
            <textarea name="content" id="ckContent" class="form-control ck-editor" data-height="500">{{ old('content', ($postCatalogue->content) ?? '') }}</textarea>
        </div>
    </div>
</div>