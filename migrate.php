<?php

// Migration script to create the database schema
class CreateDatabaseSchema
{
    private $connection;

    public function __construct($host, $user, $password, $database)
    {
        // Establish database connection
        $this->connection = new mysqli($host, $user, $password, $database);

        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function migrate()
    {
        // Start transaction
        $this->connection->begin_transaction();

        try {
            // Create `file_uploads` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `file_uploads` (
                    `id` int(30) NOT NULL AUTO_INCREMENT,
                    `file_path` text NOT NULL,
                    `date_uploaded` datetime NOT NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `queue_list` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `queue_list` (
                    `id` int(30) NOT NULL AUTO_INCREMENT,
                    `transaction_id` int(30) NOT NULL,
                    `window_id` int(30) DEFAULT 0,
                    `queue_no` varchar(50) NOT NULL,
                    `status` tinyint(1) NOT NULL DEFAULT 0,
                    `type_id` int(2) DEFAULT NULL,
                    `transfered` varchar(225) DEFAULT NULL,
                    `recall` int(10) NOT NULL DEFAULT 0,
                    `called_at` timestamp NULL DEFAULT NULL,
                    `date_created` datetime NOT NULL DEFAULT current_timestamp(),
                    `created_timestamp` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`),
                    KEY `type_cons` (`type_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `settings` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `settings` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `password` varchar(225) NOT NULL,
                    `name` varchar(225) NOT NULL,
                    `image` varchar(225) NOT NULL,
                    `ticket_company` enum('on','off') NOT NULL DEFAULT 'off',
                    `ticket_logo` enum('on','off') NOT NULL DEFAULT 'off',
                    `ticket_date` enum('on','off') NOT NULL DEFAULT 'off',
                    `ticket_note` enum('on','off') NOT NULL DEFAULT 'off',
                    `note` varchar(225) NOT NULL,
                    `period` int(11) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `staff_statistics` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `staff_statistics` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `staff_id` int(11) NOT NULL,
                    `processed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `status` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `status` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `type` varchar(1) NOT NULL,
                    `color` varchar(50) NOT NULL,
                    `ordering` varchar(1) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `transactions` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `transactions` (
                    `id` int(30) NOT NULL AUTO_INCREMENT,
                    `name` text NOT NULL,
                    `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=Inactive,=1 Active',
                    `sorting` enum('on','off') NOT NULL DEFAULT 'off',
                    `active` enum('on','off') DEFAULT 'on',
                    `priority` enum('on','off') NOT NULL,
                    `symbol` varchar(1) DEFAULT NULL,
                    `type` varchar(225) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `transaction_windows` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `transaction_windows` (
                    `id` int(30) NOT NULL AUTO_INCREMENT,
                    `transaction_id` int(11) DEFAULT NULL,
                    `transaction_ids` text DEFAULT NULL,
                    `name` varchar(100) NOT NULL,
                    `status` tinyint(100) DEFAULT 1 COMMENT '0=Inactive,1=Active',
                    PRIMARY KEY (`id`),
                    KEY `fk_cons` (`transaction_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `users` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `users` (
                    `id` int(30) NOT NULL AUTO_INCREMENT,
                    `name` text NOT NULL,
                    `window_id` int(30) NOT NULL,
                    `type` tinyint(4) NOT NULL DEFAULT 2 COMMENT '1 = Admin, 2= staff',
                    `transfer` enum('yes','no') NOT NULL DEFAULT 'no',
                    `username` varchar(100) NOT NULL,
                    `password` text NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `user_permissions` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `user_permissions` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) NOT NULL,
                    `transaction_id` int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `con1` (`transaction_id`),
                    KEY `con2` (`user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Create `waiting_stats` table
            $this->connection->query("
                CREATE TABLE IF NOT EXISTS `waiting_stats` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `queue_id` int(11) NOT NULL,
                    `transaction_id` int(11) NOT NULL,
                    `status_id` int(11) DEFAULT NULL,
                    `arrival_time` datetime NOT NULL,
                    `start_time` datetime DEFAULT NULL,
                    `end_time` datetime DEFAULT NULL,
                    `waiting_duration` int(11) DEFAULT NULL COMMENT 'in seconds',
                    `service_duration` int(11) DEFAULT NULL COMMENT 'in seconds',
                    PRIMARY KEY (`id`),
                    KEY `queue_id` (`queue_id`),
                    KEY `transaction_id` (`transaction_id`),
                    KEY `status_id` (`status_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");

            // Add foreign key constraints
            $this->connection->query("
                ALTER TABLE `queue_list`
                ADD CONSTRAINT `type_cons` FOREIGN KEY (`type_id`) REFERENCES `status`(`id`)
            ");

            $this->connection->query("
                ALTER TABLE `transaction_windows`
                ADD CONSTRAINT `fk_cons` FOREIGN KEY (`transaction_id`) REFERENCES `transactions`(`id`) ON DELETE CASCADE
            ");

            $this->connection->query("
                ALTER TABLE `user_permissions`
                ADD CONSTRAINT `con1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions`(`id`) ON DELETE CASCADE,
                ADD CONSTRAINT `con2` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ");

            // Commit transaction
            $this->connection->commit();

            echo "Migration completed successfully.";
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->connection->rollback();
            echo "Migration failed: " . $e->getMessage();
        }
    }

    public function __destruct()
    {
        // Close connection
        $this->connection->close();
    }
}

// Usage
$host = "localhost";
$user = "root";
$password = "";
$database = "q155";

$migration = new CreateDatabaseSchema($host, $user, $password, $database);
$migration->migrate();
