(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;

	HT.niceSelect = () => {
		if($('.nice-select').length){
			$('.nice-select').niceSelect();
		}
		
	}

	HT.productSwiperSlide = () => {
		var swiper = new Swiper(".product-gallery .swiper-container", {
			loop: true,
			autoplay: {
				delay: 2000,
				disableOnInteraction: false
			},
			pagination: {
				el: '.swiper-pagination'
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			thumbs: {
				swiper: {
					el: '.product-swiper-container-thumbs',
					slidesPerView: 4.75,
					spaceBetween: 10,
					// centeredSlides: true,
					slideToClickedSlide: true
				}
			}
		});
	}

	HT.selectVariantProduct = () => {
		if($('.choose-attribute').length) {
			$(document).on('click', '.choose-attribute', function(e) {
				e.preventDefault();
				let _this = $(this);
				let attribute_name = _this.text();
				_this.parents('.attribute-item').find('span').html(attribute_name);
				_this.parents('.attribute-value').find('.choose-attribute').removeClass('active');
				_this.addClass('active');

				HT.handleAttribute();
			});
		}
	}

	HT.handleAttribute = () => {
		let attribute_id = [];
		let flag = true;
		$('.attribute-value .choose-attribute').each(function() {
			let _this = $(this);
			if(_this.hasClass('active')) {
				attribute_id.push(_this.attr('data-attributeid'));
			}
		});

		$('.attribute').each(function() {
			if($(this).find('.choose-attribute.active').length === 0) {
				flag = false;

				return false;
			}
		});

		if(flag) {
			$.ajax({
				url: 'ajax/product/loadVariant',
				type: 'GET',
				data: {
					'attribute_id': attribute_id,
					'product_id': $('input[name="product_id"]').val(),
					'language_id': $('input[name="language_id"]').val(),
				},
				dataType: 'json',
				beforeSend: function() {

				},
				success: function(res) {
					HT.setupVariantPrice(res);
					HT.setupVariantName(res);
					HT.setupVariantGallery(res);
					HT.setupVariantUrl(res, attribute_id);
				},
			})
		}
	}

	HT.setupVariantUrl = (res, attribute_id) => {
		let queryString = '?attribute_id=' + attribute_id.join(',');
		let productCanonical = $('.productCanonical').val();
		productCanonical = productCanonical + queryString;

		let stateObject = { attribute_id: attribute_id };
		history.pushState(stateObject, "Page Title", productCanonical);
	}

	HT.setupVariantPrice = (res) => {
		$('.popup-product .price').html(res.variantPrice.html);
	}

	HT.setupVariantName = (res) => {
		let productName = $('.productName').val();
		let productVariantName = productName + ' ' + res.variant.languages[0].pivot.name;
		$('.product-main-title span').html(productVariantName);
	}

	HT.setupVariantGallery = (res) => {
		let gallery = res.variant.album.split(',');
		let html = `<div class="swiper-container">
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-wrapper big-pic">`
			gallery.forEach((val) => {
				html += `<div class="swiper-slide" data-swiper-autoplay="2000">
                    <a href="${val}" class="image img-cover"><img src="${val}" alt="${val}"></a>
                </div>`
			})
			html += `</div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="swiper-container-thumbs">
            <div class="swiper-wrapper pic-list">`
			gallery.forEach((val) => {
				html += `<div class="swiper-slide">
                    <span class="image img-cover"><img src="${val}" alt="${val}"></span>
                </div>`
			})
        html += `</div>
        </div>`

		if(gallery.length) {
			$('.popup-gallery').html(html);
			HT.popupSwiperSlide();
		}
	}

	HT.loadProductVariant = () => {
		let attributeCatalogue = JSON.parse($('.attributeCatalogue').val());
		if(typeof attributeCatalogue != 'undefined' && attributeCatalogue.length) {
			HT.handleAttribute();
		}
	}

	HT.changeQuantity = () => {
		
		$(document).on('click','.quantity-button', function(){
			let _this = $(this)
			let quantity = $('.quantity-text').val()
			let newQuantity = 0
			if(_this.hasClass('minus')){
				 newQuantity =  quantity - 1
			}else{
				 newQuantity = parseInt(quantity) + 1
			}
			if(newQuantity < 1){
				newQuantity = 1
			}
			$('.quantity-text').val(newQuantity)
		})

	}

	$(document).ready(function(){
		/* CORE JS */
		HT.niceSelect();
		HT.productSwiperSlide();
		HT.changeQuantity();
		HT.selectVariantProduct();
		HT.loadProductVariant();
	});

})(jQuery);



addCommas = (nStr) => { 
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