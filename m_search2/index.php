<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_m1/config.php';

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
		$page_max=$_GET['PAGE_MAX'];
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
		$page_max=$_POST['PAGE_MAX'];
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

		//$StrSQL="SELECT * FROM DAT_O2 where (ENABLE='ENABLE:公開中' and ID=".$key.") or (MID='".$_SESSION['MID']."' and ID=".$key.")";
		$StrSQL="SELECT * FROM DAT_M1 where (ENABLE='ENABLE:公開中' and ID=".$key.") or (MID='".$_SESSION['MID']."' and ID=".$key.")";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($_SESSION['MID']=="" || $item<=0){
			$url=BASE_URL . "/login2/";
			header("Location: {$url}");
			exit;
		}

	} else {
		if($_SESSION['MID']==""){
			$url=BASE_URL . "/login2/";
			header("Location: {$url}");
			exit;
		}
	}

	switch ($mode){
	case "like":
		if($_SESSION['MID']!=""){
			//$StrSQL="SELECT * FROM DAT_O1 where ID=".$key.";";
			$StrSQL="SELECT * FROM DAT_M1 where ID=".$key.";";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);
			$midt=$item['MID'];
			//$oidt=$item['OID'];
			$mid=$_SESSION['MID'];
			if(strstr($midt,"M1")==true){
				//$StrSQL="DELETE FROM DAT_IINE where MID='".$mid."' and OIDT='".$item['oidt']."';";
				$StrSQL="DELETE FROM DAT_IINE where MID='".$mid."';";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
				//$StrSQL="INSERT INTO DAT_IINE (MID, MIDT, OIDT, NEWDATE) values ('".$mid."', '".$midt."', '".$oidt."', '".date('Y/m/d H:i:s')."')";
				$StrSQL="INSERT INTO DAT_IINE (MID, MIDT, NEWDATE) values ('".$mid."', '".$midt."', '".date('Y/m/d H:i:s')."')";
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

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$sel1,$sel2,$word2,$mid1,$page_max);

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
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$sel1,$sel2,$word2,$mid1,$page_max)
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

		// O1不使用
		/*
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
		*/

		// keyはM1のIDに変わる
		//$StrSQL="SELECT * FROM DAT_M1 where MID='".$mid1."'";
		$StrSQL="SELECT * FROM DAT_M1 where ID='".$key."'";
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

		/*
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
		*/

		/*
		// 2021.03.15 yamamoto 同属性のいいねとブラックリストの禁止
		if(mb_substr($_SESSION['MID'],0,2,"UTF-8")=="M1"){
			$str=str_replace("[ZOKUSEI]",'同属性',$str);
		}
		else {
			$str=str_replace("[ZOKUSEI]",'他属性',$str);
		}
		*/

		$str = MakeHTML($str,0,$lid);

		$str=str_replace("[KEY]",$key,$str);

		/*
		// 2021.01.18 yamamoto 評価一覧
		$eval_list = GetEvalList($mid1);
		$str=str_replace("[D-O1_EVAL_LIST]",$eval_list,$str);
		*/

		$str=str_replace("[BASE_URL]",BASE_URL,$str);

		/*
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
		*/

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

		$filename="../common/template/listo1.html";
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

			$StrSQL="
		  SELECT
			  DAT_M1.*
			FROM
			  DAT_M1
				/*
				left join DAT_O1
				  on DAT_M1.MID=DAT_O1.MID
				left join DAT_MATCH
				  on DAT_MATCH.OID1=DAT_O1.OID
					and DAT_MATCH.MID2='".$_SESSION['MID']."'
				*/
			where DAT_M1.ENABLE = 'ENABLE:公開中'
			  and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_M1.MID)
				## and DAT_M1.EMAIL != '".$_SESSION['EMAIL']."' /* 同じメアドのM1は表示しない */
			  and ".ListSQLSearch($sort,$word,$sel1,$sel2,$word2)."
		";

		//debug用の記述
		//$StrSQL="
		//  SELECT
		//	  DAT_M1.*
		//	FROM
		//	  DAT_M1
		//		/*
		//		left join DAT_O1
		//		  on DAT_M1.MID=DAT_O1.MID
		//		left join DAT_MATCH
		//		  on DAT_MATCH.OID1=DAT_O1.OID
		//			and DAT_MATCH.MID2='".$_SESSION['MID']."'
		//		*/
		//	where DAT_M1.ENABLE = 'ENABLE:公開中'
		//	  and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_M1.MID)
		//	  and ".ListSQLSearch($sort,$word,$sel1,$sel2,$word2)."
		//";
echo('<!--'.$StrSQL.'-->');

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
			
			echo "<!--mode:".$mode."-->";
			echo "<!--PageSize:".$PageSize."-->";
			echo "<!--page_max:".$page_max."-->";

			if($page_max!=""){
				$PageSize=$page_max;
			}

			$reccount=mysqli_num_rows($rs);
			$pagecount=intval(($reccount-1)/$PageSize+1);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$start = ($page - 1) * $PageSize + 1;
			//$end = $pagecount == $page ? $reccount - ($page - 1) * $PageSize : $start + $PageSize - 1;
			$end = $start+$PageSize-1;
			if($end > $reccount){
				$end=$reccount;
			}

			echo "<!--pagecount:".$pagecount."-->";
			echo "<!--page:".$page."-->";
			echo "<!--reccount:".$reccount."-->";
			echo "<!--end:".$end."-->";

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
					$str=$str." <a href=\"".MakeUrl($sort,$word,$sel1,$sel2,$i,$page_max)."\" class=\"inactive\">".$i."</a>";
				} 
			}
			$pagestr=$str;

			$default_items=array();
			$CurrentRecord=1;
			$strMain="";
			while ($item = mysqli_fetch_assoc($rs)) {

				$str=$strM;

				$default_items[]=$item["ID"];

				$str=DispO1($item, $str);
				$str=DispPoint1($item['OID'], $str);

				$StrSQL="SELECT * FROM DAT_M1 where MID='".$item['MID']."'";
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				$item2=mysqli_fetch_assoc($rs2);
				$str=DispM1($item2, $str);

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			}

			//ユーザがチェックを入れたアイテムのページ持越し処理
			//ユーザがチェックをいれたアイテムをチェックが入ってる限り残して表示
			echo "<!--checked_items:".$_COOKIE['checked_items']."-->";
			$checked_items=explode(",", $_COOKIE['checked_items']);
			//var_dump($checked_items);
			echo('<!--' . json_encode($checked_items, JSON_UNESCAPED_UNICODE). '-->');
			foreach ($checked_items as $m1_id) {
				// TODO
				if($m1_id == '') {
					continue;
				}
				
				//通常の検索結果に存在するものは表示しない
				if( !in_array($m1_id, $default_items) ){
					$str=$strM;

					$StrSQL="SELECT * FROM DAT_M1 where ID='".$m1_id."'";
					$rs2=mysqli_query(ConnDB(),$StrSQL);
					$item2=mysqli_fetch_assoc($rs2);
					$str=DispM1($item2, $str);

					$str=DispParamNone($str, "POINT");

					$strMain=$strMain.$str.chr(13);
				}
				
			}

		} 

		$str=$strU.$strMain.$strD;

		$str = MakeHTML($str,0,$lid);

		$str=str_replace("[WORD2]",$word2,$str);
		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);

		// 新カテゴリーに変更
		//$str=str_replace("[WORD3]",str_replace("\t",',',$word),$str);
		$str=str_replace("[WORD3]",
			(isset($_GET['M1_ETC17']) && $_GET['M1_ETC17'] != '' ? $_GET['M1_ETC17'] . 
				(isset($_GET['M1_ETC18']) && $_GET['M1_ETC18'] != '' ? '&gt;' . $_GET['M1_ETC18'] . 
					(isset($_GET['M1_ETC19']) && $_GET['M1_ETC19'] != '' ? '&gt;' . $_GET['M1_ETC19'] . 
						(isset($_GET['M1_ETC20']) && $_GET['M1_ETC20'] != '' ? '&gt;' . $_GET['M1_ETC20']
							: '')
						: '')
					: '')
				: '')
			,$str);

		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[SEL1]",$sel1,$str);
		$str=str_replace("[SEL2]",$sel2,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);
		$str=str_replace("[RECCOUNT]",$reccount,$str);
		$str=str_replace("[START]",$start,$str);
		$str=str_replace("[END]",$end,$str);

		if($_SESSION['MATT'] == "2"){

			$StrSQL="SELECT * from DAT_M2 where MID='".$_SESSION['MID']."'";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);
			if($item["M2_DRDO01"]=="M2_DRDO01:仮登録"){
				$str=DispParam($str, "KARITORKU");
				
			} else if($item["M2_DRDO01"]=="M2_DRDO01:本登録"){
				$str=DispParam($str, "HONTORKU");
			}
		}
		$str=DispParamNone($str, "KARITORKU");
		$str=DispParamNone($str, "HONTORKU");
		


		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);

		$h1="";
		if($sort==1){
			$h1.="<option value=\"".MakeUrl(1, $word, $sel1, $sel2, 1, $page_max)."\">更新日順</option>";
		} else {
			$h1.="<option value=\"".MakeUrl(1, $word, $sel1, $sel2, 1, $page_max)."\">更新日順</option>";
		}
		if($sort==2){
			$h1.="<option value=\"".MakeUrl(2, $word, $sel1, $sel2, 1, $page_max)."\">企業名順</option>";
		} else {
			$h1.="<option value=\"".MakeUrl(2, $word, $sel1, $sel2, 1, $page_max)."\">企業名順</option>";
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

	// ここでカテゴリーデータをロードして配列生成
	$cate_list = array();
	for($i = 1; $i <= 11; $i++) {
	$fp = fopen(__dir__ . '/../category_data/cate' . $i . '.csv', 'r');
	while ($row = fgetcsv($fp)) {
		if($row[0] == '第一階層') {
			continue;
		}
		if($row[0] != '') {
			$cate1 = $row[0];
			$cate_list[$cate1] = array();
		}
		if($row[1] != '') {
			$cate2 = $row[1];
			$cate = explode("\n", $cate2);
			foreach($cate as $val) {
				$cate_list[$cate1][$val] = array();
			}
		}
		if($row[2] != '') {
			$cate3 = $row[2];
			$cate = explode("\n", $cate3);
			foreach($cate as $val) {
				$cate_list[$cate1][$cate2][$val] = array();
			}
		}
		if($row[3] != '') {
			$cate4 = $row[3];
			$cate = explode("\n", $cate4);
			foreach($cate as $val) {
				$cate_list[$cate1][$cate2][$cate3][$val] = 1;
			}
		}
	}
	fclose($fp);
	}
	$str=str_replace("[CATE_LIST]",json_encode($cate_list,JSON_UNESCAPED_UNICODE),$str);
	$str=str_replace("[CATE1_VAL]",$_GET['M1_ETC17'],$str);
	$str=str_replace("[CATE2_VAL]",$_GET['M1_ETC18'],$str);
	$str=str_replace("[CATE3_VAL]",$_GET['M1_ETC19'],$str);
	$str=str_replace("[CATE4_VAL]",$_GET['M1_ETC20'],$str);

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
function MakeUrl($sort,$word, $sel1, $sel2,$page,$page_max)
{

	return "/m_search2/?mode=list&sort=".urlencode($sort)."&word=".urlencode($word)."&page=".urlencode($page)."&sel1=".urlencode($sel1)."&sel2=".urlencode($sel2)."&PAGE_MAX=".urlencode($page_max);

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
