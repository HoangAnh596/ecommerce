<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label class="control-label">{{ __('messages.title') }} <span class="text-danger">(*)</span></label>
            <input 
                type="text" name="translate_name" 
                value="{{ old('translate_name', ($model->name) ?? '') }}" 
                class="form-control" autocomplete="off">
        </div>
    </div>
</div>
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label class="control-label">{{ __('messages.description') }}</label>
            <textarea 
                name="translate_description" id="ckDescription_1" 
                class="form-control ck-editor" data-height="100">
                {{ old('translate_description', ($model->description) ?? '') }}
            </textarea>
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <label class="control-label">{{ __('messages.content') }}</label>
                <a href="" class="multipleUploadImageCkeditor" data-target="ckContent">{{ __('messages.imageMultiple') }}</a>
            </div>
            <textarea 
                name="translate_content" id="ckContent_1" 
                class="form-control ck-editor" data-height="500">
                {{ old('translate_content', ($model->content) ?? '') }}
            </textarea>
        </div>
    </div>
</div>