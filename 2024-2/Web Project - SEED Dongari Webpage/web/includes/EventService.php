<?php
namespace App;

use mysqli;
use mysqli_sql_exception;

class EventService {
    private $connection;

    public function __construct(DatabaseManager $db) {
        $this->connection = $db->get_connection();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function saveEvent($title, $start_date, $end_date, $content = null) {
        $stmt = $this->connection->prepare("INSERT INTO Event (title, start_date, end_date, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $start_date, $end_date, $content);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "Event posted successfully"];
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

    public function deleteEvent($id) {
        $stmt = $this->connection->prepare("DELETE FROM Event WHERE event_id = ?");
        $stmt->bind_param("i", $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "Event deleted successfully"];
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

    public function updateEvent($id, $title, $start_date, $end_date, $content=null) {
        $stmt = $this->connection->prepare("UPDATE Event SET title = ?, start_date = ?, end_date = ?, content = ? WHERE event_id = ?");
        $stmt->bind_param("ssssi", $title, $start_date, $end_date, $content, $id);

        try {
            $stmt->execute();
            return ["status" => "success", "message" => "Event updated successfully"];
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

    public function getAllEvents() {
        $stmt = $this->connection->prepare("SELECT event_id, title, start_date, end_date, content, created_at, updated_at FROM Event");
        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $events = $result->fetch_all(MYSQLI_ASSOC);
            return ["status" => "success", "message" => $events];
        } catch (mysqli_sql_exception $e) {
            return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
        } finally {
            $stmt->close();
        }
    }

    public function getEventById($id) {
        $stmt = $this->connection->prepare("SELECT event_id, title, start_date, end_date, content, created_at, updated_at FROM Event WHERE event_id = ?");
        $stmt->bind_param("i", $id);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
            return ["status" => "success", "message" => $event];
        } catch (mysqli_sql_exception $e) {
            return ["status" => $e->getCode(), "message" => "Database error: " . $e->getMessage()];
        } finally {
            $stmt->close();
        }
    }
}