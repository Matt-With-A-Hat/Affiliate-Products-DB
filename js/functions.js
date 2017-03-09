jQuery(document).ready(function () {

    $('#csv-file').change(function () {

        var filename = $('input[type=file]').val().split('\\').pop();

        filename = filename.split(".")[0].toLowerCase();

        $('#table-name').val(filename);
        console.log(filename);

    });

});