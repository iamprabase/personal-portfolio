<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<head>
    <title>Visit Report</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes" name="viewport">
    <style>

        * {
            font-family: Noto Sans, "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: 400;
            font-size: 14px;
            line-height: 1.42857143;
            color: #333;
            background-color: #fff;
            margin: 0;
        }

        .order-dtl-bg {
            background: #ecf0f5;
            min-height: 80px;
            margin-bottom: 10px;
            padding: 10px;
        }

        td {
            width: auto;
            display: table-cell;
            border: 1px solid #f4f4f4;
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            padding: 10px;
        }

        th {
            border: 1px solid #f4f4f4;
            text-align: left;
            display: table-cell;
            width: auto;
            background: #d9dcdc !important;
            color: #1a1a1a !important;
            padding: 10px;
        }

        .detail-box {
            margin-bottom: 10px;
        }

        table {
            border: 1px solid #fff;
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .col-xs-4 {
            width: auto;
        }

        .imagePreview {
            width: 50%;
            height: 200px;
            display: inline-block;
            text-align: center;
            margin: 0px auto;
            padding-bottom: 5px !important;
        }

        .imageExistsPreview img {
            height: 200px;
        }

        .imgDiv {
            display: inline;
        }

        h2 {
            margin: 0px;
        }
    </style>
</head>
<body class="hold-transition skin-green-light sidebar-mini">

<section class="content" style="width: 1000px;">
    <strong><h2 class="text-center">Odometer Report</h2></strong>
    <div class="box-body">
        <div id="detail_div">
            <div class="row">
                <div class="col-xs-4">
                    <div class="order-dtl-bg">
                      <strong>Name</strong>   : {{$reports->first()->employees->name}} <br>
                        <strong>Date</strong>
                        : {{  getDeltaDate(date('Y-m-d', strtotime($start_date))) .' - '.  getDeltaDate(date('Y-m-d', strtotime($end_date)))}} <br>
                        <strong>Total Distance Traveled</strong> : {{$total_distance_in_km}} KM ({{$total_distance_in_mile}} mile) <br>
                        <strong>Reimbursement Amount</strong> : {{config('settings.currency_symbol')}} {{round($reports->sum('amount'),2)}}
                    </div>
                </div>
            </div>
            <br/>
            <table border="1px" id="example" class="display table" cellspacing="0">
                <thead>
                <tr>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Start Meter</th>
                    <th>End Meter</th>
                    <th>Start Location</th>
                    <th>End Location</th>
                    <th>Distance</th>
                    <th>Reimbursement</th>
                    <th>Notes</th>
                </tr>
                </thead>

                <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{{$report->start_time}}</td>
                        <td>{{$report->end_time}}</td>
                        <td>{{$report->start_reading}} @if($report->distance_unit == 1) Km @else Mile @endif</td>
                        <td>{{$report->end_reading}} @if($report->distance_unit == 1) Km @else Mile @endif </td>
                        <td>{{$report->start_location}}</td>
                        <td>{{$report->end_location}}</td>
                        <td> {{$report->distance}} @if($report->distance_unit == 1) Km @else Mile @endif</td>
                        <td>{{config('settings.currency_symbol')}} {{round($report->amount,2)}} </td>
                        <td>{{$report->notes}}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>


    </div>
</section>

</body>
</html>


