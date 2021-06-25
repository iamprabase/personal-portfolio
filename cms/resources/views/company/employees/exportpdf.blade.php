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
      }

      th {
        background: #1a2b4c!important;
        color: #fff!important; 
      }
    </style>
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">--}}
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">--}}
  </head>

  <body>
     
    <h2 class="text-center">{{$pageTitle}}</h2>
    
    <section class="content">
        <table id="order" class="table table-bordered table-striped">
          <thead style="vertical-align: middle;">
            <tr>
              <!-- <th>S.No.</th>
              <th>Employee Name</th>
              <th>Phone no.</th>
              <th style="width: 15%">Email</th>
              <th>Employee Group</th>
              <th>Designation</th>
              <th>Status</th>
              <th>Last Action</th> -->
              @foreach($columns as $column)
                <th>{{$column}}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @php $num=1 @endphp
            <!-- @foreach($getExportData as $data)
              <tr>
                <td>{{ $num }}</td>
                <td>{{ $data->name }}</td>
                <td>{{ $data->phone }}</td>
                <td>{{ $data->email }}</td>
                <td>{{ $data->employeegroup }}</td>
                <td>{{ $data->designations }}</td>
                <td>{{ $data->status }}</td>
                <td>{{ $data->last_action }}</td>
              </tr>
              @php ++$num @endphp
            @endforeach -->
            @foreach($getExportData as $data)
              <tr>
                @foreach($properties as $property)
                  <td>{!! $data->$property !!}</td>
                @endforeach
              </tr>
              @php ++$num @endphp
            @endforeach
          </tbody>
        </table>
    </section>
    
  </body>

</html>