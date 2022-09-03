<?php
require "conexion.php";
require "funciones.php";

$user_id = $mysqli->real_escape_string($_POST['user_id']);
$token = $mysqli->real_escape_string($_POST['token']);
$password = $mysqli->real_escape_string($_POST['contraseña']);
$con_password = $mysqli->real_escape_string($_POST['con_contraseña']);

if (validatePassword($password, $con_password)) {
    $pass_hash = hashPassword($password);

    if (cambiar_contraseña($pass_hash, $user_id, $token)) {
        echo "Contraseña modificada";
        echo "<br> <a href='index.php'> Iniciar Sesion </a>";
    } else {
        echo "Error al modificar la contraseña";
    }
} else {
    echo 'Las contraseñas no coinciden';
}
