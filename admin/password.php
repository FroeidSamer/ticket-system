<div class="row">
    <div class="col-lg-12">
        <!-- FORM Panel -->
        <div class="col-md-4">
            <form action="" id="change-pass">
                <div class="card">
                    <div class="card-header">
                        <?= tr('changePassword') ?>
                    </div>
                    <div class="card-body">
                        <div id="msg"></div>
                        <div class="form-group">
                            <label class="control-label"><?= tr('newPassword') ?></label>
                            <input type="text" name="password" id="password" class="form-control">
                        </div>


                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> <?= tr('save') ?></button>
                                <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="_reset()"> <?= tr('cancel') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- FORM Panel -->
    </div>
</div>


<hr>

<script>
    function _reset() {
        $('#password').val('');
        $('#msg').html('')
    }
    $('#change-pass').submit(function(e) {
        e.preventDefault()
        start_load()
        $('#msg').html('')
        $.ajax({
            url: 'ajax.php?action=change_pass',
            data: {
                password: $('[name=password]').val(),
            },
            method: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Password successfully changed", 'success')
                    end_load()

                } else if (resp == 2) {
                    $('#msg').html("<div class='alert alert-danger'>ERROR could not change password</div>")
                    end_load()

                }
            }
        })
    })
</script>