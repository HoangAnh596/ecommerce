@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('generate.destroy', $generate->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    @include('backend.dashboard.component.destroy', ['model' => ($generate) ?? null])
</form>