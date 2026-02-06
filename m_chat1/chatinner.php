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

	//各テンプレートファイル名
	$htmllist = "list2.html";

	$rid=$_SESSION['MID'];

	$filename=$htmllist;

	$fp=$DOCUMENT_ROOT.$filename;
	$str=@file_get_contents($fp);

	// SQLインジェクション対策
	// 2020.12.23 yamamoto 案件ID(ETC02)で絞り込む
	$StrSQL="SELECT *, NEWDATE as LKDATE, ID as CID FROM DAT_MESSAGE where DAT_MESSAGE.AID='".$word."' and DAT_MESSAGE.ETC02='".$_GET['etc02']."' and DAT_MESSAGE.ENABLE='ENABLE:公開中' order by DAT_MESSAGE.ID;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	$chat_hash = date('YmdHis');
	if($item<=0) {
		$strMain="";
	} else {

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

		$CurrentRecord=1;
		$strMain="";
		while ($item = mysqli_fetch_assoc($rs)) {
			// 出す側を限定するメッセージ
			if($item['ETC03'] != '' && $item['ETC03'] != $_SESSION['MID']) {
				continue;
			}

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

				$StrSQL="UPDATE DAT_MESSAGE SET NOREAD='".$rid."' WHERE ID=".$item['ID']." and ETC02='".$_GET['etc02']."' and RID<>'".$_SESSION['MID']."'";
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
			if(strpos($item['COMMENT'], 'UPLOADED-FILE:') === false 
				&& strpos($item['COMMENT'], '>見積作成<') === false
				&& strpos($item['COMMENT'], '>発注書<') === false) {
				/*
				$item['COMMENT'] = preg_replace('/<a .*?>(.*?)<\/a>/', "", $item['COMMENT']);
				*/
			}
			$item['COMMENT'] = str_replace("\n\n", '', $item['COMMENT']);

			// システムメッセージを目立たせる
			$item['COMMENT'] = str_replace("問い合わせが送信されました", '<span class="system_msg">[Inquiry sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("問い合わせを送信しました", '<span class="system_msg">[Inquiry sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("再見積り依頼が送信されました", '<span class="system_msg">[Request for requote has been sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("再見積り依頼を送信しました", '<span class="system_msg">[Request for requote has been sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り依頼が送信されました", '<span class="system_msg">[Request for quote has been sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り依頼を送信しました", '<span class="system_msg">[Request for quote has been sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り書が送付されました", '<span class="system_msg">[Quote sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り書を送付しました", '<span class="system_msg">[Quote sent]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("決済者により発注依頼が承認されました", '<span class="system_msg">[Order request has been approved by the payer]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("決済者により発注依頼が否認されました", '<span class="system_msg">[Order request rejected by payer]</span>', $item['COMMENT']);

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
			if(trim($item2['M2_ETC20'])!=""){
				$tmp=str_replace("[LKNAME]",$item2['M2_ETC20'],$tmp);
			}else{
				$tmp=str_replace("[LKNAME]",$item2['M2_DVAL03'],$tmp);
			}
			//$tmp=str_replace("[LKNAME]",$item2['M2_DVAL03'],$tmp);
			$tmp=str_replace("[LKDATE]",htmlspecialchars($item['LKDATE']),$tmp);

			$strMain.=$tmp.chr(13);

			//$_SESSION["CHATID".$word]=$item['CID'];
			$_SESSION["CHATID_".$chat_hash]=$item['CID'];
		} 
	} 

	// ステータスによってシステムメッセージを差し入れる
	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$_GET['etc02'].";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_shodan = mysqli_fetch_assoc($rs);
	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$item_shodan['MID2']."';";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_m2 = mysqli_fetch_assoc($rs);
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$_GET['etc02']." and STATUS = '発注依頼' order by ID desc;";
	//echo('<!--'.$StrSQL.'-->');
	$h_rs=mysqli_query(ConnDB(),$StrSQL);
	$h_item = mysqli_fetch_assoc($h_rs);
	
	//決済者承認フラグの確認
	//決済者発注承認モードが必要かどうかの判断
	//「決済者が登録してある」＆「KESSAI_SYONIN==KESSAI_SYONIN:あり」の場合に、
	//「発注依頼」があったとき、決済者発注承認モードを経る
	if($item_m2["KESSAI_SYONIN"]=="KESSAI_SYONIN:あり"){
		$StrSQL="SELECT ID,MID FROM DAT_M3 where MID='".$item_m2["M2_DVAL15"]."' ";
		$StrSQL.=" and MID IS NOT NULL and MID!='' order by ID desc;";
		$kessai_rs=mysqli_query(ConnDB(),$StrSQL);
		$kessai_item = mysqli_fetch_assoc($kessai_rs);
		$kessai_num = mysqli_num_rows($kessai_rs);
	}else{
		$kessai_num=0;
	}
	//echo "<!--kessai_item:";
	//var_dump($kessai_item);
	//echo "-->";


	//分割払い（2回払い、マイルストーン払い）の処理
	//分割払いのときは、見積り送付でDAT_SHODANステータス更新は一端とまる
	$sys_comment = "";
	$item_num_div="";
	if($item_shodan['STATUS']=="見積り送付"){
		$StrSQL="SELECT * FROM DAT_SHODAN_DIV where SHODAN_ID='".$_GET['etc02']."' and SHODAN_ID!='' and SHODAN_ID IS NOT NULL;";
		$rs_div=mysqli_query(ConnDB(),$StrSQL);
		$item_num_div=mysqli_num_rows($rs_div);
		
		while( $item_div=mysqli_fetch_assoc($rs_div) ){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$_GET['etc02']." and STATUS = '発注依頼' ";
			$StrSQL.=" and DIV_ID='".$item_div["DIV_ID"]."' order by ID desc";
			//echo('<!--'.$StrSQL.'-->');
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item = mysqli_fetch_assoc($h_rs);
			
			$sys_comment = "";
			switch($item_div['STATUS']) {
				case '発注依頼':
					//受注承認できるのは運営のみに仕様変更のためコメントアウト
					//if($kessai_num>=1){
						$sys_comment='('.$item_div["DIV_ID"].') ';
						$sys_comment.='決済者による発注の承認待ちです。しばらくお待ちください。(日本語で仮表示)';
					//}
				break;
				case '決済者発注承認':
					//受注承認できるのは運営のみに仕様変更のためコメントアウト
					//$sys_comment='('.$item_div["DIV_ID"].') ';
					//$sys_comment.='An order has been received.
					//この注文を　<a href="javascript:window.parent.shodan_hacchu_ok(' . $_GET['etc02'] . ');">approve</a>　<a href="javascript:window.parent.shodan_cancel(' . $_GET['etc02'] . ');">decline</a>';
				break;
				case '受注承認':
					//$sys_comment = '受注が承認されました。納品の準備をしてください。
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='purchase order received.
					<a href="javascript:window.parent.open_mcontact2(\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$h_item['ID'].'\');">Check purchase order</a>
					';
				break;
				case '受注承認(前払い)':
						$sys_comment='('.$item_div["DIV_ID"].') ';
						$sys_comment.= '請求書の送付をお願いします。';
						$sys_comment.= '
						<a href="/m_contact1/?type=請求&sub_type=請求書送付(前払い)&mode=new&shodan_id='.$_GET['etc02'].'&param_div_id='.$item_div["DIV_ID"].'" target="_blank">Send invoice	</a>';
				break;
				case 'データ納品':
				case '物品納品':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='Delivered.';
				break;

				case '納品確認':
						$sys_comment='('.$item_div["DIV_ID"].') ';
						$sys_comment.= '研究者が納品を承認しました。請求書の送付をお願いします。';
						$sys_comment.= '
						<a href="/m_contact1/?type=請求&mode=new&shodan_id='.$_GET['etc02'].'&param_div_id='.$item_div["DIV_ID"].'" target="_blank">Send invoice	</a>';
				break;
				case '完了':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='This case has been closed. Thank you for using Scitentist3.';
				break;
				case '辞退':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='This deal has been declined. If you have mistakenly declined this opportunity, please contact the administrator.';
				break;
				case 'キャンセル':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='This opportunity has been cancelled.';
				break;
			}

			if($sys_comment != '') {
				$tmp="<div class=\"left_balloon system_balloon\"><div class=\"balloon_sub\"><span class=\"balloon_name\">SYSTEM MESSAAGE</span><span class=\"balloon_date\">[LKDATE]</span></div><p>[D-COMMENT]</p></div>";
				$tmp=str_replace("[LKDATE]",htmlspecialchars($item_div['EDITDATE']),$tmp);
				$tmp=str_replace("[D-COMMENT]",ChatText($sys_comment),$tmp);
				$strMain.=$tmp.chr(13);
			}
		}
	}

	$sys_comment = '';
	if($item_num_div==""){
		switch($item_shodan['STATUS']) {
			case '発注依頼':
				//受注承認できるのは運営のみに仕様変更のためコメントアウト
				//if($kessai_num>=1){
					$sys_comment='決済者による発注の承認待ちです。しばらくお待ちください。(日本語で仮表示)';
				//}
				break;
			case '決済者発注承認':
				//受注承認できるのは運営のみに仕様変更のためコメントアウト
				//$sys_comment = 'An order has been received.
				//	この注文を　<a href="javascript:window.parent.shodan_hacchu_ok(' . $_GET['etc02'] . ');">approve</a>　<a href="javascript:window.parent.shodan_cancel(' . $_GET['etc02'] . ');">decline</a>';
				break;
			case '受注承認':
				//$sys_comment = '受注が承認されました。納品の準備をしてください。
				$sys_comment = 'purchase order received.
				<a href="javascript:window.parent.open_mcontact2(\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$h_item['ID'].'\');">Check purchase order</a>
				';
				break;
			case '受注承認(一括前払い)':
				$sys_comment = '受注が承認されました。';
				$sys_comment.= '
						<a href="/m_contact1/?type=請求&mode=new&shodan_id='.$_GET['etc02'].'" target="_blank">Send invoice	</a>';
				break;
			case 'データ納品':
			case '物品納品':
			case 'サプライヤーが納品(一括前払い)';
				$sys_comment = 'Delivered.';
				break;
			case '研究者が納品承認(一括前払い)':
				$sys_comment = '研究者が納品を承認しました。';
				break;
			case '納品確認':
				$sys_comment = '研究者が納品を承認しました。請求書の送付をお願いします。';
				$sys_comment.= '
						<a href="/m_contact1/?type=請求&mode=new&shodan_id='.$_GET['etc02'].'" target="_blank">Send invoice	</a>';
				break;
			/*
			case '納品確認':
				$sys_comment = $item_m2['M2_DVAL03'] . 'が納品を確認しました。';
				break;
			*/
			case '完了':
				$sys_comment = 'This case has been closed. Thank you for using Scitentist3.';
				break;
			case '辞退':
				$sys_comment = 'This deal has been declined. If you have mistakenly declined this opportunity, please contact the administrator.';
				break;
			case 'キャンセル':
				$sys_comment = 'This opportunity has been cancelled.';
				break;
		}
	}

	

	/*
	$sys_comment = '';
	switch($item_shodan['STATUS']) {
		// 発注依頼の段階ではサプライヤーに通知なし
		// 発注承認で通知する
		case '決済者発注承認':
			$sys_comment = 'An order has been received.
				この注文を　<a href="javascript:window.parent.shodan_hacchu_ok(' . $_GET['etc02'] . ');">approve</a>　<a href="javascript:window.parent.shodan_cancel(' . $_GET['etc02'] . ');">decline</a>';
			break;
		case '受注承認':
			//$sys_comment = '受注が承認されました。納品の準備をしてください。
			$sys_comment = 'purchase order received.
			<a href="javascript:window.parent.open_mcontact2(\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$h_item['ID'].'\');">Check purchase order</a>
			';
			break;
		case 'データ納品':
		case '物品納品':
			$sys_comment = 'Delivered.';
			break;

		//case '納品確認':
		//	$sys_comment = $item_m2['M2_DVAL03'] . 'が納品を確認しました。';
		//	break;
		
		case '完了':
			$sys_comment = 'This case has been closed. Thank you for using Scitentist3.';
			break;
		case '辞退':
			$sys_comment = 'This deal has been declined. If you have mistakenly declined this opportunity, please contact the administrator.';
			break;
		case 'キャンセル':
			$sys_comment = 'This opportunity has been cancelled.';
			break;
	}
	*/
	if($sys_comment != '') {
		$tmp="<div class=\"left_balloon system_balloon\"><div class=\"balloon_sub\"><span class=\"balloon_name\">SYSTEM MESSAAGE</span><span class=\"balloon_date\">[LKDATE]</span></div><p>[D-COMMENT]</p></div>";
		$tmp=str_replace("[LKDATE]",htmlspecialchars($item_shodan['EDITDATE']),$tmp);
		$tmp=str_replace("[D-COMMENT]",ChatText($sys_comment),$tmp);
		$strMain.=$tmp.chr(13);
	}

	$str=str_replace("[INNER]",$strMain,$str);

	$str = MakeHTML($str,1,$lid);

	$str=str_replace("[AID]",$word,$str);
	$str=str_replace("[ETC02]",$_GET['etc02'],$str);
	$str=str_replace("[CHAT_HASH]",$chat_hash,$str);

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
