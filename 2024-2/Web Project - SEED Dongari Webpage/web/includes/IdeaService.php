<?php
namespace App;

use mysqli;
use mysqli_sql_exception;

class IdeaService {
    private $connection;

    public function __construct(DatabaseManager $db) {
        $this->connection = $db->get_connection();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function saveIdea($title, $content, $user_id) {
        // Check if user_id exists
        $userCheckStmt = $this->connection->prepare("SELECT COUNT(*) FROM User WHERE user_id = ?");
        $userCheckStmt->bind_param("i", $user_id);
        $userCheckStmt->execute();
        $userCheckStmt->bind_result($userCount);
        $userCheckStmt->fetch();
        $userCheckStmt->close();

        if ($userCount == 0) {
            return ["status" => "error", "message" => "Invalid user_id"];
        }

        $stmt = $this->connection->prepare("INSERT INTO Idea_post (title, content, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $user_id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "Idea posted successfully"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }

    public function getAllIdeas() {
        $stmt = $this->connection->prepare("SELECT idea_id, title, content, user_id, created_at, updated_at FROM Idea_post");
        try {
            $stmt->execute();
            $stmt->bind_result($idea_id, $title, $content, $user_id, $created_at, $updated_at);

            $ideas = [];
            while ($stmt->fetch()) {
                array_push($ideas, [
                    "idea_id" => $idea_id,
                    "title" => $title,
                    "content" => $content,
                    "user_id" => $user_id,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ]);
            }

            return ["status" => "success", "message" => $ideas];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }

    public function getIdeaById($idea_id) {
        $stmt = $this->connection->prepare("SELECT idea_id, title, content, user_id, created_at, updated_at FROM Idea_post WHERE idea_id = ?");
        $stmt->bind_param("i", $idea_id);

        try {
            $stmt->execute();
            $stmt->bind_result($idea_id, $title, $content, $user_id, $created_at, $updated_at);

            $idea = null;
            $count = 0;
            while ($stmt->fetch()) {
                $idea = [
                    "idea_id" => $idea_id,
                    "title" => $title,
                    "content" => $content,
                    "user_id" => $user_id,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
                $count++;
            }

            if ($count > 1) {
                return ["status" => "error", "message" => "Multiple ideas found with the same ID"];
            }

            return $idea ? ["status" => "success", "message" => $idea] : ["status" => "error", "message" => "Idea not found"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }

    public function updateIdea($idea_id, $title, $content) {
        $stmt = $this->connection->prepare("UPDATE Idea_post SET title = ?, content = ? WHERE idea_id = ?");
        $stmt->bind_param("ssi", $title, $content, $idea_id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "Idea updated successfully"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }

    public function deleteIdea($idea_id) {
        $stmt = $this->connection->prepare("DELETE FROM Idea_post WHERE idea_id = ?");
        $stmt->bind_param("i", $idea_id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "Idea deleted successfully"];
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case 1062:
                    return ["status" => $e->getCode(), "message" => "Duplicate entry: " . $e->getMessage()];
                case 1451:
                    return ["status" => $e->getCode(), "message" => "Cannot delete or update: " . $e->getMessage()];
                case 1452:
                    return ["status" => $e->getCode(), "message" => "Cannot add or update: " . $e->getMessage()];
                case 1048:
                    return ["status" => $e->getCode(), "message" => "Column cannot be null: " . $e->getMessage()];
                default:
                    return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
            }
        } finally {
            $stmt->close();
        }
    }
}