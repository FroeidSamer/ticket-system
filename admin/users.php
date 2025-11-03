<div class="row">
    <div class="col-lg-12">
        <button class="btn btn-primary float-right btn-sm" id="new_user"><i class="fa fa-plus"></i> <?= tr('addUser') ?></button>
    </div>
</div>
<br>
<div class="row">
    <div class="card col-lg-12">
        <div class="card-body">
            <table class="table-striped table-bordered col-md-12">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center"><?= tr('name') ?>
                        </th>
                        <th class="text-center"><?= tr('window') ?></th>
                        <th class="text-center"><?= tr('username') ?></th>
                        <th class="text-center"><?= tr('options') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'db_connect.php';
                    $query = $conn->query("
                        SELECT 
                            w.*, 
                            GROUP_CONCAT(t.name SEPARATOR ', ') AS tnames
                        FROM 
                            transaction_windows w 
                        LEFT JOIN 
                            transactions t 
                                    ON (
                                        (w.transaction_ids IS NOT NULL AND FIND_IN_SET(t.id, w.transaction_ids)) 
                                        OR (w.transaction_ids IS NULL AND t.id = w.transaction_id)
                                    )
                        GROUP BY 
                            w.id 
                        ORDER BY 
                            w.name ASC
                    ");

                    $window = [];
                    while ($row = $query->fetch_assoc()) :
                        $window[$row['id']] = ucwords($row['tnames'] . ' ' . $row['name']);
                    endwhile;
                    $users = $conn->query("SELECT * FROM users u order by name asc");
                    $i = 1;
                    while ($row = $users->fetch_assoc()) :
                    ?>
                        <tr>
                            <td class="text-center">
                                <?php echo $i++ ?>
                            </td>
                            <td>
                                <?php echo ucwords($row['name']) ?>
                            </td>
                            <td>
                                <?php echo isset($window[$row['window_id']]) ? $window[$row['window_id']] : "N/A" ?>
                            </td>
                            <td>
                                <?php echo $row['username'] ?>
                            </td>
                            <td>
                                <center>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary"><?= tr('options') ?></button>
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'><?= tr('edit') ?></a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'><?= tr('delete') ?></a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item perm_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'><?= tr('permissions') ?></a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="index.php?page=user_statics_v2&id=<?php echo $row['id'] ?>" data-id='<?php echo $row['id'] ?>'><?= tr('statistics') ?></a>
                                        </div>
                                    </div>
                                </center>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    $('table').dataTable();
    $('#new_user').click(function() {
        uni_modal('New User', 'manage_user.php')
    })
    $('.edit_user').click(function() {
        uni_modal('Edit User', 'manage_user.php?id=' + $(this).attr('data-id'))
    })
    $('.perm_user').click(function() {
        show_modal('User Permissions', 'user_permissons.php?id=' + $(this).attr('data-id'))
    })
    $('.delete_user').click(function() {
        _conf("Are you sure to delete this user?", "delete_user", [$(this).attr('data-id')])
    })
    $('.stats_user').click(function() {
        show_modal('User Statistics', 'user_statistics.php?id=' + $(this).attr('data-id'))
    })

    function delete_user($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_user',
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