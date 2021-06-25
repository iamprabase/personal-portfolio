<div class="col-xs-9">
  <div class="tab-content" id="myTabContent">
    
    
    <div class="box-body tab-pane fade active in" role="tabpanel" id="updateProfile"
         aria-labelledby="updateProfile">
      <div class="col-xs-12">
        <h3 class="site-tital">Update Profile</h3>
      </div>
      <form name="updateprofile" id="updateprofile"
            action="{{ url(domain_route('company.admin.setting.updatedProfile',[$user->id])) }}"
            enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ method_field('patch')}}
        <div class="row">
          <div class="col-md-12">
          
          
          </div>
          <div class="col-xs-6 col-xs-offset-4" id="preview">
            <?php 
            $headers = get_headers(URL::asset('cms/storage/app/'.Auth::user()->profile_imagePath));
            $res = stripos($headers[0],"200 OK")?true:false; 
            ?>
            @if(Auth::user()->profile_imagePath && $res == true )
              <img src="{{ URL::asset('cms/storage/app/'.$user->profile_imagePath) }}" alt="Alternative Text"
                   style="height:150px;width:150px; border-radius: 50%;">
            @else
              <img src="{{ asset('assets/dist/img/admin-picture.png') }}" class="img-circle" alt="User Image" style="height:150px;width:150px; border-radius: 50%;">
            @endif
          </div>
          
          <!-- <div class="col-md-12" id="preview"> -->
        <!-- @if($user->profile_image)
          <img src="{{$user->profile_imagePath}}" alt="Profile Image">
               @endif -->
          
          <!-- </div> -->
          <div class="col-xs-4 col-xs-offset-4 custom-file">
            <div class="form-group @if ($errors->has('email')) has-error @endif">
              <span
                  class="custom-file-control form-control-file"><strong>{!! Form::label('profile_image', 'Profile Image') !!}</strong></span>
              <input type="file" id="image" name="image" class="custom-file-input" accept=".jpeg,.png,.jpg">
              @if ($errors->has('image')) <p class="help-block has-error">{{ $errors->first('image') }}</p> @endif
            </div>
          </div>
          <div class="col-xs-12">
            <div class="form-group @if ($errors->has('email')) has-error @endif">
              {!! Form::label('name', 'Name') !!}{{-- <span style="color: red">*</span><span style="color: green">*</span> --}}
              {!! Form::text('name', isset($user->name)? $user->name:null, ['class' => 'form-control', 'placeholder' => 'Name of the company', isset($setting->name)? 'disabled':'','required']) !!}
              @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
            </div>
          </div>
          <div class="col-xs-12">
            <div class="form-group @if ($errors->has('email')) has-error @endif">
              {!! Form::label('email', 'Email') !!}{{-- <span style="color: red">*</span><span style="color: green">*</span> --}}
              {!! Form::text('Email', isset($user->email)? $user->email:null, ['class' => 'form-control','readonly', 'placeholder' => 'Email Address', isset($setting->email)? 'disabled':'','required']) !!}
              @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
            </div>
          </div>
        
        </div>
      <!-- {{$user}} -->
        <button type="submit" class="btn btn-primary pull-right" name="submit" id="btnSave">Update Profile</button>
      </form>
    
    </div>
    
    
    <!-- <div class="tab-content" id="myTabContent"> -->
    <div class="box-body tab-pane fade" role="tabpanel" id="updatePassword" aria-labelledby="updatePassword">
      <div class="col-xs-12">
        <h3 class="site-tital">Change Password</h3>
      </div>
      <form name="changePassword" id="changePassword"
            action="{{ url(domain_route('company.admin.setting.updatedpassword')) }}" method="POST">
        {{ csrf_field() }}
        
        <div class="form-group">
          <label for="current_password" class="">Current Password<span class="astrik">*</span></label>
          <input type="password" name="current_password" id="current_password" class="text form-control"
                 placeholder="Current Password" required="" maxlength="">
        </div>
        <div class="form-group">
          <label for="password" class="">New Password<span class="astrik">*</span></label>
          <input type="password" name="password" id="password" class="text form-control" placeholder="New Password"
                 required="">
        </div>
        <div class="form-group">
          <label for="password_confirmation" class="">Confirm Password<span class="astrik">*</span></label>
          <input type="password" name="password_confirmation" id="password_confirmation" class="text form-control"
                 placeholder="Confirm Password" required="">
        </div>
        <button type="submit" class="btn btn-primary pull-right" name="submit" id="btnSave">Change Password</button>
      </form>
    
    </div>
    <!-- </div> -->
  
  
  </div>
</div>
