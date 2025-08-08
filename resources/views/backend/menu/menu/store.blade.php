@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('menu.store') : route('menu.update', $menu->id);
@endphp
<form action="{{ $url }}" method="POST" class="box menuContainer">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        @include('backend.menu.menu.component.catalogue')
        @include('backend.menu.menu.component.list')
        <hr>
        @include('backend.dashboard.component.button')
    </div>
</form>

<!-- Modal -->
@include('backend.menu.menu.component.popup')