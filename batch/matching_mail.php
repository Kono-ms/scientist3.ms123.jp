<?php

// 新着マッチングメール送信バッチ

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_o2/config.php';

set_time_limit(7200);

// 2020.12.01 yamamoto エラー捕捉
require_once('../handler.php');
try {

echo("batch start\n");

//データベース接続
ConnDB();
//メイン処理
Main();

echo("batch end\n");

}
catch(\Throwable $e) {
	$dir = '../logs/';
	if(!file_exists($dir)) {
		mkdir($dir, 0777, true);
	}
	$msg = str_replace("\n", " ", $e->getMessage());
	error_log(date('Y-m-d H:i:s') . ' ; ' . rtrim($msg) . "\n", 3, $dir . 'error_' . date('Ymd') . '.log');
}

//=========================================================================================================
//名前 Main関数
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function Main()
{

	eval(globals());

	// マッチングリスト取得
	$StrSQL = "
		SELECT
			DAT_MATCH.MID1,
			DAT_MATCH.MID2,
			DAT_MATCH.POINT,
			DAT_M1.M1_DVAL01,
			DAT_M2.M2_DVAL01,
			DAT_O1.O1_DVAL01,
			DAT_O2.O2_DVAL01,
			DAT_M1.EMAIL AS M1_EMAIL,
			DAT_M2.EMAIL AS M2_EMAIL
		FROM
			DAT_MATCH
			JOIN DAT_M1
				ON DAT_MATCH.MID1 = DAT_M1.MID
			JOIN DAT_M2
				ON DAT_MATCH.MID2 = DAT_M2.MID
			JOIN DAT_O1
				ON DAT_MATCH.MID1 = DAT_O1.MID
			JOIN DAT_O2
				ON DAT_MATCH.MID2 = DAT_O2.MID
		WHERE
			CAST(DAT_MATCH.POINT AS SIGNED) >= 50
			AND TO_DAYS(DAT_MATCH.NEWDATE) = TO_DAYS(DATE_SUB(NOW(), INTERVAL 1 DAY))
			AND NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = DAT_MATCH.MID1 and DAT_BL.MID2 = DAT_MATCH.MID2)
	";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$mail_data = array();
	// MID1とMID2の双方で送信リストを作成する
	while ($item = mysqli_fetch_assoc($rs)) {
		// MID1
		if(!isset($mail_data[$item['MID1']])) {
			$mail_data[$item['MID1']] = array('EMAIL' => $item['M1_EMAIL'], 'M_DVAL01' => $item['M1_DVAL01'], 'CHILD' => array());
		}
		$mail_data[$item['MID1']]['CHILD'][$item['MID2']] = array(
			'M_DVAL01' => $item['M2_DVAL01'],
			'O_DVAL01' => $item['O2_DVAL01'],
			'POINT' => $item['POINT']
		);
		// MID2
		if(!isset($mail_data[$item['MID2']])) {
			$mail_data[$item['MID2']] = array('EMAIL' => $item['M2_EMAIL'], 'M_DVAL01' => $item['M2_DVAL01'], 'CHILD' => array());
		}
		$mail_data[$item['MID2']]['CHILD'][$item['MID1']] = array(
			'M_DVAL01' => $item['M1_DVAL01'],
			'O_DVAL01' => $item['O1_DVAL01'],
			'POINT' => $item['POINT']
		);
	}

	if(count($mail_data) > 0) {
		//var_dump($mail_data);
		SendMail($mail_data);
	}

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($mail_data)
{

	eval(globals());

	//$fp="mail.txt";
	//$MailBody0=@file_get_contents($fp);
	$maildata = GetMailTemplate('新着マッチング');
	$MailBody0 = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	foreach($mail_data as $key => $val) {
		$MailBody = $MailBody0;

		//$to = $val['EMAIL'];
		//$to = 'info@msc-dev.com';
		$to = '197583@gmail.com';

		$MailBody=str_replace("[M_DVAL01]",$val['M_DVAL01'],$MailBody);
		$MailBody=str_replace("[COUNT]",count($val['CHILD']),$MailBody);

		$str = '';
		foreach($val['CHILD'] as $key2 => $val2) {

			$str .= $val2['O_DVAL01'] . '　【' . $val2['M_DVAL01'] . '】　マッチ率：' . $val2['POINT'] . '％' . "\n";
		}
		$MailBody=str_replace("[MATCH_LIST]",$str,$MailBody);

		//$subject = "【Scientist3】新着のマッチングデータがあります";

		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
		//mb_send_mail($to, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3事務局","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 

		mb_send_mail($to, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		mb_send_mail("info@msc-dev.com", $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

		echo("send mail: " . $to . "\n");
	}
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
