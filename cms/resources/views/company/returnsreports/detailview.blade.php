{{-- <section class="content" id="detail-view-content"> --}}
  <div class="box-body">
    <div class="row">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Product-Party-wise Returns Report </h3>
          <span id="detail-view-exports" class="detail-view-exports pull-right"></span>
        </div>
        <div class="box-body">
          <table id="detail-view-table" class="detail-view-table table table-bordered table-striped table-responsive" style="width: 100%;">
            <thead>
              <tr>
                @if($flag==1) 
                  <th>Variant Name</th>
                @else
                  <th hidden></th>
                @endif
                <th>Returned By</th>
                <th>Date</th>
                <th>Quantity</th>
                <th>Reason</th>
              </tr>
            </thead>
            <tbody>
              @forelse($prepQuery as $data)
              <tr>
                @if($flag==1) 
                  <td>{{$data->variant_name}}</td>
                @else
                  <td hidden></td>
                @endif
                <td>{{$data->company_name}}</td>
                <td>{{getDeltaDateFormat($data->return_date)}}</td>
                <td>{{$data->quantity}} {{$data->unit_name}}</td>
                <td>{{$data->reason}}</td>

              </tr>
              @empty
                <td></td>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
{{-- </section> --}}