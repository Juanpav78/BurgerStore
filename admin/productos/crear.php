<?php

require '../../includes/app.php';

use App\Producto;

requireAuth();

    $producto = new Producto;
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
            $producto->subirImagen($imagen);

            if(!$producto->imagen){
                $errores[] = "No se pudo guardar la imagen";
            }elseif($producto->guardar()){
                redirigir('/admin?status=1');
            }else{
                $errores[] = "No se pudo guardar el producto";
            }
        }
    }

//templates
    includeTemplate("header", false, ['admin' => true, 'titulo' => 'Nuevo producto | Burger Store']);
    includeTemplate("admin/formulario", false, [
        'producto' => $producto,
        'errores' => $errores,
        'titulo' => 'Nuevo producto',
        'boton' => 'Crear producto',
    ]);
    includeTemplate("footer", false, ['admin' => true]);
