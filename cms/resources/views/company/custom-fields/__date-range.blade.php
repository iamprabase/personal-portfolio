<div class="form-group ">

    <label for="{{$field->slug}}">
        {{$field->title}}
        @if($field->required == 1) <span >*</span> @endif
    </label>

    <input type="text"  class="form-control"  id="{{$field->slug}}" name="{{$field->slug}}"
           placeholder="Enter {{$field->title}}"
           @if(isset($form_data)) value="{{$form_data->{$field->slug} }}"  @endif
           @if($field->required == 1) required @endif
    >
    <script>
        var temp = {!! $field !!};
        title = temp.slug;
        $('#' + title).flatpickr({
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            mode: "range",
            allowInput: {{$field->required == 1 ? 'true' : 'false'}},
        });
    </script>
</div>