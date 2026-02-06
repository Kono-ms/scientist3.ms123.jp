<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_o1/config.php';

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
		$sel1=$_GET['sel1'];
		$sel2=$_GET['sel2'];
		$word2=$_GET['word2'];
		$mid1=$_GET['mid1'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$sel1=$_POST['sel1'];
		$sel2=$_POST['sel2'];
		$word2=$_POST['word2'];
		$mid1=$_POST['mid1'];
	}

	if(is_array($word)==true){
		$tmp="";
		for($i=0; $i<count($word); $i++){
			if($tmp!=""){
				$tmp.="\t";
			}
			$tmp.=$word[$i];
		}
		$word=$tmp;
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
	}

	if ($mode==""){
		$mode="list";
	}

	if($mode=="disp"){

		// if($_SESSION['MATT'] != "2"){
		// 	$url=BASE_URL . "/login2/";
		// 	header("Location: {$url}");
		// 	exit;
		// }

		if($key==""){
			$StrSQL="SELECT ID FROM DAT_O1 where MID='".$_SESSION['MID']."'";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_fetch_assoc($rs);
			$key=$item['ID'];
			if($key==""){
				print "自分のプロフィールを見るには、サプライヤーの募集情報を登録してください。";
				exit;
			}
		}
		// $StrSQL="SELECT * FROM DAT_O1 inner join DAT_M1 on DAT_M1.MID=DAT_O1.MID  ";
		// $StrSQL.=" inner join DAT_MATCH on DAT_MATCH.OID1=DAT_O1.OID and DAT_MATCH.MID2='".$_SESSION['MID']."'";
		// $StrSQL.=" and DAT_M1.ENABLE = 'ENABLE:公開中' and DAT_O1.ENABLE = 'ENABLE:公開中'";
		// $StrSQL.=" where (DAT_O1.ENABLE='ENABLE:公開中' and DAT_O1.ID=".$key.") or (DAT_O1.MID='".$_SESSION['MID']."' and DAT_O1.ID=".$key.")  ";

		$StrSQL="SELECT * FROM DAT_O2 where (ENABLE='ENABLE:公開中' and ID=".$key.") or (MID='".$_SESSION['MID']."' and ID=".$key.")";

		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($_SESSION['MID']=="" || $item<=0){
			$url=BASE_URL . "/login3/";
			header("Location: {$url}");
			exit;
		}

	} else {
		if($_SESSION['MID']==""){
			$url=BASE_URL . "/login3/";
			header("Location: {$url}");
			exit;
		}
	}

	switch ($mode){
	case "like":
		if($_SESSION['MID']!=""){
			$StrSQL="SELECT * FROM DAT_O1 where ID=".$key.";";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);
			$midt=$item['MID'];
			$oidt=$item['OID'];
			$mid=$_SESSION['MID'];
			if(strstr($midt,"M1")==true){
				$StrSQL="DELETE FROM DAT_IINE where MID='".$mid."' and OIDT='".$item['oidt']."';";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
				$StrSQL="INSERT INTO DAT_IINE (MID, MIDT, OIDT, NEWDATE) values ('".$mid."', '".$midt."', '".$oidt."', '".date('Y/m/d H:i:s')."')";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
				SendMail($midt);
			}
		}
		$mode="disp";
		break;
	case "disp":
		LoadData($key);
		break;
	case "list":
		if ($page==""){
			$page=1;
		} 
		if ($sort==""){
			$sort=1;
		}
		break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$sel1,$sel2,$word2,$mid1);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($midt)
{

	eval(globals());

	$StrSQL="SELECT EMAIL FROM DAT_M1 where MID='".$midt."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	//$fp="mail.txt";
	//$MailBody=@file_get_contents($fp);
	$maildata = GetMailTemplate('いいね(M2)');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	//mb_send_mail($item['EMAIL'], "【Scientist3】あなたのサプライヤーの募集情報情報が「いいね」されました！", $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3事務局","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 

	mb_send_mail($item['EMAIL'], $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail("info@msc-dev.com", $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
}

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$sel1,$sel2,$word2,$mid1)
{

	eval(globals());

	//各テンプレートファイル名
	$htmlnew = "edit.html";
	$htmledit = "edit.html";
	$htmlconf = "conf.html";
	$htmlend = "end.html";

	$htmldisp = "disp.html";
	$htmllist = "list.html";

	if ($mode!="list"){

		$filename=$htmldisp;
		$msg01="";
		$msg02="";
		$errmsg="";

		$fp=$DOCUMENT_ROOT.$filename;
		$str=@file_get_contents($fp);

		$StrSQL="SELECT * FROM DAT_O1 where ID='".$key."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);

		// ----------------------------------------------------------------------------------------
		// マーク制御1
		// ----------------------------------------------------------------------------------------
		// O系のデータにだけマークを付ける
		// M系やその他の情報にはマークは付けない
		// タグエスケープ回避のためここではタグにせず[mark1][mark2]で囲む
		$StrSQL="SELECT * FROM DAT_MATCH where MID2='".$_SESSION['MID']."' and OID1='".$item["OID"]."';";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item_mark = mysqli_fetch_assoc($rs2);
		$tmp=$item_mark['VAL_O1'];
		$t=explode("::", $tmp."::");
		foreach($item as $keys => $val) {
			if(strpos($item[$keys], '.jpeg') !== false || strpos($item[$keys], '.jpg') !== false || strpos($item[$keys], '.jpe') !== false || strpos($item[$keys], '.gif') !== false || strpos($item[$keys], '.png') !== false || strpos($item[$keys], '.bmp') !== false) {
				continue;
			}

			// 2021.04.13 yamamoto 複数リストに限り完全一致のみマークをつける
			if(strpos($item[$keys], ":") === false) {
				// 2021.05.10 yamamoto リスト以外にマークはつけない
			}
			else if(strpos($item[$keys], "\t") === false) {
				// 除外ワード
				$ex_word = array('あり'=>1,'なし'=>1,'可'=>1,'不可'=>1,'可能'=>1,'不可能'=>1,'応相談'=>1);
				for($i=0; $i<count($t); $i++){
					if(isset($ex_word[$t[$i]])) {
						continue;
					}
					if($t[$i]!=""){
						$item[$keys]=str_replace($t[$i],"[mark1]".$t[$i]."[mark2]",$item[$keys]);
					}
				}
			}
			else {
				$extab = explode("\t", $item[$keys]);
				$ret_item = '';
				for($j = 0; $j < count($extab); $j++) {
					$extab2 = explode(':', $extab[$j]);
					if(count($extab2) == 1) {
						break;
					}
					$ret_flg = false;
					for($i=0; $i<count($t); $i++){
						if($extab2[1] == $t[$i]) {
							$ret_flg = true;
							break;
						}
					}
					if($ret_flg) {
						$ret_item .= "[mark1]".$extab[$j]."[mark2]" . "\t";
					}
					else {
						$ret_item .= $extab[$j] . "\t";
					}
				}
				$item[$keys] = $ret_item;
			}

		}
		// ----------------------------------------------------------------------------------------

		$StrSQL="SELECT ID FROM DAT_IINE where MID='".$_SESSION['MID']."' and MIDT='".$item["MID"]."';";
		$rst1=mysqli_query(ConnDB(),$StrSQL);
		$itemt1=mysqli_num_rows($rst1);
		$StrSQL="SELECT ID FROM DAT_IINE where MIDT='".$_SESSION['MID']."' and MID='".$item["MID"]."';";
		$rst2=mysqli_query(ConnDB(),$StrSQL);
		$itemt2=mysqli_num_rows($rst2);
		if($itemt1==0 || $itemt2==0){
			for($i=6; $i<=8; $i++){
				$str=DispParamNone($str, "O1-O1_DFIL".sprintf("%02d", $i));
				$str=DispParamNone($str, "O1-O1_DVAL".sprintf("%02d", $i));
				$str=DispParamNone($str, "O1-O1_DTXT".sprintf("%02d", $i));
				$str=DispParamNone($str, "O1-O1_DSEL".sprintf("%02d", $i));
				$str=DispParamNone($str, "O1-O1_DRDO".sprintf("%02d", $i));
				$str=DispParamNone($str, "O1-O1_DCHK".sprintf("%02d", $i));
				$str=DispParamNone($str, "M1-M1_DFIL".sprintf("%02d", $i));
				$str=DispParamNone($str, "M1-M1_DVAL".sprintf("%02d", $i));
				$str=DispParamNone($str, "M1-M1_DTXT".sprintf("%02d", $i));
				$str=DispParamNone($str, "M1-M1_DSEL".sprintf("%02d", $i));
				$str=DispParamNone($str, "M1-M1_DRDO".sprintf("%02d", $i));
				$str=DispParamNone($str, "M1-M1_DCHK".sprintf("%02d", $i));
			}
		}

		$str=DispO1($item, $str);
		$str=DispPoint1($item['OID'], $str);
		$StrSQL="SELECT * FROM DAT_M1 where MID='".$mid1."'";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2=mysqli_fetch_assoc($rs2);

		// M系のマーク用だが不要だったためコメントアウト
		/*
		foreach($item2 as $keys => $val) {
			if(strpos($item2[$keys], '.jpeg') !== false || strpos($item2[$keys], '.jpg') !== false || strpos($item2[$keys], '.jpe') !== false || strpos($item2[$keys], '.gif') !== false || strpos($item2[$keys], '.png') !== false || strpos($item2[$keys], '.bmp') !== false) {
				continue;
			}
			for($i=0; $i<count($t); $i++){
				if($t[$i]!=""){
					$item2[$keys]=str_replace($t[$i],"[mark1]".$t[$i]."[mark2]",$item2[$keys]);
				}
			}
		}
		*/

		$str=DispM1($item2, $str);

		// ----------------------------------------------------------------------------------------
		// マーク制御2
		// ----------------------------------------------------------------------------------------
		// データにだけマークを付けるのでここはコメントアウト
		/*
		$StrSQL="SELECT * FROM DAT_O1 where ID='".$key."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$StrSQL="SELECT * FROM DAT_MATCH where MID2='".$_SESSION['MID']."' and OID1='".$item["OID"]."';";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);
		$tmp=$item2['VAL_O1'];
		$t=explode("::", $tmp."::");
		for($i=0; $i<count($t); $i++){
			if($t[$i]!=""){
				$str=str_replace($t[$i],"<mark>".$t[$i]."</mark>",$str);
			}
		}
		*/

		// ---------------------------------------------------------------------------
		// 2021.02.16 yamamoto 両方からいいねしていないとメッセージが送れない処理
		// ---------------------------------------------------------------------------
		$StrSQL="SELECT MID FROM DAT_O2 where ID='".$key."';";
	   	$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$mid=$item['MID'];
		$mid=$mid1;

		$StrSQL="SELECT ID FROM DAT_IINE where MID='".$_SESSION['MID']."' and MIDT='".$mid."';";
		$rs1=mysqli_query(ConnDB(),$StrSQL);
		$item1=mysqli_num_rows($rs1);

		$StrSQL="SELECT ID FROM DAT_IINE where MIDT='".$_SESSION['MID']."' and MID='".$mid."';";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2=mysqli_num_rows($rs1);

		if($item1>0 && $item2>0){
			// 両方からいいねしている場合
			$str=DispParam($str, "MSG-BTN-ON");
			$str=DispParamNone($str, "MSG-BTN-OFF");
		} else {
			// 両方からいいねしている場合以外
			$str=DispParamNone($str, "MSG-BTN-ON");
			$str=DispParam($str, "MSG-BTN-OFF");
		}
		// ---------------------------------------------------------------------------

		// 2021.03.15 yamamoto 同属性のいいねとブラックリストの禁止
		if(mb_substr($_SESSION['MID'],0,2,"UTF-8")=="M1"){
			$str=str_replace("[ZOKUSEI]",'同属性',$str);
		}
		else {
			$str=str_replace("[ZOKUSEI]",'他属性',$str);
		}

		$str = MakeHTML($str,0,$lid);

		$str=str_replace("[KEY]",$key,$str);

		// 2021.01.18 yamamoto 評価一覧
		$eval_list = GetEvalList($mid1);
		$str=str_replace("[D-O1_EVAL_LIST]",$eval_list,$str);

		$str=str_replace("[BASE_URL]",BASE_URL,$str);

		// ----------------------------------------------------------------------------------------
		// マーク制御3
		// ----------------------------------------------------------------------------------------
		// ここでタグにする
		$str=str_replace("[mark1]","<mark>",$str);
		$str=str_replace("[mark2]","</mark>",$str);
		// titleがマークされるのを防止
		preg_match('/<title>(.*?)<\/title>/', $str, $match);
		$val = str_replace('<mark>', '', $match[0]);
		$val = str_replace('</mark>', '', $val);
		$str = preg_replace('/<title>(.*?)<\/title>/', $val, $str);
		// h1がマークされるのを防止
		preg_match('/<h1(.*?)<\/h1>/', $str, $match);
		$val = str_replace('<mark>', '', $match[0]);
		$val = str_replace('</mark>', '', $val);
		$str = preg_replace('/<h1(.*?)<\/h1>/', $val, $str);
		// パンくずがマークされるのを防止
		preg_match('/<section class="breadcrumbs">(.*?)<\/section>/s', $str, $match);
		$val = str_replace('<mark>', '', $match[0]);
		$val = str_replace('</mark>', '', $val);
		$str = preg_replace('/<section class="breadcrumbs">(.*?)<\/section>/s', $val, $str);
		// ----------------------------------------------------------------------------------------

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

		$filename="../common/template/list_schedule3.html";
		$fp=$DOCUMENT_ROOT.$filename;
		$strM=@file_get_contents($fp);

		//アカウント種類の取得
		// $accunt = "";
		// $kigyoid = "";
		// $StrSQL="SELECT M2_DVAL12,M2_DVAL13 FROM DAT_M2 where MID ='".$_SESSION['MID']."'";
		// $rs=mysqli_query(ConnDB(),$StrSQL);
		// $item = mysqli_fetch_assoc($rs);
		// $accunt = $item["M2_DVAL12"];
		// $kigyoid = $item["M2_DVAL13"];


		// SQLインジェクション対策
		// if($accunt=="M2_DVAL12:企業") {
		// 	//DAT_MATCHではなく企業IDでマッチング
		// 	$StrSQL="SELECT DAT_O1.* FROM DAT_O1 inner join DAT_M1 on DAT_M1.MID=DAT_O1.MID  ";
		// 	$StrSQL.=" where DAT_M1.ENABLE = 'ENABLE:公開中' and DAT_O1.ENABLE = 'ENABLE:公開中'";
		// 	$StrSQL.=" and DAT_M1.M2_DVAL13='".$kigyoid."'";
		// 	$StrSQL .= " and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_O1.MID) ";
		// 	$StrSQL .= " and ".ListSQLSearch($sort,$word,$sel1,$sel2,$word2);
		// } else {
			/*
			$StrSQL="SELECT DAT_O1.* FROM DAT_O1 inner join DAT_M1 on DAT_M1.MID=DAT_O1.MID inner join DAT_MATCH on DAT_MATCH.OID1=DAT_O1.OID and DAT_MATCH.MID2='".$_SESSION['MID']."' ";
			$StrSQL.=" and DAT_M1.ENABLE = 'ENABLE:公開中' and DAT_O1.ENABLE = 'ENABLE:公開中'";
			$StrSQL .= " and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_O1.MID) ";
			$StrSQL .= " and ".ListSQLSearch($sort,$word,$sel1,$sel2,$word2);
			*/
		// }

		// DAT_O1、DAT_MATCHも使ってない可能性もあるが
		// 不明なので使っている前提で実装する
		// $StrSQL="
		//   SELECT
		// 	  DAT_O1.*
		// 	FROM
		// 	  DAT_O1
		// 		inner join DAT_M1
		// 		  on DAT_M1.MID=DAT_O1.MID
		// 		left join DAT_MATCH
		// 		  on DAT_MATCH.OID1=DAT_O1.OID
		// 			and DAT_MATCH.MID2='".$_SESSION['MID']."'
		// 	    where DAT_M1.ENABLE = 'ENABLE:公開中'
		// 			and DAT_O1.ENABLE = 'ENABLE:公開中'
		// 	    and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_O1.MID)
		// 	    and ".ListSQLSearch($sort,$word,$sel1,$sel2,$word2)."
		// ";
		/*
			$StrSQL="
		  SELECT
			  DAT_SHODAN.ID as SHODAN_ID,
			  DAT_SHODAN.STATUS,
			  DAT_SHODAN.MID1_LIST,
			  DAT_SHODAN.ID as TORIHIKI_ID
			FROM
				DAT_SHODAN
				left join DAT_FILESTATUS
				  on DAT_SHODAN.ID = DAT_FILESTATUS.SHODAN_ID
					and DAT_FILESTATUS.ID = (select max(f2.ID) from DAT_FILESTATUS f2 where f2.SHODAN_ID = DAT_SHODAN.ID)
			WHERE
				DAT_SHODAN.MID2 = '".$_SESSION['MID']."'
			ORDER BY
				DAT_SHODAN.ID desc
		";
		*/

		$mid3=$_SESSION['MID'];
// if($_SESSION['MID']=="M200001"){
// $mid3="M300001";
// }
		//mid3に対応するmid2
		$StrSQL="SELECT MID FROM DAT_M2 WHERE M2_DVAL15='".$mid3."'";
		$mid2_rs=mysqli_query(ConnDB(),$StrSQL);
		$mid2_item=mysqli_fetch_assoc($mid2_rs);
		$mid2="";
		$mid2=$mid2_item["MID"];
		//echo "<!--mid2:$mid2-->";

//		$StrSQL="
//			SELECT
//				DAT_FILESTATUS.SHODAN_ID,
//				DAT_SHODAN.TITLE,
//				DAT_FILESTATUS.STATUS as STATUS,
//				DAT_FILESTATUS.ID as FILESTATUS_ID,
//				DAT_FILESTATUS.M2_ID as M2_ID,
//				DAT_FILESTATUS.M2_VERSION as M2_VERSION,
//				DAT_FILESTATUS.M2_PAY_TYPE as M2_PAY_TYPE,
//				DAT_FILESTATUS.DIV_ID as DIV_ID,
//				DAT_FILESTATUS.M_STATUS as M_STATUS,
//				DAT_M1.M1_DVAL01,
//				DAT_M2.M2_DVAL01,
//				DAT_M1.MID as MID1,
//				DAT_M2.MID as MID2,
//				DAT_M2.M2_DVAL16,
//				DAT_M2.M2_DVAL17
//			FROM
//				DAT_FILESTATUS
//				join DAT_SHODAN
//				  on DAT_FILESTATUS.SHODAN_ID = DAT_SHODAN.ID
//				join DAT_M1
//				  on DAT_FILESTATUS.MID1 = DAT_M1.MID
//				join DAT_M2
//				  on DAT_FILESTATUS.MID2 = DAT_M2.MID
//				join DAT_M3
//				  on DAT_M2.M2_DVAL15 = DAT_M3.MID
//			WHERE
//				DAT_M3.MID = '".$mid3."'
//				AND (
//					DAT_FILESTATUS.STATUS IN ('問い合わせ', '見積り送付', '再見積り依頼')
//					OR (
//						DAT_FILESTATUS.STATUS = '見積り依頼'
//						AND DAT_FILESTATUS.SHODAN_ID NOT IN (
//							SELECT SHODAN_ID 
//							FROM DAT_FILESTATUS 
//							WHERE STATUS = '見積り送付'
//							AND MID2 = '" . $mid2 . "'
//							AND DIV_ID NOT LIKE '%Part0'
//						)
//					)
//				)
//				AND DAT_FILESTATUS.DIV_ID NOT LIKE '%Part0'
//			GROUP BY
//			 	DAT_FILESTATUS.SHODAN_ID,
//			 	DAT_FILESTATUS.ID,
//				DAT_FILESTATUS.MID1
//			ORDER BY
//				lpad(DAT_FILESTATUS.SHODAN_ID, 10, '0') DESC,
//				DAT_FILESTATUS.MID1 ASC,
//				FILESTATUS_ID DESC
//		";

		$StrSQL="
			SELECT
				DAT_FILESTATUS.SHODAN_ID,
				DAT_SHODAN.TITLE,
				DAT_FILESTATUS.STATUS as STATUS,
				DAT_FILESTATUS.ID as FILESTATUS_ID,
				DAT_FILESTATUS.M2_ID as M2_ID,
				DAT_FILESTATUS.M2_VERSION as M2_VERSION,
				DAT_FILESTATUS.M2_PAY_TYPE as M2_PAY_TYPE,
				DAT_FILESTATUS.DIV_ID as DIV_ID,
				DAT_FILESTATUS.M_STATUS as M_STATUS,
				DAT_M1.M1_DVAL01,
				DAT_M2.M2_DVAL01,
				DAT_M1.MID as MID1,
				DAT_M2.MID as MID2,
				DAT_M2.M2_DVAL16,
				DAT_M2.M2_DVAL17
			FROM
				DAT_FILESTATUS
				join DAT_SHODAN
				  on DAT_FILESTATUS.SHODAN_ID = DAT_SHODAN.ID
				join DAT_M1
				  on DAT_FILESTATUS.MID1 = DAT_M1.MID
				join DAT_M2
				  on DAT_FILESTATUS.MID2 = DAT_M2.MID
				join DAT_M3
				  on DAT_M2.M2_DVAL15 = DAT_M3.MID
			WHERE
				DAT_M3.MID = '".$mid3."'
				AND (
					DAT_FILESTATUS.STATUS IN ('問い合わせ', '見積り送付', '再見積り依頼')
					OR (
						DAT_FILESTATUS.STATUS = '見積り依頼'
						AND DAT_FILESTATUS.SHODAN_ID NOT IN (
							SELECT SHODAN_ID 
							FROM DAT_FILESTATUS 
							WHERE (STATUS = '見積り送付' OR STATUS = '運営手数料追加')
							AND MID2 = '" . $mid2 . "'
							AND DIV_ID NOT LIKE '%Part0'
						)
					)
					##OR (
					##	DAT_FILESTATUS.STATUS = '運営手数料追加'
					##	AND DAT_FILESTATUS.DIV_ID NOT IN (
					##		SELECT DIV_ID
					##		FROM DAT_FILESTATUS
					##		WHERE STATUS = '見積り送付'
					##		AND MID2 = '" . $mid2 . "' 
					##	)
					##)
				)
				AND DAT_FILESTATUS.DIV_ID NOT LIKE '%Part0'
			GROUP BY
			 	DAT_FILESTATUS.SHODAN_ID,
			 	DAT_FILESTATUS.ID,
				DAT_FILESTATUS.MID1
			ORDER BY
				lpad(DAT_FILESTATUS.SHODAN_ID, 10, '0') DESC,
				DAT_FILESTATUS.MID1 ASC,
				FILESTATUS_ID DESC
		";
//echo('<!--'.$StrSQL.'-->');

//  var_dump($StrSQL);
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs); 
//echo('<!--件数:'.$item.'-->');
		if($item=="") {
			$reccount=0;
			$pagestr="";
			// $strMain="<div class='result__item'><p class='result__ttl'>検索結果が見つかりませんでした。<p></div><div class='result__item'><p class='result__txt'>研究者の希望情報の登録はお済みでしょうか？検索結果の表示には研究者の希望情報の登録が必要になります。まだ未登録の場合は先にこちらから登録をお願いします。</p><div class='btn result__btn'><a href='/m_o2/?mode=new&sort=&word=[L-MID]&page=1'>研究者の希望情報を作成する</a></div></div>";
			$strMain="<div class='result__item'><p class='result__ttl'>検索結果が見つかりませんでした。<p></div>";
			$start = 0;
			$end = 0;
		} else {
			//================================================================================================
			//ページング処理
			//================================================================================================
			$reccount=mysqli_num_rows($rs);
			$pagecount=intval(($reccount-1)/$PageSize+1);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$start = ($page - 1) * $PageSize + 1;
			$end = $pagecount == $page ? $reccount - ($page - 1) * $PageSize : $start + $PageSize - 1;

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
				//echo "<!--";
				//var_dump($item);
				//echo "-->";

				$str=$strM;

				$str=str_replace("[SHODAN_ID]",$item['SHODAN_ID'],$str);
				$str=str_replace("[FILESTATUS_ID]",$item['FILESTATUS_ID'],$str);

				$str=str_replace("[TITLE]",'<a href="javascript:popup_file_btn(\''.$item['MID1'].'-'.$item['MID2'].'\',\''.$item['SHODAN_ID'].'\')">'.$item['TITLE'].'</a>',$str);
				//$str=str_replace("[TITLE]",$item['TITLE'],$str);
				$str=str_replace("[M1_DVAL01]",$item['M1_DVAL01'],$str);
				$str=str_replace("[M2_DVAL01]",$item['M2_DVAL01'].'<br>'.$item['M2_DVAL16'].' '.$item['M2_DVAL17'],$str);


				$StrSQL="SELECT ID, STATUS FROM DAT_SHODAN WHERE ID='".$item["SHODAN_ID"]."' limit 1";
				$shodan_rs=mysqli_query(ConnDB(),$StrSQL);
				$shodan_item=mysqli_fetch_assoc($shodan_rs);

				if(strpos($shodan_item['STATUS'], '完了') !== false) {
					$str=str_replace("[CATEGORY]",'完了',$str);
				}
				else if(strpos($shodan_item['STATUS'], 'キャンセル依頼') !== false) {
					$str=str_replace("[CATEGORY]","実施中",$str);
				}
				else if(strpos($shodan_item['STATUS'], 'サプライヤーキャンセル承認') !== false) {
					$str=str_replace("[CATEGORY]","実施中",$str);
				}
				else if(strpos($shodan_item['STATUS'], 'サプライヤーキャンセル承認（追加見積り）') !== false) {
					$str=str_replace("[CATEGORY]","実施中",$str);
				}
				else if(strpos($shodan_item['STATUS'], 'サプライヤーキャンセル否認') !== false) {
					$str=str_replace("[CATEGORY]","実施中",$str);
				}
				else if(strpos($shodan_item['STATUS'], 'キャンセル承認') !== false) {
					$str=str_replace("[CATEGORY]","キャンセル",$str);
				}
				else if(strpos($shodan_item['STATUS'], 'キャンセル承認（請求あり）') !== false) {
					$str=str_replace("[CATEGORY]","請求",$str);
				}
				else if(strpos($shodan_item['STATUS'], 'キャンセル') !== false) {
					$str=str_replace("[CATEGORY]",'キャンセル',$str);
				}
				else if(strpos($shodan_item['STATUS'], '辞退') !== false) {
					if(strpos($shodan_item['STATUS'], '見積りの辞退') !== false){
						echo "<!--ステータス変更しない：".$shodan_item["SHODAN_ID"]."-->";
					}else{
						$str=str_replace("[CATEGORY]",'辞退',$str);
					}
				}


				if(strpos($shodan_item['STATUS'], '請求書送付(一括前払い)') !== false) {
					$str=str_replace("[CATEGORY]",'実施中',$str);
				}
				else if(strpos($shodan_item['STATUS'], '請求') !== false) {
					$str=str_replace("[CATEGORY]",'請求',$str);
				}
				else if(strpos($shodan_item['STATUS'], 'サプライヤーが納品(一括前払い)') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($shodan_item['STATUS'], 'データ納品') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($shodan_item['STATUS'], '物品納品') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($shodan_item['STATUS'], '研究者が納品承認(一括前払い)') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($shodan_item['STATUS'], '納品確認') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($shodan_item['STATUS'], '受注承認') !== false) {
					$str=str_replace("[CATEGORY]",'実施中',$str);
				}
				else if(strpos($shodan_item['STATUS'], '発注承認') !== false) {
					//$str=str_replace("[CATEGORY]",'見積り',$str);
					$str=str_replace("[CATEGORY]",'発注承認済',$str);
				}
				else if(strpos($shodan_item['STATUS'], '発注依頼') !== false) {
					// 発注依頼の場合だけ特殊処理
					$StrSQL="SELECT ID, MID, M2_DVAL15,KESSAI_SYONIN";
					$StrSQL.=" FROM DAT_M2 ";
					$StrSQL.=" WHERE MID='".$item["MID2"]."' ";
					$m2_k_rs=mysqli_query(ConnDB(),$StrSQL);
					$m2_k_item =  mysqli_fetch_assoc($m2_k_rs);

					if($m2_k_item["KESSAI_SYONIN"]=="KESSAI_SYONIN:あり"){
						$str=str_replace("[CATEGORY]",'<span style="color:red;">発注<br>(承認してください)</span>',$str);
					}

					$str=str_replace("[CATEGORY]",'発注',$str);
				}
				else if(strpos($shodan_item['STATUS'], '見積り送付') !== false) {
					//2回払い、マイルストーン払いのときにステータス更新がとまるが、決済者発注承認だけステータスが変わらないと困るので、臨時対応。
					if( checkDIV_ID($item["DIV_ID"])!="" ){
						$StrSQL="SELECT ID, NEWDATE, SHODAN_ID, STATUS, DIV_ID";
						$StrSQL.=" FROM DAT_FILESTATUS ";
						$StrSQL.=" WHERE SHODAN_ID='".$item["SHODAN_ID"]."' ";
						$StrSQL.=" and DIV_ID='".$item["DIV_ID"]."'";
						$StrSQL.=" order by NEWDATE desc,ID desc";
						$hatyu_rs=mysqli_query(ConnDB(),$StrSQL);
						$hatyu_item =  mysqli_fetch_assoc($hatyu_rs);
						//echo "<!--発注SQL:$StrSQL-->";
						//echo "<!--hattyu:";
						//var_dump($hatyu_item);
						//echo "-->";

						$StrSQL="SELECT ID, MID, M2_DVAL15,KESSAI_SYONIN";
						$StrSQL.=" FROM DAT_M2 ";
						$StrSQL.=" WHERE MID='".$item["MID2"]."' ";
						$m2_k_rs=mysqli_query(ConnDB(),$StrSQL);
						$m2_k_item =  mysqli_fetch_assoc($m2_k_rs);
						//echo "<!--m2_k_item:";
						//var_dump($m2_k_item);
						//echo "-->";

						if($hatyu_item["STATUS"]=="発注依頼" && $m2_k_item["KESSAI_SYONIN"]=="KESSAI_SYONIN:あり"){
							$str=str_replace("[CATEGORY]",'<span style="color:red;">発注<br>(承認してください)</span>',$str);
						}
					}

					$str=str_replace("[CATEGORY]",'見積り',$str);
				}
				else if(strpos($shodan_item['STATUS'], '見積り依頼') !== false) {
					$str=str_replace("[CATEGORY]",'見積り',$str);
				}
				else if(strpos($shodan_item['STATUS'], '問い合わせ') !== false) {
					$str=str_replace("[CATEGORY]",'問い合わせ',$str);
				}
				else {
					$str=str_replace("[CATEGORY]",'',$str);
				}
				

				if($item["STATUS"]=="見積り送付"){
				//if($item["STATUS"]=="見積り送付" || $item["STATUS"]=="運営手数料追加"){
					//品番の欄
					$StrSQL="SELECT FILESTATUS_ID, M2_DETAIL_ITEM ";
					$StrSQL.=" FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$item["FILESTATUS_ID"]."'";
					$detail_rs=mysqli_query(ConnDB(),$StrSQL);
					$item_list="";
					$i=0;
					while ($detail_item = mysqli_fetch_assoc($detail_rs)) {
						if($i==0) {
							$item_list.=$detail_item["M2_DETAIL_ITEM"];
						}else{
							$item_list.="<br>".$detail_item["M2_DETAIL_ITEM"];
						}
						$i++;
					}
					$str=str_replace("[ITEM_LIST]",$item_list,$str);
	
	
					//「前払い」の欄
					$in_advance="";
					if( ($item["M2_PAY_TYPE"]=="Split" || $item["M2_PAY_TYPE"]=="Milestone") && 
						($item["M_STATUS"]=="直接送付(前払い)" || $item["M_STATUS"]=="手数料追加(前払い)") ){
						$tmp="";
						$tmp=explode("-", $item["DIV_ID"]);
						if(count($tmp)==3 && $tmp[2]=="Part1"){
							$in_advance="〇";
						}
					}
					$str=str_replace("[IN_ADVANCE]",$in_advance,$str);
	
	
					//「見積り書」の欄
					$tmp="";
					$tmp=explode("-", $item["DIV_ID"]);
					$part="";
					$pre_part="";
					if(count($tmp)==3){
						$part=$tmp[2];
						$pre_part=$tmp[0]."-".$tmp[1];
					}

					if($item["M2_PAY_TYPE"]=="Split"){
						$StrSQL="SELECT ID, SHODAN_ID, MID1, DIV_ID, STATUS, M2_PAY_TYPE FROM DAT_FILESTATUS ";
						$StrSQL.=" WHERE SHODAN_ID='".$item["SHODAN_ID"]."' ";
						$StrSQL.=" AND MID2='".$mid2."' ";
						$StrSQL.=" AND STATUS='見積り送付' ";
						$StrSQL.=" AND DIV_ID='".$pre_part."-Part0' ";
						
						//echo "<!--SQL:$StrSQL-->";

						$split_rs=mysqli_query(ConnDB(),$StrSQL);
						$split_item = mysqli_fetch_assoc($split_rs);

						//echo "<!--";
						//var_dump($split_item);
						//echo "-->";
						if(!is_null($split_item["ID"]) && $split_item["ID"]!=""){
							$sm_url='/m_contact1/?type=見積り送付&mode=disp_frame&key='.$split_item["ID"];
						}else{
							$sm_url="";
						}
						
					}else{
						if(!is_null($item["FILESTATUS_ID"]) && $item["FILESTATUS_ID"]!=""){
							$sm_url='/m_contact1/?type=見積り送付&mode=disp_frame&key='.$item["FILESTATUS_ID"];
						}else{
							$sm_url="";
						}
					}

					$status_m="";
					$status_m .= '<div><a href="'.$sm_url.'" target="_blank">〇（Version'.$item["M2_VERSION"].'）</a></div>';
					$str=str_replace("[STATUS_M]",$status_m,$str);

					//$status_m="";
					//$status_m .= "<div><a href=\"javascript:window.parent.open_mcontact2('/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item["FILESTATUS_ID"]."')\">〇（Version".$item["M2_VERSION"]."	）</a></div>";
					//$str=str_replace("[STATUS_M]",$status_m,$str);
	
	
					//「発注書」の欄
					//一括の場合は商談内に１つしかないはず
					//（基本的に「発注したい場合は別の商談を立てる。
					// 基本的に１つの商談内に発注できる見積りは１つ。
					// 分割の場合は分割された見積りそれぞれに発注できるだけ)
					//whileにしてるのはねんのため
					$StrSQL="SELECT ID, SHODAN_ID, STATUS, H_M2_ID ";
					$StrSQL.=" FROM DAT_FILESTATUS ";
					$StrSQL.=" WHERE SHODAN_ID='".$item["SHODAN_ID"]."' and STATUS='発注依頼' ";
					$StrSQL.=" and H_M2_ID='".$item["FILESTATUS_ID"]."'";
					$hatyu_rs=mysqli_query(ConnDB(),$StrSQL);
					//echo "<!--発注SQL:$StrSQL-->";
	
					$status_h="";
					while ($hatyu_item = mysqli_fetch_assoc($hatyu_rs)){
						//echo "<!--";
						//var_dump($hatyu_item);
						//echo "--.";
						$status_h.='<a href="/m_contact1/?type=発注依頼&mode=disp&key='.$hatyu_item["ID"].'">〇</a>';
					}
					$str=str_replace("[STATUS_H]",$status_h,$str);
					
					
					//「納品書」の欄
					//商談内のどの見積りに対する納品かは分割の場合は「DIV_ID」をみればわかる
					//一括の場合は商談内に１つしかないはず
					//（基本的に「発注したい場合は別の商談を立てる。
					// 基本的に１つの商談内に発注できる見積りは１つ。
					// 分割の場合は分割された見積りそれぞれに発注できるだけ)
					$StrSQL="SELECT ID, SHODAN_ID, STATUS, DIV_ID, M2_NOHIN_TYPE";
					$StrSQL.=" FROM DAT_FILESTATUS ";
					$StrSQL.=" WHERE SHODAN_ID='".$item["SHODAN_ID"]."' ";
					$StrSQL.=" and (STATUS='データ納品' or STATUS='物品納品' or STATUS='サプライヤーが納品(一括前払い)')";
					$nohin_rs=mysqli_query(ConnDB(),$StrSQL);
					//echo "<!--納品SQL:$StrSQL-->";
					$status_n="";
					while($nohin_item = mysqli_fetch_assoc($nohin_rs)){
						//echo "<!--";
						//var_dump($nohin_item);
						//echo "--.";
						if($item["M2_PAY_TYPE"]=="Once"){
							if($nohin_item["STATUS"]=="データ納品"){
								$status_n.='<div><a href="/m_contact1/?type=データ納品&mode=disp&key='.$nohin_item["ID"].'">〇（Data）</a></div>';
							}else if($nohin_item["STATUS"]=="物品納品"){
								$status_n.='<div><a href="/m_contact1/?type=物品納品&mode=disp&key='.$nohin_item["ID"].'">〇（Goods）</a></div>';
							}else if($nohin_item["STATUS"]=="サプライヤーが納品(一括前払い)" && $nohin_item["M2_NOHIN_TYPE"]=="データ納品"){
								$status_n.='<div><a href="/m_contact1/?type=データ納品&mode=disp&key='.$nohin_item["ID"].'">〇（Data）</a></div>';
							}else if($nohin_item["STATUS"]=="サプライヤーが納品(一括前払い)" && $nohin_item["M2_NOHIN_TYPE"]=="物品納品"){
								$status_n.='<div><a href="/m_contact1/?type=物品納品&mode=disp&key='.$nohin_item["ID"].'">〇（Goods）</a></div>';
							}
						}else if($item["M2_PAY_TYPE"]=="Split" || $item["M2_PAY_TYPE"]=="Milestone"){
							if($item["DIV_ID"]==$nohin_item["DIV_ID"]){
								if($nohin_item["STATUS"]=="データ納品"){
									$status_n.='<div><a href="/m_contact1/?type=データ納品&mode=disp&key='.$nohin_item["ID"].'">〇（Data）</a></div>';
								}else if($nohin_item["STATUS"]=="物品納品"){
									$status_n.='<div><a href="/m_contact1/?type=物品納品&mode=disp&key='.$nohin_item["ID"].'">〇（Goods）</a></div>';
								}
							}
						}
					}
					$str=str_replace("[STATUS_N]",$status_n,$str);
	
	
					//「請求書」の欄
					//（基本的に「発注したい場合は別の商談を立てる。
					// 基本的に１つの商談内に発注できる見積りは１つ。
					// 分割の場合は分割された見積りそれぞれに発注できるだけ)
					$StrSQL="SELECT ID, SHODAN_ID, STATUS, DIV_ID";
					$StrSQL.=" FROM DAT_FILESTATUS ";
					$StrSQL.=" WHERE SHODAN_ID='".$item["SHODAN_ID"]."' ";
					$StrSQL.=" and STATUS='請求' ";
					$seikyu_rs=mysqli_query(ConnDB(),$StrSQL);
					//echo "<!--請求SQL:$StrSQL-->";
					$status_s="";
					while($seikyu_item = mysqli_fetch_assoc($seikyu_rs)){
						//echo "<!--";
						//var_dump($seikyu_item);
						//echo "--.";
						if($item["M2_PAY_TYPE"]=="Once"){
							$status_s.='<a href="/m_contact1/?type=請求&mode=disp&key='.$seikyu_item["ID"].'">〇</a>';
	
						}else if($item["M2_PAY_TYPE"]=="Split" || $item["M2_PAY_TYPE"]=="Milestone"){
							if($item["DIV_ID"]==$nohin_item["DIV_ID"]){
								$status_s.='<a href="/m_contact1/?type=請求&mode=disp&key='.$seikyu_item["ID"].'">〇</a>';
							}
						}
						
					}
					$str=str_replace("[STATUS_S]",$status_s,$str);

				}
				$str=str_replace("[ITEM_LIST]","",$str);
				$str=str_replace("[IN_ADVANCE]","",$str);
				$str=str_replace("[STATUS_M]","",$str);
				$str=str_replace("[STATUS_H]","",$str);
				$str=str_replace("[STATUS_N]","",$str);
				$str=str_replace("[STATUS_S]","",$str);

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			} 
		}



		/*
		//以下は仕様変更前のバージョンv1とする
		$StrSQL="
		  SELECT
			  DAT_FILESTATUS.SHODAN_ID,
			  DAT_SHODAN.TITLE,
			  group_concat(DAT_FILESTATUS.STATUS) as STATUS,
			  group_concat(DAT_FILESTATUS.ID) as FILESTATUS_ID,
			  group_concat(ifnull(DAT_FILESTATUS.M2_ID,'')) as M2_ID,
			  group_concat(ifnull(DAT_FILESTATUS.M2_VERSION,'')) as M2_VERSION,
				DAT_M1.M1_DVAL01,
				DAT_M2.M2_DVAL01,
				DAT_M1.MID as MID1,
				DAT_M2.MID as MID2,
				DAT_M2.M2_DVAL16,
				DAT_M2.M2_DVAL17
			FROM
				DAT_FILESTATUS
				join DAT_SHODAN
				  on DAT_FILESTATUS.SHODAN_ID = DAT_SHODAN.ID
				join DAT_M1
				  on DAT_FILESTATUS.MID1 = DAT_M1.MID
				join DAT_M2
				  on DAT_FILESTATUS.MID2 = DAT_M2.MID
				join DAT_M3
				  on DAT_M2.M2_DVAL15 = DAT_M3.MID
			WHERE
				DAT_M3.MID = '".$mid3."'
			GROUP BY
			  DAT_FILESTATUS.SHODAN_ID,
				DAT_FILESTATUS.MID1
			ORDER BY
				lpad(DAT_FILESTATUS.SHODAN_ID, 10, '0') desc,
				DAT_FILESTATUS.MID1
		";
//echo('<!--'.$StrSQL.'-->');

//  var_dump($StrSQL);
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs); 
//echo('<!--件数:'.$item.'-->');
		if($item=="") {
			$reccount=0;
			$pagestr="";
			// $strMain="<div class='result__item'><p class='result__ttl'>検索結果が見つかりませんでした。<p></div><div class='result__item'><p class='result__txt'>研究者の希望情報の登録はお済みでしょうか？検索結果の表示には研究者の希望情報の登録が必要になります。まだ未登録の場合は先にこちらから登録をお願いします。</p><div class='btn result__btn'><a href='/m_o2/?mode=new&sort=&word=[L-MID]&page=1'>研究者の希望情報を作成する</a></div></div>";
			$strMain="<div class='result__item'><p class='result__ttl'>検索結果が見つかりませんでした。<p></div>";
			$start = 0;
			$end = 0;
		} else {
			//================================================================================================
			//ページング処理
			//================================================================================================
			$reccount=mysqli_num_rows($rs);
			$pagecount=intval(($reccount-1)/$PageSize+1);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$start = ($page - 1) * $PageSize + 1;
			$end = $pagecount == $page ? $reccount - ($page - 1) * $PageSize : $start + $PageSize - 1;

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
				echo "<!--";
				var_dump($item);
				echo "-->";

				$str=$strM;

				$str=str_replace("[SHODAN_ID]",$item['SHODAN_ID'],$str);

				$str=str_replace("[TITLE]",'<a href="javascript:popup_file_btn(\''.$item['MID1'].'-'.$item['MID2'].'\',\''.$item['SHODAN_ID'].'\')">'.$item['TITLE'].'</a>',$str);
				//$str=str_replace("[TITLE]",$item['TITLE'],$str);

				$str=str_replace("[M1_DVAL01]",$item['M1_DVAL01'],$str);
				$str=str_replace("[M2_DVAL01]",$item['M2_DVAL01'].'<br>'.$item['M2_DVAL16'].' '.$item['M2_DVAL17'],$str);
				if(strpos($item['STATUS'], '完了') !== false) {
					$str=str_replace("[CATEGORY]",'完了',$str);
				}
				else if(strpos($item['STATUS'], 'キャンセル') !== false) {
					$str=str_replace("[CATEGORY]",'キャンセル',$str);
				}
				else if(strpos($item['STATUS'], '辞退') !== false) {
					if(strpos($item['STATUS'], '見積りの辞退') !== false){
						echo "<!--ステータス変更しない：".$item["SHODAN_ID"]."-->";
					}else{
						$str=str_replace("[CATEGORY]",'辞退',$str);
					}
				}


				if(strpos($item['STATUS'], '請求') !== false) {
					$str=str_replace("[CATEGORY]",'請求',$str);
				}
				else if(strpos($item['STATUS'], 'データ納品') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($item['STATUS'], '物品納品') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($item['STATUS'], '納品確認') !== false) {
					$str=str_replace("[CATEGORY]",'納品',$str);
				}
				else if(strpos($item['STATUS'], '受注承認') !== false) {
					$str=str_replace("[CATEGORY]",'実施中',$str);
				}
				else if(strpos($item['STATUS'], '発注承認') !== false) {
					//$str=str_replace("[CATEGORY]",'見積り',$str);
					$str=str_replace("[CATEGORY]",'発注承認済',$str);
				}
				else if(strpos($item['STATUS'], '発注依頼') !== false) {
					// 発注依頼の場合だけ特殊処理
					$str=str_replace("[CATEGORY]",'<span style="color:red;">発注依頼<br>(承認してください)</span>',$str);
				}
				else if(strpos($item['STATUS'], '見積り送付') !== false) {
					$str=str_replace("[CATEGORY]",'見積り',$str);
				}
				else if(strpos($item['STATUS'], '見積り依頼') !== false) {
					$str=str_replace("[CATEGORY]",'見積り',$str);
				}
				else if(strpos($item['STATUS'], '問い合わせ') !== false) {
					$str=str_replace("[CATEGORY]",'問い合わせ',$str);
				}
				else {
					$str=str_replace("[CATEGORY]",'',$str);
				}
				
				//if($item['SHODAN_ID'] == '44') {
				//echo($item['STATUS'].'<br>');
				//echo($item['FILESTATS_ID'].'<br>');
				//echo($item['M2_ID'].'<br>');
				//echo($item['M2_VERSION'].'<br>');
				//exit();
				//}
				
				$s1 = explode(',', $item['STATUS']);
				$f1 = explode(',', $item['FILESTATUS_ID']);
				$m1 = explode(',', $item['M2_ID']);
				$v1 = explode(',', $item['M2_VERSION']);
				$status_m = '';
				$status_n = '';
				foreach($s1 as $key => $s2) {
					if($s2 == '発注依頼') {
						$str=str_replace("[STATUS_H]",'<a href="/m_contact1/?type=発注依頼&mode=disp&key='.$f1[$key].'">〇</a>',$str);
						//$str=str_replace("[STATUS_H]",'〇',$str);
					}
					if($s2 == '請求') {
						$str=str_replace("[STATUS_S]",'<a href="/m_contact1/?type=請求&mode=disp&key='.$f1[$key].'">〇</a>',$str);
						//$str=str_replace("[STATUS_S]",'〇',$str);
					}
					if($s2 == 'データ納品') {
						$status_n .= '<div><a href="/m_contact1/?type=データ納品&mode=disp&key='.$f1[$key].'">〇（データ）</a></div>';
						//$status_n .= '<div>〇（データ）</div>';
					}
					if($s2 == '物品納品') {
						$status_n .= '<div><a href="/m_contact1/?type=物品納品&mode=disp&key='.$f1[$key].'">〇（物品）</a></div>';
						//$status_n .= '<div>〇（物品）</div>';
					}
					if($s2 == '見積り送付') {
						
						//// 見積り書は別SQLで取得しないとうまくとれない
						//$StrSQL="SELECT M2_ID,M2_VERSION FROM DAT_FILESTATUS where MID ='".$_SESSION['MID']."'";
						//$rs2=mysqli_query(ConnDB(),$StrSQL);
						//$item = mysqli_fetch_assoc($rs2);
						


						$status_m .= '<div><a href="/m_contact1/?type=見積り送付&mode=disp&key='.$f1[$key].'">〇（'.$m1[$key].' Version'.$v1[$key].'）</a></div>';
						//$status_m .= '<div>〇（'.$m1[$key].' Version'.$v1[$key].'）</div>';
					}
				}
				$str=str_replace("[STATUS_M]",$status_m,$str);
				$str=str_replace("[STATUS_N]",$status_n,$str);
				$str=str_replace("[STATUS_H]",'',$str);
				$str=str_replace("[STATUS_S]",'',$str);

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			} 
		} */


		$str=$strU.$strMain.$strD;

		$str = MakeHTML($str,0,$lid);

		$str=str_replace("[WORD2]",$word2,$str);
		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[WORD3]",str_replace("\t",',',$word),$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[SEL1]",$sel1,$str);
		$str=str_replace("[SEL2]",$sel2,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);
		$str=str_replace("[RECCOUNT]",$reccount,$str);
		$str=str_replace("[START]",$start,$str);
		$str=str_replace("[END]",$end,$str);

		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);

		$h1="";
		if($sort==1){
			$h1.="<option value=\"".MakeUrl(1, $word, $sel1, $sel2, 1)."\">更新日順</option>";
		} else {
			$h1.="<option value=\"".MakeUrl(1, $word, $sel1, $sel2, 1)."\">更新日順</option>";
		}
		if($sort==2){
			$h1.="<option value=\"".MakeUrl(2, $word, $sel1, $sel2, 1)."\">企業名順</option>";
		} else {
			$h1.="<option value=\"".MakeUrl(2, $word, $sel1, $sel2, 1)."\">企業名順</option>";
		}
		$str=str_replace("[SEL_SORT]",$h1,$str);

		// カテゴリー
		$tmp="";
		$cur_sel = '';
		$sel=explode("::", $FieldParam[63]."::");
		foreach(explode("\t",$word) as $val) {
			if(strpos($val,$FieldName[63]) !== false) {
				$cur_sel = $val;
				break;
			}
		}
		for($i=0; $i<count($sel); $i++){
			if($sel[$i]!=""){
				if(strstr($cur_sel, $sel[$i])==true){
					$tmp.="<option value=\"".$FieldName[63].":".$sel[$i]."\" selected>".$sel[$i]."</option>";
				} else {
					$tmp.="<option value=\"".$FieldName[63].":".$sel[$i]."\">".$sel[$i]."</option>";
				}
			}
		}
		$str=str_replace("[SEL_S1]",$tmp,$str);

		$tmp="";
		$sel=explode("::", $FieldParam[64]."::");
		for($i=0; $i<count($sel); $i++){
			if($sel[$i]!=""){
				if(strstr($sel2, $sel[$i])==true){
					$tmp.="<option value=\"".$FieldName[64].":".$sel[$i]."\" selected>".$sel[$i]."</option>";
				} else {
					$tmp.="<option value=\"".$FieldName[64].":".$sel[$i]."\">".$sel[$i]."</option>";
				}
			}
		}
		$str=str_replace("[SEL_S2]",$tmp,$str);

		for ($i=0; $i<=$FieldMax; $i=$i+1){
			$strtmp="";
			$tmp=explode("::",$FieldParam[$i]);
			for ($j=0; $j<count($tmp); $j++) {
				if(strstr($word, $tmp[$j])==true){
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"word[]\" value=\"".$FieldName[$i].":".$tmp[$j]."\" checked><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				} else {
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"word[]\" value=\"".$FieldName[$i].":".$tmp[$j]."\"><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
			}
			$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
		}

		$tmp="";
		/*
		if($sel1!=""){
			if($tmp!=""){
				$tmp.="、";
			}
			$tmp.=str_replace($FieldName[63].":", "", $sel1);
		}
		if($sel2!=""){
			if($tmp!=""){
				$tmp.="、";
			}
			$tmp.=str_replace($FieldName[64].":", "", $sel2);
		}
		*/
		if($word!=""){
			if($tmp!=""){
				$tmp.="、";
			}
			$val=$word;
			for($i=63; $i<=92; $i++){
				$val=str_replace($FieldName[$i].":", "", $val);
			}
			$tmp.=str_replace("\t", "、", $val);
		}
		if($word2!=""){
			$tmp.=$word2;
		}
		if($tmp!=""){
			$str=str_replace("[SEL_WORD]",$tmp,$str);
		} else {
			$str=str_replace("[SEL_WORD]","指定なし",$str);
		}

		// 2021.01.18 yamamoto 評価一覧
		$eval_list = GetEvalList($item['MID']);
		$str=str_replace("[D-O1_EVAL_LIST]",$eval_list,$str);

		// 契約フラグ
		$StrSQL="SELECT * FROM DAT_M2 where MID='".$_SESSION['MID']."';";
   	$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_m2 = mysqli_fetch_assoc($rs);
		if($item_m2['M2_DSEL02'] == 'M2_DSEL02:済') {
			$str=DispParamNone($str, "KEIYAKU-OFF");
		}
		else {
			$str=DispParam($str, "KEIYAKU-OFF");
		}

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
function MakeUrl($sort,$word, $sel1, $sel2,$page)
{

	return "/m_schedule3/?mode=list&sort=".urlencode($sort)."&word=".urlencode($word)."&page=".urlencode($page)."&sel1=".urlencode($sel1)."&sel2=".urlencode($sel2);

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
