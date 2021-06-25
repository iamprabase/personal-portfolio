$('#btncreditdays').on('click',function(e){
	var days = $('#creditDays').val();
	if(days<0){
		alert('Number cannot be less than 0');
		return;
	}
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
			'days':days,
		},
		beforeSend:function(){
			$('#btncreditdays').val('please wait...');
		},
		success: function (data) {
			$('#creditDays').val(data.data);
			$('#btncreditdays').prop('disabled',false);
			$('#btncreditdays').val('Update');
			console.log('ajax succeed');
			if(data.status==true){
				alert('Credit days Updated');
			}
		},
		error:function(error){
			console.log('ajax failed');
			$('#btncreditdays').val('Update');
			$('#btncreditdays').prop('disabled',false);
		}
	});
});