<form action="{{ route('user.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @include('backend.dashboard.component.keyword')
                    @can('modules', 'user.update')
                    <a href="{{ route('user.create') }}" class="btn btn-primary"><i class="fa fa-plus mr5"></i>Thêm mới thành viên</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</form>