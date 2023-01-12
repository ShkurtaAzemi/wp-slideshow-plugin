// media library script
(function ($) {
    $(document).ready(function () {
       let  sliderThumbnail = new Swiper('.slider-thumbnail', {
            slidesPerView: 4,
            freeMode: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,

        });


        var slider = new Swiper('.slider', {
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            thumbs: {
                swiper: sliderThumbnail
            }
        });
    });
}(jQuery));