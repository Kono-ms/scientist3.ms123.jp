<?php

session_start();
require "../config.php";
require "../base_a.php";
require './config.php';

require("./func_export.php");

define("LABLE_LIST", 
"会員ID::研究者ID::メールアドレス::パスワード::公開フラグ::登録日時::更新日時::PF手数料（％）::契約フラグ::【表示項目】::ニックネーム::氏名::会社名または屋号::部署名::国::郵便番号::住所::TEL::FAX::メールアドレス::システム管理者::アカウント種類::企業ID::会員種別::PRコメント::ホームページURL::都道府県"
);
define("VALUE_LIST", 
"MID::MID_NEW::EMAIL::PASS::ENABLE::NEWDATE::EDITDATE::M2_ETC02::M2_DSEL02::::M2_DVAL01::M2_DVAL02::M2_DVAL03::M2_DVAL04::M2_DVAL05::M2_DVAL06::M2_DVAL07::M2_DVAL08::M2_DVAL09::M2_DVAL10::M2_DVAL11::M2_DVAL12::M2_DVAL13::M2_DVAL14::M2_DTXT01::M2_DTXT02::M2_DSEL01"
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
		$_SESSION['a_m3_expo_ids']="";//CSV出力の選択ID（カンマ区切り）
	}
	if($mode=="expo_chk"){
		$tmps=explode(",",$_SESSION['a_m3_expo_ids']);
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
		$_SESSION['a_m3_expo_ids']=implode(",",$tmps);
		exit;
	}

	//https://scientist3.ms123.jp/a_m3S3/?mode=test1
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
					$StrSQL=" UPDATE DAT_M3 SET ENABLE = '".$enable."'";
					$StrSQL.=" WHERE ID = '".$chk[$i]."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} 
			} else {
				if($chk!=""){
					$StrSQL=" UPDATE DAT_M3 SET ENABLE = '".$enable."'";
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
			break;

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
				$msg=ErrorCheck();
				if ($msg==""){
					SaveData($key);
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
			break;
		case "export":
			ExportData($expo);
			exit;
		case "import":
			ImportData($obj,$a,$b,$key,$mode);
			$mode="list";
			break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$expo);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$expo)
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

	$str=str_replace("[MID_NEW]",convert_MID($FieldValue[1]),$str);

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
				}

				if($CurrentRecord%2==0){
					$str=str_replace("[LIST-BG]","bg01",$str);
				} else {
					$str=str_replace("[LIST-BG]","bg02",$str);
				}

				$tmps=explode(",",$_SESSION['a_m3_expo_ids']);
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

				$tmp="";
		$sel=explode("::", LABLE_LIST);
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
