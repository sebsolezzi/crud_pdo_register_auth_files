<?php
require_once './templates/header.php';
require_once './helpers/funciones.php';
require_once './config/db.php';

$mensaje = [];
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // Validaciones
    if (!$username || !$password || !$password2) {

        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Debe completar todos los campos"];

    } else if (strlen($username) < 6) {

        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "El username debe tener al menos 6 caracteres"];

    } else if (strlen($password) < 6) {

        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "El password debe tener al menos 6 caracteres"];

    } else if ($password !== $password2) {

        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Los passwords no coinciden"];

    } else {
        // VERIFICAR SI EL USUARIO YA EXISTE
        $query = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $usuariodb = $query->fetch(PDO::FETCH_ASSOC);

        if ($usuariodb) {
            $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Nombre de usuario ya en uso"];
        }
    }

    if (empty($mensaje)) {
        //  GUARDA USUARIO EN BASE DE DATOS
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = $conn->prepare("INSERT INTO users (username, password) VALUES(:username, :password)");
        $query->bindParam(':username', $username, PDO::PARAM_STR);   // Asigna el username
        $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR); // Asigna el password hasheado

        if ($query->execute()) {
            $mensaje = ["tipo" => 'bg-success', "mensaje" => "Usuario creado. Ya puede usar su cuenta"];
        } else {
            $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Error al crear el usuario"];
        }
    }
}
?>

<div class="container">
    <div class="row">
        <h3 class="text-center text-info mt-2">Registro</h3>
        <?php require_once './templates/alerta.php'; ?>
        <div class="col-12 col-md-10 col-xl-4 mx-auto mt-2">
            <form method="POST" class="border border-2  border-info  rounded p-2">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label text-uppercase">username</label>
                    <input type="text" name="username" class="form-control" aria-describedby="emailHelp">

                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label text-uppercase">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label text-uppercase">Repetir Password</label>
                    <input type="password" name="password2" class="form-control" id="exampleInputPassword1">
                </div>
                <div id="emailHelp" class="form-text text-danger">No podrás recuperar tu cuenta si olvidas tu usuario o contraseña.</div>
                <button type="submit" class="btn btn-info text-uppercase d-block mx-auto mt-2">Registrar</button>

            </form>
        </div>
    </div>
    <div class="row">
        <a class="d-block text-center text-info mt-2" href="/login.php">¿Ya tienes una cuenta? Logueate</a>
    </div>
</div>


<?php
require_once './templates/footer.php';
?>