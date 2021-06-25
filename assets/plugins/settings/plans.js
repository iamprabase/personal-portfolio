$(document).on('click', '.switches', function() {
    if ($(this).hasClass('party') && !($(this).is(":checked"))) {
        $('.orders').prop('checked', false);
        $('.notes').prop('checked', false);
        $('.party_files').prop('checked', false);
        $('.party_images').prop('checked', false);
        $('.collections').prop('checked', false);
        $('.pdcs').prop('checked', false);
        $('.beat').prop('checked', false);
        $('.returns').prop('checked', false);
        $('.stock_report').prop('checked', false);
        $('.dso').prop('checked', false);
        $('.dsobyunit').prop('checked', false);
        $('.ordersreport').prop('checked', false);
        $('.psoreport').prop('checked', false);
        $('.spwise').prop('checked', false);
        $('.dpartyreport').prop('checked', false);
        $('.dempreport').prop('checked', false);
        $('.accounting').prop('checked', false);
        $('.product').prop('checked', false);
        $('.analytics').prop('checked', false);
        $('.zero_orders').prop('checked', false);
        $('.ageing').prop('checked', false);
        $('.visit_module').prop('checked', false);
        $('.targets').prop('checked', false);
        $('.targets_rep').prop('checked', false);
    }
    if ($(this).hasClass('livetracking') && !($(this).is(":checked"))) {
        $('.gpsreports').prop('checked', false);
    }
    if ($(this).hasClass('orders') && !($(this).is(":checked"))) {
        $('.analytics').prop('checked', false);
        $('.accounting').prop('checked', false);
        $('.zero_orders').prop('checked', false);
        $('.ageing').prop('checked', false);
        $('.dso').prop('checked', false);
        $('.dsobyunit').prop('checked', false);
        $('.ordersreport').prop('checked', false);
        $('.psoreport').prop('checked', false);
        $('.spwise').prop('checked', false);
        $('.dpartyreport').prop('checked', false);
        $('.dempreport').prop('checked', false);
    }
    if ($(this).hasClass('collections') && !($(this).is(":checked"))) {
        $('.analytics').prop('checked', false);
        $('.accounting').prop('checked', false);
        $('.dpartyreport').prop('checked', false);
        $('.dempreport').prop('checked', false);
        $('.ageing').prop('checked', false);
    }
    if ($(this).hasClass('product') && !($(this).is(":checked"))) {
        $('.analytics').prop('checked', false);
        $('.orders').prop('checked', false);
        $('.accounting').prop('checked', false);
        $('.zero_orders').prop('checked', false);
        $('.ageing').prop('checked', false);
        $('.dso').prop('checked', false);
        $('.dsobyunit').prop('checked', false);
        $('.ordersreport').prop('checked', false);
        $('.psoreport').prop('checked', false);
        $('.spwise').prop('checked', false);
        $('.dpartyreport').prop('checked', false);
        $('.dempreport').prop('checked', false);
        $('.schemes').prop('checked', false);
    }
    if ($(this).hasClass('accounting') && !($(this).is(":checked"))) {
        $('.ageing').prop('checked', false);
    }
    if ($(this).hasClass('beat') && !($(this).is(":checked"))) {
        $('.analytics').prop('checked', false);
    }
    if ($(this).hasClass('leaves') && !($(this).is(":checked"))) {
        $('.analytics').prop('checked', false);
    }
    if ($(this).hasClass('analytics') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.orders').prop('checked', true);
        $('.collections').prop('checked', true);
        $('.product').prop('checked', true);
        $('.beat').prop('checked', true);
        $('.leaves').prop('checked', true);
    }
    if ($(this).hasClass('accounting') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
        $('.orders').prop('checked', true);
        $('.collections').prop('checked', true);
    }
    if ($(this).hasClass('collections') && ($(this).is(":checked"))) {
        $('.product').prop('checked', true);
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('orders') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
    }
    if ($(this).hasClass('visit_module') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('notes') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('beat') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('returns') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
    }
    if ($(this).hasClass('stock_report') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
    }
    if (($(this).hasClass('party_files') || $(this).hasClass('party_images')) && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('dso') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
        $('.orders').prop('checked', true);
    }
    if ($(this).hasClass('dsobyunit') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
        $('.orders').prop('checked', true);
    }
    if ($(this).hasClass('ordersreport') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
        $('.orders').prop('checked', true);
    }
    if ($(this).hasClass('psoreport') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
        $('.orders').prop('checked', true);
    }
    if ($(this).hasClass('spwise') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.product').prop('checked', true);
        $('.orders').prop('checked', true);
    }
    if ($(this).hasClass('dpartyreport') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.orders').prop('checked', true);
        $('.product').prop('checked', true);
        $('.collections').prop('checked', true);
    }
    if ($(this).hasClass('dempreport') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.orders').prop('checked', true);
        $('.product').prop('checked', true);
        $('.collections').prop('checked', true);
    }
    if ($(this).hasClass('pdcs') && ($(this).is(":checked"))) {
        $('.collections').prop('checked', true);
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('gpsreports') && ($(this).is(":checked"))) {
        $('.livetracking').prop('checked', true);
    }
    if ($(this).hasClass('product') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('zero_orders') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.orders').prop('checked', true);
        $('.product').prop('checked', true);
    }
    if ($(this).hasClass('ageing') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
        $('.orders').prop('checked', true);
        $('.collections').prop('checked', true);
        $('.product').prop('checked', true);
        $('.accounting').prop('checked', true);
    }

    if ($(this).hasClass('targets') && ($(this).is(":checked"))) {
        $('.party').prop('checked', true);
    }
    if ($(this).hasClass('targets_rep') && ($(this).is(":checked"))) {
        $('.targets').prop('checked', true);
        $('.party').prop('checked', true);
    }


    var counter = 0;
    $.each($('.switches'), function(k, v) {
        if ($(this).is(":checked")) {
            counter++;
        }
    });
    if (counter == totalModule) {
        $(".toggle-all-switches").prop('checked', true);
    } else {
        $(".toggle-all-switches").prop('checked', false);
    }
});