@extends('layouts.company')
@section('title', 'Custom Rate Setup')
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
            
      <div class="box">
        
        <div class="box-header">
          <h3 class="box-title"><span id="rateHeader">{{$category->name}}</span> Rates Setup</h3>
          <a href=" {{ URL::previous() }}" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
          <span id="categoriesratesetupexports" class="pull-right"></span>
          <img src="{{asset('assets/dist/img/bolticon.png')}}" class="quick-custom-rate-button pull-right"></img>
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
                  <th>Product Name</th>
                  <th>Variant Name</th>
                  <th>Unit</th>
                  <th>Original Rate</th>
                  @foreach($category->categoryrates as $categoryrates)
                  <th>
                    {{$categoryrates->name}}
                  </th>
                  @endforeach
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
  </div>
</section>
<div class="modal fade" id="quickRateSetupModal" tabindex="-1" role="dialog">
  <form id="quickRateSetupForm" method="post" action="{{domain_route('company.admin.category.rates.quickRateSetup', ['id' => $category->id])}}">
    @csrf
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Quick Category Rate Setup</h4>
        </div>
        <div class="modal-body">
          <div class="row custom_rate_form">
            <div class="col-xs-4" style="text-align: left;">
              <label for="">Set Custom Rate to be</label> <span style="color:red">*</span>
            </div>
            <div class="col-xs-4">
                <span class="input-group">
                  <input class="form-control quick_rate_setup_input" type="text" name="discount_percent" required="">
                  <span class="input-group-addon">%</span>
                </span>
              <span class="discount_percent_err errlabel" style="color:red">
              </span>
            </div>
            <div class="col-xs-4" style="text-align: left;width: fit-content;">
              <label> of Original Rate.</label>
            </div>
          </div>
          <div class="row custom_rate_form" style="margin-top: 10px;">
            <div class="col-xs-4" style="text-align: left;">
              <label for="">Category Rate Type</label> <span style="color:red">*</span>
            </div>
            <div class="col-xs-4">
              <select name="category_rates" id="categoryRateTypes" required multiple>
                @foreach($category->categoryrates as $categoryrates)
                <option value="{{$categoryrates->id}}">{{$categoryrates->name}}</option>
                @endforeach
              </select>
              <span class="category_rates_err errlabel" style="color:red">
              </span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="quickCustomRateBtn" type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->
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
        { "data": "product_name" },
        { "data": "variant_name" },
        { "data": "unit" },
        { "data": "original_mrp" },
  ];
  let categoryRates = new Array()

  @foreach($category->categoryrates as $categoryrates)
    columns.push( {"data" : "{{$categoryrates->id}}"+"___categoryrate", "class": "mrpCell"})
    categoryRates.push("{{$categoryrates->id}}"+"___categoryrate")
  @endforeach

  function initializeDT(){
 
    table = $('#categoriesratesetup').DataTable({
      "stateSave": true,
      language: { search: "" },
      "order": [[ 1, "asc" ]],
      "serverSide": true,
      "processing": true,
      "paging": true,
      "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" + "<'row'<'col-xs-6'><'col-xs-6'>>" + "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
      "columnDefs": [
        {
          "orderable": false,
          "targets":[-1, -2],
        },
      ],
      "buttons": [
        {
          extend: 'pdfHtml5', 
          title: 'Custom Rate Setup', 
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
          title: 'Custom Rate Setup', 
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
          title: 'Custom Rate Setup', 
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.category.rates.fetch', [$category->id]) }}",
        "dataType": "json",
        "type": "POST",
        "data":{ 
          _token: "{{csrf_token()}}", 
          category_rates : JSON.stringify(categoryRates)
        },
        beforeSend:function(url, data){
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
      "columns": columns,
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
  $('#categoriesratesetup').on('click', '.mrpCell', function(){
    $(this).find('.mrpInput').removeClass("hidden");
    $(this).find('.mrpInput').focus();
    $(this).find('.mrpText').addClass("hidden");
  });

  $('#categoriesratesetup').on('focusout', '.mrpInput', function(){
    $(this).addClass("hidden");
    $(this).val($(this).data("value"));
    $(this).parent().find('.mrpText').removeClass("hidden");
  });

  $('#categoriesratesetup').on('keypress', '.mrpInput', function(event){
    const alphabetRegexPattern = /^[a-zA-Z]/;
    const floatvalRegexPattern = /^\d*\.?\d*$/;
    let mrp = $(this).val();
    let originalMrp = $(this).data('original_mrp');
    
    if(alphabetRegexPattern.test(event.key) && event.key!="Enter"){
      return false;
    }
    
    if(!floatvalRegexPattern.test(event.key) && event.key!="Enter"){
      return false;
    }

    if(event.keyCode == 13){
      let categoryRateTypeId = $(this).data('category_rate_type_id');
      let url = "{{domain_route('company.admin.category.rates.updateOrCreate', ['id' => $category->id, 'category_rate_type_id' => ':category_rate_type_id'])}}";
      url = url.replace(':category_rate_type_id', categoryRateTypeId);
      let product_id = $(this).data('product_id');
      let variant_id = $(this).data('variant_id');
      if(variant_id == "") variant_id = null

      if(parseFloat(originalMrp)==parseFloat(mrp)){
        return false;
      }

      $.ajax({
        "stateSave": true,
        "stateSaveParams": function (settings, data) {
        data.search.search = "";
        },
        "url": url,
        "dataType": "json",
        "type": "POST",
        "data":{ 
          _token: "{{csrf_token()}}",
          _method: "PATCH",
          product_id: product_id,
          variant_id: variant_id,
          mrp: mrp
        },
        beforeSend:function(url, data){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
        },
        success:function(response){
          alert(response.msg);
          table.ajax.reload(null, false);
        },
        error:function(xhr, textStatus){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');

          if(xhr.status==422){
            Object.keys(xhr.responseJSON.errors).map(err=>alert(xhr.responseJSON.errors[err][0]))
          }else{
            alert(xhr.responseJSON.message)
          }
        },
        complete:function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        }
      });

    }
  });

  $('.quick-custom-rate-button').click(function(){
    $('#quickRateSetupModal').modal();
  });

  $('#quickRateSetupModal').on('show.bs.modal', function(){
    $('#quickRateSetupForm')[0].reset();
    $('#categoryRateTypes').multiselect("refresh")
  });

  $('.quick_rate_setup_input').on('keypress', function(event){
    const alphabetRegexPattern = /^[a-zA-Z]/;
    let rateVal = $(this).val();
    
    if(alphabetRegexPattern.test(event.key)){
      return false;
    }
    
  });

  $('.quick_rate_setup_input').on('focusout', function(event){
    let rateVal = $(this).val();
    if(rateVal!=""){
      const floatvalRegexPattern = /^\d*\.?\d*$/;
      if(rateVal>500){
        $(this).val("");
        alert("Please add value less than or equal to 500.")  
        return false;
      }
      if(!floatvalRegexPattern.test(rateVal)){
        $(this).val("");
        alert("Please add value greater than or equal to 0.")
        return false;
      }
      $(this).val(parseFloat(rateVal).toFixed(2));
    }
    
  });

  $('.tooltip').tooltip({
    placement: "right",
    trigger: "focus"
  });

  $('.fa-info-circle').tooltip({
    placement: "bottom"
  });
  
  $('#quickRateSetupForm').submit(function(e){
    e.preventDefault();
    let el = $(this)
    let form = el[0]
    let discountPercent = el.find('.quick_rate_setup_input').val()
    let categoryRates = el.find('#categoryRateTypes').val()
    if(categoryRates.length == 0){
      alert("Please select category types.")
      return;
    }
    categoryRates =JSON.stringify(categoryRates);
    let action = form.action;
    if(discountPercent>=0){
      $.ajax({
        "url": action,
        "dataType": "json",
        "type": "POST",
        "data":{
          discount_percent: discountPercent,
          category_rates: categoryRates,
        },
        beforeSend:function(url, data){
          $('#quickCustomRateBtn').attr('disabled', true);
          $('#quickCustomRateBtn').html('Please Wait...');
          el.find('.errlabel').html('');
        },
        success:function(response){
          alert(response.msg);
          if(response.status){
            form.reset();
            table.ajax.reload(null, false);
            $('#quickRateSetupModal').modal('hide');
          }
        },
        error:function(xhr, textStatus){
          if(xhr.status==422){
            $.each(xhr.responseJSON.errors, function(key,value) {
              $('.' + key + '_err').append('<p class="help-block has-error">'+value+'</p>');
            });
          }else{
            alert(xhr.responseJSON.message);
          }
        },
        complete:function(){
          $('#quickCustomRateBtn').attr('disabled', false);
          $('#quickCustomRateBtn').html('Submit');  
        }
      });
    }
  });
  $('.rate-name-setup-form').submit(function(e) {
    e.preventDefault();
    let url = $(this)[0].action;
    let formData = $(this).serializeArray();
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":formData,
      beforeSend:function(url, data){
        $('#rate-name-submit').attr('disabled', true);
        $('#rate-name-submit').html('Please Wait...');
      },
      success:function(response){
        if(response.status){
          alert(response.msg);
          $('.rate-name-setup-form').find('#rate_name').val(response.rate_name);
          $('#rateHeader').html(response.rate_name);
          $('#rate-name-setup-form').find('.err_div').each(function(){
            $(this).html("");
          });
        }
      },
      error:function(xhr, textStatus){
        $('#rate-name-submit').attr('disabled', false);
        $('#rate-name-submit').html('Update Name');
        $('#rate-name-setup-form').find('.err_div').each(function(){
          $(this).html("");
        });
        $.each(xhr.responseJSON.errors, function(key,value) {
          $('.'+key).append('<p class="help-block has-error">'+value+'</p>');
        });
      },
      complete:function(){
        $('#rate-name-submit').attr('disabled', false);
        $('#rate-name-submit').html('Submit');
      }
    });
  });

  $('#categoryRateTypes').multiselect({
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      enableFullValueFiltering: false,
      enableClickableOptGroups: false,
      includeSelectAllOption: true,
      enableCollapsibleOptGroups : true,
      selectAllNumber: false,
      numberDisplayed: 1,
      nonSelectedText:"Select Category Types",
      allSelectedText:"All Selected",
    });
</script>
@endsection