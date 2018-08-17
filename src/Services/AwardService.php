<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Award;
use App\Models\Evaluation;

class AwardService extends Service {

  private $logItems;
  public function __construct($logger) {
    parent::__construct($logger);
  }

  public function add($awardData, $requestID) {
    $this->logger()->info('awardSvc:add', [$awardData]);
    $this->logItems = [];
    $awardIDs = [];
    foreach ($awardData as $item) {
      $award = Award::create($item);
      array_push($awardIDs, $award->id);
      $this->recordLogItems(null, $award->id);
    }
    // $this->updateEvaluation($requestID, ['proposedAwardIDs' => $ids]);
    return ['logItems' => $this->logItems, 'evaluationItems' => $awardIDs];
  }

  public function modify($awardData, $requestID) {
    $this->logger()->info('awardSvc:edit', [$awardData]);
    $this->logItems = [];
    $finalIDs = [];
    foreach ($awardData as $item) {
      if ($item['actionID'] === 35) {
        $award = new Award;
        $award->requestID = $requestID;
        $award->gtCourseID = $item['gtCourseID'];
        $award->registrarCredit = $item['registrarCredit'];
        $award->save();
      } else {
        $award = Award::find($item['awardID']);
        $award->registrarCredit = $item['registrarCredit'];
        $award->save();
      }
      if (isset($item['actionID'])) {
        $this->recordLogItems($item['actionID'], $award->id);
      }
      if ($award->registrarCredit > 0) {
        array_push($finalIDs, $award->id);
      }
    }
    // $this->finalizeAsIs($requestID);
    // $this->updateEvaluation($requestID, ['finalAwardIDs' => $finalIDs]);
    return ['logItems' => $this->logItems, 'evaluationItems' => $finalIDs];
  }

  private function recordLogItems($actionID, $awardID) {
    array_push(
      $this->logItems, 
      ['actionID' => $actionID, 'awardID' => $awardID]
    );
    return $this->logItems;
  }

  public function updateEvaluation($requestID, $evalData) {
    $this->logger()->info('awardSvc:updateEvaluation', [$requestID, $evalData]);
    $evalFound = Evaluation::where('requestID', $requestID)->first();
    if (isset($evalFound)) {
      $this->logger()->info('eval found', [$evalFound]);
      foreach ($evalData as $key => $value) {
        $this->logger()->info('key, value', [$key, $value, implode(",", $value)]);
        $evalFound->$key = implode(",", $value);
        $this->logger()->info('eval found', [$evalFound]);
      }
      $evalFound->save();
      $this->logger()->info('evalFound updated', [$evalFound]);
    } else {
      $this->logger()->info('eval not found, create new');
      $eval = new Evaluation;
      $eval->requestID = $requestID;
      foreach($evalData as $key => $value) {
        $eval->$key = implode(",", $value);
      }
      $eval->save();
      $this->logger()->info('new eval saved', [$eval]);
    }
  }

  public function finalizeAsIs($requestID) {
    $this->logger()->info('awardSvc:finalizeAsis', [$requestID]);
    $eval = Evaluation::where('requestID', $requestID)->first();
    $this->logger()->info('awardSvc:finalizeAsis', [$eval]);
    if ($eval->proposedAwardIDs !== "") {
      $ids = explode(",", $eval->proposedAwardIDs);
      foreach ($ids as $id) {
        $award = Award::find($id);
        $award->registrarCredit = $award->deptCredit;
        $award->save();
      }
    }
    $eval->finalAwardIDs = $eval->proposedAwardIDs;
    $eval->save();
  }

}
