<div class="box-body" id="printArea">
  <div class="col-xs-12">
    <div class="order-dtl-bg"> 
      <strong>Employee Name: </strong> {{ $employee_name }} <br />
      <strong>Date: </strong> {{ getDeltaDate($date) }} <br />
      <strong>Time Spent: </strong> {{ $total_duration }} <br />
      <strong>Checkin Time: </strong> {{ $checkin }} <br />
      <strong>Checkout Time: </strong> {{ $checkout }} <br />
      <strong>Distance Travelled: </strong> 50 Km <i class="fa fa-map-marker"></i> <br />
    </div>
  </div>
  <div class="col-xs-12">
    <div class="table-responsive">
      @foreach ($empVisits as $item)
        <div class="detail-box">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th colspan="2" class="text-center"> {{ $item['start_time'] }} - {{ $item['end_time'] }} </th>
              </tr>
            </thead>
            <tbody>
            <tr>
              <th scope="row">Party Name</th>
              <td>
                  {{ $item['client_name'] }}
              </td>
            </tr>
            <tr>
              <th scope="row">Time Spent</th>
              <td>
                  {{ $item['total_duration'] }} <i class="fa fa-map-marker"></i>
              </td>
            </tr>
            <tr>
              <th scope="row">Visit Purpose</th>
              <td>{{ $item['visit_purpose'] }}</td>
            </tr>
            <tr>
              <th scope="row">Comments</th>
              <td>{{ $item['comments'] }}</td>
            </tr>
            @if($item['images']->first())
              <tr>
                <th scope="row"> Images</th>
                <td>
                  <div class="imgDiv">
                    @foreach($item['images'] as $image)
                    <div class="col-xs-4">
                      <div class="imagePreview imageExistsPreview"
                        style="background-color: #fff;background-position: center center;background-size: contain;background-repeat: no-repeat;">
                        @if(isset($image['image_path']))
                        <img class="img-responsive display-imglists" @if(isset($image['image_path']))
                        src="{{ URL::asset('cms'.$image['image_path']) }}"
                        @endif alt="Picture Displays here" style="max-height: 500px;"/>
                        @else
                        <span class="pull-right">N/A</span>
                        @endif
                      </div>
                    </div>
                    @endforeach
                  </div>
                </td>
              </tr>
            @endif
            </tbody>
          </table>

        </div>
      @endforeach
    </div>
  </div>

</div>