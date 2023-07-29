<?php 

namespace App;

class Producto {
    public $id ;
    public $nombre ;
    public $categoria ;
    public $precio ;
    public $imagen ;
    public $descripcion ;
    public $creado;
    public $comprador_id;

    public function __construct($arg = []){
        $this->id = $arg['id'] ?? '';
        $this->nombre = $arg['nombre'] ?? '';
        $this->categoria = $arg['categoria'] ?? '';
        $this->precio = $arg['precio'] ?? '';
        $this->imagen = $arg['imagen'] ?? '';
        $this->descripcion = $arg['descripcion'] ?? '';
        $this->creado = $arg['creado'] ?? '';
        $this->comprador_id = $arg['comprador_id'] ?? 1;
    }
    
}