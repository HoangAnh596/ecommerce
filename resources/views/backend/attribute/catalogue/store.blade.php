@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')

@php
$url = ($config['method'] == 'create') ? route('attribute.catalogue.store') : route('attribute.catalogue.update', $attributeCatalogue->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.generalTitle') }}</h5>
                        <div class="ibox-content">
                            @include('backend.dashboard.component.content', ['model' => ($attributeCatalogue) ?? null])
                        </div>
                    </div>
                </div>
                @include('backend.dashboard.component.album')
                @include('backend.dashboard.component.seo', ['model' => ($attributeCatalogue) ?? null])
            </div>
            <div class="col-lg-3">
                @include('backend.attribute.catalogue.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>