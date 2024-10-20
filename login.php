<?php
require_once './templates/header.php';
require_once './helpers/funciones.php';
require_once './config/db.php';

$mensaje = [];
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Validaciones
    if (!$username || !$password) {

        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Debe completar todos los campos"];
    } else {
        // Verificar si el usuario ya existe
        $query = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $usuariodb = $query->fetch(PDO::FETCH_ASSOC);

        if (!$usuariodb) {
            // Si el usuario no existe, mostrar mensaje y detener la ejecución
            $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Usuario no registrado"];
        } else {
            // Verificar la contraseña solo si el usuario existe
            if (!password_verify($password, $usuariodb['password'])) {
                $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Contraseña incorrecta"];
            } else {
                // Iniciar sesión y redirigir si la contraseña es correcta
                session_start();
                $_SESSION['id'] = $usuariodb['id'];
                $_SESSION['username'] = $usuariodb['username'];
                header('Location:/');
            }
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
        <h3 class="text-center text-info mt-2">Login</h3>
        <?php require_once './templates/alerta.php'; ?>
        <div class="col-12 col-md-10 col-xl-4 mx-auto mt-2">
            <form method="POST" class="border border-2  border-info rounded p-2">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label text-uppercase">username</label>
                    <input type="text" name="username" class="form-control" aria-describedby="emailHelp">

                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label text-uppercase">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1">
                </div>

                <button type="submit" class="btn btn-info text-uppercase d-block mx-auto mt-2">Login</button>
            </form>
        </div>
    </div>
    <div class="row">
        <a class="d-block text-center text-info mt-2" href="/register.php">¿No tienes una cuenta? Crea una</a>
    </div>
</div>


<?php
require_once './templates/footer.php';
?>