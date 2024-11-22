<?php
namespace App;

use mysqli;
use mysqli_sql_exception;

class ResourceService {
    private $connection;

    public function __construct(DatabaseManager $db) {
        $this->connection = $db->get_connection();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function createResource($user_id, $title, $content, $category) {
        $tablename = ($category === 'code') ? 'Code_post' : 'Model_post';

        $stmt = $this->connection->prepare("INSERT INTO $tablename (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "자료가 성공적으로 생성되었습니다."];
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

    public function deleteResource($id, $category) {
        $tablename = ($category === 'code') ? 'Code_post' : 'Model_post';

        $stmt = $this->connection->prepare("DELETE FROM $tablename WHERE post_id = ?");
        $stmt->bind_param("i", $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "자료가 성공적으로 삭제되었습니다."];
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

    public function getAllResources($category) {
        $tablename = ($category === 'code') ? 'Code_post' : 'Model_post';

        $stmt = $this->connection->prepare("SELECT post_id, title, content, user_id, created_at, updated_at FROM $tablename");
        try {
            $stmt->execute();
            $stmt->bind_result($post_id, $title, $content, $user_id, $created_at, $updated_at);

            $resources = [];
            while ($stmt->fetch()) {
                array_push($resources, [
                    "post_id" => $post_id,
                    "title" => $title,
                    "content" => $content,
                    "user_id" => $user_id,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ]);
            }

            return ["status" => "success", "message" => $resources];
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

    public function getResourceById($id, $category) {
        $tablename = ($category === 'code') ? 'Code_post' : 'Model_post';

        $stmt = $this->connection->prepare("SELECT post_id, user_id, title, content, created_at, updated_at FROM $tablename WHERE post_id = ?");
        $stmt->bind_param("i", $id);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $resource = $result->fetch_assoc();
            return ["status" => "success", "message" => $resource];
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

    public function updateResource($id, $title, $content, $category) {
        $tablename = ($category === 'code') ? 'Code_post' : 'Model_post';

        $stmt = $this->connection->prepare("UPDATE $tablename SET title = ?, content = ? WHERE post_id = ?");
        $stmt->bind_param("ssi", $title, $content, $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "자료가 성공적으로 수정되었습니다."];
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