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
              <!-- <th>S.No.</th>
              <th>Party Name</th>
              <th>Bank Name</th>
              <th>Employee Name</th>
              <th>Cheque Date</th>
              <th>Receive Date</th>
              <th>Amount</th>
              <th>Notes</th>
              <th>Status</th> -->
              @foreach($columns as $column)
                <th>{{$column}}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            <!-- @foreach($getExportData as $data)
              <tr>
                <td>{!! $data->id !!}</td>
                <td>{{ $data->company_name }}</td>
                <td>{{ $data->bank_name }}</td>
                <td>{{ $data->employee_name }}</td>
                <td>{{ $data->cheque_date }}</td>
                <td>{{ $data->payment_date }}</td>
                <td>{{ $data->payment_received }}</td>
                <td>{{ $data->payment_status_note }}</td>
                <td>{{ $data->payment_status }}</td>
            @endforeach -->
            @foreach($getExportData as $data)
              <tr>
                @foreach($properties as $property)
                  <td>{!! $data->$property !!}</td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
    </section>
    
  </body>
</html>