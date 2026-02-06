<?php
require "../config.php";
require "../base.php";
require "../common.php";

$base_date=date("Y-m-d H:i:s",strtotime("-5 minute"));
//$base_date=date("Y-m-d H:i:s",strtotime("-10 day"));
echo "現在: ".date("Y-m-d H:i:s")."<br>\n";
echo "閾値: ".$base_date."<br><br>\n\n";

//DAT_MESSAGEでサプライヤー送信の未読メッセージで素チャットを探す。
//※ETC03に値が入ってない場合は素チャット
//※ETC03が空なので、研究者のMIDはAIDから抽出するしかない。
//【DAT_MESSAGE】
//RID: 送信者のMID
//ETC02: shodan_id
//ETC03: 読む人のMID（空の場合があり。素チャット送信の場合）
//ETC04: filestatus id（空の場合があり。素チャット送信の場合）
$StrSQL="SELECT * from DAT_MESSAGE WHERE (NOREAD <=> NULL OR NOREAD='') ";
$StrSQL.="AND RID LIKE 'M1%' ";
$StrSQL.="AND (ETC03 <=> NULL OR ETC03='') ";
$StrSQL.="ORDER BY ID desc";
$rs=mysqli_query(ConnDB(),$StrSQL);
//echo "StrSQL: ".$StrSQL."<br><br>\n\n";

while($item=mysqli_fetch_assoc($rs)){
	
	if( $item["ETC02"]=="" || is_null($item["ETC02"]) ){
		continue;
	}

	//キャンセルになってるものを除外
	$StrSQL="SELECT * from DAT_SHODAN WHERE ID='".$item["ETC02"]."' AND STATUS='キャンセル'";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$num2=mysqli_num_rows($rs2);
	if($num2!=0){
		continue;
	}

	//メール送信を1回もしてないものを探す
	if( !($item["SENDMAIL"]=="" || is_null($item["SENDMAIL"])) ){
		continue;
	}

	//データ作成日時が指定期間より前の物を探す
	$newdate=$item["NEWDATE"];
	if(strtotime($newdate) >= strtotime($base_date)){
		continue;
	}


	//メール送信
	if( $item["AID"]=="" || is_null($item["AID"]) ){
		continue;
	}
	$mid2=explode("-", $item["AID"])[1];

	//echo "aid: ".$item["AID"].", mid2: $mid2<br>";
	$StrSQL="SELECT * from DAT_M2 WHERE MID='".$mid2."' limit 1";
	$rs4=mysqli_query(ConnDB(),$StrSQL);
	$item4=mysqli_fetch_assoc($rs4);
	$email2=$item4["EMAIL"];
	if($email2=="" || is_null($email2)){
		continue;
	}

	echo "未読： ".$item["ID"].": ".$item["NEWDATE"].": shodan_id-".$item["ETC02"].": MessageFrom-".$item["RID"].": MessageTo-".$mid2.": EMAIL-".$email2."<br>\n";
	
	SendMail($item4,$item["RID"],$item["ETC02"]);


	//メール送信フラグをたてる
	$StrSQL="UPDATE DAT_MESSAGE ";
	$StrSQL.=" SET SENDMAIL = '".date('Y/m/d H:i:s')."' ";
	$StrSQL.=" WHERE ID = '".$item["ID"]."'";
	//echo "StrSQL: ".$StrSQL."<br>";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		continue;
	}

}

echo "---------------------------------<br><br>\n\n";







//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($item,$mid1,$shodan_id)
{
	$maildata = GetMailTemplateCron('サプライヤーから研究者にメッセージ送信(M2)');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	foreach ($item as $key => $val) {
		$MailBody=str_replace("[".$key."]", $val, $MailBody);
		$subject=str_replace("[".$key."]", $val, $subject);
	}

	$StrSQL="SELECT * from DAT_M1 WHERE MID='".$mid1."' limit 1";
	$rs_m1=mysqli_query(ConnDB(),$StrSQL);
	$item_m1=mysqli_fetch_assoc($rs_m1);
	//echo "mid2:$mid1<br>";

	foreach ($item_m1 as $key => $val) {
		$MailBody=str_replace("[".$key."]", $val, $MailBody);
		$subject=str_replace("[".$key."]", $val, $subject);
	}

	$MailBody=str_replace("[SHODAN_ID]", $shodan_id, $MailBody);
	$subject=str_replace("[SHODAN_ID]", $shodan_id, $subject);

	//var_dump($MailBody);
	//echo "<br><br>";
	$mailto=$item["EMAIL"];
	//echo "mailto:$mailto<br>";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}

//=========================================================================================================
//名前 
//機能 メールテンプレートを取得して定数を埋め込む
//引数 
//戻値 
//=========================================================================================================
function GetMailTemplateCron($mailname)
{
	$maildata = array();

	$StrSQL="SELECT TITLE,BODY from DAT_MAIL where MAILNAME='MAILNAME:".$mailname."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
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