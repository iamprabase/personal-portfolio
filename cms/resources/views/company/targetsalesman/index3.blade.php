
@extends('layouts.company')
@section('title', 'Salesman Target')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  #importBtn{
    margin-right: 5px;
    border-radius: 0px;
    display:none;
  }
  .starredProduct{
    color: red;
  }
  .notstarredProduct{
    color: #9c8383;
  }
  .star-icon{
    cursor: pointer;
    font-size: 15px;
    padding-right: 10px;
  }

  .unclick-star-icon{
    cursor: pointer;
    font-size: 15px;
    padding-right: 10px;
  }
  .changeStar{
    width: 20%;
  }
  .direct-chat-gotimg {
    border-radius: 50%;
    float: left;
    width: 40px;
    padding: 0px 0px;
    height: 42px;
    background-color: grey;
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

  .close{
    font-size: 30px;
    color: #080808;
    opacity: 1;
  }
  .productStatusCheckBox {
    position: relative;
    margin-right: 5px!important;
    height: auto;
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
          <span id="productexports" class="pull-right"></span>
        </div>
        <!-- /.box-header --> 
        <div class="box-body" id="mainBox">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-5">
                    <div class="brandsDiv hidden" style="margin-top:10px;">
                        <select name="brands" id="brands">
                          <option value="">Select Salesman</option>
                          @foreach($data['allsalesman'] as $id=>$salesman)
                            <option value="{{$id}}" @if($id==$salesman_id) selected @endif>{{$salesman}}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="col-xs-2">
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

          <table id="product" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>S.No.</th>
                <th>Salesman Name</th>
                <th>Current Target</th>
                <th>Action</th>
              </tr>
            </thead> 
            <tbody>
              @php $tot_targets = count($data['salestargetrecord']);$ccc=$tot_targets; @endphp
              @if($tot_targets>0)
                @foreach($data['salestargetrecord'] as $k=>$v)
                  <tr>
                      <td>{{ $ccc-- }}</td>
                      <td>@if(!empty($data['salestargetrecord'][$k]['salesman_name'])) <a href="{{ domain_route('company.admin.employee.show',[$v['salesman_id']]) }}">{{$data['salestargetrecord'][$k]['salesman_name']}} </a> @else NA @endif</td>
                      <td>
                      @php $tot_targets = count($data['salestargetrecord'][$k]['assigned_roles']);$c=1; @endphp
                      @foreach($data['salestargetrecord'][$k]['assigned_roles'] as $hh=>$pp)
                        <u><b>{{$data['salestargetrecord'][$k]['assigned_roles'][$hh][0]['target_name']}}</b></u><br>
                        @foreach($pp as $rh=>$tg)
                          @foreach($data['alltargetoptions'] as $bv=>$vb)
                            @if($vb->id==$tg['target_rule']) {{$vb->options_value}}&nbsp;&nbsp;&nbsp;[{{$data['salestargetrecord'][$k]['assigned_roles'][$hh][$rh]['target_values']}}
                              @foreach($data['targetvalue'] as $ko=>$ok)
                                @if($tg['target_interval']==$ko) {{$ok}}] @endif
                              @endforeach
                             @endif
                          @endforeach<br>
                        @endforeach
                      @endforeach
                      </td>

                      <td>
                        <a class='btn btn-success btn-sm' id="{{$v['salesman_id']}}" onClick="showSalesmanHistory(this)" style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>
                        @if(Auth::user()->can('targets-update'))
                          <a href="{{ domain_route('company.admin.salesmanindivtargetlist.modify',[$v['salesman_id']]) }}" class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>
                        @endif
                        <!-- <a id="{{$v['salesman_id']}}" class='btn btn-warning btn-sm' onClick="editTargetSalesman(this)" style='padding: 3px 6px;'><i class='fa fa-edit'></i></a> -->
                      </td>
                  </tr>
                @endforeach 
              @endif
            </tbody>
          </table>
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
<form method="post" action="{{domain_route('company.admin.products.custompdfdexport')}}" class="pdf-export-form hidden"
  id="pdf-generate">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
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
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>

<script>

 
  $(function () {
    $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        // $(".remove-record-model").attr("action",url);
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('.modal-body #m_id').val(mid);
    });
    $(document).on('click', '.updateStatuses', function () {
      $('#footer_action_button').addClass('glyphicon-check');
      $('.modal-title').text('Update Multiple Status');
      $('#product_id').val($(this).data('id'));
      const productIds = $('#pageIds').val();
      if(productIds==""){
        alert("Please Select Products.");
      }else{
        $('#upateStatuses').modal('show');
        $('#upateStatuses').children().find('#changeStatuses')[0].action = "{{ domain_route('company.admin.product.changeStatus') }}";
        $('#upateStatuses').children().find('#formMethod').remove(); 
        
        $('#changeStatuses').find('#product_ids').val(productIds);
      }
    });
    $(document).on('click', '#mass_delete', function () {
      $('#footer_action_button').addClass('glyphicon-check');
      $('.modal-title').text('Delete Multiple Products.');
      $('#product_id').val($(this).data('id'));
      const productIds = $('#pageIds').val();
      if(productIds==""){
        alert("Please Select Products.");
      }else{
        $('#mass-delete').modal('show');
        let action = "{{ domain_route('company.admin.product.massdestroy') }}";
        $('#mass-delete').children().find('#massDeleteForm')[0].action = action;
        
        // $('#mass-delete').children().find('#formMethod').remove();
        // $("<input>").attr({ 
        //   name: "_method", 
        //   type: "hidden", 
        //   value: "DELETE" ,
        //   id: "formMethod"
        // }).appendTo($('#mass-delete').children().find('#changeStatuses')); 
        $('#mass-delete').children().find('#product_ids').val(productIds);
      }
    });
  });

  $(document).ready(function () {

    @if (strpos(URL::previous(), domain_route('company.admin.product')) === false)  
      var activeRequestsTable = $('#product').DataTable();
      activeRequestsTable.state.clear();  // 1a - Clear State
      activeRequestsTable.destroy();   // 1b - Destroy
    @endif

    initializeDT();
  });

  $(document).on("click", '.star-icon', function(e){
    let currentEl = $(this);
    let product_id = currentEl.data("product_id");
    let currentStar = currentEl.data("currentstar");
    $('#starredModal').modal('show');
    if(currentStar==1){
      $('#starredModal').find(".modal-body").html("<b><p class='text-center'>Are you sure to unstar this product?</p></b>");
    }else if(currentStar==0){
      $('#starredModal').find(".modal-body").html("<b><p class='text-center'>Are you sure to star this product?</p></b>");
    }
    $('.changeStarBTN').unbind().click(function(e){
      $.ajax({
        "url": "{{ domain_route('company.admin.products.changeStarredStatus') }}",
        "dataType": "json",
        "type": "POST",
        "data":{ 
          "_token": "{{csrf_token()}}",
          "product_id": product_id,
          "currentStar": currentStar,
        },
        beforeSend:function(){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
        },
        success:function(data){
          alert(data.message);
          $('#product').DataTable().destroy();
          initializeDT();
        },
        error:function(xhr, textStatus){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');

        },
        complete:function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        }
      });
      $('#starredModal').modal('hide');
    });
  });
  function getSelVal(){
    return $('#pageIds').val();
  }
  function initializeDT(brand=null, category=null){
      const table = $('#product').removeAttr('width').DataTable({
        "processing": true,
        "serverSide": false,
        "order": [[ 0, "asc" ]],
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
        "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        "buttons": [
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
            title: 'Assigned TargetList for Salesman',
            exportOptions: {
              columns: ':visible'
            }
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // }
          },
          {
            extend: 'pdfHtml5',
            title: 'Assigned TargetList for Salesman',
            exportOptions: {
              columns: ':visible'
            }
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // }
          },
          {
            extend: 'print',
            title: 'Assigned TargetList for Salesman',
            exportOptions: {
              columns: ':visible'
            }
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // }
          },
        ],
        "columns": [
          { "data": "id" },
          { "data": "salesman_name" },
          { "data": "assigned_targetroles" },
          { "data": "action" },
        ],
      });
      table.buttons().container().appendTo('#productexports');
    }
 

    var oldExportAction = function (self, e, dt, button, config) {
      if(button[0].className.indexOf('buttons-excel') >= 0) {
        if($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
          $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
        }else{
          $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
        }
      }else if(button[0].className.indexOf('buttons-pdf') >= 0) {
        if($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
          $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
        }else{
          $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
        }
      }else if(button[0].className.indexOf('buttons-print') >= 0) {
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
        data.length = 4;
        dt.one('preDraw', function (e, settings) {
          if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
            // customExportAction(dt, data, config, settings);
            $.each(settings.json.data, function(key, htmlContent){
            settings.json.data[key].id = key+1;
            settings.json.data[key].status = $(settings.json.data[key].status)[0].textContent;
            });
            customExportAction(config, settings);
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

    


  $(document).on('click', '.edit-modal', function () {
      // $('#footer_action_button').text(" Change");
      $('#footer_action_button').addClass('glyphicon-check');
      $('#footer_action_button').removeClass('glyphicon-trash');
      $('.actionBtn').addClass('btn-success');
      $('.actionBtn').removeClass('btn-danger');
      $('.actionBtn').addClass('edit');
      $('.deleteContent').hide();
      $('.form-horizontal').show();
      $('#product_id').val($(this).data('id'));
      $('#remark').val($(this).data('remark'));
      $('#status').val($(this).data('status'));
      $('#myModal').modal('show');
      $('#myModal').find('.modal-title').text('Change Status');
  });

  $(document).on('click','.alert-modal',function(){
    $('#alertModal').modal('show');
  });

  function pushOrderIds(){
    let product_ids = [];
    $.each($("input[name='update_product_status']:checked"), function(){
      product_ids.push($(this).val());
    });
    return product_ids;
  }

  $('#selectthispage').click(function(event){
    event.stopPropagation();
    if($("input[name='update_product_status']").length==0) $("#selectthispage").prop("checked", false);
    if(this.checked){
      $("input[name='update_product_status']").prop("checked", true);
      let currentVal = $('#pageIds').val();
      let getCheckedIds = pushOrderIds();
      if(currentVal!=""){
        currentVal = currentVal.split(',');
        $.each(currentVal, function(ind, val){
          if(!getCheckedIds.includes(val)){
            getCheckedIds.push(val);
          }
        });
      }
      $('#pageIds').val(getCheckedIds);
    }else{
      $("input[name='update_product_status']").prop("checked", false);
      let uncheckedBoxes = $("input[name='update_product_status']").not(':checked');
      let uncheckVal = [];
      $.each($("input[name='update_product_status']").not(':checked'), function(){
        uncheckVal.push($(this).val());
      });
      let currentVal = $('#pageIds').val().split(',');
      let newVal = currentVal.filter(function(value, index, arr){
                    return !uncheckVal.includes(value);
                  });
      $('#pageIds').val(newVal);
      $("#selectthispage").prop("checked", false);
    }
  });

  $('.brandsDiv').removeClass('hidden');
  $('#brands').select2();
  $('body').on("change", "#brands",function () {
    var salesmanid = $(this).find('option:selected').val();
    var category = $('#categories').find('option:selected').val();
    
    
    $('#product').DataTable().destroy();
    //initializeDT(brand, category);
    url = "{{ domain_route('company.admin.salesmantarget') }}"+'/'+salesmanid;
    window.location.href = url;
  });
  $('.categoriesDiv').removeClass('hidden');
  $('#categories').select2();
  $('body').on("change", "#categories",function () {
    var category = $(this).find('option:selected').val();
    var brand = $('#brands').find('option:selected').val();
    
    
    $('#product').DataTable().destroy();
    initializeDT(brand, category);
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

  $('.multClass').multiselect({
    enableFiltering: true,
    enableCaseInsensitiveFiltering: true,
    enableFullValueFiltering: false,
    enableClickableOptGroups: false,
    includeSelectAllOption: true,
    enableCollapsibleOptGroups : true,
    selectAllNumber: false,
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