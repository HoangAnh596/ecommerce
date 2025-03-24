<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.parent') }} <span class="text-danger">(*)</span></h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="text-danger notice">{{ __('messages.parentNotice') }}</span>
                    <select name="parent_id" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('parent_id', (isset($postCatalogue->parent_id)) ? $postCatalogue->parent_id : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target"><img src="{{ old('image', ($postCatalogue->image ?? asset(''.'/backend/img/not-found.jpg'))) }}" alt="Image not found"></span>
                    <input type="hidden" name="image" value="{{ old('image', ($postCatalogue->image) ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.advange') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach(__('messages.publish') as $key => $val)
                            <option value="{{ $key }}" {{ $key == old('publish', (isset($postCatalogue->publish)) ? $postCatalogue->publish : '') ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" class="form-control setupSelect2" id="">
                        @foreach(__('messages.follow') as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('follow', (isset($postCatalogue->follow)) ? $postCatalogue->follow : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>