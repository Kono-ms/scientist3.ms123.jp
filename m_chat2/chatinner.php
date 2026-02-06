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
		$comment_back = '';
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
				$tmp="<div class=\"right_balloon\"><div class=\"balloon_div_left\"><img src=\"" . $img_m2 . "\"/></div><div class=\"balloon_div_right\"><div class=\"balloon_sub\"><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></div><p>[D-COMMENT]</p></div></div>";
				//$tmp="<div class=\"right_balloon\"><p>[D-COMMENT]<span class=\"balloon_sub\"><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></p></div>";
			} else {
				$tmp="<div class=\"left_balloon\"><div class=\"balloon_div_left\"><img src=\"" . $img_m1 . "\"/></div><div class=\"balloon_div_right\"><div class=\"balloon_sub\"><span class=\"balloon_name\">[LKNAME]</span><span class=\"balloon_date\">[LKDATE]</span><span class=\"balloon_kidoku\">[KIDOKU]</span></div><p>[D-COMMENT]</p></div></div>";
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

			// 2020.12.02 yamamoto
			// ETC01に対応する依頼IDが入っているので
			// STATUSが「STATUS:申込」か「STATUS:金額変更」以外のときはアンカーを削除する
			$StrSQL_mcontact="SELECT * FROM DAT_MCONTACT where ID='".$item['ETC01']."' and (STATUS='STATUS:申込' or STATUS='STATUS:金額変更');";
			$rs_mcontact=mysqli_query(ConnDB(),$StrSQL_mcontact);
			$item_mcontact=mysqli_num_rows($rs_mcontact);
			if($item_mcontact==0) {
				// 2020.12.22 yamamoto ファイル添付の場合はAタグを残す
				if(strpos($item['COMMENT'], 'UPLOADED-FILE:') === false 
				&& strpos($item['COMMENT'], '>見積書<') === false
				&& strpos($item['COMMENT'], '>納品書<') === false
				&& strpos($item['COMMENT'], '>請求書<') === false
				) {
					/*
					$item['COMMENT'] = preg_replace('/<a .*?>(.*?)<\/a>/', "", $item['COMMENT']);
					*/
					$item['COMMENT'] = str_replace("\n\n", '', $item['COMMENT']);
				}
			}

			// システムメッセージを目立たせる
			$item['COMMENT'] = str_replace("問い合わせが送信されました", '<span class="system_msg">[問い合わせが送信されました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("問い合わせを送信しました", '<span class="system_msg">[問い合わせを送信しました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("再見積り依頼が送信されました", '<span class="system_msg">[再見積り依頼 が送信されました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("再見積り依頼を送信しました", '<span class="system_msg">[再見積り依頼 を送信しました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り依頼が送信されました", '<span class="system_msg">[見積り依頼が送信されました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り依頼を送信しました", '<span class="system_msg">[見積り依頼を送信しました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り書が送付されました", '<span class="system_msg">[見積り書が送付されました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("見積り書を送付しました", '<span class="system_msg">[I have sent the 見積り書]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("決済者により発注依頼が承認されました", '<span class="system_msg">[決済者により発注依頼が承認されました]</span>', $item['COMMENT']);
			$item['COMMENT'] = str_replace("決済者により発注依頼が否認されました", '<span class="system_msg">[決済者により発注依頼が否認されました]</span>', $item['COMMENT']);

			$tmp=str_replace("[D-COMMENT]",ChatText($item['COMMENT']),$tmp);

			if($item['NOREAD']!=""){
				$tmp=str_replace("[KIDOKU]","既読",$tmp);
			} else {
				$tmp=str_replace("[KIDOKU]","",$tmp);
			}

			$rrid=str_replace("-", "", str_replace($rid, "", $item['AID']));
			$StrSQL="SELECT * FROM DAT_M1 where MID='".$rrid."';";
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			$item2 = mysqli_fetch_assoc($rs2);
			$tmp=str_replace("[LKNAME]",$item2['M1_DVAL01'],$tmp);
			$tmp=str_replace("[LKDATE]",htmlspecialchars($item['LKDATE']),$tmp);

			$strMain.=$tmp.chr(13);

			//$_SESSION["CHATID".$word]=$item['CID'];
			$_SESSION["CHATID_".$chat_hash]=$item['CID'];


			$comment_back = $item['COMMENT'];
		} 
	} 

	// ステータスによってシステムメッセージを差し入れる
	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$_GET['etc02'].";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_shodan = mysqli_fetch_assoc($rs);
	// FILESTATUS情報を無理やり取得
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$_GET['etc02']." ";
	$StrSQL.=" and (STATUS='請求' or STATUS='請求書送付(一括前払い)' or STATUS='請求書送付(前払い)') and S_STATUS='請求（研究者）' ORDER BY ID DESC;";
	//echo('<!--請求：'.$StrSQL.'-->');
	$s_rs=mysqli_query(ConnDB(),$StrSQL);
	$s_item = mysqli_fetch_assoc($s_rs);
	//echo "<!--s_item:";
	//var_dump($s_item);
	//echo "-->";
	// m2情報
	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$item_shodan['MID2']."';";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_m2 = mysqli_fetch_assoc($rs);

	//分割払い（2回払い、マイルストーン払い）の処理
	//分割払いのときは、見積り送付でDAT_SHODANステータス更新は一端とまる
	$sys_comment = "";
	$item_num_div="";
	if($item_shodan['STATUS']=="見積り送付"){
		$StrSQL="SELECT * FROM DAT_SHODAN_DIV where SHODAN_ID='".$_GET['etc02']."' and SHODAN_ID!='' and SHODAN_ID IS NOT NULL;";
		$rs_div=mysqli_query(ConnDB(),$StrSQL);
		$item_num_div=mysqli_num_rows($rs_div);

		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$_GET['etc02']."' "; 
		$StrSQL.=" and (STATUS = '請求' or STATUS = '請求書送付(一括前払い)' or STATUS = '請求書送付(前払い)') and S_STATUS='請求（研究者）'";
		$StrSQL.=" order by ID desc";
		
		//echo('<!--seikyu:'.$StrSQL.'-->');
		//$s_rs=mysqli_query(ConnDB(),$StrSQL);
		//$s_item = mysqli_fetch_assoc($s_rs);
		//echo "<!--seikyu:";
		//var_dump($s_item);
		//echo "-->";
		
		while( $item_div=mysqli_fetch_assoc($rs_div) ){
			
			$sys_comment = "";
			switch($item_div['STATUS']) {
				case '発注依頼':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='決済者による発注の承認待ちです。しばらくお待ちください。';
				break;
				case '決済者発注承認':
					// 決済者機能から承認否認するとメッセージが重複する
					if(strpos($comment_back, '決済者により発注依頼が承認されました') === false) {
						$sys_comment='('.$item_div["DIV_ID"].') ';
						$sys_comment.='発注依頼が承認されました
						サプライヤーからの受注承認をお待ちください。
						';
					}
					break;
				case '発注否認':
					// 決済者機能から承認否認するとメッセージが重複する
					if(strpos($comment_back, '決済者により発注依頼が否認されました') === false) {
						$sys_comment='('.$item_div["DIV_ID"].') ';
						$sys_comment.='発注が否認されました';
					}
				break;

				case '受注承認':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='受注が承認されました。サプライヤーからの納品をお待ちください。
					';
				break;
				case '受注承認(前払い)':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='受注が承認されました。
					';
				break;

				case 'データ納品':
				case '物品納品':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='サプライヤーから納品されました。';
				break;
				case '納品確認':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='納品を確認、承認しました。';
				break;
				case '請求':
				case '請求書送付(前払い)':
					//$sys_comment = 'ご請求させていただきますので、ご確認のほどよろしくお願いいたします。
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.=$s_item['S2_MESSAGE'] . '
					<a href="javascript:window.parent.open_mcontact2(\'/m_contact2/?type=請求&mode=disp_frame&key='.$s_item['ID'].'\');">請求書</a>
					';
				break;
				case '完了':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='本案件はクローズしました。この度はScitentist3をご利用いただきありがとうございました。';
				break;
				case '辞退':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='この商談は辞退されました。';
				break;
				case 'キャンセル':
					$sys_comment='('.$item_div["DIV_ID"].') ';
					$sys_comment.='この商談はキャンセルされました。誤ってキャンセルした場合には管理者にご連絡ください。';
				break;
			}
			if($sys_comment != '') {
				$tmp="<div class=\"left_balloon system_balloon\"><div class=\"balloon_sub\"><span class=\"balloon_name\">システムメッセージ</span><span class=\"balloon_date\">[LKDATE]</span></div><p>[D-COMMENT]</p></div>";
				$tmp=str_replace("[LKDATE]",htmlspecialchars($item_div['EDITDATE']),$tmp);
				$tmp=str_replace("[D-COMMENT]",ChatText($sys_comment),$tmp);
				$strMain.=$tmp.chr(13);
			}
		}
	}

	$sys_comment = '';
	if($item_num_div==""){
		switch($item_shodan['STATUS']) {
			case '見積り送付':
				if($item_num_div=="" || $item_num_div==0){
					$sys_comment='';
				}
			break;
			case '発注依頼':
				$sys_comment = '決済者による発注の承認待ちです。しばらくお待ちください。';
				break;
			case '決済者発注承認':
				// 決済者機能から承認否認するとメッセージが重複する
				if(strpos($comment_back, '決済者により発注依頼が承認されました') === false) {
					$sys_comment = '発注依頼が承認されました
						サプライヤーからの受注承認をお待ちください。
					';
				}
				break;
			case '発注否認':
				// 決済者機能から承認否認するとメッセージが重複する
				if(strpos($comment_back, '決済者により発注依頼が否認されました') === false) {
					$sys_comment = '発注が否認されました';
				}
				break;
	
			case '受注承認':
				$sys_comment = '受注が承認されました。サプライヤーからの納品をお待ちください。
				';
				break;

			case '受注承認(一括前払い)':
				$sys_comment = '受注が承認されました。
				';
				break;
	
			case 'データ納品':
			case '物品納品':
			case 'サプライヤーが納品(一括前払い)';
				$sys_comment = 'サプライヤーから納品されました。';
				break;
			case '納品確認':
			case '研究者が納品承認(一括前払い)':
				$sys_comment = '納品を確認、承認しました。';
				break;
			case '請求':
			case '請求書送付(一括前払い)':
				//$sys_comment = 'ご請求させていただきますので、ご確認のほどよろしくお願いいたします。
				$sys_comment = $s_item['S2_MESSAGE'] . '
		    	<a href="javascript:window.parent.open_mcontact2(\'/m_contact2/?type=請求&mode=disp_frame&key='.$s_item['ID'].'\');">請求書</a>
				';
				break;
			case '完了':
				$sys_comment = '本案件はクローズしました。この度はScitentist3をご利用いただきありがとうございました。';
				break;
			case '辞退':
				$sys_comment = 'この商談は辞退されました。';
							break;
			case 'キャンセル':
				$sys_comment = 'この商談はキャンセルされました。誤ってキャンセルした場合には管理者にご連絡ください。';
							break;
		}
	}

	/*
	$sys_comment = '';
	switch($item_shodan['STATUS']) {
		case '発注依頼':
			$sys_comment = '決済者による発注の承認待ちです。しばらくお待ちください。';
			break;
		case '決済者発注承認':
			// 決済者機能から承認否認するとメッセージが重複する
			if(strpos($comment_back, '決済者により発注依頼が承認されました') === false) {
				$sys_comment = '発注依頼が承認されました
					サプライヤーからの受注承認をお待ちください。
				';
			}
			break;
		case '発注否認':
			// 決済者機能から承認否認するとメッセージが重複する
			if(strpos($comment_back, '決済者により発注依頼が否認されました') === false) {
				$sys_comment = '発注が否認されました';
			}
			break;

		case '受注承認':
			$sys_comment = '受注が承認されました。サプライヤーからの納品をお待ちください。
			';
			break;

		case 'データ納品':
		case '物品納品':
			$sys_comment = 'サプライヤーから納品されました。';
			break;
		case '納品確認':
			$sys_comment = '納品を確認、承認しました。';
			break;
		case '請求':
			//$sys_comment = 'ご請求させていただきますので、ご確認のほどよろしくお願いいたします。
			$sys_comment = $s_item['S2_MESSAGE'] . '
	    	<a href="javascript:window.parent.open_mcontact2(\'/m_contact2/?type=請求&mode=disp_frame&key='.$s_item['ID'].'\');">請求書</a>
			';
			break;
		case '完了':
			$sys_comment = '本案件はクローズしました。この度はScitentist3をご利用いただきありがとうございました。';
			break;
		case '辞退':
			$sys_comment = 'この商談は辞退されました。';
						break;
		case 'キャンセル':
			$sys_comment = 'この商談はキャンセルされました。誤ってキャンセルした場合には管理者にご連絡ください。';
						break;
	}
	*/
	if($sys_comment != '') {
		$tmp="<div class=\"left_balloon system_balloon\"><div class=\"balloon_sub\"><span class=\"balloon_name\">システムメッセージ</span><span class=\"balloon_date\">[LKDATE]</span></div><p>[D-COMMENT]</p></div>";
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
