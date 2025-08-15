@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('menu.store') }}" method="POST" class="box menuContainer">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        @include('backend.menu.menu.component.catalogue')
        @include('backend.menu.menu.component.list')
        <input type="hidden" name="redirect" value="{{ ($id) ?? 0 }}">
        @include('backend.dashboard.component.button')
    </div>
</form>

<!-- Modal -->
@include('backend.menu.menu.component.popup')