<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Log;
use App\Models\LogTemplate;

class LogService extends Service {

  public function __construct($logger) {
    parent::__construct($logger);
    $this->placeholderMarker = '"';
    $this->multipleItemsMarker = '!';
    $this->delimiter = ',';
  }

  private $placeholderMarker;
  private $multipleItemsMarker;
  private $delimiter;

  public function add($logTemplateID, $requestID, $stepID, $note = null, $items = null) {
    $this->logger()->info('logSvc:add', [$logTemplateID]);
    $logTemplate = LogTemplate::find($logTemplateID);
    $newLog = new Log;
    $newLog->logTemplateID = $logTemplateID;
    $newLog->requestID = $requestID;
    $newLog->stepID = $stepID;
    $newLog->description = $logTemplate->description;
    if (isset($note)) {
      $newLog->note = $note;
    }
    $newLog->itemString = $this->createItemString($logTemplate, $items);
    $newLog->save();
    $this->logger()->info('newLog saved', [$newLog]);
  }


  private function createItemString($logTemplate, $items) {
    if (is_null($logTemplate->itemTemplate) || empty($items)) {
      return null;
    }
    if ($logTemplate->isItemMulti) {
      return $this->processMultipleItems($items, $logTemplate->itemTemplate);
    }
    return $this->processSingleItem($items, $logTemplate->itemTemplate);
  }

  private function processMultipleItems($items, $template) {
    $arr = [];
    foreach ($items as $item) {
      array_push($arr, $this->processSingleItem($item, $template));
    }
    return implode($this->multipleItemsMarker, $arr);
  }

  private function processSingleItem($item, $template) {

    $arr = explode($this->delimiter, $template);
    foreach ($arr as $key => $value) {
      if ($this->checkIfPlaceholder($value)) {
        $arr[$key] = $item[0];
        array_shift($item);
      }
    }
    $str = implode(' ', $arr);
    return $str;
  }

  private function checkIfPlaceholder($value) {
    if (strpos($value, $this->placeholderMarker) === 0) {
      return true;
    }
    return false;
  }

}
