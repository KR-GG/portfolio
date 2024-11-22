<?php
header("Content-Type: application/json");
require __DIR__ . '/../../vendor/autoload.php';

use App\DatabaseManager;
use App\ResourceService;

$databaseManager = new DatabaseManager();
$resourceService = new ResourceService($databaseManager);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

if (!isset($_GET['category']) || !isset($_GET['resource_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Category and Resource_id are required"]);
    exit();
}
$category = $_GET['category'];
if ($category !== 'code' && $category !== 'model') {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid category"]);
    exit();
}
$id = intval($_GET['resource_id']);

$result = $resourceService->getResourceById($id, $category);

if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);