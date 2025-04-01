@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('user.destroy', $user->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    @include('backend.dashboard.component.destroy', ['model' => ($user) ?? null])
</form>