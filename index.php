<?php
session_start();

require_once 'dao/DAOAnuncio.php';
require_once 'dao/DAOUsuario.php';
require_once 'dao/DAOFoto.php';
require_once 'modelo/Anuncio.php';
require_once 'modelo/Usuario.php';
require_once 'utilidades/dbconn.php';
require_once 'utilidades/conninfo.php';

$connDB = new dbconn(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
$conn = $connDB->getConnexion();
$DAOUsuarios = new DAOUsuario($conn);

$error = '';
//Si existe la cookie y no ha iniciado sesión, le iniciamos sesión de forma automática
if (!isset($_SESSION['email']) && isset($_COOKIE['sid'])) {
    //Nos conectamos para obtener el id y la foto del usuario
    if ($usuario = $DAOUsuarios->selectBySid($_COOKIE['sid'])) {
        //Inicio sesión
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['id'] = $usuario->getId();
        $_SESSION['foto'] = $usuario->getFoto();
    }
}

$DAOAnuncios = new DAOAnuncio($conn);
$anuncios = $DAOAnuncios->selectAll();

if ($anuncios == null) {
    $error = "No existe ningún anuncio en la base de datos de Navapop aún";
} else {
    $DAOFotos = new DAOFoto($conn);

    if (isset($_GET['misanuncios'])) {
        if (isset($_SESSION['id'])) {
            $anuncios = $DAOAnuncios->selectAllById($_SESSION['id']);
        } else {
            $error = "Para poder ver tus anuncios debes de iniciar sesión";
        }
    }
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
    <title>Navapop</title>
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
            <div class="anuncios-cont">
                <?php foreach ($anuncios as $anuncio): ?>
                    <?php
                        $mainfoto = $DAOFotos->selectMainById($anuncio->getId());
                    ?>
                    <div class="anuncio">
                        <div>
                            <img class="fotoA" src="fotosAnuncios/<?= $mainfoto ?>">
                        </div>
                        <div class="n-p-A">
                            <h4 class="nombreA"><a href="anuncio.php?id=<?=$anuncio->getId()?>"><?= $anuncio->getNombre() ?></a></h4>
                            <p class="precioA"><?= $anuncio->getPrecio() . '€' ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>Copyright © Navapop 2023</p>
    </footer>
</body>
</html>