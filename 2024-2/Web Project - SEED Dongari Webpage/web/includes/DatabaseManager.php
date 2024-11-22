<?php
namespace App;

use mysqli;
use mysqli_sql_exception;
use Exception;

class DatabaseManager {
    private $connection;
    private $host = "host.docker.internal";
    private $username = "admin";
    private $password = "Asd8875!?";
    private $dbname = "SEED";

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        $this->connection->set_charset("utf8mb4");
    }

    public function runMigrations() {
        $this->createUserTable();
        $this->createEventTable();
        $this->createEventApplyTable();
        $this->createBoardTable();
        $this->createIdeaPostTable();
        $this->createCommentTable();
        $this->createCodePostTable();
        $this->createModelPostTable();
        $this->createTokenBlacklistTable();
        $this->createAdminUser();
    }

    public function dropAllTables() {
        $this->connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $tables = $this->connection->query("SHOW TABLES")->fetch_all(MYSQLI_NUM);
        foreach ($tables as $table) {
            $table = $table[0];
            $stmt = $this->connection->prepare("DROP TABLE IF EXISTS $table");
            if ($stmt->execute() === TRUE) {
                echo "Table $table dropped successfully\n";
            } else {
                echo "Error dropping table $table: " . $stmt->error . "\n";
            }
            $stmt->close();
        }

        $this->connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function truncateAllTables() {
        $this->connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $tables = $this->connection->query("SHOW TABLES")->fetch_all(MYSQLI_NUM);
        foreach ($tables as $table) {
            $table = $table[0];
            $stmt = $this->connection->prepare("TRUNCATE TABLE $table");
            if ($stmt->execute() === TRUE) {
                echo "Table $table truncated successfully\n";
            } else {
                echo "Error truncating table $table: " . $stmt->error . "\n";
            }
            $stmt->close();
        }

        $this->connection->query("SET FOREIGN_KEY_CHECKS = 1");
    }

    private function createUserTable() {
        $sql = "CREATE TABLE IF NOT EXISTS User (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(60) NOT NULL UNIQUE,
            role ENUM('admin', 'user', 'pending') NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table User created successfully\n";
        } else {
            echo "Error creating User table: " . $this->connection->error . "\n";
        }
    }

    private function createEventTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Event (
            event_id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(50) NOT NULL,
            content TEXT,
            start_date DATETIME NOT NULL,
            end_date DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Event created successfully\n";
        } else {
            echo "Error creating Event table: " . $this->connection->error . "\n";
        }
    }

    private function createEventApplyTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Event_apply (
            apply_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_id INT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES User(user_id),
            FOREIGN KEY (event_id) REFERENCES Event(event_id)
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Event_apply created successfully\n";
        } else {
            echo "Error creating Event_apply table: " . $this->connection->error . "\n";
        }
    }

    private function createBoardTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Board (
            board_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES User(user_id)
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Board created successfully\n";
        } else {
            echo "Error creating Board table: " . $this->connection->error . "\n";
        }
    }

    private function createIdeaPostTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Idea_post (
            idea_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(50) NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES User(user_id)
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Idea_post created successfully\n";
        } else {
            echo "Error creating Idea_post table: " . $this->connection->error . "\n";
        }
    }

    private function createCommentTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Comment (
            comment_id INT AUTO_INCREMENT PRIMARY KEY,
            idea_id INT NOT NULL,
            user_id INT NOT NULL,
            author_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (idea_id) REFERENCES Idea_post(idea_id),
            FOREIGN KEY (user_id) REFERENCES User(user_id),
            FOREIGN KEY (author_id) REFERENCES User(user_id)
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Comment created successfully\n";
        } else {
            echo "Error creating Comment table: " . $this->connection->error . "\n";
        }
    }

    private function createCodePostTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Code_post (
            post_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(50) NOT NULL,
            content TEXT NOT NULL,
            file_url VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES User(user_id)
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Code_post created successfully\n";
        } else {
            echo "Error creating Code_post table: " . $this->connection->error . "\n";
        }
    }

    private function createModelPostTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Model_post (
            post_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(50) NOT NULL,
            content TEXT NOT NULL,
            file_url VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES User(user_id)
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Model_post created successfully\n";
        } else {
            echo "Error creating Model_post table: " . $this->connection->error . "\n";
        }
    }

    private function createTokenBlacklistTable() {
        $sql = "CREATE TABLE IF NOT EXISTS Token_blacklist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token VARCHAR(255) NOT NULL,
            expiration_time DATETIME NOT NULL
        )";

        if ($this->connection->query($sql) === TRUE) {
            echo "Table Token_blacklist created successfully\n";
        } else {
            echo "Error creating Token_blacklist table: " . $this->connection->error . "\n";
        }
    }

    private function createAdminUser() {
        // Define admin user details
        $username = "admin";
        $email = "admin@example.com";
        $role = "admin";
        $password = password_hash("admin", PASSWORD_DEFAULT);

        // Check if an admin user already exists
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM User WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // If no admin user exists, create one
        if ($count === 0) {
            $stmt = $this->connection->prepare("INSERT INTO User (username, email, role, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $role, $password);
            if ($stmt->execute() === TRUE) {
                echo "Admin user created successfully\n";
            } else {
                echo "Error creating admin user: " . $stmt->error . "\n";
            }
        } else {
            echo "Admin user already exists\n";
        }
    }

    public function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function get_connection() {
        return $this->connection;
    }
}