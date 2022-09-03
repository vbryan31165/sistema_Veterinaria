<?php
require "conexion.php";
require "funciones.php";

$errors = array();

if (!empty($_POST)) {

    $email = $mysqli->real_escape_string($_POST["email"]);

    if (!isEmail($email)) {
        $errors[] = "Debe ingresar un correo electrónico valido.";
    }
    if (correoExiste($email)) {
        $user_id = getValor('id_usuario', 'correo', $email);
        $nombre = getValor('nombres', 'correo', $email);
        $apellidos = getValor('apellidos', 'correo', $email);
        echo var_dump($user_id);
        echo var_dump($nombre);
        $token = generaTokenPass($user_id);

        $url = 'http://' . $_SERVER["SERVER_NAME"] . '/sistema_Veterinaria/app/cambiar_contraseña.php?id=' . $user_id . '&token=' . $token;

        $asunto = 'Recuperar Contraseña - Sistema Veterinaria';
        $cuerpo = "Hola ". ucfirst($nombre) . " " . ucfirst($apellidos) . ": <br></br> Se ha solicitado un reinicio de contraseña. <br></br> Para restaurar la contraseña, visite la siguiente dirección <a href='$url'>Cambiar Contraseña</a>";

        if (enviaremail($email, $nombre, $apellidos, $asunto, $cuerpo)) {

            echo "Hemos enviado un correo electronico a la dirección $email para reestablecer su contraseña. </br>";
            echo "<a href='index.php'> Iniciar Sesion </a>";
            exit;
        } else {
            $errors[] = "Error al enviar el email";
        }
    } else {
        $errors[] = "No existe el correo electrónico";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Page Title - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Recuperación de contraseña</h3>
                                </div>
                                <div class="card-body">
                                    <div class="small mb-3 text-muted">Ingrese su dirección de correo electrónico y le enviaremos un enlace para restablecer su contraseña.</div>
                                    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputEmailAddress">Email</label>
                                            <input class="form-control py-4" id="email" name="email" type="email" aria-describedby="emailHelp" placeholder="Introduzca la dirección de correo electrónico" />
                                        </div>
                                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="index.php">Volver a iniciar sesión</a>
                                            <button class="btn btn-primary" type="submit">Restablecer la contraseña</button>
                                        </div>
                                    </form>
                                    <?php echo resultBlock($errors); ?>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="register.php">Necesitas una cuenta? Registrate!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2019</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>