<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
include 'User.php';
include 'Settings.php';
include 'Transaction.php';
include 'Queue.php';

$crud = new Action();
$db = $crud->getDb();

$user = new User($db);
$settings = new Settings($db);
$transaction = new Transaction($db);
$queue = new Queue($db);

$user_actions = ['login', 'logout', 'save_user', 'delete_user'];
$settings_actions = ['save_settings', 'save_ticket_setting', 'close_app', 'change_pass'];
$transaction_actions = ['save_transaction', 'delete_transaction', 'enable_transaction'];
$queue_actions = [
    'save_queue', 'get_queue', 'get_window_queue', 'get_queue_sound', 'recall_queue',
    'get_waiting_queue', 'update_queue', 'custom_queue', 'custom_queue_all',
    'update_queue_statue', 'transfer_queue', 'get_staff_info', 'get_staff_info_waiting',
    'get_staff_info_transfered', 'call_queue'
];


if (in_array($action, $user_actions)) {
    $result = $user->{$action}();
    if ($result) {
        echo $result;
    }
} elseif (in_array($action, $settings_actions)) {
    $result = $settings->{$action}();
    if ($result) {
        echo $result;
    }
} elseif (in_array($action, $transaction_actions)) {
    $result = $transaction->{$action}();
    if ($result) {
        echo $result;
    }
} elseif (in_array($action, $queue_actions)) {
    if ($action == 'custom_queue' || $action == 'custom_queue_all') {
        $result = $queue->{$action}($_POST['customqueueid']);
    } else {
        $result = $queue->{$action}();
    }
    if ($result) {
        echo $result;
    }
} else {
    if (method_exists($crud, $action)) {
        $result = $crud->{$action}();
        if ($result) {
            echo $result;
        }
    }
}

