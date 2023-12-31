<?php

class DAOUsuario {
    private mysqli $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtiene todos los usuarios de la tabla usuarios
     * @return array|null Devuelve un array con todos los usuarios o null en caso de error
     */
    public function selectAll():array|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM usuarios")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >= 1) {
            $array_usuarios = array();
            while ($usuario = $result->fetch_object(Usuario::class)) {
                $array_usuarios[] = $usuario;
            }
            return $array_usuarios;
        } else {
            return null;
        }
    }

    /**
     * Obtiene un usuario de la tabla usuarios en función del email
     * @return Usuario|null Devuelve un Objeto de la clase Usuario o null si no existe
     */
    public function selectByEmail($email):Usuario|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        } else {
            return null;
        }
    }

    /**
     * Elimina un usuario y todo su contenido de la base de datos en función del email
     * @return bool true en caso de que se haya eliminado correctamente o false en caso contrario
     */
    public function deleteByEmail($email):bool {
        if (!$stmt = $this->conn->prepare("DELETE FROM usuarios WHERE email = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->bind_param('s', $email);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Obtiene un usuario de la base de datos en función del id
     * @return Usuario|null Devuelve un objeto de la clase Usuario o null si no existe
     */
    public function selectById($id):Usuario|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        } else {
            return null;
        }
    }

    /**
     * Obtiene un usuario de la base de datos en función del sid
     * @return Usuario|null Devuelve un objeto de la clase Usuario o null si no existe
     */
    public function selectBySid($sid):Usuario|null {
        if (!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE sid = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }

        $stmt->bind_param('s', $sid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        } else {
            return null;
        }
    }

    /**
     * Obtiene el último id de la tabla de usuarios y le suma 1
     * @return int Devuelve un número entero que servirá cómo id a un nuevo usuario
     */
    public function returnNewId():int {
        if (!$stmt = $this->conn->prepare("SELECT id FROM usuarios ORDER BY id DESC LIMIT 1")) {
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
     * Inserta en la base de datos el usuario que recibe como parámetro
     * @return bool Devuelve true si se ha ejecutado correctamente o false en caso de error
     */
    public function insert(Usuario $usuario):bool {
        if (!$stmt = $this->conn->prepare("INSERT INTO usuarios (id, nombre, email, passwd, telefono, poblacion, foto, sid) VALUES (?,?,?,?,?,?,?,?)")) {
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }

        $id = $usuario->getId();
        $nombre = $usuario->getNombre();
        $email = $usuario->getEmail();
        $passwd = $usuario->getPasswd();
        $telefono = $usuario->getTelefono();
        $poblacion = $usuario->getPoblacion();
        $foto = $usuario->getFoto();
        $sid = $usuario->getSid();
        $stmt->bind_param('isssisss', $id, $nombre, $email, $passwd, $telefono, $poblacion, $foto, $sid);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}