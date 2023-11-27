<?php

class DAOFoto {
    private mysqli $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Devuelve todas las fotos del anuncio que se le pasa cómo parámetro
     * @return array|null Devuelve las fotos de un anuncio en concreto o null en caso de que el anuncio no tenga fotos
     */
    public function selectAllById(int $id):array|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM fotos WHERE id = ?")) {
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >= 1) {
            $array_fotos = array();
            while ($foto = $result->fetch_object(Foto::class)) {
                $array_fotos[] = $foto;
            }
            return $array_fotos;
        } else {
            return null;
        }
    }

    /**
     * Devuelve una de las fotos del anuncio que se le pasa cómo parámetro para usarla cómo main foto
     * @return string|null Devuelve el nombre de una de las fotos que se insertó de un anuncio en concreto o null en caso de que no se pueda devolver una foto
     */
    public function selectMainById(int $id):string|null {
        if (!$stmt = $this->conn->prepare("SELECT foto FROM fotos WHERE id = ? ORDER BY foto LIMIT 1")) {
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_column();
        } else {
            return null;
        }
    }

    /**
     * Inserta en la base de datos las fotos de un anuncio teniendo en cuenta su id
     * @return bool Devuelve true si se ha ejecutado correctamente y false en caso contrario
     */
    public function insert(int $id, array $fotos):bool {
        if (!$stmt = $this->conn->prepare("INSERT INTO fotos (id, foto) VALUES (?,?)")) {
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }

        foreach ($fotos as $foto) {
            $stmt->bind_param('is', $id, $foto);
            $stmt->execute();
        }

        if ($this->conn->affected_rows == count($fotos)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Elimina de la base de datos las fotos de un anuncio teniendo en cuenta su id
     * @return bool Devuelve true si se ha ejecutado correctamente y false en caso contrario
     */
    public function delete(int $id):bool {
        if (!$stmt = $this->conn->prepare("DELETE FROM fotos where id = ?")) {
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }

        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}