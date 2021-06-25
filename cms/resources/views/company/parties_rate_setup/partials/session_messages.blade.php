@if (\Session::has('success'))
  <div class="alert alert-success">
    <p>{{ \Session::get('success') }}</p>
  </div>
  <br />
@endif
@if (\Session::has('error'))
  <div class="alert alert-warning">
    <p>{{ \Session::get('error') }}</p>
  </div>
  <br />
@endif