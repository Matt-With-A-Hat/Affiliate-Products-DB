jQuery(document).ready(function ($) {

    $('#csv-file').change(function () {

        var filename = $('input[type=file]').val().split('\\').pop();

        filename = filename.split(".")[0].toLowerCase();

        $('#table-name').val(filename);
        console.log(filename);
    });

    $('#product-tables-selection').change(function () {
        console.log("IN");
        hideAndShow();
    });

    /**
     * Display/hide post generation form elements
     */
    function hideAndShow() {
        $productSelector = $('#product-tables-selection');
        var val = $productSelector.val();
        $theColumnBox = $('#' + val + '-column-box');
        $theProductBox = $('#' + val + '-product-box');
        $columnBoxes = $('.column-box');
        $productBoxes = $('.product-box');

        $columnBoxes.not($theColumnBox).addClass('display-none');
        $productBoxes.not($theProductBox).addClass('display-none');
        $theColumnBox.removeClass('display-none').addClass('display-block');
        $theProductBox.removeClass('display-none').addClass('display-block');
    }

    $('#single-post').click(function () {
        var checked = this.checked;
        $productLabel = $(this).closest('.form-group').find('label.product-selector');
        $productSelector = $(this).closest('.form-group').find('select.product-selector');
        console.log($productSelector);
        if (checked) {
            console.log("It's not checked");
            $productLabel.removeClass('disabled');
            if ($productSelector) {
                $productSelector.removeAttr('disabled');
            }
        } else {
            console.log("It's checked");
            $productLabel.addClass('disabled');
            if ($productSelector) {
                $productSelector.attr('disabled', 'disabled');
            }
        }
    })
});