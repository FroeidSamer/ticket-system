<?php
header('Content-Type: application/json');
// Get table name from query parameter
$waiting_stats = isset($_GET['table']) ? $_GET['table'] : '';

// Validate table name
if (empty($waiting_stats)) {
    echo json_encode(['error' => 'Table name is required']);
    exit;
}

// Sanitize table name to prevent SQL injection
$waiting_stats = preg_replace('/[^a-zA-Z0-9_]/', '', $waiting_stats);

// Check if table exists
$check_table = $conn->query("SHOW TABLES LIKE '$waiting_stats'");
if ($check_table->num_rows === 0) {
    echo json_encode(['error' => 'Table does not exist']);
    exit;
}

// Query to fetch data
$sql = "SELECT * FROM `$waiting_stats`";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['error' => 'Error executing query: ' . $conn->error]);
    exit;
}

// Fetch all data as associative array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return JSON response
echo json_encode([
    'success' => true,
    'data' => $data,
    'count' => count($data)
]);

$conn->close();
