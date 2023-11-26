<?php
session_start();

require_once 'dao/DAOAnuncio.php';
require_once 'dao/DAOFoto.php';
require_once 'dao/DAOUsuario.php';
require_once 'modelo/Anuncio.php';
require_once 'modelo/Foto.php';
require_once 'modelo/Usuario.php';
require_once 'utilidades/dbconn.php';
require_once 'utilidades/conninfo.php';

$connDB = new dbconn(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
$conn = $connDB->getConnexion();

$error = '';

if (isset($_GET['id'])) {
    $DAOAnuncios = new DAOAnuncio($conn);
    $anuncio = $DAOAnuncios->selectById($_GET['id']);
    $nombreanuncio = $anuncio->getNombre();

    $DAOFotos = new DAOFoto($conn);
    $fotos = $DAOFotos->selectAllById($_GET['id']);
    if ($fotos == null) {
        $error = "No tiene fotos el anuncio";
    }

    $idUsuario = $anuncio->getIdusuario();
    $DAOUsuario = new DAOUsuario($conn);
    $usuario = $DAOUsuario->selectById($idUsuario);
} else {
    $error = "No puedes ver ningún anuncio si no especificas una id de un anuncio";
}

function mostrarError($error) {
    if ($error != '') {
        echo '<div class="error"><p style="color: red">' . $error . '</p></div>';
    }

    if (isset($_SESSION['error'])) {
        echo '<div class="error"><p style="color: red">' . $_SESSION['error'] . '</p></div>';
        unset($_SESSION['error']);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombreanuncio ?></title>
    <link rel="stylesheet" href="./src/estilos/estilos.css">
</head>
<body>
    <header>
        <img src="" alt="">
        <h1>Navapop</h1>
    </header>
    <main>
        <nav>
            <?php if(isset($_SESSION['email'])): ?>
                <div class="LogInfo">
                    <div class="imgL">
                        <img src="fotosUsuarios/<?= $_SESSION['foto']?>">
                    </div>
                    <div class="emailL">
                        <p><?= $_SESSION['email'] ?></p>
                    </div>
                </div>
                <div class="nav-items">
                    <h4><a href="index.php?misanuncios">Mis anuncios</a></h4>
                </div>
                <div class="nav-items">
                    <h4><a href="crearanuncio.php">Crear anuncio</a></h4>
                </div>
                <div class="logoutL"><a href="logout.php">Cerrar Sesión</a></div>
            <?php else: ?>
                <div class="nav-items">
                    <h4>Iniciar sesión</h4>
                </div>
                <form action="login.php" method="post" class="login">
                    <label>Email: <br></label><input type="email" name="email" placeholder="Introduce tu email">
                    <label>Contraseña: <br></label><input type="password" name="passwd" placeholder="Introduce tu contraseña">
                    <input type="submit" value="Iniciar sesión">
                </form>
                <div>
                    <?php
                        mostrarError($error);
                    ?>
                </div>
                <div class="nav-items">
                    <label>¿No tienes una cuenta aún? </label><br>
                    <a href="registro.php">Crea una aquí</a>
                </div>
            <?php endif; ?>
        </nav>
        <section>
            <?php
                mostrarError($error);
            ?>
            <div class="anuncios-cont">
                <div class="anuncio">
                    <div>
                        <img src="" alt="">
                    </div>
                    <?php foreach ($fotos as $foto): ?>
                        <div>
                            <img class="fotoA-M" src="fotosAnuncios/<?= $foto->getFoto()?>">
                        </div>
                    <?php endforeach; ?>
                    <div class="n-p-A">
                        <h4 class="nombreA"><?= $anuncio->getNombre() ?></h4>
                        <p class="descA"><?= $anuncio->getDescripcion()?></p>
                        <p class="precioA"><?= $anuncio->getPrecio() . '€' ?></p>
                    </div>
                    <div class="n-t-p-f-U">
                        <h4 class="nombre-U"><?= $usuario->getNombre() ?></h4>
                        <p class="telefono-U"><?= $usuario->getTelefono() ?></p>
                        <p class="poblacion-U"><?= $usuario->getPoblacion() ?></p>
                        <img class="foto-U" src="fotosUsuarios/<?= $usuario->getFoto() ?>">
                    </div>
                    <div>
                        <button>Chat</button>
                        <button>Comprar</button>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <p>Copyright © Navapop 2023</p>
    </footer>
</body>
</html>