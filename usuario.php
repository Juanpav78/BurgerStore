<?php 
//Importar conexión a la base de datos
$db = conectarDB();

//Crear usuario

$email = "jpav@correo.com";
$password= "123456";

$passwordHash= password_hash($password, PASSWORD_BCRYPT);

//Query para crear el usuario
$query = "INSERT INTO usuarios (email, password) VALUES ('{$email}','{$passwordHash}')";
echo $query;

exit;
//Insertar en base de datos
mysqli_query($db, $query);