<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invoice</title>
    <!-- Bootstrap core CSS -->
    <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
    />

    <style>
        body {
            font-family: Georgia, serif;
        }

        h1 {
            margin-top: 0;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 5px;
        }

        h1 .small,
        h3 .small {
            display: block;
        }

        p {
            margin: 0;
        }

        footer {
            font-size: 14px;
            text-align: center;
            display: block;
        }

        .text-right {
            text-align: right;
        }

        address {
            margin-bottom: 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table > tbody > tr > td,
        .table > tbody > tr > th,
        .table > tfoot > tr > td,
        .table > tfoot > tr > th,
        .table > thead > tr > td,
        .table > thead > tr > th {
            padding: 4px;
        }
    </style>
</head>
<body class="login-page" style="background: white">
<div>
    <div class="row">
        <div class="col-xs-7">
            @if(!is_null(config('settings.logo_path')))
                <img src="{{asset('cms/'.config('settings.logo_path'))}}" alt="{{config('settings.title')}}" class="logo" height="50px" />
            @else
                <p>
                    {{config('settings.title')}}
                </p>
            @endif
            <div style="margin-bottom: 0">&nbsp;</div>
            <div class="row">
                <div class="col-xs-6">
                    <h4 class="text-uppercase">To</h4>
                    <address>
                        <strong>{{getClient($order->client_id)['company_name']}} </strong><br/> @if(!empty(getClient($order->client_id)['name'])){{ getClient($order->client_id)['name'] }}
                        <br/>@endif
                        @if(!empty(getClient($order->client_id)['address_1'])) {{ getClient($order->client_id)['address_1'] }}
                        <br/> @endif
                        {!! (getClient($order->client_id)['city'])? getCityName(getClient($order->client_id)['city']).',':''  !!}
                        {{ (getClient($order->client_id)['state'])? getStateName(getClient($order->client_id)['state'])->name.',':'' }}
                        {{ (getClient($order->client_id)['country'])? getCountryName(getClient($order->client_id)['country'])->name:'' }}
                        <br>
                        @if(isset(getClient($order->client_id)['mobile']))
                            {{ getClient($order->client_id)['mobile'] }} <br>
                        @endif

                        @if($order->employee_id!=0)
                            Salesman: {{$order->employee_id == 0 ? Auth::user()->managerName($order->company_id)->name.' (Admin)' : getEmployee($order->employee_id)['name']  }}
                            <br>
                        @else
                            Outlet Name:
                            {{$order->outlet_name}} <br>
                        @endif

                        @if($partyTypeLevel)
                            Ordered To:
                            @if($order->order_to)
                                {{getClient($order->order_to)['company_name'] }}
                                <br/>
                                Address:
                                {!! isset(getClient($order->order_to)['address_1']) ? getClient($order->order_to)['address_1'] .'<br>' : '' !!}
                                {{ (getClient($order->order_to)['city'])? getCityName(getClient($order->order_to)['city']).',':'' }}
                                {{ (getClient($order->order_to)['state'])? getStateName(getClient($order->order_to)['state'])->name.',':'' }}
                                {{ (getClient($order->order_to)['country'])? getCountryName(getClient($order->order_to)['country'])->name:'' }}
                                <br/>
                                @if(isset( getClient($order->order_to)['mobile']))
                                    Mobile: {{ getClient($order->order_to)['mobile'] }} <br>
                                @endif
                            @else
                                {{Auth::user()->companyName($order->company_id)->domain}}
                            @endif
                            <br>
                        @endif
                    </address>
                </div>
            </div>

        </div>


        <div class="col-xs-4 text-right">
            <h1>
                Invoice
                <span class="small">{{config('settings.title')}}</span>
            </h1>
            <address>
                {{getCityName(config('settings.city')) ? getCityName(config('settings.city')) : '' }},
                {{getStateName(config('settings.state')) ? getStateName(config('settings.state'))->name : ''}}
                {{getCountryName(config('settings.country'))  ? getCountryName(config('settings.country'))->name : '' }}
                <br/>
                {{config('settings.email')}}<br/>
                {{config('settings.phone')}}
            </address>
            <br/>
        </div>
    </div>

    <div class="row" style="margin-bottom: 0px">
        <div class="col-xs-6"></div>

        <div class="col-xs-5">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <th>Invoice Number:</th>
                    <td class="text-right">{{ getClientSetting()->order_prefix }}{{$order->order_no}}</td>
                </tr>
                <tr>
                    <th>Invoice Date:</th>
                    <td class="text-right">{{ getDeltaDate(date('Y-m-d',strtotime($order->order_date))) }}</td>
                </tr>

                @if(config('settings.ageing')==1 && Auth::user()->can('ageing-view'))
                    <tr>
                        <th>Due Date:</th>
                        <td class="text-right">
                            {{$order->due_date?getDeltaDate(date('Y-m-d', strtotime($order->due_date))):null}}
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div style="margin-bottom: 0px">&nbsp;</div>
    <br>
    <br>

    <table class="table">
        <thead style="background: #f5f5f5">
        <tr>
            <th>Product Details</th>
            <th></th>
            @if($getClientSetting->order_with_amt==0)
                <th class="text-right">Rate</th>
                <th class="text-right">Qty</th>
                @if($order->product_level_discount_flag==1)
                    <th class="text-right">Discount</th>
                @endif
                <th class="text-right">Applied Rate</th>

                @if($order->product_level_tax_flag==1)
                    <th class="text-right">Tax Implied</th>
                @endif

                <th class="text-right">Amount</th>
            @else
                <th class="text-right">Qty</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($orderdetails as $orderdetail)
            <tr>
                <td>
                    <div><strong>{{ $orderdetail->product_name }}</strong></div>
                    <p> {!! (isset($orderdetail->short_desc))? '( '.$orderdetail->short_desc.' )</br>':'' !!}
                        {{ (isset($orderdetail->product_variant_name))? 'Variant:- '. $orderdetail->product_variant_name:'' }}
                        <br/>
                        {{ (isset($orderdetail->variant_colors))? 'Color:- '.$orderdetail->variant_colors:'' }}
                    </p>
                </td>
                <td></td>
                @if($getClientSetting->order_with_amt==0)
                    <td class="text-right">{{ config('settings.currency_symbol')}} {{ number_format((float)$orderdetail->mrp,2)}} {{isset($orderdetail->unit_name)? ' per '.$orderdetail->unit_name:''}}</td>
                    <td class="text-right">{{ number_format($orderdetail->quantity) }}
                        {{ number_format($orderdetail->quantity)==1?$orderdetail->unit_name:($orderdetail->unit_name) }}</td>
                    @if($order->product_level_discount_flag==1)
                        <td class="text-right">
                            {{($orderdetail->pdiscount>0)?(($orderdetail->pdiscount_type=="Amt" || $orderdetail->pdiscount_type=="oAmt")?$getClientSetting->currency_symbol." ".$orderdetail->pdiscount:$orderdetail->pdiscount."%"):"0.0"}}
                            <span>{{($orderdetail->pdiscount_type=="oAmt")?"Overall Discount":null}}</span>
                        </td>
                    @endif
                    <td class="text-right">{{ config('settings.currency_symbol')}} {{ number_format((float)$orderdetail->rate,2) }}{{isset($orderdetail->unit_name)? ' per '.$orderdetail->unit_name:''}}</td>

                    @if($order->product_level_tax_flag==1)
                        <td class="text-right">
                            @foreach($orderdetail->taxes()->withTrashed()->get() as $tax)
                                <p>{{$tax->name.' ('.$tax->percent.'%)'}}</p>
                            @endforeach
                        </td>
                    @endif

                    <td class="text-right">{{ config('settings.currency_symbol')}} {{ number_format((float)$orderdetail->amount,2) }}</td>
                @else
                    <td class="text-right">{{ number_format($orderdetail->quantity) }} {{ ($orderdetail->quantity==1)?$orderdetail->unit_name:($orderdetail->unit_name) }}</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-bottom: 0px">&nbsp;</div>

    <div class="row">
        @if(count($free_schemes))
            <div class="col-xs-6">
                <table class="table">
                    <thead style="background: #f5f5f5">
                    <tr>
                        <th>Offered Product Details</th>
                        <th></th>
                        <th class="text-right">Qty</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($free_schemes as $schemes)
                        <tr>
                            <td>
                                <div><strong>{{ $schemes['product_name']}} </strong></div>
                                <p> {{ $schemes['product_variant'] }}</p>
                            </td>
                            <td></td>
                            <td class="text-right">{{$schemes['freeItem']}} {{$schemes['unit_name']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="col-xs-5 pull-right">
            <table style="width: 100%">
                <tbody>
                @if($getClientSetting->order_with_amt==0)
                    @if($scheme_off_value > 0)
                        <tr>
                            <th>Scheme Amount Off:</th>
                            <td class="text-right">{{ config('settings.currency_symbol')}} {{ number_format($scheme_off_value,2)}}</td>
                        </tr>

                    @endif
                    <tr>
                        <th>Sub-Total:</th>
                        <td class="text-right">{{ config('settings.currency_symbol')}} {{ number_format($order->tot_amount,2)}}</td>
                    </tr>
                    <tr>
                        <th>Discount:</th>
                        <td class="text-right">{{ ($order->discount_type=='%')?$order->discount.' %': config('settings.currency_symbol').' '.number_format($order->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td class="text-right">{{ config('settings.currency_symbol')}} {{ number_format($order->total,2)}}</td>
                    </tr>
                    @if($order->product_level_tax_flag==0)
                        @if($order->taxes()->withTrashed()->get()->count()>0)
                            @foreach($order->taxes()->withTrashed()->get() as $tax)
                                <tr>
                                    <th>{{$tax->name}} ({{$tax->percent}} %)</th>
                                    <td class="text-right">{{ number_format((($order->total*$tax->percent)/100),2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @else
                        @foreach($order->applicable_taxes as $tax)
                            <tr>
                                <th>{{$tax['name']}}</th>
                                <td class="text-right">
                                    {{ $getClientSetting->currency_symbol }} {{ number_format($tax['amount'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>Total Tax:</th>
                            <td class="text-right">{{ config('settings.currency_symbol')}} {{ number_format($order->tax, 2) }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <th>&nbsp;</th>
                        <td>&nbsp;</td>
                    </tr>

                    <tr class="well" style="padding: 5px">
                        <th style="padding: 5px">
                            <div>Grand Total:</div>
                        </th>
                        <td style="padding: 5px" class="text-right">
                            <strong> {{ config('settings.currency_symbol')}} {{ number_format($order->grand_total,2)}}</strong>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-bottom: 0px">&nbsp;</div>

    <div class="row">
        <div class="col-xs-8 invbody-terms">
            <p><strong>Order Status:</strong> {{ $order->delivery_status }}</p>

            @if(isset($order->order_note)) <b>Order Remark :- </b>{!! $order->order_note !!} <br> @endif
            @if($getClientSetting->order_approval==1)

                <p><strong>Dispatch Date:</strong>
                    {{ !empty($order->delivery_date)?getDeltaDate(date('Y-m-d',strtotime($order->delivery_date))):'N/A' }}
                </p>
                <p><strong> Dispatch Place: </strong>
                    {{ !empty($order->delivery_place)?$order->delivery_place:'N/A' }}
                </p>

                <p><strong>Dispatch Note: </strong>
                    {{ !empty($order->delivery_note)?strip_tags($order->delivery_note):'N/A' }}
                </p>

                <p>
                    <strong>Transport No. :
                    </strong>{{ !empty($order->transport_number)?strip_tags($order->transport_number):'N/A' }}
                </p>

                <p><strong>Transport Name:</strong>
                    {{ !empty($order->transport_name)?strip_tags($order->transport_name):'N/A' }}
                </p>

                <p><strong>Bilty Number:</strong>
                    {{ !empty($order->billty_number)?strip_tags($order->billty_number):'N/A' }}
                </p>
            @endif
            <br/>
            <br/>

        </div>
    </div>
    <hr/>
    <footer>
        <p class="text-muted">
            Invoice was created on a computer and is valid without the signature
            and seal.
        </p>
    </footer>
</div>
</body>
</html>

