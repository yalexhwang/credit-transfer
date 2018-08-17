<?php
namespace App\Middleware;

class HomeMiddleware extends Middleware {
  
  /**
  * Public common methods inherited from Middleware
  * @method object $this->logger()
  * @method object $this->session()
  */

  public function __construct($session, $logger) {
    parent::__construct($session, $logger);
  }

  public function __invoke($req, $res, $next) {
    $path = $req->getUri()->getPath();
    $in = $this->session()->checkUserSession();
    $this->logger()->info('Home middleware (/, /home, /login, /', ['user' => $in, 'path' => $path]);

    if ($path === '/' || $path === 'login') {
      if ($in) {
        $this->logger()->info('accessing landing or login page, but user in; redirect to /home...');
        return $res->withRedirect('./home');
      }
    }
    if ($path === 'home') {
      if (!$in) {
        $this->logger()->info('acessing home page, and user not in, redirect to ./');
        return $res->withRedirect('./');
      }
    }
    return $res = $next($req, $res);
  }

}
