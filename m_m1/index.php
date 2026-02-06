<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_m1/config.php';

require_once('../TCPDF/tcpdf.php');
//平行作業用
require_once('./func.php');

// ini_set( 'display_errors', 1 );
require("../crawl/simple_html_dom.php");
require("../crawl/func1.php");

//echo等の出力のバッファリングを無効にし則出力
ini_set('max_execution_time', 0);
set_time_limit(0);
//ini_set('memory_limit', '1G');
ini_set('memory_limit', '-1');

// set_time_limit(7200);
define("MAX_ELM", 500);

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
		$param=$_GET['param'];
		$url=$_GET['url'];
		$ENABLE_REQUEST=$_GET['ENABLE_REQUEST'];
		$print=$_GET['print'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$param=$_POST['param'];
		$url=$_POST['url'];

		$ENABLE_REQUEST=$_POST['ENABLE_REQUEST'];
		$print=$_POST['print'];
	}

	if($param == '') {
		$param = 'Company_information';
	}

	if($_SESSION['MID']==""){
		$url=BASE_URL . "/login1/";
		header("Location: {$url}");
		exit;
	}

	if ($mode=="mailtest"){
		//https://scientist3.ms123.jp/m_m1/?mode=mailtest
		SendMail($_SESSION['MNAME']);
	}
	
	if($mode=="torikesi"){

		//if($_SESSION['token']==$token){
			//$StrSQL=" UPDATE DAT_M1 SET M1_ETC77 = '',M1_ETC78='',M1_ETC96 = '' ";
			//$StrSQL.=" WHERE MID = '".$_SESSION['MID']."'";
			//if($print!="on") echo "<!--torikesi:".$StrSQL."-->";
			//if (!(mysqli_query(ConnDB(),$StrSQL))) {
			//	var_dump("UPDATErr1:".$StrSQL);
			//	exit;
			//}
		//}

		// 運営に通知
		SendMail_torikesi();

		$param="Agreements";
		$mode="edit";
	}

	if ($mode==""){
		$mode="edit";
	}

	if($print!="on") echo "<!--mode:".$mode."-->";

	if ($key==""){
		//$StrSQL="SELECT ID from DAT_M1 where MID='".$_SESSION['MID']."' and ENABLE='ENABLE:公開中';";
		$StrSQL="SELECT ID from DAT_M1 where MID='".$_SESSION['MID']."' ;";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item>0){
			$item = mysqli_fetch_assoc($rs);
			$key = $item['ID'];
		} else {
			$url=BASE_URL . "/login1/";
			header("Location: {$url}");
			exit;
		}
	}

	//クロールする
	if($mode=="crawl"){

		if($_SESSION['token']==$token){

			$url_top=$url;
			$url_top=trim($url_top);
			// if($print!="on") echo "<!--url_top:".$url_top."-->";
			$search_word="";
			$errmsg="";
			if( !checkURL_crawl($url_top) ){
				// die("不正なurlです");
				$errmsg="不正なurlです";
				$search_word="";
			}

			$doc_root=make_docRoot($url_top);
			if($doc_root==""){
				// die("不正なurlです");
				$errmsg="不正なurlです";
				$search_word="";
			}

			define("DOC_ROOT", $doc_root);
			if($errmsg==""){

				$mid=$_SESSION['MID'];

				//初期化
				$StrSQL=" UPDATE DAT_M1 SET M1_ETC09 = 'クローリング中。。'";
				$StrSQL.=" WHERE MID = '".$mid."'";
				//if($print!="on") echo "<!--".$StrSQL."-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					var_dump("UPDATErr1:".$StrSQL);
					// file_put_contents($tmp_filename, "UPDATErr1:".$StrSQL, FILE_APPEND);
					// die;
				}


				$tmp_filename=make_filename($mid);
				// if($print!="on") echo "<!--tmp_filename:".$tmp_filename."-->";

				

				$exec_file="../crawl/crawl_proc.php";
				$cmd = "nohup php -c '' '$exec_file' '$url_top' '$doc_root' '$tmp_filename' '$mid' > nohup.dat &";
				exec($cmd);
				//if($print!="on") echo  "<!--cmd:".$cmd."-->";
				//$disp_path="https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/".$tmp_filename;
				//if($print!="on") echo  "<!--disp_path:".$disp_path."-->";
			} else {
				//初期化
				$StrSQL=" UPDATE DAT_M1 SET M1_ETC09 = '".$errmsg."'";
				$StrSQL.=" WHERE MID = '".$mid."'";
				//if($print!="on") echo "<!--".$StrSQL."-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					var_dump("UPDATErr1:".$StrSQL);
					// file_put_contents($tmp_filename, "UPDATErr1:".$StrSQL, FILE_APPEND);
					// die;
				}
			}
		}



		$mode="edit";
	}

	switch ($mode){
		case "new":
			InitData();
			break;
		case "edit":
			LoadData($key);
//			RequestData($obj,$a,$b,$key,$mode);

			if($FieldValue[125]==""){
				$FieldValue[125]="2000"; //従業員数
			}

		
			break;
		case "disp":
			LoadData($key);
			break;
		case "saveconf":
			LoadData($key);
			RequestData($obj,$a,$b,$key,$mode);

			$param="Confirmation";
			
			break;
		case "save":
			// CSRFチェック OKならDB書き込み
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);

				//変更前情報
				$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$itemBefore = mysqli_fetch_assoc($rs);

				//サインされた場合
				if($itemBefore["M1_ETC77"]!=$FieldValue[214]){
					$FieldValue[233]=date("Y/m/d H:i:s");
				}

				SaveData($key);


				//署名完了後は /m_m1/?param=Agreements の画面に
				if($param=="Agreements2e" || $param=="Agreements2j"){
					$param="Agreements";
					$mode="edit";
				}

				$_SESSION['MNAME'] = $FieldValue[5];

				//公開依頼
				if($ENABLE_REQUEST!=""){
					SendMail($_SESSION['MNAME']);
				}

// 要調整
/*
				$StrSQL="DELETE FROM DAT_MATCH where MID='".$FieldValue[1]."';";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
				$tmp="";
				$StrSQL="SELECT DAT_JOB.HID, DAT_JOB.JID FROM DAT_JOB inner join DAT_HOSPITAL on DAT_HOSPITAL.HID=DAT_JOB.HID order by DAT_JOB.JID;";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				while ($item = mysqli_fetch_assoc($rs)) {
					if($tmp!=""){
						$tmp.="::";
					}
					$tmp.=$item['JID'];
				}
				$jids=explode("::", $tmp);

				for($j=0; $j<count($jids); $j++){
					CulcMatching($FieldValue[1], $jids[$j], 0);
				}
*/
// 要調整

			}

			
			// draft(mode=save)では完了画面ではなく編集画面に戻るとのこと
			// next(mode=save)では次の画面へ
			
			//再びLoadData($key)することで、NEXTボタンでの遷移後のページでリロード（mode:save状態でのリロード）
			//したときにデータ消えるバグを修正。
			LoadData($key);
			$mode="edit";
			if($_POST['btnmode'] == 'next') {
				if($param == 'Company_information') {
					$param = 'Banking_details';
				}
				else if($param == 'Banking_details') {
					$param = 'Agreements';
				}
				else if($param == 'Agreements') {
					$param = 'File10';
				}
				else if($param == 'File10') {
					$param = 'Category';
				}
			}
			else if($_POST['btnmode'] == 'back') {
				if($param == 'Agreements') {
					$param = 'Banking_details';
				}
				else if($param == 'Banking_details') {
					$param = 'Company_information';
				}
				else if($param == 'File10') {
					$param = 'Agreements';
				}
				else if($param == 'Category') {
					$param = 'File10';
				}
				else if($param == 'Confirmation') {
					$param = 'Category';
				}
				else if($param == 'Agreements2e') {
					$param = 'Agreements';
				}
				else if($param == 'Agreements2j') {
					$param = 'Agreements';
				}
			}

			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			$mode="edit1";
			break;

		case "revoke":
			RequestData($obj,$a,$b,$key,$mode);
			$mode="shinsa";
			echo "<!--revoke!--->";
			SendMail_revoke();

			break;

		case "shinsa":
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);
				SaveData($key);

				$_SESSION['MNAME'] = $FieldValue[5];

				// 登録状態を審査依頼に変更
				if($FieldValue[35]=="M1_DRDO01:仮登録中" || $FieldValue[35]=="M1_DRDO01:審査依頼" || $FieldValue[35]=="M1_DRDO01:要再審査"){
					$StrSQL=" UPDATE DAT_M1 SET M1_DRDO01 = 'M1_DRDO01:審査依頼' where MID='".$_SESSION['MID']."'";

				} else{
				
					$StrSQL=" UPDATE DAT_M1 SET M1_DRDO01 = 'M1_DRDO01:登録変更審査中' where MID='".$_SESSION['MID']."'";
				}
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
				// 管理者にメール送信
				SendMail_shinsa();
			}
			// $mode="edit";
			break;
	} 

	//echo "<!--FieldValue:";
	//var_dump($FieldValue);
	//echo "-->";

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
	
	DispData($mode,$sort,$word,$key,$page,$lid,$token,$param,$print);

	return $function_ret;
} 
//=========================================================================================================
//名前 公開依頼メール
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($name)
{

	eval(globals());

	$maildata = GetMailTemplate('公開依頼');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	$MailBody=str_replace("[MID]",$_SESSION['MID'],$MailBody);
	$MailBody=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$MailBody);
	$MailBody=str_replace("[NAME]",$_SESSION['MNAME'],$MailBody);

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	// テスト
	//mb_send_mail('197583@gmail.com', $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	// 本番
	$mailto=SENDER_EMAIL;
	// $mailto="toretoresansan00@gmail.com";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}

//=========================================================================================================
//名前 公開依頼メール
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail_shinsa()
{

	eval(globals());

	$maildata1 = GetMailTemplate('審査依頼（M1）');
	$maildata2 = GetMailTemplate('審査依頼');
	$MailBody1 = $maildata1['BODY'];
	$subject1 = $maildata1['TITLE'];
	$MailBody2 = $maildata2['BODY'];
	$subject2 = $maildata2['TITLE'];

	$StrSQL="SELECT * from DAT_M1 where MID='".$_SESSION['MID']."' ;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemM1 = mysqli_fetch_assoc($rs);

	$MailBody1=str_replace("[MID]",$_SESSION['MID'],$MailBody1);
	$MailBody1=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$MailBody1);
	$MailBody1=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$MailBody1);
	$MailBody1=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$MailBody1);
	$MailBody2=str_replace("[MID]",$_SESSION['MID'],$MailBody2);
	$MailBody2=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$MailBody2);
	$MailBody2=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$MailBody2);
	$MailBody2=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$MailBody2);

	$subject1=str_replace("[MID]",$_SESSION['MID'],$subject1);
	$subject1=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$subject1);
	$subject1=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$subject1);
	$subject1=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$subject1);
	$subject2=str_replace("[MID]",$_SESSION['MID'],$subject2);
	$subject2=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$subject2);
	$subject2=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$subject2);
	$subject2=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$subject2);

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	mb_send_mail($itemM1["EMAIL"], $subject1, $MailBody1, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail(SENDER_EMAIL, $subject2, $MailBody2, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}


//=========================================================================================================
//名前 公開依頼メール
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail_revoke()
{

	eval(globals());

	$maildata1 = GetMailTemplate('サプライヤー署名取り消し依頼(M-1)');
	$maildata2 = GetMailTemplate('サプライヤー署名取り消し依頼');
	$MailBody1 = $maildata1['BODY'];
	$subject1 = $maildata1['TITLE'];
	$MailBody2 = $maildata2['BODY'];
	$subject2 = $maildata2['TITLE'];

	$StrSQL="SELECT * from DAT_M1 where MID='".$_SESSION['MID']."' ;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemM1 = mysqli_fetch_assoc($rs);

	$MailBody1=str_replace("[MID]",$_SESSION['MID'],$MailBody1);
	$MailBody1=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$MailBody1);
	$MailBody1=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$MailBody1);
	$MailBody1=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$MailBody1);
	$MailBody2=str_replace("[MID]",$_SESSION['MID'],$MailBody2);
	$MailBody2=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$MailBody2);
	$MailBody2=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$MailBody2);
	$MailBody2=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$MailBody2);

	$subject1=str_replace("[MID]",$_SESSION['MID'],$subject1);
	$subject1=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$subject1);
	$subject1=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$subject1);
	$subject1=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$subject1);
	$subject2=str_replace("[MID]",$_SESSION['MID'],$subject2);
	$subject2=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$subject2);
	$subject2=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$subject2);
	$subject2=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$subject2);

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	mb_send_mail($itemM1["EMAIL"], $subject1, $MailBody1, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail(SENDER_EMAIL, $subject2, $MailBody2, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}


//=========================================================================================================
//名前 署名取消しメール
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail_torikesi()
{

	eval(globals());

	$maildata = GetMailTemplate('署名取消し');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$StrSQL="SELECT * from DAT_M1 where MID='".$_SESSION['MID']."' ;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemM1 = mysqli_fetch_assoc($rs);

	$MailBody=str_replace("[MID]",$_SESSION['MID'],$MailBody);
	$MailBody=str_replace("[M1_DVAL01]",$_SESSION['MNAME'],$MailBody);
	$MailBody=str_replace("[M1_DVAL22]",$itemM1["M1_DVAL22"],$MailBody);
	$MailBody=str_replace("[M1_DVAL23]",$itemM1["M1_DVAL23"],$MailBody);

	$MailBody=str_replace("[D-M1_ETC77]",str_replace("M1_ETC77:","",$itemM1["M1_ETC77"]),$MailBody);
	$MailBody=str_replace("[D-M1_ETC78]",str_replace("M1_ETC78:","",$itemM1["M1_ETC78"]),$MailBody);
	$MailBody=str_replace("[D-M1_ETC79]",str_replace("M1_ETC79:","",$itemM1["M1_ETC79"]),$MailBody);
	$MailBody=str_replace("[D-M1_ETC80]",str_replace("M1_ETC80:","",$itemM1["M1_ETC80"]),$MailBody);
	$MailBody=str_replace("[D-M1_ETC81]",str_replace("M1_ETC81:","",$itemM1["M1_ETC81"]),$MailBody);
	$MailBody=str_replace("[D-M1_ETC96]",str_replace("M1_ETC96:","",$itemM1["M1_ETC96"]),$MailBody);



	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	mb_send_mail(SENDER_EMAIL, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$param,$print)
{
	eval(globals());

	//各テンプレートファイル名
	//$htmlnew = "edit.html";
	$htmlnew = "Company_information.html";
	//$htmledit = "edit.html";
	$htmledit ="Company_information.html";
	$htmlconf = "conf.html";
	$htmlend = "end.html";
	$htmlend2 = "end2.html";
	$htmldisp = "disp.html";
	//$htmlerr = "edit.html";
	$htmlerr = "Company_information.html";
	$htmllist = "list.html";

	switch ($mode){
		case "new":
			$filename=$htmlnew;
			$msg01="";
			$msg02="";
			$errmsg="";
			break;
		case "edit":
			$filename=$htmledit;
			if($print!="on") echo "<!--edit_param:".$param."-->";
			switch ($param) {
				case "Company_information":
					$filename="Company_information.html";
					break;
				case "Banking_details":
					$filename="Banking_details.html";
					break;
				case "Agreements":
					$filename="Agreements.html";
					break;
				case "Agreements2e":
					if($print=="on"){
						$filename="Agreements2e_print.html";
					} else {
						$filename="Agreements2e.html";
					}
					
					break;
				case "Agreements2j":
					if($print=="on"){
						$filename="Agreements2j_print.html";
					} else {
						$filename="Agreements2j.html";
					}
					break;
				case "Agreements3e":
					$filename="Agreements3e.html";
					break;
				case "Agreements3j":
					$filename="Agreements3j.html";
					break;
				case "Signature":
					$filename="Signature.html";
					break;
				case "Commercial_Due_Dilig":
					$filename="Commercial_Due_Dilig.html";
					break;
				case "File10":
					$filename="File10.html";
					break;
				case "File10conf":
					$filename="File10conf.html";
					break;
				case "Category":
					$filename="Category.html";
					break;
				default:
					
					break;
			}

						//$filename="Agreements2j_print.html";

			$msg01="";
			$msg02="";
			$errmsg="";
			break;
		case "saveconf":
			if($print!="on") echo "<!--saveconf_param:".$param."-->";
			$msg=ErrorCheckM1();
			if ($msg==""){
				$filename=$htmlconf;
				if($param=="File10"){
					$filename="File10conf.html";
				}

					
				
				$msg01="保存";
				$msg02="save";
				$errmsg="";
			} else {
				$filename=$htmledit;
			switch ($param) {
				case "Company_information":
					$filename="Company_information.html";
					break;
				case "Banking_details":
					$filename="Banking_details.html";
					break;
				case "Agreements":
					$filename="Agreements.html";
					break;
				case "Agreements2e":
					if($print=="on"){
						$filename="Agreements2e_print.html";
					} else {
						$filename="Agreements2e.html";
					}
					
					break;
				case "Agreements2j":
					if($print=="on"){
						$filename="Agreements2j_print.html";
					} else {
						$filename="Agreements2j.html";
					}
					break;
				case "Agreements3e":
					$filename="Agreements3e.html";
					break;
				case "Agreements3j":
					$filename="Agreements3j.html";
					break;
				case "Signature":
					$filename="Signature.html";
					break;
				case "Commercial_Due_Dilig":
					$filename="Commercial_Due_Dilig.html";
					break;
				case "File10":
					// $filename="File10.html";
					$filename="File10conf.html";
					break;
				// case "File10conf":
				// 	$filename="File10conf.html";
				// 	break;
				case "Category":
					$filename="Category.html";
					break;

				case "Confirmation":
					$filename="Category.html";
					break;
				break;
				default:
					
					break;
			}
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
			$filename=$htmlend;
			$msg01="保存";
			$msg02="";
			$errmsg="";
			break;
		case "shinsa":
			$filename=$htmlend2;
			$msg01="保存";
			$msg02="";
			$errmsg="";
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

if($print!="on") echo "<!--filename:".$filename."-->";

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

	// 日本語と英語を連動させないのでコメントアウトした
	/*
	if($FieldValue[214]!=""){
		if($_GET['param'] == 'Agreements2e') {
			$FieldValue[214]="I have read Terms 1) and 2), fully understand their contents, and agree to these Terms.";
			$FieldValue[215]="I hereby confirm that I have the authority to enter into a binding agreement on behalf of ".$FieldValue[5]."";

		}
		else if($_GET['param'] == 'Agreements2j') {
			$FieldValue[214]="私は、本規約1)および2)を読み、その内容を完全に理解し、本規約に同意します。";
			$FieldValue[215]="私は、".$FieldValue[5]."を代表して拘束力のある契約を締結する権限を有することをここに確認します。";
		}
	}
	*/

	//// 日本語と英語で連動させない。署名したほうだけが署名済みになるように
	//if($_GET['param'] == 'Agreements2e' || $_GET['param'] == 'Agreements2j') {
	//	if($FieldValue[214]!="" && $FieldValue[215]!=""){
	//		if($_GET['param'] == 'Agreements2e' && strpos($FieldValue[214], 'I have') !== false) {
	//		}
	//		else if($_GET['param'] == 'Agreements2j' && strpos($FieldValue[214], '私は') !== false) {
	//		}
	//		else {
	//			$FieldValue[216] = '';
	//			$FieldValue[217] = '';
	//			$FieldValue[218] = '';
	//		}
	//	}
	//	else {
	//		$FieldValue[216] = '';
	//		$FieldValue[217] = '';
	//		$FieldValue[218] = '';
	//	}
	//}

	// デフォルトでLegal Entity Nameに会社名を出す
	if($_GET['param'] == 'Agreements2e' || $_GET['param'] == 'Agreements2j') {
		if($FieldValue[216] == '') {
			$FieldValue[216] = $FieldValue[5];
		}
	}

	for ($i=0; $i<=$FieldMax; $i=$i+1){
		//if($print!="on") echo ('<!--'.$FieldName[$i].' : '.$FieldValue[$i].'-->');
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
		$str=str_replace("[".$FieldName[$i]."]",htmlspecialchars($FieldValue[$i]),$str);
		$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($FieldName[$i].":","",htmlspecialchars($FieldValue[$i]))),$str);
		$str=str_replace("[DD-".$FieldName[$i]."]",$FieldValue[$i],$str);

		if ($FieldAtt[$i]=="1"){
			$strtmp="";
			$strtmp=$strtmp."<option value=''>Please select ▼</option>";
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
			if (($filename==$htmlerr || $mode=="new" || $mode=="edit" || $mode=="edit1" || $mode=="edit2" || $mode=="edit3" || $mode=="edit4" || $mode=="edit5" || $mode=="edit6" || $mode=="edit7" || $mode=="edit8") && $FieldValue[$i]!="") {

				$str=str_replace("'".$FieldValue[$i]."'","'".$FieldValue[$i]."' selected",$str);
			} 
		} 

		if ($FieldAtt[$i]=="2"){
			$required = '';
			// if($FieldName[$i] == 'M1_DRDO08') {
			// 	$required = ' required ';
			// }
			if($filename == "Commercial_Due_Dilig.html") {
				$required = ' required ';
			}

			$strtmp="";
			$tmp=explode("::",$FieldParam[$i]);
			$strtmp=$strtmp."<ul>";
			for ($j=0; $j<count($tmp); $j=$j+1) {
				$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"radio\" name=\"".$FieldName[$i]."\" value=\"".$FieldName[$i].":".$tmp[$j]."\" ".$required."><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
			}
			$strtmp=$strtmp."</ul>";
			$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
			if (($filename==$htmlerr || $mode=="new" || $mode=="edit" || $mode=="edit1" || $mode=="edit2" || $mode=="edit3" || $mode=="edit4" || $mode=="edit5" || $mode=="edit6" || $mode=="edit7" || $mode=="edit8") && $FieldValue[$i]!="") {
				$str=str_replace("\"".$FieldValue[$i]."\"","\"".$FieldValue[$i]."\" checked",$str);
			} 
		} 

		if ($FieldAtt[$i]=="3"){
			$required = '';
			if($FieldName[$i] == 'M1_ETC77' || $FieldName[$i] == 'M1_ETC78') {
				$required = ' required ';
			}
			if($filename == "Commercial_Due_Dilig.html") {
				$required = ' required ';
			}

			$strtmp="";
			$tmp=explode("::",$FieldParam[$i]);
			$strtmp=$strtmp."<ul class='mlist25p'>";

			//if($FieldName[$i]=="M1_ETC77" || $FieldName[$i]=="M1_ETC78"){
				//if($print!="on") echo "<!--".$FieldName[$i].":".$FieldValue[$i]."-->";
				//if($print!="on") echo "<!--mode:".$mode."-->";
			//}

			for ($j=0; $j<count($tmp); $j=$j+1) {
				$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"".$FieldName[$i]."[]\" value=\"".$FieldName[$i].":".$tmp[$j]."\" ".$required."><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
			}
			$strtmp=$strtmp."</ul>";
			$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
			if (($filename==$htmlerr || $mode=="new" || $mode=="edit" || $mode=="edit1" || $mode=="edit2" || $mode=="edit3" || $mode=="edit4" || $mode=="edit5" || $mode=="edit6" || $mode=="edit7" || $mode=="edit8") && $FieldValue[$i]!="") {
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
	$str=str_replace("[PARAM]",$param,$str);

	$str=str_replace("[URL]","https://scientist3.ms123.jp/m_m1/?param=Agreements3e",$str);

	switch ($param) {
		case "Company_information":
			$str=str_replace("[Company_information_ACTIVE]","active",$str);
			break;
		case "Banking_details":
			$str=str_replace("[Banking_details_ACTIVE]","active",$str);
			break;
		case "Agreements":
		case "Agreements2e":
		case "Agreements2j":
		case "Agreements3e":
		case "Agreements3j":
			$str=str_replace("[Agreements_ACTIVE]","active",$str);
			break;
		case "Signature":
			$str=str_replace("[Signature_ACTIVE]","active",$str);
			break;
		case "Commercial_Due_Dilig":
			$str=str_replace("[Commercial_Due_Dilig_ACTIVE]","active",$str);
			break;
		case "File10":
		case "File10conf":
			$str=str_replace("[File10_ACTIVE]","active",$str);
			break;
		case "Category":
			$str=str_replace("[Category_ACTIVE]","active",$str);
			break;
		default:
			
			break;
	}
	$str=str_replace("[Company_information_ACTIVE]","",$str);
	$str=str_replace("[Banking_details_ACTIVE]","",$str);
	$str=str_replace("[Agreements_ACTIVE]","",$str);
	$str=str_replace("[Signature_ACTIVE]","",$str);
	$str=str_replace("[Commercial_Due_Dilig_ACTIVE]","",$str);
	$str=str_replace("[File10_ACTIVE]","",$str);
	$str=str_replace("[Category_ACTIVE]","",$str);


	$filename="Banking_Details.txt";
	$fp=$DOCUMENT_ROOT.$filename;
	$tmp=@file_get_contents($fp);
	$str=str_replace("[Banking_Details.txt]",$tmp,$str);

echo "<!--param(request):".$_REQUEST['param']."-->";
echo "<!--param:".$param."-->";
echo "<!--M1_ETC77:".$FieldValue[214]."-->";
echo "<!--M1_ETC78:".$FieldValue[215]."-->";

	// Banking Detailsが登録済みだったら登録ボタン出さない
	// Next,Backボタンの仕様によりなくなった？
	if($param == 'Banking_details') {
		/*
		if($FieldValue[112] == '1') {
			$str=DispParam($str, "BANKING_DETAILS_1");
			$str=DispParamNone($str, "BANKING_DETAILS_0");
		}
		else {
			$str=DispParam($str, "BANKING_DETAILS_0");
			$str=DispParamNone($str, "BANKING_DETAILS_1");
		}
		*/
		$str=DispParam($str, "BANKING_DETAILS_0");
		$str=DispParamNone($str, "BANKING_DETAILS_1");
	}


	
	// 日本語と英語で連動させない。署名したほうだけが署名済みになるように
	else if($FieldValue[214]!="" && $FieldValue[215]!=""){
		if($param == 'Agreements' || $mode == 'saveconf') {
			$str=DispParam($str, "AGREEMENT_SIGNED");
			$str=DispParamNone($str, "NOT_AGREEMENT_SIGNED");
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_J");
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_E");
		}
		// else if($param == 'Agreements2e' && strpos($FieldValue[214], 'I have') !== false) {
		// 	$str=DispParam($str, "AGREEMENT_SIGNED");
		// 	$str=DispParamNone($str, "NOT_AGREEMENT_SIGNED");
		// }
		// else if($param == 'Agreements2j' && strpos($FieldValue[214], '私は') !== false) {
		// 	$str=DispParam($str, "AGREEMENT_SIGNED");
		// 	$str=DispParamNone($str, "NOT_AGREEMENT_SIGNED");
		// }
		else if($param == 'Agreements2e' && strpos($FieldValue[214], '私は') !== false){
			$str=DispParamNone($str, "NOT_AGREEMENT_SIGNED");
			$str=DispParamNone($str, "AGREEMENT_SIGNED");
			$str=DispParam($str, "AGREEMENT_SIGNED_WITH_J");
		}
		else if($param == 'Agreements2j' && strpos($FieldValue[214], 'I have') !== false){
			$str=DispParamNone($str, "NOT_AGREEMENT_SIGNED");
			$str=DispParamNone($str, "AGREEMENT_SIGNED");
			$str=DispParam($str, "AGREEMENT_SIGNED_WITH_E");
		}
		else if($param == 'Agreements2e' || $param == 'Agreements2j'){
			//片方しか署名できないようになった
			if($FieldValue[214]!=""){
				$str=DispParamNone($str, "NOT_AGREEMENT_SIGNED");
				$str=DispParam($str, "AGREEMENT_SIGNED");
			} else {
				$str=DispParam($str, "NOT_AGREEMENT_SIGNED");
				$str=DispParamNone($str, "AGREEMENT_SIGNED");
			}
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_J");
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_E");
		}
		else {
			$str=DispParam($str, "NOT_AGREEMENT_SIGNED");
			$str=DispParamNone($str, "AGREEMENT_SIGNED");
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_J");
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_E");
		}
		/*
		if($print!="on") echo "<!--AGREEMENT_SIGNED-->";
			$str=DispParam($str, "AGREEMENT_SIGNED");
			$str=DispParamNone($str, "NOT_AGREEMENT_SIGNED");
		*/
	} else {
		if($print!="on") echo "<!--NOT_AGREEMENT_SIGNED-->";
			$str=DispParam($str, "NOT_AGREEMENT_SIGNED");
			$str=DispParamNone($str, "AGREEMENT_SIGNED");
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_J");
			$str=DispParamNone($str, "AGREEMENT_SIGNED_WITH_E");
	}

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
	$str=str_replace("[CATE_LIST]",json_encode($cate_list,JSON_UNESCAPED_UNICODE),$str);
	// $str=str_replace("[CATE1_VAL]",$FieldValue[114],$str);
	// $str=str_replace("[CATE2_VAL]",$FieldValue[115],$str);
	// $str=str_replace("[CATE3_VAL]",$FieldValue[116],$str);
	// $str=str_replace("[CATE4_VAL]",$FieldValue[117],$str);
	$str=str_replace("[CATE1a_VAL]",$FieldValue[114],$str);
	$str=str_replace("[CATE2a_VAL]",$FieldValue[115],$str);
	$str=str_replace("[CATE3a_VAL]",$FieldValue[116],$str);
	$str=str_replace("[CATE4a_VAL]",$FieldValue[117],$str);
	
	$str=str_replace("[CATE1b_VAL]",$FieldValue[43],$str);
	$str=str_replace("[CATE2b_VAL]",$FieldValue[44],$str);
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
	
//if($print!="on") echo "<!--M1_DRDO01:".$FieldValue[35]."-->";
	if($FieldValue[35]=="M1_DRDO01:仮登録中"){
		$str=DispParam($str, "KARI_REGIST");
		if($print!="on") echo "<!--KARI_REGIST-->";
		
	} else if($FieldValue[35]=="M1_DRDO01:審査依頼" || $FieldValue[35]=="M1_DRDO01:要再審査"){
		$str=DispParam($str, "SHINSA_REGIST");
		if($print!="on") echo "<!--SHINSA_REGIST-->";
	} else {
		// ここには登録変更審査中も含む
		$str=DispParam($str, "HON_REGIST");
		if($print!="on") echo "<!--HON_REGIST-->";
	}
	$str=DispParamNone($str, "KARI_REGIST");
	$str=DispParamNone($str, "SHINSA_REGIST");
	$str=DispParamNone($str, "HON_REGIST");

	// Agree2の会社名
	$str=str_replace("[M1_DVAL01]",$FieldValue[5],$str);
	// サインしたのはどっちか
	if(strpos($FieldValue[214], '私は') !== false) {
		$str=DispParam($str, "SIGN-JP");
		$str=DispParamNone($str, "SIGN-EN");
	}
	else if(strpos($FieldValue[214], 'I have') !== false) {
		$str=DispParamNone($str, "SIGN-JP");
		$str=DispParam($str, "SIGN-EN");
	}
	else {
		$str=DispParamNone($str, "SIGN-JP");
		$str=DispParamNone($str, "SIGN-EN");
	}

	// 初回ポップアップ制御
	$popup_flg = $FieldValue[261];
	if($popup_flg != '3') {
		$StrSQL=" UPDATE DAT_M1 SET M1_ETC124 = '3' ";
		$StrSQL.=" WHERE MID = '".$_SESSION['MID']."'";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			exit;
		}
	}
	if(isset($_GET['init_popup']) && $_GET['init_popup'] == 'on') {
		$popup_flg = '1';
	}
	$str=str_replace("[POPUP_FLG]",$popup_flg,$str);

	// ステータス
	$str=str_replace("[M1_STATUS]",$FieldValue[35],$str);

	if($print=="on"){

		// old
		//$pdf = new TCPDF('P');// 縦向き
		//$pdf ->SetFont('notosansjpvariablefont_wght', '', 12);// 英語日本語フォント
		//$pdf ->SetMargins(12,0,12);// ページマージン（left,top,right）
		//$pdf ->setPrintHeader(false);// ページヘッダー（なし）
		//$pdf ->setPrintFooter(false);// ページフッター（なし）
		//$pdf ->addPage();
		//$pdf ->writeHTML($str);

		// new 2024.09.11
		$font_size = 12;
		$pdf = new TCPDF('P');// 縦向き
		$pdf ->AddFont('caveatvariablefont_wght');//caveat(全ウェイト、CSS上の名称 Caveat-Regular)
		$pdf ->AddFont('kozminpro');//kozminpro(全ウェイト、CSS上の名称 KozMinPro-Regular-Acro, Kozuka Mincho Pro)
		$pdf ->AddFont('genshingothic');// GenShinGothic(全ウェイト、CSS上の名称 GenShinGothic-Regular)
        $pdf ->SetFont('genshingothic', '', $font_size);// GenShinGothic(bodyにfontプロパティ設定とほぼ同義)
		$pdf ->SetMargins(12,8,12);// ページマージン（left,top,right）
		$pdf ->setPrintHeader(false);// ページヘッダー（なし）
		$pdf ->setPrintFooter(false);// ページフッター（なし）
		$pdf ->setListIndentWidth(4);// リストタグのインデント調整
		$pdf ->addPage();

		// 日本語の場合禁則処理を挟む
		if($_GET['param'] == 'Agreements2j') {
			$str = getKinsokuHTML($str);
		}

		$pdf ->writeHTML($str);

		ob_end_clean();

		$pdf->Output(date('Ymd')."_".$_SESSION['MID'].".pdf", "D");

	} else {
		print $str;
	}

	return $function_ret;
} 


//=========================================================================================================
//名前 入力後のエラーチェック（エラーがない場合は空を指定）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ErrorCheckM1()
{
	//extract($GLOBALS);
	eval(globals());

	$function_ret="";

	//Company Information
	$form_ary1=array(
		"M1_DVAL01"=>$FieldValue[5],
		"M1_DVAL22"=>$FieldValue[129],
		"M1_DVAL23"=>$FieldValue[130],
		"M1_DVAL24"=>$FieldValue[131],
		"M1_ETC97"=>$FieldValue[234],
		"M1_DVAL07"=>$FieldValue[11],
		"M1_DVAL04"=>$FieldValue[8],
		"M1_DSEL03"=>$FieldValue[27],
		"M1_DVAL14"=>$FieldValue[121],
		"M1_DVAL15"=>$FieldValue[122],
		"M1_DVAL16"=>$FieldValue[123],
		"M1_DVAL17"=>$FieldValue[124],
		"M1_DVAL18"=>$FieldValue[125],
		"M1_DTXT03"=>$FieldValue[17],
		"M1_DTXT04"=>$FieldValue[18],
		"M1_ETC128"=>$FieldValue[265],
		"M1_ETC129"=>$FieldValue[266],
		"M1_ETC130"=>$FieldValue[267],
		"M1_ETC131"=>$FieldValue[268],
		"M1_ETC132"=>$FieldValue[269],
		"M1_ETC133"=>$FieldValue[270],
		"M1_ETC08"=>$FieldValue[105],
		"M1_ETC91"=>$FieldValue[228],
		"M1_DTXT25"=>$FieldValue[152],
		"M1_DTXT28"=>$FieldValue[155],
		"M1_ETC10"=>$FieldValue[107],
		"M1_ETC21"=>$FieldValue[158],
		"M1_ETC24"=>$FieldValue[161],
		"M1_DSEL07"=>$FieldValue[31],
		"M1_ETC99"=>$FieldValue[236]
	);

	//Banking Details
	$form_ary2=array(
		"M1_ETC31"=>$FieldValue[168],
		"M1_ETC32"=>$FieldValue[169],
		"M1_ETC34"=>$FieldValue[171],
		"M1_ETC35"=>$FieldValue[172],
		"M1_ETC36"=>$FieldValue[173],
		//"M1_ETC37"=>$FieldValue[174],
		"M1_ETC38"=>$FieldValue[175],
		"M1_DSEL10"=>$FieldValue[34],
		"M1_ETC39"=>$FieldValue[176],
		"M1_ETC40"=>$FieldValue[177],
		"M1_ETC41"=>$FieldValue[178],
		"M1_ETC42"=>$FieldValue[179],
		"M1_ETC100"=>$FieldValue[237],
		"M1_ETC101"=>$FieldValue[238],
		"M1_ETC43"=>$FieldValue[180],
		"M1_ETC44"=>$FieldValue[181],
		"M1_ETC45"=>$FieldValue[182],
		"M1_ETC47"=>$FieldValue[184],
		"M1_ETC48"=>$FieldValue[185],
		"M1_ETC49"=>$FieldValue[186],
		"M1_DRDO02"=>$FieldValue[36],
		"M1_DRDO03"=>$FieldValue[37],
		"M1_DRDO04"=>$FieldValue[38],
		"M1_DRDO05"=>$FieldValue[39]
		//"M1_DRDO07"=>$FieldValue[41]
	);

	//Agreements
	$form_ary3=array(
		"M1_ETC77"=>$FieldValue[214],
		"M1_ETC78"=>$FieldValue[215]
	);


	$form_flg1=0;
	foreach ($form_ary1 as $key => $val) {
		//echo "<!--key:$key, val:$val-->";
		if($val=="" || is_null($val)){
			$form_flg1=1;
		}
	}


	$form_flg2=0;
	foreach ($form_ary2 as $key => $val) {
		//echo "<!--key:$key, val:$val-->";
		if($val=="" || is_null($val)){
			//echo "<!--key:$key, val:$val-->";
			$form_flg2=1;
		}
	}

	$form_flg3=0;
	foreach ($form_ary3 as $key => $val) {
		//echo "<!--key:$key, val:$val-->";
		if($val=="" || is_null($val)){
			$form_flg3=1;
		}
	}

	if($form_flg1==1){
		$function_ret.="<span>Please fill in the required fields of the Company Information.</span><br>";
		
	}
	if($form_flg2==1){
		$function_ret.="<span>Please fill in the required fields of the Banking Details</span><br>";

	}
	if($form_flg3==1){
		$function_ret.="<span>Please register your signature.</span><br>";

	}

	//if($FieldValue[5]==""){
	//	$function_ret.="<span>Company Informationを登録してください。</span><br>";
	//	
	//}
	//if($FieldValue[168]==""){
	//	$function_ret.="<span>Banking Detailsを登録してください。</span><br>";
	//}
	//if($FieldValue[218]==""){
	//	$function_ret.="<span>Please register your signature.</span><br>";
	//	//$function_ret.="<span>署名登録してください。</span><br>";
	//	
	//}



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
			if (isset($_POST[$FieldName[$i]])) {
				$FieldValue[$i]=(str_replace("\\","",$_POST[$FieldName[$i]]));
			}

		}

// 		if($FieldName[$i]=="M1_ETC46"){
			//if($print!="on") echo "<!--M1_ETC46_ATT:".$FieldAtt[$i]."-->";
// 		}

		//if($print!="on") echo "<!--mode:".$mode."-->";

		if ($FieldAtt[$i]==4 && ($mode=="save" || $mode=="shinsa")) {

			$exts = explode("[/\\.]", $_FILES["EP_".$FieldName[$i]]['name']);
			$n = count($exts) - 1;
			$extention = $exts[$n];
			if ($extention=="jpeg") {
				$extention="jpg";
			} 
// 			if($FieldName[$i]=="M1_ETC46"){
				//if($print!="on") echo "<!--M1_ETC46:".$_FILES["EP_".$FieldName[$i]]['name']."-->";
// 			}
// 			if($FieldName[$i]=="M1_DRDO06"){
				//if($print!="on") echo "<!--M1_DRDO06:".$_FILES["EP_".$FieldName[$i]]['name']."-->";
// 			}
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
				move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], "../a_m1/data/".$filename);
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
function LoadData($key)
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

	// Banking Detailの初回登録
	if($_POST['submode'] == 'banking_details') {
		$FieldValue[112] = '1';
	}

	// SQLインジェクション対策
	// HTMLエスケープ処理（SQL書き込み）
	$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item=="") {
		$FieldValue[96]=date("Y/m/d H:i:s");
		$FieldValue[97]=date("Y/m/d H:i:s");

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
		$FieldValue[97]=date("Y/m/d H:i:s");

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
if($print!="on") echo "<!--".$StrSQL."-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
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
