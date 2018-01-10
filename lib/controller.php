<?php
// -------------------------------------------
// controller.php
//
// Steven Mead 2016
// School of Computing
// Teesside University
//
// Base class for all CodeHarness Controllers
// also contains some static functions and
// methods used by the Controller class.
// -------------------------------------------

require_once '../lib/db_connection.php';
require_once '../lib/view.php';

function extractHostInfo() {
  global $FileName;

  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $host = (!empty($_SERVER['HTTP_X_FORWARDED_SERVER'])) ? $_SERVER['HTTP_X_FORWARDED_SERVER'] : $_SERVER['HTTP_HOST'];
  $full_uri = explode($FileName,$_SERVER['REQUEST_URI']);

  $action = isset($full_uri[1]) ? $full_uri[1] : '';

  //strip any GET parameters, these will still be accessible
  //from the $_GET super global.
  $action = preg_replace("/\?.*/","",$action);

  if(in_array($action,Controller::$DefaultIndexActions)) {
    $action = "/index";
  }

  $ret_val = array(
    'protocol' => $protocol,
    'host' => $host,
    'path' => isset($full_uri[0]) ? $full_uri[0] : '',
    'controller' => $FileName,
    'action' => substr($action,1),
    'xhr' => isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest',
    'params' => array()
  );

  //Copy the $_GET & $_POST super variables into the single 'params' array
  //
  //Note: 1) This is done for convenience rather that efficiency.
  //      2) Parameters with the same keys in both $_GET and $_POST will
  //         end up with the values in $_POST.  No attempt is made to
  //         resolve name clashes.
  //         array_merge_recursive() could be used, but this would create
  //         arrays for each duplicated key.  This would be more problematic
  //         than simply overwriting the values of duplicated request parameters.
  $ret_val['params'] = array_merge($ret_val['params'], $_GET);
  $ret_val['params'] = array_merge($ret_val['params'], $_POST);

  return $ret_val;
}

abstract class Controller {

  public static $DefaultIndexActions = array("","/","/index","/index/");

  private static function generate_table_name($controller) {
    $ctrl_class = preg_replace("/Controller/","",get_class($controller));
    $table = "";
    foreach(str_split($ctrl_class) as $char) {
      if(ctype_upper($char)) {
        if(strlen($table) == 0) {
          $table = $table.strtolower($char);
        }
        else {
          $table = $table."_".strtolower($char);
        }
      }
      else {
        $table = $table.strtolower($char);
      }
    }
    return $table."s";
  }

  public function __construct() {
    $this->className = get_class($this);
    $this->title = "";
    $this->assets = array('css' => array(), 'js' => array());
    $this->table_name = Controller::generate_table_name($this);
    $this->remoteIp = $_SERVER['REMOTE_ADDR'];
    $this->requestUri = $_SERVER['REQUEST_URI'];
    $this->view = new View($this);
  }

  public function path($action = "") {
    return $this->requestInfo['protocol'].$this->requestInfo['host'].$this->requestInfo['path'].$this->requestInfo['controller']."/$action";
  }

  public function pathToOther($otherController = "",$action = "") {
    return $this->requestInfo['protocol'].$this->requestInfo['host'].$this->requestInfo['path'].((isset($otherController) && strlen($otherController) > 0) ? $otherController.".php/$action" : "");
  }

  public function className() {
    return $this->className;
  }

  public function getAssets() {
    return $this->assets;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getHostInfo() {
    return $this->requestInfo;
  }

  //Function: param()
  //
  //This is a shortcut functin to avoid have to write $this->requestInfo['params'][name]
  //each time.
  protected function param($name) {
    return $this->requestInfo['params'][$name];
  }

  protected function addAsset($type,$file) {
    if(array_key_exists($type,$this->assets)) {
      array_push($this->assets[$type],$file);
    }
  }

  private function setHostInfo($requestInfo) {
    $this->requestInfo = $requestInfo;
  }

  public static function router($controller) {

    if(class_exists($controller)) {
      $requestInfo = extractHostInfo();

      //check method exists
      if(method_exists($controller,$requestInfo['action'])) {
        $controller_obj = new $controller;

        $controller_obj->setHostInfo($requestInfo);

        try {
          call_user_func_array(array($controller_obj,$requestInfo['action']),array());
        }
        catch(Exception $e) {
          echo "Exception Caught: ".$e->getMessage();
        }
      }
      else {
        echo "Error, unknown action '".$requestInfo['action']."' for $controller<br>";
      }
    }
    else {
      echo "Error, unknown controller '$controller'<br>";
    }
  }



}

function extract_file_name($file) {
  $file_path = explode("/",$file);

  return $file_path[count($file_path)-1];
}

function extract_controller_name($controller_file) {
  $controller = explode(".php",$controller_file);
  return $controller[0];
}

 ?>
