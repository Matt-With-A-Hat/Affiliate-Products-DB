jQuery(document).ready(function () {

    jQuery('#csv-file').change(function () {

        var filename = jQuery('input[type=file]').val().split('\\').pop();

        filename = filename.split(".")[0].toLowerCase();

        jQuery('#table-name').val(filename);
        console.log(filename);
    });
});