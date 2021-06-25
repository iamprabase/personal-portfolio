@extends('layouts.company-login')

@section('title', 'Authentication')

@section('content')
<section class="custom-register-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-10 col-centered">
        <div class="invoice">
          <div class="row no-gutters d-lg-flex">
            <div class="col-md-6 invoice-left text-center">
              <img src="{{asset('assets/dist/img/deltasales-logo.svg')}}" alt="Delta Sales App">
              <p>
                Field Sales Automation &amp; Employee Location Tracking App
              </p>
              <div class="d-flex-bgi-size" style="background-image: url({{asset('assets/dist/img/login.png')}})"></div>
            </div>

            <div class="invoice-right col-md-6 col-xs-10 col-centered">
              @if(session()->has('verify') && session()->get('verify')!='')
              <div class="valid-feedback">
                {{session()->get('verify')}}
              </div>
              @endif
              @if(session()->has('msg'))
              <div class="valid-feedback">
                {{session()->get('msg')}}
              </div>
              @endif
              @if(config('settings.logo_path') && file_exists(('cms/'.config('settings.logo_path'))) )
              <img src="{{ URL::asset('cms/'.config('settings.logo_path')) }}" alt="{{config('settings.title')}}"
                style="max-height: 150px;">
              @endif
              <h1>{{ ucfirst(config('settings.title')) }}</h1>
              <h3>Forgot Password</h3>
              <form action="{{ domain_route('company.forgotPassword') }}" method="post">
                {{ csrf_field() }}
      
                <div class="form-group">
                  <input type="text" class="form-control {{$errors->has('login_error')? 'invalid' :''}}"
                    id="emailAddress" placeholder="Email Address / Phone Number" name="username"
                    value="{{ old('username') }}" required />
          
                  @if ($errors->has('login_error'))
                    <div class="invalid-feedback">{{ $errors->first() }}</div>
                  @endif
                </div>
                <div class="form-group d-flex-custom">
                  <div>
                    <a href="{{domain_route('company.login')}}">Back to login</a>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                  Send Reset Link
                </button>
              </form>

              <div class="col-md-12 col-xs-12 col-centered">
                <span class="text-muted">
                  &copy; {{ date('Y') }} <a href="https://deltasalesapp.com">DeltaSalesApp</a>. All rights reserved.<br>
                  <div class="text-center">
                    Developed by <a href="https://deltatechnepal.com" title="Delta Tech Nepal" target="_blank">Delta
                      Tech Nepal</a>.
                </span>
              </div>
            
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
</section>
@endsection