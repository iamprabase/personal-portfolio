<form action="{{ $route }}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="form-group row">
    <div class="col-xs-4 text-right">
      <label for="spreadsheet">Select a spreadsheet you want to upload</label>
    </div>
    <div class="col-xs-4 text-center">
      <input type="file" name="spreadsheet" id="spreadsheet">
    </div>
    <div class="col-xs-4">
      <button type="submit" class="btn btn-default" style="margin-top: -8px;"><i class="fa fa-upload fa-fw"></i> Upload</button>
    </div>
  </div>
</form>
