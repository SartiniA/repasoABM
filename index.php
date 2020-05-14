<?php
require_once('./app/controller/controller.php');

$controller = new Controller();

if (!isset($_GET['action'])) {
  $controller->renderIndex();
} else {
  $controller->performAction($_GET['action']);
}

?>
