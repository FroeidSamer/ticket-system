<div class="container-fluid">
    <div id="msg"></div>

    <form action="" id="close-form">
        <div class="form-group">
            <label for="name"><?= tr('password') ?></label>
            <input type="text" name="password" id="password" class="form-control" required>
        </div>

    </form>
</div>
<script>
    $('#close-form').submit(function(e) {
        e.preventDefault();
        start_load()
        $.ajax({
            url: 'admin/ajax.php?action=close_app',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if (resp == 1) {
                    end_load()
                    window.close();
                } else {
                    $('#msg').html('<div class="alert alert-danger">Password not correct</div>')
                    end_load()
                }
            }
        })
    })
</script>