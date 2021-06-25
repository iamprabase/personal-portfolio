<div class="form-group">
    <label for="{{$field->slug}}">
        {{$field->title}}
        @if($field->required == 1) <span >*</span> @endif

    </label>
    <input type="text"
           class="form-control phone_numbers"
           id="{{$field->slug}}"
           name="{{$field->slug}}"
           placeholder="Enter {{$field->title}}"
           @if(isset($form_data))
           value="{{ $form_data->{$field->slug} }}"
           @endif
           @if($field->required == 1)
           required
            @endif
    >

    <p class="help-block has-error">{{ $errors->first('phone') }}</p>
</div>