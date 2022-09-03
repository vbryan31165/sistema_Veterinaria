<?php
require "conexion.php";

$usuario = "rodriguito123";
$password = "123";

// global $mysqli;

// $stmt = $mysqli->prepare("SELECT id_usuario, id_rol, contraseña FROM usuarios WHERE usuario = ? || correo = ? LIMIT 1");
// $stmt->bind_param("ss", $usuario, $usuario);
// $stmt->execute();
// $stmt->store_result();
// $stmt->bind_result($id, $id_rol, $passwBd);
// $stmt->fetch();

// echo "aqui es la contraseña de la BD" . $passwBd . "<br>";
// echo " contrasena escrita " . $password . "<br>";

// //$validaPassw = password_verify($password, $passwBd);
// if (password_verify($password, $passwBd)) {
//     echo "entro";
// } else {
//     echo "no entro";
// }

function isEmail($correo)
{
    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

// $correo='rodriguito133@yopmail.com';
// echo isEmail($correo);


function hashPassword($contraseña)
{
    $hash = password_hash($contraseña, PASSWORD_DEFAULT);
    return $hash;
}

$pass='1234';

$hash=hashPassword($pass);
echo $hash;
//echo $validaPassw;
