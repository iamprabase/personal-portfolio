@extends('layouts.company')
@section('title', 'Show Order')
@section('stylesheets')
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <style type="text/css">
    th {
      text-align: left;
    }
  
    table.dataTable tbody th,
    table.dataTable tbody td {
      padding-left: 18px;
    }

    .content{
      overflow-x: scroll;
    }

    .fa-map-marker{
      padding-left: 5px; 
      color: green;
      font-size: 20px;
      cursor: pointer;
    }
    .ordergpsmodal {
      height: 400px !important;
      overflow: hidden;
      padding: 0;
      position: relative;
    }
    .delete, .edit{
      font-size: 15px;
    }
    .fa-edit, .fa-trash-o{
      padding-left: 5px;
    }

    .btn-warning{
      margin-right: 2px;
      color: #fff;
      background-color: #ec971f;
      border-color: #d58512;
    }
    #ordergpsmap{
      position: unset !important;
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
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-default">
          @if (\Session::has('success'))
            <div class="alert alert-success">
              <p>{{ \Session::get('success') }}</p>
            </div>
            <br/>
          @endif
          @if (\Session::has('error'))
            <div class="alert alert-warning">
              <p>{{ \Session::flash('error') }}</p>
            </div>
            <br/>
          @endif
          <div id="printArea">
            <div class="box-header with-border">
              <a href="{{ URL::previous() }}" class="btn btn-default btn-sm" id="backBtn"> <i class="fa fa-arrow-left"></i> Back</a>
              <div class="page-action pull-right">
                {!!$action!!}
              </div>
            </div>
            <div class="box-header with-border">
              <h3 class="box-title">{{ getClient($order->client_id)['company_name'] }} </h3>
              <div class="page-action pull-right">
                {{-- {!!$action!!} --}}
                <a href="{{ domain_route('company.admin.order.download',[$order->id])}}" class="btn btn-default btn-sm"
                  id="downloadBtn"> <i class="fa fa-download"></i> Download</a>
                <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal" id="mailBtn"> <i
                    class="fa fa-envelope"></i> Mail</a>
                <button class="btn btn-default btn-sm" onclick="printSingleOrder();" id="printBtn"><i class="fa fa-print"></i> Print
                </button>
                {{-- <a href="{{ URL::previous() }}" class="btn btn-default btn-sm" id="backBtn"> <i class="fa fa-arrow-left"></i>
                  Back</a> --}}
              </div>
              <h3 class="box-title pull-right" style="color:#f16022; margin-right: 30px;">Order
                No: {{ $getClientSetting->order_prefix }}{{$order->order_no}} </h3>
            </div>
    
            <div class="box-body">
              <div id="detail_div">
                <div class="row">
                  <div class="col-xs-6">
                    <div class="order-dtl-bg"> To: {{ getClient($order->client_id)['name'] }} <br>
                      Address:
                      {{ getClient($order->client_id)['address_1'] }} 
                      <br />
                      {{ (getClient($order->client_id)['city'])? getCityName(getClient($order->client_id)['city']).',':'' }}
                      {{ (getClient($order->client_id)['state'])? getStateName(getClient($order->client_id)['state'])->name.',':'' }}
                      {{ (getClient($order->client_id)['country'])? getCountryName(getClient($order->client_id)['country'])->name:'' }}
                      <br />
                      Mobile: {{ getClient($order->client_id)['mobile'] }}
                    </div>
                  </div>
                  @if($order->employee_id!=0)
                    <div class="col-xs-6">
                      <div class="order-dtl-bg">
                        Salesman Name: @if($order->employee_id == 0)
                          {{ Auth::user()->managerName($order->company_id)->name.' (Admin)' }}
                        @else
                          {{ getEmployee($order->employee_id)['name'] }}
                        @endif
                        <br />
                        Date: {{ getDeltaDate(date('Y-m-d',strtotime($order->order_date))) }}
                        <br>
                        @if(config('settings.ageing')==1 && Auth::user()->can('ageing-view'))
                        Due Date: {{$order->due_date?getDeltaDate(date('Y-m-d', strtotime($order->due_date))):null}}
                        @endif
                      </div>
                    </div>
                  @else
                    <div class="col-xs-6">
                      <div class="order-dtl-bg">
                        Outlet Name:
                        {{$order->outlet_name}}
                        <br />
                        Date: {{ getDeltaDate(date('Y-m-d',strtotime($order->order_date))) }}
                        <br>
                        @if(config('settings.ageing')==1 && Auth::user()->can('ageing-view'))
                        Due Date: {{$order->due_date?getDeltaDate(date('Y-m-d', strtotime($order->due_date))):null}}
                        @endif
                      </div>
                    </div>
                  @endif
                </div>
                <br/>
                  <table border="1px" id="example" class="display table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                      <th>S.No.</th>
                      <th>Product Details</th>
                      @if($getClientSetting->order_with_amt==0)
                        <th>Rate</th>
                        <th>Qty</th>
                        @if($order->product_level_discount_flag==1)
                          <th>Discount</th>
                        @endif
                        <th>Applied Rate</th>
                        {{-- <th>Qty</th> --}}
    
                        @if($order->product_level_tax_flag==1)
                          <th>Tax Implied</th>
                        @endif
    
                        <th>Amount</th>
                      @else
                        <th>Qty</th>
                      @endif
                    </tr>
                    </thead>
                  
                    <tbody>
                      @php $i = 1 @endphp
                      @foreach($orderdetails as $orderdetail)
                        <tr>
                          <td>{{ $i++ }}</td>
                          <td>
                            {{ $orderdetail->product_name }}{{ (isset($orderdetail->short_desc))? '( '.$orderdetail->short_desc.' )':'' }}
                            <br/>
                            {{ (isset($orderdetail->product_variant_name))? 'Variant:- '. $orderdetail->product_variant_name:'' }}
                            <br/>
                            {{ (isset($orderdetail->variant_colors))? 'Color:- '.$orderdetail->variant_colors:'' }}
                          </td>
                          @if($getClientSetting->order_with_amt==0)
                            <td>{{ config('settings.currency_symbol')}} {{ number_format((float)$orderdetail->mrp,2)}} {{isset($orderdetail->unit_name)? ' per '.$orderdetail->unit_name:''}}</td>
                            <td>{{ number_format($orderdetail->quantity) }}
                              {{ number_format($orderdetail->quantity)==1?$orderdetail->unit_name:($orderdetail->unit_name) }}</td>
                            @if($order->product_level_discount_flag==1)
                            <td>
                              {{($orderdetail->pdiscount>0)?(($orderdetail->pdiscount_type=="Amt" || $orderdetail->pdiscount_type=="oAmt")?$getClientSetting->currency_symbol." ".$orderdetail->pdiscount:$orderdetail->pdiscount."%"):"0.0"}} <span>{{($orderdetail->pdiscount_type=="oAmt")?"Overall Discount":null}}</span>
                            </td>
                            @endif
      
                            <td>{{ config('settings.currency_symbol')}} {{ number_format((float)$orderdetail->rate,2) }}{{isset($orderdetail->unit_name)? ' per '.$orderdetail->unit_name:''}}</td>
                            {{-- <td>{{ number_format($orderdetail->quantity) }} {{ number_format($orderdetail->quantity)==1?$orderdetail->unit_name:($orderdetail->unit_name) }}</td> --}}
                            
                            @if($order->product_level_tax_flag==1)
                              <td>
                                @foreach($orderdetail->taxes()->withTrashed()->get() as $tax)
                                  <p>{{$tax->name.' ('.$tax->percent.'%)'}}</p>
                                @endforeach 
                              </td>
                            @endif
      
                            <td>{{ config('settings.currency_symbol')}} {{ number_format((float)$orderdetail->amount,2) }}</td>
                          @else
                            <td>{{ number_format($orderdetail->quantity) }} {{ ($orderdetail->quantity==1)?$orderdetail->unit_name:($orderdetail->unit_name) }}</td>
                          @endif
                        </tr>
                      @endforeach
                      @if($getClientSetting->order_with_amt==0)
                        <tr>
                          <td colspan="{{$colspan}}" class="text-right"><b>Sub-Total</b></td>
                          <td><b>{{ config('settings.currency_symbol')}} {{ number_format($order->tot_amount,2)}}</b></td>
                        </tr>
                        <tr>
                          <td colspan="{{$colspan}}" class="text-right"><b>Discount</b></td>
                          <td>
                            <b>{{ ($order->discount_type=='%')?$order->discount.' %': config('settings.currency_symbol').' '.number_format($order->discount, 2) }}</b>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="{{$colspan}}" class="text-right"><b>Total</b></td>
                          <td><b>{{ config('settings.currency_symbol')}} {{ number_format($order->total,2)}}</b></td>
                        </tr>
                        @if($order->product_level_tax_flag==0)
                          @if($order->taxes()->withTrashed()->get()->count()>0)
                            @foreach($order->taxes()->withTrashed()->get() as $tax)
                              <tr>
                                <td colspan="{{$colspan}}" class="text-right"><b>{{$tax->name}} ({{$tax->percent}} %)</b></td>
                                <td>
                                  <b>{{ number_format((($order->total*$tax->percent)/100),2) }} </b>
                                </td>
                              </tr>
                            @endforeach
                          @endif
                        @else
                          @foreach($order->applicable_taxes as $tax)
                            {{-- @foreach($orderdetail->taxes as $tax) --}}
                              <tr>
                                <td colspan="{{$colspan}}" class="text-right"><b>{{$tax['name']}} </b></td>
                                <td>
                                  <b>{{ $getClientSetting->currency_symbol }} {{ number_format($tax['amount'], 2) }} </b>
                                </td>
                              </tr>
                            {{-- @endforeach --}}
                          @endforeach
                          <tr>
                            <td colspan="{{$colspan}}" class="text-right"><b>Total Tax </b></td>
                            <td>
                              <b>{{ config('settings.currency_symbol')}} {{ number_format($order->tax, 2) }} </b>
                            </td>
                          </tr>
                        @endif
                        <tr>
                          <td colspan="{{$colspan}}" class="text-right"><b>Grand Total</b></td>
                          <td><b>{{ config('settings.currency_symbol')}} {{ number_format($order->grand_total,2)}}</b></td>
                        </tr>
                      @endif
                    </tbody>
    
                  </table>
                  <p><b>Order Status :- </b>{{ $order->delivery_status }}</p>
                  <p><b>Order Remark :- </b>{!! $order->order_note !!}</p>
                  <br><br>
                @if($getClientSetting->order_approval==1)
                  <div class="row">
                    <div class="col-xs-6">
                      <div class="order-dtl-bg">
                        Dispatch
                        Date: {{ !empty($order->delivery_date)?getDeltaDate(date('Y-m-d',strtotime($order->delivery_date))):'N/A' }}
                        <br>
                        Dispatch Place: {{ !empty($order->delivery_place)?$order->delivery_place:'N/A' }}
                        <br>
                        Dispatch Note: {{ !empty($order->delivery_note)?strip_tags($order->delivery_note):'N/A' }}
                        <br>
                        Transport No.: {{ !empty($order->transport_number)?strip_tags($order->transport_number):'N/A' }}
                        <br>
                        Transport Name: {{ !empty($order->transport_name)?strip_tags($order->transport_name):'N/A' }}
                        <br>
                        Bilty Number: {{ !empty($order->billty_number)?strip_tags($order->billty_number):'N/A' }}
                      </div>
                    </div>
                  </div>
                @endif
                @if($getClientSetting->order_with_authsign==1)
                  <div id="authsign" class="row" style="display: none;">
                    <div class="col-xs-12">
                      <table class="table" width="100%" border="0">
                        <tr style="border-style:hidden; height:100px">
                          <td style="width: 120px;">Approved By:</td>
                          <td>____________________</td>
                          <td style="width: 200px;"></td>
                          <td style="width: 300px;"></td>
                          <td style="width: 75px;">Incharge:</td>
                          <td>____________________</td>
                        </tr>
    
                        <tr style="border-style:hidden;">
                          <td style="width: 140px;">Authorized By:</td>
                          <td>____________________</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                      </table>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
          <div class="box-body order-location">
            <b><p>Order History</p></b> 
            <p>
              <b>
                {{-- {{ date_default_timezone_get() }} --}}
                {{getDeltaDate(date('Y-m-d',strtotime($order->order_date)))}}</b> 

                @php 
                if(isset($order->order_datetime)){
                $convertedDate=null;
                  $date = new DateTime($order->order_datetime, new DateTimeZone('Asia/kathmandu'));

                  $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
                $convertedDate=$date->format('h:i A');
              }
                @endphp

                {!!isset($convertedDate)?"- <b>".$convertedDate."</b>":"<b>".date('h:i A', strtotime($order->order_datetime))."</b>"!!}
                </b> - Order added by <b>@if($order->employee_id == 0) {{$order->outlet_name}}
              }
              }
              @else {{ getEmployee($order->employee_id)['name'] }} @endif</b> .<span>@if(isset($order->latitude) && isset($order->longitude))<i class="fa fa-map-marker" aria-hidden="true" onclick="showModal();"></i>@endif</span>
            </p>
          </div>
        </div>
          
      </div>
    </div>
  </div>
</section>
<div class="modal fade" id="myModal" role="document">
  <div class="modal-dialog" style="width:400px;margin:90px auto;">
    {!! Form::open(array('url' => url(domain_route("company.admin.order.mail", [$order->id])), 'method' => 'post','id'
    =>
    'sendmail','files'=> true)) !!}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-send" style="padding-right:10px;"></i>Send as Mail</h4>
      </div>
      <div class="modal-body">
        {!! Form::label('Send To:') !!}
        {!! Form::text('email',null, ['class'=>'form-control', 'placeholder'=>'Enter Email Address']) !!}
        <div id="errors">
          @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
        </div>
      </div>
      <div class="modal-footer">
        <button class='btn btn-primary' type='submit' value='submit' id="send_mail">
          <i class='fa fa-send'> </i> Send
        </button>
        {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
    {!! Form::close() !!}
  </div>
</div>
<div class="modal fade bd-example-modal-xs" tabindex="-1" role="dialog" id="viewordergps">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><span id="exampleModalLongTitle_name">Order Added by <b>@if($order->employee_id == 0)
              {{$order->outlet_name}}
              @else {{ getEmployee($order->employee_id)['name'] }} @endif</b> {!!isset($convertedDate)?"at
            <b>".$convertedDate."</b>":null!!}</b> on
            <b>{{getDeltaDate(date('Y-m-d',strtotime($order->order_date)))}}</b>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button></h3>
      </div>
      <div class="modal-body ordergpsmodal">
        <span id="ordergpsmap"> </span>
      </div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>

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
            Are you sure you want to delete this order?
          </p>
          <input type="hidden" name="order_id" id="c_id" value="">
          <input type="hidden" name="return_url" id="return_url" value="{{URL::previous()}}">
        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
          <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $("#sendmail").on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      "url": $(this).attr('url'),
      "type": "POST",
      "data": new FormData(this),
      "contentType": false,
      "cache": false,
      "processData": false,
      beforeSend: function () {
        $('#send_mail').html('Please wait...');
        $('#send_mail').attr('disabled');
      },
      success: function (data) {
        $('#sendmail')[0].reset();
        $('#myModal').modal('hide');
      },
      error: function (xhr, status, error) {
        $('#errors').html('');
        for (var error in xhr.responseJSON.errors) {
          $('#errors').html("<p style='padding-top:5px;color:red;'>" + xhr.responseJSON.errors[error] + "</p>");
        }
      },
      complete: function () {
        $('#send_mail').html("<i class='fa fa-send'> </i>Send");
        $('#send_mail').removeAttr('disabled');
      }
    });//ajax
  });

  function printSingleOrder() {
    $('#backBtn').hide();
    $('#downloadBtn').hide();
    $('#printBtn').hide();
    $('#mailBtn').hide();
    $('#authsign').show();
    var print_div = document.getElementById("printArea");
    var print_area = window.open();
    print_area.document.write(print_div.innerHTML);
    print_area.document.close();
    print_area.focus();
    print_area.print();
    print_area.close();
    $('#printBtn').show();
    $('#backBtn').show();
    $('#downloadBtn').show();
    $('#mailBtn').show();
    $('#authsign').hide();
  }

  function showModal(){
    $('#viewordergps').modal('show');
    showOrderLocation();

  }

  $('#delete').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var mid = button.data('mid');
    var url = button.data('url');
    $(".remove-record-model").attr("action", url);
    var modal = $(this)
    modal.find('.modal-body #m_id').val(mid);
  });
  // $(function() {
  //   // $('#viewordergps').on('shown.bs.modal', function ()
  //   // {
  //   //   google.maps.event.trigger(map, "resize");
  //   // });
  // });

  function showOrderLocation() {
    let map;
    let bounds = new google.maps.LatLngBounds();

    let salesmanLatitude = "{{$order->latitude}}";
    let salesmanLongitude = "{{$order->longitude}}";
    let salesmanName = "@if($order->employee_id == 0) {{$order->outlet_name}} @else {{ getEmployee($order->employee_id)['name'] }} @endif";
    let client = @json(getClient($order->client_id));
    let client_name = client.company_name;
    let partyLatitude = client.latitude;
    let partyLongitude = client.longitude;
    let mapOptions="";
    if(salesmanLatitude && salesmanLongitude){
      recentPosition = new google.maps.LatLng(salesmanLatitude, salesmanLongitude);
      mapOptions = {
        zoom: 6, 
        mapTypeId: 'roadmap',
        center: recentPosition,
      };
    }else{
      recentPosition = new google.maps.LatLng(partyLatitude, partyLongitude);
      mapOptions = {
        zoom: 6, 
        mapTypeId: 'roadmap',
        center: recentPosition,
      };
    } 
    map = new google.maps.Map(document.getElementById("ordergpsmap"), mapOptions);
    map.setTilt(45);
    let infoWindow = new google.maps.InfoWindow(), marker, j;
    if(salesmanLatitude && salesmanLongitude){
      marker = new google.maps.Marker({
        position: recentPosition,
        map: map,
        icon: {
          url: "/assets/dist/img/markers/employee.png",
          scaledSize: new google.maps.Size(25, 40),      
        },
        title: salesmanName,
      });
    }

    if(partyLatitude && partyLongitude){
      var goldStar = {
          path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
          fillColor: 'yellow',
          fillOpacity: 0.7,
          scale: (0.15, 0.15),
          strokeColor: 'red',
          strokeWeight: 0.9
      };
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(partyLatitude, partyLongitude),
        map: map,
        // icon: goldStar,
        icon: {
          url: "/assets/dist/img/markers/party-star.png",
          scaledSize: new google.maps.Size(30, 30),      
        },
        title: client_name,
      });
    }
  }
  </script>
@endsection
