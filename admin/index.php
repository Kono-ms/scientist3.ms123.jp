<?
  session_start();
  require "../config.php";
require "../base_a.php";
  require './config.php';

set_time_limit(7200);

Main();//メイン処理

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function Main()
{
  extract($GLOBALS);

  if($_POST['lid'][0]==""){
    $lid=$_GET['lid'];
  }
    else
  {
    $lid=sprintf("%s,%s", $_POST['lid'][0], $_POST['lid'][1]);
  }

  DispData($mode,$sort,$word,$key,$page,$lid);

  return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid)
{
  extract($GLOBALS);

  $str="";

  $fp=$DOCUMENT_ROOT."main.html";
  // $fso is of type "Scripting.FileSystemObject"
//  $tso=fopen($fp,"r");
//  $str=fpassthru($tso);
//  $str=fgets($tso,65535);;
  $str=@file_get_contents($fp);

  print MakeHTML($str,1,$lid);

  return $function_ret;
} 

?>

