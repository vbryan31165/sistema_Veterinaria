<?php
require "conexion.php";
require "funciones.php";

$errors = array();


if (!empty($_POST)) {

    $cedula = $mysqli->real_escape_string($_POST['cedula']);
    $tarjetaProfesional = $mysqli->real_escape_string($_POST['tarjetaProfesional']);
    $nombres = $mysqli->real_escape_string($_POST['nombres']);
    $apellidos = $mysqli->real_escape_string($_POST['apellidos']);
    $correo = $mysqli->real_escape_string($_POST['correo']);
    $celular = $mysqli->real_escape_string($_POST['celular']);
    $municipio = $mysqli->real_escape_string($_POST['municipio']);
    $departamento = $mysqli->real_escape_string($_POST['departamento']);
    $usuario = $mysqli->real_escape_string($_POST['usuario']);
    $contraseña = $mysqli->real_escape_string($_POST['contraseña']);
    $con_contraseña = $mysqli->real_escape_string($_POST['con_contraseña']);
    $captcha = $mysqli->real_escape_string($_POST['g-recaptcha-response']);

    $pass = get_password();

    $estado = 0;
    $id_rol = 2;
    $secret = $pass['data_sitekey_server'];

    if (!$captcha) {
        $errors[] = "Por Favor verifica el captcha";
    }

    if (isNull($cedula, $tarjetaProfesional, $nombres, $apellidos, $correo, $celular, $municipio, $departamento, $usuario, $contraseña)) {

        $errors[] = "Debe llenar todos los campos";
    }

    if (!isEmail($correo)) {
        $errors[] = "Direccion de correo invalida";
    }

    if (!validatePassword($contraseña, $con_contraseña)) {
        $errors[] = "Las contraseñas no coninciden";
    }
    if (usuarioExiste($usuario)) {
        $errors[] = "El usuario ya existe";
    }
    if (correoExiste($correo)) {
        $errors[] = "El correo electronico $correo ya existe";
    }

    if (count($errors) == 0) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'secret' => $secret,
                'response' => $captcha
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        //$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha METHOD: POST");

        $arr = json_decode($response, TRUE);

        if ($arr['success']) {
            $pass_hash = hashPassword($contraseña);
            $token = generaToken();

            $registro = registrarUsuario($cedula, $tarjetaProfesional, $nombres, $apellidos, $correo, $celular, $municipio, $departamento, $usuario, $pass_hash, $token, $id_rol, $estado);

            if ($registro > 0) {
                $url = 'http://' . $_SERVER["SERVER_NAME"] . '/sistema_Veterinaria/app/activar.php?id=' . $registro . '&val=' . $token;

                $asunto = 'Activar Cuenta - Sistema Veterinaria';
                $cuerpo = "Estimado $nombres $apellidos : <br/><br/> Para continuar con el proceso de registro, es necesario dar clik en el siguiente link <a href='$url'>Activar Cuenta</a>";

                if (enviaremail($correo, $nombres, $apellidos, $asunto, $cuerpo)) {
                    echo "Para terminar el proceso de registro siga las instrucciones que le hemos enviado a la direccion de correo electronico : $correo";

                    echo "<br><a href='index.php'>Iniciar Sesion</a>";
                    exit;
                } else {
                    $errors[] = "Error al enviar Email";
                }
            } else {
                $errors[] = "Error al Registrar";
            }
        } else {
            $errors[] = "Error al comprobar Captcha";
        }
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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Crear Cuenta</h3>
                                </div>
                                <div class="card-body">
                                    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="small mb-1" for="inputCedula">Cedula</label>
                                                    <input class="form-control py-4" id="inputCedula" name="cedula" type="text" value="<?php if (isset($cedula)) echo $cedula; ?>" require />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="small mb-1" for="inputTarjetaProfesional">Tarjeta profesional</label>
                                                    <input class="form-control py-4" id="inputTarjetaProfesional" name="tarjetaProfesional" type="text" value="<?php if (isset($tarjetaProfesional)) echo $tarjetaProfesional; ?>" require />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="small mb-1" for="inputFirstName">Nombres</label>
                                                    <input class="form-control py-4" id="inputFirstName" name="nombres" type="text" value="<?php if (isset($nombres)) echo $nombres; ?>" require />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="small mb-1" for="inputLastName">Apellidos</label>
                                                    <input class="form-control py-4" id="inputLastName" name="apellidos" type="text" value="<?php if (isset($apellidos)) echo $apellidos; ?>" require />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputEmailAddress">Correo</label>
                                            <input class="form-control py-4" id="inputEmailAddress" type="email" name="correo" aria-describedby="emailHelp" value="<?php if (isset($correo)) echo $correo; ?>" require />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputPhone">Celular</label>
                                            <input class="form-control py-4" id="inputPhone" type="text" name="celular" aria-describedby="emailHelp" value="<?php if (isset($celular)) echo $celular; ?>" require />
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputMunicipio">Municipio</label>
                                                <select id="inputMunicipio" class="form-control" name="municipio">
                                                    <option selected>Elige...</option>
                                                    <option value="Arauca">Arauca</option>
                                                    <option value="Armenia">Armenia</option>
                                                    <option value="Barranquilla">Barranquilla</option>
                                                    <option value="Bogotá">Bogotá</option>
                                                    <option value="Bucaramanga">Bucaramanga</option>
                                                    <option value="Cali">Cali</option>
                                                    <option value="Cartagena">Cartagena</option>
                                                    <option value="Cúcuta">Cúcuta</option>
                                                    <option value="Florencia">Florencia</option>
                                                    <option value="Ibagué">Ibagué</option>
                                                    <option value="Leticia">Leticia</option>
                                                    <option value="Manizales">Manizales</option>
                                                    <option value="Medellín">Medellín</option>
                                                    <option value="Mitú">Mitú</option>
                                                    <option value="Mocoa">Mocoa</option>
                                                    <option value="Montería">Montería</option>
                                                    <option value="Neiva">Neiva</option>
                                                    <option value="Pasto">Pasto</option>
                                                    <option value="Pereira">Pereira</option>
                                                    <option value="Popayán">Popayán</option>
                                                    <option value="Puerto Carreño">Puerto Carreño</option>
                                                    <option value="Puerto Inírida">Puerto Inírida</option>
                                                    <option value="Quibdó">Quibdó</option>
                                                    <option value="Riohacha">Riohacha</option>
                                                    <option value="San Andrés">San Andrés</option>
                                                    <option value="San José del Guaviare">San José del Guaviare</option>
                                                    <option value="Santa Marta">Santa Marta</option>
                                                    <option value="Sincelejo">Sincelejo</option>
                                                    <option value="Tunja">Tunja</option>
                                                    <option value="Valledupar">Valledupar</option>
                                                    <option value="Villavicencio">Villavicencio</option>
                                                    <option value="Yopal">Yopal</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="small mb-1" for="inputDepartamento">Departamento</label>
                                                    <select id="inputDepartamento" class="form-control" name="departamento">
                                                        <option selected>Elige...</option>
                                                        <option value="Amazonas">Amazonas</option>
                                                        <option value="Antioquia">Antioquia</option>
                                                        <option value="Arauca">Arauca</option>
                                                        <option value="Atlántico">Atlántico</option>
                                                        <option value="Bolívar">Bolívar</option>
                                                        <option value="Boyacá">Boyacá</option>
                                                        <option value="Caldas">Caldas</option>
                                                        <option value="Caquetá">Caquetá</option>
                                                        <option value="Casanare">Casanare</option>
                                                        <option value="Cauca">Cauca</option>
                                                        <option value="Cesar">Cesar</option>
                                                        <option value="Chocó">Chocó</option>
                                                        <option value="Córdoba">Córdoba</option>
                                                        <option value="Cundinamarca">Cundinamarca</option>
                                                        <option value="Guainía">Guainía</option>
                                                        <option value="Guaviare">Guaviare</option>
                                                        <option value="Huila">Huila</option>
                                                        <option value="La Guajira">La Guajira</option>
                                                        <option value="Magdalena">Magdalena</option>
                                                        <option value="Meta">Meta</option>
                                                        <option value="Nariño">Nariño</option>
                                                        <option value="Norte de Santander">Norte de Santander</option>
                                                        <option value="Putumayo">Putumayo</option>
                                                        <option value="Quindío">Quindío</option>
                                                        <option value="Risaralda">Risaralda</option>
                                                        <option value="San Andrés y Providencia">San Andrés y Providencia</option>
                                                        <option value="Santander">Santander</option>
                                                        <option value="Sucre">Sucre</option>
                                                        <option value="Tolima">Tolima</option>
                                                        <option value="Valle del Cauca">Valle del Cauca</option>
                                                        <option value="Vaupés">Vaupés</option>
                                                        <option value="Vichada">Vichada</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputUser">Usuario</label>
                                            <input class="form-control py-4" id="inputUser" type="text" placeholder="Ingrese Usuario" name="usuario" require />
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="small mb-1" for="inputPassword">Contraseña</label>
                                                    <input class="form-control py-4" id="inputPassword" type="password" placeholder="Enter password" name="contraseña" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="small mb-1" for="inputConfirmPassword">Confirmar contraseña</label>
                                                    <input class="form-control py-4" id="inputConfirmPassword" type="password" placeholder="Confirm password" name="con_contraseña" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-3 mt-4 mb-0">
                                            </div>
                                            <div class="form-group col-md-6 mt-4 mb-0">
                                                <label for="captcha"></label>
                                                <div class="g-recaptcha" data-sitekey=<?php $pass = get_password();
                                                                                        echo $pass['data_sitekey'] ?>>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 mt-4 mb-0">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-3 mt-4 mb-0">
                                            </div>
                                            <div class="form-group col-md-6 mt-4 mb-0">
                                                <button class="btn btn-primary btn-block " type="submit">Crear Cuenta</button>
                                            </div>
                                            <div class="form-group col-md-3 mt-4 mb-0">
                                            </div>
                                        </div>
                                    </form>
                                    <?php echo resultBlock($errors); ?>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small">
                                        <a href="index.php">Tiene una cuenta? Iniciar sesión</a>
                                    </div>
                                </div>
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