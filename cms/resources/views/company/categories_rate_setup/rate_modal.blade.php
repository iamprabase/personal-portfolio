<div class="modal fade" id="addRateModal" tabindex="-1" role="dialog">
  <form id="add_new_rate" method="post" action="{{domain_route('company.admin.category.rates.store')}}">
    @csrf
    <input type="hidden" name="category_id" required>
              
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Add New Rate</h4>
        </div>
        <div class="modal-body">
         
          <div class="row custom_rate_form">
            <div class="col-xs-4" style="text-align: left;">
              <label for="">Name</label><span style="color:red">*</span>
            </div>
            <div class="col-xs-4">
                <span class="input-group">
                <input class="form-control rate_name" type="text" name="rate_name" required="">
                </span>
                <span class="name_err errlabel" style="color:red">
              </span>
            </div>
          </div>

          <div class="row custom_rate_form" style="margin-top: 25px;">
            <div class="col-xs-4" style="text-align: left;margin-top: 10px;">
              <label for="">Set Custom Rate to be</label>
            </div>
            <div class="col-xs-4">
                <span class="input-group">
                  <input class="form-control quick_rate_setup_input" type="text" name="discount_percent">
                  <span class="input-group-addon">%</span>
                </span>
              <span class="discount_percent_err errlabel" style="color:red">
              </span>
            </div>
            <div class="col-xs-4" style="text-align: left;width: fit-content;margin-top: 10px;">
              <label> of Original Rate.</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="addRateBtn" type="submit" class="btn btn-primary">Create</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->