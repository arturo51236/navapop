<?php
session_start();

require_once 'dao/DAOAnuncio.php';
require_once 'dao/DAOUsuario.php';
require_once 'dao/DAOFoto.php';
require_once 'modelo/Anuncio.php';
require_once 'modelo/Usuario.php';
require_once 'modelo/Foto.php';
require_once 'utilidades/dbconn.php';
require_once 'utilidades/conninfo.php';
require_once 'utilidades/error.php';

$connDB = new dbconn(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
$conn = $connDB->getConnexion();
$DAOUsuarios = new DAOUsuario($conn);
$DAOAnuncios = new DAOAnuncio($conn);
$DAOFotos = new DAOFoto($conn);
$error = '';

// Si existe la cookie y no ha iniciado sesión, le iniciamos sesión de forma automática
if (!isset($_SESSION['email']) && isset($_COOKIE['sid'])) {
    // Nos conectamos para obtener el id y la foto del usuario
    if ($usuario = $DAOUsuarios->selectBySid($_COOKIE['sid'])) {
        // Inicio de sesión
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['id'] = $usuario->getId();
        $_SESSION['foto'] = $usuario->getFoto();

        // Renovar cookie
        setcookie('sid', '', 0, '/');
        setcookie('sid', $usuario->getSid(), time()+24*60*60, '/');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['busqueda'])) {
        if (empty($_GET['busqueda'])) {
            $error = "No has especificado un nombre para la búsqueda";
            $anuncios = array();
        } else {
            if ($DAOAnuncios->filter($_GET['busqueda']) == null) {
                $error = "No existe ningún anuncio con lo introducido en tu búsqueda";
                $anuncios = array();
            } else {
                $anuncios = $DAOAnuncios->filter($_GET['busqueda']);
            }
        }
    } else {
        $anuncios = $DAOAnuncios->selectAll();
    }

    if ($anuncios == null && !isset($_GET['busqueda'])) {
        $error = "No existe ningún anuncio en la base de datos de Navapop aún";
    } else {
        if (isset($_GET['misanuncios'])) {
            if (isset($_SESSION['id'])) {
                $anuncios = $DAOAnuncios->selectAllById($_SESSION['id']);

                if ($anuncios == null) {
                    $error = "Tu usuario no tiene anuncios todavía";
                    $anuncios = array();
                }
            } else {
                $error = "Para poder ver tus anuncios debes de iniciar sesión";
                $anuncios = array();
            }
        }
    }

    if (isset($_GET['eliminar'])) {
        if (!empty($_GET['eliminar'])) {
            if (isset($_SESSION['email'])) {
                $anuncioAEliminar = $DAOAnuncios->selectById($_GET['eliminar']);
                $idusuario = $anuncioAEliminar->getIdusuario();

                if ($idusuario == $_SESSION['id']) {
                    $fotos = $DAOFotos->selectAllById($_GET['eliminar']);
                    if ($DAOAnuncios->deleteById($_GET['eliminar'])) {
                        // Borrar fotos del anuncio
                        foreach ($fotos as $foto) {
                            $nombreFoto = $foto->getFoto();
                            unlink("fotosAnuncios/$nombreFoto");
                        }

                        // Redirigir a la sección mis anuncios para recargar los anuncios
                        header('location: index.php?misanuncios');
                    } else {
                        $error = "Error al eliminar el anuncio";
                    }
                } else {
                    $error = "No puedes eliminar el anuncio de otro usuario";
                }
            } else {
                $error = "No puedes eliminar un anuncio si no inicias sesión";
            }
        } else {
            $error = "El id del anuncio a eliminar está vacio";
        }
    }

    if (isset($_GET['eliminarmicuenta'])) {
        if (isset($_SESSION['email'])) {
            $usuario = $DAOUsuarios->selectByEmail($_SESSION['email']);
            $fotoUsuario = $usuario->getFoto();
            if ($DAOUsuarios->deleteByEmail($_SESSION['email'])) {
                // Borrar foto del usuario
                unlink("fotosUsuarios/$fotoUsuario");

                // Borrar cookie del usuario y redirigir a index
                header('location: logout.php');
            } else {
                $error = "Error al eliminar el usuario";
            }
        } else {
            $error = "No puedes eliminar una cuenta si no inicias sesión";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navapop</title>
    <link rel="stylesheet" href="./src/estilos/estilos.css">
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
            <?php if (isset($_SESSION['email'])): ?>
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
                <a href="index.php?eliminarmicuenta">
                    <div class="deleteL">
                        <p style="color: red;">Eliminar mi cuenta</p>
                    </div>
                </a>
            <?php else: ?>
                <a href="index.php">
                    <div class="nav-items">
                        <h4>Anuncios</h4>
                    </div>
                </a>
                <a href="index.php?misanuncios">
                    <div class="nav-items">
                        <h4>Mis anuncios</h4>
                    </div>
                </a>
                <div class="login-div">
                    <div class="login-text">
                        <h4>Iniciar sesión</h4>
                    </div>
                    <form action="login.php" method="post" class="login">
                        <label>Email: <br></label><input type="email" name="email" placeholder="Introduce tu email">
                        <label>Contraseña: <br></label><input type="password" name="passwd" placeholder="Introduce tu contraseña">
                        <input type="submit" value="Iniciar sesión">
                    </form>
                </div>
                <a href="registro.php">
                    <div class="nav-items" style="border-top: 1px solid #89c6d7; font-size: 1.3rem; padding-top: 8px;">
                        <label>¿No tienes una cuenta aún?</label><br>
                        <p>Crea una aquí</p>
                    </div>
                </a>
            <?php endif; ?>
        </nav>
        <section>
            <?php mostrarError($error); ?>
            <div class="anuncios-cont">
                <?php if ($anuncios != null): ?>
                    <?php foreach ($anuncios as $anuncio): ?>
                        <?php $mainfoto = $DAOFotos->selectMainById($anuncio->getId()); ?>
                        <div class="anuncio">
                            <a href="anuncio.php?id=<?= $anuncio->getId() ?>">
                                <div>
                                    <img class="fotoA" src="fotosAnuncios/<?= $mainfoto ?>">
                                </div>
                                <div class="n-p-A-1">
                                    <h4 class="nombreA"><?= $anuncio->getNombre() ?></h4>
                                    <p class="precioA"><?= $anuncio->getPrecio() . '€' ?></p>
                                </div>
                            </a>
                            <?php if (isset($_GET['misanuncios'])): ?>
                                <div class="e-e-A">
                                    <form method="get" action="editaranuncio.php?id=">
                                        <input type="hidden" name="editar" value="<?= $anuncio->getId() ?>">
                                        <button>Editar</button>
                                    </form>
                                    <form method="get" action="index.php?eliminaranuncio=">
                                        <input type="hidden" name="eliminar" value="<?= $anuncio->getId() ?>">
                                        <button>Eliminar</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>Copyright &#169 Navapop 2023</p>
    </footer>
</body>
</html>