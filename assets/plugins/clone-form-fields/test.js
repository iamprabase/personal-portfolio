
$(function () {
    $(document).on("click", ".btnAdd", function () {
        let currentRowNum = $(this).data("id");
        $('#brand_buy' + currentRowNum).select2("destroy");
        $('#category_buy' + currentRowNum).select2("destroy");
        $('#product_buy' + currentRowNum).select2("destroy");
        $('#unit_buy' + currentRowNum).select2("destroy");

        $('#brand_free' + currentRowNum).select2("destroy");
        $('#category_free' + currentRowNum).select2("destroy");
        $('#product_free' + currentRowNum).select2("destroy");
        $('#unit_free' + currentRowNum).select2("destroy");
        var num = $(".clonedInput").length, // Checks to see how many "duplicatable" input fields we currently have
            newNum = parseInt(num + 1), // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $("#entry" + currentRowNum)
                .clone()
                .attr("id", "entry" + newNum)
                .fadeIn("slow");
        $('#brand_buy' + currentRowNum).select2({
            'placeholder': 'Select Brand',
        });
        $('#product_buy' + currentRowNum).select2({
            'placeholder': 'Select a product',
        });
        $('#category_buy' + currentRowNum).select2({
            'placeholder': 'Select a Category',
        });
        $('#unit_buy' + currentRowNum).select2({
            'placeholder': 'Unit',
        });

        $('#brand_free' + currentRowNum).select2({
            'placeholder': 'Select Brand',
        });
        $('#product_free' + currentRowNum).select2({
            'placeholder': 'Select a product',
        });
        $('#category_free' + currentRowNum).select2({
            'placeholder': 'Select a Category',
        });
        $('#unit_free' + currentRowNum).select2({
            'placeholder': 'Unit',
        });

        // newElem
        //     .find(".heading-reference")
        //     .attr("id", "ID" + newNum + "_reference")
        //     .html("Type #" + newNum);


        // Brand - select
        newElem.find(".label_cb").attr("for", "brand_buy" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".brand_buy")
            .attr("id", "brand_buy" + newNum)
            .attr("name", "brand")
            .val("");

        //Category
        newElem.find(".label_cb").attr("for", "category_buy" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".category_buy")
            .attr("id", "category_buy" + newNum)
            .attr("name", "category")
            .val("");

        //product
        newElem.find(".label_pb").attr("for", "product_buy" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".product_buy")
            .attr("id", "product_buy" + newNum)
            .attr("name", "product_buy["+newNum+"]")
            .val("");

        newElem.find(".label_ub").attr("for", "unit_buy" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".unit_buy")
            .attr("id", "unit_buy" + newNum)
            .attr("name", "unit_buy["+newNum+"]")
            .val("");

        newElem.find(".label_pub").attr("for", "purchase_unit_buy" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".purchase_unit_buy")
            .attr("id", "purchase_unit_buy" + newNum)
            .attr("name", "purchase_unit_buy["+newNum+"]")
            .val("");


        newElem.find(".label_bf").attr("for", "brand_free" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".brand_free")
            .attr("id", "brand_free" + newNum)
            .attr("name", "brand")
            .val("");

        //Category
        newElem.find(".label_cf").attr("for", "category_free" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".category_free")
            .attr("id", "category_free" + newNum)
            .attr("name", "category")
            .val("");

        //product
        newElem.find(".label_pf").attr("for", "product_free" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".product_free")
            .attr("id", "product_free" + newNum)
            .attr("name", "product_free["+newNum+"]")
            .val("");

        newElem.find(".label_uf").attr("for", "unit_free" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".unit_free")
            .attr("id", "unit_free" + newNum)
            .attr("name", "unit_free["+newNum+"]")
            .val("");

        newElem.find(".label_puf").attr("for", "purchase_unit_free" + newNum);
        newElem.find(".btnAdd").removeAttr("id");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".purchase_unit_free")
            .attr("id", "purchase_unit_free" + newNum)
            .attr("name", "purchase_unit_free["+newNum+"]")
            .val("");


        $("div.clonedInput").last().after(newElem);
        $("#ID" + newNum + "_title").focus();
        $('#brand_buy' + newNum).select2({
            placeholder: "Select Brand",
            allowClear: true
        });
        $('#product_buy' + newNum).select2({
            'placeholder': 'Select a product',
        });
        $('#category_buy' + newNum).select2({
            'placeholder': 'Select a Category',
        });
        $('#unit_buy' + newNum).select2({
            'placeholder': 'Unit',
        });

        $("#category_buy" + newNum).on('change', function (e) {
            let brand = $('#brand_buy' + newNum).val();
            let category = $(this).val();
            if (category) {
                $.ajax({
                    type: "post",
                    data: {
                        category: category,
                        brand: brand
                    },
                    url: route,
                    success: function (res) {
                        var $select = $('#product_buy' + newNum);

                        $select.find('option').remove();
                        $.each(res, function (key, value) {
                            $select.append('<option value="' + key + '">' + value + '</option>');
                        });

                    }
                });
            }

        });

        $("#brand_buy" + newNum).on('change', function (e) {

            let category = $('#category_buy' + newNum).val();
            let brand = $(this).val();
            if (brand) {
                $.ajax({
                    type: "post",
                    data: {
                        category: category,
                        brand: brand
                    },
                    url: route,
                    success: function (res) {
                        var $select = $('#product_buy' + newNum);
                        $select.find('option').remove();
                        $.each(res, function (key, value) {
                            $select.append('<option value="' + key + '">' + value + '</option>');
                        });

                    }
                });
            }
        });


        $('#brand_free' + newNum).select2({
            placeholder: "Select Brand",
            allowClear: true
        });
        $('#product_free' + newNum).select2({
            'placeholder': 'Select a product',
        });
        $('#category_free' + newNum).select2({
            'placeholder': 'Select a Category',
        });
        $('#unit_free' + newNum).select2({
            'placeholder': 'Unit',
        });

        $("#category_free" + newNum).on('change', function (e) {
            let brand = $('#brand_free' + newNum).val();
            let category = $(this).val();
            if (category) {
                $.ajax({
                    type: "post",
                    data: {
                        category: category,
                        brand: brand
                    },
                    url: route,
                    success: function (res) {
                        var $select = $('#product_free' + newNum);

                        $select.find('option').remove();
                        $.each(res, function (key, value) {
                            $select.append('<option value="' + key + '">' + value + '</option>');
                        });

                    }
                });
            }

        });
        $("#brand_free" + newNum).on('change', function (e) {

            let category = $('#category_free' + newNum).val();
            let brand = $(this).val();
            if (brand) {
                $.ajax({
                    type: "post",
                    data: {
                        category: category,
                        brand: brand
                    },
                    url: route,
                    success: function (res) {
                        var $select = $('#product_free' + newNum);
                        $select.find('option').remove();
                        $.each(res, function (key, value) {
                            $select.append('<option value="' + key + '">' + value + '</option>');
                        });

                    }
                });
            }
        });

        // Enable the "remove" button. This only shows once you have a duplicated section.
        $("#btnDel").removeAttr("style")
        $("#btnDel").attr("disabled", false);


    });

    $("#btnDel").click(function () {
        // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if (
            confirm(
                "Are you sure you wish to remove this section? This cannot be undone."
            )
        ) {
            var num = $(".clonedInput").length;
            // how many "duplicatable" input fields we currently have
            $("#entry" + num).slideUp("slow", function () {
                $(this).remove();
                // if only one element remains, disable the "remove" button
                if (num - 1 === 1) {
                    $("#btnDel").attr("disabled", true);
                    $("#btnDel").hide();
                    $("#btnAdd")
                        .attr("disabled", false)
                        .prop("value", "add section");
                }


            });
        }
        return false; // Removes the last section you added
    });


    $(document).on("click", ".btnAdd1", function () {

        let currentRowNum = $(this).data("id");
        $('#brand1_buy' + currentRowNum).select2("destroy");
        $('#category1_buy' + currentRowNum).select2("destroy");
        $('#product1_buy' + currentRowNum).select2("destroy");
        $('#unit1_buy' + currentRowNum).select2("destroy");

        var num = $(".second").length, // Checks to see how many "duplicatable" input fields we currently have
            newNum = parseInt(num + 1), // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $("#second" + currentRowNum)
                .clone()
                .attr("id", "second" + newNum)
                .fadeIn("slow");

        $('#brand1_buy' + currentRowNum).select2({
            'placeholder': 'Select Brand',
        });
        $('#product1_buy' + currentRowNum).select2({
            'placeholder': 'Select a product',
        });
        $('#category1_buy' + currentRowNum).select2({
            'placeholder': 'Select a Category',
        });
        $('#unit1_buy' + currentRowNum).select2({
            'placeholder': 'Unit',
        });

        newElem
            .find(".heading-reference2")
            .attr("id", "ID" + newNum + "_reference")
            .html("Type #" + newNum);


        // Brand - select
        newElem.find(".label1_cb").attr("for", "brand1_buy" + newNum);
        newElem.find(".btnAdd1").removeAttr("id");
        newElem.find(".btnAdd1").attr("data-id", newNum);
        newElem
            .find(".brand1_buy")
            .attr("id", "brand1_buy" + newNum)
            .attr("name", "brand")
            .val("");

        //Category
        newElem.find(".label1_cb").attr("for", "category1_buy" + newNum);
        newElem.find(".btnAdd1").removeAttr("id");
        newElem.find(".btnAdd1").attr("data-id", newNum);
        newElem
            .find(".category1_buy")
            .attr("id", "category1_buy" + newNum)
            .attr("name", "category")
            .val("");

        //product
        newElem.find(".label1_pb").attr("for", "product1_buy" + newNum);
        newElem.find(".btnAdd1").removeAttr("id1");
        newElem.find(".btnAdd1").attr("data-id", newNum);
        newElem
            .find(".product1_buy")
            .attr("id", "product1_buy" + newNum)
            .attr("name", "product1_buy["+newNum+"]")
            .val("");

        newElem.find(".label1_ub").attr("for", "unit1_buy" + newNum);
        newElem.find(".btnAdd1").removeAttr("id");
        newElem.find(".btnAdd1").attr("data-id", newNum);
        newElem
            .find(".unit1_buy")
            .attr("id", "unit1_buy" + newNum)
            .attr("name", "unit1_buy["+newNum+"]")
            .val("");

        newElem.find(".label1_pub").attr("for", "purchase_unit1_buy" + newNum);
        newElem.find(".btnAdd1").removeAttr("id1");
        newElem.find(".btnAdd").attr("data-id", newNum);
        newElem
            .find(".purchase_unit1_buy")
            .attr("id", "purchase_unit_buy" + newNum)
            .attr("name", "purchase_unit_buy["+newNum+"]")
            .val("");

        newElem.find(".label1_dp").attr("for", "percentage_discount" + newNum);
        newElem.find(".btnAdd1").removeAttr("id1");
        newElem.find(".btnAdd1").attr("data-id", newNum);
        newElem
            .find(".purchase_unit_free")
            .attr("id", "percentage_discount" + newNum)
            .attr("name", "percentage_discount["+newNum+"]")
            .val("");


        $("div.second").last().after(newElem);
        $("#ID" + newNum + "_title").focus();
        $('#brand1_buy' + newNum).select2({
            placeholder: "Select Brand",
            allowClear: true
        });
        $('#product1_buy' + newNum).select2({
            'placeholder': 'Select a product',
        });
        $('#category1_buy' + newNum).select2({
            'placeholder': 'Select a Category',
        });
        $('#unit1_buy' + newNum).select2({
            'placeholder': 'Unit',
        });

        $("#category_buy1" + newNum).on('change', function (e) {
            let brand = $('#brand1_buy' + newNum).val();
            let category = $(this).val();
            if (category) {
                $.ajax({
                    type: "post",
                    data: {
                        category: category,
                        brand: brand
                    },
                    url: route,
                    success: function (res) {
                        var $select = $('#product1_buy' + newNum);

                        $select.find('option').remove();
                        $.each(res, function (key, value) {
                            $select.append('<option value="' + key + '">' + value + '</option>');
                        });

                    }
                });
            }

        });

        $("#brand_buy1" + newNum).on('change', function (e) {

            let category = $('#category1_buy' + newNum).val();
            let brand = $(this).val();
            if (brand) {
                $.ajax({
                    type: "post",
                    data: {
                        category: category,
                        brand: brand
                    },
                    url: route,
                    success: function (res) {
                        var $select = $('#product1_buy' + newNum);
                        $select.find('option').remove();
                        $.each(res, function (key, value) {
                            $select.append('<option value="' + key + '">' + value + '</option>');
                        });

                    }
                });
            }
        });

        // Enable the "remove" button. This only shows once you have a duplicated section.
        $("#btnDel1").removeAttr("style")
        $("#btnDel1").attr("disabled", false);


    });

    $("#btnDel1").click(function () {
        // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if (
            confirm(
                "Are you sure you wish to remove this section? This cannot be undone."
            )
        ) {
            var num = $(".second").length;
            // how many "duplicatable" input fields we currently have
            $("#second" + num).slideUp("slow", function () {
                $(this).remove();
                // if only one element remains, disable the "remove" button
                if (num - 1 === 1) {
                    $("#btnDel1").attr("disabled", true);
                    $("#btnDel1").hide();
                    $("#btnAdd1")
                        .attr("disabled", false)
                        .prop("value", "add section");
                }


            });
        }
        return false; // Removes the last section you added
    });

});

