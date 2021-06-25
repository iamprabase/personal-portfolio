
<div class="ordTabContent tab-content">
  <div class="tab-pane active" id="general-setup" style="margin-top: 25px;">
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('order_prefix')) has-error @endif">
        {!! Form::label('order_prefix', 'Order Prefix') !!}
        {!! Form::text('order_prefix', isset($setting->order_prefix)? $setting->order_prefix:null , ['class' =>
        'form-control', 'placeholder' => 'Order Prefix']) !!}
        @if ($errors->has('order_prefix')) <p class="help-block has-error">{{ $errors->first('order_prefix') }}</p> @endif
      </div>
    </div>
    @if(config('settings.ageing')==1 && Auth::user()->can('ageing-view') &&  config('settings.order_with_amt') == 0)
    <div class="col-xs-6">
      <div class="form-group">
        {!! Form::label('credit_days', 'Default Credit Days') !!}
        <input class="form-control" oninput="validity.valid||(value='');" class="number" min="0" type="number" name="creditDays" id="creditDays" value="{{config('settings.credit_days')}}" />
        
      </div>
    </div>
    @endif

    <div class="col-xs-12 form-inline mb-20">
      <div class="form-group @if ($errors->has('salesman_to_party_radius')) has-error @endif">
      <div class="checkbox">
        <input type="checkbox" name="enable_salesman_to_party_radius" id="enable_salesman_to_party_radius" value="0" style="margin-top: 0; margin-right: 5px;" {{$setting->enable_salesman_to_party_radius? "checked": ""}}>
      </div>  
        {!! Form::label('salesman_to_party_radius', 'Only allow marking Orders and Zero Orders if salesman is within a radius of ', ['class' => 'margin-r-5 margin-bottom-none']) !!}
        {!! Form::number('salesman_to_party_radius', isset($setting->salesman_to_party_radius)? $setting->salesman_to_party_radius:null , ['class' => 'form-control margin-r-5 margin-bottom-none', 'placeholder' => '', "min" => 100, "style"=>"width: 70px;", 'onFocusout' => 'validatePositiveNumber(this, 100)']) !!}
        {!! Form::label('salesman_to_party_radius', 'meters from party.') !!}
        @if ($errors->has('salesman_to_party_radius')) <p class="help-block has-error">{{ $errors->first('salesman_to_party_radius') }}</p> @endif
      </div>
    </div>


    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('order_with_authsign')) has-error @endif">
        {!! Form::label('order_with_authsign', 'Do you want signature while printing order invoice?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('order_with_authsign', '0' , ($clientSettings->order_with_authsign==0)?true:false, ['class'=>'minimal']) }} No
          </label>
          <label>
            {{ Form::radio('order_with_authsign', '1' , ($clientSettings->order_with_authsign==1)?true:false, ['class'=>'minimal']) }}
            Yes
          </label>
        </div>
      </div>
    </div>

    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('order_approval')) has-error @endif">
        {!! Form::label('order_approval', 'Do you want to include dispatch details in each order?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('order_approval', '0' , ($clientSettings->order_approval==0)?true:false, ['class'=>'minimal']) }} No
          </label>
          <label>
            {{ Form::radio('order_approval', '1' , ($clientSettings->order_approval==1)?true:false, ['class'=>'minimal']) }}
            Yes
          </label>
        </div>
      </div>
    </div>
    @if(config('settings.order_with_amt') == 0)
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('non_zero_discount')) has-error @endif">
        {!! Form::label('non_zero_discount', 'Is it mandatory to offer non-zero discount in orders?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('non_zero_discount', '0' , ($clientSettings->non_zero_discount==0)?true:false, ['class'=>'minimal']) }} No
          </label>
          <label>
            {{ Form::radio('non_zero_discount', '1' , ($clientSettings->non_zero_discount==1)?true:false, ['class'=>'minimal']) }}
            Yes
          </label>
        </div>
      </div>
    </div>

    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('product_level_discount')) has-error @endif">
        {!! Form::label('product_level_discount', 'Would discount be at product level or on overall invoice?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('product_level_discount', '0' , ($clientSettings->product_level_discount==0)?true:false, ['class'=>'minimal']) }} Overall
          </label>
          <label>
            {{ Form::radio('product_level_discount', '1' , ($clientSettings->product_level_discount==1)?true:false, ['class'=>'minimal']) }}
            Product-wise
          </label>
        </div>
      </div>
    </div>

    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('product_level_tax')) has-error @endif">
        {!! Form::label('product_level_tax', 'Would the tax be product-wise or on overall amount?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('product_level_tax', '0' , ($clientSettings->product_level_tax==0)?true:false, ['class'=>'minimal']) }} Overall
          </label>
          <label>
            {{ Form::radio('product_level_tax', '1' , ($clientSettings->product_level_tax==1)?true:false, ['class'=>'minimal']) }}
            Product-wise
          </label>
        </div>
      </div>
    </div>
    @endif

    @if($partyTypeLevel)
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('order_to')) has-error @endif">
        {!! Form::label('order_to', 'Who can order be placed to?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('order_to', '0' , ($clientSettings->order_to==0)?true:false, ['class'=>'minimal']) }} Only
            Superior
          </label>
          <label>
            {{ Form::radio('order_to', '1' , ($clientSettings->order_to==1)?true:false, ['class'=>'minimal']) }}
            Superior and superior party types
          </label>
        </div>
      </div>
    </div>
    @endif

    @if(config('settings.order_with_amt') ==0 && config('settings.accounting')==1)
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('order_above_credit_limit')) has-error @endif">
        {!! Form::label('order_above_credit_limit', 'Do you want to allow users to take orders above credit limit?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('order_above_credit_limit', '0' , ($clientSettings->order_above_credit_limit==0)?true:false,
            ['class'=>'minimal']) }} No
          </label>
          <label>
            {{ Form::radio('order_above_credit_limit', '1' , ($clientSettings->order_above_credit_limit==1)?true:false,
            ['class'=>'minimal']) }} Yes
          </label>
        </div>
      </div>
    </div>
    <div class="col-xs-12">
      <div class="form-group @if ($errors->has('outstanding_amt_calculation')) has-error @endif">
        {!! Form::label('outstanding_amt_calculation', 'How do you want the outstanding amount to be calculated?') !!}
        <div class="radio">
          <label class="margin-r-20">
            {{ Form::radio('outstanding_amt_calculation', '0' ,
            ($clientSettings->outstanding_amt_calculation==0)?true:false, ['class'=>'minimal']) }} Automatically
          </label>
          <label>
            {{ Form::radio('outstanding_amt_calculation', '1' ,
            ($clientSettings->outstanding_amt_calculation==1)?true:false, ['class'=>'minimal']) }} Manually (via import/Tally/other software)
          </label>
        </div>
      </div>
    </div>
    @endif

    <div class="col-xs-12">
    <button id="btnOrderSetupUpdate" type="button" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;margin-top: 25px;"class="btn btn-primary pull-right">Update
            </button>
    </div>

  </div>

  
  <div class="tab-pane" id="tax-setup" style="margin-top: 25px;">
  
    <div class="col-xs-12">
    <div class="row">

    <div class="col-xs-12">
    
    </div>


      <div class="col-xs-12">
        <!-- <div class="panel panel-primary"> -->
          <!-- <div class="panel-heading"> -->
            <h4><label>Currently Implied Taxes</label>
            <button id="addTaxesShowModalButton" type="button" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;"class="btn btn-primary pull-right"  data-toggle="modal"
               data-target="#addTaxesModal">Add Tax
            </button>
            </h4>
            <div class="" id="showTaxes">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Tax Name</th>
                    <th>Percentage</th>
                    <th>Default</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($taxes as $tax)
                  <tr id="taxRow{{$tax->id}}">
                    <td>{{$tax->name}}</td>
                    <td>{{$tax->percent}}</td>
                    <td>
                    <input type="hidden" name="edit_tax_id[{{$tax->id}}]" value="{{$tax->id}}">
                    <input type="checkbox" name="edit_defaultTax[{{$tax->id}}]" class="edit_defaultTax"
                                id="edit_defaultTax{{$tax->id}}" data-id="{{$tax->id}}" {{($tax->default_flag)?"checked":""}} />
                    </td>
                    <td>
                    <a id="updateTax_{{$tax->id}}" class="btn btn-warning btn-xs update-tax-btn"
                                data-id="{{$tax->id}}" data-name="{{$tax->name}}" data-percent="{{$tax->percent}}">
                                <i class="fa fa-edit"></i>
                              </a>
                    <a id="removeTax_{{$tax->id}}" onclick="removeTaxAlert({{$tax->id}})"
                      class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
      </div>
    </div>
  </div>
  
</div>
  <!-- /.tab-pane -->

<!-- <div class="modal fade" id="addTaxesModal" tabindex="-1" role="dialog">
  <div class="col-xs-12">
  <h4><label>Add Taxes To be Implied on Orders</label></h4>
  <div class="table-responsive">
    <table class="table table-bordered" id="dynamic_field">
      <thead>
        <tr>
          <th>Tax Name</th>
          <th>Percentage</th>
          <th>Set as Default</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
          
      </tbody>
    </table>
    <button type="button" name="add" id="add" class="btn btn-success btn-md">Add Field</button>
  </div>
  

  <div class="col-xs-12">
    <button id="btnTaxSetupUpdate" type="button" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;margin-top: 25px;"class="btn btn-primary pull-right">Submit
            </button>
    </div>
</div> -->
<div class="modal fade" id="addTaxesModal" tabindex="-1" role="dialog">
    <form id="addNewStatus" method="post"
          action="{{domain_route('company.admin.orderstatus.store')}}">@csrf
      <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4><label>Add Taxes To be Implied on Orders</label></h4>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
            <table class="table table-bordered" id="dynamic_field">
              <thead>
                <tr>
                  <th>Tax Name</th>
                  <th>Percentage</th>
                  <th>Set as Default</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                  
              </tbody>
            </table>
            <button type="button" name="add" id="add" class="btn btn-success btn-md">Add Field</button>
          </div>
          </div>
          <div class="modal-footer">
          <button id="btnTaxSetupUpdate" type="button" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;margin-top: 25px;"class="btn btn-primary pull-right">Submit
            </button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->
</div><!-- /.modal -->