@include('backend.user.catalogue.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('user.catalogue.destroy', $userCatalogue->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Bạn đang muốn xóa nhóm thành viên có tên là: <span class="text-danger">{{ $userCatalogue->name }}</span></p>
                        <p>- Lưu ý: Không thể khôi phục nhóm thành viên sau khi xóa. Hãy chắc chắn là bạn muốn thực hiện chức năng này.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Tên nhóm thành viên <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($userCatalogue->name) ?? '') }}" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Ghi chú <span class="text-danger">(*)</span></label>
                                    <input type="text" name="description" value="{{ old('description', ($userCatalogue->description) ?? '') }}" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-danger" type="submit" name="send" value="send">Xóa dữ liệu</button>
        </div>
    </div>
</form>