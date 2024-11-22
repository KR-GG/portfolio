<?php
require __DIR__ . '/vendor/autoload.php';

use OpenApi\Generator;

$openapi = Generator::scan([
    __DIR__ . '/api/apispec',
    __DIR__ . '/api/controllers/Controller.php'
]);

header('Content-Type: application/yaml');
echo $openapi->toYaml();