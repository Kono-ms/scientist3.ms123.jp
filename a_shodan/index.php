<?php

session_start();
require "../config.php";
require "../base_a.php";
require './config.php';

require("./func_export.php");

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
		$p_div_id=$_GET['div_id'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$p_div_id=$_POST['div_id'];
	}

	if ($mode==""){
		$mode="list";
	}

	if ($mode=="mailtest1"){
		//https://scientist3.ms123.jp/a_shodan/?mode=mailtest1
		$key="27";
		//SendMail($key);
		exit;
	}
	if ($mode=="mailtest2"){
		//https://scientist3.ms123.jp/a_shodan/?mode=mailtest2
		$key="27";
		SendMailKanryo($key);
		exit;
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
				$msg=ErrorCheck();
				if ($msg==""){
					// 更新前情報
					$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
					$rs=mysqli_query(ConnDB(),$StrSQL);
					$itemBefore = mysqli_fetch_assoc($rs);
					
					SaveData($key);

					if($FieldValue[9]=="受注承認" && $itemBefore["STATUS"]!=$FieldValue[9]){
						echo "<!--受注承認に更新-->";
						//SendMail($key);
					}
					if($FieldValue[9]=="完了" && $itemBefore["STATUS"]!=$FieldValue[9]){
						echo "<!--完了に更新-->";
						SendMailKanryo($key);
					}
					$mode="list";
					if ($page==""){
						$page=1;
					} 
				}
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
			if ($sort==""){
				$sort=1;
			} 
			break;
		case "export":
			ExportData();
			exit;
		case "import":
			ImportData($obj,$a,$b,$key,$mode);
			$mode="list";
			break;
		case "update1":
			UpdateStatusDone($key,$p_div_id);
			$url=BASE_URL . "/a_shodan/?mode=edit&key=".$key;
			header("Location: {$url}");
			break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 商談のステータスを「完了」にする
//引数 商談ID
//戻値 
//=========================================================================================================
function UpdateStatusDone($key,$p_div_id){
	$now=strtotime("now");
	$date_stmp=date('Y/m/d H:i:s',$now);
	$status = '完了';
	$c_status = '完了';
	$status_sort = '99';

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$key." ";
	$StrSQL.=" AND DIV_ID='".$p_div_id."' limit 1";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus=mysqli_fetch_assoc($rs);

	$shodan_id=$key;
	$mid1=$item_filestatus["MID1"];
	$mid2=$item_filestatus["MID2"];

	if(checkDIV_ID($p_div_id)==""){
		$StrSQL = "
		UPDATE DAT_SHODAN SET
		EDITDATE = '".$date_stmp."',
		STATUS_SORT = '".$status_sort."',
		C_STATUS = '".$c_status."',
		STATUS = '".$status."'
		WHERE
		ID = ".$key."
		";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

	}else{
		$StrSQL = "
		UPDATE DAT_SHODAN_DIV SET
		EDITDATE = '".$date_stmp."',
		C_STATUS = '".$c_status."',
		STATUS = '".$status."'
		WHERE
		DIV_ID = '".$p_div_id."'
		";
		//echo "<!--sql: $StrSQL-->";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}

	$StrSQL = "
		INSERT INTO DAT_FILESTATUS (
			SHODAN_ID,
			MID1,
			MID2,

			CATEGORY,
			STATUS,

			DIV_ID,

			NEWDATE,
			EDITDATE
		) VALUE (
			'".$shodan_id."',
			'".$mid1."',
			'".$mid2."',

			'".$c_status."',
			'".$status."',

			'".$p_div_id."',

			'".$date_stmp."',
			'".$date_stmp."'
		)";
	echo('<!--'.$StrSQL.'-->');
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	//2回払いは、前払いのあと自動で後払いに移行するように仕様変更
	//2回払いで「前払い」のステータスが「完了」になった場合、
	//Part2の「発注依頼」「受注承認」のデータを作る関数を作成
	$m2_pay_type=check_M2_PAY_TYPE($shodan_id,$mid1);
	//echo "<!--m2_pay_type:$m2_pay_type-->";
	if($m2_pay_type=="Split"){
		$tmp="";
		$tmp=explode("-", $p_div_id);
		$pre_part="";
		$part="";
		if(count($tmp)==3){
			$pre_part=$tmp[0]."-".$tmp[1];
			$part=$tmp[2];
		}
		$part2_div_id=$pre_part."-Part2";
		//echo "<!--part2_div_id:$part2_div_id-->";

		if($part=="Part1"){
			$StrSQL="SELECT * FROM DAT_SHODAN_DIV WHERE SHODAN_ID='".$shodan_id."'";
			$StrSQL.=" AND DIV_ID='".$part2_div_id."' ";
			//echo('<!--sql_oya:'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$sdiv_part2_item=mysqli_fetch_assoc($rs);
			if($sdiv_part2_item["STATUS"]!="見積り送付"){
				return "err_msg";
			}
			//echo "<!--sdiv_part2_item:";
			//var_dump($sdiv_part2_item);
			//echo "-->";
			//Part2の「発注依頼」のデータを作る
			//Part2の「受注承認」のデータを作る
			if(makeHatyuAuto($shodan_id,$part2_div_id)==true){
				makeJyutyusyoninAuto($shodan_id,$part2_div_id);
			}

		}

	}


}

//=========================================================================================================
//名前 
//機能\2回払いで、Part1の完了時に、Part2の発注依頼された状態にする
//引数 $shodan_id, $div_id （2回払いの、パート2のDIV_ID）
//戻値 
//=========================================================================================================
function makeHatyuAuto($shodan_id,$div_id){
	if($shodan_id=="" || $div_id==""){
		return "";
	}

	$now=strtotime("now");
	$date_stmp=date('Y/m/d H:i:s',$now);
	$status = '発注依頼';
	$c_status = '見積り';
	$status_sort = '4';

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." ";
	$StrSQL.=" AND DIV_ID='".$div_id."' limit 1";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus=mysqli_fetch_assoc($rs);

	$mid1=$item_filestatus["MID1"];
	$mid2=$item_filestatus["MID2"];

	$tmp="";
	$tmp=explode("-", $div_id);
	$pre_part="";
	$part="";
	if(count($tmp)==3){
		$pre_part=$tmp[0]."-".$tmp[1];
		$part=$tmp[2];
	}

	$StrSQL="SELECT ID,SHODAN_ID,DIV_ID,STATUS FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." ";
	$StrSQL.=" AND DIV_ID='".$div_id."' AND STATUS='見積り送付' limit 1";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$mitsu_item_filestatus=mysqli_fetch_assoc($rs);
	$h_m2_id=$mitsu_item_filestatus["ID"];

	$StrSQL = "
	UPDATE DAT_SHODAN_DIV SET
	EDITDATE = '".$date_stmp."',
	C_STATUS = '".$c_status."',
	STATUS = '".$status."'
	WHERE
	DIV_ID = '".$div_id."'
	";
	//echo "<!--sql1: $StrSQL-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}
	

	$StrSQL = "
		INSERT INTO DAT_FILESTATUS (
			SHODAN_ID,
			MID1,
			MID2,

			CATEGORY,
			STATUS,

			DIV_ID,
			H_M2_ID,

			NEWDATE,
			EDITDATE
		) VALUE (
			'".$shodan_id."',
			'".$mid1."',
			'".$mid2."',

			'".$c_status."',
			'".$status."',

			'".$div_id."',
			'".$h_m2_id."',

			'".$date_stmp."',
			'".$date_stmp."'
		)";
	//echo('<!--sql2:'.$StrSQL.'-->');
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	return true;
}


//=========================================================================================================
//名前 
//機能\2回払いで、Part1の完了時に、Part2の受注承認された状態にする
//（発注依頼makeHatyuAuto($shodan_id,$div_id)後）
//引数 $shodan_id, $div_id （2回払いの、パート2のDIV_ID）
//戻値 
//=========================================================================================================
function makeJyutyusyoninAuto($shodan_id,$div_id){
	if($shodan_id=="" || $div_id==""){
		return "";
	}

	$now=strtotime("now");
	$date_stmp=date('Y/m/d H:i:s',$now);
	$status="受注承認";
	$c_status = '実施中';
	$status_sort = '6';

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." ";
	$StrSQL.=" AND DIV_ID='".$div_id."' limit 1";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus=mysqli_fetch_assoc($rs);

	$mid1=$item_filestatus["MID1"];
	$mid2=$item_filestatus["MID2"];

	$tmp="";
	$tmp=explode("-", $div_id);
	$pre_part="";
	$part="";
	if(count($tmp)==3){
		$pre_part=$tmp[0]."-".$tmp[1];
		$part=$tmp[2];
	}

	$StrSQL = "
	UPDATE DAT_SHODAN_DIV SET
	EDITDATE = '".$date_stmp."',
	C_STATUS = '".$c_status."',
	STATUS = '".$status."'
	WHERE
	DIV_ID = '".$div_id."'
	";
	//echo "<!--sql3: $StrSQL-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}
	

	$StrSQL = "
		INSERT INTO DAT_FILESTATUS (
			SHODAN_ID,
			MID1,
			MID2,

			CATEGORY,
			STATUS,

			DIV_ID,
			H_M2_ID,

			NEWDATE,
			EDITDATE
		) VALUE (
			'".$shodan_id."',
			'".$mid1."',
			'".$mid2."',

			'".$c_status."',
			'".$status."',

			'".$div_id."',
			'".$h_m2_id."',

			'".$date_stmp."',
			'".$date_stmp."'
		)";
	//echo "<!--sql4: $StrSQL-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	return true;
}




//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($key)
{

	eval(globals());

	//更新後情報
	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	$maildata = GetMailTemplate('受注承認(M1)');


	$mid1s=explode(",",$item['MID1_LIST']);
	
	for($i=0; $i<count($mid1s); $i++){

		$mid1=$mid1s[$i];

		$MailBody = $maildata['BODY'];
		$subject = $maildata['TITLE'];

		$StrSQL2="SELECT * from DAT_M1 where MID = '".$mid1."'";
		$rs2=mysqli_query(ConnDB(),$StrSQL2);
		$m1 = mysqli_fetch_assoc($rs2);

		$mailto = $m1['EMAIL'];
		// $mailto = "toretoresansan00@gmail.com";
		$MailBody=str_replace("[D-NAME]",$m1['M1_DVAL01'],$MailBody);
		$MailBody=DispShodan($key,$MailBody);

		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
echo "<!--mailtoM1:".$mailto."-->";
		mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	}

	$StrSQL="SELECT * from DAT_M2 where MID = '".$item["MID2"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m2 = mysqli_fetch_assoc($rs);

	$maildata = GetMailTemplate('受注承認(M2)');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$mailto = $m2['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);
	$MailBody=DispShodan($key,$MailBody);
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--mailtoM2:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		
	
	$StrSQL="SELECT * from DAT_M3 where MID = '".$m2["M2_DVAL15"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m3 = mysqli_fetch_assoc($rs);

	$maildata = GetMailTemplate('受注承認(M3)');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$mailto = $m3['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	$MailBody=str_replace("[D-NAME]",$m3['M2_DVAL01'],$MailBody);
	$MailBody=DispShodan($key,$MailBody);
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--mailtoM3:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMailKanryo($key)
{

	eval(globals());

	//更新後情報
	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	

	$StrSQL="SELECT * from DAT_M2 where MID = '".$item["MID2"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m2 = mysqli_fetch_assoc($rs);

	$maildata = GetMailTemplate('完了(M2)');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$mailto = $m2['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);
	$MailBody=DispShodan($key,$MailBody);
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		
	


	//$maildata = GetMailTemplate('完了(ADMIN)');
	//$MailBody = $maildata['BODY'];
	//$subject = $maildata['TITLE'];
	//
	//$mailto = SENDER_EMAIL;
	//// $mailto = "toretoresansan00@gmail.com";
	//$MailBody=str_replace("[D-NAME]","管理者",$MailBody);
	//$MailBody=DispShodan($key,$MailBody);
	//mb_language("Japanese");
	//mb_internal_encoding("UTF-8");
	//mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispShodan($key,$str){

$type="";
	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$key.";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_shodan = mysqli_fetch_assoc($rs);

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$key.";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus = mysqli_fetch_assoc($rs);

	$word = $item_shodan['CATEGORY'];
	$word2 = $item_shodan['KEYWORD'];

	$m1_view="";
	$mid1s=explode(",",$item_shodan['MID1_LIST']);
	for($i=0; $i<count($mid1s); $i++){

		$mid1=$mid1s[$i];

		$StrSQL2="SELECT * from DAT_M1 where MID = '".$mid1."'";
		$rs2=mysqli_query(ConnDB(),$StrSQL2);
		$m1 = mysqli_fetch_assoc($rs2);

		$m1_view .= $m1['M1_DVAL01'];
		
	}
	$str=str_replace("[M1_VIEW]",$m1_view,$str);

	$str=str_replace("[D-TITLE]",$item_shodan['TITLE'],$str);
	$str=str_replace("[D-COMMENT]",str_replace("\n", '<br>', $item_filestatus['T_COMMENT']),$str); // Dはbr変換
	$str=str_replace("[D-FILE]",$item_filestatus['T_FILE'],$str);
	$str=str_replace("[D-FILE2]",$item_filestatus['T_FILE2'],$str);
	$str=str_replace("[D-FILE3]",$item_filestatus['T_FILE3'],$str);
	$str=str_replace("[D-FILE4]",$item_filestatus['T_FILE4'],$str);
	$str=str_replace("[D-FILE5]",$item_filestatus['T_FILE5'],$str);
	$str=str_replace("[D-KIGEN]",$item_filestatus['T_ANSWERDATE'],$str);
	$str=str_replace("[D-NEWDATE]",substr($item_filestatus['NEWDATE'],0,10),$str);

	$str=str_replace("[D-M1_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['M1_MESSAGE']),$str); // Dはbr変換
	$str=str_replace("[D-M1_TRANS_FLG]",$item_filestatus['M1_TRANS_FLG'],$str);
	$str=str_replace("[D-M1_TRANS_FLG_あり]",($item_filestatus['M1_TRANS_FLG'] == 'あり' ? 'checked' : ''),$str);
	$str=str_replace("[D-M1_TRANS_FLG_なし]",($item_filestatus['M1_TRANS_FLG'] == 'なし' ? 'checked' : ''),$str);
	$str=str_replace("[D-M1_TRANS_TXT]",$item_filestatus['M1_TRANS_TXT'],$str);
	$str=str_replace("[D-M1_PRICE]",$item_filestatus['M1_PRICE'],$str);
	$str=str_replace("[D-M1_FILE]",$item_filestatus['M1_FILE'],$str);
	$str=str_replace("[D-M1_KIGEN]",$item_filestatus['M1_KIGEN'],$str);

	// 新見積り書
	// -------------------------------------------------------------------------------
	$str=str_replace("[D-M2_NOHIN_TYPE]",$item_filestatus['M2_NOHIN_TYPE'],$str);
	$str=str_replace("[D-M2_PAY_TYPE]",$item_filestatus['M2_PAY_TYPE'],$str);
	$str=str_replace("[D-M2_QUOTE_NO]",$item_filestatus['M2_QUOTE_NO'],$str);
	$str=str_replace("[D-M2_STUDY_CODE]",$item_filestatus['M2_STUDY_CODE'],$str);
	$str=str_replace("[D-M2_DATE]",$item_filestatus['M2_DATE'],$str);
	$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$item_filestatus['M2_QUOTE_VALID_UNTIL'],$str);
	$str=str_replace("[D-M2_DESCRIPTION]",$item_filestatus['M2_DESCRIPTION'],$str);
	$str=str_replace("[D-M2_CURRENCY]",$item_filestatus['M2_CURRENCY'],$str);
	$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$item_filestatus['M2_SPECIAL_DISCOUNT'],$str);
	$str=str_replace("[D-M2_SPECIAL_NOTE]",$item_filestatus['M2_SPECIAL_NOTE'],$str);

	$str=str_replace("[M2_NOHIN_TYPE]",$item_filestatus['M2_NOHIN_TYPE'],$str);
	$str=str_replace("[M2_PAY_TYPE]",$item_filestatus['M2_PAY_TYPE'],$str);
	$str=str_replace("[M2_QUOTE_NO]",$item_filestatus['M2_QUOTE_NO'],$str);
	$str=str_replace("[M2_STUDY_CODE]",$item_filestatus['M2_STUDY_CODE'],$str);
	$str=str_replace("[M2_DATE]",$item_filestatus['M2_DATE'],$str);
	$str=str_replace("[M2_QUOTE_VALID_UNTIL]",$item_filestatus['M2_QUOTE_VALID_UNTIL'],$str);
	$str=str_replace("[M2_DESCRIPTION]",$item_filestatus['M2_DESCRIPTION'],$str);
	$str=str_replace("[M2_CURRENCY]",$item_filestatus['M2_CURRENCY'],$str);
	$str=str_replace("[M2_SPECIAL_DISCOUNT]",$item_filestatus['M2_SPECIAL_DISCOUNT'],$str);
	$str=str_replace("[M2_SPECIAL_NOTE]",$item_filestatus['M2_SPECIAL_NOTE'],$str);

	$detail_template = '
		Details XXX
		Item #:[D-M2_DETAIL_ITEM_XXX]
		Description:[D-M2_DETAIL_DESCRIPTION_XXX]
		Price:[D-M2_DETAIL_PRICE_XXX]
		Note:[D-M2_DETAIL_NOTE_XXX]
	';
	$add_detail_area = '';
	$detail_key = 0;

	$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$item_filestatus["ID"].";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	while($item_filestatus_detail = mysqli_fetch_assoc($rs)) {
		$detail_no = $detail_key + 1;
		$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);

		$add_detail_area=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area);
		$add_detail_area=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area); // Dはbr変換
		$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area);
		$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換

		$detail_key++;
	}
	$str=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$str);
	// -------------------------------------------------------------------------------

	if($type == '見積り送付' || $type == '発注依頼') {
		//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_shodan['ID']." and STATUS='見積り送付' order by ID desc;";
		//echo('<!--'.$StrSQL.'-->');
		//echo('<!--'.$key.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$h_m2_list = '';
		$h_m2_detail = array();
		while($item_filestatus2 = mysqli_fetch_assoc($rs)) {
			$selected = '';
			if($item_filestatus2['ID'] == $key) {
				$selected = ' selected ';
			}
			$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）</option>';

			$h_m2_detail[$item_filestatus2['ID']] = $item_filestatus2;
			$h_m2_detail[$item_filestatus2['ID']]['detail'] = array();

			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$item_filestatus2['ID']." order by ID asc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			while($item_filestatus_detail2 = mysqli_fetch_assoc($rs2)) {
				$h_m2_detail[$item_filestatus2['ID']]['detail'][] = $item_filestatus_detail2;
			}

			if($type == '発注依頼') {
				$str=str_replace("[M2_ID]",$item_filestatus2['M2_ID'],$str);
				$str=str_replace("[M2_VERSION]",$item_filestatus2['M2_VERSION'],$str);
			}
		}
		$str=str_replace("[H_M2_LIST]",$h_m2_list,$str);
		$str=str_replace("[H_M2_DETAIL]",json_encode($h_m2_detail),$str);
	}

	$str=str_replace("[D-H_M2_ID]",$item_filestatus['H_M2_ID'],$str);
	$str=str_replace("[D-M1_M2_ID]",$item_filestatus['M1_M2_ID'],$str);
	$str=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $item_filestatus['H_COMMENT']),$str); // Dはbr変換

	$str=str_replace("[D-N_FILE]",(isset($item_filestatus['N_FILE']) ? $item_filestatus['N_FILE'] : $_FILES['N_FILE']['name']),$str);
	$str=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['N_MESSAGE']),$str); // Dはbr変換

	// サプライヤーからの請求書ではなくa_filestatusから登録されたS2を出す
	/*
	$str=str_replace("[D-S_FILE]",(isset($item_filestatus['S_FILE']) ? $item_filestatus['S_FILE'] : $_FILES['S_FILE']['name']),$str);
	$str=str_replace("[D-S_MESSAGE]",$item_filestatus['S_MESSAGE'],$str);
	*/
	$str=str_replace("[D-S_FILE]",(isset($item_filestatus['S2_FILE']) ? $item_filestatus['S2_FILE'] : $_FILES['S2_FILE']['name']),$str);
	$str=str_replace("[D-S_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['S_MESSAGE']),$str); // Dはbr変換

	
	$str=str_replace("[TITLE]",$item_shodan['TITLE'],$str);
	$str=str_replace("[COMMENT]",$item_filestatus['T_COMMENT'],$str);
	$str=str_replace("[FILE]",$item_filestatus['T_FILE'],$str);
	$str=str_replace("[FILE2]",$item_filestatus['T_FILE2'],$str);
	$str=str_replace("[FILE3]",$item_filestatus['T_FILE3'],$str);
	$str=str_replace("[FILE4]",$item_filestatus['T_FILE4'],$str);
	$str=str_replace("[FILE5]",$item_filestatus['T_FILE5'],$str);
	$str=str_replace("[KIGEN]",$item_filestatus['T_ANSWERDATE'],$str);
	$str=str_replace("[NEWDATE]",substr($item_filestatus['NEWDATE'],0,10),$str);

	$str=str_replace("[M1_MESSAGE]",$item_filestatus['M1_MESSAGE'],$str);
	$str=str_replace("[M1_TRANS_FLG]",$item_filestatus['M1_TRANS_FLG'],$str);
	$str=str_replace("[M1_TRANS_TXT]",$item_filestatus['M1_TRANS_TXT'],$str);
	$str=str_replace("[M1_PRICE]",$item_filestatus['M1_PRICE'],$str);
	$str=str_replace("[M1_FILE]",$item_filestatus['M1_FILE'],$str);
	$str=str_replace("[M1_KIGEN]",$item_filestatus['M1_KIGEN'],$str);

	$str=str_replace("[H_M2_ID]",$item_filestatus['H_M2_ID'],$str);
	$str=str_replace("[M1_M2_ID]",$item_filestatus['M1_M2_ID'],$str);
	$str=str_replace("[H_COMMENT]",$item_filestatus['H_COMMENT'],$str);

	$str=str_replace("[N_FILE]",(isset($item_filestatus['N_FILE']) ? $item_filestatus['N_FILE'] : $_FILES['N_FILE']['name']),$str);
	$str=str_replace("[N_MESSAGE]",$item_filestatus['N_MESSAGE'],$str);

	// サプライヤーからの請求書ではなくa_filestatusから登録されたS2を出す
	/*
	$str=str_replace("[S_FILE]",(isset($item_filestatus['S_FILE']) ? $item_filestatus['S_FILE'] : $_FILES['S_FILE']['name']),$str);
	$str=str_replace("[S_MESSAGE]",$item_filestatus['S_MESSAGE'],$str);
	*/
	$str=str_replace("[S_FILE]",(isset($item_filestatus['S2_FILE']) ? $item_filestatus['S2_FILE'] : $_FILES['S2_FILE']['name']),$str);
	$str=str_replace("[S_MESSAGE]",$item_filestatus['S2_MESSAGE'],$str);


	$h_m2_detail = array();
	if($type == '発注依頼' || $type == '再見積り依頼') {
		//echo('<!-- disp -->');
		if($type == '発注依頼') {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['H_M2_ID']." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus["ID"]." and STATUS='見積り送付' order by ID desc;";
		}
		else {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['M1_M2_ID']." and STATUS='見積り送付' order by ID desc;";
		}
		//echo('<!--'.$StrSQL.'-->');
		//echo('<!--'.$key.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$h_m2_list = '';
		$h_m2_detail = array();
		while($item_filestatus2 = mysqli_fetch_assoc($rs)) {

			$str=str_replace("[M2_ID]",$item_filestatus2['M2_ID'],$str);
			$str=str_replace("[M2_VERSION]",$item_filestatus2['M2_VERSION'],$str);

			$selected = '';
			if($item_filestatus2['ID'] == $key) {
				$selected = ' selected ';
			}
			$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）</option>';

			$h_m2_detail[$item_filestatus2['ID']] = $item_filestatus2;
			$h_m2_detail[$item_filestatus2['ID']]['detail'] = array();

			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$item_filestatus2['ID']." order by ID asc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			while($item_filestatus_detail2 = mysqli_fetch_assoc($rs2)) {
				$h_m2_detail[$item_filestatus2['ID']]['detail'][] = $item_filestatus_detail2;
			}
		}
		$str=str_replace("[H_M2_LIST]",$h_m2_list,$str);
		$str=str_replace("[H_M2_DETAIL]",json_encode($h_m2_detail),$str);
	}

	return $str;
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
	$htmlnew = "edit.html";
	$htmledit = "edit.html";
	$htmlconf = "conf.html";
	$htmlend = "end.html";
	$htmldisp = "disp.html";
	$htmlerr = "edit.html";
	$htmllist = "list.html";

	if ($mode!="list"){

		switch ($mode){
			case "new":
				$filename=$htmlnew;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "edit":
				$filename=$htmledit;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "saveconf":
				$msg=ErrorCheck();
				if ($msg==""){
					$filename=$htmlconf;
					$msg01="保存";
					$msg02="save";
					$errmsg="";
				} else {
					$filename=$htmlerr;
					$msg01=$msg;
					$msg02="";
					$errmsg=$msg;
				} 
				break;
			case "deleteconf":
				$filename=$htmlconf;
				$msg01="削除";
				$msg02="delete";
				$errmsg="";
				break;
			case "save":
				$msg=ErrorCheck();
				if ($msg==""){
					$filename=$htmlend;
					$msg01="保存";
					$msg02="";
					$errmsg="";
				} else {
					$filename=$htmlerr;
					$msg01=$msg;
					$msg02="";
					$errmsg=$msg;
				} 
				break;
			case "delete":
				$filename=$htmlend;
				$msg01="削除";
				$msg02="";
				$errmsg="";
				break;
			case "disp":
				$filename=$htmldisp;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
		} 

		$fp=$DOCUMENT_ROOT.$filename;
		$str=@file_get_contents($fp);

		$str = MakeHTML($str,1,$lid);

		if ($mode=="new"){
			$str=DispParam($str, "NEWDATA");
			$str=DispParamNone($str, "EDITDATA");
		} else {
			$str=DispParamNone($str, "NEWDATA");
			$str=DispParam($str, "EDITDATA");
		} 

		//大カテゴリー
		$StrSQL="SELECT BIG FROM DAT_CCATE2 ORDER BY BIG,cast(sort as SIGNED )";
		$FieldParam[63]="";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		while ($item = mysqli_fetch_assoc($rs)) {
			if(strpos($FieldParam[63], $item["BIG"]) !== false){
				continue;
			}
			if($FieldParam[63]!=""){
				$FieldParam[63].="::";
			}
			$FieldParam[63].=$item["BIG"];
		}

		//小カテゴリー
		$StrSQL="SELECT SMALL FROM DAT_CCATE2 ORDER BY BIG,cast(sort as SIGNED )";
		$FieldParam[64]="";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		while ($item = mysqli_fetch_assoc($rs)) {

			if($FieldParam[64]!=""){
				$FieldParam[64].="::";
			}
			$FieldParam[64].=$item["SMALL"];
		}

		// サプライヤーは名前を表示
		// ただし、発注依頼以降は対象のサプライヤーだけ表示
		//$item['MID1_LIST'] = str_replace(',', "\n", $item['MID1_LIST']);
		$mid1_name = '';
		if($FieldValue[9] == '問い合わせ' || $$FieldValue[9] == '見積り依頼' || $FieldValue[9] == '再見積もり依頼') {
		}
		else {
			$StrSQL="SELECT MID1 from DAT_FILESTATUS where SHODAN_ID = '".$FieldValue[0]."' order by ID desc;";
			$rs_filestatus=mysqli_query(ConnDB(),$StrSQL);
			$filestatus = mysqli_fetch_assoc($rs_filestatus);
			$StrSQL="SELECT * from DAT_M1 where MID = '".$filestatus['MID1']."';";
			$rs_m1=mysqli_query(ConnDB(),$StrSQL);
			$m1 = mysqli_fetch_assoc($rs_m1);
			$mid1_name .= $m1['M1_DVAL01'];
		}
		$str=str_replace("[MID1_NAME]",$mid1_name,$str);

		for ($i=0; $i<=$FieldMax; $i=$i+1){
			if ($FieldAtt[$i]==4){
				if ($FieldValue[$i]==""){
					$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
					$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
				} 

				if(strstr($FieldValue[$i],"s.gif") == true){
					$str=DispParamNone($str, $FieldName[$i]);
				} else {
					$str=DispParam($str, $FieldName[$i]);
				} 
			} else {
				if ($FieldValue[$i]==""){
					$str=DispParamNone($str, $FieldName[$i]);
				} else {
					$str=DispParam($str, $FieldName[$i]);
				} 
			} 
			// HTMLエスケープ処理（詳細表示系、HIDDEN値）
			$str=str_replace("[".$FieldName[$i]."]",htmlspecialchars($FieldValue[$i]),$str);
			$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($FieldName[$i].":","",htmlspecialchars($FieldValue[$i]))),$str);
			if ($FieldAtt[$i]=="1"){
				$strtmp="";
				$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
				$tmp=explode("::",$FieldParam[$i]);
				for ($j=0; $j<count($tmp); $j=$j+1) {
					//$strtmp=$strtmp."<option value='".$FieldName[$i].":".$tmp[$j]."'>".$tmp[$j]."</option>";
					$strtmp=$strtmp."<option value='".$tmp[$j]."'>".$tmp[$j]."</option>";

				}

				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {

					$str=str_replace("'".$FieldValue[$i]."'","'".$FieldValue[$i]."' selected",$str);
				} 
			} 

			if ($FieldAtt[$i]=="2"){
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"radio\" name=\"".$FieldName[$i]."\" value=\"".$FieldName[$i].":".$tmp[$j]."\"><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$str=str_replace("\"".$FieldValue[$i]."\"","\"".$FieldValue[$i]."\" checked",$str);
				} 
			} 

			if ($FieldAtt[$i]=="3"){
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul class='mlist25p'>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"".$FieldName[$i]."[]\" value=\"".$FieldName[$i].":".$tmp[$j]."\"><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$tmp=explode("\t",$FieldValue[$i]);
					for ($j=0; $j<count($tmp); $j=$j+1) {
						$str=str_replace("\"".$tmp[$j]."\"","\"".$tmp[$j]."\" checked",$str);
					}
				} 
			} 

			if (is_numeric($FieldValue[$i])) {
				$str=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$str);
			} else {
				$str=str_replace("[N-".$FieldName[$i]."]","",$str);
			} 
		}


		//MID2,[1]
		//MID1_LIST,[3]
		$str=str_replace("[MID2_NEW]",convert_mid($FieldValue[1]),$str);
		$ary_mid1_list=explode(",", $FieldValue[3]);
		$tmp_str="";
		foreach ($ary_mid1_list as $val) {
			if($tmp_str==""){
				$tmp_str.=convert_mid($val);
			}else{
				$tmp_str.=",".convert_mid($val);
			}
		}
		$str=str_replace("[MID1_LIST_NEW]",$tmp_str,$str);


		//分割時のステータス一覧
		$StrSQL="SELECT * FROM DAT_SHODAN_DIV WHERE SHODAN_ID='".$key."'";
		$shodan_div_rs=mysqli_query(ConnDB(),$StrSQL);
		$div_status_list="";
		while($shodan_div_item = mysqli_fetch_assoc($shodan_div_rs)){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$key."' AND DIV_ID='".$shodan_div_item["DIV_ID"]."' ";
			$StrSQL.=" AND STATUS='見積り送付' ";
			$status_rs=mysqli_query(ConnDB(),$StrSQL);
			$status_item=mysqli_fetch_assoc($status_rs);
			
			$tmp="";
			$tmp=explode("-", $shodan_div_item["DIV_ID"]);
			$part="";
			if(count($tmp)==3){
				$part=$tmp[2];
			}

			if($part=="Part0"){
				continue;
			}

			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$SCNo_str="";

			$SCNo_ary["SCNo_yy"]=$status_item["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$status_item["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$status_item["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$status_item["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$status_item["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$status_item["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);

			$preview_m2_version="";
			$preview_m2_version=$status_item["M2_VERSION"];

			$div_status=$shodan_div_item["STATUS"];

			$div_status_list.="<tr><th>".$SCNo_str."-Version".$preview_m2_version."</th><td>".$div_status."</td></tr>";

		}
		
		$str=str_replace("[DIV_STATUS_LIST]",$div_status_list,$str);

		/*
		while($status_item = mysqli_fetch_assoc($status_rs)){
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$SCNo_str="";

			$SCNo_ary["SCNo_yy"]=$status_item["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$status_item["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$status_item["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$status_item["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$status_item["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$status_item["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);

			$preview_m2_version="";
			$preview_m2_version=$status_item["M2_VERSION"];

			$div_status=$status_item[""]

			$div_status_list.="<tr><th>ステータス：".$SCNo_str."-Version".$preview_m2_version."</th><td>".."</td></tr>";

		}*/


		//プレビュー関連
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$key." AND STATUS='見積り送付';";
		//echo('<!--previewSQL:'.$StrSQL.'-->');
		$preview_rs=mysqli_query(ConnDB(),$StrSQL);
		$opt_preview_list_r="";
		$opt_preview_list_cb="";
		//$opt_test="";
		while($preview_item = mysqli_fetch_assoc($preview_rs)){
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$SCNo_str="";

			$SCNo_ary["SCNo_yy"]=$preview_item["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$preview_item["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$preview_item["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$preview_item["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$preview_item["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$preview_item["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);

			$preview_m2_version="";
			$preview_m2_version=$preview_item["M2_VERSION"];

			$opt_preview_list_r.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=r&btn_version=1&key=".$preview_item["ID"]."'>";
			//$opt_preview_list_r.="<option value='/m_contact1/?type=見積り送付&mode=disp_frame&key=".$preview_item["ID"]."'>";
			$opt_preview_list_r.=$SCNo_str."-Version".$preview_m2_version."</option>";
			
			$opt_preview_list_cb.="<option value='/a_filestatus/?mode=disp_frame1&preview_type=cb&btn_version=1&key=".$preview_item["ID"]."'>";
			//$opt_preview_list_cb.="<option value='/m_contact1/?type=見積り送付&mode=disp_frame&key=".$preview_item["ID"]."'>";
			$opt_preview_list_cb.=$SCNo_str."-Version".$preview_m2_version."</option>";
			//$opt_test.="<a href=\"javascript:window.parent.open_mcontact2('/m_contact1/?type=見積り送付&mode=disp_frame&key=".$preview_item["ID"]."')\">".$SCNo_str."</a><br>";
		}
		$str=str_replace("[OPT_PREVIEW_LIST_R]",$opt_preview_list_r,$str);
		$str=str_replace("[OPT_PREVIEW_LIST_CB]",$opt_preview_list_cb,$str);
		//$str=str_replace("[OPT_TEST]",$opt_test,$str);


		$str=str_replace("[MSG]",$msg01,$str);
		$str=str_replace("[NEXTMODE]",$msg02,$str);
		if($errmsg<>""){
			$str=str_replace("[ERRMSG]",$errmsg,$str);
			$str=DispParam($str, "ERR");
		} else {
			$str=DispParamNone($str, "ERR");
		}
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);

		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);

		$str=str_replace("[BASE_URL]",BASE_URL,$str);
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
			$strD=$strD.$line;
		}
		fclose($tso);

		// SQLインジェクション対策

		// TODO
		//$StrSQL="SELECT DAT_SHODAN.*,DAT_M2.M2_DVAL01,M1.M1_DVAL01,FILESTATUS.M1_DVAL01,DAT_M2.M2_DVAL03 FROM ".$TableName." ";
		$StrSQL="SELECT DAT_SHODAN.*,DAT_M2.M2_DVAL01,M1.M1_DVAL01,FILESTATUS.M1_DVAL01,DAT_M2.M2_DVAL03, FILESTATUS.FILESTATUS_ID, DAT_M2.MID as M2_MID, DAT_SHODAN.NEWDATE FROM ".$TableName." ";

		$StrSQL.=" LEFT JOIN (";
		$StrSQL.=" SELECT GROUP_CONCAT(DISTINCT DAT_M1.M1_DVAL01 SEPARATOR ',') as M1_DVAL01,DAT_SHODAN.ID ";
		$StrSQL.=" FROM DAT_SHODAN  ";
		$StrSQL.=" LEFT JOIN DAT_M1 ON DAT_SHODAN.MID1_LIST LIKE concat(concat('%',DAT_M1.MID),'%') ";
		$StrSQL.=" GROUP BY DAT_SHODAN.ID  ";
		$StrSQL.="  ) as M1 ON DAT_SHODAN.ID=M1.ID";

	
		$StrSQL.=" LEFT JOIN (";

		// TODO
		//$StrSQL.="   SELECT DAT_FILESTATUS.SHODAN_ID,MIN(DAT_M1.M1_DVAL01) as M1_DVAL01 ";
		$StrSQL.="   SELECT DAT_FILESTATUS.SHODAN_ID,DAT_M1.M1_DVAL01 as M1_DVAL01, DAT_FILESTATUS.ID as FILESTATUS_ID ";

		$StrSQL.="   FROM DAT_FILESTATUS  ";
		$StrSQL.="   LEFT JOIN DAT_M1 ON DAT_FILESTATUS.MID1=DAT_M1.MID  ";

		// TODO
		//$StrSQL.="   GROUP BY DAT_FILESTATUS.SHODAN_ID  ";
		$StrSQL.="   GROUP BY DAT_FILESTATUS.SHODAN_ID,DAT_FILESTATUS.MID1  ";

		$StrSQL.="  ) as FILESTATUS ON DAT_SHODAN.ID=FILESTATUS.SHODAN_ID";


		$StrSQL.=" LEFT JOIN DAT_M2 ON DAT_SHODAN.MID2=DAT_M2.MID ";
		$StrSQL.=" ".ListSql(mysqli_real_escape_string(ConnDB(),$sort),mysqli_real_escape_string(ConnDB(),$word)).";";

echo "<!--".$StrSQL."-->";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item=="") {
			$pagestr="";
			$strMain="<tr><td align=center colspan=7>対象データがありません。</td></tr>";
		} else {
			//================================================================================================
			//ページング処理
			//================================================================================================
			$reccount=mysqli_num_rows($rs);
			$pagecount=intval(($reccount-1)/$PageSize+1);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$str="";
			$str.="<div class=\"paging\"><div class=\"row\">";
			$str.="<div class=\"col-sm-5\"><div class=\"dataTables_info\" id=\"table_summary_info\" role=\"status\" aria-live=\"polite\">対象件数(".$reccount."件)</div></div>";
			$str.="<div class=\"col-sm-7\"><div class=\"dataTables_paginate paging_simple_numbers\" id=\"table_summary_paginate\"><ul class=\"pagination\">";

			if (intval($page)>1) {
				$str.="<li class=\"paginate_button previous disabled\" id=\"table_summary_previous\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page-1)."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">前の".$PageSize."件</a></li>";
			}

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
					$str.="<li class=\"paginate_button active\"><span>".$i."</span></li>";
				} else {
					$str.="<li class=\"paginate_button\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">".$i."</a></li>";
				} 
			}
			if (intval($page)<$pagecount) {
				$str.="<li class=\"paginate_button next\" id=\"table_summary_next\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page+1)."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">次の".$PageSize."件</a></li>";
			} 

			$str.="</ul></div></div>";
			$str.="</div></div>";

			$pagestr=$str;
			$CurrentRecord=1;
			$strMain="";
			while ($item = mysqli_fetch_assoc($rs)) {
				//echo "<!--";
				//var_dump($item);
				//echo "-->";

				$str=$strM;

				$StrSQL="SELECT * from DAT_M2 where MID = '".$item['MID2']."';";
				$rs_m2=mysqli_query(ConnDB(),$StrSQL);
				$m2 = mysqli_fetch_assoc($rs_m2);
				//$str=str_replace("[D-MID2]",$m2['M2_DVAL01'],$str);
				$str=str_replace("[D-MID2]",$m2['M2_DVAL03'],$str);

				$StrSQL="SELECT * from DAT_M1 where MID = '".$item['MID1']."';";
				$rs_m2=mysqli_query(ConnDB(),$StrSQL);
				$m2 = mysqli_fetch_assoc($rs_m2);
				$str=str_replace("[D-MID1]",$m2['M1_DVAL01'],$str);

				// サプライヤーは名前を表示
				// ただし、発注依頼以降は対象のサプライヤーだけ表示
				//$item['MID1_LIST'] = str_replace(',', "\n", $item['MID1_LIST']);
				$mid1_name = '';
				if($item['STATUS'] == '問い合わせ' || $item['STATUS'] == '見積り依頼' || $item['STATUS'] == '再見積もり依頼') {
					$mid1_list = explode(',', $item['MID1_LIST']);
					foreach($mid1_list as $mid1) {
						$StrSQL="SELECT * from DAT_M1 where MID = '".$mid1."';";
						$rs_m1=mysqli_query(ConnDB(),$StrSQL);
						$m1 = mysqli_fetch_assoc($rs_m1);
						if($m1['M1_DVAL01']!=""){
							$mid1_name .= '<p>'.$m1['M1_DVAL01'].'</p>';
						}
						
					}
				}
				else {
	
					$StrSQL="SELECT MID1 from DAT_FILESTATUS where SHODAN_ID = '".$item['ID']."' order by ID desc;";
					$rs_filestatus=mysqli_query(ConnDB(),$StrSQL);
					$filestatus = mysqli_fetch_assoc($rs_filestatus);
					$StrSQL="SELECT * from DAT_M1 where MID = '".$filestatus['MID1']."';";
					$rs_m1=mysqli_query(ConnDB(),$StrSQL);
					$m1 = mysqli_fetch_assoc($rs_m1);
					if($m1['M1_DVAL01']!=""){
						$mid1_name .= '<p>'.$m1['M1_DVAL01'].'</p>';
					}
					// if($item['ID']=="21"){
					// 	echo "<!--DAT_FILESTATUS:".$mid1_name."-->";
					// }
				}
				$str=str_replace("[D-MID1_LIST]",$mid1_name,$str);


				//1回払い
				$ext_msg = '';
				if($item['STATUS'] == '受注承認(一括前払い)'){
					$ext_msg .= '<p style="color:red;font-weight:bold;">[要請求書発行（サプライヤーから請求書送付後）]<p>';
				}

				//2回払い、マイルストーン
				$StrSQL="SELECT * FROM DAT_SHODAN_DIV WHERE SHODAN_ID='".$item["ID"]."'";
				echo "<!--".$item["ID"].": $StrSQL-->";
				$shodan_div_rs=mysqli_query(ConnDB(),$StrSQL);
				while( $shodan_div_item=mysqli_fetch_assoc($shodan_div_rs) ){
					if($shodan_div_item["STATUS"]=="受注承認(前払い)"){
						//2重の文言表示を防止
						$disp_word="[受注承認（前払い） サプライヤーから受領次第、要請求書発行]";
						if(strpos($ext_msg, $disp_word)!==false){
							$ext_msg .='';
						}else{
							$ext_msg .= '<p style="color:red;font-weight:bold;">'.$disp_word.'<p>';
						}
					
					}else if($shodan_div_item['STATUS'] == '決済者発注承認') {
						//2重の文言表示を防止
						$disp_word="[要承認対応]";
						if(strpos($ext_msg, $disp_word)!==false){ 
							$ext_msg .='';
						}else{
							$ext_msg .= '<p style="color:red;font-weight:bold;">'.$disp_word.'<p>';
						}
					}
				}
				$str=str_replace("[EXT-MSG]",$ext_msg,$str);


				//「ファイル・ステータス」ボタンの未読表示
				//「運営手数料追加」のデータの後に、「見積り送付」のデータがない場合に、未読マークを表示
				$StrSQL="
					SELECT 
						t1.SHODAN_ID, 
						t1.STATUS, 
						t1.NEWDATE
					FROM 
						DAT_FILESTATUS t1
					WHERE
						t1.SHODAN_ID='".$item["ID"]." ' 
						AND t1.STATUS = '運営手数料追加'
						AND NOT EXISTS (
							SELECT 1 
							FROM DAT_FILESTATUS t2 
							WHERE t2.SHODAN_ID = t1.SHODAN_ID 
							  AND t2.STATUS = '見積り送付'
							  AND t2.NEWDATE > t1.NEWDATE
						);";

				$new_rs=mysqli_query(ConnDB(),$StrSQL);
				$new_item_num=mysqli_num_rows($new_rs);
				//while($new_item=mysqli_fetch_assoc($new_rs)){
				//	echo "<!--new_item:";
				//	var_dump($new_item);
				//	echo "-->";
				//}
				if($new_item_num>=1){
					$str=str_replace("[FS_NEW]","NEW",$str);
				}else{
					$str=str_replace("[FS_NEW]","",$str);
				}


				for ($i=0; $i<=$FieldMax; $i=$i+1) {
					if ($FieldAtt[$i]==4) {
						if ($item[$FieldName[$i]]=="") {
							$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
							$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
						} 
					} 
					// HTMLエスケープ処理（一覧表示系）
					$str=str_replace("[".$FieldName[$i]."]",htmlspecialchars($item[$FieldName[$i]]),$str);
					$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($FieldName[$i].":","",htmlspecialchars($item[$FieldName[$i]]))),$str);
					if (is_numeric($item[$FieldName[$i]])) {
						$str=str_replace("[N-".$FieldName[$i]."]",number_format($item[$FieldName[$i]],0),$str);
					} else {
						$str=str_replace("[N-".$FieldName[$i]."]","",$str);
					} 
					if ($item[$FieldName[$i]]==""){
						$str=DispParamNone($str, $FieldName[$i]);
					} else {
						$str=DispParam($str, $FieldName[$i]);
					} 
				}

				if($CurrentRecord%2==0){
					$str=str_replace("[LIST-BG]","bg01",$str);
				} else {
					$str=str_replace("[LIST-BG]","bg02",$str);
				}

				// TODO
				$str=str_replace("[D-FILESTATUS-ID]",$item['FILESTATUS_ID'],$str);
				$dt = str_replace('/', '', $item['NEWDATE']);
				$dt = str_replace(':', '', $dt);
				$dt = str_replace(' ', '', $dt);
				$str=str_replace("[D-Q-ID]",$item['M2_MID'] . '-' . $dt,$str);
				$str=str_replace("[D-M1-NAME]",$item['M1_DVAL01'],$str);

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			} 
		} 


		$str=$strU.$strMain.$strD;

		$str = MakeHTML($str,1,$lid);

		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);

		switch($sort){
			case "1":
				$str=DispParamNone($str, "SYODAN");
				$str=DispParam($str, "SYODAN_ASC");
				$str=DispParamNone($str, "SYODAN_DESC");
				break;
			case "2":
				$str=DispParamNone($str, "SYODAN");
				$str=DispParamNone($str, "SYODAN_ASC");
				$str=DispParam($str, "SYODAN_DESC");
				break;

			case "101":
				$str=DispParamNone($str, "SYODAN2");
				$str=DispParam($str, "SYODAN2_ASC");
				$str=DispParamNone($str, "SYODAN2_DESC");
				break;
			case "102":
				$str=DispParamNone($str, "SYODAN2");
				$str=DispParamNone($str, "SYODAN2_ASC");
				$str=DispParam($str, "SYODAN2_DESC");
				break;

			// TODO
			case "21":
				$str=DispParamNone($str, "Q");
				$str=DispParam($str, "Q_ASC");
				$str=DispParamNone($str, "Q_DESC");
				break;
			case "22":
				$str=DispParamNone($str, "Q");
				$str=DispParamNone($str, "Q_ASC");
				$str=DispParam($str, "Q_DESC");
				break;

			case "3":
				$str=DispParamNone($str, "M2");
				$str=DispParam($str, "M2_ASC");
				$str=DispParamNone($str, "M2_DESC");
				break;
			case "4":
				$str=DispParamNone($str, "M2");
				$str=DispParamNone($str, "M2_ASC");
				$str=DispParam($str, "M2_DESC");
				break;
			case "5":
				$str=DispParamNone($str, "M1");
				$str=DispParam($str, "M1_ASC");
				$str=DispParamNone($str, "M1_DESC");
				break;
			case "6":
				$str=DispParamNone($str, "M1");
				$str=DispParamNone($str, "M1_ASC");
				$str=DispParam($str, "M1_DESC");
				break;
			case "7":
				$str=DispParamNone($str, "TITLE");
				$str=DispParam($str, "TITLE_ASC");
				$str=DispParamNone($str, "TITLE_DESC");
				break;
			case "8":
				$str=DispParamNone($str, "TITLE");
				$str=DispParamNone($str, "TITLE_ASC");
				$str=DispParam($str, "TITLE_DESC");
				break;
			case "9":
				$str=DispParamNone($str, "STATUS");
				$str=DispParam($str, "STATUS_ASC");
				$str=DispParamNone($str, "STATUS_DESC");
				break;
			case "10":
				$str=DispParamNone($str, "STATUS");
				$str=DispParamNone($str, "STATUS_ASC");
				$str=DispParam($str, "STATUS_DESC");
				break;
			case "11":
				$str=DispParamNone($str, "EDITDATE");
				$str=DispParam($str, "EDITDATE_ASC");
				$str=DispParamNone($str, "EDITDATE_DESC");
				break;
			case "12":
				$str=DispParamNone($str, "EDITDATE");
				$str=DispParamNone($str, "EDITDATE_ASC");
				$str=DispParam($str, "EDITDATE_DESC");
				break;
		}
		$str=DispParam($str, "SYODAN");
		$str=DispParamNone($str, "SYODAN_ASC");
		$str=DispParamNone($str, "SYODAN_DESC");

		$str=DispParam($str, "SYODAN2");
		$str=DispParamNone($str, "SYODAN2_ASC");
		$str=DispParamNone($str, "SYODAN2_DESC");

		// TODO
		$str=DispParam($str, "Q");
		$str=DispParamNone($str, "Q_ASC");
		$str=DispParamNone($str, "Q_DESC");

		$str=DispParam($str, "M2");
		$str=DispParamNone($str, "M2_ASC");
		$str=DispParamNone($str, "M2_DESC");
		$str=DispParam($str, "M1");
		$str=DispParamNone($str, "M1_ASC");
		$str=DispParamNone($str, "M1_DESC");
		$str=DispParam($str, "M1");
		$str=DispParamNone($str, "M1_ASC");
		$str=DispParamNone($str, "M1_DESC");
		$str=DispParam($str, "TITLE");
		$str=DispParamNone($str, "TITLE_ASC");
		$str=DispParamNone($str, "TITLE_DESC");
		$str=DispParam($str, "STATUS");
		$str=DispParamNone($str, "STATUS_ASC");
		$str=DispParamNone($str, "STATUS_DESC");
		$str=DispParam($str, "EDITDATE");
		$str=DispParamNone($str, "EDITDATE_ASC");
		$str=DispParamNone($str, "EDITDATE_DESC");
		// $tmp="";
		// $sel=explode("::", "商談ID（昇順）::商談ID（降順）");
		// for($i=0; $i<count($sel); $i++){
		// 	if($sel[$i]!=""){
		// 		if($sort==$i){
		// 			$tmp.="<option value=\"".$i."\" selected>".$sel[$i]."</option>";
		// 		} else {
		// 			$tmp.="<option value=\"".$i."\">".$sel[$i]."</option>";
		// 		}
		// 	}
		// }
		// $str=str_replace("[OPT-SORT]",$tmp,$str);

		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);

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
			$exts = explode("[/\\.]", $_FILES["EP_".$FieldName[$i]]['name']);
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
				pic_resize("data/".$filename, 800,800);
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

	// STATUSに連動してC_STATUSも変更
	switch($FieldValue[9]) {
		case '問い合わせ':
			$c_status = '問い合わせ';
  	  break;
		case '見積り依頼':
			$c_status = '見積り';
  	  break;
		case '再見積り依頼':
			$c_status = '見積り';
  	  break;
		case '見積り送付':
			$c_status = '見積り';
  	  break;
		case '案件の取り下げ':
			$c_status = 'キャンセル';
  	  break;
		case '発注依頼':
			$c_status = '見積り';
  	  break;
		case '決済者発注承認':
			$c_status = '見積り';
  	  break;
		case '受注承認':
			$c_status = '実施中';
  	  break;
		case 'データ納品':
			$c_status = '納品';
  	  break;
		case '物品納品':
			$c_status = '納品';
  	  break;
		case '納品確認':
			$c_status = '納品';
  	  break;
		case '請求':
			$c_status = '請求';
  	  break;
		case '完了':
			$c_status = '完了';
  	  break;
		case 'キャンセル':
			$c_status = 'キャンセル';
  	  break;
		case '辞退':
			$c_status = '辞退';
  	  break;
		default:
			$c_status = '';
	}
	$FieldValue[10] = $c_status;

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


//
//別ファイルへ移行
//
////=========================================================================================================
////名前 
////機能\ 
////引数 
////戻値 
////=========================================================================================================
//function ExportData()
//{
//	eval(globals());
//
//	$csv_data = "";
//
//	$StrSQL="SELECT * FROM ".$TableName." order by ID";
//	$rs=mysqli_query(ConnDB(),$StrSQL);
//	$item=mysqli_num_rows($rs);
//	if($item<>"") {
//		header("Content-Type: application/octet-stream");
//		header("Content-Disposition: attachment; filename=member".date('Ymd').".txt");
//
//		$str="";
//		for ($j=0; $j<=$FieldMax; $j=$j+1){
//			$StrSQL=$StrSQL."`".$FieldName[$j]."`";
//			if ($str!=""){
//				$str=$str."\t";
//			} 
//			$str=$str.$FieldName[$j];
//		}
//		$str=$str."\r\n";
//		$csv_data = $str;
//		$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
//		echo($csv_data);
//		while ($item = mysqli_fetch_assoc($rs)) {
//			$str="";
//			for ($i=0; $i<=$FieldMax; $i=$i+1){
//				if ($i!=0){
//					$str=$str."\t";
//				}
//				$str=$str.str_replace("\r\n", "[rn]", str_replace("\r", "[r]", str_replace("\n", "[n]", str_replace("\t", "[t]", $item[$FieldName[$i]]))));
//			}
//			$csv_data = $str."\r\n";
//			$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
//			echo($csv_data);
//		} 
//	} 
//
//	return $function_ret;
//} 


//=========================================================================================================
//名前 タブ区切りデータのインポート処理
//機能 タブ区切りテキストデータ（ShiftJIS→UTF-8）のエクスポート処理
//引数 なし
//戻値 なし
//=========================================================================================================
function ImportData($obj,$a,$b,$key,$mode)
{
	eval(globals());

	$fp = fopen($_FILES['importfile']['tmp_name'], "r");
	$txt = fgets($fp);

	$cnt=0;
	$cols=explode("\t",$txt);
	for ($i=0; $i<=count($cols); $i=$i+1){
		if($cols[$i]<>""){
			$cnt++;
		}
	}
	$tmp="";
	for ($j=0; $j<$cnt; $j=$j+1){
		if ($tmp!=""){
			$tmp=$tmp.",";
		} 
		$tmp=$tmp."`".trim($cols[$j])."`";
		$fn[$j]=trim($cols[$j]);
	}
	$StrSQLI="INSERT INTO ".$TableName." (".$tmp.") values ([VALS]);";

	while (!feof($fp)) {
		$txt = fgets($fp);
		$txt=str_replace("\"","",$txt);
		$cols=explode("\t",$txt);
		if($cols[0]<>""){
			$StrSQL="SELECT * FROM ".$TableName." where ID='".$cols[0]."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_num_rows($rs);
			if($item=="") {
				$tmp="";
				for ($j=0; $j<$cnt; $j=$j+1){
					if ($tmp!=""){
						$tmp=$tmp.",";
					} 
					$tmp=$tmp."'".trim(str_replace("[rn]","\r\n",str_replace("[r]","\r",str_replace("[n]","\n",str_replace("[t]","\t",str_replace("'","''",$cols[$j]))))))."'";
				}
				$StrSQL=str_replace("[VALS]", $tmp, $StrSQLI);
				$StrSQL = mb_convert_encoding($StrSQL, "UTF-8", "SJIS-win");
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			} else {
				if ($cols[1]!="delete"){
					$tmp="";
					for ($j=1; $j<$cnt; $j=$j+1){
						if ($tmp!=""){
							$tmp=$tmp.",";
						} 
						$tmp=$tmp."`".$fn[$j]."`='".trim(str_replace("[rn]","\r\n",str_replace("[r]","\r",str_replace("[n]","\n",str_replace("[t]","\t",str_replace("'","''",$cols[$j]))))))."'";
					}
					$StrSQL="UPDATE ".$TableName." SET ".$tmp." WHERE ".$FieldName[$FieldKey]."='".$cols[0]."';";
					$StrSQL = mb_convert_encoding($StrSQL, "UTF-8", "SJIS-win");
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} else {
					$StrSQL="DELETE FROM ".$TableName." WHERE ID='".$cols[0]."';";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} 
			} 
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
