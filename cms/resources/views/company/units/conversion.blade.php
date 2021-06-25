@extends('layouts.company')
@section('title', 'Unit Conversion')

@section('stylesheets')
<link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  .dataTables_filter{
    padding-top: 10px;
  }
  .input-group-addon {
  padding: 0px 0px;
  font-size: 14px;
  font-weight: normal;
  line-height: 1;
  color: #555;
  text-align: center;
  background-color: #eee;
  border: 1px solid #ccc;
  border-radius: 4px;
  }
  .box-body .btn-success {
    background-color: #00da76!important;
    border-color: #00da76!important;
    color: #fff!important;
  }
  .addCancelBtn{
    width: 40%;
    margin-left: 5px;
  }

  .select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #d2d6de;
    border-radius: 0px;
  }

  .input-group .input-group-addon {
    border-radius: 0;
    border-color: #fff;
    background-color: #fff;
    width: 220px;
  }

  .unit_field{
    margin-top: 1px;
    width: 80px;
  }
  .main-group{
    width: -webkit-fill-available;
  }
  .btnTd{
    width: 20%;
  }
  .spanfocus{
    border: 1px solid red;
  }

  .errLabel{
    color:red;
  }
  #submitBtn{
    width: 18%;
  }
  .flex-container{
    display: flex;
  }

  .close{
    font-size: 30px;
    color: #080808;
    opacity: 1;
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
          <br/>
        @endif
        @if (\Session::has('error'))
          <div class="alert alert-error">
            <p>{{ \Session::get('error') }}</p>
          </div>
          <br/>
        @endif
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Units Conversion</h3>
          </div>
          <div class="box-body">
            <div class="col-xs-12">
              {!! Form::open(array('url' => url(domain_route("company.admin.unit.storeunitconversion")), 'method' => 'post',
              'files'=>true, 'id'=>'orderForm')) !!}
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Units</th>
                    <th>Conversion Rate</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="dynamic_append">
                  <tr class="prodrow" id="row1" data-rownum="1">
                    <input type="hidden" name="index[1]" value="1">
                    <td>
                      <div class="input-group main-group">
                        <input type="text" name="conversion_unit[1]" class="form-control unit_field onlynumber conversion_quantity"
                            id="conversion_unit1" placeholder="Quantity" data-id="1" required />
                        <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection1">
                          {{-- <select name="conversion-unit-symbol[1]" class="select2 conversion-unit-symbol"
                            id="conversion-unit-symbol1" data-id="1">
                            <option value="%">Percent</option>
                            <option value="Amt" selected="selected">Amount</option>
                          </select> --}}
                          {!! Form::select('conversion-unit-symbol[1]', array_column($units, 'name', 'id'), null, ['class' => 'select2 conversion-unit-symbol' , 'id' => 'conversion-unit-symbol1', 'data-id'=>"1",
                          'required'=>true]) !!}
                        </span>
                      </div>
                      <span class="errLabel hidden"></span>
                    </td>
                    <td>
                      <div class="input-group main-group">
                        <input type="text" name="converted_unit[1]" class="form-control unit_field onlynumber converted_quantity"
                            id="converted_unit1" placeholder="Quantity" data-id="1" required />
                        <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection1">
                          {{-- <select name="converted-unit-symbol[1]" class="select2 converted-unit-symbol"
                            id="converted-unit-symbol1" data-id="1">
                            <option value="%">Percent</option>
                            <option value="Amt" selected="selected">Amount</option>
                          </select> --}}
                          {!! Form::select('converted-unit-symbol[1]', array_column($units, 'name', 'id'), null, ['class' => 'select2 converted-unit-symbol' , 'id' => 'converted-unit-symbol1', 'data-id'=>"1",
                          'required'=>true]) !!}
                        </span>
                      </div>
                    </td>
                    <td class="btnTd">
                      <button type="button" class="btn btn-danger form-control addCancelBtn removeRowBtn hidden" id="remove1"
                        data-rownum="1">X</button>
                      <button type="button" class="btn btn-success form-control addCancelBtn addMoreOrder" id="add1"
                        data-rownum="1">+</button>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3">
                      {{-- <a href="{{ domain_route('company.admin.unit.create') }}" class="btn btn-primary pull-right" id="submitBtn" style="margin-left: 5px;">
                        <i class="fa fa-check"></i> Submit
                      </a> --}}
                      {!! Form::submit('Submit', ['class' => 'btn btn-primary pull-right', 'id'=>'submitBtn']) !!}
                    </td>
                  </tr>
                </tfoot>
              </table>
              {!! Form::close() !!}
            </div>
          </div>
          <div class="box-header">
            <span id="unitexports" class="pull-right"></span>
          </div>
          <div class="box-body">
            <div class="col-xs-12">
              <table id="units" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Unit</th>
                    <th>Equals To</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @php $i=0 @endphp
                  @foreach($defined_converted_units as $defined_converted_unit)
                    <tr>
                      <td>{{++$i}}</td>
                      <td>{{$defined_converted_unit->quantity}} {{getUnitName($defined_converted_unit->unit_type_id)}}</td>
                      <td>{{$defined_converted_unit->converted_quantity}} {{getUnitName($defined_converted_unit->converted_unit_type_id)}}</td>
                      <td>
                        <a data-url="{{ domain_route('company.admin.unit.updateunitconversion',[$defined_converted_unit->id]) }}" class="btn btn-warning btn-sm" data-quantity="{{$defined_converted_unit->quantity}}" data-id="{{$defined_converted_unit->id}}" data-converted-quantity="{{$defined_converted_unit->converted_quantity}}" data-unit="{{$defined_converted_unit->unit_type_id}}" data-converted-unit="{{$defined_converted_unit->converted_unit_type_id}}" data-toggle="modal" data-target="#update" style="padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm" data-url="{{ domain_route('company.admin.unit.deleteunitconversion', [$defined_converted_unit->id]) }}" data-id="{{$defined_converted_unit->id}}" data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="modal modal-default fade" id="update" tabindex="-1" unit="dialog" aria-labelledby="myUpdateModalLabel">
    <div class="modal-dialog modal-lg" unit="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Update</h4>
        </div>
        <form method="post" class="update-record-model" id="update-record-model">
          @csrf
          {{method_field('patch')}}
          <div class="modal-body">
            <div class="flex-container">
              <input type="hidden" name="unit_id" id="m_id" value="">
              <div class="input-group main-group">
                <input type="text" name="conversion_unit" class="form-control unit_field onlynumber" id="conversion_unit"
                  placeholder="Quantity" data-id="1" required />
                <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection">
                  {!! Form::select('conversion-unit-symbol', array_column($units, 'name', 'id'), null, ['class' => 'modselect2
                  conversion-unit-symbol' , 'id' => 'conversion-unit-symbol', 'data-id'=>"1",
                  'required'=>true]) !!}
                </span>
              </div>
              <div class="input-group main-group">
                <input type="text" name="converted_unit" class="form-control unit_field onlynumber" id="converted_unit"
                  placeholder="Quantity" data-id="1" required />
                <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection">
                  {!! Form::select('converted-unit-symbol', array_column($units, 'name', 'id'), null, ['class' => 'modselect2
                  converted-unit-symbol' , 'id' => 'converted-unit-symbol', 'data-id'=>"1",
                  'required'=>true]) !!}
                </span>
              </div>
            </div>
            <span class="errLabel hidden"></span>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button> --}}
            <button type="submit" class="btn btn-warning delete-button" id="update-form">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal modal-default fade" id="delete" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" unit="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <form method="post" class="remove-record-model">
          {{method_field('delete')}}
          @csrf
          <div class="modal-body">
            <p class="text-center">
              Are you sure you want to delete this?
            </p>
            <input type="hidden" name="unit_id" id="m_id" value="">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </form>
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
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script>
    let conversionUnits = @json($conversionUnits);
    let convertedUnits = @json($convertedUnits);
    const count = {{$unitsCount}};
    const conversions = @json($defined_converted_units);

    $(function () {
      $('.select2').select2();
      $('.modselect2').select2({ dropdownParent: $("#update") });
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var mid = button.data('id');
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
          modal.find('.modal-body #m_id').val(mid);
      });
    });
    $('#update').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var mid = button.data('id');
      var url = button.data('url');
      $(".update-record-model").attr("action", url);
      var modal = $(this);
      modal.find('.modal-body #m_id').val(mid);
      var quantity = button.data('quantity');
      var unit = button.data('unit');
      var converted_quantity = button.data('converted-quantity');
      var converted_unit = button.data('converted-unit');
      modal.find('.modal-body #conversion_unit').val(quantity);
      modal.find('.modal-body #converted_unit').val(converted_quantity);
      modal.find('.modal-body #conversion-unit-symbol').val(unit).trigger("change");
      modal.find('.modal-body #converted-unit-symbol').val(converted_unit).trigger("change");
    });

    $('#update-record-model').submit(function(e){
      $(this).find('#update-form').attr('disabled', true);
      let modal = $('#update');
      let conversionUnitSymbol = modal.find('.modal-body #conversion-unit-symbol').val();
      let convertedUnitSymbol = modal.find('.modal-body #converted-unit-symbol').val();
      if(conversionUnitSymbol != convertedUnitSymbol) return true;
      else e.preventDefault();
      $(this).find('.errLabel').removeClass('hidden');
      $(this).find('.errLabel').html("Invalid Conversion");
      $(this).find('#update-form').attr('disabled', false);
    });
    var table;
    $(document).ready(function () {
      table = $('#units').DataTable({
        "columnDefs": [ {
            "targets": -1,
            "orderable": false
          }],
        "dom":"<'row'<'col-xs-9'l><'col-xs-3'Bf>>" +
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        stateSave:true,
        "stateSaveParams": function (settings, data) {
        data.search.search = "";
        },
        buttons: [
          {
            extend: 'excelHtml5',
            title: 'Unit List',
            exportOptions: {
              columns: [0, 1, 2]
            }
          },
          {
            extend: 'pdfHtml5',
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // },
            title: 'Unit List',
            exportOptions: {
              columns: [0, 1, 2]
            }
          },
          {
            extend: 'print',
            title: 'Unit List',
            exportOptions: {
              columns: [0, 1, 2]
            }
          },
        ]
      });
      table.buttons().container().appendTo('#unitexports');
    });
    function confirmation() {
      var result = confirm('Confirm to change the status?');
      if (result == true) {
        $('#changeStatus').submit();
      }
    }

    function customExportAction(config, data){
      $('#exportedData').val(JSON.stringify(data));
      $('#pageTitle').val(config.title);
      $('#pdf-generate').submit();
    }
        
    var newExportAction = function (e, dt, button, config) {
      var self = this;
      var data = [];
      var count = 0;
      table.rows({"search":"applied" }).every( function () {
        var row = {};
        row["id"] = ++count;
        row["name"] = this.data()[1];
        row["symbol"] = this.data()[2];
        row["status"] = this.data()[3].replace(/<[^>]+>/g, '').trim();
        data.push(row);
      });
      customExportAction(config, data);
    };

    $(document).on('click','.addMoreOrder',function () {
      let rowNum = $(this).data("rownum");
      let qtyVal = $(`#conversion_unit${rowNum}`).val();
      let conQtyVal = $(`#converted_unit${rowNum}`).val();
      if(qtyVal==""){
        $(`#conversion_unit${rowNum}`).focus();
        return false;
      }
      if(conQtyVal==""){
        $(`#converted_unit${rowNum}`).focus();
        return false;
      }
      if(validateConversion()){
        let befIncrement = rowNum;
        $(this).addClass("hidden");
        rowNum++;
  
        let getCountRow = $(".prodrow").length;
        if(getCountRow<=1){ $(`#remove${befIncrement}`).removeClass("hidden"); }
        
        let conversionRow = `<td>
                        <div class="input-group main-group">
                          <input type="text" name="conversion_unit[${rowNum}]" class="form-control unit_field onlynumber conversion_quantity"
                              id="conversion_unit${rowNum}" placeholder="Quantity" data-id="${rowNum}" required />
                          <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection${rowNum}">
                            <select name="conversion-unit-symbol[${rowNum}] select2" class="select2 conversion-unit-symbol"
                              id="conversion-unit-symbol${rowNum}" data-id="${rowNum}">
                              @foreach(array_column($units, 'name', 'id') as $id=>$name)
                              <option value="{{$id}}">{{$name}}</option>
                              @endforeach
                            </select>
                          </span>
                        </div>
                        <span class="errLabel hidden"></span>
                      </td>`;
        let convertedRow = `<td>
                        <div class="input-group main-group">
                          <input type="text" name="converted_unit[${rowNum}]" class="form-control unit_field onlynumber converted_quantity"
                              id="converted_unit${rowNum}" placeholder="Quantity" data-id="${rowNum}" required />
                          <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection${rowNum}">
                            <select name="converted-unit-symbol[${rowNum}] select2" class="select2 converted-unit-symbol"
                              id="converted-unit-symbol${rowNum}" data-id="${rowNum}">
                              @foreach(array_column($units, 'name', 'id') as $id=>$name)
                              <option value="{{$id}}">{{$name}}</option>
                              @endforeach
                            </select>
                          </span>
                        </div>
                      </td>`;
  
        let actionBtn = `<td class="btnTd"><button type="button" class="btn btn-danger form-control addCancelBtn removeRowBtn" id="remove${rowNum}"data-rownum="${rowNum}">X</button><button type="button" name="add" id="add${rowNum}" class="btn btn-success form-control addCancelBtn addMoreOrder" data-rownum="${rowNum}">+</button></td>`;
  
        $('#dynamic_append').append(`<tr class="prodrow" id="row${rowNum}" data-rownum="${rowNum}"><input type="hidden" name="index[${rowNum}]" value="${rowNum}">${conversionRow}${convertedRow}${actionBtn}</tr>`);
        
        $('.select2').select2();
      }
    });
    $(document).on('click', '.removeRowBtn', function () {
      let button_id = $(this).data("rownum");
      let lastRowIdBeforeRemove = $("#dynamic_append").find("tr").last().data('rownum');
      $(`#row${button_id}`).remove();
      let lastRowIdAfterRemove = $("#dynamic_append").find("tr").last().data('rownum');
      if(lastRowIdBeforeRemove == lastRowIdAfterRemove){
        console.log("Keep Hidden.");
      }else{
        $(`#add${lastRowIdAfterRemove}`).removeClass("hidden");
      }
      let getCountRow = $(".prodrow").length;
      if(getCountRow<=1){
        $(`#add${lastRowIdAfterRemove}`).removeClass("hidden");
        $(`#remove${lastRowIdAfterRemove}`).addClass("hidden");
      }
      validateConversion();
    });
    $(".onlynumber").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    function validateConversion(){
      let preDefinedConversion = {};
      let returnVal = true;
      $('.prodrow').each(function(){
        let conversionUnitSymbol = parseInt($(this).find('.conversion-unit-symbol').val());
        let convertedUnitSymbol = parseInt($(this).find('.converted-unit-symbol').val());
        let qty = parseInt($(this).find('.conversion_quantity').val());
        let qtyVal = qty+''+$(this).find('.conversion-unit-symbol option:selected').text();
        let convertedQty = parseInt($(this).find('.converted_quantity').val());
        let convertedQtyVal = convertedQty+''+$(this).find('.converted-unit-symbol option:selected').text();
        
        if(conversionUnitSymbol==convertedUnitSymbol){
          $(this).find('.main-group').addClass('spanfocus');
          $(this).find('.errLabel').removeClass('hidden');
          $(this).find('.errLabel').html('').html('Cannot use same unit for conversion.');
          returnVal = false;
        } else {
          if($(this).find('.main-group').hasClass('spanfocus')) $(this).find('.main-group').removeClass('spanfocus');
          if(!$(this).find('.errLabel').hasClass('hidden')) if(!$(this).find('.errLabel').addClass('hidden'));
        }

        // Validate For Appended Rows
        if(qtyVal in preDefinedConversion){
          if(preDefinedConversion[qtyVal] == convertedQtyVal){
            $(this).find('.main-group').addClass('spanfocus');
            $(this).find('.errLabel').removeClass('hidden');
            $(this).find('.errLabel').html('').html('Conversion is already defined for same units.');
            returnVal = false;
          }
        }else{
          preDefinedConversion[qtyVal] = convertedQtyVal;
        }
        if($('.prodrow').length>1){
          if(convertedQtyVal in preDefinedConversion){
            if(preDefinedConversion[convertedQtyVal] == qtyVal){
              $(this).find('.main-group').addClass('spanfocus');
              $(this).find('.errLabel').removeClass('hidden');
              $(this).find('.errLabel').html('').html('Conversion is already defined for same units.');
              returnVal = false;
            }
          }else{
            preDefinedConversion[convertedQtyVal] = qtyVal;
          }
        }

        // Validate For Storted Rows
        if(! validateStoredValues(qty, conversionUnitSymbol, convertedQty, convertedUnitSymbol)){
          $(this).find('.main-group').addClass('spanfocus');
          $(this).find('.errLabel').removeClass('hidden');
          $(this).find('.errLabel').html('').html('Conversion is already defined for same units.');
          returnVal = false;
        }
        // for(let i=0; i<count; i++){
        //   if((conversionUnits[i]==conversionUnitSymbol && convertedUnits[i]==convertedUnitSymbol) || (conversionUnits[i]==convertedUnitSymbol && convertedUnits[i]==conversionUnitSymbol)){
            // $(this).find('.main-group').addClass('spanfocus');
            // $(this).find('.errLabel').removeClass('hidden');
            // $(this).find('.errLabel').html('').html('Conversion with same unit has already been added.');
        //     returnVal = false;
        //   }
        // }

      });
      return returnVal;
    }

    function validateStoredValues(qty, unitSym, convertedQty, convertedUnitSym){
      let returnVal= true;
      $.each(conversions, function(i,conversion){
        if((conversion['quantity'] == qty && conversion['unit_type_id'] == unitSym && conversion['converted_quantity'] == convertedQty && conversion['converted_unit_type_id'] == convertedUnitSym) || (conversion['converted_quantity'] == qty && conversion['converted_unit_type_id'] == unitSym && conversion['quantity'] == convertedQty && conversion['unit_type_id'] == convertedUnitSym)){
          returnVal = false;
        }
      });
      return returnVal;
    }

    $('#submitBtn').click(function(e){
      let canSubmit = validateConversion();
      if(canSubmit==false){
        e.preventDefault();
        return false;
      }
    });

    $('#dynamic_append').on("change", ".select2", function(){
      let result = validateConversion();
    });
  </script>
@endsection