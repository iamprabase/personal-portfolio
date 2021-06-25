@extends('layouts.app')

@section('title', 'Create Plan')
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



  .mb-15 {
    margin-bottom: 25px;
    display: inline-flex;
  }
</style>
@endsection

@section('content')
<section class="content">

  <!-- SELECT2 EXAMPLE -->
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Create Company Plan</h3>

      <div class="box-tools pull-right">
        <div class="col-md-7 page-action text-right">
          <a href="{{ route('app.custom-plan.index') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i>
            Back</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      {!! Form::open(['route' => ['app.custom-plan.store'],'autocomplete'=>'off' ]) !!}
      <div class="col-xs-12">
      @include('admin.custom-plans._form')
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
  const allModules = @json($modules);
  const totalModule = allModules.length;

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

    $('.inputDefaultPrice').keyup(function(e){
      let value = Math.round(e.target.value, 2);
      if(/^[0-9]+\.?[0-9]*?$/.test(value)){
        $(this).css("border-color", "#d2d6de") 
        return;
        }

      $(this).css("border-color", "red") 
    });

    CKEDITOR.replace('description');

  });

  $(document).ready(function () {
    allModules.map((module, index) => {
      let checkBox = `<input class="switches ${module.field}" name="module[]" id="${module.field}" type="checkbox" value="${module.id}"/>`
      let tData = `<div class="col-xs-4 mb-15"><span>${checkBox}</span><span style="margin: 0px 0px 0px 10px;"> ${module.name}</span></div>`;
      $('.moduleListing').append(tData);
    });
  });
  
  const switchDependentModules = (moduleList, parentCheckedStatus) => {
    let childCheckedStatus = parentCheckedStatus ? true : false;
    moduleList.map(moduleClassName => {
      $('.moduleListing').find(`#${moduleClassName}`).prop('checked', childCheckedStatus);
    });
  }
  
  $(document).on('click', '.toggle-all-switches', function () {
    if ($(this).is(':checked')) {
      $('.switches').prop('checked', true);
    } else {
      $('.switches').prop('checked', false);
    }
  });
  $(document).on('click', '.switches', function () {
    let currentTarget = $(this);
    let isChecked = currentTarget.is(':checked');
    let modules = null;

    if (isChecked) {
      if (currentTarget.hasClass('analytics')) {
        modules = new Array('party', 'orders', 'collections', 'product', 'beat', 'leaves');
      } else if (currentTarget.hasClass('ageing')) {
        modules = new Array('party', 'orders', 'collections', 'product', 'accounting');
      } else if (currentTarget.hasClass('accounting') || currentTarget.hasClass('dpartyreport') || currentTarget.hasClass('dempreport')) {
        modules = new Array('party', 'product', 'orders', 'collections');
      } else if (currentTarget.hasClass('zero_orders') || currentTarget.hasClass('dso') || currentTarget.hasClass('dsobyunit') || currentTarget.hasClass('ordersreport') || currentTarget.hasClass('psoreport') || currentTarget.hasClass('spwise')) {
        modules = new Array('party', 'product', 'orders');
      } else if (currentTarget.hasClass('collections') || currentTarget.hasClass('orders') || currentTarget.hasClass('returns') || currentTarget.hasClass('stock_report')) {
        modules = new Array('party', 'product');
      } else if (currentTarget.hasClass('pdcs')) {
        modules = new Array('party', 'collections');
      } else if (currentTarget.hasClass('notes') || currentTarget.hasClass('beat') || currentTarget.hasClass('product')) {
        modules = new Array('party');
      } else if (currentTarget.hasClass('gpsreports')) {
        modules = new Array('livetracking');
      }
    } else {
      if (currentTarget.hasClass('party')) {
        modules = new Array('orders', 'notes', 'collections', 'pdcs', 'beat', 'returns', 'stock_report', 'dso', 'dsobyunit', 'ordersreport', 'psoreport', 'spwise', 'dpartyreport', 'dempreport', 'accounting', 'product', 'analytics', 'zero_orders', 'ageing');
      } else if (currentTarget.hasClass('livetracking')) {
        modules = new Array('gpsreports');
      } else if (currentTarget.hasClass('orders')) {
        modules = new Array('analytics', 'accounting', 'zero_orders', 'ageing', 'dso', 'dsobyunit', 'ordersreport', 'psoreport', 'spwise', 'dpartyreport', 'dempreport');
      } else if (currentTarget.hasClass('collections')) {
        modules = new Array('analytics', 'accounting', 'dpartyreport', 'dempreport', 'ageing');
      } else if (currentTarget.hasClass('product')) {
        modules = new Array('analytics', 'orders', 'accounting', 'zero_orders', 'ageing', 'dso', 'dsobyunit', 'ordersreport', 'psoreport', 'spwise', 'dpartyreport', 'dempreport');
      } else if (currentTarget.hasClass('accounting')) {
        modules = new Array('ageing');
      } else if (currentTarget.hasClass('beat')) {
        modules = new Array('analytics');
      } else if (currentTarget.hasClass('leaves')) {
        modules = new Array('analytics');
      }
    }

    if (modules) switchDependentModules(modules, isChecked);
    if ($(".switches:not(:checked)").length == 0) $(".toggle-all-switches").prop('checked', true);
    else $(".toggle-all-switches").prop('checked', false);
    

  });
</script>

@endsection