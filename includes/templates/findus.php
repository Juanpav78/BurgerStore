<?php
    $telefono = config('TELEFONO', '605 1444-88');
    $whatsapp = numeroWhatsapp();

    $restaurantes = [
        [
            'nombre' => 'Rest. CC Boyacá',
            'direccion' => 'Cr. 155 #22-55, local 104',
            'descripcion' => 'Nuestro primer local, dentro del centro comercial. Zona de juegos para niños y terraza al aire libre. Abierto todos los días de 12:00 m a 10:00 pm.',
            'imagen' => 'restaurant_2',
        ],
        [
            'nombre' => 'Rest. Parque 93',
            'direccion' => 'Cl. 93 #13-40',
            'descripcion' => 'Ambiente urbano con música en vivo los viernes y sábados. Ideal para compartir con amigos. Abierto de lunes a sábado hasta la medianoche.',
            'imagen' => 'restaurant_3',
        ],
        [
            'nombre' => 'Rest. Calle 80',
            'direccion' => 'Av. Cl. 80 #69-12',
            'descripcion' => 'Nuestro local más grande, con servicio para llevar y autoservicio. Perfecto para recoger tu pedido de camino a casa. Abierto todos los días.',
            'imagen' => 'restaurant_4',
        ],
    ];
?>
<div class="findUs">
        <div class="findUs__contenedor">
            <section class="domicilio">
                <h2 class="domicilio__title">Domicilio</h2>
                <img class="domicilio__img" src="/build/img/delivery.webp" alt="moto domicilio envios" width="512" height="512" loading="lazy" decoding="async">
                <a class="domicilio__contacto" href="tel:<?php echo s(preg_replace('/[^\d+]/', '', $telefono)); ?>">
                    <p class="domicilio__contacto__txt">Llamanos al:</p>
                    <p class="domicilio__contacto__number"><?php echo s($telefono); ?></p>
                </a>
                <?php if($whatsapp): ?>
                <a class="domicilio__contacto" href="https://wa.me/<?php echo s($whatsapp); ?>" target="_blank" rel="noopener">
                    <p class="domicilio__contacto__txt">Escribenos al:</p>
                    <p class="domicilio__contacto__number"><?php echo s(whatsappLegible()); ?></p>
                </a>
                <?php endif; ?>
            </section>

            <section class="restaurantes">
                <h2 class="restaurantes__title">Restaurantes</h2>

                <?php foreach($restaurantes as $restaurante): ?>
                <div class="card--restaurante">
                    <div class="card--restaurante__img">
                        <img src="/build/img/<?php echo s($restaurante['imagen']); ?>.webp" alt="<?php echo s($restaurante['nombre']); ?>" width="550" height="411" loading="lazy" decoding="async">
                    </div>

                    <div class="card--restaurante__info">
                    <h3 class="card--restaurante__title"><?php echo s($restaurante['nombre']); ?></h3>
                    <span class="card--restaurante__direction"><?php echo s($restaurante['direccion']); ?></span>
                    <p class="card--restaurante__description"><?php echo s($restaurante['descripcion']); ?></p>
                    </div>

                </div>
                <?php endforeach; ?>
            </section>
        </div>
    </div>
