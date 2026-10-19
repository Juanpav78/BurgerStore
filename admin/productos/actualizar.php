<?php
    require '../../includes/app.php';

    use App\Producto;

    requireAuth();

    $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT) ;
    $producto = $id ? Producto::find($id) : null;
    if(!$producto){
      redirigir('/admin');
    }

    $errores = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $producto->sincronizar($_POST);
        $imagen = $_FILES['imagen'] ?? null;

        //Validacion de formulario
        $errores = $producto->validar($imagen);
        if(!csrfValido()){
            $errores[] = "La sesión expiró, vuelve a intentarlo";
        }

        if(empty($errores)){
            // Si se sube una nueva imagen se reemplaza (y se borra la anterior)
            $producto->subirImagen($imagen);

            if($producto->guardar()){
                redirigir('/admin?status=2');
            }
            $errores[] = "No se pudo actualizar el producto";
        }
    }

//templates
    includeTemplate("header", false, ['admin' => true, 'titulo' => 'Editar producto | Burger Store']);
    includeTemplate("admin/formulario", false, [
        'producto' => $producto,
        'errores' => $errores,
        'titulo' => 'Editar: ' . $producto->nombre,
        'boton' => 'Guardar cambios',
    ]);
    includeTemplate("footer", false, ['admin' => true]);
