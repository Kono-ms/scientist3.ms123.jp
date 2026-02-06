<h1>テストcron.php</h1>
<br>

<?php
require "../config.php";
require "../base.php";
require "../common.php";


/*
$filename="test.dat";
$str=date('Y/m/d H:i:s')."\n";
file_put_contents($filename, $str, FILE_APPEND);
*/

/*
if(isset($_SESSION['MID']) && $_SESSION['MID']!=""){
	$StrSQL="SELECT * from DAT_M2 where MID = '".$_SESSION['MID']."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_fetch_assoc($rs);
	echo "<pre>";
	var_dump($item);
	echo "</pre>";
}*/

//SendMail();

echo "今: ".date("Y-m-d H:i:s")."<br>";
echo "1分前: ".date("Y-m-d H:i:s",strtotime("-1 minute"))."<br>";
echo "1日前: ".date("Y-m-d H:i:s",strtotime("-1 day"))."<br>";
echo "<br>";

$StrSQL="SELECT * from DAT_MESSAGE WHERE NOREAD <=> NULL OR NOREAD='' ORDER BY ID desc";
$rs=mysqli_query(ConnDB(),$StrSQL);


$base_date=date("Y-m-d H:i:s",strtotime("-2 day"));

$str="【開始】\n";
while($item=mysqli_fetch_assoc($rs)){
	//echo $item["ID"].": ".$item["NEWDATE"]."<br>";

	$newdate=$item["NEWDATE"];

	if(strtotime($newdate) >= strtotime($base_date)){
		$str.="2日間未読: ".$item["ID"].": ".$item["NEWDATE"]."<br>\n";
		echo "2日間未読: ".$item["ID"].": ".$item["NEWDATE"]."<br>\n";
	}

}

$str.="\n\n";
$filename="test.dat";
//file_put_contents($filename, $str, FILE_APPEND);

//test_mail($str);



function test_mail($str){
	$mailto="h.tsurumi@ms123.co.jp";
	$subject="cron test";
	$MailBody=$str;
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
}



//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail()
{

	eval(globals());

	//$fp="./contact_mail.txt";
	//$MailBody=@file_get_contents($fp);
	$maildata = GetMailTemplate('契約締結確認');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$maildata2 = GetMailTemplate('研究者契約締結確認(ADMIN)');
	$MailBody2 = $maildata2['BODY'];
	$subject2 = $maildata2['TITLE'];

	for ($i=0; $i<=$FieldMax; $i=$i+1)
	{
		$MailBody=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody);
		$MailBody=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody);
		$MailBody2=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody2);
		$MailBody2=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody2);
		$subject=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$subject);
		$subject=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$subject);
		$subject2=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$subject2);
		$subject2=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$subject2);
		if (is_numeric($FieldValue[$i]))
		{
			$MailBody=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody);
			$MailBody2=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody2);
			$subject=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$subject);
			$subject2=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$subject2);
		}
			else
		{
			$MailBody=str_replace("[N-".$FieldName[$i]."]","",$MailBody);
			$MailBody2=str_replace("[N-".$FieldName[$i]."]","",$MailBody2);
			$subject=str_replace("[N-".$FieldName[$i]."]","",$subject);
			$subject2=str_replace("[N-".$FieldName[$i]."]","",$subject2);
		} 
	}

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--mailto:".$FieldValue[3]."-->";
	$mailto=$FieldValue[3];
	$mailto="h.tsurumi@ms123.co.jp";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	//mb_send_mail(SENDER_EMAIL, $subject2, $MailBody2, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
}

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

?>