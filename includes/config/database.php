<?php

// Lee un valor de configuracion: primero las variables del sistema/terminal
// y si no existen, las del archivo .env. No usa putenv() porque algunos
// hostings gratuitos (como InfinityFree) la tienen desactivada.
function env(string $clave) : string|false {
    static $valores = null;

    if($valores === null){
        $valores = [];
        $ruta = __DIR__ . '/../../.env';
        if(is_readable($ruta)){
            foreach(file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea){
                $linea = trim($linea);
                if($linea === '' || str_starts_with($linea, '#') || !str_contains($linea, '=')) continue;

                [$nombre, $valor] = array_map('trim', explode('=', $linea, 2));
                $valores[$nombre] = trim($valor, "\"'");
            }
        }
    }

    $sistema = getenv($clave);
    if($sistema !== false) return $sistema;
    return $valores[$clave] ?? false;
}

function conectarDB() : mysqli{
    // Valores tomados del .env (DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)
    $host = env('DB_HOST') ?: 'localhost';
    $user = env('DB_USER') ?: 'root';
    $pass = env('DB_PASS') !== false ? env('DB_PASS') : '0000';
    $name = env('DB_NAME') ?: 'burgershop_crud';
    $port = (int) (env('DB_PORT') ?: 3306);

    try {
        // Desde PHP 8.1 mysqli lanza una excepcion en vez de devolver false
        $db = mysqli_connect($host, $user, $pass, $name, $port);
        mysqli_set_charset($db, 'utf8mb4');
    } catch (mysqli_sql_exception $e) {
        echo "Error en la BD";
        exit;
    }

    return $db;
}
