<!-- Begin Page Content -->
<style>
    td {
        vertical-align: middle !important;
    }

    td p {
        margin: unset
    }

    img {
        max-width: 100px;
        max-height: 150px;
    }

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
<div class="col-lg-12">
    <div class="row">
        <!-- FORM Panel -->
        <div class="col-md-4">
            <form action="" id="manage-transaction">
                <div class="card text-center">
                    <div class="card-header">
                        <?= tr('addEditSection') ?>
                    </div>
                    <div class="card-body">
                        <div id="msg"></div>
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label"><?= tr('sectionName') ?></label>
                            <textarea name="name" id="" cols="30" rows="2" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= tr('symbol') ?></label>
                            <input type="text" name="symbol" id="symbol" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= tr('type') ?></label>
                            <select name="type" class="form-control" required>
                                <option value=""><?= tr('select_type') ?></option>
                                <option value="sorting">فرز</option>
                                <option value="doctor">طبيب</option>
                                <option value="notes">ملاحظة</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <label class="switch">
                                <input type="checkbox" class="chk_box" name="priority">
                                <span class="slider round"></span>
                            </label>
                            <span class="ml-2"><?= tr('priority') ?></span>
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

        <!-- Table Panel -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center"><?= tr('sectionName') ?></th>
                                <th class="text-center"><?= tr('type') ?></th>
                                <th class="text-center"><?= tr('symbol') ?></th>
                                <th class="text-center"><?= tr('options') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $types = $conn->query("SELECT * FROM transactions where status = 1 order by id asc");
                            while ($row = $types->fetch_assoc()) :
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td class="">
                                        <p> <b><?php echo $row['name'] ?></b></p>
                                    </td>
                                    <td class="text-center">
                                        <?php switch ($row['type']) {
                                            case 'sorting':
                                                echo 'فرز';
                                                break;
                                            case 'doctor':
                                                echo 'طبيب';
                                                break;
                                            case 'notes':
                                                echo 'ملاحظة';
                                                break;
                                            default:
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $row['symbol'] ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_transaction" type="button"
                                            data-id="<?php echo $row['id'] ?>"
                                            data-name="<?php echo $row['name'] ?>"
                                            data-priority="<?php echo $row['priority'] ?>"
                                            data-symbol="<?php echo $row['symbol'] ?>"
                                            data-type="<?php echo $row['type'] ?>">
                                            <?= tr('edit') ?>
                                        </button>
                                        <?php if ($row['active'] == 'on') : ?>
                                            <button class=" btn btn-sm btn-success enable_transaction" type="button" data-id="<?php echo $row['id'] ?>"><?= tr('show') ?></button>
                                        <?php else : ?>
                                            <button class="btn btn-sm btn-secondary enable_transaction" type="button" data-id="<?php echo $row['id'] ?>"><?= tr('hide') ?></button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-danger delete_transaction" type="button" data-id="<?php echo $row['id'] ?>"><?= tr('delete') ?></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Table Panel -->
    </div>
</div>

<script>
    function _reset() {
        $('[name="id"]').val('');
        $('#msg').html('')
        $('#manage-transaction').get(0).reset();
    }

    $('#manage-transaction').submit(function(e) {
        e.preventDefault()
        start_load()
        $('#msg').html('')

        // Validate type
        var type = $('[name="type"]').val();
        if (!['sorting', 'doctor', 'notes'].includes(type)) {
            $('#msg').html("<div class='alert alert-danger'>Please select a valid type (فرز, طبيب, ملاحظة)</div>")
            end_load()
            return false;
        }

        $.ajax({
            url: 'ajax.php?action=save_transaction',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully added", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)
                } else if (resp == 2) {
                    $('#msg').html("<div class='alert alert-danger'>Name or Symbol already exist.</div>")
                    end_load()
                } else {
                    $('#msg').html("<div class='alert alert-danger'>Error: " + resp + "</div>")
                    end_load()
                }
            },
            error: function(xhr, status, error) {
                $('#msg').html("<div class='alert alert-danger'>AJAX Error: " + error + "</div>")
                end_load()
            }
        })
    })

    $('.edit_transaction').click(function() {
        start_load()
        var cat = $('#manage-transaction')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='name']").val($(this).attr('data-name'))
        cat.find("[name='symbol']").val($(this).attr('data-symbol'))
        cat.find("[name='type']").val($(this).attr('data-type'))
        if ($(this).attr('data-priority') == 'on') {
            cat.find("[name='priority']").prop('checked', true);
        } else {
            cat.find("[name='priority']").prop('checked', false);
        }
        end_load()
    })

    $('.delete_transaction').click(function() {
        _conf("Are you sure to delete this transaction type?", "delete_transaction", [$(this).attr('data-id')])
    })

    $('.enable_transaction').click(function() {
        start_load()
        var id = $(this).attr('data-id');
        $.ajax({
            url: 'ajax.php?action=enable_transaction',
            method: 'POST',
            data: {
                id: id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Status successfully updated", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)
                } else {
                    alert_toast("Error: " + resp, 'error')
                    end_load()
                }
            },
            error: function(xhr, status, error) {
                alert_toast("AJAX Error: " + error, 'error')
                end_load()
            }
        })
    })

    function delete_transaction($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_transaction',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)
                } else {
                    alert_toast("Error: " + resp, 'error')
                    end_load()
                }
            },
            error: function(xhr, status, error) {
                alert_toast("AJAX Error: " + error, 'error')
                end_load()
            }
        })
    }
</script>