$('#bannerCarousel').on('slide.bs.carousel', function (e) {

    var $currentItem = $(e.relatedTarget);
    var $previousItem = $(this).find('.carousel-item.active');

    $previousItem.css('animation', '');
    $currentItem.css('animation', '');

    $previousItem.css('animation', 'slideOutToLeft 1s forwards');

    $currentItem.css('animation', 'slideInFromRight 1s forwards');
});
