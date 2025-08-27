@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])

@include('backend.dashboard.component.formError')
@php
$url = ($config['method'] == 'create') ? route('post.store') : route('post.update', $post->id);
@endphp
<form action="{{ $url }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                        <div class="ibox-content">
                            @include('backend.dashboard.component.content', ['model' => ($post) ?? null])
                        </div>
                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => ($post) ?? null])
                @include('backend.dashboard.component.seo', ['model' => ($post) ?? null])
            </div>
            <div class="col-lg-3">
                @include('backend.post.post.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>