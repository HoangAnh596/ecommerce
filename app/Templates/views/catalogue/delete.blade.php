@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('{view}.destroy', ${module}->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    @include('backend.dashboard.component.destroy', ['model' => (${module}) ?? null])
</form>