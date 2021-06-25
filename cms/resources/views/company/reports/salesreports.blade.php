<table id="dailyempreport" class="table table-bordered table-striped">
  <thead>
  <tr>
    @foreach($data[2] as $key=>$value)
      <th>{{$value}}</th>
    @endforeach
  </tr>
  <tr>
    @foreach($data[3] as $key=>$value)
      <th>{{$value}}</th>
    @endforeach
  </tr>
  </thead>
  <tbody>
  @foreach($distributors as $distributor)
    <tr>
      <td><strong>Store Name :</strong> <span style="color:red;">{{ $distributor->name }}</span></td>
      <td><strong>Address :</strong> <span style="color:red;">{{ $distributor->address_1 }}</span></td>
      <td><strong>Phone Number :</strong> <span style="color:red;">{{ $distributor->phone }}</span></td>
    </tr>
    <tr>
      <td>Store Name :</td>
      <td>Address :</td>
      <td>Phone Number :</td>
    </tr>
  @endforeach
  </tbody>
</table>
