
<div class="row">
  <div class="make-no-reponsive col-xs-12">
    <table class="table table-bordered" id="dynamic_field" style="margin-top:15px;">
      <thead>
        <tr>
          <th>S.No</th>
          <th>Target Options<span style="color: red">*</span></th>
          <th>Target Interval<span style="color: red">*</span></th>
          <th>Target Value<span style="color: red">*</span></th>
          <th>Add More Targets</th>
        </tr>
      </thead>
      <tbody> 

          @if(isset($target) && $target)
            @php $row=0;$j=1;  @endphp
            {{-- <input type="hidden" class="allow-edit-variant" value="1"> --}}
            @php $finaltrchk = 'npres'; @endphp
            @foreach($target as $tgt)
              @php $c1 = 'nps'; @endphp
              @if($tgt['target_rules']=='1')
                @if(config('settings.orders')==1)
                  @php $c1 = 'ps'; @endphp
                @endif
              @elseif($tgt['target_rules']=='2')
                @if(config('settings.orders')==1)
                  @php $c1 = 'ps'; @endphp
                @endif
              @elseif($tgt['target_rules']=='3')
                @if(config('settings.collections')==1)
                  @php $c1 = 'ps'; @endphp
                @endif
              @elseif($tgt['target_rules']=='4')
                @if(config('settings.collections')==1)
                  @php $c1 = 'ps'; @endphp
                @endif
              @elseif($tgt['target_rules']=='5')
                @if(config('settings.visit_module')==1)
                  @php $c1 = 'ps'; @endphp
                @endif
              @elseif($tgt['target_rules']=='6')
                @if(config('settings.party')==1)
                  @php $c1 = 'ps'; @endphp
                @endif
              @elseif($tgt['target_rules']=='7')
                @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
                  @php $c1 = 'ps'; @endphp
                @endif
              @endif
              @if($c1=='ps')
                @php $finaltrchk = 'pres'; @endphp
                <tr class="rowElement" id="rowElement{{$row}}" data-row_id="{{$row}}">
                  <input type="hidden" name="numofRows[]" value="{{$row}}">
                  <input type="hidden" name="tgid[{{$row}}]" id="tgrmid_{{$row}}" value="{{$tgt['id']}}">
                  <td>{{$row+1}}</td>
                  <!-- <td><input class="form-control variantClass" id="variant{{$row}}" placeholder="Target Name" name="tname[{{$row}}]" type="text" value="{{$tgt['target_name']}}">                   
                  </td> -->
                  <td>
                    @php $totval = count($data['targetoptions']); @endphp
                    @if($totval>0)
                      <select name="topt[{{$row}}]" onChange="prefupdated(this)" id="topt_{{$row}}" class="form-control toptionsclass">
                      @foreach($data['targetoptions']  as $k=>$v)
                          <option value='{{$v->id}}' @if($tgt['target_rules']==$v->id) selected @endif>{{$v->options_value}}</option>
                      @endforeach
                      </select>
                    @endif
                  </td>
                  <td>
                    @php $totval = count($data['targetvalue']); @endphp
                    @if($totval>0)
                      <select name="tinterval[{{$row}}]" class="form-control targetvalueClass" id="unit{{$row}}" data-id="{{$row}}" required >
                      @foreach($data['targetvalue']  as $k=>$v)
                          <option value='{{$k}}' @if($tgt['target_interval']==$k) selected @endif>{{$v}}</option>
                      @endforeach
                      </select>
                    @endif
                  </td>
                  <td><input class="form-control variantClass" id="variantval{{$row}}" placeholder="Target Value" name="tval[{{$row}}]" type="text" onchange="checkValGreater(this)" value="{{$tgt['target_value']}}">
                  </td>
                  <td>
                    <button class="btn btn-danger btn-remove action-Btn hidden" id="remove_entry{{$row}}" data-id="{{$row}}" type="button">X</button>
                    <button class="btn btn-primary pull-right btn-add action-Btn" id="add_entry{{$row}}" data-id="{{$row}}" type="button">+</button> 
                  </td>
                </tr>
                @php $row++; @endphp
              @endif
            @endforeach

            @if($finaltrchk=='npres')
              <tr class="rowElement" id="rowElement{{$row}}" data-row_id="{{$row}}">
                <input type="hidden" name="numofRows[]" value="{{$row}}" id="totrows">
                <td>1</td>
                <td>
                  @php $totval = count($data['targetoptions']); @endphp
                  @if($totval>0)
                    <select name="topt[0]" onChange="prefupdated(this)" tse="fopt" class="form-control toptionsclass" id="topt_0" required>
                    @foreach($data['targetoptions']  as $k=>$v)
                        <option value='{{$v->id}}'@if($v->id==1) selected @endif>{{$v->options_value}}</option>
                    @endforeach
                    </select>
                  @endif
                </td>
                <td>{!! Form::select('tinterval[0]',$data['targetvalue'], null, ['class' => 'form-control targetvalueClass','id'=>'unit0','data-id'=>'0','required']) !!}</td>
                <td>{!! Form::number('tval[0]',null,['class'=>'form-control variantClass','id'=>'variant0','placeholder' => 'Target Value','required','onchange'=>'checkValGreater(this)']) !!}</td>

                <td>{!! Form::button('X', ['class' => 'btn btn-danger btn-remove action-Btn hidden', 'id' => 'remove_entry0', 'data-id' => 0, 'disabled'=>false]) !!} 
                {!! Form::button('+', ['class' => 'btn btn-primary pull-right btn-add action-Btn', 'id' => 'add_entry0', 'data-id' => 0]) !!} </td>
              </tr>
            @endif
          @else

            <tr class="rowElement" id="rowElement0" data-row_id="0">
              <input type="hidden" name="numofRows[]" value="rows_0" id="totrows">
              <td>1</td>
              <!-- <td>{!! Form::text('tname[0]',null,['class'=>'form-control variantClass','id'=>'variant0','placeholder' => 'Target Name']) !!} </td> -->
              <td>
                @php $totval = count($data['targetoptions']); @endphp
                @if($totval>0)
                  <select name="topt[0]" onChange="prefupdated(this)" tse="fopt" class="form-control toptionsclass" id="topt_0">
                  @foreach($data['targetoptions']  as $k=>$v)
                      <option value='{{$v->id}}'@if($v->id==1) selected @endif>{{$v->options_value}}</option>
                  @endforeach
                  </select>
                @endif
              </td>
              <td>{!! Form::select('tinterval[0]',$data['targetvalue'], null, ['class' => 'form-control targetvalueClass','id'=>'unit0','data-id'=>'0','required']) !!}</td>
              <td>{!! Form::number('tval[0]',null,['class'=>'form-control variantClass','id'=>'variant0','placeholder' => 'Target Value','required','onchange'=>'checkValGreater(this)']) !!}</td>

              <td>{!! Form::button('X', ['class' => 'btn btn-danger btn-remove action-Btn hidden', 'id' => 'remove_entry0', 'data-id' => 0, 'disabled'=>false]) !!} 
              {!! Form::button('+', ['class' => 'btn btn-primary pull-right btn-add action-Btn', 'id' => 'add_entry0', 'data-id' => 0]) !!} </td>
            </tr>

          @endif
      
      </tbody>
    </table>
  </div>
</div>

<script>
  // var selectedopt = [];
  var selectedopt = {};

  function checkValGreater(mydiv){
    var tgvalue = parseInt(mydiv.value);
    if(tgvalue==0 || tgvalue<0 || tgvalue==''){
      alert('Value cannot be less than 1');
      mydiv.value = 1;
      return false;
    }else{
      return true;
    }
  }

</script>

@if(isset($target) && $target)
<script>

  function prefupdated(sel=''){
    var totrows = $('.rowElement').length;
    changedrowid = (parseInt((sel.id).split('_')[1]));
    changedrowid_curval = parseInt($("#topt_"+changedrowid).val());
    changedrowid_prevval = selectedopt['topt_'+changedrowid];
    newarr = [];
    $.each(selectedopt,function(a,b){
      if(!isNaN(b)){
        newarr.push(a);
      }
    });
    for(var gh=0;gh<newarr.length;gh++){
      rownum = (parseInt(newarr[gh].split('_')[1]));
      if(rownum!=changedrowid){
        curr_row = "#topt_"+rownum;
        var inputf = $(curr_row+' option[value="'+changedrowid_curval+'"]');
        inputf.attr('disabled', 'disabled');
      }
    }
    for(var gh=0;gh<newarr.length;gh++){
      rownum = (parseInt(newarr[gh].split('_')[1]));
      if(rownum!=changedrowid){
        curr_row = "#topt_"+rownum;
        var inputf = $(curr_row+' option[value="'+changedrowid_prevval+'"]');
        inputf.removeAttr('disabled');
      }
    }
    var currrow = ("topt_"+changedrowid);
    selectedopt[currrow] = changedrowid_curval;
  }




</script>
@endif