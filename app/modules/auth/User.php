<?php

class User extends Model
{
  public function findByEmail($email)
  {
    // Si NO tiene columna activo, quite el "AND activo = 1"
    // Yo lo dejo opcional: si existe, úsela; si no, no.
    // Para no depender de schema, lo dejo SIN activo para que no truene.

    $stmt = $this->pdo->prepare(
      "SELECT id, nombre, email, rol, password_hash, activo
       FROM usuarios
       WHERE email = ?
       LIMIT 1"
    );

    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
