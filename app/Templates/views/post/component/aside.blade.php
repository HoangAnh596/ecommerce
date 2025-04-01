<div class="ibox">
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label">Chọn danh mục cha <span class="text-danger">(*)</span></label>
                    <span class="text-danger notice">Chọn root nếu không có danh mục cha</span>
                    <select name="{module}_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('{module}_catalogue_id', (isset(${module}->{module}_catalogue_id)) ? ${module}->{module}_catalogue_id : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset(${module})) {
                foreach(${module}->{module}_catalogues as $key => $val) {
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
                                && isset(${module}->{module}_catalogue_id) && ($key !== ${module}->{module}_catalogue_id) 
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
                    <span class="image img-cover image-target"><img src="{{ old('image', (${module}->image ?? asset(''.'/backend/img/not-found.jpg'))) }}" alt="Image not found"></span>
                    <input type="hidden" name="image" value="{{ old('image', (${module}->image) ?? '') }}">
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
                            <option value="{{ $key }}" {{ $key == old('publish', (isset(${module}->publish)) ? ${module}->publish : '') ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" class="form-control setupSelect2" id="">
                        @foreach(config('apps.general.follow') as $key => $val)
                        <option value="{{ $key }}" {{ $key == old('follow', (isset(${module}->follow)) ? ${module}->follow : '') ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>