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
    </style>
</head>

<body>

    <?php
    $tw_res = $conn->query("SELECT * FROM transaction_windows WHERE status = 1 ORDER BY name ASC");
    $windows = [];
    while ($r = $tw_res->fetch_assoc()) {
        $windows[] = $r;
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
                            <tr class="queue-row" data-wid="<?= htmlspecialchars($w['id']) ?>" data-tids="<?= htmlspecialchars($w['transaction_ids']) ?>">
                                <td class="td-clinic clinic-name">-</td> <!-- بدل td-symbol -->
                                <td class="td-queue">-</td>
                                <td class="td-symbol">-</td> <!-- لو حابة تظهري النوع بعد كده -->
                                <td class="td-window"><?= htmlspecialchars($w['name']) ?></td>
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
    <script>
        var slides = <?= json_encode($slides) ?>;
        var scount = slides.length;

        function render_slides(k) {
            if (scount === 0) return;
            if (k >= scount) k = 0;
            var src = slides[k];
            k++;
            var ext = src.split('.').pop().toLowerCase();
            var file;
            if (ext === 'webm' || ext === 'mp4') {
                file = $("<video id='slide' src='admin/assets/uploads/" + src + "' autoplay muted playsinline onended='render_slides(" + k + ")'></video>");
            } else {
                file = $("<img id='slide' src='admin/assets/uploads/" + src + "' onload='slideInterval(" + k + ")' />");
            }
            if ($('#slide').length > 0) {
                $('#slide').css({
                    "opacity": 0
                });
                setTimeout(function() {
                    $('#slide').remove();
                    $('.slideShow').append(file);
                    $('#slide').css({
                        "opacity": 1
                    });
                    if (ext === 'webm' || ext === 'mp4') $('video').trigger('play');
                }, 400);
            } else {
                $('.slideShow').append(file);
                $('#slide').css({
                    "opacity": 1
                });
            }
        }

        function slideInterval(i = 0) {
            setTimeout(function() {
                render_slides(i);
            }, 5000);
        }
        if (scount > 0) $(function() {
            render_slides(0);
        });

        var previousPerRow = {};
        var rows = $('.queue-row');
        rows.each(function() {
            var row = $(this);
            var wid = row.data('wid');
            var tids = row.data('tids');
            previousPerRow[wid] = {
                queue_no: '',
                date_created: '',
                tsymbol: ''
            };
            setInterval(function() {
                $.ajax({
                    url: 'admin/ajax.php?action=get_queue',
                    method: 'POST',
                    data: {
                        id: tids,
                        wid: wid
                    },
                    success: function(resp) {
                        try {
                            var r = typeof resp === 'object' ? resp : JSON.parse(resp);
                        } catch (e) {
                            return;
                        }
                        if (r.status == 1 && r.data) {
                            var tsymbol = r.data.tsymbol || '';
                            var qno = r.data.queue_no || '';
                            var clinic = r.data.clinic_name || r.data.tname || '';
                            var wname = r.data.wname || '';
                            var date_created = r.data.date_created || r.data.created_timestamp || '';

                            row.find('.td-clinic').text(clinic ? clinic : '-'); // العمود الأول
                            row.find('.td-queue').text(qno ? (tsymbol + ' - ' + qno) : '-'); // العمود الثاني
                            row.find('.td-symbol').text(tsymbol ? tsymbol : '-'); // العمود الثالث (النوع)
                            row.find('.td-window').text(wname ? wname : row.find('.td-window').text()); // الشباك

                            if (qno && qno !== '0') {
                                row.show();
                            } else {
                                row.hide();
                            }

                            row.data('date_created', date_created);

                            var prev = previousPerRow[wid];
                            if (prev.queue_no !== qno || prev.tsymbol !== tsymbol || prev.date_created !== date_created) {
                                if (qno) {
                                    var text = 'البطاقة رقم ' + tsymbol + ' ' + qno + '، برجاء التوجه إلى ' + clinic;
                                    fetch('tts/tts.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: 'text=' + encodeURIComponent(text)
                                    }).catch(function(e) {
                                        console.error(e);
                                    });
                                    previousPerRow[wid] = {
                                        queue_no: qno,
                                        date_created: date_created,
                                        tsymbol: tsymbol
                                    };
                                }
                            }
                        } else {
                            row.hide();
                        }
                        sortRowsByDateCreated();
                    },
                    error: function() {}
                });
            }, 2000);
        });

        function sortRowsByDateCreated() {
            var tbody = $('#queue-tbody');
            var rowsArr = tbody.find('tr:visible').get();
            rowsArr.sort(function(a, b) {
                var ad = $(a).data('date_created') || '';
                var bd = $(b).data('date_created') || '';
                if (!ad && !bd) return 0;
                if (!ad) return 1;
                if (!bd) return -1;
                return new Date(ad) - new Date(bd);
            });
            $.each(rowsArr, function(idx, rowEl) {
                tbody.append(rowEl);
            });
        }

        setInterval(function() {
            var visible = $('#queue-tbody').find('tr:visible').length;
            if (visible === 0) {
                if ($('#no-data-row').length === 0) {
                    $('#queue-tbody').append('<tr id="no-data-row"><td colspan="4" style="padding:30px;font-size:20px;color:#666;">لا توجد أدوار حالياً</td></tr>');
                }
            } else {
                $('#no-data-row').remove();
            }
        }, 1500);
    </script>
</body>

</html>