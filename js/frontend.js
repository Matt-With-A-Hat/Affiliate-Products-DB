jQuery(document).ready(function ($) {
    //tabbing for details template
    var id, asin;
    $(".tablinks").click(function () {
        $(this).addClass('active');
        id = $(this).data("toggle");
        asin = id.split("-");
        asin = asin[asin.length - 1];
        $('.' + asin).not($(this)).removeClass("active");
        $("#" + id).addClass('active');
    });
});