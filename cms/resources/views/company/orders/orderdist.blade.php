@extends('layouts.company')
@section('title', 'Orders')
@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <style>
    .daterangepicker .calendar-table th, .daterangepicker .calendar-table td {
      min-width: 25px !important;
      width: 25px !important;
    }

    .table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th {
      padding: 3px !important;
    }

    .daterangepicker.ltr .drp-calendar.right {
      margin-left: 0;
      border-left: 1px solid #ccc !important;
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
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Order List</h3>
            <a href="{{ domain_route('company.admin.order.create') }}" class="btn btn-primary pull-right"
               style="margin-left: 5px;">
              <i class="fa fa-plus"></i> Create New
            </a>
            <span id="orderexports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div id="reportrange" name="reportrange"
                 style="background: #fff; cursor: pointer; padding: 5px 8px; border: 1px solid #ccc; width: 28%;position: absolute;margin-left: 50%;z-index: 999;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span> <i class="fa fa-caret-down"></i>
            </div>
            <div id="partyfilter"></div>
            <div id="salesmfilter"></div>
            <table id="order" class="table table-bordered table-striped">
              <thead>

              <tr>
                <th>#</th>
                <th>Party Name</th>
                <th>Created By</th>
                <th>Order Date</th>
                <th>Order No.</th>
                @if(getClientSetting()->order_with_amt==0)
                  <th>Discount</th>
                  <th>Grand Total</th>
                @endif
                <th>Order Status</th>

                <th>Action</th>
              </tr>
              </thead>

              <tbody>
              @php($i = 0)
              @foreach($orders as $order)
                @php($i++)
                <tr>
                  <td>{{ $i }}</td>
                  <td>{{ ucfirst(getClient($order->client_id)['company_name']) }}</td>
                  <td>
                    @if($order->employee_id == 0)
                      {{ Auth::user()->name.' (Admin)' }}
                    @else
                      {{ ucfirst(strtolower(getEmployee($order->employee_id)['name'])) }}
                    @endif
                  </td>
                  <td>{{date("d M Y", strtotime($order->order_date))}}</td>
                  <td>{{ getClientSetting()->order_prefix }}{{ $order->order_no}}</td>
                  {{-- <td>{{ $order->tot_amount}}</td>
                  <td>
                    @foreach(getTaxesOnOrders($order->id) as $tax)
                      {{$tax->tax_name}}: {{$tax->tax_percent}} % <br>
                    @endforeach
                  </td> --}}
                  @if(getClientSetting()->order_with_amt==0)
                    <td>{{ ($order->discount_type=='%')?$order->discount.' %':number_format((float)$order->discount, 2, '.', '')}}</td>
                    <td>{{ number_format((float)$order->grand_total,2)}}</td>
                  @endif
                  @if(getClientSetting()->order_approval==0)
                    <td>
                      <a href="#" class="edit-modal" data-id="{{$order->id}}" data-status="{{$order->delivery_status}}">
                        @if($order->delivery_status == 'New')
                          <span class="label label-warning">{{ $order->delivery_status }}</span>

                        @elseif($order->delivery_status == 'In Process')
                          <span class="label label-default">{{ $order->delivery_status}}</span>
                        
                        @elseif($order->delivery_status == 'Complete')
                          <span class="label label-success">{{ $order->delivery_status}}</span>
                        @elseif($order->delivery_status == 'Cancelled')
                          <span class="label label-danger">{{ $order->delivery_status}}</span>

                        @else
                          <span class="label label-danger">N/A</span>
                        
                        @endif
                      </a>
                    </td>
                  @else
                    <td>
                      <a href="#" class="edit-modal" data-id="{{$order->id}}"
                         data-status="{{$order->delivery_status}}" data-orderdate="{{$order->delivery_date}}"
                         data-note="{{$order->delivery_note}}" data-place="{{$order->delivery_place}}">
                        @if($order->delivery_status == 'Pending')
                          <span class="label label-warning">{{ $order->delivery_status }}</span>

                        @elseif($order->delivery_status == 'Approved')
                          <span class="label label-default">{{ $order->delivery_status}}</span>
                        
                        @elseif($order->delivery_status == 'Declined')
                          <span class="label label-danger">{{ $order->delivery_status}}</span>
                        @endif
                      </a>
                    </td>
                  @endif
                  <td>
                    <a href="{{ domain_route('company.admin.order.show',[$order->id]) }}" class="btn btn-success btn-sm"
                       style="padding: 3px 6px;"><i class="fa fa-eye"></i></a>
                    @if($order->delivery_status == 'New')
                      <a class="btn btn-danger btn-sm delete" data-mid="{{ $order->id }}"
                         data-url="{{ domain_route('company.admin.order.destroy', [$order->id]) }}" data-toggle="modal"
                         data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>
                    @endif

                  </td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
              @if(getClientSetting()->order_with_amt==0)
                <tr>
                  <th colspan="6" style="text-align:right">Total:</th>
                  <th colspan="3" style="text-align:left"></th>
                </tr>
              @endif
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


  <!-- Modal -->
  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
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
            <input type="hidden" name="order_id" id="c_id" value="">

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
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
          <form class="form-horizontal" role="form" id="changeDeliveryStatus" method="POST"
                action="{{URL::to('admin/order/changeDeliveryStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="order_id" id="order_id" value="">
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Status</label>
              <div class="col-sm-10">
                <select class="form-control" id="delivery_status" name="delivery_status">
                  @if(getClientSetting()->order_approval==0)
                    <option value="New">New</option>
                    <option value="In Process">In Process</option>
                    <option value="Complete">Complete</option>
                    <option value="Cancelled">Cancelled</option>
                  @else
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Declined">Declined</option>
                  @endif
                </select>
              </div>
            </div>
            @if(getClientSetting()->order_approval==1)
              <div class="form-group">
                <label class="control-label col-sm-2" for="name">Dispatch Date</label>
                <div class="col-sm-10">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('delivery_date', null, ['class' => 'form-control pull-right', 'id' => 'delivery_datenew', 'autocomplete'=>'off', 'placeholder' => 'Start Date','required']) !!}
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="name">Dispatch Place</label>
                <div class="col-sm-10">
                  {!! Form::text('delivery_place', null, ['class' => 'form-control', 'id=delivery_place', 'placeholder' => 'Delivery Place']) !!}
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="name">Dispatch Note</label>
                <div class="col-sm-10">
                  {!! Form::textarea('delivery_note', null, ['class' => 'form-control', 'rows="5"', 'id=delivery_note', 'placeholder' => 'Delivery Notes']) !!}
                </div>
              </div>
            @endif
            <div class="modal-footer">
              <button id="btn_status_change" type="submit" class="btn actionBtn">
                <span id="footer_action_button" class='glyphicon'> </span> Change
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button>
            </div>


          </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script>
      $(function () {

          $('#delete').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget);
              var mid = button.data('mid');
              var url = button.data('url');
              // $(".remove-record-model").attr("action",url);
              $(".remove-record-model").attr("action", url);
              var modal = $(this)
              modal.find('.modal-body #m_id').val(mid);
          });


          var table = $('#order').DataTable({
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Order List'
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Order List'
                  },
                  {
                      extend: 'print',
                      title: 'Order List'
                  },
              ],
              footerCallback: function (row, data, start, end, display) {
                  var api = this.api(), data;

                  // Remove the formatting to get integer data for summation
                  var intVal = function (i) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '') * 1 :
                          typeof i === 'number' ?
                              i : 0;
                  };

                  // Total over all pages
                  total = api
                      .column(6)
                      .data()
                      .reduce(function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0);

                  // Total over this page
                  pageTotal = api
                      .column(6, {page: 'current'})
                      .data()
                      .reduce(function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0);

                  // Update footer
                  $(api.column(6).footer()).html(
                      'Rs.' + (pageTotal).toLocaleString('hi')
                  );
              }
          });

          table.buttons().container()
              .appendTo('#orderexports');

          $('#reportrange').bind('DOMSubtreeModified', function (event) {
              table.draw();
          });

          var start = moment().subtract(29, 'days');
          var end = moment();

          function cb(start, end) {
              $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
              $('#startdate').val(start.format('MMMM D, YYYY'));
              $('#enddate').val(end.format('MMMM D, YYYY'));
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

          cb(start, end);

          $.fn.dataTable.ext.search.push(
              function (settings, data, dataIndex) {
                  var start2 = $('#reportrange').data('daterangepicker').startDate;
                  var end2 = $('#reportrange').data('daterangepicker').endDate;
                  var start_date = Date.parse(start2.format('MMMM D, YYYY'));
                  var end_date = Date.parse(end2.format('MMMM D, YYYY'));
                  var create_date = Date.parse(data[3]); // use data for the age column
                  if (create_date >= start_date && create_date <= end_date) {
                      return true;
                  }
                  return false;
              }
          );

      });

      $(document).ready(function () {
          var table = $('#order').DataTable();

          // $("#order tfoot th").each( function ( i ) {
          var select = $('<select style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 15%;position: absolute;margin-left: 15%;z-index: 999;"><option value="">Search By Party</option></select>')
              .appendTo($('#partyfilter').empty())
              .on('change', function () {
                  table.column(1)
                      .search($(this).val())
                      .draw();
              });

          table.column(1).data().unique().sort().each(function (d, j) {
              select.append('<option value="' + d + '">' + d + '</option>')
          });
          // } );

          var select = $('<select style="background: #fff; cursor: pointer; padding: 5px 7px; border: 1px solid #ccc; width: 15%;position: absolute;margin-left: 33%;z-index: 999;"><option value=""> Search By Salesman</option></select>')
              .appendTo($('#salesmfilter').empty())
              .on('change', function () {
                  table.column(2)
                      .search($(this).val())
                      .draw();
              });

          table.column(2).data().unique().sort().each(function (d, j) {
              select.append('<option value="' + d + '">' + d + '</option>')
          });
      });

      $(document).on('click', '.edit-modal', function () {
          // $('#footer_action_button').text(" Change");
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Change Delivery Status');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#order_id').val($(this).data('id'));
          // $('#remark').val($(this).data('remark'));
          $('#delivery_status').val($(this).data('status'));
          $('#delivery_datenew').val($(this).data('orderdate'));
          $('#delivery_place').val($(this).data('place'));
          $('#delivery_note').val($(this).data('note'));
          $('#myModal').modal('show');
      });

      $('#changeDeliveryStatus').on('submit',function(){
          $('#btn_status_change').attr('disabled',true);
      });

      $(function () {
          $("#delivery_datenew").datepicker({
              format: "yyyy-mm-dd",
              startDate: new Date(),
              autoclose: true,
          });    // Here the current date is set
      });
  </script>

@endsection