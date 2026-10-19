<?php $whatsapp = numeroWhatsapp(); ?>
<div class="cart__fondo" id="carrito-fondo" hidden></div>

<aside class="cart" id="carrito" aria-label="Carrito de compras" aria-hidden="true" data-whatsapp="<?php echo s($whatsapp); ?>">
    <div class="cart__header">
        <h2 class="cart__heading">Tu pedido</h2>
        <button class="cart__cerrar" type="button" id="carrito-cerrar" aria-label="Cerrar carrito"><?php echo icono('cerrar'); ?></button>
    </div>

    <!-- Los productos se pintan desde src/js/app.js con los datos guardados en el navegador -->
    <div class="cart__contenedor" id="carrito-lista"></div>

    <div class="cart__vacio" id="carrito-vacio">
        <img src="/build/img/Hamburguesas_0004_burger_09.webp" alt="" width="160" height="128" loading="lazy">
        <p>Tu carrito está vacío</p>
        <a class="boton boton--yellow" href="/menu.php">Ver el menú</a>
    </div>

    <form class="cart__pedido" id="carrito-form">
        <div class="cart__resumen">
            <span>Total</span>
            <span class="cart__resumen__total" id="carrito-total">0k$</span>
        </div>

        <input class="cart__input" name="nombre" type="text" placeholder="Tu nombre" autocomplete="name" required>
        <input class="cart__input" name="direccion" type="text" placeholder="Dirección de entrega" autocomplete="street-address" required>
        <textarea class="cart__input" name="notas" rows="2" placeholder="Notas (sin cebolla, timbre dañado...)"></textarea>

        <button class="boton boton--green cart__whatsapp" type="submit">
            <?php echo icono('whatsapp'); ?> Enviar pedido por WhatsApp
        </button>
        <button class="cart__vaciar" type="button" id="carrito-vaciar">Vaciar carrito</button>
    </form>
</aside>

<div class="toast" id="toast" role="status" aria-live="polite" hidden></div>
