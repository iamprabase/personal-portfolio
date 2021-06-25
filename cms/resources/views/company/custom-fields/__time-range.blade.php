<?php
if (isset($form_data))
    $arrayTimeRange = explode('-', $form_data->{$field->slug});
?>
<br>
<div class="form-group ">
    <label for="{{$field->slug}}">{{$field->title}}
        @if($field->required == 1) <span >*</span> @endif
    </label>
    <div class="row">
        <div class="col-xs-5">
            <input type="time" class="form-control " id="{{$field->slug}}1" name="{{$field->slug}}"
                   placeholder="Enter {{$field->title}}"
                   @if(isset($form_data))  value="{{array_key_exists(0, $arrayTimeRange)?$arrayTimeRange[0]:null}}"
                   @endif
                   @if($field->required == 1) required @endif
            >
        </div>
        <div class="col-xs-1">_</div>
        <div class="col-xs-5">
            <input type="time" class="form-control" id="{{$field->slug}}2" name="{{$field->slug}}2"
                   placeholder="Enter {{$field->title}}"
                   @if(isset($form_data)) value="{{array_key_exists(1, $arrayTimeRange)?$arrayTimeRange[1]:null}}" @endif
                    @if($field->required == 1) required @endif >
        </div>
    </div>
    <script>
        var temp = {!! $field !!};
        title = temp.slug;
        $('#' + title + '1').flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            allowInput: {{$field->required == 1 ? 'true' : 'false'}},
        });
        $('#' + title + '2').flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            allowInput: {{$field->required == 1 ? 'true' : 'false'}},
        });
    </script>

</div>
