<?php
include_once 'config.php';
include_once 'lang/lang_main.php';

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Could not connect to mysql" . mysqli_error($con));

if (!isset($_SESSION['setting_name'])) {
    $query = $conn->query("SELECT * FROM settings limit 1")->fetch_array();
    foreach ($query as $key => $value) {
        if (!is_numeric($key))
            $_SESSION['setting_' . $key] = $value;
    }
}

if (!isset($_COOKIE['lang'])) {
    setcookie('lang', 'ar', time() + 31556926, '/');
}

?>