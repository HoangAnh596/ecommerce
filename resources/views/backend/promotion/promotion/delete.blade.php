@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('widget.destroy', $widget->id) }}" method="POST" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Bạn đang muốn xóa widget có tên là: {{ $widget->name }}</p>
                        <p>- Lưu ý: Không thể khôi phục widget sau khi xóa. Hãy chắc chắn là bạn muốn thực hiện chức năng này.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Tên widget <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', ($widget->name) ?? '') }}" class="form-control" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label">Từ khóa <span class="text-danger">(*)</span></label>
                                    <input type="text" name="keyword" value="{{ old('keyword', ($widget->keyword) ?? '') }}" class="form-control" placeholder="" readonly>
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