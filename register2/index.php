<?php

session_start();
require "../config.php";
require "../base.php";
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

	if ($mode==""){
		$mode="new";
	}

// ソーシャル会員登録用
	if($_GET['status']=="authorized"){
		$apikey="SP-APIKEY";
		$token=$_GET['token'];
		$response=file_get_contents("https://api.socialplus.jp/api/authenticated_user?key=".$apikey."&token=".$token."&add_profile=true&delete_profile=true");
		$val=json_decode($response, true);
		$lineid=$val['user']['identifier'];
		$StrSQL="SELECT ID from DAT_M2 where SOCIALID='".$lineid."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item>0){
			$filename = "err2.html";
			$fp=$DOCUMENT_ROOT.$filename;
			$str=@file_get_contents($fp);
			$str = MakeHTML($str,0,$lid);
			$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;
			exit;
		}

	}

	switch ($mode){
		case "new":
			InitData();
			if($_GET['status']=="authorized"){
				$FieldValue[4]=$lineid;
			}
			break;
		case "edit":
			LoadData($key);
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "saveconf":
			LoadData($key);
			RequestData($obj,$a,$b,$key,$mode);

			$StrSQL="SELECT ID from DAT_M2 where EMAIL='".$FieldValue[2]."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_num_rows($rs);
			if($item>0){
				$filename = "err1.html";
				$fp=$DOCUMENT_ROOT.$filename;
				$str=@file_get_contents($fp);
				$str = MakeHTML($str,0,$lid);
				$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;
				exit;
			}

			$StrSQL="SELECT ID from DAT_M2 where EMAIL='".$FieldValue[2]."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_num_rows($rs);
			if($item>0){
				$filename = "err1.html";
				$fp=$DOCUMENT_ROOT.$filename;
				$str=@file_get_contents($fp);
				$str = MakeHTML($str,0,$lid);
				$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;
				exit;
			}

			//フリーメールチェック
			$email = $FieldValue[2];
			$domain = substr($email, strpos($email, "@")); //ドメイン抜き出し
			$domain = strtolower($domain); //小文字に変換
 
			$freeDomains = array('msn.com','gmail.com','hotmail.co.jp','hotmail.com','yahoo.co.jp','mail.goo.ne.jp','freemail.ne.jp','tokyo24.com','excite.co.jp','infoseek.co.jp','infoseek.jp','comeon.to','comeon.cx','cuib.or.jp','ziplip.co.jp','zdnetmail.ne.jp','supermail.com','lycos.ne.jp','lycos.com','clubaa.com','prontomail.ne.jp','itpmail.itp.ne.jp','bizoffi.com','xaque.com','safe-mail.ne.jp','100100.co.jp','mailkun.com','curio-city.com','teamgear.net','24h.co.jp','gariya.net','pub.ne.jp','jmail.co.jp','kigaru.zzn.com','goomail.com','iloveyou-jp.com','wbs-club.ne.jp','otegami.com','piyomail.com','iat.ne.jp','aol.com','tok2.com','kobe-city.com','xmail.to','club.wonder.ne.jp','pub.to','csc.ne.jp','club.ne.jp','mcn.ne.jp','postpet.co.jp','manbow.com','ijk.com','drive.co.jp','yagi.net','pospe.jp.prg','estyle.ne.jp','eastmail.com','shagami.com','voo.to','julex.to','yi-web.com','mailfriend.net','koei.nu','goo.jp','kobej.zzn.com','pc_run.zzn.com','fact-mail.com','walkerplus.com','keyakiclub.net','yesyes.jp','fubako.com','smoug.net','meritmail.net','vjp.jp','melmel.tv','ultrapostman.com','uymail.com','sailormoon.com','astroboymail.com','doramail.com','dbzmail.com','aamail.jp','lunashine.net','gooo.jp','1kw.jp','hsjp.net','glaystyle.net','saku2.com','kyouin.com','Netidol.jp','csc.jp','kanagawa.to','mukae.com','anet.ne.jp','docomo.ne.jp','softbank.ne.jp','i.softbank.jp','disney.ne.jp','d.vodafone.ne.jp','h.vodafone.ne.jp','t.vodafone.ne.jp','c.vodafone.ne.jp','r.vodafone.ne.jp','k.vodafone.ne.jp','n.vodafone.ne.jp','s.vodafone.ne.jp','q.vodafone.ne.jp','jp-d.ne.jp','jp-h.ne.jp','jp-t.ne.jp','jp-c.ne.jp','jp-c.ne.jp','jp-k.ne.jp','jp-n.ne.jp','jp-s.ne.jp','jp-q.ne.jp','ezweb.ne.jp','au.com','biz.ezweb.ne.jp','ido.ne.jp','sky.tkk.ne.jp','sky.tkc.ne.jp','sky.tu-ka.ne.jp','pdx.ne.jp','di.pdx.ne.jp','dj.pdx.ne.jp','dk.pdx.ne.jp','wm.pdx.ne.jp','willcom.com','emnet.ne.jp');
			//参考：https://biz.tm.softbank.jp/PG2241A1_biz_paypay_campaign_freemailaddress_list.html

			$freemail = 0;
			foreach($freeDomains as $freeDomain){ //fruitsの先頭から１つずつ$fruitに代入する
				if(strpos($domain,$freeDomain) !== false){
					$freemail = 1;
					break;
				} 
			}
			
			if($freemail == 1){
				$filename = "err3.html";
				$fp=$DOCUMENT_ROOT.$filename;
				$str=@file_get_contents($fp);
				$str = MakeHTML($str,0,$lid);
				$str=str_replace("[BASE_URL]",BASE_URL,$str);
				print $str;
				exit;
			}
			break;
		case "deleteconf":
			LoadData($key);
			break;
		case "save":
			// CSRFチェック OKならDB書き込み
//			if ($_SESSION['token']==$token) {

				RequestData($obj,$a,$b,$key,$mode);

				$StrSQL="SELECT MID from DAT_M2 where MID !='".M2_SYSTEM_MID."'  order by MID desc limit 0,1;";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$item = mysqli_fetch_assoc($rs);
				$FieldValue[1]="M2".sprintf("%05d", str_replace("M2", "", $item['MID'])+1);
				$FieldValue[95]="ENABLE:公開中";
				$FieldValue[96]=date("Y/m/d H:i:s");
				$FieldValue[97]=date("Y/m/d H:i:s");
				$FieldValue[26]=$FieldName[26].":未確認";

				$_SESSION['MATT'] = "2";
				$_SESSION['MID'] = $FieldValue[1];
				$_SESSION['MNAME'] = $FieldValue[5];
				SaveData($key);
				SendMail();
//			}
			break;
		case "delete":
			// CSRFチェック OKならDB削除
			if ($_SESSION['token']==$token) {
				RequestData($obj,$a,$b,$key,$mode);
				DeleteData($key);
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
		case "export":
			ExportData();
			exit;
		case "import":
			ImportData($obj,$a,$b,$key,$mode);
			$mode="list";
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
function SendMail()
{

	eval(globals());

	$maildata = GetMailTemplate('会員登録完了(M2)');
	$MailBody = $maildata['BODY'];
	$subject = $maildata['TITLE'];

	$maildata2 = GetMailTemplate('研究者会員登録完了(ADMIN)');
	$MailBody2 = $maildata2['BODY'];
	$subject2 = $maildata2['TITLE'];

	for ($i=0; $i<=$FieldMax; $i=$i+1)
	{
		$MailBody=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody);
		$MailBody=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody);
		$MailBody2=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody2);
		$MailBody2=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody2);
		if (is_numeric($FieldValue[$i]))
		{
			$MailBody=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody);
			$MailBody2=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody2);
		}
			else
		{

			$MailBody=str_replace("[N-".$FieldName[$i]."]","",$MailBody);
			$MailBody2=str_replace("[N-".$FieldName[$i]."]","",$MailBody2);
		}

		$subject=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$subject);
		$subject=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$subject);
		$subject2=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$subject2);
		$subject2=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$subject2);
		if (is_numeric($FieldValue[$i]))
		{
			$subject=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$subject);
			$subject2=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$subject2);
		}
			else
		{

			$subject=str_replace("[N-".$FieldName[$i]."]","",$subject);
			$subject2=str_replace("[N-".$FieldName[$i]."]","",$subject2);
		} 
	}

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	mb_send_mail($FieldValue[2], $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail(SENDER_EMAIL, $subject2, $MailBody2, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
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
	if($_GET['status']!="authorized"){
		$htmlnew = "edit.html";
		$htmlerr = "edit.html";
		$htmledit = "edit.html";
		$htmlconf = "conf.html";
		$htmlend = "end.html";
		$htmldisp = "disp.html";
		$htmllist = "list.html";
	} else {
		$htmlnew = "editl.html";
		$htmlerr = "editl.html";
		$htmledit = "editl.html";
		$htmlconf = "conf.html";
		$htmlend = "end.html";
		$htmldisp = "disp.html";
		$htmllist = "list.html";
	}

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
		case "test":
			$filename=$htmlend;
			$msg01="テスト";
			$msg02="";
			$errmsg="";
			break;
	} 

	//$filename = set_basic_authentication($filename,'auth.html'); // 疑似BASIC認証

	$fp=$DOCUMENT_ROOT.$filename;
	$str=@file_get_contents($fp);

	$str = MakeHTML($str,0,$lid);

	if ($mode=="new"){
		$str=DispParam($str, "NEWDATA");
		$str=DispParamNone($str, "EDITDATA");
	} else {
		$str=DispParamNone($str, "NEWDATA");
		$str=DispParam($str, "EDITDATA");
	} 

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
				$strtmp=$strtmp."<li><input id=\"".$FieldName[$i].$j."\" type=\"checkbox\" name=\"".$FieldName[$i]."[]\" value=\"".$FieldName[$i].":".$tmp[$j]."\" required><label for=\"".$FieldName[$i].$j."\">".$tmp[$j]."</label></li>";
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
			$FieldValue[$i]=htmlspecialchars(str_replace("\\","",$_POST[$FieldName[$i]]));
		}
		if ($FieldAtt[$i]==4 && ($mode=="saveconf")) {
			$filename = $_FILES["EP_".$FieldName[$i]]['name'];
			$extention = pathinfo($filename, PATHINFO_EXTENSION);
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

				move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], $filedir1 . $filename);

				pic_resize($FieldValue[$i], 800,800);
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
		var_dump("savedata_err:".$StrSQL);
		die;
	}


	//M2が登録すると、無条件でM199999とスレッドが立ち、m_message2のメッセージリストの一番上に常に表示されるようにお願いします。
	//（必然的にM199999の/m_message1/のメッセージリストには全てのM2とのメッセージが表示されることになります）
	//サプライヤーと同様に登録後すぐに管理者とのチャットが立ち上がるようにする
	$date_stmp=date('Y/m/d H:i:s');

	$title="Scientist3";
	$mid2=$FieldValue[1];
	$mid1=M1_SYSTEM_MID;
	$CATEGORY="";
	$KEYWORD="";
	$STATUS_SORT="0";
	$C_STATUS="問い合わせ";
	$STATUS="問い合わせ";
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
			'".$mid2."',
			'".$title."',
			'".$mid1."',
			'".$CATEGORY."',
			'".$KEYWORD."',
			'".$date_stmp."',
			'".$date_stmp."',
			'".$STATUS_SORT."',
			'".$C_STATUS."',
			'".$STATUS."'
		)";
echo "<!--DAT_SHODAN_save:".$StrSQL."-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		var_dump("DAT_SHODAN_err:".$StrSQL);
		die;
	}

	// 商談ID取得
	$StrSQL="SELECT ID FROM DAT_SHODAN where EDITDATE='".$date_stmp."' order by ID desc;";
	//$StrSQL="SELECT ID FROM DAT_SHODAN order by ID desc;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$shodan_id = $item['ID'];

	$CATEGORY="問い合わせ";
	// ファイルステータス
	$StrSQL = "
		INSERT INTO DAT_FILESTATUS (
			SHODAN_ID,
			MID1,
			MID2,
			CATEGORY,
			STATUS,
			NEWDATE,
			EDITDATE
		) VALUE (
			'".$shodan_id."',
			'".$mid1."',
			'".$mid2."',
			'".$STATUS."',
			'".$STATUS."',
			'".$date_stmp."',
			'".$date_stmp."'
	)";
echo "<!--DAT_FILESTATUS_save:".$StrSQL."-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		var_dump("DAT_FILESTATUS_err:".$StrSQL);
		die;
	}

	// filestatusのIDを取得
	$StrSQL="SELECT ID FROM DAT_FILESTATUS where EDITDATE='".$date_stmp."' order by ID desc;";
	//$StrSQL="SELECT ID FROM DAT_FILESTATUS order by ID desc;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item_filestatus = mysqli_fetch_assoc($rs);
	$filestatus_id = $item_filestatus['ID'];

	// ファイルステータス
	$StrSQL = "
		INSERT INTO DAT_FILESTATUS_DETAIL (
			FILESTATUS_ID,
			NEWDATE,
			EDITDATE
		) VALUE (
			'".$filestatus_id."',

			'".$date_stmp."',
			'".$date_stmp."'
		)";
echo "<!--DAT_FILESTATUS_DETAIL_save:".$StrSQL."-->";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		var_dump("DAT_FILESTATUS_DETAIL_err:".$StrSQL);
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
