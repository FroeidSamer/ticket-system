<?php
header('Content-Type: application/json');

// Query to get all tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['error' => 'Error fetching tables: ' . $conn->error]);
    exit;
}

// Fetch all table names
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

// Return JSON response
echo json_encode([
    'success' => true,
    'tables' => $tables
]);

$conn->close();
?>