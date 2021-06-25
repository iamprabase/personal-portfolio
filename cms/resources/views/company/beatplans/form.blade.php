

<tr class="appendedElement{{$id}}" id="appendedElement{{$id}}">

    <td>

        <div class="input-group" style="width: 100%;">

            <input class="form-control title" type="text" id="title{{$id}}" name="title[]" required>

        </div>

    </td>

    <td>

        <div class="input-group beat_class" style="width:-webkit-fill-available">

        <select class="form-control multibeat beat_list" id="beat_list{{$id}}" name="beat_list[{{$id-1}}][]" data-id="{{$id}}" multiple>

            @foreach($beats_list as $beat_id=>$client_ids)
            <optgroup label="{{getBeatName($beat_id)}}">
                @foreach($client_ids as $client_id=>$company_name)
                <option value="{{ $client_id }}" >{{$company_name}}</option>
                @endforeach
            </optgroup>
            @endforeach

        </select>

        <span class="err" id="beat_lists{{$id-1}}"></span>

        </div>

    </td>

    {{-- <td>

        <div class="input-group party_list" style="position:initial;">

            <select class="form-control party_list multiparty" id="party_list{{$id}}"  name="party_list[{{$id-1}}][]" data-id="{{$id}}" multiple>

            </select>

            <span class="err" id="party_lists{{$id-1}}"></span>

        </div>

    </td> --}}

    <td tyle="width:170px;">

        <div class="input-group" style="width:100%;">

        <input autocomplete="off" required class="form-control pd-left fromdate" type="text" id="start_date{{$id}}" name="start_date[]" data-id="{{$id}}">

        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>                  </div>

    </td>

    {{-- <td style="width:125px;">

        <div class="input-group" style="width:inherit;">

        <input autocomplete="off" class="form-control fromtime" type="text" id="start_time{{$id}}" name="start_time[]" data-id="{{$id}}"  style="padding-left:5px;">

        <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>    

        </div>

    </td> --}}

    {{-- <td style="width:125px;">

        <div class="input-group" style="width:inherit;">

        <input autocomplete="off" class="form-control totime" type="text" id="end_time{{$id}}" name="end_time[]" data-id="{{$id}}" style="padding-left:5px;">

            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>    

        </div>

    </td> --}}

    <td>

        <div class="input-group" style="width:100%;">

            <textarea class="form-control remark" id="remark{{$id}}" name="remark[]" style="height:40px;"></textarea>

        </div>

    </td>

    

    <input type="hidden" name="getCount[]" value="<?php $id-1; ?>"> 

    

    <td>

        <button type="button" name="removePlans" id="{{$id}}" class="btn btn-danger form-control removePlans" style="background-color:red;color:white;">X</button>

    </td>

</tr>



<script>

    $('.removePlans').click(function(){

        let removeButtonId = $(this).attr("id");

        $('.appendedElement'+removeButtonId+'').remove();  

    });



    $('.select2').select2({placeholder: 'select...',});



    $('.multibeat').multiselect({

        placeholder: 'Select Beats',

        columns: 1,

        search: true,

        selectAll: true,

        keepOrder: true,

        maxPlaceholderOpts : 2,

    }); 





    $('.multiparty').multiselect({

        placeholder: 'Select Parties',

        columns: 1,

        search: true,

        selectAll: true,

        keepOrder: true,

        maxPlaceholderOpts : 2,



    });    



    $('.fromdate').datepicker({

        startDate: new Date(),

        format:'yyyy-mm-dd',

        autoclose:true,

    });



    $('.todate').datepicker({

        startDate: new Date(),

        format:'yyyy-mm-dd',

        autoclose:true,

    }).attr('disabled');



    $('.fromtime').datetimepicker({

        format: 'h:mmA',

        pickDate: false,

    });



  $('.totime').datetimepicker({

        format: 'h:mmA',

        pickDate: false,

  }).attr('disabled',false);

</script>