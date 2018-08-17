<?php
namespace App\Services\Internal;

abstract class InternalService {

  private $logger;

  public function __construct($logger) {
    $this->logger = $logger;
  }

  public function logger() {
    return $this->logger;
  }

}

?>
