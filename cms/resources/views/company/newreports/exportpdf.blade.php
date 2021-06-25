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
        font-family: Noto Sans, sans-serif !important;
      }
      .text-center{
        font-size: 28px;
        color: #1a2b4c !important;
      }

      span{
        color: #1a2b4c !important;
        font-family: Noto Sans, sans-serif !important;
      }

      table{
        width: 100% !important;
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
    <section class="content">
      <span class="text-center">{{$pageTitle}}</span>
        <table id="order" class="table table-bordered table-striped">
          <thead style="vertical-align: middle;">
            <tr>
              <th>S.No.</th>
              @foreach($columns as $cols)
                <th>{{$cols}}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($getExportData as $data)
              <tr>
                @foreach($properties as $prop)
                  <td>{{ $data->$prop }}</td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
    </section>
    
  </body>
</html>