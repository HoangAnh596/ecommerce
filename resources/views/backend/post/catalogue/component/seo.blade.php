<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.seo') }}</h5>
        <div class="ibox-content">
            <div class="seo-container">
                <div class="meta-title">{{ (old('meta_title', ($postCatalogue->meta_title) ?? __('messages.metaTitle') )) }}</div>
                <div class="canonical">{{ (old('canonical', ($postCatalogue->canonical) ?? '')) ? config('app.url').old('canonical', ($postCatalogue->canonical) ?? '').config('apps.general.suffix') : __('messages.metaCanonical') }}</div>
                <div class="meta-description">{{ (old('meta_description', ($postCatalogue->meta_description) ?? __('messages.metaDescription'))) }}</div>
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
                        <input type="text" name="meta_title" value="{{ old('meta_title', ($postCatalogue->meta_title) ?? '') }}" class="form-control" placeholder="" autocomplete="off">
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
                        <input type="text" name="meta_keyword" value="{{ old('meta_keyword', ($postCatalogue->meta_keyword) ?? '') }}" class="form-control" placeholder="" autocomplete="off">
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
                        <textarea name="meta_description" id="" class="form-control">{{ old('meta_description', ($postCatalogue->meta_description) ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label">{{ __('messages.canonical') }} <span class="text-danger">(*)</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="canonical" value="{{ old('canonical', ($postCatalogue->canonical) ?? '') }}" class="form-control" placeholder="" autocomplete="off">
                            <span class="baseUrl">{{ config('app.url') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>