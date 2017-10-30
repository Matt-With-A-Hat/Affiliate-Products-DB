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
        $tabs = $(this).closest('.tabbing-box').find('.tablink');
        $tabcontent = $(this).closest('.tabbing-box').find('.tab-content');
        $(this).addClass('active');

        id = $(this).data('toggle');
        $activetab = $(this).closest('.tabbing-box').find('#' + id);
        $tabs.not($(this)).removeClass('active');
        $tabcontent.removeClass('active');
        $activetab.addClass('active');
    });
});