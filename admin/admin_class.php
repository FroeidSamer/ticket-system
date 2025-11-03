<?php
session_start();
// ini_set('display_errors', 1);
date_default_timezone_set("Asia/Riyadh");

class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		include 'db_connect.php';

		$this->db = $conn;
	}

	public function getDb()
	{
		return $this->db;
	}

	function __destruct()
	{
		$this->db->close();
		ob_end_flush();
	}

	
	

	
	

	

	
	

	function save_window()
	{
		extract($_POST);

		$transaction_ids_str = null;
		if (!empty($transaction_ids)) {
			$transaction_ids_str = implode(',', $transaction_ids);
		}

		$cwhere = '';
		$cparams = [];
		$ctypes = "";
		if (!empty($id)) {
			$cwhere = " AND id != ? ";
			$cparams[] = $id;
			$ctypes .= "i";
		}

		$chk_stmt = $this->db->prepare("SELECT * FROM transaction_windows WHERE name = ? AND transaction_ids = ? " . $cwhere);
		$chk_stmt->bind_param("ss" . $ctypes, $name, $transaction_ids_str, ...$cparams);
		$chk_stmt->execute();
		$chk_result = $chk_stmt->get_result();

		if ($chk_result->num_rows > 0) {
			return 2;
			exit;
		}

		$data = " name = ?, transaction_ids = ?, transaction_id = NULL ";
		$params = [$name, $transaction_ids_str];
		$types = "ss";

		if (empty($id)) {
			$stmt = $this->db->prepare("INSERT INTO transaction_windows SET " . $data);
			$stmt->bind_param($types, ...$params);
		} else {
			$data .= " WHERE id = ? ";
			$params[] = $id;
			$types .= "i";
			$stmt = $this->db->prepare("UPDATE transaction_windows SET " . $data);
			$stmt->bind_param($types, ...$params);
		}

		if ($stmt->execute()) {
			return 1;
		}
	}
	function delete_window()
	{
		extract($_POST);
		$stmt = $this->db->prepare("DELETE FROM transaction_windows WHERE id = ?");
		$stmt->bind_param("i", $id);
		if ($stmt->execute()) {
			return 1;
		}
	}
	function save_uploads()
	{
		extract($_POST);
		$saved_count = 0;
		$stmt = $this->db->prepare("INSERT INTO file_uploads SET file_path = ?");

		for ($i = 0; $i < count($img); $i++) {
			list($type, $img_data) = explode(';', $img[$i]);
			list(, $img_data)      = explode(',', $img_data);
			$img_data = str_replace(' ', '+', $img_data);
			$img_data = base64_decode($img_data);

			// Sanitize filename
			$original_fname = basename($imgName[$i]);
			$fname = strtotime(date('Y-m-d H:i')) . "_" . $original_fname;

			$upload = file_put_contents("assets/uploads/" . $fname, $img_data);

			if ($upload) {
				$stmt->bind_param("s", $fname);
				if ($stmt->execute()) {
					$saved_count++;
				}
			}
		}

		if ($saved_count > 0) {
			return 1;
		}
	}
	function delete_uploads()
	{
		extract($_POST);

		$stmt_select = $this->db->prepare("SELECT file_path FROM file_uploads WHERE id = ?");
		$stmt_select->bind_param("i", $id);
		$stmt_select->execute();
		$result = $stmt_select->get_result();
		$path = $result->fetch_array()['file_path'];

		if ($path) {
			$stmt_delete = $this->db->prepare("DELETE FROM file_uploads WHERE id = ?");
			$stmt_delete->bind_param("i", $id);
			if ($stmt_delete->execute()) {
				// Sanitize path to prevent directory traversal
				$safe_path = 'assets/uploads/' . basename($path);
				if (file_exists($safe_path)) {
					unlink($safe_path);
				}
				return 1;
			}
		}
		return 0; // Or some other error indicator
	}
	
	
	
	
	
	
	
	

	
	
	
	
	function set_transfer()
	{
		extract($_POST);
		$stmt = $this->db->prepare("UPDATE users SET transfer = ? WHERE id = ?");
		$stmt->bind_param("si", $val, $id);
		if ($stmt->execute()) {
			return 1;
		} else {
			return 0;
		}
	}
	function set_permissions()
	{
		extract($_POST);
		if ($act == 'add') {
			$stmt = $this->db->prepare("INSERT INTO user_permissions (user_id, transaction_id) VALUES (?, ?)");
			$stmt->bind_param("ii", $id, $val);
			if ($stmt->execute()) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$stmt = $this->db->prepare("DELETE FROM user_permissions WHERE user_id = ? AND transaction_id = ?");
			$stmt->bind_param("ii", $id, $val);
			if ($stmt->execute()) {
				return 1;
			} else {
				return 0;
			}
		}
	}
	
	
	
	
	// function save_conditions()
	// {
	// 	extract($_POST);
	// 	$this->db->query("UPDATE status SET color = '" . $A3 . "' WHERE id = 1");
	// 	$this->db->query("UPDATE status SET color = '" . $B4 . "' WHERE id = 2");
	// 	$this->db->query("UPDATE status SET color = '" . $C5 . "' WHERE id = 3");

	// 	return 1;
	// }
	function add_status()
	{
		$type = $_POST['type'];
		$ordering = $_POST['ordering'];
		$color = $_POST['color'];

		$stmt = $this->db->prepare("INSERT INTO status (type, ordering, color) VALUES (?, ?, ?)");
		$stmt->bind_param("sis", $type, $ordering, $color);
		$stmt->execute();

		return 1;
	}


	function update_status()
	{
		$id = $_POST['id'];
		$type = $_POST['type'];
		$ordering = $_POST['ordering'];
		$color = $_POST['color'];

		$stmt = $this->db->prepare("UPDATE status SET type = ?, ordering = ?, color = ? WHERE id = ?");
		$stmt->bind_param("sisi", $type, $ordering, $color, $id);
		$stmt->execute();

		return 1;
	}

	function update_statuses()
	{
		$stmt = $this->db->prepare("UPDATE status SET type = ?, ordering = ?, color = ? WHERE id = ?");
		foreach ($_POST['type'] as $id => $type) {
			$ordering = $_POST['ordering'][$id];
			$color = $_POST['color'][$id];
			$stmt->bind_param("sisi", $type, $ordering, $color, $id);
			$stmt->execute();
		}
		return 1;
	}

	function delete_status()
	{
		$id = $_POST['id'];
		$stmt = $this->db->prepare("DELETE FROM status WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		return 1;
	}
	//GET LAST REVIEWED AND WAITING NUMBER 
	

	
	


	

	
	function set_lang()
	{
		extract($_POST);
		if ($lang === 'ar') {
			$_SESSION['lang'] = 'ar';
			setcookie('lang', 'ar', time() + 31556926, '/');
			return 1;
		} elseif ($lang === 'en') {
			$_SESSION['lang'] = 'en';
			setcookie('lang', 'en', time() + 31556926, '/');
			return 1;
		} else {
			return 2;
		}
	}
}
