<form action="{{ route('post.catalogue.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @include('backend.dashboard.component.keyword')
                    @can('modules', 'post.catalogue.create')
                    <a href="{{ route('post.catalogue.create') }}" class="btn btn-primary"><i class="fa fa-plus mr5"></i>{{ __('messages.postCatalogue.create.title') }}</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</form>