jQuery(document).ready(function ($) {
    /**
     * =Global variables
     */
    $selects = $('.apd-select');
    $sliders = $('.apd-slider');
    $checkboxes = $('.apd-checkbox');


    $("#AmazonPriceInteger").slider({
        id: "AmazonPriceInteger",
        min: 0,
        max: 5000,
        range: true,
        value: [0, 5000],
    });
    $("#OverallRatingPP").slider({
        id: "OverallRatingPP",
        min: 0,
        max: 100,
        range: true,
        value: [0, 100],
    });
    $("#SurfaceArea").slider({
        id: "SurfaceArea",
        min: 0,
        max: 6000,
        range: true,
        value: [0, 6000],
    });
    $("#MaxRaising").slider({
        id: "MaxRaising",
        min: 0,
        max: 70,
        range: true,
        value: [0, 70],
    });
    $("#LoudnessDb").slider({
        id: "LoudnessDb",
        min: 0,
        max: 100,
        range: true,
        value: [0, 100],
    });

    /**
     * =Functions
     */
    function getSelectValues() {
        var selects = {};
        $selects.each(function (index, element) {
            var id = $(this).attr('id');
            selects[id] = $(element).val();
        });
        return selects;
    }

    function getCheckboxValues() {
        var checkboxes = {};
        $checkboxes.each(function () {
            var id = $(this).attr('id');
            checkboxes[id] = $(this)[0].checked;
        });
        return checkboxes;
    }

    function getSliderValues() {
        var sliders = {};
        $sliders.each(function () {
            var id = $(this).attr('id');
            sliders[id] = $(this).slider('getValue');
        });
        return sliders;
    }

    function storeValuesInJson() {
        var selectValues = getSelectValues();
        var sliderValues = getSliderValues();
        var checkboxValues = getCheckboxValues();
        values = $.extend(selectValues, sliderValues, checkboxValues);
        sessionStorage.setItem('automowersFilter', JSON.stringify(values));
    }

    storeValuesInJson();

    /**
     * =Store data in local storage
     */
    $selects.add($sliders).add($checkboxes).change(function () {
        storeValuesInJson();
    });
});