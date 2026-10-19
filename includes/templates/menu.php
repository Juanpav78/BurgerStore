<?php
    use App\Producto;

    $productos = Producto::all($inicio ? 6 : null);
?>

<main class="menu">
    <h2 class="menu__title">Explora el menu</h2>
        <div class="menu__contenedor">
        <?php foreach($productos as $producto) :?>
            <article class="card--burger"
                tabindex="0"
                role="button"
                aria-label="Ver <?php echo s($producto->nombre)?>"
                data-id="<?php echo s($producto->id)?>"
                data-nombre="<?php echo s($producto->nombre)?>"
                data-categoria="<?php echo s($producto->categoria)?>"
                data-precio="<?php echo s($producto->precio)?>"
                data-imagen="<?php echo s($producto->imagenUrl())?>">
                <div class="card--burger__media">
                    <img class="card--burger__img" src="<?php echo s($producto->imagenUrl())?>" alt="<?php echo s($producto->nombre)?>" width="700" height="560" loading="lazy" decoding="async">
                </div>
                <h3 class="card--burger__title"><?php echo s($producto->nombre)?></h3>
                <p class="card--burger__description"><?php echo s($producto->descripcion)?></p>
                <div class="card--burger__footer">
                    <span class="card--burger__price"><?php echo $producto->precioK()?></span>
                    <button class="card--burger__add" type="button" aria-label="Añadir <?php echo s($producto->nombre)?> al carrito">
                        <?php echo icono('mas'); ?>
                    </button>
                </div>
            </article>
        <?php endforeach;?>

        <?php if(empty($productos)) : ?>
            <p class="menu__vacio">Muy pronto tendremos hamburguesas para ti 🍔</p>
        <?php endif; ?>
        </div>
        <?php if ($inicio) : ?>
                <a class="boton--menu boton boton--green" href="/menu.php">Ver mas</a>
            <?php endif;?>
    </main>

<!-- Un solo modal reutilizable: se llena con los datos de la tarjeta (src/js/app.js) -->
<div class="modal--burger" id="modal-producto" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
    <div class="modal--burger__contenedor">
        <button class="modal--burger__close btnClose" type="button" aria-label="Cerrar"><?php echo icono('cerrar'); ?></button>
        <div class="modal--burger__contenedor--img">
            <img class="modal--burger__img" id="modal-img" src="data:," alt="" width="700" height="560">
        </div>
        <div class="modal--burger__contenedor--info">
            <span class="modal--burger__categoria" id="modal-categoria"></span>
            <h3 class="modal--burger__title" id="modal-titulo"></h3>
            <span class="modal--burger__price" id="modal-precio"></span>
            <p class="modal--burger__description" id="modal-descripcion"></p>

            <form class="modal--burger__formulario" id="modal-form">
                <div class="modal--burger__formulario__contenedor">
                    <div class="cantidad">
                        <button class="cantidad__btn" type="button" data-cambio="-1" aria-label="Quitar uno"><?php echo icono('menos'); ?></button>
                        <input class="cantidad__input" name="cantidad" type="number" value="1" min="1" max="10" aria-label="Cantidad">
                        <button class="cantidad__btn" type="button" data-cambio="1" aria-label="Agregar uno"><?php echo icono('mas'); ?></button>
                    </div>
                    <p class="modal--burger__formulario__precio">Total: <span class="modal--burger__formulario__total" id="modal-total"></span></p>
                </div>
                <div class="modal--burger__formulario__botones">
                    <button class="boton boton--yellow" type="submit">Añadir al carrito</button>
                    <button class="boton boton--green" type="button" id="modal-comprar">Comprar ahora</button>
                </div>
            </form>
        </div>
    </div>
</div>
