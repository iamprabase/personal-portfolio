
@extends('layouts.company')
@section('title', 'Salesman Target')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">

<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<!-- <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" /> -->


<style>
    .box-loader{
      opacity: 0.5;
    }
    table td {
      max-width: 300px;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
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
  

</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div>
      @endif

      @if (\Session::has('error'))
      <div class="alert alert-error">
        <p>{{ \Session::get('error') }}</p>
      </div>
      @endif

      @if (\Session::has('warning'))
      <div class="alert alert-warning">
        <p>{{ \Session::get('warning') }}</p>
      </div>
      @endif
      <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="box-title">Salesman Target</h3>
              @if(Auth::user()->can('targets-create'))
              <a href="{{ domain_route('company.admin.salesmantarget.create') }}" class="btn btn-primary pull-right"
                style="margin-left: 5px;">
                <i class="fa fa-plus"></i> Create New Target
              </a>
              <a href="{{ domain_route('company.admin.salesmantarget.set') }}" class="btn btn-primary pull-right"
                style="margin-left: 5px;">
                Assign Target
              </a>
              @endif
              <span id="expenseexports" class="pull-right"></span>
            </div>
          </div>
        </div>
        <!-- /.box-header --> 
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-8">
              <div class="row">
                <div class="select-2-sec">
                  @if(Auth::user()->can('targets_rep-create'))
                    <div class="col-xs-5">
                      <div class="brandsDiv hidden" style="margin-top:10px;">
                        <select name="salesman" id="salesman">
                          <option value="">Select Salesman</option>
                          @foreach($data['allsalesman'] as $id=>$salesman)
                            <option value="{{$id}}">{{$salesman}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  @endif
                  <div class="col-xs-3">
                    
                  </div>
                  <div class="col-xs-4" style="margin-top:10px;">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-2">
            </div>
          </div>
          <div id="loader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div id="mainBox">
            <table id="expense" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th>S.No.</th>
                  <th>Salesman Name</th>
                  <th>Current Target</th>
                  <th>Action</th>
                </tr>
              </thead>
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

<input type="hidden" name="pageIds[]" id="pageIds">
<form method="post" action="{{domain_route('company.admin.salesmantargetreporpdf_tg')}}" class="pdf-export-form hidden"
  id="pdf-generate">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>


<!-- Modal -->
<div id="showSalesmanHistory" class="modal fade" role="dialog">
  <div class="modal-dialog small-modal">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Salesman Assigned Target History</h4>
      </div>
      <div class="modal-body">
        <div class="row" style="margin-bottom:20px;">
          <div class="col-md-5">Salesman Name:&nbsp;&nbsp;<span style="font-style:italic;" id="salesmanname"></span></div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Target Name</th>
                  <th>Target Rule</th>
                  <th>Target Interval</th>
                  <th>Target Values</th>
                  <th>Assigned Date(From - Till)</th>
                </tr>
              </thead>
              <tbody id="historybody">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          {{-- <button id="tgtupd" type="submit" class="btn btn-primary">
            <span id="footer_action_button" class='glyphicon'> </span>
          </button> --}}
          {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
            <span class='glyphicon glyphicon-remove'></span> Close
          </button> --}}
        </div>
      </div>
    </div>
  </div>
</div>

 

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
<!-- <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script> -->

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script> -->

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="{{asset('assets/dist/js/tether.min.js') }}"></script>

<!-- <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script> -->


<script>

  var daterangeval = [];

  $(document).ready(function() {

    $('#salesman').select2();
    $(".brandsDiv").removeClass('hidden');
    var empVal = null;
    initializeDT(empVal);

    function initializeDT(empVal=null){
      var table = $('#expense').DataTable({
        "order": [[ 0, "desc" ]],
        "columnDefs": [
          {
            "orderable": false,
            "targets":[3],
          },
          {
            "width": "8%",
            "targets":[0],
          }
        ],
        "processing": true,
        "serverSide": true,
        "stateSave": false,
        "ajax":{
          "url": "{{ domain_route('company.admin.salesmantargetdt') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}",
            salesmanID:empVal,
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
          },
        },
        "columns": [
          { "data": "id" },
          { "data": "salesman_name" },
          { "data": "assigned_roles" },
          { "data": "action" },
        ],  
        "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
        "<'row'<'col-xs-6'><'col-xs-6'>>" +
        "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        buttons: [
            {
                extend: 'colvis',
                order: 'alpha',
                className: 'dropbtn',
                columns:[0,1,2,3],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },

            {
                extend: 'excelHtml5',
                title: 'Salesman Target',
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                footer: true,
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Salesman Target',
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                orientation:'landscape',
                footer: true,
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
            },
            {
                extend: 'print',
                title: 'Salesman Target',
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                footer: true,
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
            },
        ]
      });
      table.buttons().container()
      .appendTo('#expenseexports');

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
          data.length = 10;
          dt.one('preDraw', function (e, settings) {
            if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
              var columnsArray = [];
              var visibleColumns = settings.aoColumns.map(setting => {
                                      if(setting.bVisible){
                                        columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                      } 
                                    })    
              columnsArray.pop('Action');
              var columns = JSON.stringify(columnsArray);

              customExportAction(config, settings, columns);
            }else{
              oldExportAction(self, e, dt, button, config);
            }
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
    }

    function customExportAction(config, settings, cols){
      $('#exportedData').val(JSON.stringify(settings.json));
      $('#pageTitle').val(config.title);
      $('#columns').val(cols);
      var propertiesArray = [];
      var visibleColumns = settings.aoColumns.map(setting => {
                            if(setting.bVisible) propertiesArray.push(setting.data)
                          })
      var properties = JSON.stringify(propertiesArray);
      $('#properties').val(properties);
      $('#pdf-generate').submit();
    }

    $("#salesman").on('change',function(){
      var empVal = '';
      empVal = $('#salesman').val();
      if(empVal==""){
        empVal = null;
      }
      sessionStorage.setItem('DT_Exp_filters', JSON.stringify({
        "empVal": empVal,
      }));
      $('#expense').DataTable().destroy();
      initializeDT(empVal);
    });


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

  function editTargetSalesman(sl){
    var tgid = parseInt(sl.id);
    var available_targets = $("#"+tgid+"_ava").html();
    $("#upd_salesmanid").val(tgid);
    $("#availabletargets").html(available_targets);
    $('#editTargetModal').modal('show');
  }

  $("#tgtupd").on('change',function(e){
    e.preventDefault();
    var salesmanid = $("#upd_salesmanid").val();
    $("#toptid_"+salesmanid).val([]);
  })

  $("#tgtupd").on('click',function(e){
    e.preventDefault();
    var salesmanid = $("#upd_salesmanid").val();
    var sel_targets = $("#toptid_"+salesmanid).val();
    $("#toptid_"+salesmanid+" :selected").each(function(i, sel){ 
        alert( $(sel).val() ); 
    });
    // console.log(salesmanid,sel_targets);
  }) 

  function showSalesmanHistory(divinfo){
    var salesmanname = '';
    var salesman_id = parseInt(divinfo.id);
    $.ajax({
      type: 'post',
      url: "{{ domain_route('company.admin.salesmantargethistory') }}"+'/'+salesman_id,
      data: {sid: salesman_id},
      success:function(res){
        $("#salesmanname").text(res.sname);
        $("#historybody").html(res.msg);
        $("#showSalesmanHistory").modal('show');
      },
      error:function(res){
        var resptext = JSON.parse(res.responseText);
        alert(resptext.msg);
      }
    });

  }






</script>

@endsection