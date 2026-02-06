<?php
session_start();
require "../config.php";
require "../base.php";
require '../a_m2/config.php';

ini_set( 'display_errors', 0 );
set_time_limit(7200);

//InitSub();//データベースデータの読み込み
//ConnDB();//データベース接続
Main();//メイン処理

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function Main()
{

	eval(globals());

	if($_POST['id']==""){
		$id=$_GET['id'];
		$pass=$_GET['pass'];
	} else {
		$id=$_POST['id'];
		$pass=$_POST['pass'];
	}

	$mode="new";

	DispData($id,$pass);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispData($id,$pass)
{

	eval(globals());

	//各テンプレートファイル名
	$htmlnew = "edit.html";

	$filename=$htmlnew;
	$errmsg="";

	if($id!=""){
		//$StrSQL="SELECT * from DAT_M3 where EMAIL='".$id."' and ENABLE='ENABLE:公開中';";
		$StrSQL="SELECT * from DAT_M3 where EMAIL='".$id."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item>0){
			$item = mysqli_fetch_assoc($rs);
			SendMail($item);
			$filename="end.html";
		} else {
			$errmsg="<font style='color:#ff0000;'>入力されたメールアドレスでの登録がありません。</font><br />";
		}
	}

	$fp=$DOCUMENT_ROOT.$filename;
	$str=@file_get_contents($fp);

	$str = MakeHTML($str,0,$lid);

	if($errmsg<>""){
		$str=str_replace("[ERRMSG]",$errmsg,$str);
		$str=DispParam($str, "ERR");
	} else {
		$str=DispParamNone($str, "ERR");
	}

	$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($item)
{

	eval(globals());

	//$fp="mail.txt";
	//$MailBody=@file_get_contents($fp);
	$maildata = GetMailTemplate('パスワード再送信(M3)');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	for ($i=0; $i<=$FieldMax; $i=$i+1)
	{
		$MailBody=str_replace("[".$FieldName[$i]."]",$item[$FieldName[$i]],$MailBody);
		$MailBody=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$item[$FieldName[$i]])),$MailBody);
		if (is_numeric($item[$FieldName[$i]]))
		{
			$MailBody=str_replace("[N-".$FieldName[$i]."]",number_format($item[$FieldName[$i]],0),$MailBody);
		}
			else
		{

			$MailBody=str_replace("[N-".$FieldName[$i]."]","",$MailBody);
		} 
	}

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	//mb_send_mail($item[$FieldName[2]], "【Scientist3】パスワード再送信のお知らせ", $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 

	mb_send_mail($item[$FieldName[2]], $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail(SENDER_EMAIL, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
}

//=========================================================================================================
//【関数名】	:ConnDB()
//【機能\】	:データベースへの接続
//【引数】	:なし
//【戻り値】	:なし
//【備考】	:DB接続
//=========================================================================================================
function ConnDB()
{
	eval(globals());

	$ConnDB=mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);

	return $ConnDB;
} 

?>
