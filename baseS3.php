<?php
	session_start();
	ini_set( 'display_errors', 1 );
    date_default_timezone_set('Asia/Tokyo');

	//https://scientist3.ms123.jp/baseS3.php

// define('DB_HOST', 'mysql203.xbiz.ne.jp');
// define('DB_USERNAME', 'ms123_scientist3');
// define('DB_PASSWD', 'x7WYr3a9');
// define('DB_DBNAME', 'ms123_scientist3');
// 	$ret=S3SendMail("お問い合わせ", "toretoresansan00@gmail.com", "[D-TMP02]->松浦,[D-SYUBETSU]->東京都");	
// echo $ret;
// exit;
//=========================================================================================================
//名前 
//機能\ 
//引数 template・・・テンプレート名
//     sendemail・・・送信先メールアドレス
//     pram・・・置換文字群
//戻値 正常終了・・・0 エラー・・・-1
//=========================================================================================================
function S3SendMail($template,$sendemail,$pram)
{
	//使用例
	//ret=S3SendMail("受注メール", "matsuura@ms123.co.jp", "[D-M1_TXT1]->松浦,[D-M1_TXT2]->東京都");	

	// 　※置換文字群のルールは以下となります。
	// ---------------------
	// 置換元->置換先,置換元->置換先,置換元->置換先
	// ---------------------
	// 　（置換元と置換先でワンセット、セットの上限は特になし）
	try {
		$maildata = GetMailTemplate($template);
		$MailBody = $maildata['BODY'];
		$subject = $maildata['TITLE'];

		$pram=str_replace(" ","",$pram);
		$tmps1=explode(",",$pram);
		for($i=0; $i<count($tmps1); $i++){
			$tmps2=explode("->",$tmps1[$i]);
			$key=$tmps2[0];
			$val=$tmps2[1];
			$subject=str_replace($key,$val,$subject);
			$MailBody=str_replace($key,$val,$MailBody);
		}
		
		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
		$ret=mb_send_mail($sendemail, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

		return $ret;
	} catch (Exception $e) {
		// 例外が発生した場合に行う処理
	}
	return -1;
} 

//=========================================================================================================
//名前 
//機能 メールテンプレートを取得して定数を埋め込む
//引数 
//戻値 
//=========================================================================================================
function GetMailTemplate($mailname)
{
	eval(globals());

  $maildata = array();

	$StrSQL="SELECT TITLE,BODY from DAT_MAIL where MAILNAME='MAILNAME:".$mailname."';";
	$rs=mysqli_query(ConnDB_S3(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	if($item>0){
		$maildata['TITLE'] = $item['TITLE'];
		$maildata['BODY'] = $item['BODY'];

		$maildata['BODY']=str_replace("[BASE_URL]",BASE_URL,$maildata['BODY']);
		$maildata['BODY']=str_replace("[SENDER_EMAIL]",SENDER_EMAIL,$maildata['BODY']);
		$maildata['BODY']=str_replace("[SENDER_NAME]",SENDER_NAME,$maildata['BODY']);
		$maildata['BODY']=str_replace("[WEBSITE_NAME]",WEBSITE_NAME,$maildata['BODY']);
		$maildata['BODY']=str_replace("[COMPANY_NAME]",COMPANY_NAME,$maildata['BODY']);
		$maildata['BODY']=str_replace("[M1_CAPTION]",M1_CAPTION,$maildata['BODY']);
		$maildata['BODY']=str_replace("[M2_CAPTION]",M2_CAPTION,$maildata['BODY']);

		$maildata['TITLE']=str_replace("[WEBSITE_NAME]",WEBSITE_NAME,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[O1_CAPTION]",O1_CAPTION,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[O2_CAPTION]",O2_CAPTION,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[M1_CAPTION]",M1_CAPTION,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[M2_CAPTION]",M2_CAPTION,$maildata['TITLE']);
	}

	return $maildata;
} 
//=========================================================================================================
//名前
//機能
//引数
//戻値
//=========================================================================================================
function globals(){

	$vars = array();
	foreach($GLOBALS as $k => $v){
		$vars[] = "$".$k;
	}
	return "global ".join(",", $vars).";";
}
//=========================================================================================================
//名前 DB初期化
//機能 DBとの接続を確立する
//引数 なし
//戻値 $function_ret
//=========================================================================================================
function ConnDB_S3()
{
	eval(globals());

	$ConnDB=mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);

	return $ConnDB;
}
?>