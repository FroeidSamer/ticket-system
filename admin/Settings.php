<?php

class Settings
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function save_settings()
    {
        extract($_POST);
        $data = " name = ?, period = ? ";
        $params = [$name, $period];
        $types = "ss";

        if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
            $fname = strtotime(date('y-m-d H:i')) . '_' . basename($_FILES['img']['name']);
            $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/img/' . $fname);
            if ($move) {
                $data .= ", image = ? ";
                $params[] = $fname;
                $types .= "s";
            }
        }

        $chk = $this->db->query("SELECT * FROM settings");
        if ($chk->num_rows > 0) {
            $stmt = $this->db->prepare("UPDATE settings SET " . $data);
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt = $this->db->prepare("INSERT INTO settings SET " . $data);
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt->execute()) {
            $query = $this->db->query("SELECT * FROM settings limit 1")->fetch_array();
            foreach ($query as $key => $value) {
                if (!is_numeric($key))
                    $_SESSION['setting_' . $key] = $value;
            }
            return 1;
        }
    }

    public function save_ticket_setting()
    {
        extract($_POST);
        $name_val = isset($name) ? 'on' : 'off';
        $logo_val = isset($logo) ? 'on' : 'off';
        $time_val = isset($time) ? 'on' : 'off';
        $note_val = isset($note) ? 'on' : 'off';
        $notetext_val = isset($notetext) && $notetext != null ? $notetext : '';

        $stmt = $this->db->prepare("UPDATE settings SET ticket_company = ?, ticket_logo = ?, ticket_date = ?, ticket_note = ?, note = ? WHERE id = 1");
        $stmt->bind_param("sssss", $name_val, $logo_val, $time_val, $note_val, $notetext_val);
        $stmt->execute();

        return 1;
    }

    public function close_app()
    {
        extract($_POST);
        $query = $this->db->query("SELECT * FROM settings  WHERE id = 1");
        $result = $query->fetch_assoc();

        if (password_verify($password, $result['password'])) {
            return 1;
        } else {
            return 2;
        }
    }

    public function change_pass()
    {
        extract($_POST);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE settings SET password = ? WHERE id = 1");
        $stmt->bind_param("s", $hashed_password);

        if ($stmt->execute()) {
            return 1;
        } else {
            return 2;
        }
    }
}
