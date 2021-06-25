<style>
    .multiselect-item.multiselect-group label input{
        height:auto;
    }
    .multiselect-container.dropdown-menu{
        width:inherit;
    }
</style>
  
  {{-- <div id="edit_modal_single" class="modal fade"> --}}
    {!! Form::open(array('url' => url(domain_route("company.admin.beatplan.updateSingle",[$fetchBeatPlans->first()->employee_id])), 'method' => 'post','id'=>'EditBeatPlans', 'files'=> true)) !!}
        <div class="modal-dialog" role="document" style="width:90%;">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" text-align="center" id="exampleModalLongTitle"  style="font-size:26px;">Update BeatPlan
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size:26px;">
                <span aria-hidden="true">&times;</span>
                </button></h4>
            </div>
            <div class="modal-body" id="toAppend">
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-4 employee_lists" style="margin-bottom:20px;">
                        <label class="pull-left">Assigned To<span style="color:red">*</span></label>
                        <input type="hidden" name="employee_list" value="{{$fetchBeatPlans->first()->employee_id}}">
                        <select class="select2 form-control employee_lists" id="employee_list" data-id="{{$fetchBeatPlans->first()->employee_id}}" disabled required>
                        <option></option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{$employee->name}}</option>
                        @endforeach
                        </select>
                    </div>  
                </div>
                
                <div class="">  
                    <table class="table table-bordered" id="main_table">  
                        <tbody id="toAppend">
                        <tr>
                            <th>Title<span style="color: red">*</span></th>
                            <th>Beats<span style="color: red">*</span></th>
                            {{-- <th>Parties<span style="color: red">*</span></th> --}}
                            <th>Date<span style="color: red">*</span></th>
                            {{-- <th>From Time</th>
                            <th>To Time</th> --}}
                            <th>Remark</th>
                        </tr>
                        @php $id=0 @endphp
                        @foreach($fetchBeatPlans as $fetchBeatPlan)
                        @php ++$id @endphp
                        <tr>
                            <td>
                            <div class="input-group" style="width:100%;">
                                <input id="title{{$id}}" class="form-control" type="text" name="title[]" value="{{$fetchBeatPlan->title}}" @if($fetchBeatPlan->plandate<date("Y-m-d"))disabled @endif required>
                            </div>
                            </td>
                            <td>
                            <div class="input-group beat_class"  style="width:-webkit-fill-available; @if($fetchBeatPlan->plandate<date("Y-m-d"))background-color:#eeeeee; @endif">
                                    @if($fetchBeatPlan->plandate<date("Y-m-d") && isset($fetchBeatPlan->beat_clients))
                                    <select class="form-control edit_beat_list" id="edit_beat_list{{$id}}" data-id={{$id}} multiple name="edit_beat_list[{{$id-1}}][]" @if($fetchBeatPlan->plandate<date("Y-m-d")) @endif>
                                        @php $beats_lists = json_decode($fetchBeatPlan->beat_clients); $beatIds = explode(',', $fetchBeatPlan->beat_id) @endphp
                                        @foreach($beats_lists as $beat_id=>$client_ids)
                                            @if(in_array($beat_id, $beatIds))
                                            <optgroup label="{{getBeatName($beat_id)}}" disabled>
                                            @foreach($client_ids as $company_name)
                                              @if(getClient($company_name))
                                                <option value="{{$company_name }}" selected>{{ getClient($company_name)->company_name}}</option>
                                              @endif
                                            @endforeach
                                            </optgroup>
                                            @endif
                                        @endforeach
                                    @else
                                    <select class="form-control multibeat" id="edit_beat_list{{$id}}" data-id={{$id}} multiple name="edit_beat_list[{{$id-1}}][]" @if($fetchBeatPlan->plandate<date("Y-m-d")) @endif>
                                        @foreach($beats_list as $beat_id=>$client_ids)
                                            <optgroup label="{{$client_ids["name"]}}">
                                            @foreach($client_ids["clients"] as $client_id=>$company_name)
                                                <option value="{{$beat_id}},{{ $client_id }}"@if(in_array($client_id,explode(',',$fetchBeatPlan->client_id)))selected @endif >{{$company_name}}</option>
                                            @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @endif
                                <span class="err" id="edit_beat_lists{{$id-1}}" ></span>
                            </div>
                            </td>
                            {{-- <td>
                            <div class="input-group party_list" style="position:initial">
                                <select  class="form-control multiparty party_list" style="width: 100%;" id="party_list{{$id}}"  name="edit_party_list[{{$id-1}}][]" multiple data-id={{$id}}>
                                    @php $clientids = explode(',', $fetchBeatPlan->client_id) @endphp
                                    @foreach($fetchBeatPlan->parties as $beat=>$value)
                                    <option value="{{ $beat }}" @foreach($clientids as $clientid) {{ ( $beat == $clientid ) ? 'selected' : '' }} @endforeach>{{$value}}</option>
                                    @endforeach
                                </select>
                                <span class="err" id="edit_party_lists{{$id-1}}"></span>
                            </div>
                            </td> --}}
                            <td style="width:170px;">
                                <div class="input-group" style="width:100%;">
                                    @if(config('settings.ncal')==1)

                                    <input autocomplete="off" required class="form-control fromdate" type="text" id="edit_start_date{{$id}}" name="edit_start_date[]" value={{getDeltaDateFormat($fetchBeatPlan->plandate)}} data-id={{$id}} @if($fetchBeatPlan->plandate<date("Y-m-d"))disabled @endif>
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    @else
                                    <input autocomplete="off" required class="form-control fromdate" type="text" id="edit_start_date{{$id}}" name="edit_start_date[]" value={{$fetchBeatPlan->plandate}} data-id={{$id}} @if($fetchBeatPlan->plandate<date("Y-m-d"))disabled @endif>
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    @endif
                                </div>
                            </td>
                            {{-- <td style="width:125px;">
                            <div class="input-group" style="width:inherit;">
                                <input autocomplete="off" class="form-control fromtime" type="text" id="edit_start_time{{$id}}" name="edit_start_time[]" value="{{$fetchBeatPlan->plan_from_time}}" style="padding-left:5px;" data-id={{$id}}>
                                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                            </td> --}}
                            {{-- <td style="width:125px;">
                            <div class="input-group" style="width:inherit;">

                                <input autocomplete="off" class="form-control totime" type="text" id="edit_end_time{{$id}}" name="edit_end_time[]" value="{{$fetchBeatPlan->plan_to_time}}" style="padding-left:5px;" data-id={{$id}}>
                                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>        
                            </div>
                            </td> --}}
                            <td>
                            <div class="input-group" style="width:100%;">
                                <textarea class="form-control" name="remark[]" id="remark{{$id}}"  style="height:40px;" @if($fetchBeatPlan->plandate<date("Y-m-d"))disabled @endif >{{$fetchBeatPlan->remark}}</textarea>                            
                            </div>
                            </td>
                            <input type="hidden" name="getCount[]" value="{{$id}}">
                            <input hidden type="text" id="edit_beatvplan_id" name="beatvplan_id" value="{{$fetchBeatPlan->beatvplan_id}}"/>
                            <input hidden type="text" id="edit_id{{$fetchBeatPlan->id}}" name="edit_id[]" value="{{$fetchBeatPlan->id}}"/>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                    @if($fetchBeatPlans->count()==1 && $fetchBeatPlans->first()->plandate<date("Y-m-d"))Close
                    @else Cancel
                    @endif
                </button>
                {{-- @if(!isset($view))
                <button type="submit" class="btn btn-primary updateBtn" @if($fetchBeatPlans->count()==1 && $fetchBeatPlans->first()->plandate<date("Y-m-d"))style="display:none;" @endif>Update</button>
                @else --}}
                <button type="submit" class="btn btn-primary updateBtn" @if($fetchBeatPlans->count()==1 && $fetchBeatPlans->first()->plandate<date("Y-m-d"))style="display:none;" @endif>Update</button>
                {{-- @endif</div> --}}
            </div>
        </div>
    {!! Form::close() !!}

<script>
    
    $('.select2').select2();
    $('.edit_beat_list').multiselect({
        enableFiltering: false,
        enableClickableOptGroups: false,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Assign Parties",
    });
    $('.multibeat').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: true,
        enableClickableOptGroups: true,
        includeSelectAllOption: true,	
        enableCollapsibleOptGroups : true,
        nonSelectedText:"Assign Parties",
    });
    // $('.multibeat').multiselect({
    //     placeholder: 'select...',
    //     columns: 1,
    //     search: true,
    //     selectAll: true,
    //     keepOrder: true,
    //     maxPlaceholderOpts : 2,
    // }); 

    // $('.multiparty').multiselect({
    //     placeholder: 'select...',
    //     columns: 1,
    //     search: true,
    //     selectAll: true,
    //     keepOrder: true,
    //     maxPlaceholderOpts : 2,
    // }); 

    // $('.fromdate').datepicker({
    //     startDate: new Date(),
    //     format:'yyyy-mm-dd',
    //     autoclose:true,
    // });
    @if(config('settings.ncal')==0)

    $('.fromdate').datepicker({
        startDate: new Date(),
        format:'yyyy-mm-dd',
        autoclose:true,
    });
    @else
    $('.fromdate').nepaliDatePicker({
        ndpEnglishInput: 'englishDate',
        disableBefore: moment(getNepaliDate()).format('MM/D/Y'),
    });
    @endif

    // var start_time = $('.fromtime').datetimepicker({
    //     format: 'h:mmA',
    //     pickDate: false,
    //     autoclose: false,
    // });

    // $('.totime').datetimepicker({
    //     format: 'h:mmA',
    //     pickDate: false,
    //     minDate: start_time.data("DateTimePicker"),
    //     autoclose: false,
    // });

    // $('body').on('focusout','.fromtime',function(){
    //     let fromTimeId = $(this).data("id");
    //     let choosenDate = $("#edit_start_date"+fromTimeId).val();
    //     let startTime = $('#edit_start_time'+fromTimeId).val();
    //     let endTime = $('#edit_end_time'+fromTimeId).val();
    //     let currentDate = moment().format("Y-MM-DD");
    //     let currentTime = moment().format("HH:mm");
    //     let chosenTime = moment(startTime, ["h:mmA"]).format("HH:mm");

    //     if(choosenDate == currentDate){
    //         if(!(chosenTime >= currentTime)){
    //             $('#edit_start_time'+fromTimeId).css("border", "1px solid red");
    //             alert("Please choose a valid Start Time.");
    //         }else{
    //             $('#edit_start_time'+fromTimeId).css("border", "");
    //             $('#edit_end_time'+fromTimeId).css("border", "");
    //         }
    //     }else{
    //         $('#edit_start_time'+fromTimeId).css("border", "");
    //         $('#edit_end_time'+fromTimeId).css("border", "");
    //     }
    // });

    // $('body').on('focusout','.totime',function(){

    //     let toTimeId = $(this).data("id");
    //     let startTime = $('#edit_start_time'+toTimeId).val();
    //     let chosenStartTime = moment(startTime, ["h:mmA"]).format("HH:mm");
    //     let endTime = $('#edit_end_time'+toTimeId).val();
    //     let chosenEndTime = moment(endTime, ["h:mmA"]).format("HH:mm");
    //     if(startTime!= ""){
    //         if(chosenEndTime <= chosenStartTime){
    //             $('#edit_end_time'+toTimeId).css("border", "1px solid red");
    //             alert("End Time must be greater than Start Time");
    //         }else{
    //             $('#edit_end_time'+toTimeId).css("border", "");
    //         }
    //     }
    // });

    // $('body').on('change','.fromdate',function(){

    // let fromDateId = $(this).data("id");
    // let choosenDate = $("#edit_start_date"+fromDateId).val();
    // let currentDate = moment().format("Y-MM-DD");

    // let startTime = $('#edit_start_time'+fromDateId).val();
    // let chosenStartTime = moment(startTime, ["h:mmA"]).format("HH:mm");
    // let endTime = $('#edit_end_time'+fromDateId).val();
    // let chosenEndTime = moment(endTime, ["h:mmA"]).format("HH:mm");

    // let currentTime = moment().format("HH:mm");

    // if(startTime !=""){
    //     if(choosenDate == currentDate){
    //         if(!(chosenStartTime >= currentTime)){
    //         $('#edit_start_time'+fromDateId).css("border", "1px solid red");
    //         alert("Please choose a valid Start Time");
    //         }else{
    //         $('#edit_start_time'+fromDateId).css("border", "");
    //         }
    //     }else{
    //         $('#edit_start_time'+fromDateId).css("border", "");
    //     }
    // }

    // if(endTime !=""){
    //     if(choosenDate == currentDate){
    //         if(!(chosenEndTime >= currentTime)){
    //         $('#edit_end_time'+fromDateId).css("border", "1px solid red");
    //         alert("Please choose a valid End Time");
    //         }else{
    //         $('#edit_end_time'+fromDateId).css("border", "");
    //         }
    //     }else{
    //     $('#edit_end_time'+fromDateId).css("border", "");
    //   }
    // }

    // });
    $('#EditBeatPlans').find('#employee_list').on('select2:select',function(){debugger;
        let sel_employee_id = $('#EditBeatPlans').find('#employee_list').val();
        window.location.href = sel_employee_id;
    });
</script>