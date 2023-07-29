<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    $auth = $_SESSION['login'] ?? false;

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Karla:wght@300;400&family=Kavoon&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">    
<link rel="stylesheet" href="/build/css/index.css">
    <title>burger Store</title>
</head>
<body>
    <header class="header">
        <div class="header__contenedor">
        <a href="/" class="header__logo">
            <img class="header__img" src="/src/img/logo.svg" alt="logo hamburguesa burgershop">
        </a>
        <nav class="header__nav" id="nav">
            <a class="header__nav__link" href="/">Inicio</a>
            <a class="header__nav__link" href="/menu.php">Menú</a>
            <a class="header__nav__link" href="/login.php">Login</a>

            <?php if($auth) : ?>
                <a class="header__nav__link" href="/admin">Admin</a>
                <a class="header__nav__link" href="/cerrarSesion.php">Cerrar Sesion</a>
            <?php endif ;?>
        
        </nav>
        <button class="header__btn boton--circle" id="btn-menu" >
            <ion-icon name="menu-outline"></ion-icon>
        </button>
        </div>
    </header>