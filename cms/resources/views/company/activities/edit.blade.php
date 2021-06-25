@extends('layouts.company')
@section('title', 'Activity Edit')
@section('stylesheets')
  <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  @if(config('settings.ncal')==1)
    <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
    <link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif
@endsection
@section('customstyles')
@endsection
@section('content')
  <div class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="box-header with-border">
            <h3 class="box-title">Update Activity</h3>            
            <div class="box-tools pull-right">
              <div class="col-md-7 page-action text-right">
                <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="box-body">
        {!! Form::model($row, ['method' => 'PATCH','id'=>'frmAddEdit', 'autocomplete' => 'off' , 'url' => url(domain_route('company.admin.activities.update',  [$row->id ])) ]) !!}
        {{  Form::hidden('url',URL::previous())  }}
        <div class="col-xs-12 ">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="" class=" control-label">Type<span
                      style="color: red">*</span></label></div>
              <div class="col-xs-6 activity-tab">
                <ul class="nav nav-tabs" id="myTab" role="tablist" required>
                  @php $i=0;  @endphp

                  @if($editAccess=='true')
                    @if($activityType->count()>0)
                      @foreach($activityType as $activityTypeRow)
                        <li class="nav-item">
                          <a class="nav-link  {{($row->type==$activityTypeRow->id)?'active':''}} type"
                             id="{{$activityTypeRow->id}}-tab" name="{{$activityTypeRow->id}}" data-toggle="tab"
                             href="#{{$activityTypeRow->id}}" role="tab" aria-controls="call" aria-selected="true"
                             aria-expanded="true">{{$activityTypeRow->name}}</a>
                        </li>
                        @php $i++; @endphp
                      @endforeach
                    @endif
                  @else
                    @if($activityType->count()>0)
                      @foreach($activityType as $activityTypeRow)
                        <li class="nav-item">
                          <a class="nav-link {{($row->type==$activityTypeRow->id)?'active':''}}">{{$activityTypeRow->name}}</a>
                        </li>
                        @php $i++; @endphp
                      @endforeach
                    @endif
                  @endif
                </ul>
                <input type="hidden" name="type" id="type" value="{{$row->type}}">
              </div>
            </div>
          </div>
        </div>

        
        <div class="col-xs-12 ">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="" class=" control-label">Title<span
                      style="color: red">*</span></label></div>
              <div class="col-xs-6">
              @if($editAccess=='true')
                {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control ','required','maxlength'=>191,'minlength'=>getSettings('minimum_characters'),'id'=>'title')) !!}
              @else
                {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control ','required','readonly','maxlength'=>191,'minlength'=>getSettings('minimum_characters'),'id'=>'title')) !!}
              @endif
              </div>
            </div>
          </div>
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3  text-right">
                <label for="" class=" control-label">Notes</label>
              </div>
              <div class="col-xs-6">
                {!! Form::textarea('note', null, array('placeholder' => 'Note','class' => 'form-control ckeditor','minlength'=>getSettings('minimum_characters'),'id'=>'note')) !!}
              </div>
            </div>
          </div>
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"></div>
              
              <div class="col-xs-2">
                <label for="duration">Date <span style="color: red">*</span></label>
                @if($editAccess)
                  @if(config('settings.ncal')==0)
                    <input placeholder="Enter date" class="form-control datepicker" id="start_date" name="start_date" type="text" required value="{{Carbon\Carbon::parse($row->start_datetime)->format('Y-m-d')}}">
                  @else
                  <input type="text" id="start_date_np" class="form-control" required />
                  <input placeholder=""  id="start_date_ad" autocomplete="off" name="start_date" type="text" value="{{Carbon\Carbon::parse($row->start_datetime)->format('Y-m-d')}}" hidden>
                  @endif
                @else
                  @if(config('settings.ncal')==0)
                    <input disabled placeholder="" class="form-control datepicker" id="start_date" autocomplete="off" name="start_date" type="text" required value="{{Carbon\Carbon::parse($row->start_datetime)->format('Y-m-d')}}">
                  @else
                  <input disabled type="text" id="start_date_np" class="form-control" required />
                  <input disabled placeholder=""  id="start_date_ad" autocomplete="off" name="start_date" type="text" value="{{Carbon\Carbon::parse($row->start_datetime)->format('Y-m-d')}}" hidden>
                  @endif
                @endif
              </div>
              <div class="col-xs-2">
                <div class="bootstrap-timepicker">
                  <label for="duration">Time <span style="color: red">*</span></label>
                  @if($editAccess=='true')
                    <input placeholder="" class="form-control timepicker2" id="start_time" name="start_time" type="text" value="{{Carbon\Carbon::parse($row->start_datetime)->format('h:i A')}}" />
                  @else
                    <input disabled placeholder="" class="form-control timepicker2" id="start_time" name="start_time" type="text" value="{{Carbon\Carbon::parse($row->start_datetime)->format('h:i A')}}" />
                  @endif
                </div>
              </div>
              <div class="col-xs-2">
                <label for="duration">Duration
                  <small>(in mins)</small>
                  <span style="color: red">*</span> </label>
                  @if($editAccess=='true')
                  <input type="number" class="form-control" id="duration" name="duration" min="1" value="{{$row->duration}}">
                  @else
                  <input disabled type="number" class="form-control" id="duration" name="duration" min="1" value="{{$row->duration}}">
                  @endif
              </div>
            </div>
          </div>
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="">Priority <span style="color: red">*</span></label></div>
              <div class="col-xs-6">
                <select class="form-control select2" required name="priority" id="priority" @if($editAccess!='true') disabled @endif>
                  <option value="">Select priority</option>
                  @if(count($activityPriorities)>0)
                    @foreach($activityPriorities as $key=>$val)
                      <option value="{{$key}}" {{ ($row->priority==$key)?'selected="selected"':''}} >{{$val}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="">Assign To <span style="color: red">*</span></label></div>
              <div class="col-xs-6">
                <select class="form-control select2" required name="assigned_to" id="assigned_to" @if(isset($row->assigned_to) && $row->assigned_to==Auth::user()->employeeId() && $row->created_by!=Auth::user()->employeeId()) disabled  @endif>
                  <option value="">Select assign to</option>
                  @if(count($users)>0)
                    @foreach($users as $user)
                      <option
                          value="{{$user->id}}" {{ ($row->assigned_to==$user->id)?'selected="selected"':''}}>{{$user->name}}</option>
                    @endforeach
                  @endif
                </select>
                  @if(isset($row->assigned_to) && $row->assigned_to==Auth::user()->employeeId()) 
                    <input type="text" name="assigned_to" value="{{$row->assigned_to}}" hidden>
                  @endif
                <input type="text" name="previous_url" value="{{URL::previous()}}" hidden>
              </div>
            </div>
          </div>
          @if(config('settings.party')==1)
          <input type="hidden" name="linkedTo" id="linkedTo" value="{{($row->client_id!='')?'party':'none'}}">
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for="">Link To </label></div>
              <div class="col-xs-6">
                

                @if($editAccess=='true')
                <ul class="nav nav-tabs" id="linkto" role="tablist">
                  <li class="nav-item {{($row->client_id=='')?'active':''}}">
                    <a class="nav-link  tn btn-primary @if($editAccess=='true')linkedTo @endif" id="none-tab"
                       data-toggle="tab" href="#none" role="tab" aria-controls="none" name="none" aria-selected="true">None</a>
                  </li>
                  
                  <li class="nav-item {{($row->client_id!='')?'active':''}}">
                    <a class="nav-link tn btn-primary @if($editAccess=='true')linkedTo @endif" id="party-tab"
                       data-toggle="tab" href="#party" role="tab" aria-controls="party" name="party"
                       aria-selected="false">Party</a>
                  </li>
                </ul>
                @else
                <ul class="nav nav-tabs" id="linkto" role="tablist">
                  <li class="nav-item {{($row->client_id=='')?'active':''}}">
                    <a class="nav-link  tn btn-primary" id="none-tab" aria-controls="none" name="none" aria-selected="true">None</a>
                  </li>
                  
                  <li class="nav-item {{($row->client_id!='')?'active':''}}">
                    <a class="nav-link tn btn-primary" id="party-tab"  role="tab" aria-controls="party" name="party"
                       aria-selected="false">Party</a>
                  </li>
                </ul>
                @endif

                <div class="tab-content" id="myTabContent">
                    @if($row->client_id!=NULL)
                      <div class="tab-pane fade" id="none" role="tabpanel" aria-labelledby="none-tab"></div>
                      <div class="tab-pane fade active in" id="party" role="tabpanel"
                     aria-labelledby="contact-tab">
                    @else
                      <div class="tab-pane fade active in" id="none" role="tabpanel" aria-labelledby="none-tab"></div>
                      <div class="tab-pane fade" id="party" role="tabpanel"
                     aria-labelledby="contact-tab">
                    @endif
                    <div class="position-relative has-icon-left">
                      <select class="form-control select2" name="client_id" id="client_id" @if($editAccess!="true") disabled @endif>
                        <option value="">Select Party</option>
                        @if(count($clients)>0)
                          @foreach($clients as $client)
                            <option @if($client->id==$row->client_id) selected="selected" @endif value="{{$client->id}}">{{$client->company_name}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3 text-right"><label for=""> </label></div>
              <div class="col-xs-6">
                <div class="checkbox">
                  <label>
                    @if(Auth::user()->can('activity-status'))
                      <input type="checkbox" id="status" name="status" value="completed" {{ ($row->completion_datetime!='')?'checked="checked"':''}}> 
                      Mark as
                    Completed*
                    @else 
                      <input type="checkbox" id="status" name="status" value="completed" {{ ($row->completion_datetime!='')?'checked="checked"':''}} hidden disabled>
                      <input disabled type="checkbox" value="completed" {{ ($row->completion_datetime!='')?'checked="checked"':''}}>
                      Mark as
                    Completed*
                    @endif
                    
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group ">
            <div class="row">
              <div class="col-xs-3"></div>
              <div class="col-xs-6">
                <input class="btn btn-primary pull-left" type="submit" id='btnSave' value="Update Activity">
              </div>
            </div>
          </div>
        </div>
        
        {!! Form::close() !!}
        <input type="text" id="created_by" value="{{$row->created_by}}" hidden>
      </div>  <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
@endsection
@section('scripts')
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript">
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

    $('.timepicker2').timepicker({
      showInputs: false
    });
    $('.select2').select2();

    var today = moment().format('YYYY-MM-DD');

    @if(config('settings.ncal')==0)

    $('.datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true,
    });
      @if($editAccess==null)  
      $('.datepicker').datepicker('setDate', today).on('changeDate', function (e) {
        $('#start_date_ad').val($('#start_date').val());
      });
      @endif   // Here the current date is set
    @else
    var tempdate = '{{$row->start_datetime}}';
    tempdate = tempdate.split(" ");
    ntempdate = AD2BS(tempdate[0]);
    $('#start_date_np').val(ntempdate);
    $('#start_date_np').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange: function(){
        $('#start_date_ad').val(BS2AD($('#start_date_np').val()));
      }
    });
    @endif


    $('.form-control').on('click', function () {
      currentElement = $(this);
      currentElement.parent().parent().parent().parent().removeClass('has-error');
      $('.' + currentElement.attr('name')).remove();
    });

    $("#frmAddEdit").on('submit', (function (e) {
      e.preventDefault();
      currentElement = $(this);
      var note = CKEDITOR.instances['note'].getData();
      $('#note').val(note);
      var formdata = new FormData(this);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: currentElement.attr('action'),
        type: "POST",
        data: formdata,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          $('#btnSave').val('Please wait...');
          $('#btnSave').attr('disabled', 'disabled');
        },
        success: function (data) {
          $('#btnSave').attr('disabled', 'disabled');
          window.location.href = "{{URL::previous()}}";
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

          $('#btnSave').val('Update Activity');
          $('#btnSave').removeAttr('disabled');
        },
        complete: function () {
          $('#btnSave').val('Update Activity');
          // $('#btnSave').removeAttr('disabled');
        }
      });
    }));

  });
</script>
@endsection