<?php
// Set timezone to Asia/Riyadh
date_default_timezone_set("Asia/Riyadh");
$conn->query("SET time_zone = '+03:00'");

// Get date range from GET parameters or set defaults
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // First day of current month
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Today

// Validate dates
if (!$start_date || !$end_date) {
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-d');
}

// Query for daily data
$daily_query = "
    SELECT 
        DATE(processed_at) as date,
        COUNT(*) as count,
        COUNT(DISTINCT staff_id) as unique_staff
    FROM staff_statistics 
    WHERE DATE(processed_at) BETWEEN ? AND ?
    GROUP BY DATE(processed_at)
    ORDER BY DATE(processed_at)
";

// Query for staff performance
$staff_query = "
    SELECT 
        u.id as staff_id,
        COALESCE(u.name, CONCAT('Staff #', u.id)) as staff_name,
        COUNT(t.id) as total_processed,
        DATE(MIN(t.processed_at)) as first_process,
        DATE(MAX(t.processed_at)) as last_process
    FROM users u
    LEFT JOIN staff_statistics t ON u.id = t.staff_id 
        AND DATE(t.processed_at) BETWEEN ? AND ?
    WHERE u.id IN (SELECT DISTINCT staff_id FROM staff_statistics WHERE DATE(processed_at) BETWEEN ? AND ?)
    GROUP BY u.id
    ORDER BY total_processed DESC
";

// Execute queries
$daily_stmt = $conn->prepare($daily_query);
$daily_stmt->bind_param("ss", $start_date, $end_date);
$daily_stmt->execute();
$daily_result = $daily_stmt->get_result();

$staff_stmt = $conn->prepare($staff_query);
$staff_stmt->bind_param("ssss", $start_date, $end_date, $start_date, $end_date);
$staff_stmt->execute();
$staff_result = $staff_stmt->get_result();

// Prepare data for charts
$daily_data = [];
$staff_data = [];

while ($row = $daily_result->fetch_assoc()) {
    $daily_data[] = [
        'date' => $row['date'],
        'count' => (int)$row['count'],
        'unique_staff' => (int)$row['unique_staff']
    ];
}

while ($row = $staff_result->fetch_assoc()) {
    $staff_data[] = [
        'staff_name' => $row['staff_name'],
        'total_processed' => (int)$row['total_processed'],
        'first_process' => $row['first_process'],
        'last_process' => $row['last_process']
    ];
}

// Get summary statistics
$summary_query = "
    SELECT 
        COUNT(*) as total_records,
        COUNT(DISTINCT staff_id) as total_staff,
        COUNT(DISTINCT DATE(processed_at)) as total_days,
        MIN(processed_at) as earliest_date,
        MAX(processed_at) as latest_date
    FROM staff_statistics 
    WHERE DATE(processed_at) BETWEEN ? AND ?
";

$summary_stmt = $conn->prepare($summary_query);
$summary_stmt->bind_param("ss", $start_date, $end_date);
$summary_stmt->execute();
$summary = $summary_stmt->get_result()->fetch_assoc();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<style>
    .header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .header h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 300;
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
    }

    .stat-card:hover {
        transform: translateY(-5px);
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

    .staff-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .staff-table th,
    .staff-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #e9ecef;
    }

    .staff-table th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
    }

    .staff-table tr:hover {
        background: #f8f9fa;
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
    }
</style>

<div class="">
    <div class="controls">
        <form method="GET" class="date-controls">
            <div class="date-group">
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
            </div>
            <div class="date-group">
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
            </div>
            <button type="submit" class="btn">📈 Update Charts</button>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($summary['total_records']); ?></div>
            <div class="stat-label">Total Records</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($summary['total_staff']); ?></div>
            <div class="stat-label">Active Staff</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($summary['total_days']); ?></div>
            <div class="stat-label">Active Days</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $summary['total_records'] > 0 ? number_format($summary['total_records'] / max($summary['total_days'], 1), 1) : '0'; ?></div>
            <div class="stat-label">Avg Daily</div>
        </div>
    </div>

    <div class="charts-container">
        <?php if (empty($daily_data)): ?>
            <div class="no-data">
                <h3>No data found for the selected date range</h3>
                <p>Try selecting a different date range or check if data exists in your table.</p>
            </div>
        <?php else: ?>
            <div class="chart-section">
                <h2 class="chart-title">Daily Processing Activity</h2>
                <div class="chart-wrapper">
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="chart-section">
                <h2 class="chart-title">Staff Performance Overview</h2>
                <div class="chart-wrapper">
                    <div class="chart-container">
                        <canvas id="staffChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="chart-section">
                <h2 class="chart-title">Staff Performance Details</h2>
                <table class="staff-table">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Total Processed</th>
                            <th>First Activity</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staff_data as $staff): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($staff['staff_name']); ?></td>
                                <td><?php echo number_format($staff['total_processed']); ?></td>
                                <td><?php echo $staff['first_process']; ?></td>
                                <td><?php echo $staff['last_process']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Daily Chart Data
    const dailyData = <?php echo json_encode($daily_data); ?>;
    const staffData = <?php echo json_encode($staff_data); ?>;

    // Daily Activity Chart
    if (dailyData.length > 0) {
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date),
                datasets: [{
                    label: 'Total Processed',
                    data: dailyData.map(item => item.count),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Unique Staff',
                    data: dailyData.map(item => item.unique_staff),
                    borderColor: '#764ba2',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
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
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Staff Performance Chart
        const staffCtx = document.getElementById('staffChart').getContext('2d');
        new Chart(staffCtx, {
            type: 'bar',
            data: {
                labels: staffData.map(item => item.staff_name),
                datasets: [{
                    label: 'Total Processed',
                    data: staffData.map(item => item.total_processed),
                    backgroundColor: staffData.map((_, index) => {
                        const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'];
                        return colors[index % colors.length];
                    }),
                    borderRadius: 8,
                    borderSkipped: false,
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