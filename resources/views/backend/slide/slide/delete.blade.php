@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('slide.destroy', $slide->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Bạn đang muốn xóa Slide có tên là: {{ $slide->mail }}</p>
                        <p>- Lưu ý: Không thể khôi phục thành viên sau khi xóa. Hãy chắc chắn là bạn muốn thực hiện chức năng này.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Email <span class="text-danger">(*)</span></label>
                                    <input type="text" name="email" value="{{ old('email', ($slide->email) ?? '') }}" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Họ và tên <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($slide->name) ?? '') }}" class="form-control" placeholder="" readonly>
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