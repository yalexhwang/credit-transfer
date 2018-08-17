<?php
namespace App\Middleware;

class ApiAuthMiddleware extends Middleware {

  /**
  * Public common methods inherited from Middleware
  * @method object $this->logger()
  * @method object $this->session()
  */

  private $cas;
  private $currentUserPath;
  private $cookieName;

  public function __construct($session, $logger, $cas, $settings) {
    parent::__construct($session, $logger);
    $this->cas = $cas;
    $this->currentUserPath = $settings['ApiAuthMiddleware']['path_current_user'];
    $this->cookieName = $settings['ApiAuthMiddleware']['cookie_name'];
  }

  public function __invoke($req, $res, $next) {
    $this->logger()->info('ApiAuthMiddleware1', [$req->getHeaders()]);
    $this->logger()->info('ApiAuthMiddleware2', [$req->getHeader('Cookie')]);
    $this->logger()->info('ApiAuthMiddleware3', [$req->getHeaderLine('Cookie')]);

    $cookies = $this->parseCookies($req->getHeaderLine('Cookie'));
    $path = $req->getUri()->getPath();
    $user = $this->session()->getUserSession();
    $this->logger()->info('ApiAuthMiddleware', array('cookies' => $cookies, 'path' => $path, 'user' => $user));
    
    if (!$this->session()->checkUserSession()) {
      $this->logger()->error('User session not found, access denied');
      return $res->withStatus(401);
    }

    if ($path === $this->currentUserPath) {
      $this->logger()->info('API accessing current user info');
      if (!$this->verifyCASAuthentication($this->session()->getUserSession()['username'])) {
        $this->logger()->warn('Confirming current user failed, access denied');
        return $res->withStatus(401);   
      }
    }
    
    $cookie = $cookies[$this->cookieName];
    $this->logger()->info('session cookie retrieved', array('cookie' => $cookie, 'sessionID' => $this->session()->getSessionID()));

    if (!$this->confirmToken($cookie)) {
      $this->logger()->error('Token and session ID do not match, access denied');
      return $res->withStatus(401);
    }

    return $res = $next($req, $res);
 }

  private function parseCookies($str) {
    $cookies = explode('; ', $str);
    $arr = [];
    foreach ($cookies as $cookie) {
      $delimiterIndex = strpos($cookie, '=');
      $key = substr($cookie, 0, $delimiterIndex);
      $value = substr($cookie, $delimiterIndex + 1);
      $arr[$key] = $value;
    }
    return $arr;
  }

  private function verifyCASAuthentication($username) {
    if (!$this->$this->cas->checkAuthentication()) {
      return false;
    }
    return $this->cas->getUser() == $username;
  }

  private function confirmToken($token) {
    return $this->session()->getSessionID() == $token;
  }

}
