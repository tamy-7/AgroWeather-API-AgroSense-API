<?php

function handleHealth() {
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode(['ok' => true, 'status' => 'healthy', 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}