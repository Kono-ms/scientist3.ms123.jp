<?php

//テーブル名の指定
	$TableName="DAT_MAIL";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="MAILNAME";
  $FieldName[2]="TITLE";
  $FieldName[3]="BODY";
  $FieldName[3]="BODY";
  $FieldName[4]="SENDER";
  $FieldName[5]="NUMBER";
  $FieldName[6]="COMMENT";


  $FieldValue[0]="";
  $FieldValue[1]="";
  $FieldValue[2]="";
  $FieldValue[3]="";
  $FieldValue[4]="";
  $FieldValue[5]="";
  $FieldValue[6]="";


//入力フィールドの書式　0-TEXT, 1-SELECT, 2-RADIO, 3-CHECKBOX, 4-FILE
  $FieldAtt[0]="0";
  $FieldAtt[1]="1";
  $FieldAtt[2]="0";
  $FieldAtt[3]="0";
  $FieldAtt[4]="0";
  $FieldAtt[5]="0";
  $FieldAtt[6]="0";


//SELECT, RADIO, CHECKBOX時の値群
  $FieldParam[0]="";
  $FieldParam[1]="お問い合わせ::パスワード再送信(M1)::パスワード再送信(M2)::パスワード再送信(M3)::会員登録完了(M1)::会員登録完了(M2)::契約締結確認::サプライヤーへの問合せ::銀行口座情報の変更::銀行口座情報の変更（M1）::審査依頼::審査依頼（M1）::サプライヤーへの問合せ(M1)::サプライヤーへの問合せ(M2)::サプライヤーへの見積もり依頼(M1)::サプライヤーへの見積もり依頼(M2)::研究者への見積もり送信(M1)::研究者への見積もり送信(M2)::物品納品(M1)::物品納品(M2)::データ納品(M1)::データ納品(M2)::請求(M1)::請求(ADMIN)::発注依頼(M2)::発注依頼(M3)::発注依頼(ADMIN)::受注承認(M1)::受注承認(M2)::受注承認(M3)::発注依頼承認(M2)::発注依頼承認(M3)::発注依頼否認(M2)::発注依頼否認(M3)::登録状態変更::署名取消し::CompanyInformationの変更::CompanyInformationの変更(M1)::File10の変更::File10の変更(M1)::サプライヤー本登録完了(M1-1)::Contact Us(ADMIN)::サプライヤー要再審査(M1-1)::サプライヤー本登録不可(M1-1)::Contact Us（M1）::サプライヤー署名取り消し依頼(M-1)::サプライヤー署名取り消し依頼::サプライヤー署名取り消し完了(M-1)::研究者契約締結確認(ADMIN)::研究者会員登録完了(ADMIN)::研究者からサプライヤーへの見積もり依頼(ADMIN)::研究者からサプライヤーにメッセージ送信(M1)::サプライヤーから研究者にメッセージ送信(M2)::契約締結完了のお知らせ::契約未締結::研究者情報変更::キャンセル::キャンセル依頼(M1)::キャンセル依頼(ADMIN)::メールテンプレート1::メールテンプレート2::メールテンプレート3::メールテンプレート4::メールテンプレート5::メールテンプレート6::メールテンプレート7::メールテンプレート8::メールテンプレート9::メールテンプレート10";
  $FieldParam[2]="";
  $FieldParam[3]="";
  $FieldParam[4]="";
  $FieldParam[5]="";
  $FieldParam[6]="";


//全フィールド数
	$FieldMax=6;

//キーフィールドの設定
	$FieldKey=0;

//リスト行数
	$PageSize=20;

//ASPファイル名
	$aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
	$filepath1="/a_mail/data/";

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

	$str="WHERE ID>0 ";

	if ($word!=""){
		$str=$str."AND ".$FieldName[$FieldKey]." like '%".$word."%' ";
	} 

	$str=$str."ORDER BY ID desc";

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
