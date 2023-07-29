<?php 


//Funciones para pathings

define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . 'functions.php');
function includeTemplate ($name, $inicio = false){
    include TEMPLATES_URL . "/$name.php";
}


function isAuth() : bool {
    session_start();
    $auth = $_SESSION['login'];

    if($auth){
        return true;
    }
    return false;
}

?>