<?php
require __DIR__ . '/vendor/autoload.php';

use App\DatabaseManager;

$databaseManager = new DatabaseManager();
$databaseManager->truncateAllTables();
$databaseManager->dropAllTables();
$databaseManager->runMigrations();

echo "Migration completed";