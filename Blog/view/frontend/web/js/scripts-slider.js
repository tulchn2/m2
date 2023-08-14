define(['jquery', 'owlcarousel'], function($) {
    $(document).ready(function() {
        $('.blog-slider').owlCarousel({
            loop: true,
            margin: 10,
            nav: false,
            dots: false,
            autoplay: true,
            autoplayHoverPause: true,
            items: 1,
            autoHeight:true,
        });
    });
});