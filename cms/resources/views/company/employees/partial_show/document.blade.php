<div class="box-body">
  <form id="UpdateDocumentDetail" enctype="multipart/formData">
    <div class="row">
      @if(Auth::user()->isCompanyEmployee() && Auth::user()->EmployeeId()==$employee->id)
      @elseif(!($isManager=='true') && $employee->is_owner==1)
      @else
      @if(Auth::user()->can('employee-update'))
      <span id="ActivateDocumentEdit" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
      <span id="ActivateDocumentCancel" class="btn btn-default btn-sm pull-right hide" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
      <span id="ActivateDocumentUpdate" class="hide"><button style="margin-right: 10px;" class="btn btn-default btn-sm pull-right" type="submit"><i class="fa fa-edit"></i>Update</button></span>
      @endif
      @endif
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-file icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Resume</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p id="empResume">
              @if($employee->resume)
                <a href="{{ URL::asset('cms'.$employee->resume) }}">View/download</a> <i class="fa fa-trash btn-red" data-type="resume"></i>
              @else
                N/A
              @endif
            </p>
            <div class="text-form" hidden>
                <div class="input-group input-file" name="resume">
                  <span class="input-group-btn">
                        <button class="btn btn-default btn-choose" type="button">Choose</button>
                    </span>
                    <input type="text" name="resume" class="form-control" placeholder='Choose a file...' />
                    <span class="input-group-btn">
                         <button class="btn btn-danger btn-reset" type="button">Remove</button>
                    </span>
                </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-file icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Offer Letter</h4>
            <p id="empOL">
              @if($employee->offer_letter)
                <a href="{{ URL::asset('cms'.$employee->offer_letter) }}">View/download</a> <i class="fa fa-trash btn-red" data-type="offer_letter"></i>
              @else
                N/A
              @endif
            </p>
            <div class="text-form" hidden>
              <div class="input-group input-file" name="offer_letter">
                <span class="input-group-btn">
                      <button class="btn btn-default btn-choose" type="button">Choose</button>
                  </span>
                  <input type="text" name="offer_letter" class="form-control" placeholder='Choose a file...' />
                  <span class="input-group-btn">
                       <button class="btn btn-danger btn-reset" type="button">Remove</button>
                  </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-file icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Joining Letter</h4>
            <p id="empJL">
              @if($employee->joining_letter)
                <a href="{{ URL::asset('cms'.$employee->joining_letter) }}">View/download</a> <i class="fa fa-trash btn-red" data-type="joining_letter"></i>
              @else
                N/A
              @endif
            </p>
            <div class="text-form" hidden>
              <div class="input-group input-file" name="joining_letter">
                <span class="input-group-btn">
                      <button class="btn btn-default btn-choose" type="button">Choose</button>
                  </span>
                  <input type="text" name="joining_letter" class="form-control" placeholder='Choose a file...' />
                  <span class="input-group-btn">
                       <button class="btn btn-danger btn-reset" type="button">Remove</button>
                  </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-file icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Contract And Agreement</h4>
            <p id="empCA">
              @if($employee->contract)
                <a href="{{ URL::asset('cms'.$employee->contract) }}">View/download</a> <i class="fa fa-trash btn-red" data-type="contract"></i>
              @else
                N/A
              @endif
            </p>
            <div class="text-form" hidden>
              <div class="input-group input-file" name="contract">
                <span class="input-group-btn">
                      <button class="btn btn-default btn-choose" type="button">Choose</button>
                  </span>
                  <input type="text" name="contract" class="form-control" placeholder='Choose a file...' />
                  <span class="input-group-btn">
                       <button class="btn btn-danger btn-reset" type="button">Remove</button>
                  </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-file icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">ID Proof</h4>
            <p id="empIDP">
              @if($employee->id_proof)
                <a href="{{ URL::asset('cms'.$employee->id_proof) }}">View/download</a> <i class="fa fa-trash btn-red" data-type="id_proof"></i>
              @else
                N/A
              @endif
            </p>
            <div class="text-form" hidden>
              <div class="input-group input-file" name="id_proof">
                <span class="input-group-btn">
                    <button class="btn btn-default btn-choose" type="button">Choose</button>
                </span>
                <input type="text" name="id_proof" class="form-control" placeholder='Choose a file...' />
                <span class="input-group-btn">
                     <button class="btn btn-danger btn-reset" type="button">Remove</button>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </form>
</div>