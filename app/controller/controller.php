<?php
require_once('./app/model/model.php');
require_once('./app/view/view.php');

class Controller {

  public $model, $view;

  public function __CONSTRUCT() {
    $this->model = new Model($this);
    $this->view = new View($this);
    session_start();
  }

  public function renderIndex($_param = null) {
    $this->view->renderIndex($_param);
  }

  public function renderError($_param = null) {
    $this->view->renderError(array('error' => $_param));
  }

  public function renderLogin($_param = null) {
    $this->view->renderLogin($_param);
  }

  public function renderHome($_param = null) {
    $_param['user'] = $this->model->getUserById($_SESSION['user_id']);
    $_param['productos'] = $this->model->getProductos();
    $this->view->renderHome($_param);
  }

  public function renderCreateProd($_param = null) {
    $_param['categorias'] = $this->model->getCategorias();
    $this->view->renderCreateProd($_param);
  }

  public function renderViewProd($_param = null) {
    if ($_param['producto'] = $this->model->getProdById($_GET['producto'])) {
      $this->view->renderViewProd($_param);
    } else {
      $this->renderHome(array('error' => 'No existe el producto con numero '.$_GET['producto']));
    }
  }

  public function renderEditProd($_param = null) {
    if ($_param['producto'] = $this->model->getProdById($_GET['producto'])) {
      $_param['categorias'] = $this->model->getCategorias();
      $this->view->renderEditProd($_param);
    } else {
      $this->renderHome(array('error' => 'No existe el producto con numero '.$_GET['producto']));
    }
  }

  public function performAction($action) {
    switch ($action) {
      case 'index':
        $this->renderIndex();
        break;
      case 'home':
      case 'login':
        if (isset($_SESSION['user_id'])) {
          $this->renderHome();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
          $this->renderLogin();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $this->login();
        }
        break;
      case 'logout':
        if (!isset($_SESSION['user_id'])) {
          $this->renderIndex();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
          $this->logout();
          $this->renderIndex();
        }
        break;
      case 'prod_create':
        if (!isset($_SESSION['user_id'])) {
          $this->renderLogin(array('error' => 'Necesita inciar sesion para realizar esta accion'));
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
          $this->renderCreateProd();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $this->createProduct(array('nombre' => $_POST['nombre'],
                                     'precio' => $_POST['precio'],
                                     'categoria' => $_POST['categoria'],
                                     'stock_minimo' => $_POST['stock_minimo']
                                    ));
        }
        break;
      case 'prod_view':
        if (!isset($_SESSION['user_id'])) {
          $this->renderLogin(array('error' => 'Necesita inciar sesion para realizar esta accion'));
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
          $this->renderViewProd();
        }
        break;
      case 'prod_edit':
        if (!isset($_SESSION['user_id'])) {
          $this->renderLogin(array('error' => 'Necesita inciar sesion para realizar esta accion'));
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
          $this->renderEditProd();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $this->editProduct($_GET['producto'],
                             array('nombre' => $_POST['nombre'],
                                   'precio' => $_POST['precio'],
                                   'categoria' => $_POST['categoria'],
                                   'stock_minimo' => $_POST['stock_minimo']
                                  ));
        }
        break;
      case 'prod_delete':
        if (!isset($_SESSION['user_id'])) {
          $this->renderLogin(array('error' => 'Necesita inciar sesion para realizar esta accion'));
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
          $this->deleteProduct($_GET['producto']);
        }
        break;
      default:
        $this->renderError('404, Pagina no encontrada.');
        break;
    }
  }

  public function login() {
    $user_id = $this->model->existUser(array('user' => $_POST['user'],
                                             'password' => $_POST['password']));
    if ($user_id) {
      $_SESSION['user_id'] = $user_id;
      header('Location: index.php?action=home');
    } else {
      $this->renderLogin(array('error' => 'Credenciales incorrectas'));
    }
  }

  public function logout() {
    unset($_SESSION['user_id']);
    session_destroy();
  }

  public function createProduct($datos) {
    if ($this->model->createProduct($datos)) {
      header('Location: index.php?action=home');
    } else {
      $this->renderCreateProd(array('error' => 'Datos mal ingresados'));
    }
  }

  public function editProduct($id, $datos) {
    if ($this->model->editProduct($id, $datos)) {
      header('Location: index.php?action=home');
    } else {
      $this->renderEditProd(array('error' => 'Datos mal ingresados'));
    }
  }

  public function deleteProduct($id) {
    if ($this->model->deleteProduct($id)) {
      header('Location: index.php?action=home');
    } else {
      $this->renderHome(array('error' => 'No se pudo realizar la acción'));
    }
  }

}
?>
