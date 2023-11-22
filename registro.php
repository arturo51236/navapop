<?php

require_once 'dao/DAOUsuario.php';
require_once 'modelo/Usuario.php';
require_once 'utilidades/dbconn.php';
require_once 'utilidades/conninfo.php';

$error = '';
$foto = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conectamos con la BD
    $connDB = new dbconn(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
    $conn = $connDB->getConnexion();
    $DAOUsuarios = new DAOUsuario($conn);

    // Limpiamos los datos
    $nombre = htmlentities($_POST['nombre']);
    $email = htmlentities($_POST['email']);
    $passwd = htmlentities($_POST['passwd']);
    $telefono = htmlentities($_POST['telefono']);
    $poblacion = htmlentities($_POST['poblacion']);

    // Validamos los datos
    if (empty($nombre) || empty($email) || empty($passwd) || empty($telefono) || empty($poblacion)) {
        $error = "Es obligatorio rellenar todos los campos";
    }

    if (strlen($passwd <= 4)) {
        $error = "La contraseña debe de tener más de 4 caracteres";
    }

    // Compruebo que no haya un usuario registrado con el mismo email
    if ($DAOUsuarios->selectByEmail($email) != null) {
        $error = "Ya hay un usuario con ese email";
    } else {
        // Comprobamos que la extensión del archivo introducido es válida
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'webp' && $extension != 'png') {
            $error = "La foto no tiene un formato admitido, debe ser jpg, jpeg, png o webp";
        } else {
            // Copiamos la foto al disco
            // Calculamos un hash para el nombre del archivo
            $foto = uniqid(true) . '.' . $extension;

            // Si existe un archivo con ese nombre volvemos a calcular el hash
            while (file_exists("fotosUsuarios/$foto")) {
                $foto = uniqid(true) . '.' . $extension;
            }

            if ($error == '') {
                if (!move_uploaded_file($_FILES['foto']['tmp_name'], "fotosUsuarios/$foto")) {
                    die("Error al copiar la foto a la carpeta fotosUsuarios");
                }
            }
        }

        if (strlen($telefono) != 9) {
            $error = "El teléfono debe ser de 9 caracteres";
        }

        if ($error == '') {
            $usuario = new Usuario();
            $usuario->setId($DAOUsuarios->returnNewId());
            $usuario->setNombre($nombre);
            $usuario->setEmail($email);
            $passwdCifrado = password_hash($passwd, PASSWORD_DEFAULT);
            $usuario->setPasswd($passwdCifrado);
            $usuario->setTelefono($telefono);
            $usuario->setPoblacion($poblacion);
            $usuario->setFoto($foto);
            $usuario->setSid(sha1(rand() + time()), true);

            if ($DAOUsuarios->insert($usuario)) {
                header("location: index.php");
                die();
            } else {
                $error = "No se ha podido insertar el usuario";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="./src/estilos/estilos.css">
</head>

<body>
    <main>
        <?= $error ?>
        <form action="registro.php" method="post" enctype="multipart/form-data">
            <label>Nombre: </label><input type="text" name="nombre" placeholder="Introduce tu nombre" required><br>
            <label>Email: </label><input type="email" name="email" placeholder="Introduce tu email" required><br>
            <label>Contraseña: </label><input type="password" name="passwd" placeholder="Introduce tu contraseña" required><br>
            <label>Teléfono: </label><input type="number" name="telefono" placeholder="Introduce tu teléfono" required><br>
            <label>Población: </label><input type="text" name="poblacion" placeholder="Introduce tu población" required><br>
            <label>Foto de perfil: </label><input type="file" name="foto" accept="image/jpeg, image/webp, image/png"><br>
            <input type="submit" value="Confirmar registro">
            <a href="index.php">Volver atrás</a>
        </form>
    </main>
</body>

</html>