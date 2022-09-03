<?php
require "conexion.php";
require "funciones.php";

$errors = array();

if (empty($_GET['id'])) {
    header('Location: index.php');
}

if (empty($_GET['token'])) {
    header('Location: index.php');
}

$user_id = $mysqli->real_escape_string($_GET['id']);
$token = $mysqli->real_escape_string($_GET['token']);


if (!verificaTokenPass($user_id, $token)) {
    echo 'No se pudo verificar los Datos';
    echo "</br>";
    echo "<a href='index.php'> Iniciar Sesion </a>";
    exit;
}





?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Confirmar Usuario</title>
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
                                <form action="guardar_contraseña.php" method="POST">
                                    <div class="card-header">
                                        <h3 class="text-center font-weight-light my-4">Cambiar contraseña</h3>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id=" user_id" name="user_id" value="<?php echo $user_id; ?>" />
                                        <input type="hidden" id="token" name="token" value="<?php echo $token; ?>" />
                                        <label class="small mb-1" for="inputPassword">Nueva contraseña</label>
                                        <input class="form-control py-4" id="contraseña" name="contraseña" type="password" placeholder="Ingrese contraseña" required />
                                        <label class="small mb-1" for="inputPassword">Confirmar contraseña</label>
                                        <input class="form-control py-4" id="con_contraseña" name="con_contraseña" type="password" placeholder="Confirmar contraseña" required />
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small"><button type="submit" class="btn btn-primary">Cambiar contraseña</button></div>
                                    </div>
                                </form>
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