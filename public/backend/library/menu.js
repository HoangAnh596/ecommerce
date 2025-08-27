(function($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf_token"]').attr('content');
    var typingTimer;
    var doneTypingInterval = 500;

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

    HT.menuRowHtml = (option) => {
        let row  = $('<div>').addClass('row mb10 menu-item '+ ((typeof(option) != 'undefined') ? option.canonical : '') +'');
        const columns = [
            { class: 'col-md-4', name: 'menu[name][]', value: (typeof(option) != 'undefined') ? option.name : '' },
            { class: 'col-md-4', name: 'menu[canonical][]', value: (typeof(option) != 'undefined') ? option.canonical : '' },
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
        let $input = $('<input>').addClass('hidden').attr('name', 'menu[id][]').attr('value', 0);

        $btnLink.append($image);
        $removeRow.append($btnLink);
        $removeCol.append($removeRow);
        $removeCol.append($input);
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
            let target = _this.parents('.panel-default').find('.menu-list').html('html');
            let menuRowClass = HT.checkedMenuRowExist();
            HT.sendAjaxGetMenu(option, target, menuRowClass);
        })
    }

    HT.checkedMenuRowExist = () => {
        let menuRowClass= $('.menu-item').map(function() {
            let allClasses = $(this).attr('class').split(' ').slice(3).join('');

            return allClasses
        }).get();

        return menuRowClass;
    }

    HT.sendAjaxGetMenu = (option, target, menuRowClass) => {
        $.ajax({
            url: 'ajax/dashboard/getMenu',
            type: 'GET',
            data: option,
            dataType: 'json',
            beforeSend: function() {
                $('.menu-list').html('');
            },
            success: function(res) {
                let html = '';
                for(let i = 0; i < res.data.length; i++) {
                    html += HT.renderModelMenu(res.data[i], menuRowClass);
                }
                html += HT.menuLinks(res.links).prop('outerHTML');
                target.html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
            }
        })
    }

    HT.menuLinks = (links) => {
        let nav = $('<nav>');
        if(links.length > 3) {
            let paginationUl = $('<ul>').addClass('pagination');
            $.each(links, function(index, link) {
                let li = $('<li>').addClass('page-item');
                let labelRaw = $('<div>').html(link.label).text().trim(); // giải mã entity

                if (link.active) {
                    // Trang hiện tại
                    li.addClass('active').attr('aria-current', 'page');
                    li.append($('<span>').addClass('page-link').text(labelRaw));
                } 
                else if (!link.url) {
                    // Nút disabled (Previous khi không có link)
                    li.addClass('disabled').attr('aria-disabled', 'true');
                    li.append($('<span>').addClass('page-link')
                        .attr('aria-hidden', 'true')
                        .text((labelRaw.toLowerCase().includes('previous') || labelRaw.includes('«')) ? '«' : (labelRaw.toLowerCase().includes('next') || labelRaw.includes('»')) ? '»' : labelRaw)
                    );
                } 
                else {
                    // Link bình thường hoặc Next
                    let a = $('<a>').addClass('page-link').attr('href', link.url);

                    if (labelRaw.toLowerCase().includes('previous') || labelRaw.includes('«')) {
                        a.attr('aria-label', '« Previous').text('«');
                    } 
                    else if (labelRaw.toLowerCase().includes('next') || labelRaw.includes('»')) {
                        a.attr('rel', 'next').attr('aria-label', 'Next »').text('»');
                    } 
                    else {
                        a.text(labelRaw);
                    }

                    li.append(a);
                }

                paginationUl.append(li);
            });
            nav.append(paginationUl);
        }
        return nav;
    }

    // HT.menuLinks = (links) => {
    //     let nav = $('<nav>');
    //     if(links.length > 3) {
    //         let paginationUl = $('<ul>').addClass('pagination');
    //         $.each(links, function(index, link) {
    //             let liClass = 'page-item'
    //             if(link.active) {
    //                 liClass += ' active';
    //             }else if(!link.url) {
    //                 liClass += ' disabled';
    //             }
    
    //             let li = $('<li>').addClass(liClass);
    //             if(link.label == 'pagination.previous') {
    //                 let span = $('<span>').addClass('page-link').attr('aria-hidden', true).html('<');
    //                 li.append(span);
    //             }else if(link.label == 'pagination.next') {
    //                 let span = $('<span>').addClass('page-link').attr('aria-hidden', true).html('>');
    //                 li.append(span);
    //             }else if(link.url) {
    //                 let a = $('<a>').addClass('page-link').text(link.label).attr('href', link.url).attr('data-page', link.label);
    //                 li.append(a);
    //             }
    //             paginationUl.append(li);
    //         })
    //         nav.append(paginationUl);
    //     }
    //     return nav;
    // }

    HT.getPaginationMenu = () => {
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            let _this = $(this);
            let option = {
                'model' : _this.parents('.panel-collapse').attr('id'),
                page: _this.text()
            }
            let target = _this.parents('.menu-list');
            let menuRowClass  = HT.checkedMenuRowExist();
            HT.sendAjaxGetMenu(option, target, menuRowClass);
        })
    }

    HT.renderModelMenu = (object, renderModelMenu) => {
        let html = '';
        html += '<div class="m-item">'
            html += '<div class="uk-flex uk-flex-middle">'
                html += '<input type="checkbox" '+ ((renderModelMenu.includes(object.canonical)) ? 'checked' : '') +' class="m0 choose-menu" id="'+object.canonical+'" name="" value="'+object.canonical+'">'
                html += '<label for="'+object.canonical+'">'+object.name+'</label>'
            html += '</div>'
        html += '</div>'

        return html;
    }

    HT.chooseMenu = () => {
        $(document).on('click', '.choose-menu', function(e) {
            let _this = $(this);
            let canonical = _this.val();
            let name = _this.siblings('label').text();
            let $row = HT.menuRowHtml({
                name: name,
                canonical: canonical
            })

            let isChecked = _this.prop('checked');
            if(isChecked === true) {
                $('.menu-wrapper').append($row).find('.notification').hide();
            }else{
                $('.menu-wrapper').find('.'+canonical).remove();
                HT.checkMenuItemLength();
            }
        })
    }

    HT.searchMenu = () => {
        $(document).on('keyup', '.search-menu', function(e) {
            e.preventDefault();
            let _this = $(this);
            let keyword = _this.val();
            let option = {
                model: _this.parents('.panel-collapse').attr('id'),
                keyword: keyword,
            }
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                let target = _this.siblings('.menu-list');
                let menuRowClass  = HT.checkedMenuRowExist();
                HT.sendAjaxGetMenu(option, target, menuRowClass);
            }, doneTypingInterval);
        })
    }

    HT.setupNestable = () => {
        var updateOutput = function(e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');
            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
            } else {
                output.val('JSON browser support required for this demo.');
            }
        };

        // activate Nestable for list 2
        $('#nestable2').nestable({
            group: 1
        }).on('change', HT.updateNestableOutput);

        // output initial serialised data
        updateOutput($('#nestable2').data('output', $('#nestable2-output')));

        $('#nestable-menu').on('click', function(e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
    }

    HT.updateNestableOutput = (e) => {
        var list = $(e.currentTarget),
        output = list.data('output');
        let json = window.JSON.stringify(list.nestable('serialize'));
        if(json.length){
            let option = {
                json: json,
                menu_catalogue_id: $('#dataCatalogue').attr('data-catalogueId'),
                _token: _token,
            }
            $.ajax({
                url: 'ajax/menu/drag',
                type: 'POST',
                data: option,
                dataType: 'json',
                success: function(res) {
                    console.log(res); 
                },
            })
        }
    }

    $(document).ready(function() {
        HT.createMenuCatalogue();
        HT.createMenuRow();
        HT.deleteMenuRow();
        HT.getMenu();
        HT.chooseMenu();
        HT.getPaginationMenu();
        HT.searchMenu();
        HT.setupNestable();
    });
})(jQuery);