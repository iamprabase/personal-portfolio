<style>
  .multiselect-item.multiselect-group label input{
    height:auto;
  }
  .multiselect-container.dropdown-menu{
    width:inherit;
  }

</style>

<div id="exampleModalCenter" class="modal fade">

  {!! Form::open(array('url' => url(domain_route("company.admin.beatplan.store")),'id'=>'AddNewBeatPlan', 'files'=> true)) !!}

  <div class="modal-dialog" role="document" style="width:80%;">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" text-align="center" id="exampleModalLongTitle" style="font-size:26px;">Plan New Beat

        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size:26px;">

          <span aria-hidden="true">&times;</span>

        </button></h4>

      </div>

      <div class="modal-body" style="min-height:350px;">

        <div class="row">

            <div class="col-xs-4 col-xs-offset-4 employee_lists" style="margin-bottom:20px;">

                <label class="pull-left">Assign To<span style="color:red">*</span></label>

                <select class=" select2 form-control employee_lists" id="employee_list" name="employee_list" required>

                  <option></option>

                  @foreach($employees as $employee)

                    <option value="{{ $employee->id }}">{{$employee->name}}</option>

                  @endforeach

                </select>

            </div>  

        </div>

        <div class="">  

          <table class="table table-bordered" id="main_table" >  

            <tbody id="toAppend">

              <tr>

                <th>Title<span style="color: red">*</span></th>

                <th>Beats<span style="color: red">*</span></th>

                {{-- <th>Parties<span style="color: red">*</span></th> --}}

                <th>Date<span style="color: red">*</span></th>

                {{-- <th>From Time</th> --}}

                {{-- <th>To Time</th> --}}

                <th>Remark</th>

                <th> 

                </th>

              </tr>

              <tr>

                <td>

                  <div class="input-group" style="width:100%;">

                    <input class="form-control title" type="text" id="title1" name="title[]" required>

                  </div>

                </td>

                <td>

                  <div class="input-group beat_class" style="width:-webkit-fill-available">
                    <select class="form-control multibeat beat_list" id="beat_list1" name="beat_list[0][]" data-id="1" multiple>
                      @foreach($beats_list as $beat_id=>$client_ids)
                        <optgroup label="{{$client_ids["name"]}}">
                          @foreach($client_ids["clients"] as $client_id=>$company_name)
                            <option value="{{$beat_id}},{{ $client_id }}" >{{$company_name}}</option>
                          @endforeach
                        </optgroup>
                      @endforeach
                    </select>
                    <span class="err" id="beat_lists0" ></span>
                  </div>

                </td>

                {{-- <td>

                  <div class="input-group party_list" style="position:initial;">

                      <select class="form-control multiparty party_list" id="party_list1" name="party_list[0][]" data-id="1" multiple>

                      </select>

                      <span class="err" id="party_lists0"></span>

                  </div>

                </td> --}}

                <td  style="width:170px;">

                  <div class="input-group" style="width:100%;">
                    

                    <input autocomplete="off"  class="form-control pd-left fromdate" type="text" id="start_date1" name="start_date[]" data-id="1" required>
                    {{-- @if(config('settings.ncal')==0)
                    <input autocomplete="off"  class="form-control pd-left fromdate" type="text" id="start_date1" name="start_date[]" data-id="1" required>
                    @else
                    <input autocomplete="off"  class="form-control pd-left fromdate" type="text" id="start_date1_bs" name="start_date_bs[]" data-id="1" required>
                    <input autocomplete="off"  class="form-control pd-left fromdate" type="hidden" id="start_date1" name="start_date[]" data-id="1">
                    @endif --}}

                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>                  </div>

                </td>

                {{-- <td  style="width:125px;">

                  <div class="input-group" style="width:inherit;">

                    <input autocomplete="off" class="form-control fromtime" type="text" id="start_time1" name="start_time[]" data-id="1"  style="padding-left:5px;">

                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>       

                  </div>

                </td> --}}

                {{-- <td style="width:125px;">

                  <div class="input-group" style="width:inherit;">

                    <input autocomplete="off" class="form-control totime" type="text" id="end_time1" name="end_time[]" data-id="1"  style="padding-left:5px;">

                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>       

                  </div>

                </td> --}}

                <td>

                  <div class="input-group" style="width:100%;">

                    <textarea class="form-control remark" id="remark1" name="remark[]" style="height:40px;"></textarea>

                  </div>

                </td>

                

                <input type="hidden" name="getCount[]" value="0">

                

                <td>

                  <button type="button" name="add" id="addPlans" class="btn btn-success form-control addPlans" style="background-color:#079292!important;color:white;">+</button> 

                </td>

              </tr>

            </tbody>

          </table>

        </div>

      </div>

      <div class="modal-footer">

        {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button> --}}

        <button type="submit" class="btn btn-primary addBeatPlans">Add</button>

      </div>

    </div>

  </div>

  {!! Form::close() !!}

</div>





