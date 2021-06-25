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
  </head>

  <body>
     
    <h2 class="text-center">{{$pageTitle}}</h2>
    
    <section class="content">
        <table id="order" class="table table-bordered table-striped">
          <thead style="vertical-align: middle;">
            <tr>
              <!-- <th>S.No.</th>
              <th>Order No.</th>
              <th>Order Date</th>
              <th>Party Name</th>
              <th>Created By</th>
              @if($partyTypeLevel)
                <th>Ordered To</th>
              @endif
              @if($orderwithQTYAMT==0)
                <th>Grand Total</th>
              @endif
              <th>Order Status</th> -->
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
                <td>{{ $data->orderno }}</td>
                <td>{{ $data->orderdate }}</td>
                <td>{{ $data->partyname }}</td>
                <td>{{ $data->createdby }}</td>
                @if($partyTypeLevel)
                  <td>{{$data->ordered_to}}</td>
                @endif
                @if($orderwithQTYAMT==0)
                  <td>{{ $data->grandtotal }}</td>
                @endif
                <td>{{ $data->orderstatus }}</td>
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