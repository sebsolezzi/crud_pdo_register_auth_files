<?php

require_once './helpers/funciones.php';
require_once './config/db.php';
session_start();
isAuth();

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

$photo = $post_db['photo'];

$query = $conn->prepare("DELETE FROM posts WHERE id = :id");
$query->bindParam(':id', $post_id);


/* 
Pude ser necesario usar __DIR__ en un servidor real
if($query->execute()){
    $photo_path = __DIR__ . "/uploads/$photo"; 
    unlink($photo_path); //borramos la foto si se borra el post de la db
    header('Location:/?msg=deleteok');
} else{
    header('Location:/?msg=deleteerror');
}
*/
if($query->execute()){
    unlink("uploads/$photo"); //borramos la foto si se borra el post de la db
    header('Location:/?msg=deleteok');
} else{
    header('Location:/?msg=deleteerror');
}



