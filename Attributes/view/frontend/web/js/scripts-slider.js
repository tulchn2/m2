define(['jquery', 'owlcarousel'], function($) {
    $(document).ready(function() {
        $('.product-slider').owlCarousel({
            loop: true,
            margin: 10,
            autoplay: true,
            autoplayHoverPause: true,
            items: 3,
            autoHeight:true,
            lazyLoad:true,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:2,
                    nav:false
                },
                1000:{
                    items:4,
                    nav:true,
                    loop:false
                }
            }
        });
    });
});