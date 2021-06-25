@extends('layouts.app')

@section('title', 'Create Subscription')
@section('stylesheets')
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<style>
  .icheckbox_minimal-blue {
    margin-top: -2px;
    margin-right: 3px;
  }

  .checkbox label,
  .radio label {
    font-weight: bold;
  }

  .has-error {
    color: red;
  }

  input[type=checkbox] {
    /* Double-sized Checkboxes */
    -ms-transform: scale(1.4);
    /* IE */
    -moz-transform: scale(1.4);
    /* FF */
    -webkit-transform: scale(1.4);
    /* Safari and Chrome */
    -o-transform: scale(1.4);
    /* Opera */
    transform: scale(1.4);
    padding: 10px;
  }

  .flex{
    display: flex;
    align-items: center;
  }

  .mr-15{
    margin-right: 15px;
  }

  .mb-15 {
    margin-bottom: 25px;
    display: inline-flex;
  }

  .mt-30{
    margin-top: 30px;
  }
</style>
@endsection

@section('content')
<section class="content">

  <!-- SELECT2 EXAMPLE -->
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Create Subscription</h3>
      @if($errors->first())
      <div class="alert alert-warning">

        <p>{{ $errors->first('error') }}</p>

      </div><br/>
      @endif

      <div class="box-tools pull-right">
        <div class="col-md-7 page-action text-right">
          <a href="{{ route('app.subscription.index') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i>
            Back</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      {!! Form::open(['route' => ['app.subscription.store'],'autocomplete'=>'off' ]) !!}
      <div class="col-xs-12">
      @include('admin.subscriptions._form')
      </div>  
      <!-- Submit Form Button -->
      {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right']) !!}
      {!! Form::close() !!}

    </div>
  </div>

</section>


@endsection

@section('scripts')

<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

<script>
  const removeField = e => {
    // if($('.extraCharge tbody').length == 1) return;
    e.parentElement.parentElement.remove();
  }
  const addField = () => { 
    let chargeIndexes = `<input type="hidden" name="chargeIndexes[]">`;
    let chargeNameField = '{!! Form::text("charge_name[]", isset($subscription)? $subscription->charge_name : null, ["class" => "form-control", "placeholder" => "Charge Title", "required" => true]) !!}';
    let chargePriceField = '{!! Form::text("charge_price[]", isset($subscription)? $subscription->charge_price : null, ["class" => "form-control priceValidate", "placeholder" => "Charge", "required" => true]) !!}';
    let chargeTypeField = '{!! Form::select("price_type[]", array("Per User"=>"Per User", "Fixed"=>"Fixed"), isset($subscription) ? $subscription->price_type : old("price_type"), ["class" => "form-control", "placeholder" => "Select Charge Type", "required" => true]) !!}';
    let removeButton = '{!! Form::button("Remove Field", ["class" => "btn btn-danger pull-right removeFieldBtn", "onclick" => "removeField(this)"]) !!}';
    $('.extraCharge tbody').append(`<tr>
      ${chargeIndexes}
      <td>${chargeNameField}</td>
      <td>${chargePriceField}</td>
      <td>${chargeTypeField}</td>
      <td>${removeButton}</td>
    </tr>`);
  }
  $(function () {
    $('#start_date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
    });
    $('#end_date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
    });
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });

    $('.priceValidate').keyup(function(e){
      let value = Math.round(e.target.value, 2);
      if(/^[0-9]+\.?[0-9]*?$/.test(value)){
        $(this).css("border-color", "#d2d6de") 
        return;
      }

      $(this).css("border-color", "red") 
    });

    $('.inputSubscriptionDomain').keyup(function(e){
      let value = e.target.value;
      if(/^[a-zA-Z]+$/.test(value)){
        $(this).css("border-color", "#d2d6de") 
        return;
      }
      $(this).css("border-color", "red") 
      
    }); 

    $('.inputSubscriptionPhone').keyup(function(e){
      let value = e.target.value;
      if(/^[0-9]{10,15}$/.test(value)){
        $(this).css("border-color", "#d2d6de") 
        return;
      }
      $(this).css("border-color", "red") 
      
    }); 

    $('input[name="expiry_after_current_billing"]').on('ifClicked', function(e){
      if(this.value == 1) $('.autoRnTime').addClass('hidden');
      else $('.autoRnTime').removeClass('hidden');
    });
  });
</script>

@endsection