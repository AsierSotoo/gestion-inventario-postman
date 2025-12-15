<?php
// public/index.php

header("Content-Type: application/json");

// Autoload simple
require_once __DIR__ . '/../app/controllers/CamisetaController.php';

// Obtener método HTTP y URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Limpiar la URI
$uri = trim($uri, '/');
$segments = explode('/', $uri);

// 🔹 ELIMINAR "public" DE LA RUTA SI EXISTE
if (isset($segments[0]) && $segments[0] === 'public') {
    array_shift($segments);
}

// Esperamos rutas tipo: camisetas o camisetas/{id}
$resource = $segments[0] ?? null;
$id = $segments[1] ?? null;


// Instancia del controlador
$controller = new CamisetaController();

switch ($resource) {
    case 'camisetas':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $controller->show($id);
                } else {
                    $controller->index();
                }
                break;

            case 'POST':
                $controller->store();
                break;

            case 'PUT':
                if ($id) {
                    $controller->update($id);
                } else {
                    respuestaError(400, 'ID requerido para actualizar');
                }
                break;

            case 'DELETE':
                if ($id) {
                    $controller->destroy($id);
                } else {
                    respuestaError(400, 'ID requerido para eliminar');
                }
                break;

            default:
                respuestaError(405, 'Método no permitido');
        }
        break;

    default:
        respuestaError(404, 'Ruta no encontrada');
}

// Función genérica de error
function respuestaError($code, $message) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'code' => $code,
        'message' => $message,
        'data' => null
    ]);
    exit;
}
