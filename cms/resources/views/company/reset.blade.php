@extends('layouts.company-login')

@section('title', 'Authentication')

@section('content')
  
  <div class="login-box">
    <div class="login-logo">
      @if(config('settings.logo_path') && file_exists(URL::asset('cms'.config('settings.logo_path'))) )
        <img src="{{ URL::asset('cms'.config('settings.logo_path')) }}" style="max-height: 150px;">
      @else
        {{ ucfirst(config('settings.title')) }}
      
      @endif
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
            {{ $error }}
          @endforeach
        </div>
      @endif
      @if(session()->has('verify') && session()->get('verify')!='')
        <div class="alert alert-success">
          {{session()->get('verify')}}
        </div>
      @endif
      <p class="login-box-msg">Sign in to start your session</p>
      
      <form action="{{ domain_route('company.password.update') }}" method="post">
        @csrf
      <input type="hidden" name="token" value="{{$token}}">
       <input type="hidden" id="email" name="email" value="{{ $email ?? old('email') }}">
      
        <div class="form-group has-feedback">
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="New Password" required autocomplete="current-password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
         
        </div>
        <div class="form-group has-feedback">
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          <!-- @if ($errors->has('password'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
          @endif -->
        </div>
        <div class="row" style="margin-bottom:20px;">
          <div class="col-xs-6">
            <a href="{{domain_route('company.login')}}">Login</a>
          </div>
          <!-- /.col -->
          <div class="col-xs-6">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Reset Password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
  
      
      <span class="text-muted">
      &copy; {{ date('Y') }} <a href="https://deltasalesapp.com">DeltaSalesApp</a>. All rights reserved.<br>
      
         Developed by <a href="https://deltatechnepal.com" title="Delta Tech Nepal" target="_blank">Delta Tech Nepal</a>.
                        </span>
    
    </div>
    <!-- /.login-box-body -->
  </div>

@endsection