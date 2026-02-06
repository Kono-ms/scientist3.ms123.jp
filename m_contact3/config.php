<?php

//====================
//グローバル変数宣言
//====================

//=========================================================================================================
//名前 基本データのセット
//機能\ 
//引数 
//戻値 
//=========================================================================================================
//function InitSub()
//{
//  extract($GLOBALS);


//DBへの接続モード
//  $DBParam="Driver={SQL Server};Server=m-server01;UID=;PWD=;DATABASE=pg";

//テーブル名の指定
  $TableName="DAT_CONTACT2";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="MID";
  $FieldName[2]="S_KEY";
  $FieldName[3]="S_WORD";
  $FieldName[4]="S_WORD2";
  $FieldName[5]="TITLE";
  $FieldName[6]="COMMENT";
  $FieldName[7]="FILE"; 
  $FieldName[8]="KIGEN";

  $FieldValue[0]="";
  $FieldValue[1]="";
  $FieldValue[2]="";
  $FieldValue[3]="";
  $FieldValue[4]="";
  $FieldValue[5]="";
  $FieldValue[6]="";
  $FieldValue[7]="";
  $FieldValue[8]="";

//入力フィールドの書式　0-TEXT, 1-SELECT, 2-RADIO, 3-CHECKBOX, 4-FILE
  $FieldAtt[0]="0";
  $FieldAtt[1]="0";
  $FieldAtt[2]="0";
  $FieldAtt[3]="0";
  $FieldAtt[4]="0";
  $FieldAtt[5]="0";
  $FieldAtt[6]="0";
  $FieldAtt[7]="4";
  $FieldAtt[8]="0";

//SELECT, RADIO, CHECKBOX時の値群
  $FieldParam[0]="";
  $FieldParam[1]="";
  $FieldParam[2]="";
  $FieldParam[3]="";
  $FieldParam[4]="";
  $FieldParam[5]="";
  $FieldParam[6]="";
  $FieldParam[7]="";
  $FieldParam[8]="";

//全フィールド数
  $FieldMax=8;

//キーフィールドの設定
  $FieldKey=0;

//リスト行数
  $PageSize=20;

//ASPファイル名
  $aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
  $filepath1="/m_contact2/data/";

//  return $function_ret;
//} 

//=========================================================================================================
//名前 新規作成時の初期値設定
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function InitData()
{
  //extract($GLOBALS);
  eval(globals());

//各フィールドの初期値
  $FieldValue[0]="";
  $FieldValue[1]="";
  $FieldValue[2]="";
  $FieldValue[3]="";
  $FieldValue[4]="";
  $FieldValue[5]="";
  $FieldValue[6]="";
  $FieldValue[7]="";
  $FieldValue[8]="";

  return $function_ret;
} 

//=========================================================================================================
//名前 入力後のエラーチェック（エラーがない場合は空を指定）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ErrorCheck()
{
  //extract($GLOBALS);
  eval(globals());

  $function_ret="";


  return $function_ret;
} 

//=========================================================================================================
//名前 SQL条件（WHERE ･･･ ORDER BY･･･）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ListSql($sort,$word)
{
  //extract($GLOBALS);
  eval(globals());

  $str="";

  if ($word!="")
  {

    $str=$str."WHERE ".$FieldName[$FieldKey]." like '%".$word."%'";
  } 

  $str=$str." ORDER BY ID desc";

  $function_ret=$str;

  return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckKana($str)
{
  //extract($GLOBALS);
  eval(globals());

  $strKana="アイウエオカキクケコサシスセソ\タチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンァィゥェォッャュョーガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポ";

  for ($i=1; $i<=strlen($str); $i=$i+1)
  {
    if ((strpos($strKana,substr($str,$i-1,1)) ? strpos($strKana,substr($str,$i-1,1))+1 : 0)<=0)
    {

      $function_ret=false;
      return $function_ret;

    } 


  }


  $function_ret=true;

  return $function_ret;
} 
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckURL($str)
{
  //extract($GLOBALS);
  eval(globals());

  $strUrl="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";

  for ($i=1; $i<=strlen($str); $i=$i+1)
  {
    if ((strpos($strUrl,substr($str,$i-1,1)) ? strpos($strUrl,substr($str,$i-1,1))+1 : 0)<=0)
    {

      $function_ret=false;
      return $function_ret;

    } 


  }


  $function_ret=true;

  return $function_ret;
} 
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckEmail($str)
{
  //extract($GLOBALS);
  eval(globals());

  if ((strpos($str,"@") ? strpos($str,"@")+1 : 0)<=0)
  {

    $function_ret=false;
    return $function_ret;

  } 

  if ((strpos($str,".") ? strpos($str,".")+1 : 0)<=0)
  {

    $function_ret=false;
    return $function_ret;

  } 


  $function_ret=true;

  return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckNumber($str)
{
  //extract($GLOBALS);
  eval(globals());

  if (!is_numeric($str))
  {

    $function_ret=false;
    return $function_ret;

  } 


  $function_ret=true;

  return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ConvertToHalfNum($hnum)
{
  //extract($GLOBALS);
  eval(globals());

  $function_ret="";

  if (strlen($hnum)==0)
  {

    return $function_ret;

  } 


  $returnString=$hnum;
  $returnString=str_replace("０","0",$returnString);
  $returnString=str_replace("１","1",$returnString);
  $returnString=str_replace("２","2",$returnString);
  $returnString=str_replace("３","3",$returnString);
  $returnString=str_replace("４","4",$returnString);
  $returnString=str_replace("５","5",$returnString);
  $returnString=str_replace("６","6",$returnString);
  $returnString=str_replace("７","7",$returnString);
  $returnString=str_replace("８","8",$returnString);
  $returnString=str_replace("９","9",$returnString);
  $function_ret=$returnString;

  return $function_ret;
} 

?>
