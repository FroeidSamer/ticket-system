<?php
require_once 'db_connect.php';

$host = DB_HOST;
$user = DB_USERNAME;
$pass = DB_PASSWORD;
$dbname = DB_NAME;
$host = 'localhost';
$port = '3306';

// XAMPP path to mysqldump
$mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

// =============================================
// Generate filename
// =============================================
$timestamp = date('Y-m-d_H-i-s');
$filename = "{$dbname}_backup_{$timestamp}.sql";
$temp_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

// =============================================
// Build mysqldump command
// =============================================
$command = "\"{$mysqldump_path}\" --user={$user} --password={$pass} --host={$host} --port={$port} "
         . "--single-transaction --routines --triggers --events --hex-blob --add-drop-table {$dbname} "
         . "> \"{$temp_file}\" 2>&1";

// =============================================
// Execute mysqldump
// =============================================
$output = shell_exec($command);

// Check if file was created
if (!file_exists($temp_file) || filesize($temp_file) === 0) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo "❌ Database backup failed. File not created.\n\n";
    echo "Command: {$command}\n\n";
    echo "Output:\n{$output}";
    exit;
}

// =============================================
// Download the file
// =============================================
$filesize = filesize($temp_file);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $filesize);
header('Pragma: no-cache');
header('Expires: 0');

readfile($temp_file);
@unlink($temp_file);

// Log the backup
$log_entry = date('Y-m-d H:i:s') . " - Backup created: {$filename} (" . number_format($filesize / 1024, 2) . " KB)\n";
@file_put_contents('database_backup_log.txt', $log_entry, FILE_APPEND);

exit;
?>