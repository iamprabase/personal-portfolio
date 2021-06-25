$('#addNewbusiness_type').on('submit',function(e){
	e.preventDefault();
	var url = $(this).attr('action');
	$.ajax({
		headers: {
		  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: url,
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		beforeSend:function(){
			$('.addNewbusiness_type').attr('disabled','disabled');
		},
		success: function (data) {
			if(data.status==true){
				$('#addNewbusiness_type')[0].reset();
				$('#tbl_business_types').empty();
				$('#tbl_business_types').html(data.businessTypes);
			}
			alert(data.message);
			$('.addNewbusiness_type').removeAttr('disabled');
		},
		error:function(error){
			$('.addNewbusiness_type').removeAttr('disabled');
			console.log('Oops! Something went Wrong'+error);
		}
	});
});

$('#tblbusiness_type').on('click','.edit-business_type',function(){
	$('#editbusiness_type').modal('show');
	$('#editbusiness_typename').val($(this).data('name'));
	$('#formEditbusiness_type').attr('action',$(this).data('url'));
});

$('#tblbusiness_type').on('click','.delete-business_type',function(){
	$('#modalDeletebusiness_type').modal('show');
	$('#frmDelbusiness_type').attr('action',$(this).data('url'));
});

$('#formEditbusiness_type').on('submit',function(e){
	e.preventDefault();
	var url = $(this).attr('action'); 
	$.ajax({
		headers: {
		  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: url,
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		beforeSend:function(){
			$('.updatebusiness_type').attr('disabled','disabled');
		},
		success: function (data) {
			if(data.status==true){
				$('#tbl_business_types').empty();
				$('#tbl_business_types').html(data.businessTypes);
			}
			alert(data.message);
			$('.updatebusiness_type').removeAttr('disabled');
			$('#editbusiness_type').modal('hide');
		},
		error:function(error){
			$('.updatebusiness_type').removeAttr('disabled');
			$('#editbusiness_type').modal('hide');
			console.log('Oops! Something went Wrong'+error);
		}
	});
});

$('#frmDelbusiness_type').on('submit',function(e){
	e.preventDefault();
	var url = $(this).attr('action'); 
	$.ajax({
		headers: {
		  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: url,
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		beforeSend:function(){
			$('.removebusiness_typeKey').attr('disabled','disabled');
		},
		success: function (data) {
			if(data.status==true){
				$('#tbl_business_types').empty();
				$('#tbl_business_types').html(data.businessTypes);
			}
			alert(data.message);
			$('.removebusiness_typeKey').removeAttr('disabled');
			$('#modalDeletebusiness_type').modal('hide');
		},
		error:function(error){
			$('.removebusiness_typeKey').removeAttr('disabled');
			$('#modalDeletebusiness_type').modal('hide');
			console.log('Oops! Something went Wrong'+error);
		}
	});
});