@if(!isset($offTitle))
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label class="control-label">{{ __('messages.title') }} <span class="text-danger">(*)</span></label>
            <input
                type="text" name="name"
                value="{{ old('name', ($model->name) ?? '') }}" class="form-control"
                autocomplete="off" {{ isset($disabled) ? 'disabled' : '' }}>
        </div>
    </div>
</div>
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label class="control-label">{{ __('messages.canonical') }} <span class="text-danger">(*)</span></label>
            <div class="input-wrapper">
                <input
                    type="text" name="canonical"
                    value="{{ old('canonical', ($model->canonical) ?? '') }}" class="form-control seo-canonical"
                    autocomplete="off" {{ isset($disabled) ? 'disabled' : '' }}>
                <span class="baseUrl">{{ config('app.url') }}</span>
            </div>
        </div>
    </div>
</div>
@endif
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label class="control-label">{{ __('messages.description') }}</label>
            <textarea
                name="description" id="ckDescription"
                class="form-control ck-editor" data-height="100" {{ isset($disabled) ? 'disabled' : '' }}>
                {{ old('description', ($model->description) ?? '') }}
            </textarea>
        </div>
    </div>
</div>
@if(!isset($offContent))
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <label class="control-label">{{ __('messages.content') }}</label>
                <a href="" class="multipleUploadImageCkeditor" data-target="ckContent">{{ __('messages.imageMultiple') }}</a>
            </div>
            <textarea
                name="content" id="ckContent"
                class="form-control ck-editor" data-height="500" {{ isset($disabled) ? 'disabled' : '' }}>
                {{ old('content', ($model->content) ?? '') }}
            </textarea>
        </div>
    </div>
</div>
@endif