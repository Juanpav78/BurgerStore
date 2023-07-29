<?php 
    $db= conectarDB();
    if($inicio){
        $query = "SELECT * FROM productos LIMIT 6;";
    }else{
        $query = "SELECT * FROM productos;";
    }
    
    $resultadoQuery = mysqli_query($db, $query);

?>

<main class="menu">
    <h2 class="menu__title">Explora el menu</h2>
        <div class="menu__contenedor">
        <?php while($producto = mysqli_fetch_assoc($resultadoQuery)) :?>
            <div class="card--burger" id="<?php echo $producto['id']?>">
                <h3 class="card--burger__title"><?php echo $producto['nombre']?></h3>
                <p class="card--burger__description"><?php echo $producto['descripcion']?></p>
                <div class="contenedor__img">
                <img class="card--burger__img" src="/img/<?php echo $producto['imagen']?>.png" alt="hamburguesa">
                <span class="card--burger__price"><?php echo $producto['precio']/1000?>k$</span>  
                </div>  
            </div>
            <div class="modal--burger" >
                <div class="modal--burger__contenedor">
                <a class="modal--burger__close btnClose">X</a>
                <div class="modal--burger__contenedor--img">
                <img class="modal--burger__img" src="/img/<?php echo $producto['imagen']?>.png" alt="hamburguesa">
                </div>
                <div class="modal--burger__contenedor--info">
                <h3 class="modal--burger__title"><?php echo $producto['nombre']?></h3>
                <span class="modal--burger__price"><?php echo $producto['precio']/1000?>k$</span>  
                <p class="modal--burger__description"><?php echo $producto['descripcion']?></p>
                
                <form class="modal--burger__formulario">
                    <div class="modal--burger__formulario__contenedor">
                    <input class="modal--burger__formulario__input" id="cantidad" name="cantidad" type="number" placeholder="Cantidad" max="5" min="1">
                    <p class="modal--burger__formulario__precio" id="priceTotal">Total: <?php echo $producto['precio']* 2/1000?>k$</p>
                    </div>
                    <div class="modal--burger__formulario__botones">
                    <button class="boton boton--yellow">Añadir al carrito</button>
                    <a class="boton boton--green">Comprar ahora</a>
                    </div>   
                </form>
                </div>
                </div>
            </div>
        <?php  endwhile;?>
           
        </div>
        <?php if ($inicio) : ?>
                <a class="boton--menu boton boton--green" href="/menu.php">Ver mas</a>
            <?php endif;?>
    </main>