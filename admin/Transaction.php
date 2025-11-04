<?php

class Transaction
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function save_transaction()
    {
        extract($_POST);
    
        $data = " name = ? ";
        $params = [$name];
        $types = "s";

        $cwhere = '';
        $cparams = [];
        $ctypes = "";
        if (!empty($id)) {
            $cwhere = " AND id != ? ";
            $cparams[] = $id;
            $ctypes .= "i";
        }

        $chk_stmt = $this->db->prepare("SELECT * FROM transactions WHERE name = ? " . $cwhere);
        $chk_stmt->bind_param("s" . $ctypes, $name, ...$cparams);
        $chk_stmt->execute();
        $chk_result = $chk_stmt->get_result();
    
        if ($chk_result->num_rows > 0) {
            return 2;
            exit;
        }

        $data .= ", sorting = ? ";
        $params[] = isset($sort) ? 1 : 2;
        $types .= "i";

        $data .= ", priority = ? ";
        $params[] = isset($priority) ? 1 : 2;
        $types .= "i";
        
        if (isset($symbol)) {
            $data .= ", symbol = ? ";
            $params[] = $symbol;
            $types .= "s";
        }
        if (isset($type)) {
            $data .= ", type = ? ";
            $params[] = $type;
            $types .= "s";
        }

        if (isset($numberFrom) && $numberFrom>=0) {
            $data .= ", numberFrom = ? ";
            $params[] = $numberFrom;
            $types .= "i";
        }
        else
        {
            return 3;
            exit;
        }

        if (isset($numberTo) && $numberTo>=0 && $numberTo>=$numberFrom) {
            $data .= ", numberTo = ? ";
            $params[] = $numberTo;
            $types .= "i";
        }
        else
        {
            return 4;
            exit;
        }

        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO transactions SET " . $data);
            $stmt->bind_param($types, ...$params);
        } else {
            $data .= " WHERE id = ? ";
            $params[] = $id;
            $types .= "i";
            $stmt = $this->db->prepare("UPDATE transactions SET " . $data);
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt->execute()) {
            return 1;
        }
    }

    public function delete_transaction()
    {
        extract($_POST);
        $stmt = $this->db->prepare("DELETE FROM transactions WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return 1;
        }
    }

    public function enable_transaction()
    {
        extract($_POST);
        $stmt_select = $this->db->prepare("SELECT active FROM transactions WHERE id = ?");
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $transaction = $result->fetch_assoc();

        if ($transaction) {
            $new_status = ($transaction['active'] == 'on') ? 'off' : 'on';
            $stmt_update = $this->db->prepare("UPDATE transactions SET active = ? WHERE id = ?");
            $stmt_update->bind_param("si", $new_status, $id);
            if ($stmt_update->execute()) {
                return 1;
            }
        }
        return 5;
    }
}
