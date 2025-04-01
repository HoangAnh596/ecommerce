<form action="{{ route('permission.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @include('backend.dashboard.component.keyword')
                    @can('modules', 'permission.create')
                    <a href="{{ route('permission.create') }}" class="btn btn-primary"><i class="fa fa-plus mr5"></i>{{ __('messages.permission.create.title') }}</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</form>