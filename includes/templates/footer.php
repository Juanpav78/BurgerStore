<?php
    $admin = $admin ?? false;
    $whatsapp = numeroWhatsapp();
    $telefono = config('TELEFONO', '605 1444-88');

    // Solo se muestran las redes configuradas en el .env
    $redes = array_filter([
        'instagram' => config('INSTAGRAM_URL'),
        'facebook'  => config('FACEBOOK_URL'),
        'tiktok'    => config('TIKTOK_URL'),
        'whatsapp'  => $whatsapp ? "https://wa.me/$whatsapp" : '',
    ]);
?>
<footer class="footer">

    <div class="footer__contenedor">
    <div class="footer__social">
        <h3 class="footer__title">Redes Sociales</h3>
        <?php if($redes): ?>
        <p class="footer__text">Síguenos para enterarte primero de las nuevas burgers y promociones.</p>
        <div class="footer__redes">
            <?php foreach($redes as $red => $url): ?>
                <a class="footer__red" href="<?php echo s($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo s(ucfirst($red)); ?>"><?php echo icono($red); ?></a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="footer__text">Muy pronto podrás encontrarnos en redes sociales.</p>
        <?php endif; ?>
    </div>

    <div class="footer__help">
        <h3 class="footer__title">¿Necesitas ayuda?</h3>
        <p class="footer__text">¿Tienes una duda con tu pedido o quieres hacer un pedido grande para un evento? Escríbenos y te respondemos lo antes posible.</p>
        <ul class="footer__datos">
            <li><?php echo icono('reloj'); ?> <?php echo s(config('HORARIO', 'Lunes a domingo, 12:00 m – 10:00 pm')); ?></li>
            <li><?php echo icono('telefono'); ?> <a href="tel:<?php echo s(preg_replace('/[^\d+]/', '', $telefono)); ?>"><?php echo s($telefono); ?></a></li>
            <?php if($whatsapp): ?>
            <li><?php echo icono('whatsapp'); ?> <a href="https://wa.me/<?php echo s($whatsapp); ?>" target="_blank" rel="noopener"><?php echo s(whatsappLegible()); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
    </div>

    <p class="footer__copy">Copyright &copy; - Juan Alvarado - Pandax-<?php echo date('Y'); ?></p>

</footer>

<?php if(!$admin) includeTemplate('cart'); ?>

<script src="/build/js/motion.js" defer></script>
<script src="/src/js/app.js" defer></script>
<?php if($admin) : ?>
<script src="/src/js/admin.js" defer></script>
<?php endif; ?>
</body>
</html>
