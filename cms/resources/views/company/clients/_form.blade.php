<div class="row">
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Basic Info</div>
      <div class="panel-body">

        <div class="form-group @if ($errors->has('company_name')) has-error @endif">
          {!! Form::label('company_name', 'Party Name') !!}<span style="color: red">*</span>
          {!! Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => 'Party Name','required']) !!}
          {{-- @if ($errors->has('company_name'))  --}}
            <p class="help-block has-error">{{ $errors->first('company_name') }}</p>
          {{-- @endif --}}
        </div>

        <div class="form-group @if ($errors->has('client_code')) has-error @endif">
          {!! Form::label('client_code', 'Party Code') !!}
          {!! Form::text('client_code', null, ['class' => 'form-control', 'placeholder' => 'A unique Code']) !!}
          @if ($errors->has('client_code')) <p class="help-block has-error">{{ $errors->first('client_code') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('name')) has-error @endif">
          {!! Form::label('name', 'Contact Person Name') !!}<span style="color: red">*</span>
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Contact Person Name','required']) !!}
          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
        </div>

        <!-- <div class="form-group @if ($errors->has('image')) has-error @endif">
          <label>Photo</label>
          <small> Size of image should not be more than 2MB.</small>
          <div class="input-group" style="margin-bottom:10px;">
            <span class="input-group-btn">
              <span class="btn btn-default btn-file imagefile">
                Browse… {!! Form::file('image', ['id'=>'imgInp']) !!}
              </span>
            </span>
            <input type="text" class="form-control" readonly>
          </div>

          <img id='img-upload' class="img-responsive" src="@if(isset($client->image_path)) {{ URL::asset('cms/'.$client->image_path) }} @endif" />
        </div> -->

        <div id="imggroup" class="form-group @if ($errors->has('image')) has-error @endif">
          {!! Form::label('about', 'Photo') !!}<span>(Size of image should not be more than 2MB.)</span>
          @if ($errors->has('image')) <p class="help-block has-error">{{ $errors->first('image') }}</p> @endif
          <div class="imgUp">
              <div class="imagePreview2 imageExistsPreview  @if(isset($client->image_path)) clientImageExists @endif" style="text-align: center;"><img class="img-responsive display-imglists" @if(isset($client->image_path)) src="{{ URL::asset('cms'.$client->image_path) }}" @endif /></div><i id="clearImage" class="fa fa-times del-img @if(isset($client->image_path)) @else hide @endif"></i>
              <div class="row">
              <div class="col-xs-6">
                  <label id="lblChange" class="btn btn-primary form-control"> Choose<input id="receipt" type="file" name="image" class="uploadFile img" @if(isset($client->image_path)) value="cms/{{$client->image_path}}" @endif style="width: 0px;height: 0px;overflow: hidden;">
                  </label>
              </div>
              <div class="col-xs-6">
                <input type="text" id="confirmremove" name="confirmremove" hidden>
                <!-- <button id="clearImage" class="btn btn-danger form-control pull-right" type="button">Remove</button> -->
              </div>
              </div>
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('about', 'About Company') !!}
          {!! Form::textarea('about', null, ['class' => 'form-control ckeditor', 'id'=>'about', 'placeholder' => 'Something
          about company...']) !!}
        </div>

      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Business Details</div>
      <div class="panel-body">
        @if(count($partytypes)>0 && $hasPermission==true)
          <div class="form-group @if ($errors->has('client_type')) has-error @endif">
            {!! Form::label('client_type', 'Party Type') !!}<span style="color: red">*</span>
            @if(isset($client->id) && hasChild($client->id))
            <input type="text" name="client_type" value="{{$client->client_type}}" hidden>
            <select name="client-type" class="form-control select2" id="party_type" disabled="disabled">
              <option value=""></option>
              @foreach($partytypes as $partytype)
              <?php
                $stringName = str_replace(' ','-',$partytype->name).'-'.'create';
                $permission = \Spatie\Permission\Models\Permission::where('company_id',config('settings.company_id'))->where('name',$stringName)->first();
                $hasPartyPermission = false;
                if($permission){
                  $hasPartyPermission = $partytype->id==$client->client_type?true:Auth::user()->hasPermissionTo($permission->id);
                }else{
                  $hasPartyPermission = $partytype->id==$client->client_type?true:false;
                }
              ?>
              @if($hasPartyPermission)
              <option @if($partytype->id==$client->client_type) selected="selected" @endif
                value="{{$partytype->id}}">{{$partytype->name}}</option>
              @endif
              @if(count($partytype->childs)>0)
              @include('company.clients.partials_show.partyChilds',['childs' => $partytype->childs])
              @endif
              @endforeach
            </select>
            @else
            <select name="client_type" class="form-control select2 bbbb" id="party_type" required>
              <option value=""></option>
              @foreach($partytypes as $partytype)
              <?php
                $stringName = str_replace(' ','-',$partytype->name).'-'.'create';
                $permission = \Spatie\Permission\Models\Permission::where('company_id',config('settings.company_id'))->where('name',$stringName)->first();
                $hasPartyPermission = false;
                if(isset($client->id)){
                  if($permission){
                    $hasPartyPermission = $partytype->id==$client->client_type?true:Auth::user()->hasPermissionTo($permission->id);
                  }else{
                    $hasPartyPermission = $partytype->id==$client->client_type?true:false;
                  }
                }else{
                  $hasPartyPermission = $permission?Auth::user()->hasPermissionTo($permission->id):false;
                }
              ?>
              @if($hasPartyPermission)
                @if(isset($client->id))
                  <option @if($partytype->id==$client->client_type) selected="selected" @endif                 value="{{$partytype->id}}">{{$partytype->name}}</option>
                @else
                  <option {{ old('client_type') != $partytype->id ?: 'selected' }} value="{{$partytype->id}}">{{$partytype->name}}</option>
                @endif
              @endif

              @if(count($partytype->childs)>0)
              @include('company.clients.partials_show.partyChilds',['childs' => $partytype->childs])
              @endif
              @endforeach
            </select>
            @endif
            @if ($errors->has('client_type')) <p class="help-block has-error">{{ $errors->first('client_type') }}</p> @endif
          </div>

          <div class="form-group @if ($errors->has('superior')) has-error @endif">
            {!! Form::label('Superior', 'Superior') !!}
            <!-- <span style="color: red">*</span> -->
            {!! Form::select('superior', [null => $companyName] + $superiors, isset($client)?$client->superior:null,
            ["class" => "form-control select2", "id" => "superior"]) !!}

            @if ($errors->has('superior')) <p class="help-block has-error">{{ $errors->first('superior') }}</p> @endif
          </div>

        @endif

        <div class="form-group">
          {!! Form::label('Business_Type', 'Business Type') !!}
          @if(isset($client->business_id))
          <select name="business_id" class="select2 form-control" id="business_id">
            <option></option>
            @foreach($businessTypes as $businessType)
            <option @if($client->business_id==$businessType->id || old('business_id')===$businessType->id) selected="selected" @endif
              value="{{$businessType->id}}">{{$businessType->business_name}}</option>
            @endforeach
          </select>
          @else
          <select name="business_id" class="select2 form-control" id="business_id">
            <option></option>
            @foreach($businessTypes as $businessType)
            <option value="{{$businessType->id}}" {{ old('business_id') != $businessType->id ?: 'selected' }}>{{$businessType->business_name}}</option>
            @endforeach
          </select>
          @endif
        </div>

        <div class="form-group">
          {!! Form::label('pan', 'PAN/VAT/GST no.') !!}
          {!! Form::text('pan', null, ['class' => 'form-control', 'placeholder' => 'PAN/VAT/GST no.']) !!}
        </div>

      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Contact Details</div>
      <div class="panel-body">

        <div class="form-group @if ($errors->has('website')) has-error @endif">
          {!! Form::label('website', 'Website') !!}
          {!! Form::text('website', null, ['class' => 'form-control', 'placeholder' => 'Website']) !!}
          @if ($errors->has('website')) <p class="help-block has-error">{{ $errors->first('website') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('email')) has-error @endif">
          {!! Form::label('email', 'Email') !!}
          {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Mailing Address']) !!}
          @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('phonecode')) has-error @endif">
          {!! Form::label('phonecode', 'Phone Code') !!}
          {!! Form::text('phonecode', null, ['class' => 'form-control', 'readonly', 'id'=>'phonecode']) !!}
          @if ($errors->has('phonecode')) <p class="help-block has-error">{{ $errors->first('phonecode') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('phone')) has-error @endif">
          {!! Form::label('phone', 'Phone') !!}<span>(Use comma(,) as multiple phone separator.)</span>
          {!! Form::text('phone', null, ['class' => 'form-control', 'id' => 'multiphone', 'placeholder' => 'Phone No.']) !!}
          @if ($errors->has('phone')) <p class="help-block has-error">{{ $errors->first('phone') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('mobile')) has-error @endif">
          {!! Form::label('mobile', 'Mobile') !!}
          {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => 'Mobile No.']) !!}
{{--          @if ($errors->has('mobile')) --}}
                <p class="help-block has-error">{{ $errors->first('mobile') }}</p>
{{--            @endif--}}
        </div>

      </div>
    </div>
  </div>
</div>

<div class="row">

  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Location Details</div>
      <div class="panel-body">

        <div class="form-group @if ($errors->has('country')) has-error @endif">
          {!! Form::label('country', 'Country') !!}<span style="color: red">*</span>
          {!! Form::select('country', [null => 'Select a Country'] + $countries, isset($client)?
          ($client->country):((config('settings.country'))?config('settings.country'):null), ["class" => "form-control
          select2", "id" => "country",'required']) !!}
          @if ($errors->has('country')) <p class="help-block has-error">{{ $errors->first('country') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('state')) has-error @endif">
          {!! Form::label('state', 'State') !!}
          <!-- <span style="color: red">*</span> -->
          {!! Form::select('state', [null => 'Select a State']+$states, isset($client)? $client->state:old('state'), ["class" =>
          "form-control select2", "id" => "state"]) !!}
          @if ($errors->has('state')) <p class="help-block has-error">{{ $errors->first('state') }}</p> @endif
        </div>


        <div class="form-group @if ($errors->has('city')) has-error @endif">
          {!! Form::label('city', 'City') !!}
          <!-- <span style="color: red">*</span> -->
          {!! Form::select('city', [null => 'Select a City']+$cities, isset($client)?$client->city:old('city'), ["class" =>
          "form-control select2", "id" => "city"]) !!}
          @if ($errors->has('city')) <p class="help-block has-error">{{ $errors->first('city') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('address_1')) has-error @endif">
          {!! Form::label('address_1', 'Address Line 1') !!}
          {!! Form::text('address_1', null, ['class' => 'form-control', 'placeholder' => 'Address Line 1']) !!}
          @if ($errors->has('address_1')) <p class="help-block has-error">{{ $errors->first('address_1') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('address_2')) has-error @endif">
          {!! Form::label('address_2', 'Address Line 2') !!}
          {!! Form::text('address_2', null, ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
          @if ($errors->has('address_2')) <p class="help-block has-error">{{ $errors->first('address_2') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('pin')) has-error @endif">
          {!! Form::label('pin', 'Pin Code') !!}
          {!! Form::text('pin', null, ['class' => 'form-control', 'placeholder' => 'Zip']) !!}
          @if ($errors->has('pin')) <p class="help-block has-error">{{ $errors->first('pin') }}</p> @endif
        </div>

        <div class="form-group">
          {!! Form::label('beat' ,'Beat') !!}
          @if(isset($currentBeatID))
          <select name="beat" id="beat" class="select2 form-control">
            <option value="0">Select Beat</option>
            @foreach($beats as $beat)
            <option @if($beat->id == $currentBeatID) selected="selected" @endif value="{{$beat->id}}">{{$beat->name}}
            </option>
            @endforeach
          </select>
          @else
          <select name="beat" id="beat" class="select2 form-control">
            <option>Select Beat</option>
            @foreach($beats as $beat)
            <option value="{{$beat->id}}" {{ old('beat') != $beat->id ?: 'selected' }} >{{$beat->name}}</option>
            @endforeach
          </select>
          @endif
        </div>

        <div class="form-group">
          {!! Form::label('location' ,'Location') !!}<span style="color: red">*</span>
          @if(isset($client->location))
          {!! Form::text('location' , $client->location, ['class' => 'form-control', 'placeholder' => 'Enter Location',
          'id'=>"search_addr",'required']) !!}
          {!! Form::hidden('lat' , $client->latitude, ['class' => 'form-control', 'name'=>'lat', 'id'=>'search_latitude'])
          !!}
          {!! Form::hidden('lng' , $client->longitude, ['class' => 'form-control', 'name'=>'lng','id'=>'search_longitude'])
          !!}
          @else
          {!! Form::text('location' , null, ['class' => 'form-control', 'placeholder' => 'Enter Location',
          'id'=>"search_addr",'required']) !!}
          {!! Form::hidden('lat' , null, ['class' => 'form-control', 'name'=>'lat', 'id'=>'search_latitude']) !!}
          {!! Form::hidden('lng' , null, ['class' => 'form-control', 'name'=>'lng','id'=>'search_longitude']) !!}
          @endif
          @if ($errors->has('location')) <p class="help-block has-error">{{ $errors->first('location') }}</p> @endif
          @if ($errors->has('lat')) <p class="help-block has-error">{{ $errors->first('lat') }}</p> @endif
          @if ($errors->has('lng')) <p class="help-block has-error">{{ $errors->first('lng') }}</p> @endif
        </div>
        <div class="form-group" id="geomap" style="width: 100%; height: 400px;"></div>

      </div>
    </div>
  </div>
  @if(config('settings.accounting')==1 && Auth::user()->can('Accounting-view'))
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Accounting Info</div>
      <div class="panel-body">

        <div class="form-group @if ($errors->has('opening_balance')) has-error @endif">
          {!! Form::label('opening_balance', 'Opening Balance') !!}
          {!! Form::text('opening_balance', null, ['class' => 'form-control', 'placeholder' => 'Opening Balance']) !!}
          @if ($errors->has('opening_balance')) <p class="help-block has-error">{{ $errors->first('opening_balance') }}</p>
          @endif
        </div>

        <div class="form-group @if ($errors->has('credit_limit')) has-error @endif">
          {!! Form::label('credit_limit', 'Credit Limit') !!}
          {!! Form::text('credit_limit', null, ['class' => 'form-control', 'placeholder' => 'Credit Limit']) !!}
          @if ($errors->has('credit_limit')) <p class="help-block has-error">{{ $errors->first('credit_limit') }}</p>
          @endif
        </div>

        @if(Auth::user()->can('ageing-view') && config('settings.ageing')==1)
        <div class="form-group @if ($errors->has('credit_days')) has-error @endif">
          {!! Form::label('credit_days', 'Credit Days') !!}
          {!! Form::text('credit_days', (isset($client))?$client->credit_days:config('settings.credit_days'), ['class' => 'form-control', 'placeholder' => 'Credit days']) !!}
          @if ($errors->has('credit_days')) <p class="help-block has-error">{{ $errors->first('credit_days') }}</p>
          @endif
        </div>
        @endif

      </div>
    </div>
  </div>
  @endif

  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Miscellaneous</div>
        <div class="panel-body">

          <div class="form-group">
            {!! Form::label('created_by', 'Party added by') !!}
            @if(isset($client->created_by))
            <select name="created_by" id="created_by" class="form-control select2" disabled>
              <option value="0" {{($client->created_by == 0)? 'selected':''}}>{{Auth::user()->name.' (Admin)'}}</option>
              @foreach($employees as $employee)
              <option value="{{$employee->id}}" {{($client->created_by == $employee->id)? 'selected':''}}>{{$employee->name}}
              </option>
              @endforeach
            </select>
            {{-- {!! Form::select('created_by', array('Active' => 'Active', 'Inactive' => 'Inactive'), $company->status, ['class' => 'form-control']) !!}  --}}
            @else
            <select name="created_by" id="created_by" class="form-control select2" disabled>
              @if(Auth::user()->isCompanyManager())
              <option value="0">{{Auth::user()->name.' (Admin)'}}</option>
              @else
              <option></option>
              @endif
              @foreach($employees as $employee)
              <option value="{{$employee->id}}" @if(Auth::user()->EmployeeId()==$employee->id) selected
                @endif>{{$employee->name}}</option>
              @endforeach
            </select>
            {{-- {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), 'Active', ['class' => 'form-control']) !!} --}}
            @endif
          </div>

          <div class="form-group">
            {!! Form::label('status', 'Status') !!}
            @if(isset($client->status))
            {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $client->status, ['class' =>
            'form-control']) !!}
            @else
            {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), 'Active', ['class' =>
            'form-control']) !!}
            @endif
          </div>
          @if(config('settings.party_wise_rate_setup')==1)
          <div class="form-group">
            {!! Form::label('rate', 'Order Rates') !!}
            @if(isset($client->rate_id))
            {!! Form::select('rate', array(null=>'Default')+$rates, $client->rate_id, ['class' =>'form-control select2']) !!}
            @else
            {!! Form::select('rate', array(null=>'Default')+$rates, null, ['class' => 'form-control select2']) !!}
            @endif
          </div>
          @endif
          @if(config('settings.category_wise_rate_setup') == 1)
          <div c lass="form-group">
            {!! Form::label('rate', 'Category Rates') !!}
            <select name="category_rates[]" class="form-control" id="categoryRates" multiple>
              
                @foreach($category_with_rates as $category_with_rate)
                  <optgroup label="{{$category_with_rate['name']}}">
                    @foreach($category_with_rate['categoryrates'] as $rate)
                      <option value="{{$rate['id']}}" data-categoryid="{{$category_with_rate['id']}}">
                      {{$rate['name']}}
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              
              
            </select>
          </div>
          @endif
        </div>
    </div>
  </div>

@if(!($custom_fields->isEmpty()))
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Custom Fields</div>
        <div class="panel-body">

          <!-- begin custom field forms code  -->

          @foreach ($custom_fields->where('visible',true) as $field)
              <div class="form-group">
                  <div class="row">
                    <div class="col-xs-12">
                      <label>{{$field->title}}</label>
                    </div>
                  </div>
                  @switch($field->type)
                      @case("Text")
                          <input type="text" class="form-control" name="{{$field->slug}}" maxlength="255" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          @break
                      @case("Numerical")
                          <input type="number" step=".01" class="form-control" name="{{$field->slug}}"  placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          @break
                      @case("Large text")
                          <textarea type="text" class="form-control" name="{{$field->slug}}" placeholder="Enter {{$field->title}}">@if(isset($field->custom_value)) {{$field->custom_value}} @endif</textarea>
                          @break
                      @case("Monetary")
                          @php(
                              $currencies= Cache::rememberforever('currencies', function()
                              {
                                  return \App\Currency::orderBy('currency', 'ASC')->get()->unique('code');
                              })
                          )
                          <?php
                              if(isset($field->custom_value)){

                               $arrayMonetory = explode(" ",$field->custom_value);
                              }
                          ?>
                          <div class="row">
                              <div class="col-xs-2" style="padding-right:0;">
                                  <select name="{{$field->slug}}2" id="{{$field->slug}}2">
                                      @foreach ($currencies as $currency)
                                          <option value="{{$currency->id}}" @if(isset($field->custom_value) && ($arrayMonetory[0]==$currency->id)) selected="selected" @endif>{{$currency->code}}</option>
                                      @endforeach
                                  </select>
                              </div>
                              <div class="col-xs-10"  style="padding-left:0;">
                                  <input type="number" step=".01" class="form-control" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$arrayMonetory[1]}}" @endif>
                              </div>
                          </div>
                          @break
                      @case("User")
                          <select type="text" class="form-control select2" id="{{$field->slug}}" name="{{$field->slug}}">
                            <option value="">Please Select</option>
                              @if(session('users'))
                                 @php(
                                      $users= session('users')
                                  )
                              @else
                                  @php(
                                      $users= \App\Employee::where('status','Active')->where('company_id',config('settings.company_id'))->orderBy('name', 'ASC')->get(['id', 'name'])
                                  )
                              @endif
                              @foreach ($users as $user)
                              <option value="{{$user->id}}" @if(isset($field->custom_value) && $field->custom_value==$user->id) selected="selected" @endif>{{$user->name}}</option>
                              @endforeach
                          </select>
                          @break
                      @case("Person")
                          <select type="text" class="" id="{{$field->slug}}" name="{{$field->slug}}">
                              @if(session('contacts'))
                                  @php(
                                      $contacts= session('contacts')
                                  )
                              @else
                                  @php(
                                      $contacts= \App\Customer\Contact::all()
                                  )
                              @endif
                              @foreach ($contacts as $item)
                              <option value="{{$item->id}}">{{$item->name}}</option>
                              @endforeach
                          </select>
                          @break
                      @case("Phone")
                          <input type="text" class="form-control phone_numbers" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          @break
                      @case("Time")
                          <input type="time" class="form-control" id="{{$field->slug}}" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              $('#'+title).flatpickr({
                                  enableTime: true,
                                  noCalendar: true,
                                  dateFormat: "H:i",
                              });
                          </script>
                          @break
                      @case("Time range")
                          <?php
                          if(isset($field->custom_value))
                            $arrayTimeRange = explode('-',$field->custom_value);
                          ?>
                          <div class="row">
                              <div class="col-xs-5">
                                <input type="time" class="form-control " id="{{$field->slug}}1" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{array_key_exists(0, $arrayTimeRange)?$arrayTimeRange[0]:null}}" @endif>
                             </div> <div class="col-xs-1">_</div>
                              <div class="col-xs-5">
                               <input type="time" class="form-control" id="{{$field->slug}}2" name="{{$field->slug}}2" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{array_key_exists(1, $arrayTimeRange)?trim($arrayTimeRange[1]):null}}" @endif>
                              </div>
                          </div>
                          <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              let pick1 = $('#'+title+'1').flatpickr({
                                  enableTime: true,
                                  noCalendar: true,
                                  dateFormat: "H:i",
                                  // onChange: function(selectedDates, dateStr, instance) {
                                  //   pick2.set('minDate', dateStr)
                                  // },
                              });
                              let pick2 = $('#'+title+'2').flatpickr({
                                  enableTime: true,
                                  noCalendar: true,
                                  dateFormat: "H:i",
                                  // onChange: function(selectedDates, dateStr, instance) {
                                  //   pick1.set('maxDate', dateStr)
                                  // },
                              });
                          </script>

                          @break
                      @case("Date")
                          <input type="date" class="form-control" id="{{$field->slug}}" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              $('#'+title).flatpickr({
                                  altInput: true,
                                  altFormat: "F j, Y",
                                  dateFormat: "Y-m-d",
                                  // defaultDate: new Date(),
                              });
                          </script>
                          @break
                      @case("Date range")
                          <input type="text" class="form-control" id="{{$field->slug}}" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              $('#'+title).flatpickr({
                                  altInput: true,
                                  altFormat: "F j, Y",
                                  dateFormat: "Y-m-d",
                                  // defaultDate: new Date(),
                                  mode: "range"
                              });
                          </script>
                          @break
                      @case("Address")
                          <input type="text"  id="searchMapInput"  class="form-control" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          <!-- <script>
                          var temp= {!! $field !!};
                              title= temp.slug;

                              // function initAutocomplete(title) {
                              //     var input = $('#'+title);
                              //     // debugger;
                              //     var autocomplete = new google.maps.places.Autocomplete(input[0]);
                              // }
                              
                              // initAutocomplete(title);

                          </script> -->


                          @break
                      @case("Single option")
                      <?php
                      $v=str_replace('[','',str_replace(']','',str_replace('"','',str_replace("\/",'/',$field->options))));

                            $cus_value=explode(',',$v);
                      ?>
                          <select type="text" id="{{$field->slug}}" name="{{$field->slug}}" class="select2 form-control multiselect">
                            <option value="">Please Select</option>

                              @foreach ($cus_value as $item)
                               @if($item!='')
                                  <option value="{{$item}}" @if($item==$field->custom_value) selected="selected" @endif>{{$item}}</option>
                                  @endif
                              @endforeach
                          </select>
<!--                           <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              var $select =  $('#'+title).selectize();
                              var control = $select[0].selectize;
                              control.clear();
                          </script> -->
                          @break
                      @case("Multiple options")
                          <?php
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                            }
                           // echo $field->options;
                            $v=str_replace('[','',str_replace(']','',str_replace('"','',str_replace("\/",'/',$field->options))));

                            $cus_value=explode(',',$v);
                           // print_r($cus_value);


                          ?>
                          <select class="select2 multiselect" name="{{$field->slug}}[]"  id="{{$field->slug}}" multiple="true">
                              @foreach ($cus_value as $item)
                                 @if($item!='')
                                  <option value="{{$item}}" @if(isset($field->custom_value) && in_array($item,$arrayMultiple)) selected="selected" @endif >{{$item}}</option>
                                  @endif
                              @endforeach
                          </select>

                          @break
                      @case("Multiple Images")
                          <?php
                            $imageVal = 0;
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              foreach($arrayMultiple as $key => $image){
                                  $imageVal++;
                              }
                            }
                          ?>
                          <div id="imggroup{{$field->id}}" class="form-group @if ($errors->has('expense_photo')) has-error @endif multiimg">
                            <input class="hide" type="text" name="{{$field->slug}}-deleted" id="{{$field->slug}}-deleted">
                           <?php
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              foreach($arrayMultiple as $key => $image){
                                  echo '<div class="col-xs-4 imgUp">
                                          <div class="imagePreview imagePreview" style="background:url(/cms/'.$image[0].');background-color: grey;background-position: center center;background-size: contain;background-repeat: no-repeat;" >
              </div>
              <label class="btn btn-primary upload" data-action="'.$field->slug.'-deleted" data-field="'.$field->slug.'" data-name="'.$key.'"> Upload
                 <input type="file" data-value="'.$imageVal.'" id="'.$field->slug.'-original" name="'.$field->slug.'[]" class="uploadFile img custom_field_files" value="Upload Photo" style="width: 0px;height: 0px;overflow:;">
                <span hidden>
                 
                </span>
              </label>
              <i class="fa fa-times del" data-action="'.$field->slug.'-deleted" data-field="'.$field->slug.'" data-name="'.$key.'" data-id="imggroup'.$field->id.'"></i>
            </div>';
                              }
                              echo ' <i class="fa fa-plus imgAdd" data-name="'.$field->slug.'[]" data-id=imggroup'.$field->id.'></i>';
                            }else{
                          echo '<div class="col-xs-4 imgUp">
                            <div class="imagePreview"></div>
                              <label class="btn btn-primary"> Upload
                                 <input type="file" data-value="'.$imageVal.'" id="'.$field->slug.'-original" name="'.$field->slug.'[]" class="uploadFile img custom_field_files" value="Upload Photo" style="width: 0px;height: 0px;overflow:;">
                                  </label>
                                  <i class="fa fa-times del" data-action="'.$field->slug.'-deleted" data-field="'.$field->slug.'" data-id="imggroup'.$field->id.'"></i>
                                </div><!-- col-2 -->
                                <i class="fa fa-plus imgAdd" data-name="'.$field->slug.'[]" data-id=imggroup'.$field->id.'></i>
                          </div>';
                        }
                          ?>
                          @break

                          @case("File")

                            <?php
                            if(isset($field->custom_value)){

                          echo '<div class="form-group">
            <div class="input-group input-file" name="'.$field->slug.'[]">
              <span class="input-group-btn">
                <button class="btn btn-default btn-choose" type="button">Choose</button>
              </span>
              <input type="text" name="'.$field->slug.'[]" class="form-control custom_field_files" placeholder="Choose a file..." />
                
              <span class="input-group-btn">
                <button class="btn btn-danger btn-reset" type="button">Remove</button>
              </span>
            </div>
          ';
          if(isset($field->custom_value)){
                $arrayMultiple = json_decode($field->custom_value);
                foreach($arrayMultiple as $key => $file){
                    echo '<div class="col-xs-12">';
                    echo '<span><a style="width:100px;" href="'.asset('cms/').$file[0].'" target="_blank">'.$key.'</a></span><br>';
                    echo '</div>';
                }
              }
              echo '</div>';
        }else{
         echo'<div class="form-group">
            <div class="input-group input-file" name="'.$field->slug.'[]">
              <span class="input-group-btn">
                <button class="btn btn-default btn-choose" type="button">Choose</button>
              </span>
              <input  type="text" name="'.$field->slug.'[]" class="form-control fileupload" placeholder="Choose a file..." />
              
              <span class="input-group-btn">
                <button class="btn btn-danger btn-reset" type="button">Remove</button>
              </span>
            </div>
          </div>';
        }
        ?>
                          @break


                      @case("File2")
                          <?php
                            $fileValue = 0;
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              foreach($arrayMultiple as $key => $file){
                                  $fileValue++;
                              }
                            }
                          ?>
                          <input type="file" class="custom_field_files" name="{{$field->slug}}[]" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,text/plain, application/pdf" data-value="{{$fileValue}}">
                                              <?php
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              foreach($arrayMultiple as $key => $file){
                                  echo '<div class="col-xs-12">';
                                  echo '<span><a style="width:100px;" href="'.asset('cms/').$file[0].'">'.$key.'</a><span class="custom_image_remove" style="color:red; cursor: pointer;" data-action="'.$field->slug.'-deleted" data-name="'.$key.'"><i class="fa fa-trash"></i></span></span><br>';
                                  echo '</div>';
                              }
                            }
                          ?>
                          @break

                      @default
                          {{-- <input type="text" class="form-control" name="{{$field->slug}}"> --}}
                          @break
                  @endswitch

              </div>
          @endforeach
          <!-- End of field forms code -->

        </div>
    </div>
  </div>
@endif
</div>