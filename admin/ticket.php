<?php
$qry = $conn->query("SELECT * from settings limit 1");
if ($qry->num_rows > 0) {
    foreach ($qry->fetch_array() as $k => $val) {
        $meta[$k] = $val;
    }
}
?>
<style>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 55px;
        height: 27px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    label {
        display: block;
    }
</style>

<div class="card col-lg-12">
    <div class="card-body">
        <form action="" id="manage-settings">
            <div class="form-check form-switch mb-3">
                <label class="switch">
                    <input type="checkbox" class="chk_box" name="name" <?= $meta['ticket_company'] == 'on' ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
                <span class="ml-2"><?= tr('addCompanyName') ?></span>
            </div>
            <div class="form-check form-switch mb-3">
                <label class="switch">
                    <input type="checkbox" class="chk_box" name="logo" <?= $meta['ticket_logo'] == 'on' ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
                <span class="ml-2"><?= tr('addCompanyLogo') ?></span>
            </div>
            <div class="form-check form-switch mb-3">
                <label class="switch">
                    <input type="checkbox" class="chk_box" name="time" <?= $meta['ticket_date'] == 'on' ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
                <span class="ml-2"><?= tr('addTimeDate') ?></span>
            </div>
            <div class="form-check form-switch mb-3">
                <label class="switch">
                    <input type="checkbox" class="chk_box" id="note-chkbox" name="note" <?= $meta['ticket_note'] == 'on' ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
                <span class="ml-2"><?= tr('addNote') ?></span>
            </div>

            <div class="form-group" id="note-area" <?= $meta['ticket_note'] == 'off' ? 'style = "display: none;"' : '' ?>>
                <textarea class="form-control" id="exampleFormControlTextarea1" name="notetext" rows="3"><?= $meta['note'] != null ? $meta['note'] : '' ?></textarea>
            </div>
            <center>
                <button class="btn btn-info btn-primary btn-block col-md-2"><?= tr('save') ?></button>
            </center>
        </form>
    </div>
</div>

<script>
    $('#note-chkbox').change(function() {
        if (this.checked) {
            $('#note-area').show();
        } else {
            $('#note-area').hide();
        }
    });
    $('#manage-settings').submit(function(e) {
        e.preventDefault()
        start_load()
        $.ajax({
            url: 'ajax.php?action=save_ticket_setting',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            error: err => {
                console.log(err)
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast('Data successfully saved.', 'success')
                    end_load()
                    console.log(resp)
                }
            }
        })

    })
</script>