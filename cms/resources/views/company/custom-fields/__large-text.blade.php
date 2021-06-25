<div class="form-group">
    <label for="{{$field->slug}}">
        {{$field->title}}
        @if($field->required == 1) <span >*</span> @endif
    </label>
    <textarea type="text" class="form-control" name="{{$field->slug}}"  id="{{$field->slug}}"
              placeholder="Enter {{$field->title}}"  @if($field->required == 1)  required @endif
    >@if(isset($form_data)){{ $form_data->{$field->slug} }}@endif</textarea>
</div>