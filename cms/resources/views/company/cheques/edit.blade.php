@extends('layouts.company')
@section('title', 'Edit Cheque')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  @if(config('settings.ncal')==1)
    <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
    <link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif
@endsection

@section('content')

  <section class="content">

    <div class="box box-default">

      <div class="box-header with-border">

        <h3 class="box-title">Cheque Information</h3>

        <div class="box-tools pull-right">

          <div class="col-xs-7 page-action text-right">

            <a href="{{ domain_route('company.admin.cheque.index') }}" class="btn btn-default btn-sm"> <i

                  class="fa fa-arrow-left"></i> Back</a>

          </div>

        </div>

      </div>

      <div class="box-body">

      {!! Form::model($cheque, array('url' => url(domain_route('company.admin.cheque.update',[$cheque->id])) , 'method' => 'PATCH', 'autocomplete' => 'off', 'files'=> true)) !!}

      @include('company.cheques._form')

        {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}

        {!! Form::close() !!}

      </div>

    </div>

  </section>

@endsection

@section('scripts')

<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script>
  $('.DT_Cheq_FILTER').val(sessionStorage.getItem('DT_Cheq_filters'));

  $(function () {
    $('.select2').select2();
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });

    @if(config('settings.ncal')==0)
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true,
    });
    @else
    tempchequedate = '{{$cheque->cheque_date}}';
    if(tempchequedate==""){
      ntempdate = AD2BS(moment().format('YYYY-MM-DD'));
    }else{
      ntempdate = AD2BS(tempchequedate);
    }
    $('#cheque_date_np').val(ntempdate);
    $('#cheque_date_np').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange: function(){
        $('#cheque_date_eng').val(BS2AD($('#cheque_date_np').val()));
      }
    });
    temppaymentdate = '{{$cheque->payment_date}}';
    if(temppaymentdate==""){
      ntempdate = AD2BS(moment().format('YYYY-MM-DD'));
    }else{
      ntempdate = AD2BS(temppaymentdate);
    }
    $('#receive_date_np').val(ntempdate);
    $('#receive_date_np').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange: function(){
        $('#receive_date_eng').val(BS2AD($('#receive_date_np').val()));
      }
    });
    @endif
  });
</script>
@endsection