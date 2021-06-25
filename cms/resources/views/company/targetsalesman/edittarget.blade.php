@extends('layouts.company')
@section('title', 'Edit Salesman Target')
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
        
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Edit Salesman Target</h3>
                <div class="page-action pull-right">
                    <a href="{{ domain_route('company.admin.salesmantargetlist.show') }}" class="btn btn-default btn-sm"> <i
                                class="fa fa-arrow-left"></i>
                        Back</a> 
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                {!! Form::open(array('url' => url(domain_route("company.admin.salesmantargetlist.update",[$targetid])),
                'method' => 'PATCH', 'id'=>'orderForm')) !!}
                  <input type="hidden" name="removeTgtIds" id="removeTgtIds" value="">
                <div class="col-md-2">
                  <h5>Target Name</h5>
                </div>    
                <div class="col-md-4">
                  <?php if(empty($target[0]['target_name'])){$name = 'NA'; }else{ $name = $target[0]['target_name']; } ?>
                  {!! Form::text('tname',$name,['class'=>'form-control variantClass','id'=>'tgt_editname','placeholder' => 'Target Name']) !!} 
                </div>
                @if($errors->any())
                  <div class="col-md-6">
                      @foreach($errors->all() as $error)
                        <h5 class="has-error"><i>{{$error}}</i></h5>
                      @endforeach
                  </div>
                @endif
                @include('company.targetsalesman._form')
                {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right', 'id' => 'submitBtn']) !!}
                
                {!! Form::close() !!} 

            </div>
        </div>

    </section>

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

      $("#submitBtn").on("click",function(){
        let total_tablerows = parseInt($('#dynamic_field tr').length)-1;
        var tgOptCheck = 0;var selcurVal = tgtname = '';
        for(var gh=0;gh<total_tablerows;gh++){
          selcurVal = $("#topt_"+gh).val();
          if(selcurVal==null || selcurVal==undefined){
          }else{
            tgOptCheck++;
          }
        }
        tgtname = $("#tgt_editname").val();
        if((tgOptCheck==total_tablerows) && (tgtname!='')){
          return true;
        }else{
          alert('Please select all necessary fields');
          return false;
        }
      });

      function checkValGreater(mydiv){
        var tgvalue = parseInt(mydiv.value);
        if(tgvalue==0 || tgvalue<0 || tgvalue==''){
          alert('Value cannot be less than 1');
          mydiv.value = 1;
          return false;
        }else{
          return true;
        }
      }


      $(function () {
        $('.targetvalueClass').select2();

        var sel_remids = '';

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
              @php $totval = count($data['targetoptions']);  @endphp
              tot_tgtopt = parseInt({{ $totval }});
              if(total_targets==(tot_tgtopt-1)){
                $(`#add_entry`+(tot_tgtopt-1)).addClass("hidden");
              }
            }else{
              $(`#remove_entry`+j).removeClass("hidden");
              $(`#add_entry`+j).addClass("hidden");
            }
          }

          // var availableTgt = (total_targets+1);
          // @php $totTgts = count($target);  @endphp
          // var currentlyAssignedTopt = {{ $totTgts }} ;
          // var remTgopt = (5-currentlyAssignedTopt);
          // for(var kk=availableTgt;kk>remTgopt;kk--){
          //   $("#rowElement"+(kk-1)).remove();
          // }
          // var nowRow = $(".rowElement").length;
          // if(nowRow==1){
          //   $("#remove_entry0").addClass("hidden");
          // }
        })


        $('#dynamic_field').on('click', '.btn-add', function(e){
          let currentRowId = $(this).data('id');
          
          let actionBtn = '';
          let rowId = currentRowId+1;
          let total_tablerows = $('#dynamic_field tr').length;

          let tnameField = `<input class="form-control" placeholder="Target Name" id="tname${rowId}" data-id="${rowId}" required name="tname[${rowId}]" type="text" >`;

          let toptField = `<select class="form-control toptionsclass" required onChange="prefupdated(this)" id="topt_${rowId}" data-id="${rowId}" name="topt[${rowId}]" >@if(!empty($data['targetoptions'])) @foreach($data['targetoptions'] as $id=>$tot)<option value="{{$tot->id}}">{{$tot->options_value}}</option>@endforeach @endif</select>`;

          let tintervalField = `<select class="form-control targetvalueClass" id="tinterval${rowId}" data-id="${rowId}" required name="tinterval[${rowId}]" >@if(!empty($data['targetvalue'])) @foreach($data['targetvalue'] as $id=>$tot)<option value="{{$id}}">{{$tot}}</option>@endforeach @endif</select>`;

          let tvalField = `<input class="form-control" placeholder="Target Value" id="tval${rowId}" data-id="${rowId}" required name="tval[${rowId}]" onchange="checkValGreater(this)" type="number" >`;
          
          var tot_tgtopt = 7;
          @php $totval = count($data['targetoptions']);  @endphp
          tot_tgtopt = {{ $totval }};
          var totrows = (tot_tgtopt>=5)?5:tot_tgtopt;

          if(total_tablerows>=totrows){ 
            actionBtn = `<button class="btn btn-danger btn-remove action-Btn" id="remove_entry${rowId}" data-id="${rowId}" type="button">X</button><button class="btn btn-primary pull-right btn-add action-Btn hidden" id="add_entry${rowId}" data-id="${rowId}" type="button">+</button>`;
          }else{
            actionBtn = `<button class="btn btn-danger btn-remove action-Btn" id="remove_entry${rowId}" data-id="${rowId}" type="button">X</button><button class="btn btn-primary pull-right btn-add action-Btn" id="add_entry${rowId}" data-id="${rowId}" type="button">+</button>`;
          }

          $('#dynamic_field').append(`<tr class="rowElement" id="rowElement${rowId}" data-row_id="${rowId}"><input type="hidden" name="newrow_numofRows[]" value="${rowId}"><td>${total_tablerows}</td><td>${toptField}</td><td>${tintervalField}</td><td>${tvalField}</td><td>${actionBtn}</td></tr>`);
          // $('#dynamic_field').append(`<tr class="rowElement" id="rowElement${rowId}" data-row_id="${rowId}"><input type="hidden" name="newrow_numofRows[]" value="${rowId}"><td>${toptField}</td><td>${tintervalField}</td><td>${tvalField}</td><td>${actionBtn}</td></tr>`);


          $(`#remove_entry${currentRowId}`).removeClass("hidden");
          $(`#add_entry${currentRowId}`).addClass("hidden");

          $('.targetvalueClass').select2();


          var availableoptions = [1,2,3,4,5,6,7];
          var selectedopt_val = Object.values(selectedopt);
          let difference_val = availableoptions
                 .filter(x => !selectedopt_val.includes(x))
                 .concat(selectedopt_val.filter(x => !availableoptions.includes(x)));

          var toptid = 'topt_'+rowId;
          var toptval = difference_val[0];
          $("#"+toptid).val(toptval);
          var selectedopt_val_wonan = selectedopt_val.filter(function (value) {
                    return !Number.isNaN(value);
                });
          $("#dynamic_field").find('tr').each(function(index,tr){
            if(index!=0){
              dataids = $(this).data('row_id'); 
              curr_row = ('#topt_'+dataids);
              if(rowId!=dataids){
                var inputf = $(curr_row+' option[value="'+toptval+'"]');
                inputf.attr('disabled', 'disabled');
              }else{
                for(var jk=0;jk<=(selectedopt_val_wonan.length);jk++){
                  var tgvlle = selectedopt_val_wonan[jk];
                  var inputf = $(curr_row+' option[value="'+tgvlle+'"]');
                  inputf.attr('disabled', 'disabled');
                }

              }
            }
          });
          selectedopt[toptid] = toptval;

          arrangeserialno();

        });

        $('#dynamic_field').on('click','.btn-remove', function(e){
          let rowId = $(this).data('id');
          let lastRowIdBeforeRemove = $("#dynamic_field").find("tr").last().data('row_id');

          var removeTargetSubid = $("#tgrmid_"+rowId).val();
          sel_remids += removeTargetSubid+'-';
          $("#removeTgtIds").val(sel_remids); 


          optremrowid = "topt_"+rowId;
          curselval = selectedopt[optremrowid];
          delete selectedopt[optremrowid];

          var totrows = ($('.rowElement').length)-1; 
          $("#dynamic_field").find('tr').each(function(index,tr){
            if(index!=0){
              dataids = $(this).data('row_id'); 
              if(rowId!=dataids){
                curr_row = ('#topt_'+dataids);
                var inputf = $(curr_row+' option[value="'+curselval+'"]');
                inputf.removeAttr('disabled');
              }
            }
          });

          arrangeserialno();
          let total_tablerows = parseInt($('#dynamic_field tr').length)-1;
          if(total_tablerows==2){
            $("#dynamic_field tr:nth-child(2)").children("td:first").text(1);
          } 


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


          arrangeserialno();
        });


      $(document).ready(function(){
        // console.log('test');
        prefupdated();
      }) 

      function prefupdated(sel = ''){
        var totrows = $('.rowElement').length;
        for(var j=0;j<totrows;j++){
          currentrowid = "topt_"+j;
          var currentseloptval = parseInt($('#'+currentrowid).val());
          var selectedval = parseInt($("#"+currentrowid).val());
          currentrowid = (parseInt(currentrowid.split('_')[1]));

          currid = ("topt_"+currentrowid);
          curselval = selectedopt[currid];
          for(k=0;k<totrows;k++){
            if(currentrowid!=k){
              var curr_row = '#topt_'+k;
              var inputf = $(curr_row+' option[value="'+curselval+'"]');
              inputf.removeAttr('disabled');
            }
          }

          var currrow = ("topt_"+currentrowid);
          selectedopt[currrow] = selectedval;

          for(k=0;k<totrows;k++){
            var curid = 'topt_'+k;
            var currrow = '#topt_'+k;
            $.each(selectedopt,function(g,h){
              if(curid!=g){
                var inputf = $(currrow+' option[value="'+selectedopt[g]+'"]');
                inputf.attr('disabled', 'disabled');
              }
            });
          }  
        }

      }


      function arrangeserialno(){
        let total_tablerows = parseInt($('#dynamic_field tr').length)-1;
        for(var i=1;i<=total_tablerows;i++){
          $("#dynamic_field tr:nth-child("+i+")").children("td:first").text(i);
        }
      }

      });
    </script>

@endsection
