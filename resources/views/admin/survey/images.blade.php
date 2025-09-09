@if (isset($data))
    <?php $img_path = 'storage/surveys/' . $data->logo; ?>
    <input type="hidden" name="llogo_path" value="{{ $data->logo }}">

    <div class="form-group">
        <label for="logo" class="col-sm-2">Left Logo</label>

        <div class="col-sm-10">


            <button type="button" class="btn-link img-change">change</button>
            <button type="button" class="btn-link img-cancel">cancel</button>

        </div>
    </div>

    <div class="form-group changefile">
        <div class="col-sm-10 col-sm-offset-2">
            <input type="file" id="logo-image-survey" class="filestyle" data-buttonName="btn-success" name="logo"
                accept=".jpg,.jpeg,.png">
            <small><b>Note:</b>Maximum allowed image width size is 400px.</small>
            <br><small id="error-image"></small>
        </div>
    </div>

    <!-- For Right logo -->

    <?php $img_path = 'storage/surveys/' . $data->right_logo; ?>
    <input type="hidden" name="rlogo_path" value="{{ $data->right_logo }}">

    <div class="form-group">
        <label for="logo" class="col-sm-2">Right Logo</label>

        <div class="col-sm-10">


            <button type="button" class="btn-link img-change">change</button>
            <button type="button" class="btn-link img-cancel">cancel</button>

        </div>
    </div>

    <div class="form-group changefile">
        <div class="col-sm-10 col-sm-offset-2"> name="right_logo" accept=".jpg,.jpeg,.png">
            <small><b>Note:</b>Maximum allowed image width size is 400px.</small>
            <br><small id="error-image"></small>
        </div>
    </div>
@else
    <div class="form-group">
        <label for="logo" class="col-sm-2">Left Logo</label>

        <div class="col-sm-10">
            <input type="file" id="logo-image-survey" class="filestyle" data-buttonName="btn-success" name="logo"
                accept=".jpg,.jpeg,.png">
            <small><b>Note:</b>Maximum allowed image width size is 400px.</small>
            <br><small id="error-image"></small>
        </div>
    </div>

    <div class="form-group">
        <label for="right_logo" class="col-sm-2">Right Logo</label>

        <div class="col-sm-10">
            <input type="file" id="right-logo-image-survey" class="filestyle" data-buttonName="btn-success"
                name="right_logo" accept=".jpg,.jpeg,.png">
            <small><b>Note:</b>Maximum allowed image width size is 400px.</small>
            <br><small id="error-image"></small>
        </div>
    </div>
@endif

<script src="{{ asset('script/bootstrap-filestyle.js') }}"></script>

<script type="text/javascript">
    var _URL = window.URL || window.webkitURL;
    $("#logo-image-survey").change(function(e) {
        var file, img;
        if ((file = this.files[0])) {
            img = new Image();
            img.onload = function() {

                var width = this.width;
                var height = this.height;
                if (width > 400) {

                    swal('Maximum allowed image width size is 400px');

                    $("#logo-image-survey").filestyle('clear');

                    //Revalidate Field
                    $('#add-survey').bootstrapValidator('revalidateField', 'logo');

                }
            };
            img.src = _URL.createObjectURL(file);
        }
    });
</script>
<script type="text/javascript">
    var _URL = window.URL || window.webkitURL;
    $("#right-logo-image-survey").change(function(e) {
        var file, img;
        if ((file = this.files[0])) {
            img = new Image();
            img.onload = function() {

                var width = this.width;
                var height = this.height;
                if (width > 400) {

                    swal('Maximum allowed image width size is 400px');

                    $("#right-logo-image-survey").filestyle('clear');

                    //Revalidate Field
                    $('#add-survey').bootstrapValidator('revalidateField', 'right_logo');

                }
            };
            img.src = _URL.createObjectURL(file);
        }
    });
</script>
