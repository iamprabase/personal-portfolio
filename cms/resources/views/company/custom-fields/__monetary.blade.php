@php(
                             $currencies= Cache::rememberforever('currencies', function()
                             {
                                 return \App\Currency::orderBy('currency', 'ASC')->get()->unique('code');
                             })
                         )
<?php
if (!empty($form_data->{$field->slug})) {

    $arrayMonetory = explode(" ", $form_data->{$field->slug});
}
?>

<div class="form-group">
    <label for="{{$field->slug}}">{{$field->title}}
        @if($field->required == 1) <span>*</span> @endif
    </label>
    <div class="row">
        <div class="col-xs-2" style="padding-right:0;">
            <select name="{{$field->slug}}2" id="{{$field->slug}}2" class="select2">
                <option value="">Select</option>
                @foreach ($currencies as $currency)
                    <option value="{{$currency->id}}"
                            @if(!empty($form_data->{$field->slug}) && ($arrayMonetory[0]==$currency->id)) selected="selected" @endif>{{$currency->code}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-xs-10" style="padding-left:0;">
            <input type="number" step="0.01" id="{{$field->slug}}" class="form-control two-digits"
                   name="{{$field->slug}}"
                   placeholder="Enter {{$field->title}}" @if($field->required == 1) required @endif
                   @if(!empty($form_data->{$field->slug}))  value="{{isset($arrayMonetory[1]) ? $arrayMonetory[1] : null}}" @endif
            >
        </div>
        <span id="currencyMessage-{{$field->slug}}" hidden style="color: red; padding-left: 15px"></span>

    </div>

    <script>

        $('#' + "{{$field->slug}}" + 2).on('change', function () {
            if (!$('#' + "{{$field->slug}}" + 2).val() && !$('#' + "{{$field->slug}}").val()) {
                $('#' + "currencyMessage-{{$field->slug}}").hide();
                $('.keySubmit').prop('disabled', false);
            } else if ($('#' + "{{$field->slug}}" + 2).val() && !$('#' + "{{$field->slug}}").val()) {
                $('#' + "currencyMessage-{{$field->slug}}").show();
                $('#' + "currencyMessage-{{$field->slug}}").text('Please insert value before submitting');
                $('.keySubmit').prop('disabled', true);
            }
            else if (!$('#' + "{{$field->slug}}" + 2).val() && $('#' + "{{$field->slug}}").val()) {
                $('#' + "currencyMessage-{{$field->slug}}").show();
                $('#' + "currencyMessage-{{$field->slug}}").text('Please select currency type before submitting');
                $('.keySubmit').prop('disabled', true);
            }else if ($('#' + "{{$field->slug}}" + 2).val() && $('#' + "{{$field->slug}}").val()) {
                $('#' + "currencyMessage-{{$field->slug}}").hide();
                $('.keySubmit').prop('disabled', false);
            }
        });


        $('#' + "{{$field->slug}}").on('focusout', function () {
            if (!$('#' + "{{$field->slug}}").val() && !$('#' + "{{$field->slug}}" + 2).val()) {
                $('#' + "currencyMessage-{{$field->slug}}").hide();
                $('.keySubmit').prop('disabled', false);
            } else if ($('#' + "{{$field->slug}}").val() && !$('#' + "{{$field->slug}}" + 2).val()) {
                $('#' + "currencyMessage-{{$field->slug}}").show();
                $('#' + "currencyMessage-{{$field->slug}}").text('Please select currency type before submitting');
                $('.keySubmit').prop('disabled', true);
            } else if (!$('#' + "{{$field->slug}}").val() && $('#' + "{{$field->slug}}" + 2).val()) {
                $('#' + "currencyMessage-{{$field->slug}}").show();
                $('#' + "currencyMessage-{{$field->slug}}").text('Please insert value before submitting');
                $('.keySubmit').prop('disabled', true);
            }else if ($('#' + "{{$field->slug}}").val() && $('#' + "{{$field->slug}}" + 2).val()) {
                $('#' + "currencyMessage-{{$field->slug}}").hide();
                $('.keySubmit').prop('disabled', false);
            }
        });

    </script>

</div>

