<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require './config.php';

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

	if ($_SESSION['MID'] == "") {
		$url = BASE_URL . "/login2/";
		header("Location: {$url}");
		exit;
	}
	
	if ($_POST['mode'] == "") {
		$mode = $_GET['mode'];
		$sort = $_GET['sort'];
		$word = $_GET['word'];
		$key = $_GET['key'];
		$page = $_GET['page'];
		$token = $_GET['token'];
		$lid = $_GET['lid'];
		$mid1 = $_GET['mid1'];
		$mid2 = $_GET['mid2'];
	} else {
		$mode = $_POST['mode'];
		$sort = $_POST['sort'];
		$word = $_POST['word'];
		$key = $_POST['key'];
		$page = $_POST['page'];
		$token = $_POST['token'];
		$lid = $_POST['lid'];
		$mid1 = $_POST['mid1'];
		$mid2 = $_POST['mid2'];
	}

	//add
	if ($mode == "") {
		$mode = "list";
	}

	switch ($mode) {
		case "add":
			SaveData($mid2);
			$mode = "list";
			if ($page == "") {
				$page = 1;
			}
			break;
		case "delete":
			DeleteData($mid1);

			$mode = "list";
			if ($page == "") {
				$page = 1;
			}
			break;
		case "list":
			if ($page == "") {
				$page = 1;
			}
			break;
	}

	DispData($mode, $sort, $word, $key, $page, $lid, $token);

	return $function_ret;
}

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode, $sort, $word, $key, $page, $lid, $token)
{

	eval(globals());

	$htmllist = "list.html";

	$filename = $htmllist;

	$fp = $filename;
	$tso = @fopen($fp, "r");

	while ($line = fgets($tso, 1024)) {
		if (strstr($line, "LIST-START") == true) {
			break;
		}
		$strU = $strU . $line . chr(13);
	}
	while ($line = fgets($tso, 1024)) {
		if (strstr($line, "LIST-END") == true) {
			break;
		}
		$strM = $strM . $line . chr(13);
	}
	while ($line = fgets($tso, 1024)) {
		$strD = $strD . $line . chr(13);
	}
	fclose($tso);

	$filename = "../common/template/listmbl1.html";
	//$filename = "../common/template/listo1.html";
	
	$fp = $DOCUMENT_ROOT . $filename;
	$strM = @file_get_contents($fp);

	$hid = "";
	// SQLインジェクション対策
	// 2021.03.15 yamamoto ENABLE:公開中のみ表示
	$StrSQL="SELECT DAT_O2.* FROM DAT_O2 inner join DAT_BL on DAT_O2.MID=DAT_BL.MID2 where DAT_BL.MID1='".$_SESSION['MID']."' and DAT_O2.ENABLE = 'ENABLE:公開中'";

	$rs = mysqli_query(ConnDB(), $StrSQL);
	$item = mysqli_num_rows($rs);
	if ($item == "") {
		$reccount = 0;
		$pagestr = "";
		$strMain = "<tr class=tableset__list><td align=center colspan=7>まだブラックリストはありません</td></tr>";
	} else {
		//================================================================================================
		//ページング処理
		//================================================================================================
		$reccount=mysqli_num_rows($rs);
		$pagecount=intval(($reccount-1)/$PageSize+1);
		mysqli_data_seek($rs, $PageSize*($page-1));

		$str="";
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
				$str=$str."<span class=\"current\">".$i."</span>";
			} else {
				$str=$str." <a href=\"".MakeUrl($sort,$word,$sel1,$sel2,$i)."\" class=\"inactive\">".$i."</a>";
			} 
		}
		$pagestr=$str;

		$CurrentRecord=1;
		$strMain="";
		while ($item = mysqli_fetch_assoc($rs)) {

			$str=$strM;

			$str=DispO2($item, $str);
			$str=DispPoint2($item['OID'], $str);
 
			$StrSQL="SELECT * FROM DAT_M2 where MID='".$item['MID']."'";
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			$item2=mysqli_fetch_assoc($rs2);
			$str=DispM2($item2, $str);

			$strMain=$strMain.$str.chr(13);

			$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

			if ($CurrentRecord>$PageSize){
				break;
			}
		} 
	}

	$str = $strU . $strMain . $strD;

	$str = MakeHTML($str, 0, $lid);

	$str = str_replace("[PAGING]", $pagestr, $str);
	$str = str_replace("[SORT]", $sort, $str);
	$str = str_replace("[WORD]", $word, $str);
	$str = str_replace("[PAGE]", $page, $str);
	$str = str_replace("[KEY]", $key, $str);
	$str = str_replace("[LID]", $lid, $str);
	$str = str_replace("[RECCOUNT]", $reccount, $str);

	// CSRFトークン生成
	if ($token == "") {
		$token = htmlspecialchars(session_id());
		$_SESSION['token'] = $token;
	}
	$str = str_replace("[TOKEN]", $token, $str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

	return $function_ret;
}

//=========================================================================================================
//名前 DB書き込み
//機能 DBにレコードを保存
//引数 $mid1, $mid2
//戻値 $function_ret
//=========================================================================================================
function SaveData($mid2)
{
	eval(globals());

	// SQLインジェクション対策
	// HTMLエスケープ処理（SQL書き込み）
	$StrSQL = "SELECT * FROM " . $TableName . " WHERE MID1 ='" . mysqli_real_escape_string(ConnDB(), $_SESSION['MID']) . "' AND MID2 ='" . mysqli_real_escape_string(ConnDB(), $mid2) . "';";
	$rs = mysqli_query(ConnDB(), $StrSQL);
	$item = mysqli_num_rows($rs);
	if ($item == "") {
		$StrSQL = "INSERT INTO " . $TableName . " (";
		for ($i = 1; $i <= $FieldMax; $i++) {
			if ($i > 1) {
				$StrSQL .= ",";
			}
			$StrSQL .= "`" . $FieldName[$i] . "`";
		}
		$StrSQL .=  ") VALUES (";
		$StrSQL .=  "'" . $_SESSION['MID'] . "',";
		$StrSQL .=  "'" . $mid2 . "',";
		$StrSQL .=  "'" . date("Y/m/d H:i:s") . "'";
		$StrSQL .=  ")";
	}

	//var_dump($StrSQL);

	if (!(mysqli_query(ConnDB(), $StrSQL))) {
		die;
	}

	return $function_ret;
}

//=========================================================================================================
//名前 DB削除
//機能 DBからレコードを削除
//引数 $mid1, $mid2
//戻値 $function_ret
//=========================================================================================================
function DeleteData($mid2)
{
	eval(globals());

	// SQLインジェクション対策
	$StrSQL = "DELETE FROM " . $TableName . " WHERE MID1 ='" . mysqli_real_escape_string(ConnDB(), $_SESSION['MID']) . "' AND MID2 ='" . mysqli_real_escape_string(ConnDB(), $mid2) . "';";
	if (!(mysqli_query(ConnDB(), $StrSQL))) {
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
