<?php

namespace App;

use mysqli;

class Usuario {
    protected static mysqli $db;

    public $id;
    public $email;
    public $password;

    public function __construct($arg = []){
        $this->id = $arg['id'] ?? null;
        $this->email = $arg['email'] ?? '';
        $this->password = $arg['password'] ?? '';
    }

    public static function setDB(mysqli $db) : void {
        self::$db = $db;
    }

    public static function findByEmail(string $email) : ?Usuario {
        $stmt = self::$db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        return $fila ? new self($fila) : null;
    }

    public function comprobarPassword(string $password) : bool {
        return password_verify($password, $this->password);
    }
}
