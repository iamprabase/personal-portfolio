$('.switches').on('click', function() {
    var index = $(this).data('index');
    var role = $(this).data('role');
    var category = $(this).data('categoryname');
    var max = $(this).data('max');
    var totalmax = $(this).data('totalmax');
    if ($(this).is(":checked")) {
        var checked = true;
    } else {
        var checked = false;
        $('.toggle-all-' + role).prop('checked', false);
    }
    if ($(this).data('toggle') == true) {
        toggleCategory(category, role, checked);
    } else {
        switchingButtons(index, role, category, checked);
    }
    toggleChecker(category, role, max);
    categoryChecker(role, totalmax);
});

$('.toggle-all').on('click', function() {
    if ($(this).is(":checked")) {
        var checked = true;
    } else {
        var checked = false;
    }
    var role = $(this).data('role');
    if (checked == true) {
        $('.switches-' + role).prop('checked', true);
    } else {
        $('.switches-' + role).prop('checked', false);
    }
});

function switchingButtons(index, role, category, checked) {
    if (index == 2 && checked == false) {
        category = $.trim(category);
        $('.' + category + '-' + role).prop('checked', false);
    } else if (index != 2 && checked == true) {
        category = $.trim(category);
        $('.' + category + '-' + role + '-2').prop('checked', true);
    }
    if (category == "Employees" && checked == false && (index == 1 || index == 2 || index == 3)) {
        $('.Role_Assignment-' + role).prop('checked', false);
        $('.' + role + '-Role_Assignment').prop('checked', false);
    }
    if ((category == "Orders" || category == "Collections" || category == "Parties") && checked == false && index == 2) {
        $('.Accounting-' + role).prop('checked', false);
        $('.' + role + '-Accounting').prop('checked', false);
        $('.dpartyreport-' + role).prop('checked', false);
        $('.' + role + '-dpartyreport').prop('checked', false);
        $('.dempreport-' + role).prop('checked', false);
        $('.' + role + '-dempreport').prop('checked', false);
        $('.ageing-' + role).prop('checked', false);
        $('.' + role + '-ageing').prop('checked', false);
    }

    if ((category == "Orders" || category == "Parties") && checked == false && index == 2) {
        $('.Zero_Orders-' + role).prop('checked', false);
        $('.' + role + '-Zero_Orders').prop('checked', false);
        $('.dsoreportbyunit-' + role).prop('checked', false);
        $('.' + role + '-dsoreportbyunit').prop('checked', false);
        $('.dsoreport-' + role).prop('checked', false);
        $('.' + role + '-dsoreport').prop('checked', false);
        $('.oreport-' + role).prop('checked', false);
        $('.' + role + '-oreport').prop('checked', false);
        $('.psoreport-' + role).prop('checked', false);
        $('.' + role + '-psoreport').prop('checked', false);
        $('.spartywisereport-' + role).prop('checked', false);
        $('.' + role + '-spartywisereport').prop('checked', false);
    }

    if (category == "Orders" && checked == false && index == 2) {
        $(".Parties_Rate_Setup-" + role).prop("checked", false);
        $("." + role + "-Parties_Rate_Setup").prop("checked", false);
        console.log($(".product_order_detail_report-" + role))
        $(".product_order_detail_report-" + role).prop("checked", false);
        $("." + role + "-product_order_detail_report").prop("checked", false);
    }

    if ((category == "Parties") && checked == false && index == 2) {
        $('.permission_partytype-' + role).prop('checked', false);
        $('.Notes-' + role).prop('checked', false);

        $("." + role + '-Notes').prop('checked', false);
    }


    if (category == "Role_Assignment" && checked == true) {
        $('.Employees-' + role + '-1').prop('checked', true);
        $('.Employees-' + role + '-2').prop('checked', true);
        $('.Employees-' + role + '-3').prop('checked', true);
    }

    if (category == "Accounting" && checked == true) {
        $('.Parties-' + role + '-2').prop('checked', true);
        $('.Orders-' + role + '-2').prop('checked', true);
        $('.Collections-' + role + '-2').prop('checked', true);
    }

    if (category == "Collections" && checked == false && (index == 2)) {
        $('.' + role + '-PDCs').prop('checked', false);
        $('.PDCs-' + role + '-5').prop('checked', false);
    }

    if (category == "PDCs" && checked == true) {
        $('.Collections-' + role + '-2').prop('checked', true);
    }

    if (category == "Zero_Orders" && checked == true) {
        $('.Parties-' + role + '-2').prop('checked', true);
        $('.Orders-' + role + '-2').prop('checked', true);
    }

    if (category == "Parties_Rate_Setup" && checked == true) {
        $(".Orders-" + role + "-2").prop("checked", true);
    }

    if (category == "product_order_detail_report" && checked == true) {
        $(".Orders-" + role + "-2").prop("checked", true);
    }

    if (category == "product_order_detail_report" && checked == true) {
        $(".Orders-" + role + "-2").prop("checked", true);
    }

    if (category == "ageing" && checked == true) {
        $('.Parties-' + role + '-2').prop('checked', true);
        $('.Orders-' + role + '-2').prop('checked', true);
        $('.Collections-' + role + '-2').prop('checked', true);
    }

    if (category.includes('permission_partytype')) {
        $('.Parties-' + role + '-2').prop('checked', true);
    }

    if (category == "Reports") {
        if (checked == true) {
            $('.Orders-' + role + '-2').prop('checked', true);
        }
        $('.odometer_report-' + role + '-2').prop('checked', checked);
        $('.odometer_report-' + role + '-3').prop('checked', checked);
        $('.' + role + '-odometer_report').prop('checked', checked);
        $('.returns_report-' + role + '-2').prop('checked', checked);
        $('.' + role + '-returns_report').prop('checked', checked);
        $('.stocks_report-' + role + '-2').prop('checked', checked);
        $('.' + role + '-stocks_report').prop('checked', checked);
        $('.salesman_gps_path-' + role + '-2').prop('checked', checked);
        $('.' + role + '-salesman_gps_path').prop('checked', checked);
        $('.monthly_attendance-' + role + '-2').prop('checked', checked);
        $('.' + role + '-monthly_attendance').prop('checked', checked);
        $('.checkin_cout-' + role + '-2').prop('checked', checked);
        $('.' + role + '-checkin_cout').prop('checked', checked);
        $('.dsoreportbyunit-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dsoreportbyunit').prop('checked', checked);
        $('.dsoreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dsoreport').prop('checked', checked);
        $('.oreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-oreport').prop('checked', checked);
        $('.psoreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-psoreport').prop('checked', checked);
        $('.spartywisereport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-spartywisereport').prop('checked', checked);
        $('.dpartyreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dpartyreport').prop('checked', checked);
        $('.dempreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dempreport').prop('checked', checked);
        $('.beatplanreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-beatplanreport').prop('checked', checked);
        $('.ageing-' + role + '-2').prop('checked', checked);
        $('.' + role + '-ageing').prop('checked', checked);
        $('.product_order_detail_report-' + role + '-2').prop('checked', checked);
        $('.' + role + '-product_order_detail_report').prop('checked', checked);
    }

    if ((category == "returns_report" || category == "stocks_report" || category == "salesman_gps_path" || category == "monthly_attendance" || category == "checkin_cout" || category == "dsoreportbyunit" || category == "dsoreport" || category == "oreport" || category == "psoreport" || category == "spartywisereport" || category == "dpartyreport" || category == "dempreport" || category == "beatplanreport" || category == "ageing" || category == 'odometer_report' || category == 'product_order_detail_report') && checked == true && index == 2) {
        $('.Reports-' + role + '-2').prop('checked', true);
        $('.' + role + '-Reports').prop('checked', true);
    }
    if ((category == "returns_report" || category == "Orders" || category == "stocks_report" || category == "salesman_gps_path" || category == "monthly_attendance" || category == "checkin_cout" || category == "dsoreportbyunit" || category == "dsoreport" || category == "oreport" || category == "psoreport" || category == "spartywisereport" || category == "dpartyreport" || category == "dempreport" || category == "beatplanreport" || category == 'odometer_report' || category == 'product_order_detail_report') && checked == false && (index == 1 || index == 2)) {
        let resultMaxCount = reportToggleChecker(role);
        if (resultMaxCount == 0) {
            $('.Reports-' + role + '-2').prop('checked', false);
            $('.' + role + '-Reports').prop('checked', false);
        }
    }

}

function reportToggleChecker(role) {
    let count = 0;

    if ($('.odometer_report-' + role + '-3').is(':checked')) {
        count++;
    }
    if ($('.odometer_report-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.returns_report-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.stocks_report-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.salesman_gps_path-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.monthly_attendance-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.checkin_cout-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.dsoreportbyunit-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.dsoreport-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.oreport-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.psoreport-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.spartywisereport-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.dpartyreport-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.dempreport-' + role + '-2').is(':checked')) {
        count++;
    }
    if ($('.beatplanreport-' + role + '-2').is(':checked')) {
        count++;
    }

    if ($('.product_order_detail_report-' + role + '-2').is(':checked')) {
        count++;
    }
    return count;
}

function toggleChecker(category, role, max) {
    var max = parseInt(max);
    var count = 0;
    for (i = 1; i <= 5; i++) {
        if ($('.' + category + '-' + role + '-' + i).is(":checked")) {
            count++;
        }
    }
    if (count == max) {
        $('.' + role + '-' + category).prop('checked', true);
    } else {
        $('.' + role + '-' + category).prop('checked', false);
    }
}

function toggleCategory(category, role, checked) {
    if (checked == true) {
        $('.' + category + '-' + role).prop('checked', true);
    } else {
        $('.' + category + '-' + role).prop('checked', false);
        $('.toggle-all-' + role).prop('checked', false);
    }
    if (category == "Employees" && checked == false) {
        $('.Role_Assignment-' + role).prop('checked', false);
        $('.' + role + '-Role_Assignment').prop('checked', false);
    }
    if ((category == "Parties" || category == "Orders" || category == "Collections") && checked == false) {
        $('.Accounting-' + role).prop('checked', false);
        $('.' + role + '-Accounting').prop('checked', false);
    }
    if (category == "Role_Assignment" && checked == true) {
        $('.Employees-' + role + '-1').prop('checked', true);
        $('.Employees-' + role + '-2').prop('checked', true);
        $('.Employees-' + role + '-3').prop('checked', true);
    }
    if (category == "Accounting" && checked == true) {
        $('.Parties-' + role + '-2').prop('checked', true);
        $('.Orders-' + role + '-2').prop('checked', true);
        $('.Collections-' + role + '-2').prop('checked', true);
    }
    if (category == "Collections" && checked == false) {
        $('.' + role + '-PDCs').prop('checked', false);
        $('.PDCs-' + role + '-5').prop('checked', false);
    }
    if (category == "Orders" && checked == false) {
        $('.' + role + '-Zero_Orders').prop('checked', false);
        $('.Zero_Orders-' + role + '-2').prop('checked', false);
        $('.dsoreportbyunit-' + role).prop('checked', false);
        $('.' + role + '-dsoreportbyunit').prop('checked', false);
        $('.dsoreport-' + role).prop('checked', false);
        $('.' + role + '-dsoreport').prop('checked', false);
        $('.oreport-' + role).prop('checked', false);
        $('.' + role + '-oreport').prop('checked', false);
        $('.psoreport-' + role).prop('checked', false);
        $('.' + role + '-psoreport').prop('checked', false);
        $('.spartywisereport-' + role).prop('checked', false);
        $('.' + role + '-spartywisereport').prop('checked', false);
        $('.dpartyreport-' + role).prop('checked', false);
        $('.' + role + '-dpartyreport').prop('checked', false);
        $('.dempreport-' + role).prop('checked', false);
        $('.' + role + '-dempreport').prop('checked', false);
    }

    if (category == "Orders" && checked == false) {
        $(".Parties_Rate_Setup-" + role).prop("checked", false);
        $("." + role + "-Parties_Rate_Setup").prop("checked", false);
    }

    if (category == "Parties_Rate_Setup" && checked == true) {
        $(".Orders-" + role).prop("checked", true);
        $("." + role + "-Orders").prop("checked", true);
    }
    if (category == "PDCs" && checked == true) {
        $('.Collections-' + role + '-2').prop('checked', true);
    }
    if (category == "Zero_Orders" && checked == true) {
        $('.Orders-' + role + '-2').prop('checked', true);
    }

    if (category == "Reports") {
        if (checked == true) {
            $('.Orders-' + role + '-2').prop('checked', true);
        }
        console.log('aaaa');
        $('.odometer_report-' + role + '-2').prop('checked', checked);
        $('.odometer_report-' + role + '-3').prop('checked', checked);
        $('.' + role + '-odometer_report').prop('checked', checked);
        $('.returns_report-' + role + '-2').prop('checked', checked);
        $('.' + role + '-returns_report').prop('checked', checked);
        $('.stocks_report-' + role + '-2').prop('checked', checked);
        $('.' + role + '-stocks_report').prop('checked', checked);
        $('.salesman_gps_path-' + role + '-2').prop('checked', checked);
        $('.' + role + '-salesman_gps_path').prop('checked', checked);
        $('.monthly_attendance-' + role + '-2').prop('checked', checked);
        $('.' + role + '-monthly_attendance').prop('checked', checked);
        $('.checkin_cout-' + role + '-2').prop('checked', checked);
        $('.' + role + '-checkin_cout').prop('checked', checked);
        $('.dsoreportbyunit-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dsoreportbyunit').prop('checked', checked);
        $('.dsoreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dsoreport').prop('checked', checked);
        $('.oreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-oreport').prop('checked', checked);
        $('.psoreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-psoreport').prop('checked', checked);
        $('.spartywisereport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-spartywisereport').prop('checked', checked);
        $('.dpartyreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dpartyreport').prop('checked', checked);
        $('.dempreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-dempreport').prop('checked', checked);
        $('.beatplanreport-' + role + '-2').prop('checked', checked);
        $('.' + role + '-beatplanreport').prop('checked', checked);
        $('.ageing-' + role + '-2').prop('checked', checked);
        $('.' + role + '-ageing').prop('checked', checked);
        $('.product_order_detail_report-' + role + '-2').prop('checked', checked);
        $('.' + role + '-product_order_detail_report').prop('checked', checked);
    }

    if ((category == "returns_report" || category == "stocks_report" || category == "salesman_gps_path" || category == "monthly_attendance" || category == "checkin_cout" || category == "dsoreportbyunit" || category == "dsoreport" || category == "oreport" || category == "psoreport" || category == "spartywisereport" || category == "dpartyreport" || category == "dempreport" || category == "beatplanreport" || category == "ageing" || category == "odometer_report" || category == "product_order_detail_report") && checked == true) {
        $('.Reports-' + role + '-2').prop('checked', true);
        $('.' + role + '-Reports').prop('checked', true);
    }

    if ((category == "Parties") && checked == false) {
         $('.permission_partytype-' + role).prop('checked', false);
         $('.Notes-' + role).prop('checked', false);
     }

    let resultMaxCount = reportToggleChecker(role);
    if (resultMaxCount == 0) {
        $('.Reports-' + role + '-2').prop('checked', false);
        $('.' + role + '-Reports').prop('checked', false);
    }
}

function categoryChecker(role, totalmax) {
    var counter = 0;
    $.each($('.rowtoggler' + '-' + role), function(k, v) {
        if ($(this).is(":checked")) {
            counter++;
        }
    });
    if (counter == totalmax) {
        $('.toggle-all-' + role).prop('checked', true);
    }
}