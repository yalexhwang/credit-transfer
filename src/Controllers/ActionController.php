<?php
namespace App\Controllers;

use App\Services\ActionService;
use App\Services\AwardService;
use App\Services\Internal\MailService;
use App\Services\RequestService;
use App\Services\LogService;

class ActionController extends Controller {

  private $actionSvc;
  private $awardSvc;
  private $logSvc;
  private $mailSvc;
  private $reqSvc;

  public function __construct($logger, $settings) {
    parent::__construct($logger);
    $this->actionSvc = new ActionService($logger);
    $this->awardSvc = new AwardService($logger);
    $this->logSvc = new LogService($logger);
    $this->mailSvc = new MailService($logger, $settings);
    $this->reqSvc = new RequestService($logger, $settings);
  }

  public function get($req, $res, $args) {
    $this->logger()->info('ActionCtrl:get');
    return $res->withJson($this->actionSvc->getActions($this->getSelf()));
  }

  public function take($req, $res, $args) {
    $actionID = $args['actionID'];
    $user = $this->getSelf();
    $data = $req->getParsedBody();
    if (isset($req->getQueryParams()['requestID'])) {
      $requestID = $req->getQueryParams()['requestID'];
    } else {
      $requestID = null;
    }

    // Student submits
    if ($actionID == 80) {
      $req =$this->requestSubmitted($data['values']);
      return $res->withJson($req);
    }

    // Dept evaluates
    if ($actionID == 63 || $actionID == 64) {
      $evaluated = $this->deptEvaluated($data['values'], $requestID);
      $data['values'] = null;
      $request = $this->reqSvc->edit($data, $requestID, $user);
      return $res->withJson([
        'awards' => $evaluated,
        'request' => $request
      ]);
    }
    
    // Registrar evaluates
    if ($actionID == 32 || $actionID == 13) {
      $modified = $this->awardsModified($data['values'], $requestID);
      $data['values'] = null;
      $request = $this->reqSvc->edit($data, $requestID, $user);
      // $this->awardsFinalized($requestID);
      return $res->withJson([
        'awards' => $modified,
        'request' => $request
      ]);
    }

    // Registrar finalizes
    if ($actionID == 39) {
      $this->logger()->info('39', [$requestID]);
      $this->awardsFinalized($requestID);
    }

    $request = $this->reqSvc->edit($data, $requestID, $user);

    // Discard request
    if ($actionID == 3 || $actionID == 84) {
      $this->logger()->info('discard request');
      $this->reqSvc->delete($requestID);
      return $res->withJson(null);
    }
    
    return $res->withJson([
      'request' => $request
    ]);
  }

  private function requestSubmitted($data) {
    return $this->reqSvc->add($data);
  }

  private function deptEvaluated($data, $requestID) {
    return $this->awardSvc->add($data, $requestID);
  }
 
  private function awardsModified($data, $requestID) {
    return $this->awardSvc->modify($data, $requestID);
  }

  private function awardsFinalized($requestID) {
    $this->awardSvc->finalizeAsis($requestID);
  }

  public function log($req, $res, $args) {
    $logTemplateID = $args['logTemplateID'];
    $note = $req->getParsedBody()['note'];
    $items = $req->getParsedBody()['items'];
    $requestID = $req->getQueryParams()['requestID'];
    $stepID = $req->getQueryParams()['stepID'];
    $this->logger()->info('ActionCtrl:log', [$logTemplateID, $note, $items, $requestID, $stepID]);
    $this->logSvc->add($logTemplateID, $requestID, $stepID, $note, $items);
    return $res->withJson();
  }

  public function sendTurnMail($req, $res, $args) {
    $requestID = $req->getQueryParams()['requestID'];
    $this->logger()->info('ActionCtrl:sendTurnMail', [$req->getQueryParams(), $requestID]);
    $this->mailSvc->sendTurnUpdated($this->reqSvc->find('id', $requestID));
  }

  public function sendStatusMail($req, $res, $args) {
    $requestID = $req->getQueryParams()['requestID'];
    $this->logger()->info('ActionCtrl:sendStatusMail', [$req->getQueryParams(), $requestID]);
    $this->mailSvc->sendStatusUpdated($this->reqSvc->find('id', $requestID));
  }

  public function evaluate($req, $res, $args) {
    $requestID = $args['requestID'];
    $data = $req->getParsedBody();
    $this->logger()->info('ActionCtrl:evaluate', [$data, $requestID]);
    $this->awardSvc->updateEvaluation($requestID, $data);
    return $res->withJson();
  }

}
