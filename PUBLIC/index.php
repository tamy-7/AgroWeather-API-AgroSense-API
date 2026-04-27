<?php
declare(strict_types=1);
session_start();

$config = require __DIR__ . '/../config/config.php';

require_once __DIR__ . '/../src/Validators/ClimaValidator.php';
require_once __DIR__ . '/../src/Services/ClimaService.php';
require_once __DIR__ . '/../src/Helpers/UsuarioHelper.php';

require_once __DIR__ . '/../src/Controllers/HealthController.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/ClimaController.php';

use Validators\ClimaValidator;
use Services\ClimaService;
use Helpers\UsuarioHelper;

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

$basePath = '/AuraTerra/public';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = '/' . ltrim($path, '/');
if ($path === '//') $path = '/';


// ========== FUNCIONES DE MANEJO DE ENDPOINTS ==========

function handleNotFound($path) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Not Found', 'path' => $path]);
}

// ========== LÓGICA DE ENRUTAMIENTO ==========

if ($method === 'GET' && $path === '/health') {
    handleHealth();
} elseif ($method === 'GET' && $path === '/register') {
    handleRegisterGet();
} elseif ($method === 'POST' && $path === '/register') {
    handleRegisterPost();
} elseif ($method === 'GET' && $path === '/login') {
    handleLoginGet();
} elseif ($method === 'POST' && $path === '/login') {
    handleLoginPost();
} elseif ($method === 'GET' && $path === '/logout') {
    handleLogout();
} elseif ($method === 'GET' && $path === '/perfil') {
    handlePerfil();
} elseif ($method === 'GET' && $path === '/clima/actual') {
    handleClimaActual($config);
} elseif ($method === 'GET' && $path === '/clima/pronostico') {
    handleClimaPronostico($config);
} else {
    handleNotFound($path);
}