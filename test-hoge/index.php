<?php
session_start();

require "../config.php";
require "../base.php";
//require './config.php';

set_time_limit(7200);

// ---------------------------------------------
// デバッグセット
// ---------------------------------------------
require_once(__dir__ . '/../handler.php');
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_error_handler('cms_error_handler', E_ALL);
register_shutdown_function('cms_shutdown_handler');
// ---------------------------------------------

//InitSub();//データベースデータの読み込み
ConnDB();//データベース接続

echo "test<br>";

echo "part1のdiv_id: ".check_split_progress("884", "503-1-Part0");


//$shodan_id="868";
////$shodan_id="870";
//$mid1="M100012";
//
//echo "shodan_id:$shodan_id<br>";
//echo "mid1:$mid1<br>";
//echo "check_M2_PAY_TYPE:".check_M2_PAY_TYPE($shodan_id,$mid1);







//=========================================================================================================
//名前 DB初期化
//機能 DBとの接続を確立する
//引数 なし
//戻値 $function_ret
//=========================================================================================================
function ConnDB()
{
	eval(globals());

	$ConnDB=mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);

	return $ConnDB;
} 


//=========================================================================================================
//名前 
//機能\ 
//引数  $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート1');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	//echo "<pre>";
	//var_dump($maildata);
	//echo "</pre>";

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$key."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemF = mysqli_fetch_assoc($rs);
	//foreach ($itemF as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}
	
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$itemF["MID1"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID1 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID1 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$itemF["MID2"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID2 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID2 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$mailto = $item_MID2["EMAIL"];

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMail_v:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}




//=========================================================================================================
//名前 
//機能\ 
//引数 $keyはDAT_SHODANのID
//戻値 
//=========================================================================================================
function SendMail_v2($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート4');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	//echo "<pre>";
	//var_dump($maildata);
	//echo "</pre>";

	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID='".$key."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemS = mysqli_fetch_assoc($rs);
	//foreach ($itemS as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}
	
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$itemS["MID1_LIST"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID1 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID1 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$itemS["MID2"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID2 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID2 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$mailto = $item_MID2["EMAIL"];


	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMail_v:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	

}



//=========================================================================================================
//名前 
//機能\ 
//引数 $keyはDAT_FILESTATUS_DETAILのID
//戻値 
//=========================================================================================================
function SendMail_v3($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート10');

	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	//echo "<pre>";
	//var_dump($maildata);
	//echo "</pre>";

	$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE ID='".$key."' order by ID desc limit 1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemFSD=mysqli_fetch_assoc($rs);
	//echo "<!--StrSQL:$StrSQL-->";

	$filestatus_id_original=$itemFSD["FILESTATUS_ID"];

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$filestatus_id_original."' order by ID desc limit 1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemFS=mysqli_fetch_assoc($rs);
	//echo "<!--StrSQL:$StrSQL-->";

	$fs_shodan_id=$itemFS["SHODAN_ID"];
	$fs_m2_id=$itemFS["M2_ID"];
	$fs_m2_version=$itemFS["M2_VERSION"];
	$fs_mid1=$itemFS["MID1"];
	$fs_mid2=$itemFS["MID2"];

	//echo "<!--filestatus_id_original:$filestatus_id_original-->";
	//echo "<!--fs_shodan_id:$fs_shodan_id-->";
	//echo "<!--fs_m2_id:$fs_m2_id-->";
	//echo "<!--fs_mid1:$fs_mid1-->";
	//echo "<!--fs_mid2:$fs_mid2-->";

	
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$fs_mid1."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID1 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID1 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$fs_mid2."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID2 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID2 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$mailto = $item_MID2["EMAIL"];


	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMail_v:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	

}


?>