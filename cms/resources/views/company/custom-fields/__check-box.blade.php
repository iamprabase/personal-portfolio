<div class="form-group">
    <label>{{$field->title}}</label>
    @if($field->required == 1) <span>*</span> @endif
    @php
        $options = json_decode($field->options);

    if(isset($form_data->{$field->slug})){
    $arrayMultiple = json_decode($form_data->{$field->slug});
    }

    @endphp
    @if(count($options))
        @foreach($options as $option)
            <div class="checkbox ">
                <label>
                    <input type="checkbox" value="{{$option}}" class="{{$field->slug}}"
                           name="{{$field->slug}}[]" @if(isset($form_data->{$field->slug}))  {{(collect($arrayMultiple)->contains($option)) ? 'checked="checked"':''}}@endif>
                    {{$option}}
                </label>
            </div>
        @endforeach
        <span id="{{$field->slug}}" style="color: red"></span>
    @endif
</div>