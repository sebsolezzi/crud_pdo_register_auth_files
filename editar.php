<?php
require_once './templates/header.php';
require_once './helpers/funciones.php';
require_once './config/db.php';
//session_start();
isAuth();

$mensaje = [];

$id_user = $_SESSION['id'];
$post_id = filter_var($_GET['postid'], FILTER_VALIDATE_INT);

if (!$post_id) {
    header('Location:/?msg=notid');
    return;
}
//BERIFICAR SI EXISTE LA TAREA
$query = $conn->prepare("SELECT * FROM posts WHERE id = :id");
$query->bindParam(':id', $post_id);
$query->execute();
$post_db = $query->fetch(PDO::FETCH_ASSOC);

if (!$post_db) {
    header('Location:/?msg=notfound');
    return;
}
//VERIFICAR QUE LA TAREA QUE SE DESEA BORRA PERTENECE AL USUARIO LOGUEADO
if (!$post_db['id_user'] === $id_user) {
    header('Location:/?msg=forbidden');
    return;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $title = htmlspecialchars($_POST['title']);
    $comment = htmlspecialchars($_POST['comment']);
    $post_id = filter_var($_POST['post_id'], FILTER_VALIDATE_INT);
    $photo = $_FILES['photo'];
    
    // Verificar si existe el post
    $query = $conn->prepare("SELECT * FROM posts WHERE id = :id");
    $query->bindParam(':id', $post_id);
    $query->execute();
    $post_db = $query->fetch(PDO::FETCH_ASSOC);

    if (!$post_db) {
        header('Location:/?msg=notfound');
        return;
    }

    // Verificar si el post pertenece al usuario logueado
    if ($post_db['id_user'] !== $id_user) {
        header('Location:/?msg=forbidden');
        return;
    }

    // Validaciones de título y comentario
    if (!$title || !$comment) {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Debe completar el campo"];
    } else if (strlen($title) < 4) {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "El título debe tener al menos 4 caracteres"];
    } elseif (strlen($comment) < 8) {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "El comentario debe ser de al menos 8 caracteres"];
    }

    // Solo continuar si no hay errores previos
    if (empty($mensaje)) {
        // Manejo de la subida de la nueva foto (si existe)
        if (!empty($photo['name'])) {
            // Validaciones del archivo (ej: tamaño, tipo)
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($photo['type'], $allowedTypes)) {
                $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Formato de imagen no permitido. Solo se permiten JPEG, PNG y GIF"];
            } elseif ($photo['size'] > 5000000) { // Limitar tamaño a 5MB
                $mensaje = ["tipo" => 'bg-danger', "mensaje" => "La imagen es demasiado grande. El tamaño máximo es 5MB"];
            }

            // Solo proceder si no hay errores en la validación del archivo
            if (empty($mensaje)) {
                // Ruta de la foto anterior
                $foto_anterior = $post_db['photo'];
                
                // Eliminar la foto anterior si existe
                if ($foto_anterior && file_exists("uploads/$foto_anterior")) {
                    unlink("uploads/$foto_anterior");
                }

                // Guardar la nueva foto
                $nombre_nuevo_foto = uniqid() . '_' . basename($photo['name']);
                $ruta_foto = "uploads/" . $nombre_nuevo_foto;
                move_uploaded_file($photo['tmp_name'], $ruta_foto);

                // Actualizar el registro con la nueva foto
                $query = $conn->prepare("UPDATE posts SET title=:title, comment=:comment, photo=:photo WHERE id = :id");
                $query->bindParam(':photo', $nombre_nuevo_foto, PDO::PARAM_STR);
            }
        } else {
            // Si no se sube una nueva foto, mantener la anterior
            $query = $conn->prepare("UPDATE posts SET title=:title, comment=:comment WHERE id = :id");
        }

        // Actualizar el título y comentario
        $query->bindParam(':id', $post_id, PDO::PARAM_INT);
        $query->bindParam(':title', $title, PDO::PARAM_STR);
        $query->bindParam(':comment', $comment, PDO::PARAM_STR);

        // Ejecutar la actualización solo si no hay errores
        if (empty($mensaje) && $query->execute()) {
            header('Location:/?msg=updateok');
        } else {
            if (empty($mensaje)) {
                $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Error al actualizar el post"];
            }
        }
    }
}

?>

<div class="container">
    <div class="row">
        <h3 class="text-center text-info mt-2">Editar Post</h3>
        <?php require_once './templates/alerta.php'; ?>
        <div class="col-12 col-md-10 col-xl-4 mx-auto mt-2">
            <form method="POST" class="border border-info border-2 rounded p-2" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label text-uppercase">Titulo</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post_db['title']); ?>">
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label text-uppercase">Comentario</label>
                    <textarea class="form-control" name="comment" aria-label="With textarea"><?php echo htmlspecialchars($post_db['comment']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label text-uppercase">Foto actual</label>
                    <img class="img-fluid" src="/uploads/<?php echo htmlspecialchars($post_db['photo']); ?>" alt="">
                </div>
                <div class="mb-3">
                    <label for="formFile" class="form-label text-uppercase">Foto</label>
                    <input class="form-control" type="file" name="photo" id="formFile">
                </div>
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_db['id']); ?>">
                <button type="submit" class="btn btn-info text-uppercase d-block mx-auto mt-2">Editar</button>

            </form>
            <a class="nav-link d-block text-center mt-3 text-info fw-bold" href="/">Volver al Inicio</a>
        </div>
    </div>
</div>


<?php
require_once './templates/footer.php';
?>