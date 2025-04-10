<div class="ibox">
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label">Chọn danh mục cha <span class="text-danger">(*)</span></label>
                    <span class="text-danger notice">Chọn root nếu không có danh mục cha</span>
                    <select name="attribute_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('attribute_catalogue_id', (isset($attribute->attribute_catalogue_id)) ? $attribute->attribute_catalogue_id : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset($attribute)) {
                foreach($attribute->attribute_catalogues as $key => $val) {
                    $catalogue[] = $val->id;
                }
            }
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="form-label">Chọn danh mục phụ</label>
                    <select name="catalogue[]" class="form-control setupSelect2" multiple>
                        @foreach($dropdown as $key => $val)
                        <option value="{{ $key }}"
                            @if(is_array(old('catalogue', (
                                isset($catalogue) && count($catalogue)) ? $catalogue : [])) 
                                && isset($attribute->attribute_catalogue_id) && ($key !== $attribute->attribute_catalogue_id) 
                                && in_array($key, old('catalogue', (isset($catalogue)
                                ) ? $catalogue : [])))
                            selected @endif >
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
        <h5>Chọn ảnh đại diện</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target"><img src="{{ old('image', ($attribute->image ?? asset(''.'/backend/img/not-found.jpg'))) }}" alt="Image not found"></span>
                    <input type="hidden" name="image" value="{{ old('image', ($attribute->image) ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>Cấu hình nâng cao</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach(config('apps.general.publish') as $key => $val)
                            <option value="{{ $key }}" {{ $key == old('publish', (isset($attribute->publish)) ? $attribute->publish : '') ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" class="form-control setupSelect2" id="">
                        @foreach(config('apps.general.follow') as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('follow', (isset($attribute->follow)) ? $attribute->follow : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>