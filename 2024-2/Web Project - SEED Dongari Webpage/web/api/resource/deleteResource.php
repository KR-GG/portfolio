<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\ResourceService;
use App\TokenManager;

$databaseManager = new DatabaseManager();
$resourceService = new ResourceService($databaseManager);
$tokenManager = new TokenManager($databaseManager);

try {
    $tokenData = $tokenManager->validateToken();
    $user_id = $tokenData->user_id;
    $user_role = $tokenData->role;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["status" => $e->getCode(), "message" => $e->getMessage()]);
    exit();
}

if ($user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Not authorized to create resources"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit();
}

if (!isset($data['id']) || !isset($data['category'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit();
}

$id = intval($data['id']);
$category = $data['category'];
if ($category !== 'code' && $category !== 'model') {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid category"]);
    exit();
}

$result = $resourceService->deleteResource($id, $category);
if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);