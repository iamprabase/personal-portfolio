
<!DOCTYPE html>
<head>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
    </title>
    {{-- <link href="https://fonts.googleapis.com/css?family=Noto+Sans&subset=devanagari" rel="stylesheet"> --}}
    <style>
      * {
        font-family: Noto Sans, sans-serif;
      }
  
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
  </head>
</head>
<body  style="font-family: sans-serif">
    <section class="content">
      <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title"  style="color:#089c9c">Tour Plan Details </h3>
          </div>
          <div class="box-body">
            <h4 class="box-title" ><b>Salesman Name:- {{ getEmployee($tourplan->employee_id)['name']}}</b></h4>
            <div class="table-responsive">
              <table style="width:100%;">
                  <colgroup>
                    <col class="col-xs-2">
                    <col class="col-xs-7">
                  </colgroup>
                  <tbody>
                    <tr style="width:100%;">
                        <th style="border:1px solid #f4f4f4;padding:8px;width:;color:white; background:#089c9c!important;" scope="row"> Visit Location</th>
                        <td style="border:1px solid #f4f4f4;padding:8px;color:black; background:#f9f9f9!important;">{{ $tourplan->visit_place}}</td>
                      </tr>
                      <tr>
                        <th style="border:1px solid #f4f4f4;padding:8px;color:white; background:#089c9c!important;"  scope="row"> Visit Purpose</th>
                        <td style="border:1px solid #f4f4f4;padding:8px;color:black; background:#f9f9f9!important;">{!! $tourplan->visit_purpose !!}</td>
                      </tr>
                      <tr>
                        <th style="border:1px solid #f4f4f4;padding:8px;color:white; background:#089c9c!important;"  scope="row"> Date (From - To)</th>
                        @if(getClientSetting()->ncal==0)
                          <td>{{ date('d M Y', strtotime($tourplan->start_date)) }}

                            - {{ date('d M Y', strtotime($tourplan->end_date)) }}</td>
                        @else
                          <td>{{ getDeltaDate(date('Y-m-d', strtotime($tourplan->start_date))) }}

                            - {{ getDeltaDate(date('Y-m-d', strtotime($tourplan->end_date))) }}</td>
                        @endif
                          </tr>
                          <tr>
                            <th style="border:1px solid #f4f4f4;padding:8px;color:white; background:#089c9c!important;"  scope="row"> Number of Days</th>
                            <td style="border:1px solid #f4f4f4;padding:8px;color:black; background:#f9f9f9!important;">{{ getDays($tourplan->start_date,$tourplan->end_date) }}</td>
                  </tr>
                  <tr>
                    <th style="border:1px solid #f4f4f4;padding:8px;color:white; background:#089c9c!important;"  scope="row"> Date Created</th>
                    <td style="border:1px solid #f4f4f4;padding:8px;color:black; background:#f9f9f9!important;">{{ getDeltaDate(date('Y-m-d', strtotime($tourplan->created_at)))}}
                      </tr>
                      <tr>
                        <th style="border:1px solid #f4f4f4;padding:8px;color:white; background:#089c9c!important;"  scope="row"> Status</th>
                        <td style="border:1px solid #f4f4f4;padding:8px;color:black; background:#f9f9f9!important;"><span class="">
                          @if($tourplan->status =='Approved')
                          <span class="label label-success">{{ $tourplan->status}}</span>
                          @elseif($tourplan->status =='Pending')
                          <span class="label label-warning">{{ $tourplan->status}}</span>
                      @else
                      <span class="label label-danger">{{ $tourplan->status}}</span>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th style="border:1px solid #f4f4f4;padding:8px;color:white; background:#089c9c!important;"  scope="row"> Remarks:</th>
                    <td style="border:1px solid #f4f4f4;padding:8px;color:black; background:#f9f9f9!important;"><span class="">
                      @if($tourplan->remark)
                      <span>{{ $tourplan->remark}}</span>
                      @else
                      <span></span>
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          @if(config('settings.party')==1)
          <div class="box-header">
            <h3 class="box-title">Tour Report</h3>
          </div>
          <div class="box-body">
            @if(getClientSetting()->ncal==0)
            <p class="box-title"><strong>{{ date('d M Y', strtotime($tourplan->start_date)) }} TO {{ date('d M Y', strtotime($tourplan->end_date)) }}</strong></p>
            @else
            <p class="box-title"><strong>{{ getDeltaDate(date('Y-m-d', strtotime($tourplan->start_date))) }} TO {{ getDeltaDate(date('Y-m-d', strtotime($tourplan->end_date))) }}</strong></p>
            @endif
            <table style="width:100%; border:1px solid #f4f4f4;">
                <thead>
                  <tr style="width:100%;color:white; background:#089c9c!important;">
                      <th>Orders</th>
                      <th>Collection</th>
                      <th>Expense</th>
                    </tr>
              </thead>
              <tbody>
                <tr style="width:inherit;color:black; background:#f9f9f9!important;">
                    <td>{{config('settings.currency_symbol')}} {{$sales_total}} </td>
                    <td>{{config('settings.currency_symbol')}} {{$collection_total}}</td>
                    <td>{{config('settings.currency_symbol')}} {{$expense_total}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          @endif
        </div>
      </div>
    </section>
  </body>
</html>