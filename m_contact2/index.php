<?php
	session_start();
	require "../config.php";
require "../base.php";
	require './config.php';

set_time_limit(7200);

// ---------------------------------------------
// デバッグセット
// ---------------------------------------------
require_once(__dir__ . '/../handler.php');
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_error_handler('cms_error_handler', E_ALL);
register_shutdown_function('cms_shutdown_handler');
// ---------------------------------------------

//InitSub();//データベースデータの読み込み
ConnDB();//データベース接続
Main();//メイン処理

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function Main()
{

	eval(globals());

	//echo "<!--sessin_mid:".$_SESSION["MID"]."-->";

	if($_POST['mode']==""){
		$type=$_GET['type'];
		$mode=$_GET['mode'];
		$sort=$_GET['sort'];
		$word=$_GET['word'];
		$word2=$_GET['word2'];
		$mid_list=$_GET['mid_list'];
		$m1_id=$_GET['m1_id'];
		$m1_mid=$_GET['m1_mid'];
		$key=$_GET['key'];
		$shodan_id=$_GET['shodan_id'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
		$chk=$_GET['chk'];
		$param_div_id=$_GET['param_div_id'];
		$sub_type=$_GET['sub_type'];

	} else {
		$type=$_POST['type'];
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$word2=$_POST['word2'];
		$mid_list=$_POST['mid_list'];
		$m1_id=$_POST['m1_id'];
		$m1_mid=$_POST['m1_mid'];
		$key=$_POST['key'];
		$shodan_id=$_POST['shodan_id'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$chk=$_POST['chk'];

		if($_POST['param_div_id']=="" || !isset($_POST['param_div_id'])){
			$param_div_id=$_GET['param_div_id'];
		}else{
			$param_div_id=$_POST['param_div_id'];
		}

		$sub_type=$_POST['sub_type'];
	}

	if ($mode==""){
		$mode="new";
	}
	if ($type==""){
		$mode="1";
	}

	echo "<!--sub_type:".$sub_type."-->";

	//echo "<!--";
	//var_dump($_POST);
	//var_dump($_GET);
	//echo "-->";

	// サプライヤー情報取得
	if($key) {
		// ファイルステータスID直接指定
		if($type == '問い合わせ' || $type == '見積り依頼' || $type == '再見積り依頼') {
			$StrSQL="
			  SELECT
				  *
				FROM
					DAT_M1
					inner join DAT_SHODAN
					  on DAT_SHODAN.MID1_LIST like concat('%', DAT_M1.MID, '%')
					inner join DAT_FILESTATUS
				 	 on DAT_FILESTATUS.SHODAN_ID = DAT_SHODAN.ID
				WHERE
					DAT_FILESTATUS.ID = '".$key."'
			";
		}
		else {
			$StrSQL="
			  SELECT
				  *
				FROM
					DAT_M1
					inner join DAT_FILESTATUS
				 	 on DAT_FILESTATUS.MID1 = DAT_M1.MID
				WHERE
					DAT_FILESTATUS.ID = '".$key."'
			";
		}
	}
	else if($mid_list) {
		// MIDで複数指定
		$mid_list2 = '"' . str_replace(',', '","', $mid_list) . '"';
		$StrSQL="
		  SELECT
			  *
			FROM
				DAT_M1
			WHERE
			  DAT_M1.MID in (" . $mid_list2 . ")
		";
	}
	else if($m1_id) {
		// ID直接指定
		$StrSQL="
		  SELECT
			  *
			FROM
				DAT_M1
			WHERE
				DAT_M1.ID = '".$m1_id."'
		";
	}
	else if($m1_mid) {
		// ID直接指定
		$StrSQL="
		  SELECT
			  *
			FROM
				DAT_M1
			WHERE
				DAT_M1.MID = '".$m1_mid."'
		";
	}
	else if($shodan_id) {
		// 商談ID直接指定
		$StrSQL="
		  SELECT
			  *
			FROM
				DAT_M1
				inner join DAT_SHODAN
				  on DAT_SHODAN.MID1_LIST like concat('%', DAT_M1.MID, '%')
			WHERE
				DAT_SHODAN.ID = '".$shodan_id."'
		";
	}
	else if($chk) {
		$StrSQL="
		  SELECT
			 *
			FROM
				DAT_M1
			WHERE
			  DAT_M1.M1_DVAL01 like '%" . $word2 . "%'
			AND
			  DAT_M1.ID IN (" . $chk . ")
		";
	} else {
		// 検索条件による複数サプライヤー
		// O1を使わないのでwordをどうするかはまだ仮状態
		$StrSQL="
		  SELECT
			  *
			FROM
				DAT_M1
			WHERE
			  DAT_M1.M1_DVAL01 like '%" . $word2 . "%'
		";
	}
	//echo('<!--SQL(m1_list):'.$StrSQL.'-->');
// exit;
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m1_list = array();
	$mid1_list = '';
	while ($item = mysqli_fetch_assoc($rs)) {
		if($m1_mid != '') {
			$m1_id = $item['ID'];
		}

		//$m1_idの値が入って、m1_midが空のときはm1_midを復活させる。
		if($m1_id!="" && $m1_mid==""){
			$m1_mid=$item["MID"];
		}

		$m1_list[] = $item;
		//echo('<!--m1_list:'.$item['ID'].'-->');
		$mid1_list .= ($mid1_list != '' ? ',' : '') . $item['MID'];
	}
// echo "<!--m1_list:".$m1_list."-->";
// echo "<!--mid1_list:".$mid1_list."-->";
// exit;


	echo "<!--m1_list:SQL:$StrSQL-->";
	echo "<!--m1_list:".$mid1_list."-->";
	echo "<!--m1_id:".$m1_id."-->";
	echo "<!--m1_mid:".$m1_mid."-->";
	//echo "<!--m1_list:";
	//var_dump($m1_list);
	//echo "-->";

	switch ($mode){
		case "new":
			InitData();
			break;
		case "edit":
			//RequestData($obj,$a,$b,$key,$mode);

			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);

			break;
		case "preview": // プレビュー
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "saveconf":
			//RequestData($obj,$a,$b,$key,$mode);
			break;
		case "saveconf2": // 一時保存
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "save":
			RequestData($obj,$a,$b,$key,$mode);

			if($type=="発注依頼" 
				&& ($_POST['H_M2_ID']=="" || is_null($_POST['H_M2_ID'])) ){
				$mode="h_err";
				break;
			}

			$StrSQL="SELECT * from DAT_M2 where MID = '".$_SESSION['MID']."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$m2 = mysqli_fetch_assoc($rs);

			// DAT_SHODANとDAT_MESSAGEに登録する
			SaveData($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$m2,$word,$word2,$mid_list,$param_div_id,$sub_type);

			// 両者メール
			//if($type == '見積り依頼' || $type == '再見積り依頼'){
			if($type == '問い合わせ' || $type == '見積り依頼' || $type == '再見積り依頼'){
				SendMail($type,$m1_list,$m2);
			} else if($type == '発注依頼'){

				$StrSQL="SELECT * from DAT_M3 where MID = '".$m2["M2_DVAL15"]."';";
				//echo "<!--DAT_M3:".$StrSQL."-->";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$m3 = mysqli_fetch_assoc($rs);
				//echo "<!--発注依頼メール-->";
				// echo "<!--m1_list:".$m1_list."-->";
				//echo "<!--MID_M2:".$m2["MID"]."-->";
				//echo "<!--MID_M3:".$m3["MID"]."-->";
				SendMailhacchu($type,$m1_list,$m2,$m3);
			
			} else if($type=="案件の取り下げ"){
				SendMail_cancel_1($type,$m1_list,$m2,$m3);

			} else if($type=="キャンセル依頼"){
				SendMail_cancel_2($type,$m1_list,$m2,$m3);

			} else {
				
			}

			if($key!="" && $type=="再見積り依頼"){
				SendMail_v1($key);
				SendMail_v1_2($key);

			}else if($shodan_id!="" && $m1_mid!="" && $type=="再見積り依頼"){
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$shodan_id."' ";
				$StrSQL.="AND MID1='".$m1_mid."' ";
				$StrSQL.="AND STATUS='見積り依頼' ";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$x_item = mysqli_fetch_assoc($rs);
				SendMail_v1($x_item["ID"]);
				SendMail_v1_2($x_item["ID"]);
			}

			break;
		case "save2":
			RequestData($obj,$a,$b,$key,$mode);
			SaveData2($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$word,$word2,$mid_list);
			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			break;
	} 

	DispData($type,$mode,$sort,$word,$word2,$mid_list,$m1_id,$m1_mid,$key,$shodan_id,$page,$lid,$m1_list,$mid1_list,$chk,$param_div_id,$sub_type);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($type,$m1_list,$m2)
{

	eval(globals());

	if($type == '問い合わせ'){
		$maildata1 = GetMailTemplate('サプライヤーへの問合せ(M1)');

	} else if($type=="見積り依頼") {
		$maildata1 = GetMailTemplate('サプライヤーへの見積もり依頼(M1)');
		$maildataAdmin = GetMailTemplate('研究者からサプライヤーへの見積もり依頼(ADMIN)');
	
	} else if($type=="再見積り依頼"){
		$maildataAdmin = GetMailTemplate('研究者からサプライヤーへの見積もり依頼(ADMIN)');
	}

	$MailBody1 = empty($maildata1['BODY']) ? "" : $maildata1['BODY'];
	$subject1 = empty($maildata1['TITLE']) ? "" : $maildata1['TITLE'];

	$MailBodyAdmin = empty($maildataAdmin['BODY']) ? "" : $maildataAdmin['BODY'];
	$subjectAdmin = empty($maildataAdmin['TITLE']) ? "" : $maildataAdmin['TITLE'];

	//$MailBody1 = $maildata1['BODY'];
	//$subject1 = $maildata1['TITLE'];
	//
	//$MailBodyAdmin = $maildataAdmin['BODY'];
	//$subjectAdmin = $maildataAdmin['TITLE'];


	$MailBody1=str_replace("[D-TITLE]",$FieldValue[4],$MailBody1);
	$MailBody1=str_replace("[D-COMMENT]",$FieldValue[5],$MailBody1);
	$MailBody1=str_replace("[D-KIGEN]",$FieldValue[8],$MailBody1);


	foreach($m1_list as $item) {

		//echo "<!--";
		//var_dump($item);
		//echo "-->";

		$mailto = $item['EMAIL'];
		// $mailto = "toretoresansan00@gmail.com";

		$MailBody1=str_replace("[D-NAME]",$item['M1_DVAL01'],$MailBody1);

		// 研究者情報
		$MailBody1=str_replace("[M2_DVAL03]",$m2['M2_DVAL03'],$MailBody1);
		$MailBody1=str_replace("[M2_DVAL01]",$m2['M2_DVAL01'],$MailBody1);
		$MailBody1=str_replace("[M2_EMAIL]",$m2['EMAIL'],$MailBody1);
		$MailBody1=str_replace("[MID1]",$item['MID'],$MailBody1);
		$MailBody1=str_replace("[M1_DVAL01]",$item['M1_DVAL01'],$MailBody1);
		$MailBody1=str_replace("[M1_DVAL22]",$item['M1_DVAL22'],$MailBody1);
		$MailBody1=str_replace("[M1_DVAL23]",$item['M1_DVAL23'],$MailBody1);


		//管理者メール
		$MailBodyAdmin=str_replace("[MID2]",$m2['MID'],$MailBodyAdmin);
		$MailBodyAdmin=str_replace("[M2_DVAL01]",$m2['M2_DVAL01'],$MailBodyAdmin);
		$MailBodyAdmin=str_replace("[M2_DVAL03]",$m2['M2_DVAL03'],$MailBodyAdmin);
		
		$MailBodyAdmin=str_replace("[MID1]",$item['MID'],$MailBodyAdmin);
		$MailBodyAdmin=str_replace("[M1_DVAL01]",$item['M1_DVAL01'],$MailBodyAdmin);
		$MailBodyAdmin=str_replace("[M1_DVAL22]",$item['M1_DVAL22'],$MailBodyAdmin);
		$MailBodyAdmin=str_replace("[M1_DVAL23]",$item['M1_DVAL23'],$MailBodyAdmin);



		// $MailBody2 = "--__BOUNDARY__\n";
		// $MailBody2 .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
		// $MailBody2 .= $MailBody1 . "\n";
		// $MailBody2 .=	"--__BOUNDARY__\n";

		// // if($FieldValue[6] != '') {
		// // 	$file = $FieldValue[6];

		// // 	$MailBody2 .= "Content-Type: application/octet-stream; name=\"{$file}\"\n";
		// // 	$MailBody2 .= "Content-Disposition: attachment; filename=\"{$file}\"\n";
		// // 	$MailBody2 .= "Content-Transfer-Encoding: base64\n";
		// // 	$MailBody2 .= "\n";
		// // 	$MailBody2 .= chunk_split(base64_encode(file_get_contents("data/".$file)));
		// // 	$MailBody2 .=	"--__BOUNDARY__\n";
		// // }

		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
		// var_dump($mailto);
//echo "<!--M1_mailto:".$mailto."-->";
		if( $mailto!="" && ($MailBody1!="" || $subject1!="") ){
			mb_send_mail($mailto, $subject1, $MailBody1, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		}
		
		// mb_send_mail($mailto, $subject1.$item['EMAIL'], $MailBody2, "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\nFrom:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

		if($MailBodyAdmin!="" || $subjectAdmin!=""){
			mb_send_mail(SENDER_EMAIL, $subjectAdmin, $MailBodyAdmin, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		}
		
	}

	if($type == '問い合わせ'){
		$maildata2 = GetMailTemplate('サプライヤーへの問合せ(M2)');
	} else if($type == '見積り依頼') {
		//'見積り依頼 再見積り依頼'
		$maildata2 = GetMailTemplate('サプライヤーへの見積もり依頼(M2)');
	} 

	$MailBody2 = empty($maildata2['BODY']) ? "" : $maildata2['BODY'];
	$subject2 = empty($maildata2['TITLE'])? "" : $maildata2['TITLE'];

	$MailBody2=str_replace("[SEND_DATE]",date('Y/m/d'),$MailBody2);

	foreach ($m2 as $idx => $val) {
		$MailBody2=str_replace("[".$idx."]",$m2[$idx],$MailBody2);
		$MailBody2=str_replace("[D-".$idx."]",$m2[$idx],$MailBody2);
	}

	$MailBody2=str_replace("[D-FILE]",$_POST["FILE"],$MailBody2);
	$MailBody2=str_replace("[D-FILE2]",$_POST["FILE2"],$MailBody2);
	$MailBody2=str_replace("[D-FILE3]",$_POST["FILE3"],$MailBody2);
	$MailBody2=str_replace("[D-FILE4]",$_POST["FILE4"],$MailBody2);
	$MailBody2=str_replace("[D-FILE5]",$_POST["FILE5"],$MailBody2);

	for($i=0; $i<=$FieldMax; $i++){
		$MailBody2=str_replace("[".$FieldName[$i]."]",$_POST[$FieldName[$i]],$MailBody2);
		$MailBody2=str_replace("[D-".$FieldName[$i]."]",$_POST[$FieldName[$i]],$MailBody2);
	}



	//$MailBody2=str_replace("[NAME]",$m2['M2_DVAL01'],$MailBody2);
	//$MailBody2=str_replace("[TITLE]",$FieldValue[4],$MailBody2);
	//$MailBody2=str_replace("[COMMENT]",$FieldValue[5],$MailBody2);
	//$MailBody2=str_replace("[KIGEN]",$FieldValue[8],$MailBody2);
	//
	//$MailBody2=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody2);
	//$MailBody2=str_replace("[D-TITLE]",$FieldValue[4],$MailBody2);
	//$MailBody2=str_replace("[D-COMMENT]",$FieldValue[5],$MailBody2);
	//$MailBody2=str_replace("[D-FILE]",$FieldValue[7],$MailBody2);
	//$MailBody2=str_replace("[D-KIGEN]",$FieldValue[8],$MailBody2);

	$mailto = $m2['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
//echo "<!--M2_mailto:".$mailto."-->";
	if($mailto!="" && ($subject2!="" || $MailBody2!="" )){
		mb_send_mail($mailto, $subject2, $MailBody2, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	}
	
		
	

}
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMailhacchu($type,$m1_list,$m2,$m3)
{

	eval(globals());


	if($type == '発注依頼'){
		$maildata = GetMailTemplate('発注依頼(M2)');

	} else {
		$maildata = GetMailTemplate('発注依頼(M2)');
	} 
	
	$m1_view ="";
	foreach($m1_list as $item) {
		if($item['MID'] == $item_filestatus['MID1']) {
			$m1_view .= $item['M1_DVAL01'];
		}
	}
	
	
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);

	if($_POST['TITLE'] != '') {
		$MailBody=str_replace("[D-TITLE]",$_POST['TITLE'],$MailBody);
	}
	else {
		if($shodan_id == '') {
			$MailBodySQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
			//echo('<!--'.$MailBodySQL.'-->');
			$rs=mysqli_query(ConnDB(),$MailBodySQL);
			$item_filestatus = mysqli_fetch_assoc($rs);
			$shodan_id = $item_filestatus['SHODAN_ID'];
		}
		$MailBodySQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
		//echo('<!--'.$MailBodySQL.'-->');
		$rs=mysqli_query(ConnDB(),$MailBodySQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		$MailBody=str_replace("[D-TITLE]",$item_shodan['TITLE'],$MailBody);
	}
	$MailBody=str_replace("[M1_VIEW]",$m1_view,$MailBody);

	$MailBody=str_replace("[D-COMMENT]",str_replace("\n", '<br>', $_POST['COMMENT']),$MailBody); // Dはbr変換
	$MailBody=str_replace("[D-FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$MailBody);
	$MailBody=str_replace("[D-KIGEN]",$_POST['KIGEN'],$MailBody);
	$MailBody=str_replace("[D-NEWDATE]",date('Y/m/d'),$MailBody);

	$MailBody=str_replace("[D-M1_MESSAGE]",str_replace("\n", '<br>', $_POST['M1_MESSAGE']),$MailBody); // Dはbr変換
	$MailBody=str_replace("[D-M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$MailBody);
	$MailBody=str_replace("[D-M1_TRANS_FLG_あり]",($_POST['M1_TRANS_FLG'] == 'あり' ? 'checked' : ''),$MailBody);
	$MailBody=str_replace("[D-M1_TRANS_FLG_なし]",($_POST['M1_TRANS_FLG'] == 'なし' ? 'checked' : ''),$MailBody);
	$MailBody=str_replace("[D-M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$MailBody);
	$MailBody=str_replace("[D-M1_PRICE]",$_POST['M1_PRICE'],$MailBody);
	$MailBody=str_replace("[D-M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE2]",(isset($_POST['M1_FILE2']) ? $_POST['M1_FILE2'] : $_FILES['M1_FILE2']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE3]",(isset($_POST['M1_FILE3']) ? $_POST['M1_FILE3'] : $_FILES['M1_FILE3']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE4]",(isset($_POST['M1_FILE4']) ? $_POST['M1_FILE4'] : $_FILES['M1_FILE4']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE5]",(isset($_POST['M1_FILE5']) ? $_POST['M1_FILE5'] : $_FILES['M1_FILE5']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_KIGEN]",$_POST['M1_KIGEN'],$MailBody);

	$MailBody=str_replace("[D-M2_TITLE]",$_POST['M2_TITLE'],$MailBody);
	$MailBody=str_replace("[D-M2_PRICE]",$_POST['M2_PRICE'],$MailBody);
	$MailBody=str_replace("[D-M2_COMMENT]",str_replace("\n", '<br>', $_POST['M2_COMMENT']),$MailBody); // Dはbr変換

	// 新見積り書
	// -------------------------------------------------------------------------------
	if($type == '発注依頼' || $type == '再見積り依頼') {
		// DBからデータ取得
		if($type == '発注依頼') {
			$MailBodySQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['H_M2_ID'].";";
		}
		else {
			$MailBodySQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['M1_M2_ID'].";";
		}
		//echo('<!--'.$MailBodySQL.'-->');
		$rs=mysqli_query(ConnDB(),$MailBodySQL);
		$item_filestatus2 = mysqli_fetch_assoc($rs);

		$MailBody=str_replace("[D-M2_NOHIN_TYPE]",$item_filestatus2['M2_NOHIN_TYPE'],$MailBody);
		$MailBody=str_replace("[D-M2_PAY_TYPE]",$item_filestatus2['M2_PAY_TYPE'],$MailBody);
		$MailBody=str_replace("[D-M2_QUOTE_NO]",$item_filestatus2['M2_QUOTE_NO'],$MailBody);
		$MailBody=str_replace("[D-M2_STUDY_CODE]",$item_filestatus2['M2_STUDY_CODE'],$MailBody);
		$MailBody=str_replace("[D-M2_DATE]",$item_filestatus2['M2_DATE'],$MailBody);
		$MailBody=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$item_filestatus2['M2_QUOTE_VALID_UNTIL'],$MailBody);
		$MailBody=str_replace("[D-M2_DESCRIPTION]",$item_filestatus2['M2_DESCRIPTION'],$MailBody);
		$MailBody=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:", "", $item_filestatus2['M2_CURRENCY']),$MailBody);
		$MailBody=str_replace("[D-M2_SPECIAL_DISCOUNT]",$item_filestatus2['M2_SPECIAL_DISCOUNT'],$MailBody);
		$MailBody=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $item_filestatus2['M2_SPECIAL_NOTE']),$MailBody); // Dはbr変換

		$MailBody=str_replace("[D-H_M2_ID]",$item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）',$MailBody);
		$MailBody=str_replace("[D-M1_M2_ID]",$item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）',$MailBody);

		$detail_template = 'Details XXX
		Item #:[D-M2_DETAIL_ITEM_XXX]
		Description:[D-M2_DETAIL_DESCRIPTION_XXX]
		Price:[D-M2_DETAIL_PRICE_XXX]
		Note:[D-M2_DETAIL_NOTE_XXX]
		
		';
		$add_detail_area = '';
		$detail_key = 0;

		$MailBodySQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$_POST['H_M2_ID'].";";
		//echo('<!--'.$MailBodySQL.'-->');
		$rs=mysqli_query(ConnDB(),$MailBodySQL);
		while($item_filestatus_detail = mysqli_fetch_assoc($rs)) {
			$detail_no = $detail_key + 1;
			$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);

			$add_detail_area=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area); // Dはbr変換
			$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換

			$detail_key++;
		}
		$MailBody=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$MailBody);
	}
	else {

	$MailBody=str_replace("[D-M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$MailBody);
	$MailBody=str_replace("[D-M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$MailBody);

	$MailBody=str_replace("[D-M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$MailBody);
	$MailBody=str_replace("[D-M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$MailBody);
	$MailBody=str_replace("[D-M2_DATE]",$_POST['M2_DATE'],$MailBody);
	$MailBody=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$MailBody);
	$MailBody=str_replace("[D-M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$MailBody);
	$MailBody=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:", "", $_POST['M2_CURRENCY']),$MailBody);

	$detail_template = '
	Details XXX
	Item #:[D-M2_DETAIL_ITEM_XXX]
	Description:[D-M2_DETAIL_DESCRIPTION_XXX]
	Price:[D-M2_DETAIL_PRICE_XXX]
	Note:[D-M2_DETAIL_NOTE_XXX]
		
	';
						
	$add_detail_area = '';
	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);
	}
	$MailBody=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$MailBody);

	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$MailBody=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$MailBody);
		$MailBody=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_DESCRIPTION'][$detail_key]),$MailBody); // Dはbr変換
		$MailBody=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$MailBody);
		$MailBody=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_NOTE'][$detail_key]),$MailBody); // Dはbr変換
	}

	$MailBody=str_replace("[D-M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$MailBody);
	$MailBody=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $_POST['M2_SPECIAL_NOTE']),$MailBody); // Dはbr変換

	}
	// -------------------------------------------------------------------------------

	$MailBody=str_replace("[D-H_M2_ID]",$_POST['H_M2_ID'],$MailBody);
	$MailBody=str_replace("[D-M1_M2_ID]",$_POST['M1_M2_ID'],$MailBody);

	$MailBody=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $_POST['H_COMMENT']),$MailBody); // Dはbr変換

	$MailBody=str_replace("[D-N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $_POST['N_MESSAGE']),$MailBody); // Dはbr変換

	$MailBody=str_replace("[D-S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-S_MESSAGE]",str_replace("\n", '<br>', $_POST['S_MESSAGE']),$MailBody); // Dはbr変換


	$MailBody=str_replace("[TITLE]",$_POST['TITLE'],$MailBody);
	$MailBody=str_replace("[COMMENT]",$_POST['COMMENT'],$MailBody);
	$MailBody=str_replace("[FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$MailBody);
	$MailBody=str_replace("[FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$MailBody);
	$MailBody=str_replace("[FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$MailBody);
	$MailBody=str_replace("[FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$MailBody);
	$MailBody=str_replace("[FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$MailBody);
	$MailBody=str_replace("[KIGEN]",$_POST['KIGEN'],$MailBody);
	$MailBody=str_replace("[NEWDATE]",date('Y/m/d'),$MailBody);

	$MailBody=str_replace("[M1_MESSAGE]",$_POST['M1_MESSAGE'],$MailBody);
	$MailBody=str_replace("[M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$MailBody);
	$MailBody=str_replace("[M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$MailBody);
	$MailBody=str_replace("[M1_PRICE]",$_POST['M1_PRICE'],$MailBody);
	$MailBody=str_replace("[M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$MailBody);
	$MailBody=str_replace("[M1_KIGEN]",$_POST['M1_KIGEN'],$MailBody);

	$MailBody=str_replace("[M2_TITLE]",$_POST['M2_TITLE'],$MailBody);
	$MailBody=str_replace("[M2_PRICE]",$_POST['M2_PRICE'],$MailBody);
	$MailBody=str_replace("[M2_COMMENT]",$_POST['M2_COMMENT'],$MailBody);

	// 新見積り書
	// -------------------------------------------------------------------------------
	$MailBody=str_replace("[M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$MailBody);
	$MailBody=str_replace("[M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$MailBody);

	$MailBody=str_replace("[M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$MailBody);
	$MailBody=str_replace("[M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$MailBody);
	$MailBody=str_replace("[M2_DATE]",$_POST['M2_DATE'],$MailBody);
	$MailBody=str_replace("[M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$MailBody);
	$MailBody=str_replace("[M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$MailBody);
	$MailBody=str_replace("[M2_CURRENCY]",$_POST['M2_CURRENCY'],$MailBody);

	$detail_template = '
		<input type="hidden" name="M2_DETAIL_ITEM[]" value="[M2_DETAIL_ITEM_XXX]">
		<input type="hidden" name="M2_DETAIL_DESCRIPTION[]" value="[M2_DETAIL_DESCRIPTION_XXX]">
		<input type="hidden" name="M2_DETAIL_PRICE[]" value="[M2_DETAIL_PRICE_XXX]">
		<input type="hidden" name="M2_DETAIL_NOTE[]" value="[M2_DETAIL_NOTE_XXX]">
	';
						
	$hidden_detail_area = '';
	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$hidden_detail_area .= str_replace('XXX', $detail_no, $detail_template);
	}
	$MailBody=str_replace("[HIDDEN_DETAIL_AREA]",$hidden_detail_area,$MailBody);

	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$MailBody=str_replace("[M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$MailBody);
		$MailBody=str_replace("[M2_DETAIL_DESCRIPTION_".$detail_no."]",$_POST['M2_DETAIL_DESCRIPTION'][$detail_key],$MailBody);
		$MailBody=str_replace("[M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$MailBody);
		$MailBody=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",$_POST['M2_DETAIL_NOTE'][$detail_key],$MailBody);
	}

	$MailBody=str_replace("[M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$MailBody);
	$MailBody=str_replace("[M2_SPECIAL_NOTE]",$_POST['M2_SPECIAL_NOTE'],$MailBody);
	// -------------------------------------------------------------------------------

	$MailBody=str_replace("[H_M2_ID]",$_POST['H_M2_ID'],$MailBody);
	$MailBody=str_replace("[M1_M2_ID]",$_POST['M1_M2_ID'],$MailBody);
	$MailBody=str_replace("[H_COMMENT]",$_POST['H_COMMENT'],$MailBody);

	$MailBody=str_replace("[N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$MailBody);
	$MailBody=str_replace("[N_MESSAGE]",$_POST['N_MESSAGE'],$MailBody);

	$MailBody=str_replace("[S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$MailBody);
	$MailBody=str_replace("[S_MESSAGE]",$_POST['S_MESSAGE'],$MailBody);


	$mailto = $m2['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 



	if($type == '発注依頼'){
		$maildata = GetMailTemplate('発注依頼(M3)');
	} else {
		//'見積り依頼 再見積り依頼'
		$maildata = GetMailTemplate('発注依頼(M3)');
	} 
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);

	if($_POST['TITLE'] != '') {
		$MailBody=str_replace("[D-TITLE]",$_POST['TITLE'],$MailBody);
	}
	else {
		if($shodan_id == '') {
			$MailBodySQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
			//echo('<!--'.$MailBodySQL.'-->');
			$rs=mysqli_query(ConnDB(),$MailBodySQL);
			$item_filestatus = mysqli_fetch_assoc($rs);
			$shodan_id = $item_filestatus['SHODAN_ID'];
		}
		$MailBodySQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
		//echo('<!--'.$MailBodySQL.'-->');
		$rs=mysqli_query(ConnDB(),$MailBodySQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		$MailBody=str_replace("[D-TITLE]",$item_shodan['TITLE'],$MailBody);
	}
	$MailBody=str_replace("[M1_VIEW]",$m1_view,$MailBody);

	$MailBody=str_replace("[D-COMMENT]",str_replace("\n", '<br>', $_POST['COMMENT']),$MailBody); // Dはbr変換
	$MailBody=str_replace("[D-FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$MailBody);
	$MailBody=str_replace("[D-FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$MailBody);
	$MailBody=str_replace("[D-KIGEN]",$_POST['KIGEN'],$MailBody);
	$MailBody=str_replace("[D-NEWDATE]",date('Y/m/d'),$MailBody);

	$MailBody=str_replace("[D-M1_MESSAGE]",str_replace("\n", '<br>', $_POST['M1_MESSAGE']),$MailBody); // Dはbr変換
	$MailBody=str_replace("[D-M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$MailBody);
	$MailBody=str_replace("[D-M1_TRANS_FLG_あり]",($_POST['M1_TRANS_FLG'] == 'あり' ? 'checked' : ''),$MailBody);
	$MailBody=str_replace("[D-M1_TRANS_FLG_なし]",($_POST['M1_TRANS_FLG'] == 'なし' ? 'checked' : ''),$MailBody);
	$MailBody=str_replace("[D-M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$MailBody);
	$MailBody=str_replace("[D-M1_PRICE]",$_POST['M1_PRICE'],$MailBody);
	$MailBody=str_replace("[D-M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE2]",(isset($_POST['M1_FILE2']) ? $_POST['M1_FILE2'] : $_FILES['M1_FILE2']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE3]",(isset($_POST['M1_FILE3']) ? $_POST['M1_FILE3'] : $_FILES['M1_FILE3']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE4]",(isset($_POST['M1_FILE4']) ? $_POST['M1_FILE4'] : $_FILES['M1_FILE4']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_FILE5]",(isset($_POST['M1_FILE5']) ? $_POST['M1_FILE5'] : $_FILES['M1_FILE5']['name']),$MailBody);
	$MailBody=str_replace("[D-M1_KIGEN]",$_POST['M1_KIGEN'],$MailBody);

	$MailBody=str_replace("[D-M2_TITLE]",$_POST['M2_TITLE'],$MailBody);
	$MailBody=str_replace("[D-M2_PRICE]",$_POST['M2_PRICE'],$MailBody);
	$MailBody=str_replace("[D-M2_COMMENT]",str_replace("\n", '<br>', $_POST['M2_COMMENT']),$MailBody); // Dはbr変換

	// 新見積り書
	// -------------------------------------------------------------------------------
	if($type == '発注依頼' || $type == '再見積り依頼') {
		// DBからデータ取得
		if($type == '発注依頼') {
			$MailBodySQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['H_M2_ID'].";";
		}
		else {
			$MailBodySQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['M1_M2_ID'].";";
		}
		//echo('<!--'.$MailBodySQL.'-->');
		$rs=mysqli_query(ConnDB(),$MailBodySQL);
		$item_filestatus2 = mysqli_fetch_assoc($rs);

		$MailBody=str_replace("[D-M2_NOHIN_TYPE]",$item_filestatus2['M2_NOHIN_TYPE'],$MailBody);
		$MailBody=str_replace("[D-M2_PAY_TYPE]",$item_filestatus2['M2_PAY_TYPE'],$MailBody);
		$MailBody=str_replace("[D-M2_QUOTE_NO]",$item_filestatus2['M2_QUOTE_NO'],$MailBody);
		$MailBody=str_replace("[D-M2_STUDY_CODE]",$item_filestatus2['M2_STUDY_CODE'],$MailBody);
		$MailBody=str_replace("[D-M2_DATE]",$item_filestatus2['M2_DATE'],$MailBody);
		$MailBody=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$item_filestatus2['M2_QUOTE_VALID_UNTIL'],$MailBody);
		$MailBody=str_replace("[D-M2_DESCRIPTION]",$item_filestatus2['M2_DESCRIPTION'],$MailBody);
		$MailBody=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:", "", $item_filestatus2['M2_CURRENCY']),$MailBody);
		$MailBody=str_replace("[D-M2_SPECIAL_DISCOUNT]",$item_filestatus2['M2_SPECIAL_DISCOUNT'],$MailBody);
		$MailBody=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $item_filestatus2['M2_SPECIAL_NOTE']),$MailBody); // Dはbr変換

		$MailBody=str_replace("[D-H_M2_ID]",$item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）',$MailBody);
		$MailBody=str_replace("[D-M1_M2_ID]",$item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）',$MailBody);

		$detail_template = 'Details XXX
		Item #:[D-M2_DETAIL_ITEM_XXX]
		Description:[D-M2_DETAIL_DESCRIPTION_XXX]
		Price:[D-M2_DETAIL_PRICE_XXX]
		Note:[D-M2_DETAIL_NOTE_XXX]
		
		';
		$add_detail_area = '';
		$detail_key = 0;

		$MailBodySQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$_POST['H_M2_ID'].";";
		//echo('<!--'.$MailBodySQL.'-->');
		$rs=mysqli_query(ConnDB(),$MailBodySQL);
		while($item_filestatus_detail = mysqli_fetch_assoc($rs)) {
			$detail_no = $detail_key + 1;
			$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);

			$add_detail_area=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area); // Dはbr変換
			$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換

			$detail_key++;
		}
		$MailBody=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$MailBody);
	}
	else {

	$MailBody=str_replace("[D-M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$MailBody);
	$MailBody=str_replace("[D-M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$MailBody);

	$MailBody=str_replace("[D-M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$MailBody);
	$MailBody=str_replace("[D-M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$MailBody);
	$MailBody=str_replace("[D-M2_DATE]",$_POST['M2_DATE'],$MailBody);
	$MailBody=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$MailBody);
	$MailBody=str_replace("[D-M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$MailBody);
	$MailBody=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:", "", $_POST['M2_CURRENCY']),$MailBody);

	$detail_template = '
	Details XXX
	Item #:[D-M2_DETAIL_ITEM_XXX]
	Description:[D-M2_DETAIL_DESCRIPTION_XXX]
	Price:[D-M2_DETAIL_PRICE_XXX]
	Note:[D-M2_DETAIL_NOTE_XXX]
		
	';
						
	$add_detail_area = '';
	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);
	}
	$MailBody=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$MailBody);

	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$MailBody=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$MailBody);
		$MailBody=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_DESCRIPTION'][$detail_key]),$MailBody); // Dはbr変換
		$MailBody=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$MailBody);
		$MailBody=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_NOTE'][$detail_key]),$MailBody); // Dはbr変換
	}

	$MailBody=str_replace("[D-M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$MailBody);
	$MailBody=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $_POST['M2_SPECIAL_NOTE']),$MailBody); // Dはbr変換

	}
	// -------------------------------------------------------------------------------

	$MailBody=str_replace("[D-H_M2_ID]",$_POST['H_M2_ID'],$MailBody);
	$MailBody=str_replace("[D-M1_M2_ID]",$_POST['M1_M2_ID'],$MailBody);

	$MailBody=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $_POST['H_COMMENT']),$MailBody); // Dはbr変換

	$MailBody=str_replace("[D-N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $_POST['N_MESSAGE']),$MailBody); // Dはbr変換

	$MailBody=str_replace("[D-S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$MailBody);
	$MailBody=str_replace("[D-S_MESSAGE]",str_replace("\n", '<br>', $_POST['S_MESSAGE']),$MailBody); // Dはbr変換


	$MailBody=str_replace("[TITLE]",$_POST['TITLE'],$MailBody);
	$MailBody=str_replace("[COMMENT]",$_POST['COMMENT'],$MailBody);
	$MailBody=str_replace("[FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$MailBody);
	$MailBody=str_replace("[FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$MailBody);
	$MailBody=str_replace("[FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$MailBody);
	$MailBody=str_replace("[FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$MailBody);
	$MailBody=str_replace("[FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$MailBody);
	$MailBody=str_replace("[KIGEN]",$_POST['KIGEN'],$MailBody);
	$MailBody=str_replace("[NEWDATE]",date('Y/m/d'),$MailBody);

	$MailBody=str_replace("[M1_MESSAGE]",$_POST['M1_MESSAGE'],$MailBody);
	$MailBody=str_replace("[M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$MailBody);
	$MailBody=str_replace("[M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$MailBody);
	$MailBody=str_replace("[M1_PRICE]",$_POST['M1_PRICE'],$MailBody);
	$MailBody=str_replace("[M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$MailBody);
	$MailBody=str_replace("[M1_KIGEN]",$_POST['M1_KIGEN'],$MailBody);

	$MailBody=str_replace("[M2_TITLE]",$_POST['M2_TITLE'],$MailBody);
	$MailBody=str_replace("[M2_PRICE]",$_POST['M2_PRICE'],$MailBody);
	$MailBody=str_replace("[M2_COMMENT]",$_POST['M2_COMMENT'],$MailBody);

	// 新見積り書
	// -------------------------------------------------------------------------------
	$MailBody=str_replace("[M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$MailBody);
	$MailBody=str_replace("[M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$MailBody);

	$MailBody=str_replace("[M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$MailBody);
	$MailBody=str_replace("[M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$MailBody);
	$MailBody=str_replace("[M2_DATE]",$_POST['M2_DATE'],$MailBody);
	$MailBody=str_replace("[M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$MailBody);
	$MailBody=str_replace("[M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$MailBody);
	$MailBody=str_replace("[M2_CURRENCY]",$_POST['M2_CURRENCY'],$MailBody);

	$detail_template = '
		<input type="hidden" name="M2_DETAIL_ITEM[]" value="[M2_DETAIL_ITEM_XXX]">
		<input type="hidden" name="M2_DETAIL_DESCRIPTION[]" value="[M2_DETAIL_DESCRIPTION_XXX]">
		<input type="hidden" name="M2_DETAIL_PRICE[]" value="[M2_DETAIL_PRICE_XXX]">
		<input type="hidden" name="M2_DETAIL_NOTE[]" value="[M2_DETAIL_NOTE_XXX]">
	';
						
	$hidden_detail_area = '';
	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$hidden_detail_area .= str_replace('XXX', $detail_no, $detail_template);
	}
	$MailBody=str_replace("[HIDDEN_DETAIL_AREA]",$hidden_detail_area,$MailBody);

	for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
		$detail_no = $detail_key + 1;
		$MailBody=str_replace("[M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$MailBody);
		$MailBody=str_replace("[M2_DETAIL_DESCRIPTION_".$detail_no."]",$_POST['M2_DETAIL_DESCRIPTION'][$detail_key],$MailBody);
		$MailBody=str_replace("[M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$MailBody);
		$MailBody=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",$_POST['M2_DETAIL_NOTE'][$detail_key],$MailBody);
	}

	$MailBody=str_replace("[M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$MailBody);
	$MailBody=str_replace("[M2_SPECIAL_NOTE]",$_POST['M2_SPECIAL_NOTE'],$MailBody);
	// -------------------------------------------------------------------------------

	$MailBody=str_replace("[H_M2_ID]",$_POST['H_M2_ID'],$MailBody);
	$MailBody=str_replace("[M1_M2_ID]",$_POST['M1_M2_ID'],$MailBody);
	$MailBody=str_replace("[H_COMMENT]",$_POST['H_COMMENT'],$MailBody);

	$MailBody=str_replace("[N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$MailBody);
	$MailBody=str_replace("[N_MESSAGE]",$_POST['N_MESSAGE'],$MailBody);

	$MailBody=str_replace("[S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$MailBody);
	$MailBody=str_replace("[S_MESSAGE]",$_POST['S_MESSAGE'],$MailBody);



	$mailto = $m3['EMAIL'];
//echo "<!--mailto_m3:".$mailto."-->";
	// $mailto = "toretoresansan00@gmail.com";
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		

	//管理者（admin）へ通知
	$maildata = GetMailTemplate('発注依頼(ADMIN)');	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];
	$mailto=SENDER_EMAIL;

	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);
	$MailBody=str_replace("[SHODAN_ID]",$_POST['shodan_id'],$MailBody);

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 


}


//=========================================================================================================
//名前 
//機能\ 案件の取り下げ用
//引数 
//戻値 
//=========================================================================================================
function SendMail_cancel_1($type,$m1_list,$m2,$m3)
{
	eval(globals());

	$maildata = GetMailTemplate('キャンセル');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);
	$MailBody=str_replace("[SHODAN_ID]",$_POST['shodan_id'],$MailBody);

	//echo "<!--";
	//var_dump($m1_list);
	//echo "-->";

	foreach($m1_list as $item){
		foreach($item as $idx => $val){
			$MailBody=str_replace("[".$idx."]",$val,$MailBody);
			$MailBody=str_replace("[D-".$idx."]",$val,$MailBody);
		}
	}

	foreach($m2 as $idx => $val){
		$MailBody=str_replace("[".$idx."]",$val,$MailBody);
		$MailBody=str_replace("[D-".$idx."]",$val,$MailBody);
	}

	foreach($m1_list as $item){
		$mailto = $item["EMAIL"];
		//echo "<!--sendmail_cancle_1:$mailto-->";
		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
		mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	}
	

}



//=========================================================================================================
//名前 
//機能\ キャンセル依頼
//引数 
//戻値 
//=========================================================================================================
function SendMail_cancel_2($type,$m1_list,$m2,$m3)
{
	eval(globals());

	//サプライヤーへメール
	$maildata = GetMailTemplate('キャンセル依頼(M1)');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);
	$MailBody=str_replace("[SHODAN_ID]",$_POST['shodan_id'],$MailBody);

	//echo "<!--";
	//var_dump($m1_list);
	//echo "-->";

	foreach($m1_list as $item){
		foreach($item as $idx => $val){
			$MailBody=str_replace("[".$idx."]",$val,$MailBody);
			$MailBody=str_replace("[D-".$idx."]",$val,$MailBody);
		}
	}

	foreach($m2 as $idx => $val){
		$MailBody=str_replace("[".$idx."]",$val,$MailBody);
		$MailBody=str_replace("[D-".$idx."]",$val,$MailBody);
	}

	foreach($m1_list as $item){
		$mailto = $item["EMAIL"];
		//echo "<!--sendmail_cancle_1:$mailto-->";
		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
		mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	}


	//管理者へメール
	$maildata = GetMailTemplate('キャンセル依頼(ADMIN)');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);
	$MailBody=str_replace("[SHODAN_ID]",$_POST['shodan_id'],$MailBody);

	//echo "<!--";
	//var_dump($m1_list);
	//echo "-->";

	foreach($m1_list as $item){
		foreach($item as $idx => $val){
			$MailBody=str_replace("[".$idx."]",$val,$MailBody);
			$MailBody=str_replace("[D-".$idx."]",$val,$MailBody);
		}
	}

	foreach($m2 as $idx => $val){
		$MailBody=str_replace("[".$idx."]",$val,$MailBody);
		$MailBody=str_replace("[D-".$idx."]",$val,$MailBody);
	}

	foreach($m1_list as $item){
		$mailto = $item["EMAIL"];
		//echo "<!--sendmail_cancle_1:$mailto-->";
		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
		mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	}
	

}



//=========================================================================================================
//名前 
//機能\ 
//引数  $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート6');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	//echo "<pre>";
	//var_dump($maildata);
	//echo "</pre>";

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$key."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemF = mysqli_fetch_assoc($rs);
	//foreach ($itemF as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}
	
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$itemF["MID1"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID1 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID1 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$itemF["MID2"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID2 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID2 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$mailto = $item_MID1["EMAIL"];

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMail_v:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}


//=========================================================================================================
//名前 
//機能\ 
//引数  $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1_2($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート7');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	//echo "<pre>";
	//var_dump($maildata);
	//echo "</pre>";

	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$key."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemF = mysqli_fetch_assoc($rs);
	//foreach ($itemF as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}
	
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$itemF["MID1"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID1 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID1 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$itemF["MID2"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID2 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID2 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$mailto = $item_MID2["EMAIL"];

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMail_v:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}



//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispData($type,$mode,$sort,$word,$word2,$mid_list,$m1_id,$m1_mid,$key,$shodan_id,$page,$lid,$m1_list,$mid1_list,$chk,$param_div_id,$sub_type)
{

	eval(globals());

	$html_prev = 'contact';
	switch($type) {
		case '問い合わせ':
			$html_prev = 'contact';
			break;
		case '見積り依頼':
			$html_prev = 'm1';
			break;
		case '再見積り依頼':
			$html_prev = 'm1b';
			break;
		case '見積り送付':
		case '追加見積り':
			$html_prev = 'm2';
			break;
		case '発注依頼':
			$html_prev = 'h';
			break;
		case '案件の取り下げ':
			$html_prev = 'cancel';
			break;
		case 'データ納品':
			$html_prev = 'n';
			break;
		case '物品納品':
			$html_prev = 'b';
			break;
		case '請求':
			$html_prev = 's';
			break;
		//新規発注キャンセルフロー用ステータス
		case 'キャンセル依頼':
			$html_prev = 'cancel_req';
			break;
		//納品確認用のフォームを新規作成
		case '納品確認':
			$html_prev = 'chknohin';
			break;
		case 'サンプル送付依頼':
			$html_prev = 'sample';
			break;
		case '完了後対応':
			$html_prev = 'done1';
			break;
	}
  $htmlnew = './' . $html_prev . '_edit.html';
  $htmledit = './' . $html_prev . '_edit.html';
  $htmlconf = './' . $html_prev . '_conf.html';
  $htmlconf2 = './' . $html_prev . '_save.html';
  $htmlpreview = './' . $html_prev . '_preview.html';
  $htmlend = './' . $html_prev . '_end.html';
  $htmlend2 = './' . $html_prev . '_end2.html';
  $htmldisp = './' . $html_prev . '_disp.html';
	$htmldisp_frame = './' . $html_prev . '_disp_frame.html';
  $htmlerr = './' . $html_prev . '_edit.html';

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
			case "disp":
				$filename=$htmldisp;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "disp_frame":
				$filename=$htmldisp_frame;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "back":
				$filename=$htmledit;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "preview":
				$msg=ErrorCheck();
				if ($msg==""){
					$filename=$htmlpreview;
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
			case "saveconf2":
				$msg=ErrorCheck();
				if ($msg==""){
					$filename=$htmlconf2;
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
				$filename=$htmlend;
				$msg01="保存";
				$msg02="";
				$errmsg="";
				break;
			case "save2":
				$filename=$htmlend2;
				$msg01="保存";
				$msg02="";
				$errmsg="";
				break;
			case "delete":
				$filename=$htmlend;
				$msg01="削除";
				$msg02="";
				$errmsg="";
				break;
		} 
//echo "<!--filename:".$filename."-->";
		$fp=$DOCUMENT_ROOT.$filename;
		$str=@file_get_contents($fp);

		$str = MakeHTML($str,0,$lid);

		if($type=="発注依頼" && $mode == 'h_err' 
			&& ($_POST['H_M2_ID']=="" || is_null($_POST['H_M2_ID'])) ){
			$str="不正なデータです";
		}

		if ($mode=="new"){
			$str=str_replace("[S-NEWDATA]","",$str);
			$str=str_replace("[E-NEWDATA]","",$str);
			$str=str_replace("[S-EDITDATA]","<!--",$str);
			$str=str_replace("[E-EDITDATA]","-->",$str);
		} else {
			$str=str_replace("[S-NEWDATA]","<!--",$str);
			$str=str_replace("[E-NEWDATA]","-->",$str);
			$str=str_replace("[S-EDITDATA]","",$str);
			$str=str_replace("[E-EDITDATA]","",$str);
		} 

		/*
		for ($i=0; $i<=$FieldMax; $i=$i+1){
			if ($FieldAtt[$i]==4){

				if(strstr($FieldValue[$i],"s.gif") == true){
					$str=str_replace("[S-".$FieldName[$i]."]","<!--",$str);
					$str=str_replace("[E-".$FieldName[$i]."]","-->",$str);
				} else {
					$str=str_replace("[S-".$FieldName[$i]."]","",$str);
					$str=str_replace("[E-".$FieldName[$i]."]","",$str);
				} 
			} else {
				if ($FieldValue[$i]==""){
					$str=str_replace("[S-".$FieldName[$i]."]","<!--",$str);
					$str=str_replace("[E-".$FieldName[$i]."]","-->",$str);
				} else {
					$str=str_replace("[S-".$FieldName[$i]."]","",$str);
					$str=str_replace("[E-".$FieldName[$i]."]","",$str);
				} 

			} 
			$str=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$str);
			if ($FieldAtt[$i]=="1"){
				$strtmp="";
				$strtmp=$strtmp."";
				$tmp=explode("::",$FieldParam[$i]);
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input type='radio' name='".$FieldName[$i]."' value='".$FieldName[$i].":".$tmp[$j]."' required><label>".$tmp[$j]."</label></li>";
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
					$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"radio\" name=\"".$FieldName[$i]."\" value=\"".$FieldName[$i].":".$tmp[$j]."\" required><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit" ) && $FieldValue[$i]!="") {
					$str=str_replace("\"".$FieldValue[$i]."\"","\"".$FieldValue[$i]."\" checked",$str);
				} 
			} 

			if ($FieldAtt[$i]=="3"){
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul class='mlist25p'>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input type='checkbox' name='".$FieldName[$i]."[]' value='".$FieldName[$i].":".$tmp[$j]."' required>&nbsp;".$tmp[$j]."</li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$tmp=explode("\t",$FieldValue[$i]);
					for ($j=0; $j<count($tmp); $j=$j+1) {
						$str=str_replace("'".$tmp[$j]."'","'".$tmp[$j]."' checked",$str);
					}
				} 
			} 

			$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br />",str_replace($FieldName[$i].":","",$FieldValue[$i])),$str);
			if (is_numeric($FieldValue[$i])) {
				$str=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$str);
			} else {
				$str=str_replace("[N-".$FieldName[$i]."]","",$str);
			} 
		}
		*/

	//「発注依頼」のSHIP TO
	//決済者情報の「国」、「郵便番号」、「住所」
	$StrSQL="SELECT ID,MID,M2_DVAL15 FROM DAT_M2 WHERE MID='".$_SESSION['MID']."';";
	$rsM2tmp=mysqli_query(ConnDB(),$StrSQL);
	$itemM2tmp = mysqli_fetch_assoc($rsM2tmp);
	$h_ship_to=array();
	if(!is_null($itemM2tmp["M2_DVAL15"]) && $itemM2tmp["M2_DVAL15"]!=""){
		$StrSQL="SELECT ID,MID,M2_DVAL05,M2_DVAL06,M2_DVAL07 FROM DAT_M3 WHERE MID='".$itemM2tmp["M2_DVAL15"]."';";
		$rsM3tmp=mysqli_query(ConnDB(),$StrSQL);
		$itemM3tmp_num = mysqli_num_rows($rsM3tmp);
		$itemM3tmp = mysqli_fetch_assoc($rsM3tmp);
		if($itemM3tmp_num>0){
			$str=DispParam($str,"KESAISYA_SHIP_TO");
		}
		//国
		$h_ship_to["v1"]["H_SHIP_TO_SPT_1"]=$itemM3tmp["M2_DVAL05"];
		//郵便番号
		$h_ship_to["v1"]["H_SHIP_TO_SPT_2"]=$itemM3tmp["M2_DVAL06"];
		//住所
		$h_ship_to["v1"]["H_SHIP_TO_SPT_3"]=$itemM3tmp["M2_DVAL07"];
	}
	//上記でひょうじになってなかったら非表示
	$str=DispParamNone($str,"KESAISYA_SHIP_TO");
	$str=str_replace("[H_SHIP_TO]",json_encode($h_ship_to),$str);

	$num_chr=16;
	foreach ($h_ship_to as $idx1 => $val1) {
		$addr="";
		foreach ($val1 as $idx2 => $val2) {
			if($idx2=="H_SHIP_TO_SPT_1"){
				continue;
			}
			$addr.=$val2;
		}
		if(mb_strlen($addr, 'UTF-8') >= $num_chr){
			$str=str_replace("[name_h_ship_to_".$idx1."]", mb_substr($addr, 0, $num_chr, 'UTF-8')."...", $str);
		}else{
			$str=str_replace("[name_h_ship_to_".$idx1."]", $addr, $str);
		}
	}


	//分割支払い対応
	//分割識別ID
	$str=str_replace("[DIV_ID]",$param_div_id,$str);
	$str=str_replace("[D-DIV_ID]",$param_div_id,$str);
	

	$str=str_replace("[MSG]",$msg01,$str);
	$str=str_replace("[NEXTMODE]",$msg02,$str);
	if($errmsg<>""){
		$str=str_replace("[ERRMSG]",$errmsg,$str);
		$str=str_replace("[ERR-S]","",$str);
		$str=str_replace("[ERR-E]","",$str);
	} else {
		$str=str_replace("[ERR-S]","<!--",$str);
		$str=str_replace("[ERR-E]","-->",$str);
	}
	$str=str_replace("[SORT]",$sort,$str);
	$str=str_replace("[WORD]",$word,$str);
	$str=str_replace("[WORD2]",$word2,$str);
	$str=str_replace("[MID_LIST]",$mid_list,$str);
	$str=str_replace("[M1_ID]",$m1_id,$str);
	$str=str_replace("[M1_MID]",$m1_mid,$str);
	$str=str_replace("[PAGE]",$page,$str);
	//$str=str_replace("[KEY]",$key,$str);
	$str=str_replace("[TYPE]",$type,$str);
	//$sub_typeは一括前払いフロー用
	$str=str_replace("[SUB_TYPE]",$sub_type,$str);
	$str=str_replace("[SHODAN_ID]",$shodan_id,$str);
	$str=str_replace("[LID]",$lid,$str);

	$str=str_replace("[MID1_LIST]",$mid1_list,$str);

	$str=str_replace("[CHK]",$chk,$str);

	$str=str_replace("[m1_mid]",$m1_mid,$str);
	$str=str_replace("[param_div_id]",$param_div_id,$str);

	

	// typeごとの違い
	switch($type) {
		case '問い合わせ':
			$str=str_replace("[PAGE_TITLE]",'サプライヤーへの問い合わせ',$str);
			break;
		case '見積り依頼':
			$str=str_replace("[PAGE_TITLE]",'サプライヤーへの見積り依頼',$str);
			break;
		case '再見積り依頼':
			$str=str_replace("[PAGE_TITLE]",'サプライヤーへの再見積り依頼',$str);
			break;
		case '発注依頼':
			$str=str_replace("[PAGE_TITLE]",'サプライヤーへの発注依頼',$str);
			break;
		case '案件の取り下げ':
			$str=str_replace("[PAGE_TITLE]",'案件の取り下げ',$str);
			break;
		case '見積り送付':
		case '追加見積り':
			$str=str_replace("[PAGE_TITLE]",'見積り書',$str);
			break;
		case '受注承認':
			$str=str_replace("[PAGE_TITLE]",'受注承認',$str);
			break;
		case 'データ納品':
			$str=str_replace("[PAGE_TITLE]",'データ納品',$str);
			break;
		case '物品納品':
			$str=str_replace("[PAGE_TITLE]",'物品納品',$str);
			break;
		case '納品確認':
			$str=str_replace("[PAGE_TITLE]",'納品確認',$str);
			break;
		case 'サンプル送付依頼':
			$str=str_replace("[PAGE_TITLE]",'サンプル送付依頼',$str);
			break;
		case '請求':
			$str=str_replace("[PAGE_TITLE]",'請求書',$str);
			break;
		case '完了後対応':
			$str=str_replace("[PAGE_TITLE]",'追加請求書（研究者）',$str);
			break;

		case 'キャンセル':
			$str=str_replace("[PAGE_TITLE]",'キャンセル',$str);
			break;
		case '辞退':
			$str=str_replace("[PAGE_TITLE]",'商談の辞退',$str);
			break;
		case 'キャンセル依頼':
			$str=str_replace("[PAGE_TITLE]",'発注キャンセルのリクエスト',$str);
			break;
	}

	//shodan_idに値が入ってる場合はDAT_SHODANはUPDATEされるが、
	//その際、TITLEのインプットを非表示にし、値の表示のみにする。
	if($type=="見積り依頼" && $shodan_id!=""){
		$str=DispParamNone($str,"TITLE_INPUT");
		$str=DispParam($str,"TITLE_HIDDEN");
		$str=DispParam($str,"TITLE_DISP");
	}else{
		$str=DispParam($str,"TITLE_INPUT");
		$str=DispParamNone($str,"TITLE_HIDDEN");
		$str=DispParamNone($str,"TITLE_DISP");
	}


  // ファイルアップロード
	if($mode == 'saveconf' || $mode == 'preview' || $mode == 'saveconf2') {
		foreach($_FILES as $file_key => $file_val) {
			$filename = $_FILES[$file_key]["name"];
			if($filename!="" && !is_null($filename)){
				move_uploaded_file($_FILES[$file_key]["tmp_name"], __dir__."/../a_filestatus/data/".$filename);
			}
		}
	}

	if($mode == 'saveconf' || $mode == 'preview' || $mode == 'saveconf2' || $mode == 'back') {
	echo "<!--1a-->";
	echo "<!--POST:";
	var_dump($_POST);
	echo "-->";
	  // フォームからデータ取得
		if($_POST['TITLE'] != '') {
			$str=str_replace("[D-TITLE]",$_POST['TITLE'],$str);
		}
		else {
			if($shodan_id == '') {
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
				//echo('<!--'.$StrSQL.'-->');
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$item_filestatus = mysqli_fetch_assoc($rs);
				$shodan_id = $item_filestatus['SHODAN_ID'];
			}
			$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_shodan = mysqli_fetch_assoc($rs);

			$str=str_replace("[D-TITLE]",$item_shodan['TITLE'],$str);
		}

		//echo "<!--_FILES:";
		//var_dump($_FILES);
		//echo "-->";
		//echo "<!--_POST:";
		//var_dump($_POST);
		//echo "-->";
		//echo "<!--評価式：";
		//echo ((isset($_FILES['EP_FILE']['name']) && $_FILES['EP_FILE']['name']!="") ? "ファイルある" : "ファイルなし");
		//echo "-->";

		$str=str_replace("[D-COMMENT]",str_replace("\n", '<br>', $_POST['COMMENT']),$str); // Dはbr変換

		$str=str_replace("[D-FILE]",((isset($_FILES['EP_FILE']['name']) && $_FILES['EP_FILE']['name']!="") ? $_FILES['EP_FILE']['name'] : $_POST['FILE']),$str);
		$str=str_replace("[D-FILE2]",((isset($_FILES['EP_FILE2']['name']) && $_FILES['EP_FILE2']['name']!="") ? $_FILES['EP_FILE2']['name'] : $_POST['FILE2']),$str);
		$str=str_replace("[D-FILE3]",((isset($_FILES['EP_FILE3']['name']) && $_FILES['EP_FILE3']['name']!="") ? $_FILES['EP_FILE3']['name'] : $_POST['FILE3']),$str);
		$str=str_replace("[D-FILE4]",((isset($_FILES['EP_FILE4']['name']) && $_FILES['EP_FILE4']['name']!="") ? $_FILES['EP_FILE4']['name'] : $_POST['FILE4']),$str);
		$str=str_replace("[D-FILE5]",((isset($_FILES['EP_FILE5']['name']) && $_FILES['EP_FILE5']['name']!="") ? $_FILES['EP_FILE5']['name'] : $_POST['FILE5']),$str);
		//$str=str_replace("[D-FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$str);
		//$str=str_replace("[D-FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$str);
		//$str=str_replace("[D-FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$str);
		//$str=str_replace("[D-FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$str);
		//$str=str_replace("[D-FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$str);
		
		$str=str_replace("[D-KIGEN]",$_POST['KIGEN'],$str);
		$str=str_replace("[D-NEWDATE]",date('Y/m/d'),$str);

		$str=str_replace("[D-M1_MESSAGE]",str_replace("\n", '<br>', $_POST['M1_MESSAGE']),$str); // Dはbr変換
		$str=str_replace("[D-M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$str);
		$str=str_replace("[D-M1_TRANS_FLG_あり]",($_POST['M1_TRANS_FLG'] == 'あり' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_FLG_なし]",($_POST['M1_TRANS_FLG'] == 'なし' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$str);
		$str=str_replace("[D-M1_PRICE]",$_POST['M1_PRICE'],$str);

		$str=str_replace("[D-M1_FILE]",((isset($_FILES['EP_M1_FILE']['name']) && $_FILES['EP_M1_FILE']['name']!="") ? $_FILES['EP_M1_FILE']['name'] : $_POST['M1_FILE']),$str);
		$str=str_replace("[D-M1_FILE2]",((isset($_FILES['EP_M1_FILE2']['name']) && $_FILES['EP_M1_FILE2']['name']!="") ? $_FILES['EP_M1_FILE2']['name'] : $_POST['M1_FILE2']),$str);
		$str=str_replace("[D-M1_FILE3]",((isset($_FILES['EP_M1_FILE3']['name']) && $_FILES['EP_M1_FILE3']['name']!="") ? $_FILES['EP_M1_FILE3']['name'] : $_POST['M1_FILE3']),$str);
		$str=str_replace("[D-M1_FILE4]",((isset($_FILES['EP_M1_FILE4']['name']) && $_FILES['EP_M1_FILE4']['name']!="") ? $_FILES['EP_M1_FILE4']['name'] : $_POST['M1_FILE4']),$str);
		$str=str_replace("[D-M1_FILE5]",((isset($_FILES['EP_M1_FILE5']['name']) && $_FILES['EP_M1_FILE5']['name']!="") ? $_FILES['EP_M1_FILE5']['name'] : $_POST['M1_FILE5']),$str);
		//$str=str_replace("[D-M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$str);
		//$str=str_replace("[D-M1_FILE2]",(isset($_POST['M1_FILE2']) ? $_POST['M1_FILE2'] : $_FILES['M1_FILE2']['name']),$str);
		//$str=str_replace("[D-M1_FILE3]",(isset($_POST['M1_FILE3']) ? $_POST['M1_FILE3'] : $_FILES['M1_FILE3']['name']),$str);
		//$str=str_replace("[D-M1_FILE4]",(isset($_POST['M1_FILE4']) ? $_POST['M1_FILE4'] : $_FILES['M1_FILE4']['name']),$str);
		//$str=str_replace("[D-M1_FILE5]",(isset($_POST['M1_FILE5']) ? $_POST['M1_FILE5'] : $_FILES['M1_FILE5']['name']),$str);
		
		$str=str_replace("[D-M1_KIGEN]",$_POST['M1_KIGEN'],$str);

		$str=str_replace("[D-M2_TITLE]",$_POST['M2_TITLE'],$str);
		$str=str_replace("[D-M2_PRICE]",$_POST['M2_PRICE'],$str);
		$str=str_replace("[D-M2_COMMENT]",str_replace("\n", '<br>', $_POST['M2_COMMENT']),$str); // Dはbr変換

		//納品確認用
		$str=str_replace("[D-CHKNOHIN_MESSAGE]",str_replace("\n", '<br>', $_POST['CHKNOHIN_MESSAGE']),$str);// Dはbr変換
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['CHKNOHIN_ID']." AND ID IS NOT NULL AND ID!=''";
		$chknohin_rs=mysqli_query(ConnDB(),$StrSQL);
		$chknohin_item=mysqli_fetch_assoc($chknohin_rs);
		$SCNo_str="";
		$SCNo_ary=array(
			"SCNo_yy" => "", 
			"SCNo_mm" => "", 
			"SCNo_dd" => "", 
			"SCNo_cnt" => "", 
			"SCNo_else1" => "", 
			"SCNo_else2" => "", 
		);
		$SCNo_ary=array(
			"SCNo_yy" => $chknohin_item['SCNo_yy'], 
			"SCNo_mm" => $chknohin_item['SCNo_mm'], 
			"SCNo_dd" => $chknohin_item['SCNo_dd'], 
			"SCNo_cnt" => $chknohin_item['SCNo_cnt'], 
			"SCNo_else1" => $chknohin_item['SCNo_else1'], 
			"SCNo_else2" => $chknohin_item['SCNo_else2'], 
		);
		$SCNo_str=formatAlphabetId($SCNo_ary);
		$str=str_replace("[D-CHKNOHIN_ID]",$_POST['CHKNOHIN_ID'],$str);
		$str=str_replace("[D-CHKNOHIN_ID_SCNO]",$SCNo_str." Version".$chknohin_item["M2_VERSION"],$str);


		//新規追加項目
		$str=str_replace("[D-TEMPR]",str_replace("TEMPR:","",$_POST['TEMPR']),$str);
		$str=str_replace("[D-SAMPLE]",$_POST['SAMPLE'],$str);
		$str=str_replace("[D-ORIGIN]",$_POST['ORIGIN'],$str);
		$str=str_replace("[D-LEGAL]",$_POST['LEGAL'],$str);
		$str=str_replace("[D-UNIT]",str_replace("UNIT:","",$_POST['UNIT']),$str);
		$str=str_replace("[TEMPR]",$_POST['TEMPR'],$str);
		$str=str_replace("[SAMPLE]",$_POST['SAMPLE'],$str);
		$str=str_replace("[ORIGIN]",$_POST['ORIGIN'],$str);
		$str=str_replace("[LEGAL]",$_POST['LEGAL'],$str);
		$str=str_replace("[UNIT]",$_POST['UNIT'],$str);

		$strtmp="";
		$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
		$tmp=explode("::","常温::冷蔵::冷凍");
		$fname="TEMPR";
		for ($j=0; $j<count($tmp); $j=$j+1) {
			$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
		}
		//echo "<!--strtmp:".$strtmp."-->";
		$str=str_replace("[OPT-".$fname."]",$strtmp,$str);
		if(isset($_POST[$fname]) && $_POST[$fname]!=""){
			$str=str_replace("'".$_POST[$fname]."'","'".$_POST[$fname]."' selected",$str);
		}

		$strtmp="";
		$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
		$tmp=explode("::","￥::$::€::￡");
		$fname="UNIT";
		for ($j=0; $j<count($tmp); $j=$j+1) {
			$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
		}
		//echo "<!--strtmp:".$strtmp."-->";
		$str=str_replace("[OPT-".$fname."]",$strtmp,$str);
		if(isset($_POST[$fname]) && $_POST[$fname]!=""){
			$str=str_replace("'".$_POST[$fname]."'","'".$_POST[$fname]."' selected",$str);
		}


		//「サンプル送付依頼」フォーム
		echo "<!--sample_hokan_tmper:".$_POST['SAMPLE_HOKAN_TMPER']."-->";
		$str=str_replace("[D-SAMPLE_HOKAN_TMPER]",$_POST['SAMPLE_HOKAN_TMPER'],$str);
		$str=str_replace("[D-SAMPLE_HAISO_CO]",$_POST['SAMPLE_HAISO_CO'],$str);
		$str=str_replace("[D-SAMPLE_NUMBER]",$_POST['SAMPLE_NUMBER'],$str);
		$str=str_replace("[D-SAMPLE_DATE]",$_POST['SAMPLE_DATE'],$str);
		$str=str_replace("[D-SAMPLE_TIME]",$_POST['SAMPLE_TIME'],$str);
		$str=str_replace("[D-SAMPLE_MSG]",str_replace("\n", '<br>', $_POST['SAMPLE_MSG']),$str); // Dはbr変換
		$str=str_replace("[D-SAMPLE_FIlE]",((isset($_FILES['EP_SAMPLE_FIlE']['name']) && $_FILES['EP_SAMPLE_FIlE']['name']!="") ? $_FILES['EP_SAMPLE_FIlE']['name'] : $_POST['SAMPLE_FIlE']),$str);



		// 新見積り書
		// -------------------------------------------------------------------------------
		if($type == '発注依頼' || $type == '再見積り依頼') {
		  // DBからデータ取得
			if($type == '発注依頼') {
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['H_M2_ID'].";";
			}
			else {
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['M1_M2_ID'].";";
			}
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus2 = mysqli_fetch_assoc($rs);

			$str=str_replace("[D-M2_NOHIN_TYPE]",$item_filestatus2['M2_NOHIN_TYPE'],$str);
			$str=str_replace("[D-M2_PAY_TYPE]",$item_filestatus2['M2_PAY_TYPE'],$str);
			$str=str_replace("[D-M2_QUOTE_NO]",$item_filestatus2['M2_QUOTE_NO'],$str);
			$str=str_replace("[D-M2_STUDY_CODE]",$item_filestatus2['M2_STUDY_CODE'],$str);
			$str=str_replace("[D-M2_DATE]",$item_filestatus2['M2_DATE'],$str);
			$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$item_filestatus2['M2_QUOTE_VALID_UNTIL'],$str);
			$str=str_replace("[D-M2_DESCRIPTION]",$item_filestatus2['M2_DESCRIPTION'],$str);
			$str=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:", "", $item_filestatus2['M2_CURRENCY']),$str);
			$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$item_filestatus2['M2_SPECIAL_DISCOUNT'],$str);
			$str=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $item_filestatus2['M2_SPECIAL_NOTE']),$str); // Dはbr変換

			//分割払いの処理
			$div_id=$item_filestatus2["DIV_ID"];
			$tmp="";
			$tmp=explode("-", $div_id);
				//echo "<!--";
				//var_dump($tmp);
				//echo "-->";
			$part="";
			$disp_part="";
			if($item_filestatus2["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
				$part=$tmp[2];
				$disp_part="分割払い".$part;
			}
			
			$SCNo_str="";
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$SCNo_ary=array(
				"SCNo_yy" => $item_filestatus2['SCNo_yy'], 
				"SCNo_mm" => $item_filestatus2['SCNo_mm'], 
				"SCNo_dd" => $item_filestatus2['SCNo_dd'], 
				"SCNo_cnt" => $item_filestatus2['SCNo_cnt'], 
				"SCNo_else1" => $item_filestatus2['SCNo_else1'], 
				"SCNo_else2" => $item_filestatus2['SCNo_else2'], 
			);
			$SCNo_str=formatAlphabetId($SCNo_ary);
			$str=str_replace("[D-H_M2_ID]",$SCNo_str,$str);
			$str=str_replace("[D-M1_M2_ID]",$SCNo_str,$str);
			//$str=str_replace("[D-H_M2_ID]",$item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . ', '.$disp_part.'）',$str);
			//$str=str_replace("[D-M1_M2_ID]",$item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . ', '.$disp_part.'）',$str);

			$detail_template = '
            <div class="formset__item formset__item-head">
              <div class="formset__ttl2"><strong>Details XXX</strong></div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Item #</div>
              <div class="formset__input">[D-M2_DETAIL_ITEM_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">内容</div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price</div>
              <div class="formset__input">[D-M2_DETAIL_PRICE_ORIGIN_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Special discount</div>
              <div class="formset__input">[D-M2_DETAIL_SP_DISCOUNT_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Note</div>
              <div class="formset__input">[D-M2_DETAIL_NOTE_XXX]</div>
            </div>
			';
			$add_detail_area = '';
			$detail_key = 0;

			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$_POST['H_M2_ID'].";";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			while($item_filestatus_detail = mysqli_fetch_assoc($rs)) {
				$detail_no = $detail_key + 1;
				$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);

				$origin_price=0;
				$m2_detail_quantity = is_numeric($item_filestatus_detail['M2_DETAIL_QUANTITY']) ? $item_filestatus_detail['M2_DETAIL_QUANTITY'] : 0;
				$m2_detail_unit_price = is_numeric($item_filestatus_detail['M2_DETAIL_UNIT_PRICE']) ? $item_filestatus_detail['M2_DETAIL_UNIT_PRICE'] : 0;
				$origin_price=$m2_detail_quantity*$m2_detail_unit_price;
				echo "<!--debug:[D-M2_DETAIL_PRICE_ORIGIN_$detail_no]-->";
				echo "<!--debug:origin_price:$origin_price-->";
				$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_ORIGIN_".$detail_no."]",$origin_price,$add_detail_area);

				$add_detail_area=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area);
				$add_detail_area=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area); // Dはbr変換
				$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area);
				$add_detail_area=str_replace("[D-M2_DETAIL_SP_DISCOUNT_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_SP_DISCOUNT'],$add_detail_area);
				$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換

				$detail_key++;
			}
			$str=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$str);
		}
		else {

		$str=str_replace("[D-M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$str);
		$str=str_replace("[D-M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$str);

		$str=str_replace("[D-M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$str);
		$str=str_replace("[D-M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$str);
		$str=str_replace("[D-M2_DATE]",$_POST['M2_DATE'],$str);
		$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[D-M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$str);
		$str=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:", "", $_POST['M2_CURRENCY']),$str);

		$detail_template = '
            <div class="formset__item formset__item-head">
              <div class="formset__ttl2"><strong>Details XXX</strong></div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Item #</div>
              <div class="formset__input">[D-M2_DETAIL_ITEM_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">内容</div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price</div>
              <div class="formset__input">[D-M2_DETAIL_PRICE_ORIGIN_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Special discount</div>
              <div class="formset__input">[D-M2_DETAIL_SP_DISCOUNT_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Note</div>
              <div class="formset__input">[D-M2_DETAIL_NOTE_XXX]</div>
            </div>
		';
							
		$add_detail_area = '';
		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
			$detail_no = $detail_key + 1;
			$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);
		}
		$str=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$str);

		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
			$detail_no = $detail_key + 1;
			
			$origin_price=0;
			$m2_detail_quantity = is_numeric($_POST['M2_DETAIL_QUANTITY'][$detail_key]) ? $_POST['M2_DETAIL_QUANTITY'][$detail_key] : 0;
			$m2_detail_unit_price = is_numeric($_POST['M2_DETAIL_UNIT_PRICE'][$detail_key]) ? $_POST['M2_DETAIL_UNIT_PRICE'][$detail_key] : 0;
			$origin_price=$m2_detail_quantity*$m2_detail_unit_price;
			$str=str_replace("[D-M2_DETAIL_PRICE_ORIGIN_".$detail_no."]",$origin_price,$str);
			

			$str=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_DESCRIPTION'][$detail_key]),$str); // Dはbr変換
			$str=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_SP_DISCOUNT_".$detail_no."]",$_POST['M2_DETAIL_SP_DISCOUNT'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_NOTE'][$detail_key]),$str); // Dはbr変換
		}

		$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $_POST['M2_SPECIAL_NOTE']),$str); // Dはbr変換

		}
		// -------------------------------------------------------------------------------

		$str=str_replace("[D-H_M2_ID]",$_POST['H_M2_ID'],$str);
		$str=str_replace("[D-M1_M2_ID]",$_POST['M1_M2_ID'],$str);

		$str=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $_POST['H_COMMENT']),$str); // Dはbr変換

		$str=str_replace("[D-N_FILE]",((isset($_FILES['EP_N_FILE']['name']) && $_FILES['EP_N_FILE']['name']!="") ? $_FILES['EP_N_FILE']['name'] : $_POST['N_FILE']),$str);
		//$str=str_replace("[D-N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $_POST['N_MESSAGE']),$str); // Dはbr変換

		$str=str_replace("[D-S_FILE]",((isset($_FILES['EP_S_FILE']['name']) && $_FILES['EP_S_FILE']['name']!="") ? $_FILES['EP_S_FILE']['name'] : $_POST['S_FILE']),$str);
		//$str=str_replace("[D-S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[D-S_MESSAGE]",str_replace("\n", '<br>', $_POST['S_MESSAGE']),$str); // Dはbr変換


		$str=str_replace("[TITLE]",$_POST['TITLE'],$str);
		$str=str_replace("[COMMENT]",$_POST['COMMENT'],$str);

		$str=str_replace("[FILE]",((isset($_FILES['EP_FILE']['name']) && $_FILES['EP_FILE']['name']!="") ? $_FILES['EP_FILE']['name'] : $_POST['FILE']),$str);
		$str=str_replace("[FILE2]",((isset($_FILES['EP_FILE2']['name']) && $_FILES['EP_FILE2']['name']!="") ? $_FILES['EP_FILE2']['name'] : $_POST['FILE2']),$str);
		$str=str_replace("[FILE3]",((isset($_FILES['EP_FILE3']['name']) && $_FILES['EP_FILE3']['name']!="") ? $_FILES['EP_FILE3']['name'] : $_POST['FILE3']),$str);
		$str=str_replace("[FILE4]",((isset($_FILES['EP_FILE4']['name']) && $_FILES['EP_FILE4']['name']!="") ? $_FILES['EP_FILE4']['name'] : $_POST['FILE4']),$str);
		$str=str_replace("[FILE5]",((isset($_FILES['EP_FILE5']['name']) && $_FILES['EP_FILE5']['name']!="") ? $_FILES['EP_FILE5']['name'] : $_POST['FILE5']),$str);
		//$str=str_replace("[FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$str);
		//$str=str_replace("[FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$str);
		//$str=str_replace("[FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$str);
		//$str=str_replace("[FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$str);
		//$str=str_replace("[FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$str);
		
		$str=str_replace("[KIGEN]",$_POST['KIGEN'],$str);
		$str=str_replace("[NEWDATE]",date('Y/m/d'),$str);

		$str=str_replace("[M1_MESSAGE]",$_POST['M1_MESSAGE'],$str);
		$str=str_replace("[M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$str);
		$str=str_replace("[M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$str);
		$str=str_replace("[M1_PRICE]",$_POST['M1_PRICE'],$str);
		$str=str_replace("[M1_FILE]",((isset($_FILES['EP_M1_FILE']['name']) && $_FILES['EP_M1_FILE']['name']!="") ? $_FILES['EP_M1_FILE']['name'] : $_POST['M1_FILE']),$str);
		//$str=str_replace("[M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$str);
		$str=str_replace("[M1_KIGEN]",$_POST['M1_KIGEN'],$str);

		$str=str_replace("[M2_TITLE]",$_POST['M2_TITLE'],$str);
		$str=str_replace("[M2_PRICE]",$_POST['M2_PRICE'],$str);
		$str=str_replace("[M2_COMMENT]",$_POST['M2_COMMENT'],$str);

		//納品確認用
		$str=str_replace("[CHKNOHIN_MESSAGE]",$_POST['CHKNOHIN_MESSAGE'],$str);
		$str=str_replace("[CHKNOHIN_ID]",$_POST['CHKNOHIN_ID'],$str);


		//「サンプル送付依頼」フォーム
		$str=str_replace("[SAMPLE_HOKAN_TMPER]",$_POST['SAMPLE_HOKAN_TMPER'],$str);
		$str=str_replace("[SAMPLE_HAISO_CO]",$_POST['SAMPLE_HAISO_CO'],$str);
		$str=str_replace("[SAMPLE_NUMBER]",$_POST['SAMPLE_NUMBER'],$str);
		$str=str_replace("[SAMPLE_DATE]",$_POST['SAMPLE_DATE'],$str);
		$str=str_replace("[SAMPLE_TIME]",$_POST['SAMPLE_TIME'],$str);
		$str=str_replace("[SAMPLE_MSG]",$_POST['SAMPLE_MSG'],$str);
		$str=str_replace("[SAMPLE_FIlE]",((isset($_FILES['EP_SAMPLE_FIlE']['name']) && $_FILES['EP_SAMPLE_FIlE']['name']!="") ? $_FILES['EP_SAMPLE_FIlE']['name'] : $_POST['SAMPLE_FIlE']),$str);
		

		// 新見積り書
		// -------------------------------------------------------------------------------
		$str=str_replace("[M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$str);
		$str=str_replace("[M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$str);

		$str=str_replace("[M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$str);
		$str=str_replace("[M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$str);
		$str=str_replace("[M2_DATE]",$_POST['M2_DATE'],$str);
		$str=str_replace("[M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$str);
		$str=str_replace("[M2_CURRENCY]",$_POST['M2_CURRENCY'],$str);

		$detail_template = '
            <input type="hidden" name="M2_DETAIL_ITEM[]" value="[M2_DETAIL_ITEM_XXX]">
            <input type="hidden" name="M2_DETAIL_DESCRIPTION[]" value="[M2_DETAIL_DESCRIPTION_XXX]">
            <input type="hidden" name="M2_DETAIL_PRICE[]" value="[M2_DETAIL_PRICE_XXX]">
            <input type="hidden" name="M2_DETAIL_SP_DISCOUNT[]" value="[M2_DETAIL_SP_DISCOUNT_XXX]">
            <input type="hidden" name="M2_DETAIL_NOTE[]" value="[M2_DETAIL_NOTE_XXX]">
		';
							
		$hidden_detail_area = '';
		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
			$detail_no = $detail_key + 1;
			$hidden_detail_area .= str_replace('XXX', $detail_no, $detail_template);
		}
		$str=str_replace("[HIDDEN_DETAIL_AREA]",$hidden_detail_area,$str);

		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
			$detail_no = $detail_key + 1;
			$str=str_replace("[M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_DESCRIPTION_".$detail_no."]",$_POST['M2_DETAIL_DESCRIPTION'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_SP_DISCOUNT_".$detail_no."]",$_POST['M2_DETAIL_SP_DISCOUNT'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",$_POST['M2_DETAIL_NOTE'][$detail_key],$str);
		}

		$str=str_replace("[M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[M2_SPECIAL_NOTE]",$_POST['M2_SPECIAL_NOTE'],$str);
		// -------------------------------------------------------------------------------

		$str=str_replace("[H_M2_ID]",$_POST['H_M2_ID'],$str);
		$str=str_replace("[M1_M2_ID]",$_POST['M1_M2_ID'],$str);
		$str=str_replace("[H_COMMENT]",$_POST['H_COMMENT'],$str);

		$str=str_replace("[N_FILE]",((isset($_FILES['EP_N_FILE']['name']) && $_FILES['EP_N_FILE']['name']!="") ? $_FILES['EP_N_FILE']['name'] : $_POST['N_FILE']),$str);
		//$str=str_replace("[N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[N_MESSAGE]",$_POST['N_MESSAGE'],$str);

		$str=str_replace("[S_FILE]",((isset($_FILES['EP_S_FILE']['name']) && $_FILES['EP_S_FILE']['name']!="") ? $_FILES['EP_S_FILE']['name'] : $_POST['S_FILE']),$str);
		//$str=str_replace("[S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[S_MESSAGE]",$_POST['S_MESSAGE'],$str);


		//「発注依頼」のSHIP TO
		//決済者情報の「国」、「郵便番号」、「住所」
		$str=str_replace("[H_SHIP_TO_SPT_1]",$_POST['H_SHIP_TO_SPT_1'],$str);
		$str=str_replace("[H_SHIP_TO_SPT_2]",$_POST['H_SHIP_TO_SPT_2'],$str);
		$str=str_replace("[H_SHIP_TO_SPT_3]",$_POST['H_SHIP_TO_SPT_3'],$str);

		$str=str_replace("[D-H_SHIP_TO_SPT_1]",$_POST['H_SHIP_TO_SPT_1'],$str);
		$str=str_replace("[D-H_SHIP_TO_SPT_2]",$_POST['H_SHIP_TO_SPT_2'],$str);
		$str=str_replace("[D-H_SHIP_TO_SPT_3]",$_POST['H_SHIP_TO_SPT_3'],$str);


		if($type == '見積り送付' || $type=="追加見積り" || $type=="再見積り依頼") {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='見積り送付' order by ID desc;";
			if($type=="再見積り依頼"){
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$_POST['M1_M2_ID']."' and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			}else{
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			}

			
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			while($item_filestatus = mysqli_fetch_assoc($rs)) {
				$selected = '';
				if($item_filestatus['ID'] == $_POST['H_M2_ID']) {
					$selected = ' selected ';
				}
				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $item_filestatus['SCNo_yy'], 
					"SCNo_mm" => $item_filestatus['SCNo_mm'], 
					"SCNo_dd" => $item_filestatus['SCNo_dd'], 
					"SCNo_cnt" => $item_filestatus['SCNo_cnt'], 
					"SCNo_else1" => $item_filestatus['SCNo_else1'], 
					"SCNo_else2" => $item_filestatus['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);
				$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" ' . $selected . ' >' .$SCNo_str. ' Version'.$item_filestatus['M2_VERSION'] . '</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" ' . $selected . '>' . $item_filestatus['ID'] . '</option>';

				$h_m2_detail[$item_filestatus['ID']] = $item_filestatus;
				$h_m2_detail[$item_filestatus['ID']]['detail'] = array();

				$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$item_filestatus['ID']." order by ID asc;";
				//echo('<!--'.$StrSQL.'-->');
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				while($item_filestatus_detail = mysqli_fetch_assoc($rs2)) {
					$h_m2_detail[$item_filestatus['ID']]['detail'][] = $item_filestatus_detail;
				}
			}
			$str=str_replace("[H_M2_LIST]",$h_m2_list,$str);
			$str=str_replace("[H_M2_DETAIL]",json_encode($h_m2_detail),$str);
		}


		if($type == '発注依頼') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";

			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			while($item_filestatus = mysqli_fetch_assoc($rs)) {
				$selected = '';
				if($item_filestatus['ID'] == $_POST['H_M2_ID']) {
					$selected = ' selected ';
				}

				//分割払いの処理
				$div_id=$item_filestatus["DIV_ID"];
				$tmp="";
				$tmp=explode("-", $div_id);
				//echo "<!--";
				//var_dump($tmp);
				//echo "-->";
				$part="";
				$disp_part="";
				if($item_filestatus["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
					$part=$tmp[2];
					$pre_part=$tmp[0]."-".$tmp[1];
					$disp_part="分割払い".$part;
				}

				if( $item_filestatus["M2_PAY_TYPE"]=='Milestone' && $part=="Part0" ){
					continue;
				}
				//2回払いの場合は、Part0の発注書を表示させ、保存の段階でPart1のデータに替える。
				if($item_filestatus["M2_PAY_TYPE"]=='Split' && $part!="Part0"){
					continue;
				}

				//if( ($item_filestatus["M2_PAY_TYPE"]=='Milestone' || $item_filestatus["M2_PAY_TYPE"]=='Split') && $part=="Part0" ){
				//	continue;
				//}

				//既に発注依頼されてるものは表示しない
				$StrSQL="SELECT ID,STATUS,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' and STATUS='発注依頼' ";
				$StrSQL.=" and DIV_ID='".$div_id."' ";
				$StrSQL.=" order by ID desc ";
				$h_done_rs=mysqli_query(ConnDB(),$StrSQL);
				$h_done_num = mysqli_num_rows($h_done_rs);
				if($h_done_num >=1){
					continue;
				}

				//2回払いの場合はPart1に発注依頼があったらその後は発注できないようにする
				//既に発注依頼されてるものは表示しない
				if($item_filestatus["M2_PAY_TYPE"]=='Split' && $part=="Part0"){
					$StrSQL="SELECT ID,STATUS,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' and STATUS='発注依頼' ";
					$StrSQL.=" and DIV_ID='".$pre_part."-Part1' ";
					$StrSQL.=" order by ID desc ";
					$h_done_rs=mysqli_query(ConnDB(),$StrSQL);
					$h_done_num = mysqli_num_rows($h_done_rs);
					if($h_done_num >=1){
						continue;
					}
				}

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $item_filestatus['SCNo_yy'], 
					"SCNo_mm" => $item_filestatus['SCNo_mm'], 
					"SCNo_dd" => $item_filestatus['SCNo_dd'], 
					"SCNo_cnt" => $item_filestatus['SCNo_cnt'], 
					"SCNo_else1" => $item_filestatus['SCNo_else1'], 
					"SCNo_else2" => $item_filestatus['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);
				$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" ' . $selected . ' >' .$SCNo_str. ' Version'.$item_filestatus['M2_VERSION'] . '</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" ' . $selected . '>' . $item_filestatus['ID'] . '</option>';

				$h_m2_detail[$item_filestatus['ID']] = $item_filestatus;
				$h_m2_detail[$item_filestatus['ID']]['detail'] = array();

				$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$item_filestatus['ID']." order by ID asc;";
				//echo('<!--'.$StrSQL.'-->');
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				while($item_filestatus_detail = mysqli_fetch_assoc($rs2)) {
					$h_m2_detail[$item_filestatus['ID']]['detail'][] = $item_filestatus_detail;
				}
			}
			$str=str_replace("[H_M2_LIST]",$h_m2_list,$str);
			$str=str_replace("[H_M2_DETAIL]",json_encode($h_m2_detail),$str);
			if($h_m2_list==""){
				$str=DispParam($str,"HATYU_CAUTION");
				$str=DispParamNone($str,"HATYU_TYPICAL");
			}else{
				$str=DispParamNone($str,"HATYU_CAUTION");
				$str=DispParam($str,"HATYU_TYPICAL");
			}
		}


		if($type=="納品確認"){
			echo "<!--納品確認1-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid." ";
			$StrSQL.="' and (STATUS='データ納品' or STATUS='物品納品' or STATUS='サプライヤーが納品(一括前払い)') order by ID asc";
			echo "<!--$StrSQL-->";
			$n_rs=mysqli_query(ConnDB(),$StrSQL);
			$chknohin_id_list="";
			while($n_item=mysqli_fetch_assoc($n_rs)){
				//一括払いでもDIV_IDを設定するようになったので、
				//分割払いと一括払いそれぞれの共通処理
				$div_id="";
				$div_id=$n_item["DIV_ID"];

				//納品確認がされてない、データをとってきて一覧にする。
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' ";
				$StrSQL.=" AND DIV_ID='".$div_id."' ";
				$StrSQL.=" AND (STATUS='見積り送付' OR STATUS='追加見積り') ";
				$StrSQL.=" AND DIV_ID NOT IN (SELECT DIV_ID FROM DAT_FILESTATUS WHERE DIV_ID = '".$div_id."' ";
				$StrSQL.=" AND (STATUS = '納品確認' OR STATUS = '研究者が納品承認(一括前払い)') ) ";
				$StrSQL.=" ORDER BY ID DESC ";
				echo "<!--$StrSQL-->";
				$nlist_rs=mysqli_query(ConnDB(),$StrSQL);
				$nlist_item=mysqli_fetch_assoc($nlist_rs);
				//echo "<!--nlist_item:";
				//var_dump($nlist_item);
				//echo "-->";

				$selected = '';
				if($nlist_item['ID'] == $_POST["CHKNOHIN_ID"]) {
					$selected = ' selected ';
				}

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $nlist_item['SCNo_yy'], 
					"SCNo_mm" => $nlist_item['SCNo_mm'], 
					"SCNo_dd" => $nlist_item['SCNo_dd'], 
					"SCNo_cnt" => $nlist_item['SCNo_cnt'], 
					"SCNo_else1" => $nlist_item['SCNo_else1'], 
					"SCNo_else2" => $nlist_item['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);

				if(!is_null($nlist_item['ID']) && $nlist_item['ID']!=""){
					$chknohin_id_list .= '<option value="' . $nlist_item['ID'] . '" ' . $selected . ' >' .$SCNo_str. ' Version' . $nlist_item['M2_VERSION'] .'</option>';
				}

			}
			$str=str_replace("[CHKNOHIN_ID_LIST]",$chknohin_id_list,$str);

		}

	}
	else if($key || ($shodan_id != '' && $type == '問い合わせ')) {
	echo "<!--2a-->";
	//disp_frameモード等
    if($key == '') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='問い合わせ';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus0 = mysqli_fetch_assoc($rs);
			$key = $item_filestatus0['ID'];
			$str=str_replace("[KEY]",$key,$str);
		}

	  // DBからデータ取得
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
		echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_filestatus = mysqli_fetch_assoc($rs);

		$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$item_filestatus['SHODAN_ID'].";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		$word = $item_shodan['CATEGORY'];
		$word2 = $item_shodan['KEYWORD'];

		//以下の再見積り依頼のタイトルの更新は先方の要望により廃止
		//
		////再見積り依頼時のとタイトル更新の特別処理
		//if($type=="再見積り依頼" && $mode=="new"){
		//	$str=str_replace("[D-TITLE]","【再見積り依頼】".$item_shodan['TITLE'],$str);
		//	$str=str_replace("[TITLE]","【再見積り依頼】".$item_shodan['TITLE'],$str);
		//}

		if($type=="再見積り依頼" && ($mode=="disp_frame" || $mode=="disp") ){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus["M1_M2_ID"].";";
			echo('<!--'.$StrSQL.'-->');
			$rs3=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus3 = mysqli_fetch_assoc($rs3);

			$SCNo_str="";
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$SCNo_ary=array(
				"SCNo_yy" => $item_filestatus3['SCNo_yy'], 
				"SCNo_mm" => $item_filestatus3['SCNo_mm'], 
				"SCNo_dd" => $item_filestatus3['SCNo_dd'], 
				"SCNo_cnt" => $item_filestatus3['SCNo_cnt'], 
				"SCNo_else1" => $item_filestatus3['SCNo_else1'], 
				"SCNo_else2" => $item_filestatus3['SCNo_else2'], 
			);
			//echo "<!--";
			//var_dump($item_filestatus3);
			//echo "-->";
			$SCNo_str=formatAlphabetId($SCNo_ary);
			$str=str_replace("[D-H_M2_ID]",$SCNo_str.' Version'.$item_filestatus3['M2_VERSION'],$str);
			$str=str_replace("[D-M1_M2_ID]",$SCNo_str.' Version'.$item_filestatus3['M2_VERSION'],$str);
		}

		if($type=="請求"){
			if($item_filestatus["S_STATUS"]=="請求（研究者）"){
				$str=DispParam($str,"SEIKYU_R");
				$str=DispParamNone($str,"SEIKYU_S");
			}else{
				$str=DispParamNone($str,"SEIKYU_R");
				$str=DispParam($str,"SEIKYU_S");
			}
		}

		if($type=="完了後対応"){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_filestatus["SHODAN_ID"]." ";
			$StrSQL.=" AND DIV_ID='".$item_filestatus["DIV_ID"]."' ";
			$StrSQL.=" AND (STATUS='請求' OR STATUS='請求書送付(前払い)' OR STATUS='請求書送付(一括前払い)' ) ";
			$StrSQL.=" AND S_STATUS='請求（研究者）' ";
			echo('<!--完了後対応：'.$StrSQL.'-->');
			$done1_rs=mysqli_query(ConnDB(),$StrSQL);
			$done1_item = mysqli_fetch_assoc($done1_rs);
			echo "<!--完了後対応：";
			var_dump($done1_item);
			echo "-->";
			$pdf_invoice_url2="/a_filestatus/data/".$done1_item["ID"]."/".$done1_item["S2_FILE"];
			$str=str_replace("[INVOICE_URL]",$pdf_invoice_url2,$str);
			$str=str_replace("[S2_FILE]",$done1_item["S2_FILE"],$str);
			$str=str_replace("[S2_MESSAGE]",$done1_item["S2_MESSAGE"],$str);
			$str=str_replace("[S2_NEWDATE]",$done1_item["NEWDATE"],$str);
			$str=str_replace("[S_ADD_CHARGE2]",$item_filestatus["S_ADD_CHARGE2"],$str);

			$str=str_replace("[D-S2_FILE]",$done1_item["S2_FILE"],$str);
			$str=str_replace("[D-S2_MESSAGE]",$done1_item["S2_MESSAGE"],$str);
			$str=str_replace("[D-S2_NEWDATE]",$done1_item["NEWDATE"],$str);
			$str=str_replace("[D-S_ADD_CHARGE2]",$item_filestatus["S_ADD_CHARGE2"],$str);


		}

		//発注書プレビュープルダウンのプレビュー内容の日付は「見積り送付」ではなく、「決済者発注承認」または、「発注依頼」のデータをもってくる。
		//（１）管理画面の発注書の発注日：③決裁者ありの場合：決裁者が発注承認した日
		//（２）管理が目の発注書の発注日：④決裁者なしの場合：研究者が発注依頼した日
		//上記を実現するために、「決済者発注承認」のデータがあればそのデータのNEWDATEを使い、
		//それがなければ、「発注依頼」のデータを使うようにした。

		$StrSQL="SELECT ID,SHODAN_ID,STATUS,NEWDATE FROM DAT_FILESTATUS WHERE DIV_ID='".$item_filestatus["DIV_ID"]."' ";
		$StrSQL.=" AND STATUS='決済者発注承認' ";
		$preview_k_rs=mysqli_query(ConnDB(),$StrSQL);
		$preview_k_item=mysqli_fetch_assoc($preview_k_rs);
		$preview_k_num=mysqli_num_rows($preview_k_rs);

		$StrSQL="SELECT ID,SHODAN_ID,STATUS,NEWDATE FROM DAT_FILESTATUS WHERE DIV_ID='".$item_filestatus["DIV_ID"]."' ";
		$StrSQL.=" AND STATUS='発注依頼' ";
		$preview_h_rs=mysqli_query(ConnDB(),$StrSQL);
		$preview_h_item=mysqli_fetch_assoc($preview_h_rs);
		$preview_h_num=mysqli_num_rows($preview_h_rs);

		if($preview_k_num>0){
			$str=str_replace("[HATYU_NEWDATE]",$preview_k_item["NEWDATE"],$str);
		}else if($preview_h_num>0){
			$str=str_replace("[HATYU_NEWDATE]",$preview_h_item["NEWDATE"],$str);
		}else{
			$str=str_replace("[HATYU_NEWDATE]","",$str);
		}


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
		$str=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:", "", $item_filestatus['M2_CURRENCY']),$str);
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

		//新規追加項目
		$str=str_replace("[D-TEMPR]",str_replace("TEMPR:","",$item_filestatus['TEMPR']),$str);
		$str=str_replace("[D-SAMPLE]",$item_filestatus['SAMPLE'],$str);
		$str=str_replace("[D-ORIGIN]",$item_filestatus['ORIGIN'],$str);
		$str=str_replace("[D-LEGAL]",$item_filestatus['LEGAL'],$str);
		$str=str_replace("[D-UNIT]",str_replace("UNIT:","",$item_filestatus['UNIT']),$str);
		$str=str_replace("[TEMPR]",$item_filestatus['TEMPR'],$str);
		$str=str_replace("[SAMPLE]",$item_filestatus['SAMPLE'],$str);
		$str=str_replace("[ORIGIN]",$item_filestatus['ORIGIN'],$str);
		$str=str_replace("[LEGAL]",$item_filestatus['LEGAL'],$str);
		$str=str_replace("[UNIT]",$item_filestatus['UNIT'],$str);

		//キャンセル依頼用
		$str=str_replace("[D-C_COMMENT]",str_replace("\n", '<br>', $item_filestatus['C_COMMENT']),$str); // Dはbr変換

		$detail_template = '
            <div class="formset__item formset__item-head">
              <div class="formset__ttl2"><strong>Details XXX</strong></div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Item #</div>
              <div class="formset__input">[D-M2_DETAIL_ITEM_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">内容</div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price</div>
              <div class="formset__input">[D-M2_DETAIL_PRICE_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Note</div>
              <div class="formset__input">[D-M2_DETAIL_NOTE_XXX]</div>
            </div>
		';
		$add_detail_area = '';
		$detail_key = 0;

		$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$key.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		while($item_filestatus_detail = mysqli_fetch_assoc($rs)) {
			$detail_no = $detail_key + 1;
			$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);

			$add_detail_area=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area); // Dはbr変換
			$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_SP_DISCOUNT_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_SP_DISCOUNT'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換

			$detail_key++;
		}
		$str=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$str);
		// -------------------------------------------------------------------------------

		if($type == '発注依頼' && $mode!="disp_frame" && $mode!="disp") {
		//if($type == '見積り送付' || $type == '発注依頼' || $type=="追加見積り") {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_shodan['ID']." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_filestatus['SHODAN_ID']." and MID1='".$item_filestatus["MID1"]."' and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			//echo('<!--'.$key.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			while($item_filestatus2 = mysqli_fetch_assoc($rs)) {
				//分割払いの処理
				$div_id=$item_filestatus2["DIV_ID"];
				$tmp="";
				$tmp=explode("-", $div_id);
				//echo "<!--";
				//var_dump($tmp);
				//echo "-->";
				$part="";
				$disp_part="";
				$pre_part="";
				if($item_filestatus2["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
					$part=$tmp[2];
					$pre_part=$tmp[0]."-".$tmp[1];
					$disp_part="分割払い".$part;
				}

				if( $item_filestatus2["M2_PAY_TYPE"]=='Milestone' && $part=="Part0" ){
					continue;
				}
				//2回払いの場合は、Part0の発注書を表示させ、保存の段階でPart1のデータに替える。
				if($item_filestatus2["M2_PAY_TYPE"]=='Split' && $part!="Part0"){
					continue;
				}

				//既に発注依頼されてるものは表示しない
				$StrSQL="SELECT ID,STATUS,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_filestatus['SHODAN_ID']." and MID1='".$m1_mid."' and STATUS='発注依頼' ";
				$StrSQL.=" and DIV_ID='".$div_id."' ";
				$StrSQL.=" order by ID desc ";
				$h_done_rs=mysqli_query(ConnDB(),$StrSQL);
				$h_done_num = mysqli_num_rows($h_done_rs);
				//echo "<!--発注依頼プルダウン SQL：$StrSQL-->";
				//echo "<!--発注依頼プルダウン num：$h_done_num-->";
				if($h_done_num >=1){
					continue;
				}

				//2回払いの場合はPart1に発注依頼があったらその後は発注できないようにする
				//既に発注依頼されてるものは表示しない
				if($item_filestatus2["M2_PAY_TYPE"]=='Split' && $part=="Part0"){
					$StrSQL="SELECT ID,STATUS,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_filestatus['SHODAN_ID']." and MID1='".$m1_mid."' and STATUS='発注依頼' ";
					$StrSQL.=" and DIV_ID='".$pre_part."-Part1' ";
					$StrSQL.=" order by ID desc ";
					$h_done_rs=mysqli_query(ConnDB(),$StrSQL);
					$h_done_num = mysqli_num_rows($h_done_rs);
					if($h_done_num >=1){
						continue;
					}
				}

				$selected = '';
				if($item_filestatus2['ID'] == $key) {
					$selected = ' selected ';
				}

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $item_filestatus2['SCNo_yy'], 
					"SCNo_mm" => $item_filestatus2['SCNo_mm'], 
					"SCNo_dd" => $item_filestatus2['SCNo_dd'], 
					"SCNo_cnt" => $item_filestatus2['SCNo_cnt'], 
					"SCNo_else1" => $item_filestatus2['SCNo_else1'], 
					"SCNo_else2" => $item_filestatus2['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);
				

				$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $SCNo_str.' Version'.$item_filestatus2['M2_VERSION']. '</option>';
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
			if($h_m2_list==""){
				$str=DispParam($str,"HATYU_CAUTION");
				$str=DispParamNone($str,"HATYU_TYPICAL");
			}else{
				$str=DispParamNone($str,"HATYU_CAUTION");
				$str=DispParam($str,"HATYU_TYPICAL");
			}
			


		}


		if($type == '発注依頼' && ($mode=="disp_frame" || $mode=="disp") ) {
		//if($type == '見積り送付' || $type == '発注依頼' || $type=="追加見積り") {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_shodan['ID']." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_filestatus['SHODAN_ID']." and MID1='".$item_filestatus["MID1"]."' and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
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

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $item_filestatus2['SCNo_yy'], 
					"SCNo_mm" => $item_filestatus2['SCNo_mm'], 
					"SCNo_dd" => $item_filestatus2['SCNo_dd'], 
					"SCNo_cnt" => $item_filestatus2['SCNo_cnt'], 
					"SCNo_else1" => $item_filestatus2['SCNo_else1'], 
					"SCNo_else2" => $item_filestatus2['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);

				$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $SCNo_str.' Version'.$item_filestatus2['M2_VERSION']. '</option>';
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


			//2回払いの発注依頼はPart0を表示。
			$m2_pay_type=check_M2_PAY_TYPE($item_filestatus["SHODAN_ID"], $item_filestatus["MID1"]);
			echo "<!--m2_pay_type:$m2_pay_type-->";
			//念のためモードも指定
			if($m2_pay_type=="Split" && $mode=="disp_frame"){
				$div_id=$item_filestatus["DIV_ID"];
				$tmp="";
				$tmp=explode("-", $div_id);
				//echo "<!--";
				//var_dump($tmp);
				//echo "-->";
				$pre_part="";
				$part="";
				if($item_filestatus["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
					$pre_part=$tmp[0]."-".$tmp[1];
					$part=$tmp[2];
				}

				$StrSQL="SELECT ID, NEWDATE, SHODAN_ID, STATUS, DIV_ID";
				$StrSQL.=" FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$item_filestatus["SHODAN_ID"]."' ";
				$StrSQL.=" and DIV_ID='".$pre_part."-Part0"."'";
				$StrSQL.=" and STATUS='見積り送付' ";
				//echo "<!--一覧用：$StrSQL-->";
				$hatyu_part0_rs=mysqli_query(ConnDB(),$StrSQL);
				$hatyu_part0_item =  mysqli_fetch_assoc($hatyu_part0_rs);
				//echo "<!--";
				//var_dump($hatyu_part0_item);
				//echo "-->";
				$str=str_replace("[H_M2_ID]",$hatyu_part0_item["ID"],$str);
			}
			
		}


		//現在、こちらがつかわれている。プレビュー表示にになる場合にブロックのさらに下のブロックに変更。
		//プレビューの表示用
		//サービス費用などのエリア表示
		if($type == '発注依頼' && ($mode=="disp_frame" || $mode=="disp") ) {
			//サービスエリア表示
			$div_id=$item_filestatus["DIV_ID"];
			$tmp="";
			$tmp=explode("-", $div_id);
			//echo "<!--";
			//var_dump($tmp);
			//echo "-->";
			$pre_part="";
			$part="";
			if(count($tmp)>=2){
				$pre_part=$tmp[0]."-".$tmp[1];
				$part=$tmp[2];
			}

			//2回払いの発注依頼はPart0を表示。
			$m2_pay_type=check_M2_PAY_TYPE($item_filestatus["SHODAN_ID"], $item_filestatus["MID1"]);
			echo "<!--m2_pay_type:$m2_pay_type-->";
			if($m2_pay_type=="Split"){
				$StrSQL="SELECT ID, NEWDATE, SHODAN_ID, STATUS, DIV_ID";
				$StrSQL.=" FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$item_filestatus["SHODAN_ID"]."' ";
				$StrSQL.=" and DIV_ID='".$pre_part."-Part0"."'";
				$StrSQL.=" and STATUS='見積り送付' ";
			}else{
				$StrSQL="SELECT ID, NEWDATE, SHODAN_ID, STATUS, DIV_ID";
				$StrSQL.=" FROM DAT_FILESTATUS ";
				$StrSQL.=" WHERE SHODAN_ID='".$item_filestatus["SHODAN_ID"]."' ";
				$StrSQL.=" and DIV_ID='".$div_id."'";
				$StrSQL.=" and STATUS='見積り送付' ";
			}
			$hatyu_part0_rs=mysqli_query(ConnDB(),$StrSQL);
			$hatyu_part0_item =  mysqli_fetch_assoc($hatyu_part0_rs);
			//echo "<!--サービズエリア暫定：$StrSQL-->";
			//echo "<!--";
			//var_dump($hatyu_part0_item);
			//echo "-->";
			$sf_tpl=file_get_contents("../common/template/service_fee_typeB.html");
			$sf_tpl=makeServiceArea($hatyu_part0_item["ID"],$sf_tpl);
			$str=str_replace("[SERVICE_FEE_AREA]",$sf_tpl,$str);
		}


		//まだ使わないが、ユーザ画面のM1側にあったものをもってきた
		//プレビューの表示用
		//サービス費用などのエリア表示
		if($type == '見積り送付'){
			$sf_tpl=file_get_contents("../common/template/service_fee_typeA.html");
			$sf_tpl=makeServiceArea($item_filestatus['ID'],$sf_tpl);

			$sf_tpl2=file_get_contents("../common/template/preview_mitsu_info_cb.html");
			$sf_tpl2=makePreviewInfo_CB($item_filestatus['ID'],$sf_tpl2);

			$sf_tpl3=file_get_contents("../common/template/preview_mitsu_info_r.html");
			$sf_tpl3=makePreviewInfo_CB($item_filestatus['ID'],$sf_tpl3);

		}
		if($type == '発注依頼'){
			$StrSQL="SELECT * FROM DAT_FILESTATUS ";
			$StrSQL.=" WHERE ID=".$item_filestatus['H_M2_ID']." ";
			$StrSQL.=" and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			$h_rs1=mysqli_query(ConnDB(),$StrSQL);
			$h_item1 = mysqli_fetch_assoc($h_rs1);
			$sf_tpl=file_get_contents("../common/template/service_fee_typeB.html");
			$sf_tpl=makeServiceArea($h_item1["ID"],$sf_tpl);
		}
		$str=str_replace("[SERVICE_FEE_AREA]",$sf_tpl,$str);
		$str=str_replace("[PREVIEW_INFO_CB]",$sf_tpl2,$str);
		$str=str_replace("[PREVIEW_INFO_R]",$sf_tpl3,$str);
		
		if(strpos( $_SESSION["MID"], "M1" ) === false){
			//研究者側
			//$str=DispParam($str, "SERVICE_FEE_AREA");
			$str=DispParam($str, "PREVIEW_BIKO_CB_AREA");

			$str=DispParamNone($str, "PREVIEW_INFO_CB_AREA");
			$str=DispParam($str, "PREVIEW_INFO_R_AREA");

			$str=DispParamNone($str, "PREVIEW_ITEM_CB_AREA");
			$str=DispParam($str, "PREVIEW_ITEM_R_AREA");
			
			$str=DispParamNone($str, "PREVIEW_ITEM_DETAIL_CB_AREA");
			$str=DispParam($str, "PREVIEW_ITEM_DETAIL_R_AREA");

			$str=DispParamNone($str, "PREVIEW_SYOKEI_CB_AREA");
			$str=DispParam($str, "PREVIEW_SYOKEI_R_AREA");
			
			$str=DispParamNone($str, "PAGE_TITLE_CB");
			$str=DispParam($str, "PAGE_TITLE_R");
		}else{
			//サプライヤー側
			//$str=DispParamNone($str, "SERVICE_FEE_AREA");
			$str=DispParamNone($str, "PREVIEW_BIKO_CB_AREA");
			
			$str=DispParam($str, "PREVIEW_INFO_CB_AREA");
			$str=DispParamNone($str, "PREVIEW_INFO_R_AREA");
			
			$str=DispParam($str, "PREVIEW_ITEM_CB_AREA");
			$str=DispParamNone($str, "PREVIEW_ITEM_R_AREA");
			
			$str=DispParam($str, "PREVIEW_ITEM_DETAIL_CB_AREA");
			$str=DispParamNone($str, "PREVIEW_ITEM_DETAIL_R_AREA");

			$str=DispParam($str, "PREVIEW_SYOKEI_CB_AREA");
			$str=DispParamNone($str, "PREVIEW_SYOKEI_R_AREA");
			
			$str=DispParam($str, "PAGE_TITLE_CB");
			$str=DispParamNone($str, "PAGE_TITLE_R");
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

		$str=str_replace("[D-S2_FILE]",(isset($item_filestatus['S2_FILE']) ? $item_filestatus['S2_FILE'] : $_FILES['S2_FILE']['name']),$str);
		$str=str_replace("[D-S2_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['S2_MESSAGE']),$str); // Dはbr変換

		$str=str_replace("[D-S2_NEWDATE]",$item_filestatus["NEWDATE"],$str);

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

		$str=str_replace("[S2_FILE]",(isset($item_filestatus['S2_FILE']) ? $item_filestatus['S2_FILE'] : $_FILES['S2_FILE']['name']),$str);
		$str=str_replace("[S2_MESSAGE]",$item_filestatus['S2_MESSAGE'],$str);

		$str=str_replace("[S2_NEWDATE]",$item_filestatus["NEWDATE"],$str);


		//キャンセル依頼用
		$str=str_replace("[C_COMMENT]",str_replace("\n", '<br>', $item_filestatus['C_COMMENT']),$str); // Dはbr変換


		$h_m2_detail = array();
		if($type == '再見積り依頼') {
		//if($type == '発注依頼' || $type == '再見積り依頼' || $type=="追加見積り") {
			//echo('<!-- disp -->');
//			if($type == '発注依頼') {
//				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['H_M2_ID']." and STATUS='見積り送付' order by ID desc;";
//				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
//				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
//			}
//			else {
//				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['M1_M2_ID']." and STATUS='見積り送付' order by ID desc;";
//				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['M1_M2_ID']." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
//				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
//			}
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
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

				//再見積り依頼の際は、Part0以外はプルダウンに表示しない。
				$div_id=$item_filestatus2["DIV_ID"];
				$tmp="";
				$tmp=explode("-", $div_id);
				echo "<!--";
				var_dump($tmp);
				echo "-->";
				$part="";
				$disp_part="";
				if($item_filestatus2["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
					$part=$tmp[2];
					if($part!="Part0"){
						echo "<!--debug:パート0以外-->";
						continue;
					}
				}

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $item_filestatus2['SCNo_yy'], 
					"SCNo_mm" => $item_filestatus2['SCNo_mm'], 
					"SCNo_dd" => $item_filestatus2['SCNo_dd'], 
					"SCNo_cnt" => $item_filestatus2['SCNo_cnt'], 
					"SCNo_else1" => $item_filestatus2['SCNo_else1'], 
					"SCNo_else2" => $item_filestatus2['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);
				$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' .$SCNo_str. ' Version' . $item_filestatus2['M2_VERSION'] .'</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）</option>';

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
	}
	else if($shodan_id) {
		echo "<!--3a-->";
		$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		$str=str_replace("[TITLE]",$item_shodan['TITLE'],$str);
		$str=str_replace("[D-TITLE]",$item_shodan['TITLE'],$str);

		if($type == '発注依頼') {
		//if($type == '発注依頼' || $type == '見積り送付' || $type=="追加見積り") {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			$service_fee_info = array();
			while($item_filestatus = mysqli_fetch_assoc($rs)) {
				//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $item_filestatus['ID'] . '</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $item_filestatus['M2_ID'] . '（バージョン' . $item_filestatus['M2_VERSION'] . '）</option>';

				//分割払いの処理
				$div_id=$item_filestatus["DIV_ID"];
				$tmp="";
				$tmp=explode("-", $div_id);
				//echo "<!--";
				//var_dump($tmp);
				//echo "-->";
				$part="";
				$disp_part="";
				$pre_part="";
				if($item_filestatus["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
					$part=$tmp[2];
					$pre_part=$tmp[0]."-".$tmp[1];
					$disp_part="分割払い".$part;
				}

				if( $item_filestatus["M2_PAY_TYPE"]=='Milestone' && $part=="Part0" ){
					continue;
				}
				//2回払いの場合は、Part0の発注書を表示させ、保存の段階でPart1のデータに替える。
				if($item_filestatus["M2_PAY_TYPE"]=='Split' && $part!="Part0"){
					continue;
				}

				//if( ($item_filestatus["M2_PAY_TYPE"]=='Milestone' || $item_filestatus["M2_PAY_TYPE"]=='Split') && $part=="Part0" ){
				//	continue;
				//}

				//既に発注依頼されてるものは表示しない
				$StrSQL="SELECT ID,STATUS,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' and STATUS='発注依頼' ";
				$StrSQL.=" and DIV_ID='".$div_id."' ";
				$StrSQL.=" order by ID desc ";
				$h_done_rs=mysqli_query(ConnDB(),$StrSQL);
				$h_done_num = mysqli_num_rows($h_done_rs);
				if($h_done_num >=1){
					continue;
				}

				//2回払いの場合はPart1に発注依頼があったらその後は発注できないようにする
				//既に発注依頼されてるものは表示しない
				if($item_filestatus["M2_PAY_TYPE"]=='Split' && $part=="Part0"){
					$StrSQL="SELECT ID,STATUS,DIV_ID FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' and STATUS='発注依頼' ";
					$StrSQL.=" and DIV_ID='".$pre_part."-Part1' ";
					$StrSQL.=" order by ID desc ";
					$h_done_rs=mysqli_query(ConnDB(),$StrSQL);
					$h_done_num = mysqli_num_rows($h_done_rs);
					if($h_done_num >=1){
						continue;
					}
				}

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $item_filestatus['SCNo_yy'], 
					"SCNo_mm" => $item_filestatus['SCNo_mm'], 
					"SCNo_dd" => $item_filestatus['SCNo_dd'], 
					"SCNo_cnt" => $item_filestatus['SCNo_cnt'], 
					"SCNo_else1" => $item_filestatus['SCNo_else1'], 
					"SCNo_else2" => $item_filestatus['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);

				//keyが空できているこの条件内なので、最新のバージョンをselectedにする。
				if($h_m2_list==""){
					$h_m2_list.='<option value="' . $item_filestatus['ID'] . '" selected>';
					$h_m2_list.=$SCNo_str . ' Version' . $item_filestatus['M2_VERSION'] . '</option>';
					//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" selected>' . $item_filestatus['M2_ID'] . '（バージョン' . $item_filestatus['M2_VERSION'] . ', '.$disp_part.'）</option>';
				}else{
					$h_m2_list.='<option value="' . $item_filestatus['ID'] . '">';
					$h_m2_list.=$SCNo_str . ' Version' . $item_filestatus['M2_VERSION'] . '</option>';
					//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $item_filestatus['M2_ID'] . '（バージョン' . $item_filestatus['M2_VERSION'] . ', '.$disp_part.'）</option>';
				}

				$h_m2_detail[$item_filestatus['ID']] = $item_filestatus;
				$h_m2_detail[$item_filestatus['ID']]['detail'] = array();

				$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$item_filestatus['ID']." order by ID asc;";
				//echo('<!--'.$StrSQL.'-->');
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				while($item_filestatus_detail = mysqli_fetch_assoc($rs2)) {
					$h_m2_detail[$item_filestatus['ID']]['detail'][] = $item_filestatus_detail;
				}


				//サービス料金関連
				$tmp_service_fee_info1=array("MITSUMORISYO_ALL_CHARGE" => "[MITSUMORISYO_ALL_CHARGE]");
				$service_fee_info=array();
				foreach ($tmp_service_fee_info1 as $idx => $val) {
					$tmp_str="";
					$tmp_str=makeServiceArea($item_filestatus['ID'], $val);
					$service_fee_info[$item_filestatus['ID']][$idx]=$tmp_str;
				}
				echo "<!--tmp_service_fee_info2:";
				var_dump($service_fee_info);
				echo "-->";

			}
			$str=str_replace("[H_M2_LIST]",$h_m2_list,$str);
			$str=str_replace("[H_M2_DETAIL]",json_encode($h_m2_detail),$str);
			$str=str_replace("[SERVICE_FEE_INFO]",json_encode($service_fee_info),$str);

			if($h_m2_list==""){
				$str=DispParam($str,"HATYU_CAUTION");
				$str=DispParamNone($str,"HATYU_TYPICAL");
			}else{
				$str=DispParamNone($str,"HATYU_CAUTION");
				$str=DispParam($str,"HATYU_TYPICAL");
			}
		}


		if($type == '案件の取り下げ') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='問い合わせ' order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus = mysqli_fetch_assoc($rs);
			$str=str_replace("[D-COMMENT]",str_replace("\n", '<br>', $item_filestatus['T_COMMENT']),$str); // Dはbr変換
			$str=str_replace("[D-FILE]",$item_filestatus['T_FILE'],$str);
			$str=str_replace("[D-FILE2]",$item_filestatus['T_FILE2'],$str);
			$str=str_replace("[D-FILE3]",$item_filestatus['T_FILE3'],$str);
			$str=str_replace("[D-FILE4]",$item_filestatus['T_FILE4'],$str);
			$str=str_replace("[D-FILE5]",$item_filestatus['T_FILE5'],$str);
			$str=str_replace("[D-KIGEN]",$item_filestatus['T_ANSWERDATE'],$str);
			$str=str_replace("[D-NEWDATE]",substr($item_filestatus['NEWDATE'],0,10),$str);
		}

		if($type=="納品確認"){
			echo "<!--納品確認3-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid." ";
			$StrSQL.="' and (STATUS='データ納品' or STATUS='物品納品' or STATUS='サプライヤーが納品(一括前払い)') order by ID asc";
			echo "<!--$StrSQL-->";
			$n_rs=mysqli_query(ConnDB(),$StrSQL);
			$chknohin_id_list="";
			while($n_item=mysqli_fetch_assoc($n_rs)){
				//一括払いでもDIV_IDを設定するようになったので、
				//分割払いと一括払いそれぞれの共通処理
				$div_id="";
				$div_id=$n_item["DIV_ID"];

				//納品確認がされてない、データをとってきて一覧にする。
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and MID1='".$m1_mid."' ";
				$StrSQL.=" AND DIV_ID='".$div_id."' ";
				$StrSQL.=" AND (STATUS='見積り送付' OR STATUS='追加見積り') ";
				$StrSQL.=" AND DIV_ID NOT IN (SELECT DIV_ID FROM DAT_FILESTATUS WHERE DIV_ID = '".$div_id."' ";
				$StrSQL.=" AND (STATUS = '納品確認' OR STATUS = '研究者が納品承認(一括前払い)') ) ";
				$StrSQL.=" ORDER BY ID DESC ";
				echo "<!--$StrSQL-->";
				$nlist_rs=mysqli_query(ConnDB(),$StrSQL);
				$nlist_item=mysqli_fetch_assoc($nlist_rs);
				//echo "<!--nlist_item:";
				//var_dump($nlist_item);
				//echo "-->";

				//$selected = '';
				//if($nlist_item['ID'] == $key) {
				//	$selected = ' selected ';
				//}

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $nlist_item['SCNo_yy'], 
					"SCNo_mm" => $nlist_item['SCNo_mm'], 
					"SCNo_dd" => $nlist_item['SCNo_dd'], 
					"SCNo_cnt" => $nlist_item['SCNo_cnt'], 
					"SCNo_else1" => $nlist_item['SCNo_else1'], 
					"SCNo_else2" => $nlist_item['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);

				if(!is_null($nlist_item['ID']) && $nlist_item['ID']!=""){
					$chknohin_id_list .= '<option value="' . $nlist_item['ID'] . '" ' . $selected . ' >' .$SCNo_str. ' Version' . $nlist_item['M2_VERSION'] .'</option>';
				}
				

			}
			$str=str_replace("[CHKNOHIN_ID_LIST]",$chknohin_id_list,$str);

		}

		$h_m2_detail = array();
		if($type == '再見積り依頼') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$shodan_id."' ";
			$StrSQL.=" and MID1='".$m1_mid."' ";
			$StrSQL.=" and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			//echo('<!--saimitsu:'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			while($item_filestatus = mysqli_fetch_assoc($rs)) {
				//echo "<!--";
				//var_dump($item_filestatus);
				//echo "-->";
				$str=str_replace("[M2_ID]",$item_filestatus['M2_ID'],$str);
				$str=str_replace("[M2_VERSION]",$item_filestatus['M2_VERSION'],$str);

				$selected = '';
				if($item_filestatus['ID'] == $key) {
					$selected = ' selected ';
				}

				//再見積り依頼の際は、Part0以外はプルダウンに表示しない。
				$div_id=$item_filestatus["DIV_ID"];
				$tmp="";
				$tmp=explode("-", $div_id);
				echo "<!--";
				var_dump($tmp);
				echo "-->";
				$part="";
				$disp_part="";
				if($item_filestatus["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
					$part=$tmp[2];
					if($part!="Part0"){
						echo "<!--debug:パート0以外-->";
						continue;
					}
				}

				$SCNo_str="";
				$SCNo_ary=array(
					"SCNo_yy" => "", 
					"SCNo_mm" => "", 
					"SCNo_dd" => "", 
					"SCNo_cnt" => "", 
					"SCNo_else1" => "", 
					"SCNo_else2" => "", 
				);
				$SCNo_ary=array(
					"SCNo_yy" => $item_filestatus['SCNo_yy'], 
					"SCNo_mm" => $item_filestatus['SCNo_mm'], 
					"SCNo_dd" => $item_filestatus['SCNo_dd'], 
					"SCNo_cnt" => $item_filestatus['SCNo_cnt'], 
					"SCNo_else1" => $item_filestatus['SCNo_else1'], 
					"SCNo_else2" => $item_filestatus['SCNo_else2'], 
				);
				$SCNo_str=formatAlphabetId($SCNo_ary);
				$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" ' . $selected . ' >' .$SCNo_str. ' Version' . $item_filestatus['M2_VERSION'] .'</option>';

				$h_m2_detail[$item_filestatus['ID']] = $item_filestatus;
				$h_m2_detail[$item_filestatus['ID']]['detail'] = array();

				$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$item_filestatus['ID']." order by ID asc;";
				//echo('<!--'.$StrSQL.'-->');
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				while($item_filestatus_detail2 = mysqli_fetch_assoc($rs2)) {
					$h_m2_detail[$item_filestatus['ID']]['detail'][] = $item_filestatus_detail2;
				}
			}
			if($h_m2_list==""){
				$h_m2_list="<option value=''>サプライヤーからの見積り送付なし</option>";
			}
			$str=str_replace("[H_M2_LIST]",$h_m2_list,$str);
			$str=str_replace("[H_M2_DETAIL]",json_encode($h_m2_detail),$str);
		}

	}

	$str=str_replace("[D-TITLE]",'',$str);
	$str=str_replace("[D-COMMENT]",'',$str);
	$str=str_replace("[D-FILE]",'',$str);
	$str=str_replace("[D-FILE2]",'',$str);
	$str=str_replace("[D-FILE3]",'',$str);
	$str=str_replace("[D-FILE4]",'',$str);
	$str=str_replace("[D-FILE5]",'',$str);
	$str=str_replace("[D-KIGEN]",'',$str);
	$str=str_replace("[D-NEWDATE]",date('Y/m/d'),$str);

	$str=str_replace("[D-M1_MESSAGE]",'',$str);
	$str=str_replace("[M1_TRANS_FLG]",'',$str);
	$str=str_replace("[D-M1_TRANS_FLG_あり]",'',$str);
	$str=str_replace("[D-M1_TRANS_FLG_なし]",'',$str);
	$str=str_replace("[D-M1_TRANS_TXT]",'',$str);
	$str=str_replace("[D-M1_PRICE]",'',$str);
	$str=str_replace("[D-M1_FILE]",'',$str);
	$str=str_replace("[D-M1_KIGEN]",'',$str);

	$str=str_replace("[D-M2_TITLE]",'',$str);
	$str=str_replace("[D-M2_PRICE]",'',$str);
	$str=str_replace("[D-M2_COMMENT]",'',$str);

	// 新見積り書
	// -------------------------------------------------------------------------------
	$str=str_replace("[D-M2_NOHIN_TYPE]",'',$str);
	$str=str_replace("[D-M2_PAY_TYPE]",'',$str);

	$str=str_replace("[D-M2_QUOTE_NO]",'',$str);
	$str=str_replace("[D-M2_STUDY_CODE]",'',$str);
	$str=str_replace("[D-M2_DATE]",'',$str);
	$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",'',$str);
	$str=str_replace("[D-M2_DESCRIPTION]",'',$str);
	$str=str_replace("[D-M2_CURRENCY]",'',$str);
	$str=str_replace("[ADD_DETAIL_AREA]",'',$str);
	$detail_no = 1;
	$str=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",'',$str);
	$str=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",'',$str);
	$str=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",'',$str);
	$str=str_replace("[D-M2_DETAIL_PRICE_ORIGIN_".$detail_no."]",'',$str);
	$str=str_replace("[D-M2_DETAIL_SP_DISCOUNT_".$detail_no."]",'',$str);
	$str=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",'',$str);

	$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",'',$str);
	$str=str_replace("[D-M2_SPECIAL_NOTE]",'',$str);
	$str=str_replace("[D-MITSUMORISYO_ALL_CHARGE]",'',$str);

	// -------------------------------------------------------------------------------

	$str=str_replace("[D-H_M2_ID]",'',$str);
	$str=str_replace("[D-M1_M2_ID]",'',$str);
	$str=str_replace("[D-H_COMMENT]",'',$str);

	$str=str_replace("[D-N_FILE]",'',$str);
	$str=str_replace("[D-N_MESSAGE]",'',$str);

	$str=str_replace("[D-S_FILE]",'',$str);
	$str=str_replace("[D-S_MESSAGE]",'',$str);

	$str=str_replace("[D-S2_FILE]",'',$str);
	$str=str_replace("[D-S2_MESSAGE]",'',$str);
	$str=str_replace("[D-S2_NEWDATE]",'',$str);


	//キャンセル依頼用
	$str=str_replace("[D-C_COMMENT]",str_replace("\n", '<br>', $item_filestatus['C_COMMENT']),$str); // Dはbr変換

	//納品確認用
	$str=str_replace("[D-CHKNOHIN_MESSAGE]",'',$str);

	//「サンプル送付依頼」フォーム
	$str=str_replace("[D-SAMPLE_HOKAN_TMPER]",'',$str);
	$str=str_replace("[D-SAMPLE_HAISO_CO]",'',$str);
	$str=str_replace("[D-SAMPLE_NUMBER]",'',$str);
	$str=str_replace("[D-SAMPLE_DATE]",'',$str);
	$str=str_replace("[D-SAMPLE_TIME]",'',$str);
	$str=str_replace("[D-SAMPLE_MSG]",'',$str);
	$str=str_replace("[D-SAMPLE_FIlE]",'',$str);


	$str=str_replace("[TITLE]",'',$str);
	$str=str_replace("[COMMENT]",'',$str);
	$str=str_replace("[FILE]",'',$str);
	$str=str_replace("[FILE2]",'',$str);
	$str=str_replace("[FILE3]",'',$str);
	$str=str_replace("[FILE4]",'',$str);
	$str=str_replace("[FILE5]",'',$str);
	$str=str_replace("[KIGEN]",'',$str);
	$str=str_replace("[NEWDATE]",date('Y/m/d'),$str);

	$str=str_replace("[M1_MESSAGE]",'',$str);
	$str=str_replace("[M1_TRANS_FLG]",'',$str);
	$str=str_replace("[M1_TRANS_TXT]",'',$str);
	$str=str_replace("[M1_PRICE]",'',$str);
	$str=str_replace("[M1_FILE]",'',$str);
	$str=str_replace("[M1_KIGEN]",'',$str);

	$str=str_replace("[M2_TITLE]",'',$str);
	$str=str_replace("[M2_PRICE]",'',$str);
	$str=str_replace("[M2_COMMENT]",'',$str);

	// 新見積り書
	// -------------------------------------------------------------------------------
	$str=str_replace("[M2_NOHIN_TYPE]",'',$str);
	$str=str_replace("[M2_PAY_TYPE]",'',$str);

	$str=str_replace("[M2_QUOTE_NO]",'',$str);
	$str=str_replace("[M2_STUDY_CODE]",'',$str);
	$str=str_replace("[M2_DATE]",'',$str);
	$str=str_replace("[M2_QUOTE_VALID_UNTIL]",'',$str);
	$str=str_replace("[M2_DESCRIPTION]",'',$str);
	$str=str_replace("[M2_CURRENCY]",'',$str);
	$str=str_replace("[ADD_DETAIL_AREA]",'',$str);
	$detail_no = 1;
	$str=str_replace("[M2_DETAIL_ITEM_".$detail_no."]",'',$str);
	$str=str_replace("[M2_DETAIL_DESCRIPTION_".$detail_no."]",'',$str);
	$str=str_replace("[M2_DETAIL_PRICE_".$detail_no."]",'',$str);
	$str=str_replace("[M2_DETAIL_SP_DISCOUNT_".$detail_no."]",'',$str);
	$str=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",'',$str);

	$str=str_replace("[M2_SPECIAL_DISCOUNT]",'',$str);
	$str=str_replace("[M2_SPECIAL_NOTE]",'',$str);
	$str=str_replace("[MITSUMORISYO_ALL_CHARGE]",'',$str);
		// -------------------------------------------------------------------------------
	
	$str=str_replace("[H_M2_ID]",'',$str);
	$str=str_replace("[M1_M2_ID]",'',$str);
	$str=str_replace("[H_COMMENT]",'',$str);

		$str=str_replace("[N_FILE]",'',$str);
		$str=str_replace("[N_MESSAGE]",'',$str);

	$str=str_replace("[S_FILE]",'',$str);
	$str=str_replace("[S_MESSAGE]",'',$str);

	$str=str_replace("[S2_FILE]",'',$str);
	$str=str_replace("[S2_MESSAGE]",'',$str);

	//新規追加項目
	$str=str_replace("[D-TEMPR]",str_replace("TEMPR:","",$_POST['TEMPR']),$str);
	$str=str_replace("[D-SAMPLE]",$_POST['SAMPLE'],$str);
	$str=str_replace("[D-ORIGIN]",$_POST['ORIGIN'],$str);
	$str=str_replace("[D-LEGAL]",$_POST['LEGAL'],$str);
	$str=str_replace("[D-UNIT]",str_replace("UNIT:","",$_POST['UNIT']),$str);
	$str=str_replace("[TEMPR]",$_POST['TEMPR'],$str);
	$str=str_replace("[SAMPLE]",$_POST['SAMPLE'],$str);
	$str=str_replace("[ORIGIN]",$_POST['ORIGIN'],$str);
	$str=str_replace("[LEGAL]",$_POST['LEGAL'],$str);
	$str=str_replace("[UNIT]",$_POST['UNIT'],$str);

	//キャンセル依頼用
	$str=str_replace("[C_COMMENT]",str_replace("\n", '<br>', $item_filestatus['C_COMMENT']),$str); // Dはbr変換

	//納品確認用
	$str=str_replace("[CHKNOHIN_MESSAGE]",'',$str);


	//「サンプル送付依頼」フォーム
	$str=str_replace("[SAMPLE_HOKAN_TMPER]",'',$str);
	$str=str_replace("[SAMPLE_HAISO_CO]",'',$str);
	$str=str_replace("[SAMPLE_NUMBER]",'',$str);
	$str=str_replace("[SAMPLE_DATE]",'',$str);
	$str=str_replace("[SAMPLE_TIME]",'',$str);
	$str=str_replace("[SAMPLE_MSG]",'',$str);
	$str=str_replace("[SAMPLE_FIlE]",'',$str);


	//「発注依頼」のSHIP TO
	//決済者情報の「国」、「郵便番号」、「住所」
	$str=str_replace("[H_SHIP_TO_SPT_1]","",$str);
	$str=str_replace("[H_SHIP_TO_SPT_2]","",$str);
	$str=str_replace("[H_SHIP_TO_SPT_3]","",$str);

	$str=str_replace("[D-H_SHIP_TO_SPT_1]","",$str);
	$str=str_replace("[D-H_SHIP_TO_SPT_2]","",$str);
	$str=str_replace("[D-H_SHIP_TO_SPT_3]","",$str);


	$strtmp="";
	$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
	$tmp=explode("::","常温::冷蔵::冷凍");
	$fname="TEMPR";
	for ($j=0; $j<count($tmp); $j=$j+1) {
		$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
	}
	//echo "<!--strtmp:".$strtmp."-->";
	$str=str_replace("[OPT-".$fname."]",$strtmp,$str);
	if(isset($_POST[$fname]) && $_POST[$fname]!=""){
		$str=str_replace("'".$_POST[$fname]."'","'".$_POST[$fname]."' selected",$str);
	}

	$strtmp="";
	$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
	$tmp=explode("::","￥::$::€::￡");
	$fname="UNIT";
	for ($j=0; $j<count($tmp); $j=$j+1) {
		$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
	}
	//echo "<!--strtmp:".$strtmp."-->";
	$str=str_replace("[OPT-".$fname."]",$strtmp,$str);
	if(isset($_POST[$fname]) && $_POST[$fname]!=""){
		$str=str_replace("'".$_POST[$fname]."'","'".$_POST[$fname]."' selected",$str);
	}


	$word_view = '';
	if($word != '') {
		/*
		$word3 = explode(',', $word);
		foreach($word3 as $word3_row) {
			if($word3_row == '') {
				continue;
			}
			$word4 = explode(':', $word3_row);
			$word_view .= $word4[1] . '<br>';
		}
		*/
		$word_view .= $word . '<br>';
	}
	if($word2 != '') {
		$word_view .= $word2 . '<br>';
	}
	$str=str_replace("[WORD_VIEW]",$word_view,$str);

	$m1_view = '';
	if($type == '問い合わせ' || $type == '見積り依頼' || $type == '再見積り依頼' || $type == '案件の取り下げ' || $type=='発注依頼') {
	//if($type == '問い合わせ' || $type == '見積り依頼' || $type == '再見積り依頼' || $type == '案件の取り下げ') {
		foreach($m1_list as $item) {
			$m1_view .= $item['M1_DVAL01'] . '<br>';
		}
	}
	//else if($item_filestatus) {
	else if(count($m1_list) != 0) {
		foreach($m1_list as $item) {
			if($item['MID'] == $item_filestatus['MID1']) {
			  $m1_view .= $item['M1_DVAL01'];
			}
		}
	}
//echo "<!--m1_view:".$m1_view."-->";
	$str=str_replace("[M1_VIEW]",$m1_view,$str);

	// keyは後からつく場合がある	
	$str=str_replace("[KEY]",$key,$str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);

	print $str;

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function RequestData($obj,$a,$b,$key,$mode)
{
	eval(globals());

	for ($i=0; $i<=$FieldMax; $i=$i+1) {
		if ($FieldAtt[$i]==3) {
			if(strstr($_POST[$FieldName[$i]],"\t") == true) {
				$FieldValue[$i]=$_POST[$FieldName[$i]];
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
			$FieldValue[$i]=str_replace("\\","",$_POST[$FieldName[$i]]);
		}
		if ($FieldAtt[$i]==4 && ($mode=="saveconf" || $mode=="preview" || $mode=="saveconf2")) {
			$filename = $_FILES["EP_".$FieldName[$i]]["name"];
			move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], "data/".$filename);
			$FieldValue[$i]=$filename;
		} 
	}

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SaveData2($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$word,$word2,$mid_list)
{
	eval(globals());

	$post_title=str_replace("'","''",htmlspecialchars($_POST['TITLE']));
	$post_comment=str_replace("'","''",htmlspecialchars($_POST['COMMENT']));
	$date_stmp=date('Y/m/d H:i:s');

	// 一時保存

	// 商談
	if($shodan_id != '') {
		// 他のステータスに変更するだけ
		$StrSQL = "
		UPDATE DAT_SHODAN SET
			EDITDATE = '".$date_stmp."',
			TITLE = '".$post_title."',
			C_STATUS = '下書き',
			STATUS = '問い合わせ'
		WHERE
		  ID = ".$shodan_id."
		";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}
	else if($key != '') {
		// 商談IDを取得してステータス更新
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_filestatus = mysqli_fetch_assoc($rs);
		$shodan_id = $item_filestatus['SHODAN_ID'];

		// 更新
		$StrSQL = "
		UPDATE DAT_SHODAN SET
			EDITDATE = '".$date_stmp."',
			TITLE = '".$post_title."',
			C_STATUS = '下書き',
			STATUS = '問い合わせ'
		WHERE
		  ID = ".$shodan_id."
		";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}
	else {
		// 新規

	$StrSQL = "
		INSERT INTO DAT_SHODAN (
			MID2,
			TITLE,
			MID1_LIST,
			CATEGORY,
			KEYWORD,
			COMMENT,
			FILE,
			ANSWERDATE,

			NEWDATE,
			EDITDATE,

			STATUS_SORT,
			C_STATUS,
			STATUS
		) VALUE (
			'".$_SESSION['MID']."',
			'".$post_title."',
			'".$mid1_list."',
			'".$word."',
			'".$word2."',
			'".$post_comment."',
			'".$_POST['FILE']."',
			'".$_POST['KIGEN']."',

			'".$date_stmp."',
			'".$date_stmp."',

			'".$status_sort."',
			'下書き',
			'問い合わせ'
	)";
	//echo "<!--StrSQL:".$StrSQL."-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		var_dump("err:".$StrSQL);
		die;
	}

	}

	// 商談ID取得
	//$StrSQL="SELECT ID FROM DAT_SHODAN order by ID desc;";
	$StrSQL="SELECT ID FROM DAT_SHODAN where MID2='".$_SESSION['MID']."' and EDITDATE='".$date_stmp."' order by ID desc;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$shodan_id = $item['ID'];
	$mid2=$_SESSION['MID'];

	// ここからは複数サプライヤー対応
	$mid1s = explode(',', $mid1_list);
	foreach($mid1s as $mid1) {

	// ファイルステータス
	$StrSQL = "
		INSERT INTO DAT_FILESTATUS (
			SHODAN_ID,
			MID1,
			MID2,

			CATEGORY,
			STATUS,

			T_COMMENT,
			T_FILE,
			T_FILE2,
			T_FILE3,
			T_FILE4,
			T_FILE5,
			T_ANSWERDATE,

			NEWDATE,
			EDITDATE
		) VALUE (
			'".$shodan_id."',
			'".$mid1."',
			'".$mid2."',

			'下書き',
			'問い合わせ',

			'".$post_comment."',
			'".$_POST['FILE']."',
			'".$_POST['FILE2']."',
			'".$_POST['FILE3']."',
			'".$_POST['FILE4']."',
			'".$_POST['FILE5']."',
			'".$_POST['KIGEN']."',

			'".$date_stmp."',
			'".$date_stmp."'
	)";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	// filestatusのIDを取得
	//$StrSQL="SELECT ID FROM DAT_FILESTATUS order by ID desc;";
	$StrSQL="SELECT ID FROM DAT_FILESTATUS where MID2='".$_SESSION['MID']."' and EDITDATE='".$date_stmp."' order by ID desc;";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus = mysqli_fetch_assoc($rs);
	$key = $item_filestatus['ID'];

	// ファイルを移動
	$file_dir = __dir__ . '/../a_filestatus/data/';
	if(!file_exists($file_dir . $key . '/')) {
		mkdir($file_dir . $key, 0777, true);
	}
	
	if($_POST['FILE'] != '') {
		$save_filename=basename($_POST['FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['FILE2'] != '') {
		$save_filename=basename($_POST['FILE2']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['FILE3'] != '') {
		$save_filename=basename($_POST['FILE3']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['FILE4'] != '') {
		$save_filename=basename($_POST['FILE4']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}

	if($_POST['FILE5'] != '') {
		$save_filename=basename($_POST['FILE5']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['M1_FILE'] != '') {
		$save_filename=basename($_POST['M1_FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['N_FILE'] != '') {
		$save_filename=basename($_POST['N_FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['S_FILE'] != '') {
		$save_filename=basename($_POST['S_FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	//$file_dir = __dir__ . '/../a_filestatus/data/';
	//if(!file_exists($file_dir . $key . '/')) {
	//	mkdir($file_dir . $key, 0777, true);
	//}
	//if($_POST['FILE'] != '') {
	//	copy($file_dir . $_POST['FILE'], $file_dir . $key . '/' . $_POST['FILE']);
	//}
	//if($_POST['FILE2'] != '') {
	//	copy($file_dir . $_POST['FILE2'], $file_dir . $key . '/' . $_POST['FILE2']);
	//}
	//if($_POST['FILE3'] != '') {
	//	copy($file_dir . $_POST['FILE3'], $file_dir . $key . '/' . $_POST['FILE3']);
	//}
	//if($_POST['FILE4'] != '') {
	//	copy($file_dir . $_POST['FILE4'], $file_dir . $key . '/' . $_POST['FILE4']);
	//}
	//if($_POST['FILE5'] != '') {
	//	copy($file_dir . $_POST['FILE5'], $file_dir . $key . '/' . $_POST['FILE5']);
	//}
	//if($_POST['M1_FILE'] != '') {
	//	copy($file_dir . $_POST['M1_FILE'], $file_dir . $key . '/' . $_POST['M1_FILE']);
	//}
	//if($_POST['N_FILE'] != '') {
	//	copy($file_dir . $_POST['N_FILE'], $file_dir . $key . '/' . $_POST['N_FILE']);
	//}
	//if($_POST['S_FILE'] != '') {
	//	copy($file_dir . $_POST['S_FILE'], $file_dir . $key . '/' . $_POST['S_FILE']);
	//}

	} // 複数サプライヤーのforeach

}


//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SaveData($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$m2,$word,$word2,$mid_list,$param_div_id,$sub_type)
{
	eval(globals());

	$post_title=str_replace("'","''",htmlspecialchars($_POST['TITLE']));
	$post_comment=str_replace("'","''",htmlspecialchars($_POST['COMMENT']));
	$date_stmp=date('Y/m/d H:i:s');

	$m1_message=str_replace("'","''",htmlspecialchars($_POST['M1_MESSAGE']));
	$m1_trans_txt=str_replace("'","''",htmlspecialchars($_POST['M1_TRANS_TXT']));
	$m1_price=str_replace("'","''",htmlspecialchars($_POST['M1_PRICE']));
	$h_comment=str_replace("'","''",htmlspecialchars($_POST['H_COMMENT']));
	$n_message=str_replace("'","''",htmlspecialchars($_POST['N_MESSAGE']));
	$s_message=str_replace("'","''",htmlspecialchars($_POST['S_MESSAGE']));

	$m2_title=str_replace("'","''",htmlspecialchars($_POST['M2_TITLE']));
	$m2_price=str_replace("'","''",htmlspecialchars($_POST['M2_PRICE']));
	$m2_comment=str_replace("'","''",htmlspecialchars($_POST['M2_COMMENT']));
	
	//「キャンセル依頼」用のコメント
	$c_comment=str_replace("'","''",htmlspecialchars($_POST['C_COMMENT']));

	//新規追加項目
	//輸送温度（TEMPR）,サンプルの種類（SAMPLE）
	//由来（ORIGIN）,法規制（LEGAL）
	//通貨単位（UNIT）
	$post_tempr=str_replace("'","''",htmlspecialchars($_POST['TEMPR']));
	$post_sample=str_replace("'","''",htmlspecialchars($_POST['SAMPLE']));
	$post_origin=str_replace("'","''",htmlspecialchars($_POST['ORIGIN']));
	$post_legal=str_replace("'","''",htmlspecialchars($_POST['LEGAL']));
	$post_unit=str_replace("'","''",htmlspecialchars($_POST['UNIT']));

	$post_chknohin_id=str_replace("'","''",htmlspecialchars($_POST['CHKNOHIN_ID']));
	$post_chknohin_message=str_replace("'","''",htmlspecialchars($_POST['CHKNOHIN_MESSAGE']));

	//「サンプル送付依頼」用
	$post_sample_hokan_tmper = str_replace("'", "''", htmlspecialchars($_POST['SAMPLE_HOKAN_TMPER']));
	$post_sample_haiso_co = str_replace("'", "''", htmlspecialchars($_POST['SAMPLE_HAISO_CO']));
	$post_sample_number = str_replace("'", "''", htmlspecialchars($_POST['SAMPLE_NUMBER']));
	$post_sample_date = str_replace("'", "''", htmlspecialchars($_POST['SAMPLE_DATE']));
	$post_sample_time = str_replace("'", "''", htmlspecialchars($_POST['SAMPLE_TIME']));
	$post_sample_msg = str_replace("'", "''", htmlspecialchars($_POST['SAMPLE_MSG']));
	$post_sample_file = str_replace("'", "''", htmlspecialchars($_POST['SAMPLE_FILE']));

	//2回払いの発注依頼はPart0のデータが送られてくるが、Part1に発注をかけるため変数おきかえる。
	//ここでは準備
	$h_m2_id=$_POST['H_M2_ID'];


	// １．商談を作成してIDを取得
  // ２．ファイルステータスを作成
	// ３・メッセージ作成

	$status = '';
	$c_status = '';
	$status_sort = '';
	switch($type) {
		case '問い合わせ':
			$status = '問い合わせ';
			$c_status = '問い合わせ';
			$status_sort = '1';
  	  break;
		case '見積り依頼':
			$status = '見積り依頼';
			$c_status = '見積り';
			$status_sort = '2';
  	  break;
		case '再見積り依頼':
			$status = '再見積り依頼';
			$c_status = '見積り';
			$status_sort = '2';
  	  break;
		case '見積り送付':
			$status = '見積り送付';
			$c_status = '見積り';
			$status_sort = '3';
  	  break;
		case '追加見積り':
			$status = '追加見積り';
			$c_status = '見積り';
			$status_sort = '3';
  	  break;
		case '案件の取り下げ':
			$status = 'キャンセル';
			$c_status = 'キャンセル';
			$status_sort = '92';
  	  break;
		case '発注依頼':
			$status = '発注依頼';
			$c_status = '見積り';
			$status_sort = '4';
  	  break;
		case '受注承認':
			$status = '受注承認';
			$c_status = '発注';
			$status_sort = '6';
  	  break;
		case 'データ納品':
			$status = 'データ納品';
			$c_status = '納品';
			$status_sort = '7';
  	  break;
		case '物品納品':
			$status = '物品納品';
			$c_status = '納品';
			$status_sort = '7';
  	  break;
		case '納品確認':
			$status = '納品確認';
			$c_status = '納品';
			$status_sort = '8';
  	  break;
		case '請求':
			$status = '請求';
			$c_status = '請求';
			$status_sort = '9';
  	  break;
		case '完了':
			$status = '完了';
			$c_status = '完了';
			$status_sort = '99';
  	  break;
		case '完了後対応':
			$status = '完了後対応';
			$c_status = '完了';
			$status_sort = '991';
  	  break;
		case 'キャンセル':
			$status = 'キャンセル';
			$c_status = 'キャンセル';
			$status_sort = '92';
  	  break;
		case '辞退':
			$status = '辞退';
			$c_status = '辞退';
			$status_sort = '93';
  	  break;
  	  //新規の発注キャンセル依頼
  	  case 'キャンセル依頼':
			$status = 'キャンセル依頼';
			$c_status = '実施中';
			$status_sort = '94';
  	  break;
  	   case 'サンプル送付依頼':
			$status = 'サンプル送付依頼';
			$c_status = '実施中';
			$status_sort = '100';
  	  break;
	}


	if( $type=="納品確認" && $sub_type=="研究者が納品承認(一括前払い)"){
		$status=$sub_type;
	}


	$m2_pay_type="";
	$h_div_id="";
	if($type=="発注依頼" && $h_m2_id!=""){
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$h_m2_id.";";
		$rs_chk=mysqli_query(ConnDB(),$StrSQL);
		$item_chk = mysqli_fetch_assoc($rs_chk);
		$m2_pay_type=$item_chk["M2_PAY_TYPE"];
		$h_div_id=$item_chk["DIV_ID"];

		//2回払いの発注依頼はPart0のデータが送られてくるが、Part1に発注をかけるため変数おきかえる。
		if($m2_pay_type=="Split"){
			$h_div_id_part1=check_split_progress_hatyu($item_chk["SHODAN_ID"], $h_div_id);
			if($h_div_id_part1!=""){
				$StrSQL="SELECT ID,SHODAN_ID,DIV_ID,STATUS FROM DAT_FILESTATUS WHERE DIV_ID='".$h_div_id_part1."' ";
				$StrSQL.=" AND STATUS='見積り送付' ";
				$h_part1_rs=mysqli_query(ConnDB(),$StrSQL);
				$h_part1_item = mysqli_fetch_assoc($h_part1_rs);

				if($h_part1_item["ID"]!=""){
					//条件クリアしたら、H_M2_IDと,DIV_IDをPart1のデータに置き換える。
					$h_m2_id=$h_part1_item["ID"];
					$h_div_id = $h_div_id_part1;
				}
			}
		}

	}else if($type!="" 
		&& $type!="問い合わせ" 
		&& $type!="見積り依頼"
		&& $type!="再見積り依頼"
		&& $type!="見積り送付"
		&& $type!="運営手数料追加"
		&& $type!="発注依頼"){
		//発注以降、「一括払い」時にも扱いを一律にするために、便宜上DIV_IDを設定するようにした。
		//2回払い、マイルストーンの場合は、m_chat1,m_chat2でDIV_ID（$param_div_id）を指定してここにおくってくる。
		//最新の発注依頼をとってきて、その発注依頼の対象の見積り送付データをとってきて、1括払いの時に、$h_div_idを設定
		//発注は1商談内に1つしか存在しない仕様と決定したが、念のため最新の発注依頼をとってくるようにしている。
		//商談内で複数サプライヤー一括依頼はなくなったが、$_SESSION["MID"]指定も念のため
		if($shodan_id!=""){
			$StrSQL="SELECT ID, H_M2_ID FROM DAT_FILESTATUS where SHODAN_ID='".$shodan_id."' ";
			$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
			$StrSQL.=" and STATUS='発注依頼' order by ID desc ";
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item= mysqli_fetch_assoc($h_rs);
			echo "<!--$StrSQL:[1]:\n";
			var_dump($h_item);
			echo "-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$h_item["H_M2_ID"]." ";
			$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
			$rs_chk=mysqli_query(ConnDB(),$StrSQL);
			$item_chk = mysqli_fetch_assoc($rs_chk);
			echo "<!--$StrSQL:[2]:\n";
			var_dump($item_chk);
			echo "-->";
			if($item_chk["M2_PAY_TYPE"]=="Once"){
				$h_div_id=$item_chk["DIV_ID"];
			}

		}else if($key != ''){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
			//echo('<!--'.$StrSQL.'-->');
			$s_rs=mysqli_query(ConnDB(),$StrSQL);
			$s_item = mysqli_fetch_assoc($s_rs);
			echo "<!--$StrSQL:[3]:\n";
			var_dump($s_item);
			echo "-->";
			$StrSQL="SELECT ID, H_M2_ID FROM DAT_FILESTATUS where SHODAN_ID='".$s_item['SHODAN_ID']."' ";
			$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
			$StrSQL.=" and STATUS='発注依頼' order by ID desc ";
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item= mysqli_fetch_assoc($h_rs);
			echo "<!--$StrSQL:[4]:\n";
			var_dump($h_item);
			echo "-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$h_item["H_M2_ID"]." ";
			$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
			$rs_chk=mysqli_query(ConnDB(),$StrSQL);
			$item_chk = mysqli_fetch_assoc($rs_chk);
			echo "<!--$StrSQL:[5]:\n";
			var_dump($item_chk);
			echo "-->";
			if($item_chk["M2_PAY_TYPE"]=="Once"){
				$h_div_id=$item_chk["DIV_ID"];
			}
		}
	}

	echo "<!--type: $type-->";
	echo "<!--param_div_id: $param_div_id-->";
	echo "<!--h_div_id: $h_div_id-->";

	// 商談
	if($shodan_id != '' || $type=="サンプル送付依頼") {
		// 他のステータスに変更するだけ

		if($type=="発注依頼" && $h_m2_id!=""){
			//「発注依頼」時に分割払いかどうかチェック
			//一括払いだったら更新するが、分割払いだったら更新しない
			if($m2_pay_type=="Once"){
				$StrSQL = "
				UPDATE DAT_SHODAN SET
				EDITDATE = '".$date_stmp."',
				STATUS_SORT = '".$status_sort."',
				C_STATUS = '".$c_status."',
				STATUS = '".$status."'
				WHERE
				ID = ".$shodan_id."
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
				DIV_ID = '".$h_div_id."'
				";
				//echo "<!--sql: $StrSQL-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}

		}else if($type=="納品確認"){
			//$param_div_idがDAT_SHODAN_DIVテーブル（分割支払い用のテーブル）にない場合、一括払いとして処理
			//$param_div_id自体は、見積り送信以降のフェーズの管理のため、一括でも受け渡されるように変更
			echo "<!--param_div_id at savedata:".$param_div_id."-->";
			echo "<!--checkDIV_ID(param_div_id) at savedata:".checkDIV_ID($param_div_id)."-->";
			if(checkDIV_ID($param_div_id)==""){
			//if($param_div_id==""){
				$StrSQL = "
				UPDATE DAT_SHODAN SET
				EDITDATE = '".$date_stmp."',
				STATUS_SORT = '".$status_sort."',
				C_STATUS = '".$c_status."',
				STATUS = '".$status."'
				WHERE
				ID = ".$shodan_id."
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
				DIV_ID = '".$param_div_id."'
				";
				//echo "<!--sql: $StrSQL-->";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}

		}else{
			$StrSQL = "
			UPDATE DAT_SHODAN SET
			EDITDATE = '".$date_stmp."',
			STATUS_SORT = '".$status_sort."',
			C_STATUS = '".$c_status."',
			STATUS = '".$status."'
			WHERE
			ID = ".$shodan_id."
			";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
		}
	}
	else if($key != '') {
		// 商談IDを取得してステータス更新
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_filestatus = mysqli_fetch_assoc($rs);
		$shodan_id = $item_filestatus['SHODAN_ID'];

		
		// 基本情報を更新
		$StrSQL = "
		UPDATE DAT_SHODAN SET
			EDITDATE = '".$date_stmp."',
			STATUS_SORT = '".$status_sort."',
			C_STATUS = '".$c_status."',
			STATUS = '".$status."'
		WHERE
		  ID = ".$shodan_id."
		";
	
		//以下の再見積り依頼のタイトルの更新は先方の要望により廃止
		//
		//if($type=="再見積り依頼"){
		//	// 基本情報とタイトルを更新
		//	$StrSQL = "
		//	UPDATE DAT_SHODAN SET
		//		EDITDATE = '".$date_stmp."',
		//		STATUS_SORT = '".$status_sort."',
		//		C_STATUS = '".$c_status."',
		//		STATUS = '".$status."',
		//		TITLE = '".$_POST['TITLE']."'
		//	WHERE
		//	  ID = ".$shodan_id."
		//	";
		//}else{
		//	// 基本情報を更新
		//	$StrSQL = "
		//	UPDATE DAT_SHODAN SET
		//		EDITDATE = '".$date_stmp."',
		//		STATUS_SORT = '".$status_sort."',
		//		C_STATUS = '".$c_status."',
		//		STATUS = '".$status."'
		//	WHERE
		//	  ID = ".$shodan_id."
		//	";
		//}

		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}
	else {
		// 新規

	$StrSQL = "
		INSERT INTO DAT_SHODAN (
			MID2,
			TITLE,
			MID1_LIST,
			CATEGORY,
			KEYWORD,
			COMMENT,
			FILE,
			ANSWERDATE,
			
			NEWDATE,
			EDITDATE,
			
			STATUS_SORT,
			C_STATUS,
			STATUS

		) VALUE (
			'".$_SESSION['MID']."',
			'".$post_title."',
			'".$mid1_list."',
			'".$word."',
			'".$word2."',
			'".$post_comment."',
			'".$_POST['FILE']."',
			'".$_POST['KIGEN']."',

			'".$date_stmp."',
			'".$date_stmp."',
			
			'".$status_sort."',
			'".$c_status."',
			'".$status."'
		)";


//echo "<!--StrSQL:".$StrSQL."-->";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			var_dump("err:".$StrSQL);
			die;
		}
		// 商談ID取得
		//$StrSQL="SELECT ID FROM DAT_SHODAN order by ID desc;";
		$StrSQL="SELECT ID FROM DAT_SHODAN where MID2='".$_SESSION['MID']."' and EDITDATE='".$date_stmp."' order by ID desc;";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$shodan_id = $item['ID'];
	}

	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_shodan = mysqli_fetch_assoc($rs);
	$mid2 = $item_shodan['MID2'];
	//$mid2=$_SESSION['MID'];

	// ここからは複数サプライヤー対応
	$mid1s = explode(',', $mid1_list);
	foreach($mid1s as $mid1) {

	if($h_div_id!=""){
			// ファイルステータス
			//発注依頼のために作ったが以下に仕様変更。
			//発注以降、一括払い時にも扱いを一律にするために、便宜上DIV_IDを設定するようにした。
			//発注依頼と、1活の場合にここが実行される。
		$StrSQL = "
			INSERT INTO DAT_FILESTATUS (
				SHODAN_ID,
				MID1,
				MID2,
	
				CATEGORY,
				STATUS,
	
				T_COMMENT,
				T_FILE,
				T_FILE2,
				T_FILE3,
				T_FILE4,
				T_FILE5,
				T_ANSWERDATE,
	
				M1_MESSAGE,
				M1_TRANS_FLG,
				M1_TRANS_TXT,
				M1_PRICE,
				M1_KIGEN,
				M1_FILE,
	
				H_M2_ID,
				M1_M2_ID,
				H_COMMENT,
	
				N_FILE,
				N_MESSAGE,
	
				S_FILE,
				S_MESSAGE,

				H_SHIP_TO_SPT_1,
				H_SHIP_TO_SPT_2,
				H_SHIP_TO_SPT_3,
	
				TEMPR,
				SAMPLE,
				ORIGIN,
				LEGAL,
				UNIT,
				DIV_ID,

				C_COMMENT,

				CHKNOHIN_ID,
				CHKNOHIN_MESSAGE,

				SAMPLE_HOKAN_TMPER,
				SAMPLE_HAISO_CO,
				SAMPLE_NUMBER,
				SAMPLE_DATE,
				SAMPLE_TIME,
				SAMPLE_MSG,
				SAMPLE_FIlE,
	
				NEWDATE,
				EDITDATE
			) VALUE (
				'".$shodan_id."',
				'".$mid1."',
				'".$mid2."',
	
				'".$c_status."',
				'".$status."',
	
				'".$post_comment."',
				'".$_POST['FILE']."',
				'".$_POST['FILE2']."',
				'".$_POST['FILE3']."',
				'".$_POST['FILE4']."',
				'".$_POST['FILE5']."',
				'".$_POST['KIGEN']."',
	
				'".$m1_message."',
				'".$_POST['M1_TRANS_FLG']."',
				'".$m1_trans_txt."',
				'".$m1_price."',
				'".$_POST['M1_KIGEN']."',
				'".$_POST['M1_FILE']."',
	
				'".$h_m2_id."',
				'".$_POST['M1_M2_ID']."',
				'".$h_comment."',
	
				'".$_POST['N_FILE']."',
				'".$n_message."',
	
				'".$_POST['S_FILE']."',
				'".$s_message."',

				'".$_POST['H_SHIP_TO_SPT_1']."',
				'".$_POST['H_SHIP_TO_SPT_2']."',
				'".$_POST['H_SHIP_TO_SPT_3']."',
	
				'".$post_tempr."',
				'".$post_sample."',
				'".$post_origin."',
				'".$post_legal."',
				'".$post_unit."',
				'".$h_div_id."',

				'".$c_comment."',

				'".$post_chknohin_id."',
				'".$post_chknohin_message."',

				'".$post_sample_hokan_tmper."',
				'".$post_sample_haiso_co."',
				'".$post_sample_number."',
				'".$post_sample_date."',
				'".$post_sample_time."',
				'".$post_sample_msg."',
				'".$post_sample_file."',
	
				'".$date_stmp."',
				'".$date_stmp."'
			)";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

	}else{
		// ファイルステータス
		$StrSQL = "
			INSERT INTO DAT_FILESTATUS (
				SHODAN_ID,
				MID1,
				MID2,
	
				CATEGORY,
				STATUS,
	
				T_COMMENT,
				T_FILE,
				T_FILE2,
				T_FILE3,
				T_FILE4,
				T_FILE5,
				T_ANSWERDATE,
	
				M1_MESSAGE,
				M1_TRANS_FLG,
				M1_TRANS_TXT,
				M1_PRICE,
				M1_KIGEN,
				M1_FILE,
	
				H_M2_ID,
				M1_M2_ID,
				H_COMMENT,
	
				N_FILE,
				N_MESSAGE,
	
				S_FILE,
				S_MESSAGE,

				H_SHIP_TO_SPT_1,
				H_SHIP_TO_SPT_2,
				H_SHIP_TO_SPT_3,
	
				TEMPR,
				SAMPLE,
				ORIGIN,
				LEGAL,
				UNIT,
				DIV_ID,

				C_COMMENT,

				CHKNOHIN_ID,
				CHKNOHIN_MESSAGE,

				SAMPLE_HOKAN_TMPER,
				SAMPLE_HAISO_CO,
				SAMPLE_NUMBER,
				SAMPLE_DATE,
				SAMPLE_TIME,
				SAMPLE_MSG,
				SAMPLE_FIlE,

				NEWDATE,
				EDITDATE
			) VALUE (
				'".$shodan_id."',
				'".$mid1."',
				'".$mid2."',
	
				'".$c_status."',
				'".$status."',
	
				'".$post_comment."',
				'".$_POST['FILE']."',
				'".$_POST['FILE2']."',
				'".$_POST['FILE3']."',
				'".$_POST['FILE4']."',
				'".$_POST['FILE5']."',
				'".$_POST['KIGEN']."',
	
				'".$m1_message."',
				'".$_POST['M1_TRANS_FLG']."',
				'".$m1_trans_txt."',
				'".$m1_price."',
				'".$_POST['M1_KIGEN']."',
				'".$_POST['M1_FILE']."',
	
				'".$h_m2_id."',
				'".$_POST['M1_M2_ID']."',
				'".$h_comment."',
	
				'".$_POST['N_FILE']."',
				'".$n_message."',
	
				'".$_POST['S_FILE']."',
				'".$s_message."',

				'".$_POST['H_SHIP_TO_SPT_1']."',
				'".$_POST['H_SHIP_TO_SPT_2']."',
				'".$_POST['H_SHIP_TO_SPT_3']."',
	
				'".$post_tempr."',
				'".$post_sample."',
				'".$post_origin."',
				'".$post_legal."',
				'".$post_unit."',
				'".$param_div_id."',

				'".$c_comment."',

				'".$post_chknohin_id."',
				'".$post_chknohin_message."',

				'".$post_sample_hokan_tmper."',
				'".$post_sample_haiso_co."',
				'".$post_sample_number."',
				'".$post_sample_date."',
				'".$post_sample_time."',
				'".$post_sample_msg."',
				'".$post_sample_file."',
	
				'".$date_stmp."',
				'".$date_stmp."'
			)";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}


	// filestatusのIDを取得
	//$StrSQL="SELECT ID FROM DAT_FILESTATUS order by ID desc;";
	$StrSQL="SELECT ID FROM DAT_FILESTATUS where MID2='".$_SESSION['MID']."' and EDITDATE='".$date_stmp."' order by ID desc;";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus = mysqli_fetch_assoc($rs);
	$key = $item_filestatus['ID'];


	// ファイルを移動
	$file_dir = __dir__ . '/../a_filestatus/data/';
	if(!file_exists($file_dir . $key . '/')) {
		mkdir($file_dir . $key, 0777, true);
	}
	
	if($_POST['FILE'] != '') {
		$save_filename=basename($_POST['FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['FILE2'] != '') {
		$save_filename=basename($_POST['FILE2']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['FILE3'] != '') {
		$save_filename=basename($_POST['FILE3']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['FILE4'] != '') {
		$save_filename=basename($_POST['FILE4']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}

	if($_POST['FILE5'] != '') {
		$save_filename=basename($_POST['FILE5']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['M1_FILE'] != '') {
		$save_filename=basename($_POST['M1_FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['N_FILE'] != '') {
		$save_filename=basename($_POST['N_FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}
	
	if($_POST['S_FILE'] != '') {
		$save_filename=basename($_POST['S_FILE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}

	if($_POST['M2_FILE1'] != '') {
		$save_filename=basename($_POST['M2_FILE1']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}

	if($_POST['SAMPLE_FIlE'] != '') {
		$save_filename=basename($_POST['SAMPLE_FIlE']);
		rename($file_dir . $save_filename, $file_dir . $key . '/' . $save_filename);
	}

	//$file_dir = __dir__ . '/../a_filestatus/data/';
	//if(!file_exists($file_dir . $key . '/')) {
	//	mkdir($file_dir . $key, 0777, true);
	//}
	//if($_POST['FILE'] != '') {
	//	copy($file_dir . $_POST['FILE'], $file_dir . $key . '/' . $_POST['FILE']);
	//}
	//if($_POST['FILE2'] != '') {
	//	copy($file_dir . $_POST['FILE2'], $file_dir . $key . '/' . $_POST['FILE2']);
	//}
	//if($_POST['FILE3'] != '') {
	//	copy($file_dir . $_POST['FILE3'], $file_dir . $key . '/' . $_POST['FILE3']);
	//}
	//if($_POST['FILE4'] != '') {
	//	copy($file_dir . $_POST['FILE4'], $file_dir . $key . '/' . $_POST['FILE4']);
	//}
	//if($_POST['FILE5'] != '') {
	//	copy($file_dir . $_POST['FILE5'], $file_dir . $key . '/' . $_POST['FILE5']);
	//}
	//if($_POST['M1_FILE'] != '') {
	//	copy($file_dir . $_POST['M1_FILE'], $file_dir . $key . '/' . $_POST['M1_FILE']);
	//}
	//if($_POST['N_FILE'] != '') {
	//	copy($file_dir . $_POST['N_FILE'], $file_dir . $key . '/' . $_POST['N_FILE']);
	//}
	//if($_POST['S_FILE'] != '') {
	//	copy($file_dir . $_POST['S_FILE'], $file_dir . $key . '/' . $_POST['S_FILE']);
	//}





	//
	//DAT_FILESTATUS_DETAILは「見積り送付」のアイテムのためのデータなので、研究者側はいらいない。
	//
	// 見積り書の場合
	// ファイルステータス
	//	$StrSQL = "
	//		INSERT INTO DAT_FILESTATUS_DETAIL (
	//			FILESTATUS_ID,
	//
	//			TITLE,
	//			PRICE,
	//			COMMENT,
	//
	//			NEWDATE,
	//			EDITDATE
	//		) VALUE (
	//			'".$key."',
	//
	//			'".$m2_title."',
	//			'".$m2_price."',
	//			'".$m2_comment."',
	//
	//			'".$date_stmp."',
	//			'".$date_stmp."'
	//		)";
	//	if (!(mysqli_query(ConnDB(),$StrSQL))) {
	//		die;
	//	}



	$StrSQL="SELECT DIV_ID FROM DAT_FILESTATUS where MID2='".$_SESSION['MID']."' and ID='".$key."' order by ID desc;";
	//echo('<!--'.$StrSQL.'-->');
	$tmp_rs=mysqli_query(ConnDB(),$StrSQL);
	$tmp_item = mysqli_fetch_assoc($tmp_rs);

	$StrSQL="SELECT * FROM DAT_FILESTATUS where MID2='".$_SESSION['MID']."' ";
	$StrSQL.=" and DIV_ID='".$tmp_item["DIV_ID"]."' and STATUS='見積り送付' order by ID desc ";
	//echo('<!--'.$StrSQL.'-->');
	$scn_rs=mysqli_query(ConnDB(),$StrSQL);
	$scn_item = mysqli_fetch_assoc($scn_rs);


	//Scientist Control No.
	$SCNo_str="";
	$SCNo_ary=array(
		"SCNo_yy" => "", 
		"SCNo_mm" => "", 
		"SCNo_dd" => "", 
		"SCNo_cnt" => "", 
		"SCNo_else1" => "", 
		"SCNo_else2" => "", 
	);
	$SCNo_ary=array(
		"SCNo_yy" => $scn_item['SCNo_yy'], 
		"SCNo_mm" => $scn_item['SCNo_mm'], 
		"SCNo_dd" => $scn_item['SCNo_dd'], 
		"SCNo_cnt" => $scn_item['SCNo_cnt'], 
		"SCNo_else1" => $scn_item['SCNo_else1'], 
		"SCNo_else2" => $scn_item['SCNo_else2'], 
	);
	$SCNo_str=formatAlphabetId($SCNo_ary);
	$SCNo_str.="-Version".$scn_item["M2_VERSION"];



	$cancel_scno_ary=array();
	if($type=="キャンセル依頼"){

		$StrSQL="SELECT ID,SHODAN_ID,DIV_ID,STATUS,H_M2_ID FROM DAT_FILESTATUS WHERE SHODAN_ID = '" . $shodan_id . "' ";
		$StrSQL.=" AND STATUS='発注依頼' ";
		$StrSQL.=" order by ID asc";
		$c_hatyu_rs=mysqli_query(ConnDB(),$StrSQL);
		
		while( $c_hatyu_item = mysqli_fetch_assoc($c_hatyu_rs) ){
			//echo "<!--キャンセル依頼(発注依頼)：";
			//var_dump($c_hatyu_item);
			//echo "-->";
			$StrSQL="SELECT ID,SHODAN_ID,DIV_ID,STATUS,SCNo_yy,SCNo_mm,SCNo_dd,SCNo_cnt,SCNo_else1,SCNo_else2,M2_VERSION ";
			$StrSQL.=" FROM DAT_FILESTATUS WHERE SHODAN_ID = '" . $shodan_id . "' ";
			$StrSQL.=" AND ID='".$c_hatyu_item["H_M2_ID"]."' ";
			$StrSQL.=" AND STATUS='見積り送付' ";
			$StrSQL.=" order by ID desc";
			$c_mitsu_rs=mysqli_query(ConnDB(),$StrSQL);
			$c_mitsu_item = mysqli_fetch_assoc($c_mitsu_rs);
			//echo "<!--キャンセル依頼(見積り送付)：";
			//var_dump($c_mitsu_item);
			//echo "-->";

			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$m2_quote_no="";

			$SCNo_ary["SCNo_yy"]=$c_mitsu_item["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$c_mitsu_item["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$c_mitsu_item["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$c_mitsu_item["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$c_mitsu_item["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$c_mitsu_item["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);
			if($SCNo_str!=""){
				$cancel_scno_ary[]=$SCNo_str."-Version".$c_mitsu_item["M2_VERSION"];
			}
		}

		//echo "<!--キャンセル依頼(SCNO)：";
		//var_dump($cancel_scno_ary);
		//echo "-->";
	}




// 自分へのメッセージ
	$comment = '';
	switch($type) {
	//	case '問い合わせ':
	//		$comment = '問い合わせを送信しました<br><br>' . $post_title . '<br>' . $post_comment . '<br>' . $_POST['KIGEN'] . '<br>
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE'] . '">' . $_POST['FILE'] . '</a><br> 
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE2'] . '">' . $_POST['FILE2'] . '</a><br>
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE3'] . '">' . $_POST['FILE3'] . '</a><br> 
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE4'] . '">' . $_POST['FILE4'] . '</a><br> 
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE5'] . '">' . $_POST['FILE5'] . '</a>';
  	//  break;
		case '問い合わせ':
			$comment = '問い合わせを送信しました<br><br>' 
				.'案件名: '. $post_title . '<br>' 
				.'お問い合わせ内容: '. $post_comment . '<br>' 
				.'回答希望日: '. $_POST['KIGEN'] . '<br>
				添付ファイル: 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE'] . '" target="_blank">' . $_POST['FILE'] . '</a> 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE2'] . '" target="_blank">' . $_POST['FILE2'] . '</a>
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE3'] . '" target="_blank">' . $_POST['FILE3'] . '</a> 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE4'] . '" target="_blank">' . $_POST['FILE4'] . '</a> 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE5'] . '" target="_blank">' . $_POST['FILE5'] . '</a>';
		break;
		case '見積り依頼':
			$comment = '見積り依頼を送信しました<br><br>' . $m1_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact2/?type=見積り依頼&mode=disp_frame&key='.$key.'\'\');">
				見積り依頼
			</a>
			';
		break;
		case '再見積り依頼':
			$comment = '再見積り依頼を送信しました<br><br>' . $m1_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact2/?type=再見積り依頼&mode=disp_frame&key='.$key.'\'\');">
				再見積り依頼
			</a>
			';
		break;
		case '見積り送付':
			$comment = '見積り書を送付しました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
				見積り書
			</a>
			';
		break;
		case '追加見積り':
			$comment = '追加見積りを送付しました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
				見積り書
			</a>
			';
		break;
		case '発注依頼':
			//発注依頼のときは、param_div_idではなくdiv_id
			$comment = $h_comment . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact2/?type=発注依頼&mode=disp_frame&key='.$key.'\'\');">
				発注依頼 '.$SCNo_str.'
			</a>
			';
			//$comment = $h_comment . '
			//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact2/?type=発注依頼&mode=disp_frame&key='.$key.'\'\');">
			//	発注依頼 '.$h_div_id.'
			//</a>
			//';
		break;
		case 'データ納品':
			$comment = $n_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=データ納品&mode=disp_frame&key='.$key.'\'\');">
				納品データ '.$SCNo_str.'
			</a>
			';
			//$comment = $n_message . '
			//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=データ納品&mode=disp_frame&key='.$key.'\'\');">
			//	納品データ '.$param_div_id.'
			//</a>
			//';
		break;
		case '物品納品':
			$comment = $n_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=物品納品&mode=disp_frame&key='.$key.'\'\');">
				Report '.$SCNo_str.'
			</a>
			';
			//$comment = $n_message . '
			//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=物品納品&mode=disp_frame&key='.$key.'\'\');">
			//	Report '.$param_div_id.'
			//</a>
			//';
		break;
		case '請求':
			$comment = $s_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$key.'\'\');">
				請求書 '.$SCNo_str.'
			</a>
			';
			//$comment = $s_message . '
			//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$key.'\'\');">
			//	請求書 '.$param_div_id.'
			//</a>
			//';
		break;
		case 'キャンセル依頼':
			$comment = 'キャンセル依頼中です<br>
			発注キャンセルの理由：'.$c_comment.'<br>
			';
		break;
	}

	echo "<!--type,comment1:$type, $comment-->";

	// DAT_MESSAGE
	if($comment != '') {
		$aid = $mid1 . '-' . $mid2;
		$StrSQL = "
			INSERT INTO DAT_MESSAGE (
				AID,
				RID,
				ENABLE,
				NEWDATE,
				COMMENT,
				ETC02,
				ETC03,
				ETC04
			) VALUE (
				'".$aid."',
				'".$_SESSION['MID']."',
				'ENABLE:公開中',
				'".$date_stmp."',
				'".$comment."',
				'".$shodan_id."',
				'".$_SESSION['MID']."',
				'".$key."'
			)
		";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}

	// 相手へのメッセージ
	$comment = '';
	switch($type) {
	//	case '問い合わせ':
	//		$comment = '問い合わせが送信されました<br><br>' . $post_title . '<br>' . $post_comment . '<br>' . $_POST['KIGEN'] . '<br>
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE'] . '">' . $_POST['FILE'] . '</a><br> 
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE2'] . '">' . $_POST['FILE2'] . '</a><br>
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE3'] . '">' . $_POST['FILE3'] . '</a><br> 
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE4'] . '">' . $_POST['FILE4'] . '</a><br> 
	//			<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE5'] . '">' . $_POST['FILE5'] . '</a>';
  	  //break;
		case '問い合わせ':
			$comment = '問い合わせが送信されました<br><br>' 
				.'Subject: '. $post_title . '<br>' 
				.'Inquiry: '. $post_comment . '<br>' 
				.'Due date: '. $_POST['KIGEN'] . '<br>
				Attached File: 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE'] . '" target="_blank">' . $_POST['FILE'] . '</a> 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE2'] . '" target="_blank">' . $_POST['FILE2'] . '</a>
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE3'] . '" target="_blank">' . $_POST['FILE3'] . '</a> 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE4'] . '" target="_blank">' . $_POST['FILE4'] . '</a> 
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE5'] . '" target="_blank">' . $_POST['FILE5'] . '</a>';
		break;
		case '見積り依頼':
			$comment = '見積り依頼が送信されました<br><br>' . $m1_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り依頼&mode=disp_frame&key='.$key.'\'\');">
				Quote Request
			</a>
			';
		break;
		case '再見積り依頼':
			$comment = '再見積り依頼が送信されました<br><br>' . $m1_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=再見積り依頼&mode=disp_frame&key='.$key.'\'\');">
				Quote Request
			</a>
			';
		break;
		case '見積り送付':
			$comment = '見積り書が送付されました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
				見積り書
			</a>
			';
		break;
		case '追加見積り':
			$comment = '追加見積りが送付されました
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
				見積り書
			</a>
			';
		break;
		/* 発注依頼の段階でサプライヤーには通知しない
		case '発注依頼':
			$comment = $h_comment . '
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$key.'\'\');">発注依頼</a>
			';
  	  break;
		*/
		case 'データ納品':
			$comment = $n_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=データ納品&mode=disp_frame&key='.$key.'\'\');">
				納品データ '.$param_div_id.'
			</a>
			';
		break;
		case '物品納品':
 			$comment = $n_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=物品納品&mode=disp_frame&key='.$key.'\'\');">
				Report '.$param_div_id.'
			</a>
			';
		break;
		case '請求':
			$comment = $s_message . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$key.'\'\');">
				請求書 '.$param_div_id.'
			</a>
			';
		break;
		case 'キャンセル依頼':
		$c_hatyu_str="";
		if( count($cancel_scno_ary)>0 ){
			foreach ($cancel_scno_ary as $idx => $val) {
				if($val!=""){
					$c_hatyu_str.=$val."<br>";
				}
			}
		}
		if($c_hatyu_str!=""){
			$c_hatyu_str="Order No:<br>".$c_hatyu_str;
		}

		$comment = 'A cancellation request has been received from the researcher.<br>
		'.$c_hatyu_str.'
		Reason for order cancellation：'.$c_comment.'<br>
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=サプライヤーキャンセル承認&mode=save&shodan_id='.$shodan_id.'\'\');">Approve (Already billed)</a>
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=キャンセル承認&mode=save&shodan_id='.$shodan_id.'\'\');">Approve (Not yet billed)</a>
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=サプライヤーキャンセル否認&mode=save&shodan_id='.$shodan_id.'\'\');">Decline</a>
		';
		//$comment = 'キャンセル依頼中です<br>
		//発注キャンセルの理由：'.$c_comment.'<br>
		//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=サプライヤーキャンセル承認&mode=save&shodan_id='.$shodan_id.'\'\');">承認する（請求あり）</a>
		//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=キャンセル承認&mode=save&shodan_id='.$shodan_id.'\'\');">承認する（請求なし）</a>
		//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=サプライヤーキャンセル否認&mode=save&shodan_id='.$shodan_id.'\'\');">否認する</a>
		//';
		break;
	}

	echo "<!--type,comment2:$type, $comment-->";

	// DAT_MESSAGE
	if($comment != '') {
		$aid = $mid1 . '-' . $mid2;
		$StrSQL = "
			INSERT INTO DAT_MESSAGE (
				AID,
				RID,
				ENABLE,
				NEWDATE,
				COMMENT,
				ETC02,
				ETC03,
				ETC04
			) VALUE (
				'".$aid."',
				'".$_SESSION['MID']."',
				'ENABLE:公開中',
				'".$date_stmp."',
				'".$comment."',
				'".$shodan_id."',
				'".$mid1."',
				'".$key."'
			)
		";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}


	} // 複数サプライヤーのforeach

	/*
	$FieldValue[1] = $_SESSION['MID'];
	$FieldValue[2] = $key;
	$FieldValue[3] = $word;
	$FieldValue[4] = $word2;

	$StrSQL="SELECT * FROM ".$TableName." WHERE MID='".$_SESSION['MID']."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item=="") {
		$StrSQL="INSERT INTO ".$TableName." (";
		for ($i=1; $i<=$FieldMax-1; $i=$i+1) {
			$StrSQL=$StrSQL."`".$FieldName[$i]."`,";
		}

		$StrSQL=$StrSQL."`".$FieldName[$FieldMax]."`";
		$StrSQL=$StrSQL.") VALUES (";
		for ($i=1; $i<=$FieldMax-1; $i=$i+1) {
			$StrSQL=$StrSQL."'".str_replace("'","''",$FieldValue[$i])."',";
		}

		$StrSQL=$StrSQL."'".str_replace("'","''",$FieldValue[$FieldMax])."'";
		$StrSQL=$StrSQL.")";
	} else {
		$StrSQL="UPDATE ".$TableName." SET ";
		for ($i=1; $i<=$FieldMax-1; $i=$i+1) {
			$StrSQL=$StrSQL."`".$FieldName[$i]."`='".str_replace("'","''",$FieldValue[$i])."',";
		}

		$StrSQL=$StrSQL."`".$FieldName[$FieldMax]."`='".str_replace("'","''",$FieldValue[$FieldMax])."' ";
		$StrSQL=$StrSQL."WHERE MID='".$_SESSION['MID']."'";
	} 

	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}
	*/

	return $function_ret;
} 

//======================================================================
//名前 GetFld
//機能\ フィールドの値を取得
//引数 (i) pfldCol		：フィールド
//		 (i) pvarNull		：Null時代替
//戻値 フィールド値
//詳細 
//======================================================================
function GetFld($pfldCol,$pvarNull)
{
	eval(globals());

//Null確認
	if (!isset($pfldCol->Value)==true) {
		$function_ret=$pvarNull;
		return $function_ret;
	} 


//値取得
	$function_ret=$pfldCol->Value;

	return $function_ret;
} 


//=========================================================================================================
//名前 SQL条件（WHERE ･･･ ORDER BY･･･）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ListSQLSearch($word,$word2)
{
	//extract($GLOBALS);
	eval(globals());

	$word = str_replace(',', "\t", $word);

	$str="DAT_O1.ID>0";

	if ($word!=""){
		if(strstr($word, "MID:")==true){
			$str.=" AND (DAT_M1.MID='".str_replace("MID:", "", $word)."')";
		} else {
			//$tmp1=explode("::","DAT_M1.M1_DVAL01::DAT_O1.O1_DVAL01::DAT_M1.M1_DTXT01::DAT_O1.O1_DTXT01");
			$tmp2=explode("\t",str_replace(" ", "\t", str_replace("　", " ", $word))."\t");
			//$tmp3="";
			for ($j=0; $j<count($tmp2); $j++) {

        // カテゴリー
        if(strpos($tmp2[$j], 'O1_MSEL01:') !== false) {
				  $str.=" AND DAT_O1.O1_MSEL01 = '".$tmp2[$j]."' ";
        }
        // 国、GLP、〇〇については仕様不明
        else if(strpos($tmp2[$j], 'O1_???:') !== false) {
				  $str.=" AND DAT_O1.O1_??? = '".$tmp2[$j]."' ";
        }

        /*
				if($tmp2[$j]!=""){
					$tmp4="";
					for ($i=0; $i<count($tmp1); $i++) {
						if($tmp4!=""){
							$tmp4.=" or ";
						}
						$tmp4.=$tmp1[$i]." like \"%".$tmp2[$j]."%\"";
					}
					if($tmp3!=""){
						$tmp3.=" or ";
					}
					$tmp3.="(".$tmp4.")";
				}
        */
			}
      /*
			if($tmp3!=""){
				$str.=" AND (".$tmp3.")";
			} 
      */
		} 
	} 

  if($word2!=""){
    $str=$str." AND (";
    
    $str=$str."    DAT_O1.O1_DVAL01 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DVAL02 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT01 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT02 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT03 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT04 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT05 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT06 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT07 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT08 like '%".$word2."%'";
    $str=$str." OR DAT_M1.EMAIL like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC02 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL01 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL02 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL03 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL04 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL05 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL06 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL07 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL08 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL09 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL10 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL11 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DTXT01 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DTXT02 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DSEL01 like '%".$word2."%'";
    
    $str=$str." ) ";
  }

	$function_ret=$str;

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
