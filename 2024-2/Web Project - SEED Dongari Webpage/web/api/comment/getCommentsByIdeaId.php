<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\CommentService;

$databaseManager = new DatabaseManager();
$commentService = new CommentService($databaseManager);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

if (!isset($_GET['idea_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Idea ID is required"]);
    exit();
}

$idea_id = intval($_GET['idea_id']);

$result = $commentService->getCommentsByIdeaId($idea_id);

if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);