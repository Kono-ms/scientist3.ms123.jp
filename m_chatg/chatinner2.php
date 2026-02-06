<?php

session_start();
require "../config.php";
require "../base.php";
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

	$word=$_GET['aid'];

	if ($mode==""){
		$mode="list";
	}

	if ($page==""){
		$page=1;
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

	$rid=$_SESSION['MID'];

	$str="";
	// SQLインジェクション対策
	$StrSQL="SELECT *, NEWDATE as LKDATE, ID as CID FROM DAT_MESSAGE2 where DAT_MESSAGE2.AID='".$word."' and DAT_MESSAGE2.ENABLE='ENABLE:公開中' order by DAT_MESSAGE2.ID desc LIMIT 0, 1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0) {
		$item = mysqli_fetch_assoc($rs);

		$sql_m1="SELECT M1_DFIL01 FROM DAT_M1 where MID='".$_SESSION['MID']."'";
		$rs_m1=mysqli_query(ConnDB(),$sql_m1);
		$item_m1 = mysqli_fetch_assoc($rs_m1);
		$img_m1 = $item_m1['M1_DFIL01'];

		if($_SESSION["CHATID".$word]!=$item['CID']){
			if($item['RID']==$_SESSION['MID']){
				$tmp="<div class=\"right_balloon\"><div class=\"balloon_div_left\"><img src=\"" . $img_m1 . "\"/></div><div class=\"balloon_div_right\"><div class=\"balloon_sub\"><span class=\"balloon_date\">[LKDATE]</span></div><p>[D-COMMENT]</p></div></div>";
			} else {
				$tmp="<div class=\"left_balloon\"><div class=\"balloon_div_left\"><img src=\"[LKIMG]\"/></div><div class=\"balloon_div_right\"><div class=\"balloon_sub\"><span class=\"balloon_name\">[LKNAME]</span><span class=\"balloon_date\">[LKDATE]</span></div><p>[D-COMMENT]</p></div></div>";
			}

			// 2021.02.16 yamamoto 画像のサムネ表示
			if(strpos($item['COMMENT'], 'UPLOADED-FILE:') !== false) {
				preg_match('/<a .*?>/', $item['COMMENT'], $match);
				if(strpos($match[0], '.jpeg') !== false || strpos($match[0], '.jpg') !== false || strpos($match[0], '.jpe') !== false || strpos($match[0], '.gif') !== false || strpos($match[0], '.png') !== false || strpos($match[0], '.bmp') !== false) {
					$img = str_replace("<a href", '<img class="thumbnail" src', $match[0]);
					$img = str_replace("</a>", '', $img);
					$item['COMMENT'] = str_replace('<!-- UPLOADED-FILE: -->', '<!-- UPLOADED-FILE: -->' . $img . '<br>', $item['COMMENT']);
					$item['COMMENT'] = preg_replace('/<a href/', "<a download href", $item['COMMENT']);
				}
			}

			$tmp=str_replace("[D-COMMENT]",ChatText($item['COMMENT']),$tmp);
			$tmp=str_replace("[LKDATE]",htmlspecialchars($item['LKDATE']),$tmp);

			// 2020.10.22 yamamoto AIDではなくメッセージのRIDを元に会員データを取得
			if(strstr($item['RID'], "M1")==true){
				$StrSQL="SELECT M1_DVAL01,M1_DFIL01 FROM DAT_M1 where MID='".$item['RID']."';";
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				$item2 = mysqli_fetch_assoc($rs2);
				$tmp=str_replace("[LKNAME]",$item2['M1_DVAL01'],$tmp);
				$tmp=str_replace("[LKIMG]",$item2['M1_DFIL01'],$tmp);
			} else {
				$StrSQL="SELECT M2_DVAL01,M2_DFIL01 FROM DAT_M2 where MID='".$item['RID']."';";
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				$item2 = mysqli_fetch_assoc($rs2);
				$tmp=str_replace("[LKNAME]",$item2['M2_DVAL01'],$tmp);
				$tmp=str_replace("[LKIMG]",$item2['M2_DFIL01'],$tmp);
			}

			$strMain.=$tmp.chr(13);

			$_SESSION["CHATID".$word]=$item['CID'];
		}
	} 

	print $strMain;

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
