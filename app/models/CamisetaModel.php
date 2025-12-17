<?php

class CamisetaModel {

    private $file;

    public function __construct() {
        $this->file = __DIR__ . '/../../data/camisetas.csv';
    }

    // 🔹 Leer CSV usando ;
    private function readCsv() {
        return array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($this->file, FILE_SKIP_EMPTY_LINES));
    }

    // 🔹 Obtener todas las camisetas
    public function getAll() {
        $rows = $this->readCsv();
        $header = array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            if (count($row) === count($header)) {
                $data[] = array_combine($header, $row);
            }
        }
        return $data;
    }

    // 🔹 Obtener camiseta por ID
    public function getById($id) {
        $camisetas = $this->getAll();
        foreach ($camisetas as $camiseta) {
            if ((string)$camiseta['id'] === (string)$id) {
                return $camiseta;
            }
        }
        return null;
    }

    // 🔹 Crear nueva camiseta (POST)
    public function create($data) {
        $camisetas = $this->getAll();
        $ids = array_column($camisetas, 'id');
        $newId = empty($ids) ? 1 : max($ids) + 1;

        $fechaAlta = date('Y-m-d');

        $row = [
            $newId,
            $data['equipo'],
            $data['temporada'],
            $data['talla'],
            $data['precio_compra'],
            $data['precio_venta'],
            $data['estado'],
            $fechaAlta
        ];

        $fp = fopen($this->file, 'a');
        fputcsv($fp, $row, ';');
        fclose($fp);

        return [
            'id' => $newId,
            'equipo' => $data['equipo'],
            'temporada' => $data['temporada'],
            'talla' => $data['talla'],
            'precio_compra' => $data['precio_compra'],
            'precio_venta' => $data['precio_venta'],
            'estado' => $data['estado'],
            'fecha_alta' => $fechaAlta
        ];
    }

    // 🔹 Actualizar camiseta (PUT)
    public function update($id, $newData) {
        $rows = $this->readCsv();
        $header = array_shift($rows);
        $updated = false;

        foreach ($rows as &$row) {
            if ((string)$row[0] === (string)$id) {

                foreach ($header as $index => $field) {
                    // No permitir cambiar id ni fecha
                    if (in_array($field, ['id', 'fecha_alta'])) {
                        continue;
                    }

                    if (isset($newData[$field])) {
                        $row[$index] = $newData[$field];
                    }
                }

                $updated = true;
                break; // 🔴 CLAVE: no tocar más filas
            }
        }

        if (!$updated) {
            return false;
        }

        $fp = fopen($this->file, 'w');
        fputcsv($fp, $header, ';');
        foreach ($rows as $r) {
            fputcsv($fp, $r, ';');
        }
        fclose($fp);

        return true;
    }

    // 🔹 Eliminar camiseta (DELETE)
    public function delete($id) {
        $rows = $this->readCsv();
        $header = array_shift($rows);

        $newRows = [];
        $deleted = false;

        foreach ($rows as $row) {
            if ((string)$row[0] === (string)$id) {
                $deleted = true;
                continue;
            }
            $newRows[] = $row;
        }

        if (!$deleted) {
            return false;
        }

        $fp = fopen($this->file, 'w');
        fputcsv($fp, $header, ';');
        foreach ($newRows as $r) {
            fputcsv($fp, $r, ';');
        }
        fclose($fp);

        return true;
    }
}
