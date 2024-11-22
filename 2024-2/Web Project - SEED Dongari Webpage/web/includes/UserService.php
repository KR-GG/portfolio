<?php
namespace App;

use mysqli;
use mysqli_sql_exception;

class UserService {
    private $connection;

    public function __construct(DatabaseManager $db) {
        $this->connection = $db->get_connection();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function saveUserApply($username, $password_hash, $email, $role) {
        $stmt = $this->connection->prepare("INSERT INTO User (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password_hash, $email, $role);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "User registered successfully"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    // Duplicate entry error code
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    // Cannot delete or update a parent row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    // Cannot add or update a child row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    // Column cannot be null
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    // Other database errors
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }

    public function getAllUsers() {
        $stmt = $this->connection->prepare("SELECT user_id, username, email, role, created_at, updated_at FROM User");
        
        try { 
            $stmt->execute();
            $stmt->bind_result($user_id, $username, $email, $role, $created_at, $updated_at);

            $users = [];
            while ($stmt->fetch()) {
                array_push($users, [
                    "user_id" => $user_id, 
                    "username" => $username, 
                    "email" => $email, "role" => $role, 
                    "created_at" => $created_at, 
                    "updated_at" => $updated_at
                ]);
            }

            return ["status" => "success", "data" => $users];
        } catch (mysqli_sql_exception $e) {
            return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
        } finally {
            $stmt->close();
        }
    }

    public function getUserById($user_id) {
        $stmt = $this->connection->prepare("SELECT user_id, username, email, role, created_at, updated_at FROM User WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        
        try { 
            $stmt->execute();
            $stmt->bind_result($user_id, $username, $email, $role, $created_at, $updated_at);

            $user = null;
            $count = 0;
            while ($stmt->fetch()) {
                $user = [
                    "user_id" => $user_id, 
                    "username" => $username, 
                    "email" => $email, 
                    "role" => $role, 
                    "created_at" => $created_at, 
                    "updated_at" => $updated_at
                ];
                $count++;
            }

            if ($count > 1) {
                return ["status" => "error", "message" => "Multiple users found with the same ID"];
            }

            return $user ? ["status" => "success", "message" => $user] : ["status" => "error", "message" => "User not found"];
        } catch (mysqli_sql_exception $e) {
            return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
        } finally {
            $stmt->close();
        }
    }

    public function getUserByEmail($email) {
        $stmt = $this->connection->prepare("SELECT user_id, username, password, email, role, created_at, updated_at FROM User WHERE email = ?");
        $stmt->bind_param("s", $email);
        
        try { 
            $stmt->execute();
            $stmt->bind_result($user_id, $username, $password, $email, $role, $created_at, $updated_at);

            $user = null;
            $count = 0;
            while ($stmt->fetch()) {
                $user = [
                    "user_id" => $user_id, 
                    "username" => $username, 
                    "password" => $password,
                    "email" => $email, 
                    "role" => $role, 
                    "created_at" => $created_at, 
                    "updated_at" => $updated_at
                ];
                $count++;
            }

            if ($count > 1) {
                return ["status" => "error", "message" => "Multiple users found with the same username"];
            }

            return $user ? ["status" => "success", "message" => $user] : ["status" => "error", "message" => "User not found"];
        } catch (mysqli_sql_exception $e) {
            return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
        } finally {
            $stmt->close();
        }
    }

    public function getPendingUsers() {
        $stmt = $this->connection->prepare("SELECT user_id, username, email, role, created_at, updated_at FROM User WHERE role = ?");
        $pendingRole = Role::PENDING->value;
        $stmt->bind_param("s", $pendingRole);
        
        try { 
            $stmt->execute();
            $stmt->bind_result($user_id, $username, $email, $role, $created_at, $updated_at);

            $users = [];
            while ($stmt->fetch()) {
                array_push($users, [
                    "user_id" => $user_id, 
                    "username" => $username, 
                    "email" => $email, "role" => $role, 
                    "created_at" => $created_at, 
                    "updated_at" => $updated_at
                ]);
            }

            return ["status" => "success", "message" => $users];
        } catch (mysqli_sql_exception $e) {
            return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
        } finally {
            $stmt->close();
        }
    }

    public function changeUserRole($user_id, $role) {
        $stmt = $this->connection->prepare("UPDATE User SET role = ? WHERE user_id = ?");
        $stmt->bind_param("si", $role, $user_id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "User role updated successfully"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    // Duplicate entry error code
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    // Cannot delete or update a parent row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    // Cannot add or update a child row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    // Column cannot be null
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    // Other database errors
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }

    public function deletePendingUser($user_id) {
        $stmt = $this->connection->prepare("DELETE FROM User WHERE user_id = ? AND role = ?");
        $pendingRole = Role::PENDING->value;
        $stmt->bind_param("is", $user_id, $pendingRole);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "User deleted successfully"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    // Duplicate entry error code
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    // Cannot delete or update a parent row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    // Cannot add or update a child row: a foreign key constraint fails
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    // Column cannot be null
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    // Other database errors
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }
}