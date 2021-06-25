
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

        #noblank_chk > li.select2-results__option:last-child {          
          color: black;
          text-decoration: underline;
          /* background-color: #dd4b39; */
        }

    </style>
@endsection

@section('content')
    <section class="content">  
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Update Salesman Target</h3>
                <div class="page-action pull-right">
                    <a href="{{ domain_route('company.admin.salesmantarget') }}" class="btn btn-default btn-sm"> <i
                                class="fa fa-arrow-left"></i>
                        Back</a> 
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body"> 
                <div class="row" style="margin-bottom:10px;">
                  <div class="col-md-2">
                    <h5 class="pull-right">Salesman Name</h5>
                  </div>    
                  <div class="col-md-4">
                    {!! Form::text('tsalesman',$salesman_name,['class'=>'form-control variantClass','id'=>'variant0','placeholder' => 'Salesman Name','disabled']) !!} 
                  </div>
                </div>
                {!! Form::open(array('url' => url(domain_route("company.admin.salesmantargetsdata.changeconf",[$salesman_id])),
                'method' => 'PATCH', 'id'=>'orderForm')) !!}
                <input type="hidden" name="salemsids" value="{{$salesman_id}}">
                  <div class="row">
                    <div class="col-md-2">
                      <h5 class="pull-right">Select Target</h5>
                    </div>    
                    <div class="col-md-4" id="noblank_chk">
                      <select class="form-control targets" name="salesmantarget" id="select_target" data-it='0' required>
                        @if(count($data['alltargets'])>0)
                          @foreach($data['alltargets'] as $ky=>$yk)
                            <option value="{{$ky}}" @if($salesman_targetid==$ky && $salesman_targetid!=0) selected @endif>{{$yk}}</option>
                          @endforeach
                            <option value="notaraget" @if($salesman_targetid==0) selected @endif style="font-weight:bold!important;">No Target</option>
                        @endif
                      </select>
                    </div>
                    @if(Auth::user()->can('targets-create'))
                      <div class="col-md-3" style="margin-top:10px;">
                        <a id="create_new_target" style="color:blue;cursor:pointer;"><u>Create New Target?</u></a>
                      </div>
                    @endif
                  </div>
    

                <div class="row" id="targets_alldats">
                  <div class="col-md-offset-1 col-xs-10" >
                  <hr>
                    <table class="table table-bordered" style="margin-top:15px;">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Target Options<span style="color: red">*</span></th>
                          <th>Target Interval<span style="color: red">*</span></th>
                          <th>Target Value<span style="color: red">*</span></th>
                        </tr>
                      </thead>
                      <tbody id="targetdata"> 
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="row" style="margin-top:15px;margin-bottom:20px;" id="rem_margintopbottom">
                  <div class="col-md-offset-10 col-md-1">
                    {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right', 'id' => 'submitBtn']) !!}
                  </div>
                </div>
              {!! Form::close() !!}



                <div class="row" style="display:none;" id="crt_assgn_tgt">
                  <div class="col-md-offset-1 col-md-10">
                    <hr>
                    {!! Form::open(array('url' => url(domain_route("company.admin.salesmantargetsdata.crtsngemp",[$salesman_id])),
                    'method' => 'post', 'id'=>'orderForm')) !!}
                    <input type="hidden" name="sel_salesman" id="sel_salesman" val="">
                    <div class="col-md-2">
                      <h5>Target Name<span style="color: red">*</span></h5>
                    </div>    
                    <div class="col-md-4">
                      {!! Form::text('tname[0]',null,['class'=>'form-control variantClass','id'=>'asstgt_name2','placeholder' => 'Target Name','required']) !!} 
                    </div>
                    @if($errors->any())
                      <div class="col-md-6">
                          @foreach($errors->all() as $error)
                            <h5 class="has-error"><i>{{ $error }}</i></h5>
                          @endforeach
                      </div>
                    @endif
                    @include('company.targetsalesman._form')
                    
                    <div class="row" style="margin-top:15px;margin-bottom:20px;">
                      <div class="col-md-offset-11 col-md-1">
                        {!! Form::submit('Create and Assign Target', ['class' => 'btn btn-primary pull-right', 'id' => 'create_new_entry']) !!}
                      </div>
                    </div>

                    {!! Form::close() !!}
                  </div>
                </div>

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

      $("#create_new_entry").on("click",function(){
        let total_tablerows = parseInt($('#dynamic_field tr').length)-1;
        var tgOptCheck = 0;var selcurVal = tgtname = '';
        for(var gh=0;gh<total_tablerows;gh++){
          selcurVal = $("#topt_"+gh).val();
          if(selcurVal==null || selcurVal==undefined){
          }else{
            tgOptCheck++;
          }
        }
        tgtname = $("#asstgt_name2").val();
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

        $('.targets').select2();

        $(document).ready(function(){
          var sel_targetgroupid = '';
          sel_targetgroupid = $("#select_target").val();
          if(sel_targetgroupid=='notaraget'){
            $("#targets_alldats").hide();
            $("#crt_assgn_tgt").hide();
            $("#submitBtn").show();
          }else{
            @if($errors->any())
              $("#targets_alldats").hide();
              $("#submitBtn").hide();
              $("#rem_margintopbottom").removeAttr('style');
              $("#crt_assgn_tgt").show();
            @else
              fetchrelatedvalues(sel_targetgroupid);
            @endif
          }
          prefupdated();
        });

        function fetchrelatedvalues(targetgroupid=''){
          if(targetgroupid==''){
            alert('This salesman has no associated target, Assign target first');
            url = "{{ domain_route('company.admin.salesmantarget') }}";
            window.location.href = url;
          }else{
            $.ajax({
              type: "post",
              url: "{{ domain_route('company.admin.salesmantargetsdata') }}",
              data: {sel_tgtgrpid: targetgroupid},
              success:function(res){
                $("#crt_assgn_tgt").hide();
                $("#targetdata").html(res.msg);
                $("#submitBtn").show();
                $("#targets_alldats").show();
              },
              error:function(res){
                resptext = JSON.parse(res.responseText);
                alert(resptext.msg);
                url = "{{ domain_route('company.admin.salesmantarget') }}";
                window.location.href = url;
              }
            });
          }
        }

        $("#select_target").on('change',function(){
          var sel_targetgroupid = '';
          sel_targetgroupid = $("#select_target").val();
          if(sel_targetgroupid=='notaraget'){
            $("#targets_alldats").hide();
            $("#crt_assgn_tgt").hide();
            $("#submitBtn").show();
          }else{
            fetchrelatedvalues(sel_targetgroupid);
          }
        });


        $("#create_new_target").on('click',function(){
          $("#targets_alldats").hide();
          $("#submitBtn").hide();
          $("#rem_margintopbottom").removeAttr('style');
          $("#crt_assgn_tgt").show();
        });



        $('#dynamic_field').on('click', '.btn-add', function(e){
          let currentRowId = $(this).data('id');
          let actionBtn = '';
          let rowId = currentRowId+1;
          let total_tablerows = $('#dynamic_field tr').length;

          let tnameField = `<input class="form-control" placeholder="Target Name" id="tname${rowId}" data-id="${rowId}" required name="tname[${rowId}]" type="text" >`;

          let toptField = `<select class="form-control toptionsclass" required onChange="prefupdated(this)" id="topt_${rowId}" data-id="${rowId}" name="topt[${rowId}]" >@if(!empty($data['targetoptions'])) @foreach($data['targetoptions'] as $id=>$tot)<option value="{{$tot->id}}" >{{$tot->options_value}}</option>@endforeach @endif</select>`;

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

          $('#dynamic_field').append(`<tr class="rowElement" id="rowElement${rowId}" data-row_id="${rowId}"><input type="hidden" name="numofRows[]" value="rows_${rowId}"><td>${total_tablerows}</td><td>${toptField}</td><td>${tintervalField}</td><td>${tvalField}</td><td>${actionBtn}</td></tr>`);
          // $('#dynamic_field').append(`<tr class="rowElement" id="rowElement${rowId}" data-row_id="${rowId}"><input type="hidden" name="numofRows[]" value="rows_${rowId}"><td>${toptField}</td><td>${tintervalField}</td><td>${tvalField}</td><td>${actionBtn}</td></tr>`);


          $(`#remove_entry${currentRowId}`).removeClass("hidden");
          $(`#add_entry${currentRowId}`).addClass("hidden");

          $('.targetvalueClass').select2();


          // var totrows = $('.rowElement').length;
          // var availableopt = [1,2,3,4,5,6,7];
          // var currrow = ("topt_"+rowId);
          // $('#'+currrow).val(parseInt(totrows));
          // var selectedval = parseInt($('#'+currrow).val());
          // currentrowid = (rowId+1);
          // // selectedopt.push(selectedval);
          // selectedopt[currrow] = selectedval;

          // for(k=0;k<totrows;k++){
          //   var curid = 'topt_'+k;
          //   var currrow = '#topt_'+k;
          //   $.each(selectedopt,function(g,h){
          //     if(curid!=g){
          //       var inputf = $(currrow+' option[value="'+selectedopt[g]+'"]');
          //       inputf.attr('disabled', 'disabled');
          //     }
          //   });
          // }

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

          optremrowid = "topt_"+rowId;
          curselval = selectedopt[optremrowid];
          delete selectedopt[optremrowid];

          var totrows = ($('.rowElement').length)-1;
          $("#dynamic_field").find('tr').each(function(index,tr){
            if(index!=0){
              dataids = $(this).data('row_id'); //console.log(dataids,rowId);
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

 

      });

      function prefupdated(sel = ''){
        if(sel!==''){
          currentrowid = sel.id;
        }else{
          currentrowid = "topt_0";
        }
        var totrows = $('.rowElement').length;
        // var currentseloptval = parseInt($('#'+currentrowid).val());
        // var availableopt = [1,2,3,4,5,6,7];
        // var selectedval = parseInt($("#"+currentrowid).val());
        currentrowid = (parseInt(currentrowid.split('_')[1]));

        changedrowid = currentrowid;//(parseInt((sel.id).split('_')[1]));
        changedrowid_curval = parseInt($("#topt_"+changedrowid).val());
        changedrowid_prevval = selectedopt['topt_'+changedrowid];
        newarr = [];
        $.each(selectedopt,function(a,b){
          if(!isNaN(b)){
            newarr.push(a);
          }
        });
        for(var gh=0;gh<newarr.length;gh++){
          rownum = (parseInt(newarr[gh].split('_')[1]));
          if(rownum!=changedrowid){
            curr_row = "#topt_"+rownum;
            var inputf = $(curr_row+' option[value="'+changedrowid_curval+'"]');
            inputf.attr('disabled', 'disabled');
          }
        }
        for(var gh=0;gh<newarr.length;gh++){
          rownum = (parseInt(newarr[gh].split('_')[1]));
          if(rownum!=changedrowid){
            curr_row = "#topt_"+rownum;
            var inputf = $(curr_row+' option[value="'+changedrowid_prevval+'"]');
            inputf.removeAttr('disabled');
          }
        }
        var currrow = ("topt_"+changedrowid);
        selectedopt[currrow] = changedrowid_curval;

      }


    function arrangeserialno(){
      let total_tablerows = parseInt($('#dynamic_field tr').length)-1;
      for(var i=1;i<=total_tablerows;i++){
        $("#dynamic_field tr:nth-child("+i+")").children("td:first").text(i);
      }
    }


      

      
    </script>

@endsection
