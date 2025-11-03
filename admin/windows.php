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
</style>
<div class="col-lg-12">
    <div class="row">
        <!-- FORM Panel -->
        <div class="col-md-4">
            <form action="" id="manage-window">
                <div class="card">
                    <div class="card-header">
                        <?= tr('addEdit') ?>
                    </div>
                    <div class="card-body">
                        <div id="msg"></div>
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label"><?= tr('sections') ?></label>
                            <select name="transaction_ids[]" id="transaction_ids" class="custom-select browser-default select2" multiple="multiple" require>
                                <?php
                                $trans = $conn->query("SELECT * FROM transactions where status = 1 order by name asc");
                                while ($row = $trans->fetch_assoc()) :
                                ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= tr('name') ?></label>
                            <textarea name="name" id="" cols="30" rows="2" class="form-control" require></textarea>
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
                                <th class="text-center"><?= tr('sections') ?></th>
                                <th class="text-center"><?= tr('window') ?></th>
                                <th class="text-center"><?= tr('options') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $types = $conn->query("SELECT 
                                                        w.*, 
                                                        GROUP_CONCAT(t.name SEPARATOR ', ') AS tnames, 
                                                        GROUP_CONCAT(t.id SEPARATOR ',') AS transaction_ids 
                                                    FROM 
                                                        transaction_windows w 
                                                    LEFT JOIN 
                                                        transactions t 
                                                        ON (
                                                            (w.transaction_ids IS NOT NULL AND FIND_IN_SET(t.id, w.transaction_ids)) 
                                                            OR (w.transaction_ids IS NULL AND t.id = w.transaction_id)
                                                        )
                                                    WHERE 
                                                        w.status = 1 
                                                    GROUP BY 
                                                        w.id 
                                                    ORDER BY 
                                                        w.name ASC");
                            while ($row = $types->fetch_assoc()) :
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td class="">
                                        <p> <b><?php echo $row['tnames'] ?></b></p>
                                    </td>
                                    <td class="">
                                        <p> <b><?php echo $row['name'] ?></b></p>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_window" type="button" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" data-transaction_ids="<?php echo $row['transaction_ids'] ?>"><?= tr('edit') ?></button>
                                        <button class="btn btn-sm btn-danger delete_window" type="button" data-id="<?php echo $row['id'] ?>"><?= tr('delete') ?></button>
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


<script src="vendor/jquery/jquery.min.js"></script>

<script>
    function _reset() {
        $('[name="id"]').val('');
        $('#msg').html('')
        $('#manage-window').get(0).reset();
        $('.select2').trigger("change")
    }

    $('#manage-window').submit(function(e) {
        e.preventDefault()
        start_load()
        $('#msg').html('')
        $.ajax({
            url: 'ajax.php?action=save_window',
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
                    $('#msg').html("<div class='alert alert-danger'>Name already exist.</div>")
                    end_load()

                }
            }
        })
    })
    
    $('.edit_window').click(function() {
        start_load()
        var cat = $('#manage-window')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='name']").val($(this).attr('data-name'))
        
        // Handle multiple transaction IDs
        var transactionIds = $(this).attr('data-transaction_ids').split(',');
        cat.find("[name='transaction_ids[]']").val(transactionIds)
        $('.select2').trigger("change")
        end_load()
    })
    
    $('.delete_window').click(function() {
        _conf("Are you sure to delete this window type?", "delete_window", [$(this).attr('data-id')])
    })

    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#cimg').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function delete_window($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_window',
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

                }
            }
        })
    }
</script>