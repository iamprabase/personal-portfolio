<div class="form-group">
    <label for="{{$field->slug}}">
        {{$field->title}}
        @if($field->required == 1) <span >*</span> @endif
    </label>
    <input type="number" step="any" class="form-control" id="{{$field->slug}}"  name="{{$field->slug}}"
           placeholder="Enter {{$field->title}}"
           @if(isset($form_data)) value="{{ $form_data->{$field->slug} }}" @endif
           @if($field->required == 1) required @endif
    >
</div>