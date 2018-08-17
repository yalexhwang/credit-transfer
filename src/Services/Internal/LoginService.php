<?php
namespace App\Services\Internal;

class LoginService extends InternalService {

  private $cas;

  public function __construct($logger, $cas) {
    parent::__construct($logger);
    $this->cas = $cas;
  }

  public function casAuthenticate() {
    if (!$this->cas->isAuthenticated()) {
      $this->logger()->info('Authenticating via CAS');
      $this->cas->forceAuthentication();
    }
    $this->logger()->info('CAS Authenticated');
    return true;
  }

  public function getCasUsername() {
    return $this->cas->getUser();
  }

  public function casLogOut() {
    return $this->cas->logout();
  }

}
