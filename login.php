<?php

require 'includes/app.php';

use App\Usuario;

    // Si ya inicio sesion no tiene sentido mostrar el login
    if(isAuth()){
        redirigir('/admin');
    }

    $errores = [];
    $email = '';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(!csrfValido()){
        $errores[]= "La sesión expiró, vuelve a intentarlo";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errores[]= "el Email es obligatorio o no es valido";
    }
    if(!$password){
        $errores[]= "el Password es obligatorio";
    }

    if(empty($errores)){
        //revisar si el usuario existe y si el password concuerda (consulta preparada)
        $usuario = Usuario::findByEmail($email);

        if($usuario && $usuario->comprobarPassword($password)){
            // Nuevo id de sesion para evitar robo de sesion
            session_regenerate_id(true);
            $_SESSION['usuario'] = $usuario->email;
            $_SESSION['login'] = true;
            redirigir('/admin');
        }
        // Mismo mensaje en ambos casos para no revelar que emails existen
        $errores[]= "Email o password incorrectos";
    };

};

    includeTemplate("header", false, ['admin' => true, 'titulo' => 'Iniciar sesión | Burger Store']);

?>

<main class="login" >
    <h1 class="login__title">Iniciar sesion</h1>
    <?php
    foreach($errores as $error) :
    ?>
    <div class="alert warning">
        <?php echo s($error); ?>
    </div>
    <?php
    endforeach;
    ?>
    <form class="formulario formulario--login" method="POST">
        <?php echo csrfInput(); ?>
        <img class="formulario__img" src="/src/img/Logo.svg" alt="logo burgershop" width="240" height="153">

        <label class="formulario__label" for="email">Email: </label>
        <input class="formulario__input" name="email" id="email" type="email" placeholder="email" value="<?php echo s($email); ?>" autocomplete="username" required>

        <label class="formulario__label" for="password">Clave: </label>
        <input class="formulario__input" name="password" id="password" type="password" required placeholder="password" autocomplete="current-password">

        <input class="formulario__btn boton " type="submit" value="Iniciar sesión">
    </form>
</main>

<?php

    includeTemplate("footer", false, ['admin' => true]);
?>
