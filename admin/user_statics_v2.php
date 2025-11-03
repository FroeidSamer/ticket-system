<?php
include('db_connect.php');

// Set timezone to Asia/Riyadh
date_default_timezone_set("Asia/Riyadh");
$conn->query("SET time_zone = '+03:00'");

// Get user ID from GET parameter
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    die(tr('user_id_required') . " " . tr('provide_user_id_url'));
}

// Get date range from GET parameters or set defaults
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01', strtotime('-6 months')); // 6 months ago
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Today

// Get user information
$user_query = "SELECT * FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die(tr('user_not_found') . ": " . $user_id);
}

// Get user's basic statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_records,
        COUNT(DISTINCT DATE(processed_at)) as active_days,
        MIN(processed_at) as first_activity,
        MAX(processed_at) as last_activity,
        AVG(CASE WHEN DATE(processed_at) = CURDATE() THEN 1 ELSE 0 END) as today_activity
    FROM staff_statistics 
    WHERE staff_id = ? AND DATE(processed_at) BETWEEN ? AND ?
";

$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

// Get daily activity for charts
$daily_query = "
    SELECT 
        DATE(processed_at) as date,
        COUNT(*) as count,
        HOUR(processed_at) as hour
    FROM staff_statistics 
    WHERE staff_id = ? AND DATE(processed_at) BETWEEN ? AND ?
    GROUP BY DATE(processed_at)
    ORDER BY DATE(processed_at)
";

$daily_stmt = $conn->prepare($daily_query);
$daily_stmt->bind_param("iss", $user_id, $start_date, $end_date);
$daily_stmt->execute();
$daily_result = $daily_stmt->get_result();

$daily_data = [];
while ($row = $daily_result->fetch_assoc()) {
    $daily_data[] = [
        'date' => $row['date'],
        'count' => (int)$row['count']
    ];
}

// Get hourly activity pattern
$hourly_query = "
    SELECT 
        HOUR(processed_at) as hour,
        COUNT(*) as count
    FROM staff_statistics 
    WHERE staff_id = ? AND DATE(processed_at) BETWEEN ? AND ?
    GROUP BY HOUR(processed_at)
    ORDER BY HOUR(processed_at)
";

$hourly_stmt = $conn->prepare($hourly_query);
$hourly_stmt->bind_param("iss", $user_id, $start_date, $end_date);
$hourly_stmt->execute();
$hourly_result = $hourly_stmt->get_result();

$hourly_data = array_fill(0, 24, 0); // Initialize 24 hours with 0
while ($row = $hourly_result->fetch_assoc()) {
    $hourly_data[(int)$row['hour']] = (int)$row['count'];
}

// Get weekly activity pattern
$weekly_query = "
    SELECT 
        DAYOFWEEK(processed_at) as day_of_week,
        DAYNAME(processed_at) as day_name,
        COUNT(*) as count
    FROM staff_statistics 
    WHERE staff_id = ? AND DATE(processed_at) BETWEEN ? AND ?
    GROUP BY DAYOFWEEK(processed_at), DAYNAME(processed_at)
    ORDER BY DAYOFWEEK(processed_at)
";

$weekly_stmt = $conn->prepare($weekly_query);
$weekly_stmt->bind_param("iss", $user_id, $start_date, $end_date);
$weekly_stmt->execute();
$weekly_result = $weekly_stmt->get_result();

$weekly_data = [];
while ($row = $weekly_result->fetch_assoc()) {
    $weekly_data[] = [
        'day' => $row['day_name'],
        'count' => (int)$row['count']
    ];
}

// Get monthly summary
$monthly_query = "
    SELECT 
        DATE_FORMAT(processed_at, '%Y-%m') as month,
        COUNT(*) as count
    FROM staff_statistics 
    WHERE staff_id = ? AND DATE(processed_at) BETWEEN ? AND ?
    GROUP BY DATE_FORMAT(processed_at, '%Y-%m')
    ORDER BY month
";

$monthly_stmt = $conn->prepare($monthly_query);
$monthly_stmt->bind_param("iss", $user_id, $start_date, $end_date);
$monthly_stmt->execute();
$monthly_result = $monthly_stmt->get_result();

$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_data[] = [
        'month' => $row['month'],
        'count' => (int)$row['count']
    ];
}

// Get recent activity (last 10 records)
$recent_query = "
    SELECT processed_at
    FROM staff_statistics 
    WHERE staff_id = ?
    ORDER BY processed_at DESC
    LIMIT 10
";

$recent_stmt = $conn->prepare($recent_query);
$recent_stmt->bind_param("i", $user_id);
$recent_stmt->execute();
$recent_result = $recent_stmt->get_result();

$recent_activities = [];
while ($row = $recent_result->fetch_assoc()) {
    $recent_activities[] = $row['processed_at'];
}

// Calculate performance metrics
$total_days_in_range = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
$activity_percentage = $stats['active_days'] > 0 ? ($stats['active_days'] / $total_days_in_range) * 100 : 0;
$avg_daily = $stats['active_days'] > 0 ? $stats['total_records'] / $stats['active_days'] : 0;

// Get user rank compared to others
$rank_query = "
    SELECT COUNT(*) + 1 as user_rank
    FROM (
        SELECT staff_id, COUNT(*) as total
        FROM staff_statistics 
        WHERE DATE(processed_at) BETWEEN ? AND ?
        GROUP BY staff_id
        HAVING total > (
            SELECT COUNT(*) 
            FROM staff_statistics 
            WHERE staff_id = ? AND DATE(processed_at) BETWEEN ? AND ?
        )
    ) ranked_users
";

$rank_stmt = $conn->prepare($rank_query);
$rank_stmt->bind_param("ssiss", $start_date, $end_date, $user_id, $start_date, $end_date);
$rank_stmt->execute();
$rank_result = $rank_stmt->get_result()->fetch_assoc();
$user_rank = $rank_result['user_rank'];
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<style>
    .header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }

    .header h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 300;
    }

    .user-info {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
    }

    .user-details h2 {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .user-details p {
        opacity: 0.9;
        font-size: 1rem;
    }

    .back-btn {
        position: absolute;
        left: 30px;
        top: 30px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateX(-5px);
    }

    .controls {
        background: #f8f9fa;
        padding: 25px;
        border-bottom: 1px solid #e9ecef;
    }

    .date-controls {
        display: flex;
        gap: 20px;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }

    .date-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .date-group label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }

    .date-group input {
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .date-group input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        padding: 30px;
        background: #f8f9fa;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
    }

    .performance-indicators {
        display: flex;
        justify-content: space-around;
        padding: 20px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        margin: 0 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .indicator {
        text-align: center;
    }

    .indicator-value {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .indicator-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .charts-container {
        padding: 30px;
    }

    .chart-section {
        margin-bottom: 50px;
    }

    .chart-title {
        font-size: 1.5rem;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
    }

    .chart-wrapper {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 20px;
    }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 30px;
    }

    .recent-activity {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .activity-list {
        list-style: none;
    }

    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-time {
        background: #667eea;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .no-data {
        text-align: center;
        padding: 60px;
        color: #6c757d;
        font-size: 1.2rem;
    }

    @media (max-width: 768px) {
        .date-controls {
            flex-direction: column;
        }

        .header h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            padding: 20px;
        }

        .chart-grid {
            grid-template-columns: 1fr;
        }

        .performance-indicators {
            flex-direction: column;
            gap: 15px;
        }

        .back-btn {
            position: static;
            margin-bottom: 20px;
            display: inline-block;
        }
    }
</style>

<div class="">
    <div class="header">
        <a href="javascript:history.back()" class="back-btn">← <?php echo tr('back'); ?></a>
        <h1>👤 <?php echo tr('user_statistics'); ?></h1>
        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['name'] ?? tr('user'), 0, 1)); ?>
            </div>
            <div class="user-details">
                <h2><?php echo htmlspecialchars($user['name'] ?? tr('user') . ' #' . $user_id); ?></h2>
                <p><?php echo tr('user_id'); ?>: <?php echo $user_id; ?> | <?php echo tr('rank'); ?>: #<?php echo $user_rank; ?></p>
            </div>
        </div>
    </div>

    <div class="controls">
        <form method="GET" class="date-controls">
            <input type="hidden" name="page" value="user_statics_v2">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">
            <div class="date-group">
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
            </div>
            <div class="date-group">
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
            </div>
            <button type="submit" class="btn">📊 <?php echo tr('update_stats'); ?></button>
        </form>
    </div>

    <div class="performance-indicators">
        <div class="indicator">
            <div class="indicator-value"><?php echo number_format($activity_percentage, 1); ?>%</div>
            <div class="indicator-label"><?php echo tr('activity_rate'); ?></div>
        </div>
        <div class="indicator">
            <div class="indicator-value"><?php echo number_format($avg_daily, 1); ?></div>
            <div class="indicator-label"><?php echo tr('daily_average'); ?></div>
        </div>
        <div class="indicator">
            <div class="indicator-value">#<?php echo $user_rank; ?></div>
            <div class="indicator-label"><?php echo tr('performance_rank'); ?></div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($stats['total_records']); ?></div>
            <div class="stat-label"><?php echo tr('total_records'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($stats['active_days']); ?></div>
            <div class="stat-label"><?php echo tr('active_days'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['first_activity'] ? date('M d', strtotime($stats['first_activity'])) : tr('not_available'); ?></div>
            <div class="stat-label"><?php echo tr('first_activity'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['last_activity'] ? date('M d', strtotime($stats['last_activity'])) : tr('not_available'); ?></div>
            <div class="stat-label"><?php echo tr('last_activity'); ?></div>
        </div>
    </div>

    <div class="charts-container">
        <?php if (empty($daily_data)): ?>
            <div class="no-data">
                <h3><?php echo tr('no_data_found_user'); ?></h3>
                <p><?php echo tr('try_different_date_range'); ?></p>
            </div>
        <?php else: ?>
            <div class="chart-section">
                <h2 class="chart-title"><?php echo tr('daily_activity_trend'); ?></h2>
                <div class="chart-wrapper">
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="chart-grid">
                <div class="chart-wrapper">
                    <h3 class="chart-title"><?php echo tr('hourly_activity_pattern'); ?></h3>
                    <div class="chart-container">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>

                <div class="chart-wrapper">
                    <h3 class="chart-title"><?php echo tr('weekly_activity_pattern'); ?></h3>
                    <div class="chart-container">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>
            </div>

            <?php if (!empty($monthly_data)): ?>
                <div class="chart-section">
                    <h2 class="chart-title"><?php echo tr('monthly_summary'); ?></h2>
                    <div class="chart-wrapper">
                        <div class="chart-container">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($recent_activities)): ?>
                <div class="chart-section">
                    <h2 class="chart-title"><?php echo tr('recent_activity'); ?></h2>
                    <div class="recent-activity">
                        <ul class="activity-list">
                            <?php foreach ($recent_activities as $activity): ?>
                                <li class="activity-item">
                                    <div class="activity-time"><?php echo date('M d, H:i', strtotime($activity)); ?></div>
                                    <div><?php echo tr('processing_completed'); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // Chart data
    const dailyData = <?php echo json_encode($daily_data); ?>;
    const hourlyData = <?php echo json_encode($hourly_data); ?>;
    const weeklyData = <?php echo json_encode($weekly_data); ?>;
    const monthlyData = <?php echo json_encode($monthly_data); ?>;

    // Chart colors
    const colors = {
        primary: '#667eea',
        secondary: '#764ba2',
        accent: '#f093fb',
        success: '#00f2fe',
        warning: '#f5576c'
    };

    // Daily Activity Chart
    if (dailyData.length > 0) {
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date),
                datasets: [{
                    label: '<?php echo tr("daily_activity"); ?>',
                    data: dailyData.map(item => item.count),
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    }

    // Hourly Activity Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: Array.from({
                length: 24
            }, (_, i) => i + ':00'),
            datasets: [{
                label: '<?php echo tr("activity_by_hour"); ?>',
                data: hourlyData,
                backgroundColor: colors.secondary,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            }
        }
    });

    // Weekly Activity Chart
    if (weeklyData.length > 0) {
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'doughnut',
            data: {
                labels: weeklyData.map(item => item.day),
                datasets: [{
                    data: weeklyData.map(item => item.count),
                    backgroundColor: [
                        colors.primary,
                        colors.secondary,
                        colors.accent,
                        colors.success,
                        colors.warning,
                        '#4facfe',
                        '#00f2fe'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20
                        }
                    }
                }
            }
        });
    }

    // Monthly Chart
    if (monthlyData.length > 0) {
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: '<?php echo tr("monthly_activity"); ?>',
                    data: monthlyData.map(item => item.count),
                    backgroundColor: colors.accent,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    }
</script>