$(document).on('click', '.addNewCustomField', function () {
    $('#customFieldModal').modal('show');
    $('#customFieldModal').data('module', $(this).data('module'));
    $('#customFieldModal .modal-body').html($('#inner-modal-main .modal-body').html());
});


$(document).on('click', '.cancel_customfield', function () {
    let id = $(this).data('id');
    $('.content_text_' + id).removeClass('hide');
    $('.content_edit_' + id).addClass('hide');
    $('.customField_update_' + id).addClass('hide');
    $('.deletebtn_customField_' + id).removeClass('hide');
    $('.editbtn_customField_' + id).removeClass('hide');
});


$('#customFieldModal').on('submit', 'form', function (e) {
    e.preventDefault();
    //alert('hi');
    var url = $('#customFieldModal').data('url');
    var data = null;
    var type = $("#customFieldModal").find('h5').html();
    var title = $(this).find('input').val();
    var module = module_id;
    var is_mandatory = $(this).find('#is_mandatory').is(':checked');

    data = {type: type, title: title, module: module, is_mandatory: is_mandatory};
    if (type === "Single option" || type === "Multiple options" || type === "Check Box" || type === "Radio Button") {
        var avalue = $(this).find('textarea').val();
        var newVal = avalue.replace(/^\s*[\r\n]/gm, '');
        var options = newVal.split(/\n/);

        data = {type: type, title: title, options: options, module: module, is_mandatory: is_mandatory};
    } else if (type == "Employee") {
        type = "User";
        data['type'] = type;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: "POST",
        data: data,
        beforeSend: function () {
            $('.submit').attr('disabled', 'true');
        },
        success: function (data) {
            $('.submit').removeAttr('disabled');
            if (data.error === 'false') {
                alert(data.message);
            } else {
                $('#party_custom_fields').DataTable().destroy();
                $('#party_custom_fields').find('tbody').first().html(data);
                $('#customFieldModal').modal('hide');
                location.reload()
            }
        },
        error: function (error) {
            $('.submit').removeAttr('disabled');
            if (error.status === 422){
                alert(error.responseJSON.errors.title)
            }else {
                alert('Oops! Something went Wrong');
            }
        }
    });
});


// Displaying Modals of different types of fields
$("#customFieldModal").on("click", "#signin1", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal1 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin2", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal2 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin3", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal3 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin4", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal4 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin5", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal5 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin6", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal6 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin7", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal7 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin8", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal8 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin9", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal9 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin10", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal10 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin11", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal11 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin12", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal12 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin13", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal13 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin14", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal14 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin15", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal15 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin16", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal16 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin17", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal17 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin18", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal18 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin19", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal19 .modal-body').html());
});
$("#customFieldModal").on("click", "#signin20", function (e) {
    e.preventDefault();
    $('#customFieldModal .modal-body').html($('#innerfield-modal20 .modal-body').html());
});