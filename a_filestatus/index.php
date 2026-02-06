<?php

session_start();
require "../config.php";
require "../base_a.php";
require './config.php';

require("./func_export.php");

//pdf対応
require_once('../TCPDF/tcpdf.php');

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
		//pdf対応
		//preview_type=cbのとき、CB宛て見積り書テンプレート
		//preview_type=rのとき、研究者宛て見積り書テンプレート
		$preview_type=$_GET['preview_type'];
		//pdf_action=downloadのとき、pdfをダウンロードさせる
		$pdf_action=$_GET['pdf_action'];
		//pdfのボタンエリアの表示の仕方のバリエーション指定
		$pdf_btn_version=$_GET['btn_version'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		//pdf対応
		$preview_type=$_POST['preview_type'];
		$pdf_action=$_POST['pdf_action'];
		//pdfのボタンエリアの表示の仕方のバリエーション指定
		$pdf_btn_version=$_POST['btn_version'];
	}

	if ($mode==""){
		$mode="list";
	}

	//分割見積り時の同期処理用
	if($key!=""){
		$StrSQL="SELECT * from DAT_FILESTATUS where ID='".$key."' and STATUS='見積り送付' ";
		$StrSQL.=" and (M2_PAY_TYPE='Split' or M2_PAY_TYPE='Milestone') ";
		$rs_sync1=mysqli_query(ConnDB(),$StrSQL);
		$item_sync1 = mysqli_fetch_assoc($rs_sync1);
		$item_sync_num1=mysqli_num_rows($rs_sync1);
		if($item_sync_num1>0){
			$div_id=$item_sync1["DIV_ID"];
			$tmp="";
			$tmp=explode("-", $div_id);
		//echo "<!--";
		//var_dump($tmp);
		//echo "-->";
			if(count($tmp)==3 && $tmp[0]!="" && $tmp[1]!=""){
				$invoice_no=$tmp[0]."-".$tmp[1];
				$StrSQL="SELECT * from DAT_FILESTATUS where DIV_ID LIKE '".$invoice_no."-PART%' ";
				$StrSQL.=" and MID1='".$item_sync1["MID1"]."' and MID2='".$item_sync1["MID2"]."' ";
				$StrSQL.=" and STATUS='見積り送付' ";
				$rs_sync2=mysqli_query(ConnDB(),$StrSQL);
				$item_sync_num2=mysqli_num_rows($rs_sync2);
				$sync_item_ary=array();
				while($item_sync2 = mysqli_fetch_assoc($rs_sync2)){
					echo "<!--同期アイテム：".$item_sync2["ID"]."-->";
					$sync_item_ary[]=$item_sync2["ID"];
				}
			}
		}
	}

	switch ($mode){
		case "new":
			InitData();

			// 初期値をいじる
			$shodan_id0 = explode('：', $word);
			$shodan_id = $shodan_id0[1];
			$StrSQL="SELECT * from DAT_FILESTATUS where SHODAN_ID = '".$shodan_id."' ORDER BY ID DESC;";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$latest_filestatus = mysqli_fetch_assoc($rs);
			$FieldValue[1] = $shodan_id;
			$FieldValue[2] = $latest_filestatus['MID1'];
			$FieldValue[3] = $latest_filestatus['MID2'];
			$FieldValue[32] = date('Y/m/d H:i:s');
			$FieldValue[33] = date('Y/m/d H:i:s');

			break;
		case "edit":
		case "disp_frame1": //pdf対応
		case "disp_frame2": //pdf対応
		case "disp_frame3": //pdf対応
		case "disp_frame4": //pdf対応
			LoadData($key);
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
				$FieldValue[33] = date('Y/m/d H:i:s');
				$msg=ErrorCheck();
				if ($msg==""){
					SaveData($key,$sync_item_ary,$pdf_action);
					$url=BASE_URL . "/a_filestatus/?word=".$word."&page=".$page;
					header("Location: {$url}");
					//$mode="list";
					//if ($page==""){
					//	$page=1;
					//} 
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
			break;
		case "export":
			ExportData();
			exit;
		case "import":
			ImportData($obj,$a,$b,$key,$mode);
			$mode="list";
			break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$sync_item_ary,$preview_type,$pdf_action,$pdf_btn_version);

	return $function_ret;
}

//=========================================================================================================
//名前 
//機能\ 
//引数 $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート2');
	
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
//引数 $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1_2($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート3');
	
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

	$mailto = SENDER_EMAIL;

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMail_v:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}




//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$sync_item_ary,$preview_type,$pdf_action,$pdf_btn_version)
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
			case "disp_frame1":
				//pdf対応
				//見積書のpdf表示用モード
				$filename="disp_frame1.html";
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "disp_frame2":
				//pdf対応
				//発注書のpdf表示用モード
				$filename="disp_frame2.html";
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "disp_frame3":
				//pdf対応
				//請求書のpdf表示用モード
				$filename="disp_frame3.html";
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "disp_frame4":
				//pdf対応
				//請求書のpdf表示用モード
				$filename="disp_frame4.html";
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
		} 

		$fp=$DOCUMENT_ROOT.$filename;
		$str=@file_get_contents($fp);

		//発注書のテンプレート読み込んだら後の処理はdisp_frame1と同じ
		if($mode=="disp_frame2"){
			$mode="disp_frame1";
		}
		//請求書のテンプレート読み込んだら後の処理はdisp_frame1と同じ
		if($mode=="disp_frame3"){
			$mode="disp_frame1";
		}
		//追加請求書のテンプレート読み込んだら後の処理はdisp_frame1と同じ
		if($mode=="disp_frame4"){
			$mode="disp_frame1";
		}

		//pdf対応
		//disp_frame1モードの場合、ヘッダ―などを管理画面以外のものにかえるために、通常の処理からはずす。
		if($mode!="disp_frame1"){
			$str = MakeHTML($str,1,$lid);
		}
		//$str = MakeHTML($str,1,$lid);

		//pdf対応
		if($mode=="disp_frame1"){
			if($preview_type=="cb"){
				$pdf_view=@file_get_contents($DOCUMENT_ROOT."pdf_cb.html");
			}else if($preview_type=="r"){
				$pdf_view=@file_get_contents($DOCUMENT_ROOT."pdf_r.html");
			}else if($preview_type=="h"){
				$pdf_view=@file_get_contents($DOCUMENT_ROOT."pdf_h.html");
			}else if($preview_type=="s"){
				$pdf_view=@file_get_contents($DOCUMENT_ROOT."pdf_s.html");
			}
			else if($preview_type=="s_add"){
				$pdf_view=@file_get_contents($DOCUMENT_ROOT."pdf_s_add.html");
			}
			$str=str_replace("[PDF_VIEW]",$pdf_view,$str);

			$pdf_shoan_id=$FieldValue[1];
			$pdf_mid1=$FieldValue[2];
			$pdf_mid2=$FieldValue[3];
			$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID='".$pdf_shoan_id."' ";
			$StrSQL.=" and MID1_LIST='".$pdf_mid1."' ";
			$StrSQL.=" and MID2='".$pdf_mid2."' ";
			echo "<!--pdf SQL:$StrSQL-->";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$pdf_shodan_item=mysqli_fetch_assoc($rs);
			$str=str_replace("[PDF_SHODAN_TITLE]",$pdf_shodan_item["TITLE"],$str);
			
			$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$pdf_mid1."' ";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$pdf_m1_item=mysqli_fetch_assoc($rs);

			$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$pdf_mid2."' ";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$pdf_m2_item=mysqli_fetch_assoc($rs);

			$StrSQL="SELECT * FROM DAT_M3 WHERE MID='".$pdf_m2_item["M2_DVAL15"]."' ";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$pdf_m3_item=mysqli_fetch_assoc($rs);

			foreach ($pdf_m1_item as $idx => $val) {
				$str=str_replace("[PDF_M1-".$idx."]",$val,$str);
				$str=str_replace("[D-PDF_M1-".$idx."]", str_replace($idx.":","",$val), $str);
			}
			foreach ($pdf_m2_item as $idx => $val) {
				$str=str_replace("[PDF_M2-".$idx."]",$val,$str);
				$str=str_replace("[D-PDF_M2-".$idx."]", str_replace($idx.":","",$val), $str);
			}
			foreach ($pdf_m3_item as $idx => $val) {
				$str=str_replace("[PDF_M3-".$idx."]",$val,$str);
				$str=str_replace("[D-PDF_M3-".$idx."]", str_replace($idx.":","",$val), $str);
			}

			//サプライヤーのbill toの1番目のアドレス
			$pdf_address1="";
			$pdf_address1.=$pdf_m1_item["M1_ETC34"];
			$pdf_address1.=!empty($pdf_m1_item["M1_ETC35"]) ? ", ".$pdf_m1_item["M1_ETC35"] : "";
			$pdf_address1.=!empty($pdf_m1_item["M1_ETC36"]) ? ", ".$pdf_m1_item["M1_ETC36"] : "";
			$pdf_address1.=!empty($pdf_m1_item["M1_DSEL10"]) ? ", ".str_replace("M1_DSEL10:","",$pdf_m1_item["M1_DSEL10"]) : "";
			$str=str_replace("[PDF_ADDRESS1]",$pdf_address1,$str);

			//見積り送付時に選択されたbill to
			$pdf_address2="";
			$pdf_address2.=$FieldValue[66];
			$pdf_address2.=!empty($FieldValue[67]) ? ", ".$FieldValue[67] : "";
			$pdf_address2.=!empty($FieldValue[68]) ? ", ".$FieldValue[68] : "";
			$pdf_address2.=!empty($FieldValue[70]) ? ", ".$FieldValue[70] : "";
			$str=str_replace("[PDF_ADDRESS2]",$pdf_address2,$str);

			//サプライヤーのCompany Information (基本情報)のAddress Line 1～Country
			$pdf_address3="";
			$pdf_address3.=$pdf_m1_item["M1_ETC129"];
			$pdf_address3.=!empty($pdf_m1_item["M1_ETC130"]) ? ", ".$pdf_m1_item["M1_ETC130"] : "";
			$pdf_address3.=!empty($pdf_m1_item["M1_ETC131"]) ? ", ".$pdf_m1_item["M1_ETC131"] : "";
			$pdf_address3.=!empty($pdf_m1_item["M1_ETC132"]) ? ", ".$pdf_m1_item["M1_ETC132"] : "";
			$pdf_address3.=!empty($pdf_m1_item["M1_ETC133"]) ? ", ".str_replace("M1_ETC133:","",$pdf_m1_item["M1_ETC133"]) : "";
			$str=str_replace("[PDF_ADDRESS3]",$pdf_address3,$str);

			//CBの見積書に表示するShip to
			$pdf_address4="";
			if($pdf_m1_item["M1_ETC133"]!="M1_ETC133:Japan"){
				//CBの物流センターの住所
				$pdf_address4="Cosmo Bio Shinsuna Distribution Center ShinSuna 1-Chome, Koto-Ku,Tokyo 136-0075, Japan 3F 12-39 TEL: 81-3-5632-9608";
			}else{
				$pdf_address4="";
				$pdf_address4.=$pdf_m3_item["M2_DVAL03"];
				$pdf_address4.=!empty($pdf_m3_item["M2_DVAL07"]) ? ", ".$pdf_m3_item["M2_DVAL07"] : "";
				$pdf_address4.=!empty($pdf_m3_item["M2_DVAL06"]) ? ", ".$pdf_m3_item["M2_DVAL06"] : "";
				$pdf_address4.=!empty($pdf_m3_item["M2_DVAL05"]) ? ", ".$pdf_m3_item["M2_DVAL05"] : "";
				$pdf_address4.=!empty($pdf_m3_item["M2_DVAL08"]) ? ", ".$pdf_m3_item["M2_DVAL08"] : "";
			}
			$str=str_replace("[PDF_ADDRESS4]",$pdf_address4,$str);
			//Number of Payments
			//M2_PAY_TYPE
			if($FieldValue[16]=="Once"){
				$number_of_payments=1;
			}else if($FieldValue[16]=="Split"){
				$number_of_payments=2;
			}else if($FieldValue[16]=="Milestone"){
				$tmp="";
				$tmp=explode("-", $FieldValue[54]);
				$part="";
				$pre_part="";
				if(count($tmp)==3){
					$part=$tmp[2];
					$pre_part=$tmp[0]."-".$tmp[1];
				}
				$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_IMPORT_FEE FROM DAT_FILESTATUS WHERE ";
				$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
				$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
				$StrSQL.=" AND STATUS='".$FieldValue[34]."' ";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$number_of_payments=mysqli_num_rows($rs);
			}
			$str=str_replace("[NUMBER_OF_PAYMENTS]",$number_of_payments,$str);


			//送信した「請求書（研究者）」のデータの情報。
			//請求書送信後データ表示用。
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$pdf_shoan_id."' ";
			$StrSQL.=" and MID1='".$pdf_mid1."' ";
			$StrSQL.=" and MID2='".$pdf_mid2."' ";
			$StrSQL.=" and DIV_ID='".$FieldValue[54]."' ";
			$StrSQL.=" and (STATUS='請求' or STATUS='請求書送付(一括前払い)' or STATUS='請求書送付(前払い)') ";
			$StrSQL.=" and S_STATUS='請求（研究者）' ";
			echo "<!--pdf SQL:$StrSQL-->";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$pdf_seikyu_r_item=mysqli_fetch_assoc($rs);
			$pdf_invoice_url2=$filepath1.$pdf_seikyu_r_item["ID"]."/".$pdf_seikyu_r_item["S2_FILE"];
			$str=str_replace("[PDF_INVOICE_URL1]",$pdf_invoice_url2,$str);
			$str=str_replace("[PDF_S2_FILE1]",$pdf_seikyu_r_item["S2_FILE"],$str);
			$str=str_replace("[PDF_S2_MESSAGE1]",$pdf_seikyu_r_item["S2_MESSAGE"],$str);

			//「納品確認」データに保存した「請求書（研究者）」のデータの情報。
			//請求書送信前データ表示用。
			$pdf_invoice_url1=$filepath1.$FieldValue["0"]."/".$FieldValue[30];
			$str=str_replace("[PDF_INVOICE_URL2]",$pdf_invoice_url1,$str);
			$str=str_replace("[PDF_S2_FILE2]",$FieldValue[30],$str);
			$str=str_replace("[PDF_S2_MESSAGE2]",$FieldValue[31],$str);

			if($preview_type=="s" && $pdf_btn_version=="1"){
				//請求書送信後
				//$str=DispParam($str, "SEIKYU_PREVIEW1");
				//$str=DispParamNone($str, "SEIKYU_PREVIEW2");

				//どちらが要求されてるのかわからないため一端フル表示
				$str=DispParam($str, "SEIKYU_PREVIEW1");
				$str=DispParam($str, "SEIKYU_PREVIEW2");

			}else if($preview_type=="s" && $pdf_btn_version=="2"){
				//請求書送信前。sendボタン付き表示。
				//$str=DispParamNone($str, "SEIKYU_PREVIEW1");
				//$str=DispParam($str, "SEIKYU_PREVIEW2");

				//どちらが要求されてるのかわからないため一端フル表示
				$str=DispParam($str, "SEIKYU_PREVIEW1");
				$str=DispParam($str, "SEIKYU_PREVIEW2");
			
			}else{
				$str=DispParamNone($str, "SEIKYU_PREVIEW1");
				$str=DispParam($str, "SEIKYU_PREVIEW2");
			}
		}

		//追加請求
		$str=str_replace("[PDF_S_ADD_CHARGE2]",$FieldValue[90],$str);

		//発注書（決済者発注承認用）フォーム用
		//H3A_MESSAGE,H_COMMENT,DIV_ID,STATUS
		if($FieldValue[34]=="決済者発注承認" || $FieldValue[34]=="発注依頼"){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE DIV_ID='".$FieldValue[54]."' ";
			$StrSQL.=" AND STATUS='発注依頼' ";
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item=mysqli_fetch_assoc($h_rs);

			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$m2_quote_no="";
			$m2_version="";

			$str=str_replace("[PRE_H_COMMENT]",$h_item["H_COMMENT"],$str);
			$str=str_replace("[PRE_H3A_MESSAGE]",$FieldValue[83],$str);

			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$h_item["H_M2_ID"]."' ";
			$StrSQL.=" AND STATUS='見積り送付' ";
			$mitsu_rs=mysqli_query(ConnDB(),$StrSQL);
			$mitsu_item=mysqli_fetch_assoc($mitsu_rs);

			$SCNo_ary["SCNo_yy"]=$mitsu_item["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$mitsu_item["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$mitsu_item["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$mitsu_item["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$mitsu_item["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$mitsu_item["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);
			$m2_quote_no=$mitsu_item["M2_QUOTE_NO"];
			$m2_version=$mitsu_item["M2_VERSION"];
			//echo "<!--m2_quote_no:$m2_quote_no-->";
			//echo "<!--SCNo_str:$SCNo_str-->";
			$str=str_replace("[PRE_SCNO]",$SCNo_str."-Version".$m2_version,$str);
		}
		

		//手動で商談を「完了」にするボタンの表示非表示
		if($FieldValue[34]=="請求" || $FieldValue[34]=="請求書送付(一括前払い)" 
			|| $FieldValue[34]=="請求書送付(前払い)" || $FieldValue[34]=="研究者が納品承認(一括前払い)"){
			$str=DispParam($str, "KANRYO_BTN");
		}else{
			$str=DispParamNone($str, "KANRYO_BTN");
		}

		if ($mode=="new"){
			$str=DispParam($str, "NEWDATA");
			$str=DispParamNone($str, "EDITDATA");
		} else {
			$str=DispParamNone($str, "NEWDATA");
			$str=DispParam($str, "EDITDATA");
		} 

		//大カテゴリー
		$StrSQL="SELECT BIG FROM DAT_CCATE2 ORDER BY BIG,cast(sort as SIGNED )";
		$FieldParam[63]="";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		while ($item = mysqli_fetch_assoc($rs)) {
			if(strpos($FieldParam[63], $item["BIG"]) !== false){
				continue;
			}
			if($FieldParam[63]!=""){
				$FieldParam[63].="::";
			}
			$FieldParam[63].=$item["BIG"];
		}

		//小カテゴリー
		$StrSQL="SELECT SMALL FROM DAT_CCATE2 ORDER BY BIG,cast(sort as SIGNED )";
		$FieldParam[64]="";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		while ($item = mysqli_fetch_assoc($rs)) {

			if($FieldParam[64]!=""){
				$FieldParam[64].="::";
			}
			$FieldParam[64].=$item["SMALL"];
		}


		for ($i=0; $i<=$FieldMax; $i=$i+1){

			//納品形態
			if($FieldName[$i]=="M2_NOHIN_TYPE"){
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul class='mlist25p'>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"".$FieldName[$i]."[]\" value=\"".$tmp[$j]."\"><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$tmp=explode("\t",$FieldValue[$i]);
					for ($j=0; $j<count($tmp); $j=$j+1) {
						$str=str_replace("\"".$tmp[$j]."\"","\"".$tmp[$j]."\" checked",$str);
					}
				} 

				//納品形態の日本語表示
				$output_str="";
				if($FieldName[$i]=="M2_NOHIN_TYPE" && ($FieldValue[$i]=="data" || $FieldValue[$i]=="Data") ){
					$output_str="データ";

				}else if($FieldName[$i]=="M2_NOHIN_TYPE" && ($FieldValue[$i]=="goods" || $FieldValue[$i]=="Goods") ){
					$output_str="物品";

				}
				$str=str_replace("[JP-M2_NOHIN_TYPE]", $output_str, $str);
			}

			//支払い条件の日本語表示
			$output_str="";
			if($FieldName[$i]=="M2_PAY_TYPE"){
				if($FieldValue[$i]=="Once"){
					$output_str="1回払い";

				}else if($FieldValue[$i]=="Split"){
					$output_str="2回払い";

				}else if($FieldValue[$i]=="Milestone"){
					$output_str="マイルストーン払い";
				}
				$str=str_replace("[JP-M2_PAY_TYPE]", $output_str, $str);
			}

			if ($FieldAtt[$i]==4){
				if ($FieldValue[$i]==""){
					//$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
					$str=str_replace("[".$FieldName[$i]."]","",$str);
					//$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
					$str=str_replace("[D-".$FieldName[$i]."]","",$str);
				} 

				//if(strstr($FieldValue[$i],"s.gif") == true){
				if($FieldValue[$i] == ''){
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
			if ($FieldAtt[$i]=="1"){
				$strtmp="";
				$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
				$tmp=explode("::",$FieldParam[$i]);
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<option value='".$FieldName[$i].":".$tmp[$j]."'>".$tmp[$j]."</option>";

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

		//MID1:[2]
		//MID2:[3]
		$str=str_replace("[MID1_NEW]",convert_mid($FieldValue[2]),$str);
		$str=str_replace("[MID2_NEW]",convert_mid($FieldValue[3]),$str);

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
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);


		//保存時の同期対象アイテム表示
		$sync_item_str="※保存の際に同時編集されるデータのID : ";
		$i=0;
		foreach ($sync_item_ary as $val) {
			if($i==0){
				$sync_item_str.=$val;
			}else{
				$sync_item_str.=",".$val;
			}
			$i++;
		}
		$str=str_replace("[SYNC_ITEMS]",$sync_item_str,$str);



		//決済者承認フラグのフロー
		//・決済者なしパターン：「発注依頼」→運営による「受注承認」
		//・決済者ありパターン：「発注依頼」→決裁者による「決済者発注承認」→運営による「受注承認」
		//上記の仕様変更のためここでは以下を実装。
		//「決裁者承認フラグ（KESSAI_SYONIN）」が「なし」もしくは、決済者が登録されてない場合、ステータス「発注依頼」で、「＜発注書（決済者発注承認用）＞」のフォームを表示する（本来、決済者発注承認のフォーム）。
		$StrSQL="SELECT ID,MID,M2_DVAL15,KESSAI_SYONIN FROM DAT_M2 where MID='".$FieldValue[3]."' order by ID desc;";
		$m2_rs=mysqli_query(ConnDB(),$StrSQL);
		$m2_item = mysqli_fetch_assoc($m2_rs);
		//echo "<!--m2_item:";
		//var_dump($m2_item);
		//echo "-->";
		$StrSQL="SELECT ID,MID FROM DAT_M3 where MID='".$m2_item["M2_DVAL15"]."' ";
		$StrSQL.=" and MID IS NOT NULL and MID!='' order by ID desc;";
		$kessai_rs=mysqli_query(ConnDB(),$StrSQL);
		$kessai_item = mysqli_fetch_assoc($kessai_rs);
		$kessai_num = mysqli_num_rows($kessai_rs);

		//echo "<!--kessai_item:";
		//var_dump($kessai_item);
		//echo "-->";

		// 関係ないステータスの入力欄を非表示にする
		if($key == '') {
			// 新規
			$str=DispParam($str, "FORM_T");
			$str=DispParam($str, "FORM_M1");
			$str=DispParam($str, "FORM_M1B");
			$str=DispParam($str, "FORM_M2");
			$str=DispParam($str, "FORM_H");
			$str=DispParam($str, "FORM_N");
			$str=DispParam($str, "FORM_S1");
			$str=DispParam($str, "FORM_S2");
		}
		else {
			switch($FieldValue[34]) {
				case '問い合わせ':
					$str=DispParam($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
				case '見積り依頼':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParam($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
				case '再見積り依頼':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParam($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
				case '見積り送付':
				case '運営手数料追加':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParam($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
				case '発注依頼':
					if( $kessai_num<=0 || $m2_item["KESSAI_SYONIN"]=="KESSAI_SYONIN:なし"){
						//決済者が登録されてない、もしくは決済者承認フラグが「なし」の場合、
						//「決済者発注承認」のフォームを表示
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParamNone($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParamNone($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_C_OK1");
						$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
						$str=DispParam($str, "FORM_K_OK");
						$str=DispParamNone($str, "FORM_DONE1");
						$str=DispParamNone($str, "FORM_DONE2");
					}else{
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParam($str, "FORM_H");
						$str=DispParamNone($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParamNone($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_C_OK1");
						$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_K_OK");
						$str=DispParamNone($str, "FORM_DONE1");
						$str=DispParamNone($str, "FORM_DONE2");
					}
					break;
				case '発注承認':
				case '受注承認':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParam($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
				case '決済者発注承認':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParam($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;


				case 'データ納品':
				case '物品納品':
				case 'サプライヤーが納品(一括前払い)':
				//case '納品確認':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParam($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
				case '納品確認':
				case '受注承認(一括前払い)':
				case '受注承認(前払い)':
				case '研究者が納品承認(一括前払い)':
					$str=DispParam($str, "SEIKYUSYO_PREVIEW_AT_SEIKYUSOY_R");

					//echo "<!--FieldValue[54]:".$FieldValue[54]."-->";
					//echo "<!--checkDIV_ID:".checkDIV_ID($FieldValue[54])."-->";
					$ck_div_id=checkDIV_ID($FieldValue[54]);
					//一括払い
					if($ck_div_id==""){
						$StrSQL="SELECT * FROM DAT_FILESTATUS ";
						$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND (STATUS='請求' OR STATUS='請求書送付(一括前払い)' OR STATUS='請求書送付(前払い)') ";
						$StrSQL.=" AND S_STATUS='請求（研究者）' ";
						$rs=mysqli_query(ConnDB(),$StrSQL);
						echo "<!--StrSQL3:$StrSQL-->";
						$item_num=mysqli_num_rows($rs);

					//分割払い
					}else{
						$StrSQL="SELECT * FROM DAT_FILESTATUS ";
						$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND (STATUS='請求' OR STATUS='請求書送付(一括前払い)' OR STATUS='請求書送付(前払い)') ";
						$StrSQL.=" AND DIV_ID='".$ck_div_id."' ";
						$StrSQL.=" AND S_STATUS='請求（研究者）' ";
						$rs=mysqli_query(ConnDB(),$StrSQL);
						echo "<!--StrSQL3:$StrSQL-->";
						$item_num=mysqli_num_rows($rs);
					}
					
					if($item_num==0){
						//納品確認時に研究者への請求データが存在しなかったらそこから送れるように
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParam($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParam($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_C_OK1");
						$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_K_OK");
						$str=DispParamNone($str, "FORM_DONE1");
						$str=DispParamNone($str, "FORM_DONE2");
					}else{
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParam($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParamNone($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_C_OK1");
						$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
						$str=DispParam($str, "FORM_S2-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_K_OK");
						$str=DispParamNone($str, "FORM_DONE1");
						$str=DispParamNone($str, "FORM_DONE2");
					}
					break;
				case '請求':
				case '請求書送付(一括前払い)':
				case '請求書送付(前払い)':
					//$StrSQL="SELECT ID,DIV_ID,STATUS FROM DAT_FILESTATUS WHERE ";
					//$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
					//$StrSQL.=" AND DIV_ID = '".$FieldValue[54]."' ";
					//$StrSQL.=" AND STATUS='完了' ";
					//$kanryo_rs=mysqli_query(ConnDB(),$StrSQL);
					//$kanryo_item=mysqli_fetch_assoc($kanryo_rs);
					////echo "<!--StrSQL:$StrSQL-->";
					////echo "<!--kanryo:";
					////var_dump($kanryo_item);
					////echo "-->";
					//$kanryo_num=mysqli_num_rows($kanryo_rs);

					$str=DispParamNone($str, "SEIKYUSYO_PREVIEW_AT_SEIKYUSOY_R");

					if($FieldValue[88]=="請求（サプライヤー）"){
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParamNone($str, "FORM_N");
						$str=DispParam($str, "FORM_S1");
						$str=DispParamNone($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_C_OK1");
						$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_K_OK");
						$str=DispParamNone($str, "FORM_DONE1");
						$str=DispParamNone($str, "FORM_DONE2");
					
					}else if($FieldValue[88]=="請求（研究者）"){
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParamNone($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParam($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_C_OK1");
						$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_K_OK");
						$str=DispParamNone($str, "FORM_DONE1");
						$str=DispParamNone($str, "FORM_DONE2");
						//if($kanryo_num>=1){
						//	$str=DispParam($str, "FORM_DONE2");
						//}else{
						//	$str=DispParamNone($str, "FORM_DONE2");
						//}

					}else{
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParamNone($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParamNone($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_C_OK1");
						$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
						$str=DispParamNone($str, "FORM_K_OK");
						$str=DispParamNone($str, "FORM_DONE1");
						$str=DispParamNone($str, "FORM_DONE2");
					}
					break;
				case 'サプライヤーキャンセル承認':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParam($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
				case '完了':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParam($str, "FORM_DONE2");
					break;
				default:
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_C_OK1");
					$str=DispParamNone($str, "FORM_S1-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					$str=DispParamNone($str, "FORM_K_OK");
					$str=DispParamNone($str, "FORM_DONE1");
					$str=DispParamNone($str, "FORM_DONE2");
					break;
			}
		}


		//一括支払い＆前払いの時だけ「〇」を出力
		//M2_PAY_TYPE,M_STATUS
		if($FieldValue[16]=="Once" && 
			($FieldValue[61]=="直接送付(前払い)" || $FieldValue[61]=="手数料追加(前払い)") ){
			$str=str_replace("[MITSUMORISYO_MAEBARAI]", "〇", $str);
		}else{
			$str=str_replace("[MITSUMORISYO_MAEBARAI]", "", $str);
		}


		//「Scientist3 control No.」が設定されていたら整形
		$SCNo_ary=array(
			"SCNo_yy" => "", 
			"SCNo_mm" => "", 
			"SCNo_dd" => "", 
			"SCNo_cnt" => "", 
			"SCNo_else1" => "", 
			"SCNo_else2" => "", 
		);
		$m2_quote_no="";

		$SCNo_ary["SCNo_yy"]=$FieldValue[72];
		$SCNo_ary["SCNo_mm"]=$FieldValue[73];
		$SCNo_ary["SCNo_dd"]=$FieldValue[74];
		$SCNo_ary["SCNo_cnt"]=$FieldValue[75];
		$SCNo_ary["SCNo_else1"]=$FieldValue[76];
		$SCNo_ary["SCNo_else2"]=$FieldValue[77];
		$SCNo_str=formatAlphabetId($SCNo_ary);
		$m2_quote_no=$FieldValue[71];
			//echo "<!--m2_quote_no:$m2_quote_no-->";
			//echo "<!--SCNo_str:$SCNo_str-->";
		$str=str_replace("[SCNO]",$SCNo_str,$str);


		//分割支払い：1枚にまとめたデータ
		//もしくは、一括払いの場合
		//「見積り書」の小計等

		//1活払い
		//M2_PAY_TYPE:$FieldValue[16]
		//M2_SPECIAL_DISCOUNT:$FieldValue[22]
		//MID1:$FieldValue[2]
		//MID2:$FieldValue[3]
		//M_STATUS:$FieldValue[61]
		//M2_IMPORT_FEE:$FieldValue[62]
		//M2_MANAGE_DISCOUNT:$FieldValue[63]
		//M2_TAX_RATE2:$FieldValue[64]
		//DIV_ID:$FieldValue[54]
		echo "<!--".$FieldName[16].": ".$FieldValue[16]."-->";
		echo "<!--".$FieldName[22].": ".$FieldValue[22]."-->";
		echo "<!--".$FieldName[2].": ".$FieldValue[2]."-->";
		echo "<!--".$FieldName[3].": ".$FieldValue[3]."-->";
		echo "<!--".$FieldName[62].": ".$FieldValue[62]."-->";
		echo "<!--".$FieldName[63].": ".$FieldValue[63]."-->";
		echo "<!--".$FieldName[64].": ".$FieldValue[64]."-->";
		echo "<!--".$FieldName[54].": ".$FieldValue[54]."-->";

		$tmp="";
		$tmp=explode("-", $FieldValue[54]);
		$part="";
		$pre_part="";
		if(count($tmp)==3){
			$part=$tmp[2];
			$pre_part=$tmp[0]."-".$tmp[1];
		}
		echo "<!--サービス費用エリア：part:$part-->";
		echo "<!--サービス費用エリア：pre_part:$pre_part-->";

		//先方の要望によりサービス費用エリアの表示条件変更
		if($FieldValue[34]=="見積り送付" || $FieldValue[34]=="運営手数料追加"){

		//if(($FieldValue[34]=="見積り送付" || $FieldValue[34]=="運営手数料追加") && 
		//	($FieldValue[16]=="Once" || 
		//	($FieldValue[16]=="Split" && $part=="Part0") || 
		//	($FieldValue[16]=="Milestone" && $part=="Part0") ) ){

			$str=DispParam($str, "FORM_M2_SUB");

			//pdf対応
			$str=DispParam($str, "PDF_D_ITEMS");

			
			//小計1
			//※先方の要望により、
			//スペシャルディスカウント(M2_SPECIAL_DISCOUNT)の仕様変更で、各アイテムごとにスペシャルディスカウントを設定するようにした。
			//=該当分割分のアイテムのM2_DETAIL_PRICEの合計+M2_DETAIL_HANDLING_FEEの合計
			//※注意：M2_DETAIL_PRICE=M2_DETAIL_QUANTITY*M2_DETAIL_UNIT_PRICE-M2_DETAIL_SP_DISCOUNTに変更になった。
			//元はM2_DETAIL_QUANTITY*M2_DETAIL_UNIT_PRICEだった
			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$FieldValue[0]."' order by NEWDATE";
			//echo "<!--SQL:".$StrSQL."-->";
			$rs_detail=mysqli_query(ConnDB(),$StrSQL);
			$syoke1=0;
			$sum_m2_detail_sp_discount=0;
			while($item_detail = mysqli_fetch_assoc($rs_detail)){
				if(is_numeric($item_detail["M2_DETAIL_HANDLING_FEE"])){
					$m2_detail_handling_fee=$item_detail["M2_DETAIL_HANDLING_FEE"];
				}else{
					$m2_detail_handling_fee=0;
				}

				if(is_numeric($item_detail["M2_DETAIL_PRICE"])){
					$m2_detail_price=$item_detail["M2_DETAIL_PRICE"];
				}else{
					$m2_detail_price=0;
				}

				if(is_numeric($item_detail["M2_DETAIL_SP_DISCOUNT"])){
					$m2_detail_sp_discount=$item_detail["M2_DETAIL_SP_DISCOUNT"];
				}else{
					$m2_detail_sp_discount=0;
				}

				$sum_m2_detail_sp_discount=$sum_m2_detail_sp_discount+$m2_detail_sp_discount;
				$syoke1=$syoke1+$m2_detail_price+$m2_detail_handling_fee;
				echo "<!--m2_detail_handling_fee:$m2_detail_handling_fee-->";
				echo "<!--m2_detail_sp_discount:$m2_detail_sp_discount-->";
				echo "<!--M2_DETAIL_PRICE:".$item_detail["M2_DETAIL_PRICE"]."-->";
			}
			
			echo "<!--M2_detail_SP_DISCOUNTの合計:$sum_m2_detail_sp_discount-->";
			echo "<!--syoke1:$syoke1-->";
			$str=str_replace("[SUM_M2_DETAIL_SP_DISCOUNT]", $sum_m2_detail_sp_discount, $str); //PDF対応
			$str=str_replace("[MITSUMORISYO_SUBTOTAL1]", $syoke1, $str);


//			//小計1
//			//=該当分割分のアイテムのM2_DETAIL_PRICEの合計-M2_SPECIAL_DISCOUNT
//			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$FieldValue[0]."' order by NEWDATE";
//			//echo "<!--SQL:".$StrSQL."-->";
//			$rs_detail=mysqli_query(ConnDB(),$StrSQL);
//			$syoke1=0;
//			while($item_detail = mysqli_fetch_assoc($rs_detail)){
//				if(is_numeric($item_detail["M2_DETAIL_HANDLING_FEE"])){
//					$m2_detail_handling_fee=$item_detail["M2_DETAIL_HANDLING_FEE"];
//				}else{
//					$m2_detail_handling_fee=0;
//				}
//
//				if(is_numeric($item_detail["M2_DETAIL_PRICE"])){
//					$m2_detail_price=$item_detail["M2_DETAIL_PRICE"];
//				}else{
//					$m2_detail_price=0;
//				}
//
//				$syoke1=$syoke1+$m2_detail_price+$m2_detail_handling_fee;
//				echo "<!--m2_detail_handling_fee:$m2_detail_handling_fee-->";
//				echo "<!--M2_DETAIL_PRICE:".$item_detail["M2_DETAIL_PRICE"]."-->";
//			}
//			if(is_numeric($FieldValue[22])){
//				$m2_special_discount=$FieldValue[22];
//			}else{
//				$m2_special_discount=0;
//			}
//			
//			$syoke1=$syoke1-$m2_special_discount;
//			echo "<!--M2_SPECIAL_DISCOUNT:$m2_special_discount-->";
//			echo "<!--syoke1:$syoke1-->";
//			$str=str_replace("[M2_SPECIAL_DISCOUNT]", $m2_special_discount, $str); //PDF対応
//			$str=str_replace("[MITSUMORISYO_SUBTOTAL1]", $syoke1, $str);


			//税率1
			//国内サプライヤー10%,海外サプライヤー0%。
			//編集もしたいということで、
			//DBに値がなかったら自動入力し、常に編集できる状態に仕様変更
			$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$FieldValue[2]."';";
			$rsM1=mysqli_query(ConnDB(),$StrSQL);
			$itemM1 = mysqli_fetch_assoc($rsM1);

			$tax_rate1=0;
			if($FieldValue[79]!="" && !is_null($FieldValue[79])){
				$tax_rate1=$FieldValue[79];

			}else{
				if($itemM1["M1_DVAL04"]=="M1_DVAL04:Japan"){
					$tax_rate1=10;
				}else{
					$tax_rate1=0;

				}
			}
			$str_tax_rate1='<input type="text" name="M2_TAX_RATE1" class="input_w10 form-control" 
				value="'.$tax_rate1.'" size="90">';
			//pdf対応
			if($mode=="disp_frame1"){
				$str_tax_rate1=$tax_rate1;
			}
			$str=str_replace("[MITSUMORISYO_TAX_RATE1]",$str_tax_rate1,$str);

			////税率1
			////国内サプライヤー10%,海外サプライヤー0%。
			//$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$FieldValue[2]."';";
			//$rsM1=mysqli_query(ConnDB(),$StrSQL);
			//$itemM1 = mysqli_fetch_assoc($rsM1);
			//$tax_rate1=0;
			//if($itemM1["M1_DVAL04"]=="M1_DVAL04:Japan"){
			//	$tax_rate1=10;
			//}else{
			//	$tax_rate1=0;
			//	
			//}
			//$str=str_replace("[MITSUMORISYO_TAX_RATE1]",$tax_rate1,$str);


			//消費税
			$tax_bill1=$tax_rate1*$syoke1/100;
			$str=str_replace("[MITSUMORISYO_TAX_BILL1]",$tax_bill1,$str);

			//PDF対応
			//PDF用の表示
			$pdf_total1=$syoke1+$tax_bill1;
			$str=str_replace("[PDF_TOTAL1]", $pdf_total1, $str);
			

			//PF手数料
			//研究者管理で入力したPF手数料率/100を使用
			//仕様変更。PF手数料率は見積り送付データに見積り送付時点で保存される。
			//[85]:PF_RATE
			if(is_numeric($FieldValue[85])){
				$pf_fee=$syoke1*$FieldValue[85]/100;
			}else{
				$pf_fee=0;
			}
			$str=str_replace("[MITSUMORISYO_PF_FEE]",$pf_fee,$str);

			echo "<!--PF手数料率:M2_ETC02:".$FieldValue[85]."-->";


//			//PF手数料
//			//研究者管理で入力したPF手数料率/100を使用
//			$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$FieldValue[3]."';";
//			$rsM2=mysqli_query(ConnDB(),$StrSQL);
//			$itemM2 = mysqli_fetch_assoc($rsM2);
//			if(is_numeric($itemM2["M2_ETC02"])){
//				$pf_fee=$syoke1*$itemM2["M2_ETC02"]/100;
//			}else{
//				$pf_fee=0;
//			}
//			$str=str_replace("[MITSUMORISYO_PF_FEE]",$pf_fee,$str);
//
//			echo "<!--M2_ETC02:".$itemM2["M2_ETC02"]."-->";



			//輸入代行費用
			//初期値はない。管理画面から手動で入力。
			//「手数料追加（前払い）＆2回払い＆(2回払いの2回目の支払い書 or フロント用)」or
			//「マイルストーンの場合」or
			//「1活払いの場合」
			//の場合、入力可能なインプットにする
			//M_STATUS,M2_PAY_TYPE

			//仕様変更１
			//一括の場合はインプットにする。
			//2回払い、マイルストーン払いの場合は、ラストの見積り送付（PartN）データのみインプットにし、
			//Part0はPartNの値を表示のみ、その他（Part1~PartN-1）は何も表示しない。

			//仕様変更2
			//マイルストーンの場合は、納品物がDataかGoodsかにかかわらず、輸出代行費用も輸入代行費用もInput形式。
			//Part0はPart1~PartNの合算値を表示のみ。

			if( $FieldValue[16]=="Once"){

				if(is_numeric($FieldValue[62])){
					$import_fee=$FieldValue[62];
				}else{
					$import_fee=0;
				}
				$str_import_fee='<input type="text" name="M2_IMPORT_FEE" class="input_w10 form-control" 
				value="'.$import_fee.'" size="90">';

			}else if($FieldValue[16]=="Split"){
				//分割されてナンバリングされた見積り送付のデータの最後のデータ（PartLAST）をとってくる
				$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_IMPORT_FEE FROM DAT_FILESTATUS WHERE ";
				$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
				$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
				$StrSQL.=" AND STATUS='".$FieldValue[34]."' ";
				$StrSQL.=" ORDER BY CAST(SUBSTRING_INDEX(DIV_ID, 'Part', -1) AS UNSIGNED) DESC;";
				$partLAST_rs=mysqli_query(ConnDB(),$StrSQL);
				$partLAST_item = mysqli_fetch_assoc($partLAST_rs);
				echo "<!--サービス費用エリア(輸入)：partLAST:";
				echo "$StrSQL\n";
				var_dump($partLAST_item);
				echo "-->";
				echo "<!--54:".$FieldValue[54].", prepart合体：".$pre_part."-Part0"."-->";
				if($FieldValue[54]==$pre_part."-Part0"){
					//Part0だったら
					if(is_numeric($partLAST_item["M2_IMPORT_FEE"])){
						$import_fee=$partLAST_item["M2_IMPORT_FEE"];
					}else{
						$import_fee=0;
					}
					$str_import_fee=$import_fee;
					$str=DispParam($str, "HIDDEN_M2_IMPORT_FEE");

				}else if($FieldValue[54]==$partLAST_item["DIV_ID"]){
					//PartNだったら
					if(is_numeric($FieldValue[62])){
						$import_fee=$FieldValue[62];
					}else{
						$import_fee=0;
					}
					$str_import_fee='<input type="text" name="M2_IMPORT_FEE" class="input_w10 form-control" 
					value="'.$import_fee.'" size="90">';

				}else{
					$import_fee=0;
					$str_import_fee=$import_fee;
					$str=DispParam($str, "HIDDEN_M2_IMPORT_FEE");

				}

			}else if($FieldValue[16]=="Milestone"){
				echo "<!--54:".$FieldValue[54].", prepart合体：".$pre_part."-Part0"."-->";
				if($FieldValue[54]==$pre_part."-Part0"){
					//Part0だったら
					//分割されてナンバリングされた見積り送付のデータ（PartN）をとってくる
					$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_IMPORT_FEE FROM DAT_FILESTATUS WHERE ";
					$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
					$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
					$StrSQL.=" AND STATUS='".$FieldValue[34]."' ";
					$partN_rs=mysqli_query(ConnDB(),$StrSQL);
					
					echo "<!--サービス費用エリア(輸入)：partN:";
					echo "$StrSQL\n";
					var_dump($partN_item);
					echo "-->";
					$import_fee=0;
					while( $partN_item = mysqli_fetch_assoc($partN_rs) ){
						if($partN_item["DIV_ID"]==$pre_part."-Part0"){
							continue;
						}

						if(is_numeric($partN_item["M2_IMPORT_FEE"])){
							$import_fee+=$partN_item["M2_IMPORT_FEE"];
						}

					}
					$str_import_fee=$import_fee;
					$str=DispParam($str, "HIDDEN_M2_IMPORT_FEE");


				}else{
					//Part0以外
					if(is_numeric($FieldValue[62])){
						$import_fee=$FieldValue[62];
					}else{
						$import_fee=0;
					}
					$str_import_fee='<input type="text" name="M2_IMPORT_FEE" class="input_w10 form-control" 
					value="'.$import_fee.'" size="90">';

				}

			}else{
				$import_fee=0;
				$str_import_fee=$import_fee;
					$str=DispParam($str, "HIDDEN_M2_IMPORT_FEE");


			}
			//pdf対応
			if($mode=="disp_frame1"){
				$str_import_fee=$import_fee;
			}

			//ここまで[HIDDEN_M2_IMPORT_FEE]タグがのこってたら非表示
			$str=DispParamNone($str, "HIDDEN_M2_IMPORT_FEE");

			$str=str_replace("[INPUT-M2_IMPORT_FEE]",$str_import_fee,$str);
			$str=str_replace("[HIDDEN_M2_IMPORT_FEE]", $import_fee,$str);




//			//輸入代行費用
//			//「手数料追加（前払い）＆2回払い＆(2回払いの2回目の支払い書 or フロント用)」or
//			//「マイルストーンの場合」or
//			//「1活払いの場合」
//			//の場合、入力可能なインプットにする
//			//M_STATUS,M2_PAY_TYPE
//
//			//仕様変更１
//			//一括の場合はインプットにする。
//			//2回払い、マイルストーン払いの場合は、ラストの見積り送付（PartN）データのみインプットにし、
//			//Part0はPartNの値を表示のみ、その他（Part1~PartN-1）は何も表示しない。
//			
//			if( $FieldValue[16]=="Once"){
//
//				if(is_numeric($FieldValue[62])){
//					$import_fee=$FieldValue[62];
//				}else{
//					$import_fee=0;
//				}
//				$str_import_fee='<input type="text" name="M2_IMPORT_FEE" class="input_w10 form-control" 
//				value="'.$import_fee.'" size="90">';
//				$str=DispParamNone($str,"HIDDEN_M2_IMPORT_FEE");
//
//			}else if($FieldValue[16]=="Split" || $FieldValue[16]=="Milestone" ){
//				//分割されてナンバリングされた見積り送付のデータの最後のデータ（PrtN）をとってくる
//				$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_IMPORT_FEE FROM DAT_FILESTATUS WHERE ";
//				$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
//				$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
//				$StrSQL.=" AND STATUS='".$FieldValue[34]."' ";
//				$StrSQL.=" ORDER BY CAST(SUBSTRING_INDEX(DIV_ID, 'Part', -1) AS UNSIGNED) DESC;";
//				$partN_rs=mysqli_query(ConnDB(),$StrSQL);
//				$partN_item = mysqli_fetch_assoc($partN_rs);
//				echo "<!--サービス費用エリア(輸入)：partN:";
//				echo "$StrSQL\n";
//				var_dump($partN_item);
//				echo "-->";
//				echo "<!--54:".$FieldValue[54].", prepart合体：".$pre_part."-Part0"."-->";
//				if($FieldValue[54]==$pre_part."-Part0"){
//					//Part0だったら
//					if(is_numeric($partN_item["M2_IMPORT_FEE"])){
//						$import_fee=$partN_item["M2_IMPORT_FEE"];
//					}else{
//						$import_fee=0;
//					}
//					$str_import_fee=$import_fee;
//					$str=DispParam($str,"HIDDEN_M2_IMPORT_FEE");
//
//				}else if($FieldValue[54]==$partN_item["DIV_ID"]){
//					//PartNだったら
//					if(is_numeric($FieldValue[62])){
//						$import_fee=$FieldValue[62];
//					}else{
//						$import_fee=0;
//					}
//					$str_import_fee='<input type="text" name="M2_IMPORT_FEE" class="input_w10 form-control" 
//					value="'.$import_fee.'" size="90">';
//					$str=DispParamNone($str,"HIDDEN_M2_IMPORT_FEE");
//
//				}else{
//					$import_fee=0;
//					$str_import_fee=$import_fee;
//					$str=DispParam($str,"HIDDEN_M2_IMPORT_FEE");
//				}
//
//			}
//			else{
//				$import_fee=0;
//				$str_import_fee=$import_fee;
//				$str=DispParam($str,"HIDDEN_M2_IMPORT_FEE");
//
//			}
//			//pdf対応
//			if($mode=="disp_frame1"){
//				$str_import_fee=$import_fee;
//			}
//			$str=str_replace("[INPUT-M2_IMPORT_FEE]",$str_import_fee,$str);





			//輸出代行費用
			///管理画面/a_agency_setting/の各通貨にたいするその時点の手数料の値が、
			//「見積り送付」時に、DAT_FILESTATUSの、カラムM2_EXPORT_FEE_TABLEに、json形式で保存される。
			//カラムM2_CURRENCYに設定されている取引に使用される通貨に対応する値を、
			//カラムM2_EXPORT_FEE_TABLEからさがし、「輸出代行費用」として設定する。
			//関連カラム：M2_EXPORT_FEE_TABLE,M2_CURRENCY,M2_PAY_TYPE,SHODAN_ID,M1_TRANS_FLG,NEWDATE
			//例：M2_EXPORT_FEE_TABLE:{"USD":"200","EUR":"300","GBP":"400","JPY":"100"}
			//M2_CURRENCY=="M2_CURRENCY:USD"の場合、export_fee="200"

			//仕様変更：
			//※分割支払いの場合はPart1の見積り送付データで輸出代行費用を計上する。
			//※輸出代行費用が発生するのは、研究者が見積り依頼時に「輸出代行　あり」を選択した場合のみ。
			//Part1~PartNで、Part1は値が表示され、Part1以外はこの値を0にする。
			//Part0は、Part1の値。Part1の値を参照してるだけなので表示のみ。

			//仕様変更２：
			//先方の要望でM2_CURRENCYを変更できないようにした。
			//輸出代行費用用に新規カラム作成。M2_EXPORT_FEE
			//inputで値を上書きできるようにする。

			//仕様変更3:
			//マイルストーンの場合は、納品物がDataかGoodsかにかかわらず、輸出代行費用も輸入代行費用もInput形式。
			//Part0はPart1~PartNの合算値を表示のみ。

			$export_fee=0;
			$str_export_fee="";
			if( ($FieldValue[16]=="Split" && $part=="Part0") || 
				($FieldValue[16]=="Milestone" && $part=="Part0") ){
				//partNのデータとってくる
				$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_EXPORT_FEE FROM DAT_FILESTATUS WHERE ";
				$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
				$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
				$StrSQL.=" AND STATUS='".$FieldValue[34]."' ";
				$partN_rs=mysqli_query(ConnDB(),$StrSQL);

				$export_fee=0;
				while($partN_item = mysqli_fetch_assoc($partN_rs)){
					echo "<!--サービス費用エリア：part0:";
					var_dump($partN_item);
					echo "-->";
					if($partN_item["DIV_ID"]==$pre_part."-Part0"){
						continue;
					}
					$export_fee += is_numeric($partN_item["M2_EXPORT_FEE"]) ? $partN_item["M2_EXPORT_FEE"] : 0;
				};
				$str_export_fee=$export_fee;
				echo "<!--export_fee1:$export_fee-->";

				$str=DispParam($str, "HIDDEN_M2_EXPORT_FEE");
				$str=DispParam($str, "Part0_EXP");
				$str=DispParamNone($str, "PartN_EXP");
				//$str=DispParamNone($str, "ELSE_EXP");

			}else if( ($FieldValue[16]=="Split" && $part=="Part1") || 
				$FieldValue[16]=="Milestone" || 
				$FieldValue[16]=="Once"){
				//自分のデータを使う
				$export_fee = is_numeric($FieldValue[81]) ? $FieldValue[81] : 0;
				$str_export_fee='<input type="text" name="M2_EXPORT_FEE" class="input_w10 form-control" 
				value="'.$export_fee.'" size="90">';
				echo "<!--export_fee2:$export_fee-->";

				$str=DispParamNone($str, "Part0_EXP");
				$str=DispParam($str, "PartN_EXP");
				//$str=DispParamNone($str, "ELSE_EXP");

			}else{
				$export_fee=0;
				$str_export_fee=$export_fee;

				$str=DispParam($str, "HIDDEN_M2_EXPORT_FEE");
				$str=DispParamNone($str, "Part0_EXP");
				$str=DispParamNone($str, "PartN_EXP");
				//$str=DispParam($str, "ELSE_EXP");
			}

			//輸出代行費用が発生するのは、研究者が見積り依頼時に「輸出代行　あり」を選択した場合のみ。
			//見積り送付や運営手数料追加のNEWDATEの値より前の日付けに送信した、
			//「見積り依頼」データの「M1_TRANS_FLG」が「なし」の場合は強制的に「0」
			$StrSQL="SELECT ID,NEWDATE,STATUS,M1_TRANS_FLG FROM DAT_FILESTATUS WHERE ";
			$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
			$StrSQL.=" AND STATUS='見積り依頼' ";
			$StrSQL.=" AND NEWDATE<'".$FieldValue[32]."' ";
			$StrSQL.=" ORDER BY NEWDATE DESC ";
			$irai_rs=mysqli_query(ConnDB(),$StrSQL);
			$irai_item = mysqli_fetch_assoc($irai_rs);
			echo "<!--サービス合計エリア irai_item:";
			var_dump($irai_item);
			echo "-->";
			if($irai_item["M1_TRANS_FLG"]=="なし"){
				$str=DispParam($str, "M1_TRANS_FLG_EXP");
				//データはそもそもこの場合保存されてないが念のためここでも0に設定する。
				$export_fee=0;
				//inputフォームなしで表示のみ
				$str_export_fee=$export_fee;

				$str=DispParam($str, "HIDDEN_M2_EXPORT_FEE");

			}else{
				$str=DispParamNone($str, "M1_TRANS_FLG_EXP");
			}

			//ここまで、[HIDDEN_M2_EXPORT_FEE]タグがのこってたら、非表示
			$str=DispParamNone($str, "HIDDEN_M2_EXPORT_FEE");

			//pdf対応
			if($mode=="disp_frame1"){
				$str_export_fee=$export_fee;
			}

			$str=str_replace("[INPUT-MITSUMORISYO_EXPORT_FEE]",$str_export_fee,$str);
			$str=str_replace("[HIDDEN_M2_EXPORT_FEE]", $export_fee,$str);




//			$export_fee=0;
//			$str_export_fee="";
//			if( ($FieldValue[16]=="Split" && $part=="Part0") || 
//				($FieldValue[16]=="Milestone" && $part=="Part0") ){
//				//part1のデータとってくる
//				$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_EXPORT_FEE FROM DAT_FILESTATUS WHERE ";
//				$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
//				$StrSQL.=" AND DIV_ID='".$pre_part."-Part1' ";
//				$StrSQL.=" AND STATUS='".$FieldValue[34]."' ";
//				$part1_rs=mysqli_query(ConnDB(),$StrSQL);
//				$part1_item = mysqli_fetch_assoc($part1_rs);
//				echo "<!--サービス費用エリア：part0:";
//				var_dump($part1_item);
//				echo "-->";
//
//				$export_fee = is_numeric($part1_item["M2_EXPORT_FEE"]) ? $part1_item["M2_EXPORT_FEE"] : 0;
//				$str_export_fee=$export_fee;
//				echo "<!--export_fee1:$export_fee-->";
//
//				$str=DispParam($str, "Part0_EXP");
//				$str=DispParamNone($str, "Part1_EXP");
//				//$str=DispParamNone($str, "ELSE_EXP");
//				$str=str_replace("[PART1_ID]",$part1_item["ID"],$str);
//
//
//			}else if( ($FieldValue[16]=="Split" && $part=="Part1") || 
//				($FieldValue[16]=="Milestone" && $part=="Part1") || 
//				$FieldValue[16]=="Once"){
//				//自分のデータを使う
//				$export_fee = is_numeric($FieldValue[81]) ? $FieldValue[81] : 0;
//				$str_export_fee='<input type="text" name="M2_EXPORT_FEE" class="input_w10 form-control" 
//				value="'.$export_fee.'" size="90">';
//				echo "<!--export_fee2:$export_fee-->";
//
//				$str=DispParamNone($str, "Part0_EXP");
//				$str=DispParam($str, "Part1_EXP");
//				//$str=DispParamNone($str, "ELSE_EXP");
//
//			}else{
//				$export_fee=0;
//				$str_export_fee=$export_fee;
//
//				$str=DispParamNone($str, "Part0_EXP");
//				$str=DispParamNone($str, "Part1_EXP");
//				//$str=DispParam($str, "ELSE_EXP");
//			}
//
//			//輸出代行費用が発生するのは、研究者が見積り依頼時に「輸出代行　あり」を選択した場合のみ。
//			//見積り送付や運営手数料追加のNEWDATEの値より前の日付けに送信した、
//			//「見積り依頼」データの「M1_TRANS_FLG」が「なし」の場合は強制的に「0」
//			$StrSQL="SELECT ID,NEWDATE,STATUS,M1_TRANS_FLG FROM DAT_FILESTATUS WHERE ";
//			$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
//			$StrSQL.=" AND STATUS='見積り依頼' ";
//			$StrSQL.=" AND NEWDATE<'".$FieldValue[32]."' ";
//			$StrSQL.=" ORDER BY NEWDATE DESC ";
//			$irai_rs=mysqli_query(ConnDB(),$StrSQL);
//			$irai_item = mysqli_fetch_assoc($irai_rs);
//			echo "<!--サービス合計エリア irai_item:";
//			var_dump($irai_item);
//			echo "-->";
//			if($irai_item["M1_TRANS_FLG"]=="なし"){
//				$str=DispParam($str, "M1_TRANS_FLG_EXP");
//				//データはそもそもこの場合保存されてないが念のためここでも0に設定する。
//				$export_fee=0;
//				//inputフォームなしで表示のみ
//				$str_export_fee=$export_fee;
//			}else{
//				$str=DispParamNone($str, "M1_TRANS_FLG_EXP");
//			}
//
//			$str=str_replace("[INPUT-MITSUMORISYO_EXPORT_FEE]",$str_export_fee,$str);
//
			



			
			//特別値引き（運営）
			if($FieldValue[61]=="直接送付" ||
				$FieldValue[61]=="直接送付(前払い)"){
				$mng_discount=0;
				$str_mng_discount=$mng_discount;
				$str=DispParam($str,"HIDDEN_M2_MANAGE_DISCOUNT");
				//$str_mng_discount=$mng_discount.'<input type="hidden" name="M2_MANAGE_DISCOUNT" value="'.$mng_discount.'">';

			}else if( ($FieldValue[16]=="Split" && $part=="Part0") || 
				($FieldValue[16]=="Milestone" && $part=="Part0") ){
				//PartNのデータをとってくる
				$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_MANAGE_DISCOUNT FROM DAT_FILESTATUS WHERE ";
				$StrSQL.=" SHODAN_ID='".$FieldValue[1]."' ";
				$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
				$StrSQL.=" AND STATUS='".$FieldValue[34]."' ";
				$partN_rs=mysqli_query(ConnDB(),$StrSQL);

				$mng_discount=0;
				while($partN_item = mysqli_fetch_assoc($partN_rs)){
					echo "<!--サービス費用エリア：part0:";
					var_dump($partN_item);
					echo "-->";
					if($partN_item["DIV_ID"]==$pre_part."-Part0"){
						continue;
					}
					$mng_discount += is_numeric($partN_item["M2_MANAGE_DISCOUNT"]) ? $partN_item["M2_MANAGE_DISCOUNT"] : 0;
				};
				$str_mng_discount=$mng_discount;
				$str=DispParam($str,"HIDDEN_M2_MANAGE_DISCOUNT");

			}else if( $FieldValue[16]=="Split" || 
				$FieldValue[16]=="Milestone" || 
				$FieldValue[16]=="Once" ){
				
				$mng_discount=$FieldValue[63];
				$str_mng_discount='<input type="text" name="M2_MANAGE_DISCOUNT" class="input_w10 form-control" 
				value="'.$mng_discount.'" size="90">';

			}
			//pdf対応
			if($mode=="disp_frame1"){
				$str_mng_discount=$mng_discount;
			}
			//ここまで、[HIDDEN_M2_MANAGE_DISCOUNT]タグが残ってたら非表示
			$str=DispParamNone($str,"HIDDEN_M2_MANAGE_DISCOUNT");

			$str=str_replace("[INPUT-M2_MANAGE_DISCOUNT]",$str_mng_discount,$str);
			$str=str_replace("[HIDDEN_M2_MANAGE_DISCOUNT]",$mng_discount,$str);




			//小計2
			$syoke2=$pf_fee+$import_fee+$export_fee-$mng_discount;
			echo "<!--syoke2:$syoke2=$pf_fee+$import_fee+$export_fee-$mng_discount-->";
			$str=str_replace("[MITSUMORISYO_SUBTOTAL2]",$syoke2,$str);


			//税率2
			$tax_rate2=$FieldValue[64];
			

			//消費税率2
			$tax_bill2=$syoke2*$tax_rate2/100;
			echo "<!--tax_bill2:$tax_bill2=$syoke2*$tax_rate2/100;-->";
			$str=str_replace("[MITSUMORISYO_TAX_BILL2]",$tax_bill2,$str);


			//合計金額
			//M2_CURRENCY
			$all_charge=$syoke1+$tax_bill1+$syoke2+$tax_bill2;
			echo "<!--all_charge:$all_charge=$syoke1+$tax_bill1+$syoke2+$tax_bill2-->";
			if($FieldValue[21]=="M2_CURRENCY:JPY"){
				$rounded_all_charge=round($all_charge);
			}else{
				$rounded_all_charge=round($all_charge,1);
			}
			$str=str_replace("[MITSUMORISYO_ALL_CHARGE]",$all_charge,$str);
			$str=str_replace("[R_MITSUMORISYO_ALL_CHARGE]",$rounded_all_charge,$str);


		}else{
			$str=DispParamNone($str, "FORM_M2_SUB");

			//hidden
			$str=DispParam($str,"HIDDEN_M2_IMPORT_FEE");
			$str=DispParamNone($str, "HIDDEN_M2_EXPORT_FEE");
			$str=DispParam($str,"HIDDEN_M2_MANAGE_DISCOUNT");

			//pdf対応
			$str=DispParamNone($str, "PDF_D_ITEMS");
		}


		//見積り書の「SHIP TO」「BILL TO」
		//$FieldValue[55]:M2_SHIP_TO_SPT_1
		//$FieldValue[56]:M2_SHIP_TO_SPT_2
		//$FieldValue[57]:M2_SHIP_TO_SPT_3
		//$FieldValue[58]:M2_SHIP_TO_SPT_4
		//$FieldValue[59]:M2_SHIP_TO_SPT_5
		//$FieldValue[60]:M2_SHIP_TO_SPT_6
		//$FieldValue[65]:M2_BILL_TO_SPT_1
		//$FieldValue[66]:M2_BILL_TO_SPT_2
		//$FieldValue[67]:M2_BILL_TO_SPT_3
		//$FieldValue[68]:M2_BILL_TO_SPT_4
		//$FieldValue[69]:M2_BILL_TO_SPT_5
		//$FieldValue[70]:M2_BILL_TO_SPT_6
		if($FieldValue[34]=="見積り送付" || $FieldValue[34]=="運営手数料追加"){
			$ship_to=$FieldValue[55].", ".$FieldValue[56].", ".$FieldValue[57];
			$ship_to.=" ".$FieldValue[58].", ".$FieldValue[59].", ".$FieldValue[60];
			
			$bill_to=$FieldValue[65].", ".$FieldValue[66].", ".$FieldValue[67];
			$bill_to.=" ".$FieldValue[68].", ".$FieldValue[69].", ".$FieldValue[70];

			$str=str_replace("[VIEW-SHIP_TO]",$ship_to,$str);
			$str=str_replace("[VIEW-BILL_TO]",$bill_to,$str);
		}


		//a_filestatus_detailの情報表示エリア
		$tpl_fd=file_get_contents("f_detail.html");

		//pdf対応
		$tpl_pdf_items="";
		if($preview_type=="cb"){
			$tpl_pdf_items=file_get_contents("pdf_items_cb.html");
		}else if($preview_type=="r"){
			$tpl_pdf_items=file_get_contents("pdf_items_r.html");
		}else if($preview_type=="h"){
			$tpl_pdf_items=file_get_contents("pdf_items_h.html");
		}

		$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$key."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		//echo "<!--StrSQL:".$StrSQL."-->";
		//echo "<!--key:".$key."-->";
		$output1="";
		$each_output1="";
		$output2="";
		$each_output2="";
		while ($item = mysqli_fetch_assoc($rs)) {
			$each_output1=$tpl_fd;
			$each_output2=$tpl_pdf_items;

			$each_output2=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:","",$FieldValue[21]),$each_output2);

			foreach ($item as $fkey => $val) {
				if($fkey=="M_STATUS"){
					$each_output1=str_replace("[".$fkey."]", str_replace("M_STATUS:","",$val), $each_output1);
					$each_output2=str_replace("[".$fkey."]", str_replace("M_STATUS:","",$val), $each_output2);
				}
				$each_output1=str_replace("[".$fkey."]", $val, $each_output1);
				$each_output2=str_replace("[".$fkey."]", $val, $each_output2);

				//2回払いとマイルストーン払いで、前払いの対象になったitemにだけ「〇」と表示
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$item["FILESTATUS_ID"]."'";
				$rs_fs=mysqli_query(ConnDB(),$StrSQL);
				$fs_item=mysqli_fetch_assoc($rs_fs);
				//echo "<!--前払い〇用：親FS:ID:".$fs_item["ID"]."-->";
				//echo "<!--前払い〇用：親FS::M2_PAY_TYPE:".$fs_item["M2_PAY_TYPE"]."-->";

				if( ($fs_item["M2_PAY_TYPE"]=="Split" || $fs_item["M2_PAY_TYPE"]=="Milestone") && 
					($fs_item["M_STATUS"]=="直接送付(前払い)" || $fs_item["M_STATUS"]=="手数料追加(前払い)") ){
					if($item["M2_DETAIL_SPLIT_PART"]=="Part1"){
						$each_output1=str_replace("[DETAIL_MAEBARAI]", "〇", $each_output1);
						$each_output2=str_replace("[DETAIL_MAEBARAI]", "〇", $each_output2);
					}
				}
				$each_output1=str_replace("[DETAIL_MAEBARAI]", "", $each_output1);
				$each_output2=str_replace("[DETAIL_MAEBARAI]", "", $each_output2);
			}
			$output1.=$each_output1;
			$output2.=$each_output2;
		}
		if(trim($output1)==""){
			$str=str_replace("[AREA-FDETAIL]","データがありません",$str);
		}
		if(trim($output2)==""){
			$str=str_replace("[AREA-FDETAIL-ITEMS]","データがありません",$str);
		}
		$str=str_replace("[AREA-FDETAIL]",$output1,$str);
		$str=str_replace("[AREA-FDETAIL-ITEMS]",$output2,$str);


		//関連するDAT_FILESTATUSのレコード表示
		//アコーディオン
		$tpl_fd=file_get_contents("f_related.html");

		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$FieldValue[1]."' order by NEWDATE";
		//echo "<!--SQL:".$StrSQL."-->";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		//echo "<!--StrSQL:".$StrSQL."-->";
		//echo "<!--key:".$key."-->";
		$output="";
		$each_output="";
		while ($item = mysqli_fetch_assoc($rs)) {
			//if($item["ID"]==$key){
			//	//echo "<!--key:".$key.",ID:".$item["ID"]."-->";
			//	continue;
			//}
			if($item["MID1"]!=$FieldValue[2] || $item["MID2"]!=$FieldValue[3]){
				continue;
			}
			echo "<!--ID:".$item["ID"]."-->";
			$each_output=$tpl_fd;
			if($item["ID"]==$key){
				$each_output=DispParam($each_output, "MYSELF");
			}else{
				$each_output=DispParamNone($each_output, "MYSELF");
			}


			//「Scientist3 control No.」が設定されていたら整形
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$m2_quote_no="";

			$SCNo_ary["SCNo_yy"]=$item["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$item["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$item["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$item["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$item["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$item["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);
			$m2_quote_no=$item["M2_QUOTE_NO"];
			//echo "<!--m2_quote_no:$m2_quote_no-->";
			//echo "<!--SCNo_str:$SCNo_str-->";
			$each_output=str_replace("[SCNO_RELATED]",$SCNo_str,$each_output);

			
			////マイルストーン払いの場合に、Item名も表示。
			//$item_name="";
			//if($item["M2_PAY_TYPE"]=='Milestone'){
			//	$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL where FILESTATUS_ID='".$item["ID"]."' order by ID desc;";
			//	//echo('<!--'.$StrSQL.'-->');
			//	$rs_dmile=mysqli_query(ConnDB(),$StrSQL);
			//	$item_dmile = mysqli_fetch_assoc($rs_dmile);
			//	$item_name=$item_dmile["M2_DETAIL_ITEM"];
			//}


			//「全額一括前払い」の項目
			//以下の条件のときに「〇」を出力
			//M2_PAY_TYPE=Once
			//M_STATUS=直接送付(前払い),手数料追加(前払い)
			if($item["M2_PAY_TYPE"]=="Once" &&
				($item["M_STATUS"]=="直接送付(前払い)" || $item["M_STATUS"]=="手数料追加(前払い)")){
				
				$each_output=str_replace("[ADVANCED_FULL_PAYMENT]","〇",$each_output);
			}else{
				$each_output=str_replace("[ADVANCED_FULL_PAYMENT]","",$each_output);
			}


			//DAT_FILESTATUS_DETAILの各itemのデータをアコーディオン内に表示
			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$item["ID"]."' order by NEWDATE";
			//echo "<!--SQL:".$StrSQL."-->";
			$rs_detail=mysqli_query(ConnDB(),$StrSQL);
			$tpl_item=file_get_contents("f_item.html");
			$item_list="";
			$i=0;
			while ($item_detail = mysqli_fetch_assoc($rs_detail)) {
				$i++;
				$tpl_tmp=$tpl_item;
				$tpl_tmp=str_replace("[ITM_NO]", $i, $tpl_tmp);

				//「前払い」の項目
				//以下の条件のときに「〇」を出力
				//M2_PAY_TYPE=Milestone
				//Item毎の、分割割り当て（M2_DETAIL_SPLIT_PART）=Part1
				if($item["M2_PAY_TYPE"]=="Milestone" && $item_detail["M2_DETAIL_SPLIT_PART"]=="Part1" ){
					$tpl_tmp=str_replace("[ADVANCED_PAYMENT_ITEM]", "〇", $tpl_tmp);
				}else{
					$tpl_tmp=str_replace("[ADVANCED_PAYMENT_ITEM]", "", $tpl_tmp);
				}

				foreach ($item_detail as $fkey => $val) {
					//echo "<!--key:".$fkey.",val:".$val."-->";
					$tpl_tmp=str_replace("[FS_DETAIL-".$fkey."]", $val, $tpl_tmp);
					$tpl_tmp=str_replace("[D-FS_DETAIL-".$fkey."]", str_replace($fkey.":","", $val), $tpl_tmp);
				}
				$item_list.=$tpl_tmp;
			}
			$each_output=str_replace("[MITSUMORI_ITEMS]", $item_list, $each_output);


			//サービス費用エリアの表示
			if($item["STATUS"]=="見積り送付" || $item["STATUS"]=="運営手数料追加"){
				//echo "<!--";
				//var_dump($item);
				//echo "-->";
				$each_output=makeServiceArea($item["ID"],$each_output);
			}

			foreach ($item as $fkey => $val) {
				//echo "<!--key:".$fkey.",val:".$val."-->";

				$each_output=str_replace("[".$fkey."]", $val, $each_output);
				$each_output=str_replace("[D-".$fkey."]", str_replace($fkey.":","", $val), $each_output);
				if($val==""){
					$each_output=DispParamNone($each_output,$fkey);
				}else{
					$each_output=DispParam($each_output,$fkey);
				}

				if($fkey=="STATUS"){
					switch($val) {
						case '問い合わせ':
						$each_output=DispParam($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case '見積り依頼':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParam($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case '再見積り依頼':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParam($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case '見積り送付':
						case '運営手数料追加':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParam($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case '発注依頼':
						case '発注承認':
						case '受注承認':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParam($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case 'データ納品':
						case '物品納品':
						case '納品確認':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParam($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case '請求':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParam($each_output, "FORM_S1");
						$each_output=DispParam($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case 'サプライヤーキャンセル承認':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParam($each_output, "FORM_C_OK1");
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						default:
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParamNone($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
						$each_output=DispParamNone($each_output, "FORM_C_OK1");
						$each_output=DispParam($each_output, "FORM_DEFAULT");
						break;
					}
				}

			}

			$output.=$each_output;
		}
		if(trim($output)==""){
			$str=str_replace("[AREA-RELATED]","データがありません",$str);
		}
		$str=str_replace("[AREA-RELATED]",$output,$str);
		$str=str_replace("[BASE_URL]",BASE_URL,$str);


		//pdf対応
		//メールの基本情報
		$StrSQL="SELECT ID,MID,EMAIL FROM DAT_M2 WHERE MID='".$FieldValue[3]."'";
			//echo "<--m2_mail:SQL:$StrSQL-->";
		$m2_mail_rs=mysqli_query(ConnDB(),$StrSQL);
		$m2_mail_item = mysqli_fetch_assoc($m2_mail_rs);
		$to=$m2_mail_item["EMAIL"];
		$str=str_replace("[SEND_PDF_TO1]",$to,$str);

		//pdf対応
		if($mode=="disp_frame1" && $pdf_action=="download"){
			$str=DispParamNone($str,"PDF_BTN_AREA");
			downloadPDF($str);
		
		}else if($mode=="disp_frame1" && $pdf_action=="send"){
			$str=DispParam($str,"PDF_BTN_AREA");
			//sendPDF($str,$to);
			SubmitMitsumori($key);
			SendMail_v1($key);
			SendMail_v1_2($key);

		}else if($mode=="disp_frame1" && $pdf_action=="send2"){
			$str=DispParam($str,"PDF_BTN_AREA");
			SubmitJyutyusyonin($key);
		
		}else if($mode=="disp_frame1" && $pdf_action=="send3"){
			$str=DispParam($str,"PDF_BTN_AREA");
			SubmitSeikyu($key);

		}else if($mode=="disp_frame1" && $pdf_action=="send4"){
			$str=DispParam($str,"PDF_BTN_AREA");
			SubmitSeikyu_add($key);
		}else{
			$str=DispParam($str,"PDF_BTN_AREA");
		}

		

		//pdf対応
		//ボタンエリアに何を表示するか
		if($pdf_btn_version=="1"){
			$str=DispParamNone($str,"BTN_SEND");

		}else if($pdf_btn_version=="2"){

			if($FieldValue[34]=="見積り送付" || $FieldValue[34]=="運営手数料追加"){
				//運営手数料追加モードを経由した場合、すでに「見積り送付」済みかどうか
				//つまり同じ「見積り送付」のデータが自分いがいにあるかどうか
				$redundancy_num=0;
				$StrSQL="SELECT ID,STATUS,M2_ID,M2_VERSION,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID='".$FieldValue[1]."' AND";
				$StrSQL.=" M2_ID='".$FieldValue[13]."' AND ";
				$StrSQL.=" M2_VERSION='".$FieldValue[14]."' AND ";
				$StrSQL.=" DIV_ID='".$FieldValue[54]."' AND ";
				$StrSQL.=" ID!='".$FieldValue[0]."' AND";
				$StrSQL.=" (STATUS='見積り送付' OR STATUS='運営手数料追加') ";
				$redundancy_rs=mysqli_query(ConnDB(),$StrSQL);
				$redundancy_num=mysqli_num_rows($redundancy_rs);

				//echo "<!--redundancy sql:$StrSQL-->";
				//echo "<!--redundancy_num:$redundancy_num-->";
				if($redundancy_num<=0){
					$str=DispParam($str,"BTN_SEND");

				}else{
					$str=DispParamNone($str,"BTN_SEND");
				}
			}

			$str=DispParam($str,"BTN_SEND");

		}else{
			$str=DispParamNone($str,"BTN_SEND");
		}


		//pdf対応
		//詳細ページのpdfプレビューボタンのプルダウン
		if($FieldValue[34]=="見積り送付" || $FieldValue[34]=="運営手数料追加"){
			$str=DispParam($str,"PREVIEW_LIST_CB");
			$str=DispParam($str,"PREVIEW_LIST_R");
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE M2_ID='".$FieldValue[13]."' ";
			$StrSQL.=" AND M2_VERSION='".$FieldValue[14]."' AND (STATUS='見積り送付' OR STATUS='運営手数料追加') ";
			//echo('<!--previewSQL:'.$StrSQL.'-->');
			$preview_rs=mysqli_query(ConnDB(),$StrSQL);
			
			$opt_preview_list_r="";
			$opt_preview_list_cb="";
			while($preview_item = mysqli_fetch_assoc($preview_rs)){
				if($preview_item["STATUS"]!=$FieldValue[34]){
					//見積り送付のデータには見積り送付、
					//運営手数料のデータには運営手数料のデータのみプルダウンで表示。
					continue;
				}

				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_str="";
				$SCNo_ary["SCNo_yy"]=$preview_item["SCNo_yy"];
				$SCNo_ary["SCNo_mm"]=$preview_item["SCNo_mm"];
				$SCNo_ary["SCNo_dd"]=$preview_item["SCNo_dd"];
				$SCNo_ary["SCNo_cnt"]=$preview_item["SCNo_cnt"];
				$SCNo_ary["SCNo_else1"]=$preview_item["SCNo_else1"];
				$SCNo_ary["SCNo_else2"]=$preview_item["SCNo_else2"];
				$SCNo_str=formatAlphabetId($SCNo_ary);
				$preview_m2_version="";
				$preview_m2_version=$preview_item["M2_VERSION"];


				//運営手数料追加モードを経由した場合、すでに「見積り送付」済みかどうか
				//つまり同じ「見積り送付」のデータが自分いがいにあるかどうか
				$redundancy_num=0;
				$StrSQL="SELECT ID,STATUS,M2_ID,M2_VERSION,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID='".$preview_item["SHODAN_ID"]."' AND";
				$StrSQL.=" M2_ID='".$preview_item["M2_ID"]."' AND ";
				$StrSQL.=" M2_VERSION='".$preview_item["M2_VERSION"]."' AND ";
				$StrSQL.=" DIV_ID='".$preview_item["DIV_ID"]."' AND ";
				$StrSQL.=" ID!='".$preview_item["ID"]."' AND";
				$StrSQL.=" (STATUS='見積り送付' OR STATUS='運営手数料追加') ";
				$redundancy_rs=mysqli_query(ConnDB(),$StrSQL);
				$redundancy_num=mysqli_num_rows($redundancy_rs);
				$redundancy_item=mysqli_fetch_assoc($redundancy_rs);
				//echo "<!--StrSQL(redundancy):$StrSQL-->";
				//echo "<!--redundancy:";
				//var_dump($redundancy_item);
				//echo "-->";
				if($redundancy_num<=0){
					$str=DispParamNone($str,"SENT_MITSU_MARK");
				}else{
					$str=DispParam($str,"SENT_MITSU_MARK");
				}

				if( ($preview_item["M2_PAY_TYPE"]=="Once" || $preview_item["M2_PAY_TYPE"]=="Milestone") &&
					($preview_item["M_STATUS"]=="手数料追加" || $preview_item["M_STATUS"]=="手数料追加(前払い)") &&
					$redundancy_num<=0 ){
					//送信ボタン付きで表示
					$opt_preview_list_r.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=r&btn_version=2&key=".$preview_item["ID"]."'>";
					$opt_preview_list_r.=$SCNo_str."-Version".$preview_m2_version."</option>";
					
					//送信ボタンない状態で表示
					$opt_preview_list_cb.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=cb&btn_version=1&key=".$preview_item["ID"]."'>";
					$opt_preview_list_cb.=$SCNo_str."-Version".$preview_m2_version."</option>";
				}else if($preview_item["M2_PAY_TYPE"]=="Split" && 
					($preview_item["M_STATUS"]=="手数料追加" || $preview_item["M_STATUS"]=="手数料追加(前払い)") &&
					$redundancy_num<=0 ){
					$tmp="";
					$tmp=explode("-", $preview_item["DIV_ID"]);
					echo "<!--";
					var_dump($tmp);
					echo "-->";
					$part="";
					$part_no="";
					if(count($tmp)==3){
						$part=$tmp[2];
					}
					if($part=="Part0"){
						//送信ボタン付きで表示
						$opt_preview_list_r.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=r&btn_version=2&key=".$preview_item["ID"]."'>";
						$opt_preview_list_r.=$SCNo_str."-Version".$preview_m2_version."</option>";
						$opt_preview_list_cb.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=cb&btn_version=2&key=".$preview_item["ID"]."'>";
						$opt_preview_list_cb.=$SCNo_str."-Version".$preview_m2_version."</option>";
					}else{
						//送信ボタンない状態で表示
						$opt_preview_list_r.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=r&btn_version=1&key=".$preview_item["ID"]."'>";
						$opt_preview_list_r.=$SCNo_str."-Version".$preview_m2_version."</option>";
						$opt_preview_list_cb.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=cb&btn_version=1&key=".$preview_item["ID"]."'>";
						$opt_preview_list_cb.=$SCNo_str."-Version".$preview_m2_version."</option>";
					}
				}
				else{
					//送信ボタンない状態で表示
					$opt_preview_list_r.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=r&btn_version=1&key=".$preview_item["ID"]."'>";
					$opt_preview_list_r.=$SCNo_str."-Version".$preview_m2_version."</option>";
					$opt_preview_list_cb.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=cb&btn_version=1&key=".$preview_item["ID"]."'>";
					$opt_preview_list_cb.=$SCNo_str."-Version".$preview_m2_version."</option>";
				}
				
			}
			$str=str_replace("[OPT_PREVIEW_LIST_R]",$opt_preview_list_r,$str);
			$str=str_replace("[OPT_PREVIEW_LIST_CB]",$opt_preview_list_cb,$str);
		}else{
				$str=DispParamNone($str,"PREVIEW_LIST_CB");
				$str=DispParamNone($str,"PREVIEW_LIST_R");
		}


		//pdf対応
		//詳細ページのpdfプレビューボタンのプルダウン
		if($FieldValue[34]=="決済者発注承認" || $FieldValue[34]=="発注依頼"){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE DIV_ID='".$FieldValue[54]."' ";
			$StrSQL.=" AND STATUS='見積り送付'";
			//echo('<!--previewSQL:'.$StrSQL.'-->');
			$preview_rs=mysqli_query(ConnDB(),$StrSQL);
			
			$opt_preview_list_h="";
			while($preview_item = mysqli_fetch_assoc($preview_rs)){

				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_str="";
				$SCNo_ary["SCNo_yy"]=$preview_item["SCNo_yy"];
				$SCNo_ary["SCNo_mm"]=$preview_item["SCNo_mm"];
				$SCNo_ary["SCNo_dd"]=$preview_item["SCNo_dd"];
				$SCNo_ary["SCNo_cnt"]=$preview_item["SCNo_cnt"];
				$SCNo_ary["SCNo_else1"]=$preview_item["SCNo_else1"];
				$SCNo_ary["SCNo_else2"]=$preview_item["SCNo_else2"];
				$SCNo_str=formatAlphabetId($SCNo_ary);
				$preview_m2_version="";
				$preview_m2_version=$preview_item["M2_VERSION"];

				if($FieldValue[34]=="決済者発注承認"){
					//送信ボタンある状態で表示
					$opt_preview_list_h.="<option value='/a_filestatus/?mode=disp_frame2&preview_type=h&btn_version=2&key=".$preview_item["ID"]."'>";
					$opt_preview_list_h.=$SCNo_str."-Version".$preview_m2_version."</option>";

				}else if($kessai_num<=0 || $m2_item["KESSAI_SYONIN"]=="KESSAI_SYONIN:なし"){
					//送信ボタンある状態で表示
					$opt_preview_list_h.="<option value='/a_filestatus/?mode=disp_frame2&preview_type=h&btn_version=2&key=".$preview_item["ID"]."'>";
					$opt_preview_list_h.=$SCNo_str."-Version".$preview_m2_version."</option>";
				}else{
					//送信ボタンある状態で表示
					$opt_preview_list_h.="<option value='/a_filestatus/?mode=disp_frame2&preview_type=h&btn_version=1&key=".$preview_item["ID"]."'>";
					$opt_preview_list_h.=$SCNo_str."-Version".$preview_m2_version."</option>";
				}
				
			}
			$str=str_replace("[OPT_PREVIEW_LIST_HK]",$opt_preview_list_h,$str);
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
		$StrSQL="SELECT DAT_FILESTATUS.*,DAT_M1.M1_DVAL01,DAT_M2.M2_DVAL01 FROM ".$TableName." ";

		if(strpos($word, '商談ID：') !== false) {
			$shodan_id = str_replace('商談ID：', '', $word);
			$StrSQL.="
join (
select
  f2.SHODAN_ID,
  f2.MID1
from
  DAT_FILESTATUS f2
where
  f2.ID = " . $shodan_id . "
) f3
  on f3.SHODAN_ID = DAT_FILESTATUS.SHODAN_ID
  and f3.MID1 = DAT_FILESTATUS.MID1
			";
		}

		$StrSQL.=" LEFT JOIN DAT_M1 ON DAT_FILESTATUS.MID1=DAT_M1.MID ";
		$StrSQL.=" LEFT JOIN DAT_M2 ON DAT_FILESTATUS.MID2=DAT_M2.MID ".ListSql(mysqli_real_escape_string(ConnDB(),$sort),mysqli_real_escape_string(ConnDB(),$word)).";";
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
				$str.="<li class=\"paginate_button previous disabled\" id=\"table_summary_previous\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page-1)."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">前の".$PageSize."件</a></li>";
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

				$StrSQL="SELECT * from DAT_M2 where MID = '".$item['MID2']."';";
				$rs_m2=mysqli_query(ConnDB(),$StrSQL);
				$m2 = mysqli_fetch_assoc($rs_m2);
				//$str=str_replace("[D-MID2]",$m2['M2_DVAL01'],$str);
				$str=str_replace("[D-MID2]",$m2['M2_DVAL03'],$str);

				$m1_list = explode(',', $item['MID1_LIST']);
				$m1_name_list = '';
				foreach($m1_list as $m1_mid) {
					$StrSQL="SELECT * from DAT_M1 where MID = '".$m1_mid."';";
					$rs_m1=mysqli_query(ConnDB(),$StrSQL);
					$m1 = mysqli_fetch_assoc($rs_m1);
					$m1_name_list .= '<div>' . $m1['M1_DVAL01'] . '</div>';
				}
				$str=str_replace("[D-MID1_LIST]",$m1_name_list,$str);

				//プレビュー関連
				//リストページ
				//仕様変更で該当行のデータのみをリンク表示
				if($item["STATUS"]=="見積り送付" || $item["STATUS"]=="運営手数料追加"){
					$str=DispParam($str,"PREVIEW_LIST_CB");
					$str=DispParam($str,"PREVIEW_LIST_R");

					

					$SCNo_ary=array(
						"SCNo_yy" => "", 
						"SCNo_mm" => "", 
						"SCNo_dd" => "", 
						"SCNo_cnt" => "", 
						"SCNo_else1" => "", 
						"SCNo_else2" => "", 
					);
					$SCNo_str="";

					$SCNo_ary["SCNo_yy"]=$item["SCNo_yy"];
					$SCNo_ary["SCNo_mm"]=$item["SCNo_mm"];
					$SCNo_ary["SCNo_dd"]=$item["SCNo_dd"];
					$SCNo_ary["SCNo_cnt"]=$item["SCNo_cnt"];
					$SCNo_ary["SCNo_else1"]=$item["SCNo_else1"];
					$SCNo_ary["SCNo_else2"]=$item["SCNo_else2"];
					$SCNo_str=formatAlphabetId($SCNo_ary);

					$preview_m2_version="";
					$preview_m2_version=$item["M2_VERSION"];

					//送信ボタンない状態で表示
					$preview_name=$SCNo_str."-Version".$preview_m2_version;
					$str=str_replace("[OPT_PREVIEW_LIST_R_STR]",$preview_name,$str);
					$str=str_replace("[OPT_PREVIEW_LIST_CB_STR]",$preview_name,$str);
					

				}else{
						$str=DispParamNone($str,"PREVIEW_LIST_CB");
						$str=DispParamNone($str,"PREVIEW_LIST_R");
				}



//				//プレビュー関連
//				//リストページ
//				if($item["STATUS"]=="見積り送付" || $item["STATUS"]=="運営手数料追加"){
//					$str=DispParam($str,"PREVIEW_LIST_CB");
//					$str=DispParam($str,"PREVIEW_LIST_R");
//
//					$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE M2_ID='".$item["M2_ID"]."' ";
//					$StrSQL.=" AND M2_VERSION='".$item["M2_VERSION"]."' AND (STATUS='見積り送付' OR STATUS='運営手数料追加') ";
//					//echo('<!--previewSQL:'.$StrSQL.'-->');
//					$preview_rs=mysqli_query(ConnDB(),$StrSQL);
//					
//					$opt_preview_list_r="";
//					$opt_preview_list_cb="";
//					while($preview_item = mysqli_fetch_assoc($preview_rs)){
//						if($preview_item["STATUS"]!=$FieldValue[34]){
//							//見積り送付のデータには見積り送付、
//							//運営手数料のデータには運営手数料のデータのみプルダウンで表示。
//							continue;
//						}
//
//						$SCNo_ary=array(
//							"SCNo_yy" => "", 
//							"SCNo_mm" => "", 
//							"SCNo_dd" => "", 
//							"SCNo_cnt" => "", 
//							"SCNo_else1" => "", 
//							"SCNo_else2" => "", 
//						);
//						$SCNo_str="";
//	
//						$SCNo_ary["SCNo_yy"]=$preview_item["SCNo_yy"];
//						$SCNo_ary["SCNo_mm"]=$preview_item["SCNo_mm"];
//						$SCNo_ary["SCNo_dd"]=$preview_item["SCNo_dd"];
//						$SCNo_ary["SCNo_cnt"]=$preview_item["SCNo_cnt"];
//						$SCNo_ary["SCNo_else1"]=$preview_item["SCNo_else1"];
//						$SCNo_ary["SCNo_else2"]=$preview_item["SCNo_else2"];
//						$SCNo_str=formatAlphabetId($SCNo_ary);
//	
//						$preview_m2_version="";
//						$preview_m2_version=$preview_item["M2_VERSION"];
//
//						//送信ボタンない状態で表示
//						$opt_preview_list_r.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=r&btn_version=1&key=".$preview_item["ID"]."'>";
//						$opt_preview_list_r.=$SCNo_str."-Version".$preview_m2_version."</option>";
//
//						$opt_preview_list_cb.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=cb&btn_version=1&key=".$preview_item["ID"]."'>";
//						$opt_preview_list_cb.=$SCNo_str."-Version".$preview_m2_version."</option>";
//						
//						
//					}
//					$str=str_replace("[OPT_PREVIEW_LIST_R]",$opt_preview_list_r,$str);
//					$str=str_replace("[OPT_PREVIEW_LIST_CB]",$opt_preview_list_cb,$str);
//
//				}else{
//						$str=DispParamNone($str,"PREVIEW_LIST_CB");
//						$str=DispParamNone($str,"PREVIEW_LIST_R");
//				}



				
				$str=str_replace("[M1_DVAL01]",htmlspecialchars($item["M1_DVAL01"]),$str);
				$str=str_replace("[D-M1_DVAL01]",htmlspecialchars($item["M1_DVAL01"]),$str);
				for ($i=0; $i<=$FieldMax; $i=$i+1) {
					if ($FieldAtt[$i]==4) {
						if ($item[$FieldName[$i]]=="") {
							//$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
							$str=str_replace("[".$FieldName[$i]."]","",$str);
							//$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
							$str=str_replace("[D-".$FieldName[$i]."]","",$str);
						} 
					} 
					// HTMLエスケープ処理（一覧表示系）
					$str=str_replace("[".$FieldName[$i]."]",htmlspecialchars($item[$FieldName[$i]]),$str);
					$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($FieldName[$i].":","",htmlspecialchars($item[$FieldName[$i]]))),$str);
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

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			} 
		} 


		$str=$strU.$strMain.$strD;

		$str = MakeHTML($str,1,$lid);

		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);

		switch($sort){
			case "1":
				$str=DispParamNone($str, "SYODAN");
				$str=DispParam($str, "SYODAN_ASC");
				$str=DispParamNone($str, "SYODAN_DESC");
				break;
			case "2":
				$str=DispParamNone($str, "SYODAN");
				$str=DispParamNone($str, "SYODAN_ASC");
				$str=DispParam($str, "SYODAN_DESC");
				break;

			case "3":
				$str=DispParamNone($str, "M2");
				$str=DispParam($str, "M2_ASC");
				$str=DispParamNone($str, "M2_DESC");
				break;
			case "4":
				$str=DispParamNone($str, "M2");
				$str=DispParamNone($str, "M2_ASC");
				$str=DispParam($str, "M2_DESC");
				break;
			case "5":
				$str=DispParamNone($str, "M1");
				$str=DispParam($str, "M1_ASC");
				$str=DispParamNone($str, "M1_DESC");
				break;
			case "6":
				$str=DispParamNone($str, "M1");
				$str=DispParamNone($str, "M1_ASC");
				$str=DispParam($str, "M1_DESC");
				break;
			case "9":
				$str=DispParamNone($str, "STATUS");
				$str=DispParam($str, "STATUS_ASC");
				$str=DispParamNone($str, "STATUS_DESC");
				break;
			case "10":
				$str=DispParamNone($str, "STATUS");
				$str=DispParamNone($str, "STATUS_ASC");
				$str=DispParam($str, "STATUS_DESC");
				break;
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
		$str=DispParam($str, "SYODAN");
		$str=DispParamNone($str, "SYODAN_ASC");
		$str=DispParamNone($str, "SYODAN_DESC");
		$str=DispParam($str, "M2");
		$str=DispParamNone($str, "M2_ASC");
		$str=DispParamNone($str, "M2_DESC");
		$str=DispParam($str, "M1");
		$str=DispParamNone($str, "M1_ASC");
		$str=DispParamNone($str, "M1_DESC");
		$str=DispParam($str, "M1");
		$str=DispParamNone($str, "M1_ASC");
		$str=DispParamNone($str, "M1_DESC");
		$str=DispParam($str, "STATUS");
		$str=DispParamNone($str, "STATUS_ASC");
		$str=DispParamNone($str, "STATUS_DESC");
		$str=DispParam($str, "EDITDATE");
		$str=DispParamNone($str, "EDITDATE_ASC");
		$str=DispParamNone($str, "EDITDATE_DESC");

		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
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
				$FieldValue[$i]=htmlspecialchars($_POST[$FieldName[$i]]);
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
			$FieldValue[$i]=htmlspecialchars(str_replace("\\","",$_POST[$FieldName[$i]]));
		}
		if ($FieldAtt[$i]==4 && $mode=="save") {
			$exts = explode("[/\\.]", $_FILES["EP_".$FieldName[$i]]['name']);
			$n = count($exts) - 1;
			$extention = $exts[$n];
			if ($extention=="jpeg") {
				$extention="jpg";
			} 

			if ($extention!="" && !!isset($extention)) {
				//$filename=$FieldName[$i]."-".date("YmdHis").".".$extention;
				$filename=$extention;
				//$FieldValue[$i]=$filepath1.$filename;
				$FieldValue[$i]=$filename;
			} else {
				if ($FieldValue[$i]=="" || !isset($FieldValue[$i])) {
					//$filename="s.gif";
					$filename="";
					//$FieldValue[$i]=$filepath1.$filename;
					$FieldValue[$i]=$filename;
				} 
			} 

			if ($_POST["DEL_IMAGE_".$FieldName[$i]]=="on") {
				//$filename="s.gif";
				$filename="";
				//$FieldValue[$i]=$filepath1.$filename;
				$FieldValue[$i]=$filename;
			}
			if ($filename!="s.gif" && ($extention!="" && !!isset($extention))) {
				//move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], "data/".$filename);
				$fdir="data/".$FieldValue[0]."/";
				if(!file_exists($fdir)) {
					mkdir($fdir, 0777, true);
				}
				move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], $fdir.$filename);
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
			$FieldValue[$i]=htmlspecialchars($item[$FieldName[$i]]);
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
function SaveData($key,$sync_item_ary,$pdf_action)
{
	eval(globals());

	//M2_EXPORT_FEE_TABLE
	//RequestData()と、LoadDaa()の分をデコード
	//jsonにもどしてからDBに格納
	//echo "<!--at save:M2_EXPORT_FEE_TABLE(1):".$FieldValue[78]."-->";
	$FieldValue[78]=htmlspecialchars_decode(htmlspecialchars_decode($FieldValue[78]));
	echo "<!--at save:M2_EXPORT_FEE_TABLE(2):".$FieldValue[78]."-->";

	//// S2_FILEにデータがあったら自動的にステータスを「請求」に変更
	//// DAT_SHODANのステータスも「請求」に変更
	//if($FieldValue[30] != '') {
	//	$FieldValue[34] = '請求';
	//	
	//	$StrSQL="UPDATE DAT_SHODAN SET STATUS='請求', C_STATUS='請求' WHERE ID='".$FieldValue[1]."'";
	//	if (!(mysqli_query(ConnDB(),$StrSQL))) {
	//		die;
	//	}
	//}

	//echo "<!--FieldValue[54]:".$FieldValue[54]."-->";
	//echo "<!--checkDIV_ID:".checkDIV_ID($FieldValue[54])."-->";

	//一括払いか分割払いかチェックし、1活払いの場合は空を返し、
	//分割払いの場合は渡されたDIV_IDをそのまま返す関数
	$ck_div_id=checkDIV_ID($FieldValue[54]);
	//一括でもdiv_idを設定するようにしたので使う
	$div_id=$FieldValue[54];
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE DIV_ID='".$div_id."' AND STATUS='見積り送付' ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$mitsu_item=mysqli_fetch_assoc($rs);
	$SCNo_ary["SCNo_yy"]=$mitsu_item["SCNo_yy"];
	$SCNo_ary["SCNo_mm"]=$mitsu_item["SCNo_mm"];
	$SCNo_ary["SCNo_dd"]=$mitsu_item["SCNo_dd"];
	$SCNo_ary["SCNo_cnt"]=$mitsu_item["SCNo_cnt"];
	$SCNo_ary["SCNo_else1"]=$mitsu_item["SCNo_else1"];
	$SCNo_ary["SCNo_else2"]=$mitsu_item["SCNo_else2"];
	$SCNo_str=formatAlphabetId($SCNo_ary);
	$m2_quote_no=$mitsu_item["M2_QUOTE_NO"];
	$m2_version=$mitsu_item["M2_VERSION"];

	$date_stmp=date('Y/m/d H:i:s');
	$mid1=$FieldValue[2];
	$mid2=$FieldValue[3];
	$shodan_id=$FieldValue[1];

//以下、SubmitSeikyu()に引っ越し。
/*
	if($FieldValue[30] != '') {
		//【$FieldValue[34]が「納品確認」の時に、が表示されて値が入力された場合。】
		//今開いてるSTATUSが納品確認のデータで、S2_FILEに値が入ってる場合は、
		//DAT_FILESTATUSのSTATUSが請求になってるレコードがあるか探して、なかったら、新規データを作り、かつ今開いてるデータを更新
		//（DAT_FILESTATUSのSTATUSは更新しない）。
		//あったら、＜請求書（研究者）＞（S2_FILE入力箇所）を表示のみにする制御を別の場所で行う。
		
		if($FieldValue[34] == '納品確認'){

			//一括払いの場合
			if($ck_div_id==""){
				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$shodan_id."' AND MID1='".$mid1."' AND MID2='".$mid2."' AND STATUS='請求' AND S_STATUS='請求（研究者）' ";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				echo "<!--StrSQL1:$StrSQL-->";
				$item_num=mysqli_num_rows($rs);
				if($item_num==0){
					$StrSQL = "
					INSERT INTO DAT_FILESTATUS (
						SHODAN_ID,
						MID1,
						MID2,

						CATEGORY,
						STATUS,

						S2_FILE,
						S2_MESSAGE,
						DIV_ID,

						S_STATUS,

						NEWDATE,
						EDITDATE
						) VALUE (
						'".$shodan_id."',
						'".$mid1."',
						'".$mid2."',

						'請求',
						'請求',

						'".$FieldValue[30]."',
						'".$FieldValue[31]."',
						'".$div_id."',

						'請求（研究者）',

						'".$date_stmp."',
						'".$date_stmp."'
					)";

					echo "<!--StrSQL2:$StrSQL-->";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}

					$StrSQL="UPDATE DAT_SHODAN SET STATUS='請求', C_STATUS='請求', STATUS_SORT='9' WHERE ID='".$shodan_id."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}

				}

			//分割払いの場合
			}else{
				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$shodan_id."' AND MID1='".$mid1."' AND MID2='".$mid2."' AND STATUS='請求' AND S_STATUS='請求（研究者）' ";
				$StrSQL.=" AND DIV_ID='".$ck_div_id."'";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				echo "<!--StrSQL1:$StrSQL-->";
				$item_num=mysqli_num_rows($rs);
				if($item_num==0){
					$StrSQL = "
					INSERT INTO DAT_FILESTATUS (
						SHODAN_ID,
						MID1,
						MID2,

						CATEGORY,
						STATUS,

						S2_FILE,
						S2_MESSAGE,
						DIV_ID,

						S_STATUS,

						NEWDATE,
						EDITDATE
						) VALUE (
						'".$shodan_id."',
						'".$mid1."',
						'".$mid2."',

						'請求',
						'請求',

						'".$FieldValue[30]."',
						'".$FieldValue[31]."',
						'".$ck_div_id."',

						'請求（研究者）',

						'".$date_stmp."',
						'".$date_stmp."'
					)";

					echo "<!--StrSQL2:$StrSQL-->";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}

					$StrSQL="UPDATE DAT_SHODAN_DIV SET STATUS='請求', C_STATUS='請求' WHERE DIV_ID='".$ck_div_id."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				}
			}


			// 作成した請求のfilestatusのIDを取得
			$StrSQL="SELECT ID FROM DAT_FILESTATUS where NEWDATE='".$date_stmp."' and STATUS='請求' and S_STATUS='請求（研究者）' order by ID desc;";
			//echo('<!--SQL3:'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$s_item= mysqli_fetch_assoc($rs);
			$s_key = $s_item['ID'];
			echo "<!--s_item:";
			var_dump($s_item);
			echo "-->";

			$file_dir = __dir__ . '/data/';
			if(!file_exists($file_dir . $s_key . '/')) {
				mkdir($file_dir . $s_key, 0777, true);
			}
			if($_FILES['EP_S2_FILE']['name'] != '') {
				copy($file_dir . $key . '/' . $_FILES['EP_S2_FILE']['name'], $file_dir . $s_key . '/' . $_FILES['EP_S2_FILE']['name']);
			}
			//echo "<!--".$file_dir . $key . '/' . $_FILES['EP_S2_FILE']['name'].", ". $file_dir . $s_key . '/' . $_FILES['EP_S2_FILE']['name']."-->";

			//echo "<!--";
			//var_dump($_FILES);
			//echo "-->";

			//サプライヤーへのメッセージ
			$comment = 'Invoice to Scientist From Cosmo Bio.
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$s_key.'\'\');">
				'.$SCNo_str.' Version'.$m2_version.'
			</a>
			';

			// DAT_MESSAGE
			if($comment != '') {
				$aid = $mid1 . '-' . $mid2;
				$StrSQL = "
					INSERT INTO DAT_MESSAGE (
						AID,
						RID,
						ENABLE,
						NEWDATE,
						COMMENT,
						ETC02,
						ETC03,
						ETC04
					) VALUE (
						'".$aid."',
						'".$mid2."',
						'ENABLE:公開中',
						'".$date_stmp."',
						'".$comment."',
						'".$shodan_id."',
						'".$mid1."',
						'".$s_key."'
					)
				";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}


		}
	}
*/



//以下、仕様変更のためコメントアウト	
//	if($FieldValue[30] != '') {
//		//【$FieldValue[34]が「請求」の時に、＜請求書（研究者）＞（S2_FILE入力箇所）が表示されて値が入力された場合】
//		if($FieldValue[34] == '請求'){
//			//一括払いの場合
//			if($ck_div_id==""){
//				$StrSQL="UPDATE DAT_SHODAN SET STATUS='請求', C_STATUS='請求', STATUS_SORT='9' WHERE ID='".$FieldValue[1]."'";
//				if (!(mysqli_query(ConnDB(),$StrSQL))) {
//					die;
//				}
//
//			//分割払いの場合
//			}else{
//				$StrSQL="UPDATE DAT_SHODAN_DIV SET STATUS='請求', C_STATUS='請求' WHERE DIV_ID='".$ck_div_id."'";
//				if (!(mysqli_query(ConnDB(),$StrSQL))) {
//					die;
//				}
//			}
//
//		//【$FieldValue[34]が「納品確認」の時に、が表示されて値が入力された場合。】
//		//今開いてるSTATUSが納品確認のデータで、S2_FILEに値が入ってる場合は、
//		//DAT_FILESTATUSのSTATUSが請求になってるレコードがあるか探して、なかったら、新規データを作り、かつ今開いてるデータを更新
//		//（DAT_FILESTATUSのSTATUSは更新しない）。
//		//あったら、＜請求書（研究者）＞（S2_FILE入力箇所）を表示のみにする制御を別の場所で行う。
//		}else if($FieldValue[34] == '納品確認'){
//
//			//一括払いの場合
//			if($ck_div_id==""){
//				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
//				$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND STATUS='請求'";
//				$rs=mysqli_query(ConnDB(),$StrSQL);
//				echo "<!--StrSQL1:$StrSQL-->";
//				$item_num=mysqli_num_rows($rs);
//				if($item_num==0){
//					$StrSQL = "
//					INSERT INTO DAT_FILESTATUS (
//						SHODAN_ID,
//						MID1,
//						MID2,
//
//						CATEGORY,
//						STATUS,
//
//						S2_FILE,
//						S2_MESSAGE,
//
//						NEWDATE,
//						EDITDATE
//						) VALUE (
//						'".$FieldValue[1]."',
//						'".$FieldValue[2]."',
//						'".$FieldValue[3]."',
//
//						'請求',
//						'請求',
//
//						'".$FieldValue[30]."',
//						'".$FieldValue[31]."',
//
//						'".$date_stmp."',
//						'".$date_stmp."'
//					)";
//
//					echo "<!--StrSQL2:$StrSQL-->";
//					if (!(mysqli_query(ConnDB(),$StrSQL))) {
//						die;
//					}
//
//					$StrSQL="UPDATE DAT_SHODAN SET STATUS='請求', C_STATUS='請求', STATUS_SORT='9' WHERE ID='".$FieldValue[1]."'";
//					if (!(mysqli_query(ConnDB(),$StrSQL))) {
//						die;
//					}
//				}
//
//			//分割払いの場合
//			}else{
//				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
//				$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND STATUS='請求' ";
//				$StrSQL.=" AND DIV_ID='".$ck_div_id."'";
//				$rs=mysqli_query(ConnDB(),$StrSQL);
//				echo "<!--StrSQL1:$StrSQL-->";
//				$item_num=mysqli_num_rows($rs);
//				if($item_num==0){
//					$StrSQL = "
//					INSERT INTO DAT_FILESTATUS (
//						SHODAN_ID,
//						MID1,
//						MID2,
//
//						CATEGORY,
//						STATUS,
//
//						S2_FILE,
//						S2_MESSAGE,
//						DIV_ID,
//
//						NEWDATE,
//						EDITDATE
//						) VALUE (
//						'".$FieldValue[1]."',
//						'".$FieldValue[2]."',
//						'".$FieldValue[3]."',
//
//						'請求',
//						'請求',
//
//						'".$FieldValue[30]."',
//						'".$FieldValue[31]."',
//						'".$ck_div_id."',
//
//						'".$date_stmp."',
//						'".$date_stmp."'
//					)";
//
//					echo "<!--StrSQL2:$StrSQL-->";
//					if (!(mysqli_query(ConnDB(),$StrSQL))) {
//						die;
//					}
//
//					$StrSQL="UPDATE DAT_SHODAN_DIV SET STATUS='請求', C_STATUS='請求' WHERE DIV_ID='".$ck_div_id."'";
//					if (!(mysqli_query(ConnDB(),$StrSQL))) {
//						die;
//					}
//				}
//
//			}
//
//		}
//	}





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
			if($FieldName[$i]=="M2_EXPORT_FEE_TABLE"){
				$StrSQL.="'".$FieldValue[$i]."'";
			}else{
				$StrSQL.="'".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";
			}
			
		}
		$StrSQL=$StrSQL.")";
	} else {
		$StrSQL="UPDATE ".$TableName." SET ";
		for ($i=1; $i<=$FieldMax; $i++) {
			if($i>1){
				$StrSQL.=",";
			}
			if($FieldName[$i]=="M2_EXPORT_FEE_TABLE"){
				$StrSQL.="`".$FieldName[$i]."`='".$FieldValue[$i]."'";
			}else{
				$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";
			}
		}
		$StrSQL=$StrSQL." WHERE ".$FieldName[$FieldKey]."='".$key."'";
	} 
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}


	//分割見積り時の同期処理用
	$sync_column=array(
		"M2_STUDY_CODE",
		"M2_QUOTE_NO",
		"M2_DATE",
		"M2_QUOTE_VALID_UNTIL",
		"M2_SPECIAL_NOTE"
	);
	foreach ($sync_item_ary as $sync_id) {
		if($sync_id==$key){
			continue;
		}
		$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".$sync_id."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_num=mysqli_num_rows($rs);
		//echo "<!--at savedata():sync_id:$sync_id-->";
		if($item_num>0){
			$StrSQL="UPDATE ".$TableName." SET ";
			$dot=0;
			for ($i=1; $i<=$FieldMax; $i++) {
				if( in_array($FieldName[$i], $sync_column) ){
					if($dot>0){
						$StrSQL.=",";
					}
					if($FieldName[$i]=="M2_EXPORT_FEE_TABLE"){
						$StrSQL.="`".$FieldName[$i]."`='".$FieldValue[$i]."'";

					}else{
						$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";

					}
					$dot++;
				}
			}
			$StrSQL=$StrSQL." WHERE ".$FieldName[$FieldKey]."='".$sync_id."'";

			echo "<!--at savedata():StrSQL:$StrSQL-->";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
		}
	}
	

//以下、Requestdataでファイルをディレクトリに移動してるから不要な記述。そもそも、コピー元にそのファイルは存在しない。
//
//	// filestatusのIDを取得
//	$StrSQL="SELECT ID FROM DAT_FILESTATUS WHERE EDITDATE='".$FieldValue[33]."' order by ID desc;";
//	//echo('<!--File:'.$StrSQL.'-->');
//	$rs=mysqli_query(ConnDB(),$StrSQL);
//	$item_filestatus = mysqli_fetch_assoc($rs);
//	$key = $item_filestatus['ID'];
//
//	// ファイルを移動
//	$file_dir = __dir__ . '/../a_filestatus/data/';
//	if(!file_exists($file_dir . $key . '/')) {
//		mkdir($file_dir . $key, 0777, true);
//	}
//
//
//	if($_FILES['EP_FILE']['name'] != '') {
//		copy($file_dir . $_FILES['EP_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_FILE']['name']);
//	}
//	if($_FILES['EP_M1_FILE']['name'] != '') {
//		copy($file_dir . $_FILES['EP_M1_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_M1_FILE']['name']);
//	}
//	if($_FILES['EP_N_FILE']['name'] != '') {
//		copy($file_dir . $_FILES['EP_N_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_N_FILE']['name']);
//	}
//	if($_FILES['EP_S_FILE']['name'] != '') {
//		copy($file_dir . $_FILES['EP_S_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_S_FILE']['name']);
//	}
//	if($_FILES['EP_S2_FILE']['name'] != '') {
//		copy($file_dir . $_FILES['EP_S2_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_S2_FILE']['name']);
//	}

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


//=========================================================================================================
//名前 PDF生成
//機能 PDF生成
//引数 PDFにしたいhtmlソース（文字列）
//戻値 なし
//=========================================================================================================

function downloadPDF($str){
	//pdfデータ生成
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

	$pdf ->writeHTML($str);

	//To Avoid the PDF Error
	ob_end_clean();

	//ダウンロードの場合
	$pdf->Output(date('Ymd-his')."-test.pdf", "D");
}


//=========================================================================================================
//名前 PDF生成し送信
//機能 PDF生成し送信
//引数 PDFにしたいhtmlソース（文字列）
//戻値 なし
//=========================================================================================================
function sendPDF($str,$to){
	eval(globals());

	//pdfデータ生成
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

	$pdf ->writeHTML($str);

	//To Avoid the PDF Error
	ob_end_clean();

	$filename=date('Ymd-his')."-tmp.pdf";
	$pdfData=$pdf->Output($filename, "S");

	//メールの基本情報
	//$StrSQL="SELECT ID,MID,EMAIL FROM DAT_M2 WHERE MID='".$FieldValue[3]."'";
	////echo "<--m2_mail:SQL:$StrSQL-->";
	//$m2_mail_rs=mysqli_query(ConnDB(),$StrSQL);
	//$m2_mail_item = mysqli_fetch_assoc($m2_mail_rs);

	//$to=$m2_mail_item["EMAIL"];
	//echo "<!--to:$to-->";
	$to2 = 'h.tsurumi@ms123.co.jp';
	$to3 = 'info@scientist-cube.com';
	$from = 'info@scientist-cube.com';
	$fromName = '送信者名';
	$subject = 'pdfメール送信テスト';
	$body = "pdfメール送信テストです。\n添付のPDFファイルをご確認ください。";
	$attachFileName = $filename; // 添付ファイル名
	
	// ヘッダー情報
	$headers = [
		'From' => mb_encode_mimeheader($fromName) . ' <' . $from . '>',
		'MIME-Version' => '1.0',
	];
	
	// バウンダリを生成
	$boundary = '----=' . md5(uniqid(rand(), true));
	$headers['Content-Type'] = 'multipart/mixed; boundary="' . $boundary . '"';
	
	// メッセージ全体のボディを作成
	$message = '';
	
	// テキスト部分
	$message .= '--' . $boundary . "\r\n";
	$message .= 'Content-Type: text/plain; charset=ISO-2022-JP' . "\r\n";
	//$message .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
	$message .= 'Content-Transfer-Encoding: 7bit' . "\r\n";
	//$message .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
	$message .= "\r\n";
	$message .= $body . "\r\n";
	$message .= "\r\n";
	
	// 添付ファイル部分
	$encodedPdf = chunk_split(base64_encode($pdfData)); // TCPDFから得たPDFデータをエンコード
	
	$message .= '--' . $boundary . "\r\n";
	$message .= 'Content-Type: application/pdf; name="' . $attachFileName . '"' . "\r\n";
	$message .= 'Content-Disposition: attachment; filename="' . $attachFileName . '"' . "\r\n";
	$message .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$message .= "\r\n";
	$message .= $encodedPdf . "\r\n"; // エンコードしたPDFデータを追加
	$message .= "\r\n";
	
	// 最後のバウンダリ
	$message .= '--' . $boundary . '--' . "\r\n";

	////ボタンエリアが消える対策
	//$btn_area='
	//<a href="javascript:close_popup();">
	//	Close
	//</a><br>
	//<a href="/a_filestatus/?mode=disp_frame1&preview_type=cb&pdf_action=download&key=[KEY]">
	//	Download
	//</a><br>';
	//echo $btn_area."<br>";

	// メール送信
	if (mb_send_mail($to, $subject, $message, $headers)) {
		echo '<span style="color:tomato;">「'.$to.'」へPDFを生成し、メールを送信しました。</span><br>';
	} else {
		echo '<span style="color:tomato;">「'.$to.'」へのメールの送信に失敗しました。</span><br>';
	}

	//デバッグ用
	if(mb_send_mail($to2, $subject, $message, $headers)){
		echo '<span style="color:tomato;">「'.$to2.'」へPDFを生成し、メールを送信しました。</span><br>';
	}else{
		echo '<span style="color:tomato;">「'.$to2.'」へのメールの送信に失敗しました。</span><br>';
	}

	//デバッグ用
	if(mb_send_mail($to3, $subject, $message, $headers)){
		echo '<span style="color:tomato;">「'.$to3.'」へPDFを生成し、メールを送信しました。</span><br>';
	}else{
		echo '<span style="color:tomato;">「'.$to3.'」へのメールの送信に失敗しました。</span><br>';
	}
}


//=========================================================================================================
//名前 DB書き込み（運営手数料追加モード時の見積もり送信）
//機能 DBにレコードを保存
//引数 a_filestatusのレコードのID($key)
//戻値 $function_ret
//=========================================================================================================
function SubmitMitsumori($key)
{
	eval(globals());

	echo "<!--at SubmitMitsumori-->";
	//更新基本データ
	$date_stmp=date('Y/m/d H:i:s');
	$status="見積り送付";

	//$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".mysqli_real_escape_string(ConnDB(),$key)."' order by ID desc limit 1;";
	//$rs=mysqli_query(ConnDB(),$StrSQL);
	//$itemFSD=mysqli_fetch_assoc($rs);
	//echo "<!--StrSQL:$StrSQL-->";
	//
	//$filestatus_id_original=$itemFSD["FILESTATUS_ID"];

	$filestatus_id_original=$key;

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$filestatus_id_original."' order by ID desc limit 1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemFS=mysqli_fetch_assoc($rs);
	echo "<!--StrSQL:$StrSQL-->";

	$fs_shodan_id=$itemFS["SHODAN_ID"];
	$fs_m2_id=$itemFS["M2_ID"];
	$fs_m2_version=$itemFS["M2_VERSION"];
	$fs_mid1=$itemFS["MID1"];
	$fs_mid2=$itemFS["MID2"];

	echo "<!--filestatus_id_original:$filestatus_id_original-->";
	echo "<!--fs_shodan_id:$fs_shodan_id-->";
	echo "<!--fs_m2_id:$fs_m2_id-->";
	echo "<!--fs_mid1:$fs_mid1-->";
	echo "<!--fs_mid2:$fs_mid2-->";



	//DAT_SHODANの該当取引のレコードのステータスを「見積り送付」に更新
	$StrSQL = "
		UPDATE DAT_SHODAN SET
			EDITDATE = '".$date_stmp."',
			STATUS = '".$status."'
		WHERE
		  ID = ".$fs_shodan_id."
		";

	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}



	//コピー対象の行をDAT_FILESTATUSから探す
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$fs_shodan_id."' ";
	$StrSQL.=" AND M2_ID='".$fs_m2_id."' ";
	$StrSQL.=" AND M2_VERSION='".$fs_m2_version."' ";
	$StrSQL.=" AND MID1='".$fs_mid1."' ";
	$StrSQL.=" AND MID2='".$fs_mid2."' ";
	$StrSQL.=" AND STATUS='運営手数料追加' ";
	$copy_rs_FS=mysqli_query(ConnDB(),$StrSQL);

	while( $copy_item_FS=mysqli_fetch_assoc($copy_rs_FS) ){
			//DAT_FILESTATUSに新規レコード作成
		$StrSQL="INSERT INTO DAT_FILESTATUS (";
			$i=0;
			foreach ($copy_item_FS as $idx => $val) {
				if($idx=="ID"){
					continue;
				}
				if($i>=1){
					$StrSQL.=",";
				}

				$StrSQL.="`".$idx."`";

				$i++;
			}
			$StrSQL=$StrSQL.") VALUES (";
			$i=0;
			foreach ($copy_item_FS as $idx => $val) {
				if($idx=="ID"){
					continue;
				}
				if($i>=1){
					$StrSQL.=",";
				}

				if($idx=="STATUS"){
					$StrSQL.="'".$status."'";
				}else if($idx=="NEWDATE"){
					$StrSQL.="'".$date_stmp."'";
				}else if($idx=="EDITDATE"){
					$StrSQL.="'".$date_stmp."'";
				}else{
					$StrSQL.="'".$val."'";
				}

				$i++;
			}
			$StrSQL=$StrSQL.")";
			echo "<!--StrSQL(copy_rs_FS):$StrSQL-->";
			
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
			

			//上記で作成したDAT_FILESTATUSのIDとデータ取得
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE NEWDATE='".$date_stmp."' order by ID desc limit 1;";
			$new_FS_rs=mysqli_query(ConnDB(),$StrSQL);
			$item_new_FS=mysqli_fetch_assoc($new_FS_rs);
			$new_filestatus_id=$item_new_FS["ID"];


			$copy_filestatus_id=$copy_item_FS["ID"];
			//コピー対象の行をDAT_FILESTATUS_DETAILから探す
			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$copy_filestatus_id."' ";
			$copy_rs_FSD=mysqli_query(ConnDB(),$StrSQL);

			while( $copy_item_FSD=mysqli_fetch_assoc($copy_rs_FSD) ){
				//DAT_FILESTATUS_DETAILに新規レコード作成
				$StrSQL="INSERT INTO DAT_FILESTATUS_DETAIL (";
					$i=0;
					foreach ($copy_item_FSD as $idx => $val) {
						if($idx=="ID"){
							continue;
						}
						if($i>=1){
							$StrSQL.=",";
						}

						$StrSQL.="`".$idx."`";

						$i++;
					}
					$StrSQL=$StrSQL.") VALUES (";
					$i=0;
					foreach ($copy_item_FSD as $idx => $val) {
						if($idx=="ID"){
							continue;
						}
						if($i>=1){
							$StrSQL.=",";
						}

						if($idx=="FILESTATUS_ID"){
							$StrSQL.="'".$new_filestatus_id."'";
						}else if($idx=="NEWDATE"){
							$StrSQL.="'".$date_stmp."'";
						}else if($idx=="EDITDATE"){
							$StrSQL.="'".$date_stmp."'";
						}else{
							$StrSQL.="'".$val."'";
						}

						$i++;
					}
					$StrSQL=$StrSQL.")";
					echo "<!--StrSQL(copy_item_FSD):$StrSQL-->";
					
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
			}

	}
	

	//メッセージウィンドウにメッセージを表示の処理
	//DAT_MESSAGEに「見積り送付」の新規レコード作成

	//今作ったレコード探す
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$fs_shodan_id."' ";
	$StrSQL.=" AND M2_ID='".$fs_m2_id."' ";
	$StrSQL.=" AND M2_VERSION='".$fs_m2_version."' ";
	$StrSQL.=" AND MID1='".$fs_mid1."' ";
	$StrSQL.=" AND MID2='".$fs_mid2."' ";
	$StrSQL.=" AND NEWDATE='".$date_stmp."' ";
	$StrSQL.=" AND STATUS='見積り送付' ";
	$new_rs_FS=mysqli_query(ConnDB(),$StrSQL);

	while( $new_item_FS=mysqli_fetch_assoc($new_rs_FS) ){
		$fs_key=$new_item_FS["ID"];
		$shodan_id=$new_item_FS["SHODAN_ID"];
		$m2_id=$new_item_FS["M2_ID"];
		$m2_version=$new_item_FS["M2_VERSION"];
		$mid1=$new_item_FS["MID1"];
		$mid2=$new_item_FS["MID2"];
		$aid=$mid1."-".$mid2;

		$tmp="";
		$div_id=$new_item_FS["DIV_ID"];
		$tmp=explode("-", $div_id);
		//echo "<!--";
		//var_dump($tmp);
		//echo "-->";
		$part="";
		$disp_part="";
		if($new_item_FS["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
			$part=$tmp[2];
			//$disp_part="Split ".$part;
		}

		//マイルストーンの場合はPart0はユーザには表示しない
		if($new_item_FS["M2_PAY_TYPE"]=="Milestone" && $part=="Part0"){
			$part0_key=$fs_key;
			continue;
		}

		//2回払いの場合はPart0しかユーザには表示しない
		if($new_item_FS["M2_PAY_TYPE"]=="Split" && $part!="Part0"){
			//2回払いの場合はPart0はユーザには表示しない
			continue;
		}

		//「Scientist3 control No.」が設定されていたら整形
		$SCNo_ary=array(
			"SCNo_yy" => "", 
			"SCNo_mm" => "", 
			"SCNo_dd" => "", 
			"SCNo_cnt" => "", 
			"SCNo_else1" => "", 
			"SCNo_else2" => "", 
		);
		$m2_quote_no="";
		$SCNo_ary["SCNo_yy"]=$new_item_FS["SCNo_yy"];
		$SCNo_ary["SCNo_mm"]=$new_item_FS["SCNo_mm"];
		$SCNo_ary["SCNo_dd"]=$new_item_FS["SCNo_dd"];
		$SCNo_ary["SCNo_cnt"]=$new_item_FS["SCNo_cnt"];
		$SCNo_ary["SCNo_else1"]=$new_item_FS["SCNo_else1"];
		$SCNo_ary["SCNo_else2"]=$new_item_FS["SCNo_else2"];
		$SCNo_str=formatAlphabetId($SCNo_ary);
		$m2_quote_no=$new_item_FS["M2_QUOTE_NO"];

		//マイルストーン払いの場合に、Item名も表示。
		$item_name="";
		if($new_item_FS["M2_PAY_TYPE"]=='Milestone'){
			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL where FILESTATUS_ID='".$fs_key."' order by ID desc;";
				//echo('<!--'.$StrSQL.'-->');
			$rs_dmile=mysqli_query(ConnDB(),$StrSQL);
			$item_dmile = mysqli_fetch_assoc($rs_dmile);
			$item_name=$item_dmile["M2_DETAIL_ITEM"];
		}


		//研究者（MID2）へのメッセージのみ送信
		$comment = '';
		if($new_item_FS["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part!="Part1"){
			//マイルストーン払いの場合に、Item名も表示。
			//Part1以外には再見積り依頼ボタンを非表示
			$comment = '見積を受信しました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$fs_key.'\'\');">
				'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.'-'.$item_name.' '.$disp_part.'
			</a>';
		}else if($new_item_FS["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part=="Part1"){
			//マイルストーン払いの場合に、Item名も表示。
			//Part1には再見積り依頼ボタンを表示
			//再見積り依頼ボタンにはPart0のkeyを使う
			$comment = '見積を受信しました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$fs_key.'\'\');">
				'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.'-'.$item_name.' '.$disp_part.'
			</a>' . 
			'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$part0_key.'" target="_top">再見積りを依頼する</a>
			';
		}else if($new_item_FS["M2_PAY_TYPE"]=='Split' && $part=="Part0"){
			//2回払いでPart0以外は上でcontinueしてる。
			//2回払いの場合はPart0しか表示しない。
			//2回払いの場合に、Part0に再見積り依頼ボタンを表示
			$comment = '見積を受信しました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$fs_key.'\'\');">
				'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
			</a>' . 
			'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$fs_key.'" target="_top">再見積りを依頼する</a>
			';
		
		}else if($new_item_FS["M2_PAY_TYPE"]=='Once'){
			//1回払いの場合に、再見積り依頼ボタンを表示
			$comment = '見積を受信しました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$fs_key.'\'\');">
				'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
			</a>' . 
			'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$fs_key.'" target="_top">再見積りを依頼する</a>
			';
		}else{
			//例外があったら表示のみ
			$comment = '見積を受信しました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$fs_key.'\'\');">
				'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
			</a>';
		}


		$StrSQL = "
				INSERT INTO DAT_MESSAGE (
					AID,
					RID,
					ENABLE,
					NEWDATE,
					COMMENT,
					ETC02,
					ETC03,
					ETC04
				) VALUE (
					'".$aid."',
					'".$mid1."',
					'ENABLE:公開中',
					'".$date_stmp."',
					'".$comment."',
					'".$shodan_id."',
					'".$mid2."',
					'".$fs_key."'
				)
			";
		
		echo "<!--StrSQL(message):$StrSQL-->";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
		}


	}

}



//=========================================================================================================
//名前 DB書き込み（決済者発注承認時の「受注承認」レコードの送信）
//機能 DBにレコードを保存
//引数 a_filestatusのレコード（発注書プレビュー用につかっている「見積り送付」のレコード）のID($key):
//戻値 $function_ret
//=========================================================================================================
function SubmitJyutyusyonin($key)
{
	eval(globals());

	echo "<!--at SubmitJyutyusyonin-->";

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$key."' order by ID desc limit 1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemFS=mysqli_fetch_assoc($rs);
	echo "<!--StrSQL:$StrSQL-->";

	$shodan_id=$itemFS["SHODAN_ID"];
	$div_id=$itemFS["DIV_ID"];
	$mid1=$itemFS["MID1"];
	$mid2=$itemFS["MID2"];

	//更新基本データ
	$date_stmp=date('Y/m/d H:i:s');
	if( ($itemFS["M2_PAY_TYPE"]=="Once") && 
		($itemFS["M_STATUS"]=="直接送付(前払い)" || $itemFS["M_STATUS"]=="手数料追加(前払い)") ){
		$status="受注承認(一括前払い)";
	
	}else if( ($itemFS["M2_PAY_TYPE"]=="Split" ||$itemFS["M2_PAY_TYPE"]=="Milestone") && 
		($itemFS["M_STATUS"]=="直接送付(前払い)" || $itemFS["M_STATUS"]=="手数料追加(前払い)") ){
		$status="受注承認(前払い)";

	}else{
		$status="受注承認";
	}
//	if(($itemFS["M2_PAY_TYPE"]=="Once") && 
//		($itemFS["M_STATUS"]=="直接送付(前払い)" || $itemFS["M_STATUS"]=="手数料追加(前払い)") ){
//		$status="受注承認(一括前払い)";
//
//	}else{
//		$status="受注承認";
//	}
	$c_status = '実施中';
	$status_sort = '6';


	if(checkDIV_ID($div_id)==""){
		$StrSQL = "
		UPDATE DAT_SHODAN SET
		EDITDATE = '".$date_stmp."',
		STATUS_SORT = '".$status_sort."',
		C_STATUS = '".$c_status."',
		STATUS = '".$status."'
		WHERE
		ID = ".$shodan_id."
		";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

	}else{
		$StrSQL = "
		UPDATE DAT_SHODAN_DIV SET
		EDITDATE = '".$date_stmp."',
		C_STATUS = '".$c_status."',
		STATUS = '".$status."'
		WHERE
		DIV_ID = '".$div_id."'
		";
			//echo "<!--sql: $StrSQL-->";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}

	$StrSQL = "
		INSERT INTO DAT_FILESTATUS (
			SHODAN_ID,
			MID1,
			MID2,

			CATEGORY,
			STATUS,

			DIV_ID,

			NEWDATE,
			EDITDATE
		) VALUE (
			'".$shodan_id."',
			'".$mid1."',
			'".$mid2."',

			'".$c_status."',
			'".$status."',

			'".$div_id."',

			'".$date_stmp."',
			'".$date_stmp."'
		)";
	echo('<!--'.$StrSQL.'-->');
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}



//	$StrSQL="SELECT ID FROM DAT_FILESTATUS where SHODAN_ID='".$shodan_id."' ";
//	$StrSQL.=" AND STATUS='発注依頼' AND DIV_ID='".$div_id."' order by ID desc;";
//	echo('<!--'.$StrSQL.'-->');
//	$rs=mysqli_query(ConnDB(),$StrSQL);
//	$h_item = mysqli_fetch_assoc($rs);
//	$h_key = $h_item['ID'];
//	echo "<!--h_item:\n";
//	var_dump($h_item);
//	echo "-->";
//
//	$comment = 'A purchase order has been approved.
//	<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$h_key.'\'\');">
//		Order request
//	</a>
//	';
//
//	if($comment != '') {
//		$aid = $mid1 . '-' . $mid2;
//		$StrSQL = "
//			INSERT INTO DAT_MESSAGE (
//				AID,
//				RID,
//				ENABLE,
//				NEWDATE,
//				COMMENT,
//				ETC02,
//				ETC03,
//				ETC04
//			) VALUE (
//				'".$aid."',
//				'".$mid1."',
//				'ENABLE:公開中',
//				'".$date_stmp."',
//				'".$comment."',
//				'".$shodan_id."',
//				'".$mid1."',
//				'".$key."'
//			)
//		";
//		if (!(mysqli_query(ConnDB(),$StrSQL))) {
//			die;
//		}
//	}


}


//=========================================================================================================
//名前 DB書き込み（納品確認データに表示される請求書プレビューのSENDボタンを押したときの、「請求（研究者）」レコードの送信）
//機能 DBにレコードを保存
//引数 a_filestatusのレコード（「納品確認」or「受注承認(一括前払い)」or「受注承認(前払い)」のレコード）のID($key)
//※開いてるページと違うページにデータを作るためにkeyを使う。開いてるページのkeyが入ってればいい。
//戻値 $function_ret
//=========================================================================================================
function SubmitSeikyu($key){
	eval(globals());

	//一括払いか分割払いかチェックし、1活払いの場合は空を返し、
	//分割払いの場合は渡されたDIV_IDをそのまま返す関数
	$ck_div_id=checkDIV_ID($FieldValue[54]);
	//一括でもdiv_idを設定するようにしたので使う
	$div_id=$FieldValue[54];
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE DIV_ID='".$div_id."' AND STATUS='見積り送付' ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$mitsu_item=mysqli_fetch_assoc($rs);
	$SCNo_ary["SCNo_yy"]=$mitsu_item["SCNo_yy"];
	$SCNo_ary["SCNo_mm"]=$mitsu_item["SCNo_mm"];
	$SCNo_ary["SCNo_dd"]=$mitsu_item["SCNo_dd"];
	$SCNo_ary["SCNo_cnt"]=$mitsu_item["SCNo_cnt"];
	$SCNo_ary["SCNo_else1"]=$mitsu_item["SCNo_else1"];
	$SCNo_ary["SCNo_else2"]=$mitsu_item["SCNo_else2"];
	$SCNo_str=formatAlphabetId($SCNo_ary);
	$m2_quote_no=$mitsu_item["M2_QUOTE_NO"];
	$m2_version=$mitsu_item["M2_VERSION"];

	$date_stmp=date('Y/m/d H:i:s');
	$mid1=$FieldValue[2];
	$mid2=$FieldValue[3];
	$shodan_id=$FieldValue[1];

	if($FieldValue[30] != '') {
		//【$FieldValue[34]が「納品確認」の時に、が表示されて値が入力された場合。】
		//今開いてるSTATUSが納品確認のデータで、S2_FILEに値が入ってる場合は、
		//DAT_FILESTATUSのSTATUSが請求になってるレコードがあるか探して、なかったら、新規データを作り、かつ今開いてるデータを更新
		//（DAT_FILESTATUSのSTATUSは更新しない）。
		//あったら、＜請求書（研究者）＞（S2_FILE入力箇所）を表示のみにする制御を別の場所で行う。

		if($FieldValue[34] == '納品確認'){
			$status = '請求';
			$c_status = '請求';
			$status_sort = '9';

		}else if($FieldValue[34] == '受注承認(一括前払い)'){
			$status = '請求書送付(一括前払い)';
			$c_status = '請求';
			$status_sort = '9';

		}else if($FieldValue[34] == '受注承認(前払い)'){
			$status = '請求書送付(前払い)';
			$c_status = '請求';
			$status_sort = '9';
		}
		

		
		if($FieldValue[34] == '納品確認' || $FieldValue[34] == '受注承認(一括前払い)' || $FieldValue[34] == '受注承認(前払い)'){

			//一括払いの場合
			if($ck_div_id==""){
				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$shodan_id."' AND MID1='".$mid1."' AND MID2='".$mid2."' AND (STATUS='請求' OR STATUS='請求書送付(一括前払い)' OR STATUS='請求書送付(前払い)') AND S_STATUS='請求（研究者）' ";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				echo "<!--StrSQL1:$StrSQL-->";
				$item_num=mysqli_num_rows($rs);
				if($item_num==0){
					$StrSQL = "
					INSERT INTO DAT_FILESTATUS (
						SHODAN_ID,
						MID1,
						MID2,

						CATEGORY,
						STATUS,

						S2_FILE,
						S2_MESSAGE,
						DIV_ID,

						S_STATUS,

						NEWDATE,
						EDITDATE
						) VALUE (
						'".$shodan_id."',
						'".$mid1."',
						'".$mid2."',

						'".$c_status."',
						'".$status."',

						'".$FieldValue[30]."',
						'".$FieldValue[31]."',
						'".$div_id."',

						'請求（研究者）',

						'".$date_stmp."',
						'".$date_stmp."'
					)";

					echo "<!--StrSQL2:$StrSQL-->";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}

					$StrSQL="UPDATE DAT_SHODAN SET STATUS='".$status."', C_STATUS='".$c_status."', STATUS_SORT='9', EDITDATE='".$date_stmp."' WHERE ID='".$shodan_id."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}

				}

			//分割払いの場合
			}else{
				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$shodan_id."' AND MID1='".$mid1."' AND MID2='".$mid2."' AND (STATUS='請求' OR STATUS='請求書送付(一括前払い)' OR STATUS='請求書送付(前払い)') AND S_STATUS='請求（研究者）' ";
				$StrSQL.=" AND DIV_ID='".$ck_div_id."'";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				echo "<!--StrSQL1:$StrSQL-->";
				$item_num=mysqli_num_rows($rs);
				if($item_num==0){
					$StrSQL = "
					INSERT INTO DAT_FILESTATUS (
						SHODAN_ID,
						MID1,
						MID2,

						CATEGORY,
						STATUS,

						S2_FILE,
						S2_MESSAGE,
						DIV_ID,

						S_STATUS,

						NEWDATE,
						EDITDATE
						) VALUE (
						'".$shodan_id."',
						'".$mid1."',
						'".$mid2."',

						'".$c_status."',
						'".$status."',

						'".$FieldValue[30]."',
						'".$FieldValue[31]."',
						'".$ck_div_id."',

						'請求（研究者）',

						'".$date_stmp."',
						'".$date_stmp."'
					)";

					echo "<!--StrSQL2:$StrSQL-->";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}

					$StrSQL="UPDATE DAT_SHODAN_DIV SET STATUS='".$status."', C_STATUS='".$c_status."', EDITDATE='".$date_stmp."' WHERE DIV_ID='".$ck_div_id."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				}
			}


			// 作成した請求のfilestatusのIDを取得
			$StrSQL="SELECT ID FROM DAT_FILESTATUS where NEWDATE='".$date_stmp."' and (STATUS='請求' OR STATUS='請求書送付(一括前払い)' OR STATUS='請求書送付(前払い)') and S_STATUS='請求（研究者）' order by ID desc;";
			//echo('<!--SQL3:'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$s_item= mysqli_fetch_assoc($rs);
			$s_key = $s_item['ID'];
			echo "<!--s_item:";
			var_dump($s_item);
			echo "-->";

			$file_dir = __dir__ . '/data/';
			if(!file_exists($file_dir . $s_key . '/')) {
				mkdir($file_dir . $s_key, 0777, true);
			}

			if($FieldValue[30] != '') {
				copy($file_dir . $key . '/' . $FieldValue[30], $file_dir . $s_key . '/' . $FieldValue[30]);
			}

			//if($_FILES['EP_S2_FILE']['name'] != '') {
			//	copy($file_dir . $key . '/' . $_FILES['EP_S2_FILE']['name'], $file_dir . $s_key . '/' . $_FILES['EP_S2_FILE']['name']);
			//}
			////echo "<!--".$file_dir . $key . '/' . $_FILES['EP_S2_FILE']['name'].", ". $file_dir . $s_key . '/' . $_FILES['EP_S2_FILE']['name']."-->";
			//
			////echo "<!--";
			////var_dump($_FILES);
			////echo "-->";

			//サプライヤーへのメッセージ
			$comment = 'Invoice to Scientist From Cosmo Bio.
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$s_key.'\'\');">
				'.$SCNo_str.' Version'.$m2_version.'
			</a>
			';

			// DAT_MESSAGE
			if($comment != '') {
				$aid = $mid1 . '-' . $mid2;
				$StrSQL = "
					INSERT INTO DAT_MESSAGE (
						AID,
						RID,
						ENABLE,
						NEWDATE,
						COMMENT,
						ETC02,
						ETC03,
						ETC04
					) VALUE (
						'".$aid."',
						'".$mid2."',
						'ENABLE:公開中',
						'".$date_stmp."',
						'".$comment."',
						'".$shodan_id."',
						'".$mid1."',
						'".$s_key."'
					)
				";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					echo "<span class='err-disp'>送信エラー</span>";
					die;
				}
			}


		}
	}else{
		echo "<span class='err-disp'>送信エラー：添付ファイルを選択し保存してからもう一度お試しください</span>";
	}

}


//=========================================================================================================
//名前 DB書き込み（完了データに表示される請求書プレビューのSENDボタンを押したときの、「完了後対応」（追加請求（研究者））レコードの送信）
//機能 DBにレコードを保存
//引数 a_filestatusのレコード（「納品確認」のレコード）のID($key):
//戻値 $function_ret
//=========================================================================================================
function SubmitSeikyu_add($key){
	eval(globals());

	//一括払いか分割払いかチェックし、1活払いの場合は空を返し、
	//分割払いの場合は渡されたDIV_IDをそのまま返す関数
	$ck_div_id=checkDIV_ID($FieldValue[54]);
	//一括でもdiv_idを設定するようにしたので使う
	$div_id=$FieldValue[54];
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE DIV_ID='".$div_id."' AND STATUS='見積り送付' ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$mitsu_item=mysqli_fetch_assoc($rs);
	$SCNo_ary["SCNo_yy"]=$mitsu_item["SCNo_yy"];
	$SCNo_ary["SCNo_mm"]=$mitsu_item["SCNo_mm"];
	$SCNo_ary["SCNo_dd"]=$mitsu_item["SCNo_dd"];
	$SCNo_ary["SCNo_cnt"]=$mitsu_item["SCNo_cnt"];
	$SCNo_ary["SCNo_else1"]=$mitsu_item["SCNo_else1"];
	$SCNo_ary["SCNo_else2"]=$mitsu_item["SCNo_else2"];
	$SCNo_str=formatAlphabetId($SCNo_ary);
	$m2_quote_no=$mitsu_item["M2_QUOTE_NO"];
	$m2_version=$mitsu_item["M2_VERSION"];

	$date_stmp=date('Y/m/d H:i:s');
	$mid1=$FieldValue[2];
	$mid2=$FieldValue[3];
	$shodan_id=$FieldValue[1];

	$status = '完了後対応';
	$c_status = '完了';
	$status_sort = '991';

	
	if($FieldValue[34] == '完了'){

		//一括払いの場合
		if($ck_div_id==""){
			$StrSQL="SELECT * FROM DAT_FILESTATUS ";
			$StrSQL.=" WHERE SHODAN_ID='".$shodan_id."' AND MID1='".$mid1."' AND MID2='".$mid2."' ";
			$StrSQL.=" AND DIV_ID='".$div_id."' ";
			$StrSQL.=" AND STATUS='請求' AND S_STATUS='請求（研究者）' ";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			echo "<!--StrSQL1:$StrSQL-->";
			$item_num=mysqli_num_rows($rs);
			if($item_num>=1){
				$StrSQL = "
				INSERT INTO DAT_FILESTATUS (
					SHODAN_ID,
					MID1,
					MID2,

					CATEGORY,
					STATUS,

					S_ADD_CHARGE2,
					DIV_ID,

					S_STATUS,

					NEWDATE,
					EDITDATE
					) VALUE (
					'".$shodan_id."',
					'".$mid1."',
					'".$mid2."',

					'".$c_status."',
					'".$status."',

					'".$FieldValue[90]."',
					'".$div_id."',

					'請求（研究者）',

					'".$date_stmp."',
					'".$date_stmp."'
				)";

				echo "<!--StrSQL2:$StrSQL-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}

				$StrSQL="UPDATE DAT_SHODAN SET STATUS='".$status."', C_STATUS='".$c_status."', STATUS_SORT='".$status_sort."', EDITDATE='".$date_stmp."' WHERE ID='".$shodan_id."'";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}

			}

		//分割払いの場合
		}else{
			$StrSQL="SELECT * FROM DAT_FILESTATUS ";
			$StrSQL.=" WHERE SHODAN_ID='".$shodan_id."' AND MID1='".$mid1."' AND MID2='".$mid2."' ";
			$StrSQL.=" AND DIV_ID='".$ck_div_id."' ";
			$StrSQL.=" AND STATUS='請求' AND S_STATUS='請求（研究者）' ";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			echo "<!--StrSQL1:$StrSQL-->";
			$item_num=mysqli_num_rows($rs);
			if($item_num==0){
				$StrSQL = "
				INSERT INTO DAT_FILESTATUS (
					SHODAN_ID,
					MID1,
					MID2,

					CATEGORY,
					STATUS,

					S_ADD_CHARGE2,
					DIV_ID,

					S_STATUS,

					NEWDATE,
					EDITDATE
					) VALUE (
					'".$shodan_id."',
					'".$mid1."',
					'".$mid2."',

					'".$c_status."',
					'".$status."',

					'".$FieldValue[90]."',
					'".$ck_div_id."',

					'請求（研究者）',

					'".$date_stmp."',
					'".$date_stmp."'
				)";

				echo "<!--StrSQL2:$StrSQL-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}

				$StrSQL="UPDATE DAT_SHODAN_DIV SET STATUS='".$status."', C_STATUS='".$c_status."', EDITDATE='".$date_stmp."' WHERE DIV_ID='".$ck_div_id."'";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}
		}


		// 作成した請求のfilestatusのIDを取得
		$StrSQL="SELECT ID FROM DAT_FILESTATUS where NEWDATE='".$date_stmp."' and STATUS='".$status."' and S_STATUS='請求（研究者）' order by ID desc;";
		//echo('<!--SQL3:'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$s_item= mysqli_fetch_assoc($rs);
		$s_key = $s_item['ID'];
		echo "<!--s_item:";
		var_dump($s_item);
		echo "-->";

		//$file_dir = __dir__ . '/data/';
		//if(!file_exists($file_dir . $s_key . '/')) {
		//	mkdir($file_dir . $s_key, 0777, true);
		//}
		//
		//if($FieldValue[30] != '') {
		//	copy($file_dir . $key . '/' . $FieldValue[30], $file_dir . $s_key . '/' . $FieldValue[30]);
		//}


		//研究者へのメッセージ
		$comment = '追加請求
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact2/?type=完了後対応&mode=disp_frame&key='.$s_key.'\'\');">
			'.$SCNo_str.' Version'.$m2_version.'
		</a>
		';

		// DAT_MESSAGE
		if($comment != '') {
			$aid = $mid1 . '-' . $mid2;
			$StrSQL = "
				INSERT INTO DAT_MESSAGE (
					AID,
					RID,
					ENABLE,
					NEWDATE,
					COMMENT,
					ETC02,
					ETC03,
					ETC04
				) VALUE (
					'".$aid."',
					'".$mid1."',
					'ENABLE:公開中',
					'".$date_stmp."',
					'".$comment."',
					'".$shodan_id."',
					'".$mid2."',
					'".$s_key."'
				)
			";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
		}

		
//		//サプライヤーへのメッセージ
//		$comment = 'Invoice to Scientist From Cosmo Bio.
//		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$s_key.'\'\');">
//			'.$SCNo_str.' Version'.$m2_version.'
//		</a>
//		';
//
//		// DAT_MESSAGE
//		if($comment != '') {
//			$aid = $mid1 . '-' . $mid2;
//			$StrSQL = "
//				INSERT INTO DAT_MESSAGE (
//					AID,
//					RID,
//					ENABLE,
//					NEWDATE,
//					COMMENT,
//					ETC02,
//					ETC03,
//					ETC04
//				) VALUE (
//					'".$aid."',
//					'".$mid2."',
//					'ENABLE:公開中',
//					'".$date_stmp."',
//					'".$comment."',
//					'".$shodan_id."',
//					'".$mid1."',
//					'".$s_key."'
//				)
//			";
//			if (!(mysqli_query(ConnDB(),$StrSQL))) {
//				die;
//			}
//		}




	}
	

}
?>
