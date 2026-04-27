<?php
namespace Services;

class ClimaService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct(string $apiKey, string $baseUrl)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    public function obtenerActual(array $params): array
    {
        $url = $this->baseUrl . '?appid=' . $this->apiKey . '&units=metric&lang=es';
        if (isset($params['lat'], $params['lon'])) {
            $url .= "&lat={$params['lat']}&lon={$params['lon']}";
        } elseif (isset($params['ciudad'])) {
            $url .= "&q=" . urlencode($params['ciudad']);
        } else {
            return ['error' => true, 'codigo' => 400, 'mensaje' => 'Faltan parámetros'];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($respuesta === false || $error) {
            return ['error' => true, 'codigo' => 500, 'mensaje' => 'Error de conexión: ' . $error];
        }
        if ($httpCode !== 200) {
            return ['error' => true, 'codigo' => $httpCode, 'mensaje' => 'Error externo HTTP ' . $httpCode];
        }
        $datos = json_decode($respuesta, true);
        if (!$datos || isset($datos['cod']) && $datos['cod'] != 200) {
            return ['error' => true, 'codigo' => 500, 'mensaje' => $datos['message'] ?? 'Respuesta inválida'];
        }
        return [
            'error' => false,
            'data' => [
                'temperatura' => $datos['main']['temp'] ?? null,
                'humedad' => $datos['main']['humidity'] ?? null,
                'viento' => $datos['wind']['speed'] ?? null,
                'descripcion' => $datos['weather'][0]['description'] ?? null,
                'fuente' => 'OpenWeatherMap',
                'timestamp' => date('Y-m-d H:i:s'),
                'ubicacion' => $datos['name'] ?? 'Desconocida',
            ]
        ];
    }
    public function obtenerPronostico(array $params): array
    {
        $url = "https://api.openweathermap.org/data/2.5/forecast?appid={$this->apiKey}&units=metric&lang=es";

        if (isset($params['lat'], $params['lon'])) {
            $url .= "&lat={$params['lat']}&lon={$params['lon']}";
        } elseif (isset($params['ciudad'])) {
            $url .= "&q=" . urlencode($params['ciudad']);
        } else {
            return ['error' => true, 'codigo' => 400, 'mensaje' => 'Faltan parámetros'];
        }

        $respuesta = file_get_contents($url);
        $datos = json_decode($respuesta, true);

        if (!$datos || $datos['cod'] != 200) {
            return ['error' => true, 'codigo' => 500, 'mensaje' => 'Error en API'];
        }

        return [
            'error' => false,
            'data' => $datos['list']
        ];
    }

}