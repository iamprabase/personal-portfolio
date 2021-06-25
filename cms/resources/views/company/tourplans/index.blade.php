@extends('layouts.company')
@section('title', 'Tour Plans')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
    }

    .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }
    .hide_column {
      display: none;
    }
    .round {
      position: relative;
      width: 15px;
    }
    .round label {
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 50%;
      cursor: pointer;
      height: 15px;
      left: 0;
      position: absolute;
      top: 3px;
      width: 28px;
    }
    .round label:after {
      border: 2px solid #fff;
      border-top: none;
      border-right: none;
      content: "";
      height: 6px;
      left: 0px;
      opacity: 0;
      position: absolute;
      top: 3px;
      transform: rotate(-45deg);
      width: 12px;
    }
    .round input{
      height: 10px;
    }
    .round input[type="checkbox"] {
      visibility: hidden;
    }
    .round input[type="checkbox"]:checked + label {
      background-color: #66bb6a;
      border-color: #66bb6a;
    }
    .round input[type="checkbox"]:checked + label:after {
      opacity: 1;
    }
    .pad-left{
      padding-left: 0px;
    }

    .select-2-sec{
      display: flex;
      /* flex-wrap: wrap; */
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
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Tour Plans</h3>
          @if(Auth::user()->can('tourplan-create'))
          <a href="#" class="btn btn-primary pull-right" style="margin-left: 5px;" id="addTourPlan">
            <i class="fa fa-plus"></i> Create New
          </a>
          @endif
          <span id="tourplanexports" class="pull-right"></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs" style="min-width: 200px;margin-right: 5px">
                    <div style="margin-top:10px;height: 40px;z-index: 999 " id="empFilters"></div>
                  </div>
                  <div class="col-xs" style="min-width: 200px;margin-right: 5px">
                    <div id="statusfilter" style="margin-top: 10px;">
                      <select name='status' id='status_filters' class='status_filters select2'>
                        <option value=null>All</option>
                        <option value="Approved">Approved</option>
                        <option value="Pending">Pending</option>
                        <option value="Rejected">Rejected</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-xs">
                    @if(config('settings.ncal')==0)
                      <div id="reportrange" name="reportrange" class="reportrange hidden" style="margin-top: 10px; ">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                      </div>
                      <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                      <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                    @else
                      <div class="input-group hidden" id="nepCalDiv" style="margin-top: 10px;">
                        <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                          placeholder="Start Date" autocomplete="off" />
                        <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                        <span class="input-group-addon" aria-readonly="true"><i
                            class="glyphicon glyphicon-calendar"></i></span>
                        <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                          autocomplete="off" />
                        <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                        <button id="filterTable" style="color:#0b7676!important;" hidden><i class="fa fa-filter"
                            aria-hidden="true"></i></button>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-3"></div>
          </div>
          <div id="mainBox">
            <table id="tourplantbl" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Employee Name</th>
                  <th>Place of visit</th>
                  <th>From</th>
                  <th>To</th>
                  <th>No. of Days</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <div id="loader1" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
            </table>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="changeStatus" method="POST"
          action="{{URL::to('admin/tourplans/changeStatus')}}">
          {{csrf_field()}}
          <input type="hidden" name="tourplan_id" id="tourplan_id" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Remark</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                rows="5"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="status" name="status">
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
                <option value="Pending">Pending</option>
              </select>
            </div>
          </div>
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

<div id="myEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="updateTourplan" method="POST">
          {{csrf_field()}}
          {!! method_field('patch') !!}
          <input type="hidden" name="tourplan_id" id="tourplan_id" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Place of Visit</label>
            <div class="col-sm-10">
              <input class="form-control" type="text" name="place_of_visit" id="place_of_visit" class="place_of_visit" required>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Visit Purpose</label>
            <div class="col-sm-10">
              {{-- <input class="form-control" type="text" name="visit_purpose" id="visit_purpose" class="visit_purpose"> --}}
              {!! Form::textarea('visit_purpose', null, ['class' => 'form-control ckeditor visit_purpose', 'id=edit_visit_purpose']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Start Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
                <input class="form-control edit_date" type="text" name="start_date" id="edit_start_date" autocomplete="off" required>
              @else
                <input class="form-control edit_date" type="text" id="edit_start_ndate" autocomplete="off" required>
                <input class="form-control hidden" type="text" name="start_date" id="hidden_edit_start_ndate" autocomplete="off">
              @endif
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">End Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
                <input class="form-control edit_date" type="text" name="end_date" id="edit_end_date" autocomplete="off" required>
              @else
                <input class="form-control edit_date" type="text" id="edit_end_ndate" autocomplete="off" required>
                <input class="form-control hidden" type="text" name="end_date" id="hidden_edit_end_ndate" autocomplete="off">
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
          <input type="hidden" name="DT_Tour_FILTER" class="DT_Tour_FILTER">

        </form>
      </div>
    </div>
  </div>
</div>

<div id="myAddModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="addTourplan" method="POST">
          {{csrf_field()}}
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Place of Visit</label>
            <div class="col-sm-10">
              <input class="form-control" type="text" name="place_of_visit" id="place_of_visit" class="place_of_visit"
                required>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Visit Purpose</label>
            <div class="col-sm-10">
              {{-- <input class="form-control" type="text" name="visit_purpose" id="visit_purpose" class="visit_purpose"> --}}
              {!! Form::textarea('visit_purpose', null, ['class' => 'form-control ckeditor visit_purpose', 'id=visit_purpose']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Start Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
              <input class="form-control add_date" type="text" name="start_date" id="add_start_date"
                autocomplete="off" required>
              @else
              <input class="form-control add_date" type="text" id="add_start_ndate"
                autocomplete="off" required>
              <input class="form-control hidden" type="text" name="start_date" id="hidden_add_start_ndate" autocomplete="off">
              @endif
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">End Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
              <input class="form-control" type="text" name="end_date" id="add_end_date" autocomplete="off"
                required>
              @else
              <input class="form-control add_date" type="text" id="add_end_ndate" autocomplete="off"
                required>
              <input class="form-control hidden" type="text" name="end_date" id="hidden_add_end_ndate" autocomplete="off">
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

<div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center">Alert!</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Sorry! You are not authorized to update the status for the selected record.
        </p>
        <input type="hidden" name="expense_id" id="c_id" value="">
        <input type="text" id="accountType" name="account_type" hidden />
      </div>
      <div class="modal-footer">
        {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
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
      <form method="post" class="remove-record-model">
        {{method_field('delete')}}
        {{csrf_field()}}
        <div class="modal-body">
          <p class="text-center">
            Are you sure you want to delete this?
          </p>
          <input type="hidden" name="del_id" id="del_id" value="">

        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
          <button type="submit" class="btn btn-warning delete-button" id="delBtn">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
<form method="post" action="{{domain_route('company.admin.tourplan.customPdfExport')}}" class="pdf-export-form hidden"
  id="pdf-generate">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>
@endsection

@section('scripts')
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
  @if(config('settings.ncal')==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  @else
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  @endif
  
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
  <script>
      $(document).ready(function () {
                @if (strpos(URL::previous(), domain_route('company.admin.tours')) === false)
          var activeRequestsTable = $('#tourplantbl').DataTable();
          activeRequestsTable.state.clear();
          activeRequestsTable.destroy();
        @endif
      });
    $(function () {
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('.modal-body #m_id').val(mid);
      });

      @if(config('settings.ncal')==0)
        $(".edit_date").datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
          startDate: new Date()
        });
        $(".add_date").datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
          startDate: new Date()
        });
        var start = moment().subtract(3, 'months');
        var end = moment().add(3,'months');

        function cb(start, end) {
          $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#startdate').val(start.format('MMMM D, YYYY'));
          $('#enddate').val(end.format('MMMM D, YYYY'));
          $('#start_edate').val(start.format('Y-MM-DD'));
          $('#end_edate').val(end.format('Y-MM-DD'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $.fn.dataTable.ext.search.push(
          function (settings, data, dataIndex) {
            var start2 = $('#reportrange').data('daterangepicker').startDate;
            var end2 = $('#reportrange').data('daterangepicker').endDate;
            var start_date = Date.parse(start2.format('MMMM D, YYYY'));
            var end_date = Date.parse(end2.format('MMMM D, YYYY'));
            var from_date = Date.parse(data[3]);
            var to_date = Date.parse(data[4]);
            if (from_date >= start_date && to_date <= end_date) {
                return true;
            }
            return false;
          }
        );
        
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
          var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          $('#start_edate').val(start);
          $('#end_edate').val(end);
          var empVal = $('.employee_filters').find('option:selected').val();
          if(empVal=="null"){
            empVal = null;
          }
          
          var startD = $('#start_edate').val();
          var endD = $('#end_edate').val();
          sessionStorage.setItem('DT_Tour_filters', JSON.stringify({
            "empVal": empVal,
            "start": start,
            "end": end,
          }));
          if(startD != '' || endD != ''){
            $('#tourplantbl').DataTable().destroy();
            initializeDT(empVal, start, end);
          }
        });

        $('#reportrange').removeClass('hidden');

        $('#add_start_date').on('change', function(){
          let currentVal = $(this).val();
          let endDateVal = $('#add_end_date').val();
          $('#addTourplan').find('#add_end_date').val(currentVal);
          $('#addTourplan').find('#add_end_date').datepicker('destroy');
          $('#addTourplan').find('#add_end_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            startDate: currentVal
          });
        });

        $('#edit_start_date').on('change', function(){
          let currentVal = $(this).val();
          let endDateVal = $('#edit_end_date').val();
          $('#updateTourplan').find('#edit_end_date').val(currentVal);
          $('#updateTourplan').find('#edit_end_date').datepicker('destroy');
          $('#updateTourplan').find('#edit_end_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            startDate: currentVal
          });
        });
      @else
        var lastmonthdate = AD2BS(moment().subtract(3,'months').format('YYYY-MM-DD'));
        var ntoday = AD2BS(moment().add(3,'months').format('YYYY-MM-DD'));
        $('#start_ndate').val(lastmonthdate);
        $('#end_ndate').val(ntoday);
        $('#start_edate').val(BS2AD(lastmonthdate));
        $('#end_edate').val(BS2AD(ntoday));
        $('#nepCalDiv').removeClass('hidden');

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
            sessionStorage.setItem('DT_Tour_filters', JSON.stringify({
              "empVal": empVal,
              "start": start,
              "end": end,
            }));
            if(start != '' || end != '')
            {
              $('#tourplantbl').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });
        $('#end_ndate').nepaliDatePicker({
          onChange:function(){
            $('#end_edate').val(BS2AD($('#end_ndate').val()));
            if($('#end_ndate').val()<$('#start_ndate').val()){
              $('#start_ndate').val($('#end_ndate').val());
              $('#start_edate').val(BS2AD($('#end_ndate').val()));
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
            sessionStorage.setItem('DT_Tour_filters', JSON.stringify({
              "empVal": empVal,
              "start": start,
              "end": end,
            }));
            if(start != '' || end != '')
            {
              $('#tourplantbl').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });

        let today = moment().format('YYYY-MM-DD');
        let todayPlus = moment().format('YYYY-MM-DD');
        let neptoday = AD2BS(todayPlus);
            neptoday= neptoday.split('-');
        neptoday = neptoday[1]+'/'+neptoday[2]+'/'+neptoday[0];

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

        $('#add_start_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          disableBefore: neptoday,
          onChange:function(){
            if($('#add_start_ndate').val()>$('#add_end_ndate').val()){
              $('#add_end_ndate').val($('#add_start_ndate').val());
            }
          }
        });
        $('#add_end_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          disableBefore: neptoday,
          onChange:function(){
            if($('#add_end_ndate').val()<$('#add_start_ndate').val()){
              $('#add_start_ndate').val($('#add_end_ndate').val());
            }
          }
        });
      @endif

      var empSelect = "<select sname='employee' id='employee_filters' class='select2 employee_filters'><option></option><option value=null>All</option>@forelse($employeesWithTourPlans as $id=>$employee)<option value='{{$id}}'>{{$employee}}</option>@empty<option></option>@endforelse</select>";
      $('#empFilters').append(empSelect);

      var table;
      var startD = $('#start_edate').val();
      var endD = $('#end_edate').val();
      var empVal = null;
      @if (\Session::has('DT_Tour_filters'))
        let filtersSel = @json(\Session::get('DT_Tour_filters'));
        sessionStorage.setItem('DT_Tour_filters', filtersSel);
      @else
        sessionStorage.setItem('DT_Tour_filters', "");
      @endif
      if(sessionStorage.getItem('DT_Tour_filters')!="" || !sessionStorage.getItem('DT_Tour_filters')==undefined ){
        let filterValue = JSON.parse(sessionStorage.getItem('DT_Tour_filters'));
        if(filterValue){
          empVal = filterValue.empVal;
          startD = filterValue.start;
          endD = filterValue.end;
          @if($nCal==0)
            $('#start_edate').val(moment(startD).format('YYYY-MM-DD'));
            $('#end_edate').val(moment(endD).format('YYYY-MM-DD'));
            cb(moment(startD), moment(endD));
          @else
            $('#start_ndate').val(AD2BS(moment(startD).format('YYYY-MM-DD')));
            $('#end_ndate').val(AD2BS(moment(endD).format('YYYY-MM-DD')));
            $('#start_edate').val(moment(startD).format('YYYY-MM-DD'));
            $('#end_edate').val(moment(endD).format('YYYY-MM-DD'));
          @endif
          if(empVal!=null) $('.employee_filters').val(empVal);
          // sessionStorage.setItem('DT_Tour_filters', "");
          sessionStorage.setItem('DT_Tour_filters', JSON.stringify({
            "empVal": empVal,
            "start": startD,
            "end": endD,
          }));
        }
      }else{
        sessionStorage.setItem('DT_Tour_filters', "");
      }
      initializeDT(empVal, startD, endD);
      $('#employee_filters').select2({
        "placeholder": "Select Employee",
      }); 
      $('#status_filters').select2({
        "placeholder": "Select Status",
      }); 
    });

    function initializeDT(empVal=null, startD, endD){
      let status = $('.status_filters').find('option:selected').val();
      if(status=="null"){
        status = null;
      }
      const table = $('#tourplantbl').DataTable({
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search"
        },
        "order": [[ 3, "desc" ]],
        "serverSide": true,
        "processing": true,
        "paging": true,
        "stateSave": true,
        "dom":  "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
                "<'row'<'col-xs-6'><'col-xs-6'>>" +
                "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
        "columnDefs": [
          {
            "orderable": false,
            "targets":-1,
          }, 
          { 
            width: 20, 
            targets: [0],
          },
          { 
            width: 100, 
            targets: [-1],
          },
        ],

        "buttons": [
            {
                extend: 'colvis',
                order: 'alpha',
                className: 'dropbtn',
                columns:[0,1,2,3,4,5,6],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },
          {
            extend: 'pdfHtml5', 
            title: 'Tour Plans', 
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'excelHtml5', 
            title: 'Tour Plans', 
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'print', 
            title: 'Tour Plans', 
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
        ],
        "ajax":{
          "url": "{{ domain_route('company.admin.tourplan.ajaxDatatable') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}", 
            empVal : empVal,
            startDate: startD,
            endDate: endD, 
            status: status
          },
          beforeSend:function(){
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
          },
          error:function(){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          },
          complete:function(){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          }
        },
        "columns": [
          {"data" : "id"},
          {"data" : "employee_name"},
          {"data" : "visit_place"},
          {"data" : "start_date"},
          {"data" : "end_date"},
          {"data" : "date_diff"},
          {"data" : "status"},
          {"data" : "action"},
        ],
      });
      table.buttons().container()
          .appendTo('#tourplanexports');
      var oldExportAction = function (self, e, dt, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
          if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
              $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
          } else {
              $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
          }
        } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
          if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
              $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
          } else {
              $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
          }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
          $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
        }
      };

      var newExportAction = function (e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function (e, s, data) {
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
          data.start = 0;
          data.length = {{$tourplansCount}};
          dt.one('preDraw', function (e, settings) {
            if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
              var columnsArray = [];
              var visibleColumns = settings.aoColumns.map(setting => {
                                      if(setting.bVisible){
                                        columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                      } 
                                    })    
              columnsArray.pop("Action")
              // columnsArray.push("S.No.", "Party Name", "Salesman", "Date", "Remark");
              var columns = JSON.stringify(columnsArray);
              $.each(settings.json.data, function(key, htmlContent){
                settings.json.data[key].id = key+1;
                settings.json.data[key].employee_name = $(settings.json.data[key].employee_name)[0].textContent;
                settings.json.data[key].status = $(settings.json.data[key].status)[0].textContent;
              });
              customExportAction(config, settings, columns);
            }else{
              oldExportAction(self, e, dt, button, config);
            }
            // oldExportAction(self, e, dt, button, config);
            dt.one('preXhr', function (e, s, data) {
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
                $('#mainBox').removeClass('box-loader');
                $('#loader1').attr('hidden', 'hidden');
            });
            setTimeout(dt.ajax.reload, 0);
            return false;
          });
        });
        dt.ajax.reload();
      }
      $('#reportrange').removeClass('hidden');
      function customExportAction(config, settings, cols){
        $('#exportedData').val(JSON.stringify(settings.json));
        $('#pageTitle').val(config.title);
        $('#columns').val(cols);
        var propertiesArray = [];
        var visibleColumns = settings.aoColumns.map(setting => {
                              if(setting.bVisible) propertiesArray.push(setting.data)
                            })
        propertiesArray.pop("action")
        // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
        var properties = JSON.stringify(propertiesArray);
        $('#properties').val(properties);
        $('#pdf-generate').submit();
      }
    }; // Data Table initialize 

    $('body').on("change", ".employee_filters",function () {
      var empVal = $(this).find('option:selected').val();
      if(empVal=="null"){
        empVal = null;
      }
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      sessionStorage.setItem('DT_Tour_filters', JSON.stringify({
        "empVal": empVal,
        "start": start,
        "end": end,
      }));
      if(empVal != '')
      {
        $('#tourplantbl').DataTable().destroy();
        initializeDT(empVal, start, end);
      }
    });

    $('body').on("change", ".status_filters",function () {
      var empVal = $('.employee_filters').find('option:selected').val();
      if(empVal=="null"){
        empVal = null;
      }
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      sessionStorage.setItem('DT_Leav_filters', JSON.stringify({
        "empVal": empVal,
        "start": start,
        "end": end,
      }));
      $('#tourplantbl').DataTable().destroy();
      initializeDT(empVal, start, end);
  });

    $('#tourplantbl').on('click','.alert-modal',function(){
      $('#alertModal').modal('show');
    });

    $('#changeStatus').on('submit',function(){
      $('#btn_status_change').attr('disabled',true);
    });

    // after loading show
    $(function () {
      $('#reportrange').removeClass('hidden');
    });

    //responsive 
    $('#reportrange').on('click',function(){
      if ($(window).width() <= 320) {   
        $(".daterangepicker").addClass("tourplandateposition");
        
      }
      else if ($(window).width() <= 768) {
        $(".daterangepicker").addClass("tourplandateposition");
      }
      else {   
        $(".daterangepicker").removeClass("tourplandateposition");
      }
    });

    $('#tourplantbl').on('click','.edit-modal',function(){
      $('#myModal').modal('show');
      $('.modal-title').text('Change Status');
      $('#tourplan_id').val($(this).data('id'));
      $('#status').val($(this).data('status'));
      $('#remark').val($(this).data('remark'));
    });

    $('#addTourPlan').click(function(){
      const current = $('#myAddModal'); 
      current.modal('show');
      $('.modal-title').text('Add Tourplan');
      current.find('#addTourplan')[0].action = "{{domain_route('company.admin.tourplan.store')}}";
      @if(config('settings.ncal')==1)
        $('#addTourplan').submit(function(){
          current.find('#hidden_add_start_ndate').val(BS2AD($('#add_start_ndate').val()));
          current.find('#hidden_add_end_ndate').val(BS2AD($('#add_end_ndate').val()));
        });
      @endif
    });

    $('#tourplantbl').on('click','.update-modal',function(){
      const current = $('#myEditModal'); 
      current.find('.DT_Tour_FILTER').val(sessionStorage.getItem('DT_Tour_filters'));
      current.modal('show');
      $('.modal-title').text('Edit Tourplan');
      current.find('#tourplan_id').val($(this).data('id'));
      current.find('#place_of_visit').val($(this).data('place_of_visit'));
      // current.find('#visit_purpose').val($(this).data('visit_purpose'));
      var text = $(this).data('visit_purpose');
      CKEDITOR.instances.edit_visit_purpose.setData(text);
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

    $('#tourplantbl').on('click','.del-modal',function(){
      let tourPlanId = $(this).data("id");
      let delUrl = $(this).data("delurl");
      $('#delete').modal('show');
      $('#delBtn').click(function(){
        $('#del_id').val(tourPlanId);
        $('.remove-record-model')[0].action = delUrl;
      });
    });
    $('#addTourplan').submit(function(){
      $(this).find('#btn_status_change').attr('disabled', true);
    });
    $('#updateTourplan').submit(function(){
      $(this).find('#btn_status_change').attr('disabled', true);
    });

    $(document).on('click','.buttons-columnVisibility',function(){
        if($(this).hasClass('active')){
            $(this).find('input').first().prop('checked',true);
            console.log($(this).find('input').first().prop('checked'));
        }else{
            $(this).find('input').first().prop('checked',false);
            console.log($(this).find('input').first().prop('checked'));
        }
    });

    $(document).on('click','.buttons-colvis',function(e){
        var filterBox = $('.dt-button-collection');
        filterBox.find('li').each(function(k,v){
            if($(v).hasClass('active')){
                $(v).find('input').first().prop('checked',true);
            }else{
                $(v).find('input').first().prop('checked',false);
            }
        });
    });

  </script>

@endsection