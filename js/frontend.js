jQuery(document).ready(function ($) {
    //tabbing for details template
    var id, asin;
    $('.tablink.asin-link').click(function () {
        $(this).addClass('active');
        id = $(this).data('toggle');
        asin = id.split('-');
        asin = asin[asin.length - 1];
        $('.' + asin).not($(this)).removeClass('active');
        $('#' + id).addClass('active');
    });

    //tabbing general
    $('.tablink').click(function () {
        $tabs = $(this).closest('.products-box').find('.tablink');
        $tabcontent = $(this).closest('.products-box').find('.tab-content');
        console.log($tabcontent);

        $(this).addClass('active');
        id = $(this).data('toggle');
        $tabs.not($(this)).removeClass('active');
        $tabcontent.removeClass('active');
        $('#' + id).addClass('active');
    });
});