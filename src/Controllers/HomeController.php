<?php
namespace App\Controllers;

class HomeController extends Controller {

  private $view;

  public function __construct($logger, $view) {
    parent::__construct($logger);
    $this->view = $view;
  }
  public function landing($req, $res, $args) {
    return $this->view->render($res, 'landing.html');
  }

  public function home($req, $res, $args) {
    return $this->view->render(
      $res, 
      'home.html', 
      array('user' => $this->session()->getUserSession()
    ));
  }

}
