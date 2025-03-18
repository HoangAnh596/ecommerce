(function($) {
    var HT = {};
    HT.uploadImageToInput = () => {
        $('.upload-image').click(function(){
            let input = $(this);
            let type = input.attr('data-type');
            HT.setupCkFinder2(input, type);
        })
    }

    HT.setupCkFinder2 = (object, type) => {
        if(typeof(type) == 'undefined'){
            type = 'Images';
        }
        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function( fileUrl, data ) {
            fileUrl = fileUrl.replace("/public", ""); // Loại bỏ /public

            object.val(fileUrl);
        }
        finder.popup();
    }

    $(document).ready(function(){
        HT.uploadImageToInput();
    })
})(jQuery);