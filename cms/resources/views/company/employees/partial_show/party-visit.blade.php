<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" aria-hidden="true">&times;</button>
        <p>{{ \Session::get('success') }}</p>
      </div><br/>
    @endif
    @if (\Session::has('alert'))
    <div class="alert alert-warning">
      <p>{{ \Session::get('alert') }}</p>
    </div><br/>           
    @endif
    <div class="box">
      <div class="box-header">
        <span id="partyvisitexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body table-fix">
        <div id="mainBox">
          <table id="partyvisit" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>No. of Visits</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>

<form method="post" action="{{domain_route('company.admin.clientvisit.customPdfExport')}}" class="pdf-export-form hidden"
  id="pdf-generate-party-visit">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="party-visit-exportedData" id="party-visit-exportedData">
  <input type="text" name="pageTitle" class="party-visit-pageTitle" id="party-visit-pageTitle">
  <input type="text" name="columns" class="party-visit-columns" id="party-visit-columns">
  <input type="text" name="properties" class="party-visit-properties" id="party-visit-properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>