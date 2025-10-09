(function($) {
   "use strict";
   var HT = {}; // Khai báo là 1 đối tượng
   var typingTimer = null;
   var _token = $('meta[name="csrf_token"]').attr('content');

   /* MAIN VARIABLE */

   var $window = $(window),
      $document = $(document);

   // FUNCTION DECLARGE
   $.fn.elExists = function() {
     return this.length > 0;
   };

   HT.addCart = () => {
      $(document).on('click', '.addToCart', function(e){
         e.preventDefault()
         let _this = $(this)
         let id = _this.attr('data-id')
         
         let quantity = $('.quantity-text').val()
         if(typeof quantity === 'undefined'){
            quantity = 1
         }

         let attribute_id = [];
         $('.attribute-value .choose-attribute').each(function() {
            let _this = $(this);
            if(_this.hasClass('active')) {
               attribute_id.push(_this.attr('data-attributeid'));
            }
         });

         let option = {
            id: id,
            quantity: quantity,
            attribute_id: attribute_id,
            _token: _token,
         }

         $.ajax({
				url: 'ajax/cart/create',
				type: 'POST',
				data: option,
				dataType: 'json',
				beforeSend: function() {

				},
				success: function(res) {
               toastr.clear()
					if(res.code === 10) {
                  toastr.success(res.messages, 'Thông báo từ hệ thống!')
               } else {
                  toastr.error('Có vấn đề xảy ra! Hãy thử lại')
               }
				},
			})
      });
   }

   HT.changeQuantity = () => {
      $(document).on('click', '.btn-qty', function() {
         let _this = $(this)
         let qtyElement = _this.siblings('.input-qty')
         let qty = qtyElement.val()
         let newQty = (_this.hasClass('minus')) ? parseInt(qty) - 1 : parseInt(qty) + 1
         newQty = (newQty < 1) ? 1 : newQty
         qtyElement.val(newQty)

         clearTimeout(typingTimer);
         typingTimer = setTimeout(() => {
            let option = {
               qty: newQty,
               rowId: _this.siblings('.rowId').val(),
               _token: _token
            }
   
            HT.handleUpdateCart(_this, option)
         }, 1000)
      })
   }

   HT.changeQuantityInput = () => {
      $(document).on('change', '.input-qty', function() {
         let _this = $(this)
         let val = _this.val().trim();
         clearTimeout(typingTimer);
         typingTimer = setTimeout(() => {
            if(!/^\d+$/.test(val)){
               toastr.error('Số lượng nhập không chính xác', 'Thông báo từ hệ thống!')
               return false
            }

            let option = {
               qty: parseInt(val),
               rowId: _this.siblings('.rowId').val(),
               _token: _token
            }

            HT.handleUpdateCart(_this, option)
         }, 1000)
      })
   }

   HT.handleUpdateCart = (_this, option) => {
      $.ajax({
         url: 'ajax/cart/update',
         type: 'POST',
         data: option,
         dataType: 'json',
         beforeSend: function() {
   
         },
         success: function(res) {
            toastr.clear()
            if(res.code === 10) {
               HT.changeMinyCartQuantity(res)
               HT.changeMinyQuantityItem(_this, option)
               HT.changeCartItemSubTotal(_this, res)
               HT.changeCartTotal(res)
               toastr.success(res.messages, 'Thông báo từ hệ thống!')
            } else {
               toastr.error('Có vấn đề xảy ra! Hãy thử lại')
            }
         },
      })
   }

   HT.changeMinyQuantityItem = (item, option) => {
      item.parents('.cart-item').find('.cart-item-number').html(option.qty)
   }

   HT.changeCartItemSubTotal = (item, res) => {
      item.parents('.cart-item-info').find('.cart-price-sale').html(addCommas(res.response.cartItemSubTotal) + ' đ')
   }

   HT.changeMinyCartQuantity = (res) => {    
      $('#cartTotalItem').html(res.response.cartTotalItems)
   }

   HT.changeCartTotal = (res) => {
      $('.cart-total').html(addCommas(res.response.cartTotal) + ' đ')
      $('.discount-value').html('-' + addCommas(res.response.cartDiscount) + ' đ')
   }

   HT.removeCartItem = () => {
      $(document).on('click', '.cart-item-remove', function() {
         let _this = $(this)
         let option = {
            rowId: _this.attr('data-row-id'),
            _token: _token
         }

         $.ajax({
            url: 'ajax/cart/delete',
            type: 'POST',
            data: option,
            dataType: 'json',
            beforeSend: function() {
      
            },
            success: function(res) {
               toastr.clear()
               if(res.code === 10) {
                  HT.changeMinyCartQuantity(res)
                  HT.changeCartTotal(res)
                  HT.removeCartItemRow(_this)
                  toastr.success(res.messages, 'Thông báo từ hệ thống!')
               } else {
                  toastr.error('Có vấn đề xảy ra! Hãy thử lại')
               }
            },
         })
      })
   }

   HT.removeCartItemRow = (_this) => {
      _this.parents('.cart-item').remove()
   }

   HT.select2 = () => {
      if($('.setupSelect2').length){
         $('.setupSelect2').select2();
      }
   };

   // Document ready functions
   $document.ready(function() {
      HT.addCart();
      HT.changeQuantity();
      HT.changeQuantityInput();
      HT.removeCartItem()
      HT.select2();
   });

})(jQuery);
