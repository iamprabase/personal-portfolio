<?php

// foreach($data['allrec']['assigned_roles'] as $yy=>$ss){
  // foreach($ss as $ii=>$ff){
    // print_r($ff);
  // }
  // print_r($data['allrec']['assigned_roles'][$yy][0]['target_name']);

// }

// print_r($errors->all());

// die();

?>



@extends('layouts.company')
@section('title', 'Update Salesman Target')
@section('stylesheets')
    @if(config('settings.ncal')==1)
        <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
    @else 
        <link rel="stylesheet"
              href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
    <style>
        .ms-options-wrap {
            min-width: 120px;
            z-index: 1;
        }

        .select2-selection__placeholder,
        .select2-selection__rendered {
            color: #000 !important;
        }

        .addCancelBtn {
            width: 45px;
        }

        .box-body .btn-success {
            background-color: #00da76 !important;
            border-color: #00da76 !important;
            color: #fff !important;
        }

        .caret {
            position: absolute;
            top: 20px;
        }

        .multiselect.dropdown-toggle.btn.btn-default .caret {
            margin-top: 0px;
        }

        .qty,
        .product_discount,
        .rate,
        .mrp,
        .amt {
            width: 80px !important;
        }

        .pdisinputaddon,
        .discount-symbol-selection,
        .unit-symbol-selection {
            padding: 0px 0px;
            */ font-size: 14px;
            font-weight: normal;
            line-height: 1;
            color: #555;
            text-align: center;
            background-color: #eee;
            border: 0px solid #ccc;
            border-radius: 0px;
        }

        .mrp_addon {
            padding: 0px 0px;
            font-size: 14px;
            font-weight: normal;
            line-height: 1;
            color: #555;
            text-align: center;
            background-color: #eeeeee !important;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .amt {
            padding: 0px;
        }

        .contentFit {
            width: fit-content;
        }

        #loaderDiv img {
            position: absolute;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 99;
        }

        .loaderDiv {
            position: absolute;
            z-index: 1;
            left: 30%;
        }

        .loaderOpacityControl {
            opacity: 0.4;
        }

        .contDisp {
            display: -webkit-inline-box;
        }

        .add-on-height {
            height: 40px !important;
        }

    </style>
@endsection

@section('content')
    <section class="content">  
        <div class="row">
          <div class="col-xs-12">
          <!-- @if ($errors->any())
              <div class="alert alert-error">
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </div>
          @endif -->
          </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Update Salesman Target</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body"> 
                <div class="row" style="margin-bottom:10px;">
                  <div class="col-md-2">
                    <h5 class="pull-right">SalesMan Name</h5>
                  </div>    
                  <div class="col-md-4">
                    {!! Form::text('tsalesman',$salesman_name,['class'=>'form-control variantClass','id'=>'variant0','placeholder' => 'Salesman Name','disabled']) !!} 
                  </div>
                </div>
                {!! Form::open(array('url' => url(domain_route("company.admin.salesmanindivtargetlist.update",[$salesman_id])),
                'method' => 'PATCH', 'id'=>'orderForm')) !!}
                <input type="hidden" name="salemsids" value="{{$salesman_id}}">
                @foreach($data['allrec']['assigned_roles'] as $yy=>$ss)
                  <div class="row">
                    <div class="col-md-2">
                      <h5 class="pull-right @if($errors->any()) has-error @endif">Target Name</h5>
                    </div>    
                    <div class="col-md-4">
                      @php $row=0;$j=1;  @endphp
                      <input class="form-control variantClass" id="variantval{{$row}}" placeholder="Target Name" name="tname[{{$yy}}]" type="text" value="{{$data['allrec']['assigned_roles'][$yy][0]['target_name']}}">
                      @if($errors->any()) @foreach($errors->all() as $error) <p class="help-block has-error">{{ $error }}</p> @endforeach @endif
                    </div>
                  </div>
                  @include('company.targetsalesman._salestgtedit')
                @endforeach
                {!! Form::submit('Update Salesman Target', ['class' => 'btn btn-primary pull-right', 'id' => 'submitBtn']) !!}
                
                {!! Form::close() !!}

            </div> 
        </div>

    </section>

  <div id="editTargetModal" class="modal fade" role="dialog">
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

@endsection

@section('scripts')

    @if(config('settings.ncal')==1)
        <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
    @else
        <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    @endif
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
    <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
      $(function () {
        $('.targetvalueClass').select2();

        // $('.toptionsclass').multiselect({
        //   enableFiltering: true,
        //   enableCaseInsensitiveFiltering: true,
        //   enableFullValueFiltering: false,
        //   enableClickableOptGroups: false,
        //   includeSelectAllOption: true,
        //   enableCollapsibleOptGroups : true,
        //   selectAllNumber: false,
        //   nonSelectedText:"Select Target Options",
        // });

        $(document).ready(function(){
          let total_targets = ($(".rowElement").length)-1;
          for(var j=0;j<=total_targets;j++){
            if(j==total_targets){
              if(j==0){
                $(`#remove_entry`+j).addClass("hidden");
                $(`#add_entry`+j).removeClass("hidden");
              }else if(j==4){
                $(`#remove_entry`+j).removeClass("hidden");
                $(`#add_entry`+j).addClass("hidden");
              }else{
                $(`#remove_entry`+j).removeClass("hidden");
                $(`#add_entry`+j).removeClass("hidden");
              }
            }else{
              $(`#remove_entry`+j).removeClass("hidden");
              $(`#add_entry`+j).addClass("hidden");
            }
          }

        })


        $('#dynamic_field').on('click', '.btn-add', function(e){
          let currentRowId = $(this).data('id');
          
          let actionBtn = '';
          let rowId = currentRowId+1;
          let total_tablerows = $('#dynamic_field tr').length;

          let tnameField = `<input class="form-control" placeholder="Target Name" id="tname${rowId}" data-id="${rowId}" required name="tname[${rowId}]" type="text" >`;

          let toptField = `<select class="form-control toptionsclass" id="topt${rowId}" data-id="${rowId}" name="topt[${rowId}]" >@if(!empty($alltargetoptions)) @foreach($alltargetoptions as $id=>$tot)<option value="{{$tot->id}}">{{$tot->options_value}}</option>@endforeach @endif</select>`;

          let tintervalField = `<select class="form-control targetvalueClass" id="tinterval${rowId}" data-id="${rowId}" required name="tinterval[${rowId}]" >@if(!empty($data['targetvalue'])) @foreach($data['targetvalue'] as $id=>$tot)<option value="{{$id}}">{{$tot}}</option>@endforeach @endif</select>`;

          let tvalField = `<input class="form-control" placeholder="Target Value" id="tval${rowId}" data-id="${rowId}" required name="tval[${rowId}]" onchange="checkValGreater(this)" type="number" >`;

          if(total_tablerows>=5){
            actionBtn = `<button class="btn btn-danger btn-remove action-Btn" id="remove_entry${rowId}" data-id="${rowId}" type="button">X</button><button class="btn btn-primary pull-right btn-add action-Btn hidden" id="add_entry${rowId}" data-id="${rowId}" type="button">+</button>`;
          }else{
            actionBtn = `<button class="btn btn-danger btn-remove action-Btn" id="remove_entry${rowId}" data-id="${rowId}" type="button">X</button><button class="btn btn-primary pull-right btn-add action-Btn" id="add_entry${rowId}" data-id="${rowId}" type="button">+</button>`;
          }

          $('#dynamic_field').append(`<tr class="rowElement" id="rowElement${rowId}" data-row_id="${rowId}"><input type="hidden" name="newrow_numofRows[]" value="${rowId}"><td>${total_tablerows}</td><td>${toptField}</td><td>${tintervalField}</td><td>${tvalField}</td></tr>`);

          $(`#remove_entry${currentRowId}`).removeClass("hidden");
          $(`#add_entry${currentRowId}`).addClass("hidden");

          // $('.toptionsclass').multiselect({
          //   enableFiltering: true,
          //   enableCaseInsensitiveFiltering: true,
          //   enableFullValueFiltering: false,
          //   enableClickableOptGroups: false,
          //   includeSelectAllOption: true,
          //   enableCollapsibleOptGroups : true,
          //   selectAllNumber: false,
          //   nonSelectedText:"Select Target Options",
          // });
          $('.targetvalueClass').select2();

        });

        $('#dynamic_field').on('click','.btn-remove', function(e){
          let rowId = $(this).data('id');
          let lastRowIdBeforeRemove = $("#dynamic_field").find("tr").last().data('row_id');

          $('#dynamic_field').find(`#rowElement${rowId}`).remove();
          let rowCount = $('.rowElement').length;
          let lastRowIdAfterRemove = $("#dynamic_field").find("tr").last().data('row_id');
          if(rowCount==1){
            $(`#remove_entry${lastRowIdAfterRemove}`).addClass("hidden");
            $(`#add_entry${lastRowIdAfterRemove}`).removeClass("hidden");
            return true;
          }
          
          if(lastRowIdBeforeRemove==rowId || (rowCount<=5)){
            $(`#remove_entry${lastRowIdAfterRemove}`).removeClass("hidden");
            $(`#add_entry${lastRowIdAfterRemove}`).removeClass("hidden");
          }

          //arrangeserialno();
        });

        function arrangeserialno(){
          let total_tablerows = $('#dynamic_field tr').length;
          let act_sno = '';
          for(var i=1;i<total_tablerows;i++){
            act_sno = $('#dynamic_field tr td:nth-child(1)').text();
            console.log(act_sno);
            //$('#sno_'+i).text(i);
          }
        }


      });

      
    </script>

@endsection
