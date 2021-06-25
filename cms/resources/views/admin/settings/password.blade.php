@extends('layouts.app')
@section('title', 'Password Settings')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <style>
    .icheckbox_minimal-blue {
      margin-top: -2px;
      margin-right: 3px;
    }

    .checkbox label, .radio label {
      font-weight: bold;
    }

    .has-error {
      color: red;
    }
  </style>
@endsection

@section('content')
  <section class="content">

    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="text-blue">Change Password</h3 class="text-blue">
      </div>
      <!-- /.box-header -->
      <div class="box-body">
		<form id="updateAdminPassword" action="{{route('app.update.password.change')}}" method="POST">
			@csrf
			<div class="row">
		  		<div class="col-xs-12">
		  			<b><label>Current Password<span class="text-red">*</span></label></b>
		  			<input id="currentPassword" class="form-control" type="password" name="current_password" value="" placeholder="Current Password" required>
		  		</div>
		  		<div class="col-xs-12">
		  			<b><label>New Password<span class="text-red">*</span></label></b>
		  			<input id="password" class="form-control" type="password" name="password" value="" placeholder="New Password" required>
		  		</div>
		  		<div class="col-xs-12">
		  			<b><label>Confirm Password<span class="text-red">*</span></label></b>
		  			<input id="confirm_password" class="form-control" type="password" name="password_confirmation" value="" placeholder="Confirm Password" required>
		  		</div>
		  		<div class="col-xs-12">
		  			<button id="cPassword" class="btn btn-primary" type="submit">Change Password</button>
		  		</div>
		  	</div>
		</form>
      </div>
    </div>

  </section>


@endsection

@section('scripts')
<script>

	$('#updateAdminPassword').on('submit',function(e){
		e.preventDefault();
		if($('#currentPassword').val()==$('#password').val()){
			alert("Current password should not be same as new password!");
		}else if($('#password').val() == $('#confirm_password').val()){
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
					$('#cPassword').attr('disabled','disabled');
				},
				success: function (data) {
					alert(data.message);
					if(data.status==true){
						window.location = "{{route('app.home')}}";
					}
					$('#cPassword').removeAttr('disabled');
				},
				error:function(data){
					console.log(data);
					$('#cPassword').removeAttr('disabled');
				}
			});

		}else{
			alert("Confirm Password didn't matched with new password");
		}

	});

	





</script>

 

@endsection