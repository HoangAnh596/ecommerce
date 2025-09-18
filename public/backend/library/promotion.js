(function($) {
	"use strict";
	var HT = {};
    var typingTimer;
    var doneTypingInterval = 500;

    $.fn.elExist = function () {
        return this.length > 0;
    }

    HT.promotionNeverEnd = () => {
        $(document).on('change', '#neverEnd', function() {
            let _this = $(this);
            let isChecked = _this.prop('checked');
            let startDateVal = $('input[name="startDate"]').val(); // lấy giá trị ngày bắt đầu
            let $endDate = $('input[name="endDate"]');
            if(isChecked) {
                $endDate.val('').attr('disabled', true);
            }else{
                $endDate.val(startDateVal).attr('disabled', false);
            }
        });
    }

    HT.promotionSource = () => {
        $(document).on('click', '.chooseSource', function() {
            let _this = $(this);
            let flag = (_this.attr('id') == 'allSource') ? true : false;
            if(flag) {
                _this.parents('.ibox-content').find('.source-wrapper').remove;
            }else{
                $.ajax({
                    url: 'ajax/source/getAllSource',
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        let sourceData = res.data;
                        if(!$('.source-wrapper').length) {
                            let sourceHtml = HT.renderPromotionSource(sourceData).prop('outerHTML');
                            _this.parents('.ibox-content').append(sourceHtml);
                            HT.promotionMultipleSelect2();
                        }
                    },
                });
            }
        });
    }

    HT.renderPromotionSource = (sourceData) => {
        let wrapper = $('<div>').addClass('source-wrapper');
        if(sourceData.length) {
            let select = $('<select>')
                .addClass('multipleSelect2')
                .attr('name', 'sourceValue[]')
                .attr('multiple', true);

            for(let i = 0; i < sourceData.length; i++) {
                let option = $('<option>').attr('value', sourceData[i].id).html(sourceData[i].name);
                select.append(option);
            }
            wrapper.append(select);
        }

        return wrapper;
    }

    HT.chooseCustomerCondition = () => {
        $(document).on('change', '.chooseApply', function(){
            let _this = $(this);
            let id = _this.attr('id');

            if(id === 'allApply'){
                _this.parents('.ibox-content').find('.apply-wrapper').remove;
            }else{
                let applyHtml = HT.renderApplyCondition().prop('outerHTML');
                _this.parents('.ibox-content').append(applyHtml);
                HT.promotionMultipleSelect2();
            }
        })
    }

    HT.renderApplyCondition = () => {
        let applyConditionData = JSON.parse($('.applyStatusList').val());
        let wrapper = $('<div>').addClass('apply-wrapper');
        let wrapperCondition = $('<div>').addClass('wrapper-condition');
        if(applyConditionData.length) {
            let select = $('<select>')
                .addClass('multipleSelect2 conditionItem')
                .attr('name', 'applyValue[]')
                .attr('multiple', true);

            for(let i = 0; i < applyConditionData.length; i++) {
                let option = $('<option>').attr('value', applyConditionData[i].id).text(applyConditionData[i].name);
                select.append(option);
            }
            wrapper.append(select);
            wrapper.append(wrapperCondition);
        }

        return wrapper;
    }

    HT.chooseApplyItem = () => {
        $(document).on('change', '.conditionItem', function(){
            let _this = $(this);
            let condition = {
                value: _this.val(),
                label: _this.select2('data')
            };

            $('.wrapperConditionItem').each(function(){
                let _item = $(this);
                let itemClass = _item.attr('class').split(' ')[2];
                if(condition.value.includes(itemClass) == false) {
                    _item.remove();
                }
            });

            for(let i = 0; i < condition.value.length; i++){
                let value = condition.value[i];
                HT.createConditionItem(value, condition.label[i].text);
            }
        });
    }

    HT.createConditionLabel = (label, value) => {
        let deleteButton = $('<div>')
            .addClass('delete')
            .html(`<svg data-icon="TrashSolidLarge" aria-hidden="true" focusable="false" width="15" height="16" viewBox="0 0 15 16" class="bem-Svg" style="display: block;">
                <path fill="currentColor" d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H1v2h13V2h-1z"></path>
            </svg>`)
            .attr('data-condition-item', value);
        let conditionLabel = $('<div>').addClass('conditionLabel').text(label);
        let flex = $('<div>').addClass('uk-flex uk-flex-middle uk-flex-space-between');
        let wrapperBox = $('<div>').addClass('mb5');
        flex.append(conditionLabel).append(deleteButton);
        wrapperBox.append(flex);

        return wrapperBox.prop('outerHTML');
    }

    HT.createConditionItem = (value, label) => {
        if (!$('.wrapper-condition').find('.' + value).elExist()) {
            $.ajax({
                url: 'ajax/dashboard/getPromotionConditionValue',
                type: 'GET',
                data: {
                    value
                },
                dataType: 'json',
                success: function(res) {
                    let optionData = res.data;
                    let conditionItem = $('<div>').addClass('wrapperConditionItem mt10 ' + value);

                    let conditionHiddenInput = $('.condition_input_' + value);
                    let conditionHiddenInputValue = [];
                    if(conditionHiddenInput.length) {
                        conditionHiddenInputValue = JSON.parse(conditionHiddenInput.val());
                    }

                    let select = $('<select>')
                            .addClass('multipleSelect2 objectItem')
                            .attr('name', value + "[]")
                            .attr('multiple', true);
            
                    for(let i = 0; i < optionData.length; i++) {
                        let option = $('<option>').attr('value', optionData[i].id).text(optionData[i].text);
                        select.append(option);
                    }
                    select.val(conditionHiddenInputValue).trigger('change');

                    const conditionLabel = HT.createConditionLabel(label, value);
                    conditionItem.append(conditionLabel);
                    conditionItem.append(select);
            
                    $('.wrapper-condition').append(conditionItem);
                    HT.promotionMultipleSelect2();
                },
            });
        }
    }

    // HT.deleteCondition = () => {
    //     $(document).on('click', '.wrapperConditionItem .delete', function() {
    //         let _this = $(this);
    //         let unSelectedValue = _this.attr('data-condition-item');
    //         let selectedItem = $('.conditionItem').val();
    //         let indexOf = selectedItem.indexOf(unSelectedValue);
    //         if(indexOf !== 1) {
    //             selectedItem.splice(selectedItem, indexOf);
    //         }
    //     });
    // }

    HT.promotionMultipleSelect2 = (object) => {
        $('.multipleSelect2').select2({
            // minimumInputLength: 2,
            placeholder: 'Click vào để chọn...',
            // ajax: {
            //     url: 'ajax/attribute/getAttribute',
            //     type: 'GET',
            //     dataType: 'json',
            //     deley: 250,
            //     data: function (params){
            //         return {
            //             search: params.term,
            //             option: option,
            //         }
            //     },
            //     processResults: function(data){
            //         return {
            //             results: data.items
            //         }
            //     },
            //     cache: true
              
            //   }
        });
    }

    HT.btnJs100 = () => {
        $(document).on('click', '.btn-js-100', function(){
            let trLastChild = $('.order_amount_range').find('tbody tr:last-child');
            let newTo = parseInt(trLastChild.find('.order_amount_range_to input').val().replace(/\./g, ''));

            let $tr = $('<tr>')
            let tdList = [
                { 
                    class: 'order_amount_range_from td-range', 
                    name: 'promotion_order_amount_range[amountFrom][]', 
                    value: addCommas(parseInt(newTo) + 1) },
                { 
                    class: 'order_amount_range_to td-range', 
                    name: 'promotion_order_amount_range[amountTo][]', 
                    value: 0 },
            ];

            for(let i = 0; i < tdList.length; i++) {
                let $td = $('<td>', { class: tdList[i].class });
                let $input = $('<input>')
                                .addClass('form-control int')
                                .attr('name', tdList[i].name)
                                .val(tdList[i].value);

                $td.append($input);
                $tr.append($td);
            }

            let $discountTd = $('<td>').addClass('discountType');
            $discountTd.append(
                $('<div>', { class: 'uk-flex uk-flex-middle' }).append(
                    $('<input>', { type: 'text', name: 'promotion_order_amount_range[amountValue][]', class: 'form-control int', placeholder: 0, value: 0 })
                ).append(
                    $('<select>', { class: 'multipleSelect2'})
                        .attr('name', 'promotion_order_amount_range[amountType][]')
                        .append($('<option>', { value: 'cash', text: 'đ'}))
                        .append($('<option>', { value: 'percent', text: '%'}))
                )
            );

            $tr.append($discountTd);
            let deleteButton = $('<td>').append(
                $('<div>', { 
                    class: 'delete-order-amount-range-condition delete-some-item'
                }).append(`<svg data-icon="TrashSolidLarge" aria-hidden="true" focusable="false" width="15" height="16" viewBox="0 0 15 16" class="bem-Svg" style="display: block;">
                            <path fill="currentColor" d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H1v2h13V2h-1z"></path>
                        </svg>`)
            );

            $tr.append(deleteButton);
            $('.order_amount_range table tbody').append($tr);
            HT.promotionMultipleSelect2();
        });
    }

    HT.deletedAmountRangeCondition = () => {
        $(document).on('click', '.delete-order-amount-range-condition', function(){
            let _this = $(this);
            _this.parents('tr').remove()
        })
    }

    HT.renderOrderRangeConditionContainer = () => {
        $(document).on('change', '.promotionMethod', function(){
            let _this = $(this);
            let option = _this.val();
            switch (option) {
                case "order_amount_range":
                    HT.renderOrderAmountRange();
                    break;
                case "product_and_quantity":
                    HT.renderProductAndQuantity();
                    break;
                case "product_quantity_range":
                    console.log("product_quantity_range");
                    break;
                case "goods_discount_by_quantity":
                    console.log("goods_discount_by_quantity");
                    break;
                default:
                    HT.removePromotionContainer();
                    break;
            }
        });

        let method = $('.preload_promotionMethod').val();
        if(method.length && typeof method !== 'undefined'){
            $('.promotionMethod').val(method).trigger('change');
        }
    }

    HT.removePromotionContainer = () => {
        $('.promotion-container').html('');
    }

    HT.renderOrderAmountRange = () => {
        let $tr = '';
        let order_amount_range = JSON.parse($('.input_order_amount_range').val()) || { 
            amountFrom: ['0'], 
            amountTo: ['0'], 
            amountValue: ['0'], 
            amountType: ['cash'], 
        };

        for(let i = 0; i < order_amount_range.amountFrom.length; i++) {
            let $amountFrom = order_amount_range.amountFrom[i];
            let $amountTo = order_amount_range.amountTo[i];
            let $amountValue = order_amount_range.amountValue[i];
            let $amountType = order_amount_range.amountType[i];
            $tr += `<tr>
                <td class="order_amount_range_from td-range">
                    <input type="text"
                        class="form-control int"
                        name="promotion_order_amount_range[amountFrom][]"
                        placeholder="0"
                        value="${$amountFrom}">
                </td>
                <td class="order_amount_range_to td-range">
                    <input type="text"
                        class="form-control int"
                        name="promotion_order_amount_range[amountTo][]"
                        placeholder="0"
                        value="${$amountTo}">
                </td>
                <td class="discountType">
                    <div class="uk-flex uk-flex-middle">
                        <input type="text"
                            class="form-control int"
                            name="promotion_order_amount_range[amountValue][]"
                            placeholder="0"
                            value="${$amountValue}">
                        <select name="promotion_order_amount_range[amountType][]" class="multipleSelect2">
                            <option value="cash" ${ ($amountType == 'cash') ? 'selected' : ''}>đ</option>
                            <option value="percent" ${ ($amountType == 'percent') ? 'selected' : ''}>%</option>
                        </select>
                    </div>
                </td>
                <td>
                    <div class="delete-order-amount-range-condition delete-some-item">
                        <svg data-icon="TrashSolidLarge" aria-hidden="true" focusable="false" width="15" height="16" viewBox="0 0 15 16" class="bem-Svg" style="display: block;">
                            <path fill="currentColor" d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H1v2h13V2h-1z"></path>
                        </svg>
                    </div>
                </td>
            </tr>`
        }
        
        let html = `<div class="order_amount_range">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-right">Giá trị từ</th>
                        <th class="text-right">Giá trị đến</th>
                        <th class="text-right">Chiết khấu (%)</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    ${$tr}
                </tbody>
            </table>
            <button class="btn btn-success btn-custom btn-js-100" type="button">
                Thêm điều kiện
            </button>
        </div>`;

        HT.renderPromotionContainer(html);
    }

    HT.renderProductAndQuantity = () => {
        let selectData = JSON.parse($('.input-product-and-quantity').val());
        let selectHtml = '';
        let moduleType = $('.preload_select-product-and-quantity').val();

        for(let key in selectData) {
            selectHtml += '<option '+ ((moduleType.length && typeof moduleType !== 'undefined' && moduleType == key) ? 'selected' : '') +' value="'+key+'">'+selectData[key]+'</option>'
        }

        let preloadData = JSON.parse($('.input_product_and_quantity').val()) || { 
            quantity: ['1'], 
            maxDiscountValue: ['0'], 
            discountValue: ['0'], 
            discountType: ['cash'], 
        };

        let html = `<div class="product_and_quantity">
            <div class="choose-module mt20">
                <div class="fix-label" style="color: blue;">Sản phẩm áp dụng</div>
                <select name="module_type" class="select-product-and-quantity multipleSelect2">
                    ${selectHtml}
                </select>
            </div>
            <table class="table table-striped mt20">
                <thead>
                    <tr>
                        <th class="text-right" style="width: 400px">Sản phẩm mua</th>
                        <th class="text-right" style="width: 80px">SL tối thiểu</th>
                        <th class="text-right">Giới hạn KM</th>
                        <th class="text-right" style="width: 150px">Chiết khấu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="chooseProductPromotionTd">
                            <div class="product-quantity" data-toggle="modal" data-target="#findProduct">
                                <div class="boxWrapper">
                                    <div class="boxSearchIcon">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <div class="boxSearchInput">
                                        <p>Tìm theo tên, mã sản phẩm</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text"
                                class="form-control int"
                                name="product_and_quantity[quantity]"
                                value="${ preloadData.quantity }">
                        </td>
                        <td class="order_amount_range_to td-range">
                            <input type="text"
                                class="form-control int"
                                name="product_and_quantity[maxDiscountValue]"
                                placeholder="0"
                                value="${ preloadData.maxDiscountValue }">
                        </td>
                        <td class="discountType">
                            <div class="uk-flex uk-flex-middle">
                                <input type="text"
                                    class="form-control int"
                                    name="product_and_quantity[discountValue]"
                                    placeholder="0"
                                    value="${ preloadData.discountValue }">
                                <select name="product_and_quantity[discountType]" class="multipleSelect2">
                                    <option value="cash" ${ (preloadData.discountType == 'cash') ? 'selected' : ''}>đ</option>
                                    <option value="percent" ${ (preloadData.discountType == 'percent') ? 'selected' : ''}>%</option>
                                </select>
                            </div>
                        <td>
                    </tr>
                </tbody>
            </table>
        </div>`

        HT.renderPromotionContainer(html);
    }

    HT.renderPromotionContainer = (html) => {
        $('.promotion-container').html(html);
        HT.promotionMultipleSelect2();
    }

    HT.loadProduct = (option) => {
        $.ajax({
            url: 'ajax/product/loadProductPromotion',
            type: 'GET',
            data: option,
            dataType: 'json',
            success: function(res) {
                HT.fillToObjectList(res);
            },
        });
    }

    HT.getPaginationPromotion = () => {
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            let _this = $(this);
            let option = {
                'model' : $('.select-product-and-quantity').val(),
                page: _this.text(),
                keyword: $('search-model').val()
            }
            HT.loadProduct(option);
        })
    }

    HT.productQuantityFindProduct = () => {
        $(document).on('click', '.product-quantity', function(e){
            e.preventDefault();
            let option = {
                'model' : $('.select-product-and-quantity').val()
            }
            HT.loadProduct(option);
        });
    }

    HT.fillToObjectList = (data) => {
        switch (data.model) {
            case "Product":
                HT.fillProductToList(data.objects);
                break;
            case "ProductCatalogue":
                HT.fillProductCatalogueToList(data.objects);
                break;
        }
    }

    HT.fillProductCatalogueToList = (object) => {
        let html = '';
        if(object.data.length) {
            let model = $('.select-product-and-quantity').val();
            for(let i = 0; i < object.data.length; i++) {
                let name = object.data[i].name;
                let id = object.data[i].id;
                let classBox = model + '_' + id;
                let isChecked = ($('.boxWrapper .'+classBox+'').length ? true : false )
            
                html += `<div class="search-object-item" data-product-id="${id}" data-name="${name}">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="object-info">
                            <div class="uk-flex uk-flex-middle">
                                <input type="checkbox" 
                                    class="input-checkbox" 
                                    value="${id}"
                                    ${ (isChecked) ? 'checked' : ''}>
                                <div class="object-name">
                                    <div class="name" style="margin: 0 0 0 5px">${name}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }
        }

        html = html + HT.paginationLinks(object.links).prop('outerHTML');
        $('.search-list').html(html);
    }

    HT.fillProductToList = (object) => {
        let html = '';
        if(object.data.length) {
            let model = $('.select-product-and-quantity').val();
            for(let i = 0; i < object.data.length; i++) {
                let image = object.data[i].image;
                let name = object.data[i].variant_name;
                let product_variant_id = object.data[i].product_variant_id;
                let product_id = object.data[i].id;
                let inventory = (typeof object.data.inventory != 'undefined') ? object.data.inventory : 0;
                let couldSall = (typeof object.data.couldSall != 'undefined') ? object.data.couldSall : 0;
                let sku = object.data[i].sku;
                let price = object.data[i].price;
                let classBox = model + '_' + product_id + '_' + product_variant_id;
                let isChecked = ($('.boxWrapper .'+classBox+'').length ? true : false );
                let uuid = object.data[i].uuid;
            
                html += `<div class="search-object-item" data-product-id="${product_id}" 
                        data-variant-id="${product_variant_id}" data-name="${name}" data-uuid="${uuid}">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="object-info">
                            <div class="uk-flex uk-flex-middle">
                                <input type="checkbox" 
                                    class="input-checkbox" 
                                    value="${product_id+'_'+product_variant_id}"
                                    ${ (isChecked) ? 'checked' : ''}>
                                <span class="image img-scaledown">
                                    <img src="${image}" alt="">
                                </span>
                                <div class="object-name">
                                    <div class="m0">${name}</div>
                                    <div class="jscode">Mã SP: ${sku}</div>
                                </div>
                            </div>
                        </div>
                        <div class="object-extra-info">
                            <div class="price">${addCommas(price)}</div>
                            <div class="object-inventory">
                                <div class="uk-flex uk-flex-middle">
                                    <span class="text-1">Tồn kho: </span>
                                    <span class="text-value">${inventory}</span>
                                    <span class="text-1 slash">|</span>
                                    <span class="text-value">Có thể bán: ${couldSall}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }
        }

        html = html + HT.paginationLinks(object.links).prop('outerHTML');
        $('.search-list').html(html);
    }

    HT.changePromotionMethod = () => {
        $(document).on('change', '.select-product-and-quantity', function(){
            $('.fixGrid6').remove();
            objectChoose = [];
        });
    }

    HT.paginationLinks = (links) => {
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

    HT.searchObject = () => {
        $(document).on('keyup', '.search-model', function(e) {
            e.preventDefault();
            let _this = $(this);
            let keyword = _this.val();
            let option = {
                model : $('.select-product-and-quantity').val(),
                keyword: keyword
            }
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                HT.loadProduct(option);
            }, doneTypingInterval);
        })
    }

    var objectChoose = [];
    HT.chooseProuctPromtion = () => {
        $(document).on('click', '.search-object-item', function(e) {
            e.preventDefault();
            let _this = $(this);
            let isChecked = _this.find('input[type=checkbox]').prop('checked');
            let objectItem = {
                product_id: _this.attr('data-product-id'),
                product_variant_id: _this.attr('data-variant-id'),
                name: _this.attr('data-name'),
                uuid: _this.attr('data-uuid')
            }

            if(isChecked) {
                objectChoose = objectChoose.filter(item => item.product_id !== objectItem.product_id)
                _this.find('input[type=checkbox]').prop('checked', false);
            } else {
                objectChoose.push(objectItem);
                _this.find('input[type=checkbox]').prop('checked', true);
            }
            
        });
    }

    HT.confirmProductPromotion = () => {
        let preloadObject = JSON.parse($('.input_object').val()) || { 
            id: [], 
            product_variant_id: [],
            name: [],
            uuid: [],
        };

        let objectArray = preloadObject.id.map((id, index) => ({
            product_id: id,
            product_variant_id: preloadObject.product_variant_id[index] || 'null',
            name: preloadObject.name[index],
            uuid: preloadObject.uuid[index] || 'null',
        }));

        if(objectArray.length && typeof objectArray !== 'undefined') {
            let preloadHtml = HT.renderBoxWrapper(objectArray);
            HT.checkFixGrid(preloadHtml);
        }

        $(document).on('click', '.confirm-product-promotion', function() {
            // e.preventDefault();
            let html = HT.renderBoxWrapper(objectChoose);
            HT.checkFixGrid(html);
        });
    }
    
    HT.renderBoxWrapper = (objectData) => {
        let html = '';
        let model = $('.select-product-and-quantity').val();
        if(objectData.length) {
            for(let i = 0; i < objectData.length; i++) {
                let { product_id, product_variant_id, name, uuid } = objectData[i];
                let classBox = `${model}_${product_id}_${product_variant_id}`;
                if(!$(`.boxWrapper .${classBox}`).length) {
                    html += `<div class="fixGrid6 ${classBox}">
                        <div class="goods-item">
                            <a class="goods-item-name" title="${name}">${name}</a>
                            <button class="delete-goods-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                </svg>
                            </button>
                            <div class="hidden">
                                <input name="object[id][]" value="${product_id}">
                                <input name="object[product_variant_id][]" value="${product_variant_id}">
                                <input name="object[variant_uuid][]" value="${uuid}">
                                <input name="object[name][]" value="${name}">
                            </div>
                        </div>
                    </div>`;
                }
            }
        }
        return html;
    }

    HT.checkFixGrid = (html) => {
        if($('.fixGrid6').elExist) {
            $('.boxSearchIcon').remove();
            $('.boxWrapper').prepend(html);
        }else{
            $('.fixGrid6').remove();
            $('.boxWrapper').prepend(HT.boxSearchIcon());
        }
    }

    HT.boxSearchIcon = () => {
        return `<div class="boxSearchInput">
            <i class="fa fa-search"></i>
        </div>`;
    }

    HT.deleteGoodsItem = () => {
        $(document).on('click', '.delete-goods-item', function(e){
            e.stopPropagation();
            let _button = $(this);
            _button.parents('.fixGrid6').remove();
            HT.checkFixGrid();
        });
    }

    HT.checkConditionItem = () => {
        let checkedValue = $('.conditionItemSelected').val();
        if(checkedValue.length && $('.conditionItem').length) {
            checkedValue = JSON.parse(checkedValue);
            $('.conditionItem').val(checkedValue).trigger('change');
        }
    }

	$(document).ready(function(){
        HT.promotionNeverEnd();
        HT.promotionSource();
        HT.promotionMultipleSelect2();
        HT.chooseCustomerCondition();
        HT.chooseApplyItem();
        // HT.deleteCondition();
        HT.btnJs100();
        HT.deletedAmountRangeCondition();
        HT.renderOrderRangeConditionContainer();
        HT.productQuantityFindProduct();
        HT.getPaginationPromotion();
        HT.searchObject();
        HT.chooseProuctPromtion();
        HT.confirmProductPromotion();
        HT.deleteGoodsItem();
        HT.changePromotionMethod();
        HT.checkConditionItem();
	});
})(jQuery);
