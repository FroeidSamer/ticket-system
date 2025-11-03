<?php
$stmt = $conn->query("SELECT 
    tw.id AS window_id,
    tw.transaction_id AS window_transaction_id,
    tw.transaction_ids AS window_transaction_ids,
    tw.name AS window_name,
    tw.status AS window_status,
    tr.name AS transaction_name,
    tr.type AS transaction_type
FROM transaction_windows tw 
INNER JOIN transactions tr ON tw.transaction_ids = tr.id 
WHERE tw.id = " . (int)$_SESSION['login_window_id']);

$result = $stmt->fetch_assoc();
// var_dump($result);
// echo $result['window_id'];
// echo $result['window_transaction_id'];
// echo $result['transaction_name'];
$tr_type = $result['transaction_type'];
$qry = $conn->query("SELECT * from settings limit 1");
if ($qry->num_rows > 0) {
    foreach ($qry->fetch_array() as $k => $val) {
        $meta[$k] = $val;
    }
}
?>
<style>
    .t-list {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .widthfit {
        width: fit-content;
        height: fit-content;
    }

    table th,
    tr {
        font-size: 17px;
        font-weight: 900;
    }

    #custom_queue_id {
        border-radius: 15px;
        outline: none;
        border: 1px solid blue;
        padding: 5px;
        display: block;
        margin: 10px auto;
    }



    #nextBtn {
        position: relative;
        min-width: 100px;
        /* Ensure consistent width */
    }

    #nextBtn .timer {
        margin-left: 5px;
        font-size: 0.9em;
    }

    #nextBtn[disabled] {
        opacity: 0.7;
        cursor: not-allowed;
        background-color: #6c757d;
        /* Gray color when disabled */
        border-color: #6c757d;
    }
</style>
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= tr('dashboard') ?></h1>
</div>


<?php if ($_SESSION['login_type'] == 2) : ?>
    <script>
        $('.type-btn').each(function(i, obj) {
            obj.disabled = true;
        });

        function queueNow() {
            var period = <?php echo isset($meta['period']) ? (int)$meta['period'] : 30; ?>;
            var nextBtn = $('#nextBtn');
            var timerSpan = nextBtn.find('.timer');

            nextBtn.prop('disabled', true);
            timerSpan.show();

            var secondsLeft = period;
            updateTimerDisplay();

            var timerInterval = setInterval(function() {
                secondsLeft--;
                updateTimerDisplay();

                if (secondsLeft <= 0) {
                    clearInterval(timerInterval);
                    nextBtn.prop('disabled', false);
                    timerSpan.hide().text('');
                }
            }, 1000);

            function updateTimerDisplay() {
                timerSpan.text(' (' + secondsLeft + 's)');
            }

            var qid = $('#queue_id').val();
            var typeId = $('#queue_id').attr('data-typeid');

            if (qid != null && qid != '') {
                if (typeId == '' || typeId == null) {
                    alert_toast("قم يتصنيف الحالة أولاً", 'warning');
                    return false;
                }

                $.ajax({
                    url: 'ajax.php?action=update_queue',
                    method: 'POST',
                    data: {
                        previous_ticket_id: qid
                    },
                    success: function(resp) {
                        resp = JSON.parse(resp)
                        if (resp.status === 1) {
                            $('#sname').html(resp.data.name)
                            $('#squeue').html(resp.data.tsymbol + resp.data.queue_no)
                            $('#window').html(resp.data.wname)
                            $('#queue_id').val(resp.data.id)
                            $('#queue_id').attr('data-symbol', resp.data.type)
                            $('#queue_id').attr('data-typeid', resp.data.type_id)
                            $('#queue_id').attr('data-qno', resp.data.queue_no)

                            $('.type-btn').each(function(i, obj) {
                                obj.disabled = false;
                            });
                            update_tables();
                            update_waiting_review_tables()
                        } else {
                            $('#sname').html('')
                            $('#squeue').html('')
                            $('#window').html('')
                            $('#queue_id').val('')
                            $('#queue_id').attr('data-symbol', '')
                            $('#queue_id').attr('data-typeid', '')
                            $('#queue_id').attr('data-qno', '')
                            update_tables();
                            update_waiting_review_tables()
                        }
                    }
                })
            } else {
                $.ajax({
                    url: 'ajax.php?action=update_queue',
                    method: 'POST',
                    data: {
                        previous_ticket_id: qid
                    },
                    success: function(resp) {
                        resp = JSON.parse(resp)
                        if (resp.status === 1) {
                            $('#sname').html(resp.data.name)
                            $('#squeue').html(resp.data.tsymbol + resp.data.queue_no)
                            $('#window').html(resp.data.wname)
                            $('#queue_id').val(resp.data.id)
                            $('#queue_id').attr('data-symbol', resp.data.type)
                            $('#queue_id').attr('data-typeid', resp.data.type_id)
                            $('#queue_id').attr('data-qno', resp.data.queue_no)

                            $('.type-btn').each(function(i, obj) {
                                obj.disabled = false;
                            });
                            update_tables();
                            update_waiting_review_tables()
                        } else {
                            $('#sname').html('')
                            $('#squeue').html('')
                            $('#window').html('')
                            $('#queue_id').val('')
                            $('#queue_id').attr('data-symbol', '')
                            $('#queue_id').attr('data-typeid', '')
                            $('#queue_id').attr('data-qno', '')
                            update_tables();
                            update_waiting_review_tables()
                        }
                    }
                })
            }
        }

        function customQueue() {
            $.ajax({
                url: 'ajax.php?action=custom_queue_all',
                method: 'POST',
                data: {
                    customqueueid: $('#custom_queue_id').val()
                },
                success: function(resp) {
                    resp = JSON.parse(resp)
                    if (resp.status === 1) {
                        $('#sname').html(resp.data.name)
                        $('#squeue').html(resp.data.type + resp.data.queue_no)
                        $('#window').html(resp.data.wname)
                        $('#queue_id').val(resp.data.id)
                        $('#queue_id').attr('data-symbol', resp.data.type)
                        $('#queue_id').attr('data-typeid', resp.data.type_id)
                        $('#queue_id').attr('data-qno', resp.data.queue_no)

                        $('.type-btn').each(function(i, obj) {
                            obj.disabled = false;
                        });
                        update_tables();
                    } else {
                        $('#sname').html('')
                        $('#squeue').html('')
                        $('#window').html('')
                        $('#queue_id').val('')
                        $('#queue_id').attr('data-symbol', '')
                        $('#queue_id').attr('data-typeid', '')
                        $('#queue_id').attr('data-qno', '')
                        update_tables();
                    }
                }
            })
        }
    </script>
    <div class="row justify-content-center">


        <div class="col-md-4  text-center">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center"><b><?= tr('nowServing') ?></b></h3>
                </div>
                <div class="card-body">
                    <h4 class="text-center" id="sname"></h4>
                    <hr class="divider">
                    <h3 class="text-center" id="squeue"></h3>
                    <hr class="divider">
                    <h5 class="text-center" id="window"></h5>
                    <input type="text" id="queue_id" data-symbol="" data-typeid="" data-qno="" value="" hidden>
                    <button class="btn btn-primary" data-ttype="<?= $ttype ?>" onclick="queueNow()" id="nextBtn">
                        <?= tr('next') ?> <span class="timer" style="display:none;"></span>
                    </button> <a href="javascript:void(0)" class="btn btn-primary" onclick="recallQueue()"><?= tr('recall') ?></a>

                </div>
            </div>
        </div>
        <?php if ($tr_type == 'sorting') : ?>

            <div class="col-md-3  text-center">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-center"><b><?= tr('PatientStatue') ?></b></h4>
                    </div>
                    <div class="card-body t-list">
                        <?php
                        $qry = $conn->query("SELECT * from status");
                        ?>
                        <?php while ($row = $qry->fetch_array()) : ?>
                            <button type="button" class="btn btn-outline-success type-btn" style="background-color: <?= $row['color'] ?>; color:black;" data-id="<?= $row['id'] ?>"><?= $row['type'] . ' - ' . $row['ordering'] ?></button>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <hr>

    <div class="row" style="flex-direction: column;">


        <div class="row d-flex justify-content-center">
            <div class="col-md-5  text-center">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center"><b><?= tr('custom_call') ?></b></h3>
                    </div>
                    <div class="card-body">

                        <input type="text" id="custom_queue_id" value="" placeholder="<?= tr('enter_card') ?>">
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="customQueue()"><?= tr('call') ?></a>

                        <a href="javascript:void(0)" class="btn btn-primary" onclick="recallQueue()"><?= tr('recall') ?></a>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 text-center d-flex" style="gap: 5px;">

                <table class="table  table-bordered table-hover w-100">
                    <thead>
                        <th style="background: turquoise;"><?= tr('waiting') ?></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="waiting-num"></td>
                        </tr>

                    </tbody>
                </table>

            </div>
            <div class="col-md-8 text-center">
                <div class="card  w-100">
                    <div class="card-header d-flex justify-content-between bg-primary text-white">
                        <a href="javascript:void(0)" class="btn btn-info" onclick="update_all()"><?= tr('update') ?></a>

                        <h5 class="text-center"><b><?= tr('waitForExamination') ?></b></h5>

                    </div>
                    <div class="card-body p-0">
                        <h4></h4>
                        <table class="table wait-review table-bordered table-hover w-100" style="direction : rtl; margin: 0 auto;">
                            <thead>
                                <th><i class="fas fa-home"></i></th>
                                <th><?= tr('ticketNo') ?></th>
                                <th><?= tr('statue') ?></th>
                                <th><?= tr('waitingTime') ?></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

<?php endif; ?>




<script>
    $('.type-btn').click(function() {
        var typeId = $(this).attr('data-id');
        var qid = $('#queue_id').val();
        if (qid != null && qid != '') {

            $.ajax({
                url: 'ajax.php?action=update_queue_statue',
                method: 'POST',
                data: {
                    val: qid,
                    typeid: typeId,
                },
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("تم التصنيف", 'success');
                        $('#queue_id').attr('data-typeid', typeId);
                        $('.type-btn').each(function(i, obj) {
                            obj.disabled = true;
                        });
                        if (qid != null && qid != '') {
                            var qsymbol = $('#queue_id').attr('data-typeid');
                            var qno = $('#queue_id').attr('data-qno');
                            $.ajax({
                                url: 'ajax.php?action=transfer_queue',
                                method: 'POST',
                                data: {
                                    to: 'doctor',
                                    val: qid,
                                    typeid: qsymbol,
                                    qnumber: qno
                                },
                                success: function(resp) {
                                    if (resp == 1) {
                                        alert_toast("تم التحويل", 'success')
                                        $('.type-btn').each(function(i, obj) {
                                            obj.disabled = true;
                                        });
                                        update_tables();
                                        update_waiting_review_tables()
                                        update_tables();
                                    } else {
                                        $('#msg').html('<div class="alert alert-danger">There was an error</div>')
                                        end_load()
                                    }
                                }
                            })
                        }
                    } else {
                        $('#msg').html('<div class="alert alert-danger">There was an error</div>')
                        end_load()
                    }
                }
            })
        }

    })

    function recallQueue() {
        var qid = $('#queue_id').val();

        if (qid != null && qid != '') {

            $.ajax({
                url: 'ajax.php?action=recall_queue',
                method: 'POST',
                data: {
                    val: qid,
                },
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("تم إعادة النداء", 'success');

                    } else {
                        $('#msg').html('<div class="alert alert-danger">There was an error</div>')
                    }
                }
            })
        }
    }

    function update_tables() {
        $.ajax({
            url: 'ajax.php?action=get_staff_info',
            method: 'POST',
            success: function(resp) {
                resp = JSON.parse(resp);

                const waiting_num = document.querySelector('#waiting-num');
                waiting_num.innerHTML = resp.waiting;
            }
        })
    }
    update_tables();

    //other staff

    function callQueue(event) {
        var old_qid = $('#queue_id').val();
        var qid = $(event).attr('data-id');
        $.ajax({
            url: 'ajax.php?action=call_queue',
            method: 'POST',
            data: {
                queueId: qid,
                old_qid: old_qid
            },
            success: function(resp) {
                alert_toast("تم النداء", 'success');
                resp = JSON.parse(resp)
                $('#sname').html(resp.data.name)
                $('#squeue').html(resp.data.type + resp.data.queue_no)
                $('#window').html(resp.data.wname)
                $('#queue_id').val(resp.data.id)
                $('#queue_id').attr('data-symbol', resp.data.type)
                $('#queue_id').attr('data-typeid', resp.data.type_id)
                $('#queue_id').attr('data-qno', resp.data.queue_no)
                $('.type-btn').each(function(i, obj) {
                    obj.disabled = false;
                });
                update_tables();
                update_waiting_review_tables();

            }
        })
    }

    function update_waiting_review_tables() {
        $.ajax({
            url: 'ajax.php?action=get_staff_info_waiting',
            method: 'POST',
            success: function(resp) {
                resp = JSON.parse(resp);

                const table = document.querySelector('.wait-review');
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';

                for (let id in resp.data) {
                    let info = resp.data[id];

                    let queueId = info.id;
                    let queueNo = info.queue_no;
                    let typeId = info.type; // Changed from 'type' to 'type_id' to match your response
                    let typeColor = info.type_color; // Get the color from response
                    let createdTimestamp = info.created_timestamp;

                    const row = document.createElement('tr');

                    // Call button cell
                    const queueCall = document.createElement('td');
                    var button = document.createElement("button");
                    button.setAttribute("data-id", queueId);
                    button.innerHTML = "<?= tr('call') ?>";
                    button.className = "btn";
                    button.setAttribute("onclick", "callQueue(this)");
                    queueCall.appendChild(button);

                    // Queue number cell
                    const queueNoCell = document.createElement('td');
                    queueNoCell.textContent = queueNo;

                    // Type ID cell - now with color styling
                    const typeIdCell = document.createElement('td');
                    typeIdCell.textContent = typeId;
                    if (typeColor) {
                        typeIdCell.style.backgroundColor = typeColor;
                        typeIdCell.style.color = getContrastColor(typeColor);
                        typeIdCell.style.padding = '5px';
                        typeIdCell.style.borderRadius = '3px';
                        typeIdCell.style.textAlign = 'center';
                    }

                    const timeCell = document.createElement('td');
                    const time = info.waiting_time;
                    timeCell.textContent = time;

                    row.appendChild(queueCall);
                    row.appendChild(queueNoCell);
                    row.appendChild(typeIdCell);
                    row.appendChild(timeCell);

                    tbody.appendChild(row);
                }
            }
        });
    }

    function getContrastColor(hexColor) {
        const r = parseInt(hexColor.substr(1, 2), 16);
        const g = parseInt(hexColor.substr(3, 2), 16);
        const b = parseInt(hexColor.substr(5, 2), 16);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        return brightness > 128 ? '#000000' : '#ffffff';
    }

    update_waiting_review_tables();

    function update_all() {
        update_tables();
        update_waiting_review_tables();
    }

    setInterval(() => {
        update_all()
    }, 5000);
</script>