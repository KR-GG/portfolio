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

if (!isset($_GET['category'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Category is required"]);
    exit();
}
$category = $_GET['category'];
if ($category !== 'code' && $category !== 'model') {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid category"]);
    exit();
}

$result = $resourceService->getAllResources($category);
if ($result['status'] !== 'success') {
    http_response_code(500);
}
echo json_encode($result);