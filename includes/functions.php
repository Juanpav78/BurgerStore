<?php


//Funciones para pathings

define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . '/functions.php');
require_once __DIR__ . '/iconos.php';

// $datos se convierten en variables dentro de la plantilla
function includeTemplate ($name, $inicio = false, array $datos = []){
    extract($datos);
    include TEMPLATES_URL . "/$name.php";
}

function iniciarSesion() : void {
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
}

function isAuth() : bool {
    iniciarSesion();
    return $_SESSION['login'] ?? false;
}

// Protege las paginas del panel de administracion
function requireAuth() : void {
    if(!isAuth()){
        redirigir('/login.php');
    }
}

// Redirige y detiene la ejecucion (sin exit el resto de la pagina se sigue ejecutando)
function redirigir(string $url) : never {
    header("Location: $url");
    exit;
}

// Escapa texto antes de imprimirlo en el HTML
function s($texto) : string {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// 21500 -> "21.5k$"
function formatoK($precio) : string {
    return round((float) $precio / 1000, 1) . 'k$';
}

/* ---------- Proteccion CSRF para los formularios ---------- */

function csrfToken() : string {
    iniciarSesion();
    if(empty($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfInput() : string {
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function csrfValido() : bool {
    iniciarSesion();
    return isset($_SESSION['csrf'], $_POST['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

// Lee un valor de configuracion del .env (o usa el valor por defecto)
function config(string $clave, string $porDefecto = '') : string {
    $valor = env($clave);
    return $valor !== false && $valor !== '' ? $valor : $porDefecto;
}

// 573001234567 -> "+57 300 123 4567"
function whatsappLegible() : string {
    $n = numeroWhatsapp();
    if(strlen($n) === 12 && str_starts_with($n, '57')){
        return '+57 ' . substr($n, 2, 3) . ' ' . substr($n, 5, 3) . ' ' . substr($n, 8);
    }
    return $n ? "+$n" : '';
}

// Numero de WhatsApp del negocio (solo digitos, con indicativo de pais)
function numeroWhatsapp() : string {
    return preg_replace('/\D/', '', config('WHATSAPP_NUMERO'));
}

?>