<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.seo') }}</h5>
        <div class="ibox-content">
            <div class="seo-container">
                <div class="meta-title">{{ (old('translate_meta_title', ($model->translate_meta_title) ?? __('messages.metaTitle') )) }}</div>
                <div class="canonical">{{ (old('translate_canonical', ($model->translate_canonical) ?? '')) ? config('app.url').old('translate_canonical', ($model->translate_canonical) ?? '').config('apps.general.suffix') : __('messages.metaCanonical') }}</div>
                <div class="meta-description">{{ (old('translate_meta_description', ($model->translate_meta_description) ?? __('messages.metaDescription'))) }}</div>
            </div>
        </div>
        <div class="seo-wrapper">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('messages.titleSeo') }}</span>
                                <span class="count_meta-title">0 {{ __('messages.character') }}</span>
                            </div>
                        </label>
                        <input type="text" name="translate_meta_title" value="{{ old('translate_meta_title', ($model->meta_title) ?? '') }}" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('messages.keywordSeo') }}</span>
                                <span class="count_meta-keyword">0 {{ __('messages.character') }}</span>
                            </div>
                        </label>
                        <input type="text" name="translate_meta_keyword" value="{{ old('translate_meta_keyword', ($model->meta_keyword) ?? '') }}" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label text-left">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>{{ __('messages.descriptionSeo') }}</span>
                                <span class="count_meta-description">0 {{ __('messages.character') }}</span>
                            </div>
                        </label>
                        <textarea name="translate_meta_description" id="" class="form-control">{{ old('translate_meta_description', ($model->meta_description) ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label">{{ __('messages.canonical') }} <span class="text-danger">(*)</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="translate_canonical" value="{{ old('translate_canonical', ($model->canonical) ?? '') }}" class="form-control seo-canonical" placeholder="" autocomplete="off">
                            <span class="baseUrl">{{ config('app.url') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>