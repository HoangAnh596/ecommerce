<form action="{{ route('permission.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="perpage">
                @php
                    $perpage = request('perpage') ?: old('perpage');
                @endphp
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <select name="perpage" class="form-control input-sm perpage filter mr10">
                        @for($i = 20; $i<=200; $i+=20)
                            <option value="{{ $i }}" {{ ($perpage == $i) ? 'selected' : '' }}>{{ $i }} bản ghi</option>
                            @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @php
                        $pushlish = request('publish') ?: old('publish');
                    @endphp
                    <select name="publish" class="form-control mr10 setupSelect2">
                        @foreach(config('apps.general.publish') as $key => $val)
                        <option value="{{ $key }}" {{ ($pushlish == $key) ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                    <div class="uk-search uk-flex uk-flex-middle mr10">
                        <div class="input-group">
                            <input type="text" name="keyword" value="{{ request('keyword') ?: old('keyword') }}" class="form-control" placeholder="Bạn muốn tìm kiếm gì...">
                            <span class="input-group-btn">
                                <button type="submit" name="search" value="search" class="btn btn-primary mb0 btn-sm">Tìm kiếm</button>
                            </span>
                        </div>
                    </div>
                    @can('modules', 'permission.create')
                    <a href="{{ route('permission.create') }}" class="btn btn-primary"><i class="fa fa-plus mr5"></i>{{ __('messages.permission.create.title') }}</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</form>