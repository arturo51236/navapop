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
require_once 'utilidades/error.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombreanuncio ?></title>
    <link rel="stylesheet" href="./src/estilos/estilos.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
            <?php if(isset($_SESSION['email'])): ?>
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
            <div class="anuncios-div">
                <div class="anuncio-check">
                    <div style="display: flex; margin-top: 2%; margin-bottom: 10px;">
                        <div class="fotoA-main">
                            <?php foreach ($fotos as $i => $foto): ?>
                                <?php if ($i == 0): ?>
                                    <img id="cambiarFoto" src="fotosAnuncios/<?= $foto->getFoto()?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="fotoA-notmain">
                            <?php foreach ($fotos as $j => $foto): ?>
                                <div style="fotoA-div">
                                    <button class="<?= $j ?>" style="border: none; background: none;">
                                        <img id="<?= $foto->getFoto()?>" class="fotoA-M" src="fotosAnuncios/<?= $foto->getFoto()?>">
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div style="display: flex; text-align: center;">
                        <div class="n-p-A-2">
                            <h4 class="nombreA"><?= $anuncio->getNombre() ?></h4>
                            <p class="descA"><?= $anuncio->getDescripcion()?></p>
                            <p class="precioA"><?= $anuncio->getPrecio() . '€' ?></p>
                        </div>
                        <div class="n-t-p-f-U">
                            <div style="display: flex; flex-direction: column">
                                <h4 class="nombre-U"><?= $usuario->getNombre() ?></h4>
                                <p class="telefono-U"><?= $usuario->getTelefono() ?></p>
                                <p class="poblacion-U"><?= $usuario->getPoblacion() ?></p>
                            </div>
                            <img class="foto-U" src="fotosUsuarios/<?= $usuario->getFoto() ?>">
                        </div>
                    </div>
                    <div class="but-anuncios" style="text-align: center; padding-top: 15px;">
                        <button>Chat</button>
                        <button>Comprar</button>
                    </div>
                </div>
            </div>
            <div class="vueltaA" style="transform: translateY(-70%);">
                <a href="index.php">Volver atrás</a>
            </div>
        </section>
    </main>
    <footer>
        <p>Copyright &#169 Navapop 2023</p>
    </footer>
    <script>
        jQuery(document).ready(function($) {
            $('.0').on({
                'click': function(){
                    var foto = $(this).find('img').attr('id');
                    $('#cambiarFoto').attr('src','./fotosAnuncios/' + foto);
                }
            });
            
            $('.1').on({
                'click': function(){
                    var foto = $(this).find('img').attr('id');
                    $('#cambiarFoto').attr('src','./fotosAnuncios/' + foto);
                }
            });
            
            $('.2').on({
                'click': function(){
                    var foto = $(this).find('img').attr('id');
                    $('#cambiarFoto').attr('src','./fotosAnuncios/' + foto);
                }
            });
            
            $('.3').on({
                'click': function(){
                    var foto = $(this).find('img').attr('id');
                    $('#cambiarFoto').attr('src','./fotosAnuncios/' + foto);
                }
            });

            $('.4').on({
                'click': function(){
                    var foto = $(this).find('img').attr('id');
                    $('#cambiarFoto').attr('src','./fotosAnuncios/' + foto);
                }
            });
        });
    </script>
</body>
</html>