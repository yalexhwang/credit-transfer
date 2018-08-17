<?php
namespace App\Handlers;

use \Slim\Middleware\Session;
use \SlimSession\Helper;

class SessionHandler {

  private $session;
  private $sessionName;

  public function __construct($name) {
    $this->session = new Helper();
    $this->sessionName = $name;
  }

  public function checkUserSession() {
    if (!$this->session->exists($this->sessionName)) {
      return false;
    }
    return true;
  }

  public function getUserSession() {
    return $this->session->get($this->sessionName);
  }

  public function getSessionID() {
    return $this->session->id();
  }

  public function setUserSession($user) {
    $this->session->set($this->sessionName, [
      'username' => $user['username'],
      'id' => $user['id'], 
      'roleID' => $user['roleID'],
      'role' => $this->setRole($user['roleID'])
    ]);
  }

  private function setRole($roleID) {
    if ($roleID === 2) {
      return [
        'registrar' => true,
        'dept' => false, 
        'student' => false
      ];
    }
    if ($roleID === 6) {
      return [
        'registrar' => false,
        'dept' => false, 
        'student' => true
      ];
    }
    return [
      'registrar' => false,
      'dept' => true,
      'student' => false
    ];
  }

  public function removeUserSession() {
    $this->session->delete($this->sessionName);
    $this->session->destroy();
  }

}
