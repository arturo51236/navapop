<?php 
session_start();

require_once 'dao/DAOAnuncio.php';
require_once 'dao/DAOFoto.php';
require_once 'modelo/Anuncio.php';
require_once 'modelo/Foto.php';
require_once 'utilidades/dbconn.php';
require_once 'utilidades/conninfo.php';
require_once 'utilidades/error.php';

// Si no existe una variable de sesión sacar al usuario
if (!isset($_SESSION['email'])) {
    header("location: index.php");
    $_SESSION['error'] = "No puedes editar anuncios si no inicias sesión";
    die();
}

$error = '';
$foto = '';

// Conectamos con la BD
$connDB = new dbconn(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
$conn = $connDB->getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['editar'])) {
        $idAEditar = $_GET['editar'];
        $_SESSION['ideditar'] = $idAEditar;

        $DAOAnuncios = new DAOAnuncio($conn);
        $anuncioAnterior = $DAOAnuncios->selectById($idAEditar);
        if ($anuncioAnterior != null) {
            $idusuario = $anuncioAnterior->getIdusuario();

            if ($idusuario == $_SESSION['id']) {
                $_SESSION['correcto'] = true;
            } else {
                header("location: index.php");
                $_SESSION['error'] = "No puedes editar el anuncio de otro usuario";
                die();
            }
        } else {
            header("location: index.php");
            $_SESSION['error'] = "No existe el id del anuncio";
            die();
        }
    }

    if (empty($_GET['editar'])) {
        $error = "El id del anuncio a editar está vacio";
    }
}

if ($_SESSION['correcto']) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Eliminamos las fotos del anuncio de la carpeta porque serán remplazadas por las nuevas fotos
        $DAOFotos = new DAOFoto($conn);
        $fotos = $DAOFotos->selectAllById($_SESSION['ideditar']);
        foreach ($fotos as $foto) {
            $nombreFoto = $foto->getFoto();
            unlink("fotosAnuncios/$nombreFoto");
        }

        // Limpiamos los datos
        $nombre = htmlspecialchars($_POST['nombre']);
        $descripcion = htmlspecialchars($_POST['descripcion']);
        $precio = htmlspecialchars($_POST['precio']);
        $array_fotos = array();
        $array_fotosTMP = array();
        $array_fotosMDF = array();

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

                $array_fotosMDF[] = $foto;
            }
        }

        if ($error == '') {
            $DAOAnuncios = new DAOAnuncio($conn);
            $anuncio = new Anuncio();
            $anuncio->setNombre($nombre);
            $anuncio->setDescripcion($descripcion);
            $anuncio->setPrecio($precio);
            $anuncio->setFechacreacion(date("Y-m-d H:i:s"));
            if ($DAOAnuncios->modify($anuncio, $_SESSION['ideditar'])) {
                $DAOFotos->delete($_SESSION['ideditar']);
                $DAOFotos->insert($_SESSION['ideditar'], $array_fotosMDF);
                unset($_SESSION['ideditar']);
                header('location: index.php');
                die();
            } else {
                $error = "No se ha podido modificar el anuncio";
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
    <title>Editar anuncio</title>
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
            <?php mostrarError($error); ?>
            <div class="editA">
                <h3 style="padding-top: 20px;">MODIFICAR ANUNCIO</h3>
                <form action="editaranuncio.php" method="post" enctype="multipart/form-data" id="formeditar">
                    <label>Nombre: </label><br><input type="text" name="nombre" placeholder="Modifica el nombre del anuncio" required value="<?= $anuncioAnterior->getNombre() ?>"><br>
                    <label style="margin-bottom: 10px;">Descripción: </label>
                    <div style="width: 80%; margin: auto; margin-top: 10px; margin-bottom: 10px;">
                        <div id="desc">
                            <p><?= $anuncioAnterior->getDescripcion() ?></p>
                        </div>
                    </div>
                    <input type="hidden" id="descripcion" name="descripcion">
                    <label>Fotos: </label><br><input type="file" name="fotos[]" accept="image/jpeg, image/png" multiple required><br>
                    <label>Precio: </label><br>
                    <div class="loseuros">
                        <input type="number" name="precio" placeholder="Modifica el precio para tu anuncio" required value="<?= $anuncioAnterior->getPrecio() ?>"><br>
                    </div>
                    <input class="but-editar" type="submit" value="Modificar anuncio"><br>
                </form>
            </div>
            <div class="vueltaA">
                <a href="index.php">Volver atrás</a>
            </div>
        </section>
    </main>
    <footer>
        <p>Copyright &#169 Navapop 2023</p>
    </footer>
    <script>
        var quill = new Quill('#desc', {
            theme: 'snow'
        });

        var form = document.getElementById("formeditar");
        form.onsubmit = function () {
            var texto = quill.getText();
            var name = document.querySelector('input[name=descripcion]');
            name.value = texto.trim();
            return true;
        }
    </script>
</body>
</html>