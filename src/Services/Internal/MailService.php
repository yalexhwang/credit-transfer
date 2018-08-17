<?php
namespace App\Services\Internal;

use PHPMailer\PHPMailer\PHPMailer;
use App\Services\UserService;

class MailService extends InternalService {

  public $mailer;
  private $userSvc;

  private $fromEmail;
  private $subjects;
  private $templates;

  public function __construct($logger, $settings) {
    parent::__construct($logger);
    $this->mailer = new PHPMailer;
    $this->userSvc = new UserService($logger);
    $this->fromEmail = $settings['mailer']['email'];
    $this->subjects = $settings['mailer']['subjects'];
    $this->templates = $this->getTemplates($settings['mailer']['templates']);

    $this->mailer->SetFrom($this->fromEmail);
  }

  private function getTemplates($paths) {
    $templates = [];
    foreach ($paths as $key => $value) {
      $templates[$key] = file_get_contents($value);
    }
    return $templates;
  }

  public function sendTurnUpdated($req) {
    $this->logger()->info('mailSvc:sendTurnUpdated', [$req]);
    if ($req->turn === 'deptStaffID') {
      $user = $this->userSvc->find('id', $req->deptStaffID);
    }
    if ($req->turn === 'studentID') {
      $user = $this->userSvc->find('id', $req->studentID);
    }
    if (isset($user)) {
      $this->mailer->AddAddress($user->email);
      $subject = $this->createTurnSubject($req);
      $message = $this->processTurnTemplate($req->id, $user->name);
      $this->mailer->Subject = $subject;
      $this->mailer->AltBody = $this->processAltBody($subject);
      $this->mailer->msgHTML($message);
      $this->logger()->info('mailSvc:sendTurnUpdated', [$this->mailer]);
      $this->send();
    }
  }

  public function sendStatusUpdated($req) {
    $student = $this->userSvc->find('id', $req->studentID);
    $this->mailer->AddAddress($student->email);
    $subject = $this->createStatusSubject($req);
    $message = $this->processStatusTemplate($req->id, $student->name, $req->status->name);
    $this->mailer->Subject = $subject;
    $this->mailer->AltBody = $this->processAltBody($subject);
    $this->mailer->msgHTML($message);
    $this->logger()->info('mailSvc:sendStatusUpdated', [$this->mailer]);
    $this->send();
  }

  private function send() {
    if (!$this->mailer->send()) {
      $this->logger()->info('email fail', [$this->mailer->ErrorInfo]);
      $this->logger()->info($this->mailer->ErrorInfo);
    } else {
      $this->logger()->info('email success!');
    }
  }

  private function createTurnSubject($request) {
    return $this->subjects['turn'][0] . $request->id . $this->subjects['turn'][1];
  }

  private function processTurnTemplate($requestID, $name) {
    $message = $this->templates['turn'];
    $message = str_replace('%id%', $requestID, $message);
    $message = str_replace('%user%', $name, $message);
    return $message;
  }

  private function createStatusSubject($request) {
    return $this->subjects['status'][0] . $request->id . $this->subjects['status'][1] . $request->status->name;
  }

  private function processStatusTemplate($requestID, $name, $status) {
    $message = $this->templates['status'];
    $message = str_replace('%id%', $requestID, $message);
    $message = str_replace('%student%', $name, $message);
    $message = str_replace('%status%', $status, $message);
    return $message;
  }

  private function processAltBody($subject) {
    return "(Plain-text message) " . $subject . "[AUTOMATED MESSAGE: DO NOT REPLY]";
  }

}
