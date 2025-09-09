<div class="modal fade" id="createMenuCatalogue">
    <form class="form create-menu-catalogue" action="" method="">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Thêm mới vị trí hiển thị của menu</h4>
                    <small class="font-bold text-navy">Nhập đầy đủ thông tin để tạo vị trí hiển thị của menu.</small>
                </div>
                <div class="modal-body">
                    <div class="form-error text-success"></div>
                    <div class="row">
                        <div class="col-lg-12 mb10">
                            <label for="">Tên vị trí hiển thị</label>
                            <input type="text" class="form-control" name="name" value="">
                            <div class="error name"></div>
                        </div>
                        <div class="col-lg-12 mb10">
                            <label for="">Từ khóa</label>
                            <input type="text" class="form-control" name="keyword" value="">
                            <div class="error keyword"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="create" value="create" class="btn btn-primary">Lưu Lại</button>
                </div>
            </div>
        </div>
    </form>
</div>