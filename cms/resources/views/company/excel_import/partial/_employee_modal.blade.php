<button type="button" class="btn btn-default" data-toggle="modal" data-target="#excelModal">
  <i class="fa fa-download fa fw"></i> Download Sample Format
</button>

<div class="modal fade" id="excelModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="excelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" style="width: 400px;">
    <div class="modal-content">
      <div class="modal-body" style="padding: 10px;">
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-body">
                <div class="row">
                  <div class="col-xs-2">
                    <img src="{{ asset('assets/dist/img/excel_logo.png') }}" alt="Excel workbook Logo" height="50">
                  </div>
                  <div class="col-xs-10">
                    <a href="{{ asset('assets/files/employees.xls') }}" class="btn btn-link"> Download .xls spreadsheet (97-2003)</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-body">
                <div class="row">
                  <div class="col-xs-2">
                    <img src="{{ asset('assets/dist/img/ms_excel_logo.png') }}" alt="MS Excel Logo" height="50">
                  </div>
                  <div class="col-xs-10">
                    <a href="{{ asset('assets/files/employees.xlsx') }}" class="btn btn-link"> Download .xlsx spreadsheet (2007 - current)</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>