<?php
// ----------------------------------------------------------------------------
// エラーハンドラー
// 2023/09/12 yamamoto
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
// エラーハンドラー
// ----------------------------------------------------------------------------
function cms_error_handler($errno, $errstr, $errfile, $errline) {
  $type = array(
    '1' => 'E_ERROR'
   ,'2' => 'E_WARNING'
   ,'4' => 'E_PARSE'
   ,'8' => 'E_NOTICE'
   ,'16' => 'E_CORE_ERROR'
   ,'32' => 'E_CORE_WARNING'
   ,'64' => 'E_COMPILE_ERROR'
   ,'128' => 'E_COMPILE_WARNING'
   ,'256' => 'E_USER_ERROR'
   ,'512' => 'E_USER_WARNING'
   ,'1024' => 'E_USER_NOTICE'
   ,'2048' => 'E_STRICT'
   ,'4096' => 'E_RECOVERABLE_ERROR'
   ,'8192' => 'E_DEPRECATED'
   ,'16384' => 'E_USER_DEPRECATED'
  );

  cms_error_log("[" . $type[$errno] . "] $errstr $errfile($errline)", true);
}

// ----------------------------------------------------------------------------
// シャットダウンハンドラー
// ----------------------------------------------------------------------------
function cms_shutdown_handler() {
  global $s;

  $flg = false;
  if($e = error_get_last()){
    switch($e['type']){
      case E_ERROR:
      case E_PARSE:
      case E_CORE_ERROR:
      case E_CORE_WARNING:
      case E_COMPILE_ERROR:
      case E_COMPILE_WARNING:
        $isError = true;
        break;
    }
  }
  if($flg){
    cms_error_handler($e['type'], $e['message'], $e['file'], $e['line']);
  }
}

// ----------------------------------------------------------------------------
// エラーログ出力
// ----------------------------------------------------------------------------
function cms_error_log($val, $direct = false) {
    if($direct) {
      //$log = $val;
      $msg = $val;

      $msg = str_replace("\r\n", " ", $msg);
      $msg = str_replace("\r", " ", $msg);
      $msg = str_replace("\n", " ", $msg);

      $msg = str_replace("      ", " ", $msg);
      $msg = str_replace("  ", " ", $msg);
      $msg = str_replace("  ", " ", $msg);
      $msg = str_replace("  ", " ", $msg);
      $msg = str_replace("  ", " ", $msg);
      $msg = str_replace("  ", " ", $msg);

      $log = $msg;
    }
    else {
      $log = json_encode($val, JSON_UNESCAPED_UNICODE);
    }
    error_log(date('H:i:s') . ' ' . rtrim($log) . "\n", 3, __dir__ . '/logs/php_error_' . date('Ymd') . '.log');
  }