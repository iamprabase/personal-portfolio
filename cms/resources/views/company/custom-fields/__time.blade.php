<div class="form-group">
    <label for="{{$field->slug}}">
        {{$field->title}}
        @if($field->required == 1) <span >*</span> @endif

    </label>
    <input type="time" class="form-control" id="{{$field->slug}}" name="{{$field->slug}}"
           placeholder="Enter {{$field->title}}" @if($field->required == 1) required @endif
           @if(isset($form_data))  value="{{ $form_data->{$field->slug} }}" @endif

    >
    <script>
        var temp = {!! $field !!};
        title = temp.slug;
        $('#' + title).flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            allowInput: {{$field->required == 1 ? 'true' : 'false'}},
        });
    </script>
</div>