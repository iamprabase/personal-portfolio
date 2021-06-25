@if (\Session::has('alert'))
  <div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4><br/>
    {{ \Session::get('alert') }}
  </div>
@endif
@if ($errors->has('spreadsheet'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{$errors->first('spreadsheet')}}
  </div>
@endif
@if (\Session::has('success'))
  <div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <p>Success!</p>
    {{ \Session::get('success') }}
  </div>
@endif
