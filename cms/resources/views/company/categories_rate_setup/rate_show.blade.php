@extends('layouts.company')
@section('title', 'Category Rate Details')
@section('stylesheets')
  @include('company.parties_rate_setup.assets.custom_css')
  <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
  <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" />
  <style>
    .dt-buttons.btn-group{
      margin-right: 10px;
    }
    .multiselect-selected-text {
      margin-right: 90px;
      color: #333 !important;
    }
    .multiselect-selected-text{
      margin-right: 0px;
    }

    .multiselect.dropdown-toggle.btn.btn-default .caret {
      position: relative;
      margin-top: 10px;
    }
  </style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @include('layouts.partials.flashmessage')
      <div class="box">
        
        <div class="box-header">
          <h3 class="box-title"><span id="rateHeader">{{$category->name}}</span> Rates Details</h3>
          <a href=" {{ URL::previous() }}" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
          <span id="categoriesratesetupexports" class="pull-right"></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div style="margin-top:10px;"></div>
                  </div>
                  <div class="col-xs-3">
                    <div style="margin-top:10px;"></div>
                  </div>

                  <div class="col-xs-4">
                  </div>
                  <div class="col-xs-2">
                  </div>

                </div>
              </div>
            </div>
            <div class="col-xs-2"></div>
          </div>
          <div id="loader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div id="mainBox">
            <table id="categoriesratesetup" class="table table-bordered table-striped" style="width: 100% !important;">
              <thead>
                <tr>
                  <th>S.No</th>
                  <th>Category Rate Name</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php $i=0 @endphp
                @foreach($category->categoryrates as $categoryrates)
                  <tr>
                    <td>
                      {{++$i}}
                    </td>
                    <td>
                      {{$categoryrates->name}}
                    </td>
                    <td>
                      <a data-href="{{domain_route('company.admin.category.rates.update', [$categoryrates->id])}}" class='btn btn-warning btn-sm rate_show_details' data-toggle='modal' data-name="{{$categoryrates->name}}" data-target='#editRateModal'><i class='fa fa-edit'></i></a>
                      <a data-href="{{domain_route('company.admin.category.rates.delete', [$categoryrates->id])}}" class='btn btn-danger btn-sm rate_show_details' data-toggle='modal' data-target='#deleteModal'><i class='fa fa-trash'></i></a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
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
  </div>
</section>
<div class="modal fade" id="editRateModal" tabindex="-1" role="dialog">
  <form id="add_new_rate" method="post" action="#">
    @csrf
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Rate Name</h4>
        </div>
        <div class="modal-body">
         
          <div class="row custom_rate_form">
            <div class="col-xs-4" style="text-align: left;">
              <label for="">Name</label><span style="color:red">*</span>
            </div>
            <div class="col-xs-4">
                <span class="input-group">
                  <input class="form-control rate_name" type="text" name="rate_name" required="">
                </span>
                <span class="name_err errlabel" style="color:red">
                </span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="addRateBtn" type="submit" class="btn btn-primary">Update</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->

<div class="modal modal-default fade" id="deleteModal" tabindex="-1" category="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" category="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
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
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-buttons/buttons.flash.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
<script>
  let table;
  let editor;
  $('document').ready(()=>{
    initializeDT();
  });
  let columns = [
        { "data": "id" },
        { "data": "rate_name" },
        { "data": "action" },
  ];
  
  function initializeDT(){
 
    table = $('#categoriesratesetup').DataTable({
      "stateSave": false,
      language: { search: "" },
      "order": [[ 0, "asc" ]],
      "serverSide": false,
      "processing": false,
      "paging": true,
      "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" + "<'row'<'col-xs-6'><'col-xs-6'>>" + "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
      "columnDefs": [
        {
          "orderable": false,
          "targets":[-1],
        },
      ],
      "buttons": [
        {
          extend: 'pdfHtml5', 
          title: 'Category Rate Details', 
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7],
            stripNewlines: false,
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'excelHtml5', 
          title: 'Category Rate Details', 
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'print', 
          title: 'Category Rate Details', 
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
    });
    table.buttons().container().appendTo('#categoriesratesetupexports');
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
        data.length = -1;
        dt.one('preDraw', function (e, settings) {
          // if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
          //   customExportAction(config, settings);
          // }else{
          //   oldExportAction(self, e, dt, button, config);
          // }
          oldExportAction(self, e, dt, button, config);
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
  }; // Data Table initialize
  

  $('.tooltip').tooltip({
    placement: "right",
    trigger: "focus"
  });

  $('.fa-info-circle').tooltip({
    placement: "bottom"
  });

  $('#editRateModal').on('show.bs.modal', function(e){
    $('#add_new_rate')[0].reset();
    let name = e.relatedTarget.dataset.name;
    let formAction = e.relatedTarget.dataset.href;
    $('#add_new_rate')[0].action = e.relatedTarget.dataset.href
    $('#add_new_rate').find('input[name=rate_name]').val(name)
    $('.errlabel').html('');
  });

  $('#deleteModal').on('show.bs.modal', function(e){
    $(this).find('.remove-record-model')[0].reset();
    let formAction = e.relatedTarget.dataset.href;
    $(this).find('.remove-record-model')[0].action = e.relatedTarget.dataset.href
  });

  $('#add_new_rate').submit(function(e){
    e.preventDefault();
    const formEl = $(this); 
    const url = formEl[0].action;
    const name = formEl.find('input[name=rate_name]').val();
    
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":{
        _method: "PATCH",
        _token: "{{csrf_token()}}",
        name,
      },
      beforeSend: function(){
        $('#addRateBtn').prop('disabled', true);
      },
      success:function(response){
        alert(response.msg);
        if(response.status){
          formEl[0].reset();
          $('#addRateBtn').prop('disabled', false);
          $('#editRateModal').modal('hide');
        }else{
          formEl[0].reset();
          $('#addRateBtn').prop('disabled', false);
          $('#editRateModal').modal('hide');
        }
        location.reload()
      },
      error:function(errs){
        $.each(errs.responseJSON.errors, function(key,value) {
          formEl.find('.'+key+'_err').append('<p class="help-block has-error">'+value+'</p>');
        });
        $('#addRateBtn').prop('disabled', false);
      },
      complete:function(response){
        $('#addRateBtn').prop('disabled', false);
      }

    });
  });

  
</script>
@endsection