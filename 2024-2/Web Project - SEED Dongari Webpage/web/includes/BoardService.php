<?php
namespace App;

use mysqli;
use mysqli_sql_exception;

class BoardService {
    private $connection;

    public function __construct(DatabaseManager $db) {
        $this->connection = $db->get_connection();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function createBoard($user_id, $title, $content) {
        $stmt = $this->connection->prepare("INSERT INTO Board (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "게시판이 성공적으로 생성되었습니다."];
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

    public function deleteBoard($id) {
        $stmt = $this->connection->prepare("DELETE FROM Board WHERE board_id = ?");
        $stmt->bind_param("i", $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "게시판이 성공적으로 삭제되었습니다."];
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

    public function updateBoard($id, $title, $content) {
        $stmt = $this->connection->prepare("UPDATE Board SET title = ?, content = ? WHERE board_id = ?");
        $stmt->bind_param("ssi", $title, $content, $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "게시판이 성공적으로 수정되었습니다."];
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

    public function getBoardById($id) {
        $stmt = $this->connection->prepare("SELECT board_id, user_id, title, content, created_at, updated_at FROM Board WHERE board_id = ?");
        $stmt->bind_param("i", $id);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $board = $result->fetch_assoc();
            return ["status" => "success", "message" => $board];
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

    public function getAllBoards() {
        $stmt = $this->connection->prepare("SELECT board_id, user_id, title, content, created_at, updated_at FROM Board");
        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $boards = $result->fetch_all(MYSQLI_ASSOC);
            return ["status" => "success", "message" => $boards];
        } catch (mysqli_sql_exception $e) {
            return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
        } finally {
            $stmt->close();
        }
    }
}