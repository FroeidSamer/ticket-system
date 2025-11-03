<?php
$qry = $conn->query("SELECT * FROM status ORDER BY ordering");
?>

<div class="card col-lg-12">
    <div class="card-header">
        <h4>Status Management</h4>
        <button class="btn btn-sm btn-primary float-right" id="add_new_status">Add New Status</button>
    </div>
    <div class="card-body">
        <form action="" id="manage-settings">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Order</th>
                        <th>Color</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $qry->fetch_array()): ?>
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="type[<?= $row['id'] ?>]" value="<?= $row['type'] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control" name="ordering[<?= $row['id'] ?>]" value="<?= $row['ordering'] ?>">
                            </td>
                            <td>
                                <input type="color" class="form-control form-control-color" name="color[<?= $row['id'] ?>]" value="<?= $row['color'] ?>">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-status" data-id="<?= $row['id'] ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div id="new_status_fields"></div>
            <center>
                <button class="btn btn-info btn-primary btn-block col-md-2"><?= tr('save') ?></button>
            </center>
        </form>
    </div>
</div>

<!-- Modal for adding new status -->
<div class="modal fade" id="status_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="status-form">
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <label for="type">Status Type</label>
                        <input type="text" class="form-control" name="type" required>
                    </div>
                    <div class="form-group">
                        <label for="ordering">Order</label>
                        <input type="number" class="form-control" name="ordering" required>
                    </div>
                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="color" class="form-control form-control-color" name="color" value="#2196F3" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save_status">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Add new status button click
        $('#add_new_status').click(function() {
            $('#status-form')[0].reset();
            $('#status-form input[name="id"]').val('');
            $('#status_modal .modal-title').text('Add New Status');
            $('#status_modal').modal('show');
        });

        // Save status (both new and edit)
        $('#save_status').click(function() {
            var formData = new FormData($('#status-form')[0]);
            var action = $('#status-form input[name="id"]').val() ? 'update_status' : 'add_status';

            start_load();
            $.ajax({
                url: 'ajax.php?action=' + action,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast('Status successfully saved.', 'success');
                        $('#status_modal').modal('hide');
                        location.reload();
                    } else {
                        alert_toast('Error: ' + resp, 'error');
                        end_load();
                    }
                },
                error: function(err) {
                    console.log(err);
                    alert_toast('An error occurred.', 'error');
                    end_load();
                }
            });
        });

        // Remove status
        $('.remove-status').click(function() {
            var id = $(this).attr('data-id');
            _conf("Are you sure to delete this status?", "delete_status", [id]);
        });

        // Save all statuses
        $('#manage-settings').submit(function(e) {
            e.preventDefault();
            start_load();
            $.ajax({
                url: 'ajax.php?action=update_statuses',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast('Statuses successfully updated.', 'success');
                    } else {
                        alert_toast('Error: ' + resp, 'error');
                    }
                    end_load();
                },
                error: function(err) {
                    console.log(err);
                    alert_toast('An error occurred.', 'error');
                    end_load();
                }
            });
        });
    });

    function delete_status(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_status',
            method: 'POST',
            data: {
                id: id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Status successfully deleted", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert_toast("Error: " + resp, 'error');
                    end_load();
                }
            },
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_load();
            }
        });
    }
</script>