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

	global $contact_id;
	global $contact_status;
	global $TITLE;

	eval(globals());

	if($_POST['mode']!=""){
		$mode=$_POST['mode'];
		$word=$_POST['word'];
		$mid1=$_POST['mid1'];
		$mid2=$_POST['mid2'];
		$etc02=$_POST['etc02'];
	} else {
		$mode=$_GET['mode'];
		$word=$_GET['word'];
		$mid1=$_GET['mid1'];
		$mid2=$_GET['mid2'];
		$etc02=$_GET['etc02'];
	}

	if($_SESSION['MID']==""){
		$url=BASE_URL . "/login2/";
		header("Location: {$url}");
		exit;
	}

	// 2020.12.17 yamamoto クリエイターがchat2を開くのを阻止する
	if($_SESSION['MID']==$mid1){
		$url=BASE_URL . "/login2/";
		header("Location: {$url}");
		exit;
	}

	$rid=$_SESSION['MID'];

	if($mode==""){
		$mode="list";
	}

	// 2020.12.17 yamamoto タイトルをメッセージから取得
	/*
	$TITLE = '(タイトルが入ります)';
	$StrSQL="SELECT COMMENT FROM DAT_MESSAGE where AID='".$word."' and ETC02='".$_GET['etc02']."' and COMMENT like '[Change title]%' order by ID desc;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	if ($rs==true) {
		$item = mysqli_fetch_assoc($rs);
		$preg = preg_match_all('/タイトル「.+?」/i', $item['COMMENT'], $match);
		for($i = 0; $i < count($match[0]); $i++) {
			$tmp = str_replace('タイトル「', '', $match[0][$i]);
			$TITLE = str_replace('」', '', $tmp);
		}
	}

	// 2020.12.02 yamamoto 金額提示状況の取得
	$contact_status = '商談中';
	$StrSQL="SELECT ID,STATUS FROM DAT_MCONTACT where MID='".$mid1."' and MIDT='".$mid2."' and ETC02='".$_GET['etc02']."' order by ID desc;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$contact_id = '';
	if ($rs==true) {
		$item = mysqli_fetch_assoc($rs);
		if($item['STATUS'] == 'STATUS:申込' || $item['STATUS'] == 'STATUS:金額変更') {
			$contact_id = $item['ID'];
			$contact_status = '金額提示中';
		}
		else if($item['STATUS'] == 'STATUS:承認') {
			$contact_status = '金額承認済';
		}
		else if($item['STATUS'] == 'STATUS:否認') {
			$contact_status = '金額否認';
		}
		else if($item['STATUS'] != '') {
			$contact_status = str_replace('STATUS:', '', $item['STATUS']);
		}
	}


	// 2020.11.06 yamamoto 承認
	if ($mode=="presentation_ok"){
		// 2020.11.16 yamamoto
		// STATUSが「STATUS:承認」のときはなにもしない
		// 2020.12.23 yamamoto 案件ID(ETC02)追加
		$StrSQL_mcontact="SELECT * FROM DAT_MCONTACT where ID='".$_GET['contact_id']."';";
		$rs_mcontact=mysqli_query(ConnDB(),$StrSQL_mcontact);
		$item_mcontact = mysqli_fetch_assoc($rs_mcontact);
		if($item_mcontact['STATUS'] != 'STATUS:承認') {
			$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
			$StrSQL.="'".$word."',";
			$StrSQL.="'".$rid."',";
			$StrSQL.="'ENABLE:公開中',";
			$StrSQL.="'".date("Y/m/d H:i:s")."',";
			$StrSQL.="'[" . str_replace('STATUS:', '', $item_mcontact['STATUS']) . "]を承諾しました。',";
			$StrSQL.="'".$_GET['etc02']."'";
			$StrSQL.=")";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

			$StrSQL="UPDATE DAT_MCONTACT SET ";
			$StrSQL.="STATUS = 'STATUS:承認',";
			$StrSQL.="EDITDATE = '".date("Y/m/d H:i:s")."' ";
			$StrSQL.="WHERE ID= ".$_GET['contact_id'];
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

			// メール送信
			$mids = explode(',', $word);
			SendMail($mids[0], $item_mcontact['STATUS'], '承諾');
			SendMail($mids[1], $item_mcontact['STATUS'], '承諾');

			$mode="list";
		} // すでに回答済みでないかどうか
	}

	// 2020.11.16 yamamoto 否認
	if ($mode=="presentation_cancel"){
		// 2020.11.16 yamamoto
		// STATUSが「STATUS:否認」のときはなにもしない
		// 2020.12.23 yamamoto 案件ID(ETC02)追加
		$StrSQL_mcontact="SELECT * FROM DAT_MCONTACT where ID='".$_GET['contact_id']."';";
		$rs_mcontact=mysqli_query(ConnDB(),$StrSQL_mcontact);
		$item_mcontact = mysqli_fetch_assoc($rs_mcontact);
		if($item_mcontact['STATUS'] != 'STATUS:否認') {
			$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
			$StrSQL.="'".$word."',";
			$StrSQL.="'".$rid."',";
			$StrSQL.="'ENABLE:公開中',";
			$StrSQL.="'".date("Y/m/d H:i:s")."',";
			$StrSQL.="'[" . str_replace('STATUS:', '', $item_mcontact['STATUS']) . "]を否認しました。',";
			$StrSQL.="'".$_GET['etc02']."'";
			$StrSQL.=")";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

			$StrSQL="UPDATE DAT_MCONTACT SET ";
			$StrSQL.="STATUS = 'STATUS:否認',";
			$StrSQL.="EDITDATE = '".date("Y/m/d H:i:s")."' ";
			$StrSQL.="WHERE ID= ".$_GET['contact_id'];
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

			// メール送信
			$mids = explode(',', $word);
			SendMail($mids[0], $item_mcontact['STATUS'], '否認');
			SendMail($mids[1], $item_mcontact['STATUS'], '否認');

			$mode="list";
		} // すでに回答済みでないかどうか
	}
	*/

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

		// 2020.12.23 yamamoto 案件ID(ETC02)追加
		if(trim(str_replace("'","''",htmlspecialchars($_POST['COMMENT'])))!="" || $file_msg != ''){
			$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
			$StrSQL.="'".$word."',";
			$StrSQL.="'".$rid."',";
			$StrSQL.="'ENABLE:公開中',";
			$StrSQL.="'".date("Y/m/d H:i:s")."',";
			$StrSQL.="'".str_replace("'","''",htmlspecialchars($_POST['COMMENT'])).$file_msg."',";
			$StrSQL.="'".$_GET['etc02']."'";
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

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2,$etc02);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($mid, $status, $mode)
{

	eval(globals());

	$status =  str_replace('STATUS:', '', $status);

	// 相手のメールアドレスを取得
	$tbl = 'DAT_M' . ($_SESSION['MATT'] == '1' ? '2' : '1');

	$StrSQL="SELECT EMAIL FROM $tbl where MID='".$mid."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	//$fp="mail.txt";
	//$MailBody=@file_get_contents($fp) . $item['EMAIL'];
	$maildata = GetMailTemplate('提示の承認/拒否');
	$MailBody = $maildata['BODY'];

	$MailBody=str_replace("[STATUS]",$status,$MailBody);
	$MailBody=str_replace("[MODE]",$mode,$MailBody);
	$MailBody=str_replace("[DATE]",date('Y-m-d H:i:s'),$MailBody);

	$subject = $maildata['TITLE'];
	$subject=str_replace("[STATUS]",$status,$subject);
	$subject=str_replace("[MODE]",$mode,$subject);

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	//mb_send_mail($item['EMAIL'], "【Scientist3】[" . $status . "]が" . $mode . "されました", $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3事務局","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 
	//mb_send_mail('info@msc-dev.com', "【Scientist3】[" . $status . "]が" . $mode . "されました", $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3事務局","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 

	mb_send_mail($item['EMAIL'], $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail("info@msc-dev.com", $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
}

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2,$etc02)
{

	eval(globals());

	//各テンプレートファイル名
	$htmllist = "list.html";

	$fp=$DOCUMENT_ROOT.$htmllist;
	$str=@file_get_contents($fp);

	$StrSQL="UPDATE DAT_MESSAGE SET NOREAD='".$_SESSION['MID']."' WHERE AID='".$word."' and ETC02='".$etc02."' and RID<>'".$_SESSION['MID']."'";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	$str = MakeHTML($str,1,$lid);

	$StrSQL="SELECT * FROM DAT_M1 where MID='".$mid1."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$str=DispM1($item, $str);

	$str=str_replace("[MID1]",$mid1,$str);
	$str=str_replace("[MID2]",$mid2,$str);
	$str=str_replace("[AID]",$word,$str);

	// 2020.12.02 yamamoto 金額提示状況
	global $contact_id;
	global $contact_status;
	global $TITLE;
	$str=str_replace("[CONTACT_ID]",$contact_id,$str);
	$str=str_replace("[CONTACT_STATUS]",$contact_status,$str);
	//$str=str_replace("[TITLE]",$TITLE,$str);
	$str=str_replace("[ETC02]",$_GET['etc02'],$str); // 商談ID

	$StrSQL="SELECT * from DAT_M2 where MID='".$_SESSION['MID']."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemM2 = mysqli_fetch_assoc($rs);

	// ヘッダ部分のボタン制御
	$StrSQL="SELECT * FROM DAT_SHODAN where ID='".$_GET['etc02']."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_shodan = mysqli_fetch_assoc($rs);



	//分割払い（2回払い、マイルストーン払い）の処理
	//分割払いのときは、見積り送付でDAT_SHODANステータス更新は一端とまる
	$btn_area = '';
	$item_num_div="";
	if($item_shodan['STATUS']=="見積り送付"){
		//発注依頼
		$StrSQL="SELECT * FROM DAT_SHODAN_DIV where SHODAN_ID='".$_GET['etc02']."' and SHODAN_ID!='' and SHODAN_ID IS NOT NULL;";
		$rs_div=mysqli_query(ConnDB(),$StrSQL);
		$item_num_div=mysqli_num_rows($rs_div);

		$btn_area="";
		$tmp_status=array();
		while( $item_div=mysqli_fetch_assoc($rs_div) ){
			//分割見積り時のPart0（各分割を合体したデータ）の処理
			//Part0は表示しない
			$div_id=$item_div['DIV_ID'];
			$tmp="";
			$tmp=explode("-", $div_id);
			echo "<!--";
			var_dump($tmp);
			echo "-->";
			if(count($tmp)==3 && $tmp[2]!=""){
				if($tmp[2]=="Part0"){
					continue;
				}
			}
			
			$tmp_status[]=$item_div['STATUS'];
			switch($item_div['STATUS']) {
				case '見積り送付':
				case '運営手数料追加':
					$btn_area.= '
						<div>
							<span style="color:tomato;">'.$item_div['STATUS'].'('.$item_div["DIV_ID"].')操作:</span>
						</div>
						';
					//以下の発注を依頼するボタンを、個別に表示しないという指示により非表示。
					//if($itemM2["M2_DRDO01"]=="M2_DRDO01:仮登録"){
					//	$btn_area.= '
					//	<div>
					//		<span style="color:tomato;">'.$item_div['STATUS'].'('.$item_div["DIV_ID"].')操作:</span>
					//		<a href="javascript:void(0)" onclick="alert(\'仮登録中のため、発注できません。\')">発注を依頼する</a>
					//	</div>
					//	';
					//
					//} else {
					//	$btn_area.= '
					//	<div>
					//		<span style="color:tomato;">'.$item_div['STATUS'].'('.$item_div["DIV_ID"].')操作:</span>
					//		<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" 
					//		target="_parent">発注を依頼する</a>
					//	</div>
					//	';
					//}

				break;

				case '発注依頼':
				case '決済者発注承認':
				case '受注承認':
				$btn_area.= '
						<div>
							<span style="color:tomato;">'.$item_div['STATUS'].'('.$item_div["DIV_ID"].')操作:</span>
						</div>
					';
				break;

				case 'データ納品':
				case '物品納品':
					$btn_area.= '
					<div>
						<span style="color:tomato;">'.$item_div['STATUS'].'('.$item_div["DIV_ID"].')操作:</span>
						<a href="/m_contact2/?type=納品確認&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'&param_div_id='.$item_div["DIV_ID"].'" target="_blank">
							納品を承認する
						</a>
					</div>
					';
					//$btn_area.= '
					//<div>
					//	<span style="color:tomato;">'.$item_div['STATUS'].'('.$item_div["DIV_ID"].')操作:</span>
					//	<a href="javascript:shodan_nohin('.$_GET['etc02'].',\''.$item_div["DIV_ID"].'\');">納品を承認する</a>
					//</div>
					//';
				break;
				
			}
		}

		//共通ボタン
		if( in_array("見積り送付", $tmp_status) ||
				in_array("運営手数料追加", $tmp_status) ){
			if($itemM2["M2_DRDO01"]=="M2_DRDO01:仮登録"){
				$btn_area.= '
				<div>
					<span style="color:tomato;">共通操作:</span>
					<a href="javascript:void(0)" onclick="alert(\'仮登録中のため、発注できません。\')">発注を依頼する</a>
				</div>
				';
			
			} else {
				//発注依頼
				//div_idは、m_contact2で発注依頼の「H_M2_ID」に対応するdiv_idを
				//m_contact2で取得しているので、ここで渡していない。
				$btn_area.= '
				<div>
					<span style="color:tomato;">共通操作:</span>
					<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" 
					target="_parent">発注を依頼する</a>
				</div>
				';
			}
		}
		if(//in_array("見積り送付", $tmp_status) ||
				//in_array("運営手数料追加", $tmp_status) ||
				in_array("発注依頼", $tmp_status) ||
				in_array("決済者発注承認", $tmp_status) ||
				in_array("受注承認", $tmp_status) ){
			$btn_area.= '
					<div>
						<span style="color:tomato;">共通操作:</span>
						<a href="/m_contact2/?type=キャンセル依頼&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">発注キャンセル</a>
						<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
					</div>
					';
		}

	}

	//echo "<!--ID:".$item_shodan["ID"].", status:".$item_shodan["STATUS"]."-->";

	//$btn_area = '';
	switch($item_shodan['STATUS']) {
		case '問い合わせ':
			$btn_area = '
        <div>
					<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">見積りを依頼する</a>
        </div>
        <div>
        			<a href="/m_contact2/?type=案件の取り下げ&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">キャンセル</a>
        </div>
			';
			break;

		case '見積り依頼':
		case '再見積り依頼':
		$btn_area = '
        <div>
					<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
        </div>
        <div>
					<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
					<a href="/m_contact2/?type=案件の取り下げ&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">キャンセル</a>
        </div>
			';
			break;
		case '運営手数料追加':
		//研究者の場合は「運営手数料追加」の際にはボタンエリアは変化させない
			$btn_area = '
				<!--
        <div>
					<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
        </div>
				-->
        <div>
					<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
					<a href="/m_contact2/?type=案件の取り下げ&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">キャンセル</a>
        </div>
			';
			break;
		case '見積り送付':
		case '追加見積り':
		// 	$btn_area = '
		// 		<!--
        // <div>
		// 			<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
        // </div>
		// 		-->
        // <div>
		// 			<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">発注を依頼する</a>
        // </div>
        // <div>
		// 			<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
        // </div>
		// 	';

			if($item_num_div=="" || $item_num_div==0){
				if($itemM2["M2_DRDO01"]=="M2_DRDO01:仮登録"){
				$btn_area = '
					<div>
								<a href="javascript:void(0)" onclick="alert(\'仮登録中のため、発注できません。\')">発注を依頼する</a>
					</div>
					<div>
								<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
					</div>
				';
	
				} else {
					$btn_area = '
						<div>
									<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">発注を依頼する</a>
						</div>
						<div>
									<a href="/m_contact2/?type=キャンセル依頼&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">発注キャンセル</a>
									<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
						</div>
					';
				}
			}
		

			break;
		case '発注依頼':
			$btn_area = '
        <div>
        			<a href="/m_contact2/?type=キャンセル依頼&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">発注キャンセル</a>
					<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
        </div>
			';
			break;
		case '決済者発注承認':
			$btn_area = '
        <div>
        			<a href="/m_contact2/?type=キャンセル依頼&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">発注キャンセル</a>
					<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
        </div>
			';
			break;
		case '発注否認':
			if($itemM2["M2_DRDO01"]=="M2_DRDO01:仮登録"){

			$btn_area = '
				<div>
							<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
				</div>
				<div>
							<a href="javascript:void(0)" onclick="alert(\'仮登録中のため、発注できません。\')">発注を依頼する</a>
				</div>
				<div>
							<a href="/m_contact2/?type=キャンセル依頼&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">発注キャンセル</a>
							<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
				</div>
					';
			} else {
			$btn_area = '
				<div>
							<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
				</div>
				<div>
							<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">発注を依頼する</a>
				</div>
				<div>
							<a href="/m_contact2/?type=キャンセル依頼&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">発注キャンセル</a>
							<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
				</div>
					';
			}
		


			break;
		case '受注承認':
			$btn_area = '
        <div>
        			<a href="/m_contact2/?type=キャンセル依頼&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">発注キャンセル</a>
					<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
        </div>
			';
			break;
		case 'データ納品':
		case '物品納品':
			$btn_area = '
				<div>
					<a href="/m_contact2/?type=納品確認&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_blank">
						納品を承認する
					</a>
				</div>
			';
			//$btn_area = '
			//	<div>
			//		<a href="javascript:shodan_nohin('.$_GET['etc02'].',\'\');">納品を承認する</a>
			//	</div>
			//';
			break;
		case 'サプライヤーが納品(一括前払い)':
			$btn_area = '
				<div>
					<a href="/m_contact2/?type=納品確認&sub_type=研究者が納品承認(一括前払い)&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_blank">
						納品を承認する
					</a>
				</div>
			';
			break;
	}
	$str=str_replace("[HEADER_BUTTON_AREA]",$btn_area,$str);



/*
	$btn_area = '';
	switch($item_shodan['STATUS']) {
		case '問い合わせ':
			$btn_area = '
        <div>
					<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">見積りを依頼する</a>
        </div>
        <div>
        			<a href="/m_contact2/?type=案件の取り下げ&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">キャンセル</a>
        </div>
			';
			break;
		case '見積り依頼':
		case '再見積り依頼':
		//研究者の場合は「運営手数料追加」の際にはボタンエリアは変化させない
		case '運営手数料追加':
			$btn_area = '
				<!--
        <div>
					<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
        </div>
				-->
        <div>
					<!--<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>-->
					<a href="/m_contact2/?type=案件の取り下げ&mode=new&shodan_id='.$_GET['etc02'].'" target="_parent">キャンセル</a>
        </div>
			';
			break;
		case '見積り送付':
		case '追加見積り':
		// 	$btn_area = '
		// 		<!--
        // <div>
		// 			<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
        // </div>
		// 		-->
        // <div>
		// 			<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">発注を依頼する</a>
        // </div>
        // <div>
		// 			<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
        // </div>
		// 	';

			if($itemM2["M2_DRDO01"]=="M2_DRDO01:仮登録"){
			$btn_area = '
				<div>
							<a href="javascript:void(0)" onclick="alert(\'仮登録中のため、発注できません。\')">発注を依頼する</a>
				</div>
				<div>
							<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
				</div>
			';

			} else {
				$btn_area = '
					<div>
								<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">発注を依頼する</a>
					</div>
					<div>
								<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
					</div>
				';
			}
		



			break;
		case '発注依頼':
			$btn_area = '
        <div>
					<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
        </div>
			';
			break;
		case '決済者発注承認':
			$btn_area = '
        <div>
					<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
        </div>
			';
			break;
		case '発注否認':
			if($itemM2["M2_DRDO01"]=="M2_DRDO01:仮登録"){

			$btn_area = '
				<div>
							<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
				</div>
				<div>
							<a href="javascript:void(0)" onclick="alert(\'仮登録中のため、発注できません。\')">発注を依頼する</a>
				</div>
				<div>
							<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
				</div>
					';
			} else {
			$btn_area = '
				<div>
							<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">再見積りを依頼する</a>
				</div>
				<div>
							<a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$mid1.'" target="_parent">発注を依頼する</a>
				</div>
				<div>
							<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
				</div>
					';
			}
		


			break;
		case '受注承認':
			$btn_area = '
        <div>
					<a href="javascript:shodan_cancel('.$_GET['etc02'].');">キャンセル</a>
        </div>
			';
			break;
		case 'データ納品':
		case '物品納品':
			$btn_area = '
        <div>
					<a href="javascript:shodan_nohin('.$_GET['etc02'].');">納品を承認する</a>
        </div>
			';
			break;
	}
	$str=str_replace("[HEADER_BUTTON_AREA]",$btn_area,$str);
	*/

	// ステータスが完了ならチャット不可
	if($item_shodan['STATUS'] == '完了' || $item_shodan['STATUS'] == 'キャンセル' || $item_shodan['STATUS'] == '辞退') {
		$str=str_replace("[READONLY_CSS]",'display:none;',$str);
	}
	else {
		$str=str_replace("[READONLY_CSS]",'',$str);
	}

	// 商談情報
	$status = $item_shodan['C_STATUS'];
	// 受領ボタン（納品承認のことと思われる）を押した時点で「請求」にするように変更とのこと
	// なので「納品確認」というステータスはなくなった
	if($item_shodan['STATUS']=="納品確認"){
		$status="請求";
	}

	if($item_shodan['STATUS']=="請求書送付(一括前払い)" || $item_shodan['STATUS']=="請求書送付(前払い)"){
		$status="実施中";
	}

	$str=str_replace("[TITLE]",$item_shodan['TITLE'],$str);
	$str=str_replace("[SHODAN_STATUS]",$status,$str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);

	if($_SESSION['MID'] == 'M299999') {
		$str=DispParam($str, "ADMIN-ON");
		$str=DispParamNone($str, "ADMIN-OFF");
	}
	else {
		$str=DispParamNone($str, "ADMIN-ON");
		$str=DispParam($str, "ADMIN-OFF");
	}

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
