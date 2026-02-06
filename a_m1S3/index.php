<?php

session_start();
require "../config.php";
require "../base_a.php";
require './config.php';

// ini_set( 'display_errors', 1 );se
require("../crawl/simple_html_dom.php");
require("../crawl/func1.php");

require("./func_export.php");

//echo等の出力のバッファリングを無効にし則出力
ini_set('max_execution_time', 0);
set_time_limit(0);
//ini_set('memory_limit', '1G');
ini_set('memory_limit', '-1');

// set_time_limit(7200);
define("MAX_ELM", 500);


define("LABLE_EXPORT_LIST", 
"1. Basic Information::" . 
"サプライヤーID::" . 
"2. Profile::" . 
"Company name::" . 
"First name::" . 
"Last name::" . 
"Job title::" . 
"CEO / Exective Director::" . 
"Main Phone Number::" . 
"Banking Details::" .
"Billing Address 1::" .
"Organization Name::" . 
"Name::" . 
"Attention (Optional)::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"Zip/Postal Code::" . 
"Country::" . 
"Billing Address 2::" .
"Organization Name::" . 
"Name::" . 
"Attention (Optional)::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"Zip/Postal Code::" . 
"Country::" . 
"Billing Address 3::" .
"Organization Name::" . 
"Name::" . 
"Attention (Optional)::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"Zip/Postal Code::" . 
"Country::" . 
"Banking Details::".
"Currency::" . 
"Bank name::" . 
"Account name::" . 
"Bank branch::" . 
"Bank address::" . 
"Account number or IBAN::" . 
"ABA(Routing) number::" . 
"SWIFT/BIC code::" . 
"Intermediary Bank name::" . 
"Intermediary Bank branch::" . 
"Intermediary Bank address::" . 
"適格請求書発行事業者登録番号（Japanese Companies Only）::" . 
"File::" . 
"Banking Details::" . 
"General Accounting Contact::" . 
"Name::" . 
"Email::" . 
"Title::" . 
"Use different contact information for purchase orders::" . 
"Remittance Contact::" . 
"Name::" . 
"Email::" . 
"Title::" . 
"Tax ID::" . 
"D-U-N-S::" . 
"Tax forms::" . 
"Form Type::" 
);
define("VALUE_EXPORT_LIST", 
"::" . 
"MID::" . 
"::" . 
"M1_DVAL01::" . 
"M1_DVAL22::" . 
"M1_DVAL23::" . 
"M1_DVAL24::" . 
"M1_ETC99::" . 
"M1_DVAL15::" . 
"::" . 
"::" . 
"M1_ETC31::" . 
"M1_ETC32::" . 
"M1_ETC33::" . 
"M1_ETC34::" . 
"M1_ETC35::" . 
"M1_ETC36::" . 
"M1_ETC38::" . 
"M1_DSEL10::" . 
"::" . 
"M1_ETC106::" . 
"M1_ETC107::" . 
"M1_ETC108::" . 
"M1_ETC109::" . 
"M1_ETC110::" . 
"M1_ETC111::" . 
"M1_ETC113::" . 
"M1_ETC114::" . 
"::" . 
"M1_ETC115::" . 
"M1_ETC116::" . 
"M1_ETC117::" . 
"M1_ETC118::" . 
"M1_ETC119::" . 
"M1_ETC120::" . 
"M1_ETC122::" . 
"M1_ETC123::" . 
"::" . 
"M1_ETC40::" . 
"M1_ETC41::" . 
"M1_ETC42::" . 
"M1_ETC100::" . 
"M1_ETC101::" . 
"M1_ETC43::" . 
"M1_ETC44::" . 
"M1_ETC45::" . 
"M1_ETC102::" . 
"M1_ETC103::" . 
"M1_ETC104::" . 
"M1_ETC105::" . 
"M1_ETC46::" . 
"::" . 
"::" . 
"M1_ETC47::" .
"M1_ETC48::" .
"M1_ETC49::" .
"M1_DCHK01::" .
"::" . 
"M1_DRDO02::" .
"M1_DRDO03::" .
"M1_DRDO04::" .
"M1_DRDO05::" .
"M1_DRDO07::" .
"::" . 
"M1_DRDO08"
);
define("LABLE_LIST", 
"MID::" . 
"メールアドレス::" . 
"パスワード::" . 
"公開フラグ::" . 
"登録日時::" . 
"更新日時::" . 
"利用規約に同意した日時（仮登録）::" . 
"Company name::" . 
"First name::" . 
"Last name::" . 
"Job title::" . 
"Phone::" . 
"Country::" . 
"Time zone::" . 
"Website::" . 
"Phone Number::" . 
"Number of Employees::" . 
"Type of Company::" . 
"Year Established::" . 
"Short Description::" . 
"Keywords (comma separated)::" . 
"Laboratories (one per line)::" . 
"logo::" . 
"BILL TO::" . 
"ship to::" . 
"Service Website::" . 
"Service introduction::" . 
"Service Website::" . 
"Service introduction::" . 
"Service Website::" . 
"Service introduction::" . 
"Service Website::" . 
"Service introduction::" . 
"Service Website::" . 
"Service introduction::" . 
"Organization name::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"ZIP/Postal code::" . 
"Country::" . 
"Organization name::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"ZIP/Postal code::" . 
"Country::" . 
"Organization name::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"ZIP/Postal code::" . 
"Country::" . 
"Organization name::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"ZIP/Postal code::" . 
"Country::" . 
"Organization name::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"ZIP/Postal code::" . 
"Country::" . 
"Organization name::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"ZIP/Postal code::" . 
"Country::" . 
"Organization Name::" . 
"Name/Care of (Optional)::" . 
"Attention (Optional)::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Region::" . 
"State/Province::" . 
"Zip/Postal Code::" . 
"Country::" . 
"Country::" . 
"Currency::" . 
"Bank name::" . 
"Account name::" . 
"Account number::" . 
"Routing Number (ACH and Checks) / ABA  (Domestic Wires)::" . 
"Additional Account/Bank Identifiers::" . 
"Please upload supporting documentation for this banking::" . 
"General Accounting Contact: Name::" . 
"General Accounting Contact: Email::" . 
"General Accounting Contact: Title::" . 
"Use different contact information for purchase orders::" . 
"Remittance Contact: Name::" . 
"Remittance Contact: Email::" . 
"Remittance Contact: Title::" . 
"Tax ID::" . 
"D-U-N-S::" . 
"Form Type::" . 
"Upload a new tax form::" . 
"Agree1::" . 
"Agree2::" . 
"legal Entity Name::" . 
"Job title::" . 
"Signature::" . 
"Signed On::" . 
"1. What is your organization name?::" . 
"2. Location::" . 
"3. Contact name::" . 
"4. Contact phone number::" . 
"5. Contact email address::" . 
"Are you willing to share basic financial information on your::" . 
"Please provide an explanation as to why this information will::" . 
"1. Have you had any significant incidents relating to breach of::" . 
"2. Do you have policies and/or processes that relate to the::" . 
"3. Do you at least once a year communicate and train your::" . 
"4. Is there a formal process to report and manage::" . 
"1. Do you or do you expect to handle personal data in regards to::" . 
"2. Will you process customer's Personal Data for your own::" . 
"3. Does your company have an established governance structure in::" . 
"4. Does your company have a privacy policy in place which your::" . 
"5. Does your company have a process in place to ensure that any::" . 
"6. Does your company provide regular training and awareness to::" . 
"7. In the last five years has your company suffered a loss, leak::" . 
"8. Has your company established an incident response plan that::" . 
"9. Are there access controls in place to ensure that only the::" . 
"10. If any Personal Data be maintained on your company's::" . 
"1. Has your company had a breach of local Employment law/human::" . 
"2. Do you have formal process/policies or recognised practices::" . 
"3. Do you have a formal process/policies or recognised practices::" . 
"4. Do you use or have you ever used forced, bonded, indentured::" . 
"5. Do you have a formal policy/contract to ensure you pay all::" . 
"6. Do you have a formal policy or recognised practice which::" . 
"1. Does the organization identify, document and implement::" . 
"2. Is user access to the newtwork services monitored and::" . 
"3. Does the organization have a clear policy governing the use::" . 
"4. Does the organization have a policy governing the destruction::" . 
"5. Does the organization identify and document all relevant::" . 
"6. Have information security roles (capabilities and decision::" . 
"7. Are security education, trainng and awareness programs::" . 
"8. Does the organization's top management establish an::" . 
"9. Does the organization perform information security risk::" . 
"10. Does the organization conduct internal audits at planned::" . 
"11. Does the organization adopt policies and supporting security::" . 
"12. Are privacy risk assessments performed periodically?::" . 
"13. Is a firewall installed at all connections from an internal::" . 
"14. Does the organization regularly test the backup copies of::" . 
"15. Is electronic data encrypted at rest (network storage,::" . 
"16. Is electronic data encrypted in transit (TLS enabled for::" . 
"17. Does the organization log the activities performed by system::" . 
"18. Does the organization implement procedures to control the::" . 
"19. Has the organization been accredited by any external::" . 
"20. Does the organization implement physical access to controls::" . 
"21. Does the organization have a policy to notify Scientist.com::" . 
"ファイルのタイトル１::" . 
"ファイル１::" . 
"ファイルのタイトル２::" . 
"ファイル２::" . 
"ファイルのタイトル３::" . 
"ファイル３::" . 
"ファイルのタイトル４::" . 
"ファイル４::" . 
"ファイルのタイトル５::" . 
"ファイル５::" . 
"ファイルのタイトル６::" . 
"ファイル６::" . 
"ファイルのタイトル７::" . 
"ファイル７ ::" . 
"ファイルのタイトル８::" . 
"ファイル８ ::" . 
"ファイルのタイトル９::" . 
"ファイル９ ::" . 
"ファイルのタイトル１０::" . 
"ファイル１０ ::" . 
"Youtube動画::" . 
"カテゴリー(第1階層)::" . 
"カテゴリー(第2階層)::" . 
"カテゴリー(第3階層)::" . 
"カテゴリー(第4階層)::" . 
"カテゴリー(第1階層)::" . 
"カテゴリー(第2階層)::" . 
"カテゴリー(第3階層)::" . 
"カテゴリー(第4階層)::" . 
"カテゴリー(第1階層)::" . 
"カテゴリー(第2階層)::" . 
"カテゴリー(第3階層)::" . 
"カテゴリー(第4階層)::" . 
"カテゴリー(第1階層)::" . 
"カテゴリー(第2階層)::" . 
"カテゴリー(第3階層)::" . 
"カテゴリー(第4階層)::" . 
"検索ワード::" . 
"通貨単位::" . 
"登録状態::" . 
"登録状態補足::" . 
"Explanation::" . 
"Explanation::" . 
"Explanation::" . 
"Organization Name::" . 
"Name/Care of (Optional)::" . 
"Attention (Optional)::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Region::" . 
"State/Province::" . 
"Zip/Postal Code::" . 
"Country::" . 
"Organization Name::" . 
"Name/Care of (Optional)::" . 
"Attention (Optional)::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Region::" . 
"State/Province::" . 
"Zip/Postal Code::" . 
"Country::" . 
"Bank branch::" . 
"Bank address::" . 
"Intermediary Bank name::" . 
"Intermediary Bank branch::" . 
"Intermediary Bank address::" . 
"適格請求書発行事業者登録番号（Japanese Companies Only）::" . 
"Youtube動画::" . 
"Organization name::" . 
"Address Line 1::" . 
"Address Line 2::" . 
"City/Province::" . 
"ZIP/Postal code::" . 
"Country::" . 
"I have read and agree to the policies and procedures::" . 
"CEO / Exective Director::" . 
"アカウント情報（社内メモ）"
);
define("VALUE_LIST", 
"MID::" . 
"EMAIL::" . 
"PASS::" . 
"ENABLE::" . 
"NEWDATE::" . 
"EDITDATE::" . 
"M1_DTXT08::" . 
"M1_DVAL01::" . 
"M1_DVAL22::" . 
"M1_DVAL23::" . 
"M1_DVAL24::" . 
"M1_DVAL07::" . 
"M1_DVAL04::" . 
"M1_DSEL03::" . 
"M1_DVAL14::" . 
"M1_DVAL15::" . 
"M1_DVAL16::" . 
"M1_DVAL17::" . 
"M1_DVAL18::" . 
"M1_DTXT03::" . 
"M1_DTXT04::" . 
"M1_DTXT05::" . 
"M1_DFIL02::" . 
"M1_DVAL06::" . 
"M1_DVAL25::" . 
"M1_ETC08::" . 
"M1_ETC91::" . 
"M1_ETC27::" . 
"M1_ETC92::" . 
"M1_ETC28::" . 
"M1_ETC93::" . 
"M1_ETC29::" . 
"M1_ETC94::" . 
"M1_ETC30::" . 
"M1_ETC95::" . 
"M1_DTXT24::" . 
"M1_DTXT12::" . 
"M1_DTXT15::" . 
"M1_DTXT18::" . 
"M1_DTXT21::" . 
"M1_DSEL04::" . 
"M1_DTXT10::" . 
"M1_DTXT13::" . 
"M1_DTXT16::" . 
"M1_DTXT19::" . 
"M1_DTXT22::" . 
"M1_DSEL05::" . 
"M1_DTXT11::" . 
"M1_DTXT14::" . 
"M1_DTXT17::" . 
"M1_DTXT20::" . 
"M1_DTXT23::" . 
"M1_DSEL06::" . 
"M1_DTXT25::" . 
"M1_DTXT28::" . 
"M1_ETC10::" . 
"M1_ETC21::" . 
"M1_ETC24::" . 
"M1_DSEL07::" . 
"M1_DTXT26::" . 
"M1_DTXT29::" . 
"M1_ETC11::" . 
"M1_ETC22::" . 
"M1_ETC25::" . 
"M1_DSEL08::" . 
"M1_DTXT27::" . 
"M1_DTXT30::" . 
"M1_ETC12::" . 
"M1_ETC23::" . 
"M1_ETC26::" . 
"M1_DSEL09::" . 
"M1_ETC31::" . 
"M1_ETC32::" . 
"M1_ETC33::" . 
"M1_ETC34::" . 
"M1_ETC35::" . 
"M1_ETC36::" . 
"M1_ETC37::" . 
"M1_ETC38::" . 
"M1_DSEL10::" . 
"M1_ETC39::" . 
"M1_ETC40::" . 
"M1_ETC41::" . 
"M1_ETC42::" . 
"M1_ETC43::" . 
"M1_ETC44::" . 
"M1_ETC45::" . 
"M1_ETC46::" . 
"M1_ETC47::" . 
"M1_ETC48::" . 
"M1_ETC49::" . 
"M1_DCHK01::" . 
"M1_DRDO02::" . 
"M1_DRDO03::" . 
"M1_DRDO04::" . 
"M1_DRDO05::" . 
"M1_DRDO07::" . 
"M1_DRDO08::" . 
"M1_DRDO06::" . 
"M1_ETC77::" . 
"M1_ETC78::" . 
"M1_ETC79::" . 
"M1_ETC80::" . 
"M1_ETC81::" . 
"M1_ETC96::" . 
"M1_MSEL01::" . 
"M1_MSEL02::" . 
"M1_MSEL03::" . 
"M1_MSEL04::" . 
"M1_MSEL05::" . 
"M1_MRDO01::" . 
"M1_MSEL06::" . 
"M1_MRDO02::" . 
"M1_MRDO03::" . 
"M1_MRDO04::" . 
"M1_MRDO05::" . 
"M1_MRDO06::" . 
"M1_MRDO07::" . 
"M1_MRDO08::" . 
"M1_MRDO09::" . 
"M1_MRDO10::" . 
"M1_MSEL07::" . 
"M1_MSEL08::" . 
"M1_MSEL09::" . 
"M1_MSEL10::" . 
"M1_DVAL13::" . 
"M1_ETC50::" . 
"M1_ETC51::" . 
"M1_ETC52::" . 
"M1_ETC53::" . 
"M1_ETC54::" . 
"M1_ETC55::" . 
"M1_ETC56::" . 
"M1_ETC57::" . 
"M1_ETC58::" . 
"M1_ETC59::" . 
"M1_ETC60::" . 
"M1_ETC61::" . 
"M1_ETC62::" . 
"M1_ETC63::" . 
"M1_ETC64::" . 
"M1_ETC65::" . 
"M1_ETC66::" . 
"M1_ETC67::" . 
"M1_ETC68::" . 
"M1_ETC69::" . 
"M1_ETC70::" . 
"M1_ETC71::" . 
"M1_ETC72::" . 
"M1_ETC73::" . 
"M1_ETC74::" . 
"M1_ETC75::" . 
"M1_ETC76::" . 
"M1_DTXT09::" . 
"M1_MCHK01::" . 
"M1_ETC82::" . 
"M1_DFIL04::" . 
"M1_ETC83::" . 
"M1_DFIL05::" . 
"M1_ETC84::" . 
"M1_DFIL06::" . 
"M1_ETC85::" . 
"M1_DFIL07::" . 
"M1_ETC86::" . 
"M1_DFIL08::" . 
"M1_ETC87::" . 
"M1_DFIL09::" . 
"M1_ETC88::" . 
"M1_DFIL10::" . 
"M1_ETC89::" . 
"M1_ETC04::" . 
"M1_ETC90::" . 
"M1_ETC05::" . 
"M1_DTXT07::" . 
"M1_ETC17::" . 
"M1_ETC18::" . 
"M1_ETC19::" . 
"M1_ETC20::" . 
"M1_DRDO09::" . 
"M1_DRDO10::" . 
"M1_DCHK02::" . 
"M1_DCHK03::" . 
"M1_DCHK04::" . 
"M1_DCHK05::" . 
"M1_DCHK06::" . 
"M1_DCHK07::" . 
"M1_DCHK08::" . 
"M1_DCHK09::" . 
"M1_DCHK10::" . 
"M1_ETC13::" . 
"M1_ETC09::" . 
"M1_ETC03::" . 
"M1_DRDO01::" . 
"M1_ETC14::" . 
"M1_ETC125::" . 
"M1_ETC126::" . 
"M1_ETC127::" . 
"M1_ETC106::" . 
"M1_ETC107::" . 
"M1_ETC108::" . 
"M1_ETC109::" . 
"M1_ETC110::" . 
"M1_ETC111::" . 
"M1_ETC112::" . 
"M1_ETC113::" . 
"M1_ETC114::" . 
"M1_ETC115::" . 
"M1_ETC116::" . 
"M1_ETC117::" . 
"M1_ETC118::" . 
"M1_ETC119::" . 
"M1_ETC120::" . 
"M1_ETC121::" . 
"M1_ETC122::" . 
"M1_ETC123::" . 
"M1_ETC100::" . 
"M1_ETC101::" . 
"M1_ETC102::" . 
"M1_ETC103::" . 
"M1_ETC104::" . 
"M1_ETC105::" . 
"M1_ETC15::" . 
"M1_ETC128::" . 
"M1_ETC129::" . 
"M1_ETC130::" . 
"M1_ETC131::" . 
"M1_ETC132::" . 
"M1_ETC133::" . 
"M1_ETC07::" . 
"M1_ETC99::" . 
"M1_ETC98"
);



set_time_limit(7200);

//データベース接続
//ConnDB();
//メイン処理
Main();

//=========================================================================================================
//名前 Main関数
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function Main()
{

	eval(globals());

	if($_POST['mode']==""){
		$mode=$_GET['mode'];
		$sort=$_GET['sort'];
		$word=$_GET['word'];
		$key=$_GET['key'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
		$token=$_GET['token'];
		$chk=$_GET['chk'];
		$allenable=$_GET['ALLENABLE'];
		$version=$_GET['version'];
		
		// $expo="";
		// if(strstr($_GET["expo"],"\t") == true) {
		// 	$expo=htmlspecialchars($_GET["expo"]);
		// } else {
		// 	for ($j=0; $j<count($_GET["expo"]); $j=$j+1) {
		// 		if ($j!=0) {
		// 			$expo=$expo."\t";
		// 		}
		// 		$expo=$expo.$_GET["expo"][$j];
		// 	}
		// }

		
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$chk=$_POST['chk'];
		$allenable=$_POST['ALLENABLE'];
		$version=$_POST['version'];

		// $expo="";
		// if(strstr($_POST["expo"],"\t") == true) {
		// 	$expo=htmlspecialchars($_POST["expo"]);
		// } else {
		// 	for ($j=0; $j<count($_POST["expo"]); $j=$j+1) {
		// 		if ($j!=0) {
		// 			$expo=$expo."\t";
		// 		}
		// 		$expo=$expo.$_POST["expo"][$j];
		// 	}
		// }
	}

	if ($mode==""){
		$mode="list";
		$_SESSION['a_m1_expo_ids']="";//CSV出力の選択ID（カンマ区切り）
	}

	if($mode=="expo_chk"){
		$tmps=explode(",",$_SESSION['a_m1_expo_ids']);
		if($word=="on"){
			if (in_array($key, $tmps)===false) {
				array_push($tmps,$key);
			}
			//念の為
			$newtmp=array();
			for ($i=0; $i<=count($tmps); $i++) {
				$id=$tmps[$i];
				if($id!="" ){
					array_push($newtmp,$id);
				}
			}
			$tmps=$newtmp;
		} else {
			$newtmp=array();
			for ($i=0; $i<=count($tmps); $i++) {
				$id=$tmps[$i];
				if($id!="" && $id!=$key){
					array_push($newtmp,$id);
				}
			}
			$tmps=$newtmp;
		}
		$_SESSION['a_m1_expo_ids']=implode(",",$tmps);
		exit;
	}
// echo "<!--a_m1_expo_ids:".$_SESSION['a_m1_expo_ids']."-->";

	//クロールする
	if($mode=="crawl"){

		if($_SESSION['token']==$token){

			$StrSQL="SELECT * from DAT_M1 where ID='".$key."'";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);
			$mid=$item['MID'];
			$url=$item['M1_ETC08'];

			$url_top=$url;
			$url_top=trim($url_top);
			$search_word="";
			$errmsg="";
			if( !checkURL_crawl($url_top) ){
				$errmsg="不正なurlです";
				$search_word="";
			}

			$doc_root=make_docRoot($url_top);
			if($doc_root==""){
				$errmsg="不正なurlです";
				$search_word="";
			}

			define("DOC_ROOT", $doc_root);
			if($errmsg==""){



				//初期化
				$StrSQL=" UPDATE DAT_M1 SET M1_ETC09 = 'クローリング中。。'";
				$StrSQL.=" WHERE ID = '".$mid."'";
				// echo "<!--".$StrSQL."-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					var_dump("UPDATErr1:".$StrSQL);
					// file_put_contents($tmp_filename, "UPDATErr1:".$StrSQL, FILE_APPEND);
					// die;
				}


				$tmp_filename=make_filename($mid);
	// echo "<!--tmp_filename:".$tmp_filename."-->";		

				$exec_file="../crawl/crawl_proc.php";
				$cmd = "nohup php -c '' '$exec_file' '$url_top' '$doc_root' '$tmp_filename' '$mid' > nohup.dat &";
				exec($cmd);
	// echo "<!--cmd:".$cmd."-->";
	// 			$disp_path="https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/".$tmp_filename;
	// echo "<!--disp_path:".$disp_path."-->";
			} else {
				//初期化
				$StrSQL=" UPDATE DAT_M1 SET M1_ETC09 = '".$errmsg."'";
				$StrSQL.=" WHERE MID = '".$mid."'";
				// echo "<!--".$StrSQL."-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					var_dump("UPDATErr1:".$StrSQL);
					// file_put_contents($tmp_filename, "UPDATErr1:".$StrSQL, FILE_APPEND);
					// die;
				}
			}
		} else {
			echo "<!--token不一致-->";
			echo "<!--SESSION[token]:".$_SESSION['token']."-->";
			echo "<!--token         :".$token."-->";

		}



		$mode="list";
	}


	// ここでカテゴリーデータをロードして配列生成
	$cate_list = array();
	for($i = 1; $i <= 11; $i++) {
	$fp = fopen(__dir__ . '/../category_data/cate' . $i . '.csv', 'r');
	while ($row = fgetcsv($fp)) {
		if($row[0] == '第一階層') {
			continue;
		}
		if($row[0] != '') {
			$cate1 = $row[0];
			$cate_list[$cate1] = array();
		}
		if($row[1] != '') {
			$cate2 = $row[1];
			$cate = explode("\n", $cate2);
			foreach($cate as $val) {
				$cate_list[$cate1][$val] = array();
			}
		}
		if($row[2] != '') {
			$cate3 = $row[2];
			$cate = explode("\n", $cate3);
			foreach($cate as $val) {
				$cate_list[$cate1][$cate2][$val] = array();
			}
		}
		if($row[3] != '') {
			$cate4 = $row[3];
			$cate = explode("\n", $cate4);
			foreach($cate as $val) {
				$cate_list[$cate1][$cate2][$cate3][$val] = 1;
			}
		}
	}
	fclose($fp);
	}
	
	switch ($mode){
		case "enable":
			$enable="ENABLE:公開中";
			if($allenable=="ALLENABLE:公開中"){
				$enable="ENABLE:公開中";
			} else {
				$enable="ENABLE:非公開";
			}
			if(is_array($chk)){
				for($i=0; $i<count($chk); $i++){
					$StrSQL=" UPDATE DAT_M1 SET ENABLE = '".$enable."'";
					$StrSQL.=" WHERE ID = '".$chk[$i]."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} 
			} else {
				if($chk!=""){
					$StrSQL=" UPDATE DAT_M1 SET ENABLE = '".$enable."'";
					$StrSQL.=" WHERE ID = '".$chk."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				}
			}

			$mode="list";
			if ($page==""){
				$page=1;
			} 
			if ($sort==""){
				$sort=1;
			} 
			break;
		case "new":
			InitData();
			break;
		case "edit":
			LoadData($key,$version);
			break;
		case "saveconf":
			LoadData($key);
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "deleteconf":
			LoadData($key);
			break;
		case "save":
			// CSRFチェック OKならDB書き込み
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);
				$msg=ErrorCheck();
				if ($msg==""){

					//変更前情報
					$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
					$rs=mysqli_query(ConnDB(),$StrSQL);
					$itemBefore = mysqli_fetch_assoc($rs);

					$FieldValue[97]=date("Y/m/d H:i:s");

					SaveData($key);
					if($FieldValue[1]!="" && !is_null($FieldValue[1])){
						SaveData_history($key);
						//Reovokeリクエストの処理を運営が完了したときにメール送信
						//登録状態が「審査依頼」「要再審査」「本登録」「登録変更審査中」の時に以下の項目をすべて空白に変更して保存した時
						//legal Entity Name: M1_ETC79
						//Job title: M1_ETC80
						//Signature: M1_ETC81
						//Signed On: M1_ETC77, M1_ETC78
						SendMail_revoke($key);
					}
					
					if($itemBefore["M1_DRDO01"]!=$FieldValue[35]){
						// こっちは通知不要とのこと
						//SendMail($key);

						SendMailStatus($key,$FieldValue[35]);
					

					}
					
					$mode="list";
					if ($page==""){
						$page=1;
					} 
				}
			}
			break;
		case "delete":
			// CSRFチェック OKならDB削除
			if ($_SESSION['token']==$token) {
				RequestData($obj,$a,$b,$key,$mode);
				DeleteData($key);
			}
			$mode="list";
			if ($page==""){
				$page=1;
			} 
			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			$mode="edit";
			break;
		case "disp":
			LoadData($key);
			break;
		case "list":
			if ($page==""){
				$page=1;
			} 
			if ($sort==""){
				$sort=1;
			} 
			break;
		case "export":
			ExportData($expo);
			exit;

		case "import":
			ImportData($obj,$a,$b,$key,$mode);
			$mode="list";
			break;
	} 

	// 通貨単位
	$FieldParam[100]="";
	$StrSQL="SELECT * FROM DAT_CURRENCY order by id ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	while ($item = mysqli_fetch_assoc($rs)) {
		if($FieldParam[100]!=""){
			$FieldParam[100].="::";
		}
		$FieldParam[100].=$item["UNIT"];
	}




	DispData($mode,$sort,$word,$key,$page,$lid,$token,$version,$expo);

	return $function_ret;
} 


//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMailStatus($key,$status)
{

	eval(globals());


	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

		
	//登録状態のフラグが「本登録」になったタイミング
	if($status=="M1_DRDO01:本登録"){
		$maildata = GetMailTemplate('サプライヤー本登録完了(M1-1)');
	} else

	//登録状態が「要再審査」で保存された際
	if($status=="M1_DRDO01:要再審査"){
		$maildata = GetMailTemplate('サプライヤー要再審査(M1-1)');
	} else

	//登録状態が「本登録不可」で保存された際
	if($status=="M1_DRDO01:本登録不可"){
		$maildata = GetMailTemplate('サプライヤー本登録不可(M1-1)');
	} else {
		return;
	}

	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$mailto = $item['EMAIL'];
	
	$MailBody=str_replace("[MID]",$item["MID"],$MailBody);
	$MailBody=str_replace("[M1_DVAL01]",$item["M1_DVAL01"],$MailBody);
	$MailBody=str_replace("[M1_DVAL22]",$item["M1_DVAL22"],$MailBody);
	$MailBody=str_replace("[M1_DVAL23]",$item["M1_DVAL23"],$MailBody);


	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMailStatus_mailto:".$mailto."-->";
// $mailto = "toretoresansan00@gmail.com";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		

}
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($key)
{

	eval(globals());


	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	$maildata = GetMailTemplate('登録状態変更');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$mailto = $item['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	$MailBody=str_replace("[D-NAME]",$item['M1_DVAL01'],$MailBody);
	$MailBody=str_replace("[D-M1_DRDO01]",str_replace("M1_DRDO01:","",$item['M1_DRDO01']),$MailBody);
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--mailto:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		

}

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$version,$expo)
{

	eval(globals());

	//各テンプレートファイル名
	$htmlnew = "edit.html";
	$htmledit = "edit.html";
	$htmlconf = "conf.html";
	$htmlend = "end.html";
	$htmldisp = "disp.html";
	$htmlerr = "edit.html";
	$htmllist = "list.html";

	if ($mode!="list"){

		switch ($mode){
			case "new":
				$filename=$htmlnew;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "edit":
				$filename=$htmledit;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "saveconf":
				$msg=ErrorCheck();
				if ($msg==""){
					$filename=$htmlconf;
					$msg01="保存";
					$msg02="save";
					$errmsg="";
				} else {
					$filename=$htmlerr;
					$msg01=$msg;
					$msg02="";
					$errmsg=$msg;
				} 
				break;
			case "deleteconf":
				$filename=$htmlconf;
				$msg01="削除";
				$msg02="delete";
				$errmsg="";
				break;
			case "save":
				$msg=ErrorCheck();
				if ($msg==""){
					$filename=$htmlend;
					$msg01="保存";
					$msg02="";
					$errmsg="";
				} else {
					$filename=$htmlerr;
					$msg01=$msg;
					$msg02="";
					$errmsg=$msg;
				} 
				break;
			case "delete":
				$filename=$htmlend;
				$msg01="削除";
				$msg02="";
				$errmsg="";
				break;
			case "disp":
				$filename=$htmldisp;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
		} 

		$fp=$DOCUMENT_ROOT.$filename;
		$str=@file_get_contents($fp);

		$str = MakeHTML($str,1,$lid);

		if ($mode=="new"){
			$str=DispParam($str, "NEWDATA");
			$str=DispParamNone($str, "EDITDATA");
		} else {
			$str=DispParamNone($str, "NEWDATA");
			$str=DispParam($str, "EDITDATA");
		} 

		for ($i=0; $i<=$FieldMax; $i=$i+1){
			// Agree2の会社名
			if($FieldName[$i]=="M1_ETC78" || $FieldName[$i]=="M1_ETC77"){
				$FieldParam[$i]=str_replace("[M1_DVAL01]",$FieldValue[5],$FieldParam[$i]);
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul class='mlist25p'>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"".$FieldName[$i]."[]\" value=\"".$FieldName[$i].":".$tmp[$j]."\"><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$tmp=explode("\t",$FieldValue[$i]);
					for ($j=0; $j<count($tmp); $j=$j+1) {
						$str=str_replace("\"".$tmp[$j]."\"","\"".$tmp[$j]."\" checked",$str);
					}
				} 
			}

			if ($FieldAtt[$i]==4){
				if ($FieldValue[$i]==""){
					$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
					$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
				} 

				if(strstr(basename($FieldValue[$i]),"s.gif") == true || $FieldValue[$i]==""){
					$str=DispParamNone($str, $FieldName[$i]);
				} else {
					$str=DispParam($str, $FieldName[$i]);
				} 
			} else {
				if ($FieldValue[$i]==""){
					$str=DispParamNone($str, $FieldName[$i]);
				} else {
					$str=DispParam($str, $FieldName[$i]);
				} 
			} 
			// HTMLエスケープ処理（詳細表示系、HIDDEN値）
			$str=str_replace("[".$FieldName[$i]."]",($FieldValue[$i]),$str);
			$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($FieldName[$i].":","",($FieldValue[$i]))),$str);
			if ($FieldAtt[$i]=="1"){
				$strtmp="";
				$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
				$tmp=explode("::",$FieldParam[$i]);
				for ($j=0; $j<count($tmp); $j=$j+1) {
					if($FieldName[$i] == 'M1_DSEL03') { // datalist
						$strtmp=$strtmp."<option value='".$tmp[$j]."'>".$tmp[$j]."</option>";
					}
					else {
						$strtmp=$strtmp."<option value='".$FieldName[$i].":".$tmp[$j]."'>".$tmp[$j]."</option>";
					}

				}

				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {

					$str=str_replace("'".$FieldValue[$i]."'","'".$FieldValue[$i]."' selected",$str);
				} 
			} 

			if ($FieldAtt[$i]=="2"){
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"radio\" name=\"".$FieldName[$i]."\" value=\"".$FieldName[$i].":".$tmp[$j]."\"><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$str=str_replace("\"".$FieldValue[$i]."\"","\"".$FieldValue[$i]."\" checked",$str);
				} 
			} 

			if ($FieldAtt[$i]=="3"){
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul class='mlist25p'>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"".$FieldName[$i]."[]\" value=\"".$FieldName[$i].":".$tmp[$j]."\"><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$tmp=explode("\t",$FieldValue[$i]);
					for ($j=0; $j<count($tmp); $j=$j+1) {
						$str=str_replace("\"".$tmp[$j]."\"","\"".$tmp[$j]."\" checked",$str);
					}
				} 
			} 

			if (is_numeric($FieldValue[$i])) {
				$str=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$str);
			} else {
				$str=str_replace("[N-".$FieldName[$i]."]","",$str);
			} 
		}



		//バージョン
		// $cnt=0;
		$strtmp="";
		$strtmp=$strtmp."<option value=''>最新</option>";
		$StrSQL="SELECT * from DAT_M1_HISTORY where MID='".$FieldValue[1]."' order by cast(CHANGE_VERSION as signed) desc ";
		$rs=mysqli_query(ConnDB_a(),$StrSQL);
		while ($item = mysqli_fetch_assoc($rs)) {
			$selected="";
			if($version==$item["CHANGE_VERSION"]){
				$selected=" selected ";
			}
			$strtmp=$strtmp."<option value='".$item["CHANGE_VERSION"]."' ".$selected.">".$item["CHANGE_VERSION"]."</option>";

		}

		if($FieldValue[1]=="" || is_null($FieldValue[1])){
			$strtmp="";
		}
		$str=str_replace("[OPT-VERSION]",$strtmp,$str);


		$CHANGE_LOG="";
		if($version==""){
			$StrSQL="SELECT * FROM DAT_M1_HISTORY where MID='".$FieldValue[1]."' order by id desc";
		} else {
			$StrSQL="SELECT * FROM DAT_M1_HISTORY where MID='".$FieldValue[1]."' AND CHANGE_VERSION ='".$version."' order by id desc";
		}
		// echo "<!--".$StrSQL."-->";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$CHANGE_LOG=getChangeLog($item,$item["CHANGE_LOG"]);

		//if($item["CHANGE_VERSION"]==""){
		if($item["CHANGE_VERSION"]=="" || $item["CHANGE_VERSION"]=="1"){
			$CHANGE_LOG="";
		}

		if($FieldValue[1]=="" || is_null($FieldValue[1])){
			$item="";
			$CHANGE_LOG="";
		}

		$str=str_replace("[CHANGE_LOG]",$CHANGE_LOG,$str);

		//echo "change_log:<br>";
		//var_dump($item["CHANGE_LOG"]);
		//echo "<br>";
		$str=DispNew($item,$item["CHANGE_LOG"],$str);


		$str=str_replace("[MSG]",$msg01,$str);
		$str=str_replace("[NEXTMODE]",$msg02,$str);
		if($errmsg<>""){
			$str=str_replace("[ERRMSG]",$errmsg,$str);
			$str=DispParam($str, "ERR");
		} else {
			$str=DispParamNone($str, "ERR");
		}
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);

		// CSRFトークン生成
		// if($token==""){
			$token=htmlspecialchars(session_id()).date("YmdHis") . substr(explode(".", microtime(true))[1], 0, 3);
			$_SESSION['token'] = $token;
		// }
		$str=str_replace("[TOKEN]",$token,$str);

		$str=str_replace("[BASE_URL]",BASE_URL,$str);


	// ここでカテゴリーデータをロードして配列生成
	$cate_list = array();
	for($i = 1; $i <= 11; $i++) {
	$fp = fopen(__dir__ . '/../category_data/cate' . $i . '.csv', 'r');
	while ($row = fgetcsv($fp)) {
		if($row[0] == '第一階層') {
			continue;
		}
		if($row[0] != '') {
			$cate1 = $row[0];
			$cate_list[$cate1] = array();
		}
		if($row[1] != '') {
			$cate2 = $row[1];
			$cate = explode("\n", $cate2);
			foreach($cate as $val) {
				$cate_list[$cate1][$val] = array();
			}
		}
		if($row[2] != '') {
			$cate3 = $row[2];
			$cate = explode("\n", $cate3);
			foreach($cate as $val) {
				$cate_list[$cate1][$cate2][$val] = array();
			}
		}
		if($row[3] != '') {
			$cate4 = $row[3];
			$cate = explode("\n", $cate4);
			foreach($cate as $val) {
				$cate_list[$cate1][$cate2][$cate3][$val] = 1;
			}
		}
	}
	fclose($fp);
	}
	// var_dump(json_encode($cate_list,JSON_UNESCAPED_UNICODE));
	$str=str_replace("[CATE_LIST]",json_encode($cate_list,JSON_UNESCAPED_UNICODE),$str);
	$str=str_replace("[CATE1a_VAL]",$FieldValue[114],$str);
	$str=str_replace("[CATE2a_VAL]",$FieldValue[115],$str);
	$str=str_replace("[CATE3a_VAL]",$FieldValue[116],$str);
	$str=str_replace("[CATE4a_VAL]",$FieldValue[117],$str);
	
	$str=str_replace("[CATE1b_VAL]",$FieldValue[43],$str);
// echo "<!--[CATE2b_VAL[".$FieldName[44]."]]:".$FieldValue[44]."-->";
	$str=str_replace("[CATE2b_VAL]",$FieldValue[44],$str);
// echo "<!--[CATE3b_VAL[".$FieldName[46]."]]:".$FieldValue[46]."-->";
	$str=str_replace("[CATE3b_VAL]",$FieldValue[46],$str);
	$str=str_replace("[CATE4b_VAL]",$FieldValue[47],$str);

	$str=str_replace("[CATE1c_VAL]",$FieldValue[48],$str);
	$str=str_replace("[CATE2c_VAL]",$FieldValue[49],$str);
	$str=str_replace("[CATE3c_VAL]",$FieldValue[50],$str);
	$str=str_replace("[CATE4c_VAL]",$FieldValue[51],$str);

	$str=str_replace("[CATE1d_VAL]",$FieldValue[52],$str);
	$str=str_replace("[CATE2d_VAL]",$FieldValue[53],$str);
	$str=str_replace("[CATE3d_VAL]",$FieldValue[54],$str);
	$str=str_replace("[CATE4d_VAL]",$FieldValue[110],$str);

	$str=str_replace("[MID_NEW]",convert_MID($FieldValue[1]),$str);
	$str=str_replace("[VERSION]",$version,$str);
	
	if ($version==""){
		$str=DispParam($str, "LATESTDATA");
		$str=DispParamNone($str, "OLDDATA");
	} else {
		$str=DispParamNone($str, "LATESTDATA");
		$str=DispParam($str, "OLDDATA");
	}
	

	print $str;

	} else {

		$filename=$htmllist;

		$fp=$filename;
		$tso=@fopen($fp,"r");

		while( $line = fgets($tso,1024) ){
			if(strstr($line,"LIST-START") == true){
				break;
			}
			$strU=$strU.$line.chr(13);
		}
		while( $line = fgets($tso,1024) ){
			if(strstr($line,"LIST-END") == true){
				break;
			}
			$strM=$strM.$line.chr(13);
		}
		while( $line = fgets($tso,1024) ){
			$strD=$strD.$line;
		}
		fclose($tso);

		// SQLインジェクション対策
		$StrSQL="SELECT * FROM ".$TableName." ".ListSql(mysqli_real_escape_string(ConnDB(),$sort),mysqli_real_escape_string(ConnDB(),$word)).";";
echo "<!--".$StrSQL."-->";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item=="") {
			$pagestr="";
			$strMain="<tr><td align=center colspan=7>対象データがありません。</td></tr>";
		} else {
			//================================================================================================
			//ページング処理
			//================================================================================================
			$reccount=mysqli_num_rows($rs);
			$pagecount=intval(($reccount-1)/$PageSize+1);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$str="";
			$str.="<div class=\"paging\"><div class=\"row\">";
			$str.="<div class=\"col-sm-5\"><div class=\"dataTables_info\" id=\"table_summary_info\" role=\"status\" aria-live=\"polite\">対象件数(".$reccount."件)</div></div>";
			$str.="<div class=\"col-sm-7\"><div class=\"dataTables_paginate paging_simple_numbers\" id=\"table_summary_paginate\"><ul class=\"pagination\">";

			if (intval($page)>1) {
				$str.="<li class=\"paginate_button previous disabled\" id=\"table_summary_previous\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&version=".$version."&word=".$word."&page=".($page-1)."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">前の".$PageSize."件</a></li>";
			}

			$s=$page-5;
			if ($s<1) {
				$s=1;
			} 
			$e=$s+9;
			if ($e>$pagecount) {
				$e=$pagecount;
			} 
			for ($i=$s; $i<=$e; $i=$i+1) {
				if ($i==intval($page)) {
					$str.="<li class=\"paginate_button active\"><span>".$i."</span></li>";
				} else {
					$str.="<li class=\"paginate_button\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">".$i."</a></li>";
				} 
			}
			if (intval($page)<$pagecount) {
				$str.="<li class=\"paginate_button next\" id=\"table_summary_next\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page+1)."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">次の".$PageSize."件</a></li>";
			} 

			$str.="</ul></div></div>";
			$str.="</div></div>";

			$pagestr=$str;
			$CurrentRecord=1;
			$strMain="";
			while ($item = mysqli_fetch_assoc($rs)) {

				$str=$strM;

				for ($i=0; $i<=$FieldMax; $i=$i+1) {
					if ($FieldAtt[$i]==4) {
						if ($item[$FieldName[$i]]=="") {
							$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
							$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
						} 
					} 
					// HTMLエスケープ処理（一覧表示系）
					$str=str_replace("[".$FieldName[$i]."]",($item[$FieldName[$i]]),$str);
					$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($FieldName[$i].":","",($item[$FieldName[$i]]))),$str);
					if (is_numeric($item[$FieldName[$i]])) {
						$str=str_replace("[N-".$FieldName[$i]."]",number_format($item[$FieldName[$i]],0),$str);
					} else {
						$str=str_replace("[N-".$FieldName[$i]."]","",$str);
					} 
					if ($item[$FieldName[$i]]==""){
						$str=DispParamNone($str, $FieldName[$i]);
					} else {
						$str=DispParam($str, $FieldName[$i]);
					} 
				}

				if($CurrentRecord%2==0){
					$str=str_replace("[LIST-BG]","bg01",$str);
				} else {
					$str=str_replace("[LIST-BG]","bg02",$str);
				}

				$tmps=explode(",",$_SESSION['a_m1_expo_ids']);
				if (in_array($item["ID"], $tmps)===true) {
					$str=str_replace("[expo_checked]","checked",$str);
				}
				$str=str_replace("[expo_checked]","",$str);


				// 要再審査の場合は赤字にする
				$drdo01 = str_replace("\r\n","<br>",str_replace($FieldName[35].":","",($item[$FieldName[35]])));
				if($item['M1_DRDO01'] == 'M1_DRDO01:要再審査') {
					$str=str_replace("[D-M1_DRDO01_ORG]",'<span style="color:red;">'.$drdo01.'</span>',$str);
				}
				else {
					$str=str_replace("[D-M1_DRDO01_ORG]",$drdo01,$str);
				}

				$str=str_replace("[L-HNAME]",convert_mid($item['MID']),$str);

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			} 
		} 


		$str=$strU.$strMain.$strD;

		$str = MakeHTML($str,1,$lid);

		switch($sort){
			case "1":
				$str=DispParamNone($str, "MID");
				$str=DispParam($str, "MID_ASC");
				$str=DispParamNone($str, "MID_DESC");
				break;
			case "2":
				$str=DispParamNone($str, "MID");
				$str=DispParamNone($str, "MID_ASC");
				$str=DispParam($str, "MID_DESC");
				break;

			case "3":
				$str=DispParamNone($str, "M1_DVAL01");
				$str=DispParam($str, "M1_DVAL01_ASC");
				$str=DispParamNone($str, "M1_DVAL01_DESC");
				break;
			case "4":
				$str=DispParamNone($str, "M1_DVAL01");
				$str=DispParamNone($str, "M1_DVAL01_ASC");
				$str=DispParam($str, "M1_DVAL01_DESC");
				break;
			case "5":
				$str=DispParamNone($str, "M1_DVAL02");
				$str=DispParam($str, "M1_DVAL02_ASC");
				$str=DispParamNone($str, "M1_DVAL02_DESC");
				break;
			case "6":
				$str=DispParamNone($str, "M1_DVAL02");
				$str=DispParamNone($str, "M1_DVAL02_ASC");
				$str=DispParam($str, "M1_DVAL02_DESC");
				break;
			case "7":
				$str=DispParamNone($str, "M1_DRDO01");
				$str=DispParam($str, "M1_DRDO01_ASC");
				$str=DispParamNone($str, "M1_DRDO01_DESC");
				break;
			case "8":
				$str=DispParamNone($str, "M1_DRDO01");
				$str=DispParamNone($str, "M1_DRDO01_ASC");
				$str=DispParam($str, "M1_DRDO01_DESC");
				break;
			case "9":
				$str=DispParamNone($str, "ENABLE");
				$str=DispParam($str, "ENABLE_ASC");
				$str=DispParamNone($str, "ENABLE_DESC");
				break;
			case "10":
				$str=DispParamNone($str, "ENABLE");
				$str=DispParamNone($str, "ENABLE_ASC");
				$str=DispParam($str, "ENABLE_DESC");
			case "11":
				$str=DispParamNone($str, "EDITDATE");
				$str=DispParam($str, "EDITDATE_ASC");
				$str=DispParamNone($str, "EDITDATE_DESC");
				break;
			case "12":
				$str=DispParamNone($str, "EDITDATE");
				$str=DispParamNone($str, "EDITDATE_ASC");
				$str=DispParam($str, "EDITDATE_DESC");
				break;

		}
		$str=DispParam($str, "MID");
		$str=DispParamNone($str, "MID_ASC");
		$str=DispParamNone($str, "MID_DESC");
		$str=DispParam($str, "M1_DVAL01");
		$str=DispParamNone($str, "M1_DVAL01_ASC");
		$str=DispParamNone($str, "M1_DVAL01_DESC");
		$str=DispParam($str, "M1_DVAL02");
		$str=DispParamNone($str, "M1_DVAL02_ASC");
		$str=DispParamNone($str, "M1_DVAL02_DESC");
		$str=DispParam($str, "M1_DRDO01");
		$str=DispParamNone($str, "M1_DRDO01_ASC");
		$str=DispParamNone($str, "M1_DRDO01_DESC");
		$str=DispParam($str, "ENABLE");
		$str=DispParamNone($str, "ENABLE_ASC");
		$str=DispParamNone($str, "ENABLE_DESC");
		$str=DispParam($str, "EDITDATE");
		$str=DispParamNone($str, "EDITDATE_ASC");
		$str=DispParamNone($str, "EDITDATE_DESC");

		
		// $tmp="";
		// $sel=explode("::", "会員ID（昇順）::会員ID（降順）");
		// for($i=0; $i<count($sel); $i++){
		// 	if($sel[$i]!=""){
		// 		if($sort==$i){
		// 			$tmp.="<option value=\"".$i."\" selected>".$sel[$i]."</option>";
		// 		} else {
		// 			$tmp.="<option value=\"".$i."\">".$sel[$i]."</option>";
		// 		}
		// 	}
		// }
		// $str=str_replace("[OPT-SORT]",$tmp,$str);


		$tmp="";
		$sel=explode("::", EXPORT_LIST);
		for($i=0; $i<count($sel); $i++){
			$checked="";
			if(strpos($expo,$sel[$i])!==false){
				$checked=" checked ";
			}
			$tmp.="<li><input ".$checked." id=\""."expo".$i."\" type=\"checkbox\" name=\""."expo[]\" value=\""."expo".":".$sel[$i]."\"><label for=\""."expo".$i."\">".$sel[$i]."</label></li>";
		}
		$str=str_replace("[OPT-EXP1]",$tmp,$str);

		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);

		// CSRFトークン生成
		// if($token==""){
			$token=htmlspecialchars(session_id()).date("YmdHis") . substr(explode(".", microtime(true))[1], 0, 3);
			$_SESSION['token'] = $token;
		// }
		$str=str_replace("[TOKEN]",$token,$str);

		$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

	} 


	return $function_ret;
} 

//=========================================================================================================
//名前 データリクエストパラメータ処理
//機能 データリクエストパラメータの処理と画像の保存
//引数 $obj,$a,$b,$key,$mode
//戻値 $function_ret;
//=========================================================================================================
function RequestData($obj,$a,$b,$key,$mode)
{
	eval(globals());

	// HTMLエスケープ処理（リクエストパラメータ）
	// クロスサイトスクリプティング対策
	for ($i=0; $i<=$FieldMax; $i=$i+1) {
		if ($FieldAtt[$i]==3) {
			if(strstr($_POST[$FieldName[$i]],"\t") == true) {
				$FieldValue[$i]=($_POST[$FieldName[$i]]);
			} else {
				$FieldValue[$i]="";
				for ($j=0; $j<count($_POST[$FieldName[$i]]); $j=$j+1) {
					if ($j!=0) {
						$FieldValue[$i]=$FieldValue[$i]."\t";
					}
					$FieldValue[$i]=$FieldValue[$i].$_POST[$FieldName[$i]][$j];
				}
			}
		} else {
			$FieldValue[$i]=(str_replace("\\","",$_POST[$FieldName[$i]]));
		}
		if ($FieldAtt[$i]==4 && $mode=="save") {
			$exts = explode("[/\\.]", $_FILES["EP_".$FieldName[$i]]['name']);
			$n = count($exts) - 1;
			$extention = $exts[$n];
			if ($extention=="jpeg") {
				$extention="jpg";
			} 

			if ($extention!="" && !!isset($extention)) {
				$filename=$FieldName[$i]."-".date("YmdHis").".".$extention;
				$FieldValue[$i]=$filepath1.$filename;
			} else {
				if ($FieldValue[$i]=="" || !isset($FieldValue[$i])) {
					$filename="s.gif";
					$FieldValue[$i]=$filepath1.$filename;
				} 
			} 
			if ($_POST["DEL_IMAGE_".$FieldName[$i]]=="on") {
				$filename="s.gif";
				$FieldValue[$i]=$filepath1.$filename;
			}
			if ($filename!="s.gif" && ($extention!="" && !!isset($extention))) {
				move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], "data/".$filename);
				pic_resize("data/".$filename, 800,800);
			} 
		} 
	}

	return $function_ret;
} 

//=========================================================================================================
//名前 DB読み込み
//機能 DBからレコードを取得
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function LoadData($key,$version="")
{
	eval(globals());

	
	// SQLインジェクション対策
	// HTMLエスケープ処理（SQL読み込み）
	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	if ($rs==true) {
		$item = mysqli_fetch_assoc($rs);
		for ($i=0; $i<=$FieldMax; $i=$i+1) {
			$FieldValue[$i]=($item[$FieldName[$i]]);
		}
	} 

	if($version!=""){
		$StrSQL="SELECT * FROM DAT_M1_HISTORY where MID='".$FieldValue[1]."' AND CHANGE_VERSION ='".$version."' ";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		if($item["ID"]!=""){
			//IDは読み込まない
			for ($i=1; $i<=$FieldMax; $i=$i+1) {
				$FieldValue[$i]=($item[$FieldName[$i]]);
			}
		}
	} 

	return $function_ret;
} 

//=========================================================================================================
//名前 DB書き込み
//機能 DBにレコードを保存
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function SaveData($key)
{
	eval(globals());

	// SQLインジェクション対策
	// HTMLエスケープ処理（SQL書き込み）
	$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item=="") {
		$StrSQL="INSERT INTO ".$TableName." (";
		for ($i=1; $i<=$FieldMax; $i++) {
			if($i>1){
				$StrSQL.=",";
			}
			$StrSQL.="`".$FieldName[$i]."`";
		}
		$StrSQL=$StrSQL.") VALUES (";
		for ($i=1; $i<=$FieldMax; $i++) {
			if($i>1){
				$StrSQL.=",";
			}
			if($FieldName[$i]=="M1_DTXT07" || $FieldName[$i]=="M1_ETC09"){
				$StrSQL.="'".str_replace("'","''",($FieldValue[$i]))."'";
			} else {
				$StrSQL.="'".str_replace("'","''",($FieldValue[$i]))."'";
			}
			
		}
		$StrSQL=$StrSQL.")";
	} else {
		$StrSQL="UPDATE ".$TableName." SET ";
		for ($i=1; $i<=$FieldMax; $i++) {
			if($i>1){
				$StrSQL.=",";
			}
			if($FieldName[$i]=="M1_DTXT07" || $FieldName[$i]=="M1_ETC09"){
				$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",($FieldValue[$i]))."'";
			} else {
				$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",($FieldValue[$i]))."'";
			}
			
		}
		$StrSQL=$StrSQL." WHERE ".$FieldName[$FieldKey]."='".$key."'";
	} 
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	return $function_ret;
} 


//=========================================================================================================
//名前 getChangeLog
//機能 
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function getChangeLog($item,$log)
{
	

	// $labels="MID::メールアドレス::パスワード::公開フラグ::登録日時::更新日時::利用規約に同意した日時（仮登録）::Company name::First name::Last name::Job title::Phone::Country::Time zone::";
	// $labels.="Website::Phone Number::Number of Employees::Type of Company::Year Established::Short Description::Keywords (comma separated)::Laboratories (one per line)::logo::";
	// $labels.="BILL TO::ship to::Service Website::Service introduction::Service Website::Service introduction::Service Website::Service introduction::Service Website::Service introduction::Service Website::Service introduction::";
	// $labels.="Organization name::Address Line 1::Address Line 2::City/Province::ZIP/Postal code::Country::Organization name::Address Line 1::Address Line 2::City/Province::ZIP/Postal code::Country::Organization name::Address Line 1::Address Line 2::City/Province::ZIP/Postal code::Country::";
	// $labels.="Organization name::Address Line 1::Address Line 2::City/Province::ZIP/Postal code::Country::Organization name::Address Line 1::Address Line 2::City/Province::ZIP/Postal code::Country::Organization name::Address Line 1::Address Line 2::City/Province::ZIP/Postal code::Country::";
	// $labels.="Organization Name::Name/Care of (Optional)::Attention (Optional)::Address Line 1::Address Line 2::City/Region::State/Province::Zip/Postal Code::Country::Country::Currency::Bank name::Account name::Account number::Routing Number (ACH and Checks) / ABA  (Domestic Wires)::Additional Account/Bank Identifiers::Please upload supporting documentation for this banking::";
	// $labels.="General Accounting Contact: Name::General Accounting Contact: Email::General Accounting Contact: Title::Use different contact information for purchase orders::Remittance Contact: Name::Remittance Contact: Email::Remittance Contact: Title::Tax ID::D-U-N-S::Form Type::Upload a new tax form::";
	// $labels.="Agree1::Agree2::legal Entity Name::Job title::Signature::Signed On::1. What is your organization name?::2. Location::3. Contact name::4. Contact phone number::5. Contact email address::";
	// $labels.="Are you willing to share basic financial information on your::Please provide an explanation as to why this information will::";
	// $labels.="1. Have you had any significant incidents relating to breach of::2. Do you have policies and/or processes that relate to the::3. Do you at least once a year communicate and train your::4. Is there a formal process to report and manage::1. Do you or do you expect to handle personal data in regards to::2. Will you process customer's Personal Data for your own::3. Does your company have an established governance structure in::4. Does your company have a privacy policy in place which your::5. Does your company have a process in place to ensure that any::6. Does your company provide regular training and awareness to::7. In the last five years has your company suffered a loss, leak::8. Has your company established an incident response plan that::9. Are there access controls in place to ensure that only the::10. If any Personal Data be maintained on your company's::";
	// $labels.="1. Has your company had a breach of local Employment law/human::2. Do you have formal process/policies or recognised practices::3. Do you have a formal process/policies or recognised practices::4. Do you use or have you ever used forced, bonded, indentured::5. Do you have a formal policy/contract to ensure you pay all::6. Do you have a formal policy or recognised practice which::1. Does the organization identify, document and implement::2. Is user access to the newtwork services monitored and::3. Does the organization have a clear policy governing the use::4. Does the organization have a policy governing the destruction::5. Does the organization identify and document all relevant::6. Have information security roles (capabilities and decision::7. Are security education, trainng and awareness programs::8. Does the organization's top management establish an::9. Does the organization perform information security risk::10. Does the organization conduct internal audits at planned::11. Does the organization adopt policies and supporting security::12. Are privacy risk assessments performed periodically?::13. Is a firewall installed at all connections from an internal::14. Does the organization regularly test the backup copies of::15. Is electronic data encrypted at rest (network storage,::16. Is electronic data encrypted in transit (TLS enabled for::17. Does the organization log the activities performed by system::18. Does the organization implement procedures to control the::19. Has the organization been accredited by any external::20. Does the organization implement physical access to controls::21. Does the organization have a policy to notify Scientist.com::";
	// $labels.="ファイルのタイトル１::ファイル１::ファイルのタイトル２::ファイル２::ファイルのタイトル３::ファイル３::ファイルのタイトル４::ファイル４::ファイルのタイトル５::ファイル５::ファイルのタイトル６::ファイル６::ファイルのタイトル７::ファイル７ ::ファイルのタイトル８::ファイル８ ::ファイルのタイトル９::ファイル９ ::ファイルのタイトル１０::ファイル１０ ::Youtube動画::";
	// $labels.="カテゴリー(第1階層)::カテゴリー(第2階層)::カテゴリー(第3階層)::カテゴリー(第4階層)::カテゴリー(第1階層)::カテゴリー(第2階層)::カテゴリー(第3階層)::カテゴリー(第4階層)::カテゴリー(第1階層)::カテゴリー(第2階層)::カテゴリー(第3階層)::カテゴリー(第4階層)::カテゴリー(第1階層)::カテゴリー(第2階層)::カテゴリー(第3階層)::カテゴリー(第4階層)::";
	// $labels.="検索ワード::通貨単位::登録状態::登録状態補足::I have read and agree to the policies and procedures";

	// $vals="MID::EMAIL::PASS::ENABLE::NEWDATE::EDITDATE::M1_DTXT08::M1_DVAL01::M1_DVAL22::M1_DVAL23::M1_DVAL24::M1_DVAL07::M1_DVAL04::M1_DSEL03::";
	// $vals.="M1_DVAL14::M1_DVAL15::M1_DVAL16::M1_DVAL17::M1_DVAL18::M1_DTXT03::M1_DTXT04::M1_DTXT05::M1_DFIL02::";
	// $vals.="M1_DVAL06::M1_DVAL25::M1_ETC08::M1_ETC91::M1_ETC27::M1_ETC92::M1_ETC28::M1_ETC93::M1_ETC29::M1_ETC94::M1_ETC30::M1_ETC95::";
	// $vals.="M1_DTXT24::M1_DTXT12::M1_DTXT15::M1_DTXT18::M1_DTXT21::M1_DSEL04::M1_DTXT10::M1_DTXT13::M1_DTXT16::M1_DTXT19::M1_DTXT22::M1_DSEL05::M1_DTXT11::M1_DTXT14::M1_DTXT17::M1_DTXT20::M1_DTXT23::M1_DSEL06::";
	// $vals.="M1_DTXT25::M1_DTXT28::M1_ETC10::M1_ETC21::M1_ETC24::M1_DSEL07::M1_DTXT26::M1_DTXT29::M1_ETC11::M1_ETC22::M1_ETC25::M1_DSEL08::M1_DTXT27::M1_DTXT30::M1_ETC12::M1_ETC23::M1_ETC26::M1_DSEL09::";
	// $vals.="M1_ETC31::M1_ETC32::M1_ETC33::M1_ETC34::M1_ETC35::M1_ETC36::M1_ETC37::M1_ETC38::M1_DSEL10::M1_ETC39::M1_ETC40::M1_ETC41::M1_ETC42::M1_ETC43::M1_ETC44::M1_ETC45::M1_ETC46::";
	// $vals.="M1_ETC47::M1_ETC48::M1_ETC49::M1_DCHK01::M1_DRDO02::M1_DRDO03::M1_DRDO04::M1_DRDO05::M1_DRDO07::M1_DRDO08::M1_DRDO06::";
	// $vals.="M1_ETC77::M1_ETC78::M1_ETC79::M1_ETC80::M1_ETC81::M1_ETC96::M1_MSEL01::M1_MSEL02::M1_MSEL03::M1_MSEL04::M1_MSEL05::";
	// $vals.="M1_MRDO01::M1_MSEL06::";
	// $vals.="M1_MRDO02::M1_MRDO03::M1_MRDO04::M1_MRDO05::M1_MRDO06::M1_MRDO07::M1_MRDO08::M1_MRDO09::M1_MRDO10::M1_MSEL07::M1_MSEL08::M1_MSEL09::M1_MSEL10::M1_DVAL13::";
	// $vals.="M1_ETC50::M1_ETC51::M1_ETC52::M1_ETC53::M1_ETC54::M1_ETC55::M1_ETC56::M1_ETC57::M1_ETC58::M1_ETC59::M1_ETC60::M1_ETC61::M1_ETC62::M1_ETC63::M1_ETC64::M1_ETC65::M1_ETC66::M1_ETC67::M1_ETC68::M1_ETC69::M1_ETC70::M1_ETC71::M1_ETC72::M1_ETC73::M1_ETC74::M1_ETC75::M1_ETC76::";
	// $vals.="M1_DTXT09::M1_MCHK01::M1_ETC82::M1_DFIL04::M1_ETC83::M1_DFIL05::M1_ETC84::M1_DFIL06::M1_ETC85::M1_DFIL07::M1_ETC86::M1_DFIL08::M1_ETC87::M1_DFIL09::M1_ETC88::M1_DFIL10::M1_ETC89::M1_ETC04::M1_ETC90::M1_ETC05::M1_DTXT07::";
	// $vals.="M1_ETC17::M1_ETC18::M1_ETC19::M1_ETC20::M1_DRDO09::M1_DRDO10::M1_DCHK02::M1_DCHK03::M1_DCHK04::M1_DCHK05::M1_DCHK06::M1_DCHK07::M1_DCHK08::M1_DCHK09::M1_DCHK10::M1_ETC13::";
	// $vals.="M1_ETC09::M1_ETC03::M1_DRDO01::M1_ETC14::M1_ETC07";
	$labels=LABLE_LIST;
	$vals=VALUE_LIST;
	$label_array=explode("::",$labels);
	$val_array=explode("::",$vals);
	$log_array=explode("::",$log);
	
// echo "<!--log:".$log."-->";

	$comment="";
	for ($j=0; $j<=count($log_array); $j++) {
		// echo "<!--log_array[j]".$log_array[$j]."-->";
		if($log_array[$j]!=""){
			for ($i=0; $i<=count($val_array); $i++) {
				// echo "<!--val_array[i]".$val_array[$i]."-->";
				if($log_array[$j]==$val_array[$i]){
					$comment.=$label_array[$i]."が".$item[$val_array[$i]]."に変更されました。<br>";
				}
			}
		}
	}

	return $comment;
// echo "<!--count(labels):".count(explode("::",$labels))."-->";
// echo "<!--count(vals):".count(explode("::",$vals))."-->";
// echo "<!--(labels):".($labels)."-->";
// echo "<!--(vals):".($vals)."-->";
// return;


}

//=========================================================================================================
//名前 getChangeLog
//機能 
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function DispNew($item,$log,$str)
{
	//echo "at dispnew()<br>";
	//echo "log:<br>";
	//var_dump($log);
	//echo "<br>";

	$labels=LABLE_LIST;
	$vals=VALUE_LIST;
	$label_array=explode("::",$labels);
	$val_array=explode("::",$vals);
	$log_array=explode("::",$log);
	
// echo "<!--log:".$log."-->";
// echo "<!--log_array:".$log_array[0]."-->";
	$disp_id="";
	for ($i=0; $i<=count($val_array); $i++) {
		$disp_id=$val_array[$i]."_NEW";
		// echo "<!--disp_id:".$disp_id."-->";
		for ($j=0; $j<=count($log_array); $j++) {
			// echo "<!--log_array:".$log_array[$j]."-->";
			if($log_array[$j]!=""){
				if($log_array[$j]==$val_array[$i]){
					$str=DispParam($str, $disp_id);
				}
			}
		}
		$str=DispParamNone($str, $disp_id);
	}

	return $str;
// echo "<!--count(labels):".count(explode("::",$labels))."-->";
// echo "<!--count(vals):".count(explode("::",$vals))."-->";
// echo "<!--(labels):".($labels)."-->";
// echo "<!--(vals):".($vals)."-->";
// return;


}
//=========================================================================================================
//名前 DB書き込み
//機能 DBにレコードを保存
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function SaveData_history($key)
{
	eval(globals());


	$StrSQL="SELECT CHANGE_VERSION from DAT_M1_HISTORY where MID='".$FieldValue[1]."' order by cast(CHANGE_VERSION as signed) desc limit 0,1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$version =$item["CHANGE_VERSION"];
echo "<!--".$StrSQL."-->";
echo "<!--".$version."-->";
	if($version==""){
		$version="1";
	} else {
		$version=intval($version)+1;
	}

	$StrSQL="SELECT * from DAT_M1 where MID='".$FieldValue[1]."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemM = mysqli_fetch_assoc($rs);
// echo "<!--".$StrSQL."-->";

	$StrSQL="SELECT * from DAT_M1_HISTORY where MID='".$FieldValue[1]."' order by id desc ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemH = mysqli_fetch_assoc($rs);
// echo "<!--".$StrSQL."-->";

	$log="";
	for ($i=1; $i<=$FieldMax; $i++) {
		if($itemM[$FieldName[$i]]!=$itemH[$FieldName[$i]]){
			if($log!=""){
				$log.="::";
			}
			$log.=$FieldName[$i];
		}
	}

	// SQLインジェクション対策
	// HTMLエスケープ処理（SQL書き込み）
	$StrSQL="INSERT INTO DAT_M1_HISTORY (";
	for ($i=1; $i<=$FieldMax; $i++) {
		if($i>1){
			$StrSQL.=",";
		}
		$StrSQL.="`".$FieldName[$i]."`";
	}


	$StrSQL.=",`CHANGE_DATE`";
	$StrSQL.=",`CHANGE_LOG`";
	$StrSQL.=",`CHANGE_VERSION`";
	$StrSQL=$StrSQL.") VALUES (";
	for ($i=1; $i<=$FieldMax; $i++) {
		if($i>1){
			$StrSQL.=",";
		}
		$StrSQL.="'".str_replace("'","''",($FieldValue[$i]))."'";
	}
	$StrSQL.=",'".date("Y/m/d H:i:s")."'";
	$StrSQL.=",'".$log."'";
	$StrSQL.=",'".$version."'";
	$StrSQL=$StrSQL.")";
	
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		var_dump("SaveData_history_ERR:".$StrSQL);
		die;
	}

	return $function_ret;
}

//=========================================================================================================
//名前 DB書き込み
//機能 DBにレコードを保存
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function SendMail_revoke($key)
{
	eval(globals());


	$StrSQL="SELECT CHANGE_VERSION from DAT_M1_HISTORY where MID='".$FieldValue[1]."' order by cast(CHANGE_VERSION as signed) desc limit 0,1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$version =$item["CHANGE_VERSION"];
echo "<!--".$StrSQL."-->";
echo "<!--".$version."-->";
	if($version==""){
		$version="1";
	} else {
		$version=intval($version)+1;
	}

	$StrSQL="SELECT * from DAT_M1 where MID='".$FieldValue[1]."' limit 1";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemM = mysqli_fetch_assoc($rs);

	//最新から1個前のヒストリー
	$StrSQL="SELECT * from DAT_M1_HISTORY where MID='".$FieldValue[1]."' order by id desc limit 1, 1 ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemH = mysqli_fetch_assoc($rs);

	//対象のフィールド
	$target_fields="M1_ETC77::M1_ETC78::M1_ETC79::M1_ETC80::M1_ETC81";
	//対象フィールドの空白の数
	$m_snum=0;
	$h_snum=0;
	$tmp="MID: ".$FieldValue[1]."\n";
	$tmp.="CHANGE_VERSION: ".$itemH["CHANGE_VERSION"]."\n";
	for ($i=1; $i<=$FieldMax; $i++) {
		if(strpos($target_fields,  $FieldName[$i])!==false){

			if( $itemM[$FieldName[$i]]=="" || is_null($itemM[$FieldName[$i]]) ){
				$m_snum++;
			}

			if( $itemH[$FieldName[$i]]=="" || is_null($itemH[$FieldName[$i]]) ){
				$h_snum++;
			}

		}
	}

	$tmp.="m_snum: $m_snum\n";
	$tmp.="h_snum: $h_snum\n";

	$ary=explode("::", $target_fields);
	$cnt=count($ary);
	//例：対象フィールドの数が5個で、現在の空白が５個、1個前のヒストリーの空白が1個以上のとき
	if($m_snum==$cnt && $h_snum<$cnt
		&& ($itemM["M1_DRDO01"]=="M1_DRDO01:審査依頼"
		|| $itemM["M1_DRDO01"]=="M1_DRDO01:要再審査"
		|| $itemM["M1_DRDO01"]=="M1_DRDO01:本登録"
		|| $itemM["M1_DRDO01"]=="M1_DRDO01:登録変更審査中") ){
		
		$tmp.="Revokeメール送信\n";

		$maildata1 = GetMailTemplate('サプライヤー署名取り消し完了(M-1)');
		$MailBody1 = $maildata1['BODY'];
		$subject1 = $maildata1['TITLE'];

		$MailBody1=str_replace("[MID]",$itemM['MID'],$MailBody1);
		$MailBody1=str_replace("[M1_DVAL01]",$itemM['M1_DVAL01'],$MailBody1);
		$MailBody1=str_replace("[M1_DVAL22]",$itemM["M1_DVAL22"],$MailBody1);
		$MailBody1=str_replace("[M1_DVAL23]",$itemM["M1_DVAL23"],$MailBody1);

		$subject1=str_replace("[MID]",$itemM['MID'],$subject1);
		$subject1=str_replace("[M1_DVAL01]",$itemM['M1_DVAL01'],$subject1);
		$subject1=str_replace("[M1_DVAL22]",$itemM["M1_DVAL22"],$subject1);
		$subject1=str_replace("[M1_DVAL23]",$itemM["M1_DVAL23"],$subject1);
		

		mb_language("Japanese");
		mb_internal_encoding("UTF-8");

		mb_send_mail($itemM["EMAIL"], $subject1, $MailBody1, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

	}else{
		$tmp.="なにもしない\n";

	}
	//echo "<pre>$tmp</pre>";

	return $function_ret;
} 


//=========================================================================================================
//名前 DB削除
//機能 DBからレコードを削除
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function DeleteData($key)
{
	eval(globals());

	// SQLインジェクション対策
	$StrSQL="DELETE FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	return $function_ret;
} 


//
//別ファイルへ移行
//
////=========================================================================================================
////名前 
////機能\ 
////引数 
////戻値 
////=========================================================================================================
//function ExportData()
//{
//	eval(globals());
//
//	$csv_data = "";
//
//	$StrSQL="SELECT * FROM ".$TableName." order by ID";
//	$rs=mysqli_query(ConnDB(),$StrSQL);
//	$item=mysqli_num_rows($rs);
//	if($item<>"") {
//		header("Content-Type: application/octet-stream");
//		header("Content-Disposition: attachment; filename=member".date('Ymd').".txt");
//
//		$str="";
//		for ($j=0; $j<=$FieldMax; $j=$j+1){
//			$StrSQL=$StrSQL."`".$FieldName[$j]."`";
//			if ($str!=""){
//				$str=$str."\t";
//			} 
//			$str=$str.$FieldName[$j];
//		}
//		$str=$str."\r\n";
//		$csv_data = $str;
//		$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
//		echo($csv_data);
//		while ($item = mysqli_fetch_assoc($rs)) {
//			$str="";
//			for ($i=0; $i<=$FieldMax; $i=$i+1){
//				if ($i!=0){
//					$str=$str."\t";
//				}
//				$str=$str.str_replace("\r\n", "[rn]", str_replace("\r", "[r]", str_replace("\n", "[n]", str_replace("\t", "[t]", $item[$FieldName[$i]]))));
//			}
//			$csv_data = $str."\r\n";
//			$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
//			echo($csv_data);
//		} 
//	} 
//
//	return $function_ret;
//} 


//=========================================================================================================
//名前 タブ区切りデータのインポート処理
//機能 タブ区切りテキストデータ（ShiftJIS→UTF-8）のエクスポート処理
//引数 なし
//戻値 なし
//=========================================================================================================
function ImportData($obj,$a,$b,$key,$mode)
{
	eval(globals());

	$fp = fopen($_FILES['importfile']['tmp_name'], "r");
	$txt = fgets($fp);

	$cnt=0;
	$cols=explode("\t",$txt);
	for ($i=0; $i<=count($cols); $i=$i+1){
		if($cols[$i]<>""){
			$cnt++;
		}
	}
	$tmp="";
	for ($j=0; $j<$cnt; $j=$j+1){
		if ($tmp!=""){
			$tmp=$tmp.",";
		} 
		$tmp=$tmp."`".trim($cols[$j])."`";
		$fn[$j]=trim($cols[$j]);
	}
	$StrSQLI="INSERT INTO ".$TableName." (".$tmp.") values ([VALS]);";

	while (!feof($fp)) {
		$txt = fgets($fp);
		$txt=str_replace("\"","",$txt);
		$cols=explode("\t",$txt);
		if($cols[0]<>""){
			$StrSQL="SELECT * FROM ".$TableName." where ID='".$cols[0]."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_num_rows($rs);
			if($item=="") {
				$tmp="";
				for ($j=0; $j<$cnt; $j=$j+1){
					if ($tmp!=""){
						$tmp=$tmp.",";
					} 
					$tmp=$tmp."'".trim(str_replace("[rn]","\r\n",str_replace("[r]","\r",str_replace("[n]","\n",str_replace("[t]","\t",str_replace("'","''",$cols[$j]))))))."'";
				}
				$StrSQL=str_replace("[VALS]", $tmp, $StrSQLI);
				$StrSQL = mb_convert_encoding($StrSQL, "UTF-8", "SJIS-win");
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			} else {
				if ($cols[1]!="delete"){
					$tmp="";
					for ($j=1; $j<$cnt; $j=$j+1){
						if ($tmp!=""){
							$tmp=$tmp.",";
						} 
						$tmp=$tmp."`".$fn[$j]."`='".trim(str_replace("[rn]","\r\n",str_replace("[r]","\r",str_replace("[n]","\n",str_replace("[t]","\t",str_replace("'","''",$cols[$j]))))))."'";
					}
					$StrSQL="UPDATE ".$TableName." SET ".$tmp." WHERE ".$FieldName[$FieldKey]."='".$cols[0]."';";
					$StrSQL = mb_convert_encoding($StrSQL, "UTF-8", "SJIS-win");
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} else {
					$StrSQL="DELETE FROM ".$TableName." WHERE ID='".$cols[0]."';";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} 
			} 
		}
	}

	return $function_ret;
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
