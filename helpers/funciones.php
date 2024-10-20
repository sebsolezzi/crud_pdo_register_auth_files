<?php
function debugear($dato)
{
    echo "<br>";
    var_dump($dato);
    echo "<br>";
    exit;
}
function isAuth()
{
    //session_start();
    if (!$_SESSION['id'] || !$_SESSION['username']) {
        header('Location:/login.php');
    }
}

function transformar_fecha($fecha) {
    // Crear un objeto DateTime a partir de la fecha original
    $fechaObjeto = new DateTime($fecha);
    
    // Formatear la fecha al formato DD-MM-AAAA
    return $fechaObjeto->format('d-m-Y');
}

function check_message($msg)
{
    $mensaje = [];
    if ($msg === 'taskok') {
        $mensaje = ["tipo" => 'bg-success', "mensaje" => "Post creado"];
    } else if ($msg === 'notid') {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Id no valido"];
    } else if ($msg === 'notfound') {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Post no encontrado"];
    } else if ($msg === 'forbidden') {
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "No autorizado"];
    } else if ($msg === 'deleteok') {
        $mensaje = ["tipo" => 'bg-success', "mensaje" => "Post borrado"];
    }else if($msg === 'deleterror'){
        $mensaje = ["tipo" => 'bg-danger', "mensaje" => "Error al borrar"];
    }else if($msg === 'updateok'){
        $mensaje = ["tipo" => 'bg-success', "mensaje" => "Post actualizado"];
    } else {
        $mensaje = [];
    }
    return $mensaje;
}
