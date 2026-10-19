<?php
    iniciarSesion();

    $auth = $_SESSION['login'] ?? false;
    $admin = $admin ?? false;
    $titulo = $titulo ?? 'Burger Store';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Burger Store: las mejores hamburguesas a domicilio. Arma tu pedido y envíalo por WhatsApp.">
    <meta name="theme-color" content="#2F2D2E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:wght@300;400;700&family=Kavoon&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/build/css/index.css">
    <link rel="icon" href="/src/img/logo.png">
    <title><?php echo s($titulo); ?></title>
</head>
<body class="<?php echo $admin ? 'body--admin' : ''; ?>">
    <header class="header">
        <div class="header__contenedor">
        <a href="/" class="header__logo">
            <img class="header__img" src="/src/img/Logo.svg" alt="logo hamburguesa burgershop" width="80" height="51">
        </a>
        <nav class="header__nav" id="nav">
            <a class="header__nav__link" href="/">Inicio</a>
            <a class="header__nav__link" href="/menu.php">Menú</a>

            <?php if($auth) : ?>
                <a class="header__nav__link" href="/admin">Admin</a>
                <a class="header__nav__link" href="/cerrarSesion.php">Cerrar Sesion</a>
            <?php endif ;?>

        </nav>

        <?php if(!$admin) : ?>
        <button class="header__cart" id="btn-carrito" aria-label="Abrir carrito" aria-controls="carrito">
            <?php echo icono('carrito'); ?>
            <span class="header__cart__badge" id="carrito-contador" hidden>0</span>
        </button>
        <?php endif; ?>

        <button class="header__btn boton--circle" id="btn-menu" aria-label="Abrir menú" aria-controls="nav" aria-expanded="false">
            <?php echo icono('menu'); ?>
        </button>
        </div>
    </header>
