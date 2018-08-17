<?php
namespace App\Controllers;

use App\Services\Internal\GtedService;
use App\Services\Internal\LoginService;

class LoginController extends Controller {

  private $gtedSvc;
  private $loginSvc;

  public function __construct($logger, $cas, $settings) {
    parent::__construct($logger);
    $this->gtedSvc = new GtedService($logger, $settings);
    $this->loginSvc = new LoginService($logger, $cas);
  }

  public function login($req, $res, $args) {
    if ($this->loginSvc->casAuthenticate()) {
      $username = $this->loginSvc->getCasUsername();
      $user = $this->userSvc()->find('username', $username);
      $this->logger()->info('loginCtrl:login - user found?', [$user]);
      if (!$this->gtedSvc->connect()) {
        $this->logger()->error('LDAP connection failed');
        if (is_null($user)) {
          return $res->withStatus(503);
        }
        $newUser = $user;
      } else {
        $userData = $this->getGtedUserData($username);
        $this->gtedSvc->disconnect();
        if (is_null($user)) {
          $newUser = $this->userSvc()->add($userData);
        } else {
          unset($userData['roleID']);
          $newUser = $this->userSvc()->edit($userData, $user->id);
        }
      }
      // create user session
      $this->session()->setUserSession($newUser);
      return $res->withRedirect('./home');
    }
  }

  private function getGtedUserData($username) {
    $result = $this->gtedSvc->search($username);
    return $this->gtedSvc->extractUserData($result);
  }

  public function logout($req, $res, $args) {
    if ($this->session()->checkUserSession()) {
      $this->session()->removeUserSession();
    }
    $this->loginSvc->casLogOut();
  }

}
