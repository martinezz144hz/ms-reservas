<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// ============================================
// CARGAR VARIABLES DE ENTORNO (.env)
// ============================================
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// ============================================
// CONEXIÓN A BASE DE DATOS CON ELOQUENT
// ============================================
$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'],
    'port'      => $_ENV['DB_PORT'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// ============================================
// CREAR APP SLIM
// ============================================
$app = AppFactory::create();

// Middleware para parsear JSON en el body
$app->addBodyParsingMiddleware();

// Middleware de errores
$app->addErrorMiddleware(true, true, true);

// ============================================
// HEADERS CORS
// ============================================
$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Responder preflight OPTIONS
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

// ============================================
// RUTAS
// ============================================
require __DIR__ . '/../src/rutas.php';

// ============================================
// CORRER APP
// ============================================
$app->run();