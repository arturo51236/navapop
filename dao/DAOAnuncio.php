<?php

class DAOAnuncio
{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Obtiene todos los anuncios de la tabla anuncios
     * @return array|null Devuelve un array de anuncios o null si no existe ningún anuncio en la base de datos
     */
    public function selectAll():array|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >= 1) {
            $array_anuncios = array();
            while ($anuncio = $result->fetch_object(Anuncio::class)) {
                $array_anuncios[] = $anuncio;
            }
            return $array_anuncios;
        } else {
            return null;
        }
    }

    /**
     * Obtiene todos los anuncios de un usuario concreto de la tabla anuncios
     * @return array|null Devuelve un array de anuncios o null si el usuario no tiene anuncios
     */
    public function selectAllById(int $id):array|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios WHERE idusuario = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >= 1) {
            $array_anuncios_id = array();
            while ($anuncio = $result->fetch_object(Anuncio::class)) {
                $array_anuncios_id[] = $anuncio;
            }
            return $array_anuncios_id;
        } else {
            return null;
        }
    }

    /**
     * Obtiene el último id de la tabla de anuncios y le suma 1
     * @return int Devuelve un número entero que servirá cómo id a un nuevo anuncio
     */
    public function returnNewId():int {
        if (!$stmt = $this->conn->prepare("SELECT id FROM anuncios ORDER BY id DESC LIMIT 1")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return 1;
        } else {
            return $result->fetch_assoc()['id'] + 1;
        }
    }

    /**
     * Inserta en la base de datos el anuncio que recibe cómo parámetro
     * @return bool Devuelve true si se ha ejecutado correctamente o false en caso de error
     */
    function insert(Anuncio $anuncio):bool {
        if (!$stmt = $this->conn->prepare("INSERT INTO anuncios (id, nombre, descripcion, precio, fechacreacion, finalizado, idusuario) VALUES (?,?,?,?,?,?,?)")) {
            die("Error al preparar la consulta insert: " . $this->conn->error);
        }

        $id = $anuncio->getId();
        $nombre = $anuncio->getNombre();
        $descripcion = $anuncio->getDescripcion();
        $precio = $anuncio->getPrecio();
        $fechacreacion = $anuncio->getFechacreacion();
        $finalizado = $anuncio->getFinalizado();
        $idusuario = $anuncio->getIdusuario();
        $stmt->bind_param('issdsii', $id, $nombre, $descripcion, $precio, $fechacreacion, $finalizado, $idusuario);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}