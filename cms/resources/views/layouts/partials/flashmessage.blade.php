@if (\Session::has('success'))
  <div class="alert alert-success">
    <p>{{ \Session::get('success') }}</p>
  </div>
  <br />
@endif
@if (\Session::has('error'))
  <div class="alert alert-danger">
    <p>{{ \Session::get('error') }}</p>
  </div>
  <br />
@endif
@if (\Session::has('warning'))
  <div class="alert alert-warning">
    <p>{{ \Session::get('warning') }}</p>
  </div>
  <br />
@endif