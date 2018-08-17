<?php
namespace App\Handlers;

use \Monolog\Logger;
use \Monolog\Processor\IntrospectionProcessor;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\ChromePHPHandler;
use \Monolog\Formatter\LineFormatter;

class LogHandler {

  public $logger;
  public $name; 
  public $format;
  public $path;
  public $level;

  public function __construct($name, $format, $path, $level) {
    // $this->name = 'TC';
    // $this->format = "[%datetime%] %channel%::%level_name%:: %message% \n%context% \n %extra%\n\n";
    $this->name = $name;
    $this->format = $format;
    $this->path = $path;
    $this->level = $level;
    $this->setLogger();
  }

  private function setLogger() {
    $logger = new Logger($this->name);
    $logger->pushProcessor(new IntrospectionProcessor);

    $level_str = 'Logger:: ' . $this->level;

    $stream = new StreamHandler($this->path, $level_str);
    $stream->setFormatter(new LineFormatter($this->format));
    $logger->pushHandler($stream);
    
    $chrome = new ChromePHPHandler($level_str);
    $logger->pushHandler($chrome);

    $this->logger = $logger;
  }
  

  //DEBUG LEVEL: debug, info, warning, notice, error, critical, alert, emergecy

  public function info($message = null, $data = []) {
    $this->logger->info($message, $this->formatData($data));
  }

  public function error($message, $data = null) {
    $this->logger->error($message, $this->formatData($data));
  }

  public function alert($message, $data = null) {
    $this->logger->alert($message, $this->formatData($data));
  }

  private function formatData($data = []) {
    $arr = [];
    $num = 1;
    foreach ($data as $var) {
      $key = 'data' . $num;
      $arr[$key] = $var;
      $num++;
    };
    return $arr;
  }


}
