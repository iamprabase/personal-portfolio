
<div class="row">
  <div class="make-no-reponsive col-xs-12"  style="min-height:300px;">
    <table class="table table-bordered" id="dynamic_field" style="margin-top:15px;">
      <thead>
        <tr>
          <th>S.No</th>
          <th>Target Options<span style="color: red">*</span></th>
          <th>Target Interval<span style="color: red">*</span></th>
          <th>Target Value<span style="color: red">*</span></th>
        </tr>
      </thead>
      <tbody> 

            {{-- <input type="hidden" class="allow-edit-variant" value="1"> --}}
            @foreach($ss as $rr=>$tgt)
              <tr class="rowElement" id="rowElement{{$row}}" data-row_id="{{$row}}">
                <input type="hidden" name="numofRows[{{$yy}}][]" value="{{$row}}">
                <input type="hidden" name="tgid[{{$yy}}][{{$row}}]" value="{{$tgt['id']}}">
                <td>{{$row+1}}</td>
                <td>
                  <select name="topt[{{$yy}}][{{$row}}]" onChange="prefupdated(this)" id="topt{{$row}}" class="form-control toptionsclass" >
                  @foreach($alltargetoptions  as $k=>$v)
                    <option value='{{$v->id}}' @if($v->id==$tgt['target_rule']) selected @endif >{{$v->options_value}}</option>
                  @endforeach
                  </select>
                </td>
                <td>
                  <select name="tinterval[{{$yy}}][{{$row}}]" class="form-control targetvalueClass" id="unit{{$row}}" data-id="{{$row}}" required >
                  @foreach($data['targetvalue']  as $k=>$v)
                      <option value='{{$k}}' @if($k==$tgt['target_interval']) selected @endif >{{$v}}</option>
                  @endforeach
                  </select> 
                </td>
                <td><input class="form-control variantClass" id="variantval{{$row}}" placeholder="Target Value" name="tval[{{$yy}}][{{$row}}]" type="text" value="{{$tgt['target_values']}}">
                </td>
              </tr>
              @php $row++; @endphp
            @endforeach

      
      </tbody>
    </table>
  </div>
</div>

<script>
  // var selectedopt = [];
  var selectedopt = {};

</script>


