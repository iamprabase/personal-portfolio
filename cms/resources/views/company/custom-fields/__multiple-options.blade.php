<?php
if (isset($form_data->{$field->slug})) {
    $arrayMultiple = json_decode($form_data->{$field->slug});

}

$v = str_replace('[', '', str_replace(']', '', str_replace('"', '', str_replace("\/", '/', $field->options))));

$cus_value = explode(',', $v);


?>

<div class="form-group">
    <label for="{{$field->slug}}">{{$field->title}} @if($field->required == 1) <span >*</span> @endif</label>
    <select class="select2 multiselect" name="{{$field->slug}}[]" id="{{$field->slug}}" multiple
            @if($field->required == 1) required @endif>
        @foreach ($cus_value as $item)
            @if($item!='')
                 <option value="{{$item}}" @if(isset($form_data->{$field->slug}) && is_array($arrayMultiple) && in_array($item,$arrayMultiple)) selected @endif>{{$item}}</option>
            @endif
        @endforeach
    </select>
</div>

