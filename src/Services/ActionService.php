<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Action;
use App\Services\LogService;

class ActionService extends Service {

  private $logSvc;

  public function __construct($logger) {
    parent::__construct($logger);
    $this->logSvc = new LogService($logger);
  }

  public function getActions($user) {
    $this->logger()->info('actionSvc:getActions', [$user, $user->role]);
    if ($user->role->registrar) {
      return Action::Main()->Registrar()->get();
    }
    if ($user->role->dept) {
      $this->logger()->info('dept');
      return Action::Main()->Dept()->get();
    }
    if ($user->role->student) {
      return Action::Main()->Student()->get();
    }
  }

  public function attachNextStatusData($data, $id) {
    $nextStatusData = $this->getNextStatusData($id);
    foreach ($nextStatusData as $key => $value) {
      $data[$key] = $value;
    }
    return $data;
  }

  private function getNextStatusData($id) {
    $action = Action::find($id);
    $arr = [];
    if (isset($action->nextStepID)) {
      $arr['stepID'] = $action->nextStepID;
    }
    if (isset($action->nextTurn)) {

      $arr['turn'] = $action->nextTurn;
    }
    if (isset($action->nextStatusID)) {
      $arr['statusID'] = $action->nextStatusID;
    }
    return $arr;
  }

  public function logAction($actionID, $request, $note = null, $items = null) {
    $logTemplateID = Action::find($actionID)->logTemplateID;
    $this->logger()->info('actionSvc:logAction', [$logTemplateID, $note, $items]);
    $this->logSvc->add($logTemplateID, $request, $note, $items);
  }
  
}
