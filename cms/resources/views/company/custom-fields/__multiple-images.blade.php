@php
    $imageVal = 0;
    if (isset($form_data->{$field->slug})) {
        $arrayMultiple = json_decode($form_data->{$field->slug});
        if (!is_null($arrayMultiple)){
             foreach ($arrayMultiple as $key => $image) {
            $imageVal++;
        }
        }

    }
@endphp
<div class="form-group">
    <label for="">{{$field->title}}  @if($field->required == 1) <span>*</span> @endif <small style="color: grey"> &nbsp;
            &nbsp;Size of image should not be more than 2MB.</small></label>

    <div id="imggroup{{$field->id}}"
         class="form-group @if ($errors->has('expense_photo')) has-error @endif multiimg col-xs-12">
        <input class="hide" type="text" name="{{$field->slug}}-deleted" id="{{$field->slug}}-deleted">
        <?php
        if (isset($form_data->{$field->slug})) {
            $arrayMultiple = json_decode($form_data->{$field->slug});
        }
        if (isset($arrayMultiple)) {
            if (!is_null($arrayMultiple)) {
                foreach ($arrayMultiple as $key => $image) {
                    echo '<div class="col-xs-4 imgUp ">
                          <div class="imagePreview imagePreview" style="background:url(/cms/' . $image[0] . ');background-color: grey;background-position: center center;background-size: contain;background-repeat: no-repeat;" >
                          </div>
                            <label class="btn btn-primary upload" data-action="' . $field->slug . '-deleted" data-field="' . $field->slug . '" data-name="' . $key . '"> Upload
                                <input type="file" data-value="' . $imageVal . '" id="' . $field->slug . '-original" name="' . $field->slug . '[]" class="uploadFile img custom_field_files" value="Upload Photo" style="    width: 0px;height: 1px;overflow: auto;padding-right: 5px">
                                <span hidden></span>
                            </label>
                            <i class="fa fa-times del" id="del-' . $field->slug . '" data-action="' . $field->slug . '-deleted" data-field="' . $field->slug . '" data-name="' . $key . '" data-id="imggroup' . $field->id . '"></i>
                        </div>';
                }
            }
            echo '
                        <i class="fa fa-plus imgAdd " id="' . $field->slug . '" data-name="' . $field->slug . '[]" data-id=imggroup' . $field->id . '></i>';
        } else {
            echo '
                    <div class="col-xs-4 imgUp">
                         <div class="imagePreview form-group"></div>
                              <label class="btn btn-primary"> Upload
                               <input type="file" data-value="' . $imageVal . '" id="' . $field->slug . '-original" name="' . $field->slug . '[]" class="uploadFile img custom_field_files" value="Upload Photo"  style="    width: 0px;height: 1px;overflow: auto;padding-right: 5px" ' . (($field->required == 1) ? 'required' : "") . '>
                                </label>
                                <i class="fa fa-times del" id="del-' . $field->slug . '" data-action="' . $field->slug . '-deleted" data-field="' . $field->slug . '" data-id="imggroup' . $field->id . '"></i>
                           </div><!-- col-2 -->
                            <i class="fa fa-plus imgAdd " id="' . $field->slug . '" data-name="' . $field->slug . '[]" data-id=imggroup' . $field->id . '></i>
                     ';
        }
        ?>
    </div>
</div>

<script>


    $('{{"#".$field->slug}}').click(function () {
        var id = '{{"del-". $field->slug}}';
        var imggroupid = $(this).attr('data-id');
        var Imgcount = $("#" + imggroupid + " .imgUp").length;
        var inputname = $(this).attr('data-name');
        if (Imgcount < 3) {
            if (Imgcount === 2) {
                $("#" + imggroupid + " .imgAdd").hide();
            }
            if ("{{$field->required == 1}}") {
                $(this).closest(".form-group").find('{{"#".$field->slug}}').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="' + inputname + '" type="file" class="uploadFile img" value="Upload Photo" style="width: 0;height: 1px;overflow: auto;padding-right: 5px"  required ></label><i class="fa fa-times del" id="' + id + '" data-id="' + imggroupid + '"></i></div>');

            } else {
                $(this).closest(".form-group").find('{{"#".$field->slug}}').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="' + inputname + '" type="file" class="uploadFile img" value="Upload Photo" style="width: 0;height: 1px;overflow: auto;padding-right: 5px;"></label><i class="fa fa-times del" id="' + id + '" data-id="' + imggroupid + '"></i></div>');

            }
        } else {
            $(".imgAdd").hide();
        }
    });

    $(document).on("click", "{{"i#del-".$field->slug}}", function () {
        var imggroupid = $(this).attr('data-id');
        var inputname = $('{{"#".$field->slug}}').attr('data-name');
        var Imgcount = $("#" + imggroupid + " .imgUp").length;
        if (Imgcount < 3) {
            $("#" + imggroupid + " .imgAdd").show();
        }

        var deletedField = $(this).data('action');
        var originalField = $(this).data('field');
        var valOriginal = $('#' + originalField + '-original').data('value');
        var valOriginal = valOriginal - 1;
        $('#' + originalField + '-original').data('value', valOriginal);
        if ($('#' + deletedField).val() == "") {
            $('#' + deletedField).val($(this).data('name'));
        } else {
            $('#' + deletedField).val($('#' + deletedField).val() + ',' + $(this).data('name'));
        }


        $(this).parent().remove();
        Imgcount--;

        var id = '{{"del-". $field->slug}}';
        if (Imgcount === 0) {
            if ("{{$field->required == 1}}") {
                $('{{"#".$field->slug}}').closest(".form-group").find('{{"#".$field->slug}}').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="' + inputname + '" type="file" class="uploadFile img" value="Upload Photo" style="width: 0;height: 1px;overflow: auto;padding-right: 5px;" required></label><i class="fa fa-times del" id="' + id + '" data-id="' + imggroupid + '"></i></div>');

            } else {
                $('{{"#".$field->slug}}').closest(".form-group").find('{{"#".$field->slug}}').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="' + inputname + '" type="file" class="uploadFile img" value="Upload Photo" style="width: 0;height: 1px;overflow: auto;padding-right: 5px;"></label><i class="fa fa-times del" id="' + id + '" data-id="' + imggroupid + '"></i></div>');

            }
        }


        if (Imgcount < 3) {
            $("#" + imggroupid + " .imgAdd").show();
        }


    });



</script>

