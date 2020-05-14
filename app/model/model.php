<?php
require_once('./app/controller/controller.php');

class Model {

  public $controller;
  public $conection;

  public function __CONSTRUCT($_controller = null) {
    if (isset($_controller)) {
      $this->controller =  $_controller;
    } else {
      $this->controller = new Controller();
    }
    //try {
      $this->conection = new PDO("mysql:dbname=estudiando;host=localhost;charset=utf8;", 'root', '');
    //} catch(PDOExceptio $e) {
    //  $this->controller->renderError('500 no es posible la coneccion a la base de datos');
    //}
  }

  public function getUserById($id) {
    $sql = "SELECT * FROM usuario WHERE id = ?;";
    $consulta = $this->conection->prepare($sql);
    $consulta->execute([$id]);
    if ($consulta->rowCount() == 1) {
      return $consulta->fetchAll()[0];
    } else {
      return false;
    }
  }

  public function checkUserLogin($log_user) {
    return ((isset($log_user['user'])) && ($log_user['user'] != "")) &&
           ((isset($log_user['password'])) && ($log_user['password'] != ""));
  }

  public function existUser($log_user) {
    if ($this->checkUserLogin($log_user)) {
      $sql = "SELECT u.id FROM usuario u WHERE usuario = ? AND clave = ?;";
      $consulta = $this->conection->prepare($sql);
      $consulta->execute([$log_user['user'], $log_user['password']]);
      if ($consulta->rowCount() == 1) {
        return $consulta->fetchAll()[0]['id'];
      }
    } else {
      return false;
    }
  }

  public function getProductos() {
    $sql = "SELECT p.*, c.nombre AS categoria
            FROM producto p
            INNER JOIN categoria c ON (p.categoria_id = c.id)
            ORDER BY p.id;";
    $consulta = $this->conection->prepare($sql);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  public function getProdById($id) {
    $sql = "SELECT p.*, c.nombre AS categoria
            FROM producto p
            INNER JOIN categoria c ON (p.categoria_id = c.id)
            WHERE p.id = ?;";
    $consulta = $this->conection->prepare($sql);
    $consulta->execute([$id]);
    if ($consulta->rowCount() == 1) {
      return $consulta->fetchAll()[0];
    } else {
      return false;
    }
  }

  public function getCategorias() {
    $sql = "SELECT *
            FROM categoria c
            ORDER BY c.id;";
    $consulta = $this->conection->prepare($sql);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  public function getCategoriaIdByName($categoria) {
    $sql = "SELECT id FROM categoria WHERE nombre = ?;";
    $consulta = $this->conection->prepare($sql);
    $consulta->execute([filter_var($categoria, FILTER_SANITIZE_STRING)]);
    if ($consulta->rowCount() == 1) {
      return $consulta->fetchAll()[0][0];
    } else {
      return false;
    }
  }

  public function checkProductForm($datos) {
    return ((isset($datos['nombre']) && ($datos['nombre'] != '')) &&
            (isset($datos['precio']) && (is_numeric($datos['precio']) && ($datos['precio'] > 0)) &&
            (isset($datos['categoria'])) && ($datos['nombre'] != '')) &&
            (isset($datos['stock_minimo']) && (is_numeric($datos['stock_minimo'])) && ($datos['stock_minimo'] > 0)));
  }

  public function createProduct($datos) {
    if ($this->checkProductForm($datos)) {
      if ($categoria_id = $this->getCategoriaIdByName($datos['categoria'])) {
        $sql = "INSERT INTO producto (nombre, precio, categoria_id, stock_minimo)
                VALUES (?,?,?,?);";
        $consulta = $this->conection->prepare($sql);
        $consulta->execute([filter_var($datos['nombre'], FILTER_SANITIZE_STRING),
                                       $datos['precio'], $categoria_id, $datos['stock_minimo']]);
        if ($consulta->rowCount() == 1) {
          return true;
        }
      }
    }
    return false;
  }

  public function editProduct($id, $datos) {
    if ($this->checkProductForm($datos)) {
      if ($categoria_id = $this->getCategoriaIdByName($datos['categoria'])) {
        $sql = "UPDATE producto
                SET nombre = ?,
                    precio = ?,
                    categoria_id = ?,
                    stock_minimo = ?
                WHERE id = ?;";
        $consulta = $this->conection->prepare($sql);
        $consulta->execute([filter_var($datos['nombre'], FILTER_SANITIZE_STRING),
                                       $datos['precio'], $categoria_id, $datos['stock_minimo'], $id]);
        if ($consulta->rowCount() == 1) {
          return true;
        }
      }
    }
    return false;
  }

  public function deleteProduct($id) {
    if ($this->getProdById($id) != Null) {
      $sql = "DELETE FROM producto
              WHERE id = ?;";
      $consulta = $this->conection->prepare($sql);
      $consulta->execute([$id]);
      return true;
    }
    return false;
  }
}
?>
