(function($) {
    var HT = {};
    var _token = $('meta[name="csrf_token"]').attr('content');
    
    HT.switchery =() => {
        $('.js-switch').each(function() {
            var switchery = new Switchery(this, {
                color: '#1AB394',
                size: 'small'
            });
        });
    }

    HT.select2 = () => {
        if($('.setupSelect2').length){
            $('.setupSelect2').select2();
        }
    };

    HT.sortui = () => {
        $('#sortable').sortable();
        $('#sortable').disableSelection();
    }

    HT.changeStatus = () => {
        if($('.status').length){
            $(document).on('change', '.status', function(e){
                e.preventDefault();
                let _this = $(this);
                let option = {
                    'value': _this.val(),
                    'modelId': _this.attr('data-modelId'),
                    'model': _this.attr('data-model'),
                    'field': _this.attr('data-field'),
                    '_token': _token
                }
                
                $.ajax({
                    url: window.location.origin + '/ajax/dashboard/changeStatus',
                    type: 'POST',
                    data: option,
                    dataType: 'json',
                    success: function(res) {
                        console.log(res);
                        
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Lỗi: ' + textStatus + '' + errorThrown);
                    }
                })
                
            })
        }
    }

    HT.changeStatusAll = () => {
        if($('.changeStatusAll').length){
            $(document).on('click', '.changeStatusAll', function(e){
                e.preventDefault();
                let _this = $(this);
                let id = [];
                $('.checkBoxItem').each(function(){
                    let checkBox  = $(this)
                    if(checkBox.prop('checked')) {
                        id.push(checkBox.val())
                    }
                })

                let option = {
                    'value': _this.attr('data-value'),
                    'model': _this.attr('data-model'),
                    'field': _this.attr('data-field'),
                    'id': id,
                    '_token': _token
                }

                $.ajax({
                    url: window.location.origin + '/ajax/dashboard/changeStatusAll',
                    type: 'POST',
                    data: option,
                    dataType: 'json',
                    success: function(res) {
                        if(res.flag == true){
                            let cssActive1 = 'background-color: rgb(26, 179, 148); border-color: rgb(26, 179, 148); box-shadow: rgb(26, 179, 148) 0px 0px 0px 16px inset; transition: border 0.4s, box-shadow 0.4s, background-color 1.2s;';
                            let cssActive2 = 'left: 20px; background-color: rgb(255, 255, 255); transition: background-color 0.4s, left 0.2s;';
                            let cssUnActive1 = 'box-shadow: rgb(223, 223, 223) 0px 0px 0px 0px inset; border-color: rgb(223, 223, 223); background-color: rgb(255, 255, 255); transition: border 0.4s, box-shadow 0.4s;';
                            let cssUnActive2 = 'left: 0px; transition: background-color 0.4s, left 0.2s;';
                            for(let i = 0; i < id.length; i++){
                                if(option.value == 2){
                                    $('.js-switch-' + id[i]).find('span.switchery').attr('style', cssActive1).find('small').attr('style', cssActive2);
                                }else if(option.value == 1){
                                    $('.js-switch-' + id[i]).find('span.switchery').attr('style', cssUnActive1).find('small').attr('style', cssUnActive2);
                                }
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Lỗi: ' + textStatus + '' + errorThrown);
                    }
                })

                console.log(option);
                
            })
        }
    }

    HT.checkAll = () => {
        if($('#checkAll').length) {
            $(document).on('click', '#checkAll', function(){
                let isChecked = $(this).prop('checked');

                $('.checkBoxItem').prop('checked', isChecked);
                $('.checkBoxItem').each(function(){
                    let _this = $(this);
                    HT.changeBackground(_this);
                })
            })
        }
    }

    HT.checkBoxItem = () => {
        if($('.checkBoxItem').length) {
            $(document).on('click', '.checkBoxItem', function(){
                let _this = $(this);
                HT.changeBackground(_this);
                HT.allChecked();
            })
        }
    }

    HT.changeBackground = (object) => {
        let isChecked = object.prop('checked');
        if(isChecked){
            object.closest('tr').addClass('active-bg');
        }else {
            object.closest('tr').removeClass('active-bg');
        }
    }

    HT.allChecked = () => {
        let allChecked = $('.checkBoxItem:checked').length === $('.checkBoxItem').length;
        $('#checkAll').prop('checked', allChecked);
    }

    HT.int = () => {
        $(document).on('change keyup blur', '.int', function(){
            let _this = $(this)
            let value = _this.val()
            if(value === ''){
                $(this).val('0')
            }
            value = value.replace(/\./gi, "")
            _this.val(HT.addCommas(value))
            if(isNaN(value)){
                _this.val('0')
            }
        })

        $(document).on('keydown', '.int', function(e){
            let _this = $(this)
            let data = _this.val()
            if(data == 0){
                let unicode = e.keyCode || e.which;
                if(unicode != 190){
                    _this.val('')
                }
            }
        })
    }

    HT.addCommas = (nStr) => { 
        nStr = String(nStr);
        nStr = nStr.replace(/\./gi, "");
        let str ='';
        for (let i = nStr.length; i > 0; i -= 3){
            let a = ( (i-3) < 0 ) ? 0 : (i-3);
            str= nStr.slice(a,i) + '.' + str;
        }
        str= str.slice(0,str.length-1);
        return str;
    }

    $(document).ready(function() {
        HT.switchery();
        HT.select2();
        HT.changeStatus();
        HT.checkAll();
        HT.checkBoxItem();
        HT.allChecked();
        HT.changeStatusAll();
        HT.sortui();
        HT.int();
    });
})(jQuery);