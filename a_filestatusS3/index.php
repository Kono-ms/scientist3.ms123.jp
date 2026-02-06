<?php

session_start();
require "../config.php";
require "../base_a.php";
require './config.php';

require("./func_export.php");
// ini_set( 'display_errors', 1 );


define("LABLE_LIST", 
"商談ID::商談詳細ID::研究者ID::研究者組織名::研究者氏名::サプライヤーID::サプライヤー会社名::Scientist3 Control No.::納品形態::通貨::Item::内容::Quantity::Unit Price::Total Price::Note::小計::税率::消費税::PF手数料::輸入代行費用::輸出代行費用::特別値引き::小計::税率::消費税::合計金額"
);
//"::見積りID::コメント"
//対応するがわからないものは「不明」としています。
define("VALUE_LIST", 
"SHODAN_ID::ID::MID2::M2_DVAL03::M2_DVAL17::MID1::M1_DVAL01::Control_No::M2_NOHIN_TYPE::M2_CURRENCY::M2_DETAIL_ITEM::M2_DETAIL_DESCRIPTION::M2_DETAIL_QUANTITY::M2_DETAIL_UNIT_PRICE::M2_DETAIL_PRICE::M2_DETAIL_NOTE::MITSUMORISYO_SUBTOTAL1::MITSUMORISYO_TAX_RATE1::MITSUMORISYO_TAX_BILL1::MITSUMORISYO_PF_FEE::M2_IMPORT_FEE::MITSUMORISYO_EXPORT_FEE::M2_MANAGE_DISCOUNT::MITSUMORISYO_SUBTOTAL2::M2_TAX_RATE2::MITSUMORISYO_TAX_BILL2::MITSUMORISYO_ALL_CHARGE"
);
//"::H_M2_ID::H_COMMENT"

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

		$date1=$_GET['date1'];
		$date2=$_GET['date2'];
		$price1=$_GET['price1'];
		$price2=$_GET['price2'];
		$customer_name=$_GET['customer_name'];
		$currency=$_GET['currency'];
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

		$date1=$_POST['date1'];
		$date2=$_POST['date2'];
		$price1=$_POST['price1'];
		$price2=$_POST['price2'];
		$customer_name=$_POST['customer_name'];
		$currency=$_POST['currency'];
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
		$_SESSION['a_filestatus_expo_ids']="";//CSV出力の選択ID（カンマ区切り）
	}

	if($mode=="expo_chk"){
		$tmps=explode(",",$_SESSION['a_filestatus_expo_ids']);
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
		$_SESSION['a_filestatus_expo_ids']=implode(",",$tmps);
		exit;
	}



	//https://scientist3.ms123.jp/a_filestatusS3/?mode=test1
	if ($mode=="test1"){
		$fp=$DOCUMENT_ROOT."edit.html";
		$str=@file_get_contents($fp);

		// trタグを検出
		$set = '/<tr>(.*?)<\/tr>/is';
		preg_match_all($set, $str, $matches, PREG_SET_ORDER);
		foreach ($matches as $tags) {
			$tr_tag      = $tags[0];
			// echo $tr_tag."<br>";

			$val1="";
			if(mb_strpos($tr_tag, "[")!==false){
				$a=mb_strpos($tr_tag, "[");
				$b=mb_strpos($tr_tag, "]");
				// echo $a."<br>";
				// echo $b."<br>";
				$val1=mb_substr($tr_tag, $a+1,$b-$a-1);
				$val1=str_replace("OPT-","",$val1);
				
			}


			$lbl1="";
			if(mb_strpos($tr_tag, "<!")!==false){
				$a=mb_strpos($tr_tag, "<!");
				// echo "a1:".$a."<br>";
				$lbl1=mb_substr($tr_tag, 0,$a);
			} else
			if(mb_strpos($tr_tag, "<input")!==false){
				$a=mb_strpos($tr_tag, "<input");
				// echo "a1:".$a."<br>";
				$lbl1=mb_substr($tr_tag, 0,$a);
			} else
			if(mb_strpos($tr_tag, "<select")!==false){
				$a=mb_strpos($tr_tag, "<select");
				// echo "a1:".$a."<br>";
				$lbl1=mb_substr($tr_tag, 0,$a);
			} else
			if(mb_strpos($tr_tag, "<textarea")!==false){
				$a=mb_strpos($tr_tag, "<textarea");
				// echo "a1:".$a."<br>";
				$lbl1=mb_substr($tr_tag, 0,$a);
			} else
			if(mb_strpos($tr_tag, "<img")!==false){
				$a=mb_strpos($tr_tag, "<img");
				// echo "a1:".$a."<br>";
				$lbl1=mb_substr($tr_tag, 0,$a);
			} else
			if(mb_strpos($tr_tag, "[OPT-")!==false){
				$a=mb_strpos($tr_tag, "[OPT-");
				// echo "a1:".$a."<br>";
				$lbl1=mb_substr($tr_tag, 0,$a);
			} else
			if(mb_strpos($tr_tag, "[")!==false){
				$a=mb_strpos($tr_tag, "[");
				// echo "a2:".$a."<br>";
				$lbl1=mb_substr($tr_tag, 0,$a);
			} 
			$lbl1=str_replace("<ul>","",$lbl1);
			$lbl1=str_replace("</ul>","",$lbl1);
			$lbl1=strip_tags($lbl1);
			$val1=strip_tags($val1);
			$lbl1=str_replace("削除","",$lbl1);
			$lbl1=str_replace(" ","",$lbl1);
			$val1=str_replace("削除","",$val1);

			echo $lbl1."　".$val1."<br>";
			// echo "lbl1:".$lbl1."\t".$val1."<br>";

		}
		exit;

		
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
				$msg=ErrorCheck();
				if ($msg==""){
					SaveData($key,$sync_item_ary);
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
		case "list1":
		case "list2":
			if ($page==""){
				$page=1;
			} 
			if($mode=="list1"){
				$kbn="1";
			}
			if($mode=="list2"){
				$kbn="2";
			}
			$mode="list";
			break;
		case "export1":
			ExportData("1",$expo,$date1,$date2,$price1,$price2,$currency,$customer_name);

			exit;
		case "export2":
			ExportData("2",$expo,$date1,$date2,$price1,$price2,$currency,$customer_name);

			exit;
		case "import":
			ImportData($obj,$a,$b,$key,$mode);
			$mode="list";
			break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$sync_item_ary,$expo,$kbn,$date1,$date2,$price1,$price2,$currency,$customer_name);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$sync_item_ary,$expo,$kbn,$date1,$date2,$price1,$price2,$currency,$customer_name)
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
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
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
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
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
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					break;
				case '見積り送付':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParam($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					break;
				case '発注依頼':
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
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					break;
				case 'データ納品':
				case '物品納品':
				//case '納品確認':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParam($str, "FORM_N");
					$str=DispParamNone($str, "FORM_S1");
					$str=DispParamNone($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					break;
				case '納品確認':
					//echo "<!--FieldValue[54]:".$FieldValue[54]."-->";
					//echo "<!--checkDIV_ID:".checkDIV_ID($FieldValue[54])."-->";
					$ck_div_id=checkDIV_ID($FieldValue[54]);
					//一括払い
					if($ck_div_id==""){
						$StrSQL="SELECT * FROM DAT_FILESTATUS ";
						$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND STATUS='請求'";
						$rs=mysqli_query(ConnDB(),$StrSQL);
						echo "<!--StrSQL3:$StrSQL-->";
						$item_num=mysqli_num_rows($rs);

					//分割払い
					}else{
						$StrSQL="SELECT * FROM DAT_FILESTATUS ";
						$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND STATUS='請求' ";
						$StrSQL.=" AND DIV_ID='".$ck_div_id."'";
						$rs=mysqli_query(ConnDB(),$StrSQL);
						echo "<!--StrSQL3:$StrSQL-->";
						$item_num=mysqli_num_rows($rs);
					}
					
					if($item_num==0){
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParam($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParam($str, "FORM_S2");
						$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					}else{
						$str=DispParamNone($str, "FORM_T");
						$str=DispParamNone($str, "FORM_M1");
						$str=DispParamNone($str, "FORM_M1B");
						$str=DispParamNone($str, "FORM_M2");
						$str=DispParamNone($str, "FORM_H");
						$str=DispParam($str, "FORM_N");
						$str=DispParamNone($str, "FORM_S1");
						$str=DispParamNone($str, "FORM_S2");
						$str=DispParam($str, "FORM_S2-SEKYUZUMI");
					}
					break;
				case '請求':
					$str=DispParamNone($str, "FORM_T");
					$str=DispParamNone($str, "FORM_M1");
					$str=DispParamNone($str, "FORM_M1B");
					$str=DispParamNone($str, "FORM_M2");
					$str=DispParamNone($str, "FORM_H");
					$str=DispParamNone($str, "FORM_N");
					$str=DispParam($str, "FORM_S1");
					$str=DispParam($str, "FORM_S2");
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
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
					$str=DispParamNone($str, "FORM_S2-SEKYUZUMI");
					break;
			}
		}


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
		if(count($tmp)==3){
			$part=$tmp[2];
		}
		echo "<!--part:$part-->";

		if($FieldValue[34]=="見積り送付" && 
			($FieldValue[16]=="Once" || 
			($FieldValue[16]=="Split" && $part=="Part0") || 
			($FieldValue[16]=="Milestone" && $part=="Part0") ) ){

			$str=DispParam($str, "FORM_M2_SUB");

			//小計1
			//=該当分割分のアイテムのM2_DETAIL_PRICEの合計-M2_SPECIAL_DISCOUNT
			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$FieldValue[0]."' order by NEWDATE";
			//echo "<!--SQL:".$StrSQL."-->";
			$rs_detail=mysqli_query(ConnDB(),$StrSQL);
			$syoke1=0;
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

				$syoke1=$syoke1+$m2_detail_price+$m2_detail_handling_fee;
				echo "<!--m2_detail_handling_fee:$m2_detail_handling_fee-->";
				echo "<!--M2_DETAIL_PRICE:".$item_detail["M2_DETAIL_PRICE"]."-->";
			}
			if(is_numeric($FieldValue[22])){
				$m2_special_discount=$FieldValue[22];
			}else{
				$m2_special_discount=0;
			}
			
			$syoke1=$syoke1-$m2_special_discount;
			echo "<!--M2_SPECIAL_DISCOUNT:$m2_special_discount-->";
			echo "<!--syoke1:$syoke1-->";
			$str=str_replace("[MITSUMORISYO_SUBTOTAL1]", $syoke1, $str);


			//税率1
			//国内サプライヤー10%,海外サプライヤー0%。
			$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$FieldValue[2]."';";
			$rsM1=mysqli_query(ConnDB(),$StrSQL);
			$itemM1 = mysqli_fetch_assoc($rsM1);
			$tax_rate1=0;
			if($itemM1["M1_DVAL04"]=="M1_DVAL04:Japan"){
				$tax_rate1=10;
			}else{
				$tax_rate1=0;
				
			}
			$str=str_replace("[MITSUMORISYO_TAX_RATE1]",$tax_rate1,$str);


			//消費税
			$tax_bill1=$tax_rate1*$syoke1/100;
			$str=str_replace("[MITSUMORISYO_TAX_BILL1]",$tax_bill1,$str);
			

			//PF手数料
			//研究者管理で入力したPF手数料率/100を使用
			$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$FieldValue[3]."';";
			$rsM2=mysqli_query(ConnDB(),$StrSQL);
			$itemM2 = mysqli_fetch_assoc($rsM2);
			if(is_numeric($itemM2["M2_ETC02"])){
				$pf_fee=$syoke1*$itemM2["M2_ETC02"]/100;
			}else{
				$pf_fee=0;
			}
			$str=str_replace("[MITSUMORISYO_PF_FEE]",$pf_fee,$str);


			//輸入代行費用
			//M_STATUS
			if($FieldValue[61]=="直接送付" ||
				$FieldValue[61]=="直接送付(前払い)"){
				$import_fee=0;
				$str_import_fee=$import_fee.'<input type="hidden" name="M2_IMPORT_FEE" value="'.$import_fee.'">';

			}else{
				if(is_numeric($FieldValue[62])){
					$import_fee=$FieldValue[62];
				}else{
					$import_fee=0;
				}
				$str_import_fee='<input type="text" name="M2_IMPORT_FEE" class="input_w10 form-control" 
				value="'.$import_fee.'" size="90">';

			}
			$str=str_replace("[INPUT-M2_IMPORT_FEE]",$str_import_fee,$str);

			//if($FieldValue[61]=="直接送付" ||
			//	$FieldValue[61]=="直接送付(前払い)"){
			//	$import_fee=0;
			//	$str=str_replace("[INPUT-M2_IMPORT_FEE]",$import_fee,$str);
			//	
			//}else{
			//	$import_fee=$FieldValue[62];
			//}
			

			//輸出代行費用
			//No1269で実装する値を使用（更新日時などの条件あり）
			//上記のページがまだないため現在仮の値を使用
			$export_fee=0;
			$str=str_replace("[MITSUMORISYO_EXPORT_FEE]",$export_fee,$str);


			//特別値引き（運営）
			if($FieldValue[61]=="直接送付" ||
				$FieldValue[61]=="直接送付(前払い)"){
				$mng_discount=0;
				$str_mng_discount=$mng_discount.'<input type="hidden" name="M2_MANAGE_DISCOUNT" value="'.$mng_discount.'">';

			}else{
				$mng_discount=$FieldValue[63];
				$str_mng_discount='<input type="text" name="M2_MANAGE_DISCOUNT" class="input_w10 form-control" 
				value="'.$mng_discount.'" size="90">';

			}
			$str=str_replace("[INPUT-M2_MANAGE_DISCOUNT]",$str_mng_discount,$str);


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
			$all_charge=$syoke1+$tax_bill1+$syoke2+$tax_bill2;
			echo "<!--all_charge:$all_charge=$syoke1+$tax_bill1+$syoke2+$tax_bill2-->";
			$str=str_replace("[MITSUMORISYO_ALL_CHARGE]",$all_charge,$str);


		}else{
			$str=DispParamNone($str, "FORM_M2_SUB");

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
		if($FieldValue[34]=="見積り送付"){
			$ship_to=$FieldValue[55].", ".$FieldValue[56].", ".$FieldValue[57];
			$ship_to.=" ".$FieldValue[58].", ".$FieldValue[59].", ".$FieldValue[60];
			
			$bill_to=$FieldValue[65].", ".$FieldValue[66].", ".$FieldValue[67];
			$bill_to.=" ".$FieldValue[68].", ".$FieldValue[69].", ".$FieldValue[70];

			$str=str_replace("[VIEW-SHIP_TO]",$ship_to,$str);
			$str=str_replace("[VIEW-BILL_TO]",$bill_to,$str);
		}


		//a_filestatus_detailの情報表示エリア
		$tpl_fd=file_get_contents("f_detail.html");

		$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$key."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		//echo "<!--StrSQL:".$StrSQL."-->";
		//echo "<!--key:".$key."-->";
		$output="";
		while ($item = mysqli_fetch_assoc($rs)) {
				$output.=$tpl_fd;
				foreach ($item as $fkey => $val) {
					if($fkey=="M_STATUS"){
						$output=str_replace("[".$fkey."]", str_replace("M_STATUS:","",$val), $output);
					}
					$output=str_replace("[".$fkey."]", $val, $output);
				}
		}
		if(trim($output)==""){
			$str=str_replace("[AREA-FDETAIL]","データがありません",$str);
		}
		$str=str_replace("[AREA-FDETAIL]",$output,$str);



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
						$each_output=DispParamNone($each_output, "FORM_DEFAULT");
						break;
						case '見積り送付':
						$each_output=DispParamNone($each_output, "FORM_T");
						$each_output=DispParamNone($each_output, "FORM_M1");
						$each_output=DispParamNone($each_output, "FORM_M1B");
						$each_output=DispParam($each_output, "FORM_M2");
						$each_output=DispParamNone($each_output, "FORM_H");
						$each_output=DispParamNone($each_output, "FORM_N");
						$each_output=DispParamNone($each_output, "FORM_S1");
						$each_output=DispParamNone($each_output, "FORM_S2");
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

		$StrSQL.=" LEFT JOIN ";
		$StrSQL.=" (SELECT FILESTATUS_ID ";
		$StrSQL.=" ,ifnull(SUM(M2_DETAIL_HANDLING_FEE),0) as M2_DETAIL_HANDLING_FEE ";
		$StrSQL.=" ,ifnull(SUM(M2_DETAIL_PRICE),0) as M2_DETAIL_PRICE ";
		$StrSQL.=" FROM DAT_FILESTATUS_DETAIL";
		$StrSQL.=" GROUP BY FILESTATUS_ID ) as FILESTATUS_DETAIL";
		if($kbn=="1"){
			$StrSQL.=" ON DAT_FILESTATUS.ID = FILESTATUS_DETAIL.FILESTATUS_ID";
		} else {
			$StrSQL.=" ON DAT_FILESTATUS.H_M2_ID = FILESTATUS_DETAIL.FILESTATUS_ID";
		}
		
		$StrSQL.=" LEFT JOIN DAT_FILESTATUS_DETAIL ";
		if($kbn=="1"){
			$StrSQL.=" ON DAT_FILESTATUS.ID = DAT_FILESTATUS_DETAIL.FILESTATUS_ID";
		} else {
			$StrSQL.=" ON DAT_FILESTATUS.H_M2_ID = DAT_FILESTATUS_DETAIL.FILESTATUS_ID";
		}
		//ダミーでMITSUMORISYO_EXPORT_FEEを用意しています、後で調整してください。※No1269で実装する値を使用（更新日時などの条件あり）
		$StrSQL.=" LEFT JOIN (SELECT 0 as MITSUMORISYO_EXPORT_FEE ) as DAT_MITSUMORISYO_EXPORT_FEE ON 1=1";

		$StrSQL.=" LEFT JOIN DAT_M1 ON DAT_FILESTATUS.MID1=DAT_M1.MID ";
		$StrSQL.=" LEFT JOIN DAT_M2 ON DAT_FILESTATUS.MID2=DAT_M2.MID ".ListSql(mysqli_real_escape_string(ConnDB(),$sort),mysqli_real_escape_string(ConnDB(),$word),$kbn,$date1,$date2,$price1,$price2,$currency,$customer_name).";";
echo "<!--list:".$StrSQL."-->";
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

				$tmps=explode(",",$_SESSION['a_filestatus_expo_ids']);
				if (in_array($item["ID"], $tmps)===true) {
					$str=str_replace("[expo_checked]","checked",$str);
				}
				$str=str_replace("[expo_checked]","",$str);

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			} 
		} 


		$str=$strU.$strMain.$strD;

		$str = MakeHTML($str,1,$lid);

		// $tmp="";
		// $sel=explode("::", LABLE_LIST);
		// for($i=0; $i<count($sel); $i++){
		// 	$checked="";
		// 	if(strpos($expo,$sel[$i])!==false){
		// 		$checked=" checked ";
		// 	}
		// 	$tmp.="<li><input ".$checked." id=\""."expo".$i."\" type=\"checkbox\" name=\""."expo[]\" value=\""."expo".":".$sel[$i]."\"><label for=\""."expo".$i."\">".$sel[$i]."</label></li>";
		// }
		// $str=str_replace("[OPT-EXP1]",$tmp,$str);

		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);
		$str=str_replace("[date1]",$date1,$str);
		$str=str_replace("[date2]",$date2,$str);
		$str=str_replace("[price1]",$price1,$str);
		$str=str_replace("[price2]",$price2,$str);
		$str=str_replace("[Currency]",$currency,$str);

		$str=str_replace("[customer_name]",$customer_name,$str);

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


		$tmp="<option value=''>▼選択して下さい</option>";
		$sel=explode("::", "JPY::USD::EUR::GBP");
		for($i=0; $i<count($sel); $i++){
			$lbl=$sel[$i];
			$val="M2_CURRENCY:".$sel[$i];
			$selected="";
			if($currency==$val){
				$selected=" selected ";
			}
			$tmp.="<option ".$selected." value='".$val."'>".$lbl."</option>";
		}
		$str=str_replace("[OPT-CURRENCY]",$tmp,$str);

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
function SaveData($key,$sync_item_ary)
{
	eval(globals());

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

	$date_stmp=date('Y/m/d H:i:s');
	if($FieldValue[30] != '') {
		//【$FieldValue[34]が「請求」の時に、＜請求書（研究者）＞（S2_FILE入力箇所）が表示されて値が入力された場合】
		if($FieldValue[34] == '請求'){
			//一括払いの場合
			if($ck_div_id==""){
				$StrSQL="UPDATE DAT_SHODAN SET STATUS='請求', C_STATUS='請求', STATUS_SORT='9' WHERE ID='".$FieldValue[1]."'";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}

			//分割払いの場合
			}else{
				$StrSQL="UPDATE DAT_SHODAN_DIV SET STATUS='請求', C_STATUS='請求' WHERE DIV_ID='".$ck_div_id."'";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}

		//【$FieldValue[34]が「納品確認」の時に、が表示されて値が入力された場合。】
		//今開いてるSTATUSが納品確認のデータで、S2_FILEに値が入ってる場合は、
		//DAT_FILESTATUSのSTATUSが請求になってるレコードがあるか探して、なかったら、新規データを作り、かつ今開いてるデータを更新
		//（DAT_FILESTATUSのSTATUSは更新しない）。
		//あったら、＜請求書（研究者）＞（S2_FILE入力箇所）を表示のみにする制御を別の場所で行う。
		}else if($FieldValue[34] == '納品確認'){

			//一括払いの場合
			if($ck_div_id==""){
				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND STATUS='請求'";
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

						NEWDATE,
						EDITDATE
						) VALUE (
						'".$FieldValue[1]."',
						'".$FieldValue[2]."',
						'".$FieldValue[3]."',

						'請求',
						'請求',

						'".$FieldValue[30]."',
						'".$FieldValue[31]."',

						'".$date_stmp."',
						'".$date_stmp."'
					)";

					echo "<!--StrSQL2:$StrSQL-->";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}

					$StrSQL="UPDATE DAT_SHODAN SET STATUS='請求', C_STATUS='請求', STATUS_SORT='9' WHERE ID='".$FieldValue[1]."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				}

			//分割払いの場合
			}else{
				$StrSQL="SELECT * FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$FieldValue[1]."' AND MID1='".$FieldValue[2]."' AND MID2='".$FieldValue[3]."' AND STATUS='請求' ";
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

						NEWDATE,
						EDITDATE
						) VALUE (
						'".$FieldValue[1]."',
						'".$FieldValue[2]."',
						'".$FieldValue[3]."',

						'請求',
						'請求',

						'".$FieldValue[30]."',
						'".$FieldValue[31]."',
						'".$ck_div_id."',

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

			
		

		}
	}





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
			$StrSQL.="'".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";
		}
		$StrSQL=$StrSQL.")";
	} else {
		$StrSQL="UPDATE ".$TableName." SET ";
		for ($i=1; $i<=$FieldMax; $i++) {
			if($i>1){
				$StrSQL.=",";
			}
			$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";
		}
		$StrSQL=$StrSQL." WHERE ".$FieldName[$FieldKey]."='".$key."'";
	} 
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}


	//分割見積り時の同期処理用
	$sync_column=array(
		"M2_STUDY_CODE",
		"M2_DATE",
		"M2_QUOTE_VALID_UNTIL",
		"M2_CURRENCY",
		"M2_SPECIAL_DISCOUNT",
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
					$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";
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
	

	// filestatusのIDを取得
	$StrSQL="SELECT ID FROM DAT_FILESTATUS order by ID desc;";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus = mysqli_fetch_assoc($rs);
	$key = $item_filestatus['ID'];

	// ファイルを移動
	$file_dir = __dir__ . '/../a_filestatus/data/';
	if(!file_exists($file_dir . $key . '/')) {
		mkdir($file_dir . $key, 0777, true);
	}
	if($_FILES['EP_FILE']['name'] != '') {
		copy($file_dir . $_FILES['EP_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_FILE']['name']);
	}
	if($_FILES['EP_M1_FILE']['name'] != '') {
		copy($file_dir . $_FILES['EP_M1_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_M1_FILE']['name']);
	}
	if($_FILES['EP_N_FILE']['name'] != '') {
		copy($file_dir . $_FILES['EP_N_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_N_FILE']['name']);
	}
	if($_FILES['EP_S_FILE']['name'] != '') {
		copy($file_dir . $_FILES['EP_S_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_S_FILE']['name']);
	}
	if($_FILES['EP_S2_FILE']['name'] != '') {
		copy($file_dir . $_FILES['EP_S2_FILE']['name'], $file_dir . $key . '/' . $_FILES['EP_S2_FILE']['name']);
	}

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
