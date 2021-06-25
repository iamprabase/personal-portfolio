<script>
  $('#addNewVisitPurpose').submit(function(e){
    e.preventDefault();
    let current = $(this);
    let url = current.attr('action');
    let title = current.serializeArray()[1]['value'];

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: url,
      type: "POST",
      data: {
          '_token': '{{csrf_token()}}',
          'title':title,
      },
      beforeSend:function(){
        $('.addNewVisitPurposeBtn').attr("disabled","disabled");
      },
      success: function (data) {
        $('#addNewVisitPurpose').find('.visit-purpose-title-error').html('');
        if(data['status']){
          alert(data['msg']);
          document.getElementById('tbody_visitpurpose').innerHTML = '';
          document.getElementById('tbody_visitpurpose').innerHTML = data['body'];
          current.find('#title').val("");
        }
        $('.addNewVisitPurposeBtn').attr("disabled",false );
      },
      error:function(jqXhr){
        $('.addNewVisitPurposeBtn').attr('disabled',false);
        if(jqXhr.status==422) {
          let errors = jqXhr.responseJSON.errors;
          if(jqXhr.responseJSON.errors.title){
            $('.visit-purpose-title-error').html('');
            $('.visit-purpose-title-error').html(jqXhr.responseJSON.errors.title[0]);
          }
        }else{
          alert('Oops! Something went wrong...');
        }
      },
      complete: function(){
        $('.addNewVisitPurposeBtn').attr("disabled",false );
      }
    });
  });

  $(document).on('click', '.edit-visit-purpose-btn', function(){
    let current = $(this)[0];
    let url = current.dataset.url;
    let title = current.dataset.title; 
    $('#editVisitPurpose').modal();
    $('#editVisitPurpose').find('#editVisitPurposeTitle').val(title);
    $('#editVisitPurpose').find('#formEditVisitPurpose').attr('action', url);
    $('#formEditVisitPurpose').find('.edit-visit-purpose-title-error').html('');
  });

  $('#formEditVisitPurpose').submit(function(e){
    e.preventDefault();
    let current = $(this);
    let url = current.attr('action');
    let title = current.serializeArray()[1]['value'];

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: url,
      type: "POST",
      data: {
          '_token': '{{csrf_token()}}',
          'title':title,
      },
      beforeSend:function(){
        $('.updateVisitPurposeBtn').attr("disabled","disabled");
      },
      success: function (data) {
        $('#formEditVisitPurpose').find('.edit-visit-purpose-title-error').html('');
        if(data['status']){
          alert(data['msg']);
          document.getElementById('tbody_visitpurpose').innerHTML = '';
          document.getElementById('tbody_visitpurpose').innerHTML = data['body'];
          current.find('#title').val("");
          $('#editVisitPurpose').modal('hide');
        }
        $('.updateVisitPurposeBtn').attr("disabled",false );
      },
      error:function(jqXhr){
        $('.updateVisitPurposeBtn').attr('disabled',false);
        if(jqXhr.status==422) {
          let errors = jqXhr.responseJSON.errors;
          if(jqXhr.responseJSON.errors.title){
            $('.edit-visit-purpose-title-error').html('');
            $('.edit-visit-purpose-title-error').html(jqXhr.responseJSON.errors.title[0]);
          }
        }else{
          alert('Oops! Something went wrong...');
        }
      },
      complete: function(){
        $('.updateVisitPurposeBtn').attr("disabled",false );
      }
    });
  });

  $(document).on('click', '.delete-visit-purpose-btn', function(){
    let current = $(this)[0];
    let url = current.dataset.url;
    $('#modalDeleteVisitPurpose').modal();
    $('#modalDeleteVisitPurpose').find('#frmDelVisitPurpose').attr('action', url);
  });

  $('#frmDelVisitPurpose').submit(function(e){
    e.preventDefault();
    let current = $(this);
    let url = current.attr('action');

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: url,
      type: "POST",
      data: {
          '_token': '{{csrf_token()}}'
      },
      beforeSend:function(){
        $('.removeVisitPurposeBtn').attr("disabled","disabled");
      },
      success: function (data) {
        if(data['status']){
          alert(data['msg']);
          document.getElementById('tbody_visitpurpose').innerHTML = '';
          document.getElementById('tbody_visitpurpose').innerHTML = data['body'];
          $('#modalDeleteVisitPurpose').modal('hide');
        }
        $('.removeVisitPurposeBtn').attr("disabled",false );
      },
      error:function(){
        $('.removeVisitPurposeBtn').attr('disabled',false);
        alert('Oops! Something went wrong...');
      },
      complete: function(){
        $('.removeVisitPurposeBtn').attr("disabled",false );
      }
    });
  });
</script>