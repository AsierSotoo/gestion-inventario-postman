<?php

require_once __DIR__ . '/../models/CamisetaModel.php';

class CamisetaController {

    private $model;

    public function __construct() {
        $this->model = new CamisetaModel();
    }

    // GET /camisetas
    public function index() {
        $data = $this->model->getAll();

        $this->response(200, 'Listado de camisetas', $data);
    }

    // GET /camisetas/{id}
    public function show($id) {
        $camiseta = $this->model->getById($id);

        if (!$camiseta) {
            $this->response(404, 'Camiseta no encontrada', null, 'error');
            return;
        }

        $this->response(200, 'Camiseta encontrada', $camiseta);
    }

    // POST /camisetas
    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['equipo'], $input['temporada'], $input['talla'], $input['precio_compra'], $input['precio_venta'], $input['estado'])) {
            $this->response(400, 'Datos inválidos o incompletos', null, 'error');
            return;
        }

        $nueva = $this->model->create($input);

        $this->response(201, 'Camiseta creada correctamente', $nueva);
    }

    // PUT /camisetas/{id}
    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->response(400, 'JSON inválido', null, 'error');
            return;
        }

        $existe = $this->model->getById($id);
        if (!$existe) {
            $this->response(404, 'No existe la camiseta a actualizar', null, 'error');
            return;
        }

        $this->model->update($id, $input);
        $actualizada = $this->model->getById($id);

        $this->response(200, 'Camiseta actualizada correctamente', $actualizada);
    }

    // DELETE /camisetas/{id}
    public function destroy($id) {
        $existe = $this->model->getById($id);

        if (!$existe) {
            $this->response(404, 'No existe la camiseta a eliminar', null, 'error');
            return;
        }

        $this->model->delete($id);

        $this->response(200, 'Camiseta eliminada correctamente', null);
    }

    // Respuesta JSON unificada
    private function response($code, $message, $data = null, $status = 'success') {
        http_response_code($code);
        echo json_encode([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}
