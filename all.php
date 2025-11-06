<?php
include "admin/db_connect.php";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>شاشة الطابور</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        :root {
            --blue-dark: #003a77;
            --blue-soft: #e9f4ff;
            --accent: #1e64cc;
        }

        body {
            margin: 0;
            font-family: "Cairo", sans-serif;
            background: #fff;
            color: #111;
        }

        .full-container {
            display: flex;
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
        }

        /* الجدول على اليمين */
        .left-side {
            width: 58%;
            padding: 20px;
            background: var(--blue-soft);
            overflow: auto;
            order: 2;
        }

        .title-box {
            text-align: right;
            margin-bottom: 12px;
        }

        .title-box h3 {
            margin: 0;
            color: var(--blue-dark);
            font-size: 28px;
            font-weight: 800;
        }

        .queue-table-box {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        table.queue-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            direction: rtl;
        }

        table.queue-table thead th {
            background: var(--accent);
            color: #fff;
            padding: 12px 10px;
            font-size: 18px;
            font-weight: 700;
        }

        table.queue-table tbody td {
            padding: 14px 8px;
            font-size: 20px;
            border-bottom: 1px solid #eef4fb;
        }

        table.queue-table tbody tr:nth-child(even) {
            background: #fbfdff;
        }

        .clinic-name {
            text-align: right;
            font-weight: 700;
        }

        /* اللوجو + السلايدر على اليسار */
        .right-side {
            width: 42%;
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            box-sizing: border-box;
            order: 1;
        }

        .company {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        #company_image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        #company_title {
            font-size: 30px;
            color: var(--blue-dark);
            font-weight: 800;
            text-align: left;
        }

        .slideShow {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 90px;
        }

        .slideShow img,
        .slideShow video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* تحسين الاستجابة للشاشات الصغيرة */
        @media(max-width:1200px) {
            .left-side {
                width: 40%;
            }

            .right-side {
                width: 60%;
            }
        }

        @media(max-width:900px) {
            .full-container {
                flex-direction: column;
                height: auto;
            }

            .left-side,
            .right-side {
                width: 100%;
                order: unset;
            }

            .slideShow {
                width: 100%;
                height: 220px;
            }

            #company_image {
                width: 120px;
                height: 120px;
            }

            #company_title {
                font-size: 24px;
            }

            table.queue-table tbody td {
                font-size: 18px;
                padding: 12px 6px;
            }

            table.queue-table thead th {
                font-size: 16px;
                padding: 10px 5px;
            }
        }

        @media(max-width:600px) {
            .slideShow {
                height: 180px;
            }

            #company_image {
                width: 100px;
                height: 100px;
            }

            #company_title {
                font-size: 20px;
            }

            table.queue-table tbody td {
                font-size: 16px;
                padding: 10px 4px;
            }

            table.queue-table thead th {
                font-size: 14px;
                padding: 8px 4px;
            }
        }

        .highlight-new {
            animation: highlightFade 2s ease-in-out;
        }

        @keyframes highlightFade {
            0% {
                background-color: #4CAF50;
                transform: scale(1.02);
            }

            50% {
                background-color: #8BC34A;
            }

            100% {
                background-color: transparent;
                transform: scale(1);
            }
        }

        .queue-row {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>

    <?php
    $tw_res = $conn->query("SELECT 
                            w.id, 
                            w.name,
                            w.transaction_ids AS w_transaction_ids, -- العمود الأصلي من الجدول
                            GROUP_CONCAT(t.name SEPARATOR ', ') AS tnames, 
                            GROUP_CONCAT(t.id SEPARATOR ',') AS calculated_transaction_ids 
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

    $windows = [];
    if ($tw_res) {
        while ($r = $tw_res->fetch_assoc()) {
            // نستخدم 'calculated_transaction_ids' إذا كانت موجودة، وإلا نستخدم العمود الأصلي
            $r['final_tids'] = !empty($r['calculated_transaction_ids']) ? $r['calculated_transaction_ids'] : $r['w_transaction_ids'];
            $windows[] = $r;
        }
    }

    $uploads = $conn->query("SELECT * FROM file_uploads ORDER BY rand()");
    $slides = [];
    while ($row = $uploads->fetch_assoc()) {
        $slides[] = $row['file_path'];
    }

    $company_image = isset($_SESSION['setting_image']) ? 'admin/assets/img/' . $_SESSION['setting_image'] : 'admin/assets/img/logo.jpg';
    $company_title = isset($_SESSION['setting_name']) ? $_SESSION['setting_name'] : 'Transaction Queuing System';
    ?>

    <div class="full-container">

        <!-- جدول الطوابير -->
        <div class="left-side">
            <div class="title-box">
                <h3>يتم الآن خدمة أصحاب الأدوار</h3>
            </div>
            <div class="queue-table-box">
                <table class="queue-table">
                    <thead>
                        <tr>
                            <th>اسـم العيـــادة</th>
                            <th>رقـم الـــدور</th>
                            <th>النــــوع</th>
                            <th>الغـــرفــــة</th>
                        </tr>
                    </thead>
                    <tbody id="queue-tbody">
                        <?php foreach ($windows as $w): ?>
                            <?php
                            $clinic_names = !empty($w['tnames']) ? $w['tnames'] : '-';
                            $window_name  = !empty($w['name']) ? $w['name'] : '-';
                            $tids         = !empty($w['final_tids']) ? $w['final_tids'] : '';
                            $wid          = $w['id'];
                            ?>
                            <tr class="queue-row"
                                data-wid="<?= htmlspecialchars($wid) ?>"
                                data-tids="<?= htmlspecialchars($tids) ?>"
                                data-original-clinic="<?= htmlspecialchars($clinic_names) ?>">
                                <td class="td-clinic"><?= htmlspecialchars($clinic_names) ?></td>
                                <td class="td-queue">-</td>
                                <td class="td-symbol">-</td> <!-- Will be populated by AJAX -->
                                <td class="td-window"><?= htmlspecialchars($window_name) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

        <!-- اللوجو و السلايدر -->
        <div class="right-side">
            <div class="company">
                <div style="text-align:left;">
                    <div id="company_title"><?= htmlspecialchars($company_title) ?></div>
                </div>
                <img id="company_image" src="<?= htmlspecialchars($company_image) ?>" alt="Logo">
            </div>
            <div class="slideShow" id="slideShow"></div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        var slides = <?php echo json_encode($slides) ?>;
        var scount = slides.length;
        if (scount > 0) {
            $(document).ready(function() {
                render_slides(0)
            })
        }

        function render_slides(k) {
            if (k >= scount)
                k = 0;
            var src = slides[k]
            k++;
            var t = src.split('.');
            var file;
            t = t[1];
            if (t == 'webm' || t == "mp4") {
                file = $("<video id='slide' src='admin/assets/uploads/" + src + "' onended='render_slides(" + k + ")' autoplay='true' muted='muted'></video>");
            } else {
                file = $("<img id='slide' src='admin/assets/uploads/" + src + "' onload='slideInterval(" + k + ")' />");
            }
            //console.log(file)
            if ($('#slide').length > 0) {
                $('#slide').css({
                    "opacity": 0
                });
                setTimeout(function() {
                    $('.slideShow').html('');
                    $('.slideShow').append(file)
                    $('#slide').css({
                        "opacity": 1
                    });
                    if (t == 'webm' || t == "mp4")
                        $('video').trigger('play');


                }, 500)
            } else {
                $('.slideShow').append(file)
                $('#slide').css({
                    "opacity": 1
                });

            }

        }

        function slideInterval(i = 0) {
            setTimeout(function() {
                render_slides(i)
            }, 5000)

        }

        // ========================= START QUEUE LOGIC HERE =========================

        $(document).ready(function() {
            var previousPerRow = {};
            var rows = $('.queue-row');
            var windowData = [];

            // --- 1. Initialization and Data Collection ---
            rows.each(function() {
                var row = $(this);
                var wid = row.data('wid');
                var tids = row.data('tids');

                var originalClinicName = row.data('original-clinic') || row.find('.td-window').text();

                previousPerRow[wid] = {
                    queue_no: '',
                    date_created: 0,
                    tsymbol: '',
                    status_type: '',
                    clinic: originalClinicName,
                    wname: row.find('.td-window').text()
                };

                if (tids) {
                    windowData.push({
                        row: row,
                        wid: wid,
                        tids: tids,
                        originalClinicName: originalClinicName
                    });
                }
            });

            // --- 2. Sorting Logic ---
            function sortRowsByDateCreated() {
                var tbody = $('#queue-tbody');
                var rows = tbody.find('tr').get();

                rows.sort(function(a, b) {
                    var ad = parseInt($(a).data('date_created')) || 0;
                    var bd = parseInt($(b).data('date_created')) || 0;

                    // Primary sort: Date descending (most recent first - higher timestamp = more recent)
                    if (ad !== bd) {
                        return bd - ad;
                    }
                    // Secondary sort: By Window ID ascending
                    var aid = parseInt($(a).data('wid'));
                    var bid = parseInt($(b).data('wid'));
                    return aid - bid;
                });

                // Append sorted rows
                $.each(rows, function(i, row) {
                    tbody.append(row);
                });
            }

            // --- 3. Response Processing Helper ---
            function processResponse(row, wid, originalClinicName, resp) {
                var r;
                try {
                    r = (typeof resp === 'object') ? resp : JSON.parse(resp);
                } catch (e) {
                    console.error("JSON Parse Error for wid:", wid, e);
                    return false;
                }

                var hasValidData = (r.status == 1 && r.data && r.data.queue_no && r.data.queue_no !== '0');
                var saved = previousPerRow[wid];
                var hasChanged = false;

                if (hasValidData) {
                    var tsymbol = r.data.tsymbol || '';
                    var qno = r.data.queue_no || '';
                    var clinic = r.data.clinic_name || r.data.tname || originalClinicName;
                    var wname = r.data.wname || '';

                    // Get النوع from selection column in queue_list table
                    var displayType = r.data.display_type || r.data.queue_selection || '-';

                    // Use the actual database timestamp
                    var timestamp = r.data.timestamp_unix ? parseInt(r.data.timestamp_unix) * 1000 : parseInt(r.data.date_created) * 1000;

                    // Check if this is a NEW call
                    if (saved.queue_no !== qno || saved.date_created !== timestamp) {
                        hasChanged = true;

                        previousPerRow[wid] = {
                            tsymbol: tsymbol,
                            queue_no: qno,
                            clinic: clinic,
                            wname: wname,
                            display_type: displayType,
                            date_created: timestamp
                        };

                        row.find('.td-clinic').text(clinic);
                        row.find('.td-queue').text(tsymbol + ' - ' + qno);
                        row.find('.td-symbol').text(displayType); // Display النوع from selection column
                        row.find('.td-window').text(wname || row.find('.td-window').text());
                        row.data('date_created', timestamp);
                        row.show();

                        // Add visual highlight effect
                        row.addClass('highlight-new');
                        setTimeout(function() {
                            row.removeClass('highlight-new');
                        }, 2000);
                    }

                } else {
                    // No current queue
                    if (saved.queue_no && saved.queue_no !== '0') {
                        hasChanged = true;
                        previousPerRow[wid] = {
                            queue_no: '',
                            date_created: 0,
                            tsymbol: '',
                            display_type: '',
                            clinic: originalClinicName,
                            wname: row.find('.td-window').text()
                        };

                        row.find('.td-clinic').text(originalClinicName);
                        row.find('.td-queue').text('-');
                        row.find('.td-symbol').text('-');
                        row.data('date_created', 0);
                    }
                }

                return hasChanged;
            }

            // --- 4. Fetch Individual Window Data ---
            function fetchQueueData(data) {
                $.ajax({
                    url: 'admin/ajax.php?action=get_queue',
                    method: 'POST',
                    data: {
                        id: data.tids,
                        wid: data.wid
                    },
                    success: function(resp) {
                        var hasChanged = processResponse(data.row, data.wid, data.originalClinicName, resp);

                        // Sort immediately if there was a change
                        if (hasChanged) {
                            sortRowsByDateCreated();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error for wid:", data.wid, "Status:", status, "Error:", error);
                    }
                });
            }

            // --- 5. Centralized Fetch Function ---
            function fetchAllQueueData() {
                // Fetch data for each window
                windowData.forEach(function(data) {
                    fetchQueueData(data);
                });
            }

            // --- 6. Start Polling ---
            if (windowData.length > 0) {
                fetchAllQueueData(); // Initial fetch
                setInterval(fetchAllQueueData, 5000); // Poll every 5 seconds
            }
        });
        // ========================= END QUEUE LOGIC HERE =========================

        $(document).ready(function() {

            $('.singleCard').each(function() {
                var card = $(this);
                var tid = card.data('tid');
                var wid = card.data('wid');

                var previousResponse;


                var renderServe = setInterval(function() {
                    $.ajax({
                        url: 'admin/ajax.php?action=get_queue',
                        method: "POST",
                        data: {
                            id: tid,
                            wid: wid
                        },
                        success: function(resp) {
                            try {
                                parsedResp = JSON.parse(resp);
                            } catch (error) {
                                // Handle non-JSON response here
                                return;
                            }
                            resp = JSON.parse(resp);
                            if (resp.status == 1) {
                                card.find('#squeue').html(resp.data.tsymbol + resp.data.queue_no);
                                card.find('#window').html(resp.data.wname);

                                previousResponse = resp;
                            }
                        }
                    });
                }, 2000);
            });
            // var renderTranss = setInterval(function() {
            //   location.reload();
            // }, 60000);
            //get trans sound
            $(document).ready(function() {

                $('.transaction-x').each(function() {
                    var card = $(this);
                    var tid = card.data('tid');

                    var previousResponse = {
                        status: '',
                        data: {
                            queue_no: '',
                            date_created: '',
                            recall: ''
                        }
                    };

                    var renderServe = setInterval(function() {
                        $.ajax({
                            url: 'admin/ajax.php?action=get_queue_sound',
                            method: "POST",
                            data: {
                                id: tid
                            },
                            success: function(resp) {
                                try {
                                    parsedResp = JSON.parse(resp);
                                } catch (error) {
                                    return;
                                }
                                resp = JSON.parse(resp);
                                if (resp.status == 1) {
                                    if (
                                        (resp.data.queue_no !== previousResponse.data.queue_no &&
                                            resp.data.date_created !== previousResponse.data.date_created) || resp.data.recall !== previousResponse.data.recall
                                    ) {
                                        let start = 'البطاقة رقم ';
                                        let symbol = resp.data.tsymbol;
                                        let num = resp.data.queue_no;
                                        let to = ' إلى ';
                                        let wnum = resp.data.wname;
                                        let str = start + symbol + ' ' + num;
                                        fetch('tts/tts.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded'
                                                },
                                                body: 'text=' + encodeURIComponent(str)
                                            })

                                            .catch(error => {
                                                console.error('AJAX request failed:', error);
                                            });

                                    }

                                    previousResponse = resp;
                                }
                            }
                        });
                    }, 2000);
                });

            });
        });
    </script>

</body>

</html>