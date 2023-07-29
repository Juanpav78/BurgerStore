<?php 

require '../../includes/app.php';

use App\Producto;
$propiedad = new Producto;
$auth = isAuth();

if(!$auth){
    header('Location: /');
}
//coneccion a base de datos
    $db= conectarDB();
    $errores = [];
    $nombre = "";
    $categoria = "";
    $precio = "";
    $imagen = "";
    $descripcion = "";


    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
        $categoria = mysqli_real_escape_string($db, $_POST['categoria']);
        $precio = mysqli_real_escape_string($db, $_POST['precio']);
        $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
        $creado = date('Y/m/d');

        $imagen = $_FILES['imagen'];

        //Validacion de formulario
        if(!$nombre){
            $errores[] = "El nombre es obligatorio";
            
        }
        if(!$categoria){
            $errores[] = "la categoria es obligatoria";
            
        }
        if(!$precio){
            $errores[] = "El precio es obligatorio";
            
        }
        if(strlen($descripcion) < 50){
            $errores[] = "La descripción debe ser mayor a 50 caracteres";
        }
        if(!$nombre || !$categoria || !$precio || !$descripcion){
            $errores[] = "Todos los campos son obligatorios";
        }
        if($imagen['name'] = ''){
            $errores[] = "La imagen es obligatoria";
        }

        //validar por tamaño (1mb maximo)
        $medida =2000 * 1000;

        if(!$imagen['size'] = $medida){
            $errores[] = "La imagen es muy pesada";
        }
        
        if(empty($errores)){

            $carpetaImg = '../../img';
        // Crear carpeta para las imagenes
            if(!is_dir($carpetaImg)){
                mkdir($carpetaImg);
            }

        //generar nombre unico

        $nombreImg = strtolower($categoria) ."_".md5(uniqid(rand(), true));
        // Subir imagen

        move_uploaded_file($imagen['tmp_name'], $carpetaImg . "/". $nombreImg.".png");

            $query = "INSERT INTO productos (nombre, categoria, precio, descripcion,creado, imagen,comprador_id) 
            VALUES ('$nombre', '$categoria', '$precio', '$descripcion','$creado', '$nombreImg', '1')
            ;";

            $resultado = mysqli_query($db, $query);

            if($resultado){
                header('Location: /admin?status=1');
            }
        }
    }


//templates
    includeTemplate("header");
  
?>

<main class="main--admin">
    <h1>CREANDO</h1>
    <a href="/admin">Volver</a>

    <?php foreach($errores as $error) : ?>
        <div class="alert warning">
        <?php echo $error; ?>
        </div>
       
    <?php endforeach; ?>
    <div class="main--admin__contenedor">


    <form class="formulario" method="post" enctype="multipart/form-data" >
        <fieldset class="formulario__fs" >
            <legend class="formulario__legend">Crear producto</legend>

            <label class="formulario__label" for="nombre">Nombre: </label>
            <input value="<?php echo $nombre; ?>" class="formulario__input" type="text" id="nombre" name="nombre" placeholder="Nombre del Producto...">

            <label class="formulario__label" for="categoria">Categoria: </label>
            <input value="<?php echo $categoria; ?>" class="formulario__input" type="text" id="categoria" name="categoria" placeholder="Categoria del Producto...">

            <label class="formulario__label" for="precio">Precio: </label>
            <input value="<?php echo $precio; ?>" class="formulario__input" type="number" id="precio" name="precio" placeholder="Precio del Producto...">

            <label class="formulario__label" for="imagen">Imagen: </label>
            <input class="formulario__input" type="file" id="imagen" name="imagen" accept="image/jpeg, image/png">

            <label class="formulario__label" for="descripcion">Descripcion: </label>
            <textarea class="formulario__input" id="descripcion" name="descripcion" placeholder="Descripcion del Producto..."><?php echo $descripcion; ?></textarea>
        </fieldset>

        <input class="formulario__btn boton boton--green" type="submit" value="Crear Producto">
    </form>
    <div class="preview">
    <div class="card--burger">
        <h3 class="card--burger__title"><?php echo $nombre; ?></h3>
        <p class="card--burger__description"><?php echo $descripcion; ?></p>
        <div class="contenedor__img">
            <img class="card--burger__img" src="/src/img/burger_4.png" alt="hamburguesa">
            <span class="card--burger__price"><?php echo $precio; ?></span>  
        </div>  
    </div>
    </div>

    </div>
</main>

<?php 
  includeTemplate("footer");
?>

