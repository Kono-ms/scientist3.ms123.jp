<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_blog2/config.php';

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

//	$word=$_SESSION['MID'];

	if ($_SESSION['MID'] == "") {
		$url = BASE_URL . "/login2/";
		header("Location: {$url}");
		exit;
	}

	switch ($mode){
		case "new":
			InitData();
//			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "edit":
			LoadData($key);
			RequestData($obj,$a,$b,$key,$mode);
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
				SaveData($key);

// 要調整
/*
				$StrSQL="DELETE FROM DAT_MATCH where HID='".$FieldValue[2]."';";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
				$tmp="";
				$StrSQL="SELECT MID FROM DAT_MEMBER where KINMUCHI<>'' order by MID;";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				while ($item = mysqli_fetch_assoc($rs)) {
					if($tmp!=""){
						$tmp.="::";
					}
					$tmp.=$item['MID'];
				}
				$mids=explode("::", $tmp);

				$tmp="";
				$StrSQL="SELECT JID FROM DAT_JOB where HID='".$FieldValue[2]."' order by JID;";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				while ($item = mysqli_fetch_assoc($rs)) {
					if($tmp!=""){
						$tmp.="::";
					}
					$tmp.=$item['JID'];
				}
				$jids=explode("::", $tmp);

				for($i=0; $i<count($mids); $i++){
					for($j=0; $j<count($jids); $j++){
						CulcMatching($mids[$i], $jids[$j], 0);
					}
				}
*/
// 要調整
				$mode="list";
				if ($page==""){
					$page=1;
				}

				$_SESSION['token']="";
			}
			break;
		case "disable":
			LoadData($key);
			$FieldValue[93]="ENABLE:非公開";
			SaveData($key);
			$mode="list";
			if ($page==""){
				$page=1;
			}
			break;
		case "enable":
			LoadData($key);
			$FieldValue[93]="ENABLE:公開中";
			SaveData($key);
			$mode="list";
			if ($page==""){
				$page=1;
			}
			break;
		case "delete":
			RequestData($obj,$a,$b,$key,$mode);
			DeleteData($key);

// 要調整
/*
			$StrSQL="DELETE FROM DAT_MATCH where JID='".$FieldValue[1]."';";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
*/
// 要調整

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
	}

	DispData($mode,$sort,$word,$key,$page,$lid,$token);

	return $function_ret;
}

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token)
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
				$filename=$htmlend;
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

		$fp=$DOCUMENT_ROOT.$filename;
		$str=@file_get_contents($fp);

		$str = MakeHTML($str,1,$lid);

		$StrSQL="SELECT * FROM ".$TableName." where ENABLE='ENABLE:公開中' and ID='".$key."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$str=DispO2($item, $str);
		$word=$item['MID'];

		$StrSQL = "SELECT * FROM DAT_O2 where MID='" . $word . "';";
		$rs = mysqli_query(ConnDB(), $StrSQL);
		$item = mysqli_fetch_assoc($rs);

		// 2021.02.16 yamamoto データごとにだけマークを付ける
		// (画像パスにマークしないため)
		// タグエスケープ回避のためここではタグにしない
		$StrSQL="SELECT * FROM DAT_MATCH where MID1='".$_SESSION['MID']."' and OID2='".$item["OID"]."';";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item_mark = mysqli_fetch_assoc($rs2);
		$tmp=$item_mark['VAL_O1'];
		$t=explode("::", $tmp."::");
		foreach($item as $key => $val) {
			if(strpos($item[$key], '.jpeg') !== false || strpos($item[$key], '.jpg') !== false || strpos($item[$key], '.jpe') !== false || strpos($item[$key], '.gif') !== false || strpos($item[$key], '.png') !== false || strpos($item[$key], '.bmp') !== false) {
				continue;
			}
			for($i=0; $i<count($t); $i++){
				if($t[$i]!=""){
					$item[$key]=str_replace($t[$i],"[mark1]".$t[$i]."[mark2]",$item[$key]);
				}
			}
		}

		$str = DispO2($item, $str);
		$str = DispPoint2($item['OID'], $str);
		$StrSQL = "SELECT * FROM DAT_M2 where MID='" . $item['MID'] . "'";
		$rs2 = mysqli_query(ConnDB(), $StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);

		// 2021.02.16 yamamoto データごとにだけマークを付ける
		// (画像パスにマークしないため)
		// タグエスケープ回避のためここではタグにしない
		foreach($item2 as $key => $val) {
			if(strpos($item2[$key], '.jpeg') !== false || strpos($item2[$key], '.jpg') !== false || strpos($item2[$key], '.jpe') !== false || strpos($item2[$key], '.gif') !== false || strpos($item2[$key], '.png') !== false || strpos($item2[$key], '.bmp') !== false) {
				continue;
			}
			for($i=0; $i<count($t); $i++){
				if($t[$i]!=""){
					$item2[$key]=str_replace($t[$i],"[mark1]".$t[$i]."[mark2]",$item2[$key]);
				}
			}
		}

		$str = DispM2($item2, $str);

/*
		$StrSQL = "SELECT * FROM DAT_O2 where MID='" . $word . "';";
		$rs = mysqli_query(ConnDB(), $StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$StrSQL = "SELECT * FROM DAT_MATCH where MID1='" . $_SESSION['MID'] . "' and OID2='" . $item["OID"] . "';";
		$rs2 = mysqli_query(ConnDB(), $StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);
		$tmp = $item2['VAL_O1'];
		$t = explode("::", $tmp . "::");
		for ($i = 0; $i < count($t); $i++) {
			if ($t[$i] != "") {
				$str = str_replace($t[$i], "<mark>" . $t[$i] . "</mark>", $str);
			}
		}
*/
		// 2021.02.16 yamamoto ここでタグにする
		$str=str_replace("[mark1]","<mark>",$str);
		$str=str_replace("[mark2]","</mark>",$str);
		// 2021.02.16 yamamoto titleがマークされるのを防止
		preg_match('/<title>(.*?)<\/title>/', $str, $match);
		$title = str_replace('<mark>', '', $match[0]);
		$title = str_replace('</mark>', '', $title);
		$str = preg_replace('/<title>(.*?)<\/title>/', $title, $str);

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
			$strD=$strD.$line.chr(13);
		}
		fclose($tso);

		// SQLインジェクション対策
        	$reccount=0;

		$StrSQL="SELECT * FROM ".$TableName." where ENABLE='ENABLE:公開中' and MID='".$word."' order by ID desc;";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item=="") {
			$pagestr="";
			$strMain="<tr class=tableset__list><td align=center colspan=7>対象データがありません。</td></tr>";
		} else {
			//================================================================================================
			//ページング処理
			//================================================================================================
			$reccount=mysqli_num_rows($rs);
			$pagecount=intval(($reccount-1)/$PageSize+1);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$str="";
			if (intval($page)==1) {
				$str=$str."対象件数(".$reccount."件)　　&lt;前の".$PageSize."件&gt;";
			} else {
				$str=$str."対象件数(".$reccount."件)　　&lt;<a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page-1)."\">前の".$PageSize."件</a>&gt;";
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
					$str=$str." <b>".$i."</b>";
				} else {
					$str=$str." <a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\">".$i."</a>";
				}
			}
			if (intval($page)<$pagecount) {
				$str=$str." &lt;<a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page+1)."\">次の".$PageSize."件</a>&gt;";
			} else {
				$str=$str." &lt;次の".$PageSize."件&gt;";
			}

			$pagestr=$str;
			$CurrentRecord=1;
			$strMain="";
			while ($item = mysqli_fetch_assoc($rs)) {

				$str=$strM;

				$str=DispO2($item, $str);

				if($item['ENABLE']=="ENABLE:公開中"){
					$str=DispParam($str, "BDISABLE");
					$str=DispParamNone($str, "BENABLE");
				} else {
					$str=DispParamNone($str, "BDISABLE");
					$str=DispParam($str, "BENABLE");
				}
				$str=str_replace("[O2_DTXT01]", mb_substr($item['O2_DTXT01'],0,60,"UTF-8"), $str);

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			}
		}


		$str=$strU.$strMain.$strD;


		$StrSQL = "SELECT * FROM DAT_O2 where MID='" . $word . "';";
		$rs = mysqli_query(ConnDB(), $StrSQL);
		$item = mysqli_fetch_assoc($rs);

		// 2021.02.16 yamamoto データごとにだけマークを付ける
		// (画像パスにマークしないため)
		// タグエスケープ回避のためここではタグにしない
		$StrSQL="SELECT * FROM DAT_MATCH where MID1='".$_SESSION['MID']."' and OID2='".$item["OID"]."';";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item_mark = mysqli_fetch_assoc($rs2);
		$tmp=$item_mark['VAL_O1'];
		$t=explode("::", $tmp."::");
		foreach($item as $key => $val) {
			if(strpos($item[$key], '.jpeg') !== false || strpos($item[$key], '.jpg') !== false || strpos($item[$key], '.jpe') !== false || strpos($item[$key], '.gif') !== false || strpos($item[$key], '.png') !== false || strpos($item[$key], '.bmp') !== false) {
				continue;
			}
			for($i=0; $i<count($t); $i++){
				if($t[$i]!=""){
					$item[$key]=str_replace($t[$i],"[mark1]".$t[$i]."[mark2]",$item[$key]);
				}
			}
		}

		$str = DispO2($item, $str);
		$str = DispPoint2($item['OID'], $str);
		$StrSQL = "SELECT * FROM DAT_M2 where MID='" . $item['MID'] . "'";
		$rs2 = mysqli_query(ConnDB(), $StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);

		// 2021.02.16 yamamoto データごとにだけマークを付ける
		// (画像パスにマークしないため)
		// タグエスケープ回避のためここではタグにしない
		foreach($item2 as $key => $val) {
			if(strpos($item2[$key], '.jpeg') !== false || strpos($item2[$key], '.jpg') !== false || strpos($item2[$key], '.jpe') !== false || strpos($item2[$key], '.gif') !== false || strpos($item2[$key], '.png') !== false || strpos($item2[$key], '.bmp') !== false) {
				continue;
			}
			for($i=0; $i<count($t); $i++){
				if($t[$i]!=""){
					$item2[$key]=str_replace($t[$i],"[mark1]".$t[$i]."[mark2]",$item2[$key]);
				}
			}
		}

		$str = DispM2($item2, $str);

/*
		$StrSQL = "SELECT * FROM DAT_O2 where MID='" . $word . "';";
		$rs = mysqli_query(ConnDB(), $StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$StrSQL = "SELECT * FROM DAT_MATCH where MID1='" . $_SESSION['MID'] . "' and OID2='" . $item["OID"] . "';";
		$rs2 = mysqli_query(ConnDB(), $StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);
		$tmp = $item2['VAL_O1'];
		$t = explode("::", $tmp . "::");
		for ($i = 0; $i < count($t); $i++) {
			if ($t[$i] != "") {
				$str = str_replace($t[$i], "<mark>" . $t[$i] . "</mark>", $str);
			}
		}
*/
		// 2021.02.16 yamamoto ここでタグにする
		$str=str_replace("[mark1]","<mark>",$str);
		$str=str_replace("[mark2]","</mark>",$str);
		// 2021.02.16 yamamoto titleがマークされるのを防止
		preg_match('/<title>(.*?)<\/title>/', $str, $match);
		$title = str_replace('<mark>', '', $match[0]);
		$title = str_replace('</mark>', '', $title);
		$str = preg_replace('/<title>(.*?)<\/title>/', $title, $str);

		$str = MakeHTML($str,1,$lid);

		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[RECCOUNT]",$reccount,$str);
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
			if (isset($_POST[$FieldName[$i]])) {
				if (strstr($_POST[$FieldName[$i]], "\t") == true) {
					$FieldValue[$i] = htmlspecialchars($_POST[$FieldName[$i]]);
				} else {
					$FieldValue[$i] = "";
					for ($j = 0; $j < count($_POST[$FieldName[$i]]); $j = $j + 1) {
						if ($j != 0) {
							$FieldValue[$i] = $FieldValue[$i] . "\t";
						}
						$FieldValue[$i] = $FieldValue[$i] . $_POST[$FieldName[$i]][$j];
					}
				}
			}
		} else {
//			$FieldValue[$i]=htmlspecialchars(str_replace("\\","",$_POST[$FieldName[$i]]));
			if (isset($_POST[$FieldName[$i]])) {
				$FieldValue[$i]=htmlspecialchars(str_replace("\\","",$_POST[$FieldName[$i]]));
			}
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
				move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], "../a_o1/data/".$filename);
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
		$StrSQL="SELECT O1_OID from DAT_O1 where O1_MID='".$_SESSION['MID']."' order by O1_OID desc limit 0,1;";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$FieldValue[1]=$_SESSION['MID']."-".sprintf("%02d", str_replace($_SESSION['MID']."-", "", $item['O1_OID'])+1);
		$FieldValue[2]=$_SESSION['MID'];
		$FieldValue[93]="ENABLE:公開中";
		$FieldValue[94]=date("Y/m/d H:i:s");
		$FieldValue[95]=date("Y/m/d H:i:s");

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
		$FieldValue[95]=date("Y/m/d H:i:s");

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
