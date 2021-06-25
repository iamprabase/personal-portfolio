@extends('layouts.company')
@section('title', 'Activities')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<style>
	#activityexports .btn {
	    padding: 10px 6px !important;
	}
	.select-2-sec{
		margin-top: 0px;
	}
	.box-loader{
    opacity: 0.5;
  }
  .btn {
    border-radius: 3px;
    -webkit-box-shadow: none;
    box-shadow: none;
    border: 1px solid transparent;
    height: 40px!important;
    padding: 10px 5px;
	}
	/*.round {*/
	  /*position: relative;*/
	/*}*/

	/*.round label {*/
	  /*background-color: #fff;*/
	  /*border: 1px solid #ccc;*/
	  /*border-radius: 50%;*/
	  /*cursor: pointer;*/
	  /*height: 28px;*/
	  /*left: 0;*/
	  /*position: absolute;*/
	  /*top: 10px;*/
	  /*width: 28px;*/
	/*}*/

	/*.round label:after {*/
	  /*border: 2px solid #fff;*/
	  /*border-top: none;*/
	  /*border-right: none;*/
	  /*content: "";*/
	  /*height: 6px;*/
	  /*left: 7px;*/
	  /*opacity: 0;*/
	  /*position: absolute;*/
	  /*top: 8px;*/
	  /*transform: rotate(-45deg);*/
	  /*width: 12px;*/
	/*}*/

	/*.round input[type="checkbox"] {*/
	  /*visibility: hidden;*/
	/*}*/

	/*.round input[type="checkbox"]:checked + label {*/
	  /*background-color: #66bb6a;*/
	  /*border-color: #66bb6a;*/
	/*}*/

	/*.round input[type="checkbox"]:checked + label:after {*/
	  /*opacity: 1;*/
	/*}*/

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
</style>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			@if (\Session::has('success'))
				<div class="alert alert-success">
					<p>{{ \Session::get('success') }}</p>
				</div><br/>						
			@endif
			@if (\Session::has('alert'))
				<div class="alert alert-warning">
					<p>{{ \Session::get('alert') }}</p>
				</div><br/>						
			@endif
			<input type="hidden" name="search" id="search"  value="{{ isset($_GET['search'])?$_GET['search']:'today'}}">

			<div class="box">
				<div class="box-header">
					<div class="row">
						<div class="col-xs-2">
							<h3 class="box-title">Activities List</h3>
						</div>
						<div class="col-xs-4">
							<div class="btn-group controller" role="group" id="searchGroup">
								<a href="#"><button id="f_schedule" type="button"  value="0" class="btn btn-primary f_scheduled">Scheduled</button></a>
								<a href="#"><button id="f_overdue" type="button" value="0" class="btn btn-primary f_overdue">Overdue</button></a>
								<a href="#"><button id="f_completed" type="button" value="0" class="btn btn-primary f_completed">Completed</button></a>
								<a href="#"><button id="f_today" type="button" value="1" class="btn btn-primary f_today active">Today</button></a>
							</div>
						</div>
						<div class="col-xs-2">
							@if(config('settings.ncal')==0)
							<div id="reportrange" name="reportrange" class="reportrange">
								<i class="fa fa-calendar"></i>&nbsp;<span><i class="fa fa-caret-down"></i></span> 
							</div>
							<input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
							<input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
							@else
							<div class="row">
								<div class="col-xs-6"><input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date" />
									<input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
								</div>
								<div class="col-xs-6"><input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date" />
									<input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
									<button id="filterTable" style="color:#0b7676!important;" hidden><i class="fa fa-filter" aria-hidden="true"></i></button>
								</div>
							</div>
							@endif
						</div>
						<div class="col-xs-4">
							@if(Auth::user()->can('activity-create'))
							<a href="{{ domain_route('company.admin.activities.create') }}" class="btn btn-primary pull-right" style="margin-left: 5px;"><i class="fa fa-plus"></i> Create New</a>
							@endif
							<span id="activityexports" class="pull-right"></span>
						</div>
					</div>
				</div>

				<div class="box-body" id="ActivityBody">
					<div class="row">
						<div class="col-xs-2"></div>
						<div class="col-xs-7">
							<div class="row">
								<div class="select-2-sec">
									<div class="col-xs-3">
										<div style="margin-top:10px;height: 40px;z-index: 999 " id="assignedBy">
											<select id="assignBy" class="select2 hide">
												<option value="null">Assigned By</option>
												@foreach($createdBy as $emp)
												<option value="{{$emp->id}}">{{$emp->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-xs-3">
										<div style="margin-top:10px;height: 40px;z-index: 999 " id="assignedTo">
											<select id="assignTo" class="select2 hide">
												<option value="null">Assigned To</option>
												@foreach($assignedTo as $emp)
												<option value="{{$emp->id}}">{{$emp->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-xs-3 @if(config('settings.party')==0) hide @endif">
										<div style="margin-top:10px;height: 40px;z-index: 999 " id="partyfilter">
											<select id="partyID" class="select2 hide">
												<option value="null">Party</option>
												@foreach($parties as $party)
												<option value="{{$party->id}}">{{$party->company_name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-xs-3">
										<div style="margin-top:10px;height: 40px;z-index: 999 " id="activityType">
											<select id="activitytypefilter" class="select2 hide">
												<option value="null">Activity Type</option>
												@foreach($activityType as $type)
												<option value="{{$type->id}}">{{$type->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-3"></div>
					</div>
				</div>
				<div id="loader1" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>

        <div id="mainBox">
					<table id="activities" class="table table-bordered table-striped">

						<thead>
							<tr>
								<th>#</th>
								<th>Completed</th>
								<th>Assigned By</th>
								<th>Assigned To</th>
								<th>Title</th>
								<th>Party</th>
								<th>Type</th>
								<th>Priority</th>
								<th>Date</th>
								<th style="min-width: 76px;">Action</th>
							</tr>
						</thead>
					</table>
				</div>

			</div>
		</div>
	</div>
</section>
<!-- Modal -->
<div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
        </div>
          <div class="modal-body">
            <p class="text-center">
              Sorry! You are not authorized to view this user info.
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
<div class="modal modal-default fade" id="alertPartyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
        </div>
          <div class="modal-body">
            <p class="text-center">
              Sorry! You are not authorized to view this party details.
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
<div class="modal modal-default fade" id="alertCompleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
        </div>
          <div class="modal-body">
          	@if(Auth::user()->can('activity-status'))
            <p class="text-center">
              Sorry! Only Assignor or Assignee can mark activity as <span id="textComplete">complete</span>.
            </p>
        @else
        	<p class="text-center">
             You are not permitted to change activity status
            </p>
    @endif
            <input type="hidden" name="expense_id" id="c_id" value="">
            <input type="text" id="accountType" name="account_type" hidden/>
          </div>
          <div class="modal-footer">
            {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
          </div>
      </div>
    </div>
</div>
<div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
data-keyboard="false" data-backdrop="static">

	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
			</div>

			<form method="post" class="remove-record-model">
				{{method_field('delete')}}
				{{csrf_field()}}
				<div class="modal-body">
					<p class="text-center">
						Are you sure you want to delete this?
					</p>
					<input type="hidden" name="client_id" id="c_id" value="">
				</div>

				<div class="modal-footer">
					{{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
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
				action="{{URL::to('admin/activity/changeStatus')}}">
				{{csrf_field()}}
					<input type="hidden" name="client_id" id="client_id" value="">
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

<form method="post" action="{{domain_route('company.admin.activities.customPdfExport')}}" class="pdf-export-form hidden"
  id="pdf-generate">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
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
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/plugins/sweetalert2/sweetalert2.all.min.js')}}" type="text/javascript" charset="utf-8" async defer></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
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

		var start = moment().subtract(3, 'months').format('YYYY-MM-DD');
	  var end = moment().add(3,'months').format('YYYY-MM-DD');
	  var f1 = $('#f_schedule').val();
		var f2 = $('#f_overdue').val();
		var f3 = $('#f_completed').val();
		var f4 = $('#f_today').val();
		var ftype = [f1,f2,f3,f4];
    initializeDT(start,end,null,null,null,null,ftype);

    function initializeDT(startD, endD,assignedBy=null,assignedTo=null,client_id=null,type=null,ftype=null)
    {
    	var table = $('#activities').DataTable({
    		"order": [[ 8, "desc" ]],
    		"processing": true,
    		"serverSide": true,
    		"ajax":{
    			"url": "{{ domain_route('company.admin.activities.ajaxTable') }}",
    			"dataType": "json", 
    			"type": "POST",
    			"data":{ 
    				_token: "{{csrf_token()}}",
    				startDate: startD,
    				endDate: endD,
    				assignedBy:assignedBy,
    				assignedTo:assignedTo,
    				client_id:client_id,
    				type:type,
    				ftype:ftype,
    			},            
    			beforeSend:function(){
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
    			},
    		},         
    		"columnDefs": [ {
    			"targets": [-1],
    			"orderable": false,},
    			@if(config('settings.party')==0)
    			{
	                "targets": [ 5 ],
	                "visible": false,
	                "searchable": false
	            },  
	            @endif
    			],
    			"columns": [
    			{ "data": "id" },
    			{ "data": "completion_datetime" },
    			{ "data": "AssignedByName" },
    			{ "data": "AssignedToName" },
    			{ "data": "title" },
    			{ "data": "PartyName" },
    			{ "data": "TypeName" },
    			{ "data": "PriorityName" },
    			{ "data": "start_datetime" },
    			{ "data": "action" },
    			],
    			"dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
    			"<'row'<'col-xs-6'><'col-xs-6'>>" +
    			"<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
    			buttons: [
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
    				title: 'Activity List',
    				exportOptions: {
              columns: ':visible:not(:last-child)'
            },
    				footer: true,
    				action: function ( e, dt, node, config ) {
    					newExportAction( e, dt, node, config );
    				}
    			},
    			{
    				extend: 'pdfHtml5',
    				title: 'Activity List',
    				exportOptions: {
              columns: ':visible:not(:last-child)'
            },
    				orientation:'landscape',
    				footer: true,
    				action: function ( e, dt, node, config ) {
    					newExportAction( e, dt, node, config );
    				}
    			},
    			{
    				extend: 'print',
    				title: 'Activity List',
    				exportOptions: {
              columns: ':visible:not(:last-child)'
            },
    				footer: true,
    				action: function ( e, dt, node, config ) {
    					newExportAction( e, dt, node, config );
    				}
    			},
    			],
    		});
    	table.buttons().container()
    	.appendTo('#activityexports');

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
    			data.length = {{$activitiesCount}};
    			dt.one('preDraw', function (e, settings) {
            if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
              var columnsArray = [];
              var visibleColumns = settings.aoColumns.map(setting => {
                                      if(setting.bVisible) columnsArray.push(setting.sTitle)
                                    })
              columnsArray.pop("Action")
              // columnsArray.push("S.No.", "Party Name", "Salesman", "Date", "Remark");
              var columns = JSON.stringify(columnsArray);
              $.each(settings.json.data, function(key, htmlContent){
                settings.json.data[key].id = key+1;
                if($(htmlContent.completion_datetime).find('input').first().is(":checked")){
                  settings.json.data[key].completion_datetime = "Yes";
                }else{
                  settings.json.data[key].completion_datetime = "No";  
                }
                settings.json.data[key].PartyName = $(settings.json.data[key].PartyName)[0].textContent;
                settings.json.data[key].AssignedByName = $(settings.json.data[key].AssignedByName)[0].textContent;
                settings.json.data[key].AssignedToName = $(settings.json.data[key].AssignedToName)[0].textContent;
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
    }

    function customExportAction(config, settings, cols){
      $('#exportedData').val(JSON.stringify(settings.json));
      $('#pageTitle').val(config.title);
      $('#columns').val(cols);
      var propertiesArray = [];
      var visibleColumns = settings.aoColumns.map(setting => {
                            if(setting.bVisible) propertiesArray.push(setting.data)
                          })
      propertiesArray.pop("action");
      // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
      var properties = JSON.stringify(propertiesArray);
      $('#properties').val(properties);
      $('#pdf-generate').submit();
    }

    @if(config('settings.ncal')==0)
			var start = moment().subtract(3, 'months');
    	var end = moment().add(3,'months');
			function cb(start, end,check) {
				if(check==true){
					$('#reportrange span').html('Choose Date');
				}else{
					$('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
				}
			}
			$('#reportrange').daterangepicker({
				startDate: start,
				endDate: end,
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}
			}, cb);
			var check=true;
			cb(start, end,check);
			$('#start_edate').val(start.format('YYYY-MM-DD'));
			$('#end_edate').val(end.format('YYYY-MM-DD'));

			$('#reportrange').on('apply.daterangepicker', function (ev, picker) {
				ev.preventDefault();
				var search = "daterange";
				var start_date = picker.startDate.format('YYYY-MM-DD');
				var end_date = picker.endDate.format('YYYY-MM-DD');
				$('#start_edate').val(picker.startDate.format('YYYY-MM-DD'));
				$('#end_edate').val(picker.endDate.format('YYYY-MM-DD'));
				var assignBy = $('#assignBy').find('option:selected').val();
	      var assignTo = $('#assignTo').find('option:selected').val();
	      var client_id = $('#partyID').find('option:selected').val();
	      var type = $('#activitytypefilter').find('option:selected').val();
	      if(assignBy=="null"){
	        assignBy = null;
	      }
	      if(assignTo=="null"){
	        assignTo = null;
	      }
	      if(client_id=="null"){
	        client_id = null;
	      }
	      if(type=="null"){
	        type = null;
	      }
	      $('#f_schedule').removeClass('active');
	      $('#f_overdue').removeClass('active');
	      $('#f_completed').removeClass('active');
	      $('#f_today').removeClass('active');
	      $('#f_schedule').val(0);
	      $('#f_overdue').val(0);
	      $('#f_completed').val(0);
	      $('#f_today').val(0);	      
	      var start = $('#start_edate').val();
	      var end = $('#end_edate').val();
	      $('#activities').DataTable().destroy();
	      initializeDT(start, end,assignBy,assignTo,client_id,type,null);
			});
		@else
			var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
			var start = moment().subtract(3, 'months').format('YYYY-MM-DD');
    	var end = moment().add(3,'months').format('YYYY-MM-DD');
    	var nstart = AD2BS(start);
    	var nend = AD2BS(end);
			$('#start_ndate').val(nstart);
			$('#end_ndate').val(nend);
			$('#start_edate').val(start);
			$('#end_edate').val(end);
			$('#start_ndate').nepaliDatePicker({
				ndpEnglishInput: 'englishDate',
				onChange:function(){
					$('#start_edate').val(BS2AD($('#start_ndate').val()));
					if($('#start_ndate').val()>$('#end_ndate').val()){
						$('#end_ndate').val($('#start_ndate').val());
						$('#end_edate').val(BS2AD($('#start_ndate').val()));
					}
					var assignBy = $('#assignBy').find('option:selected').val();
		      var assignTo = $('#assignTo').find('option:selected').val();
		      var client_id = $('#partyID').find('option:selected').val();
		      var type = $('#activitytypefilter').find('option:selected').val();
		      if(assignBy=="null"){
		        assignBy = null;
		      }
		      if(assignTo=="null"){
		        assignTo = null;
		      }
		      if(client_id=="null"){
		        client_id = null;
		      }
		      if(type=="null"){
		        type = null;
		      }
		      var start = $('#start_edate').val();
		      var end = $('#end_edate').val();
		      $('#activities').DataTable().destroy();
		      initializeDT(start, end,assignBy,assignTo,client_id,type);
				}
			});
			$('#end_ndate').nepaliDatePicker({
				onChange:function(){
					$('#end_edate').val(BS2AD($('#end_ndate').val()));
					if($('#end_ndate').val()<$('#start_ndate').val()){
						$('#start_ndate').val($('#end_ndate').val());
						$('#start_edate').val(BS2AD($('#end_ndate').val()));
					}
					var assignBy = $('#assignBy').find('option:selected').val();
		      var assignTo = $('#assignTo').find('option:selected').val();
		      var client_id = $('#partyID').find('option:selected').val();
		      var type = $('#activitytypefilter').find('option:selected').val();
		      if(assignBy=="null"){
		        assignBy = null;
		      }
		      if(assignTo=="null"){
		        assignTo = null;
		      }
		      if(client_id=="null"){
		        client_id = null;
		      }
		      if(type=="null"){
		        type = null;
		      }
		      var start = $('#start_edate').val();
		      var end = $('#end_edate').val();
		      $('#activities').DataTable().destroy();
		      initializeDT(start, end,assignBy,assignTo,client_id,type);
				}
			});
		@endif

		$('#activities').on('click','.check',function () {
      var id = $(this).val();
      $(this).prop('disabled',true);
      if($(this).prop("checked") == true){
      	var myaudio = new Audio();
				myaudio.src = "{{asset('assets/plugins/sweetalert2/ting.wav')}}";
      	myaudio.play();
        var checked = true;
      }else{
        var checked = false;
      }
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '{{url(domain_route("company.admin.activities.updateMark"))}}',
          type: "POST",
          data: {
            'csrf_token':'{{csrf_token()}}',
            'id':id,
            'checked':checked,
          },
          success: function (data) {
              if(data['result']==true){
                if(data['ticked']==true){
                	$('.check_'+id).prop("checked",true);
                }else{
                	$('.check_'+id).prop("checked",false);
                }
              }else{
              	if(checked==true){
              		$('.check_'+id).prop("checked",false);
              	}else{
              		$('.check_'+id).prop("checked",true);
              	}
              }
          		$('.check_'+id).prop("disabled",false);
          },
          error: function (xhr) {
          		$('.check_'+id).prop("disabled",false);
          		console.log('ajax failed');
          },
          complete: function () {
              //   $('#btnSave').val('Add Activity');
              //  $('#btnSave').removeAttr('disabled');
          }
      });

    });

	$('.select2').select2();
	$(document).on('click','.alert-modal',function(){
	    $('#alertModal').modal('show');
	});
	$(document).on('click','.alert_party_model',function(){
	    $('#alertPartyModal').modal('show');
	});
	$(document).on('click','.no_check',function(){
		if($(this).is(':checked')){
			$(this).prop('checked',false);
		}else{
			$(this).prop('checked',true);
		}
		$('#textComplete').html('complete');
		$('#alertCompleteModal').modal('show');
	});

	$(document).on('click','.no_uncheck',function(){
		if($(this).is(':checked')){
			$(this).prop('checked',false);
		}else{
			$(this).prop('checked',true);
		}
		$('#textComplete').html('incomplete');
		$('#alertCompleteModal').modal('show');
	});

  $('#assignBy').on("change",function () {
    var assignBy = $('#assignBy').find('option:selected').val();
    var assignTo = $('#assignTo').find('option:selected').val();
    var client_id = $('#partyID').find('option:selected').val();
    var type = $('#activitytypefilter').find('option:selected').val();
    if(assignBy=="null"){
      assignBy = null;
    }
    if(assignTo=="null"){
      assignTo = null;
    }
    if(client_id=="null"){
      client_id = null;
    }
    if(type=="null"){
      type = null;
    }
    var f1 = $('#f_schedule').val();
  	var f2 = $('#f_overdue').val();
  	var f3 = $('#f_completed').val();
  	var f4 = $('#f_today').val();
  	if(f1==1 || f2==1 || f3==1 || f4==1){
  		var ftype = [f1,f2,f3,f4];
  		var start = null;
  		var end = null;
  	}else{
  		var ftype = null;
  		var start = $('#start_edate').val();
    	var end = $('#end_edate').val();
  	}
    $('#activities').DataTable().destroy();
    initializeDT(start,end,assignBy,assignTo,client_id,type,ftype);
  });

  $('#assignTo').on("change",function () {
    var assignBy = $('#assignBy').find('option:selected').val();
    var assignTo = $('#assignTo').find('option:selected').val();
    var client_id = $('#partyID').find('option:selected').val();
    var type = $('#activitytypefilter').find('option:selected').val();
    if(assignBy=="null"){
      assignBy = null;
    }
    if(assignTo=="null"){
      assignTo = null;
    }
    if(client_id=="null"){
      client_id = null;
    }
    if(type=="null"){
      type = null;
    }
    var f1 = $('#f_schedule').val();
  	var f2 = $('#f_overdue').val();
  	var f3 = $('#f_completed').val();
  	var f4 = $('#f_today').val();
  	if(f1==1 || f2==1 || f3==1 || f4==1){
  		var ftype = [f1,f2,f3,f4];
  		var start = null;
  		var end = null;
  	}else{
  		var ftype = null;
  		var start = $('#start_edate').val();
    	var end = $('#end_edate').val();
  	}
    $('#activities').DataTable().destroy();
    initializeDT(start,end,assignBy,assignTo,client_id,type,ftype);
  });

  $('#partyID').on("change",function () {
    var assignBy = $('#assignBy').find('option:selected').val();
    var assignTo = $('#assignTo').find('option:selected').val();
    var client_id = $('#partyID').find('option:selected').val();
    var type = $('#activitytypefilter').find('option:selected').val();
    if(assignBy=="null"){
      assignBy = null;
    }
    if(assignTo=="null"){
      assignTo = null;
    }
    if(client_id=="null"){
      client_id = null;
    }
    if(type=="null"){
      type = null;
    }
    var f1 = $('#f_schedule').val();
  	var f2 = $('#f_overdue').val();
  	var f3 = $('#f_completed').val();
  	var f4 = $('#f_today').val();
  	if(f1==1 || f2==1 || f3==1 || f4==1){
  		var ftype = [f1,f2,f3,f4];
  		var start = null;
  		var end = null;
  	}else{
  		var ftype = null;
  		var start = $('#start_edate').val();
    	var end = $('#end_edate').val();
  	}
    $('#activities').DataTable().destroy();
    initializeDT(start,end,assignBy,assignTo,client_id,type,ftype);
  });
  
  $('#activitytypefilter').on("change",function () {
    var assignBy = $('#assignBy').find('option:selected').val();
    var assignTo = $('#assignTo').find('option:selected').val();
    var client_id = $('#partyID').find('option:selected').val();
    var type = $('#activitytypefilter').find('option:selected').val();
    if(assignBy=="null"){
      assignBy = null;
    }
    if(assignTo=="null"){
      assignTo = null;
    }
    if(client_id=="null"){
      client_id = null;
    }
    if(type=="null"){
      type = null;
    }
    var f1 = $('#f_schedule').val();
  	var f2 = $('#f_overdue').val();
  	var f3 = $('#f_completed').val();
  	var f4 = $('#f_today').val();
  	if(f1==1 || f2==1 || f3==1 || f4==1){
  		var ftype = [f1,f2,f3,f4];
  		var start = null;
  		var end = null;
  	}else{
  		var ftype = null;
  		var start = $('#start_edate').val();
    	var end = $('#end_edate').val();
  	}
    $('#activities').DataTable().destroy();
    initializeDT(start,end,assignBy,assignTo,client_id,type,ftype);
  });

  $('#f_schedule,#f_overdue,#f_completed,#f_today').on('click',function(){
  	if($(this).hasClass('f_scheduled')){
  		$('#f_schedule').val(1);
  		$('#f_overdue').val(0);
  		$('#f_completed').val(0);
  		$('#f_today').val(0);
  		$('#f_overdue').removeClass('active');
  		$('#f_completed').removeClass('active');
  		$('#f_today').removeClass('active');
  		$(this).addClass('active');
  	}
  	if($(this).hasClass('f_overdue')){
  		$('#f_schedule').val(0);
  		$('#f_overdue').val(1);
  		$('#f_completed').val(0);
  		$('#f_today').val(0);
  		$('#f_schedule').removeClass('active');
  		$('#f_completed').removeClass('active');
  		$('#f_today').removeClass('active');
  		$(this).addClass('active');
  	}
  	if($(this).hasClass('f_completed')){
  		$('#f_schedule').val(0);
  		$('#f_overdue').val(0);
  		$('#f_completed').val(1);
  		$('#f_today').val(0);
  		$('#f_overdue').removeClass('active');
  		$('#f_schedule').removeClass('active');
  		$('#f_today').removeClass('active');
  		$(this).addClass('active');
  	}
  	if($(this).hasClass('f_today')){
  		$('#f_schedule').val(0);
  		$('#f_overdue').val(0);
  		$('#f_completed').val(0);
  		$('#f_today').val(1);
  		$('#f_overdue').removeClass('active');
  		$('#f_completed').removeClass('active');
  		$('#f_schedule').removeClass('active');
  		$(this).addClass('active');
  	}

  	var assignBy = $('#assignBy').find('option:selected').val();
    var assignTo = $('#assignTo').find('option:selected').val();
    var client_id = $('#partyID').find('option:selected').val();
    var type = $('#activitytypefilter').find('option:selected').val();
    if(assignBy=="null"){
      assignBy = null;
    }
    if(assignTo=="null"){
      assignTo = null;
    }
    if(client_id=="null"){
      client_id = null;
    }
    if(type=="null"){
      type = null;
    }
		var f1 = $('#f_schedule').val();
		var f2 = $('#f_overdue').val();
		var f3 = $('#f_completed').val();
		var f4 = $('#f_today').val();
		var ftype = [f1,f2,f3,f4];
    $('#activities').DataTable().destroy();
    @if(config('settings.ncal')==0)
    cb(null,null,true);      
    @endif
    initializeDT(null, null,assignBy,assignTo,client_id,type,ftype);
  });

  //responsive DateRange Picker
  $('#reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("activitydateposition");        
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("activitydateposition");
    }
    else {   
      $(".daterangepicker").removeClass("activitydateposition");
    }
  });

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