<?php

// Data Seeder script to populate tables with initial data
class DataSeeder
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

    public function seed()
    {
        // Start transaction
        $this->connection->begin_transaction();

        try {
            // Seed `file_uploads` table
            $this->connection->query("
                INSERT INTO `file_uploads` (`id`, `file_path`, `date_uploaded`) VALUES
                (21, '1727961900_1 (4).jpg', '2024-10-03 16:25:59'),
                (22, '1727961900_1 (5).jpg', '2024-10-03 16:25:59'),
                (23, '1727961900_1 (2).jpg', '2024-10-03 16:25:59'),
                (24, '1727961900_1 (3).jpg', '2024-10-03 16:25:59'),
                (25, '1727961900_1 (8).jpg', '2024-10-03 16:25:59'),
                (26, '1727961900_1 (9).jpg', '2024-10-03 16:25:59'),
                (27, '1727961900_1 (6).jpg', '2024-10-03 16:25:59'),
                (28, '1727961900_1 (7).jpg', '2024-10-03 16:25:59'),
                (29, '1727961960_1 (10).jpg', '2024-10-03 16:26:00'),
                (30, '1727961960_1 (1).jpg', '2024-10-03 16:26:00'),
                (31, '1755266520_file_example_MP4_480_1_5MG.mp4', '2025-08-15 17:02:06')
            ");

            // Seed `queue_list` table
            $this->connection->query("
                INSERT INTO `queue_list` (`id`, `transaction_id`, `window_id`, `queue_no`, `status`, `type_id`, `transfered`, `recall`, `called_at`, `date_created`, `created_timestamp`) VALUES
                (789, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-10-17 18:05:55', '2025-10-17 18:05:55'),
                (790, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-10-21 20:47:10', '2025-10-21 20:47:10'),
                (791, 1, 1, '1001', 1, 1, NULL, 0, '2025-10-22 09:38:54', '2025-10-22 12:38:14'),
                (792, 14, 0, '1001', 0, 1, 'عيادة الطبيب', 0, NULL, '2025-10-22 12:38:58', '2025-10-22 12:38:58'),
                (793, 1, 0, '1002', 0, NULL, NULL, 0, NULL, '2025-10-22 12:49:05', '2025-10-22 12:49:05'),
                (794, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-10-28 23:11:33', '2025-10-28 23:11:33')
            ");

            // Fix column count and seed `settings` table
            $this->connection->query("
                INSERT INTO `settings` 
                (`id`, `password`, `name`, `image`, `ticket_company`, `ticket_logo`, `ticket_date`, `ticket_note`, `note`, `period`) VALUES
                (1, '$2y$10$seGQHo1zV69aHrMF5tMGye/8upyZnDQiIbERVrl2pj8yBtHW0D2Vi', ' ', '1750210560_98AA98AC998598B9-98B998B3998A98B1-98A7998498B598AD998A.webp', 'on', 'off', 'on', 'off', 'تفضل بالجلوس في قسم الإنتظار ,شاكرين صبرك ونتمنى لك الشفاء العاجل', 15)
            ");

            // Seed other tables (example for `users`)
            $this->connection->query("
                INSERT INTO `users` 
                (`id`, `name`, `window_id`, `type`, `transfer`, `username`, `password`) VALUES
                (1, 'Administrator', 0, 1, 'yes', 'admin', '$2y$10$CEn5cqXOZ3eRjDHUBVgXRu5rP8VL8KmYATzCZvYdNmIoH4A216axm'),
                (4, 'عيادة الفرز 1', 1, 2, 'yes', 'ER1', '$2y$10$D3KebVwJFw3h6K7J.Er1f.isc9nLSQhQQjP5pYNKIrwDJMMB03PsS'),
                (5, 'عيادة الطبيب 1', 19, 2, 'yes', 'ER3', '$2y$10$Pn5dJOFufgA5AMMbn/S0s.BGc/zkM0hV9GQtrd1wHYKYqGAUNiYDG'),
                (7, 'قسم الملاحظة A', 21, 2, 'no', 'ER5', '$2y$10$WY4TGY1lvnQ2w3DYKCai.OubI/lXXr2jd5Sr1M11Z02gBgnptjDMq')
            ");

            // Commit transaction
            $this->connection->commit();

            echo "Seeding completed successfully.";
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->connection->rollback();
            echo "Seeding failed: " . $e->getMessage();
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
$password = ""; // Replace with your MySQL password
$database = "q155";

$seeder = new DataSeeder($host, $user, $password, $database);
$seeder->seed();
