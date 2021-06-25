<div class="form-group">
    <label for="{{$field->slug}}">
        {{$field->title}}
        @if($field->required == 1) <span >*</span> @endif
    </label>
    <input type="text"  id="{{$field->slug}}" class="form-control" name="{{$field->slug}}"
           maxlength="255"  placeholder="Enter {{$field->title}}"
           @if(isset($form_data)) value="{{ $form_data->{$field->slug} }}"  @endif
           @if($field->required == 1) required @endif
    >
</div>
