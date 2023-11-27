<?php
session_start();

require_once 'dao/DAOAnuncio.php';
require_once 'dao/DAOFoto.php';
require_once 'modelo/Anuncio.php';
require_once 'utilidades/dbconn.php';
require_once 'utilidades/conninfo.php';
require_once 'utilidades/error.php';

// Si no existe una variable de sesión sacar al usuario
if (!isset($_SESSION['email'])) {
    header("location: index.php");
    $_SESSION['error'] = "No puedes crear anuncios si no inicias sesión";
    die();
}

$error = '';
$foto = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conectamos con la BD
    $connDB = new dbconn(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
    $conn = $connDB->getConnexion();

    // Limpiamos los datos
    $nombre = htmlspecialchars($_POST['nombre']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $precio = htmlspecialchars($_POST['precio']);
    $array_fotos = array();
    $array_fotosTMP = array();
    $array_fotosINS = array();

    // Validamos los datos
    if (empty($nombre) || empty($descripcion) || empty($precio)) {
        $error = "Es obligatorio rellenar todos los campos";
    }

    if ($_FILES['fotos']['error'][0] == UPLOAD_ERR_NO_FILE) {
        $error = "Debes añadir al menos una foto al anuncio";
    } elseif (count($_FILES['fotos']['name']) > 5) {
        $error = "No puedes subir más de 5 fotos a un anuncio";
    } else {
        $num_files = count($_FILES['fotos']['name']);

        for ($i = 0; $i < $num_files; $i++) {
            $array_fotos[] = $_FILES['fotos']['name'][$i];
            $array_fotosTMP[] = $_FILES['fotos']['tmp_name'][$i];
        }
    }

    foreach ($array_fotos as $i => $foto) {
        // Comprobamos que la extensión de los archivos introducidos son válidas
        $extension = pathinfo($foto, PATHINFO_EXTENSION);
        if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png') {
            $error = "Alguna de las fotos no tiene un formato admitido, deben de ser jpg, jpeg o png";
        } else {
            // Copiamos la foto al disco
            // Calculamos un hash para el nombre del archivo
            $foto = uniqid(true) . '.' . $extension;

            // Si existe un archivo con ese nombre volvemos a calcular el hash
            while (file_exists("fotosAnuncios/$foto")) {
                $foto = uniqid(true) . '.' . $extension;
            }

            foreach ($array_fotosTMP as $j => $fotoTMP) {
                if ($i == $j && $error == '') {
                    if (!move_uploaded_file($fotoTMP, "fotosAnuncios/$foto")) {
                        die("Error al copiar la foto a la carpeta fotosAnuncios");
                    }
                }
            }

            $array_fotosINS[] = $foto;
        }
    }

    if ($error == '') {
        $DAOAnuncios = new DAOAnuncio($conn);
        $anuncio = new Anuncio();
        $anuncio->setId($DAOAnuncios->returnNewId());
        $anuncio->setIdusuario($_SESSION['id']);
        $anuncio->setNombre($nombre);
        $anuncio->setDescripcion($descripcion);
        $anuncio->setPrecio($precio);
        $anuncio->setFechacreacion(date("Y-m-d H:i:s"));
        $anuncio->setFinalizado(0);
        if ($DAOAnuncios->insert($anuncio)) {
            $DAOFotos = new DAOFoto($conn);
            $DAOFotos->insert($anuncio->getId(), $array_fotosINS);
            header('location: index.php');
            die();
        } else {
            $error = "No se ha podido insertar el anuncio";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo anuncio</title>
    <link rel="stylesheet" href="./src/estilos/estilos.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
</head>
<body>
    <header>
        <div class="title">
            <h1>NAVAPOP</h1>
        </div>
        <div class="search">
            <form method="get" action="index.php?busqueda=">
                <input type="search" name="busqueda" placeholder="Búsqueda de anuncio..." >
                <button>Buscar</button>
            </form>
        </div>
    </header>
    <main>
        <nav>
            <div class="LogInfo">
                <div class="imgL">
                    <img src="fotosUsuarios/<?= $_SESSION['foto']?>">
                </div>
                <div class="emailL">
                    <p><?= $_SESSION['email'] ?></p>
                </div>
            </div>
            <a href="index.php">
                <div class="nav-items" style="border-top: 1px solid #89c6d7;">
                    <h4>Anuncios</h4>
                </div>
            </a>
            <a href="index.php?misanuncios">
                <div class="nav-items">
                    <h4>Mis anuncios</h4>
                </div>
            </a>
            <a href="crearanuncio.php">
                <div class="nav-items">
                    <h4>Crear anuncio</h4>
                </div>
            </a>
            <a href="logout.php">
                <div class="logoutL">
                    <p>Cerrar Sesión</p>
                </div>
            </a>
        </nav>
        <section>
            <div class="nuevoA">
                <?php mostrarError($error); ?>
                <form action="crearanuncio.php" method="post" enctype="multipart/form-data" id="formcrear">
                    <label>Nombre: </label><input type="text" name="nombre" placeholder="Introduce el nombre del anuncio" required><br>
                    <label>Descripción: </label>
                    <div id="desc">
                        <p>Introduce aquí una <strong>descripción</strong> para tu anuncio</p>
                    </div>
                    <input type="hidden" id="descripcion" name="descripcion">
                    <label>Fotos: </label><input type="file" name="fotos[]" accept="image/jpeg, image/png" multiple required><br>
                    <label>Precio: </label>
                    <div class="loseuros">
                        <input type="number" name="precio" placeholder="Introduce el precio para tu anuncio" required><br>
                    </div>
                    <input type="submit" value="Subir anuncio"><br>
                </form>
            </div>
            <div>
                <a href="index.php">Volver atrás</a>
            </div>
        </section>
    </main>
    <footer>
        <p>Copyright © Navapop 2023</p>
    </footer>
    <script>
        var quill = new Quill('#desc', {
            theme: 'snow'
        });

        var form = document.getElementById("formcrear");
        form.onsubmit = function () {
            var texto = quill.getText();
            var name = document.querySelector('input[name=descripcion]');
            name.value = texto.trim();
            return true;
        }
    </script>
</body>
</html>