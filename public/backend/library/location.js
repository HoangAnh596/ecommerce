(function($) {
    var HT = {};
    
    HT.getLocation = () => {
        $(document).on('change', '.location', function() {
            let _this = $(this);
            let option = {
                'data' : {
                    'location_id' : _this.val(),
                },
                'target' : _this.attr('data-target'),
            }
            
            HT.sendDataTogetLocation(option);
        });
    }

    HT.sendDataTogetLocation = (option) => {
        $.ajax({
            url: 'ajax/location/getLocation',
            type: 'GET',
            data: option,
            dataType: 'json',
            success: function(res) {
                $('.'+option.target).html(res.html);
                if(districtId != '' && option.target == 'districts') {
                    $(".districts").val(districtId).trigger('change');
                }
                if(wardId != '' && option.target == 'wards') {
                    $(".wards").val(wardId).trigger('change');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Lỗi: ' + textStatus + '' + errorThrown);
            }
        })
    }

    HT.loadCity = () => {
        if(provinceId != '') {
            $(".provinces").val(provinceId).trigger('change');
        }
    }

    $(document).ready(function() {
        HT.getLocation();
        HT.loadCity();
    });
})(jQuery);