(function($) {
	"use strict";
	var HT = {};
    var _token = $('meta[name="csrf_token"]').attr('content');
    var typingTimer;
    var doneTypingInterval = 300;

    HT.searchModel = () => {
        $('.search-model').on('keyup', function(e) {
            e.preventDefault();
            let _this = $(this);
            if($('input[type="radio"]:checked').length === 0) {
                alert('Vui lòng chọn module trước khi tìm kiếm');
                _this.val('');
                return false;
            }
            
            let keyword = _this.val();
            let option = {
                model: $('input[type="radio"]:checked').val(),
                keyword: keyword,
            }
            
            HT.sendAjax(option);
        });
    }

    HT.chooseModel = () => {
        $('.input-radio').on('change', function() {
            let _this = $(this);
            let option = {
                model: _this.val(),
                keyword: $('.search-model').val(),
            }
            $('.search-model-result').html('');
            if(keyword.length >= 2) {
                HT.sendAjax(option);
            }
        });
    }

    HT.sendAjax = (option) => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            $.ajax({
                url: 'ajax/dashboard/findModelObject',
                type: 'GET',
                data: option,
                dataType: 'json',
                success: function(res) {
                    let html = HT.renderSearchResult(res);
                    if(html.length) {
                        $('.ajax-search-result').html(html).show();
                    }else {
                        $('.ajax-search-result').html(html).hide();
                    }
                },
                beforeSend: function() {
                    $('.ajax-search-result').html('').hide();
                },
            });
        }, doneTypingInterval);
    }

    HT.renderSearchResult = (data) => {
        let html = '';
        if(data.length) {
            for(let i = 0; i < data.length; i++) {
                let flag = ($('#model-' + data[i].id).length) ? 1 : 0;
                let setChecked = ($('#model-' + data[i].id).length) ? HT.setChecked() : '';
                html += `<button class="ajax-search-item" data-canonical="${data[i].languages[0].pivot.canonical}" 
                                data-id="${data[i].id}" data-flag="${flag}" 
                                data-image="${data[i].image}" data-name="${data[i].languages[0].pivot.name}">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <span>${data[i].languages[0].pivot.name}</span>
                                <div class="auto-icon">${setChecked}</div>
                            </div>
                        </button>`;
            }
        }

        return html;
    }

    HT.setChecked = () => {
        let html = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                    </svg>`;

        return html;
    }

    HT.unfocusSearchBox = () => {
        $(document).on('click', 'html', function(e) {
            if(!$(e.target).hasClass('search-model-result') && !$(e.target).hasClass('search-model')) {
                $('.ajax-search-result').html('');
            }
        });
        $(document).on('click', '.ajax-search-result', function(e) {
            e.stopPropagation();
        });
    }

    HT.addModel = () => {
        $(document).on('click', '.ajax-search-item', function(e) { //hàm này
            e.preventDefault();
            let _this = $(this);
            let data = _this.data();
            let flag = _this.attr('data-flag');
            if(flag == 0) {
                _this.find('.auto-icon').html(HT.setChecked());
                _this.attr('data-flag', 1);
                $('.search-model-result').append(HT.modelTemplate(data)).show();
            }else{
                $('#model-' + data.id).remove();
                _this.find('.auto-icon').html('');
                _this.attr('data-flag', 0);
            }
            
        });
    }

    HT.modelTemplate = (data) => {
        let html = `<div class="search-result-item" id="model-${data.id}" data-model-id="${data.id}" data-canonical="${data.canonical}">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="uk-flex uk-flex-middle">
                                <span class="image img-cover"><img src="${data.image}" alt=""></span>
                                <span class="name">${data.name}</span>
                                <div class="hidden">
                                    <input type="text" name="modelItem[id][]" value="${data.id}">
                                    <input type="text" name="modelItem[name][]" value="${data.name}">
                                    <input type="text" name="modelItem[image][]" value="${data.image}">
                                </div>
                            </div>
                            <div class="deleted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </div>
                        </div>
                    </div>`;

        return html;
    }

    HT.removeModel = () => {
        $(document).on('click', '.deleted', function(){
            let _this = $(this);
            _this.parents('.search-result-item').remove()
        });
    }

    $(document).ready(function(){
        HT.searchModel();
        HT.chooseModel();
        HT.unfocusSearchBox();
        HT.addModel();
        HT.removeModel();
    });
})(jQuery);