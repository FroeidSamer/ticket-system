<?php
include('db_connect.php');
if (isset($_GET['id'])) {
    $user = $conn->query("SELECT * FROM users where id =" . $_GET['id']);
    foreach ($user->fetch_array() as $k => $v) {
        $meta[$k] = $v;
    }
    $query = $conn->query("SELECT * FROM user_permissions where user_id =" . $_GET['id']);
    $t_list = [];
    if ($query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            $t_list[] = $row['transaction_id'];
        }
    }
}
?>
<style>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
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
        height: 26px;
        width: 26px;
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
<div class="container-fluid">
    <div id="msg"></div>

    <div id="manage-user">
        <input type="hidden" name="id" id="uid" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">
        <div class="form-group">
            <label><?= tr('transfer') ?></label>
            <!-- Rounded switch -->
            <label class="switch">
                <?php if ($meta['transfer'] == 'yes') : ?>
                    <input type="checkbox" class="chk_box" name="checkbox" checked>
                <?php else : ?>
                    <input type="checkbox" class="chk_box" name="checkbox">

                <?php endif ?>

                <span class="slider round"></span>
            </label>
        </div>
        <table class="table perm-table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col"><?= tr('section') ?></th>
                    <th scope="col"><?= tr('onOff') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = $conn->query("SELECT * FROM transactions order by name asc");
                while ($row = $query->fetch_assoc()) :
                ?>
                    <tr>
                        <td><?php echo $row['name'] ?></td>
                        <td>
                            <label class="switch">
                                <?php if (in_array($row['id'], $t_list)) : ?>
                                    <input type="checkbox" class="window_chk_box" data-id="<?= $row['id'] ?>" name="wchkbox" checked>
                                <?php else : ?>
                                    <input type="checkbox" class="window_chk_box" data-id="<?= $row['id'] ?>" name="wchkbox">

                                <?php endif ?>

                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


    </div>

</div>
<script>
    var user_id = document.getElementById('uid').value;
    var checkbox = document.querySelector("input[name=checkbox]");
    if ($('input.chk_box').is(':checked')) {
        $('.perm-table').show();
    } else {
        $('.perm-table').hide();
    }
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            $.ajax({
                url: 'ajax.php?action=set_transfer',
                method: 'POST',
                data: {
                    id: user_id,
                    val: 'yes'
                },
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("Data successfully saved", 'success')
                    } else {
                        $('#msg').html('<div class="alert alert-danger">There was an error</div>')
                        end_load()
                    }
                }
            })
            $('.perm-table').show();
        } else {
            $.ajax({
                url: 'ajax.php?action=set_transfer',
                method: 'POST',
                data: {
                    id: user_id,
                    val: 'no'
                },
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("Data successfully saved", 'success')
                    } else {
                        $('#msg').html('<div class="alert alert-danger">There was an error</div>')
                        end_load()
                    }
                }
            })
            $('.perm-table').hide();
        }
    });

    $('.window_chk_box').click(function() {
        if ($(this).is(':checked')) {
            var valu = $(this).attr('data-id')
            $.ajax({
                url: 'ajax.php?action=set_permissions',
                method: 'POST',
                data: {
                    id: user_id,
                    val: valu,
                    act: 'add'
                },
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("Data successfully saved", 'success')
                    } else {
                        $('#msg').html('<div class="alert alert-danger">There was an error</div>')
                        end_load()
                    }
                }
            })
        } else {
            var valu = $(this).attr('data-id')
            $.ajax({
                url: 'ajax.php?action=set_permissions',
                method: 'POST',
                data: {
                    id: user_id,
                    val: valu,
                    act: 'remove'
                },
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("Data successfully saved", 'success')
                    } else {
                        $('#msg').html('<div class="alert alert-danger">There was an error</div>')
                        end_load()
                    }
                }
            })
        }
    });
</script>