<?php
require 'functions.php';
require 'config/database.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Producto;
use App\Usuario;

// Una sola conexion para toda la peticion
$db = conectarDB();
Producto::setDB($db);
Usuario::setDB($db);
