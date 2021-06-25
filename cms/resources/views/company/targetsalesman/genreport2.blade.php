@extends('layouts.company')
@section('title', 'Salesman Target Report')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<!-- <link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}"> -->
<link rel="stylesheet" href="{{ asset('assets/dist/css/dateRangePicker.css') }}"> 
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>



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

  .rangePicker{
    transform: translateX(735px) translateY(170px) translateZ(0px)!important;
  }

  @media(max-width: 425px){
    .rangePicker{
      transform: translateX(568px) translateY(226px) translateZ(0px)!important;
    }
  }

  

</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Salesman Target Report</h3>
          <span id="productexports" class="pull-right"></span>
        </div>
        <!-- /.box-header --> 
        <div class="box-body" id="mainBox">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                @if(Auth::user()->can('targets_rep-create'))
                  <div class="col-xs-5">
                    <div class="brandsDiv" style="margin-top:10px;">
                        <select name="salesmanid[]" id="salesmanid" class="multClass" required multiple>
                          @if(count($data['allsalesman'])>0)
                            @foreach($data['allsalesman'] as $id=>$salesman)
                              <option value="{{$id}}" @if(count($salesman_id)>0) @foreach($salesman_id as $ssid) @if($ssid==$id) selected @endif @endforeach @endif>{{$salesman}}</option>
                            @endforeach
                          @endif
                        </select>
                    </div>
                  </div>
                @endif

                  <div class="col-xs-3">
                    <div style="margin-top:10px;" class="target_duration hidden">  
                                           
                        <div class='selectMonths'>
                          <input style="border:1px solid #CCCCCC;" type='text' placeholder='Report DateRange' id="daterange" value='@if($keepyearmonth) {{$keepyearmonth}} @endif' readonly />
                        </div>                    
										</div>
                  </div>

                @if(Auth::user()->can('targets_rep-create'))
                  <div class="col-xs-2">
                    <div style="margin-top:10px;" class="target_duration hidden">
                      <a class="btn btn-primary pull-right" id="searchterm">
                        Search
                      </a>
										</div>
                  </div>
                @endif

                  <div class="col-xs-2">
                    
                  </div>

                </div>
              </div>
              
            </div>
            <div class="col-xs-2"></div>
          </div>
          <style>
            table.table-bordered.dataTable tbody th, table.table-bordered.dataTable tbody td{
              vertical-align: unset!important;
            }
            td span {
              background: #ef10101c;    
              margin-bottom: 7px;
              padding: 5px 7px;
              display: block;
              color: black;
              text-align: center;
          }
          </style>
          <table id="product" class="table table-bordered table-striped">
            <thead>
              <tr> 
                <th>S.No.</th>
                <th>Salesman Name</th>
                <th>No.of Orders</th>
                <th>Value of Orders</th>
                <th>No. of Collections</th>
                <th>Value of Collections</th>
                <th>No. of Visits</th>
                <th>Golden Calls(New Parties)</th>
                <th>Total Calls(No.of Orders+No. of Zero Orders)</th>
              </tr>
            </thead> 
            <tbody>
              @php $tot_targets=count($arrange_targets); $ccc=1; @endphp
              @if($tot_targets>0)
                @foreach($arrange_targets as $ind=>$arr)
                  <tr>
                    <td>{{ $ccc++ }}</td>
                    <td><a href="{{ domain_route('company.admin.employee.show',[$ind]) }}">{{ $arrange_targets[$ind]['name'] }}</a></td>
                    <td>
                      @if(count($arrange_targets[$ind]['results'])>0)
                        @foreach($arrange_targets[$ind]['results'] as $ip=>$pi)
                          @foreach($pi as $bn=>$nb)
                            @foreach($arrange_targets[$ind]['tg_rule'] as $yh=>$hy)
                              @if($arrange_targets[$ind]['tg_rule'][$yh]==1)
                                @php $act_interval = $arrange_targets[$ind]['tg_values_calculated'][$ip][$bn][1] @endphp
                                <span>{{ $arrange_targets[$ind]['results'][$ip][$bn][1] }}/{{ $act_interval }}</span>
                              @endif      
                            @endforeach
                          @endforeach
                        @endforeach
                      @endif
                    </td>
                    <td>
                      @if(count($arrange_targets[$ind]['results'])>0)
                        @foreach($arrange_targets[$ind]['results'] as $ip=>$pi)
                          @foreach($pi as $bn=>$nb)
                            @foreach($arrange_targets[$ind]['tg_rule'] as $yh=>$hy)
                              @if($arrange_targets[$ind]['tg_rule'][$yh]==2)
                                @php $act_interval = $arrange_targets[$ind]['tg_values_calculated'][$ip][$bn][2] @endphp
                                <span>{{ $arrange_targets[$ind]['results'][$ip][$bn][2] }}/{{ $act_interval }}</span>
                              @endif      
                            @endforeach
                          @endforeach
                        @endforeach
                      @endif
                    </td>
                    <td>
                      @if(count($arrange_targets[$ind]['results'])>0)
                        @foreach($arrange_targets[$ind]['results'] as $ip=>$pi)
                          @foreach($pi as $bn=>$nb)
                            @foreach($arrange_targets[$ind]['tg_rule'] as $yh=>$hy)
                              @if($arrange_targets[$ind]['tg_rule'][$yh]==3)
                                @php $act_interval = $arrange_targets[$ind]['tg_values_calculated'][$ip][$bn][3] @endphp
                                <span>{{ $arrange_targets[$ind]['results'][$ip][$bn][3] }}/{{ $act_interval }}</span>
                              @endif      
                            @endforeach
                          @endforeach
                        @endforeach
                      @endif
                    </td>
                    <td>
                      @if(count($arrange_targets[$ind]['results'])>0)
                        @foreach($arrange_targets[$ind]['results'] as $ip=>$pi)
                          @foreach($pi as $bn=>$nb)
                            @foreach($arrange_targets[$ind]['tg_rule'] as $yh=>$hy)
                              @if($arrange_targets[$ind]['tg_rule'][$yh]==4)
                                @php $act_interval = $arrange_targets[$ind]['tg_values_calculated'][$ip][$bn][4] @endphp
                                <span>{{ $arrange_targets[$ind]['results'][$ip][$bn][4] }}/{{ $act_interval }}</span>
                              @endif      
                            @endforeach
                          @endforeach
                        @endforeach
                      @endif
                    </td>
                    <td>
                      @if(count($arrange_targets[$ind]['results'])>0)
                        @foreach($arrange_targets[$ind]['results'] as $ip=>$pi)
                          @foreach($pi as $bn=>$nb)
                            @foreach($arrange_targets[$ind]['tg_rule'] as $yh=>$hy)
                              @if($arrange_targets[$ind]['tg_rule'][$yh]==5)
                                @php $act_interval = $arrange_targets[$ind]['tg_values_calculated'][$ip][$bn][5] @endphp
                                <span>{{ $arrange_targets[$ind]['results'][$ip][$bn][5] }}/{{ $act_interval }}</span>
                              @endif      
                            @endforeach
                          @endforeach
                        @endforeach
                      @endif
                    </td>
                    <td>
                      @if(count($arrange_targets[$ind]['results'])>0)
                        @foreach($arrange_targets[$ind]['results'] as $ip=>$pi)
                          @foreach($pi as $bn=>$nb)
                            @foreach($arrange_targets[$ind]['tg_rule'] as $yh=>$hy)
                              @if($arrange_targets[$ind]['tg_rule'][$yh]==6)
                                @php $act_interval = $arrange_targets[$ind]['tg_values_calculated'][$ip][$bn][6] @endphp
                                <span>{{ $arrange_targets[$ind]['results'][$ip][$bn][6] }}/{{ $act_interval }}</span>
                              @endif      
                            @endforeach
                          @endforeach
                        @endforeach
                      @endif
                    </td>
                    <td>
                      @if(count($arrange_targets[$ind]['results'])>0)
                        @foreach($arrange_targets[$ind]['results'] as $ip=>$pi)
                          @foreach($pi as $bn=>$nb)
                            @foreach($arrange_targets[$ind]['tg_rule'] as $yh=>$hy)
                              @if($arrange_targets[$ind]['tg_rule'][$yh]==7)
                                @php $act_interval = $arrange_targets[$ind]['tg_values_calculated'][$ip][$bn][7] @endphp
                                <span>{{ $arrange_targets[$ind]['results'][$ip][$bn][7] }}/{{ $act_interval }}</span>
                              @endif      
                            @endforeach
                          @endforeach
                        @endforeach
                      @endif
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

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script> -->

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="{{asset('assets/dist/js/tether.min.js') }}"></script>

@if(config('settings.ncal')==0)
  <script src="{{asset('assets/dist/js/dateRangePicker.js') }}"></script>
@else
  <script src="{{asset('assets/dist/js/dateRangePickerNp.js') }}"></script>
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  <script src="{{asset('assets/dist/js/dateRangePickerNp_2.js') }}"></script>

@endif


<script>

var daterangeval = [];


@if(config('settings.ncal')==0)
  $('.selectMonths:first input')
    .rangePicker({ minDate:[01,2015], maxDate:[01,2025] })
@else
  $('.selectMonths:first input')
    .rangePicker({ minDate:[01,2072], maxDate:[01,2078] })
@endif
$(document).click(function(){
  if( $('.daterange').hasClass('show') ) {
    $('.daterange').removeClass('show');
  }
  var previoustext = $("#daterange").val();
  var str = $("#daterange").val(); 
  var n = str.search("defi");
  if(n==2){
    $("#daterange").empty().val('');
  }
});
 

  $(document).ready(function () {

    var pickedYear = new Date().getFullYear();
    var pickedMonth = new Date().getMonth() + 1;
    $('#year').val(pickedYear);
    $('#month').val(pickedMonth);
    $('#getReport').click();

    initializeDT();
    $("#salesmanid").trigger('change');
    var url = window.location.href;
    var aab = url.split('/');
    if(aab.length==8){
      if(aab[7]=='np'){
        $("#daterange").val(ucfirst(window.sessionStorage.getItem('nepalimonth')));
      }
    }else{
      @if(config('settings.ncal')!=0)
        var npmonthhs = {'01':'Baisakh','02':'Jestha','03':'Asadh','04':'Shrawn','05':'Bhadra','06':'Asoj','07':'Kartik','08':'Mangsir','09':'Poush','10':'Mangsir','11':'Falgun','12':'Chaitra'};
        var currentdate= AD2BS(moment().format('YYYY-MM-DD'));
        var currmonth = (currentdate.split('-'))[1];
        daterange = npmonthhs[currmonth];
        $("#daterange").val(ucfirst(daterange));
      @endif
    }

    $(".calendar.from").find('select').find('option').each(function(){
      var years = parseInt($(this).val());
      var curyear = new Date().getFullYear();
      if(years==curyear){
        $(".calendar.from").find('select').find('option[value="'+curyear+'"]').attr('selected','selected');
      }
    });

  });

  function ucfirst(str) {
    var firstLetter = str.substr(0, 1);
    return firstLetter.toUpperCase() + str.substr(1);
  }

  function initializeDT(brand=null, category=null){
      @if(config('settings.ncal')==0)
        var daterange = $("#daterange").val();
      @else
        if(window.sessionStorage.getItem('nepalimonth')){
          var daterange = window.sessionStorage.getItem('nepalimonth').toUpperCase();
        }else{
          var npmonthhs = {'01':'Baisakh','02':'Jestha','03':'Asadh','04':'Shrawn','05':'Bhadra','06':'Asoj','07':'Kartik','08':'Mangsir','09':'Poush','10':'Mangsir','11':'Falgun','12':'Chaitra'};
          var currentdate= AD2BS(moment().format('YYYY-MM-DD'));
          var currmonth = (currentdate.split('-'))[1];
          daterange = npmonthhs[currmonth];
        }
      @endif
      
      const table = $('#product').removeAttr('width').DataTable({
        "processing": true,
        "serverSide": false,
        "order": [[ 0, "asc" ]],
        "columnDefs": [
          {
            "orderable": false,
            "targets":[2,3,4,5,6,7,8],
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
                columns:[0,1,2,3,4,5,6,7,8],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },
          {

            extend: 'excelHtml5',
            title: 'Salesman Target Report ('+daterange+')',
            exportOptions: {
              columns: ':visible'
            },
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // }
          },
          {
            extend: 'pdfHtml5',
            title: 'Salesman Target Report ('+daterange+')',
            exportOptions: {
              columns: ':visible'
            }
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // }
          },
          {
            extend: 'print',
            title: 'Salesman Target Report ('+daterange+')',
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
          { "data": "no_oforder" },
          { "data": "value_order" },
          { "data": "noof_collection" },
          { "data": "value_collection" },
          { "data": "noof_visits" },
          { "data": "golden_calls" },
          { "data": "total_calls" },
        ],
      });
      table.buttons().container().appendTo('#productexports');
    }



$('.multClass').multiselect({
  enableFiltering: true,
  enableCaseInsensitiveFiltering: true,
  enableFullValueFiltering: false,
  enableClickableOptGroups: false,
  includeSelectAllOption: true,
  enableCollapsibleOptGroups : true,
  selectAllNumber: false,
  nonSelectedText:"Select Salesman",
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


  // $('.brandsDiv').removeClass('hidden');
  $('.target_duration').removeClass('hidden');

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


  $("#searchterm").on('click',function(){
    var yearmonth = $("#daterange").val().replace(/\s/g, '');
    var salesmanid = '';
    var params = ''; //yearmonth.length
    var ueirf = 'en';
    $("#salesmanid :selected").map(function(ind,el){
      salesmanid += $(el).val()+'-';
    }).get();
    salesmanid = salesmanid.split('-');
    salesmanid.splice(-1,1);
    salesmanid = salesmanid.join('-');
    @if(config('settings.ncal')!=0)
      if(yearmonth.length>16){
        alert('Select Proper Date');
        return false;
      }else{
        ueirf = 'np';
        window.sessionStorage.setItem("nepalimonth",yearmonth);
        yearmonth = convertnepalidate(yearmonth);
      }
    @endif
    if(salesmanid=='' && yearmonth!=''){ 
      if(yearmonth.length>16){
        params = '';
      }else{
        params = '/'+yearmonth+'/'+'a/'+ueirf;
      }    
    }else if(yearmonth=='' && salesmanid!=''){
      params = '/a'+'/'+salesmanid+'/'+ueirf;
    }else if(yearmonth!='' && salesmanid!=''){
      if(yearmonth.length>16){
        params = '/a'+'/'+salesmanid+'/'+ueirf;
      }else{
        params = '/'+yearmonth+'/'+salesmanid+'/'+ueirf;
      }
    }else{
      params = '';
    }
    url = "{{ domain_route('company.admin.salesmantargetreport') }}"+params;
    window.location.href = url;
  });

 
  $("#salesmanid").on('change',function(seleids){
    var totalselectedvalues = 0;
    var tot_selected = $(this).find('option:selected').map(function() {
        totalselectedvalues++;
    });
    if(totalselectedvalues>1){
      $("#daterange").val('');
      $("#daterange").attr('disabled','disabled');
    }else{
      $("#daterange").removeAttr('disabled');
    }
  });


  function convertnepalidate(nepdate=''){
    var npmont = ['Baisakh','Jestha','Asadh','Shrawn','Bhadra','Asoj','Kartik','Mangsir','Poush','Mangsir','Falgun','Chaitra'];
    var npmont_2 = {'Baisakh':'01','Jestha':'02','Asadh':'03','Shrawn':'04','Bhadra':'05','Asoj':'06','Kartik':'07','Mangsir':'08','Poush':'09','Mangsir':'10','Falgun':'11','Chaitra':'12'};
    var enmont = {'01':'January','02':'February','03':'March','04':'April','05':'May','06':'June','07':'July','08':'August','09':'September','10':'October','11':'November','12':'December'};
    var npdatemonth1 = npdatemonth2 = totendate1 = totendate2 = curenmon1 = curenmon2 = findat = '';
    var currentnpyear = NepaliFunctions.GetCurrentBsDate()['year'];
    var nepdate1 = nepdate.split('-');
    npdatemonth1 = nepdate1[0];
    npdatemonth2 = (nepdate1[1])?nepdate1[1]:'';
    if(npdatemonth1!='' && npdatemonth2!=''){
      if(npmont.includes(npdatemonth1) && npmont.includes(npdatemonth2)){
        totendate1 = ((BS2AD(currentnpyear+'-'+npmont_2[npdatemonth1]+'-'+'01')).split('-'))[1];
        totendate2 = ((BS2AD(currentnpyear+'-'+npmont_2[npdatemonth2]+'-'+'01')).split('-'))[1];
        curenmon1 = enmont[totendate1];
        curenmon2 = enmont[totendate2];
        findat = curenmon1+'-'+curenmon2;
        return findat;
      }else{
        var npmon = {'bai':'01','jes':'02','asa':'03','shr':'04','bha':'05','aso':'06','kar':'07','man':'08','pou':'09','mag':'10','fal':'11','cha':'12'};
        var enmon = {'01':'jan','02':'feb','03':'mar','04':'apr','05':'may','06':'jun','07':'jul','08':'aug','09':'sep','10':'oct','11':'nov','12':'dec'};
        var npdate = nepdate.split('-');var dys = '01';
        var npyear1 = npyear2 = npmonth1 = npmonth2 = convtdate1 = convtdate2 = '';
        var enyear1 = enyear2 = enmonth1 = enmonth2 = findate = '';
        npyear1 = npdate[0].substr(3,6);
        npmonth1 = npmon[npdate[0].substr(0,3)];
        npyear2 = npdate[1].substr(3,6);
        npmonth2 = npmon[npdate[1].substr(0,3)];
        npcompdate1 = BS2AD(npyear1+'-'+npmonth1+'-'+dys);
        npcompdate2 = BS2AD(npyear2+'-'+npmonth2+'-'+dys);
        convtengdate1 = npcompdate1.split('-');
        enmonth1 = enmon[convtengdate1[1]];
        enyear1 = convtengdate1[0];
        convtengdate2 = npcompdate2.split('-');
        enmonth2 = enmon[convtengdate2[1]];
        enyear2 = convtengdate2[0];
        convtdate1 = enmonth1+enyear1;
        convtdate2 = enmonth2+enyear2;
        findate =  convtdate1+'-'+convtdate2;
        return findate;
      }
    }else{
      totendate1 = ((BS2AD(currentnpyear+'-'+npmont_2['Baisakh']+'-'+'01')).split('-'))[1];
      findat = enmont[totendate1];
      return findat;
    }
  }


</script>

@endsection