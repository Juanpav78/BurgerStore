<?php 

require 'includes/app.php';
    $db = conectarDB();

    $errores = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){  

    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if(!$email){
        $errores[]= "el Email es obligatorio o no es valido";
    }
    if(!$password){
        $errores[]= "el Password es obligatorio";
    }

    if(empty($errores)){
        //revisar si el usuario existe
        $query = "SELECT * FROM usuarios WHERE email = '{$email}'";
        $resultado = mysqli_query($db, $query);

        if($resultado->num_rows){
         //revisar si el password concuerda
         $usuario = mysqli_fetch_assoc(($resultado));
         $auth = password_verify($password, $usuario['password']);
            var_dump($auth);
         if($auth){
            session_start();
            $_SESSION['usuario'] = $usuario['email'];
            $_SESSION['login'] = true;
            header('Location: /admin');
         }
        }else{
            $errores[]= "El usuario no existe";
        }
       
    };

};

    includeTemplate("header");

?>

<main class="login" >
    <h1 class="login__title">Iniciar sesion</h1>
    <?php 
    foreach($errores as $error) :
    ?>
    <div class="alert warning">
        <?php echo $error; ?>
    </div>
    <?php 
    endforeach;
    ?>
    <form class="formulario formulario--login" method="POST">
        <img class="formulario__img" src="/src/img/logo.svg" alt="">

        <label class="formulario__label" for="email">Email: </label>
        <input class="formulario__input" name="email" id="email" type="text" placeholder="email" required>

        <label class="formulario__label" for="password">Clave: </label>
        <input class="formulario__input" name="password" id="password" type="password" required placeholder="password">

        <input class="formulario__btn boton " type="submit" value="Iniciar sesión">
    </form>
</main>

<?php 

    includeTemplate("footer");
?>