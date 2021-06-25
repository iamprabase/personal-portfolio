<!DOCTYPE html>
<html lang="en">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <head>
    <title>{{$pageTitle}}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes" name="viewport">
    <style>
      *{
        margin: 5px;
        font-family: Noto Sans, sans-serif;
      }
      .text-center{
        font-weight: 600;
        color: #1a2b4c !important;
      }
      
      table td,
      table th {
        -webkit-box-sizing: content-box;
        box-sizing: content-box;
        font-size: 12px;
        text-align: left;
        width: max-content;
      }

      th {
        background: #1a2b4c!important;
        color: #fff!important; 
      }
    </style>
  </head>

  <body>
     
    <h2 class="text-center">{{$pageTitle}}</h2>
    
    <section class="content">
        <table id="order" class="table table-bordered table-striped">
          <thead style="vertical-align: middle;">
            <tr>
              <th>S.No.</th>
              <th>Party</th>
              <th>Order No</th>
              <th>Amount</th>
              <th>Overdue Period (Days)</th>
            </tr>
          </thead>
          <tbody>
            @foreach($getExportData as $data)
              <tr>
                <td>{!! $data->id !!}</td>
                <td>{{ $data->party }}</td>
                <td>{{ $data->order_no }}</td>
                <td>{{ $data->amount }}</td>
                <td>{{ $data->overdueDays }}</td>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><b>Total</b></td>
              <td></td>
              <td><b>{{config('settings.currency_symbol')}} {{$totalAmount}}</b></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
    </section>
    
  </body>
</html>