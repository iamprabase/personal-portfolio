


<div class="col-xs-12">
  <h3 class="site-tital">Disk Space Per Module </h3>
</div>
<div class="row info">
  <div class="col-xs-12 item-sec">
    <div class="riw-item" style="padding:10px">
      <span class="riw-bottom">Total Usage</span>
      <span class="riw-top">{{$total_usage}}</span>
    </div>
  </div>
  @foreach($module_space as $title => $space)
    <div class="col-xs-4 item-sec">
      <div class="riw-item" style="padding:10px">
        <span class="riw-bottom">{{$title}}</span>
        <span class="riw-top">{{ $space }}</span>
      </div>
    </div>
  @endforeach
</div>