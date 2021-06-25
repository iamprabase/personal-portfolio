$.fn.extend({
  treed: function (o) {
    var openedClass = 'glyphicon-minus-sign';
    var closedClass = 'glyphicon-plus-sign';
    if (typeof o != 'undefined') {
      if (typeof o.openedClass != 'undefined') {
        openedClass = o.openedClass;
      }
      if (typeof o.closedClass != 'undefined') {
        closedClass = o.closedClass;
      }
    }
    ;
    /* initialize each of the top levels */
    var tree = $(this);
    tree.addClass("tree");
    tree.find('li').has("ul").each(function () {
      var branch = $(this);
      branch.prepend("");
      branch.addClass('branch');
      branch.on('click', function (e) {
        if (this == e.target) {
          var icon = $(this).children('i:first');
          icon.toggleClass(openedClass + " " + closedClass);
          $(this).children().children().toggle();
        }
      })
      branch.children().children().toggle();
    });
    /* fire event from the dynamically added icon */
    tree.find('.branch .indicator').each(function () {
      $(this).on('click', function () {
        $(this).closest('li').click();
      });
    });
    /* fire event to open branch if the li contains an anchor instead of text */
    tree.find('.branch>a').each(function () {
      $(this).on('click', function (e) {
        $(this).closest('li').click();
        e.preventDefault();
      });
    });
    /* fire event to open branch if the li contains a button instead of text */
    tree.find('.branch>button').each(function () {
      $(this).on('click', function (e) {
        $(this).closest('li').click();
        e.preventDefault();
      });
    });
  }
});

$('#tree1').treed();
//Party Type Section
$('#tree1').on('click','span', function(){
  $('#modalDeletePartyType').modal('show');
  $('#delPartyType').attr('action',$(this).attr('destroy-url'));
});
$('#tree1').on('click','a', function(){
  var superior_id = $(this).attr('superior-id');
  var party_id = $(this).attr('data-id');
  $('#modalEditPartyType').modal('show');
  $('#editPartyType').attr('action',$(this).attr('edit-url'));
  $('#party_type_name').val($(this).attr('data-name'));
  $('#party_type_short_name').val($(this).attr('data-short-name'));
  $('#party_parent option').removeAttr('selected');
  if($(this).attr('data-ticked')==0){
    $('#partyType_display_status').attr('checked',false);
  }else{
    $('#partyType_display_status').attr('checked',true);
  }
  var url = $('#getPartyTypes').data('url');
  var company_id = $('#party_edit_company_id').val();
  var myId = $(this).attr('data-id');
  $.ajax({
    url: url,
    type: "GET",
    data:
    {
      'company_id':company_id,
      'myId':myId,
    },
    success: function (data) {
      $('#modalEditPartyName').modal('hide');
      $('#party_parent').empty();
      $('<option></option>').text('Select Party Type').appendTo('#party_parent');
      $.each(data, function (i, v) {
        if(v.id == superior_id){
          $('<option selected></option>').val(v.id).text(v.name).appendTo('#party_parent');
        }else{
          $('<option></option>').val(v.id).text(v.name).appendTo('#party_parent');
        }
      });
    }
  });
});
$('#tree1').on('click','p', function(){
  var superior_id =  $(this).attr('superior-id');
  var party_id = $(this).attr('data-id');
  $('#modalEditPartyName').modal('show');
  $('#editPartyName').attr('action',$(this).attr('edit-url'));
  $('#party_type_nameonly').val($(this).attr('data-name'));
  $('#party_type_short_nameonly').val($(this).attr('data-short-name'));
  if($(this).attr('data-ticked')==0){
    $('#tickedSalemanAllowed').attr('checked',false);
  }else{
    $('#tickedSalemanAllowed').attr('checked',true);
  }
});
$('#editPartyName').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#party_edit_company_id').val();
  var party_type = $('#party_type_nameonly').val();
  var short_name = $('#party_type_short_nameonly').val();
  if($('#tickedSalemanAllowed').prop('checked')==true){
    display_status=1;
  }else{
    display_status=0;
  }
  $.ajax({
    url: url,
    type: "POST",
    data:
    {
      'company_id':company_id,
      'party_type':party_type,
      'party_type_short_name':short_name,
      'display_status':display_status,
    },
    success: function (data) {
      $('#modalEditPartyName').modal('hide');
      $('#tree1').html(data['tree']);
      $('#tree1').treed();
      $('#select_party_types').empty();
      $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
      $.each(data['partytypes'],function(i,v){
        $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
      });
      alert("Party type has been updated successfully.");
    }
  });
});

$('#editPartyType').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#party_edit_company_id').val();
  var party_type = $('#party_type_name').val();
  var party_type_short_name = $('#party_type_short_name').val();
  var party_parent = $('#party_parent').val();
  var display_status = $('#partyType_display_status').val();
  if($('#partyType_display_status').prop('checked')==true){
    display_status=1;
  }else{
    display_status=0;
  }
  $.ajax({
    url: url,
    type: "POST",
    data: {
      company_id: company_id,
      party_type: party_type,
      party_parent: party_parent,
      party_type_short_name: party_type_short_name,
      display_status: display_status,
    },
    beforeSend: function () {
      $("#update-party-type").attr("disabled", true);
    },
    success: function (data) {
      $("#modalEditPartyType").modal("hide");
      $("#tree1").html(data["tree"]);
      $("#tree1").treed();
      $("#select_party_types").empty();
      $("<option></option>")
        .text("Select Party Type")
        .appendTo("#select_party_types");
      $.each(data["partytypes"], function (i, v) {
        $("<option></option>")
          .val(v.id)
          .text(v.name)
          .appendTo("#select_party_types");
      });
      alert("Party type has been updated successfully.");
      $("#update-party-type").attr("disabled", false);
    },
    error: function(error){
      if(error.status == 422) {
        alert(error.responseJSON.two_party_level_exceeds);
      } 
      $("#update-party-type").attr("disabled", false);
    }
  });
});
$('#delPartyType').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#del_company_id').val();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      company_id: company_id,
    },
    beforeSend: function () {
      $("#del-party-type").attr("disabled", true);
    },
    success: function (data) {
      $("#modalDeletePartyType").modal("hide");
      if (data.status == false) {
        alert(data.message);
      } else {
        $("#tree1").html(data["tree"]);
        $("#tree1").treed();
        $("#select_party_types").empty();
        $("<option></option>")
          .text("Select Party Type")
          .appendTo("#select_party_types");
        $.each(data["partytypes"], function (i, v) {
          $("<option></option>")
            .val(v.id)
            .text(v.name)
            .appendTo("#select_party_types");
        });
        $("#del-party-type").attr("disabled", false);
        alert("Party Type Deleted Successfully");
      }
    },
    error: function(){
      $("#del-party-type").attr("disabled", false);
    }
  });
});


$('#frmAddNewPartyType').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  $.ajax({
    url: url,
    type: "POST",
    data:new FormData(this),
    contentType: false,
    cache: false,
    processData: false,
    beforeSend:function(){
      $('#btnAddParty').attr('disabled','disabled');
      $('.refreshing').removeClass('hide');
    },
    success: function (data) {
      $('#frmAddNewPartyType')[0].reset();
      $('.refreshing').addClass('hide');
      $('#btnAddParty').removeAttr('disabled');
      if(data.status == false){
        alert(data.message);
      }else{
        $('#tree1').html(data['tree']);
        $('#tree1').treed();
        $('#select_party_types').empty();
        $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
        $.each(data['partytypes'],function(i,v){
          $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
        });
        alert("Party type has been added successfully. Please setup roles and permissions for this party type.");
      }
    },
    error:function(error){
      $('.refreshing').addClass('hide');
      $('#btnAddParty').removeAttr('disabled');
      if(error.status == 422) {
        alert(error.responseJSON.two_party_level_exceeds);
      }      
    }
  });
});