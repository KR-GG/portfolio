<?php
namespace App;

use mysqli;
use mysqli_sql_exception;

class CommentService {
    private $connection;

    public function __construct(DatabaseManager $db) {
        $this->connection = $db->get_connection();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function postComment($idea_id, $user_id, $author_id, $content) {
        $stmt = $this->connection->prepare("INSERT INTO Comment (idea_id, user_id, author_id, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $idea_id, $user_id, $author_id, $content);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "댓글 등록이 성공적입니다."];
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

    public function getAllComments() {
        $stmt = $this->connection->prepare("SELECT comment_id, idea_id, content, author_id, created_at, updated_at FROM Comment ORDER BY idea_id DESC");
        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $comments = $result->fetch_all(MYSQLI_ASSOC);
            return ["status" => "success", "message" => $comments];
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

    public function getCommentsByIdeaId($idea_id) {
        $stmt = $this->connection->prepare("SELECT comment_id, idea_id, content, author_id, created_at, updated_at FROM Comment WHERE idea_id = ?");
        $stmt->bind_param("i", $idea_id); // i는 integer
        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $comments = $result->fetch_all(MYSQLI_ASSOC);
            return ["status" => "success", "message" => $comments];
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

    public function deleteComment($id) {
        $stmt = $this->connection->prepare("DELETE FROM Comment WHERE comment_id = ?");
        $stmt->bind_param("i", $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "댓글삭제성공"];
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

    public function updateComment($id, $content) {
        $stmt = $this->connection->prepare("UPDATE Comment SET content = ? WHERE comment_id = ?");
        $stmt->bind_param("si", $content, $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "Comment updated successfully"];
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