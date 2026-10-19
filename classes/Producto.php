<?php

namespace App;

use mysqli;

class Producto {
    // Conexion compartida por todos los productos
    protected static mysqli $db;

    // Carpeta donde se guardan las imagenes de los productos
    public const CARPETA_IMAGENES = __DIR__ . '/../img';
    public const CATEGORIAS = ['hamburguesa', 'combo', 'acompañamiento', 'bebida', 'postre'];
    public const TAMANO_MAXIMO = 2 * 1024 * 1024; // 2MB

    public $id ;
    public $nombre ;
    public $categoria ;
    public $precio ;
    public $imagen ;
    public $descripcion ;
    public $creado;
    public $comprador_id;

    public function __construct($arg = []){
        $this->id = $arg['id'] ?? null;
        $this->nombre = $arg['nombre'] ?? '';
        $this->categoria = $arg['categoria'] ?? '';
        $this->precio = $arg['precio'] ?? '';
        $this->imagen = $arg['imagen'] ?? '';
        $this->descripcion = $arg['descripcion'] ?? '';
        $this->creado = $arg['creado'] ?? date('Y-m-d');
        $this->comprador_id = $arg['comprador_id'] ?? 1;
    }

    public static function setDB(mysqli $db) : void {
        self::$db = $db;
    }

    /* ---------- Consultas (todas con sentencias preparadas) ---------- */

    /** @return Producto[] */
    public static function all(?int $limite = null) : array {
        if($limite){
            return self::consultar("SELECT * FROM productos ORDER BY id LIMIT ?", 'i', [$limite]);
        }
        return self::consultar("SELECT * FROM productos ORDER BY id");
    }

    public static function find(int $id) : ?Producto {
        $resultado = self::consultar("SELECT * FROM productos WHERE id = ? LIMIT 1", 'i', [$id]);
        return $resultado[0] ?? null;
    }

    // Datos para las tarjetas del panel de administracion
    public static function estadisticas() : array {
        $stmt = self::$db->prepare("SELECT COUNT(*) total, COUNT(DISTINCT categoria) categorias,
            COALESCE(AVG(precio), 0) promedio, COALESCE(MAX(precio), 0) maximo FROM productos");
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    protected static function consultar(string $sql, string $tipos = '', array $params = []) : array {
        $stmt = self::$db->prepare($sql);
        if($tipos){
            $stmt->bind_param($tipos, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        $productos = [];
        while($fila = $resultado->fetch_assoc()){
            $productos[] = new static($fila);
        }
        $stmt->close();
        return $productos;
    }

    /* ---------- Escritura ---------- */

    public function guardar() : bool {
        return $this->id ? $this->actualizar() : $this->crear();
    }

    protected function crear() : bool {
        $stmt = self::$db->prepare("INSERT INTO productos (nombre, categoria, precio, imagen, descripcion, creado, comprador_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdsssi', $this->nombre, $this->categoria, $this->precio,
            $this->imagen, $this->descripcion, $this->creado, $this->comprador_id);
        $ok = $stmt->execute();
        if($ok){
            $this->id = $stmt->insert_id;
        }
        return $ok;
    }

    protected function actualizar() : bool {
        $stmt = self::$db->prepare("UPDATE productos SET nombre = ?, categoria = ?, precio = ?, imagen = ?, descripcion = ?
            WHERE id = ? LIMIT 1");
        $stmt->bind_param('ssdssi', $this->nombre, $this->categoria, $this->precio,
            $this->imagen, $this->descripcion, $this->id);
        return $stmt->execute();
    }

    public function eliminar() : bool {
        $stmt = self::$db->prepare("DELETE FROM productos WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $this->id);
        $ok = $stmt->execute();
        if($ok){
            $this->borrarImagen();
        }
        return $ok;
    }

    /* ---------- Formulario ---------- */

    // Copia los datos del formulario al objeto
    public function sincronizar(array $args) : void {
        $this->nombre = trim($args['nombre'] ?? $this->nombre);
        $this->categoria = strtolower(trim($args['categoria'] ?? $this->categoria));
        $this->precio = $args['precio'] ?? $this->precio;
        $this->descripcion = trim($args['descripcion'] ?? $this->descripcion);
    }

    public function validar(?array $archivo = null) : array {
        $errores = [];

        if(!$this->nombre){
            $errores[] = "El nombre es obligatorio";
        }
        if(iconv_strlen($this->nombre, 'UTF-8') > 60){
            $errores[] = "El nombre no puede tener mas de 60 caracteres";
        }
        if(!$this->categoria){
            $errores[] = "La categoria es obligatoria";
        }
        if(!is_numeric($this->precio) || $this->precio <= 0){
            $errores[] = "El precio debe ser un numero mayor a 0";
        }
        if(iconv_strlen($this->descripcion, 'UTF-8') < 50){
            $errores[] = "La descripción debe tener al menos 50 caracteres";
        }

        $subioArchivo = $archivo && ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if(!$this->imagen && !$subioArchivo){
            $errores[] = "La imagen es obligatoria";
        }
        if($subioArchivo){
            if($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > self::TAMANO_MAXIMO){
                $errores[] = "La imagen es muy pesada (maximo 2MB)";
            }elseif(!self::extensionImagen($archivo['tmp_name'])){
                $errores[] = "El archivo debe ser una imagen PNG, JPG o WebP";
            }
        }

        return $errores;
    }

    /* ---------- Imagenes ---------- */

    // Detecta el tipo real del archivo (no confia en la extension que envia el navegador)
    protected static function extensionImagen(string $ruta) : ?string {
        $info = @getimagesize($ruta);
        return match($info[2] ?? null){
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_WEBP => 'webp',
            default => null,
        };
    }

    public function subirImagen(?array $archivo) : void {
        if(!$archivo || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return;

        $extension = self::extensionImagen($archivo['tmp_name']);
        if(!$extension) return;

        if(!is_dir(self::CARPETA_IMAGENES)){
            mkdir(self::CARPETA_IMAGENES);
        }

        $base = preg_replace('/[^a-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $this->categoria)) ?: 'producto';
        $nombre = $base . '_' . bin2hex(random_bytes(16)) . '.' . $extension;

        if(move_uploaded_file($archivo['tmp_name'], self::CARPETA_IMAGENES . '/' . $nombre)){
            $this->borrarImagen();
            $this->imagen = $nombre;
        }
    }

    // Borra la imagen actual y sus versiones (.png original y .webp optimizada)
    protected function borrarImagen() : void {
        if(!$this->imagen) return;
        $base = pathinfo(basename($this->imagen), PATHINFO_FILENAME);
        foreach(['png', 'jpg', 'webp'] as $ext){
            $ruta = self::CARPETA_IMAGENES . "/$base.$ext";
            if(is_file($ruta)){
                unlink($ruta);
            }
        }
    }

    // URL publica de la imagen, prefiriendo la version WebP si existe
    public function imagenUrl() : string {
        if(!$this->imagen) return '/build/img/Hamburguesas_0004_burger_09.webp';
        if(pathinfo($this->imagen, PATHINFO_EXTENSION)){
            return '/img/' . rawurlencode($this->imagen);
        }
        $webp = self::CARPETA_IMAGENES . "/{$this->imagen}.webp";
        return '/img/' . rawurlencode($this->imagen) . (is_file($webp) ? '.webp' : '.png');
    }

    // 21500 -> "21.5k$" (formato del diseño original)
    public function precioK() : string {
        return formatoK($this->precio);
    }
}
