<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\BoardService;

$databaseManager = new DatabaseManager();
$boardService = new BoardService($databaseManager);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

if (!isset($_GET['board_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Board ID is required"]);
    exit();
}

$board_id = intval($_GET['board_id']);

$result = $boardService->getBoardById($board_id);
if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);