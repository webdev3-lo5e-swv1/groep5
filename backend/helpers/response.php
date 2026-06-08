<?php
// backend/helpers/response.php
// Helper voor consistente JSON responses in API endpoints

class Response
{
    // Succes response
    public static function succes(mixed $data = null, string $bericht = 'OK', int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'bericht' => $bericht,
            'data'    => $data
        ]);
        exit;
    }

    // Fout response
    public static function fout(string $bericht, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'bericht' => $bericht,
            'data'    => null
        ]);
        exit;
    }

    // 404 response
    public static function nietGevonden(string $bericht = 'Niet gevonden'): void
    {
        self::fout($bericht, 404);
    }

    // 401 response
    public static function nietGeautoriseerd(string $bericht = 'Niet geautoriseerd'): void
    {
        self::fout($bericht, 401);
    }
}