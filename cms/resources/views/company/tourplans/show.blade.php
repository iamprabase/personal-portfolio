@extends('layouts.company')

@section('title', 'Tour Plan')

@section('stylesheets')

<link rel="stylesheet"
href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  @if(config('settings.ncal')==1)
  <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
  <link rel="stylesheet"
    href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif
<style>


  .btn-sm{
      font-size: 14px;
      color: #fff;
    }

    .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }
  .btn-warning{
    margin-right: 2px !important;
    color: #fff!important;
    background-color: #ec971f!important;
    border-color: #d58512!important;
  }
</style>
@endsection



@section('content')

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @if (\Session::has('success'))
        <div class="alert alert-success">
          <p>{{ \Session::get('success') }}</p>
        </div><br />
        @endif
        @if (\Session::has('updated'))
        <div class="alert alert-success">
          <p>{{ \Session::get('updated') }}</p>
        </div><br />
        @endif
        @if (\Session::has('error'))
        <div class="alert alert-error">
          <p>{{ \Session::get('error') }}</p>
        </div><br />
        @endif
        @if (\Session::has('alert'))
        <div class="alert alert-warning">
          <p>{{ \Session::get('alert') }}</p>
        </div><br />
        @endif
      </div></div>
      <div id="myEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      <form class="form-horizontal" role="fvisit_purposeorm" id="updateTourplan" method="POST" action="{{domain_route('company.admin.tourplan.update', [$tourplan->id])}}" >
          {{csrf_field()}}
          {!! method_field('patch') !!}
        <input type="hidden" name="tourplan_id" id="tourplan_id" value="{{$tourplan->id}}">
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Place of Visit</label>
            <div class="col-sm-10">
            <input class="form-control" type="text" name="place_of_visit" id="place_of_visit" class="place_of_visit" value="{{$tourplan->visit_place}}" required>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Visit Purpose</label>
            <div class="col-sm-10">
            {{-- <input class="form-control" type="text" name="visit_purpose" id="visit_purpose" class="visit_purpose" value="{{$tourplan->visit_purpose}}"> --}}
            {!! Form::textarea('visit_purpose', $tourplan->visit_purpose, ['class' => 'form-control ckeditor visit_purpose', 'id=edit_visit_purpose']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Start Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
            <input class="form-control edit_date" type="text" name="start_date" id="edit_start_date" autocomplete="off" value="{{$tourplan->start_date}}" required>
              @else
                <input class="form-control edit_date" type="text" id="edit_start_ndate" autocomplete="off" value="{{$tourplan->start_date}}" required>
                <input class="form-control hidden" type="text" name="start_date" id="hidden_edit_start_ndate" value="{{$tourplan->start_date}}" autocomplete="off" >
              @endif
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">End Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
            <input class="form-control edit_date" type="text" name="end_date" id="edit_end_date" autocomplete="off" value="{{$tourplan->end_date}}" required>
              @else
                <input class="form-control edit_date" type="text" id="edit_end_ndate" autocomplete="off"  required value="{{$tourplan->end_date}}">
                <input class="form-control hidden" type="text" name="end_date" id="hidden_edit_end_ndate" autocomplete="off" value="{{$tourplan->end_date}}" >
              @endif
            </div>
          </div>
          {{-- <div class="form-group">
            <label class="control-label col-sm-2" for="id">Remark</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                rows="5"></textarea>
            </div>
          </div> --}}
          <div class="modal-footer">
            <button id="btn_status_change" type="submit" class="btn btn-primary actionBtn">
              <span id="footer_action_button" class='glyphicon'></span> Save
            </button>
            {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Close
            </button> --}}
          </div>
        </form>
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
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
    <form method="post" class="remove-record-model" action="{{domain_route('company.admin.tourplan.destroy', [$tourplan->id]) }}">
        {{method_field('delete')}}
        {{csrf_field()}}
        <div class="modal-body">
          <p class="text-center">
            Are you sure you want to delete this?
          </p>
        <input type="hidden" name="del_id" id="del_id" value="{{$tourplan->id}}">

        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
          <button type="submit" class="btn btn-warning delete-button" id="delBtn">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
   
    <div class="row">

      <div class="col-md-12">

        <div class="box box-default">

          <div class="box-header with-border">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i
              class="fa fa-arrow-left"></i> Back</a>
              <div class="page-action pull-right">
                {!!$action!!}
                </div>
                </div>
          <div class="box-header with-border">
            <h3 class="box-title">Tour Plan Details </h3>
          <!-- /.box-header -->
          <div class="page-action pull-right">
            <a id="downloadBtn" href="{{ domain_route('company.admin.tours.download',[$tourplan->id])}}" class="btn btn-default btn-sm"> <i
                class="fa fa-file-pdf-o"></i> PDF</a>
               <button class="btn btn-default btn-sm" onclick="printSingleOrder();" id="printBtn"><i
                 class="fa fa-print"></i> Print
               </button>
             </div>
             </div>
          
          <div class="box-body">

            {{-- <strong><i class="fa fa-book margin-r-5"></i>Purpose of Visit: </strong>

            <p class="text-muted">

              {{ $tourplan->visit_purpose }}

            </p> --}}
            <h4 class="box-title" ><b>Salesman Name:- {{ getEmployee($tourplan->employee_id)['name']}}</b></h4>

            <div class="table-responsive">

              <table class="table table-bordered table-striped">

                <colgroup>

                  <col class="col-xs-2">

                  <col class="col-xs-7">

                </colgroup>

                <tbody>

                <tr>

                  <th scope="row"> Visit Location</th>

                  <td>{{ $tourplan->visit_place}}</td>

                </tr>
                    <th scope="row"> Visit Purpose</th>
                    <td>{!! $tourplan->visit_purpose !!}</td>
  
                  </tr>

                <tr>

                  <th scope="row"> Date (From - To)</th>
                  @if(getClientSetting()->ncal==0)
                  <td>{{ date('d M Y', strtotime($tourplan->start_date)) }}

                    - {{ date('d M Y', strtotime($tourplan->end_date)) }}</td>
                  @else
                  <td>{{ getDeltaDate(date('Y-m-d', strtotime($tourplan->start_date))) }}

                    - {{ getDeltaDate(date('Y-m-d', strtotime($tourplan->end_date))) }}</td>
                  @endif

                </tr>

                <tr>

                  <th scope="row"> Number of Days</th>

                  <td>{{ getDays($tourplan->start_date,$tourplan->end_date) }}</td>

                </tr>

                <tr>

                  <th scope="row"> Date Created</th>

                  <td>{{ getDeltaDate(date('Y-m-d', strtotime($tourplan->created_at))) }}</td>

                </tr>

                <tr>

                  <th scope="row"> Status</th>

                  <td><span class="">

                    @if($tourplan->status =='Approved')

                        <span class="label label-success">{{ $tourplan->status}}</span>

                      

                      @elseif($tourplan->status =='Pending')

                        <span class="label label-warning">{{ $tourplan->status}}</span>



                      @else

                        <span class="label label-danger">{{ $tourplan->status}}</span>



                      @endif

                  </span></td>

                </tr>

                <tr>

                  <th scope="row"> Remarks:</th>

                  <td><span class="">

                    @if($tourplan->remark)

                        <span>{{ $tourplan->remark}}</span>

                      @else

                        <span></span>

                      @endif

                  </span></td>

                </tr>

                </tbody>

              </table>

            </div>

          </div>

          @if(config('settings.party')==1)
          @if( (config('settings.orders')==1 && Auth::user()->can('order-view')) || (config('settings.collections')==1 && Auth::user()->can('collection-view')) || (config('settings.expenses')==1 && Auth::user()->can('expense-view')))
          <div class="box-header">
            <h3 class="box-title">Tour Report</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-2"></div>
              <div class="col-sm-7">
                <div class="row">
                  <div class="select-2-sec">
                    <div class="col-sm-3">
                      <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="partyfilter"></div>
                    </div>
                    <div class="col-sm-3">
                      <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="salesmfilter"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-3"></div>
            </div>
            @if(getClientSetting()->ncal==0)
            <p class="box-title"><strong>{{ date('d M Y', strtotime($tourplan->start_date)) }} TO {{ date('d M Y', strtotime($tourplan->end_date)) }}</strong></p>
            @else
            <p class="box-title"><strong>{{ getDeltaDate(date('Y-m-d', strtotime($tourplan->start_date))) }} TO {{ getDeltaDate(date('Y-m-d', strtotime($tourplan->end_date))) }}</strong></p>
            @endif
            @if(config('settings.collections')==1 || config('settings.expenses')==1 || config('settings.orders')==1)
            <table id="order" class="table table-bordered table-striped table-responsive" style="width: 100%;">
              <thead>
              <tr>
                {{-- <th class="hidden">#</th> --}}
                @if(config('settings.orders')==1 && Auth::user()->can('order-view'))
                <th>Orders</th>
                @endif
                @if(config('settings.collections')==1 && Auth::user()->can('collection-view'))
                <th>Collection</th>
                @endif
                @if(config('settings.expenses')==1 && Auth::user()->can('expense-view'))
                <th>Expense</th>
                @endif
              </tr>
              </thead>
              <tbody>
                <tr>
                  @if(config('settings.orders')==1 && Auth::user()->can('order-view'))
                    <td>{{ config('settings.currency_symbol')}}. {{$sales_total}}</td>
                  @endif
                  @if(config('settings.collections')==1 && Auth::user()->can('collection-view'))
                    <td>{{ config('settings.currency_symbol')}}. {{$collection_total}}</td>
                  @endif
                  @if(config('settings.expenses')==1 && Auth::user()->can('expense-view'))                
                    <td>{{ config('settings.currency_symbol')}}. {{$expense_total}}</td>
                  @endif
                </tr>
              </tbody>
              <tfoot>
            </table>
            @endif
          </div>
          @endif
          @endif

        </div>

      </div>
    </div>
    <div id="printArea" hidden>
      @include('company.tourplans.download')
    </div>

    @php  $currency = config('settings.currency_symbol');@endphp
    @endsection 


    @section('scripts')
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

    {{-- <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script> --}}
      <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
      <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
      <script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
      <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

      <script>




        $('#order').DataTable({
          'searching': false,
          "paging": false,
          "bInfo" : false,
          "sorting": false,
        });

          $(function () {

              // $("#task").DataTable();



              $('#delete').on('show.bs.modal', function (event) {

                  var button = $(event.relatedTarget)

                  var mid = button.data('mid')

                  var modal = $(this)

                  modal.find('.modal-body #m_id').val(mid);

              })

          });
          

          function printSingleOrder() {
              var print_div = document.getElementById("printArea");
              var print_area = window.open();
              print_area.document.write(print_div.innerHTML);
              print_area.document.close();
              print_area.focus();
              print_area.print();
              print_area.close();
          }


          
          $('#tourplantbl').on('click','.update-modal',function(){
      const current = $('#myEditModal'); 
      current.modal('show');
      $('.modal-title').text('Edit Tourplan');
      current.find('#tourplan_id').val($(this).data('id'));
      current.find('#place_of_visit').val($(this).data('place_of_visit'));
      current.find('#visit_purpose').val($(this).data('visit_purpose'));
      @if(config('settings.ncal')==1)
        current.find('#edit_start_ndate').val(AD2BS($(this).data('start_date')));
        current.find('#edit_end_ndate').val(AD2BS($(this).data('end_date')));
      @else
        current.find('#edit_start_date').val($(this).data('start_date'));
        $('#edit_end_date').datepicker('destroy');
        $('#edit_end_date').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
          startDate: $(this).data('start_date')
        });
        current.find('#edit_end_date').val($(this).data('end_date'));
      @endif
      current.find('#remark').val($(this).data('remark'));
      current.find('#updateTourplan')[0].action = $(this).data('editurl');
      @if(config('settings.ncal')==1)
        current.find('#updateTourplan').on('submit', function(){
          const formEl = $(this);
          formEl.find('#hidden_edit_start_ndate').val(BS2AD($('#edit_start_ndate').val()));
          formEl.find('#hidden_edit_end_ndate').val(BS2AD($('#edit_end_ndate').val()));
        });
      @endif
    });
    $('#start_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          onChange:function(){
            $('#start_edate').val(BS2AD($('#start_ndate').val()));
            if($('#start_ndate').val()>$('#end_ndate').val()){
              $('#end_ndate').val($('#start_ndate').val());
              $('#end_edate').val(BS2AD($('#start_ndate').val()));
            }
            var empVal = $('.employee_filters').find('option:selected').val();
            if(empVal=="null"){
              empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(end==""){
              end = start;
            }
            if(start != '' || end != '')
            {
              $('#tourplantbl').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });
    $('#edit_start_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          disableBefore: neptoday,
          onChange:function(){
            if($('#edit_start_ndate').val()>$('#edit_end_ndate').val()){
              $('#edit_end_ndate').val($('#edit_start_ndate').val());
            }
          }
        });
        $('#edit_end_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          disableBefore: neptoday,
          onChange:function(){
            if($('#edit_end_ndate').val()<$('#edit_start_ndate').val()){
              $('#edit_start_ndate').val($('#edit_end_ndate').val());
            }
          }
        });

      
 </script>



@endsection