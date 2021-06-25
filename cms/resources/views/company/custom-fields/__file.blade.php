<?php
if (isset($form_data->{$field->slug}) && !is_null($form_data->{$field->slug})) {
    echo '<div class="form-group">
    <label for="' . $field->slug . '">' . $field->title . ' ' . (($field->required == 1) ? '*' : "") . ' <small style="color: grey" > &nbsp; &nbsp; Size of image should not be more than 2MB.</small></label>

            <div class="input-group input-file" name="' . $field->slug . '[]">
              <span class="input-group-btn">
                <button class="btn btn-default btn-choose" type="button">Choose</button>
              </span>
              <input  type="text" name="' . $field->slug . '"  id="' . $field->slug . '" class="form-control fileupload" placeholder="Choose a file..."/>

              <span class="input-group-btn">
                <button class="btn btn-danger btn-reset" type="button">Remove</button>
              </span>
            </div>
          </div>
          ';

    if (isset($form_data->{$field->slug})) {
        $arrayMultiple = json_decode($form_data->{$field->slug});
        if (!is_null($arrayMultiple)) {
            foreach ($arrayMultiple as $key => $file) {
                echo '<div class="col-xs-12">';
                echo '<span><a style="width:100px;" href="' . asset('cms/') . $file[0] . '" target="_blank">' . $key . '</a></span><br><br>';
                echo '</div>';
            }
        }
    }

} else {
    echo '<div class="form-group">
            <label for="' . $field->slug . '">' . $field->title . ' <span >' . (($field->required == 1) ? '*' : "") . '</span> <small style="color:grey "> &nbsp; &nbsp; Size of image should not be more than 2MB.</small></label>

            <div class="input-group input-file"  name="' . $field->slug . '[]">
              <span class="input-group-btn">
                <button class="btn btn-default btn-choose" type="button">Choose</button>
              </span>
              <input  type="text" name="' . $field->slug . '"  id="' . $field->slug . '" class="form-control fileupload" placeholder="Choose a file..." ' . (($field->required == 1) ? 'required="required"' : "") . '/>

              <span class="input-group-btn">
                <button class="btn btn-danger btn-reset" type="button">Remove</button>
              </span>
            </div>
          </div>';
}
?>