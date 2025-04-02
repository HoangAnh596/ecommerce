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
                        <option value="{{ $key }}" {{ $key == old('parent_id', (isset($productCatalogue->parent_id)) ? $productCatalogue->parent_id : '') ? 'selected' : '' }}>
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
                    <span class="image img-cover image-target"><img src="{{ old('image', ($productCatalogue->image ?? asset(''.'/backend/img/not-found.jpg'))) }}" alt="Image not found"></span>
                    <input type="hidden" name="image" value="{{ old('image', ($productCatalogue->image) ?? '') }}">
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
                            <option value="{{ $key }}" {{ $key == old('publish', (isset($productCatalogue->publish)) ? $productCatalogue->publish : '') ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" class="form-control setupSelect2" id="">
                        @foreach(__('messages.follow') as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('follow', (isset($productCatalogue->follow)) ? $productCatalogue->follow : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>