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
              <th>Current</th>
              <th>1-30 Days</th>
              <th>31-60 Days</th>
              <th>61-90 Days</th>
              <th>>90 Days</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($getExportData as $data)
              <tr>
                <td>{!! $data->id !!}</td>
                <td>{{ $data->company_name }}</td>
                <td>{{ $data->current }}</td>
                <td>{{ $data->before30days }}</td>
                <td>{{ $data->due31to60days }}</td>
                <td>{{ $data->due61to90days }}</td>
                <td>{{ $data->over90days }}</td>
                <td>{{ $data->total }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><b>Total</b></td>
              <td><b>{{config('settings.currency_symbol')}}{{$totalcurrent}}</b></td>
              <td><b>{{config('settings.currency_symbol')}}{{$totalbefore30days}}</b></td>
              <td><b>{{config('settings.currency_symbol')}}{{$totaldue31to60days}}</b></td>
              <td><b>{{config('settings.currency_symbol')}}{{$totaldue61to90days}}</b></td>
              <td><b>{{config('settings.currency_symbol')}}{{$totalover90days}}</b></td>
              <td><b>{{config('settings.currency_symbol')}}{{$totalAmount}}</b></td>
            </tr>
          </tfoot>
        </table>
    </section>
    
  </body>
</html>