<?php

use Validators\ClimaValidator;
use Services\ClimaService;

function handleClimaActual($config) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Debes iniciar sesión para usar el servicio de clima']);
        exit;
    }
    $params = [
        'lat'    => $_GET['lat'] ?? null,
        'lon'    => $_GET['lon'] ?? null,
        'ciudad' => $_GET['ciudad'] ?? null,
    ];
    $validacion = ClimaValidator::validarActual($params);
    if (!$validacion['valid']) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'errors' => $validacion['errors']]);
        exit;
    }
    $service = new ClimaService(
        $config['api_keys']['openweather'],
        $config['api_urls']['openweather']
    );
    $resultado = $service->obtenerActual($validacion['data']);
    if ($resultado['error']) {
        http_response_code($resultado['codigo'] ?? 500);
        echo json_encode(['ok' => false, 'error' => $resultado['mensaje']]);
        exit;
    }
    http_response_code(200);
    echo json_encode(['ok' => true, 'data' => $resultado['data']], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function handleClimaPronostico($config) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'No autenticado']);
        exit;
    }

    $params = [
        'lat'    => $_GET['lat'] ?? null,
        'lon'    => $_GET['lon'] ?? null,
        'ciudad' => $_GET['ciudad'] ?? null,
    ];

    $validacion = ClimaValidator::validarActual($params);

    if (!$validacion['valid']) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'errors' => $validacion['errors']]);
        exit;
    }

    $service = new ClimaService(
        $config['api_keys']['openweather'],
        $config['api_urls']['openweather']
    );

    $resultado = $service->obtenerPronostico($validacion['data']);

    echo json_encode($resultado);
    exit;
}