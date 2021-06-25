<section class="content">
  <div class="row">
    <div id="loader1" style="top:45%; left:60%;" hidden>
      <img src="{{asset('assets/dist/img/loader2.gif')}}" />
      <p
        style="position: absolute;top: 40%;left: 50%;transform: translate(-50%, -50%);text-align: center;font-weight: bolder;">
        Report is being generated.<br /> Depending upon the internet connection it may take several minutes.<br />
        Please come back in a moment.
      </p>
    </div>
    <div class="box" id="party_wise_latest_box">
      <div class="box-header">
        <h3 class="box-title">Party-wise Latest Stock Report </h3>
        <span id="exportBtn" class="pull-right">

        </span>
      </div>
      <div class="box-body">
        <form action="{{domain_route('company.admin.stockreport')}}" method="post" id="stockreports"
          style="margin-bottom:10px">
          @csrf
          <div class="row">
            <div class="col-xs-3">
              <label>
                Select Party
              </label>
              <select id="party_id" name="party_id" class="select2" style="width: 100%;" required>
                <option></option>
                @php $decoded_party = json_decode($parties); @endphp
              </select>
            </div>
            <div class="col-xs-3">
              <button type="submit" class="btn btn-default" id="getReport" style="width:100%;margin-top:25px;">
                <i class="fa fa-book"></i> Get Report
              </button>
            </div>
          </div>
        </form>

        <table id="party_wise_latest" class="table table-bordered table-striped" style="width: 100%;">
          <thead>
            <tr>
              <th style="width:8px !important;">S.No.</th>
              <th>
                <p>Product Name</p><span style="font-size:smaller;"> (Brand / Category)</span>
              </th>
              <th>Product Name</th>
              <th>Variant</th>
              <th>Unit</th>
              <th>Brand</th>
              <th>Category</th>
              <th>Quantity</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
          </tfoot>
        </table>
      </div>
    </div>
    <form method="post" action="{{domain_route('company.admin.stockreport.customPdfExport')}}"
      class="pdf-export-form hidden" id="pdf-generate">
      {{csrf_field()}}
      <input type="text" name="exportedData" class="exportedData" id="exportedData">
      <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
      <input type="text" name="reportName" class="reportName" id="reportName">
      <input type="text" name="columns" class="columns" id="columns">
      <input type="text" name="properties" class="properties" id="properties">
      <button type="submit" id="genrate-pdf">Generate PDF</button>
    </form>
  </div>
</section>