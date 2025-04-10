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
        @if(isset($posts) && is_object($posts))
        @foreach($posts as $post)
        <tr id="{{ $post->id }}">
            <td>
                <input type="checkbox" value="{{ $post->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <div class="uk-flex uk-flex-middle">
                    <div class="image mr5">
                        <div class="img-cover image-list"><img src="{{ asset($post->image) }}" alt="{{ $post->name }}"></div>
                    </div>
                    <div class="main-infor">
                        <div class="name">
                            <span class="maintitle">{{ $post->name }}</span>
                        </div>
                        <div class="catalogue">
                            <span class="text-danger">{{ __('messages.tableGroup') }}</span>
                            @foreach($post->post_catalogues as $val)
                            @foreach($val->post_catalogue_language as $cat)
                            <a href="{{ route('post.index', ['post_catalogue_id' => $val->id]) }}">{{ $cat->name }}</a>
                            @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </td>
            @include('backend.dashboard.component.languageTd', ['model' => $post, 'modeling' => 'Post'])
            <td>
                <input type="text" name="order" class="form-control sort-order text-right" value="{{ $post->order }}" data-id="{{ $post->id }}" data-model="{{ $config['model'] }}">
            </td>
            <td class="text-center js-switch-{{ $post->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ $post->publish }}" data-modelId="{{ $post->id }}" {{ ($post->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', 'post.update')
                <a href="{{ route('post.edit', $post->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', 'post.destroy')
                <a href="{{ route('post.delete', $post->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ $posts->links('pagination::bootstrap-4') }}