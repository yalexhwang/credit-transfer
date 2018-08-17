<?php
namespace App\Services;

abstract class Service {

  protected $logger;

  public function __construct($logger) {
    $this->logger = $logger;
  }

  public function logger() {
    return $this->logger;
  }

}

?>
