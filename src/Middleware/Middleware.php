<?php 
namespace App\Middleware;

abstract class Middleware {
	
	protected $logger;
	protected $session;
	
	public function __construct($session, $logger) {
		$this->logger = $logger;
		$this->session = $session;	
	}

	public function logger() {
		return $this->logger;
	}

	public function session() {
		return $this->session;
	}

}

 ?>