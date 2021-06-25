
initializeDataTable();

function initializeDataTable()
{
    $("#party_custom_fields").DataTable({
        dom: "<'row'<'col-xs-12 customFieldNewTableButton'>>" +
        "<'row'<'col-xs-8 alignleft'l><'col-xs-4 alignright'Bf>>" +
        "<'row'<'col-xs-6'><'col-xs-6'>>" +
        "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        columnDefs: [
            {
            targets: 2,
            searchable: false,
            orderable:false
            },
        ],
        buttons: [],
        columns: [{ width: "150px" }, { width: "150px" }, null],
        paging: true,
    });

    /** 
     * Conflict Block
     * */ 
    // $('#party_custom_fields').DataTable({
    //  "dom": "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright customFieldNewTableButton'>>" +
    //              "<'row'<'col-xs-12't>><'row'<'col-xs-12'i>>",
    //     "columnDefs":[{
    //         "targets"  :2,
    //         "visible": false,
    //         "orderable":false,
    //         "searchable": false
    //     }],
    //     "columns": [
    //         { "width": "150px" },
    //         { "width": "150px" },
    //         null,
    /**
     * Conflict Block End
     */
    // $('#party_custom_fields').DataTable({
    //  "dom": "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright customFieldNewTableButton'>>" +
    //              "<'row'<'col-xs-12't>><'row'<'col-xs-12'i>>",
    //     "columnDefs":[{
    //         "targets"  :2,
    //         "orderable":false,
    //     }],
    //     "columns": [
    //         { "width": "150px" },
    //         { "width": "150px" },
    //         null,
    //     ],
    //      "scrollY": "600px",
    //     "paging": true,
    //     "pagingType": "full_numbers",
    // });
    // @if(Auth::user()->can('customfield-create'))
   // $('.customFieldNewTableButton').append(
    //    '<button class="btn btn-primary pull-right addNewCustomField" 
    //    style="color:white;background-color: #0b7676!important;
    //   border-color: #0b7676!important;margin-right:15px;" 
     //   data-module="Party"><i class="fa fa-plus"></i> Create New</button>');
   // @endif
}


$(document).on('click','.addNewCustomField',function(){
    $('#customFieldModal').modal('show');
    $('#customFieldModal').data('module',$(this).data('module'));
    $('#customFieldModal .modal-body').html($('#inner-modal-main .modal-body').html());
});

$(document).on('click','.editContent',function(){
    let id = ($(this).data('id'));
    $('.content_text_'+ id).addClass('hide');
    $('.content_edit_'+id).removeClass('hide');
    $('.deletebtn_customField_'+id).addClass('hide');
    $('.editbtn_customField_'+id).addClass('hide');
    $('.customField_update_'+id).removeClass('hide');
});

$(document).on('click','.cancel_customfield',function(){
    let id = $(this).data('id');
    $('.content_text_'+id).removeClass('hide');
    $('.content_edit_'+id).addClass('hide');
    $('.customField_update_'+id).addClass('hide');
    $('.deletebtn_customField_'+id).removeClass('hide');
    $('.editbtn_customField_'+id).removeClass('hide');
});

$(document).on('click','.statusupdate_customField',function(){
    //$('#editCustomFieldModal').modal('show');
    var id = $(this).data('id');
    let value = $('.custom_value_'+id).val();
    let url = $('#customStatusUpdateUrl').data('url');
    var status=$('#status').val();
    //alert(status);
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url  : url,
        type : "POST",
        data : {
            "id":id,
            "status":status
        },
        success: function (data) {
        $('#party_custom_fields').DataTable().destroy();
        $('#party_custom_fields').find('tbody').first().html(data);
                initializeDataTable();
        },
        error:function(error){
            console.log('Oops! Something went Wrong'+error);
        }
    });
});

function confirmation() {

          var result = confirm('Confirm to change the status?');

          if (result == true) {

              var id = $('#customfield_id').val();
    let url = $('#customStatusUpdateUrl').data('url');
    var status=$('#status').val();
    //alert(id);
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url  : url,
        type : "POST",
        data : {
            "id":id,
            "status":status
        },
        success: function (data) {
        $('#myModal').modal('hide');
        $('#party_custom_fields').DataTable().destroy();
        $('#party_custom_fields').find('tbody').first().html(data);
                initializeDataTable();
        },
        error:function(error){
            console.log('Oops! Something went Wrong'+error);
        }
    });

          } else {
          }

      }

$(document).on('click', '.edit-modal', function () {

          // $('#footer_action_button').text(" Change");

          $('#footer_action_button').addClass('glyphicon-check');

          $('#footer_action_button').removeClass('glyphicon-trash');

          $('.actionBtn').addClass('btn-success');

          $('.actionBtn').removeClass('btn-danger');

          $('.actionBtn').addClass('edit');

          $('.modal-title').text('Change Status');


          $('.editstatus').show();

           $('#customfield_id').val($(this).data('id'));

          // $('#remark').val($(this).data('remark'));

          $('#status').val($(this).data('status'));

          //$('#warning').hide();

          $('#myModal').modal('show');

      });



$(document).on('click','.update_customField',function(){
    var id = $(this).data('id');
    let value = $('.custom_value_'+id).val();
    let url = $('#customTitleUpdateUrl').data('url');
    var module = $('#customFieldModal').data('module');
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url  : url,
        type : "POST",
        data : {
            "id":id,
            "title":value,
            "module":module,
        },
        beforeSend:function(){
            $('.customfield_refresh_'+id).removeClass('hide');
            $('.customField_update_'+id).addClass('hide');
        },
        success: function (data) {
            if(data.status==false){
                alert(data.message);
                $('.customfield_refresh_'+id).addClass('hide');
                $('.customField_update_'+id).removeClass('hide');
            }else{
                $('.content_edit_'+id).addClass('hide');
                $('.content_text_'+id).removeClass('hide');
                $('.customField_update_'+id).addClass('hide');
                $('.customfield_refresh_'+id).addClass('hide');
                $('.content_text_'+id).html(data.title);
                $('.editbtn_customField_'+id).removeClass('hide');
                $('.deletebtn_customField_'+id).removeClass('hide');
            }
        },
        error:function(error){
            console.log('Oops! Something went Wrong'+error);
        }
    });
});

$(document).on('keypress','.customfield_textbox_edit',function(e){

    if(e.keyCode == 13){
        var id = $(this).data('id');
        let value = $('.custom_value_'+id).val();
        let url = $('#customTitleUpdateUrl').data('url');
        var module = $('#customFieldModal').data('module');
        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url  : url,
            type : "POST",
            data : {
                "id":id,
                "title":value,
                "module":module,
            },
            beforeSend:function(){
                $('.customfield_refresh_'+id).removeClass('hide');
                $('.customField_update_'+id).addClass('hide');
            },
            success: function (data) {
                if(data.status==false){
                    alert(data.message);
                    $('.customfield_refresh_'+id).addClass('hide');
                    $('.customField_update_'+id).removeClass('hide');
                }else{
                    $('.content_edit_'+id).addClass('hide');
                    $('.content_text_'+id).removeClass('hide');
                    $('.customField_update_'+id).addClass('hide');
                    $('.customfield_refresh_'+id).addClass('hide');
                    $('.content_text_'+id).html(data.title);
                    $('.editbtn_customField_'+id).removeClass('hide');
                    $('.deletebtn_customField_'+id).removeClass('hide');
                }
            },
            error:function(error){
                console.log('Oops! Something went Wrong'+error);
            }
        });
    }
});

$(document).on('click','.deleteContent',function(){
    $('#deleteCustomFieldModal').modal('show');
    $('#btn_delete_customfield').data('id',$(this).data('id'));
    console.log($(this).data('id'));
});

$('#btn_delete_customfield').on('click',function(){
    let id = ($(this).data('id'));
    let url = $('#customFieldDeleteUrl').data('url');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: "POST",
        data: {
            "id":id,
        },
        success: function (data) {
            console.log('ajax successed');
            alert('Custom Field Deleted');
            $('#party_custom_fields').DataTable().destroy();
            $('#party_custom_fields').find('tbody').first().html(data);
            initializeDataTable();
            $('#deleteCustomFieldModal').modal('hide');

        },
        error:function(error){
            console.log('Oops! Something went Wrong'+error);
            $('.dayremark_refresh_'+id).addClass('hide');
            $('.dayremark_update_'+id).removeClass('hide');
        }
    });
});

$('#customFieldModal').on('submit','form',function(e){
    e.preventDefault();
    //alert('hi');
    var url = $('#customFieldModal').data('url');
    var data = null;
    var type = $("#customFieldModal").find('h5').html();
    var title = $(this).find('input').val();
    var module = $('#customFieldModal').data('module');
   // var options = $(this).find('textarea').val();

   // var options = $(this).find('textarea').val().split(/\n/);
// var options = [];
// for (var i=0; i < lines.length; i++) {
//   if (/\S/.test(lines[i])) {
//     options.push($.trim(lines[i]));
//   }
// }
  //alert(options);

    data = {type: type, title: title, module: module};
   // alert(data);
    if (type == "Single option" || type == "Multiple options") {
        var avalue = $(this).find('textarea').val();
        var newVal = avalue.replace(/^\s*[\r\n]/gm, '');
        var options = newVal.split(/\n/);
       //var options = $(this).find('textarea').val().split(/\n/);
       //  var options3 = options.replace(/,+/g,',');
       // alert(avalue);
       // alert(newVal);
       //alert(options);
        data = {type: type, title: title, options: options, module: module};
    } else if (type == "Contact") {
        type = "Person";
        data['type'] = type;
    }

    

    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url  : url,
        type : "POST",
        data : data,
        beforeSend:function(){
            $('.submit').attr('disabled','true');
        },
        success: function (data) {
            $('.submit').removeAttr('disabled');
            if(data.status==false){
                alert(data.message);
            }else{
                $('#party_custom_fields').DataTable().destroy();
                $('#party_custom_fields').find('tbody').first().html(data);
                initializeDataTable();
                $('#customFieldModal').modal('hide');
            }
        },
        error:function(error){
            $('.submit').removeAttr('disabled');
            console.log('Oops! Something went Wrong'+error);
        }
    });
});

$(document).on('click','.view-custom',function(e){
    e.preventDefault();
    let id    = $(this).data('id');
    let type  = $(this).data('type');
    let url   = $('#customUpdateUrl').data('url');
    var value = $(this).html();
    var customField = $(this);
    if(value=='Yes'){
        value = 'No';
    }else{
        value = 'Yes';
    }
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: "POST",
        data: {
            "id":id,
            "type":type,
            "value":value,
        },
        beforeSend:function(){
            customField.html(value);
            // $('.addNewbusiness_type').attr('disabled','disabled');
        },
        success: function (data) {
                // $('#party_custom_fields').find('tbody').first().html(data);
                // $('#customFieldModal').modal('hide');
        },
        error:function(error){
            // $('.addNewbusiness_type').removeAttr('disabled');
            console.log('Oops! Something went Wrong'+error);
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