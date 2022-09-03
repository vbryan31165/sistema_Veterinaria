<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
//require 'vendor/autoload.php';


function resultBlock($errors)
{
    if (count($errors) > 0) {
        echo "<div id='error' class='alert alert-danger' role='alert'><a href='#' onclick=\"showHide('error');\">[x]</a> <ul>";
        foreach ($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
}

function isNullLogin($usuario, $password)
{
    if (strlen(trim($usuario)) < 1 || strlen(trim($password)) < 1) {
        return true;
    } else {
        return false;
    }
}

function login($usuario, $password)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT id_usuario, id_rol, contraseña FROM usuarios WHERE usuario = ? || correo = ? LIMIT 1");
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $stmt->store_result();
    $rows = $stmt->num_rows;

    if ($rows > 0) {

        if (isActivo($usuario)) {

            $stmt->bind_result($id, $id_rol, $passwBd);
            $stmt->fetch();

            $validaPassw = password_verify($password, $passwBd);

            if ($validaPassw) {

                lastSession($id);
                $_SESSION['id_usuario'] = $id;
                $_SESSION['tipo_usuario'] = $id_rol;

                header("location: principal.php");
            } else {

                $errors = "La contraseña es incorrecta";
            }
        } else {
            $errors = 'El usuario no esta activo';
        }
    } else {
        $errors = "El nombre de usuario o correo electronico no existe";
    }
    return $errors;
}

function lastSession($id)
{
    global $mysqli;

    $stmt = $mysqli->prepare(" UPDATE usuarios SET last_session=NOW(), token_password='', password_request=1 WHERE id_usuario = ? ");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
}

function isNull($cedula, $tarjetaProfesional, $nombres, $apellidos, $correo, $celular, $municipio, $departamento, $usuario, $contraseña)
{

    if (strlen(trim($cedula)) < 1 || strlen(trim($tarjetaProfesional)) < 1 || strlen(trim($nombres)) < 1 || strlen(trim($apellidos)) < 1 || strlen(trim($correo)) < 1 || strlen(trim($celular)) < 1 || strlen(trim($municipio)) < 1 || strlen(trim($departamento)) < 1 || strlen(trim($usuario)) < 1 || strlen(trim($contraseña)) < 1) {
        return true;
    } else {
        return false;
    }
}

function isEmail($correo)
{
    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

function validatePassword($var1, $var2)
{
    if (strcmp($var1, $var2) !== 0) {
        return false;
    } else {
        return true;
    }
}

function usuarioExiste($usuario)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT ID_USUARIO FROM USUARIOS WHERE USUARIO= ? LIMIT 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    $num = $stmt->num_rows();
    $stmt->close();

    if ($num > 0) {
        return true;
    } else {
        return false;
    }
}

function getValor($campo, $campoWhere, $valor)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT $campo FROM usuarios WHERE $campoWhere = ? LIMIT 1");
    $stmt->bind_param('s', $valor);
    $stmt->execute();
    $stmt->store_result();
    $num = $stmt->num_rows;

    if ($num > 0) {
        $stmt->bind_result($_campo);
        $stmt->fetch();
        return $_campo;
    } else {
        return null;
    }
}

function verificaTokenPass($user_id, $token)
{

    global $mysqli;
    $stmt = $mysqli->prepare("SELECT estado FROM usuarios WHERE id_usuario=? AND token_password = ? AND password_request = 1 LIMIT 1");
    $stmt->bind_param('is', $user_id, $token);
    $stmt->execute();
    $stmt->store_result();
    $num = $stmt->num_rows;

    if ($num > 0) {
        $stmt->bind_result($estado);
        $stmt->fetch();
        if ($estado == 1) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function correoExiste($correo)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT ID_USUARIO FROM USUARIOS WHERE CORREO= ? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    $num = $stmt->num_rows();
    $stmt->close();

    if ($num > 0) {
        return true;
    } else {
        return false;
    }
}

function validaToken($id, $token)
{
    global $mysqli;
    $stmt =  $mysqli->prepare(" SELECT estado FROM usuarios WHERE id_usuario = ? AND token = ? limit 1 ");

    $stmt->bind_param("is", $id, $token);
    $stmt->execute();
    $stmt->store_result();
    $rows = $stmt->num_rows();

    if ($rows > 0) {
        $stmt->bind_result($estado);
        $stmt->fetch();

        if ($estado == 1) {
            $msg = "La cuenta ya se activó correctamente.";
        } else {
            if (activarUsuario($id)) {
                $msg = "Cuenta activada.";
            } else {
                $msg = "Error al activar cuenta.";
            }
        }
    } else {
        $msg = "No existe el resgistro para activar.";
    }
    return $msg;
}

function activarUsuario($id)
{
    global $mysqli;
    $stmt = $mysqli->prepare(" UPDATE usuarios SET estado = 1 WHERE id_usuario = ? ");
    $stmt->bind_param("s", $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}


function isActivo($usuario)
{
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT estado FROM usuarios WHERE usuario = ? || correo = ? LIMIT 1");
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $stmt->bind_result($estado);
    $stmt->fetch();

    if ($estado == 1) {
        return true;
    } else {
        return false;
    }
}


function generaToken()
{
    $gen = md5(uniqid(mt_rand(), false));
    return $gen;
}

function generaTokenPass($user_id)
{
    global $mysqli;

    $token = generaToken();

    $stmt = $mysqli->prepare("UPDATE usuarios SET token_password = ?, password_request = 1 WHERE id_usuario = ?");
    $stmt->bind_param('ss', $token, $user_id);
    $stmt->execute();
    $stmt->close();

    return $token;
}

function hashPassword($contraseña)
{
    $hash = password_hash($contraseña, PASSWORD_DEFAULT);
    return $hash;
}

function cambiar_contraseña($password, $user_id, $token)
{
    global $mysqli;

    $stmt = $mysqli->prepare("UPDATE usuarios SET contraseña = ?, token_password='', password_request=0 WHERE id_usuario = ? AND token_password = ?");
    $stmt->bind_param('sis', $password, $user_id, $token);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function registrarUsuario($cedula, $tarjetaProfesional, $nombres, $apellidos, $correo, $celular, $municipio, $departamento, $usuario, $pass_hash, $token, $id_rol, $estado)
{
    global $mysqli;

    $stmt = $mysqli->prepare(" INSERT INTO usuarios (cedula, tarjeta_Profesional, nombres, apellidos, correo, celular, municipio, departamento, usuario, contraseña, token,id_rol, estado) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ");
    $stmt->bind_param('sssssssssssii', $cedula, $tarjetaProfesional, $nombres, $apellidos, $correo, $celular, $municipio, $departamento, $usuario, $pass_hash, $token, $id_rol, $estado);

    if ($stmt->execute()) {
        return $mysqli->insert_id;
    } else {
        return 0;
    }
}

function enviaremail($email, $nombres, $apellidos, $asunto, $cuerpo)
{

    require_once 'PHPMailer/src/PHPMailer.php';
    $password = get_password();

    $mail = new PHPMailer(true);

    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = $password['host'];
    $mail->Port = $password['port'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->SMTP = true;


    $mail->Username = $password['correo'];
    $mail->Password = $password['contraseña'];

    $mail->setFrom($email, '');
    $mail->addAddress($email, $nombres, $apellidos);

    $mail->Subject = $asunto;
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Body = $cuerpo;

    if ($mail->send())
        return true;
    else
        return false;
}


function get_password()
{
    $data = file_get_contents("c:/xampp/htdocs/sistema_Veterinaria/password.json");
    $password = json_decode($data, true);

    //echo $password['data_sitekey'];
    return $password;
}
