<?php
require "conexion.php";

$usuario = "rodriguito123";
$password = "123";

global $mysqli;

$stmt = $mysqli->prepare("SELECT id_usuario, id_rol, contraseña FROM usuarios WHERE usuario = ? || correo = ? LIMIT 1");
$stmt->bind_param("ss", $usuario, $usuario);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $id_rol, $passwBd);
$stmt->fetch();

echo "aqui es la contraseña de la BD" . $passwBd . "<br>";
echo " contrasena escrita " . $password . "<br>";

//$validaPassw = password_verify($password, $passwBd);
if (password_verify($password, $passwBd)) {
    echo "entro";
} else {
    echo "no entro";
}

//echo $validaPassw;
