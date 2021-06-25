@extends('layouts.company')
@section('title', 'Create Activity')
@section('stylesheets')
  @if(config('settings.ncal')==1)
  <link rel="stylesheet"
        href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif
  <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

@endsection
@section('content')
  <div class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="box-header with-border">
            <h3 class="box-title">Create Activity</h3>
            <div class="box-tools pull-right">
              <div class="col-xs-7 page-action text-right">
                <a href="{{ domain_route('company.admin.activities.index') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="box-body">
        {!! Form::open(['url' =>url(domain_route('company.admin.activities.store')), 'id'=>'frmAddEdit', 'method' => 'post', 'autocomplete' => 'off']) !!}
        <div class="col-xs-12 ">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="" class=" control-label">Type<span style="color: red">*</span></label></div>
              <div class="col-xs-6 activity-tab">
                <ul class="nav nav-tabs" id="myTab" role="tablist" required="">
                  @php $i=0;  @endphp
                  @if($activityType->count()>0)
                    @foreach($activityType as $activityTypeRow)
                      <li class="nav-item">
                        <a class="nav-link {{($i==0)?'active':''}} type" id="{{$activityTypeRow->id}}-tab"
                           name="{{$activityTypeRow->id}}" data-toggle="tab" href="#{{$activityTypeRow->id}}" role="tab"
                           aria-controls="call" aria-selected="true" aria-expanded="true">{{$activityTypeRow->name}}</a>
                      </li>
                      @php $i++; @endphp
                    @endforeach
                  @endif
                </ul>
                <input type="hidden" name="type" id="type"
                       value="{{($activityType->count()>0)?$activityType[0]->id:NULL}}">
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="" class=" control-label">Title<span
                      style="color: red">*</span></label></div>
              <div class="col-xs-6">
                <div class="position-relative has-icon-left">
                  {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control ','required','maxlength'=>191,'id'=>'title')) !!}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3  text-right">
                <label for="" class=" control-label">Notes</label>
              </div>
              <div class="col-xs-6">
                <div class="position-relative has-icon-left">
                  <textarea name="note" id="note" class="form-control ckeditor"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12">
          <input type="hidden" name="start_date_ad" id="start_date_ad" value="{{date('Y-m-d')}}">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"></div>
              
              <div class="col-xs-2">
                <label for="duration">Date<span style="color: red">*</span></label>
                <div class="position-relative has-icon-left">
                  <input placeholder="" class="form-control datepicker" id="start_date" autocomplete="off"
                         name="start_date" type="text" required>
                </div>
              </div>
              <div class="col-xs-2">
                <div class="bootstrap-timepicker">
                  <label for="duration">Time <span style="color: red">*</span></label>
                  <div class="position-relative has-icon-left">
                    <input placeholder="" class="form-control timepicker2"
                           id="start_time" name="start_time" type="text">
                  </div>
                </div>
              </div>
              <div class="col-xs-2">
                <label for="duration">Duration
                  <small>(in mins)</small>
                  <span style="color: red">*</span> </label>
                <div class="position-relative has-icon-left">
                  <input type="number" class="form-control" id="duration" name="duration" min="1">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="">Priority <span style="color: red">*</span></label></div>
              <div class="col-xs-6">
                <div class="position-relative has-icon-left">
                  <select class="form-control select2" required name="priority" id="priority">
                    <option value="">Select priority</option>
                    @if(count($activityPriorities)>0)
                      @foreach($activityPriorities as $key=>$val)
                        <option value="{{$key}}">{{$val}}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="">Assign To <span style="color: red">*</span></label></div>
              <div class="col-xs-6">
                <select class="form-control select2" required name="assigned_to" id="assigned_to">
                  <option value="">Select assign to</option>
                  @if(count($users)>0)
                    @foreach($users as $user)
                      <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
        </div>
        @if(config('settings.party')==1)
        <div class="col-xs-12">
          <input type="hidden" name="linkedTo" id="linkedTo" value="none"/>
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="">Link To </label></div>
              <div class="col-xs-6">
                
                <ul class="nav nav-tabs" id="linkto" role="tablist">
                  <li class="nav-item active">
                    <a class="nav-link  tn btn-primary linkedTo " id="none-tab" data-toggle="tab" href="#none"
                       role="tab" aria-controls="none" name="none" aria-selected="true">None</a>
                  </li>
                  
                  <li class="nav-item">
                    <a class="nav-link tn btn-primary linkedTo" id="party-tab" data-toggle="tab" href="#party"
                       role="tab" aria-controls="party" name="party" aria-selected="false">Party</a>
                  </li>
                
                </ul>
                <div class="tab-content" id="myTabContent">
                  <div class="tab-pane fade show " id="none" role="tabpanel" aria-labelledby="none-tab"></div>
                  <div class="tab-pane fade" id="party" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="position-relative has-icon-left">
                      <select class="form-control select2" name="client_id" id="client_id">
                        <option value="">Select Party</option>
                        @if(count($clients)>0)
                          @foreach($clients as $client)
                            <option selected="selected" value="{{$client->id}}">{{$client->company_name}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="col-xs-12">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for=""> </label></div>
              <div class="col-xs-6">
                <div class="checkbox">
                  <label> 
                    @if(Auth::user()->can('activity-status'))
                    <input type="checkbox" id="status" name="status" value="completed"> Mark as Completed*
                    @else 
                      <input type="checkbox" id="status" name="status" value="completed" disabled>
                   
                      Mark as Completed*
                    @endif
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12">
          <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6">
              <input class="btn btn-primary pull-left" type="submit" id='btnSave' value="Add Activity">
            </div>
          </div>
        </div>
        {!! Form::close() !!}
        <input type="text" id="created_by" value="{{Auth::user()->EmployeeId()}}" hidden>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{ asset('assets/bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
<script>

    $(document).ready(function () {
        $('.type').on('click', function () {
            var currentElement = $(this);
            $('#type').val(currentElement.attr('name'));
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
        });

        $('.linkedTo').on('click', function () {
            var currentElement = $(this);
            $('#linkedTo').val(currentElement.attr('name'));
        });
        //Timepicker
        var minTime = moment().format('hh:mm A');
        $('.timepicker2').timepicker({
            showInputs: false,
            step:1,
        }).val(minTime);
        $('.select2').select2();

        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        @if(config('settings.ncal')==0)
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight: true,
        });
        $('.datepicker').datepicker('setDate', today).on('changeDate', function (e) {
            $('#start_date_ad').val($('#start_date').val());
        });   // Here the current date is set
        @else
        $('#start_date').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          onChange: function(){
            $('#start_date_ad').val(BS2AD($('#start_date').val()));
          }
        });
        $('#start_date').val(getNepaliDate());
        @endif

        $('.form-control').on('click', function () {
            currentElement = $(this);
            currentElement.parent().parent().parent().parent().removeClass('has-error');
            $('.' + currentElement.attr('name')).remove();
        });

        $("#frmAddEdit").on('submit', (function (e) {
            currentElement = $(this);
            var note = CKEDITOR.instances['note'].getData();
            $('#note').val(note);
            e.preventDefault();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: currentElement.attr('action'),
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    $('#btnSave').val('Please wait...');
                    $('#btnSave').attr('disabled', 'disabled');
                },
                success: function (data) {
                  $('#btnSave').attr('disabled', 'disabled');
                    window.location.href = data.url;
                },
                error: function (xhr) {
                    var i = 0;
                    for (var error in xhr.responseJSON.errors) {
                        if (i == 0) {
                            $('#' + error).focus();
                        }
                        $('#' + error).parent().parent().parent().parent().removeClass('has-error');
                        $('.' + error).remove();
                        $('#' + error).parent().parent().parent().parent().addClass('has-error');
                        $('#' + error).next().closest("div").after('<span class="help-block ' + error + '">' + xhr.responseJSON.errors[error] + '</span>');
                        i++;
                    }
                    $('#btnSave').val('Add Activity');
                    $('#btnSave').removeAttr('disabled');
                },
                complete: function (data) {
                    $('#btnSave').val('Add Activity');
                }
            });
        }));



    //responsive Time Picker
    $('#start_time').on('click',function(){
      if ($(window).width() <= 320) {   
        $(".bootstrap-timepicker-widget").addClass("activitytimeposition");        
      }
      else if ($(window).width() <= 375) {
        $(".bootstrap-timepicker-widget").addClass("activitytimeposition");
      }
      else if ($(window).width() <= 425) {
        $(".bootstrap-timepicker-widget").addClass("activitytimeposition");
      }
      else if ($(window).width() <= 768) {
        $(".bootstrap-timepicker-widget").addClass("activitytimeposition");
      }
      else if ($(window).width() <= 1024) {
        $(".bootstrap-timepicker-widget").addClass("activitytimeposition");
      }
      else {   
        $(".bootstrap-timepicker-widget").removeClass("activitytimeposition");
      }
    });

    });
</script>
@endsection