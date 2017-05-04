(function($) {
    "use strict";

    CloudZoom.quickStart();

    // Initialize the slider.
    $('.rt-woocommerce-product-gallery-nav').Thumbelina({
    	orientation:'vertical',
    	maxSpeed: 88,
        $bwdBut:$('.rt-woocommerce-product-gallery-nav .left'),
        $fwdBut:$('.rt-woocommerce-product-gallery-nav .right')
    });

    var thumb_height = $('.rt-woocommerce-product-thumbnail').innerHeight();

    $('.rt-woocommerce-product-gallery-nav').css({
        height: thumb_height,
    });

    $(window).resize(function(e) {
    	var thumb_height = $('.rt-woocommerce-product-thumbnail').innerHeight();

		$('.rt-woocommerce-product-gallery-nav').css({
            height: thumb_height,
        });
    });

})(jQuery);
