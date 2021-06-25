{!! Form::model($clientSettings, array('url' => url(route('app.company.setting.updateOther',[$clientSettings->id])) ,
'method' => 'PATCH', 'files'=> true)) !!}
@if (\Session::has('warning'))
  <div class="alert alert-warning">
    <p>{{ \Session::get('warning') }}</p>
  </div>
  <br />
@endif
<div class="row">
  <!-- basic setup starts -->
  <div class="panel panel-success">
    <div class="panel-heading custom-panel-heading">
      <span class="panel-heading-text">Basic Setup</span>
    </div>
    <div class="panel-body">
      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('default_currency')) has-error @endif">
          {!! Form::label('default_currency', 'Currency') !!}
          <select name="default_currency" id="default_currency" class="form-control">
            <option value="">Select a currency</option>
            @foreach($currencies as $currency)
            <option value="{{$currency->code}}" symbol="{{$currency->symbol}}"
              {{($clientSettings->default_currency == $currency->code)? 'selected':''}}>{{$currency->country}}
              , {{$currency->code}}</option>
            @endforeach
          </select>
          @if ($errors->has('default_currency')) <p class="help-block has-error">
            {{ $errors->first('default_currency') }}</p> @endif
        </div>
      </div>

      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('currency_symbol')) has-error @endif">
          {!! Form::label('currency_symbol', 'Currency Symbol') !!}
          <input type="text" name="currency_symbol" id="currency_symbol" class="form-control"
            placeholder="Currency Symbol"
            value="{{isset($clientSettings->currency_symbol)? $clientSettings->currency_symbol:null}}" readonly>
          @if ($errors->has('currency_symbol')) <p class="help-block has-error">{{ $errors->first('currency_symbol') }}
          </p> @endif
        </div>
      </div>

      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('time_zone')) has-error @endif">
          {!! Form::label('time_zone', 'Time Zone') !!}
          <select name="time_zone" class="form-control" required>
            <!-- <option value="Asia/Kathmandu">{{$timezonelist['Asia/Kathmandu']}}</option> -->
            @foreach (timezone_identifiers_list() as $timezone)
            <option value="{{ $timezone }}" {{ $timezone == $clientSettings->time_zone ? ' selected' : '' }}>
              {{ $timezone }}</option>
            @endforeach
          </select>
          @if ($errors->has('time_zone')) <p class="help-block has-error">{{ $errors->first('time_zone') }}</p> @endif
        </div>
      </div>

      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('allowed_party_type_levels')) has-error @endif">
          {!! Form::label('allowed_party_type_levels', 'Allowed Party Type Levels') !!}

          <input type="number" min="{{$current_party_type_hierarchy}}" max="3" step="1" placeholder="Allowed Party Type Levels" value="{{$clientSettings->allowed_party_type_levels}}"
            class="form-control" name="allowed_party_type_levels" onfocusout="validateMaxValue(this, 3, {{$current_party_type_hierarchy}})" required>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('user_roles')) has-error @endif">
          {!! Form::label('user_roles', 'Allowed User Roles') !!}

          <input type="number" min="{{$current_roles}}" step="1" placeholder="Allowed User Roles" value="{{$clientSettings->user_roles}}"
            class="form-control" name="user_roles" onfocusout="validateMaxValue(this, null, {{$current_roles}})" required>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('user_hierarchy_level')) has-error @endif">
          {!! Form::label('user_hierarchy_level', 'Allowed Hierarchy Levels') !!}

          <input type="number" min="{{$current_designation_hierarchy}}" step="1" placeholder="Allowed Hierarchy Levels" value="{{$clientSettings->user_hierarchy_level}}"
            class="form-control" name="user_hierarchy_level" onfocusout="validateMaxValue(this, null, {{$current_designation_hierarchy}})" required>
        </div>
      </div>
      
      <!-- <div class="col-xs-6">
        <div class="form-group @if ($errors->has('allow_party_duplication')) has-error @endif">
          {!! Form::label('allow_party_duplication', 'Allow Party Duplication') !!}{{-- <span style="color: green">*</span> --}}
          <span class="checkbox" style="margin-top: 2px;">
            <label style="padding: 0px;">
              {{ Form::radio('allow_party_duplication', '0' , ($clientSettings->allow_party_duplication==0)?true:false, ['class'=>'minimal']) }} No
            </label>
            <label>
              {{ Form::radio('allow_party_duplication', '1' , ($clientSettings->allow_party_duplication==1)?true:false, ['class'=>'minimal']) }} Yes
            </label>
          </span>
        </div>
      </div> -->

      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('ncalendar')) has-error @endif">
          {!! Form::label('ncal', 'Nepali Calendar') !!}{{-- <span style="color: green">*</span> --}}
          <span class="checkbox" style="margin-top: 2px;">
            <label style="padding: 0px;">
              {{ Form::radio('ncal', '0' , ($clientSettings->ncal==0)?true:false, ['class'=>'minimal']) }} No
            </label>
            <label>
              {{ Form::radio('ncal', '1' , ($clientSettings->ncal==1)?true:false, ['class'=>'minimal']) }} Yes
            </label>
          </span>
        </div>
      </div>
       {{-- <div class="col-xs-6">
        <div class="form-group @if ($errors->has('date_format')) has-error @endif">
        {!! Form::label('date_format', 'Date Format') !!}
        {!! Form::select('date_format', array('dd-M-yyyy' => 'dd-M-yyyy (31 Dec 2016)'), null, ['class' => 'form-control', 'required'=>'required']) !!}
        @if ($errors->has('date_format')) <p class="help-block has-error">{{ $errors->first('date_format') }}</p> @endif
                
        </div>
      </div>  --}}
    </div>
  </div>
  <!-- Basic setup ends -->

  @if($clientSettings->custom_module == 1)
  <div class="panel panel-success">
    <div class="panel-heading custom-panel-heading">
      <span class="panel-heading-text">Custom Module Setup</span></div>
    <div class="panel-body">
      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('no_of_custom_module')) has-error @endif">
          {!! Form::label('no_of_custom_module', ' No. of custom forms') !!}

          <input type="number" min="{{$noOfMinCustomModule}}" step="1" placeholder="No. of custom forms" value="{{$noOfCustomModule}}"
            class="form-control" name="no_of_custom_module">
        </div>
      </div>
    </div>
  </div>
  @endif
  <!-- Colaterals setup starts -->
  <div class="panel panel-success">
    <div class="panel-heading custom-panel-heading">
      <span class="panel-heading-text">Colaterals Setup</span></div>
    <div class="panel-body">
      <div class="col-xs-4">
        <div class="form-group @if ($errors->has('upload_types')) has-error @endif">
          {!! Form::label('upload_types', 'Upload Types') !!}
          <select name="upload_types[]" id="upload_types" class="upload_types" multiple="true">
            <option value="jpeg,jpg,png,gif" @if(preg_match('/jpeg,jpg,png,gif/', ($clientSettings->uploadtypes),
              $matches,
              PREG_OFFSET_CAPTURE)) selected @endif >Pictures(jpeg,jpg,png,gif)</option>
            <option value="doc,docx,odt" @if(preg_match('/doc,docx,odt/', ($clientSettings->uploadtypes), $matches,
              PREG_OFFSET_CAPTURE)) selected @endif>Document(doc,docx,odt)</option>
            <option value="pdf" @if(preg_match('/pdf/', ($clientSettings->uploadtypes), $matches, PREG_OFFSET_CAPTURE))
              selected
              @endif>PDF</option>
            <option value="csv,txt,xlsx,xls" @if(preg_match('/csv,txt,xlsx,xls/', ($clientSettings->uploadtypes), $matches,
              PREG_OFFSET_CAPTURE)) selected @endif>Excel (csv,txt,xlsx,xls)</option>
          </select>
          @if ($errors->has('upload_types')) <p class="help-block has-error">{{ $errors->first('upload_types') }}</p>
          @endif
        </div>
      </div>

      <div class="col-xs-4">
        <div class="form-group @if ($errors->has('title')) has-error @endif">
          {!! Form::label('file_upload_size', 'Select Maximum Upload size for each file')
          !!}{{-- <span style="color: green">*</span> --}}
          <span class="checkbox" style="margin-top: 2px;">
            <label style="padding: 0px;" class="file_upload_size_btn">
              {{ Form::radio('file_upload_size', '1024' , ($clientSettings->file_upload_size=='1024')?true:false ,['class'=>'minimal']) }}
              1 MB
            </label>
            <label>
              {{ Form::radio('file_upload_size', '2048', ($clientSettings->file_upload_size=='2048')?true:false, ['class'=>'minimal']) }}
              2 MB
            </label>
            <label>
              {{ Form::radio('file_upload_size', '5120', ($clientSettings->file_upload_size=='2048')?true:false, ['class'=>'minimal']) }}
              5 MB
            </label>
          </span>
        </div>
      </div>
      <div class="col-xs-4">
      <div class="form-group @if ($errors->has('title')) has-error @endif">
        {!! Form::label('total_collaterals_size', 'Specfy total allowed upload size for collaterals')
        !!}<span style="color: red"> (GB)</span>
        {!! Form::text('total_collaterals_size_gb', $clientSettings->total_collaterals_size_gb?$clientSettings->total_collaterals_size_gb:1, ['class' => 'form-control', 'placeholder' => '1 or 1.5 or 5 ...', 'required' => true]) !!}
      </div>
      </div>
    </div>
  </div>
  <!-- Colaterals setup ends -->

  <!-- Parties Files/Images setup starts -->
  <div class="panel panel-success">
    <div class="panel-heading custom-panel-heading">
      <span class="panel-heading-text">Party Files/Images Setup</span></div>
    <div class="panel-body">
      <!-- <div class="row">
                <div class="col-xs-6">
                    <div class="form-group @if ($errors->has('party_files_images')) has-error @endif">
                        {!! Form::label('party_files_images', 'On/Off') !!}
                        <span class="checkbox" style="margin-top: 2px;">
                        <label style="padding: 0px;">
                          {{ Form::radio('party_files_images', '0' , ($clientSettings->party_files_images==0)?true:false, ['class'=>'minimal']) }}
                          No
                        </label>
                        <label>
                          {{ Form::radio('party_files_images', '1' , ($clientSettings->party_files_images==1)?true:false, ['class'=>'minimal']) }}
                          Yes
                        </label>
                      </span>
                    </div>
                </div>
            </div> -->
      <div class="row">
        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('party_file_upload_types')) has-error @endif">
            {!! Form::label('party_file_upload_types', 'File Upload Types') !!}
            <select name="party_file_upload_types[]" id="party_file_upload_types" class="party_file_upload_types"
              multiple="true">
              <option value="doc,docx,odt,txt,jpeg,jpg,png,gif,svg" @if(preg_match('/doc,docx,odt,txt,jpeg,jpg,png,gif,svg/', ($clientSettings->
                party_file_upload_types), $matches,
                PREG_OFFSET_CAPTURE)) selected @endif>Word Document / Images(doc,docx,odt,txt,jpeg,jpg,png,gif,svg)</option>
              <option value="pdf" @if(preg_match('/pdf/', ($clientSettings->party_file_upload_types), $matches,
                PREG_OFFSET_CAPTURE)) selected
                @endif>PDF Documents</option>
              <option value="csv,txt,xlsx,xls" @if(preg_match('/csv,txt,xlsx,xls/', ($clientSettings->party_file_upload_types),
                $matches,
                PREG_OFFSET_CAPTURE)) selected @endif>Excel (csv,txt,xlsx,xls)</option>
            </select>
            @if ($errors->has('party_file_upload_types')) <p class="help-block has-error">
              {{ $errors->first('party_file_upload_types') }}</p>
            @endif
          </div>
        </div>
        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('file_upload_size')) has-error @endif">
            {!! Form::label('party_file_upload_size', 'File Upload Size') !!}
            {{-- <input type="text" name="party_file_upload_size" id="party_file_upload_size" class="form-control" placeholder="File Upload Size" value="{{isset($clientSettings->party_file_upload_size)? $clientSettings->party_file_upload_size:null}}">
            --}}

            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;" class="party_file_upload_size_btn">
                {{ Form::radio('party_file_upload_size', '1024' , ($clientSettings->party_file_upload_size=='1024')?true:false ,['class'=>'minimal']) }}
                1 MB
              </label>
              <label>
                {{ Form::radio('party_file_upload_size', '2048', ($clientSettings->party_file_upload_size=='2048')?true:false, ['class'=>'minimal']) }}
                2 MB
              </label>
              <label>
                {{ Form::radio('party_file_upload_size', '5120', ($clientSettings->party_file_upload_size=='2048')?true:false, ['class'=>'minimal']) }}
                5 MB
              </label>
            </span>

            @if ($errors->has('file_upload_size')) <p class="help-block has-error">
              {{ $errors->first('file_upload_size') }}</p>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('party_image_upload_types')) has-error @endif">
            {!! Form::label('party_image_upload_types', 'Image Upload Types') !!}
            <select name="party_image_upload_types[]" id="party_image_upload_types" class="party_image_upload_types"
              multiple="true">
              <option value="jpeg,jpg,png" @if(preg_match('/jpeg,jpg,png/', ($clientSettings->party_image_upload_types),
                $matches,
                PREG_OFFSET_CAPTURE)) selected @endif >JPEG/JPG/PNG Formats</option>
              <option value="svg" @if(preg_match('/svg/', ($clientSettings->party_image_upload_types), $matches,
                PREG_OFFSET_CAPTURE)) selected @endif >SVG Formats</option>
              <option value="gif" @if(preg_match('/gif/', ($clientSettings->party_image_upload_types), $matches,
                PREG_OFFSET_CAPTURE)) selected @endif >GIF Formats</option>
            </select>
            @if ($errors->has('party_image_upload_types')) <p class="help-block has-error">
              {{ $errors->first('party_image_upload_types') }}</p>
            @endif
          </div>
        </div>
        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('party_image_upload_size')) has-error @endif">
            {!! Form::label('party_image_upload_size', 'Image Upload Size') !!}
            {{-- <input type="text" name="party_image_upload_size" id="party_image_upload_size" class="form-control" placeholder="File Upload Size" value="{{isset($clientSettings->party_image_upload_size)? $clientSettings->party_image_upload_size:null}}"
            readonly> --}}

            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;" class="party_file_upload_size_btn">
                {{ Form::radio('party_image_upload_size', '1024' , ($clientSettings->party_image_upload_size=='1024')?true:false ,['class'=>'minimal']) }}
                1 MB
              </label>
              <label>
                {{ Form::radio('party_image_upload_size', '2048', ($clientSettings->party_image_upload_size=='2048')?true:false, ['class'=>'minimal']) }}
                2 MB
              </label>
              <label>
                {{ Form::radio('party_image_upload_size', '5120', ($clientSettings->party_image_upload_size=='2048')?true:false, ['class'=>'minimal']) }}
                5 MB
              </label>
            </span>

            @if ($errors->has('party_image_upload_size')) <p class="help-block has-error">
              {{ $errors->first('image_upload_size') }}</p>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
        <div class="form-group @if ($errors->has('total_file_size_gb')) has-error @endif">
          {!! Form::label('total_file_size_gb', 'Specfy total allowed upload size for files')
          !!}<span style="color: red"> (GB)</span>
          {!! Form::number('total_file_size_gb', $clientSettings->total_file_size_gb?$clientSettings->total_file_size_gb:1, ['class' => 'form-control', 'placeholder' => '1 or 1.5 or 5 ...', 'required' => true, 'min' => $min_party_file_size, 'step'=> '0.01']) !!}
          <input type="hidden" name="allowed_min_file_size_gb" value="{{$min_party_file_size}}">
          @if ($errors->has('total_file_size_gb')) 
            <p class="help-block has-error">
            {{ $errors->first('total_file_size_gb') }}
            </p>
          @endif
        </div>
      </div>

      <div class="col-xs-6">
        <div class="form-group @if ($errors->has('total_image_size_gb')) has-error @endif">
          {!! Form::label('total_image_size_gb', 'Specfy total allowed upload size for images')
          !!}<span style="color: red"> (GB)</span>
          {!! Form::number('total_image_size_gb', $clientSettings->total_image_size_gb?$clientSettings->total_image_size_gb:1, ['class' => 'form-control', 'placeholder' => '1 or 1.5 or 5 ...', 'required' => true, 'min' => $min_party_image_size, 'step'=> '0.01']) !!}
          <input type="hidden" name="allowed_min_image_size_gb" value="{{$min_party_image_size}}">
          @if ($errors->has('total_image_size_gb')) 
            <p class="help-block has-error">
            {{ $errors->first('total_image_size_gb') }}
            </p>
          @endif
        </div>
      </div>
      </div>
    </div>
  </div>
  <!-- Colaterals setup ends -->

  <!-- Orders setup starts -->
  <div class="panel panel-success">
    <div class="panel-heading custom-panel-heading">
      <span class="panel-heading-text">Orders Setup</span>
    </div>
    <div class="panel-body">
      <div class="row">

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('order_prefix')) has-error @endif">
            {!! Form::label('order_prefix', 'Order Prefix') !!}
            {!! Form::text('order_prefix', null, ['class' => 'form-control', 'placeholder' => 'Order Prefix']) !!}
            @if ($errors->has('order_prefix')) <p class="help-block has-error">{{ $errors->first('order_prefix') }}</p>
            @endif
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('var_colors')) has-error @endif">
            {!! Form::label('var_colors', 'Variant Attributes') !!}{{-- <span style="color: green">*</span> --}}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('var_colors', 0, ($clientSettings->var_colors==0)?true:false, ['class'=>'minimal']) }} No
              </label>
              <label>
                {{ Form::radio('var_colors', 1 , ($clientSettings->var_colors==1)?true:false, ['class'=>'minimal']) }}
                Yes
              </label>
            </span>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('unit_conversion')) has-error @endif">
            {!! Form::label('unit_conversion', 'Unit Conversion') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('unit_conversion', '0' , ($clientSettings->unit_conversion==0)?true:false, ['class'=>'minimal', 'id'=>'unit_conversion_0']) }}
                No
              </label>
              <label>
                {{ Form::radio('unit_conversion', '1' , ($clientSettings->unit_conversion==1)?true:false, ['class'=>'minimal', 'id'=>'unit_conversion_1']) }}
                Yes
              </label>
            </span>
          </div>
        </div>

{{--        <div class="row">--}}
{{--          <div class="col-xs-6">--}}
{{--            <div class="form-group @if ($errors->has('schemes')) has-error @endif">--}}
{{--              {!! Form::label('schemes', 'Schemes') !!}--}}
{{--              <span class="checkbox" style="margin-top: 2px;">--}}
{{--              <label style="padding: 0px;">--}}
{{--                {{ Form::radio('schemes', '0' , ($clientSettings->schemes==0)?true:false, ['class'=>'minimal', 'id'=>'schemes_0']) }}--}}
{{--                No--}}
{{--              </label>--}}
{{--              <label>--}}
{{--                {{ Form::radio('schemes', '1' , ($clientSettings->schemes==1)?true:false, ['class'=>'minimal', 'id'=>'']) }}--}}
{{--                Yes--}}
{{--              </label>--}}
{{--            </span>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--        </div>--}}

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('title')) has-error @endif">
            {!! Form::label('order_with_authsign', 'Signature in Order Print') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('order_with_authsign', '0' , ($clientSettings->order_with_authsign==0)?true:false, ['class'=>'minimal']) }}
                No
              </label>
              <label>
                {{ Form::radio('order_with_authsign', '1' , ($clientSettings->order_with_authsign==1)?true:false, ['class'=>'minimal']) }}
                Yes
              </label>
            </span>
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('title')) has-error @endif">
            {!! Form::label('order_approval', 'Include Dispatch Details') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('order_approval', '0' , ($clientSettings->order_approval==0)?true:false, ['class'=>'minimal']) }}
                No
              </label>
              <label>
                {{ Form::radio('order_approval', '1' , ($clientSettings->order_approval==1)?true:false, ['class'=>'minimal']) }}
                Yes
              </label>
              <input type="text" name="brand" value="1" hidden />
            </span>
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('title')) has-error @endif">
            {!! Form::label('order_with_amt', 'Order With') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('order_with_amt', '0' , ($clientSettings->order_with_amt==0)?true:false, ['class'=>'minimal', 'id'=>'order_with_amt_0']) }}
                Amount & Quantity
              </label>
              <label>
                {{ Form::radio('order_with_amt', '1' , ($clientSettings->order_with_amt==1)?true:false, ['class'=>'minimal', 'id'=>'order_with_amt_1']) }}
                Only Quantity
              </label>
            </span>
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('ncalendar')) has-error @endif">
            {!! Form::label('non_zero_discount', 'Make it compulsory to have non-zero discount in orders?') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('non_zero_discount', '0' , ($clientSettings->non_zero_discount==0)?true:false, ['class'=>'minimal']) }}
                No
              </label>
              <label>
                {{ Form::radio('non_zero_discount', '1' , ($clientSettings->non_zero_discount==1)?true:false, ['class'=>'minimal']) }}
                Yes
              </label>
            </span>
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('product_level_discount')) has-error @endif">
            {!! Form::label('product_level_discount', 'Discount At Product Level') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('product_level_discount', '0' , ($clientSettings->product_level_discount==0)?true:false, ['class'=>'minimal', 'id'=>'product_level_discount_0']) }}
                No
              </label>
              <label>
                {{ Form::radio('product_level_discount', '1' , ($clientSettings->product_level_discount==1)?true:false, ['class'=>'minimal', 'id'=>'product_level_discount_1']) }}
                Yes
              </label>
            </span>
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('product_level_tax')) has-error @endif">
            {!! Form::label('product_level_tax', 'Would the tax be product-wise or on overall amount?') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('product_level_tax', '0' , ($clientSettings->product_level_tax==0)?true:false, ['class'=>'minimal', 'id'=>'product_level_tax_0']) }}
                Overall
              </label>
              <label>
                {{ Form::radio('product_level_tax', '1' , ($clientSettings->product_level_tax==1)?true:false, ['class'=>'minimal', 'id'=>'product_level_tax_1']) }}
                Product-wise
              </label>
            </span>
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('party_wise_rate_setup')) has-error @endif">
            {!! Form::label('party_wise_rate_setup', 'Custom rate Setup') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('party_wise_rate_setup', '0' , ($clientSettings->party_wise_rate_setup==0)?true:false, ['class'=>'minimal', 'id'=>'party_wise_rate_setup_0']) }}
                No
              </label>
              <label>
                {{ Form::radio('party_wise_rate_setup', '1' , ($clientSettings->party_wise_rate_setup==1)?true:false, ['class'=>'minimal', 'id'=>'party_wise_rate_setup_1']) }}
                Yes
              </label>
            </span>
          </div>
        </div>

        <div class="col-xs-6">
          <div class="form-group @if ($errors->has('category_wise_rate_setup')) has-error @endif">
            {!! Form::label('category_wise_rate_setup', 'Category-wise Rate Setup') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('category_wise_rate_setup', '0' , ($clientSettings->category_wise_rate_setup==0)?true:false, ['class'=>'minimal', 'id'=>'category_wise_rate_setup_0']) }}
                No
              </label>
              <label>
                {{ Form::radio('category_wise_rate_setup', '1' , ($clientSettings->category_wise_rate_setup==1)?true:false, ['class'=>'minimal', 'id'=>'category_wise_rate_setup_1']) }}
                Yes
              </label>
            </span>
          </div>
        </div>

        <!-- <div class="col-xs-6">
          <div class="form-group @if ($errors->has('tally_integration')) has-error @endif">
            {!! Form::label('tally_integration', 'Tally Integration') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('tally_integration', '0' , ($clientSettings->tally==0)?true:false, ['class'=>'minimal', 'id'=>'tally_0']) }}
                No
              </label>
              <label>
                {{ Form::radio('tally_integration', '1' , ($clientSettings->tally==1)?true:false, ['class'=>'minimal', 'id'=>'tally_1']) }}
                Yes
              </label>
            </span>
          </div>
        </div> -->

      </div>

      <!-- <div class="row">
        <div class="col-xs-8">
          <div class="form-group @if ($errors->has('order_to')) has-error @endif">
            {!! Form::label('order_to', 'Order To') !!}
            <span class="checkbox" style="margin-top: 2px;">
              <label style="padding: 0px;">
                {{ Form::radio('order_to', '0' , ($clientSettings->order_to==0)?true:false, ['class'=>'minimal']) }} Only Superior
              </label>
              <label>
                {{ Form::radio('order_to', '1' , ($clientSettings->order_to==1)?true:false, ['class'=>'minimal']) }} Superior and superior party types
              </label>
            </span>
          </div>
        </div>
      </div> -->

      <div class="row">
        <div class="col-xs-6">
          <label>Types of Taxes Implied on Orders</label>
          <div class="table-responsive">
            <table class="table table-bordered" id="dynamic_field">
              <tbody>
                <thead style="background-color: #3c763d;color:#fff;">
                  <tr>
                    <th>Tax Name</th>
                    <th>Percentage</th>
                    <th>Set as Default</th>
                    <th></th>
                  </tr>
                </thead>
              </tbody>
            </table>
            <button type="button" name="add" id="add" class="btn btn-success btn-xs">Add</button>
          </div>
        </div>
        <div class="col-xs-6">
          <label>Currently Implied Taxes</label>
          <div class="table-responsive" id="showTaxes">
            <div class="table-responsive" id="showTaxes">
              <table class="table table-bordered">
                <thead style="background-color: #3c763d;color:#fff;">
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
                      {{-- @if($tax->products->count()<1) --}}
                      <a id="updateTax_{{$tax->id}}" class="btn btn-warning btn-xs update-tax-btn"
                        data-id="{{$tax->id}}" data-name="{{$tax->name}}" data-percent="{{$tax->percent}}">
                        <i class="fa fa-edit"></i>
                      </a>
                      <a id="removeTax_{{$tax->id}}" onclick="removeTaxAlert({{$tax->id}})"
                        class="btn btn-danger btn-xs">X</a>
                      {{-- @endif --}}
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <!-- orders setup ends -->
</div>
{!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
{!! Form::close() !!}

<div class="modal modal-default fade" id="updateTax" tabindex="-1" role="dialog" aria-labelledby="updateTax"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center" id="updateTax">Update Tax</h4>
      </div>
      <form method="post" class="update-modal"
        action="{{domain_route('app.company.updateTax', [$clientSettings->id])}}">
        {{csrf_field()}}
        <div class="modal-body">
          <input type="hidden" name="updateId" id="updateId" value="">
          <div class="form-group">
            <label for="">Tax Name</label>
            <input class="form-control ed_tax_name" placeholder="Tax Name" id="ed_tax_name" name="tax_name" type="text"
              required>
          </div>
          <div class="form-group">
            <label for="">Tax Percent</label>
            <input class="form-control ed_tax_percent" placeholder="Tax Percent" id="ed_tax_percent" name="tax_percent"
              type="text" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button>
          <button type="submit" class="btn btn-warning submit-update-btn" id="submit-update-btn">Confirm</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="deleteTax" tabindex="-1" role="dialog" aria-labelledby="deleteTax"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Are you sure you want to delete this?
        </p>
        <input type="hidden" name="tax_id" id="deltax_id" value="">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success cancel" data-dismiss="modal" id="cancelDelTax">No, Cancel</button>
        <button type="button" class="btn btn-warning delete-button" id="delTax">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>
