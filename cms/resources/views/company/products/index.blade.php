@extends('layouts.company')
@section('title', 'Products')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  #importBtn{
    margin-right: 5px;
    border-radius: 0px;
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
          <h3 class="box-title">Product List</h3>
          @if(Auth::user()->can('product-create'))
          <a href="{{ domain_route('company.admin.product.create') }}" class="btn btn-primary pull-right"
            style="margin-left: 5px;">
            <i class="fa fa-plus"></i> Create New
          </a>
          @endif
          <span id="productexports" class="pull-right"></span>
          @if(Auth::user()->can('product-status') || Auth::user()->can('product-delete'))
          <div class="dropdown pull-right tips"
            title="Mass Actions(Change Status and Mass Delete)" style="margin-right: 5px;">
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">⋮</button>
            <ul class="dropdown-menu">
              @if(Auth::user()->can('product-status'))
                <li><a href="#" class="updateStatuses">Change Status</a></li>
              @endif
              @if(Auth::user()->can('product-delete'))
                <li><a href="#" class="mass_action" id="mass_delete" data-type="massdelete">Mass Delete</a></li>
              @endif
            </ul>
          </div>
          @endif
        </div>
        <!-- /.box-header -->
        <div class="box-body" id="mainBox">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div class="brandsDiv hidden" style="margin-top:10px;">
                        <select name="brands" id="brands">
                          <option value="">Select Brand</option>
                          @foreach($brands as $id=>$brand)
                          <option value="{{$id}}">{{$brand}}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <div class="categoriesDiv hidden" style="margin-top:10px;">
                      <select name="categories" id="categories">
                        <option value="">Select Category</option>
                        @foreach($categories as $id=>$category)
                        <option value="{{$id}}">{{$category}}</option>
                        @endforeach
                      </select>
                    </div>
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
                <th>@if(Auth::user()->can('product-status') || Auth::user()->can('product-delete'))<input type='checkbox' id='selectthispage' name='selectthispage' style="height: max-content;margin-right: 10px;">@endif #</th>
                <th>Product Name</th>
                <th>Product Code</th>
                <th>Image</th>
                <th>Brand</th>
                <th>Category</th>
                <th @if(config('settings.order_with_amt')==1) hidden @endif> Rate</th> 
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <div id="loader1" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
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
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>


<!-- Modal -->
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
          <input type="hidden" name="product_id" id="c_id" value="">

        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
          <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal modal-default fade" id="mass-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <form method="post" class="remove-record-model" id="massDeleteForm">
        {{method_field('delete')}}
        {{csrf_field()}}
        <div class="modal-body">
          <p id="can_be_deleted" class="text-center"></span><br>
          <p id="product_cannot_deleted" class="text-center"></span>
          <p id="selected_item_info" class="text-center">Are you sure you want to delete selected items?</p>
          {{-- <p class="text-center"> Are you sure you want to delete selected items? </p> --}}
        </div>
        <input type="hidden" name="product_id[]" id="product_ids" value="">
        <input type="hidden" name="deletable_product_id[]" id="deletable_product_ids" value="">

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
          action="{{URL::to('admin/product/changeStatus')}}">
          {{csrf_field()}}
          <input type="hidden" name="product_id" id="product_id" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="status" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn actionBtn">
              <span id="footer_action_button" class='glyphicon'> </span> Change
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
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
        <div class="modal-body">
          <p class="text-center">
            Sorry! You are not authorized to change status.
          </p>
          <input type="hidden" name="expense_id" id="c_id" value="">
          <input type="text" id="accountType" name="account_type" hidden/>
        </div>
        <div class="modal-footer">
          {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
        </div>
    </div>
  </div>
</div>

<div id="starredModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Change Starred Status</h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary changeStar changeStarBTN">
          Yes
        </button>
        {{-- <button type="button" class="btn btn-warning changeStar" data-dismiss="modal">
          No
        </button> --}}
      </div>
    </div>
  </div>
</div>


<div id="upateStatuses" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="changeStatuses" method="POST">
          {{csrf_field()}}
          <input type="hidden" name="product_id[]" id="product_ids" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="status" name="status" required="true">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button id="btn_statuses_change" type="submit" class="btn btn-primary actionBtnStatuses">
              <span id="footer_action_button" class='glyphicon'> </span> Update Statuses
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
<script>
  var DeProductDetail=[];
  //var deleteableProducts_ids = new Array();
  $(function () { 
    LoadAjaxToGetDeletedId();
    
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
      $('#mass-delete #can_be_deleted').html(' ');
      $('#mass-delete #product_cannot_deleted').html(' ');
      $('#mass-delete #selected_item_info').html(' ');

      $('#footer_action_button').addClass('glyphicon-check');
      $('.modal-title').text('Delete Multiple Products.');
      $('#product_id').val($(this).data('id'));
      const productIds = $('#pageIds').val();
      if(productIds==""){
        alert("Please Select Products.");
      }else{
        if(productIds.indexOf(',')!=-1){
            var product_id_array = productIds.split(","); 
        }else{
            var product_id_array = new Array(productIds);
        }
        ProductDeletedAndNotDeleted(product_id_array)
        let action = "{{ domain_route('company.admin.product.massdestroy') }}";
        $('#mass-delete').children().find('#massDeleteForm')[0].action = action;
        $('#mass-delete').children().find('#product_ids').val(productIds);
      }
    });

    function LoadAjaxToGetDeletedId(){
      $.ajax({
          type:'POST',
          url:"{{ domain_route('company.admin.product.hasInfo') }}",
          success:function(data){
            $('#deletable_product_ids').val(data.deleteableProducts_id);
          }
      }); 
    }
    
    function ProductDeletedAndNotDeleted(product_id_array){
      var Deletable_Product_IDS = $('#deletable_product_ids').val()
      if(Deletable_Product_IDS.indexOf(',')!=-1){
        var Deletable_Product_IDS_array = Deletable_Product_IDS.split(",");
      }else{
        var Deletable_Product_IDS_array = new Array(Deletable_Product_IDS);
      }
      let intersection = product_id_array.filter(x => Deletable_Product_IDS_array.includes(x));
      var product_id_array_count = product_id_array.length;
      var intersection_count = intersection.length;
      if(intersection_count == 0){
          alert("None of the products are deletable.");
      }else{
          $('#mass-delete #can_be_deleted').append('Products that cannot be deleted: ' + (parseInt(product_id_array_count) - parseInt(intersection_count)) );
          $('#mass-delete #product_cannot_deleted').append('Products that can be deleted: ' + intersection_count)
          if(intersection_count > 1){
            $('#mass-delete #selected_item_info').append('Are you sure you want to delete ' + intersection_count +' items?')
          }else{
            $('#mass-delete #selected_item_info').append('Are you sure you want to delete ' + intersection_count +' item?')
          }
          $('#mass-delete').modal('show');
      }
    }

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
        "serverSide": true,
        "lengthMenu": [ 10, 25, 50, 100, 500, 1000 ],
        "order": [[ 0, "asc" ]],
        "columnDefs": [
          {
            "orderable": false,
            "targets":[0, -1, -6],
          },
          @if(config('settings.order_with_amt')==1) {
          "targets": [ -3 ],
          "visible": false,
          "searchable": false
          }, @endif
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
                columns:[0,1,2,3,4,5,6],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },
            @if(config('settings.product')==1 && Auth::user()->can('import-view') && Auth::user()->can('product-create') && config('settings.import')==1)
            { 
              text: 'Import',
              attr: { id: 'importBtn'},
                action: function ( e, dt, node, config ) {
                    onclick (window.location.href='{{ domain_route('company.admin.import.products') }}')
                }
              },
              @endif
          {

            extend: 'excelHtml5',
            title: 'Product List',
            
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'pdfHtml5',
            title: 'Product List',
            
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'print',
            title: 'Product List',
              
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
        ],
        "ajax":
        {
          "url": "{{ domain_route('company.admin.products.ajaxDatatable') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}",
            "brand": brand,
            "category": category,
            selIds: getSelVal,
          },
          beforeSend:function(){
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
          },
          error:function(){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          },
          complete:function(data){
            if(data.status==200){
              let tdata = data.responseJSON;
              if(tdata.data.length>0){
                $("#selectthispage").prop("checked", tdata.selectThisPageCheckBox);
              }
            }
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          }
        },
        "columns": [
          { "data": "id" },
          { "data": "product_name" },
          { "data": "product_code" },
          { "data": "image" },
          { "data": "brand_name" },
          { "data": "category_name" },
          { "data": "mrp" },
          { "data": "status" },
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
        data.length = {{$products}};
        dt.one('preDraw', function (e, settings) {
          if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
            var columnsArray = [];
            var visibleColumns = settings.aoColumns.map(setting => {
                                    if(setting.bVisible){
                                      columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                    } 
                                  })    
            columnsArray.pop("Action")
            var columns = JSON.stringify(columnsArray);
            $.each(settings.json.data, function(key, htmlContent){
            settings.json.data[key].id = key+1;
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

  $('body').on('change', '.productStatusCheckBox',function(){
    if(this.checked){
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
      if($("input[name='update_product_status']").not(':checked').length==0) $("#selectthispage").prop("checked", true);
    
    }else{
      let uncheckVal = $(this).val();
      let currentVal = $('#pageIds').val().split(',');
      let newVal = currentVal.filter(function(value, index, arr){
                      return value != uncheckVal;
                  });
      $('#pageIds').val(newVal);
      $("#selectthispage").prop("checked", false);
    }
  });
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
    var brand = $(this).find('option:selected').val();
    var category = $('#categories').find('option:selected').val();
    
    
    $('#product').DataTable().destroy();
    initializeDT(brand, category);
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

</script>

@endsection