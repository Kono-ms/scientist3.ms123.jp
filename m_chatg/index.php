<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_message/config.php';

set_time_limit(7200);

//データベース接続
ConnDB();
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

	if($_POST['mode']!=""){
		$mode=$_POST['mode'];
		$word=$_POST['word'];
		$mid1=$_POST['mid1'];
		$mid2=$_POST['mid2'];
	} else {
		$mode=$_GET['mode'];
		$word=$_GET['word'];
		$mid1=$_GET['mid1'];
		$mid2=$_GET['mid2'];
	}

	if($_SESSION['MID']==""){
		$url=BASE_URL . "/";
		header("Location: {$url}");
		exit;
	}

	$rid=$_SESSION['MID'];

	if($mode==""){
		$mode="list";
	}

	if ($mode=="send"){

		// ファイル添付
		$file_msg = '';
		if(is_uploaded_file($_FILES['file1']['tmp_name'])){
			if(move_uploaded_file($_FILES['file1']['tmp_name'], "../files/".$_FILES['file1']['name'])) {
				if(trim(str_replace("'","''",htmlspecialchars($_POST['COMMENT'])))!=""){
					$file_msg = '<br><br>';
				}
				$file_msg .= '<!-- UPLOADED-FILE: --><a href="../files/'.$_FILES['file1']['name'].'" target="_blank">'.$_FILES['file1']['name'].'</a>';
			}
		}

		if(trim(str_replace("'","''",htmlspecialchars($_POST['COMMENT'])))!="" || $file_msg != ''){
			$StrSQL="INSERT INTO DAT_MESSAGE2 (AID, RID, ENABLE, NEWDATE, COMMENT) values (";
			$StrSQL.="'".$word."',";
			$StrSQL.="'".$rid."',";
			$StrSQL.="'ENABLE:公開中',";
			$StrSQL.="'".date("Y/m/d H:i:s")."',";
			$StrSQL.="'".str_replace("'","''",htmlspecialchars($_POST['COMMENT'])).$file_msg."'";
			$StrSQL.=")";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
		}
	}

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
				SaveData($key);
			}
			$mode="list";
			if ($page==""){
				$page=1;
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
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2)
{

	eval(globals());

	//各テンプレートファイル名
	$htmllist = "list.html";

	$fp=$DOCUMENT_ROOT.$htmllist;
	$str=@file_get_contents($fp);

  // 2020.10.22 yamamoto 未読機能コメントアウト
  /*
	$StrSQL="UPDATE DAT_MESSAGE2 SET NOREAD='".$_SESSION['MID']."' WHERE AID='".$word."' and RID<>'".$_SESSION['MID']."'";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}
  */

	$str = MakeHTML($str,1,$lid);

	$StrSQL="SELECT * FROM DAT_M1 where MID='".$mid1."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$str=DispM1($item, $str);

	$str=str_replace("[MID1]",$mid1,$str);
	$str=str_replace("[MID2]",$mid2,$str);
	$str=str_replace("[AID]",$word,$str);

  // 2020.10.22 yamamoto 学校名を取得
	if(strstr($word, "M1")==true){
		$StrSQL="SELECT * FROM DAT_O1 where OID='".$word."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_o1 = mysqli_fetch_assoc($rs);
		$str=str_replace("[DVAL01]",$item_o1['O1_DVAL01'],$str);
	} else {
		$StrSQL="SELECT * FROM DAT_O2 where OID='".$word."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_o2 = mysqli_fetch_assoc($rs);
		$str=str_replace("[DVAL01]",$item_o2['O2_DVAL01'],$str);
	}

	$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

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
			$exts = split("[/\\.]", $_FILES["EP_".$FieldName[$i]]['name']);
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
