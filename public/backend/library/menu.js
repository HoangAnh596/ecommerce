(function($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf_token"]').attr('content');

    HT.createMenuCatalogue = () => {
        $(document).on('submit', '.create-menu-catalogue', function(e) {
            e.preventDefault()
            let _form = $(this);
            let option = {
                'name' : _form.find('input[name="name"]').val(),
                'keyword' : _form.find('input[name="keyword"]').val(),
                '_token' : _token,
            }

            $.ajax({
                url: 'ajax/menu/createCatalogue',
                type: 'POST',
                data: option,
                dataType: 'json',
                success: function(res) {
                    if(res.code == 0){
                        $('.form-error').removeClass('text-danger').addClass('text-success').html(res.message).show();
                        const menuCatalogueSelect = $('select[name="menu_catalogue_id"]');
                        menuCatalogueSelect.append('<option value="' + res.data.id + '">' +res.data.name+ '</option>');
                    }else{
                        $('.form-error').removeClass('text-success').addClass('text-danger').html(res.message).show();
                    }
                },
                beforeSend: function() {
                    _form.find('.error').html('');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(jqXHR.status === 422) {
                        let errors = jqXHR.responseJSON.errors;
                        for (let field in errors) {
                            let errorMessage = errors[field];
                            errorMessage.forEach(function(message) {
                                $('.' + field).html(message);
                            })
                        }
                    }
                }
            })
        });
    }

    HT.createMenuRow = () => {
        $(document).on('click', '.add-menu', function(e) {
            e.preventDefault();
            let _this = $(this);
            
            $('.menu-wrapper').append(HT.menuRowHtml()).find('.notification').hide();
        });
    }

    HT.menuRowHtml = () => {
        let row  = $('<div>').addClass('row mb10 menu-item');
        const columns = [
            { class: 'col-md-4', name: 'menu[name][]' },
            { class: 'col-md-4', name: 'menu[canonical][]' },
            { class: 'col-md-2', name: 'menu[order][]', value: 0 },
        ];
        columns.forEach(col => {
            let $col = $('<div>').addClass(col.class);
            let $input = $('<input>').attr('type', 'text').attr('value', col.value)
                    .addClass('form-control '+ ((col.name == 'menu[order][]') ? 'int text-right' : ''))
                    .attr('name', col.name);
            $col.append($input);
            row.append($col);
        });
        let $removeCol = $('<div>').addClass('col-lg-2');
        let $removeRow = $('<div>').addClass('form-row text-center');
        let $btnLink = $('<a>').addClass('delete-menu');
        let $image = $('<img>').attr('src', 'backend/close.png');
        $btnLink.append($image);
        $removeRow.append($btnLink);
        $removeCol.append($removeRow);
        row.append($removeCol);

        return row;
    }

    HT.deleteMenuRow = () => {
        $(document).on('click', '.delete-menu', function(e) {
            e.preventDefault();
            let _this = $(this);
            _this.parents('.menu-item').remove();
            HT.checkMenuItemLength();
        });    
    }

    HT.checkMenuItemLength = () => {
        if($('.menu-item').length === 0) {
            $('.notification').show();
        }
    }

    HT.getMenu = () => {
        $(document).on('click', '.menu-module', function(e) {
            e.preventDefault()
            let _this = $(this);
            let option = {
                'model' : _this.attr('data-model'),
            }

            $.ajax({
                url: 'ajax/dashboard/getMenu',
                type: 'GET',
                data: option,
                dataType: 'json',
                success: function(res) {
                    console.log(res);
                    
                },
                beforeSend: function() {
                    _this.find('.error').html('');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                }
            })
        })
    }

    HT.renderModelMenu = () => {
        
    }

    $(document).ready(function() {
        HT.createMenuCatalogue();
        HT.createMenuRow();
        HT.deleteMenuRow();
        HT.getMenu();
    });
})(jQuery);