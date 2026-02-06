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

	if($_POST['mode']==""){
		$type=$_GET['type'];
		$mode=$_GET['mode'];
		$sort=$_GET['sort'];
		$word=$_GET['word'];
		$word2=$_GET['word2'];
		$mid_list=$_GET['mid_list'];
		$m1_id=$_GET['m1_id'];
		$key=$_GET['key'];
		$shodan_id=$_GET['shodan_id'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
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
		$key=$_POST['key'];
		$shodan_id=$_POST['shodan_id'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		
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

	// Supplier情報取得
	// Supplier側は自分固定
	// Researchers側から呼ばれることもある！！
	if(strpos($_SESSION['MID'], 'M1') !== false) {
		$StrSQL="
		  SELECT
			  *
			FROM
				DAT_M1
			WHERE
				DAT_M1.MID = '".$_SESSION['MID']."'
		";
	}
	else if($key != '') {
		$StrSQL="
		  SELECT
			  *
			FROM
				DAT_M1
				join DAT_FILESTATUS
				  on DAT_FILESTATUS.MID1 = DAT_M1.MID
			WHERE
				DAT_FILESTATUS.ID = '".$key."'
		";
	}
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m1_list = array();
	$mid1_list = '';
	while ($item = mysqli_fetch_assoc($rs)) {
		$m1_list[] = $item;
		$mid1_list .= ($mid1_list != '' ? ',' : '') . $item['MID'];
	}
	//echo "<!--mid1_list:";
	//var_dump($mid1_list);
	//echo "-->";
	//echo('<!--mode:'.$mode.'-->');
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
		case "preview":
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "saveconf":
			//RequestData($obj,$a,$b,$key,$mode);
			break;
		case "saveconf2":
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "save":
			RequestData($obj,$a,$b,$key,$mode);

			$StrSQL="SELECT * from DAT_M2 where MID = '".$_SESSION['MID']."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$m2 = mysqli_fetch_assoc($rs);

			if($m2["ID"]==""){
				$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
				//echo('<!--'.$StrSQL.'-->');
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$item_shodan = mysqli_fetch_assoc($rs);

				$StrSQL="SELECT * from DAT_M2 where MID = '".$item_shodan["MID2"]."';";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$m2 = mysqli_fetch_assoc($rs);
			}



			// DAT_SHODANとDAT_MESSAGEに登録する
			SaveData($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$m2,$word,$word2,$mid_list,$param_div_id,$sub_type);


			SendMail($type,$m1_list,$m2);

			if($shodan_id!="" && $type=="見積りの辞退"){
				SendMail_v2($shodan_id);
				SendMail_v2_2($shodan_id);
			}

			if($type=="サプライヤーキャンセル承認"){
				$url=BASE_URL . "/m_contact1/?type=サプライヤーキャンセル承認&mode=end&shodan_id=".$shodan_id;
				header("Location: {$url}");
			}else if($type=="サプライヤーキャンセル否認"){
				$url=BASE_URL . "/m_contact1/?type=サプライヤーキャンセル否認&mode=end&shodan_id=".$shodan_id;
				header("Location: {$url}");
			}else if($type=="キャンセル承認"){
				$url=BASE_URL . "/m_contact1/?type=キャンセル承認&mode=end&shodan_id=".$shodan_id;
				header("Location: {$url}");
			}

			break;
		case "save2":
			RequestData($obj,$a,$b,$key,$mode);
			SaveData($key,$word,$word2);
			$mode="edit";
			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			break;
	} 

	DispData($type,$mode,$sort,$word,$word2,$mid_list,$m1_id,$key,$shodan_id,$page,$lid,$m1_list,$mid1_list,$param_div_id,$sub_type);

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


	$maildata = GetMailTemplate('研究者への見積もり送信(M1)');
	if($type=="見積り送付" || $type=="追加見積り"){
		$maildata = GetMailTemplate('研究者への見積もり送信(M1)');
	}
	if($type=="物品納品"){
		$maildata = GetMailTemplate('物品納品(M1)');
	}
	if($type=="データ納品"){
		$maildata = GetMailTemplate('データ納品(M1)');
	}
	if($type=="請求"){
		$maildata = GetMailTemplate('請求(M1)');
	}

	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$m1_view = '';
	foreach($m1_list as $item) {
		$m1_view .= $item['M1_DVAL01'] . '<br>';
	}

	foreach($m1_list as $item) {
		$mailto = $item['EMAIL'];
		// $mailto = "toretoresansan00@gmail.com";

		$MailBody=str_replace("[D-NAME]",$item['M1_DVAL01'],$MailBody);

		// Researchers情報
		if($_POST['TITLE'] != '') {
			$MailBody=str_replace("[D-TITLE]",$_POST['TITLE'],$MailBody);
		}
		else {
			$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_shodan = mysqli_fetch_assoc($rs);

			$MailBody=str_replace("[D-TITLE]",$item_shodan['TITLE'],$MailBody);
		}

		$MailBody=str_replace("[D-M1_MESSAGE]",str_replace("\n", '<br>', $_POST['M1_MESSAGE']),$MailBody); // Dはbr変換

		if($_POST['M1_TRANS_FLG']=="あり"){
			$eng_m1_trans_flg="Yes";
		}else if($_POST['M1_TRANS_FLG']=="なし"){
			$eng_m1_trans_flg="No";     
		}else{
			$eng_m1_trans_flg="";
		}
		$MailBody=str_replace("[D-M1_TRANS_FLG]",$eng_m1_trans_flg,$MailBody);
		//$MailBody=str_replace("[D-M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$MailBody);
		
		$MailBody=str_replace("[D-M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$MailBody);
		$MailBody=str_replace("[D-M1_PRICE]",$_POST['M1_PRICE'],$MailBody);
		$MailBody=str_replace("[D-M1_KIGEN]",$_POST['M1_KIGEN'],$MailBody);
		$MailBody=str_replace("[D-M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$MailBody);

		$MailBody=str_replace("[M1_VIEW]",$m1_view,$MailBody);

		// 新Quotation
		// -------------------------------------------------------------------------------
		// 水際英訳
		//$MailBody=str_replace("[D-M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$MailBody);
		$MailBody=str_replace("[D-M2_NOHIN_TYPE]",implode(',', showStatusAll($_POST['M2_NOHIN_TYPE'])),$MailBody);
		//$MailBody=str_replace("[D-M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$MailBody);
		$MailBody=str_replace("[D-M2_PAY_TYPE]",showStatusAll($_POST['M2_PAY_TYPE']),$MailBody);

		$MailBody=str_replace("[D-M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$MailBody);
		$MailBody=str_replace("[D-M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$MailBody);
		$MailBody=str_replace("[D-M2_DATE]",$_POST['M2_DATE'],$MailBody);
		$MailBody=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$MailBody);
		$MailBody=str_replace("[D-M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$MailBody);
		$MailBody=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:","",$_POST['M2_CURRENCY']),$MailBody);
		//$MailBody=str_replace("[D-M2_CURRENCY]",$_POST['M2_CURRENCY'],$MailBody);

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


		$MailBody=str_replace("[D-H_M2_ID]",$_POST['H_M2_ID'],$MailBody);
		
		$MailBody=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $_POST['H_COMMENT']),$MailBody); // Dはbr変換

		$MailBody=str_replace("[D-N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$MailBody);
		$MailBody=str_replace("[D-N_FILE2]",(isset($_POST['N_FILE2']) ? $_POST['N_FILE2'] : $_FILES['N_FILE2']['name']),$MailBody);
		$MailBody=str_replace("[D-N_FILE3]",(isset($_POST['N_FILE3']) ? $_POST['N_FILE3'] : $_FILES['N_FILE3']['name']),$MailBody);
		$MailBody=str_replace("[D-N_FILE4]",(isset($_POST['N_FILE4']) ? $_POST['N_FILE4'] : $_FILES['N_FILE4']['name']),$MailBody);
		$MailBody=str_replace("[D-N_FILE5]",(isset($_POST['N_FILE5']) ? $_POST['N_FILE5'] : $_FILES['N_FILE5']['name']),$MailBody);
		$MailBody=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $_POST['N_MESSAGE']),$MailBody); // Dはbr変換

		$MailBody=str_replace("[D-N_PDF]",(isset($_POST['N_PDF']) ? $_POST['N_PDF'] : $_FILES['N_PDF']['name']),$MailBody);
		$MailBody=str_replace("[D-N_SHUKKA]",$_POST['N_SHUKKA'],$MailBody);
		$MailBody=str_replace("[D-N_TEMP1]",$_POST['N_TEMP1'],$MailBody);
		$MailBody=str_replace("[D-N_TEMP2]",$_POST['N_TEMP2'],$MailBody);
		$MailBody=str_replace("[D-N_AWB]",$_POST['N_AWB'],$MailBody);

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
		
		if($_POST['M1_TRANS_FLG']=="あり"){
			$eng_m1_trans_flg="Yes";
		}else if($_POST['M1_TRANS_FLG']=="なし"){
			$eng_m1_trans_flg="No";			
		}else{
			$eng_m1_trans_flg="";
		}
		$MailBody=str_replace("[M1_TRANS_FLG]",$eng_m1_trans_flg,$MailBody);
		//$MailBody=str_replace("[M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$MailBody);
		
		$MailBody=str_replace("[M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$MailBody);
		$MailBody=str_replace("[M1_PRICE]",$_POST['M1_PRICE'],$MailBody);
		$MailBody=str_replace("[M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$MailBody);
		$MailBody=str_replace("[M1_KIGEN]",$_POST['M1_KIGEN'],$MailBody);

		$MailBody=str_replace("[M2_TITLE]",$_POST['M2_TITLE'],$MailBody);
		$MailBody=str_replace("[M2_PRICE]",$_POST['M2_PRICE'],$MailBody);
		$MailBody=str_replace("[M2_COMMENT]",$_POST['M2_COMMENT'],$MailBody);

		// 新Quotation
		// -------------------------------------------------------------------------------
		if(is_array($_POST['M2_NOHIN_TYPE'])) {
			// 水際英訳
			//$MailBody=str_replace("[M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$MailBody);
			$MailBody=str_replace("[M2_NOHIN_TYPE]",implode(',', showStatusAll($_POST['M2_NOHIN_TYPE'])),$MailBody);
		}
		else {
			// 水際英訳
			//$MailBody=str_replace("[M2_NOHIN_TYPE]",$_POST['M2_NOHIN_TYPE'],$MailBody);
			$MailBody=str_replace("[M2_NOHIN_TYPE]",showStatusAll($_POST['M2_NOHIN_TYPE']),$MailBody);
		}
		//$MailBody=str_replace("[M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$MailBody);
		$MailBody=str_replace("[M2_PAY_TYPE]",showStatusAll($_POST['M2_PAY_TYPE']),$MailBody);

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

		// 戻る押下時のためのセット
		$post_detail = array();
		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']); $detail_key++) {
			$post_detail[] = array(
				"M2_DETAIL_ITEM" => $_POST['M2_DETAIL_ITEM'][$detail_key],
				"M2_DETAIL_DESCRIPTION" => $_POST['M2_DETAIL_DESCRIPTION'][$detail_key],
				"M2_DETAIL_PRICE" => $_POST['M2_DETAIL_PRICE'][$detail_key],
				"M2_DETAIL_NOTE" => $_POST['M2_DETAIL_NOTE'][$detail_key]
			);
		}
		$MailBody=str_replace("[POST_DETAIL]",json_encode($post_detail,JSON_UNESCAPED_UNICODE),$MailBody);

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
		$MailBody=str_replace("[H_COMMENT]",$_POST['H_COMMENT'],$MailBody);

		$MailBody=str_replace("[N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$MailBody);
		$MailBody=str_replace("[N_FILE2]",(isset($_POST['N_FILE2']) ? $_POST['N_FILE2'] : $_FILES['N_FILE2']['name']),$MailBody);
		$MailBody=str_replace("[N_FILE3]",(isset($_POST['N_FILE3']) ? $_POST['N_FILE3'] : $_FILES['N_FILE3']['name']),$MailBody);
		$MailBody=str_replace("[N_FILE4]",(isset($_POST['N_FILE4']) ? $_POST['N_FILE4'] : $_FILES['N_FILE4']['name']),$MailBody);
		$MailBody=str_replace("[N_FILE5]",(isset($_POST['N_FILE5']) ? $_POST['N_FILE5'] : $_FILES['N_FILE5']['name']),$MailBody);
		$MailBody=str_replace("[N_MESSAGE]",$_POST['N_MESSAGE'],$MailBody);

		$MailBody=str_replace("[N_PDF]",(isset($_POST['N_PDF']) ? $_POST['N_PDF'] : $_FILES['N_PDF']['name']),$MailBody);
		$MailBody=str_replace("[N_SHUKKA]",$_POST['N_SHUKKA'],$MailBody);
		$MailBody=str_replace("[N_TEMP1]",$_POST['N_TEMP1'],$MailBody);
		$MailBody=str_replace("[N_TEMP2]",$_POST['N_TEMP2'],$MailBody);
		$MailBody=str_replace("[N_AWB]",$_POST['N_AWB'],$MailBody);

		$MailBody=str_replace("[S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$MailBody);
		$MailBody=str_replace("[S_MESSAGE]",$_POST['S_MESSAGE'],$MailBody);


		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
// var_dump($MailBody);
		mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">");
	}

	$maildata2 = GetMailTemplate('研究者への見積もり送信(M2)');
	if($type=="見積り送付" || $type=="追加見積り"){
		$maildata2 = GetMailTemplate('研究者への見積もり送信(M2)');
	}
	if($type=="物品納品"){
		$maildata2 = GetMailTemplate('物品納品(M2)');
	}
	if($type=="データ納品"){
		$maildata2 = GetMailTemplate('データ納品(M2)');
	}
	if($type=="請求"){
		$maildata2 = GetMailTemplate('請求(ADMIN)');
	}

	
	$MailBody2 = $maildata2['BODY'];

	$subject2 = $maildata2['TITLE'];
	if($type=="請求"){
		$MailBody2=str_replace("[D-NAME]","管理者",$MailBody2);
	}
	$MailBody2=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody2);
	$MailBody2=str_replace("[D-TITLE]",$FieldValue[4],$MailBody2);
	$MailBody2=str_replace("[D-COMMENT]",$FieldValue[5],$MailBody2);
	$MailBody2=str_replace("[D-KIGEN]",$FieldValue[7],$MailBody2);

	$mailto = $m2['EMAIL'];
	if($type=="請求"){
		$mailto = SENDER_EMAIL;
	}
	// $mailto = "toretoresansan00@gmail.com";
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	// mb_send_mail($mailto, $subject2, $MailBody2, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">");
}


//=========================================================================================================
//名前 
//機能\ 
//引数 $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート1');
	
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
//引数 $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1_2($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート8');
	
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
//引数 $keyはDAT_FILESTATUSのID
//戻値 
//=========================================================================================================
function SendMail_v1_3($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート9');
	
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

	$mailto = SENDER_EMAIL;

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
echo "<!--SendMail_v:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 

}

//=========================================================================================================
//名前 
//機能\ 
//引数  $keyはDAT_SHODANのID
//戻値 
//=========================================================================================================
function SendMail_v2($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート4');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	//echo "<pre>";
	//var_dump($maildata);
	//echo "</pre>";

	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID='".$key."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemS = mysqli_fetch_assoc($rs);
	//foreach ($itemS as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}
	
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$itemS["MID1_LIST"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID1 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID1 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$itemS["MID2"]."';";
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
//引数  $keyはDAT_SHODANのID
//戻値 
//=========================================================================================================
function SendMail_v2_2($key)
{

	eval(globals());

	$maildata = GetMailTemplate('メールテンプレート5');
	
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	//echo "<pre>";
	//var_dump($maildata);
	//echo "</pre>";

	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID='".$key."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemS = mysqli_fetch_assoc($rs);
	//foreach ($itemS as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}
	
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$itemS["MID1_LIST"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_MID1 = mysqli_fetch_assoc($rs);
	//foreach ($item_MID1 as $idx => $val) {
	//	$MailBody=str_replace("[".$idx."]",$val,$MailBody);
	//}

	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$itemS["MID2"]."';";
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
//引数 
//戻値 
//=========================================================================================================
function DispData($type,$mode,$sort,$word,$word2,$mid_list,$m1_id,$key,$shodan_id,$page,$lid,$m1_list,$mid1_list,$param_div_id,
$sub_type)
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
		//サプライヤーによる見積りの辞退
		case '見積りの辞退':
			$html_prev = 'd';
			break;
		//新規発注キャンセルフロー用ステータス
		case 'サプライヤーキャンセル承認':
			$html_prev = 'cancel_ok1';
			break;
		case 'サプライヤーキャンセル否認':
			$html_prev = 'cancel_no1';
			break;
		case 'キャンセル承認':
			$html_prev = 'cancel_ok2';
			break;
	}
  $htmlnew = './' . $html_prev . '_edit.html';
  $htmledit = './' . $html_prev . '_edit.html';
  $htmlconf = './' . $html_prev . '_conf.html';
  $htmlconf2 = './' . $html_prev . '_save.html';
  $htmlpreview = './' . $html_prev . '_preview.html';
  $htmlend = './' . $html_prev . '_end.html';
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
			case "delete":
				$filename=$htmlend;
				$msg01="削除";
				$msg02="";
				$errmsg="";
				break;
			//リダイレクト用
			case "end":
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

	//見積り送付：TAX RATE
	//国内サプライヤー10%,海外サプライヤー0%。
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$_SESSION['MID']."';";
	$rsM1=mysqli_query(ConnDB(),$StrSQL);
	$itemM1 = mysqli_fetch_assoc($rsM1);
	if($itemM1["M1_DVAL04"]=="M1_DVAL04:Japan"){
		$str=str_replace("[A2_TAX_RATE]","10",$str);
	}else{
		$str=str_replace("[A2_TAX_RATE]","0",$str);
	}

	//見積り送付：SHIP TO
	$m2_ship_to["v1"]["M2_SHIP_TO_SPT_1"]=$itemM1["M1_DTXT25"];
	$m2_ship_to["v1"]["M2_SHIP_TO_SPT_2"]=$itemM1["M1_DTXT28"];
	$m2_ship_to["v1"]["M2_SHIP_TO_SPT_3"]=$itemM1["M1_ETC10"];
	$m2_ship_to["v1"]["M2_SHIP_TO_SPT_4"]=$itemM1["M1_ETC21"];
	$m2_ship_to["v1"]["M2_SHIP_TO_SPT_5"]=$itemM1["M1_ETC24"];
	$m2_ship_to["v1"]["M2_SHIP_TO_SPT_6"]=str_replace("M1_DSEL07:","",$itemM1["M1_DSEL07"]);

	$m2_ship_to["v2"]["M2_SHIP_TO_SPT_1"]=$itemM1["M1_DTXT26"];
	$m2_ship_to["v2"]["M2_SHIP_TO_SPT_2"]=$itemM1["M1_DTXT29"];
	$m2_ship_to["v2"]["M2_SHIP_TO_SPT_3"]=$itemM1["M1_ETC11"];
	$m2_ship_to["v2"]["M2_SHIP_TO_SPT_4"]=$itemM1["M1_ETC22"];
	$m2_ship_to["v2"]["M2_SHIP_TO_SPT_5"]=$itemM1["M1_ETC25"];
	$m2_ship_to["v2"]["M2_SHIP_TO_SPT_6"]=str_replace("M1_DSEL08:","",$itemM1["M1_DSEL08"]);

	$m2_ship_to["v3"]["M2_SHIP_TO_SPT_1"]=$itemM1["M1_DTXT27"];
	$m2_ship_to["v3"]["M2_SHIP_TO_SPT_2"]=$itemM1["M1_DTXT30"];
	$m2_ship_to["v3"]["M2_SHIP_TO_SPT_3"]=$itemM1["M1_ETC12"];
	$m2_ship_to["v3"]["M2_SHIP_TO_SPT_4"]=$itemM1["M1_ETC23"];
	$m2_ship_to["v3"]["M2_SHIP_TO_SPT_5"]=$itemM1["M1_ETC26"];
	$m2_ship_to["v3"]["M2_SHIP_TO_SPT_6"]=str_replace("M1_DSEL09:","",$itemM1["M1_DSEL09"]);

	//echo "<!--SHIP TO ";
	//var_dump($m2_ship_to);
	//echo "-->";

	$str=str_replace("[M2_SHIP_TO]",json_encode($m2_ship_to),$str);


	//見積り送付：BILL TO
	$m2_bill_to["v1"]["M2_BILL_TO_SPT_1"]=$itemM1["M1_ETC31"];
	$m2_bill_to["v1"]["M2_BILL_TO_SPT_2"]=$itemM1["M1_ETC34"];
	$m2_bill_to["v1"]["M2_BILL_TO_SPT_3"]=$itemM1["M1_ETC35"];
	$m2_bill_to["v1"]["M2_BILL_TO_SPT_4"]=$itemM1["M1_ETC36"];
	$m2_bill_to["v1"]["M2_BILL_TO_SPT_5"]=$itemM1["M1_ETC38"];
	$m2_bill_to["v1"]["M2_BILL_TO_SPT_6"]=str_replace("M1_DSEL10:","",$itemM1["M1_DSEL10"]);

	$m2_bill_to["v2"]["M2_BILL_TO_SPT_1"]=$itemM1["M1_ETC106"];
	$m2_bill_to["v2"]["M2_BILL_TO_SPT_2"]=$itemM1["M1_ETC109"];
	$m2_bill_to["v2"]["M2_BILL_TO_SPT_3"]=$itemM1["M1_ETC110"];
	$m2_bill_to["v2"]["M2_BILL_TO_SPT_4"]=$itemM1["M1_ETC111"];
	$m2_bill_to["v2"]["M2_BILL_TO_SPT_5"]=$itemM1["M1_ETC113"];
	$m2_bill_to["v2"]["M2_BILL_TO_SPT_6"]=str_replace("M1_ETC114:","",$itemM1["M1_ETC114"]);

	$m2_bill_to["v3"]["M2_BILL_TO_SPT_1"]=$itemM1["M1_ETC115"];
	$m2_bill_to["v3"]["M2_BILL_TO_SPT_2"]=$itemM1["M1_ETC118"];
	$m2_bill_to["v3"]["M2_BILL_TO_SPT_3"]=$itemM1["M1_ETC119"];
	$m2_bill_to["v3"]["M2_BILL_TO_SPT_4"]=$itemM1["M1_ETC120"];
	$m2_bill_to["v3"]["M2_BILL_TO_SPT_5"]=$itemM1["M1_ETC122"];
	$m2_bill_to["v3"]["M2_BILL_TO_SPT_6"]=str_replace("M1_ETC123:","",$itemM1["M1_ETC123"]);

	echo "<!--BILL TO ";
	var_dump($m2_bill_to);
	echo "-->";

	$str=str_replace("[M2_BILL_TO]",json_encode($m2_bill_to),$str);


	//見積ステータス（仮：テスト用）
	$fparam="直接送付::直接送付(前払い)::手数料追加::手数料追加(前払い)";
	$fname="M_STATUS";
	$tmp=explode("::",$fparam);
	$strtmp="";
	$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
	for ($j=0; $j<count($tmp); $j=$j+1) {
			$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
	}
	$str=str_replace("[OPT-M_STATUS]",$strtmp,$str);
	if($_POST['M_STATUS']!=""){
		$sval=$_POST['M_STATUS'];
		$str=str_replace("'".$sval."'","'".$sval."' selected",$str);
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
	$str=str_replace("[PAGE]",$page,$str);
	$str=str_replace("[KEY]",$key,$str);
	$str=str_replace("[TYPE]",$type,$str);
	//$sub_typeは一括前払いフロー用に最初準備
	echo "<!--sub_type2:$sub_type-->";
	$str=str_replace("[SUB_TYPE]",$sub_type,$str);
	$str=str_replace("[SHODAN_ID]",$shodan_id,$str);
	$str=str_replace("[LID]",$lid,$str);

	$str=str_replace("[MID1_LIST]",$mid1_list,$str);

	// typeごとの違い
	switch($type) {
		case '問い合わせ':
			$str=str_replace("[PAGE_TITLE]",'Inquiry to Supplier',$str);
			break;
		case '見積り依頼':
			$str=str_replace("[PAGE_TITLE]",'Quote Request From Supplier',$str);
			break;
		case '再見積り依頼':
			$str=str_replace("[PAGE_TITLE]",'Request a re-quote from Supplier',$str);
			break;
		case '発注依頼':
			$str=str_replace("[PAGE_TITLE]",'Order request to Supplier',$str);
			break;
		case '案件の取り下げ':
			$str=str_replace("[PAGE_TITLE]",'Cancel of case',$str);
			break;
		case '追加見積り':
		case '見積り送付':
			$str=str_replace("[PAGE_TITLE]",'Send Quotation',$str);
			break;
		case '受注承認':
			$str=str_replace("[PAGE_TITLE]",'Order approval',$str);
			break;
		case 'データ納品':
			$str=str_replace("[PAGE_TITLE]",'Data Delivery',$str);
			break;
		case '納品確認':
			$str=str_replace("[PAGE_TITLE]",'Check delivery',$str);
			break;
		case '物品納品':
			$str=str_replace("[PAGE_TITLE]",'Delivery of goods',$str);
			break;
		case '請求':
			$str=str_replace("[PAGE_TITLE]",'Invoice',$str);
			break;

		case 'キャンセル':
			$str=str_replace("[PAGE_TITLE]",'Cancel',$str);
			break;
		case '辞退':
			$str=str_replace("[PAGE_TITLE]",'Decline',$str);
			break;
		case '見積りの辞退':
			$str=str_replace("[PAGE_TITLE]",'Decline to quote',$str);
			break;
		case 'サプライヤーキャンセル承認':
			$str=str_replace("[PAGE_TITLE]",'Cancel Approval by Supplier',$str);
			break;
		case 'サプライヤーキャンセル否認':
			$str=str_replace("[PAGE_TITLE]",'Cancel Denial by Supplier',$str);
			break;
		case 'キャンセル承認':
			$str=str_replace("[PAGE_TITLE]",'Cancel Approval',$str);
			break;
	}

	// 水際英訳
	$str = showStatusAll($str);

  // ファイルアップロード
	if($mode == 'saveconf') {
		foreach($_FILES as $file_key => $file_val) {
			$filename = $_FILES[$file_key]["name"];
			move_uploaded_file($_FILES[$file_key]["tmp_name"], __dir__."/../a_filestatus/data/".$filename);
		}
	}

	if($mode == 'saveconf' || $mode == 'back') {
echo "<!--1a-->";
	  // フォームからデータ取得
		if($_POST['TITLE'] != '') {
			$str=str_replace("[D-TITLE]",$_POST['TITLE'],$str);
		}
		else {
			$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_shodan = mysqli_fetch_assoc($rs);

			$str=str_replace("[D-TITLE]",$item_shodan['TITLE'],$str);
		}
		$str=str_replace("[D-COMMENT]",str_replace("\n", '<br>', $_POST['COMMENT']),$str); // Dはbr変換
		$str=str_replace("[D-FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$str);
		$str=str_replace("[D-FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$str);
		$str=str_replace("[D-FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$str);
		$str=str_replace("[D-FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$str);
		$str=str_replace("[D-FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$str);
		$str=str_replace("[D-KIGEN]",$_POST['KIGEN'],$str);
		$str=str_replace("[D-NEWDATE]",date('Y/m/d'),$str);

		$str=str_replace("[D-M1_MESSAGE]",str_replace("\n", '<br>', $_POST['M1_MESSAGE']),$str); // Dはbr変換

		if($_POST['M1_TRANS_FLG']=="あり"){
			$eng_m1_trans_flg="Yes";
		}else if($_POST['M1_TRANS_FLG']=="なし"){
			$eng_m1_trans_flg="No";     
		}else{
			$eng_m1_trans_flg="";
		}
		$str=str_replace("[D-M1_TRANS_FLG]",$eng_m1_trans_flg,$str);
		//$str=str_replace("[D-M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$str);
		
		$str=str_replace("[D-M1_TRANS_FLG_あり]",($_POST['M1_TRANS_FLG'] == 'あり' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_FLG_なし]",($_POST['M1_TRANS_FLG'] == 'なし' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$str);
		$str=str_replace("[D-M1_PRICE]",$_POST['M1_PRICE'],$str);
		$str=str_replace("[D-M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$str);
		

		$str=str_replace("[D-M2_TITLE]",$_POST['M2_TITLE'],$str);
		$str=str_replace("[D-M2_PRICE]",$_POST['M2_PRICE'],$str);
		$str=str_replace("[D-M2_COMMENT]",str_replace("\n", '<br>', $_POST['M2_COMMENT']),$str); // Dはbr変換


		//見積り送付（Send Quotation）のCurrency(M1_ETC40)セレクトボックス
		$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$_SESSION['MID']."';";
		$rsM1=mysqli_query(ConnDB(),$StrSQL);
		$itemM1 = mysqli_fetch_assoc($rsM1);
		//echo "<!--sql:".$StrSQL."-->";
		//echo "<!--currency:".$itemM1["M1_ETC40"]."-->";
		$fparam="JPY::USD::EUR::GBP";
		//$fparam="US Dollar::British Pound::Euro::Japanese Yen";
		$fname="M2_CURRENCY";
		$pm="M1";
		$tmp=explode("::",$fparam);
		$strtmp="";
		$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
		for ($j=0; $j<count($tmp); $j=$j+1) {
				$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
		}
		$str=str_replace("[OPT-M2_CURRENCY-M1_ETC40]",$strtmp,$str);


		// 新Quotation
		// -------------------------------------------------------------------------------
		// 水際英訳
		//$str=str_replace("[D-M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$str);
		$str=str_replace("[D-M2_NOHIN_TYPE]",implode(',', showStatusAll($_POST['M2_NOHIN_TYPE'])),$str);
		//$str=str_replace("[D-M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$str);
		$str=str_replace("[D-M2_PAY_TYPE]",showStatusAll($_POST['M2_PAY_TYPE']),$str);

		$str=str_replace("[D-M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$str);
		$str=str_replace("[D-M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$str);
		$str=str_replace("[D-M2_DATE]",$_POST['M2_DATE'],$str);
		$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[D-M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$str);
		$str=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:","",$_POST['M2_CURRENCY']),$str);
		//$str=str_replace("[D-M2_CURRENCY]",$_POST['M2_CURRENCY'],$str);
		$str=str_replace("[D-M_STATUS]",str_replace("M_STATUS:","",$_POST['M_STATUS']),$str);

		$detail_template = '
            <div class="formset__item formset__item-head">
              <div class="formset__ttl2"><strong>Details XXX</strong></div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Item #</div>
              <div class="formset__input">[D-M2_DETAIL_ITEM_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Description</div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Quantity</div>
              <div class="formset__input">[D-M2_DETAIL_QUANTITY_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Unit Price</div>
              <div class="formset__input">[D-M2_DETAIL_UNIT_PRICE_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Special discount</div>
              <div class="formset__input">[D-M2_DETAIL_SP_DISCOUNT_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price</div>
              <div class="formset__input">[D-M2_DETAIL_PRICE_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Special note</div>
              <div class="formset__input">[D-M2_SPECIAL_NOTE_TMP_XXX]</div>
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

		echo "<!--cnt_item:".count($_POST['M2_DETAIL_ITEM'])."-->";
		echo "<!--";
		var_dump($_POST['M2_DETAIL_ITEM']);
		echo "-->";

		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
				$detail_no = $detail_key + 1;
			$str=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_DESCRIPTION'][$detail_key]),$str); // Dはbr変換
			$str=str_replace("[D-M2_DETAIL_QUANTITY_".$detail_no."]",$_POST['M2_DETAIL_QUANTITY'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_UNIT_PRICE_".$detail_no."]",$_POST['M2_DETAIL_UNIT_PRICE'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_SP_DISCOUNT_".$detail_no."]",$_POST['M2_DETAIL_SP_DISCOUNT'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_NOTE'][$detail_key]),$str); // Dはbr変換
			$str=str_replace("[D-M2_SPECIAL_NOTE_TMP_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_SPECIAL_NOTE_TMP'][$detail_no]),$str); // Dはbr変換
		}

		$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $_POST['M2_SPECIAL_NOTE']),$str); // Dはbr変換
		// -------------------------------------------------------------------------------

		$str=str_replace("[D-H_M2_ID]",$_POST['H_M2_ID'],$str);
		
		$str=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $_POST['H_COMMENT']),$str); // Dはbr変換

		$str=str_replace("[D-N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[D-N_FILE2]",(isset($_POST['N_FILE2']) ? $_POST['N_FILE2'] : $_FILES['N_FILE2']['name']),$str);
		$str=str_replace("[D-N_FILE3]",(isset($_POST['N_FILE3']) ? $_POST['N_FILE3'] : $_FILES['N_FILE3']['name']),$str);
		$str=str_replace("[D-N_FILE4]",(isset($_POST['N_FILE4']) ? $_POST['N_FILE4'] : $_FILES['N_FILE4']['name']),$str);
		$str=str_replace("[D-N_FILE5]",(isset($_POST['N_FILE5']) ? $_POST['N_FILE5'] : $_FILES['N_FILE5']['name']),$str);
		$str=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $_POST['N_MESSAGE']),$str); // Dはbr変換

		$str=str_replace("[D-N_PDF]",(isset($_POST['N_PDF']) ? $_POST['N_PDF'] : $_FILES['N_PDF']['name']),$str);
		$str=str_replace("[D-N_SHUKKA]",$_POST['N_SHUKKA'],$str);
		$str=str_replace("[D-N_TEMP1]",$_POST['N_TEMP1'],$str);
		$str=str_replace("[D-N_TEMP2]",$_POST['N_TEMP2'],$str);
		$str=str_replace("[D-N_AWB]",$_POST['N_AWB'],$str);

		$str=str_replace("[D-S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[D-S_MESSAGE]",str_replace("\n", '<br>', $_POST['S_MESSAGE']),$str); // Dはbr変換


		$str=str_replace("[TITLE]",$_POST['TITLE'],$str);
		$str=str_replace("[COMMENT]",$_POST['COMMENT'],$str);
		$str=str_replace("[FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$str);
		$str=str_replace("[FILE2]",(isset($_POST['FILE2']) ? $_POST['FILE2'] : $_FILES['FILE2']['name']),$str);
		$str=str_replace("[FILE3]",(isset($_POST['FILE3']) ? $_POST['FILE3'] : $_FILES['FILE3']['name']),$str);
		$str=str_replace("[FILE4]",(isset($_POST['FILE4']) ? $_POST['FILE4'] : $_FILES['FILE4']['name']),$str);
		$str=str_replace("[FILE5]",(isset($_POST['FILE5']) ? $_POST['FILE5'] : $_FILES['FILE5']['name']),$str);
		$str=str_replace("[KIGEN]",$_POST['KIGEN'],$str);
		$str=str_replace("[NEWDATE]",date('Y/m/d'),$str);

		$str=str_replace("[M1_MESSAGE]",$_POST['M1_MESSAGE'],$str);

		if($_POST['M1_TRANS_FLG']=="あり"){
			$eng_m1_trans_flg="Yes";
		}else if($_POST['M1_TRANS_FLG']=="なし"){
			$eng_m1_trans_flg="No";			
		}else{
			$eng_m1_trans_flg="";
		}
		$str=str_replace("[M1_TRANS_FLG]",$eng_m1_trans_flg,$str);
		//$str=str_replace("[M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$str);
		
		$str=str_replace("[M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$str);
		$str=str_replace("[M1_PRICE]",$_POST['M1_PRICE'],$str);
		$str=str_replace("[M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$str);
		$str=str_replace("[M1_KIGEN]",$_POST['M1_KIGEN'],$str);

		$str=str_replace("[M2_TITLE]",$_POST['M2_TITLE'],$str);
		$str=str_replace("[M2_PRICE]",$_POST['M2_PRICE'],$str);
		$str=str_replace("[M2_COMMENT]",$_POST['M2_COMMENT'],$str);

		// 新Quotation
		// -------------------------------------------------------------------------------
		if(is_array($_POST['M2_NOHIN_TYPE'])) {
			// 水際英訳
			//$str=str_replace("[M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$str);
			$str=str_replace("[M2_NOHIN_TYPE]",implode(',', showStatusAll($_POST['M2_NOHIN_TYPE'])),$str);
		}
		else {
			// 水際英訳
			//$str=str_replace("[M2_NOHIN_TYPE]",$_POST['M2_NOHIN_TYPE'],$str);
			$str=str_replace("[M2_NOHIN_TYPE]",showStatusAll($_POST['M2_NOHIN_TYPE']),$str);
		}
		//$str=str_replace("[M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$str);
		$str=str_replace("[M2_PAY_TYPE]",showStatusAll($_POST['M2_PAY_TYPE']),$str);

		$str=str_replace("[M2_QUOTE_NO]",$_POST['M2_QUOTE_NO'],$str);
		$str=str_replace("[M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$str);
		$str=str_replace("[M2_DATE]",$_POST['M2_DATE'],$str);
		$str=str_replace("[M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$str);
		$str=str_replace("[M2_CURRENCY]",$_POST['M2_CURRENCY'],$str);
		$str=str_replace("[M_STATUS]",$_POST['M_STATUS'],$str);


		$detail_template = '
            <input type="hidden" name="M2_DETAIL_ITEM[]" value="[M2_DETAIL_ITEM_XXX]">
            <input type="hidden" name="M2_DETAIL_DESCRIPTION[]" value="[M2_DETAIL_DESCRIPTION_XXX]">
            <input type="hidden" name="M2_DETAIL_QUANTITY[]" value="[M2_DETAIL_QUANTITY_XXX]">
            <input type="hidden" name="M2_DETAIL_UNIT_PRICE[]" value="[M2_DETAIL_UNIT_PRICE_XXX]">
            <input type="hidden" name="M2_DETAIL_SP_DISCOUNT[]" value="[M2_DETAIL_SP_DISCOUNT_XXX]">
            <input type="hidden" name="M2_DETAIL_PRICE[]" value="[M2_DETAIL_PRICE_XXX]">
            <input type="hidden" name="M2_DETAIL_NOTE[]" value="[M2_DETAIL_NOTE_XXX]">
            <input type="hidden" name="M2_DETAIL_SPLIT_PART[]" value="[M2_DETAIL_SPLIT_PART_XXX]">
            <input type="hidden" name="M2_NOHIN_TYPE_TMP[XXX]" value="[M2_NOHIN_TYPE_TMP_XXX]">
            <input type="hidden" name="M2_SPECIAL_NOTE_TMP[XXX]" value="[M2_SPECIAL_NOTE_TMP_XXX]">
            <input type="hidden" name="MAEBARAI[XXX]" value="[MAEBARAI_XXX]">
            <input type="hidden" name="ItemNo[]" value="[ItemNo_XXX]">
		';
							
		$hidden_detail_area = '';
		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
			$detail_no = $detail_key + 1;
			$hidden_detail_area .= str_replace('XXX', $detail_no, $detail_template);
		}
		$str=str_replace("[HIDDEN_DETAIL_AREA]",$hidden_detail_area,$str);

		// 戻る押下時のためのセット
		// backボタン時はjavascriptで値を入れてる
		$post_detail = array();
		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']); $detail_key++) {
			$post_detail[] = array(
				"M2_DETAIL_ITEM" => $_POST['M2_DETAIL_ITEM'][$detail_key],
				"M2_DETAIL_DESCRIPTION" => $_POST['M2_DETAIL_DESCRIPTION'][$detail_key],
				"M2_DETAIL_QUANTITY" => $_POST['M2_DETAIL_QUANTITY'][$detail_key],
				"M2_DETAIL_UNIT_PRICE" => $_POST['M2_DETAIL_UNIT_PRICE'][$detail_key],
				"M2_DETAIL_SP_DISCOUNT" => $_POST['M2_DETAIL_SP_DISCOUNT'][$detail_key],
				"M2_DETAIL_PRICE" => $_POST['M2_DETAIL_PRICE'][$detail_key],
				"M2_DETAIL_NOTE" => $_POST['M2_DETAIL_NOTE'][$detail_key],
				"M2_DETAIL_SPLIT_PART" => $_POST['M2_DETAIL_SPLIT_PART'][$detail_key],
				"M2_SPECIAL_NOTE_TMP" => $_POST['M2_SPECIAL_NOTE_TMP'][($detail_key+1)],
				"M2_NOHIN_TYPE_TMP" => $_POST['M2_NOHIN_TYPE_TMP'][($detail_key+1)],
				"MAEBARAI" => $_POST['MAEBARAI'][($detail_key+1)]
				//ItemNoは連番にするためにいれない。JSのXXXの置換にまかせる。
			);
		}
		$str=str_replace("[POST_DETAIL]",json_encode($post_detail,JSON_UNESCAPED_UNICODE),$str);

		echo "<!--";
		var_dump($_POST);
		echo "-->";
		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
			$detail_no = $detail_key + 1;
			$str=str_replace("[M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_DESCRIPTION_".$detail_no."]",$_POST['M2_DETAIL_DESCRIPTION'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",$_POST['M2_DETAIL_NOTE'][$detail_key],$str);
			//$str=str_replace("[M2_SPECIAL_NOTE_TMP_".$detail_no."]",$_POST['M2_SPECIAL_NOTE_TMP'][$detail_no],$str);
			echo "<!--M2_PAY_TYPE:".$_POST['M2_PAY_TYPE']."-->";
			echo "<!--detail_no:$detail_no-->";
			if($_POST["M2_PAY_TYPE"]=="Milestone"){
				$spart="Part".$detail_no;
				$str=str_replace("[M2_DETAIL_SPLIT_PART_".$detail_no."]",$spart,$str);
			}else if($_POST["M2_PAY_TYPE"]=="Once"){
				$spart="Part1";
				$str=str_replace("[M2_DETAIL_SPLIT_PART_".$detail_no."]",$spart,$str);
			}else{
				$str=str_replace("[M2_DETAIL_SPLIT_PART_".$detail_no."]",$_POST['M2_DETAIL_SPLIT_PART'][$detail_key],$str);
			}

			//M2_NOHIN_TYPE_TMPはラジオなので特別処理
			//$str=str_replace("[M2_NOHIN_TYPE_TMP_".$detail_no."]",$_POST['M2_NOHIN_TYPE_TMP'][$detail_no],$str);
			$str=str_replace("[M2_DETAIL_QUANTITY_".$detail_no."]",$_POST['M2_DETAIL_QUANTITY'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_UNIT_PRICE_".$detail_no."]",$_POST['M2_DETAIL_UNIT_PRICE'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_SP_DISCOUNT_".$detail_no."]",$_POST['M2_DETAIL_SP_DISCOUNT'][$detail_key],$str);
			//$str=str_replace("[MAEBARAI_".$detail_no."]",$_POST['MAEBARAI'][$detail_no],$str);

			//フロントでアイテムナンバーが連番にならない場合が存在した場合の対応
			//アイテムに割り振られた番号がおくられてくるように
			$str=str_replace("[ItemNo_".$detail_no."]",$_POST['ItemNo'][$detail_key],$str);
		}


		//フロントでアイテムナンバーが連番にならない場合が存在した場合の対応
		if($mode=="saveconf"){
			foreach ($_POST["ItemNo"] as $idx => $val) {
				$detail_no=$idx+1;
				if( !is_null($_POST['M2_SPECIAL_NOTE_TMP'][$val]) && $_POST['M2_SPECIAL_NOTE_TMP'][$val]!="" ){
					$str=str_replace("[M2_SPECIAL_NOTE_TMP_".$detail_no."]",$_POST['M2_SPECIAL_NOTE_TMP'][$val],$str);
				}else{
					$str=str_replace("[M2_SPECIAL_NOTE_TMP_".$detail_no."]","",$str);
				}

				if( !is_null($_POST['M2_NOHIN_TYPE_TMP'][$val]) && $_POST['M2_NOHIN_TYPE_TMP'][$val]!="" ){
					$str=str_replace("[M2_NOHIN_TYPE_TMP_".$detail_no."]",$_POST['M2_NOHIN_TYPE_TMP'][$val],$str);
				}else{
					$str=str_replace("[M2_NOHIN_TYPE_TMP_".$detail_no."]","",$str);
				}

				if( !is_null($_POST['MAEBARAI'][$val]) && $_POST['MAEBARAI'][$val]!="" ){
					$str=str_replace("[MAEBARAI_".$detail_no."]",$_POST['MAEBARAI'][$val],$str);
				}else{
					$str=str_replace("[MAEBARAI_".$detail_no."]","",$str);
				}
			}
		}
		


		$str=str_replace("[M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[M2_SPECIAL_NOTE]",$_POST['M2_SPECIAL_NOTE'],$str);
		// -------------------------------------------------------------------------------

		$str=str_replace("[H_M2_ID]",$_POST['H_M2_ID'],$str);
		$str=str_replace("[H_COMMENT]",$_POST['H_COMMENT'],$str);

		$str=str_replace("[N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[N_FILE2]",(isset($_POST['N_FILE2']) ? $_POST['N_FILE2'] : $_FILES['N_FILE2']['name']),$str);
		$str=str_replace("[N_FILE3]",(isset($_POST['N_FILE3']) ? $_POST['N_FILE3'] : $_FILES['N_FILE3']['name']),$str);
		$str=str_replace("[N_FILE4]",(isset($_POST['N_FILE4']) ? $_POST['N_FILE4'] : $_FILES['N_FILE4']['name']),$str);
		$str=str_replace("[N_FILE5]",(isset($_POST['N_FILE5']) ? $_POST['N_FILE5'] : $_FILES['N_FILE5']['name']),$str);
		$str=str_replace("[N_MESSAGE]",$_POST['N_MESSAGE'],$str);

		$str=str_replace("[N_PDF]",(isset($_POST['N_PDF']) ? $_POST['N_PDF'] : $_FILES['N_PDF']['name']),$str);
		$str=str_replace("[N_SHUKKA]",$_POST['N_SHUKKA'],$str);
		$str=str_replace("[N_TEMP1]",$_POST['N_TEMP1'],$str);
		$str=str_replace("[N_TEMP2]",$_POST['N_TEMP2'],$str);
		$str=str_replace("[N_AWB]",$_POST['N_AWB'],$str);

		$str=str_replace("[S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[S_MESSAGE]",$_POST['S_MESSAGE'],$str);

		//SHIP TO
		$str=str_replace("[M2_SHIP_TO_SPT_1]",$_POST['M2_SHIP_TO_SPT_1'],$str);
		$str=str_replace("[M2_SHIP_TO_SPT_2]",$_POST['M2_SHIP_TO_SPT_2'],$str);
		$str=str_replace("[M2_SHIP_TO_SPT_3]",$_POST['M2_SHIP_TO_SPT_3'],$str);
		$str=str_replace("[M2_SHIP_TO_SPT_4]",$_POST['M2_SHIP_TO_SPT_4'],$str);
		$str=str_replace("[M2_SHIP_TO_SPT_5]",$_POST['M2_SHIP_TO_SPT_5'],$str);
		$str=str_replace("[M2_SHIP_TO_SPT_6]",$_POST['M2_SHIP_TO_SPT_6'],$str);

		$str=str_replace("[D-M2_SHIP_TO_SPT_1]",$_POST['M2_SHIP_TO_SPT_1'],$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_2]",$_POST['M2_SHIP_TO_SPT_2'],$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_3]",$_POST['M2_SHIP_TO_SPT_3'],$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_4]",$_POST['M2_SHIP_TO_SPT_4'],$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_5]",$_POST['M2_SHIP_TO_SPT_5'],$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_6]",$_POST['M2_SHIP_TO_SPT_6'],$str);

		//BILL TO
		$str=str_replace("[M2_BILL_TO_SPT_1]",$_POST['M2_BILL_TO_SPT_1'],$str);
		$str=str_replace("[M2_BILL_TO_SPT_2]",$_POST['M2_BILL_TO_SPT_2'],$str);
		$str=str_replace("[M2_BILL_TO_SPT_3]",$_POST['M2_BILL_TO_SPT_3'],$str);
		$str=str_replace("[M2_BILL_TO_SPT_4]",$_POST['M2_BILL_TO_SPT_4'],$str);
		$str=str_replace("[M2_BILL_TO_SPT_5]",$_POST['M2_BILL_TO_SPT_5'],$str);
		$str=str_replace("[M2_BILL_TO_SPT_6]",$_POST['M2_BILL_TO_SPT_6'],$str);

		$str=str_replace("[D-M2_BILL_TO_SPT_1]",$_POST['M2_BILL_TO_SPT_1'],$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_2]",$_POST['M2_BILL_TO_SPT_2'],$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_3]",$_POST['M2_BILL_TO_SPT_3'],$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_4]",$_POST['M2_BILL_TO_SPT_4'],$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_5]",$_POST['M2_BILL_TO_SPT_5'],$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_6]",$_POST['M2_BILL_TO_SPT_6'],$str);


		if($type == '発注依頼' || $type == '見積り送付' || $type=="追加見積り") {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			while($item_filestatus = mysqli_fetch_assoc($rs)) {
				$selected = '';
				if($item_filestatus['ID'] == $_POST['H_M2_ID']) {
					$selected = ' selected ';
				}
				$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" ' . $selected . '>' . $item_filestatus['ID'] . '</option>';

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
	}
	else if($key) {
echo "<!--2a-->";
	  // DBからデータ取得
	  //echo "<!--DBからデータ取得-->";
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
		//echo('<!--1:'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_filestatus = mysqli_fetch_assoc($rs);
		echo "<!--2a:item_filestatus:\n";
		var_dump($item_filestatus);
		echo "-->";

		$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$item_filestatus['SHODAN_ID'].";";
		//echo('<!--2:'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		$word = $item_shodan['CATEGORY'];
		$word2 = $item_shodan['KEYWORD'];

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
			"SCNo_yy" => $item_filestatus['SCNo_yy'], 
			"SCNo_mm" => $item_filestatus['SCNo_mm'], 
			"SCNo_dd" => $item_filestatus['SCNo_dd'], 
			"SCNo_cnt" => $item_filestatus['SCNo_cnt'], 
			"SCNo_else1" => $item_filestatus['SCNo_else1'], 
			"SCNo_else2" => $item_filestatus['SCNo_else2'], 
		);
		$SCNo_str=formatAlphabetId($SCNo_ary);
		$str=str_replace("[MITSU_SCNO]",$SCNo_str,$str);


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
			$str=str_replace("[D-H_M2_ID]",$SCNo_str,$str);
			$str=str_replace("[D-M1_M2_ID]",$SCNo_str,$str);
		}

		//主に見積り書の別ウィンドウ表示用(プレビュー表示用)
		if($mode=="disp_frame"){
			$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$item_filestatus["MID1"]."' ";
			$pdf_m1_rs=mysqli_query(ConnDB(),$StrSQL);
			$pdf_m1_item=mysqli_fetch_assoc($pdf_m1_rs);

			foreach ($pdf_m1_item as $idx => $val) {
				$str=str_replace("[D-M1-".$idx."]", $val, $str);
			}

			//見積り送付時に選択されたbill to
			$pdf_address2="";
			$pdf_address2.=$item_filestatus["M2_BILL_TO_SPT_2"];
			$pdf_address2.=!empty($item_filestatus["M2_BILL_TO_SPT_3"]) ? ", ".$item_filestatus["M2_BILL_TO_SPT_3"] : "";
			$pdf_address2.=!empty($item_filestatus["M2_BILL_TO_SPT_4"]) ? ", ".$item_filestatus["M2_BILL_TO_SPT_4"] : "";
			$pdf_address2.=!empty($item_filestatus["M2_BILL_TO_SPT_6"]) ? ", ".$item_filestatus["M2_BILL_TO_SPT_6"] : "";
			$str=str_replace("[PDF_ADDRESS2]",$pdf_address2,$str);

			//仕様変更後のSpecial Discount
			$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$key."' order by NEWDATE";
			$fsd_rs=mysqli_query(ConnDB(),$StrSQL);
			$sum_m2_detail_sp_discount=0;
			while( $fsd_item=mysqli_fetch_assoc($fsd_rs) ){
				if(is_numeric($fsd_item["M2_DETAIL_SP_DISCOUNT"])){
					$m2_detail_sp_discount=$fsd_item["M2_DETAIL_SP_DISCOUNT"];
				}else{
					$m2_detail_sp_discount=0;
				}

				$sum_m2_detail_sp_discount=$sum_m2_detail_sp_discount+$m2_detail_sp_discount;
			}
			$str=str_replace("[SUM_M2_DETAIL_SP_DISCOUNT]", $sum_m2_detail_sp_discount, $str);
		}


		$str=str_replace("[SHODAN_ID_FOR_M2]",$item_shodan['ID'],$str);

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

		if($item_filestatus['M1_TRANS_FLG']=="あり"){
			$eng_m1_trans_flg="Yes";
		}else if($item_filestatus['M1_TRANS_FLG']=="なし"){
			$eng_m1_trans_flg="No";     
		}else{
			$eng_m1_trans_flg="";
		}
		$str=str_replace("[D-M1_TRANS_FLG]",$eng_m1_trans_flg,$str);
		//$str=str_replace("[D-M1_TRANS_FLG]",$item_filestatus['M1_TRANS_FLG'],$str);
		
		$str=str_replace("[D-M1_TRANS_FLG_あり]",($item_filestatus['M1_TRANS_FLG'] == 'あり' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_FLG_なし]",($item_filestatus['M1_TRANS_FLG'] == 'なし' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_TXT]",$item_filestatus['M1_TRANS_TXT'],$str);
		$str=str_replace("[D-M1_PRICE]",$item_filestatus['M1_PRICE'],$str);
		$str=str_replace("[D-M1_FILE]",$item_filestatus['M1_FILE'],$str);
		$str=str_replace("[D-M1_KIGEN]",$item_filestatus['M1_KIGEN'],$str);
		$str=str_replace("[D-SAMPLE]",$item_filestatus['SAMPLE'],$str);
		$str=str_replace("[D-ORIGIN]",$item_filestatus['ORIGIN'],$str);
		$str=str_replace("[D-UNIT]",str_replace("UNIT:","",$item_filestatus['UNIT']),$str);
		$str=str_replace("[D-TEMPR]",str_replace("TEMPR:","",$item_filestatus['TEMPR']),$str);
		$str=str_replace("[D-LEGAL]",$item_filestatus['LEGAL'],$str);


		//見積り送付（Send Quotation）のCurrency(M1_ETC40)セレクトボックス
		$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$_SESSION['MID']."';";
		$rsM1=mysqli_query(ConnDB(),$StrSQL);
		$itemM1 = mysqli_fetch_assoc($rsM1);
		//echo "<!--sql:".$StrSQL."-->";
		//echo "<!--currency:".$itemM1["M1_ETC40"]."-->";
		$fparam="JPY::USD::EUR::GBP";
		//$fparam="US Dollar::British Pound::Euro::Japanese Yen";
		$fname="M2_CURRENCY";
		$pm="M1";
		$tmp=explode("::",$fparam);
		$strtmp="";
		$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
		for ($j=0; $j<count($tmp); $j=$j+1) {
				$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
		}
		$str=str_replace("[OPT-M2_CURRENCY-M1_ETC40]",$strtmp,$str);


		$StrSQL="SELECT M2_DVAL03, M2_ETC20 FROM DAT_M2 WHERE MID='".$item_filestatus['MID2']."' limit 1";
		$rsM2=mysqli_query(ConnDB(),$StrSQL);
		$itemM2=mysqli_fetch_assoc($rsM2);
		if(trim($itemM2["M2_ETC20"])!=""){
			$str=str_replace("[D-RESEARCHER]",$itemM2["M2_ETC20"],$str);
		}else{
			$str=str_replace("[D-RESEARCHER]",$itemM2["M2_DVAL03"],$str);
		}

		// 新Quotation
		// -------------------------------------------------------------------------------
		// 水際英訳
		//$str=str_replace("[D-M2_NOHIN_TYPE]",$item_filestatus['M2_NOHIN_TYPE'],$str);
		$str=str_replace("[D-M2_NOHIN_TYPE]",showStatusAll($item_filestatus['M2_NOHIN_TYPE']),$str);
		//$str=str_replace("[D-M2_PAY_TYPE]",$item_filestatus['M2_PAY_TYPE'],$str);
		$str=str_replace("[D-M2_PAY_TYPE]",showStatusAll($item_filestatus['M2_PAY_TYPE']),$str);
		$str=str_replace("[D-M2_QUOTE_NO]",$item_filestatus['M2_QUOTE_NO'],$str);
		$str=str_replace("[D-M2_STUDY_CODE]",$item_filestatus['M2_STUDY_CODE'],$str);
		$str=str_replace("[D-M2_DATE]",$item_filestatus['M2_DATE'],$str);
		$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$item_filestatus['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[D-M2_DESCRIPTION]",$item_filestatus['M2_DESCRIPTION'],$str);
		$str=str_replace("[D-M2_CURRENCY]",str_replace("M2_CURRENCY:","",$item_filestatus['M2_CURRENCY']),$str);
		//$str=str_replace("[D-M2_CURRENCY]",$item_filestatus['M2_CURRENCY'],$str);
		$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$item_filestatus['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE]",$item_filestatus['M2_SPECIAL_NOTE'],$str);

		// 水際英訳
		//$str=str_replace("[M2_NOHIN_TYPE]",$item_filestatus['M2_NOHIN_TYPE'],$str);
		$str=str_replace("[M2_NOHIN_TYPE]",showStatusAll($item_filestatus['M2_NOHIN_TYPE']),$str);
		//$str=str_replace("[M2_PAY_TYPE]",$item_filestatus['M2_PAY_TYPE'],$str);
		$str=str_replace("[M2_PAY_TYPE]",showStatusAll($item_filestatus['M2_PAY_TYPE']),$str);
		$str=str_replace("[M2_QUOTE_NO]",$item_filestatus['M2_QUOTE_NO'],$str);
		$str=str_replace("[M2_STUDY_CODE]",$item_filestatus['M2_STUDY_CODE'],$str);
		$str=str_replace("[M2_DATE]",$item_filestatus['M2_DATE'],$str);
		$str=str_replace("[M2_QUOTE_VALID_UNTIL]",$item_filestatus['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[M2_DESCRIPTION]",$item_filestatus['M2_DESCRIPTION'],$str);
		$str=str_replace("[M2_CURRENCY]",$item_filestatus['M2_CURRENCY'],$str);
		$str=str_replace("[M2_SPECIAL_DISCOUNT]",$item_filestatus['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[M2_SPECIAL_NOTE]",$item_filestatus['M2_SPECIAL_NOTE'],$str);

		$detail_template = '
            <div class="formset__item formset__item-head">
              <div class="formset__ttl2"><strong>Details XXX</strong></div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Item #</div>
              <div class="formset__input">[D-M2_DETAIL_ITEM_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Description</div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price</div>
              <div class="formset__input">[D-M2_DETAIL_PRICE_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Special Note</div>
              <div class="formset__input">[D-M2_SPECIAL_NOTE_TMP_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Note</div>
              <div class="formset__input">[D-M2_DETAIL_NOTE_XXX]</div>
            </div>
		';
		// デザインにあわせたもの
		$detail_template2 = '
              <tr>
                <td>[D-M2_DETAIL_ITEM_XXX]</td>
                <td>[D-M2_DETAIL_DESCRIPTION_XXX]</td>
                <td>[D-M2_DETAIL_NOTE_XXX]</td>
                <td>[D-M2_SPECIAL_NOTE_TMP_XXX]</td>
                <td style="text-align:right" class="detail_price">[D-M2_DETAIL_PRICE_XXX]</td>
                <td>[D-M2_DETAIL_SP_DISCOUNT_XXX]</td>
              </tr>
		';
		$add_detail_area = '';
		$add_detail_area2 = '';
		$detail_key = 0;

		$post_detail = array();

		$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID=".$key.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		while($item_filestatus_detail = mysqli_fetch_assoc($rs)) {
			$detail_no = $detail_key + 1;
			$add_detail_area .= str_replace('XXX', $detail_no, $detail_template);
			$add_detail_area2 .= str_replace('XXX', $detail_no, $detail_template2);

			$add_detail_area=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area); // Dはbr変換
			$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area);
			$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換
			$add_detail_area=str_replace("[D-M2_SPECIAL_NOTE_TMP_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_SPECIAL_NOTE_TMP']),$add_detail_area); // Dはbr変換

			$add_detail_area2=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area2);
			$add_detail_area2=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area2); // Dはbr変換
			$add_detail_area2=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area2);
			$add_detail_area2=str_replace("[D-M2_DETAIL_SP_DISCOUNT_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_SP_DISCOUNT'],$add_detail_area2);
			$add_detail_area2=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area2); // Dはbr変換
			$add_detail_area2=str_replace("[D-M2_SPECIAL_NOTE_TMP_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_SPECIAL_NOTE_TMP']),$add_detail_area2); // Dはbr変換

			$post_detail[] = array(
				"M2_DETAIL_ITEM" => $item_filestatus_detail['M2_DETAIL_ITEM'],
				"M2_DETAIL_DESCRIPTION" => $item_filestatus_detail['M2_DETAIL_DESCRIPTION'],
				"M2_DETAIL_PRICE" => $item_filestatus_detail['M2_DETAIL_PRICE'],
				"M2_DETAIL_NOTE" => $item_filestatus_detail['M2_DETAIL_NOTE'],
				"M2_SPECIAL_NOTE_TMP" => $item_filestatus_detail['M2_SPECIAL_NOTE_TMP']
			);

			$detail_key++;
		}
		$str=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$str);
		$str=str_replace("[ADD_DETAIL_AREA2]",$add_detail_area2,$str);

		$str=str_replace("[POST_DETAIL]",json_encode($post_detail,JSON_UNESCAPED_UNICODE),$str);

		// -------------------------------------------------------------------------------

		//「Revise Quotation（見積り修正）」
		//見積り修正時はkeyが設定されてくるので、そのキーのデータ１つだけ表示させる。
		if($type == '見積り送付' && $_GET['upd_mode']==1) {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and (STATUS='見積り送付' or STATUS='運営手数料追加') order by ID desc;";
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_shodan['ID']." and STATUS='見積り送付' order by ID desc;";
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_shodan['ID']." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";

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

				//見積り送付の「Revise Quotation」際は、Part0以外はプルダウンに表示しない。
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
				$h_m2_list.='<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >';
				$h_m2_list.=$SCNo_str. ' Version' . $item_filestatus2['M2_VERSION'] . '</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $item_filestatus2['M2_ID'] . '（Version' . $item_filestatus2['M2_VERSION'] . '）</option>';

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

			$str=str_replace("[M2_SPECIAL_NOTE]",$item_filestatus['M2_SPECIAL_NOTE'],$str);
			$str=str_replace("[M2_SHIP_TO_SPT_1]",$item_filestatus['M2_SHIP_TO_SPT_1'],$str);
			$str=str_replace("[M2_SHIP_TO_SPT_2]",$item_filestatus['M2_SHIP_TO_SPT_2'],$str);
			$str=str_replace("[M2_SHIP_TO_SPT_3]",$item_filestatus['M2_SHIP_TO_SPT_3'],$str);
			$str=str_replace("[M2_SHIP_TO_SPT_4]",$item_filestatus['M2_SHIP_TO_SPT_4'],$str);
			$str=str_replace("[M2_SHIP_TO_SPT_5]",$item_filestatus['M2_SHIP_TO_SPT_5'],$str);
			$str=str_replace("[M2_SHIP_TO_SPT_6]",$item_filestatus['M2_SHIP_TO_SPT_6'],$str);
			$str=str_replace("[M2_BILL_TO_SPT_1]",$item_filestatus['M2_BILL_TO_SPT_1'],$str);
			$str=str_replace("[M2_BILL_TO_SPT_2]",$item_filestatus['M2_BILL_TO_SPT_2'],$str);
			$str=str_replace("[M2_BILL_TO_SPT_3]",$item_filestatus['M2_BILL_TO_SPT_3'],$str);
			$str=str_replace("[M2_BILL_TO_SPT_4]",$item_filestatus['M2_BILL_TO_SPT_4'],$str);
			$str=str_replace("[M2_BILL_TO_SPT_5]",$item_filestatus['M2_BILL_TO_SPT_5'],$str);
			$str=str_replace("[M2_BILL_TO_SPT_6]",$item_filestatus['M2_BILL_TO_SPT_6'],$str);
			$str=str_replace("[D-M2_SPECIAL_NOTE]",$item_filestatus['M2_SPECIAL_NOTE'],$str);
			$str=str_replace("[D-M2_SHIP_TO_SPT_1]",$item_filestatus['M2_SHIP_TO_SPT_1'],$str);
			$str=str_replace("[D-M2_SHIP_TO_SPT_2]",$item_filestatus['M2_SHIP_TO_SPT_2'],$str);
			$str=str_replace("[D-M2_SHIP_TO_SPT_3]",$item_filestatus['M2_SHIP_TO_SPT_3'],$str);
			$str=str_replace("[D-M2_SHIP_TO_SPT_4]",$item_filestatus['M2_SHIP_TO_SPT_4'],$str);
			$str=str_replace("[D-M2_SHIP_TO_SPT_5]",$item_filestatus['M2_SHIP_TO_SPT_5'],$str);
			$str=str_replace("[D-M2_SHIP_TO_SPT_6]",$item_filestatus['M2_SHIP_TO_SPT_6'],$str);
			$str=str_replace("[D-M2_BILL_TO_SPT_1]",$item_filestatus['M2_BILL_TO_SPT_1'],$str);
			$str=str_replace("[D-M2_BILL_TO_SPT_2]",$item_filestatus['M2_BILL_TO_SPT_2'],$str);
			$str=str_replace("[D-M2_BILL_TO_SPT_3]",$item_filestatus['M2_BILL_TO_SPT_3'],$str);
			$str=str_replace("[D-M2_BILL_TO_SPT_4]",$item_filestatus['M2_BILL_TO_SPT_4'],$str);
			$str=str_replace("[D-M2_BILL_TO_SPT_5]",$item_filestatus['M2_BILL_TO_SPT_5'],$str);
			$str=str_replace("[D-M2_BILL_TO_SPT_6]",$item_filestatus['M2_BILL_TO_SPT_6'],$str);

		
		}else if($type == '見積り送付') {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_shodan['ID']." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_shodan['ID']." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";

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

				//見積り送付の「Revise Quotation」際は、Part0以外はプルダウンに表示しない。
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
				$h_m2_list.='<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >';
				$h_m2_list.=$SCNo_str. ' Version' . $item_filestatus2['M2_VERSION'] . '</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $item_filestatus2['M2_ID'] . '（Version' . $item_filestatus2['M2_VERSION'] . '）</option>';

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

		if($type=="請求"){
			if($item_filestatus["S_STATUS"]=="請求（研究者）"){
				$str=DispParam($str,"SEIKYU_R");
				$str=DispParamNone($str,"SEIKYU_S");
			}else{
				$str=DispParamNone($str,"SEIKYU_R");
				$str=DispParam($str,"SEIKYU_S");
			}
		}


		$str=str_replace("[D-H_M2_ID]",$item_filestatus['H_M2_ID'],$str);
		$str=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $item_filestatus['H_COMMENT']),$str); // Dはbr変換
		
		$str=str_replace("[D-N_FILE]",(isset($item_filestatus['N_FILE']) ? $item_filestatus['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[D-N_FILE2]",(isset($item_filestatus['N_FILE2']) ? $item_filestatus['N_FILE2'] : $_FILES['N_FILE2']['name']),$str);
		$str=str_replace("[D-N_FILE3]",(isset($item_filestatus['N_FILE3']) ? $item_filestatus['N_FILE3'] : $_FILES['N_FILE3']['name']),$str);
		$str=str_replace("[D-N_FILE4]",(isset($item_filestatus['N_FILE4']) ? $item_filestatus['N_FILE4'] : $_FILES['N_FILE4']['name']),$str);
		$str=str_replace("[D-N_FILE5]",(isset($item_filestatus['N_FILE5']) ? $item_filestatus['N_FILE5'] : $_FILES['N_FILE5']['name']),$str);
		$str=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['N_MESSAGE']),$str); // Dはbr変換

		$str=str_replace("[D-N_PDF]",(isset($item_filestatus['N_PDF']) ? $item_filestatus['N_PDF'] : $_FILES['N_PDF']['name']),$str);
		$str=str_replace("[D-N_SHUKKA]",$item_filestatus['N_SHUKKA'],$str);
		$str=str_replace("[D-N_TEMP1]",$item_filestatus['N_TEMP1'],$str);
		$str=str_replace("[D-N_TEMP2]",$item_filestatus['N_TEMP2'],$str);
		$str=str_replace("[D-N_AWB]",$item_filestatus['N_AWB'],$str);

		$str=str_replace("[D-S_FILE]",(isset($item_filestatus['S_FILE']) ? $item_filestatus['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[D-S_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['S_MESSAGE']),$str); // Dはbr変換

		$str=str_replace("[D-S2_FILE]",(isset($item_filestatus['S2_FILE']) ? $item_filestatus['S2_FILE'] : $_FILES['S2_FILE']['name']),$str);
		$str=str_replace("[D-S2_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['S2_MESSAGE']),$str); // Dはbr変換
		
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

		if($item_filestatus['M1_TRANS_FLG']=="あり"){
			$eng_m1_trans_flg="Yes";
		}else if($item_filestatus['M1_TRANS_FLG']=="なし"){
			$eng_m1_trans_flg="No";     
		}else{
			$eng_m1_trans_flg="";
		}
		$str=str_replace("[M1_TRANS_FLG]",$eng_m1_trans_flg,$str);
		//$str=str_replace("[M1_TRANS_FLG]",$item_filestatus['M1_TRANS_FLG'],$str);
		
		$str=str_replace("[M1_TRANS_TXT]",$item_filestatus['M1_TRANS_TXT'],$str);
		$str=str_replace("[M1_PRICE]",$item_filestatus['M1_PRICE'],$str);
		$str=str_replace("[M1_FILE]",$item_filestatus['M1_FILE'],$str);
		$str=str_replace("[M1_KIGEN]",$item_filestatus['M1_KIGEN'],$str);

		$str=str_replace("[H_COMMENT]",$item_filestatus['H_COMMENT'],$str);

		$str=str_replace("[N_FILE]",(isset($item_filestatus['N_FILE']) ? $item_filestatus['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[N_FILE2]",(isset($item_filestatus['N_FILE2']) ? $item_filestatus['N_FILE2'] : $_FILES['N_FILE2']['name']),$str);
		$str=str_replace("[N_FILE3]",(isset($item_filestatus['N_FILE3']) ? $item_filestatus['N_FILE3'] : $_FILES['N_FILE3']['name']),$str);
		$str=str_replace("[N_FILE4]",(isset($item_filestatus['N_FILE4']) ? $item_filestatus['N_FILE4'] : $_FILES['N_FILE4']['name']),$str);
		$str=str_replace("[N_FILE5]",(isset($item_filestatus['N_FILE5']) ? $item_filestatus['N_FILE5'] : $_FILES['N_FILE5']['name']),$str);
		$str=str_replace("[N_MESSAGE]",$item_filestatus['N_MESSAGE'],$str);

		$str=str_replace("[N_PDF]",(isset($item_filestatus['N_PDF']) ? $item_filestatus['N_PDF'] : $_FILES['N_PDF']['name']),$str);
		$str=str_replace("[N_SHUKKA]",$item_filestatus['N_SHUKKA'],$str);
		$str=str_replace("[N_TEMP1]",$item_filestatus['N_TEMP1'],$str);
		$str=str_replace("[N_TEMP2]",$item_filestatus['N_TEMP2'],$str);
		$str=str_replace("[N_AWB]",$item_filestatus['N_AWB'],$str);

		$str=str_replace("[S_FILE]",(isset($item_filestatus['S_FILE']) ? $item_filestatus['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[S_MESSAGE]",$item_filestatus['S_MESSAGE'],$str);

		$str=str_replace("[S2_FILE]",(isset($item_filestatus['S2_FILE']) ? $item_filestatus['S2_FILE'] : $_FILES['S2_FILE']['name']),$str);
		$str=str_replace("[S2_MESSAGE]",$item_filestatus['S2_MESSAGE'],$str);

		$str=str_replace("[M2_ID]",$item_filestatus['M2_ID'],$str);
		$str=str_replace("[M2_VERSION]",$item_filestatus['M2_VERSION'],$str);



		//サービス費用などのエリア表示
		if($type == '見積り送付'){
			$sf_tpl=file_get_contents("../common/template/service_fee_typeA.html");
			$sf_tpl=makeServiceArea($item_filestatus['ID'],$sf_tpl);

		}
		if($type == '発注依頼'){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['H_M2_ID']." and STATUS='見積り送付' or STATUS='追加見積り' order by ID desc;";
			$h_rs1=mysqli_query(ConnDB(),$StrSQL);
			$h_item1 = mysqli_fetch_assoc($h_rs1);
			$sf_tpl=file_get_contents("../common/template/service_fee_typeB.html");
			$sf_tpl=makeServiceArea($h_item1["ID"],$sf_tpl);
		}
		$str=str_replace("[SERVICE_FEE_AREA]",$sf_tpl,$str);
		if(strpos( $_SESSION["MID"], "M1" ) === false){
			$str=DispParam($str, "SERVICE_FEE_AREA");
		}else{
			$str=DispParamNone($str, "SERVICE_FEE_AREA");
		}


		$h_m2_detail = array();
		if($type == '発注依頼' || $type == '再見積り依頼' || $type=="追加見積り") {
			//echo('<!-- disp -->');
			if($type == '発注依頼') {
				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['H_M2_ID']." and STATUS='見積り送付' order by ID desc;";
				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['H_M2_ID']." and STATUS='見積り送付' or STATUS='追加見積り' order by ID desc;";
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$item_filestatus['SHODAN_ID']." and MID1='".$item_filestatus["MID1"]."' and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";

			}
			else {
				//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['M1_M2_ID']." and STATUS='見積り送付' order by ID desc;";
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['M1_M2_ID']." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			}
			echo('<!--2a:hatyu:'.$StrSQL.'-->');
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
				$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $SCNo_str . ' Version'.$item_filestatus2['M2_VERSION'] . '</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus2['ID'] . '" ' . $selected . ' >' . $item_filestatus2['M2_ID'] . '（Version' . $item_filestatus2['M2_VERSION'] . '）</option>';

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

			//2回払いの発注依頼はPart0を表示。
			$m2_pay_type=check_M2_PAY_TYPE($item_filestatus["SHODAN_ID"], $item_filestatus["MID1"]);
			echo "<!--m2_pay_type:$m2_pay_type-->";
			//念のためモードも指定
			if($type == '発注依頼' && $m2_pay_type=="Split" && $mode=="disp_frame"){
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
				echo "<!--一覧用：$StrSQL-->";
				$hatyu_part0_rs=mysqli_query(ConnDB(),$StrSQL);
				$hatyu_part0_item =  mysqli_fetch_assoc($hatyu_part0_rs);
				echo "<!--";
				var_dump($hatyu_part0_item);
				echo "-->";
				$str=str_replace("[H_M2_ID]",$hatyu_part0_item["ID"],$str);
			}
		}

		$str=str_replace("[H_M2_ID]",$item_filestatus['H_M2_ID'],$str);
	}
	else if($shodan_id) {
echo "<!--3a-->";
		$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
		//echo('<!--3:'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		if($type=="見積りの辞退"){
			$str=str_replace("[D-COMMENT]","",$str);
		}
		$str=str_replace("[D-TITLE]",$item_shodan['TITLE'],$str);
		$str=str_replace("[D-COMMENT]",$item_shodan['COMMENT'],$str);
		$str=str_replace("[D-FILE]",$item_shodan['FILE'],$str);
		$str=str_replace("[D-KIGEN]",$item_shodan['ANSWERDATE'],$str);


		//見積り送付（Send Quotation）のCurrency(M1_ETC40)セレクトボックス
		$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$_SESSION['MID']."';";
		$rsM1=mysqli_query(ConnDB(),$StrSQL);
		$itemM1 = mysqli_fetch_assoc($rsM1);
		//echo "<!--sql:".$StrSQL."-->";
		//echo "<!--currency:".$itemM1["M1_ETC40"]."-->";
		$fparam="JPY::USD::EUR::GBP";
		//$fparam="US Dollar::British Pound::Euro::Japanese Yen";
		$fname="M2_CURRENCY";
		$pm="M1";
		$tmp=explode("::",$fparam);
		$strtmp="";
		$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
		for ($j=0; $j<count($tmp); $j=$j+1) {
				$strtmp=$strtmp."<option value='".$fname.":".$tmp[$j]."'>".$tmp[$j]."</option>";
		}
		//元からgoback時はjavascriptでselectedしてる。以下はgoback時ではなく入力前のデフォルトの値。
		if($itemM1["M1_ETC40"]!=""){
			$m1_etc40=$itemM1["M1_ETC40"];
			$m1_etc40=str_replace("Japanese Yen","JPY",$m1_etc40);
			$m1_etc40=str_replace("US Dollar","USD",$m1_etc40);
			$m1_etc40=str_replace("Euro","EUR",$m1_etc40);
			$m1_etc40=str_replace("British Pound","GBP",$m1_etc40);
			$sval=str_replace("M1_ETC40",$fname,$m1_etc40);
			echo "<!--sval:$sval-->";
			$strtmp=str_replace("'".$sval."'","'".$sval."' selected",$strtmp);
			$str=str_replace("[M2_CURRENCY]",$sval,$str);
			$str=str_replace("[D-M2_CURRENCY]",$sval,$str);
		}
		$str=str_replace("[OPT-M2_CURRENCY-M1_ETC40]",$strtmp,$str);
		

		//見積り送付画面をひらいたときのJSエラー対策
		if($type=="見積り送付"){
			$str=str_replace("([POST_DETAIL])","''",$str);
		}

		if($type == '発注依頼' || $type == '見積り送付' || $type=="追加見積り") {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and (STATUS='見積り送付' or STATUS='追加見積り') order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			while($item_filestatus = mysqli_fetch_assoc($rs)) {
				//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $item_filestatus['ID'] . '</option>';
				//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $item_filestatus['M2_ID'] . '（Version' . $item_filestatus['M2_VERSION'] . '）</option>';

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
					$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '" selected>' .$SCNo_str. ' Version'.$item_filestatus['M2_VERSION'] . '</option>';
				}else{
					$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $SCNo_str. ' Version'.$item_filestatus['M2_VERSION'] . '</option>';
				}

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

		//請求フォームの表示非表示
		//サプライヤーからの請求がすでに存在してたらフォームを非表示
		$check_pay_type=check_M2_PAY_TYPE($shodan_id,$_SESSION["MID"]);
		if($check_pay_type=="Milestone" || $check_pay_type=="Split"){
			if($param_div_id!=""){
				$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$shodan_id."' ";
				$StrSQL.=" and MID1='".$_SESSION["MID"]."' ";
				$StrSQL.=" and DIV_ID='".$param_div_id."' ";
				$StrSQL.=" and (STATUS='請求' or STATUS='請求書送付(一括前払い)' or STATUS='請求書送付(前払い)') ";
				$StrSQL.=" and S_STATUS='請求（サプライヤー）' ";
				$seikyu_s_rs=mysqli_query(ConnDB(),$StrSQL);
				$seikyu_s_num=mysqli_num_rows($seikyu_s_rs);
				if($seikyu_s_num<=0){
					//フォームを表示
					$str=DispParam($str,"SEIKYU_FORM");
				}else{
					$str=DispParam($str,"ERR_SEIKYU_FORM2");
				}
			
			}else{
				$str=DispParam($str,"ERR_SEIKYU_FORM1");
			}

		}else if($check_pay_type=="Once"){
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID='".$shodan_id."' ";
			$StrSQL.=" and MID1='".$_SESSION["MID"]."' ";
			$StrSQL.=" and (STATUS='請求' or STATUS='請求書送付(一括前払い)' or STATUS='請求書送付(前払い)') ";
			$StrSQL.=" and S_STATUS='請求（サプライヤー）' ";
			$seikyu_s_rs=mysqli_query(ConnDB(),$StrSQL);
			$seikyu_s_num=mysqli_num_rows($seikyu_s_rs);
			if($seikyu_s_num<=0){
				//フォームを表示
				$str=DispParam($str,"SEIKYU_FORM");
			}else{
				$str=DispParam($str,"ERR_SEIKYU_FORM2");
			}
		}
		//上記で表示になってるところいがいは消す。
		$str=DispParamNone($str,"SEIKYU_FORM");
		$str=DispParamNone($str,"ERR_SEIKYU_FORM1");
		$str=DispParamNone($str,"ERR_SEIKYU_FORM2");



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
		$str=str_replace("[D-SAMPLE]","",$str);
		$str=str_replace("[D-ORIGIN]","",$str);
		$str=str_replace("[D-UNIT]","",$str);
		$str=str_replace("[D-TEMPR]","",$str);
		$str=str_replace("[D-LEGAL]","",$str);
		$str=str_replace("[D-RESEARCHER]","",$str);

		$str=str_replace("[D-M2_TITLE]",'',$str);
		$str=str_replace("[D-M2_PRICE]",'',$str);
		$str=str_replace("[D-M2_COMMENT]",'',$str);

		// 新Quotation
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
		$str=str_replace("[D-M2_DETAIL_QUANTITY_".$detail_no."]",'',$str);
		$str=str_replace("[D-M2_DETAIL_UNIT_PRICE_".$detail_no."]",'',$str);
		$str=str_replace("[D-M2_DETAIL_SP_DISCOUNT_".$detail_no."]",'',$str);
		$str=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",'',$str);
		$str=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",'',$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE_TMP_".$detail_no."]",'',$str);

		$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",'',$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE]",'',$str);
		// -------------------------------------------------------------------------------

		$str=str_replace("[D-H_M2_ID]",'',$str);
		$str=str_replace("[D-H_COMMENT]",'',$str);

		$str=str_replace("[D-N_FILE]",'',$str);
		$str=str_replace("[D-N_FILE2]",'',$str);
		$str=str_replace("[D-N_FILE3]",'',$str);
		$str=str_replace("[D-N_FILE4]",'',$str);
		$str=str_replace("[D-N_FILE5]",'',$str);
		$str=str_replace("[D-N_MESSAGE]",'',$str);

		$str=str_replace("[D-N_PDF]",'',$str);
		$str=str_replace("[D-N_SHUKKA]",'',$str);
		$str=str_replace("[D-N_TEMP1]",'',$str);
		$str=str_replace("[D-N_TEMP2]",'',$str);
		$str=str_replace("[D-N_AWB]",'',$str);

		$str=str_replace("[D-S_FILE]",'',$str);
		$str=str_replace("[D-S_MESSAGE]",'',$str);

		$str=str_replace("[D-S2_FILE]",'',$str);
		$str=str_replace("[D-S2_MESSAGE]",'',$str);


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

		// 新Quotation
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
		$str=str_replace("[M2_DETAIL_QUANTITY_".$detail_no."]",'',$str);
		$str=str_replace("[M2_DETAIL_UNIT_PRICE_".$detail_no."]",'',$str);
		$str=str_replace("[M2_DETAIL_SP_DISCOUNT_".$detail_no."]",'',$str);
		$str=str_replace("[M2_DETAIL_PRICE_".$detail_no."]",'',$str);
		$str=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",'',$str);
		$str=str_replace("[M2_SPECIAL_NOTE_TMP_".$detail_no."]",'',$str);

		$str=str_replace("[M2_SPECIAL_DISCOUNT]",'',$str);
		$str=str_replace("[M2_SPECIAL_NOTE]",'',$str);
		// -------------------------------------------------------------------------------

		$str=str_replace("[H_M2_ID]",'',$str);
		$str=str_replace("[H_COMMENT]",'',$str);

		$str=str_replace("[N_FILE]",'',$str);
		$str=str_replace("[N_FILE2]",'',$str);
		$str=str_replace("[N_FILE3]",'',$str);
		$str=str_replace("[N_FILE4]",'',$str);
		$str=str_replace("[N_FILE5]",'',$str);
		$str=str_replace("[N_MESSAGE]",'',$str);

		$str=str_replace("[N_PDF]",'',$str);
		$str=str_replace("[N_SHUKKA]",'',$str);
		$str=str_replace("[N_TEMP1]",'',$str);
		$str=str_replace("[N_TEMP2]",'',$str);
		$str=str_replace("[N_AWB]",'',$str);

		$str=str_replace("[S_FILE]",'',$str);
		$str=str_replace("[S_MESSAGE]",'',$str);

		$str=str_replace("[S2_FILE]",'',$str);
		$str=str_replace("[S2_MESSAGE]",'',$str);

		//SHIP TO
		$str=str_replace("[D-M2_SHIP_TO_SPT_1]","",$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_2]","",$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_3]","",$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_4]","",$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_5]","",$str);
		$str=str_replace("[D-M2_SHIP_TO_SPT_6]","",$str);

		//BILL TO
		$str=str_replace("[D-M2_BILL_TO_SPT_1]","",$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_2]","",$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_3]","",$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_4]","",$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_5]","",$str);
		$str=str_replace("[D-M2_BILL_TO_SPT_6]","",$str);
	
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
	if(count($m1_list) != 0) {
		foreach($m1_list as $item) {
			$m1_view .= $item['M1_DVAL01'] . '<br>';
		}
	}
	$str=str_replace("[M1_VIEW]",$m1_view,$str);

	// Quotationの修正
	if(isset($_GET['upd_mode'])) {
		$str=str_replace("[UPD_MODE_SHOW]",'',$str);
		$str=str_replace("[UPD_MODE_HIDE]",'display:none;',$str);
	}
	else {
		$str=str_replace("[UPD_MODE_SHOW]",'display:none;',$str);
		$str=str_replace("[UPD_MODE_HIDE]",'',$str);
	}

	// M2から見ているかどうかわかるために
	if(strpos($_SESSION['MID'], 'M2') !== false) {
		$str=DispParamNone($str, "M1_ONLY");
	}
	else {
		$str=DispParam($str, "M1_ONLY");
	}

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
function SaveData($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$m2,$word,$word2,$mid_list,$param_div_id,$sub_type)
{
	eval(globals());

	$now=strtotime("now");
	$date_stmp=date('Y/m/d H:i:s',$now);
	$m2_nohin_type=$_POST['M2_NOHIN_TYPE'];

	//手数料等に関するステータス
	//見積りステータスプルダウン廃止により、内部データをみて自動設定
	//以前のプルダウンの項目
	//M_STATUS:直接送付
	//M_STATUS:直接送付(前払い)
	//M_STATUS:手数料追加
	//M_STATUS:手数料追加(前払い)
	
	//新ルール1（「前払い」がつくかどうかの判断）
	//以下の時「直接送付(前払い)」、「手数料追加(前払い)」のどちらか。
	//・一括払い：前払いなし⇐仕様変更で1活払いも前払いありになった
	//・2回払い：Part1が前払い
	//・マイルストーン払い：テンポラリーの前払いチェックにチェックが入ってるかどうか。⇐仕様変更で複数アイテム前払い選択可能になった
	//
	//新ルール2(直接配送か、手数料追加かの判断)
	//「直接配送」は以下の条件の場合：
	//「見積り依頼」の「輸出代行の有無」が「なし」 (M1_TRANS_FLG==なし)
	//&& 
	//「見積り送付」の「Form of Delivery」が「Data」の場合（M2_NOHIN_TYPE==data）
	//「手数料追加」は以下の場合：
	//上記の直接配送に当てはまらないもの

	$m_status="";
	$tesuryo_tsuika_mode="";
	if($type=="見積り送付" || $type=="追加見積り"){

		if($shodan_id=="" && $key != '') {
			// 商談IDを取得してステータス更新
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus = mysqli_fetch_assoc($rs);
			$shodan_id = $item_filestatus['SHODAN_ID'];
		}

		$StrSQL="SELECT * FROM DAT_FILESTATUS where SHODAN_ID='".$shodan_id."' ";
		$StrSQL.=" and (STATUS='見積り依頼' or STATUS='再見積り依頼') ";
		$StrSQL.=" and MID1='".$_SESSION['MID']."' ";
		$StrSQL.=" order by ID desc;";
		$tesuryo_rs=mysqli_query(ConnDB(),$StrSQL);
		$tesuryo_item = mysqli_fetch_assoc($tesuryo_rs);
		echo "<!--tesuryoSQL:$StrSQL-->";
		echo "<!--tesuryo_item:";
		var_dump($tesuryo_item);
		echo "-->";

		if($_POST["M2_PAY_TYPE"]=="Once" || $_POST["M2_PAY_TYPE"]=="Split"){
			if($tesuryo_item["M1_TRANS_FLG"]=="なし" && 
				($_POST['M2_NOHIN_TYPE']=="data" || $_POST['M2_NOHIN_TYPE']=="Data") ){
				$m_status="直接送付";
			}else{
				$tesuryo_tsuika_mode="on";
				$m_status="手数料追加";
			}
		}else if($_POST["M2_PAY_TYPE"]=="Milestone"){
			if($tesuryo_item["M1_TRANS_FLG"]=="なし" && 
				!in_array("Goods",$_POST["M2_NOHIN_TYPE_TMP"]) ){
				$m_status="直接送付";
			}else{
				$tesuryo_tsuika_mode="on";
				$m_status="手数料追加";
			}
		}
		
	}

	echo "<!--tesuryo_tsuika_mode:$tesuryo_tsuika_mode-->";
	echo "<!--m_status上部:$m_status-->";
	echo "<!--_POST[MAEBARAI]:";
	var_dump($_POST["MAEBARAI"]);
	echo "-->";
	echo "<!--_POST['MAEBARAI'][0]:".$_POST["MAEBARAI"][0]."-->";

	//以下は、見積りステータスプルダウン廃止によりコメントアウト
	////手数料等に関するステータス
	//echo "<!--m_status post:".$_POST['M_STATUS']."-->";
	//$m_status=str_replace("M_STATUS:","",$_POST['M_STATUS']);
	//echo "<!--m_status:".$m_status."-->";


	//debug用
	//$m_status="直接送付";



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
			if($tesuryo_tsuika_mode=="on"){
				$status = '運営手数料追加';
			}else{
				$status = '見積り送付';
			}

			//以下は、見積りステータスプルダウン廃止によりコメントアウト
			//if($m_status=="手数料追加" || $m_status=="手数料追加(前払い)"){
			//	$status = '運営手数料追加';
			//}else{
			//	$status = '見積り送付';
			//}

			//$status = '見積り送付';
			$c_status = '見積り';
			$status_sort = '3';
  	  break;
		case '追加見積り':
			if($tesuryo_tsuika_mode=="on"){
				$status = '運営手数料追加';
			}else{
				$status = '追加見積り';
			}

			//以下は、見積りステータスプルダウン廃止によりコメントアウト
			//if($m_status=="手数料追加" || $m_status=="手数料追加(前払い)"){
			//	$status = '運営手数料追加';
			//}else{
			//	$status = '追加見積り';
			//}
			
			//$status = '追加見積り';
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
			$c_status = '発注';
			$status_sort = '4';
  	  break;
		case '受注承認':
			$status = '受注承認';
			//$c_status = '発注';
			$c_status = '実施中';
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
			//$status = '納品確認';
			$c_status = '請求';
			//$c_status = '納品';
			$status_sort = '9';
			//$status_sort = '8';
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
  	  //サプライヤーによる見積りの辞退
  	  case '見積りの辞退':
			$status = '見積りの辞退';
			$c_status = '見積り';
			$status_sort = '3';
  	  break;
		case 'サプライヤーキャンセル承認':
			$status = 'サプライヤーキャンセル承認';
			$c_status = '実施中';
			$status_sort = '95';
  	  break;
  	  	case 'サプライヤーキャンセル否認':
			$status = 'サプライヤーキャンセル否認';
			$c_status = '実施中';
			$status_sort = '95';
  	  break;
  	  	case 'キャンセル承認':
			$status = 'キャンセル承認';
			$c_status = 'キャンセル';
			$status_sort = '95';
  	  break;
	}


	

	if( ($type=="データ納品" || $type=="物品納品") && $sub_type=="サプライヤーが納品(一括前払い)"){
		$status=$sub_type;
		$m2_nohin_type=$type;
	
	}else if($type=="請求" && $sub_type=="請求書送付(前払い)"){
		$status=$sub_type;
	}


	$h_div_id="";
	if($type!="" 
		&& $type!="問い合わせ" 
		&& $type!="見積り依頼"
		&& $type!="再見積り依頼"
		&& $type!="見積り送付"
		&& $type!="運営手数料追加"
		&& $type!="発注依頼"){
		//発注以降、一括払い時にも扱いを一律にするために、便宜上DIV_IDを設定するようにした。
		//最新の発注依頼をとってきて、その発注依頼の対象の見積り送付データをとってきて、1括払いの時に、$h_div_idを設定
		//発注は1商談内に1つしか存在しない仕様と決定したが、念のため最新の発注依頼をとってくるようにしている。
		//商談内で複数サプライヤー一括依頼はなくなったが、$_SESSION["MID"]指定も念のため
		if($shodan_id!=""){
			$StrSQL="SELECT ID, H_M2_ID FROM DAT_FILESTATUS where SHODAN_ID='".$shodan_id."' ";
			$StrSQL.=" and MID1='".$_SESSION["MID"]."' ";
			$StrSQL.=" and STATUS='発注依頼' order by ID desc ";
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item= mysqli_fetch_assoc($h_rs);
			echo "<!--$StrSQL:[1]:\n";
			var_dump($h_item);
			echo "-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$h_item["H_M2_ID"]." ";
			$StrSQL.=" and MID1='".$_SESSION["MID"]."' ";
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
			$StrSQL.=" and MID1='".$_SESSION["MID"]."' ";
			$StrSQL.=" and STATUS='発注依頼' order by ID desc ";
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item= mysqli_fetch_assoc($h_rs);
			echo "<!--$StrSQL:[4]:\n";
			var_dump($h_item);
			echo "-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$h_item["H_M2_ID"]." ";
			$StrSQL.=" and MID1='".$_SESSION["MID"]."' ";
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
	// Supplierからの「請求」ではDAT_SHODANステータスは変更しない
	//サプライヤーによる「見積りの辞退」の際もDAT_SHODANステータスは更新しない
	if($type != '請求' && $type != '見積りの辞退') { 
	if($shodan_id != '') {
		// 他のステータスに変更するだけ
		
		//分割払い対応
		if($type=="受注承認" ||
			$type=="データ納品" ||
			$type=="物品納品"){
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

		/*
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
		*/
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
	else {
		// 新規
		//以下はおそらくサプライヤー（M1）側ではつかわれてない。
		//サプライヤーから商談をはじめることはない。
		//M2側からm_contact1にアクセスして新規作成するルートがどこかにある可能性はある。
	$StrSQL = "
		INSERT INTO DAT_SHODAN (
			MID2,
			TITLE,
			MID1_LIST,
			CATEGORY,
			KEYWORD,

			NEWDATE,
			EDITDATE,

			STATUS_SORT,
			C_STATUS,
			STATUS
		) VALUE (
			'".$_SESSION['MID']."',
			'".$_POST['TITLE']."',
			'".$mid1_list."',
			'".$word."',
			'".$word2."',

			'".$date_stmp."',
			'".$date_stmp."',

			'".$status_sort."',
			'".$c_status."',
			'".$status."'
		)";

		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
		// 商談ID取得
		//$StrSQL="SELECT ID FROM DAT_SHODAN order by ID desc;";
		$StrSQL="SELECT ID FROM DAT_SHODAN where EDITDATE='".$date_stmp."' order by ID desc;";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$shodan_id = $item['ID'];
	}
	} // 請求以外のif


	//////一端コメントアウト
	//////現行の1活払いにあわせて、Supplierからの請求ではDAT_SHODAN_DIVは変更しない
	//////
	//分割払い対応
	//if($type == '請求' && $param_div_id!=""){
	//	$StrSQL = "
	//	UPDATE DAT_SHODAN_DIV SET
	//	EDITDATE = '".$date_stmp."',
	//	C_STATUS = '".$c_status."',
	//	STATUS = '".$status."'
	//	WHERE
	//	DIV_ID = '".$param_div_id."'
	//	";
	//			//echo "<!--sql: $StrSQL-->";
	//	if (!(mysqli_query(ConnDB(),$StrSQL))) {
	//		die;
	//	}
	//}

	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_shodan = mysqli_fetch_assoc($rs);
	$mid2 = $item_shodan['MID2'];

	// ここからは複数Supplier対応
	$mid1s = explode(',', $mid1_list);
	foreach($mid1s as $mid1) {


	// 見積り送付の場合は見積番号とVersionを生成
	//「Scientist3 control No.」を生成
	//フォーマットの基礎部分「SCyymmddxxx」
	//yy(SCNo_yy):西暦年の下2桁
	//mm(SCNo_mm):月の2桁
	//dd(SCNo_dd):日付けの2桁
	//xxx(SCNo_cnt):5桁の数字。日付けが変わったら001から「見積り送付」のたびにインクリメントでナンバリング。
	$m2_id = '';
	$m2_version = '';
	$SCNo=array(
	"SCNo_yy" => "", 
	"SCNo_mm" => "", 
	"SCNo_dd" => "", 
	"SCNo_cnt" => "", 
	"SCNo_else1" => "", 
	"SCNo_else2" => "", 
	);
	if($type == '見積り送付' || $type=="追加見積り") {
		if($_POST['H_M2_ID'] != '') {
			//見積り送付2回目以降H_M2_IDが送られてくる
			//（見積り送付時に、裏で、その時点までの最新の見積り送付データのIDがH_M2_IDに設定される）
		  $StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['H_M2_ID'].";";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus2 = mysqli_fetch_assoc($rs);
			$m2_id = $item_filestatus2['M2_ID'];

			$SCNo["SCNo_yy"]=$item_filestatus2["SCNo_yy"];
			$SCNo["SCNo_mm"]=$item_filestatus2["SCNo_mm"];
			$SCNo["SCNo_dd"]=$item_filestatus2["SCNo_dd"];
			$SCNo["SCNo_cnt"]=$item_filestatus2["SCNo_cnt"];

			$StrSQL="SELECT MAX(CAST(`M2_VERSION` AS SIGNED)) as m2_version FROM DAT_FILESTATUS WHERE M2_ID=".$m2_id.";";
		  //$StrSQL="SELECT MAX(M2_VERSION) as m2_version FROM DAT_FILESTATUS WHERE M2_ID=".$m2_id.";";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus2 = mysqli_fetch_assoc($rs);
			$m2_version = intval($item_filestatus2['m2_version']) + 1;

		}
		else {
		  $StrSQL="SELECT MAX(CAST(`M2_ID` AS SIGNED)) as m2_id FROM DAT_FILESTATUS;";
		//  $StrSQL="SELECT MAX(M2_ID) as m2_id FROM DAT_FILESTATUS;";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus2 = mysqli_fetch_assoc($rs);
			$m2_id = intval($item_filestatus2['m2_id']) + 1;
			$m2_version = 1;

			$SCNo["SCNo_yy"]=date("y",$now);
			$SCNo["SCNo_mm"]=date("m",$now);
			$SCNo["SCNo_dd"]=date("d",$now);

			$StrSQL="SELECT MAX(CAST(`SCNo_cnt` AS SIGNED)) as max_scno_cnt from DAT_FILESTATUS ";
			$StrSQL.=" where SCNo_yy ='".$SCNo["SCNo_yy"]."' ";
			$StrSQL.=" and SCNo_mm='".$SCNo["SCNo_mm"]."' ";
			$StrSQL.=" and SCNo_dd='".$SCNo["SCNo_dd"]."' ";
			$scno_rs=mysqli_query(ConnDB(),$StrSQL);
			$scno_item=mysqli_fetch_assoc($scno_rs);
			$SCNo["SCNo_cnt"]=sprintf("%05d", $scno_item["max_scno_cnt"]+1);
		}
	}



//	// 見積り送付の場合は見積番号とVersionを生成
//	$m2_id = '';
//	$m2_version = '';
//	if($type == '見積り送付' || $type=="追加見積り") {
//		if($_POST['H_M2_ID'] != '') {
//		  $StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['H_M2_ID'].";";
//			$rs=mysqli_query(ConnDB(),$StrSQL);
//			$item_filestatus2 = mysqli_fetch_assoc($rs);
//			$m2_id = $item_filestatus2['M2_ID'];
//
//			$StrSQL="SELECT MAX(CAST(`M2_VERSION` AS SIGNED)) as m2_version FROM DAT_FILESTATUS WHERE M2_ID=".$m2_id.";";
//		  //$StrSQL="SELECT MAX(M2_VERSION) as m2_version FROM DAT_FILESTATUS WHERE M2_ID=".$m2_id.";";
//			$rs=mysqli_query(ConnDB(),$StrSQL);
//			$item_filestatus2 = mysqli_fetch_assoc($rs);
//			$m2_version = intval($item_filestatus2['m2_version']) + 1;
//		}
//		else {
//		  $StrSQL="SELECT MAX(CAST(`M2_ID` AS SIGNED)) as m2_id FROM DAT_FILESTATUS;";
//		//  $StrSQL="SELECT MAX(M2_ID) as m2_id FROM DAT_FILESTATUS;";
//			$rs=mysqli_query(ConnDB(),$StrSQL);
//			$item_filestatus2 = mysqli_fetch_assoc($rs);
//			$m2_id = intval($item_filestatus2['m2_id']) + 1;
//			$m2_version = 1;
//		}
//	}

//「Scientist3 control No.」を生成
//	//フォーマットの基礎部分「SCyymmddxxx」
//	//yy(SCNo_yy):西暦年の下2桁
//	//mm(SCNo_mm):月の2桁
//	//dd(SCNo_dd):日付けの2桁
//	//xxx(SCNo_cnt):5桁の数字。日付けが変わったら001から「見積り送付」のたびにインクリメントでナンバリング。
//	$SCNo=array(
//	"SCNo_yy" => "", 
//	"SCNo_mm" => "", 
//	"SCNo_dd" => "", 
//	"SCNo_cnt" => "", 
//	"SCNo_else1" => "", 
//	"SCNo_else2" => "", 
//	);
//	if($type=="見積り送付"){
//		$SCNo["SCNo_yy"]=date("y",$now);
//		$SCNo["SCNo_mm"]=date("m",$now);
//		$SCNo["SCNo_dd"]=date("d",$now);
//
//		$StrSQL="SELECT MAX(CAST(`SCNo_cnt` AS SIGNED)) as max_scno_cnt from DAT_FILESTATUS ";
//		$StrSQL.=" where SCNo_yy ='".$SCNo["SCNo_yy"]."' ";
//		$StrSQL.=" and SCNo_mm='".$SCNo["SCNo_mm"]."' ";
//		$StrSQL.=" and SCNo_dd='".$SCNo["SCNo_dd"]."' ";
//		$scno_rs=mysqli_query(ConnDB(),$StrSQL);
//		$scno_item=mysqli_fetch_assoc($scno_rs);
//		$SCNo["SCNo_cnt"]=sprintf("%05d", $scno_item["max_scno_cnt"]+1);
//	}



	$tmp_m_status=$m_status;

	//見積り送付:「分割支払い」処理
	if($type == '見積り送付' || $type=="追加見積り") {
		//分割支払い用のID発効
		//分割払いじゃない時も発注以降に使う
		$ary_div_id=array();
		$tmp="";
		if($_POST['M2_PAY_TYPE']!="Once"){
			//分割支払い1枚にまとめたデータ
			$ary_div_id[]=$m2_id."-".$m2_version."-Part0";

			for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']); $detail_key++) {
				//分割支払いにもかかわらず、分割先の割り当てが指定されてなかった場合
				if($_POST['M2_DETAIL_SPLIT_PART'][$detail_key]==""){
					$tmp=$m2_id."-".$m2_version."-Part1";
				}else{
					$tmp=$m2_id."-".$m2_version."-".$_POST['M2_DETAIL_SPLIT_PART'][$detail_key];
				}
				if(!in_array($tmp, $ary_div_id)){
					$ary_div_id[]=$tmp;
				}
			}
		}else{
			$tmp=$m2_id."-".$m2_version;
			$ary_div_id[]=$tmp;
		}
		
		//分割払い対応
		foreach ($ary_div_id as $div_id) {
			//分割支払い（2回払い、マイルストーン払い）の時
			//分割用のテーブルDAT_SHODAN_DIV更新
			if($_POST['M2_PAY_TYPE']!="Once"){
				$StrSQL = "
				INSERT INTO DAT_SHODAN_DIV (
					SHODAN_ID,
					DIV_ID,
					STATUS,
					C_STATUS,
					NEWDATE,
					EDITDATE
				) VALUE (
					'".$shodan_id."',
					'".$div_id."',
					'".$status."',
					'".$c_status."',
					'".$date_stmp."',
					'".$date_stmp."'
				)";
				//echo('<!--'.$StrSQL.'-->');
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}

			//分割支払い時に、「Scientist3 control No.」の枝番を生成
			if($_POST['M2_PAY_TYPE']!="Once"){
				$tmp="";
				$tmp=explode("-", $div_id);
				echo "<!--";
				var_dump($tmp);
				echo "-->";
				$part="";
				$part_no="";
				if(count($tmp)==3){
					$part=$tmp[2];
					if($part!=""){
						$part_no=str_replace("Part", "", $part);
						$SCNo["SCNo_else1"]=generateAlphabetId($part_no);
					}
				}
			}
			echo "<!--SCNo:";
			var_dump($SCNo);
			echo "-->";

			//マイルストーン払い時に、納品形態を分割された見積り毎に設定。
			//Special noteもマイルストーン時に分割された見積り毎に個別に設定。
			//マイルストーン払いの場合、アイテム1つに対して、見積り送付が1つ対応するため実現できる。
			$m2_nohin_type=$_POST['M2_NOHIN_TYPE'];
			$m2_special_note=$_POST['M2_SPECIAL_NOTE'];
			if($_POST["M2_PAY_TYPE"]=="Milestone"){
				$tmp="";
				$tmp=explode("-", $div_id);
				$part="";
				$part_no="";
				if(count($tmp)==3){
					$part=$tmp[2];
					if($part!=""){
						$part_no=intval(str_replace("Part", "", $part));
					}
				}
				if($part_no>0){
					$m2_nohin_type=$_POST["M2_NOHIN_TYPE_TMP"][$part_no];
					$m2_special_note=$_POST["M2_SPECIAL_NOTE_TMP"][$part_no];
				}else{
					$m2_nohin_type="";
					$m2_special_note="";
				}
				echo "<!--part_no: $part_no-->";
				echo "<!--m2_nohin_type: $m2_nohin_type-->";
			}

			//一括払いの時の見積りステータスの「前払い」の設定
			if($_POST["M2_PAY_TYPE"]=="Once"){
				
				if( !is_null($_POST["MAEBARAI"][1]) && $_POST["MAEBARAI"][1]!="" ){
					$m_status=$tmp_m_status."(前払い)";
				}else{
					$m_status=$tmp_m_status;
				}
				echo "<!--m_status: $m_status-->";
			}

			//2回払いの時の見積りステータスの「前払い」の設定
			if($_POST["M2_PAY_TYPE"]=="Split"){
				$tmp="";
				$tmp=explode("-", $div_id);
				$part="";
				$part_no="";
				if(count($tmp)==3){
					$part=$tmp[2];
				}
				if($part=="Part0" || $part=="Part1"){
					$m_status=$tmp_m_status."(前払い)";
				}else{
					$m_status=$tmp_m_status;
				}
				echo "<!--part: $part-->";
				echo "<!--m_status: $m_status-->";
			}


			//マイルストーン払いの時の見積りステータスの「前払い」の設定
			//仕様変更で複数の見積り（アイテム）を前払い対象に設定可能にする
			if($_POST["M2_PAY_TYPE"]=="Milestone"){
				$tmp="";
				$tmp=explode("-", $div_id);
				$part="";
				$part_no="";
				if(count($tmp)==3){
					$part=$tmp[2];
					if($part!=""){
						$part_no=intval(str_replace("Part", "", $part));
					}
				}

				//↓↓↓見積り事に個別に設定したい場合このエリアのコメントをはずす。
				//↓↓↓その場合、運営手数料追加の管理画面のモード切替部分を
				//↓↓↓作り替えなければならないので注意が必要。
				//if($tesuryo_item["M1_TRANS_FLG"]=="なし" && 
				//	($m2_nohin_type=="data" || $m2_nohin_type=="Data") ){
				//	$tmp_m_status="直接送付";
				//}else{
				//	$tesuryo_tsuika_mode="on";
				//	$tmp_m_status="手数料追加";
				//}

				if( $part=="Part0" && in_array("YES", $_POST["MAEBARAI"]) ){
					$m_status=$tmp_m_status."(前払い)";

				}else if($part!="Part0" && 
					!is_null($_POST["MAEBARAI"][$part_no]) && $_POST["MAEBARAI"][$part_no]!="" ){
					$m_status=$tmp_m_status."(前払い)";

				}else{
					$m_status=$tmp_m_status;
				}
				echo "<!--part: $part-->";
				echo "<!--m_status: $m_status-->";
			}


//			//マイルストーン払いの時の見積りステータスの「前払い」の設定
//			if($_POST["M2_PAY_TYPE"]=="Milestone"){
//				$tmp="";
//				$tmp=explode("-", $div_id);
//				$part="";
//				$part_no="";
//				if(count($tmp)==3){
//					$part=$tmp[2];
//					if($part!=""){
//						$part_no=intval(str_replace("Part", "", $part));
//					}
//				}
//
//				//↓↓↓見積り事に個別に設定したい場合このエリアのコメントをはずす。
//				//↓↓↓その場合、運営手数料追加の管理画面のモード切替部分を
//				//↓↓↓作り替えなければならないので注意が必要。
//				//if($tesuryo_item["M1_TRANS_FLG"]=="なし" && 
//				//	($m2_nohin_type=="data" || $m2_nohin_type=="Data") ){
//				//	$tmp_m_status="直接送付";
//				//}else{
//				//	$tesuryo_tsuika_mode="on";
//				//	$tmp_m_status="手数料追加";
//				//}
//
//				if($_POST["MAEBARAI"][0]!="" && !is_null($_POST["MAEBARAI"][0]) 
//					&& ($part=="Part0" || $part=="Part1") ){
//					$m_status=$tmp_m_status."(前払い)";
//				}else{
//					$m_status=$tmp_m_status;
//				}
//				echo "<!--part: $part-->";
//				echo "<!--m_status: $m_status-->";
//			}


			//輸出代行費用
			//※以下の$m2_export_fee_tableは仕様変更で使わなくなった。
			$StrSQL="SELECT * FROM DAT_AGENCY_SETTING";
			$exp_fee_rs=mysqli_query(ConnDB(),$StrSQL);
			$exp_fee_ary=array();
			while($exp_fee_item= mysqli_fetch_assoc($exp_fee_rs)){
				$as_keyword_currency="";
				$as_keyword_currency=str_replace("AS_KEYWORD_CURRENCY:", "", $exp_fee_item["AS_KEYWORD_CURRENCY"]);
				if($as_keyword_currency!=""){
					$exp_fee_ary[$as_keyword_currency]=$exp_fee_item["AS_EXPORT_FEE"];
				}
			}
			$m2_export_fee_table=json_encode($exp_fee_ary);


			//輸出代行費用
			//※仕様変更でこっちの値が使われている
			//2回払い、マイルストーン払いの場合は、Part1にのみ値を保存
			//1回払いの場合は、普通に保存
			$m2_currency=str_replace("M2_CURRENCY:","",$_POST["M2_CURRENCY"]);
			if($_POST["M2_PAY_TYPE"]=="Split" || $_POST["M2_PAY_TYPE"]=="Milestone"){
				$tmp="";
				$tmp=explode("-", $div_id);
				$part="";
				$part_no="";
				if(count($tmp)==3){
					$part=$tmp[2];
				}

				if($part=="Part0" || $part=="Part1"){
					if( isset($exp_fee_ary[$m2_currency]) && is_numeric($exp_fee_ary[$m2_currency]) ){
						$m2_export_fee=$exp_fee_ary[$m2_currency];
					}else{
						$m2_export_fee="";
					}
				}else{
					$m2_export_fee="";
				}
			
			}else{
				if( isset($exp_fee_ary[$m2_currency]) && is_numeric($exp_fee_ary[$m2_currency]) ){
					$m2_export_fee=$exp_fee_ary[$m2_currency];
				}else{
					$m2_export_fee="";
				}
			}


			//輸出代行費用が発生するのは、研究者が見積り依頼時に「輸出代行　あり」を選択した場合のみ。
			//見積り送付や運営手数料追加のNEWDATEの値より前の日付けに送信した、
			//「見積り依頼」データの「M1_TRANS_FLG」が「なし」の場合は強制的に値無し
			$StrSQL="SELECT ID,NEWDATE,STATUS,M1_TRANS_FLG FROM DAT_FILESTATUS WHERE ";
			$StrSQL.=" SHODAN_ID='".$shodan_id."' ";
			$StrSQL.=" AND STATUS='見積り依頼' ";
			$StrSQL.=" AND NEWDATE<'".$date_stmp."' ";
			$StrSQL.=" ORDER BY NEWDATE DESC ";
			$irai_rs=mysqli_query(ConnDB(),$StrSQL);
			$irai_item = mysqli_fetch_assoc($irai_rs);
			echo "<!--irai_item:";
			var_dump($irai_item);
			echo "-->";
			if($irai_item["M1_TRANS_FLG"]=="なし"){
				$m2_export_fee="";
			}

			//PF手数料のもとになる、PF手数料率をDBからとってきて設定
			$StrSQL="SELECT ID, M2_ETC02 FROM DAT_M2 WHERE MID='".$mid2."';";
			$rsM2=mysqli_query(ConnDB(),$StrSQL);
			$itemM2 = mysqli_fetch_assoc($rsM2);
			$pf_rate=$itemM2["M2_ETC02"];
			

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
		
					M2_ID,
					M2_VERSION,
					M2_NOHIN_TYPE,
					M2_PAY_TYPE,
					M_STATUS,
					M2_QUOTE_NO,
					M2_STUDY_CODE,
					M2_DATE,
					M2_QUOTE_VALID_UNTIL,
					M2_DESCRIPTION,
					M2_CURRENCY,
					M2_SPECIAL_DISCOUNT,
					M2_SPECIAL_NOTE,
					M2_TAX_RATE2,
		
					H_M2_ID,
					H_COMMENT,
		
					N_FILE,
					N_FILE2,
					N_FILE3,
					N_FILE4,
					N_FILE5,
					N_MESSAGE,
		
					N_PDF,
					N_SHUKKA,
					N_TEMP1,
					N_TEMP2,
					N_AWB,
		
					S_FILE,
					S_MESSAGE,
					DIV_ID,

					M2_SHIP_TO_SPT_1,
					M2_SHIP_TO_SPT_2,
					M2_SHIP_TO_SPT_3,
					M2_SHIP_TO_SPT_4,
					M2_SHIP_TO_SPT_5,
					M2_SHIP_TO_SPT_6,

					M2_BILL_TO_SPT_1,
					M2_BILL_TO_SPT_2,
					M2_BILL_TO_SPT_3,
					M2_BILL_TO_SPT_4,
					M2_BILL_TO_SPT_5,
					M2_BILL_TO_SPT_6,

					SCNo_yy,
					SCNo_mm,
					SCNo_dd,
					SCNo_cnt,
					SCNo_else1,
					SCNo_else2,

					M2_EXPORT_FEE_TABLE,
					M2_EXPORT_FEE,
					PF_RATE,

					S_STATUS,

					NEWDATE,
					EDITDATE
				) VALUE (
					'".$shodan_id."',
					'".$mid1."',
					'".$mid2."',
		
					'".$c_status."',
					'".$status."',
		
					'".$_POST['COMMENT']."',
					'".$_POST['FILE']."',
					'".$_POST['FILE2']."',
					'".$_POST['FILE3']."',
					'".$_POST['FILE4']."',
					'".$_POST['FILE5']."',
					'".$_POST['KIGEN']."',
		
					'".$_POST['M1_MESSAGE']."',
					'".$_POST['M1_TRANS_FLG']."',
					'".$_POST['M1_TRANS_TXT']."',
					'".$_POST['M1_PRICE']."',
					'".$_POST['M1_KIGEN']."',
					'".$_POST['M1_FILE']."',
		
					'".$m2_id."',
					'".$m2_version."',
					'".$m2_nohin_type."',
					'".$_POST['M2_PAY_TYPE']."',
					'".$m_status."',
					'".$_POST['M2_QUOTE_NO']."',
					'".$_POST['M2_STUDY_CODE']."',
					'".$_POST['M2_DATE']."',
					'".$_POST['M2_QUOTE_VALID_UNTIL']."',
					'".$_POST['M2_DESCRIPTION']."',
					'".$_POST['M2_CURRENCY']."',
					'".$_POST['M2_SPECIAL_DISCOUNT']."',
					'".$m2_special_note."',
					'10',
		
					'".$_POST['H_M2_ID']."',
					'".$_POST['H_COMMENT']."',
		
					'".$_POST['N_FILE']."',
					'".$_POST['N_FILE2']."',
					'".$_POST['N_FILE3']."',
					'".$_POST['N_FILE4']."',
					'".$_POST['N_FILE5']."',
					'".$_POST['N_MESSAGE']."',
		
					'".$_POST['N_PDF']."',
					'".$_POST['N_SHUKKA']."',
					'".$_POST['N_TEMP1']."',
					'".$_POST['N_TEMP2']."',
					'".$_POST['N_AWB']."',
		
					'".$_POST['S_FILE']."',
					'".$_POST['S_MESSAGE']."',
					'".$div_id."',

					'".$_POST['M2_SHIP_TO_SPT_1']."',
					'".$_POST['M2_SHIP_TO_SPT_2']."',
					'".$_POST['M2_SHIP_TO_SPT_3']."',
					'".$_POST['M2_SHIP_TO_SPT_4']."',
					'".$_POST['M2_SHIP_TO_SPT_5']."',
					'".$_POST['M2_SHIP_TO_SPT_6']."',

					'".$_POST['M2_BILL_TO_SPT_1']."',
					'".$_POST['M2_BILL_TO_SPT_2']."',
					'".$_POST['M2_BILL_TO_SPT_3']."',
					'".$_POST['M2_BILL_TO_SPT_4']."',
					'".$_POST['M2_BILL_TO_SPT_5']."',
					'".$_POST['M2_BILL_TO_SPT_6']."',

					'".$SCNo["SCNo_yy"]."',
					'".$SCNo["SCNo_mm"]."',
					'".$SCNo["SCNo_dd"]."',
					'".$SCNo["SCNo_cnt"]."',
					'".$SCNo["SCNo_else1"]."',
					'".$SCNo["SCNo_else2"]."',

					'".$m2_export_fee_table."',
					'".$m2_export_fee."',
					'".$pf_rate."',

					'".$_POST["S_STATUS"]."',

					'".$date_stmp."',
					'".$date_stmp."'
				)";
			//echo('<!--'.$StrSQL.'-->');
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

			
		}
	}else{
		// ファイルステータス
		//見積り送付、追加見積り以外

		//発注以降、一括払い時にも扱いを一律にするために、便宜上DIV_IDを設定するようにした。
		$tmp_div_id= $param_div_id!="" ? $param_div_id : $h_div_id;

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
	
				M2_ID,
				M2_VERSION,
				M2_NOHIN_TYPE,
				M2_PAY_TYPE,
				M_STATUS,
				M2_QUOTE_NO,
				M2_STUDY_CODE,
				M2_DATE,
				M2_QUOTE_VALID_UNTIL,
				M2_DESCRIPTION,
				M2_CURRENCY,
				M2_SPECIAL_DISCOUNT,
				M2_SPECIAL_NOTE,
				M2_TAX_RATE2,
	
				H_M2_ID,
				H_COMMENT,
	
				N_FILE,
				N_FILE2,
				N_FILE3,
				N_FILE4,
				N_FILE5,
				N_MESSAGE,
	
				N_PDF,
				N_SHUKKA,
				N_TEMP1,
				N_TEMP2,
				N_AWB,
	
				S_FILE,
				S_MESSAGE,
				DIV_ID,

				M2_SHIP_TO_SPT_1,
				M2_SHIP_TO_SPT_2,
				M2_SHIP_TO_SPT_3,
				M2_SHIP_TO_SPT_4,
				M2_SHIP_TO_SPT_5,
				M2_SHIP_TO_SPT_6,

				M2_BILL_TO_SPT_1,
				M2_BILL_TO_SPT_2,
				M2_BILL_TO_SPT_3,
				M2_BILL_TO_SPT_4,
				M2_BILL_TO_SPT_5,
				M2_BILL_TO_SPT_6,

				SCNo_yy,
				SCNo_mm,
				SCNo_dd,
				SCNo_cnt,
				SCNo_else1,
				SCNo_else2,

				M2_EXPORT_FEE_TABLE,
				M2_EXPORT_FEE,
				PF_RATE,

				S_STATUS,
	
				NEWDATE,
				EDITDATE
			) VALUE (
				'".$shodan_id."',
				'".$mid1."',
				'".$mid2."',
	
				'".$c_status."',
				'".$status."',
	
				'".$_POST['COMMENT']."',
				'".$_POST['FILE']."',
				'".$_POST['FILE2']."',
				'".$_POST['FILE3']."',
				'".$_POST['FILE4']."',
				'".$_POST['FILE5']."',
				'".$_POST['KIGEN']."',
	
				'".$_POST['M1_MESSAGE']."',
				'".$_POST['M1_TRANS_FLG']."',
				'".$_POST['M1_TRANS_TXT']."',
				'".$_POST['M1_PRICE']."',
				'".$_POST['M1_KIGEN']."',
				'".$_POST['M1_FILE']."',
	
				'".$m2_id."',
				'".$m2_version."',
				'".$m2_nohin_type."',
				'".$_POST['M2_PAY_TYPE']."',
				'".$m_status."',
				'".$_POST['M2_QUOTE_NO']."',
				'".$_POST['M2_STUDY_CODE']."',
				'".$_POST['M2_DATE']."',
				'".$_POST['M2_QUOTE_VALID_UNTIL']."',
				'".$_POST['M2_DESCRIPTION']."',
				'".$_POST['M2_CURRENCY']."',
				'".$_POST['M2_SPECIAL_DISCOUNT']."',
				'".$_POST['M2_SPECIAL_NOTE']."',
				'10',
	
				'".$_POST['H_M2_ID']."',
				'".$_POST['H_COMMENT']."',
	
				'".$_POST['N_FILE']."',
				'".$_POST['N_FILE2']."',
				'".$_POST['N_FILE3']."',
				'".$_POST['N_FILE4']."',
				'".$_POST['N_FILE5']."',
				'".$_POST['N_MESSAGE']."',
	
				'".$_POST['N_PDF']."',
				'".$_POST['N_SHUKKA']."',
				'".$_POST['N_TEMP1']."',
				'".$_POST['N_TEMP2']."',
				'".$_POST['N_AWB']."',
	
				'".$_POST['S_FILE']."',
				'".$_POST['S_MESSAGE']."',
				'".$tmp_div_id."',

				'".$_POST['M2_SHIP_TO_SPT_1']."',
				'".$_POST['M2_SHIP_TO_SPT_2']."',
				'".$_POST['M2_SHIP_TO_SPT_3']."',
				'".$_POST['M2_SHIP_TO_SPT_4']."',
				'".$_POST['M2_SHIP_TO_SPT_5']."',
				'".$_POST['M2_SHIP_TO_SPT_6']."',

				'".$_POST['M2_BILL_TO_SPT_1']."',
				'".$_POST['M2_BILL_TO_SPT_2']."',
				'".$_POST['M2_BILL_TO_SPT_3']."',
				'".$_POST['M2_BILL_TO_SPT_4']."',
				'".$_POST['M2_BILL_TO_SPT_5']."',
				'".$_POST['M2_BILL_TO_SPT_6']."',

				'".$SCNo["SCNo_yy"]."',
				'".$SCNo["SCNo_mm"]."',
				'".$SCNo["SCNo_dd"]."',
				'".$SCNo["SCNo_cnt"]."',
				'".$SCNo["SCNo_else1"]."',
				'".$SCNo["SCNo_else2"]."',

				'".$m2_export_fee_table."',
				'".$m2_export_fee."',
				'".$pf_rate."',

				'".$_POST["S_STATUS"]."',

				'".$date_stmp."',
				'".$date_stmp."'
			)";
		//echo('<!--'.$StrSQL.'-->');
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}
	echo "<!--";
	var_dump($ary_div_id);
	echo "-->";

	

	// filestatusのIDを取得
	//$StrSQL="SELECT ID FROM DAT_FILESTATUS order by ID desc;";
	$StrSQL="SELECT ID FROM DAT_FILESTATUS where EDITDATE='".$date_stmp."' order by ID desc;";
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
		copy($file_dir . $_POST['FILE'], $file_dir . $key . '/' . $_POST['FILE']);
	}
	if($_POST['FILE2'] != '') {
		copy($file_dir . $_POST['FILE2'], $file_dir . $key . '/' . $_POST['FILE2']);
	}
	if($_POST['FILE3'] != '') {
		copy($file_dir . $_POST['FILE3'], $file_dir . $key . '/' . $_POST['FILE3']);
	}
	if($_POST['FILE4'] != '') {
		copy($file_dir . $_POST['FILE4'], $file_dir . $key . '/' . $_POST['FILE4']);
	}
	if($_POST['FILE5'] != '') {
		copy($file_dir . $_POST['FILE5'], $file_dir . $key . '/' . $_POST['FILE5']);
	}
	if($_POST['M1_FILE'] != '') {
		copy($file_dir . $_POST['M1_FILE'], $file_dir . $key . '/' . $_POST['M1_FILE']);
	}
	if($_POST['N_FILE'] != '') {
		copy($file_dir . $_POST['N_FILE'], $file_dir . $key . '/' . $_POST['N_FILE']);
	}
	if($_POST['N_FILE2'] != '') {
		copy($file_dir . $_POST['N_FILE2'], $file_dir . $key . '/' . $_POST['N_FILE2']);
	}
	if($_POST['N_FILE3'] != '') {
		copy($file_dir . $_POST['N_FILE3'], $file_dir . $key . '/' . $_POST['N_FILE3']);
	}
	if($_POST['N_FILE4'] != '') {
		copy($file_dir . $_POST['N_FILE4'], $file_dir . $key . '/' . $_POST['N_FILE4']);
	}
	if($_POST['N_FILE5'] != '') {
		copy($file_dir . $_POST['N_FILE5'], $file_dir . $key . '/' . $_POST['N_FILE5']);
	}
	if($_POST['N_PDF'] != '') {
		copy($file_dir . $_POST['N_PDF'], $file_dir . $key . '/' . $_POST['N_PDF']);
	}
	if($_POST['S_FILE'] != '') {
		copy($file_dir . $_POST['S_FILE'], $file_dir . $key . '/' . $_POST['S_FILE']);
	}

	// Quotationの場合
	if($type == '見積り送付' || $type=="追加見積り") {
		/* 古いものを残す(UPDATEはしない)ためDELETEは不要
		// DELETE
		$StrSQL = " DELETE FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID = ".$key.";";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
		*/

		//分割払い対応
		foreach ($ary_div_id as $div_id) {

			$tmp="";
			$tmp=explode("-", $div_id);
			echo "<!--";
			var_dump($tmp);
			echo "-->";
			$part="";
			if(count($tmp)==3){
				$part=$tmp[2];
			}
			

			// filestatusのIDを取得
			$StrSQL="SELECT ID FROM DAT_FILESTATUS where DIV_ID='".$div_id."' order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus = mysqli_fetch_assoc($rs);
			$key = $item_filestatus['ID'];
	
			// INSERT
			for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']); $detail_key++) {

				if($_POST['M2_PAY_TYPE']!="Once"){
					//分割支払いにもかかわらず、分割先の割り当てが指定されてなかった場合
					if($_POST['M2_DETAIL_SPLIT_PART'][$detail_key]=="" || 
						is_null($_POST['M2_DETAIL_SPLIT_PART'][$detail_key])){
						
						$m2_detail_split_part="Part1";

					}else{
						if($part=="Part0"){
							//分割支払い：1枚にまとめたデータ
							$m2_detail_split_part=$_POST['M2_DETAIL_SPLIT_PART'][$detail_key];
						}else if($_POST['M2_DETAIL_SPLIT_PART'][$detail_key]!=$part){
							continue;

						}else{
							$m2_detail_split_part=$_POST['M2_DETAIL_SPLIT_PART'][$detail_key];

						}
					}
				}else{
					$m2_detail_split_part="";
				}

				$StrSQL = "
					INSERT INTO DAT_FILESTATUS_DETAIL (
						FILESTATUS_ID,
	
						TITLE,
						PRICE,
						COMMENT,
						M_STATUS,
	
						M2_DETAIL_ITEM,
						M2_DETAIL_DESCRIPTION,
						M2_DETAIL_PRICE,
						M2_DETAIL_NOTE,
						M2_DETAIL_SPLIT_PART,
						M2_DETAIL_QUANTITY,
						M2_DETAIL_UNIT_PRICE,
						M2_DETAIL_SP_DISCOUNT,
						DIV_ID,
						DIV_ITEM_NO,
	
						NEWDATE,
						EDITDATE
					) VALUE (
						'".$key."',
	
						'".$_POST['M2_TITLE']."',
						'".$_POST['M2_PRICE']."',
						'".$_POST['M2_COMMENT']."',
						'".$m_status."',
	
						'".$_POST['M2_DETAIL_ITEM'][$detail_key]."',
						'".$_POST['M2_DETAIL_DESCRIPTION'][$detail_key]."',
						'".$_POST['M2_DETAIL_PRICE'][$detail_key]."',
						'".$_POST['M2_DETAIL_NOTE'][$detail_key]."',
						'".$m2_detail_split_part."',
						'".$_POST['M2_DETAIL_QUANTITY'][$detail_key]."',
						'".$_POST['M2_DETAIL_UNIT_PRICE'][$detail_key]."',
						'".$_POST['M2_DETAIL_SP_DISCOUNT'][$detail_key]."',
						'".$div_id."',
						'".$detail_key."',
	
						'".$date_stmp."',
						'".$date_stmp."'
					)";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}
		}
	}


	////「Scientist3 control No.」が設定されていたら整形
	//$SCNo_str=formatAlphabetId($SCNo);
	//$m2_quote_no="";
	//if($_POST["M2_QUOTE_NO"]!="" && !is_null($_POST["M2_QUOTE_NO"])){
	//	$m2_quote_no=$_POST['M2_QUOTE_NO'];
	//}


	//メッセージ用のサプライヤーデータ
	$StrSQL="SELECT MID,M1_DVAL01 FROM DAT_M1 WHERE MID='".$mid1."'";
	$sup_rs=mysqli_query(ConnDB(),$StrSQL);
	$sup_item=mysqli_fetch_assoc($sup_rs);

	//自分へのメッセージ (分割払い対応）
	$part0_key="";
	if($type == '見積り送付' || $type=="追加見積り") {
		foreach ($ary_div_id as $div_id) {
			// filestatusのID等を取得
			$StrSQL="SELECT * FROM DAT_FILESTATUS where DIV_ID='".$div_id."' order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus = mysqli_fetch_assoc($rs);
			$key = $item_filestatus['ID'];
			
			$tmp="";
			$tmp=explode("-", $div_id);
			//echo "<!--";
			//var_dump($tmp);
			//echo "-->";
			$part="";
			$disp_part="";
			if($item_filestatus["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
				$part=$tmp[2];
				//$disp_part="Split ".$part;
			}

			//マイルストーンの場合はPart0はユーザには表示しない
			if($item_filestatus["M2_PAY_TYPE"]=="Milestone" && $part=="Part0"){
				$part0_key=$key;
				continue;
			}

			//2回払いの場合はPart0しかユーザには表示しない
			if($item_filestatus["M2_PAY_TYPE"]=="Split" && $part!="Part0"){
				//2回払いの場合はPart0はユーザには表示しない
				continue;
			}

			//「Scientist3 control No.」が設定されていたら整形
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$m2_quote_no="";

			$SCNo_ary["SCNo_yy"]=$item_filestatus["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$item_filestatus["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$item_filestatus["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$item_filestatus["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$item_filestatus["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$item_filestatus["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);
			$m2_quote_no=$item_filestatus["M2_QUOTE_NO"];

			//マイルストーン払いの場合に、Item名も表示。
			$item_name="";
			if($item_filestatus["M2_PAY_TYPE"]=='Milestone'){
				$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL where FILESTATUS_ID='".$key."' order by ID desc;";
				//echo('<!--'.$StrSQL.'-->');
				$rs_dmile=mysqli_query(ConnDB(),$StrSQL);
				$item_dmile = mysqli_fetch_assoc($rs_dmile);
				$item_name=$item_dmile["M2_DETAIL_ITEM"];
			}

			$comment = '';
			switch($type) {
				case '見積り送付':
				if($item_filestatus["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part!="Part1"){
					//マイルストーン払いの場合に、Item名も表示。
					//Part1以外にはRevise Quotationボタンを非表示
					$comment = 'Sent a quotation
					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version .'-'.$item_name. ' '.$disp_part.'
					</a>';

				}else if($item_filestatus["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part=="Part1"){
					//マイルストーン払いの場合に、Item名も表示。
					//Part1にはRevise Quotationボタンを表示
					//Revise QuotationボタンにはPart0のkeyを使う

					$comment = 'Sent a quotation
					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version .'-'.$item_name. ' '.$disp_part.'
					</a>' . 
					'　<a href="/m_contact1/?type=見積り送付&mode=new&key='.$part0_key.'&upd_mode=1' . '" target="_top">Revise Quotation</a>
					';

				}else if($item_filestatus["M2_PAY_TYPE"]=='Split' && $part=="Part0"){
					//2回払いでPart0以外は上でcontinueしてる。
					//2回払いの場合はPart0しか表示しない。
					//2回払いの場合に、Part0にRevise Quotationボタンを表示
					$comment = 'Sent a quotation
					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version .' '.$disp_part.'
					</a>' . 
					'　<a href="/m_contact1/?type=見積り送付&mode=new&key='.$key.'&upd_mode=1' . '" target="_top">Revise Quotation</a>
					';
				
				}else if($item_filestatus["M2_PAY_TYPE"]=='Once'){
					//1回払いの場合に、Revise Quotationボタンを表示
					$comment = 'Sent a quotation
					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version .' '.$disp_part.'
					</a>' . 
					'　<a href="/m_contact1/?type=見積り送付&mode=new&key='.$key.'&upd_mode=1' . '" target="_top">Revise Quotation</a>
					';

				}else{
					//例外があったら表示のみ
					$comment = 'Sent a quotation
					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version .' '.$disp_part.'
					</a>';
				}

//				if($item_name!="" && $part!="Part0"){
//					//マイルストーン払いの場合に、Item名も表示。
//					//Part0以外にはRevise Quotationボタンを非表示
//					$comment = 'Sent a quotation
//					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version .'-'.$item_name. ' '.$disp_part.'
//					</a>';
//
//				}else if($part!="" && $part!="Part0"){
//					//Part0以外にはRevise Quotationボタンを非表示
//					$comment = 'Sent a quotation
//					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version .' '.$disp_part.'
//					</a>';
//
//				}else{
//					$comment = 'Sent a quotation
//					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//					'.$m2_quote_no.' (' . $SCNo_str . ') Version' . $m2_version . ' '.$disp_part.'
//					</a>' . 
//					'　<a href="/m_contact1/?type=見積り送付&mode=new&key='.$key.'&upd_mode=1' . '" target="_top">Revise Quotation</a>
//					';
//				}

				break;
				case '追加見積り':
				if($part!="Part0"){
					//Part0以外にはRevise Quotationボタンを非表示
					$comment = '追加見積りを送付しました
					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
						Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . ', '.$disp_part.'）
					</a>';
				}else{
					$comment = '追加見積りを送付しました
					<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
						Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . ', '.$disp_part.'）
					</a>' . 
					'　<a href="/m_contact1/?type=追加見積り&mode=new&key='.$key.'&upd_mode=1' . '" target="_top">Revise Quotation</a>
					';
				}
				break;
			}


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
		}
	}

	// 自分へのメッセージ
	$comment = '';
	switch($type) {
		case '見積り依頼':
			$comment = $_POST['M1_MESSAGE'] . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り依頼&mode=disp_frame&key='.$key.'\'\');">
				Quote Request
			</a>
			';
		break;
		case '再見積り依頼':
			$comment = $_POST['M1_MESSAGE'] . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=再見積り依頼&mode=disp_frame&key='.$key.'\'\');">
				Request for re-estimate
			</a>
			';
		break;
//		case '見積り送付':
//			$comment = 'Quotationを送付しました
//			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//				Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . '）
//			</a>' . 
//			'<a href="/m_contact1/?type=見積り送付&mode=new&key='.$key.'&upd_mode=1' . '" target="_top">Revise Quotation</a>
//			';
//		break;
//		case '追加見積り':
//		$comment = '追加見積りを送付しました
//			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
//				Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . '）
//			</a>' . 
//			'<a href="/m_contact1/?type=追加見積り&mode=new&key='.$key.'&upd_mode=1' . '" target="_top">Revise Quotation</a>
//			';
//		break;
		case '発注依頼':
			$comment = $_POST['H_COMMENT'] . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$key.'\'\');">
				Order request '.$param_div_id.'
			</a>
			';
		break;
		case 'データ納品':
			$comment = $_POST['N_MESSAGE'] . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=データ納品&mode=disp_frame&key='.$key.'\'\');">
				Delivery data '.$param_div_id.'
			</a>
			';
		break;
		case '物品納品':
			$comment = $_POST['N_MESSAGE'] . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=物品納品&mode=disp_frame&key='.$key.'\'\');">
				Report '.$param_div_id.'
			</a>
			';
		break;
		case '請求':
			$comment = $_POST['S_MESSAGE'] . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$key.'\'\');">
				Invoice '.$param_div_id.'
			</a>
			';
		break;
		case '見積りの辞退':
			$comment = 'declined the request for quotation<br>
			Reason：'.$_POST['COMMENT'].'<br>
			';
		break;
		case 'サプライヤーキャンセル承認':
		$comment = 'キャンセル依頼を承認しました<br>
		';
		break;
		case 'サプライヤーキャンセル否認':
		$comment = 'キャンセル依頼を否認しました<br>
		';
		break;
		case 'キャンセル承認':
		$comment = 'キャンセル依頼を承認しました<br>
		';
		break;
	}

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



	//相手へのメッセージ (分割払い対応）
	$part0_key="";
	if($type == '見積り送付' || $type=="追加見積り") {

		foreach ($ary_div_id as $div_id) {
			// filestatusのIDを取得
			$StrSQL="SELECT * FROM DAT_FILESTATUS where DIV_ID='".$div_id."' order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus = mysqli_fetch_assoc($rs);
			$key = $item_filestatus['ID'];
			
			$tmp="";
			$tmp=explode("-", $div_id);
			//echo "<!--";
			//var_dump($tmp);
			//echo "-->";
			$part="";
			$disp_part="";
			if($item_filestatus["M2_PAY_TYPE"]!='Once' && count($tmp)==3){
				$part=$tmp[2];
				//$disp_part="分割払い".$part;
			}

			//マイルストーンの場合はPart0はユーザには表示しない
			if($item_filestatus["M2_PAY_TYPE"]=="Milestone" && $part=="Part0"){
				$part0_key=$key;
				continue;
			}

			//2回払いの場合はPart0しかユーザには表示しない
			if($item_filestatus["M2_PAY_TYPE"]=="Split" && $part!="Part0"){
				//2回払いの場合はPart0はユーザには表示しない
				continue;
			}

			//「Scientist3 control No.」が設定されていたら整形
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$m2_quote_no="";

			$SCNo_ary["SCNo_yy"]=$item_filestatus["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$item_filestatus["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$item_filestatus["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$item_filestatus["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$item_filestatus["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$item_filestatus["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);
			$m2_quote_no=$item_filestatus["M2_QUOTE_NO"];

			//マイルストーン払いの場合に、Item名も表示。
			$item_name="";
			if($item_filestatus["M2_PAY_TYPE"]=='Milestone'){
				$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL where FILESTATUS_ID='".$key."' order by ID desc;";
				//echo('<!--'.$StrSQL.'-->');
				$rs_dmile=mysqli_query(ConnDB(),$StrSQL);
				$item_dmile = mysqli_fetch_assoc($rs_dmile);
				$item_name=$item_dmile["M2_DETAIL_ITEM"];
			}


			$comment = '';
			switch($type) {
				case '見積り送付':
				if($m_status=="手数料追加" || $m_status=="手数料追加(前払い)"){
					$comment="";
				}else{
					if($item_filestatus["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part!="Part1"){
						//マイルストーン払いの場合に、Item名も表示。
						//Part1以外には再見積り依頼ボタンを非表示
						$comment = '見積を受信しました
						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.'-'.$item_name.' '.$disp_part.'
						</a>';

					}else if($item_filestatus["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part=="Part1"){
						//マイルストーン払いの場合に、Item名も表示。
						//Part1には再見積り依頼ボタンを表示
						//再見積り依頼ボタンにはPart0のkeyを使う
	
						$comment = '見積を受信しました
						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.'-'.$item_name.' '.$disp_part.'
						</a>' . 
						'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$part0_key.'" target="_top">再見積りを依頼する</a>
						';
	
					}else if($item_filestatus["M2_PAY_TYPE"]=='Split' && $part=="Part0"){
						//2回払いでPart0以外は上でcontinueしてる。
						//2回払いの場合はPart0しか表示しない。
						//2回払いの場合に、Part0に再見積り依頼ボタンを表示
						$comment = '見積を受信しました
						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
						</a>' . 
						'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$key.'" target="_top">再見積りを依頼する</a>
						';
					
					}else if($item_filestatus["M2_PAY_TYPE"]=='Once'){
						//1回払いの場合に、再見積り依頼ボタンを表示
						$comment = '見積を受信しました
						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
						</a>' . 
						'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$key.'" target="_top">再見積りを依頼する</a>
						';
	
					}else{
						//例外があったら表示のみ
						$comment = '見積を受信しました
						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
						</a>';
					}
				}

//				if($m_status=="手数料追加"){
//					$comment="";
//				}else{
//					if($item_name!="" && $part!="Part0"){
//						//マイルストーン払いの場合に、Item名も表示。
//						//Part0以外にはRevise Quotationボタンを非表示
//						$comment = '見積を受信しました
//						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.'-'.$item_name.' '.$disp_part.'
//						</a>';
//					}else if($part!="" && $part!="Part0"){
//						//Part0以外にはRevise Quotationボタンを非表示
//						$comment = '見積を受信しました
//						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
//						</a>';
//					}else{
//						$comment = '見積を受信しました
//						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//							'.$m2_quote_no.' ('.$SCNo_str.') Version'.$m2_version.' '.$disp_part.'
//						</a>' . 
//						'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$key.'" target="_top">再見積りを依頼する</a>
//						';
//					}
//				}


				break;
				case '追加見積り':
				if($m_status=="手数料追加" || $m_status=="手数料追加"){
					$comment="";
				}else{
					if($part!="Part0"){
						//Part0以外にはRevise Quotationボタンを非表示
						$comment = '追加見積りが送付されました
						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
							Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . ', '.$disp_part.'）</a>';
					}else{
						$comment = '追加見積りが送付されました
						<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
							Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . ', '.$disp_part.'）</a>' . 
						'　<a href="/m_contact2/?type=再見積り依頼&mode=new&key='.$key.'" target="_top">Request a re-quote</a>
						';
					}
					//$comment = '追加見積りが送付されました
					//<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
					//	Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . ', '.$disp_part.'）</a>' . 
					//'　<a href="/m_contact2/?type=再見積り依頼&mode=new&m2_id='.$m2_id.'&m2_version='.$m2_version.'" target="_top">Request a re-quote</a>
					//';
				}
				break;
			}

			// DAT_MESSAGE
			// 水際英訳
			$comment = showStatusAllLong($comment);

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
					'".$mid2."',
					'".$key."'
					)
					";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}
		}
	}



	// 相手へのメッセージ
	$comment = '';
	switch($type) {
		case '見積り依頼':
			$comment = $_POST['M1_MESSAGE'] . '
			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り依頼&mode=disp_frame&key='.$key.'\'\');">
				Quote Request
			</a>
		';
		break;
		case '再見積り依頼':
		$comment = $_POST['M1_MESSAGE'] . '
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=再見積り依頼&mode=disp_frame&key='.$key.'\'\');">
			Request for re-estimate
		</a>
		';
		break;
//		case '見積り送付':
//		if($m_status=="手数料追加"){
//			$comment="";
//		}else{
//			$comment = 'Quotationが送付されました
//			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">
//				Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . '）
//			</a>' . 
//			'　<a href="/m_contact2/?type=再見積り依頼&mode=new&m2_id='.$m2_id.'&m2_version='.$m2_version.'" target="_top">Request a re-quote</a>
//			';
//		}
//		break;
//		case '追加見積り':
//		if($m_status=="手数料追加"){
//			$comment="";
//		}else{
//			$comment = '追加見積りが送付されました
//			<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=追加見積り&mode=disp_frame&key='.$key.'\'\');">
//				Revise Quotation for Control Number' . $m2_id . '（Version.' . $m2_version . '）
//			</a>' . 
//			'　<a href="/m_contact2/?type=再見積り依頼&mode=new&m2_id='.$m2_id.'&m2_version='.$m2_version.'" target="_top">Request a re-quote</a>
//			';
//		}
//		break;
		case '発注依頼':
		$comment = $_POST['H_COMMENT'] . '
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$key.'\'\');">
			Order request '.$param_div_id.'
		</a>
		';
		break;
		case 'データ納品':
		$comment = $_POST['N_MESSAGE'] . '
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=データ納品&mode=disp_frame&key='.$key.'\'\');">
			Delivery data '.$param_div_id.'
		</a>
		';
		break;
		case '物品納品':
		$comment = $_POST['N_MESSAGE'] . '
		<a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=物品納品&mode=disp_frame&key='.$key.'\'\');">
			Report '.$param_div_id.'
		</a>
		';
		break;
		case '見積りの辞退':
			$comment = '見積り依頼が拒否されました<br>
			理由：'.$_POST['COMMENT'].'<br>
			';
		break;
		case 'サプライヤーキャンセル承認':
		$comment = $sup_item["M1_DVAL01"].'がキャンセル依頼を承認しました。キャンセル手続きが完了するまでお待ちください。<br>
		';
		break;
		case 'サプライヤーキャンセル否認':
		$comment = $sup_item["M1_DVAL01"].'がキャンセル依頼を否認しました<br>
		';
		break;
		case 'キャンセル承認':
		$comment = $sup_item["M1_DVAL01"].'がキャンセル依頼を承認しました。キャンセル手続きが完了するまでお待ちください。<br>
		';
		break;
		// Supplierからの請求書はResearchersには見せない
		/*
		case '請求':
			$comment = $_POST['S_MESSAGE'] . '
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$key.'\'\');">Invoice</a>
			';
  	  break;
		*/
	}

	// 水際英訳
	$comment = showStatusAllLong($comment);

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
				'".$mid2."',
				'".$key."'
			)
		";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}


	} // 複数Supplierのforeach

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


	//$typeが「見積り送付」で、運営手数料追加モードでない場合
	//or $typeが「追加見積り」の場合。
	//現在、$typeが見積り送付できて、運営手数料追加の条件じゃないと判断された場合
	//$statusは「見積り送付」
	if($key!=""){
		if($status=="見積り送付" || $status=="追加見積り"){
			SendMail_v1($key);
			SendMail_v1_2($key);
			SendMail_v1_3($key);
		}
	}
	



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
