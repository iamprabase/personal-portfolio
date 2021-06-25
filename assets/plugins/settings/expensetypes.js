$('#addNewexpense_type').on('submit',function(e){
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
			$('.addNewexpense_type').attr('disabled','disabled');
		},
		success: function (data) {
			if(data.status==true){
				$('#addNewexpense_type')[0].reset();
				$('#tbl_expense_types').empty();
				$('#tbl_expense_types').html(data.expenseTypes);
			}
			alert(data.message);
			$('.addNewexpense_type').removeAttr('disabled');
		},
		error:function(error){
			$('.addNewexpense_type').removeAttr('disabled');
			console.log('Oops! Something went Wrong'+error);
		}
	});
});

$('#tblexpense_type').on('click','.edit-expense_type',function(){
	$('#editexpense_type').modal('show');
	$('#editexpense_typename').val($(this).data('name'));
	$('#formEditexpense_type').attr('action',$(this).data('url'));
});

$('#tblexpense_type').on('click','.delete-expense_type',function(){
	$('#modalDeleteexpense_type').modal('show');
	$('#frmDelexpense_type').attr('action',$(this).data('url'));
});

$('#formEditexpense_type').on('submit',function(e){
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
			$('.updateexpense_type').attr('disabled','disabled');
		},
		success: function (data) {
			if(data.status==true){
				$('#tbl_expense_types').empty();
				$('#tbl_expense_types').html(data.expenseTypes);
			}
			alert(data.message);
			$('.updateexpense_type').removeAttr('disabled');
			$('#editexpense_type').modal('hide');
		},
		error:function(error){
			$('.updateexpense_type').removeAttr('disabled');
			$('#editexpense_type').modal('hide');
			console.log('Oops! Something went Wrong'+error);
		}
	});
});

$('#frmDelexpense_type').on('submit',function(e){
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
			$('.removeexpense_typeKey').attr('disabled','disabled');
		},
		success: function (data) {
			if(data.status==true){
				$('#tbl_expense_types').empty();
				$('#tbl_expense_types').html(data.expenseTypes);
			}
			alert(data.message);
			$('.removeexpense_typeKey').removeAttr('disabled');
			$('#modalDeleteexpense_type').modal('hide');
		},
		error:function(error){
			$('.removeexpense_typeKey').removeAttr('disabled');
			$('#modalDeleteexpense_type').modal('hide');
			console.log('Oops! Something went Wrong'+error);
		}
	});
});