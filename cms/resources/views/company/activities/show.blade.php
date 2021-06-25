@extends('layouts.company')
@section('title', 'Show Activity')
@section('pagestyles')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/dist/css/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
@endsection
<style>
  .delete, .edit{
    font-size: 15px !important;
  }
  .fa-edit, .fa-trash-o{
    padding-left: 5px;
  }

  .btn-warning{
    margin-right: 2px !important;
    color: #fff!important;
    background-color: #ec971f!important;
    border-color: #d58512!important;
  }
</style>
@section('customstyles')
@endsection
@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        @if (\Session::has('success'))
        <div class="alert alert-success">
          <p>{{ \Session::get('success') }}</p>
        </div><br />
        @endif
        @if (\Session::has('alert'))
        <div class="alert alert-warning">
          <p>{{ \Session::get('alert') }}</p>
        </div><br />
        @endif
        <div class="box">
          <div class="row">
            <div class="col-sm-12">
              <div class="box-header with-border">                  
                <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
                <div class="box-tools pull-right">                    
                  <div class="col-md-7 page-action text-right" style="display: flex;">
                    {!!$action!!}                              
                  </div>                  
                </div>                
              </div>
              <div class="box-header with-border">
                <h3 class="box-title">Activity Information</h3>
                <div class="box-tools pull-right">
                  {{-- <div class="col-md-7 page-action text-right">
                    {!!$action!!}
                    <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
                  </div> --}}
                </div>
              </div>

              <div class="box-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="table-responsive show-tab collection-details">
                      <table id="example" class="table product-detail collection-view">
                        <thead style="display: none;">
                          <tr>
                            <th></th>
                            <th>
                              <div class="dropdown" id="colvis"></div>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <th scope="row"> Title</th>
                            <td>{{$row->title}}</td>
                          </tr>
                          <tr>
                            <th scope="row"> Type</th>
                            <td>@if(isset($row->activityType->name)){{$row->activityType->name}}@else {{$row->type}} @endif</td>
                          </tr>
                          <tr>
                            <th scope="row"> Notes</th>
                            <td>
                              @if(isset($row->note)) 
                              {!!strip_tags($row->note)!!}
                              @else
                              N/A
                              @endif
                            </td>
                          </tr>
                          <tr>
                            <th scope="row"> Priority</th>
                            <td>@if(isset($row->activityPriority->name)){{ $row->activityPriority->name }} @endif</td>
                          </tr>
                          <tr>
                            <th scope="row"> Date</th>
                            <td>{{getDeltaDate(Carbon\Carbon::parse($row->start_datetime)->format('Y-m-d'))}} {{Carbon\Carbon::parse($row->start_datetime)->format('h:i A')}}</td>
                          </tr>
                          <tr>
                            <th scope="row"> Duration</th>
                            <td>{{$row->duration}} mins</td>
                          </tr>
                          <tr>
                            <th scope="row"> Assigned To</th>
                            <td>@if($row->assignedTo){{$row->assignedTo->name}}@else {{'All'}}@endif</td>
                          </tr>
                          <tr>
                            <th scope="row"> Assigned By</th>
                            <td>@if($row->created_by==0){{ucfirst(Auth::user()->managerName($row->company_id)['name'])}}@else {{$row->createdByEmployee->name}}@endif</td>
                          </tr>
                          @if(config('settings.party')==1)                          
                          <tr>
                            <th scope="row"> Link To</th>
                            <td>                    
                            @if($row->client_id!=NULL)
                              <a href="{{ domain_route('company.admin.client.show',[$row->client_id]) }}">{{$row->client->company_name}}</a>
                              @else
                              None
                            @endif</td>
                          </tr>
                          @endif
                          <tr>
                            <th scope="row"> Is it complete?</th>
                            <td>{{ ($row->completion_datetime!='')?'Yes':'No'}}</td>
                          </tr> 
                          @if(isset($row->completion_datetime) && isset($row->completed_by) && isset($row->completedByEmployee->name))
                          <tr>
                            <th scope="row"> Marked as Complete By</th>
                            <td>
                            @if(config('settings.ncal')==0)
                            {{$row->completedByEmployee->name}} at {{\Carbon\Carbon::parse($row->completion_datetime)->format('h:i A')}} on {{\Carbon\Carbon::parse($row->completion_datetime)->format('M d')}}
                            @else
                            {{$row->completedByEmployee->name}} at {{\Carbon\Carbon::parse($row->completion_datetime)->format('h:i A')}} on {{getDeltaDate(\Carbon\Carbon::parse($row->start_datetime)->format('Y-m-d'))}}
                            @endif
                            </td>
                          </tr>
                          @endif                   
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-keyboard="false" data-backdrop="static">
  
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
        </div>
  
        <form method="post" class="remove-record-model">
          {{method_field('delete')}}
          {{csrf_field()}}
          <div class="modal-body">
            <p class="text-center">
              Are you sure you want to delete this?
            </p>
            <input type="hidden" name="client_id" id="c_id" value="">
            <input type="hidden" name="return_url" id="return_url" value="{{URL::previous()}}">
          </div>
  
          <div class="modal-footer">
            <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
  
        </form>
      </div>
    </div>
  
  </div>
</section>
@endsection
@section('pagescripts')
@endsection
@section('customscripts')
@endsection
@section('scripts')
<script>
  $('#delete').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var mid = button.data('mid')
    var url = button.data('url');
    $(".remove-record-model").attr("action", url);
    var modal = $(this)
    modal.find('.modal-body #m_id').val(mid);
  });
</script>

@endsection