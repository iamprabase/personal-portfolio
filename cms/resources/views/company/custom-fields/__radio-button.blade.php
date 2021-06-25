@php
    $options = json_decode($field->options);

@endphp
<div class="form-group">
    <label>
        {{$field->title}}
        @if($field->required == 1) <span >*</span> @endif

    </label>
    @foreach($options as $option)
        <div class="radio">
            <label>
                <input type="radio" value="{{$option}}" name="{{$field->slug}}"
                       @if(isset($form_data->{$field->slug})) {{$form_data->{$field->slug} == $option ? 'checked' : ''}} @endif
                       @if($field->required == 1) required @endif
                >{{$option}}
            </label>
        </div>
    @endforeach

</div>