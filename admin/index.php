<?php 
    require '../includes/app.php';
    $auth = isAuth();

    if(!$auth){
        header('Location: /');
    }
$db= conectarDB();

$query = "SELECT * FROM productos;";

$resultadoQuery = mysqli_query($db, $query);

$status = $_GET['status'] ?? null;

//ELiminar productos
if($_SERVER['REQUEST_METHOD'] === 'POST' ){
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if($id){
      //eliminar imagen
      $query = "SELECT imagen FROM productos WHERE id = {$id}";
      $resultado = mysqli_query($db, $query);

      $producto = mysqli_fetch_assoc($resultado);
      unlink('../img/'.$producto["imagen"].".png");
      //eliminar el producto
      $query = "DELETE FROM productos WHERE id = {$id};";
      $resultado = mysqli_query($db, $query);

      if($resultado){
        header('Location: /admin?status=3');
      }
    }
}

    includeTemplate("header");
  
?>

<main class="main--admin">
    <h1 class="main--admin__title">Administrador de BurgerStore</h1>

    <?php if($status === "1"): ?>
      <p class="alert success">producto creado correctamente</p>
    <?php endif; ?>

    <?php if($status === "2"): ?>
      <p class="alert success">producto actualizado correctamente</p>
    <?php endif; ?>
    
    <?php if($status === "3"): ?>
      <p class="alert success">producto eliminado correctamente</p>
    <?php endif; ?>
    <a class="boton--green boton main--admin__btn" href="/admin/productos/crear.php">Crear</a>


      <table class="table">
        <thead class="table__head">
          <tr class="table__">
            <th class="table__name--head">ID</th>
            <th class="table__name--head">Nombre</th>
            <th class="table__name--head">Imagen</th>
            <th class="table__name--head">Precio</th>
            <th class="table__name--head">Acciones</th>
          </tr>
        </thead>

        <tbody class="table__">
          <?php while($producto = mysqli_fetch_assoc($resultadoQuery)) :?>
          <tr class="table__producto">
            <td class="table__name"><?php echo $producto['id']; ?></td>
            <td class="table__name"><?php echo $producto['nombre']; ?></td>
            <td class="table__name"><img class="table__img" src="/img/<?php echo $producto['imagen']?>.png" alt="<?php echo $producto['nombre'] ." ".$producto['categoria']?>"></td>
            <td class="table__name"><?php echo $producto['precio']; ?>$</td>
            <td class="table__btn">
            <a class="boton--green boton" href="/admin/productos/actualizar.php?id=<?php echo $producto['id']; ?>">Actualizar</a>
            <form method="POST" class="boton--red boton">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <input type="submit" class="" value="Borrar">
            </form>
            </td>
          </tr>
          <?php  endwhile;?>
        </tbody>
      </table>

  </main>

<?php 
  mysqli_close($db);
  includeTemplate("footer");
?>

