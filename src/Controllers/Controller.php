<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Handlers\SessionHandler;

abstract class Controller {

  private $logger;  
  private $session;
  private $userSvc;

  public function __construct($logger) {
    $this->logger = $logger;
    $this->session = new SessionHandler('user');
    $this->userSvc = new UserService($logger);
  }

  protected function logger() {
  	return $this->logger;
  }

  protected function session() {
    return $this->session;
  }

  protected function userSvc() {
    return $this->userSvc;
  }

  protected function getSelf() {
    $id = $this->session->getUserSession()['id'];
    // $id = 1;
    // $id = 12; 
    // $id = 15;
    $this->logger->info('controller,getself', [$this->session->getUserSession()]);
    return $this->userSvc->find('id', $id);

  }

}

?>
