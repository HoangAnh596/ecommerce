<form action="{{ route('customer.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    <!-- <select class="form-control mr-10 setupSelect2" name="search-customer-catalogue">
                        <option value="0" selected="selected">Chọn nhóm thành viên</option>
                        @foreach($customerCatalogues as $key => $val)
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select> -->
                    @include('backend.dashboard.component.keyword')
                    @can('modules', 'customer.update')
                    <a href="{{ route('customer.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm mới khách hàng</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</form>