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

	echo "<!--session:\n";
	var_dump($_SESSION);
	echo "-->";

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
	}

	if ($mode==""){
		$mode="new";
	}
	if ($type==""){
		$mode="1";
	}

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
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m1_list = array();
	$mid1_list = '';
	while ($item = mysqli_fetch_assoc($rs)) {
		if($m1_mid != '') {
			$m1_id = $item['ID'];
		}
		$m1_list[] = $item;
		$mid1_list .= ($mid1_list != '' ? ',' : '') . $item['MID'];
	}

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

			$StrSQL="SELECT * from DAT_M2 where MID = '".$_SESSION['MID']."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$m2 = mysqli_fetch_assoc($rs);

			// TODO
			//SendMail($m1_list,$m2);

			// DAT_SHODANとDAT_MESSAGEに登録する
			SaveData($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$m2,$word,$word2,$mid_list,$param_div_id);

			break;
		case "save2":
			RequestData($obj,$a,$b,$key,$mode);
			SaveData2($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$word,$word2,$mid_list);
			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			break;
	} 

	DispData($type,$mode,$sort,$word,$word2,$mid_list,$m1_id,$m1_mid,$key,$shodan_id,$page,$lid,$m1_list,$mid1_list,$chk,$param_div_id);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($m1_list,$m2)
{

	eval(globals());

	//$fp="./contact_mail.txt";
	//$MailBody=@file_get_contents($fp);
	$maildata = GetMailTemplate('サプライヤーへの問合せ');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-TITLE]",$FieldValue[4],$MailBody);
	$MailBody=str_replace("[D-COMMENT]",$FieldValue[5],$MailBody);
	$MailBody=str_replace("[D-KIGEN]",$FieldValue[7],$MailBody);

	foreach($m1_list as $item) {
		$mailto = $item['EMAIL'];
		//$mailto = '197583@gmail.com';

		$MailBody=str_replace("[D-NAME]",$item['M1_DVAL01'],$MailBody);

		// 研究者情報
		$MailBody=str_replace("[M2_DVAL03]",$m2['M2_DVAL03'],$MailBody);
		$MailBody=str_replace("[M2_DVAL01]",$m2['M2_DVAL01'],$MailBody);
		$MailBody=str_replace("[M2_EMAIL]",$m2['EMAIL'],$MailBody);


		$MailBody2 = "--__BOUNDARY__\n";
		$MailBody2 .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
		$MailBody2 .= $MailBody . "\n";
		$MailBody2 .=	"--__BOUNDARY__\n";

		if($FieldValue[6] != '') {
			$file = $FieldValue[6];

			$MailBody2 .= "Content-Type: application/octet-stream; name=\"{$file}\"\n";
			$MailBody2 .= "Content-Disposition: attachment; filename=\"{$file}\"\n";
			$MailBody2 .= "Content-Transfer-Encoding: base64\n";
			$MailBody2 .= "\n";
			$MailBody2 .= chunk_split(base64_encode(file_get_contents("data/".$file)));
			$MailBody2 .=	"--__BOUNDARY__\n";
		}

		mb_language("Japanese");
		mb_internal_encoding("UTF-8");

		mb_send_mail($mailto, $subject, $MailBody2, "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\nFrom:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	}
}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail2($type,$mid2,$comment)
{


	eval(globals());


	$StrSQL="SELECT * from DAT_M2 where MID = '".$mid2."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m2 = mysqli_fetch_assoc($rs);

	$StrSQL="SELECT * from DAT_M3 where MID = '".$m2["M2_DVAL15"]."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$m3 = mysqli_fetch_assoc($rs);

	$maildata = GetMailTemplate('発注依頼承認(M2)');
	if($type == '発注承認'){
		//echo "<!--発注依頼承認M2-->";
		$maildata = GetMailTemplate('発注依頼承認(M2)');
	} 
	if($type == '発注否認'){
		//echo "<!--発注依頼否認M2-->";
		$maildata = GetMailTemplate('発注依頼否認(M2)');
	} 
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-NAME]",$m2['M2_DVAL01'],$MailBody);
	$MailBody=str_replace("[COMMENT]",$comment,$MailBody);
	$mailto = $m2['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	//echo "<!--mailtoM2:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 


	
	$maildata = GetMailTemplate('発注依頼承認(M3)');
	if($type == '発注承認'){
		//echo "<!--発注依頼承認M3-->";
		$maildata = GetMailTemplate('発注依頼承認(M3)');
	} 
	if($type == '発注否認'){
		//echo "<!--発注依頼否認M3-->";
		$maildata = GetMailTemplate('発注依頼否認(M3)');
	} 
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$MailBody=str_replace("[D-NAME]",$m3['M2_DVAL01'],$MailBody);
	$MailBody=str_replace("[COMMENT]",$comment,$MailBody);
	$mailto = $m3['EMAIL'];
	// $mailto = "toretoresansan00@gmail.com";
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	//echo "<!--mailtoM3:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		
	

}
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispData($type,$mode,$sort,$word,$word2,$mid_list,$m1_id,$m1_mid,$key,$shodan_id,$page,$lid,$m1_list,$mid1_list,$chk,$param_div_id)
{

	eval(globals());

	$html_prev = 'contact';
	switch($type) {
		case '発注承認':
			$html_prev = 'h3a';
			break;
		case '発注否認':
			$html_prev = 'h3b';
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
	$str=str_replace("[SHODAN_ID]",$shodan_id,$str);
	$str=str_replace("[LID]",$lid,$str);

	$str=str_replace("[MID1_LIST]",$mid1_list,$str);

	$str=str_replace("[CHK]",$chk,$str);

	
	//分割支払い対応
	//分割識別ID
	$str=str_replace("[DIV_ID]",$param_div_id,$str);
	$str=str_replace("[D-DIV_ID]",$param_div_id,$str);


	// typeごとの違い
	switch($type) {
		case '発注承認':
			$str=str_replace("[PAGE_TITLE]",'発注承認',$str);
			break;
		case '発注否認':
			$str=str_replace("[PAGE_TITLE]",'発注否認',$str);
			break;
	}

  // ファイルアップロード
	if($mode == 'saveconf' || $mode == 'preview' || $mode == 'saveconf2') {
		foreach($_FILES as $file_key => $file_val) {
			$filename = $_FILES[$file_key]["name"];
			move_uploaded_file($_FILES[$file_key]["tmp_name"], __dir__."/../a_filestatus/data/".$filename);
		}
	}

	if($mode == 'saveconf' || $mode == 'preview' || $mode == 'saveconf2' || $mode == 'back') {
//echo "<!--1a-->";
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
		$str=str_replace("[D-KIGEN]",$_POST['KIGEN'],$str);

		$str=str_replace("[D-M1_MESSAGE]",str_replace("\n", '<br>', $_POST['M1_MESSAGE']),$str); // Dはbr変換
		$str=str_replace("[D-M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$str);
		$str=str_replace("[D-M1_TRANS_FLG_あり]",($_POST['M1_TRANS_FLG'] == 'あり' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_FLG_なし]",($_POST['M1_TRANS_FLG'] == 'なし' ? 'checked' : ''),$str);
		$str=str_replace("[D-M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$str);
		$str=str_replace("[D-M1_PRICE]",$_POST['M1_PRICE'],$str);
		$str=str_replace("[D-M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$str);
		$str=str_replace("[D-M1_KIGEN]",$_POST['M1_KIGEN'],$str);

		$str=str_replace("[D-M2_TITLE]",$_POST['M2_TITLE'],$str);
		$str=str_replace("[D-M2_PRICE]",$_POST['M2_PRICE'],$str);
		$str=str_replace("[D-M2_COMMENT]",str_replace("\n", '<br>', $_POST['M2_COMMENT']),$str); // Dはbr変換

		// 新見積り書
		// -------------------------------------------------------------------------------
		if($type == '発注依頼') {
		  // DBからデータ取得
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$_POST['H_M2_ID'].";";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus2 = mysqli_fetch_assoc($rs);

			$str=str_replace("[D-M2_NOHIN_TYPE]",$item_filestatus2['M2_NOHIN_TYPE'],$str);
			$str=str_replace("[D-M2_PAY_TYPE]",$item_filestatus2['M2_PAY_TYPE'],$str);
			$str=str_replace("[D-M2_STUDY_CODE]",$item_filestatus2['M2_STUDY_CODE'],$str);
			$str=str_replace("[D-M2_DATE]",$item_filestatus2['M2_DATE'],$str);
			$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$item_filestatus2['M2_QUOTE_VALID_UNTIL'],$str);
			$str=str_replace("[D-M2_DESCRIPTION]",$item_filestatus2['M2_DESCRIPTION'],$str);
			$str=str_replace("[D-M2_CURRENCY]",$item_filestatus2['M2_CURRENCY'],$str);
			$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$item_filestatus2['M2_SPECIAL_DISCOUNT'],$str);
			$str=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $item_filestatus2['M2_SPECIAL_NOTE']),$str); // Dはbr変換

			$str=str_replace("[D-H_M2_ID]",$item_filestatus2['M2_ID'] . '（バージョン' . $item_filestatus2['M2_VERSION'] . '）',$str);

			$detail_template = '
            <div class="formset__item formset__item-head">
              <div class="formset__ttl2"><strong>Details XXX</strong></div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Item #</div>
              <div class="formset__input">[D-M2_DETAIL_ITEM_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">内容<span class="formset__must">Mandatory</span></div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price<span class="formset__must">Mandatory</span></div>
              <div class="formset__input">[D-M2_DETAIL_PRICE_XXX]</div>
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

				$add_detail_area=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_ITEM'],$add_detail_area);
				$add_detail_area=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_DESCRIPTION']),$add_detail_area); // Dはbr変換
				$add_detail_area=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$item_filestatus_detail['M2_DETAIL_PRICE'],$add_detail_area);
				$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換

				$detail_key++;
			}
			$str=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$str);
		}
		else {

		$str=str_replace("[D-M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$str);
		$str=str_replace("[D-M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$str);

		$str=str_replace("[D-M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$str);
		$str=str_replace("[D-M2_DATE]",$_POST['M2_DATE'],$str);
		$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[D-M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$str);
		$str=str_replace("[D-M2_CURRENCY]",$_POST['M2_CURRENCY'],$str);

		$detail_template = '
            <div class="formset__item formset__item-head">
              <div class="formset__ttl2"><strong>Details XXX</strong></div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Item #</div>
              <div class="formset__input">[D-M2_DETAIL_ITEM_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">内容<span class="formset__must">Mandatory</span></div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price<span class="formset__must">Mandatory</span></div>
              <div class="formset__input">[D-M2_DETAIL_PRICE_XXX]</div>
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
			$str=str_replace("[D-M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_DESCRIPTION_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_DESCRIPTION'][$detail_key]),$str); // Dはbr変換
			$str=str_replace("[D-M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$str);
			$str=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $_POST['M2_DETAIL_NOTE'][$detail_key]),$str); // Dはbr変換
		}

		$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE]",str_replace("\n", '<br>', $_POST['M2_SPECIAL_NOTE']),$str); // Dはbr変換

		}
		// -------------------------------------------------------------------------------

		$str=str_replace("[D-H_M2_ID]",$_POST['H_M2_ID'],$str);

		$str=str_replace("[D-H_COMMENT]",str_replace("\n", '<br>', $_POST['H_COMMENT']),$str); // Dはbr変換

		$str=str_replace("[D-N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[D-N_MESSAGE]",str_replace("\n", '<br>', $_POST['N_MESSAGE']),$str); // Dはbr変換

		$str=str_replace("[D-S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[D-S_MESSAGE]",str_replace("\n", '<br>', $_POST['S_MESSAGE']),$str); // Dはbr変換
		$str=str_replace("[D-H3A_MESSAGE]",str_replace("\n", '<br>', $_POST['H3A_MESSAGE']),$str); // Dはbr変換
		$str=str_replace("[D-H3B_MESSAGE]",str_replace("\n", '<br>', $_POST['H3B_MESSAGE']),$str); // Dはbr変換


		$str=str_replace("[TITLE]",$_POST['TITLE'],$str);
		$str=str_replace("[COMMENT]",$_POST['COMMENT'],$str);
		$str=str_replace("[FILE]",(isset($_POST['FILE']) ? $_POST['FILE'] : $_FILES['FILE']['name']),$str);
		$str=str_replace("[KIGEN]",$_POST['KIGEN'],$str);

		$str=str_replace("[M1_MESSAGE]",$_POST['M1_MESSAGE'],$str);
		$str=str_replace("[M1_TRANS_FLG]",$_POST['M1_TRANS_FLG'],$str);
		$str=str_replace("[M1_TRANS_TXT]",$_POST['M1_TRANS_TXT'],$str);
		$str=str_replace("[M1_PRICE]",$_POST['M1_PRICE'],$str);
		$str=str_replace("[M1_FILE]",(isset($_POST['M1_FILE']) ? $_POST['M1_FILE'] : $_FILES['M1_FILE']['name']),$str);
		$str=str_replace("[M1_KIGEN]",$_POST['M1_KIGEN'],$str);

		$str=str_replace("[M2_TITLE]",$_POST['M2_TITLE'],$str);
		$str=str_replace("[M2_PRICE]",$_POST['M2_PRICE'],$str);
		$str=str_replace("[M2_COMMENT]",$_POST['M2_COMMENT'],$str);

		// 新見積り書
		// -------------------------------------------------------------------------------
		$str=str_replace("[M2_NOHIN_TYPE]",implode(',', $_POST['M2_NOHIN_TYPE']),$str);
		$str=str_replace("[M2_PAY_TYPE]",$_POST['M2_PAY_TYPE'],$str);

		$str=str_replace("[M2_STUDY_CODE]",$_POST['M2_STUDY_CODE'],$str);
		$str=str_replace("[M2_DATE]",$_POST['M2_DATE'],$str);
		$str=str_replace("[M2_QUOTE_VALID_UNTIL]",$_POST['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[M2_DESCRIPTION]",$_POST['M2_DESCRIPTION'],$str);
		$str=str_replace("[M2_CURRENCY]",$_POST['M2_CURRENCY'],$str);

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
		$str=str_replace("[HIDDEN_DETAIL_AREA]",$hidden_detail_area,$str);

		for($detail_key = 0; $detail_key < count($_POST['M2_DETAIL_ITEM']) - 1; $detail_key++) {
			$detail_no = $detail_key + 1;
			$str=str_replace("[M2_DETAIL_ITEM_".$detail_no."]",$_POST['M2_DETAIL_ITEM'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_DESCRIPTION_".$detail_no."]",$_POST['M2_DETAIL_DESCRIPTION'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_PRICE_".$detail_no."]",$_POST['M2_DETAIL_PRICE'][$detail_key],$str);
			$str=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",$_POST['M2_DETAIL_NOTE'][$detail_key],$str);
		}

		$str=str_replace("[M2_SPECIAL_DISCOUNT]",$_POST['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[M2_SPECIAL_NOTE]",$_POST['M2_SPECIAL_NOTE'],$str);
		// -------------------------------------------------------------------------------

		$str=str_replace("[H_M2_ID]",$_POST['H_M2_ID'],$str);
		$str=str_replace("[H_COMMENT]",$_POST['H_COMMENT'],$str);

		$str=str_replace("[N_FILE]",(isset($_POST['N_FILE']) ? $_POST['N_FILE'] : $_FILES['N_FILE']['name']),$str);
		$str=str_replace("[N_MESSAGE]",$_POST['N_MESSAGE'],$str);

		$str=str_replace("[S_FILE]",(isset($_POST['S_FILE']) ? $_POST['S_FILE'] : $_FILES['S_FILE']['name']),$str);
		$str=str_replace("[S_MESSAGE]",$_POST['S_MESSAGE'],$str);
		$str=str_replace("[H3A_MESSAGE]",$_POST['H3A_MESSAGE'],$str);
		$str=str_replace("[H3B_MESSAGE]",$_POST['H3B_MESSAGE'],$str);


		if($type == '発注依頼' || $type == '見積り送付') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='見積り送付' order by ID desc;";
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
	else if($key || ($shodan_id != '' && $type == '問い合わせ')) {
//echo "<!--2a-->";
    if($key == '') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='問い合わせ';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item_filestatus0 = mysqli_fetch_assoc($rs);
			$key = $item_filestatus0['ID'];
			$str=str_replace("[KEY]",$key,$str);
		}

	  // DBからデータ取得
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_filestatus = mysqli_fetch_assoc($rs);

		$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$item_filestatus['SHODAN_ID'].";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		$word = $item_shodan['CATEGORY'];
		$word2 = $item_shodan['KEYWORD'];

		$str=str_replace("[D-TITLE]",$item_shodan['TITLE'],$str);
		$str=str_replace("[D-COMMENT]",str_replace("\n", '<br>', $item_filestatus['T_COMMENT']),$str); // Dはbr変換
		$str=str_replace("[D-FILE]",$item_filestatus['T_FILE'],$str);
		$str=str_replace("[D-FILE2]",$item_filestatus['T_FILE2'],$str);
		$str=str_replace("[D-FILE3]",$item_filestatus['T_FILE3'],$str);
		$str=str_replace("[D-FILE4]",$item_filestatus['T_FILE4'],$str);
		$str=str_replace("[D-FILE5]",$item_filestatus['T_FILE5'],$str);
		$str=str_replace("[D-KIGEN]",$item_filestatus['T_ANSWERDATE'],$str);

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
		$str=str_replace("[D-M2_STUDY_CODE]",$item_filestatus['M2_STUDY_CODE'],$str);
		$str=str_replace("[D-M2_DATE]",$item_filestatus['M2_DATE'],$str);
		$str=str_replace("[D-M2_QUOTE_VALID_UNTIL]",$item_filestatus['M2_QUOTE_VALID_UNTIL'],$str);
		$str=str_replace("[D-M2_DESCRIPTION]",$item_filestatus['M2_DESCRIPTION'],$str);
		$str=str_replace("[D-M2_CURRENCY]",$item_filestatus['M2_CURRENCY'],$str);
		$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",$item_filestatus['M2_SPECIAL_DISCOUNT'],$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE]",$item_filestatus['M2_SPECIAL_NOTE'],$str);

		$str=str_replace("[M2_NOHIN_TYPE]",$item_filestatus['M2_NOHIN_TYPE'],$str);
		$str=str_replace("[M2_PAY_TYPE]",$item_filestatus['M2_PAY_TYPE'],$str);
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
              <div class="formset__ttl">内容<span class="formset__must">Mandatory</span></div>
              <div class="formset__input">[D-M2_DETAIL_DESCRIPTION_XXX]</div>
            </div>
            <div class="formset__item">
              <div class="formset__ttl">Price<span class="formset__must">Mandatory</span></div>
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
			$add_detail_area=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",str_replace("\n", '<br>', $item_filestatus_detail['M2_DETAIL_NOTE']),$add_detail_area); // Dはbr変換

			$detail_key++;
		}
		$str=str_replace("[ADD_DETAIL_AREA]",$add_detail_area,$str);
		// -------------------------------------------------------------------------------

		if($type == '見積り送付') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
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
			}
			$str=str_replace("[H_M2_LIST]",$h_m2_list,$str);
			$str=str_replace("[H_M2_DETAIL]",json_encode($h_m2_detail),$str);
		}

		$str=str_replace("[D-H_M2_ID]",$item_filestatus['H_M2_ID'],$str);
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
		$str=str_replace("[D-H3A_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['H3A_MESSAGE']),$str); // Dはbr変換
		$str=str_replace("[D-H3B_MESSAGE]",str_replace("\n", '<br>', $item_filestatus['H3B_MESSAGE']),$str); // Dはbr変換

		
		$str=str_replace("[TITLE]",$item_shodan['TITLE'],$str);
		$str=str_replace("[COMMENT]",$item_filestatus['T_COMMENT'],$str);
		$str=str_replace("[FILE]",$item_filestatus['T_FILE'],$str);
		$str=str_replace("[FILE2]",$item_filestatus['T_FILE2'],$str);
		$str=str_replace("[FILE3]",$item_filestatus['T_FILE3'],$str);
		$str=str_replace("[FILE4]",$item_filestatus['T_FILE4'],$str);
		$str=str_replace("[FILE5]",$item_filestatus['T_FILE5'],$str);
		$str=str_replace("[KIGEN]",$item_filestatus['T_ANSWERDATE'],$str);

		$str=str_replace("[M1_MESSAGE]",$item_filestatus['M1_MESSAGE'],$str);
		$str=str_replace("[M1_TRANS_FLG]",$item_filestatus['M1_TRANS_FLG'],$str);
		$str=str_replace("[M1_TRANS_TXT]",$item_filestatus['M1_TRANS_TXT'],$str);
		$str=str_replace("[M1_PRICE]",$item_filestatus['M1_PRICE'],$str);
		$str=str_replace("[M1_FILE]",$item_filestatus['M1_FILE'],$str);
		$str=str_replace("[M1_KIGEN]",$item_filestatus['M1_KIGEN'],$str);

		$str=str_replace("[H_M2_ID]",$item_filestatus['H_M2_ID'],$str);
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
		$str=str_replace("[H3A_MESSAGE]",$item_filestatus['H3A_MESSAGE'],$str);
		$str=str_replace("[H3B_MESSAGE]",$item_filestatus['H3B_MESSAGE'],$str);


		$h_m2_detail = array();
		if($type == '発注依頼') {
			//$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$item_filestatus['H_M2_ID']." and STATUS='見積り送付' order by ID desc;";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key." and STATUS='見積り送付' order by ID desc;";
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
	}
	else if($shodan_id) {
//echo "<!--3a-->";
		$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_shodan = mysqli_fetch_assoc($rs);

		$str=str_replace("[D-TITLE]",$item_shodan['TITLE'],$str);

		if($type == '発注依頼' || $type == '見積り送付') {
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE SHODAN_ID=".$shodan_id." and STATUS='見積り送付' order by ID desc;";
			//echo('<!--'.$StrSQL.'-->');
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$h_m2_list = '';
			$h_m2_detail = array();
			while($item_filestatus = mysqli_fetch_assoc($rs)) {
				//$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $item_filestatus['ID'] . '</option>';
				$h_m2_list .= '<option value="' . $item_filestatus['ID'] . '">' . $item_filestatus['M2_ID'] . '（バージョン' . $item_filestatus['M2_VERSION'] . '）</option>';

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
		}

	}
		$str=str_replace("[D-TITLE]",'',$str);
		$str=str_replace("[D-COMMENT]",'',$str);
		$str=str_replace("[D-FILE]",'',$str);
		$str=str_replace("[D-KIGEN]",'',$str);

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
		$str=str_replace("[D-M2_DETAIL_NOTE_".$detail_no."]",'',$str);

		$str=str_replace("[D-M2_SPECIAL_DISCOUNT]",'',$str);
		$str=str_replace("[D-M2_SPECIAL_NOTE]",'',$str);
		// -------------------------------------------------------------------------------

		$str=str_replace("[D-H_M2_ID]",'',$str);
		$str=str_replace("[D-H_COMMENT]",'',$str);

		$str=str_replace("[D-N_FILE]",'',$str);
		$str=str_replace("[D-N_MESSAGE]",'',$str);

		$str=str_replace("[D-S_FILE]",'',$str);
		$str=str_replace("[D-S_MESSAGE]",'',$str);
		$str=str_replace("[D-H3A_MESSAGE]",'',$str);
		$str=str_replace("[D-H3B_MESSAGE]",'',$str);


		$str=str_replace("[TITLE]",'',$str);
		$str=str_replace("[COMMENT]",'',$str);
		$str=str_replace("[FILE]",'',$str);
		$str=str_replace("[KIGEN]",'',$str);

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
		$str=str_replace("[M2_DETAIL_NOTE_".$detail_no."]",'',$str);

		$str=str_replace("[M2_SPECIAL_DISCOUNT]",'',$str);
		$str=str_replace("[M2_SPECIAL_NOTE]",'',$str);
		// -------------------------------------------------------------------------------
	
		$str=str_replace("[H_M2_ID]",'',$str);
		$str=str_replace("[H_COMMENT]",'',$str);

		$str=str_replace("[N_FILE]",'',$str);
		$str=str_replace("[N_MESSAGE]",'',$str);

		$str=str_replace("[S_FILE]",'',$str);
		$str=str_replace("[S_MESSAGE]",'',$str);
		$str=str_replace("[H3A_MESSAGE]",'',$str);
		$str=str_replace("[H3B_MESSAGE]",'',$str);


	$word_view = '';
	if($word != '') {
		$word3 = explode(',', $word);
		foreach($word3 as $word3_row) {
			if($word3_row == '') {
				continue;
			}
			$word4 = explode(':', $word3_row);
			$word_view .= $word4[1] . '<br>';
		}
	}
	if($word2 != '') {
		$word_view .= $word2 . '<br>';
	}
	$str=str_replace("[WORD_VIEW]",$word_view,$str);

	if($type == '問い合わせ' || $type == '見積り依頼') {
		foreach($m1_list as $item) {
			$m1_view .= $item['M1_DVAL01'] . '<br>';
		}
	}
	else if($item_filestatus) {
		foreach($m1_list as $item) {
			if($item['MID'] == $item_filestatus['MID1']) {
			  $m1_view .= $item['M1_DVAL01'];
			}
		}
	}
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
	$now=strtotime("now");
	$date_stmp=date('Y/m/d H:i:s',$now);

	// 一時保存

	// 商談
	if($shodan_id != '') {
		// 他のステータスに変更するだけ
		$StrSQL = "
		UPDATE DAT_SHODAN SET
			EDITDATE = '".$date_stmp."',
			TITLE = '".$_POST['TITLE']."',
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
			TITLE = '".$_POST['TITLE']."',
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
			'".$_POST['TITLE']."',
			'".$mid1_list."',
			'".$word."',
			'".$word2."',
			'".$_POST['COMMENT']."',
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
	$StrSQL="SELECT ID FROM DAT_SHODAN where EDITDATE='".$date_stmp."' order by ID desc;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$shodan_id = $item['ID'];

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

			'".$_POST['COMMENT']."',
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
	if($_POST['M1_FILE'] != '') {
		copy($file_dir . $_POST['M1_FILE'], $file_dir . $key . '/' . $_POST['M1_FILE']);
	}
	if($_POST['N_FILE'] != '') {
		copy($file_dir . $_POST['N_FILE'], $file_dir . $key . '/' . $_POST['N_FILE']);
	}
	if($_POST['S_FILE'] != '') {
		copy($file_dir . $_POST['S_FILE'], $file_dir . $key . '/' . $_POST['S_FILE']);
	}

	} // 複数サプライヤーのforeach

}


//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SaveData($type,$mode,$key,$shodan_id,$m1_list,$mid1_list,$m2,$word,$word2,$mid_list,$param_div_id)
{
	eval(globals());

	$now=strtotime("now");
	$date_stmp=date('Y/m/d H:i:s',$now);

	// １．商談を作成してIDを取得
  // ２．ファイルステータスを作成
	// ３・メッセージ作成

	$status = '';
	$c_status = '';
	$status_sort = '';
	switch($type) {
		case '発注承認':
			// サプライヤーによる受注承認ステップが不要とのことなので
			// 決済者発注承認というステータスはなくなりました。
			//20240311 決済者発注承認ステータス復活
			//$status = '発注承認';
			// $status = '受注承認';
			$status='決済者発注承認';
			$c_status = '実施中';
			$status_sort = '6';
  	  break;
		case '発注否認':
			$status = '発注否認';
			$c_status = '見積り';
			$status_sort = '94';
  	  break;
	}


	$h_div_id="";
	if($type=="発注承認" || $type=="発注否認"){
		//発注以降、一括払い時にも扱いを一律にするために、便宜上DIV_IDを設定するようにした。
		//最新の発注依頼をとってきて、その発注依頼の対象の見積り送付データをとってきて、1括払いの時に、$h_div_idを設定
		//発注は1商談内に1つしか存在しない仕様と決定したが、念のため最新の発注依頼をとってくるようにしている。

		if($shodan_id!=""){
			$StrSQL="SELECT ID, H_M2_ID FROM DAT_FILESTATUS where SHODAN_ID='".$shodan_id."' ";
			//$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
			$StrSQL.=" and STATUS='発注依頼' order by ID desc ";
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item= mysqli_fetch_assoc($h_rs);
			echo "<!--$StrSQL:[1]:\n";
			var_dump($h_item);
			echo "-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$h_item["H_M2_ID"]." ";
			//$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
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
			//$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
			$StrSQL.=" and STATUS='発注依頼' order by ID desc ";
			$h_rs=mysqli_query(ConnDB(),$StrSQL);
			$h_item= mysqli_fetch_assoc($h_rs);
			echo "<!--$StrSQL:[4]:\n";
			var_dump($h_item);
			echo "-->";
			$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$h_item["H_M2_ID"]." ";
			//$StrSQL.=" and MID2='".$_SESSION["MID"]."' ";
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

	//発注以降、一括払い時にも扱いを一律にするために、便宜上DIV_IDを設定するようにした。
	$tmp_div_id= $param_div_id!="" ? $param_div_id : $h_div_id;

	// 商談
	if($shodan_id != '') {
		//$tmp_div_idがDAT_SHODAN_DIVテーブル（分割支払い用のテーブル）にない場合、一括払いとして処理
		//$tmp_div_id自体は、見積り送信以降のフェーズの管理のため、一括でも受け渡されるように変更
		echo "<!--param_div_id at savedata:".$tmp_div_id."-->";
		echo "<!--checkDIV_ID(param_div_id) at savedata:".checkDIV_ID($tmp_div_id)."-->";
		if(checkDIV_ID($tmp_div_id)==""){
		//if($tmp_div_id==""){
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
			DIV_ID = '".$tmp_div_id."'
			";
			//echo "<!--sql: $StrSQL-->";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
		}

		// 他のステータスに変更するだけ
		//$StrSQL = "
		//	UPDATE DAT_SHODAN SET
		//		EDITDATE = '".date('Y/m/d H:i:s')."',
		//		STATUS_SORT = '".$status_sort."',
		//		C_STATUS = '".$c_status."',
		//		STATUS = '".$status."'
		//		WHERE
		//	  	ID = ".$shodan_id."
		//		";
		//	// var_dump($StrSQL);
		//	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		//		die;
		//	}
	}
	else if($key != '') {
		// 商談IDを取得してステータス更新
		$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID=".$key.";";
		//echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item_filestatus = mysqli_fetch_assoc($rs);
		$shodan_id = $item_filestatus['SHODAN_ID'];

		if(checkDIV_ID($tmp_div_id)==""){
		//if($tmp_div_id==""){
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
			DIV_ID = '".$tmp_div_id."'
			";
			//echo "<!--sql: $StrSQL-->";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}
		}

		//// 更新
		//$StrSQL = "
		//UPDATE DAT_SHODAN SET
		//	EDITDATE = '".date('Y/m/d H:i:s')."',
		//	STATUS_SORT = '".$status_sort."',
		//	C_STATUS = '".$c_status."',
		//	STATUS = '".$status."'
		//WHERE
		//  ID = ".$shodan_id."
		//";
		//// var_dump($StrSQL);
		//if (!(mysqli_query(ConnDB(),$StrSQL))) {
		//	die;
		//}
	}
	else {
		// 新規
		//現在ここは使われていない
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
			'".$_POST['TITLE']."',
			'".$mid1_list."',
			'".$word."',
			'".$word2."',
			'".$_POST['COMMENT']."',
			'".$_POST['FILE']."',
			'".$_POST['KIGEN']."',

			'".$date_stmp."',
			'".$date_stmp."',

			'".$status_sort."',
			'".$c_status."',
			'".$status."'
		)";
// echo "<!--StrSQL:".$StrSQL."-->";
// var_dump($StrSQL);
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			var_dump("err:".$StrSQL);
			die;
		}
		// 商談ID取得
		//$StrSQL="SELECT ID FROM DAT_SHODAN order by ID desc;";
		$StrSQL="SELECT ID FROM DAT_SHODAN where EDITDATE='".$date_stmp."' order by ID desc;";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$shodan_id = $item['ID'];
	}

	$StrSQL="SELECT * FROM DAT_SHODAN WHERE ID=".$shodan_id.";";
	//echo('<!--'.$StrSQL.'-->');
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_shodan = mysqli_fetch_assoc($rs);
	$mid2 = $item_shodan['MID2'];

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

			M1_MESSAGE,
			M1_TRANS_FLG,
			M1_TRANS_TXT,
			M1_PRICE,
			M1_KIGEN,
			M1_FILE,

			H_M2_ID,
			H_COMMENT,

			N_FILE,
			N_MESSAGE,

			S_FILE,
			S_MESSAGE,

			H3A_MESSAGE,
			H3B_MESSAGE,
			
			DIV_ID,

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

			'".$_POST['H_M2_ID']."',
			'".$_POST['H_COMMENT']."',

			'".$_POST['N_FILE']."',
			'".$_POST['N_MESSAGE']."',

			'".$_POST['S_FILE']."',
			'".$_POST['S_MESSAGE']."',

			'".$_POST['H3A_MESSAGE']."',
			'".$_POST['H3B_MESSAGE']."',
			
			'".$tmp_div_id."',

			'".$date_stmp."',
			'".$date_stmp."'
		)";
		// var_dump($StrSQL);
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

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
	if($_POST['M1_FILE'] != '') {
		copy($file_dir . $_POST['M1_FILE'], $file_dir . $key . '/' . $_POST['M1_FILE']);
	}
	if($_POST['N_FILE'] != '') {
		copy($file_dir . $_POST['N_FILE'], $file_dir . $key . '/' . $_POST['N_FILE']);
	}
	if($_POST['S_FILE'] != '') {
		copy($file_dir . $_POST['S_FILE'], $file_dir . $key . '/' . $_POST['S_FILE']);
	}

	// 見積り書の場合
	// ファイルステータス
	$StrSQL = "
		INSERT INTO DAT_FILESTATUS_DETAIL (
			FILESTATUS_ID,

			TITLE,
			PRICE,
			COMMENT,

			NEWDATE,
			EDITDATE
		) VALUE (
			'".$key."',

			'".$_POST['M2_TITLE']."',
			'".$_POST['M2_PRICE']."',
			'".$_POST['M2_COMMENT']."',

			'".$date_stmp."',
			'".$date_stmp."'
		)";
		// var_dump($StrSQL);
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

/*
// 自分へのメッセージ
	$comment = '';
	switch($type) {
		case '問い合わせ':
			$comment = '問い合わせを送信しました<br><br>' . $_POST['TITLE'] . '<br>' . $_POST['COMMENT'] . '<br>' . $_POST['KIGEN'] . '<br>
				<a href="/a_filestatus/data/' . $key . '/' . $_POST['FILE'] . '">' . $_POST['FILE'] . '</a>';
  	  break;
		case '見積り依頼':
			$comment = '見積り依頼を送信しました<br><br>' . $_POST['M1_MESSAGE'] . '
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り依頼&mode=disp_frame&key='.$key.'\'\');">見積り依頼</a>
			';
  	  break;
		case '見積り送付':
			$comment = '見積り書を送付しました
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=見積り送付&mode=disp_frame&key='.$key.'\'\');">見積り書</a>
			';
  	  break;
		case '発注依頼':
			$comment = $_POST['H_COMMENT'] . '
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=発注依頼&mode=disp_frame&key='.$key.'\'\');">発注依頼</a>
			';
  	  break;
		case 'データ納品':
			$comment = $_POST['N_MESSAGE'] . '
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=データ納品&mode=disp_frame&key='.$key.'\'\');">納品データ</a>
			';
  	  break;
		case '物品納品':
			$comment = $_POST['N_MESSAGE'] . '
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=物品納品&mode=disp_frame&key='.$key.'\'\');">Report</a>
			';
  	  break;
		case '請求':
			$comment = $_POST['S_MESSAGE'] . '
	      <a href="javascript:window.parent.open_mcontact2(\'\'/m_contact1/?type=請求&mode=disp_frame&key='.$key.'\'\');">請求書</a>
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
	*/

	// 相手へのメッセージ
	$comment = '';
	switch($type) {
		case '発注承認':
			$comment = '決済者により発注依頼が承認されました
				' . $_POST['H3A_MESSAGE'] . '
			';
  	  break;
		case '発注否認':
			$comment = '決済者により発注依頼が否認されました
				' . $_POST['H3B_MESSAGE'] . '
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
				'".$mid2."',
				'ENABLE:公開中',
				'".$date_stmp."',
				'".$comment."',
				'".$shodan_id."',
				'".$mid2."',
				'".$key."'
			)
		";
		// var_dump($StrSQL);
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

		SendMail2($type,$mid2,$comment);
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
