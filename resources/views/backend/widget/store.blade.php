@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
$url = ($config['method'] == 'create') ? route('widget.store') : route('widget.update', $widget->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin widget</h5>
                    </div>
                    <div class="ibox-content widgetContent">
                        @include('backend.dashboard.component.content', ['offTitle' => true, 'model' => ($widget) ?? null, 'offContent' => true])
                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => ($widget) ?? null])
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cấu hình nội dung của widget</h5>
                    </div>
                    <div class="ibox-content model-list">
                        <div class="labelText">Chọn Module</div>
                        @foreach(__('module.model') as $key => $val)
                        <div class="model-item uk-flex uk-flex-middle">
                            <input type="radio"
                                class="input-radio"
                                id="{{ $key }}"
                                value="{{ $key }}"
                                name="model"
                                {{ (old('model', ($widget->model) ?? null) == $key) ? 'checked' : '' }}>
                            <label for="{{ $key }}">{{ $val }}</label>
                        </div>
                        @endforeach
                        <div class="search-model-box">
                            <i class="fa fa-search"></i>
                            <input type="text" class="form-control search-model" placeholder="Tìm kiếm module...">
                            <div class="ajax-search-result ">
                            </div>
                        </div>
                        @php
                            $modelItem = old('modelItem', ($widgetItem) ?? [])
                        @endphp
                        <div class="search-model-result">
                            @if(count($modelItem) && is_array($modelItem))
                            @foreach($modelItem['id'] as $key => $val)
                            <div class="search-result-item" id="model-{{ $val }}" data-model-id="{{ $val }}" data-canonical="${data.canonical}">
                                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                    <div class="uk-flex uk-flex-middle">
                                        <span class="image img-cover"><img src="{{ $modelItem['image'][$key] }}" alt=""></span>
                                        <span class="name">{{ $modelItem['name'][$key] }}</span>
                                        <div class="hidden">
                                            <input type="text" name="modelItem[id][]" value="{{ $val }}">
                                            <input type="text" name="modelItem[name][]" value="{{ $modelItem['name'][$key] ?? '' }}">
                                            <input type="text" name="modelItem[image][]" value="{{ $modelItem['image'][$key] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="deleted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.widget.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>