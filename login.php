<?php
session_start();

require_once 'dao/DAOUsuario.php';
require_once 'modelo/Usuario.php';
require_once 'utilidades/dbconn.php';
require_once 'utilidades/conninfo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpiamos los datos que vienen del usuario
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['passwd']);

    $connDB = new dbconn(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
    $conn = $connDB->getConnexion();

    // Validamos el usuario
    $usuariosDAO = new DAOUsuario($conn);
    if ($usuario = $usuariosDAO->selectByEmail($email)) {
        if (password_verify($password, $usuario->getPasswd())) {
            // Iniciamos sesión si el email y el password son correctos
            $_SESSION['email'] = $usuario->getEmail();
            $_SESSION['foto'] = $usuario->getFoto();
            $_SESSION['id'] = $usuario->getId();

            // Creamos la cookie para que nos recuerde 1 semana
            setcookie('sid', $usuario->getSid(), time()+24*60*60,'/');
            // Redirigimos a index.php
            header('location: index.php');
            die();
        }
    }

    // Si el email o password son incorrectos, redirigir a index.php
    $_SESSION['error']="Email o password incorrectos";
    header('location: index.php');
}
?>