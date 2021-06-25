<div class="box-body" id="printArea">
  <div class="col-xs-12">
    <div class="order-dtl-bg"> 
      <strong>Employee Name: </strong> <a href="{{ $employee_show }}" class='clientLinks' href='{{ $employee_show }}' data-viewable='{{ $employee_show }}'> {{ $employee_name }} </a> <br />
      <strong>Date: </strong> {{ getDeltaDate($date) }} <br />
      <strong>Time spent during visits: </strong> {{ $total_duration?$total_duration: "0 second" }} <br />
      <strong>Visit Route: </strong><i class="fa fa-map-marker distance-travelled-map"></i><i class='fa fa-spinner fa-pulse fa-fw' style='display:none;'></i> <br/>
      <strong>Total work hours: </strong> {{$totalWorkedHours}} <br />
      <strong>Checkin Time: </strong> {{ $checkin }} <br />
      <strong>Checkout Time: </strong> {{ $checkout }} <br />
      <strong>Distance Travelled: </strong> <span id="total-distance">{{$distance_travelled == "NA" ? $distance_travelled : $distance_travelled  . " KM" }}</span> <br />
    </div>
  </div>
  <div class="col-xs-12">
    <div class="table-responsive">
        <div class="detail-box">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="mw-160" style="max-width:20%;">Time</th>
                <th class="mw-160" style="max-width:15%;">Party Name</th>
                <th class="mw-160" style="max-width:15%;">Purpose</th>
                <th class="mw-170" style="max-width:30%;">Comments</th>
                <th class="mw-160" style="max-width:15%;">Time Spent</th>
                @if(!isset($location))
                <th class="mw-70" style="max-width:5%;">Location</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach ($empVisits as $item)
              <tr>
                <td>
                   <div>{{ $item['start_time'] }} - {{ $item['end_time'] }}</div>
                </td>
                <td>
                  <a href="{{$item['client_show']}}" class='clientLinks' href='{{$item['client_show']}}' data-viewable='{{$item['client_show']}}'> {{ $item['client_name'] }} </a>
                </td>
                <td>
                  {{ $item['visit_purpose'] }}
                </td>
                <td>
                  @if($item['images']->first())
                    <div class="imgDiv">
                      @foreach($item['images'] as $image)
                      <div class="col-xs-4">
                        <div class="imagePreview imageExistsPreview"
                          style="background-color: #fff;background-position: center center;background-size: contain;background-repeat: no-repeat;height: auto;box-shadow: 0px -3px 6px 2px rgb(0 0 0 / 0%);">
                          @if(isset($image['image_path']))
                          <img class="img-responsive display-imglists" @if(isset($image['image_path']))
                          src="{{ URL::asset('cms'.$image['image_path']) }}"
                          @endif alt="Picture Displays here" style="height: auto;" />
                          @else
                          <span class="pull-right">N/A</span>
                          @endif
                        </div>
                      </div>
                      @endforeach
                    </div>
                  @endif  
                  {{ $item['comments'] }} 
                </td>
                <td>
                  {{ $item['total_duration'] }} 
                </td>
                @if(!isset($location))
                <td>
                  <i class="fa fa-map-marker partial-marker" data-client-id="{{$item['client_id']}}" data-start-time="{{$item['unformatted_start_time']}}" data-end-time="{{$item['unformatted_end_time']}}"></i><i class='fa fa-spinner fa-pulse fa-fw' style='display:none;'></i>
                </td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>

        </div>
    </div>
  </div>

</div>