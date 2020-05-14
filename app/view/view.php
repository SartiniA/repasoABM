<?php
require_once('./app/controller/controller.php');
require_once('./vendor/autoload.php');

class View {

  public $controller;
  public $tiwg, $loader;

  public function __CONSTRUCT($_controller = null) {
    if (isset($_controller)) {
      $this->controller =  $_controller;
    } else {
      $this->controller = new Controller();
    }
    $this->loader = new Twig_Loader_Filesystem('./app/templates');
    $this->twig = new Twig_Environment($this->loader, []);
  }

  public function renderIndex($_param = null) {
    echo $this->twig->render('index.html', array('param' => $_param));
  }

  public function renderError($_param = null) {
    echo $this->twig->render('error.html', array('param' => $_param));
  }

  public function renderLogin($_param = null) {
    echo $this->twig->render('login.html', array('param' => $_param));
  }

  public function renderHome($_param = null) {
    echo $this->twig->render('home.html', array('param' => $_param));
  }

  public function renderCreateProd($_param = null) {
    echo $this->twig->render('create_prod.html', array('param' => $_param));
  }

  public function renderViewProd($_param = null) {
    echo $this->twig->render('view_prod.html', array('param' => $_param));
  }

  public function renderEditProd($_param = null) {
    echo $this->twig->render('edit_prod.html', array('param' => $_param));
  }
}
?>
