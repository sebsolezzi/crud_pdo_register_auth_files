<?php

$host = ''; // Nombre o IP del servidor
$db = ''; // Nombre de la base de datos
$user = ''; // Usuario de la base de datos
$pass = ''; // Contraseña de la base de datos


$dsn = "mysql:host=$host;dbname=$db";

// Opciones para PDO (opcional pero recomendado)
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Modo de errores: excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Modo de obtención por defecto: array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactivar la emulación de sentencias preparadas
];

try {
    // Crear una nueva instancia de PDO
    $conn = new PDO($dsn, $user, $pass);
    //echo "Conexión exitosa";
} catch (PDOException $e) {
    // Manejar errores de conexión
    echo 'Error de conexión: ' . $e->getMessage();
}

?>
