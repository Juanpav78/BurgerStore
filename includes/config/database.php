<?php 

function conectarDB() : mysqli{
    $db = mysqli_connect('localhost', 'root', '0000', 'burgershop_crud');
    if(!$db){
        echo "Error en la BD";
        exit;
    }

    return $db;
}