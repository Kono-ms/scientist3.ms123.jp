<?php

session_start();
require "../config.php";
require "../base_a.php";
require './config.php';

require("./func_export.php");

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
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
	}

	if ($mode==""){
		$mode="list";
	}

	//分割見積り時の同期処理用
	if($key!=""){
		$StrSQL="SELECT * from DAT_FILESTATUS_DETAIL where ID='".$key."'";
		$rs_this=mysqli_query(ConnDB(),$StrSQL);
		$item_this = mysqli_fetch_assoc($rs_this);
		$item_this_num=mysqli_num_rows($rs_this);
		if($item_this_num>0){
			if($item_this["FILESTATUS_ID"]!=""){
				//親が見積り送付で、分割支払いかどうかチェック
				$StrSQL="SELECT * from DAT_FILESTATUS where ID='".$item_this["FILESTATUS_ID"]."' and STATUS='見積り送付' ";
				$StrSQL.=" and (M2_PAY_TYPE='Split' or M2_PAY_TYPE='Milestone') ";
				$rs_sync1=mysqli_query(ConnDB(),$StrSQL);
				//$item_sync1 = mysqli_fetch_assoc($rs_sync1);
				$item_sync_num1=mysqli_num_rows($rs_sync1);

				if($item_sync_num1>0){
					$div_id=$item_this["DIV_ID"];
					$tmp="";
					$tmp=explode("-", $div_id);
		
					if(count($tmp)==3 && $tmp[0]!="" && $tmp[1]!=""){
						$invoice_no=$tmp[0]."-".$tmp[1];
						$StrSQL="SELECT * from DAT_FILESTATUS_DETAIL where DIV_ID LIKE '".$invoice_no."-PART%' ";
						$StrSQL.=" and DIV_ITEM_NO IS NOT NULL ";
						$StrSQL.=" and DIV_ITEM_NO!='' ";
						$StrSQL.=" and DIV_ITEM_NO='".$item_this["DIV_ITEM_NO"]."'";

						//$StrSQL="SELECT * from DAT_FILESTATUS_DETAIL where DIV_ID LIKE '".$invoice_no."-PART%' ";
						//$StrSQL.=" and DIV_ITEM_NO='".$item_this["DIV_ITEM_NO"]."'";
						
						$rs_sync2=mysqli_query(ConnDB(),$StrSQL);
						$item_sync_num2=mysqli_num_rows($rs_sync2);
						$sync_item_ary=array();
						while($item_sync2 = mysqli_fetch_assoc($rs_sync2)){
							//「運営手数料追加」用につくられた行を除く＆ねんのためDIV_IDもチェック
							$StrSQL="SELECT * from DAT_FILESTATUS where ID='".$item_sync2["FILESTATUS_ID"]."' and STATUS='見積り送付' ";
							$StrSQL.=" and DIV_ID LIKE '".$invoice_no."-PART%' ";
							$rs_oya_check=mysqli_query(ConnDB(),$StrSQL);
							$item_oya_check=mysqli_num_rows($rs_oya_check);
							if($item_oya_check<=0){
								continue;
							}

							echo "<!--同期アイテム：".$item_sync2["ID"]."-->";
							$sync_item_ary[]=$item_sync2["ID"];
						}
					}
				}
			}
		}
	}
	

	//$StrSQL="SELECT * from DAT_FILESTATUS where ID='".$key."' and STATUS='見積り送付' ";
	//$StrSQL.=" and (M2_PAY_TYPE='Split' or M2_PAY_TYPE='Milestone') ";
	//$rs_sync1=mysqli_query(ConnDB(),$StrSQL);
	//$item_sync1 = mysqli_fetch_assoc($rs_sync1);
	//$item_sync_num1=mysqli_num_rows($rs_sync1);

	switch ($mode){
		case "new":
			InitData();
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
				//Price（サプライヤー原価の計算：[4]:M2_DETAIL_PRICE）
				//[11]:M2_DETAIL_QUANTITY,[12]:M2_DETAIL_UNIT_PRICE,[17]:M2_DETAIL_SP_DISCOUNT
				$m2_detail_quantity=is_numeric($FieldValue[11]) ? $FieldValue[11] : 0;
				$m2_detail_unit_price=is_numeric($FieldValue[12]) ? $FieldValue[12] : 0;
				$m2_detail_sp_discount=is_numeric($FieldValue[17]) ? $FieldValue[17] : 0;
				if(is_numeric($FieldValue[11]) && is_numeric($FieldValue[12])){
					//$FieldValue[4]=$m2_detail_quantity*$m2_detail_unit_price;
					$FieldValue[4]=$m2_detail_quantity*$m2_detail_unit_price-$m2_detail_sp_discount;
				}

				//TOTAL PRICE(M2_DETAIL_TOTAL_PRICE)の計算
				if(is_numeric($FieldValue[4]) && is_numeric($FieldValue[8])){
					$val=$FieldValue[4]+$FieldValue[8];
					$FieldValue[10]=sprintf('%F',$val);
				}else{
					$FieldValue[10]="";
				}

				$msg=ErrorCheck();
				if ($msg==""){
					SaveData($key,$sync_item_ary);
					$url=BASE_URL . "/a_filestatus_detail/?word=".$word."&page=".$page;
					header("Location: {$url}");
					//debug
					//$mode="list";
					//if ($page==""){
					//	$page=1;
					//} 
				}
			}
			break;
		case "save2":
			//運営から研究者へ「見積り送付」

			// CSRFチェック OKならDB書き込み
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);
				//Price（サプライヤー原価の計算：[4]:M2_DETAIL_PRICE）
				//[11]:M2_DETAIL_QUANTITY,[12]:M2_DETAIL_UNIT_PRICE,[17]:M2_DETAIL_SP_DISCOUNT
				$m2_detail_quantity=is_numeric($FieldValue[11]) ? $FieldValue[11] : 0;
				$m2_detail_unit_price=is_numeric($FieldValue[12]) ? $FieldValue[12] : 0;
				$m2_detail_sp_discount=is_numeric($FieldValue[17]) ? $FieldValue[17] : 0;
				if(is_numeric($FieldValue[11]) && is_numeric($FieldValue[12])){
					//$FieldValue[4]=$m2_detail_quantity*$m2_detail_unit_price;
					$FieldValue[4]=$m2_detail_quantity*$m2_detail_unit_price-$m2_detail_sp_discount;
				}
				
				//TOTAL PRICE(M2_DETAIL_TOTAL_PRICE)の計算
				if(is_numeric($FieldValue[4]) && is_numeric($FieldValue[8])){
					$val=$FieldValue[4]+$FieldValue[8];
					$FieldValue[10]=sprintf('%F',$val);
				}else{
					$FieldValue[10]="";
				}

				$msg=ErrorCheck();
				if ($msg==""){
					SaveData($key,$sync_item_ary);
					SubmitMitsumori($key);
					SendMail_v3($key);
					header("Location: ".BASE_URL."/a_filestatus_detail/");
					
					//debug
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

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$sync_item_ary);

	return $function_ret;
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
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$sync_item_ary)
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
			if ($FieldAtt[$i]==4){
				if ($FieldValue[$i]==""){
					$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
					$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
				} 

				if(strstr($FieldValue[$i],"s.gif") == true){
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

		$str=str_replace("[BASE_URL]",BASE_URL,$str);


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


		//保存＆見積送付（運営手数料追加用）ボタンの表示非表示
		//DAT_FILESTATUSの対応するレコードのSTATUSが「運営手数料追加」の場合のみ表示
		//すでに1回ボタンがおされてたらボタンは表示しない
		//（STATUS:運営手数料追加のレコードにたいして、すでにSTATUS:見積り送付が存在してたら）
		$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".mysqli_real_escape_string(ConnDB(),$key)."' order by ID desc limit 1;";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_tmp=mysqli_fetch_assoc($rs);
		echo "<!--StrSQL:$StrSQL-->";
		$fs_id=$item_tmp["FILESTATUS_ID"];

		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$fs_id."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_tmp=mysqli_fetch_assoc($rs);
		$fs_status=$item_tmp["STATUS"];
		$fs_shodan_id=$item_tmp["SHODAN_ID"];
		$fs_m2_id=$item_tmp["M2_ID"];
		$fs_m2_version=$item_tmp["M2_VERSION"];

		if($fs_status=="運営手数料追加"){
			//「STATUS:運営手数料追加」のレコードにたいして、すでに「STATUS:見積り送付」が存在してるかどうか
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$fs_shodan_id."' and M2_ID='".$fs_m2_id."' and M2_VERSION='".$fs_m2_version."' and STATUS='見積り送付';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_num=mysqli_num_rows($rs);
			echo "<!--StrSQL:$StrSQL-->";
			echo "<!--num:$item_num-->";
			if($item_num>0){
				$str=DispParamNone($str,"UNEI");
			}else{
				$str=DispParam($str,"UNEI");
			}
		}else{
			$str=DispParamNone($str,"UNEI");
		}


		//2回払いorマイルストーン払いで、前払いを選択し、前払い対象のアイテムに「〇」を出力
		$StrSQL="SELECT * from DAT_FILESTATUS where ID = '".$FieldValue[1]."';";
		$rs_parent_fs=mysqli_query(ConnDB(),$StrSQL);
		$parent_fs = mysqli_fetch_assoc($rs_parent_fs);

		$tmp="";
		$part="";
		$tmp=explode("-", $parent_fs["DIV_ID"]);
		if($parent_fs["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
			$part=$tmp[2];
		}
		echo "<!--parents ID:".$parent_fs["ID"]."-->";
		echo "<!--parents DIV_ID:".$parent_fs["DIV_ID"]."-->";
		if($parent_fs["STATUS"]=="見積り送付" && 
			($parent_fs["M2_PAY_TYPE"]=="Split" || $parent_fs["M2_PAY_TYPE"]=="Milestone") && 
			($parent_fs["M_STATUS"]=="直接送付(前払い)" || $parent_fs["M_STATUS"]=="手数料追加(前払い)") && 
			$part=="Part1"){
			$str=str_replace("[MAEBARAI_AT_EDIT]", "〇", $str);
		}else{
			$str=str_replace("[MAEBARAI_AT_EDIT]", "", $str);
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
				$str=str_replace("[D-MID2]",$m2['M2_DVAL01'],$str);

				$StrSQL="SELECT * from DAT_M1 where MID = '".$item['MID1']."';";
				$rs_m2=mysqli_query(ConnDB(),$StrSQL);
				$m2 = mysqli_fetch_assoc($rs_m2);
				$str=str_replace("[D-MID1]",$m2['M1_DVAL01'],$str);

				for ($i=0; $i<=$FieldMax; $i=$i+1) {
					if ($FieldAtt[$i]==4) {
						if ($item[$FieldName[$i]]=="") {
							$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
							$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
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

					
					//2回払いorマイルストーン払いで、前払いを選択し、前払い対象のアイテムに「〇」を出力
					$StrSQL="SELECT * from DAT_FILESTATUS where ID = '".$item["FILESTATUS_ID"]."';";
					$rs_parent_fs=mysqli_query(ConnDB(),$StrSQL);
					$parent_fs = mysqli_fetch_assoc($rs_parent_fs);
					
					$tmp="";
					$part="";
					$tmp=explode("-", $parent_fs["DIV_ID"]);
					if($parent_fs["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
						$part=$tmp[2];
					}
					
					if($parent_fs["STATUS"]=="見積り送付" && 
						($parent_fs["M2_PAY_TYPE"]=="Split" || $parent_fs["M2_PAY_TYPE"]=="Milestone") && 
						($parent_fs["M_STATUS"]=="直接送付(前払い)" || $parent_fs["M_STATUS"]=="手数料追加(前払い)") && 
						$part=="Part1"){
						$str=str_replace("[IS_MAEBARAI]", "〇", $str);
					}else{
						$str=str_replace("[IS_MAEBARAI]", "", $str);
					}

				}

				if(CurrentRecord%2==0){
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
		"M2_DETAIL_ITEM",
		"M2_DETAIL_DESCRIPTION",
		"M2_DETAIL_PRICE",
		"M2_DETAIL_HANDLING_FEE",
		"M2_DETAIL_HANDLING_FEE_MEMO",
		"M2_DETAIL_QUANTITY",
		"M2_DETAIL_UNIT_PRICE",
		"M2_DETAIL_TOTAL_PRICE",
		"M2_DETAIL_NOTE",
		"M2_DETAIL_SP_DISCOUNT"
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

	return $function_ret;
}

//=========================================================================================================
//名前 DB書き込み（運営手数料追加モード時の見積もり送信）
//機能 DBにレコードを保存
//引数  a_filestatus_detailのレコードのID($key)
//戻値 $function_ret
//=========================================================================================================
function SubmitMitsumori($key)
{
	eval(globals());

	echo "<!--at SubmitMitsumori-->";
	//更新基本データ
	$date_stmp=date('Y/m/d H:i:s');
	$status="見積り送付";


	$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".mysqli_real_escape_string(ConnDB(),$key)."' order by ID desc limit 1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemFSD=mysqli_fetch_assoc($rs);
	echo "<!--StrSQL:$StrSQL-->";

	$filestatus_id_original=$itemFSD["FILESTATUS_ID"];

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
			$disp_part="Split ".$part;
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
	

	




//	$new_item_FS=mysqli_fetch_assoc($new_rs_FS);
//
//	$fs_key=$new_item_FS["ID"];
//	$shodan_id=$new_item_FS["SHODAN_ID"];
//	$m2_id=$new_item_FS["M2_ID"];
//	$m2_version=$new_item_FS["M2_VERSION"];
//	$comment = 'Quotationが送付されました
//	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$fs_key.'\'\');">
//	      Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . '）</a>' . 
//	      '　<a href="/m_contact2/?type=再見積り依頼&mode=new&m2_id='.$m2_id.'&m2_version='.$m2_version.'" target="_top">Request a re-quote</a>';
//
//	$mid1=$new_item_FS["MID1"];
//	$mid2=$new_item_FS["MID2"];
//	$aid=$mid1."-".$mid2;
//
//	$StrSQL = "
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
//				'".$mid2."',
//				'".$fs_key."'
//			)
//		";
//	
//	echo "<!--StrSQL(message):$StrSQL-->";
//	if (!(mysqli_query(ConnDB(),$StrSQL))) {
//			die;
//	}

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
