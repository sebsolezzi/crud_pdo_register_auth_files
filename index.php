<?php
require_once './templates/header.php';
require_once './helpers/funciones.php';
require_once './config/db.php';
isAuth();
$msg = $_GET['msg'] ?? '';
$mensaje = check_message($msg);

$id = $_SESSION['id'];

// VERIFICAR SI EL USUARIO YA TIENE TAREAS
$query = $conn->prepare("SELECT * FROM posts WHERE id_user = :id  ORDER BY date DESC ");
$query->bindParam(':id', $id);
$query->execute();
$posts = $query->fetchAll(PDO::FETCH_ASSOC);

if (!$posts) {
    $mensaje = ["tipo" => 'bg-success', "mensaje" => "Aún no tienes posts"];
}

?>


<div class="container">
    <h3 class="mt-3">Hola <span class="text-primary"> <?php echo $_SESSION['username']; ?></span></h3>
    <div class="row">
        <div class="col-12">
            <?php require_once './templates/alerta.php'; ?>
        </div>


        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4 ">
                    <div class="card mx-auto mb-2" style="width: 18rem;">
                        <img src="/uploads/<?php echo $post['photo']; ?>" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title"> <?php echo $post['title']; ?></h5>
                            <p class="card-text"><?php echo $post['comment']; ?></p>
                            <p class="card-text">Creado: <?php echo transformar_fecha($post['date']); ?></p>
                            <a href="editar.php?postid=<?php echo $post['id']; ?>" class="btn btn-primary">Editar</a>
                            <a href="borrar.php?postid=<?php echo $post['id']; ?>" class="btn btn-danger">Borrar</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>
<?php require_once './templates/footer.php'; ?>