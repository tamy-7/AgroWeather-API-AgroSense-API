<?php

use Helpers\UsuarioHelper;

function handleRegisterGet() {
    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
    <!DOCTYPE html>
    <html>
    <head><title>Registro AuraTerra</title></head>
    <body>
        <h2>Registro de Usuario</h2>
        <form method="POST" action="/AuraTerra/public/register">
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Nombre: <input type="text" name="nombre" required></label><br>
            <label>Contraseña: <input type="password" name="password" required></label><br>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tenés cuenta? <a href="/login">Iniciar sesión</a></p>
    </body>
    </html>
    HTML;
    exit;
}

function handleRegisterPost() {
    $email = trim($_POST['email'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email inválido';
    if (strlen($nombre) < 3 || strlen($nombre) > 50) $errors['nombre'] = 'Nombre entre 3 y 50 caracteres';
    if (strlen($password) < 6) $errors['password'] = 'Contraseña mínima 6 caracteres';
    if (!empty($errors)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'errors' => $errors]);
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $guardado = UsuarioHelper::guardarUsuario($email, $nombre, $hash);
    if (!$guardado) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'errors' => ['email' => 'El email ya está registrado']]);
        exit;
    }
    http_response_code(201);
    echo json_encode(['ok' => true, 'message' => 'Usuario registrado. Ahora podés iniciar sesión.']);
    exit;
}

function handleLoginGet() {
    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
    <!DOCTYPE html>
    <html>
    <head><title>Login AuraTerra</title></head>
    <body>
        <h2>Iniciar sesión</h2>
        <form method="POST" action="/AuraTerra/public/login">
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Contraseña: <input type="password" name="password" required></label><br>
            <button type="submit">Ingresar</button>
        </form>
        <p>¿No tenés cuenta? <a href="/register">Registrate</a></p>
    </body>
    </html>
    HTML;
    exit;
}

function handleLoginPost() {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $usuario = UsuarioHelper::autenticar($email, $password);
    if (!$usuario) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Credenciales incorrectas']);
        exit;
    }
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['user_email'] = $usuario['email'];
    $_SESSION['user_nombre'] = $usuario['nombre'];
    http_response_code(200);
    echo json_encode(['ok' => true, 'message' => 'Login exitoso', 'user' => ['nombre' => $usuario['nombre'], 'email' => $usuario['email']]]);
    exit;
}

function handleLogout() {
    session_destroy();
    header('Location: /login');
    exit;
}

function handlePerfil() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'No autenticado']);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'user' => ['nombre' => $_SESSION['user_nombre'], 'email' => $_SESSION['user_email']]]);
    exit;
}