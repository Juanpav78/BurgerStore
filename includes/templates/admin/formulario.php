<?php
    use App\Producto;

    // Variables que recibe: $producto, $errores, $titulo, $boton
    $categorias = Producto::CATEGORIAS;
    if($producto->categoria && !in_array($producto->categoria, $categorias)){
        $categorias[] = $producto->categoria;
    }
?>
<main class="admin">
    <a class="admin__volver" href="/admin"><?php echo icono('volver'); ?> Volver al panel</a>
    <h1 class="admin__titulo"><?php echo s($titulo); ?></h1>

    <?php foreach($errores as $error) : ?>
        <div class="alert warning"><?php echo s($error); ?></div>
    <?php endforeach; ?>

    <div class="admin__editor">
    <form class="formulario formulario--admin" method="post" enctype="multipart/form-data" id="form-producto" novalidate>
        <?php echo csrfInput(); ?>
        <fieldset class="formulario__fs" >
            <legend class="formulario__legend">Información del producto</legend>

            <label class="formulario__label" for="nombre">Nombre</label>
            <input value="<?php echo s($producto->nombre); ?>" class="formulario__input" type="text" id="nombre" name="nombre" maxlength="60" placeholder="Ej: Hot Beacon" required>

            <div class="formulario__fila">
                <div class="formulario__campo">
                    <label class="formulario__label" for="categoria">Categoría</label>
                    <select class="formulario__input" id="categoria" name="categoria" required>
                        <option value="" disabled <?php echo $producto->categoria ? '' : 'selected'; ?>>Selecciona...</option>
                        <?php foreach($categorias as $categoria): ?>
                            <option value="<?php echo s($categoria); ?>" <?php echo $producto->categoria === $categoria ? 'selected' : ''; ?>><?php echo s(ucfirst($categoria)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="formulario__campo">
                    <label class="formulario__label" for="precio">Precio (COP)</label>
                    <input value="<?php echo s($producto->precio); ?>" class="formulario__input" type="number" id="precio" name="precio" min="100" step="100" placeholder="Ej: 25000" required>
                </div>
            </div>

            <span class="formulario__label">Imagen</span>
            <label class="dropzone" for="imagen" id="dropzone">
                <img class="dropzone__preview" id="imagen-preview" src="<?php echo $producto->imagen ? s($producto->imagenUrl()) : ''; ?>" alt="" <?php echo $producto->imagen ? '' : 'hidden'; ?>>
                <span class="dropzone__texto">
                    <?php echo icono('imagen'); ?>
                    <strong><?php echo $producto->imagen ? 'Cambiar imagen' : 'Sube una imagen'; ?></strong>
                    <small>Arrastra o haz clic · PNG, JPG o WebP · máx 2MB (fondo transparente recomendado)</small>
                </span>
                <input type="file" id="imagen" name="imagen" accept="image/png, image/jpeg, image/webp" hidden>
            </label>

            <label class="formulario__label" for="descripcion">Descripción</label>
            <textarea class="formulario__input" id="descripcion" name="descripcion" rows="4" placeholder="Ingredientes y detalles del producto..." required><?php echo s($producto->descripcion); ?></textarea>
            <small class="formulario__ayuda" id="contador-descripcion">Mínimo 50 caracteres</small>
        </fieldset>

        <button class="formulario__btn boton boton--green" type="submit"><?php echo icono('check'); ?> <?php echo s($boton); ?></button>
    </form>

    <aside class="admin__preview">
        <p class="admin__preview__label">Vista previa en el menú</p>
        <div class="card--burger card--burger--preview">
            <div class="card--burger__media">
                <img class="card--burger__img" id="preview-img" src="<?php echo s($producto->imagenUrl()); ?>" alt="">
            </div>
            <h3 class="card--burger__title" id="preview-nombre"><?php echo s($producto->nombre ?: 'Nombre del producto'); ?></h3>
            <p class="card--burger__description" id="preview-descripcion"><?php echo s($producto->descripcion ?: 'Aquí aparecerá la descripción del producto.'); ?></p>
            <div class="card--burger__footer">
                <span class="card--burger__price" id="preview-precio"><?php echo is_numeric($producto->precio) ? $producto->precioK() : '0k$'; ?></span>
                <span class="card--burger__add"><?php echo icono('mas'); ?></span>
            </div>
        </div>
    </aside>
    </div>
</main>
