<?php
include 'db_connect.php';

$stats = [];
$time_ranges = [
    'today' => 'اليوم',
    'week' => 'الأسبوع الحالي',
    'month' => 'الشهر الحالي',
    'year' => 'السنة الحالية',
    'all' => 'كل الفترات'
];

$current_range = $_GET['range'] ?? 'today';
$where = '';

switch ($current_range) {
    case 'today':
        $where = "WHERE DATE(arrival_time) = CURDATE()";
        break;
    case 'week':
        $where = "WHERE YEARWEEK(arrival_time, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $where = "WHERE MONTH(arrival_time) = MONTH(CURDATE()) AND YEAR(arrival_time) = YEAR(CURDATE())";
        break;
    case 'year':
        $where = "WHERE YEAR(arrival_time) = YEAR(CURDATE())";
        break;
    default:
        $where = "";
}

$query = "SELECT 
    t.name AS transaction_name,
    COUNT(*) AS total_cases,
    AVG(w.waiting_duration)/60 AS avg_wait_minutes,
    AVG(w.service_duration)/60 AS avg_service_minutes
FROM waiting_stats w
JOIN transactions t ON w.transaction_id = t.id
$where
GROUP BY t.name";

$result = $conn->query($query);
$stats_data = [];
while ($row = $result->fetch_assoc()) {
    $stats_data[] = $row;
}

$status_query = "SELECT 
    s.type AS status_type,
    s.color AS status_color,
    COUNT(*) AS total_cases,
    AVG(w.waiting_duration)/60 AS avg_wait_minutes
FROM waiting_stats w
JOIN status s ON w.status_id = s.id
$where
GROUP BY s.type, s.color";
$status_result = $conn->query($status_query);
$status_data = [];
while ($row = $status_result->fetch_assoc()) {
    $status_data[] = $row;
}
$date_filter = $_GET['date_filter'] ?? '';
$month_filter = $_GET['month_filter'] ?? '';

$queue_where = "WHERE ws.id IS NOT NULL";
if (!empty($date_filter)) {
    $queue_where .= " AND DATE(q.date_created) = '$date_filter'";
} elseif (!empty($month_filter)) {
    $queue_where .= " AND MONTH(q.date_created) = MONTH('$month_filter-01') AND YEAR(q.date_created) = YEAR('$month_filter-01')";
}

$queue_query = "SELECT 
    q.id,
    q.queue_no,
    t.name AS transaction_type,
    q.date_created AS queue_created,
    q.called_at,
    q.window_id,
    q.status,
    q.type_id,
    q.transfered,
    q.recall,
    ws.arrival_time,
    ws.start_time,
    ws.end_time,
    ws.waiting_duration,
    ws.service_duration,
    s.type AS status_type,
    s.color AS status_color,
    ts.name AS window_name
FROM queue_list q
INNER JOIN waiting_stats ws ON q.id = ws.queue_id
LEFT JOIN transactions t ON q.transaction_id = t.id
LEFT JOIN transaction_windows ts ON q.window_id = ts.id
LEFT JOIN status s ON q.type_id = s.id
$queue_where
ORDER BY q.date_created DESC";

$queue_result = $conn->query($queue_query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">إحصاءات أوقات الانتظار</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">تصفية حسب الفترة</h6>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $time_ranges[$current_range] ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?php foreach ($time_ranges as $key => $label): ?>
                        <a class="dropdown-item" href="?page=stats&range=<?= $key ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="waitingTimeChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="statusTimeChart"></canvas>
                    </div>
                </div>
            </div>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>نوع الخدمة</th>
                            <th>عدد الحالات</th>
                            <th>متوسط وقت الانتظار (دقيقة)</th>
                            <th>متوسط وقت الخدمة (دقيقة)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats_data as $stat): ?>
                            <tr>
                                <td><?= $stat['transaction_name'] ?></td>
                                <td><?= $stat['total_cases'] ?></td>
                                <td><?= round($stat['avg_wait_minutes'], 1) ?></td>
                                <td><?= round($stat['avg_service_minutes'], 1) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- New Queue Statistics Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">إحصاءات الطوابير التفصيلية</h6>
            <div class="filter-controls">
                <form method="get" class="form-inline">
                    <input type="hidden" name="page" value="stats">
                    <input type="hidden" name="range" value="<?= $current_range ?>">

                    <div class="form-group mr-2">
                        <label for="date_filter" class="mr-2">تصفية حسب التاريخ:</label>
                        <input type="date" class="form-control" id="date_filter" name="date_filter"
                            value="<?= $date_filter ?>" onchange="this.form.submit()">
                    </div>

                    <div class="form-group mr-2">
                        <label for="month_filter" class="mr-2">أو حسب الشهر:</label>
                        <input type="month" class="form-control" id="month_filter" name="month_filter"
                            value="<?= $month_filter ?>" onchange="this.form.submit()">
                    </div>

                    <button type="button" class="btn btn-secondary" onclick="resetQueueFilters()">
                        إعادة تعيين
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="queueStatsTable" width="100%" cellspacing="0" dir="rtl">
                    <thead>
                        <tr>
                            <th>رقم البطاقة</th>
                            <th>القسم</th>
                            <th>وقت الإنشاء</th>
                            <th>وقت الاستدعاء</th>
                            <th>النافذة</th>
                            <th>وقت الانتظار</th>
                            <th>وقت الخدمة</th>
                            <th>الوقت الكلي</th>
                            <th>نوع الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $queue_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['queue_no']) ?></td>
                                <td><?= htmlspecialchars($row['transaction_type']) ?></td>
                                <td><?= $row['queue_created'] ?></td>
                                <td><?= $row['called_at'] ?></td>
                                <td><?= $row['window_name'] ?></td>
                                <td>
                                    <?php if ($row['waiting_duration']): ?>
                                        <?= round($row['waiting_duration'] / 60, 1) ?> دقيقة
                                    <?php else: ?>
                                        غير متوفر
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['service_duration']): ?>
                                        <?= round($row['service_duration'] / 60, 1) ?> دقيقة
                                    <?php else: ?>
                                        غير متوفر
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['waiting_duration'] && $row['service_duration']): ?>
                                        <?= round(($row['waiting_duration'] + $row['service_duration']) / 60, 1) ?> دقيقة
                                    <?php else: ?>
                                        غير متوفر
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status_type']): ?>
                                        <span class="badge" style="background-color: <?= $row['status_color'] ?>">
                                            <?= $row['status_type'] ?>
                                        </span>
                                    <?php else: ?>
                                        غير متوفر
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- DataTables and Moment.js -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.11.5/dataRender/datetime.js"></script>

<script>
    // Existing chart code
    const waitingCtx = document.getElementById('waitingTimeChart').getContext('2d');
    const waitingTimeChart = new Chart(waitingCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($stats_data, 'transaction_name')) ?>,
            datasets: [{
                label: 'متوسط وقت الانتظار (دقيقة)',
                data: <?= json_encode(array_column($stats_data, 'avg_wait_minutes')) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'الدقائق'
                    }
                }
            }
        }
    });

    const statusCtx = document.getElementById('statusTimeChart').getContext('2d');
    const statusTimeChart = new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($status_data, 'status_type')) ?>,
            datasets: [{
                label: 'متوسط وقت الانتظار حسب الحالة (دقيقة)',
                data: <?= json_encode(array_column($status_data, 'avg_wait_minutes')) ?>,
                backgroundColor: <?= json_encode(array_map(function ($item) {
                                        return $item['status_color'] ? hexToRgba($item['status_color'], 0.7) : 'rgba(201, 203, 207, 0.7)';
                                    }, $status_data)) ?>,
                borderColor: <?= json_encode(array_map(function ($item) {
                                    return $item['status_color'] ? $item['status_color'] : 'rgba(201, 203, 207, 1)';
                                }, $status_data)) ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'الدقائق'
                    }
                }
            }
        }
    });

    function hexToRgba(hex, opacity) {
        hex = hex.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    $(document).ready(function() {
        $('#queueStatsTable').DataTable({
            "order": [
                [2, "desc"]
            ],
            "direction": "rtl",
            "language": {
                "url": "assets/DataTables/ar.json"
            },
            "columnDefs": [

                {
                    "targets": "_all",
                    "className": "dt-body-right",

                },
                {
                    "targets": [1, 4, 5, 6, 7, 8],
                    "orderable": false
                }, {
                    "targets": [2, 3],
                    "render": function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return data ? moment(data).format('YYYY-MM-DD HH:mm') : '';
                        }
                        return data;
                    }
                },
                {
                    "targets": [6, 7, 8],
                    "orderData": function(cell, type) {
                        if (type === 'sort') {
                            return parseFloat($(cell).text().split(' ')[0]) || 0;
                        }
                        return cell.innerHTML;
                    }
                }
            ],
            "initComplete": function() {
                $('.filter-controls form').css('justify-content', 'start');
                $('.filter-controls form').css('direction', 'rtl');
                $('.filter-controls form .form-group').css('gap', '20px');
                $('.filter-controls form button').css('margin-right', '20px');
            }
        });
    });

    function resetQueueFilters() {
        window.location.href = '?page=stats&range=<?= $current_range ?>';
    }
</script>