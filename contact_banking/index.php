<?php
	session_start();
	require "../config.php";
require "../base.php";
	require './config.php';
// ini_set( 'display_errors', 1 );
set_time_limit(7200);

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
		$mode=$_GET['mode'];
		$sort=$_GET['sort'];
		$word=$_GET['word'];
		$key=$_GET['key'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
	}

	if ($mode==""){
		$mode="new";
	}

	switch ($mode){
		case "new":
			InitData();

			if($_SESSION['MATT'] == "1"){
				$StrSQL="SELECT * from DAT_M1 where ID = '".$_SESSION['M-ID']."';";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$item=mysqli_num_rows($rs);
				if($item>0){
					$item = mysqli_fetch_assoc($rs);
					$FieldValue[8] = "TMP02:Researchers"; // ユーザー区別
					$FieldValue[7] = $item["M1_DVAL01"]; // 会社名
					$FieldValue[2] = $item["M1_DVAL22"] . ' ' . $item["M1_DVAL23"]; // お名前
					$FieldValue[3] = $item["EMAIL"]; // メールアドレス
					$FieldValue[4] = $item["M1_DVAL07"]; // TEL
					
				} 
			} else if($_SESSION['MATT'] == "2"){
				$StrSQL="SELECT * from DAT_M2 where ID = '".$_SESSION['M-ID']."';";
				$rs=mysqli_query(ConnDB(),$StrSQL);
				$item=mysqli_num_rows($rs);
				if($item>0){
					$item = mysqli_fetch_assoc($rs);
					$FieldValue[8] = "TMP02:Supplier"; // ユーザー区別
					$FieldValue[7] = $item["M2_DVAL03"]; // 会社名
					$FieldValue[2] = $item["M2_DVAL02"]; // お名前
					$FieldValue[3] = $item["EMAIL"]; // メールアドレス
					$FieldValue[4] = $item["M2_DVAL08"]; // TEL
				} 
				
			}

			break;
		case "edit":
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "saveconf":
			RequestData($obj,$a,$b,$key,$mode);
			break;
		case "save":
			RequestData($obj,$a,$b,$key,$mode);
			SaveData($key);

			// ステータスを登録変更審査中にする
			$StrSQL=" UPDATE DAT_M1 SET M1_DRDO01 = 'M1_DRDO01:登録変更審査中' where MID='".$_SESSION['MID']."'";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				var_dump($StrSQL);
				die;
			}

			SendMail();
			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			$mode="edit";
			break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid);

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

	
	$maildata1 = GetMailTemplate('銀行口座情報の変更（M1）');
	$maildata2 = GetMailTemplate('銀行口座情報の変更');
	$MailBody1 = $maildata1['BODY'];
	$subject1 = $maildata1['TITLE'];
	$MailBody2 = $maildata2['BODY'];
	$subject2 = $maildata2['TITLE'];


	$StrSQL="SELECT * from DAT_M1 where MID='".$_SESSION['MID']."' ;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$itemM1 = mysqli_fetch_assoc($rs);

	$MailBody1=str_replace("[M1_DVAL23]",$itemM1['M1_DVAL23'],$MailBody1);
	$subject1=str_replace("[M1_DVAL23]",$itemM1['M1_DVAL23'],$subject1);

	$MailBody2=str_replace("[M1_DVAL23]",$itemM1['M1_DVAL23'],$MailBody2);
	$subject2=str_replace("[M1_DVAL23]",$itemM1['M1_DVAL23'],$subject2);

	$img=str_replace($filepath1,"",$_POST['TMP02']);
	$MailBody1=str_replace("[TMP02]",$img,$MailBody1);
	$subject1=str_replace("[TMP02]",$img,$subject1);



	for ($i=0; $i<=$FieldMax; $i=$i+1)
	{
		$MailBody1=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody1);
		$MailBody1=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody1);
		$MailBody2=str_replace("[".$FieldName[$i]."]",$FieldValue[$i],$MailBody2);
		$MailBody2=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","\r\n",str_replace($FieldName[$i].":","",$FieldValue[$i])),$MailBody2);
		if (is_numeric($FieldValue[$i]))
		{
			$MailBody1=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody1);
			$MailBody2=str_replace("[N-".$FieldName[$i]."]",number_format($FieldValue[$i],0),$MailBody2);
		}
			else
		{
			$MailBody1=str_replace("[N-".$FieldName[$i]."]","",$MailBody1);
			$MailBody2=str_replace("[N-".$FieldName[$i]."]","",$MailBody2);
		} 
	}

	
	$MailBody1=str_replace("[MID]",$_SESSION['MID'],$MailBody1);
	$MailBody1=str_replace("[M1_DVAL01]",$_POST['TMP01'].' '.$_POST['NAME'],$MailBody1);
	$MailBody1=str_replace("[TMP01]",$_POST['TMP01'],$MailBody1);
	$MailBody1=str_replace("[NAME]",$_POST['NAME'],$MailBody1);
	$MailBody1=str_replace("[EMAIL]",$_POST['EMAIL'],$MailBody1);
	$MailBody1=str_replace("[TEL]",$_POST['TEL'],$MailBody1);
	$MailBody1=str_replace("[BASE_URL]",BASE_URL,$MailBody1);
	// $MailBody1=str_replace("[INPUT1]",$_POST['INPUT1'],$MailBody1);
	// $MailBody1=str_replace("[INPUT2]",$_POST['INPUT2'],$MailBody1);
	// $MailBody1=str_replace("[INPUT3]",$_POST['INPUT3'],$MailBody1);
	// $MailBody1=str_replace("[INPUT4]",$_POST['INPUT4'],$MailBody1);
	
	$subject1=str_replace("[MID]",$_SESSION['MID'],$subject1);
	$subject1=str_replace("[M1_DVAL01]",$_POST['TMP01'].' '.$_POST['NAME'],$subject1);
	$subject1=str_replace("[TMP01]",$_POST['TMP01'],$subject1);
	$subject1=str_replace("[NAME]",$_POST['NAME'],$subject1);
	$subject1=str_replace("[EMAIL]",$_POST['EMAIL'],$subject1);
	$subject1=str_replace("[TEL]",$_POST['TEL'],$subject1);
	$subject1=str_replace("[BASE_URL]",BASE_URL,$subject1);

	$MailBody2=str_replace("[MID]",$_SESSION['MID'],$MailBody2);
	$MailBody2=str_replace("[M1_DVAL01]",$_POST['TMP01'].' '.$_POST['NAME'],$MailBody2);
	$MailBody2=str_replace("[TMP01]",$_POST['TMP01'],$MailBody2);
	$MailBody2=str_replace("[NAME]",$_POST['NAME'],$MailBody2);
	$MailBody2=str_replace("[EMAIL]",$_POST['EMAIL'],$MailBody2);
	$MailBody2=str_replace("[TEL]",$_POST['TEL'],$MailBody2);
	$MailBody2=str_replace("[TMP02]",$_POST['TMP02'],$MailBody2);
	$MailBody2=str_replace("[BASE_URL]",BASE_URL,$MailBody2);
	// $MailBody2=str_replace("[INPUT1]",$_POST['INPUT1'],$MailBody2);
	// $MailBody2=str_replace("[INPUT2]",$_POST['INPUT2'],$MailBody2);
	// $MailBody2=str_replace("[INPUT3]",$_POST['INPUT3'],$MailBody2);
	// $MailBody2=str_replace("[INPUT4]",$_POST['INPUT4'],$MailBody2);
	$subject2=str_replace("[MID]",$_SESSION['MID'],$subject2);
	$subject2=str_replace("[M1_DVAL01]",$_POST['TMP01'].' '.$_POST['NAME'],$subject2);
	$subject2=str_replace("[TMP01]",$_POST['TMP01'],$subject2);
	$subject2=str_replace("[NAME]",$_POST['NAME'],$subject2);
	$subject2=str_replace("[EMAIL]",$_POST['EMAIL'],$subject2);
	$subject2=str_replace("[TEL]",$_POST['TEL'],$subject2);
	$subject2=str_replace("[TMP02]",$_POST['TMP02'],$subject2);
	$subject2=str_replace("[BASE_URL]",BASE_URL,$subject2);

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	$mailto=$FieldValue[3];
	$mailtoAdmin=SENDER_EMAIL;
	// $mailto="toretoresansan00@gmail.com";
	// $mailtoAdmin="toretoresansan11@gmail.com";
	mb_send_mail($mailto, $subject1, $MailBody1, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail($mailtoAdmin, $subject2, $MailBody2, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid)
{

	eval(globals());

  $htmlnew = "./contact_edit.html";
  $htmledit = "./contact_edit.html";
  $htmlconf = "./contact_conf.html";
  $htmlend = "./contact_end.html";
  $htmldisp = "./contact_disp.html";
  $htmlerr = "./contact_edit.html";
  $htmllist = "./contact_list.html";

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

		for ($i=0; $i<=$FieldMax; $i=$i+1){
			if ($FieldAtt[$i]==4){
				if ($FieldValue[$i]==""){
					$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
					$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
				} 

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

		$str=str_replace("[INPUT1]",$_POST['INPUT1'],$str);
		$str=str_replace("[INPUT2]",$_POST['INPUT2'],$str);
		$str=str_replace("[INPUT3]",$_POST['INPUT3'],$str);
		$str=str_replace("[INPUT4]",$_POST['INPUT4'],$str);

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
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);

		$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

	}
		else
	{


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

		$StrSQL="";
		$StrSQL="SELECT * FROM ".$TableName." ".ListSql($sort,$word).";";
		$rs=mysqli_query(ConnDB(),$$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item=="") {
			$pagestr="";
			$strMain="<tr class=tableset__list><td align=center colspan=7>対象データがありません。</td></tr>";
		} else {
			//================================================================================================
			//ページング処理
			//================================================================================================
			$reccount=mysqli_num_rows($rs);
			$pagecount=intval($reccount/$PageSize+0.9);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$str="";
			if (intval($page)==1) {
				$str=$str."対象件数(".$reccount."件)　　&lt;前の".$PageSize."件&gt;";
			} else {
				$str=$str."対象件数(".$reccount."件)　　&lt;<a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page-1)."\">前の".$PageSize."件</a>&gt;";
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
					$str=$str." <b>".$i."</b>";
				} else {
					$str=$str." <a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\">".$i."</a>";
				} 
			}
			if (intval($page)<$pagecount) {
				$str=$str." &lt;<a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page+1)."\">次の".$PageSize."件</a>&gt;";
			} else {
				$str=$str." &lt;次の".$PageSize."件&gt;";
			} 

			$pagestr=$str;
			$CurrentRecord=1;
			$strMain="";
			while ($item = mysqli_fetch_assoc($rs)) {

				$str=$strM;

				for ($i=0; $i<=$FieldMax; $i=$i+1) {
					if ($FieldAtt[$i]==4) {
						if ($item[$FieldName[$i]]=="") {
							$str=str_replace("[".$FieldName[$i]."]",$filepath1."s.gif",$str);
							$str=str_replace("[D-".$FieldName[$i]."]",$filepath1."s.gif",$str);
						} 
					} 

					$str=str_replace("[".$FieldName[$i]."]",$item[$FieldName[$i]],$str);
					$str=str_replace("[D-".$FieldName[$i]."]",str_replace("\r\n","<br />",str_replace($FieldName[$i].":","",$item[$FieldName[$i]])),$str);
					if (is_numeric($item[$FieldName[$i]])) {
						$str=str_replace("[N-".$FieldName[$i]."]",number_format($item[$FieldName[$i]],0),$str);
					} else {
						$str=str_replace("[N-".$FieldName[$i]."]","",$str);
					} 
				}

				if(CurrentRecord%2==0){
					$str=str_replace("[LIST-BG]","bg01",$str);
				} else {
					$str=str_replace("[LIST-BG]","bg02",$str);
				}

				$strMain=$strMain.$str.chr(13);

				$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

				if ($CurrentRecord>$PageSize){
					break;
				}
			} 
		} 


		$str=$strU.$strMain.$strD;

		$str = MakeHTML($str,0,$lid);

		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);

		$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

	} 


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
		if ($FieldAtt[$i]==4 && $mode=="saveconf") {
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
			} 
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
function SaveData($key)
{
	eval(globals());

	$StrSQL="";
	$StrSQL="SELECT * FROM ".$TableName." WHERE `".$FieldName[$FieldKey]."`='".$key."';";
	$rs=mysqli_query(ConnDB(),$$StrSQL);
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
		$StrSQL=$StrSQL."WHERE ".$FieldName[$FieldKey]."='".$key."'";
	} 


	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		var_dump($StrSQL);
		die;
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
