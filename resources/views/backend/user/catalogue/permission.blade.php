@include('backend.user.catalogue.component.breadcrumb', ['title' => $config['seo']['permission']['title']])

<form action="{{ route('user.catalogue.updatePermission') }}" method="POST" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox-title"><h5>Cấp quyền</h5></div>
                <div class="ibox-content">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th></th>
                            @foreach($userCatalogues as $userCatalogue)
                            <th class="text-center">{{ $userCatalogue->name }}</th>
                            @endforeach
                        </tr>
                        @foreach($permissions as $permission)
                        <tr>
                            <td>
                                <a href="" class="uk-flex uk-flex-middle uk-flex-space-between">
                                    {{ $permission->name }}
                                    <span style="color: red;">({{ $permission->canonical  }})</span>
                                </a>
                            </td>
                            @foreach($userCatalogues as $userCatalogue)
                            <td>
                                <input type="checkbox" 
                                    name="permission[{{ $userCatalogue->id }}][]" 
                                    class="form-control" 
                                    value="{{ $permission->id }}"
                                    {{ (collect($userCatalogue->permissions)->contains('id', $permission->id)) ? 'checked' : ''}}>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>