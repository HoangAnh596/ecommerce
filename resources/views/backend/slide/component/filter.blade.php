<form action="{{ route('slide.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @include('backend.dashboard.component.keyword')
                    @can('modules', 'slide.update')
                    <a href="{{ route('slide.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr5"></i>Thêm mới Slide
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</form>