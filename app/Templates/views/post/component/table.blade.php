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
        @if(isset(${module}s) && is_object(${module}s))
        @foreach(${module}s as ${module})
        <tr id="{{ ${module}->id }}">
            <td>
                <input type="checkbox" value="{{ ${module}->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                <div class="uk-flex uk-flex-middle">
                    <div class="image mr5">
                        <div class="img-cover image-list"><img src="{{ asset(${module}->image) }}" alt="{{ ${module}->name }}"></div>
                    </div>
                    <div class="main-infor">
                        <div class="name">
                            <span class="maintitle">{{ ${module}->name }}</span>
                        </div>
                        <div class="catalogue">
                            <span class="text-danger">{{ __('messages.tableGroup') }}</span>
                            @foreach(${module}->{module}_catalogues as $val)
                            @foreach($val->{module}_catalogue_language as $cat)
                            <a href="{{ route('{module}.index', ['{module}_catalogue_id' => $val->id]) }}">{{ $cat->name }}</a>
                            @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </td>
            @include('backend.dashboard.component.languageTd', ['model' => ${module}, 'modeling' => '{Module}'])
            <td>
                <input type="text" name="order" class="form-control sort-order text-right" value="{{ ${module}->order }}" data-id="{{ ${module}->id }}" data-model="{{ $config['model'] }}">
            </td>
            <td class="text-center js-switch-{{ ${module}->id }}">
                <input type="checkbox" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" value="{{ ${module}->publish }}" data-modelId="{{ ${module}->id }}" {{ (${module}->publish == 2) ? 'checked' : '' }} />
            </td>
            <td class="text-center">
                @can('modules', '{module}.update')
                <a href="{{ route('{module}.edit', ${module}->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                @endcan
                @can('modules', '{module}.destroy')
                <a href="{{ route('{module}.delete', ${module}->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                @endcan
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
{{ ${module}s->links('pagination::bootstrap-4') }}