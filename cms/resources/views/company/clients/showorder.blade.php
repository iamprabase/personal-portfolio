@extends('layouts.company')
@section('title', 'Show Party Orders')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">{{ getClient($order->client_id)['company_name'] }} </h3>
          <h3 class="box-title pull-right" style="color:#f16022">Order
            No: {{ getClientSetting()->order_prefix }}{{$order->order_no}} </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div id="detail_div">
            <style type="text/css">
              th {
                text-align: left;
              }

              table.dataTable tbody th,
              table.dataTable tbody td {
                padding-left: 18px;
              }

              .modal-dialog {
                width: 850px;
                margin: 30px auto;
              }

            </style>
            <div class="row">
              <div class="col-md-6"> To: {{ getClient($order->client_id)['company_name'] }} <br>
                Address:
                {{ getClient($order->client_id)['address_1'] }} <br>

                {{ (getClient($order->client_id)['city'])? getCityName(getClient($order->client_id)['city'])->name.',':'' }}
                {{ (getClient($order->client_id)['state'])? getStateName(getClient($order->client_id)['state'])->name.',':'' }}

                {{ (getClient($order->client_id)['country'])? getCountryName(getClient($order->client_id)['country'])->name:'' }}
                <br>
                Mobile: {{ getClient($order->client_id)['mobile'] }}
              </div>
              <div class="col-md-6">
                Salesman Name: @if($order->employee_id == 0)
                {{ Auth::user()->name.' (Admin)' }}
                @else
                {{ getEmployee($order->employee_id)['name'] }}
                @endif
                <br>
                <!-- Order No: {{$order->order_no}} <br> -->
                Date: {{ date('d M Y',strtotime($order->order_date)) }} </div>
            </div>
            <br>
            <table border="1px" id="example" class="display table" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Product Name</th>
                  @if(getClientSetting()->order_with_amt==0)
                  <th>Rate</th>
                  <th>Applied Rate</th>
                  @endif
                  <th>Qty</th>
                  @if(getClientSetting()->order_with_amt==0)
                  <th>Amount</th>
                  @endif
                </tr>
              </thead>

              <tbody>
                @php $i = 0 @endphp
                @foreach($orderdetails as $orderdetail)
                @php $i++
                @endphp
                <tr>
                  <td>{{ $i }}</td>
                  <td>
                    {{ $orderdetail->product_name }}{{ (isset($orderdetail->short_desc))? '( '.$orderdetail->short_desc.' )':'' }}
                  </td>
                  @if(getClientSetting()->order_with_amt==0)
                  <td>{{ $orderdetail->mrp}} {{isset($orderdetail->unit_name)? ' per '.$orderdetail->unit_name:''}}</td>
                  <td>{{ $orderdetail->rate }} {{isset($orderdetail->unit_name)? ' per '.$orderdetail->unit_name:''}}
                  </td>
                  @endif
                  <td>{{ $orderdetail->quantity }}</td>
                  @if(getClientSetting()->order_with_amt==0)
                  <td>{{ $orderdetail->amount }}</td>
                  @endif
                </tr>
                @endforeach
                @if(getClientSetting()->order_with_amt==0)
                <tr>
                  <td colspan="5" class="text-right"><b>Total</b></td>
                  <td><b>{{ $order->tot_amount}}</b></td>
                </tr>
                <tr>
                  <td colspan="5" class="text-right"><b>Discount</b></td>
                  <td>
                    <b>{{ ($order->discount_type=='%')?$order->discount.' %':number_format((float)$order->discount, 2, '.', '') }}</b>
                  </td>
                </tr>
                @php
                $disc_amt=0;
                if($order->discount_type=='%'){
                $disc_amt=($order->tot_amount*$order->discount)/100;
                }else{
                $disc_amt=$order->discount;
                }

                @endphp

                <tr>
                  <td colspan="5" class="text-right"><b>Sub-Total</b></td>
                  <td><b>{{ number_format((float)($order->tot_amount- $disc_amt),2)}}</b></td>
                </tr>
                @foreach(getTaxesOnOrders($order->id) as $tax)
                <tr>
                  <td colspan="5" class="text-right"><b>{{$tax->tax_name}}({{$tax->tax_percent}} %)</b></td>
                  <td>
                    <b>{{ number_format((float)((($order->tot_amount- $disc_amt)*$tax->tax_percent)/100),2) }} </b>
                  </td>
                </tr>
                @endforeach

                <tr>
                  <td colspan="5" class="text-right"><b>Grand Total</b></td>
                  <td><b>{{ $order->grand_total}}</b></td>
                </tr>
                @endif
              </tbody>

            </table>
            <br>
            Order Remark :- {{ strip_tags($order->order_note) }}
          </div>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

</section>

@endsection

@section('scripts')
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script>
  $(function () {
          $("#company").DataTable();

          $('#delete').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget)
              var mid = button.data('mid')
              var modal = $(this)
              modal.find('.modal-body #m_id').val(mid);
          })
      });
</script>

@endsection