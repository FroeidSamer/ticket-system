<?php
// Include your database connection
require_once 'db_connect.php';

// Get date range from POST/GET
$start_date = $_POST['start_date'] ?? $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_POST['end_date'] ?? $_GET['end_date'] ?? date('Y-m-d');

// Build WHERE clause based on date range
$queue_where = "WHERE ws.id IS NOT NULL";
if (!empty($start_date) && !empty($end_date)) {
    $queue_where .= " AND DATE(q.date_created) BETWEEN '$start_date' AND '$end_date'";
}

// Your exact query
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

if ($queue_result === false) {
    die("Error executing query: " . $conn->error);
}

// Generate filename with current date
$date = date('Y-m-d_H-i-s');
$filter_text = '';
if (!empty($date_filter)) {
    $filter_text = "_" . $date_filter;
} elseif (!empty($month_filter)) {
    $filter_text = "_" . $month_filter;
}
$filename = "queue_statistics" . $filter_text . "_" . $date . ".xls";

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// UTF-8 BOM for proper Arabic encoding
echo "\xEF\xBB\xBF";

// Start Excel HTML
?>
<!DOCTYPE html>
<html dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            direction: rtl;
            font-family: Arial, sans-serif;
        }

        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; direction: rtl;">إحصائيات الطوابير</h2>
    <?php if (!empty($date_filter)): ?>
        <p style="text-align: center;">التاريخ: <?= $date_filter ?></p>
    <?php elseif (!empty($month_filter)): ?>
        <p style="text-align: center;">الشهر: <?= $month_filter ?></p>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>رقم البطاقة</th>
                <th>القسم</th>
                <th>وقت الإنشاء</th>
                <th>وقت الاستدعاء</th>
                <th>النافذة</th>
                <th>وقت الانتظار (دقيقة)</th>
                <th>وقت الخدمة (دقيقة)</th>
                <th>الوقت الكلي (دقيقة)</th>
                <th>نوع الحالة</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($queue_result->num_rows > 0): ?>
                <?php while ($row = $queue_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['queue_no']) ?></td>
                        <td><?= htmlspecialchars($row['transaction_type']) ?></td>
                        <td><?= $row['queue_created'] ?></td>
                        <td><?= $row['called_at'] ?? 'غير متوفر' ?></td>
                        <td><?= $row['window_name'] ?? 'غير متوفر' ?></td>
                        <td>
                            <?php if ($row['waiting_duration']): ?>
                                <?= round($row['waiting_duration'] / 60, 1) ?>
                            <?php else: ?>
                                غير متوفر
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['service_duration']): ?>
                                <?= round($row['service_duration'] / 60, 1) ?>
                            <?php else: ?>
                                غير متوفر
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['waiting_duration'] && $row['service_duration']): ?>
                                <?= round(($row['waiting_duration'] + $row['service_duration']) / 60, 1) ?>
                            <?php else: ?>
                                غير متوفر
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['status_type'] ?? 'غير متوفر') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">لا توجد بيانات متاحة</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
<?php
$conn->close();
?>