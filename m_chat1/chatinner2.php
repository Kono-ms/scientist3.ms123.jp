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
	// 2020.12.23 yamamoto 案件ID(ETC02)で絞り込む
	$StrSQL="SELECT *, NEWDATE as LKDATE, ID as CID FROM DAT_MESSAGE where DAT_MESSAGE.AID='".$word."' and DAT_MESSAGE.ETC02='".$_GET['etc02']."' and DAT_MESSAGE.ENABLE='ENABLE:公開中' and (DAT_MESSAGE.ETC03 is null or DAT_MESSAGE.ETC03 = '".$_SESSION['MID']."') order by DAT_MESSAGE.ID desc LIMIT 0, 1;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0) {
		$item = mysqli_fetch_assoc($rs);

		//if($_SESSION["CHATID".$word]!=$item['CID']){
		if($_SESSION["CHATID_".$_GET['chat_hash']]!=$item['CID']) {

			$ids = explode('-', $word);
			$mid1 = $ids[0];
			$mid2 = $ids[1];
			$sql_m1="SELECT M1_DFIL01 FROM DAT_M1 where MID='".$mid1."'";
			$rs_m1=mysqli_query(ConnDB(),$sql_m1);
			$item_m1 = mysqli_fetch_assoc($rs_m1);
			$img_m1 = $item_m1['M1_DFIL01'];
			$sql_m2="SELECT M2_DFIL01 FROM DAT_M2 where MID='".$mid2."'";
			$rs_m2=mysqli_query(ConnDB(),$sql_m2);
			$item_m2 = mysqli_fetch_assoc($rs_m2);
			$img_m2 = $item_m2['M2_DFIL01'];

			// 2020.12.16 yamamoto システムメッセージの場合
			if(substr($item['COMMENT'], 0, 1) == '[') {
				$tmp="<div class=\"left_balloon system_balloon\"><div class=\"balloon_sub\"><span class=\"balloon_name\">[LKNAME]</span><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></div><p>[D-COMMENT]</p></div>";
			}
			else if($item['RID']==$rid){
				$tmp="<div class=\"right_balloon\"><div class=\"balloon_div_left\"><img src=\"" . $img_m1 . "\"/></div><div class=\"balloon_div_right\"><div class=\"balloon_sub\"><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></div><p>[D-COMMENT]</p></div></div>";
				//$tmp="<div class=\"right_balloon\"><p>[D-COMMENT]<span class=\"balloon_sub\"><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></p></div>";
			} else {
				$tmp="<div class=\"left_balloon\"><div class=\"balloon_div_left\"><img src=\"" . $img_m2 . "\"/></div><div class=\"balloon_div_right\"><div class=\"balloon_sub\"><span class=\"balloon_name\">[LKNAME]</span><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></div><p>[D-COMMENT]</p></div></div>";
				//$tmp="<div class=\"left_balloon\"><div class=\"balloon_sub\"><span class=\"balloon_name\">[LKNAME]</span><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></div><p>[D-COMMENT]</p></div>";

				$StrSQL="UPDATE DAT_MESSAGE SET NOREAD='".$rid."' WHERE ID=".$item['ID'].";";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
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

			// 2020.11.16 yamamoto 提示側にアンカーは出さない
			// 2020.12.22 yamamoto ファイル添付の場合はAタグを残す
			if(strpos($item['COMMENT'], 'UPLOADED-FILE:') === false) {
				$item['COMMENT'] = preg_replace('/<a .*?>(.*?)<\/a>/', "", $item['COMMENT']);
			}
			$item['COMMENT'] = str_replace("\n\n", '', $item['COMMENT']);
			$tmp=str_replace("[D-COMMENT]",ChatText($item['COMMENT']),$tmp);

			if($item['NOREAD']!=""){
				$tmp=str_replace("[KIDOKU]","read",$tmp);
			} else {
				$tmp=str_replace("[KIDOKU]","",$tmp);
			}

			$rrid=str_replace("-", "", str_replace($rid, "", $item['AID']));
			$StrSQL="SELECT * FROM DAT_M2 where MID='".$rrid."';";
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			$item2 = mysqli_fetch_assoc($rs2);
			$tmp=str_replace("[LKNAME]",$item2['M2_DVAL01'],$tmp);
			$tmp=str_replace("[LKDATE]",htmlspecialchars($item['LKDATE']),$tmp);

			$strMain.=$tmp.chr(13);

			//$_SESSION["CHATID".$word]=$item['CID'];
			$_SESSION["CHATID_".$_GET['chat_hash']]=$item['CID'];

			/*
			if($item['RID']==$rid){
				$tmp="<div class=\"right_balloon\"><p>[D-COMMENT]<span class=\"balloon_sub\"><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></p></div>";
			} else {
				$tmp="<p class=\"left_balloon\">[D-COMMENT]<br /><span class=\"balloon_sub\">[LKDATE]</span><span class=\"name\">[LKNAME]</span></p>";

				$StrSQL="UPDATE DAT_MESSAGE SET NOREAD='".$rid."' WHERE ID=".$item['ID'].";";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}
			$tmp=str_replace("[D-COMMENT]",ChatText('+++' . $item['COMMENT']),$tmp);

			if($item['NOREAD']!=""){
				$tmp=str_replace("[KIDOKU]","read",$tmp);
			} else {
				$tmp=str_replace("[KIDOKU]","",$tmp);
			}

			$rrid=str_replace("-", "", str_replace($rid, "", $item['AID']));
			$StrSQL="SELECT * FROM DAT_M2 where MID='".$rrid."';";
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			$item2 = mysqli_fetch_assoc($rs2);
			$tmp=str_replace("[LKNAME]",$item2['M2_DVAL01'],$tmp);
			$tmp=str_replace("[LKDATE]",htmlspecialchars($item['LKDATE']),$tmp);

			$strMain.=$tmp.chr(13);

			$_SESSION["CHATID".$word]=$item['CID'];
			*/

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
