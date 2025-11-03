<?php
include 'admin/db_connect.php';
$qry = $conn->query("SELECT q.*,t.name as tname,t.symbol as tsymbol FROM queue_list q inner join transactions t on t.id = q.transaction_id  where q.id=" . $_POST['id'])->fetch_array();
foreach ($qry as $k => $v) {
	$$k = $v;
}
$query = $conn->query("SELECT * FROM settings limit 1")->fetch_array();
$system = [];
foreach ($query as $key => $value) {
	if (!is_numeric($key))
		$system[$key] = $value;
}
$modalContent = '';

if ($system['ticket_logo'] == 'on') {
	$modalContent .= '<div id="ticket">';
	$modalContent .= '<img src="http://' . $_SERVER['SERVER_NAME'] . '/admin/assets/img/' . $system['image'] . '" id="company_image" >';
	$modalContent .= '</div>';
}
if ($system['ticket_company'] == 'on') {
	$modalContent .= '<h4 style="text-align:center; font-family: sans-serif;">' . $system['name'] . '</h4>';
	$modalContent .= '<hr>';
}
$modalContent .= '<h4 style="text-align:center; font-family: sans-serif;">' . $tname . '</h4>';
$modalContent .= '<hr>';
$modalContent .= '<h2 style="text-align:center; font-family: sans-serif;"><b>' . $tsymbol . $queue_no . '</b></h2>';
if ($system['ticket_date'] == 'on') {
	date_default_timezone_set("Asia/Riyadh");
	$modalContent .= '<h4 style="text-align:center; font-family: sans-serif;">التاريخ :' . date('Y-m-d', time())  . '</h4>';
	$modalContent .= '<h4 style="text-align:center; font-family: sans-serif;">الوقت :'  . date("h:i:s") . '</h4>';
	$modalContent .= '<hr>';
}
if ($system['ticket_note'] == 'on') {
	$modalContent .= '<h4 style="text-align:center; font-family: sans-serif;">' . $system['note'] . '</h4>';
}
// Return the modal content
echo $modalContent;
