<?php
    require '../includes/app.php';

    use App\Producto;

    requireAuth();

//Eliminar productos
if($_SERVER['REQUEST_METHOD'] === 'POST' ){
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    if($id && csrfValido()){
      $producto = Producto::find($id);
      if($producto && $producto->eliminar()){
        redirigir('/admin?status=3');
      }
    }
    redirigir('/admin?status=error');
}

$productos = Producto::all();
$stats = Producto::estadisticas();
$categorias = array_unique(array_map(fn($p) => $p->categoria, $productos));
sort($categorias);

$mensajes = [
    '1' => ['success', 'Producto creado correctamente'],
    '2' => ['success', 'Producto actualizado correctamente'],
    '3' => ['success', 'Producto eliminado correctamente'],
    'error' => ['warning', 'No se pudo completar la acción'],
];
$mensaje = $mensajes[$_GET['status'] ?? ''] ?? null;

    includeTemplate("header", false, ['admin' => true, 'titulo' => 'Panel | Burger Store']);

?>

<main class="admin">
    <div class="admin__encabezado">
        <div>
            <p class="admin__saludo">Hola, <?php echo s($_SESSION['usuario'] ?? 'admin'); ?> 👋</p>
            <h1 class="admin__titulo">Administrador de BurgerStore</h1>
        </div>
        <div class="admin__acciones">
            <a class="boton boton--yellow admin__boton" href="/" target="_blank"><?php echo icono('tienda'); ?> Ver tienda</a>
            <a class="boton boton--green admin__boton" href="/admin/productos/crear.php"><?php echo icono('mas'); ?> Nuevo producto</a>
        </div>
    </div>

    <?php if($mensaje): ?>
      <p class="alert <?php echo $mensaje[0]; ?>" data-autocerrar><?php echo s($mensaje[1]); ?></p>
    <?php endif; ?>

    <section class="admin__stats">
        <div class="stat">
            <span class="stat__valor"><?php echo (int) $stats['total']; ?></span>
            <span class="stat__nombre">Productos</span>
        </div>
        <div class="stat">
            <span class="stat__valor"><?php echo (int) $stats['categorias']; ?></span>
            <span class="stat__nombre">Categorías</span>
        </div>
        <div class="stat">
            <span class="stat__valor"><?php echo formatoK($stats['promedio']); ?></span>
            <span class="stat__nombre">Precio promedio</span>
        </div>
        <div class="stat">
            <span class="stat__valor"><?php echo formatoK($stats['maximo']); ?></span>
            <span class="stat__nombre">Más caro</span>
        </div>
    </section>

    <section class="admin__panel">
        <div class="admin__toolbar">
            <label class="admin__buscar">
                <?php echo icono('buscar'); ?>
                <input type="search" id="buscar" placeholder="Buscar producto..." aria-label="Buscar producto">
            </label>
            <select class="admin__filtro" id="filtro-categoria" aria-label="Filtrar por categoría">
                <option value="">Todas las categorías</option>
                <?php foreach($categorias as $categoria): ?>
                    <option value="<?php echo s($categoria); ?>"><?php echo s(ucfirst($categoria)); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="admin__contador" id="contador"><?php echo count($productos); ?> productos</span>
        </div>

      <div class="table__wrapper">
      <table class="table">
        <thead class="table__head">
          <tr>
            <th class="table__name--head">Producto</th>
            <th class="table__name--head">Categoría</th>
            <th class="table__name--head">Precio</th>
            <th class="table__name--head">Creado</th>
            <th class="table__name--head">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach($productos as $producto) :?>
          <tr class="table__producto" data-nombre="<?php echo s(strtolower($producto->nombre . ' ' . $producto->descripcion)); ?>" data-categoria="<?php echo s($producto->categoria); ?>">
            <td class="table__name table__info">
              <img class="table__img" src="<?php echo s($producto->imagenUrl()); ?>" alt="<?php echo s($producto->nombre); ?>" width="80" height="64" loading="lazy">
              <div>
                <strong class="table__nombre"><?php echo s($producto->nombre); ?></strong>
                <span class="table__id">#<?php echo s($producto->id); ?></span>
              </div>
            </td>
            <td class="table__name" data-label="Categoría"><span class="pill"><?php echo s($producto->categoria); ?></span></td>
            <td class="table__name table__precio" data-label="Precio"><?php echo $producto->precioK(); ?></td>
            <td class="table__name table__fecha" data-label="Creado"><?php echo s($producto->creado ? date('d/m/Y', strtotime($producto->creado)) : '-'); ?></td>
            <td class="table__btn">
              <a class="boton--green boton boton--icono" href="/admin/productos/actualizar.php?id=<?php echo s($producto->id); ?>" title="Editar"><?php echo icono('editar'); ?><span>Editar</span></a>
              <button class="boton--red boton boton--icono" type="button" data-eliminar="<?php echo s($producto->id); ?>" data-nombre="<?php echo s($producto->nombre); ?>" title="Eliminar"><?php echo icono('basura'); ?><span>Borrar</span></button>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      </div>

      <p class="admin__vacio" id="sin-resultados" <?php echo $productos ? 'hidden' : ''; ?>>
        <?php echo $productos ? 'Ningún producto coincide con la búsqueda' : 'Aún no hay productos. ¡Crea el primero!'; ?>
      </p>
    </section>
  </main>

<!-- Confirmacion antes de borrar -->
<dialog class="confirmar" id="confirmar">
    <form method="POST" class="confirmar__contenido">
        <?php echo csrfInput(); ?>
        <input type="hidden" name="id" id="confirmar-id">
        <?php echo icono('basura', 'confirmar__icono'); ?>
        <h2 class="confirmar__titulo">¿Eliminar producto?</h2>
        <p class="confirmar__texto">Vas a eliminar <strong id="confirmar-nombre"></strong>. Esta acción no se puede deshacer.</p>
        <div class="confirmar__botones">
            <button class="boton boton--yellow" type="button" id="confirmar-cancelar">Cancelar</button>
            <button class="boton boton--red" type="submit">Sí, eliminar</button>
        </div>
    </form>
</dialog>

<?php
  includeTemplate("footer", false, ['admin' => true]);
?>
