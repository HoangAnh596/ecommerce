@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('customer.catalogue.destroy', $customerCatalogue->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    @include('backend.dashboard.component.destroy', ['model' => ($customerCatalogue) ?? null])
</form>