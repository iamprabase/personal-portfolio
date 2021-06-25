<?php
$v = str_replace('[', '', str_replace(']', '', str_replace('"', '', str_replace("\/", '/', $field->options))));

$cus_value = explode(',', $v);
?>
<div class="form-group">
    <label for="{{$field->slug}}">{{$field->title}}
        @if($field->required == 1) <span >*</span> @endif
    </label>
    <select type="text"  id="{{$field->slug}}"  name="{{$field->slug}}" class=" form-control multiselect"
            @if($field->required == 1)  required  @endif>
        <option value="">Please Select an option</option>
        @foreach ($cus_value as $item)
            @if($item!='')
                <option value="{{$item}}"
                        @if(isset($form_data) && $item==$form_data->{$field->slug}) selected @endif>{{$item}}</option>
            @endif
        @endforeach
    </select>
</div>