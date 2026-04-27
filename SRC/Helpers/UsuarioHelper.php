<?php
namespace Helpers;

class UsuarioHelper
{
    private static $archivo = __DIR__ . '/../../data/usuarios.json';

    public static function guardarUsuario($email, $nombre, $passwordHash)
    {
        $usuarios = self::obtenerTodos();
        foreach ($usuarios as $u) {
            if ($u['email'] === $email) return false;
        }
        $nuevo = [
            'id' => count($usuarios) + 1,
            'email' => $email,
            'nombre' => $nombre,
            'password' => $passwordHash,
            'creado' => date('Y-m-d H:i:s')
        ];
        $usuarios[] = $nuevo;
        file_put_contents(self::$archivo, json_encode($usuarios, JSON_PRETTY_PRINT));
        return true;
    }

    public static function obtenerTodos()
    {
        if (!file_exists(self::$archivo)) return [];
        $contenido = file_get_contents(self::$archivo);
        return json_decode($contenido, true) ?? [];
    }

    public static function autenticar($email, $password)
    {
        $usuarios = self::obtenerTodos();
        foreach ($usuarios as $u) {
            if ($u['email'] === $email && password_verify($password, $u['password'])) {
                return $u;
            }
        }
        return null;
    }
}