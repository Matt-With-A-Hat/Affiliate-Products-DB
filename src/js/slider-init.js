jQuery(document).ready(function ($) {
    /**
     * =Global variables
     */
    gProductData = null;
    gItems = null;
    $selects = $('.apd-select');
    $sliders = $('.apd-slider');
    $checkboxes = $('.apd-checkbox');

    var filterValuesJson = sessionStorage.getItem('automowersFilter');
    var filterValues = JSON.parse(filterValuesJson);

    /**
     * =Slider initialization
     */

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
    $("#RunTime").slider({
        id: "RunTime",
        min: 0,
        max: 300,
        range: true,
        value: [0, 300],
    });
    $("#ChargingTimeMin").slider({
        id: "ChargingTimeMin",
        min: 0,
        max: 300,
        range: true,
        value: [0, 300],
    });

    /**
     * --------------------------------------------------------------
     * =Execute on page load
     * --------------------------------------------------------------
     * */
    if (filterValues !== null) {
        setSelectValues();
        setSliderValues();
        setCheckboxValues();
        updateVisibility(getHiddenItems());
        setTimeout(function () {
            $("#filter-toggle")[0].click();
        }, 10);
        setTimeout(function () {
            $(".toggle-rotate").children("i").toggleClass("down");
        }, 200);
    }

    /**
     * --------------------------------------------------------------
     * =Functions
     * --------------------------------------------------------------
     */
    function getProductData() {
        if (gProductData === null) {
            var productData = {};
            $('.apd-data').each(function () {
                var asin = $(this).data('id');
                var product = {};
                $('#' + asin + ' .apd-data .product-data').each(function (index) {
                    field = $(this).data('field');
                    value = $(this).val();
                    product[field] = value;
                });
                productData[asin] = product;
            });
            gProductData = productData;

            return productData;
        } else {
            //return global
            return gProductData;
        }
    }

    function getItems() {
        if (gItems === null) {
            var productData = getProductData();
            var items = {};
            for (asin in productData) {
                items[asin] = $('.' + asin);
            }
            gItems = items;

            return items;
        } else {
            return gItems;
        }
    }

    function getSelectValues() {
        var selects = {};
        $selects.each(function (index, element) {
            var id = $(this).attr('id');
            selects[id] = $(element).val();
        });
        return selects;
    }

    function setSelectValues() {
        $selects.each(function (index, element) {
            var id = $(element).attr('id');
            if (id in filterValues) {
                var filterVal = filterValues[id];
                $(element).val(filterVal);
            }
        })
    }

    function getCheckboxValues() {
        var checkboxes = {};
        $checkboxes.each(function () {
            var id = $(this).attr('id');
            checkboxes[id] = $(this)[0].checked;
        });
        return checkboxes;
    }

    function setCheckboxValues() {
        $checkboxes.each(function (index, element) {
            var id = $(element).attr('id');
            if (id in filterValues) {
                var filterVal = filterValues[id];
                if (filterVal === true) {
                    $(element).attr('checked', true)
                }
            }
        })
    }

    function getSliderValues() {
        var sliders = {};
        $sliders.each(function () {
            var id = $(this).attr('id');
            sliders[id] = $(this).slider('getValue');
        });
        return sliders;
    }

    function setSliderValues() {
        $sliders.each(function (index, element) {
            var id = $(this).attr('id');
            if (id in filterValues) {
                var filterVal = filterValues[id];
                $(this).slider('setValue', filterVal);
            }
        })
    }

    function updateVisibility(hiddenItems) {
        var items = getItems();
        for (var asin in items) {
            var item = items[asin];
            if (typeof hiddenItems[asin] === 'undefined') {
                item.fadeIn();
            } else {
                item.fadeOut();
            }
        }
        var countItems = 0;
        var countHiddenItems = 0;

        for (asin in items) {
            countItems++;
        }
        for (asin in hiddenItems) {
            countHiddenItems++;
        }

        var visibleItems = countItems - countHiddenItems;
        $('#filter-results').html(visibleItems);
    }

    /**
     * =FILTER MECHANICS
     */

    /**
     * =Filter selects
     */
    function checkSelects() {
        var selectValues = getSelectValues();
        var productData = getProductData();
        var hiddenItems = {};

        for (var selectId in selectValues) {
            var selectValue = selectValues[selectId];

            for (var asin in productData) {
                var itemValue = productData[asin][selectId];

                if (selectValue != itemValue && selectValue != '') {
                    hiddenItems[asin] = $('.' + asin);
                }
            }
        }
        return hiddenItems;
    }

    /**
     * =Filter sliders
     */
    function checkSliders() {
        var sliderValues = getSliderValues();
        var productData = getProductData();
        var hiddenItems = {};

        for (var sliderId in sliderValues) {
            var sliderValue = sliderValues[sliderId];

            for (var asin in productData) {
                var itemValue = parseInt(productData[asin][sliderId]);

                if ((itemValue <= sliderValue[0]) || (itemValue >= sliderValue[1])) {
                    hiddenItems[asin] = $('.' + asin);
                }
            }
        }
        return hiddenItems;
    }

    /**
     * =Filter checkboxes
     */
    function checkCheckboxes() {
        var checkboxValues = getCheckboxValues();
        var productData = getProductData();
        var hiddenItems = {};

        for (var checkboxId in checkboxValues) {
            var checked = checkboxValues[checkboxId];

            for (var asin in productData) {
                var itemValue = productData[asin][checkboxId];

                if (checked) {
                    if (itemValue == 0) {
                        hiddenItems[asin] = $('.' + asin);
                    }
                }
            }
        }
        return hiddenItems;
    }

    function getHiddenItems() {
        return hiddenItems = $.extend(checkCheckboxes(), checkSelects(), checkSliders());
    }

    /**
     * =Make stuff happen
     */
    $selects.add($sliders).add($checkboxes).change(function () {
        console.log("IN");
        var hiddenItems = getHiddenItems();
        console.log(hiddenItems);
        updateVisibility(hiddenItems);
    });
});