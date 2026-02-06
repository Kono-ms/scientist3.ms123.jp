<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_m2/config.php';

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
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
	}

	if($_SESSION['MID']==""){
		$url=BASE_URL . "/login2/";
		header("Location: {$url}");
		exit;
	}

	if ($mode==""){
		$mode="edit";
	}

	if ($key==""){
		$StrSQL="SELECT ID from DAT_M2 where MID='".$_SESSION['MID']."' and ENABLE='ENABLE:公開中';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item>0){
			$item = mysqli_fetch_assoc($rs);
			$key = $item['ID'];
		} else {
			$url=BASE_URL . "/login1/";
			header("Location: {$url}");
			exit;
		}
	}

	//ライトプランの場合、使用不可
	// $StrSQL="SELECT M2_DVAL14 from DAT_M2 where MID='".$_SESSION['MID']."' and ENABLE='ENABLE:公開中';";
	// $rs=mysqli_query(ConnDB(),$StrSQL);
	// $item = mysqli_fetch_assoc($rs);
	// if($item["M2_DVAL14"]!="M2_DVAL14:スタンダード"){
	// 	$url=BASE_URL . "/login1/";
	// 	header("Location: {$url}");
	// 	exit;
	// }




	switch ($mode){
		case "new":
			InitData();
			break;
		case "edit":
			LoadData($key);
//			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "disp":
			LoadData($key);
//			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "saveconf":
			LoadData($key);
			RequestData($obj,$a,$b,$key,$mode);
			
			break;
		case "save":
			// CSRFチェック OKならDB書き込み
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);

				//契約フラグが「済」の状態（会員登録済みのレコード）で、
				//保存された場合にメールを送信
				$keyaku_now=str_replace($FieldName[26].":", "",$FieldValue[26]);
				echo "<!--now:".$keyaku_now."-->";
				if($keyaku_now=="済"){
					SendMail($key);
				}

				SaveData($key);

				$_SESSION['MNAME'] = $FieldValue[5];

// 要調整
/*
				$StrSQL="DELETE FROM DAT_MATCH where MID='".$FieldValue[1]."';";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
				$tmp="";
				$StrSQL="SELECT DAT_JOB.HID, DAT_JOB.JID FROM DAT_JOB inner join DAT_HOSPITAL on DAT_HOSPITAL.HID=DAT_JOB.HID order by DAT_JOB.JID;";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				while ($item = mysqli_fetch_assoc($rs)) {
					if($tmp!=""){
						$tmp.="::";
					}
					$tmp.=$item['JID'];
				}
				$jids=explode("::", $tmp);

				for($j=0; $j<count($jids); $j++){
					CulcMatching($FieldValue[1], $jids[$j], 0);
				}
*/
// 要調整

			}
			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			//$mode="edit1";
			$mode="edit";
			break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token);

	return $function_ret;
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


	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	$maildata = GetMailTemplate('研究者情報変更');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$mailto = $item['EMAIL'];
	//$MailBody=str_replace("[D-NAME]",$item['M1_DVAL01'],$MailBody);

	echo "<!--StraSQL:$StrSQL-->";
	echo "<!--mailto:$mailto-->";

	for ($i=0; $i<=$FieldMax; $i=$i+1)
	{
		$MailBody=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody);
		$MailBody=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody);
		//$MailBody2=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody2);
		//$MailBody2=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody2);
		$subject=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$subject);
		$subject=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$subject);
		//$subject2=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$subject2);
		//$subject2=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$subject2);
		if (is_numeric($FieldValue[$i]))
		{
			$MailBody=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody);
			//$MailBody2=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody2);
			$subject=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$subject);
			//$subject2=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$subject2);
		}
		else
		{
			$MailBody=str_replace("[N-".$FieldName[$i]."]","",$MailBody);
			//$MailBody2=str_replace("[N-".$FieldName[$i]."]","",$MailBody2);
			$subject=str_replace("[N-".$FieldName[$i]."]","",$subject);
			//$subject2=str_replace("[N-".$FieldName[$i]."]","",$subject2);
		} 
	}


	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	echo "<!--mailto:".$mailto."-->";
	mb_send_mail($mailto, $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
		

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

	// yamamoto ここの仕様がまだ不明
	/*
	if ($FieldValue[6]==""){
		$str=DispParam($str, "NEWREGIST");
		$str=DispParamNone($str, "EDITREGIST");
	} else {
		$str=DispParamNone($str, "NEWREGIST");
		$str=DispParam($str, "EDITREGIST");
	} 
	*/
	$str=DispParam($str, "NEWREGIST");
	$str=DispParamNone($str, "EDITREGIST");

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
			$strtmp=$strtmp."<option value=''>Please select ▼</option>";
			$tmp=explode("::",$FieldParam[$i]);
			for ($j=0; $j<count($tmp); $j=$j+1) {
				$strtmp=$strtmp."<option value='".$FieldName[$i].":".$tmp[$j]."'>".$tmp[$j]."</option>";

			}

			$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
			if (($filename==$htmlerr || $mode=="new" || $mode=="edit1" || $mode=="edit2" || $mode=="edit3" || $mode=="edit4" || $mode=="edit5" || $mode=="edit6" || $mode=="edit7" || $mode=="edit8") && $FieldValue[$i]!="") {

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
			if (($filename==$htmlerr || $mode=="new" || $mode=="edit1" || $mode=="edit2" || $mode=="edit3" || $mode=="edit4" || $mode=="edit5" || $mode=="edit6" || $mode=="edit7" || $mode=="edit8") && $FieldValue[$i]!="") {
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
			if (($filename==$htmlerr || $mode=="new" || $mode=="edit1" || $mode=="edit2" || $mode=="edit3" || $mode=="edit4" || $mode=="edit5" || $mode=="edit6" || $mode=="edit7" || $mode=="edit8") && $FieldValue[$i]!="") {
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

	$str=str_replace("[MID_NEW]",convert_MID($FieldValue[1]),$str);
	
	//決裁者ID(カスタマー向けには企業IDと表示されているが、a_m2等では決裁者IDとよばれ、企業IDM2_DVAL13とは別のもの）
	//[122]:M2_DVAL15
	if($FieldValue[122]=="" || is_null($FieldValue[122])){
		$str=str_replace("[DISP1-M2_DVAL15]","未登録",$str);
	}else{
		$str=str_replace("[DISP1-M2_DVAL15]",$FieldValue[122],$str);
	}
	

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
			if (isset($_POST[$FieldName[$i]])) {
				$FieldValue[$i]=htmlspecialchars(str_replace("\\","",$_POST[$FieldName[$i]]));
			}

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
				move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], "../a_m2/data/".$filename);
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

	// SQLインジェクション対策
	// HTMLエスケープ処理（SQL書き込み）
	$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item=="") {
		$FieldValue[96]=date("Y/m/d H:i:s");
		$FieldValue[97]=date("Y/m/d H:i:s");

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
		$FieldValue[97]=date("Y/m/d H:i:s");

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
