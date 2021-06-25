@extends('layouts.app-login')

@section('title', 'Authentication')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <a href="{{ asset('assets/index2.html') }}"><b>DeltaSales<span>APP</span></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg">Authentication</p>
      @if (session('status'))
        <div class="alert alert-success">
          {{ session('status') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
            {{ $error }}
          @endforeach
        </div>
      @endif
      <form action="{{ route('app.login.post') }}" method="post">
        {{ csrf_field() }}
        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
          <input id="email" type="email" class="form-control" name="email" placeholder="Email"
                 value="{{ old('email') }}" required autofocus>
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        @if ($errors->has('email'))
          <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
        <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
          <input id="password" type="password" class="form-control" name="password" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        @if ($errors->has('password'))
          <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
        @endif
        <div class="row">
          <div class="col-xs-8">
            <div class="checkbox icheck">
              <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <a href="#">I forgot my password</a><br><br>
      <span class="text-muted">
      &copy; {{ date('Y') }} <a href="http://deltasalesapp.com">DeltaSalesApp</a>. All rights reserved.<br>
      
         Developed by <a href="https://deltatechnepal.com" title="Delta Tech Nepal" target="_blank">Delta Tech Nepal</a>.
                        </span>

    </div>
    <!-- /.login-box-body -->
  </div>
@endsection