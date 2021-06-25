<?php
// foreach($data as $k=>$v){
// foreach($v as $a=>$b){
//   if($k==$b['target_groupid']){
//       // <!-- {{ $tgtopt[$b['target_rules']] }} -->
//       foreach($v as $a=>$b){
//         if($k==$b['target_groupid']){
//             if(((int)$b['target_rules'])==1){
//               if(config('settings.orders')==1){
//                 echo $tgtopt[$b['target_rules']] ;
//               }
//             }elseif(((int)$b['target_rules'])==2) {
//               if(config('settings.orders')==1){
//                 echo $tgtopt[$b['target_rules']] ;
//               }
//             }elseif(((int)$b['target_rules'])==3){
//               if(config('settings.collections')==1){
//                 echo $tgtopt[$b['target_rules']] ;
//               }
//             }elseif(((int)$b['target_rules'])==4){
//               if(config('settings.collections')==1){
//                 echo $tgtopt[$b['target_rules']] ;
//               }
//             }elseif(((int)$b['target_rules'])==5){
//               if(config('settings.visit_module')==1){
//                 echo $tgtopt[$b['target_rules']] ;
//               }
//             }elseif(((int)$b['target_rules'])==6){
//               if(config('settings.party')==1){
//                 echo $tgtopt[$b['target_rules']];
//               }
//             }elseif(((int)$b['target_rules'])==7){
//               if(config('settings.orders')==1 && config('settings.zero_orders')==1){
//                 echo $tgtopt[$b['target_rules']] ;
//               }
//             }
//           }
//         echo '<br>';
//       }

//   }
// }
// }
// die();
?>




@extends('layouts.company')
@section('title', 'Salesman TargetList')
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
          <h3 class="box-title">Target List</h3>
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
                  <div class="col-xs-3">
                    <div class="brandsDiv hidden" style="margin-top:10px;">
                        
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <div class="categoriesDiv hidden" style="margin-top:10px;">
                     
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
                <th>S.No.</th>
                <th>Target Name</th>
                <th>Target Rules</th>
                <th>Target Interval</th>
                <th>Target Value</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php $tot_targets = count($data);$c=1; $tgtgrpid = ''; @endphp
              @if($tot_targets>0)
                @foreach($data as $k=>$v)
                  @if($v[0]['target_groupid']!=0)
                    <tr>
                        <td>{{ $c++ }}</td>
                        <td>
                          <b>{{ $v[0]['target_name'] }} @php $tgtgrpid = $v[0]['target_groupid']; @endphp</b>
                        </td>
                        <td>
                          @php $c1rule = 'npres'; @endphp
                          @foreach($v as $a=>$b)
                            @php $c1 = 'nbr'; @endphp
                            @if($k==$b['target_groupid'])                               
                                @if($b['target_rules']=='1')
                                  @if(config('settings.orders')==1)
                                    {{ $tgtopt[$b['target_rules']] }}
                                    @php $c1 = 'br'; @endphp
                                    @php $c1rule = 'pres'; @endphp
                                  @endif
                                @elseif($b['target_rules']=='2') 
                                  @if(config('settings.orders')==1)
                                    {{ $tgtopt[$b['target_rules']] }}
                                    @php $c1 = 'br'; @endphp
                                    @php $c1rule = 'pres'; @endphp
                                  @endif
                                @elseif($b['target_rules']=='3')
                                  @if(config('settings.collections')==1)
                                    {{ $tgtopt[$b['target_rules']] }}
                                    @php $c1 = 'br'; @endphp
                                    @php $c1rule = 'pres'; @endphp
                                  @endif
                                @elseif($b['target_rules']=='4')
                                  @if(config('settings.collections')==1)
                                    {{ $tgtopt[$b['target_rules']] }}
                                    @php $c1 = 'br'; @endphp
                                    @php $c1rule = 'pres'; @endphp
                                  @endif
                                @elseif($b['target_rules']=='5')
                                  @if(config('settings.visit_module')==1)
                                    {{ $tgtopt[$b['target_rules']] }}
                                    @php $c1 = 'br'; @endphp
                                    @php $c1rule = 'pres'; @endphp
                                  @endif
                                @elseif($b['target_rules']=='6')
                                  @if(config('settings.party')==1)
                                    {{ $tgtopt[$b['target_rules']] }}
                                    @php $c1 = 'br'; @endphp
                                    @php $c1rule = 'pres'; @endphp
                                  @endif
                                @elseif($b['target_rules']=='7')
                                  @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
                                    {{ $tgtopt[$b['target_rules']] }}
                                    @php $c1 = 'br'; @endphp
                                    @php $c1rule = 'pres'; @endphp
                                  @endif
                                @endif
                            @endif
                            @if($c1=='br')
                              <br>
                            @endif
                          @endforeach
                          @if($c1rule=='npres')
                            --
                          @endif
                        </td>
                        <td>
                          @php $c2rule = 'npres'; @endphp
                          @foreach($v as $a=>$b)
                            @php $c2 = 'nbr'; @endphp
                            @if($k==$b['target_groupid'])
                              @if($b['target_rules']==1)
                                @if(config('settings.orders')==1)
                                  @if($b['target_interval']==1)
                                    Daily
                                  @elseif($b['target_interval']==2)
                                    Weekly
                                  @else
                                    Monthly
                                  @endif
                                  @php $c2 = 'br'; @endphp
                                  @php $c2rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==2)
                                @if(config('settings.orders')==1)
                                  @if($b['target_interval']==1)
                                    Daily
                                  @elseif($b['target_interval']==2)
                                    Weekly
                                  @else
                                    Monthly
                                  @endif
                                  @php $c2 = 'br'; @endphp
                                @endif
                              @elseif($b['target_rules']==3)
                                @if(config('settings.collections')==1)
                                  @if($b['target_interval']==1)
                                    Daily
                                  @elseif($b['target_interval']==2)
                                    Weekly
                                  @else
                                    Monthly
                                  @endif
                                  @php $c2 = 'br'; @endphp
                                  @php $c2rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==4)
                                @if(config('settings.collections')==1)
                                  @if($b['target_interval']==1)
                                    Daily
                                  @elseif($b['target_interval']==2)
                                    Weekly
                                  @else
                                    Monthly
                                  @endif
                                  @php $c2 = 'br'; @endphp
                                  @php $c2rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==5)
                                @if(config('settings.visit_module')==1)
                                  @if($b['target_interval']==1)
                                    Daily
                                  @elseif($b['target_interval']==2)
                                    Weekly
                                  @else
                                    Monthly
                                  @endif
                                  @php $c2 = 'br'; @endphp
                                  @php $c2rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==6)
                                @if(config('settings.party')==1)
                                  @if($b['target_interval']==1)
                                    Daily
                                  @elseif($b['target_interval']==2)
                                    Weekly
                                  @else
                                    Monthly
                                  @endif
                                  @php $c2 = 'br'; @endphp
                                  @php $c2rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==7)
                                @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
                                  @if($b['target_interval']==1)
                                    Daily
                                  @elseif($b['target_interval']==2)
                                    Weekly
                                  @else
                                    Monthly
                                  @endif
                                  @php $c2 = 'br'; @endphp
                                  @php $c2rule = 'pres'; @endphp
                                @endif
                              @endif
                            @endif
                            @if($c2=='br')
                              <br>
                            @endif
                          @endforeach
                          @if($c2rule=='npres')
                            --
                          @endif
                        </td>
                        <td>
                          @php $c3rule = 'npres'; @endphp
                          @foreach($v as $a=>$b)
                            @php $c3 = 'nbr'; @endphp
                            @if($k==$b['target_groupid'])
                              @if($b['target_rules']==1)
                                @if(config('settings.orders')==1)
                                  {{$b['target_value']}}
                                  @php $c3 = 'br'; @endphp
                                  @php $c3rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==2)
                                @if(config('settings.orders')==1)
                                  {{$b['target_value']}}
                                  @php $c3 = 'br'; @endphp
                                  @php $c3rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==3)
                                @if(config('settings.collections')==1)
                                  {{$b['target_value']}}
                                  @php $c3 = 'br'; @endphp
                                  @php $c3rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==4)
                                @if(config('settings.collections')==1)
                                  {{$b['target_value']}}
                                  @php $c3 = 'br'; @endphp
                                  @php $c3rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==5)
                                @if(config('settings.visit_module')==1)
                                  {{$b['target_value']}}
                                  @php $c3 = 'br'; @endphp
                                  @php $c3rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==6)
                                @if(config('settings.party')==1)
                                  {{$b['target_value']}}
                                  @php $c3 = 'br'; @endphp
                                  @php $c3rule = 'pres'; @endphp
                                @endif
                              @elseif($b['target_rules']==7)
                                @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
                                  {{$b['target_value']}}
                                  @php $c3 = 'br'; @endphp
                                  @php $c3rule = 'pres'; @endphp
                                @endif
                              @endif
                            @endif
                            @if($c3=='br')
                              <br>
                            @endif
                          @endforeach
                          @if($c3rule=='npres')
                            --
                          @endif
                        </td>
                        <td> 
                          <a class='btn btn-success btn-sm' id="{{$v[0]['target_groupid']}}" onClick="showTargetUpdHistory(this)" style='padding: 3px 6px;'><i class='fa fa-eye'></i></a>
                          @if(!in_array($tgtgrpid,$emptargets))
                            @if(Auth::user()->can('targets-update'))
                              <a onclick="return alertedittarget()" href="{{ domain_route('company.admin.salesmantargetlist.edit',[$v[0]['target_groupid']])}}" class='btn btn-warning btn-sm' style='padding: 3px 6px;'><i class='fa fa-edit'></i></a>
                            @endif
                            @if(Auth::user()->can('targets-delete'))
                              @if(in_array($v[0]['target_groupid'],$canbedeleted))
                                <a onClick="delcompletegroup(this)" id="del_{{$v[0]['target_groupid']}}" class='btn btn-danger btn-sm' style='padding: 3px 6px;'><i class='fa fa-trash-o'></i></a>
                              @endif
                            @endif
                          @endif
                        </td>
                    </tr>
                  @endif
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
          <p class="text-center">
            Are you sure you want to delete selected items?
          </p>

        </div>
        <input type="hidden" name="product_id[]" id="product_ids" value="">

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


<!-- Modal -->
<div id="showTargetUpdHistory" class="modal fade" role="dialog">
  <div class="modal-dialog small-modal">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Targets Update History</h4>
      </div>
      <div class="modal-body">
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
                  <th>Updated Date</th>
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
          </button>--}}
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

  function alertedittarget(){
    if(confirm("All users who currently have this target will be assigned new target. Are you sure you want to modify this target?")==1){
      return true;
    }else{
      return false;
    }
  }
 
  function delcompletegroup(grptid){
    var groupid = parseInt((grptid.id).split('_')[1]);
    if(confirm('Are you sure you want to delete this Target???')){
      $.ajax({
        url: "{{ domain_route('company.admin.salesmanindivtargetlist.delete') }}",
        type: "post",
        data: {groupid},
        success:function(res){
          alert(res.msg);
          location.reload();
        },
        error: function(res){
          var resptext = JSON.parse(res.responseText);
          alert(resptext.error);
        }
      });
    }
  }


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
            "targets":[5],
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
                columns:[0,1,2,3,4,5],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },
          {

            extend: 'excelHtml5',
            title: 'Target List',
            exportOptions: {
              columns: ':visible'
            }
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // }
          },
          {
            extend: 'pdfHtml5',
            title: 'Target List',
            exportOptions: {
              columns: ':visible'
            }
            // action: function ( e, dt, node, config ) {
            //   newExportAction( e, dt, node, config );
            // }
          },
          {
            extend: 'print',
            title: 'Target List',
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
            { "data": "target_name" },
            { "data": "target_rules" },
            { "data": "target_interval" },
            { "data": "target_value" },
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
        data.length = 10;
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


  function showTargetUpdHistory(divinfo){
    var target_groupid = parseInt(divinfo.id);
    $.ajax({
      type: 'post',
      url: "{{ domain_route('company.admin.salesmanindivtargethistory') }}"+'/'+target_groupid,
      data: {sid: target_groupid},
      success:function(res){
        $("#historybody").html(res.msg);
        $("#showTargetUpdHistory").modal('show');
      },
      error:function(res){
        var resptext = JSON.parse(res.responseText);
        $("#historybody").html(resptext.msg);
      }
    });
  }


 

</script>

@endsection