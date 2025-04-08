<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 30px">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">{{ __('messages.tableName') }}</th>
            @include('backend.dashboard.component.languageTh')
            <th class="text-center" style="width: 80px">{{ __('messages.tableOrder') }}</th>
            <th class="text-center" style="width: 100px">{{ __('messages.tableStatus') }}</th>
            <th class="text-center" style="width: 100px">{{ __('messages.tableAction') }}</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($products) && is_object($products))
        @foreach($products as $product)
        <tr id="{{ $product->id }}">
            <td>
                <input type="checkbox" value="{{ $product->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <div class="uk-flex uk-flex-middle">
                    <div class="image mr5">
                        <div class="img-cover image-list"><img src="{{ asset($product->image) }}" alt="{{ $product->name }}"></div>
                    </div>
                    <div class="main-infor">
                        <div class="name">
                            <span class="maintitle">{{ $product->name }}</span>
                        </div>
                        <div class="catalogue">
                            <span class="text-danger">{{ __('messages.tableGroup') }}</span>
                            @foreach($product->product_catalogues as $val)
                            @foreach($val->product_catalogue_language as $cat)
                            <a href="{{ route('product.index', ['product_catalogue_id' => $val->id]) }}">{{ $cat->name }}</a>
                            @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </td>
            @include('backend.dashboard.component.languageTd', ['model' => $product, 'modeling' => 'Product'])
            <td>
                <input type="text" name="order" class="form-control sort-order text-right" value="{{ $product->order }}" data-id="{{ $product->id }}" data-model="{{ $config['model'] }}">
            </td>
            <td class="text-center js-switch-{{ $product->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $product->publish }}" data-modelId="{{ $product->id }}" {{ ($product->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'product.update')
                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'product.destroy')
                <a href="{{ route('product.delete', $product->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $products->links('pagination::bootstrap-4') }}