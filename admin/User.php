<?php
class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function login()
    {
        extract($_POST);
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $password_hashed = $user['password'];

            // Check for MD5 hash (32 hex characters) and verify
            if (strlen($password_hashed) === 32 && md5($password) === $password_hashed) {
                // Rehash and update the password in the database
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_hash, $user['id']);
                $update_stmt->execute();

                // Log the user in
                foreach ($user as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $value;
                    }
                }
                return 1;
            }
            // Verify modern password hash
            elseif (password_verify($password, $password_hashed)) {
                foreach ($user as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $value;
                    }
                }
                return 1;
            } else {
                return 3; // Incorrect password
            }
        } else {
            return 3; // User not found
        }
    }

    public function logout()
    {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:login.php");
    }

    public function save_user()
    {
        extract($_POST);
        $data = " name = ? ";
        $data .= ", username = ? ";
        $data .= ", type = ? ";
        $data .= ", window_id = ? ";

        $params = [$name, $username, $type, $window_id];
        $types = "ssii";

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $data .= ", password = ? ";
            $params[] = $hashed_password;
            $types .= "s";
        }

        $chk_stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND id != ?");
        $chk_stmt->bind_param("si", $username, $id);
        $chk_stmt->execute();
        $chk_result = $chk_stmt->get_result();

        if ($chk_result->num_rows > 0) {
            return 2;
            exit;
        }

        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO users SET " . $data);
            $stmt->bind_param($types, ...$params);
        } else {
            $data .= " WHERE id = ?";
            $params[] = $id;
            $types .= "i";
            $stmt = $this->db->prepare("UPDATE users SET " . $data);
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt->execute()) {
            return 1;
        }
    }

    public function delete_user()
    {
        extract($_POST);
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return 1;
        }
    }
}
