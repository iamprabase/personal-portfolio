<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<head>
    <title>Visit Report</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes" name="viewport">
    <style>

      *{
        font-family: Noto Sans,"Helvetica Neue", Helvetica, Arial, sans-serif;
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
        width: 100%;
        padding-left: 10px;
      }

      .detail-box {
        margin-bottom: 10px;
      }
      
      table{
        border: 1px solid #fff;
        width: 100%;
        table-layout: fixed!important;
      }

      td{
        display: table-cell;
        border: 1px solid #f4f4f4;
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
        padding: 10px;
        word-break: break-all;
      }

      th {
        border: 1px solid #f4f4f4;
        text-align: left;
        display: table-cell;
        width: auto;
        background: #d9dcdc!important;
        color: #1a1a1a!important;
      }

      .text-center{
        text-align: center;
      }

      .col-xs-4{
        width: max-content!important;
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
        height: 70px!important;
        width: 70px!important;
      }

      .imgDiv{
        display: inline !important;
      }

      h2{
        margin: 0px;
      }

      a{
        text-decoration: none;
        color: #333;
      }

      .mw-160 {
        min-width: 160px!important;
      }

      .mw-70 {
        min-width: 70px!important;
      }
  </style>
</head>
<body class="hold-transition skin-green-light sidebar-mini">
  
  <section class="content" style="width: 1000px;">
    <strong><h2 class="text-center">Visit Report</h2></strong>
    @include('company..client-visits.partial_show.visit_detail_partial', [
      'action' => $action,
      'checkin' => $checkin, 
      'checkout' => $checkout, 
      'date' => $date, 
      'employee_name' => $employee_name, 
      'empVisits' => $empVisits, 
      'total_duration' => $total_duration, 
      'total_distance' => $total_distance,
      'location' => "hidden"
    ])
  </section>

</body>
</html>


