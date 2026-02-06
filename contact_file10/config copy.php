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
  $TableName="DAT_CONTACT";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="SYUBETSU";
  $FieldName[2]="NAME";
  $FieldName[3]="EMAIL";
  $FieldName[4]="TEL";
  $FieldName[5]="ADDRESS";
  $FieldName[6]="COMMENT";
  $FieldName[7]="TMP01";	// 会社名・店舗名
  $FieldName[8]="TMP02";  // ユーザー区別
  $FieldName[9]="TMP03";  // 利用規約への同意
  $FieldName[10]="TMP04";
  $FieldName[11]="TMP05";
  $FieldName[12]="TMP06";
  $FieldName[13]="TMP07";
  $FieldName[14]="TMP08";
  $FieldName[15]="TMP09";
  $FieldName[16]="TMP10";
  $FieldName[17]="ENABLE";
  $FieldName[18]="NEWDATE";
  $FieldName[19]="EDITDATE";


  $FieldValue[0]="";
  $FieldValue[1]="";
  $FieldValue[2]="";
  $FieldValue[3]="";
  $FieldValue[4]="";
  $FieldValue[5]="";
  $FieldValue[6]="";
  $FieldValue[7]="";
  $FieldValue[8]="";
  $FieldValue[9]="";
  $FieldValue[10]="";
  $FieldValue[11]="";
  $FieldValue[12]="";
  $FieldValue[13]="";
  $FieldValue[14]="";
  $FieldValue[15]="";
  $FieldValue[16]="";
  $FieldValue[17]="";
  $FieldValue[18]="";
  $FieldValue[19]="";


//入力フィールドの書式　0-TEXT, 1-SELECT, 2-RADIO, 3-CHECKBOX, 4-FILE
  $FieldAtt[0]="0";
  $FieldAtt[1]="1";
  $FieldAtt[2]="0";
  $FieldAtt[3]="0";
  $FieldAtt[4]="0";
  $FieldAtt[5]="0";
  $FieldAtt[6]="0";
  $FieldAtt[7]="0";
  $FieldAtt[8]="2";
  $FieldAtt[9]="3";
  $FieldAtt[10]="0";
  $FieldAtt[11]="0";
  $FieldAtt[12]="0";
  $FieldAtt[13]="0";
  $FieldAtt[14]="0";
  $FieldAtt[15]="0";
  $FieldAtt[16]="0";
  $FieldAtt[17]="0";
  $FieldAtt[18]="0";
  $FieldAtt[19]="0";


//SELECT, RADIO, CHECKBOX時の値群
  $FieldParam[0]="";
  $FieldParam[1]="利用の検討について::利用方法について::サービスの退会について::その他";
  $FieldParam[2]="";
  $FieldParam[3]="";
  $FieldParam[4]="";
  $FieldParam[5]="";
  $FieldParam[6]="";
  $FieldParam[7]="";
  $FieldParam[8]="Researchers::Supplier";
  $FieldParam[9]="同意する";
  $FieldParam[10]="";
  $FieldParam[11]="";
  $FieldParam[12]="";
  $FieldParam[13]="";
  $FieldParam[14]="";
  $FieldParam[15]="";
  $FieldParam[16]="";
  $FieldParam[17]="";
  $FieldParam[18]="";
  $FieldParam[19]="";


//全フィールド数
  $FieldMax=19;

//キーフィールドの設定
  $FieldKey=0;

//リスト行数
  $PageSize=20;

//ASPファイル名
  $aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
  $filepath1="/a_contact/data/";

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
  $FieldValue[9]="";
  $FieldValue[10]="";
  $FieldValue[11]="";
  $FieldValue[12]="";
  $FieldValue[13]="";
  $FieldValue[14]="";
  $FieldValue[15]="";
  $FieldValue[16]="";
  $FieldValue[17]="";
  $FieldValue[18]=Date("Y/m/d H:i:s");
  $FieldValue[19]="";


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
