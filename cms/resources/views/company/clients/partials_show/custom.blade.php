<div class="box-body">
  <form id="UpdateCustomFieldDetail">
  <div class="row">
    <div class="col-xs-12">
        @if(Auth::user()->can('party-update'))
          @if(checkpartytypepermission($client->client_type,'update'))
          <a href="{{ domain_route('company.admin.client.edit', [$client->id])}}"><span id="" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span></a>
          <!-- <span id="ActivateCustomFieldEdit" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
          @endif
          <span id="ActivateCustomFieldCancel" class="btn btn-default btn-sm pull-right hide partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
          <span id="ActivateCustomFieldUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails partyactionbtn keySubmit" type="submit"><i class="fa fa-edit"></i>Update</button></span> -->
        @endif
    </div>
  </div> 

  <!-- Custom field code Section Starts -->

  @foreach ($custom_fields->where('visible',true) as $field)


            <div class="col-xs-6">
              <div class="media left-list bottom-border">
                <div class="media-left">
                  <i class="fa fa-user fa fa-circle-o icon-size"></i>
                </div>
                <div class="media-body"><h4 class="media-heading">{{$field->title}}</h4>
                  <div class="text-display" id="{{$field->slug}}">
                      @switch($field->type)
                        @case("Monetary")
                          @php(
                              $currencies= Cache::rememberforever('currencies', function()
                              {
                                  return \App\Currency::orderBy('currency', 'ASC')->get()->unique('code');
                              })
                          )
                          <?php
                              if(isset($field->custom_value)):
                                $arrayMonetory = explode(" ",$field->custom_value);  
                                foreach ($currencies as $currency):
                                  if(isset($field->custom_value) && ($arrayMonetory[0]==$currency->id)): 
                                    echo $currency->code." ".$arrayMonetory[1];
                                  endif;
                                endforeach;
                              else:
                                echo 'N/A';
                              endif;
                          ?>
                          @break
                        @case('Single option')
                       
                        <?php
                        if(isset($field->custom_value)){
                     $cus_value=(array)json_decode($party_meta->cf_value);
                        
                           foreach($cus_value as $key=>$value){
                             if($key==$field->id){
                              $v=str_replace('[','',str_replace(']','',str_replace('"','',$value)));
                               echo $v;
                            }

                           }
                        }
                           else{
                              echo 'N/A'; 
                           }
                      ?>
                        
                          <!-- @if(isset($field->custom_value))
                            @foreach ($cus_value as $item)
                                @if ($item)
                                  @if($item==$field->custom_value){{$item}} @endif  
                                @endif
                            @endforeach
                          @else
                            N/A
                          @endif -->

                          @break

                        @case("User")
                              
                            @php(
                                $users= \App\Employee::where('status','Active')->where('company_id',config('settings.company_id'))->orderBy('name', 'ASC')->get(['id', 'name'])
                            )
                            <?php
                            if(isset($field->custom_value)):
                              foreach ($users as $user):
                                if(isset($field->custom_value) && $field->custom_value==$user->id):
                                  echo $user->name;
                                endif;
                              endforeach;
                            else:
                              echo 'N/A';
                            endif;
                            ?>
                          @break

                        @case("Multiple options")
                          <?php
                          if(isset($field->custom_value)){
                          $cus_value=(array)json_decode($party_meta->cf_value);
                        
                           foreach($cus_value as $key=>$value){
                             if($key==$field->id){
                              $v=str_replace('[','',str_replace(']','',str_replace('"','',$value)));
                               echo $v;
                            }

                           }

                            // if(isset($field->custom_value)){
                            //   $arrayMultiple = json_decode($field->custom_value);
                            //   foreach (json_decode($field->options) as $item):
                            //       if ($item):
                            //         if(isset($field->custom_value) && in_array($item,$arrayMultiple)):   
                            //           echo urldecode($item).', ';
                            //         endif; 
                            //       endif;
                            //   endforeach;
                            }else{
                              echo 'N/A';
                            }
                          ?>
                          @break
                        @case("Multiple Images")
                          <?php
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              foreach($arrayMultiple as $key => $image){
                                  echo '<div class="col-xs-6">';
                                   echo '<img style="width:100px;" src="'.asset('cms/').$image[0].'" class="display-imglists">
                                  <span style="display:block;width:100%;">
                                  
                                  </span><br>';
                                  echo '</div>';
                                  //<a href="'.asset('cms/').$image[0].'">'.$key.'</a>
                              }
                            }else{
                              echo "N/A";
                            }
                          ?>
                          @break
                        @case("File")
                          <?php
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              foreach($arrayMultiple as $key => $file){
                                  echo '<span><a style="width:100px;" href="'.asset('cms/').$file[0].'" target="_blank">'.$key.'</a></span><br>';
                              }
                            }else{
                              echo 'N/A';
                            }
                          ?>
                          @break
                        @case("Time range")
                        {{$field->custom_value}}
                          <!-- {{($field->custom_value)?implode(' - ', explode(' ', $field->custom_value)):'N/A'}} -->
                          @break
                        @case("Large text")
                        {!! nl2br($field->custom_value) !!}
                        @break
                        @default
                          {{($field->custom_value)?$field->custom_value:'N/A'}}
                          @break

                      @endswitch

                  </div>
                  <div class="text-form" hidden>
                     @switch($field->type)
                      @case("Text")
                          <input type="text" class="form-control" name="{{$field->slug}}" maxlength="255" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          @break
                      @case("Numerical")
                          <input type="number" step=".01" class="form-control" name="{{$field->slug}}"  placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          @break
                      @case("Large text")
                          <textarea type="text" class="form-control" name="{{$field->slug}}" maxlength="500" placeholder="Enter {{$field->title}}">@if(isset($field->custom_value)) {{ $field->custom_value }} @endif</textarea>
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
                          <input type="tel" class="form-control phone_numbers" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          @break
                      @case("Time")
                          <input type="time" class="form-control custom_timepicker" id="{{$field->slug}}" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          <!-- <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              $('#'+title).flatpickr({
                                  enableTime: true,
                                  noCalendar: true,
                                  dateFormat: "H:i",
                              });
                          </script> -->
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
                               <input type="time" class="form-control" id="{{$field->slug}}2" name="{{$field->slug}}2" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{array_key_exists(1, $arrayTimeRange)?$arrayTimeRange[1]:null}}" @endif>
                              </div>
                          </div>
<!--                           <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              $('#'+title+'1').flatpickr({
                                  enableTime: true,
                                  noCalendar: true,
                                  dateFormat: "H:i",
                              });
                              $('#'+title+'2').flatpickr({
                                  enableTime: true,
                                  noCalendar: true,
                                  dateFormat: "H:i",
                              });
                          </script> -->

                          {{-- <input class="flatpickr flatpickr-input active" type="text" placeholder="Select Date.." data-id="timePicker" readonly="readonly"> --}}
                          @break
                      @case("Date")
                          <input type="date" class="form-control custom_datepicker" id="{{$field->slug}}" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          <!-- <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              $('#'+title).flatpickr({
                                  altInput: true,
                                  altFormat: "F j, Y",
                                  dateFormat: "Y-m-d",
                                  // defaultDate: new Date(),
                              });
                          </script> -->
                          @break
                      @case("Date range")
                          <input type="text" class="form-control custom_daterangepicker" id="{{$field->slug}}" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif>
                          <!-- <script>
                              var temp= {!! $field !!};
                              title= temp.slug;
                              $('#'+title).flatpickr({
                                  altInput: true,
                                  altFormat: "F j, Y",
                                  dateFormat: "Y-m-d",
                                  // defaultDate: new Date(),
                                  mode: "range"
                              });
                          </script> -->
                          @break
                      @case("Address")
                          <input type="text"  id="{{$field->slug}}"  class="form-control" name="{{$field->slug}}" placeholder="Enter {{$field->title}}" @if(isset($field->custom_value)) value="{{$field->custom_value}}" @endif> 
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
                          <select type="text" id="{{$field->slug}}" name="{{$field->slug}}" class="select2 form-control">
                             <option value="">Please Select</option>  
                              @foreach (json_decode($field->options) as $item)
                                  @if ($item)
                                  <option value="{{$item}}" @if($item==$field->custom_value) selected="selected" @endif>{{urldecode($item)}}</option>  
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
                          ?>
                          <select class="select2 multiselect" name="{{$field->slug}}[]"  id="{{$field->slug}}" multiple="true">
                              @foreach (json_decode($field->options) as $item)
                                  @if ($item)
                                  <option value="{{$item}}" @if(isset($field->custom_value) && in_array($item,$arrayMultiple)) selected="selected" @endif >{{urldecode($item)}}</option>  
                                  @endif
                              @endforeach
                          </select>
                          @break
                      @default
                          {{-- <input type="text" class="form-control" name="{{$field->slug}}"> --}}
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
                          <input class="custom_field_files" type="file" name="{{$field->slug}}[]" multiple="true" accept="image/x-png,image/gif,image/jpeg" data-value="{{$imageVal}}" id="{{$field->slug}}-original">
                          <input class="hide" type="text" name="{{$field->slug}}-deleted" id="{{$field->slug}}-deleted">
                          <?php
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              echo '<div id="'.$field->slug.'-editedImages">';
                              foreach($arrayMultiple as $key => $image){
                                  echo '<div class="col-xs-6">';
                                  echo '<img style="width:100px;" src="'.asset('cms/').$image[0].'"><span style="display:block;width:100%;"><span class="custom_image_remove" style="color:red;cursor: pointer;" data-action="'.$field->slug.'-deleted" data-field="'.$field->slug.'" data-name="'.$key.'"><i class="fa fa-trash"></i></span></span><br>';
                                  echo '</div>';
                              }
                              echo '</div>';
                            }
                          ?>
                          @break
                      @case("File")
                          <?php
                            $fileValue = 0;
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              foreach($arrayMultiple as $key => $file){
                                  $fileValue++;
                              }
                            }
                          ?>
                          <input type="file" class="custom_field_files" name="{{$field->slug}}[]" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,text/plain, application/pdf" data-value="{{$fileValue}}" id="{{$field->slug}}-original">
                          <input class="hide" type="text" name="{{$field->slug}}-deleted" id="{{$field->slug}}-deleted">
                          <?php
                            if(isset($field->custom_value)){
                              $arrayMultiple = json_decode($field->custom_value);
                              echo '<div id="'.$field->slug.'-editedFiles">';
                              foreach($arrayMultiple as $key => $file){
                                  echo '<div class="col-xs-12"><span><a style="width:100px;" href="'.asset('cms/').$file[0].'" target="_blank">'.$key.'</a><span class="custom_image_remove" style="color:red;cursor: pointer;" data-action="'.$field->slug.'-deleted" data-field="'.$field->slug.'" data-name="'.$key.'"><i class="fa fa-trash"></i></span></span></div>';
                              }
                              echo '</div>';
                            }
                          ?>
                          @break

                  @endswitch
                    
                  </div>
                </div>
              </div>
            </div>
          @endforeach

  <!-- Custom Field code Section Ends  -->

  <input type="text" name="client_id" value="{{$client->id}}" hidden />  
</form>
</div>