<?php

session_start();
require "./config.php";
require "./base.php";
require "./common.php";
require './a_m1/config.php';

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

	$filename="top.html";
	$fp=$DOCUMENT_ROOT.$filename;
	$str=@file_get_contents($fp);

	$filename="common/template/listo1.html";
	$fp=$DOCUMENT_ROOT.$filename;
	$strM=@file_get_contents($fp);
	$strMain="";
	$StrSQL="SELECT DAT_O1.* FROM DAT_O1 inner join DAT_M1 on DAT_M1.MID=DAT_O1.MID and DAT_M1.ENABLE='ENABLE:公開中' and DAT_O1.ENABLE='ENABLE:公開中' order by rand() limit 0,5";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	while ($item = mysqli_fetch_assoc($rs)) {
		$tmp=$strM;

		$tmp=DispO1($item, $tmp);
		$tmp=DispPoint1($item['OID'], $tmp);

		$StrSQL="SELECT * FROM DAT_M1 where MID='".$item['MID']."'";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2=mysqli_fetch_assoc($rs2);
		$tmp=DispM1($item2, $tmp);

		$strMain=$strMain.$tmp.chr(13);
	}
	$str=str_replace("[LIST_1]", $strMain, $str);

	$filename="common/template/listo2.html";
	$fp=$DOCUMENT_ROOT.$filename;
	$strM=@file_get_contents($fp);
	$strMain="";
	$StrSQL="SELECT DAT_O2.* FROM DAT_O2 inner join DAT_M2 on DAT_M2.MID=DAT_O2.MID and DAT_M2.ENABLE='ENABLE:公開中' and DAT_O2.ENABLE='ENABLE:公開中' order by rand() limit 0,5";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	while ($item = mysqli_fetch_assoc($rs)) {
		$tmp=$strM;

		$tmp=DispO2($item, $tmp);
		$tmp=DispPoint2($item['OID'], $tmp);

		$StrSQL="SELECT * FROM DAT_M2 where MID='".$item['MID']."'";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2=mysqli_fetch_assoc($rs2);
		$tmp=DispM2($item2, $tmp);

		$strMain=$strMain.$tmp.chr(13);
	}
	$str=str_replace("[LIST_2]", $strMain, $str);

	//関連Supplier一覧
	$m1List="";
	if($_SESSION['MATT'] == "2"){
		
		$filename="common/template/listo1.html";
		$fp=$DOCUMENT_ROOT.$filename;
		$strM=@file_get_contents($fp);

		//カテゴリーを取得
		$cates = "";
		$StrSQL="SELECT O1_MSEL01 FROM DAT_O1  ";
		$StrSQL.=" INNER JOIN ( ";
		$StrSQL.="  SELECT AID FROM DAT_MESSAGE WHERE AID LIKE '%".$_SESSION['MID']."%') AS MSG  ";
		$StrSQL.="  ON DAT_O1.MID LIKE '%' || MSG.AID || '%' ";
		$StrSQL.="  AND DAT_O1.MID != '".$_SESSION['MID']."' ";
		$StrSQL.=" GROUP BY O1_MSEL01 ";

		$rs=mysqli_query(ConnDB(),$StrSQL);
		while ($item = mysqli_fetch_assoc($rs)) {

			if($item['O1_MSEL01']==""){
				continue;
			}

			if($cates!=""){
				$cates.=",";
			}
			$cates.="'".$item['O1_MSEL01']."'";
		}

		if($cates!= ""){
			//Supplier情報抽出
			$StrSQL="  SELECT * FROM DAT_O1 ";
			$StrSQL.="  where O1_MSEL01 IN (".$cates.") ";
			$StrSQL.="  order by RAND() ";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			while ($item = mysqli_fetch_assoc($rs)) {
				$tmp=$strM;

				$tmp=DispO1($item, $tmp);
				$tmp=DispPoint1($item['OID'], $tmp);

				$StrSQL="SELECT * FROM DAT_M1 where MID='".$item['MID']."'";
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				$item2=mysqli_fetch_assoc($rs2);
				$tmp=DispM1($item2, $tmp);

				$m1List.=$tmp.chr(13);
			}
		}
	} 

	if($m1List==""){
		$str=DispParamNone($str, "LIST_3");
	} else {
		$str=DispParam($str, "LIST_3");
	}
	$str=str_replace("[LIST_3]", $m1List, $str);

	$press="";
	$StrSQL="SELECT * FROM DAT_INFO where ETC12='ETC12:公開中' order by ID desc limit 0,10";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	while ($item = mysqli_fetch_assoc($rs)) {
		$press.="<div class='knowledge__item'> <a href='/info/".sprintf("%04d", $item['ID'])."/'><div class='knowledge__img'><img src='".$item['PIC']."' alt=''></div><div class='knowledge__desc'><h3 class='knowledge__ttl'>".$item['TITLE']."</h3><p class='knowledge__txt'>".mb_substr($item['COMMENT'],0,60,"UTF-8")."… <span class='knowledge__txt--more'>続きを読む</span></p></div></a> </div>";
	}
	if($press!=""){
		$str=str_replace("[PRESS1]", $press, $str);
	} else {
		$str=str_replace("[PRESS1]", "公開までしばらくおちください", $str);
	}

	$str = MakeHTML($str,0,$lid);

	if($_SESSION['MID']==""){

		$str=DispParamNone($str, "LOGINM");
		$str=DispParam($str, "LOGOUTM");
	} else {

		$str=DispParam($str, "LOGINM");
		$str=DispParamNone($str, "LOGOUTM");
	}

	$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

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
