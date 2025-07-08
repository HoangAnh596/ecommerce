@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@php
$url = (isset($config['method']) && $config['method'] == 'translate') ? route('system.saveTranslate', ['languageId' => $languageId]) : route('system.store');
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="uk-flex uk-flex-middle">
        @foreach($languages as $language)
            <a class="image img-scaledown system-flag" 
                href="{{ route('system.translate', ['languageId' => $language->id]) }}">
                <img src="{{ $language->image }}" alt="">
            </a>
        @endforeach
        </div>
        @foreach($systemConfig as $key => $val)
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-head">
                    <div class="panel-title">{{ $val['label'] }}</div>
                    <div class="panel-description">
                        {{ $val['description'] }}
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox">
                    @if(count($val['value']))
                    <div class="ibox-content">
                        @foreach($val['value'] as $keyVal => $item)
                        @php
                        $name = $key.'_'.$keyVal;
                        @endphp
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="uk-flex uk-flex-space-between">
                                        <span>{{ $item['label'] }}</span>
                                        {!! renderSystemLink($item, $systems) !!}
                                        {!! renderSystemTitle($item, $systems) !!}
                                    </label>
                                    @switch($item['type'])
                                    @case('text')
                                    {!! renderSystemInput($name, $systems) !!}
                                    @break
                                    @case('images')
                                    {!! renderSystemImages($name, $systems) !!}
                                    @break
                                    @case('textarea')
                                    {!! renderSystemTextarea($name, $systems) !!}
                                    @break
                                    @case('select')
                                    {!! renderSystemSelect($item, $name, $systems) !!}
                                    @break
                                    @case('editor')
                                    {!! renderSystemEditor($name, $systems) !!}
                                    @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @include('backend.dashboard.component.button')
    </div>
</form>