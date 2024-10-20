<?php
require_once './templates/header.php';
require_once './helpers/funciones.php';
require_once './config/db.php';
isAuth();

$mensaje = [];
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id_user = $_SESSION['id'];
    $title = htmlspecialchars($_POST['title']);
    $comment = htmlspecialchars($_POST['comment']);
    $photo = $_FILES['photo']; // Tomar el archivo subido

    // Validaciones
    if (!$title || !$comment) {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Debe completar el campo"];
    } else if (strlen($title) < 4) {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "El título debe tener al menos 4 caracteres"];
    } elseif (strlen($comment) < 8) {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "El comentario debe ser de al menos 8 caracteres"];
    } elseif (!isset($photo) || $photo['error'] != 0) {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Error al cargar la imagen"];
    } else {
        // Validaciones del archivo (ej: tamaño, tipo)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photo['type'], $allowedTypes)) {
            $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Formato de imagen no permitido. Solo se permiten JPEG, PNG y GIF"];
        } elseif ($photo['size'] > 5000000) { // Limitar tamaño a 5MB
            $mensaje = ["tipo" => 'bg-danger', "mensaje" => "La imagen es demasiado grande. El tamaño máximo es 5MB"];
        }
    }

    // Si no hay errores, proceder
    if (empty($mensaje)) {

        
        // Directorio donde se guardarán las imágenes
        $uploadDir = 'uploads/';

        // Generar un nombre único para la imagen
        $fileName = uniqid() . '_' . basename($photo['name']);
        $uploadFile = $uploadDir . $fileName;

        // Intentar mover la imagen al directorio de destino
        if (move_uploaded_file($photo['tmp_name'], $uploadFile)) {
            // Guarda los datos en la base de datos
            $estado_tarea = 0;
            $query = $conn->prepare("INSERT INTO posts (title, comment, photo, id_user) VALUES(:title, :comment, :photo, :id_user)");
            $query->bindParam(':title', $title, PDO::PARAM_STR);
            $query->bindParam(':comment', $comment, PDO::PARAM_STR);
            $query->bindParam(':photo', $fileName, PDO::PARAM_STR); 
            $query->bindParam(':id_user', $id_user, PDO::PARAM_INT);

            if ($query->execute()) {
                header('Location: /?msg=postupdate');
                exit();
            } else {
                $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Error al crear el post"];
            }
        } else {
            $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Error al mover la imagen"];
        }
    }
}

?>

<div class="container">
    <div class="row">
        <h3 class="text-center mt-2">Crear Posteo</h3>
        <?php require_once './templates/alerta.php'; ?>
        <div class="col-12 col-md-10 col-xl-4 mx-auto mt-2">
            <form method="POST" action="create.php" enctype="multipart/form-data" class="border border-2 border-info rounded p-2">
                <div class="mb-3">
                    <label for="title" class="form-label text-uppercase">Titulo</label>
                    <input type="text" name="title" class="form-control" aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label text-uppercase">Comentario</label>
                    <textarea class="form-control" name="comment" aria-label="With textarea"></textarea>
                </div>
                <div class="mb-3">
                    <label for="formFile" class="form-label text-uppercase">Foto</label>
                    <input class="form-control" type="file" name="photo" id="formFile">
                </div>

                <button type="submit" class="btn btn-info text-uppercase d-block mx-auto mt-4">Crear</button>

            </form>
            <a class="nav-link d-block text-center mt-3 text-info fw-bold" href="/">Volver al Inicio</a>
        </div>
    </div>
</div>


<?php
require_once './templates/footer.php';
?>