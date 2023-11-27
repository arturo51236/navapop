<?php

class DAOAnuncio
{
    private mysqli $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Obtiene todos los anuncios de la tabla anuncios ordenados por fecha de creación de manera descendente
     * @return array|null Devuelve un array de anuncios o null si no existe ningún anuncio en la base de datos
     */
    public function selectAll():array|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios ORDER BY fechacreacion DESC")) {
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
     * Obtiene un anuncio concreto de la tabla anuncios
     * @return array|null Devuelve un anuncio o null si no existe un anuncio con ese id
     */
    public function selectById(int $id):Anuncio|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios WHERE id = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_object(Anuncio::class);
        } else {
            return null;
        }
    }

    /**
     * Elimina un anuncio y todo su contenido de la base de datos en función del id
     * @return bool true en caso de que se haya eliminado correctamente o false en caso contrario
     */
    public function deleteById($id):bool {
        if (!$stmt = $this->conn->prepare("DELETE FROM anuncios WHERE id = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Obtiene los anuncios que compartan la cadena de texto que le llega cómo parámetro
     * @return array|null Devuelve un array anuncio o null si no existe ningún anuncio con esa cadena
     */
    public function filter($nombre):array|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM anuncios WHERE nombre LIKE ?")) {
            die ("Error al preparar la consulta insert: " . $this->conn->error);
        }

        $anyWordAny= '%' . $nombre . '%';
        $stmt->bind_param('s', $anyWordAny);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($anuncio = $result->fetch_object(Anuncio::class)) {
            $array_anuncios = array();
            $array_anuncios[] = $anuncio;
        }

        if (empty($array_anuncios)) {
            return null;
        } else {
            return $array_anuncios;
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
    public function insert(Anuncio $anuncio):bool {
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

    /**
     * Modifica en la base de datos el anuncio que recibe cómo parámetro
     * @return bool Devuelve true si se ha ejecutado correctamente o false en caso de error
     */
    public function modify(Anuncio $anuncio, int $id):bool {
        if (!$stmt = $this->conn->prepare("UPDATE anuncios SET nombre = ?, descripcion = ?, precio = ?, fechacreacion = ? WHERE id = ?")) {
            die("Error al preparar la consulta insert: " . $this->conn->error);
        }

        $nombre = $anuncio->getNombre();
        $descripcion = $anuncio->getDescripcion();
        $precio = $anuncio->getPrecio();
        $fechacreacion = $anuncio->getFechacreacion();
        $stmt->bind_param('ssdsi', $nombre, $descripcion, $precio, $fechacreacion, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}