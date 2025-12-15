<?php

class CamisetaModel {

    private $file;

    public function __construct() {
        $this->file = __DIR__ . '/../../data/camisetas.csv';
    }

    // 🔹 Función auxiliar para leer el CSV con ;
    private function readCsv() {
        return array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($this->file, FILE_SKIP_EMPTY_LINES));
    }

    // Leer todas las camisetas
    public function getAll() {
        $rows = $this->readCsv();
        $header = array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            $data[] = array_combine($header, $row);
        }
        return $data;
    }

    // Buscar por ID
    public function getById($id) {
        $camisetas = $this->getAll();
        foreach ($camisetas as $camiseta) {
            if ($camiseta['id'] == $id) {
                return $camiseta;
            }
        }
        return null;
    }

    // Crear nueva camiseta
    public function create($data) {
        $camisetas = $this->getAll();
        $ids = array_column($camisetas, 'id');
        $newId = empty($ids) ? 1 : max($ids) + 1;

        $data['id'] = $newId;
        $data['fecha_alta'] = date('Y-m-d');

        $fp = fopen($this->file, 'a');
        fputcsv($fp, $data, ';');
        fclose($fp);

        return $data;
    }

    // Actualizar camiseta
    public function update($id, $newData) {
        $rows = $this->readCsv();
        $header = array_shift($rows);
        $updated = false;

        foreach ($rows as &$row) {
            if ($row[0] == $id) {
                foreach ($header as $index => $field) {
                    if (isset($newData[$field])) {
                        $row[$index] = $newData[$field];
                    }
                }
                $updated = true;
            }
        }

        if (!$updated) {
            return false;
        }

        $fp = fopen($this->file, 'w');
        fputcsv($fp, $header, ';');
        foreach ($rows as $row) {
            fputcsv($fp, $row, ';');
        }
        fclose($fp);

        return true;
    }

    // Eliminar camiseta
    public function delete($id) {
        $rows = $this->readCsv();
        $header = array_shift($rows);

        $newRows = [];
        $deleted = false;

        foreach ($rows as $row) {
            if ($row[0] == $id) {
                $deleted = true;
            } else {
                $newRows[] = $row;
            }
        }

        if (!$deleted) {
            return false;
        }

        $fp = fopen($this->file, 'w');
        fputcsv($fp, $header, ';');
        foreach ($newRows as $row) {
            fputcsv($fp, $row, ';');
        }
        fclose($fp);

        return true;
    }
}
