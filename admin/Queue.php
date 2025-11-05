<?php

class Queue
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function save_queue()
    {
        extract($_POST);

        $this->db->begin_transaction();

        try {
            $today = date("Y-m-d");
            
            $stmt = $this->db->prepare("SELECT MAX(queue_no) as last_queue FROM queue_list WHERE DATE(created_timestamp) = ? FOR UPDATE");
            $stmt->bind_param("s", $today);
            $stmt->execute();
            $result = $stmt->get_result();
            $last_queue = $result->fetch_assoc()['last_queue'];
            
            $stmt->close();

            if ($last_queue === null) {
                $queue_no = 1001;
            } else {
                $queue_no = $last_queue + 1;
            }

            // If client provided a selection (A/B), try to store it in a dedicated `selection` column.
            // Accept only 'A' or 'B' as valid selection values. Anything else will be ignored.
            $selectionVal = (isset($selection) && in_array($selection, array('A','B'))) ? $selection : null;

            // Check if `selection` column exists; if not, attempt to create it.
            $selCol = $this->db->query("SHOW COLUMNS FROM queue_list LIKE 'selection'");
            if (!($selCol && $selCol->num_rows > 0)) {
                // Try to add the column (if permissions allow). Not critical if it fails.
                @$this->db->query("ALTER TABLE queue_list ADD COLUMN selection VARCHAR(1) NULL DEFAULT NULL");
                // re-check
                $selCol = $this->db->query("SHOW COLUMNS FROM queue_list LIKE 'selection'");
            }

            // If selection provided, user requested storing A/B into `status` column as text (e.g. '-A-' or '-B-').
            // NOTE: Changing `status` contents to text may break other parts of the app that expect numeric status (0/1).
            if ($selectionVal !== null && $selCol && $selCol->num_rows > 0) {
                // attempt to store both in `selection` column and also put marker into `status` as requested
                $statusMarker = ($selectionVal === 'A') ? '-A-' : '-B-';

                // Ensure status column exists; if it's numeric MySQL will coerce, but try to modify to varchar to allow text
                $statusCol = $this->db->query("SHOW COLUMNS FROM queue_list LIKE 'status'");
                $shouldModify = false;
                if ($statusCol && $statusCol->num_rows > 0) {
                    $stype = $statusCol->fetch_assoc()['Type'];
                    if (stripos($stype, 'int') !== false || stripos($stype, 'tinyint') !== false) {
                        $shouldModify = true;
                    }
                }
                if ($shouldModify) {
                    // try to alter to varchar(10); suppress warnings
                    @$this->db->query("ALTER TABLE queue_list MODIFY status VARCHAR(10) DEFAULT NULL");
                }

                if ($selCol && $selCol->num_rows > 0) {
                    $insert_stmt = $this->db->prepare("INSERT INTO queue_list (transaction_id, queue_no, selection, status) VALUES (?, ?, ?, ?)");
                    $insert_stmt->bind_param("iiss", $transaction_id, $queue_no, $selectionVal, $statusMarker);
                } else {
                    // if no selection column, still insert status marker
                    $insert_stmt = $this->db->prepare("INSERT INTO queue_list (transaction_id, queue_no, status) VALUES (?, ?, ?)");
                    $insert_stmt->bind_param("iss", $transaction_id, $queue_no, $statusMarker);
                }
            } else {
                // No selection provided. Attempt to insert an empty status string when the
                // `status` column supports textual values. If `status` is numeric (int/tinyint),
                // fall back to inserting without specifying status (preserve existing behavior).
                $statusCol = $this->db->query("SHOW COLUMNS FROM queue_list LIKE 'status'");
                $useEmptyStatus = false;
                if ($statusCol && $statusCol->num_rows > 0) {
                    $stype = $statusCol->fetch_assoc()['Type'];
                    if (stripos($stype, 'int') === false && stripos($stype, 'tinyint') === false) {
                        $useEmptyStatus = true;
                    }
                }

                if ($useEmptyStatus) {
                    // insert explicit empty string into status
                    $emptyStatus = '';
                    $insert_stmt = $this->db->prepare("INSERT INTO queue_list (transaction_id, queue_no, status) VALUES (?, ?, ?)");
                    $insert_stmt->bind_param("iss", $transaction_id, $queue_no, $emptyStatus);
                } else {
                    $insert_stmt = $this->db->prepare("INSERT INTO queue_list (transaction_id, queue_no) VALUES (?, ?)");
                    $insert_stmt->bind_param("is", $transaction_id, $queue_no);
                }
            }

            if ($insert_stmt->execute()) {
                $queue_id = $this->db->insert_id;
                $insert_stmt->close();

                $this->db->commit();

                $this->recordWaitingTime($queue_id, $transaction_id);

                return $queue_id;
            } else {
                $this->db->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    public function get_queue()
    {
        extract($_POST);
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT q.*, t.name as wname, ts.symbol as tsymbol 
                                 FROM queue_list q 
                                 INNER JOIN transaction_windows t ON t.id = q.window_id 
                                 INNER JOIN transactions ts ON ts.id = q.transaction_id 
                                 WHERE DATE(q.created_timestamp) = ? AND q.window_id = ? AND q.status = 1 
                                 ORDER BY q.created_timestamp DESC LIMIT 1");
        $stmt->bind_param("si", $today, $wid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $type = '';
            if ($data['type_id'] != null) {
                $type_stmt = $this->db->prepare("SELECT type FROM status WHERE id = ?");
                $type_stmt->bind_param("i", $data['type_id']);
                $type_stmt->execute();
                $type_result = $type_stmt->get_result();
                if ($type_result->num_rows > 0) {
                    $type = $type_result->fetch_assoc()['type'];
                }
            }
            $data['symbol'] = $type;
            return json_encode(array('status' => 1, "data" => $data));
        } else {
            return json_encode(array('status' => 0));
        }
    }

    public function get_window_queue()
    {
        extract($_POST);
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT q.*, t.name as wname, ts.symbol as tsymbol 
                                 FROM queue_list q 
                                 INNER JOIN transaction_windows t ON t.id = q.window_id 
                                 INNER JOIN transactions ts ON ts.id = q.transaction_id 
                                 WHERE DATE(q.created_timestamp) = ? AND q.window_id = ? AND q.status = 1 
                                 ORDER BY q.created_timestamp DESC LIMIT 1");
        $stmt->bind_param("si", $today, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $type = '';
            if ($data['type_id'] != null) {
                $type_stmt = $this->db->prepare("SELECT type FROM status WHERE id = ?");
                $type_stmt->bind_param("i", $data['type_id']);
                $type_stmt->execute();
                $type_result = $type_stmt->get_result();
                if ($type_result->num_rows > 0) {
                    $type = $type_result->fetch_assoc()['type'];
                }
            }
            $data['symbol'] = $type;
            return json_encode(array('status' => 1, "data" => $data));
        } else {
            return json_encode(array('status' => 0));
        }
    }

    public function get_queue_sound()
    {
        extract($_POST);
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT q.*, t.name as wname, ts.symbol as tsymbol 
                                 FROM queue_list q 
                                 INNER JOIN transaction_windows t ON t.id = q.window_id 
                                 INNER JOIN transactions ts ON ts.id = q.transaction_id 
                                 WHERE DATE(q.created_timestamp) = ? AND q.window_id = ? AND q.status = 1 
                                 ORDER BY q.created_timestamp DESC LIMIT 1");
        $stmt->bind_param("si", $today, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $type = '';
            if ($data['type_id'] != null) {
                $type_stmt = $this->db->prepare("SELECT type FROM status WHERE id = ?");
                $type_stmt->bind_param("i", $data['type_id']);
                $type_stmt->execute();
                $type_result = $type_stmt->get_result();
                if ($type_result->num_rows > 0) {
                    $type = $type_result->fetch_assoc()['type'];
                }
            }
            $data['symbol'] = $type;
            return json_encode(array('status' => 1, "data" => $data));
        } else {
            return json_encode(array('status' => 0));
        }
    }

    public function recall_queue()
    {
        extract($_POST);
        $random = rand(1, 100);
        $stmt = $this->db->prepare("UPDATE queue_list SET recall = ? WHERE id = ?");
        $stmt->bind_param("ii", $random, $val);
        if ($stmt->execute()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_waiting_queue()
    {
        extract($_POST);
        $stmt1 = $this->db->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt1->bind_param("i", $id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($result1->num_rows > 0) {
            $transaction = $result1->fetch_assoc();
        }

        $today = date('Y-m-d');
        $stmt2 = $this->db->prepare("SELECT q.* FROM queue_list q WHERE DATE(q.created_timestamp) = ? AND q.transaction_id = ? AND q.status = 0 ORDER BY q.id ASC");
        $stmt2->bind_param("si", $today, $id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows > 0) {
            $data = [];
            while ($row = $result2->fetch_assoc()) {
                $data[$row['id']] = $row;
            }
            return json_encode(array('status' => 1, "data" => $data));
        } else {
            return json_encode(array('status' => 0));
        }
    }

    public function update_queue()
    {
        extract($_POST);
        $login_window_id = (int) $_SESSION['login_window_id'];
        $login_staff_id = (int) $_SESSION['login_id'];
        $today = date('Y-m-d');

        // Get previous_ticket_id from POST (sent from queueNow())
        $previous_ticket_id = isset($_POST['previous_ticket_id']) ? (int)$_POST['previous_ticket_id'] : null;

        // Get transaction_ids allowed for this window
        $transactionWindow = $this->db->query("SELECT * FROM transaction_windows WHERE id = $login_window_id")->fetch_array();
        $tids = $transactionWindow['transaction_ids'] ?: $transactionWindow['transaction_id'];
        $tids_array = array_filter(array_map('intval', explode(',', $tids)));

        if (empty($tids_array)) {
            return json_encode(['status' => 0, 'error' => 'No allowed transactions']);
        }

        $in_clause = implode(',', $tids_array);

        // 1. Priority handling
        $priorityQuery = $this->db->query("SELECT * FROM transactions WHERE priority = 'on' LIMIT 1");
        if ($priorityQuery->num_rows > 0) {
            $priorityTransaction = $priorityQuery->fetch_assoc();
            $priority_tid = (int)$priorityTransaction['id'];

            if (in_array($priority_tid, $tids_array)) {
                $queueCheck = $this->db->query("
                SELECT * FROM queue_list 
                WHERE transaction_id = $priority_tid 
                AND DATE(created_timestamp) = '$today' 
                AND status = 0 
                ORDER BY id ASC 
                LIMIT 1
            ");

                if ($queueCheck->num_rows > 0) {
                    $ticket = $queueCheck->fetch_assoc();
                    $ticket_id = $ticket['id'];

                    $this->db->query("UPDATE queue_list SET status = 1, window_id = $login_window_id, called_at = '" . date('Y-m-d H:i:s') . "' WHERE id = $ticket_id");
                    $this->db->query("INSERT INTO staff_statistics SET staff_id = $login_staff_id");
                    $this->recordWaitingTime($ticket_id, $ticket['transaction_id']);
                    $this->recordServiceStart($ticket_id);

                    if ($previous_ticket_id) {
                        $this->recordServiceEnd($previous_ticket_id);
                    }

                    return $this->get_updated_ticket_data($ticket_id);
                }
            }
        }

        // 2. Fallback for allowed transaction_ids
        $ticketQuery = $this->db->query("
        SELECT * FROM queue_list 
        WHERE DATE(created_timestamp) = '$today' 
        AND status = 0 
        AND transaction_id IN ($in_clause)
        ORDER BY id ASC 
        LIMIT 1
    ");

        if ($ticketQuery->num_rows > 0) {
            $ticket = $ticketQuery->fetch_assoc();
            $ticket_id = $ticket['id'];

            $this->db->query("UPDATE queue_list SET status = 1, window_id = $login_window_id, called_at = '" . date('Y-m-d H:i:s') . "' WHERE id = $ticket_id");
            $this->db->query("INSERT INTO staff_statistics SET staff_id = $login_staff_id");
            $this->recordWaitingTime($ticket_id, $ticket['transaction_id']);
            $this->recordServiceStart($ticket_id);

            if ($previous_ticket_id) {
                $this->recordServiceEnd($previous_ticket_id);
            }

            return $this->get_updated_ticket_data($ticket_id);
        }
        if ($previous_ticket_id) {
            $this->recordServiceEnd($previous_ticket_id);
        }
        return json_encode(['status' => 0]);
    }

    private function get_updated_ticket_data($ticket_id)
    {
        $login_window_id = (int) $_SESSION['login_window_id'];
        $today = date('Y-m-d');
        $data = [];

        $query = $this->db->query("
			SELECT q.*, t.name AS wname, ts.symbol AS tsymbol 
			FROM queue_list q 
			INNER JOIN transaction_windows t ON t.id = q.window_id 
			INNER JOIN transactions ts ON ts.id = q.transaction_id 
			WHERE q.id = $ticket_id 
			AND DATE(q.created_timestamp) = '$today' 
			AND q.window_id = $login_window_id 
			LIMIT 1
		");

        if ($query->num_rows > 0) {
            foreach ($query->fetch_array() as $key => $value) {
                if (!is_numeric($key)) $data[$key] = $value;
            }

            $type = '';
            if (!empty($data['type_id'])) {
                $typeResult = $this->db->query("SELECT type FROM status WHERE id = " . (int)$data['type_id']);
                if ($typeResult && $typeRow = $typeResult->fetch_array()) {
                    $type = $typeRow['type'];
                }
            }
            $data['type'] = $type;

            return json_encode(['status' => 1, 'data' => $data]);
        }

        return json_encode(['status' => 0]);
    }

    public function custom_queue($queue_number)
    {
        extract($_POST);
        $window_id = $_SESSION['login_window_id'];
        $today = date('Y-m-d');

        $tid_stmt = $this->db->prepare("SELECT transaction_id FROM transaction_windows WHERE id = ?");
        $tid_stmt->bind_param("i", $window_id);
        $tid_stmt->execute();
        $tid_result = $tid_stmt->get_result();
        $tid = $tid_result->fetch_assoc()['transaction_id'];

        if (!$tid) {
            return json_encode(array('status' => 0));
        }

        $ticket_stmt = $this->db->prepare("SELECT * FROM queue_list WHERE transaction_id = ? AND DATE(created_timestamp) = ? AND status = 0 AND queue_no = ? LIMIT 1");
        $ticket_stmt->bind_param("iss", $tid, $today, $queue_number);
        $ticket_stmt->execute();
        $ticket_result = $ticket_stmt->get_result();

        if ($ticket_result->num_rows > 0) {
            $update_stmt = $this->db->prepare("UPDATE queue_list SET status = 1, window_id = ? WHERE transaction_id = ? AND DATE(created_timestamp) = ? AND status = 0 AND queue_no = ? LIMIT 1");
            $update_stmt->bind_param("iiss", $window_id, $tid, $today, $queue_number);
            $update_stmt->execute();

            $query_stmt = $this->db->prepare("SELECT q.*, t.name as wname FROM queue_list q INNER JOIN transaction_windows t ON t.id = q.window_id WHERE DATE(q.created_timestamp) = ? AND q.window_id = ? AND q.status = 1 AND q.queue_no = ? LIMIT 1");
            $query_stmt->bind_param("sis", $today, $window_id, $queue_number);
            $query_stmt->execute();
            $query_result = $query_stmt->get_result();

            if ($query_result->num_rows > 0) {
                $data = $query_result->fetch_assoc();
                $type = '';
                if ($data['type_id'] != null) {
                    $type_stmt = $this->db->prepare("SELECT type FROM status WHERE id = ?");
                    $type_stmt->bind_param("i", $data['type_id']);
                    $type_stmt->execute();
                    $type_result = $type_stmt->get_result();
                    if ($type_result->num_rows > 0) {
                        $type = $type_result->fetch_assoc()['type'];
                    }
                }
                $data['type'] = $type;
                $this->recordServiceEnd($old_qid);
                $this->recordWaitingTime($qid, $data['transaction_id']);
                $this->recordServiceStart($qid);
                return json_encode(array('status' => 1, "data" => $data));
            }
        }
        return json_encode(array('status' => 0));
    }

    public function custom_queue_all($queue_number)
    {
        $window_id = $_SESSION['login_window_id'];
        $today = date('Y-m-d');

        $tid_stmt = $this->db->prepare("SELECT transaction_ids FROM transaction_windows WHERE id = ?");
        $tid_stmt->bind_param("i", $window_id);
        $tid_stmt->execute();
        $tid_result = $tid_stmt->get_result();
        $tid = $tid_result->fetch_assoc()['transaction_ids'];

        if (!$tid) {
            return json_encode(array('status' => 0));
        }

        $ticket_stmt = $this->db->prepare("SELECT * FROM queue_list WHERE DATE(created_timestamp) = ? AND queue_no = ? LIMIT 1");
        $ticket_stmt->bind_param("ss", $today, $queue_number);
        $ticket_stmt->execute();
        $ticket_result = $ticket_stmt->get_result();

        if ($ticket_result->num_rows > 0) {
            $update_stmt = $this->db->prepare("UPDATE queue_list SET status = 1, transaction_id = ?, window_id = ? WHERE DATE(created_timestamp) = ? AND queue_no = ? LIMIT 1");
            $update_stmt->bind_param("isss", $tid, $window_id, $today, $queue_number);
            $update_stmt->execute();

            $query_stmt = $this->db->prepare("SELECT q.*, t.name as wname FROM queue_list q INNER JOIN transaction_windows t ON t.id = q.window_id WHERE DATE(q.created_timestamp) = ? AND q.window_id = ? AND q.status = 1 AND q.queue_no = ? LIMIT 1");
            $query_stmt->bind_param("sis", $today, $window_id, $queue_number);
            $query_stmt->execute();
            $query_result = $query_stmt->get_result();

            if ($query_result->num_rows > 0) {
                $data = $query_result->fetch_assoc();
                $type = '';
                if ($data['type_id'] != null) {
                    $type_stmt = $this->db->prepare("SELECT type FROM status WHERE id = ?");
                    $type_stmt->bind_param("i", $data['type_id']);
                    $type_stmt->execute();
                    $type_result = $type_stmt->get_result();
                    if ($type_result->num_rows > 0) {
                        $type = $type_result->fetch_assoc()['type'];
                    }
                }
                $data['type'] = $type;
                return json_encode(array('status' => 1, "data" => $data));
            }
        }
        return json_encode(array('status' => 0));
    }

    public function update_queue_statue()
    {
        extract($_POST);
        $stmt = $this->db->prepare("UPDATE queue_list SET type_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $typeid, $val);
        if ($stmt->execute()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function transfer_queue()
    {
        extract($_POST);
        $tid = '';
        $trans = '';

        if ($to != null) {
            $stmt1 = $this->db->prepare("SELECT id, name FROM transactions WHERE type = ? LIMIT 1");
            $stmt1->bind_param("s", $to);
            $stmt1->execute();
            $result1 = $stmt1->get_result();

            if ($result1->num_rows > 0) {
                $transaction = $result1->fetch_assoc();
                $tid = $transaction['id'];
                $trans = $transaction['name'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }

        if (isset($typeid) && $typeid != null) {
            $stmt2 = $this->db->prepare("INSERT INTO queue_list (status, window_id, transaction_id, type_id, queue_no, transfered) VALUES (0, 0, ?, ?, ?, ?)");
            $stmt2->bind_param("iiis", $tid, $typeid, $qnumber, $trans);
        } else {
            $stmt2 = $this->db->prepare("INSERT INTO queue_list (status, window_id, transaction_id, queue_no, transfered) VALUES (0, 0, ?, ?, ?)");
            $stmt2->bind_param("iis", $tid, $qnumber, $trans);
        }

        if ($stmt2->execute()) {
            $queue = $this->db->insert_id;
            $this->recordServiceEnd($val);
            $this->recordWaitingTime($queue, $tid);
            return 1;
        } else {
            return 0;
        }
    }

    public function get_staff_info()
    {
        $login_window_id = (int) $_SESSION['login_window_id']; // Sanitize input
        $data = [];

        // Get the transaction window info
        $transactionWindow = $this->db->query("SELECT * FROM transaction_windows WHERE id = $login_window_id")->fetch_array();

        // Use multiple transaction_ids if available
        $tids = $transactionWindow['transaction_ids'] ?: $transactionWindow['transaction_id'];
        $tids_array = array_filter(array_map('intval', explode(',', $tids)));
        $in_clause = implode(',', $tids_array);

        // Last 4 completed for this window
        $query = $this->db->query("
			SELECT q.*, t.name AS wname 
			FROM queue_list q 
			INNER JOIN transaction_windows t ON t.id = q.window_id 
			WHERE 
				DATE(q.created_timestamp) = '" . date('Y-m-d') . "' 
				AND q.window_id = $login_window_id 
				AND q.status = 1 
			ORDER BY q.id DESC 
			LIMIT 4
		");

        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                $type = '';
                if (!empty($row['type_id'])) {
                    $typeRes = $this->db->query("SELECT type FROM status WHERE id = " . (int)$row['type_id']);
                    if ($typeRes && $typeRow = $typeRes->fetch_array()) {
                        $type = $typeRow['type'];
                    }
                }

                $data[$row['id']] = [
                    'transaction_id'     => $row['transaction_id'],
                    'window_id'          => $row['window_id'],
                    'queue_no'           => $row['queue_no'],
                    'type_id'            => $type,
                    'created_timestamp'  => $row['created_timestamp'],
                    // include any stored selection or raw status for display on staff UI
                    'selection'          => isset($row['selection']) ? $row['selection'] : null,
                    'status_raw'         => isset($row['status']) ? $row['status'] : null
                ];
            }
        }

        // Count waiting where transaction_id is in $tids_array
        $waiting = 0;
        if (!empty($tids_array)) {
            $waitingQuery = $this->db->query("
				SELECT COUNT(*) as cnt 
				FROM queue_list q 
				WHERE 
					DATE(q.created_timestamp) = '" . date('Y-m-d') . "'  
					AND q.status = 0 
					AND q.transaction_id IN ($in_clause)
			");
            if ($waitingQuery && $row = $waitingQuery->fetch_assoc()) {
                $waiting = $row['cnt'];
            }
        }

        return json_encode([
            'status'  => 1,
            'waiting' => $waiting,
            'data'    => $data
        ]);
    }

    public function get_staff_info_waiting()
    {
        $transactionWindow = $this->db->query("SELECT * FROM transaction_windows WHERE id = " . $_SESSION['login_window_id'])->fetch_array();

        // Get transaction_ids (can be NULL or comma-separated)
        $tids = $transactionWindow['transaction_ids'];
        if (!$tids) {
            $tids = $transactionWindow['transaction_id'];
        }

        // Sanitize and convert to array
        $tids_array = array_filter(array_map('intval', explode(',', $tids)));

        if (empty($tids_array)) {
            return json_encode(['status' => 0, 'data' => [], 'message' => 'No valid transaction IDs.']);
        }

        // Build IN clause
        $in_clause = implode(',', $tids_array);

        $data = [];

        $statusTypes = [];
        $typesQuery = $this->db->query("SELECT id,type, color FROM status");
        if ($typesQuery->num_rows > 0) {
            while ($typeRow = $typesQuery->fetch_assoc()) {
                $statusTypes[$typeRow['id']] = $typeRow['type'];
                $statusColors[$typeRow['id']] = $typeRow['color'];
            }
        }

        // Query queue_list only for relevant transaction_ids
        $query = $this->db->query("SELECT q.*, t.symbol as tsymbol 
        FROM queue_list q
        INNER JOIN transactions t ON t.id = q.transaction_id
        WHERE 
            date(q.created_timestamp) = '" . date('Y-m-d') . "'
            AND q.status = 0
            AND q.transaction_id IN ($in_clause)
        ORDER BY
            CASE
                WHEN type_id = 1 THEN 1
                WHEN type_id = 2 THEN 2
                WHEN type_id = 3 THEN 3
                ELSE 4
            END,
            id ASC
        LIMIT 10
    ");

        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                $data[] = array(
                    'id' => $row['id'],
                    'transaction_id' => $row['transaction_id'],
                    'window_id' => $row['window_id'],
                    'queue_no' => $row['queue_no'],
                    'symbol' => $row['tsymbol'],
                    'type_id' => $row['type_id'] ?? null,
                    'type' => $statusTypes[$row['type_id']] ?? null,
                    'type_color' => $statusColors[$row['type_id']] ?? "#ffffff",
                    'created_timestamp' => $row['created_timestamp'],
                    'waiting_time' => $this->secondsToArabicText(time() - strtotime($row['created_timestamp'])),
                    // expose selection and raw status so UI can show -A- / -B- markers when present
                    'selection' => isset($row['selection']) ? $row['selection'] : null,
                    'status_raw' => isset($row['status']) ? $row['status'] : null
                );
            }
        }

        return json_encode(array('status' => 1, "data" => $data));
    }

    private function secondsToArabicText($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . ' ' . ($hours == 1 ? 'ساعة' : ($hours == 2 ? 'ساعتان' : ($hours <= 10 ? $hours . ' ساعات' : 'ساعة')));
        }

        if ($minutes > 0) {
            if ($minutes == 1) {
                $parts[] = 'دقيقة';
            } elseif ($minutes == 2) {
                $parts[] = 'دقيقتان';
            } elseif ($minutes <= 10) {
                $parts[] = $minutes . ' دقائق';
            } else {
                $parts[] = $minutes . ' دقيقة';
            }
        }

        if ($remainingSeconds > 0 || empty($parts)) {
            if ($remainingSeconds == 1) {
                $parts[] = 'ثانية';
            } elseif ($remainingSeconds == 2) {
                $parts[] = 'ثانيتان';
            } elseif ($remainingSeconds <= 10) {
                $parts[] = $remainingSeconds . ' ثوانٍ';
            } else {
                $parts[] = $remainingSeconds . ' ثانية';
            }
        }

        return implode(' و ', $parts);
    }

    public function get_staff_info_transfered()
    {
        $tid = $this->db->query("SELECT * FROM transaction_windows where id =" . $_SESSION['login_window_id'])->fetch_array()['transaction_id'];
        $data = [];
        //last 4
        $query = $this->db->query("SELECT * FROM queue_list where date(created_timestamp) = '" . date('Y-m-d') . "' and transaction_id = '" . $tid . "' and status = 0
		ORDER BY
  CASE
    WHEN type_id = 1 THEN 1
    WHEN type_id = 2 THEN 2
    WHEN type_id = 3 THEN 3
    ELSE 4
  END,
  id ASC
		limit 4  ");
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                if ($row['transfered'] != null) {
                    if ($row['type_id'] != null) {
                        $type = $this->db->query("SELECT * FROM status WHERE id = " . $row['type_id'])->fetch_array()['type'];
                    } else {
                        $type = '';
                    }
                    $data[] = array(
                        'id' => $row['id'],
                        'transaction_id' => $row['transaction_id'],
                        'window_id' => $row['window_id'],
                        'queue_no' => $row['queue_no'],
                        'type_id' => $type,
                        'transfered' => $row['transfered'],
                        'created_timestamp' => $row['created_timestamp']
                    );
                }
            }
        }
        //return data
        return json_encode(array('status' => 1,  "data" => $data));
    }

    public function call_queue()
    {
        extract($_POST);
        $login_staff_id = (int) $_SESSION['login_id'];
        $window_id = $_SESSION['login_window_id'];
        $today = date('Y-m-d');
        $called_at = date('Y-m-d H:i:s');

        $update_stmt = $this->db->prepare("UPDATE queue_list SET status = 1, window_id = ?, called_at = ? WHERE id = ? AND DATE(created_timestamp) = ? AND status = 0 ORDER BY id ASC LIMIT 1");
        $update_stmt->bind_param("isis", $window_id, $called_at, $queueId, $today);
        $update_stmt->execute();
        $queueGet = $this->db->query("
                SELECT * FROM queue_list 
                WHERE id = $queueId 
                AND DATE(created_timestamp) = '$today' 
                LIMIT 1
            ");

        $ticket = $queueGet->fetch_assoc();

        $this->db->query("INSERT INTO staff_statistics SET staff_id = $login_staff_id");
        $this->recordWaitingTime($queueId, $ticket['transaction_id']);
        $this->recordServiceStart($queueId);
        $this->recordServiceEnd($old_qid);
        $query_stmt = $this->db->prepare("SELECT q.*, t.name as wname, ts.symbol as tsymbol 
                                     FROM queue_list q 
                                     INNER JOIN transaction_windows t ON t.id = q.window_id 
                                     INNER JOIN transactions ts ON ts.id = q.transaction_id 
                                     WHERE q.id = ? AND DATE(q.created_timestamp) = ? AND q.window_id = ? AND q.status = 1 
                                     ORDER BY q.id DESC LIMIT 1");
        $query_stmt->bind_param("isi", $queueId, $today, $window_id);
        $query_stmt->execute();
        $result = $query_stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $type = '';
            if ($data['type_id'] != null) {
                $type_stmt = $this->db->prepare("SELECT type FROM status WHERE id = ?");
                $type_stmt->bind_param("i", $data['type_id']);
                $type_stmt->execute();
                $type_result = $type_stmt->get_result();
                if ($type_result->num_rows > 0) {
                    $type = $type_result->fetch_assoc()['type'];
                }
            }
            $data['type'] = $type;
            return json_encode(array('status' => 1, "data" => $data));
        } else {
            return json_encode(array('status' => 0));
        }
    }
    public function recordWaitingTime($queue_id, $transaction_id, $status_id = null)
    {
        if ($queue_id != null  && $transaction_id != null) {

            $check = $this->db->query("SELECT id FROM waiting_stats WHERE queue_id = $queue_id AND end_time IS NULL");

            if ($check->num_rows == 0) {
                $this->db->query("INSERT INTO waiting_stats (queue_id, transaction_id, status_id, arrival_time) 
                         VALUES ($queue_id, $transaction_id, " . ($status_id ?: 'NULL') . ", NOW())");
            }
        }
    }

    public function recordServiceStart($queue_id)
    {
        if ($queue_id != null) {
            $this->db->query("UPDATE waiting_stats SET start_time = NOW() 
                     WHERE queue_id = $queue_id AND start_time IS NULL");
        }
    }

    public function recordServiceEnd($queue_id)
    {
        if ($queue_id != null) {
            $this->db->query("UPDATE waiting_stats 
                     SET end_time = NOW(),
                         waiting_duration = TIMESTAMPDIFF(SECOND, arrival_time, start_time),
                         service_duration = TIMESTAMPDIFF(SECOND, start_time, end_time)
                     WHERE queue_id = $queue_id AND end_time IS NULL");
        }
    }
}
