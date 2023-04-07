<?php

// Importar la conexión
require 'includes/config/database.php';
$db = conectarDB();

// Crear un email y password
$email = "tu_correo@example.com";
$password = "123456";

$passwordHash = password_hash($password, PASSWORD_BCRYPT);


// Query para crear el usuario
$query = " INSERT INTO usuarios (email, password) VALUES ('${email}','${passwordHash}');";

//echo $query;

// Agregarlo a la base de datos
mysqli_query($db, $query);