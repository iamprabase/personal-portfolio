$('#updateDateFormat').on('click',function(e){
	var dateFormat = $('#date_format_settings').val();
	$(this).prop('disabled',true);
	e.preventDefault();
	var url = $(this).attr('action');
	$.ajax({
		headers: {
		  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: url,
		type: "POST",
		data: {
			'dateFormat':dateFormat,
		},
		beforeSend:function(){
			$('#updateDateFormat').val('please wait...');
		},
		success: function (data) {
			$('#updateDateFormat').prop('disabled',false);
			$('#updateDateFormat').val('Update');
			console.log('ajax succeed');
			if(data.status==true){
				alert('Date Format Updated');
			}
		},
		error:function(error){
			console.log('ajax failed');
			$('#updateDateFormat').val('Update');
			$('#updateDateFormat').prop('disabled',false);
		}
	});
});