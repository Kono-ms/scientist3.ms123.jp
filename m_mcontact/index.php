<?php

session_start();

require "../config.php";
require "../base.php";
require '../a_mcontact/config.php';

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

	if($_POST['mode']==""){
		$mode=$_GET['mode'];
		$sort=$_GET['sort'];
		$word=$_GET['word'];
		$key=$_GET['key'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
		$token=$_GET['token'];
		$title=$_GET['title'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$title=$_POST['title'];
	}

	if ($mode==""){
		$mode="list";
	}

	switch ($mode){
		case "new":
			InitData();
			break;
		case "edit":
			LoadData($key);
			$FieldValue[1]=$_SESSION['MID'];
			$FieldValue[2]=$word;
			$FieldValue[5]="STATUS:提示中";
			$FieldValue[7]=date("Y/m/d H:i:s");
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
					SaveData($key);

					// 2020.12.02 yamamoto 金額変更時は前のメッセージのETC01をNULLにする
					// これにより前のメッセージのリンクは表示されなくなる
					if($_POST['CONTACT_STATUS'] == '2') {
						$StrSQL="UPDATE DAT_MESSAGE SET ETC01 = NULL WHERE ETC01=".$_POST['ID'].";";
						if (!(mysqli_query(ConnDB(),$StrSQL))) {
							die;
						}
					}

					// 2020.11.06 yamamoto メッセージ登録
					// 2020.12.23 yamamoto 案件ID(ETC02)追加
					$StrSQL="SELECT max(ID) as max_id FROM DAT_MCONTACT";
					//$StrSQL="SELECT LAST_INSERT_ID() as max_id";
					$rs=mysqli_query(ConnDB(),$StrSQL);
					$item = mysqli_fetch_assoc($rs);
					$max_id = $item['max_id'];

					//  2021.01.14 yamamoto 買い手側もステータス提示できるようにする
					$aid = $_POST['MID']."-".$_POST['MIDT'];
					$rid = $_POST['MID'];
					$eid = "";
					if($_POST['CONTACT_STATUS'] == '4' && $_GET['side'] == '2') {
						$aid = $_POST['rid']."-".$_POST['MIDT'];
						$rid = $_POST['MID'];
					}

					$comment = '[' . str_replace('STATUS:', '', $FieldValue[5]) . ']が送信されました。' . "\n";

					
					if($FieldValue[5] == 'STATUS:申込' || $FieldValue[5] == 'STATUS:金額変更') {
						$comment .= '金額：' . number_format($_POST['PRICE']) . '円' . "\n";
					}


					//見積依頼データ作成
					if($_POST['CONTACT_STATUS'] == '4' && str_replace('STATUS:', '', $FieldValue[5]) == '見積依頼'){
						$msStr = substr(explode(".", (microtime(true) . ""))[1], 0, 3); //ミリ秒
						$eid = "E".date("YmdHis").$msStr; //自動発番（E+YMDHIS）

						$m1name = "";
						$StrSQL=" SELECT * FROM DAT_M1";
						$StrSQL.=" WHERE MID = '".$_POST['rid']."'";
						$rs=mysqli_query(ConnDB(),$StrSQL);
						$item = mysqli_fetch_assoc($rs);
						$m1name = $item['M1_DVAL01'];

						$m2name = "";
						$StrSQL=" SELECT * FROM DAT_M2";
						$StrSQL.=" WHERE MID = '".$_POST['MIDT']."'";
						$rs=mysqli_query(ConnDB(),$StrSQL);
						$item = mysqli_fetch_assoc($rs);
						$m2name = $item['M2_DVAL01'];

						$comment .= str_replace("'","''",htmlspecialchars($_POST['MSG']));

						$StrSQL="INSERT INTO DAT_ESTIMATES (EID, M1ID, M1NAME, M2ID, M2NAME, TITLE, COMMENT, STATUS, NEWDATE,ETC02) values (";
						$StrSQL.="'".$eid."',";
						$StrSQL.="'".$_POST['rid']."',";
						$StrSQL.="'".$m1name."',";
						$StrSQL.="'".$_POST['MIDT']."',";
						$StrSQL.="'".$m2name."',";
						$StrSQL.="'".$title."',";
						$StrSQL.="'".$comment."',";
						$StrSQL.="'STATUS:見積依頼中',";
						$StrSQL.="'".date("Y/m/d H:i:s")."',";
						$StrSQL.="'".$_GET['etc02']."'";
						$StrSQL.=")";

						if (!(mysqli_query(ConnDB(),$StrSQL))) {
							die;
						}

						$id="";
						$StrSQL=" SELECT ID FROM DAT_ESTIMATES";
						$StrSQL.=" WHERE EID = '".$eid."'";
						$StrSQL.=" AND M1ID = '".$_POST['rid']."'";
						$StrSQL.=" AND M2ID = '".$_POST['MIDT']."'";

						$rs2=mysqli_query(ConnDB(),$StrSQL);
						$item2 = mysqli_fetch_assoc($rs2);
						$id = $item2['ID'];

						//見積依頼の場合、見積作成リンク生成
						$mid1=$_POST['rid'];
						$mid2=$_POST['MIDT'];
						$comment.="<a target=\"_blank\" href=\"/m_estimates/?mode=edit&word=".$aid."&mid1=".$mid1."&mid2=".$mid2."&eid=".$eid."&key=".$id."&etc02=".$_GET['etc02']."\">見積作成</a>"; //見積作成

					}

					
					

					$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC01, ETC02) values (";
					$StrSQL.="'".$aid."',";
					$StrSQL.="'".$rid."',";
					$StrSQL.="'ENABLE:公開中',";
					$StrSQL.="'".date("Y/m/d H:i:s")."',";
					$StrSQL.="'".$comment."',";
					$StrSQL.="'".$item['max_id']."',";
					$StrSQL.="'".$_GET['etc02']."'";
					$StrSQL.=")";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}


					SendMail($_POST['MID']);
					// 2021.01.18 yamamoto 「納品完了」の場合はさらに評価メールを送る
					if($_POST['CONTACT_STATUS'] == '4' && $_POST['STATUS'] == 'STATUS:納品完了') {
						SendMail($_POST['MID'], $_POST['MIDT'], $item['max_id'], 'mail_eval.txt');
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
			break;
		case "export":
			ExportData();
			exit;
		case "import":
			ImportData($obj,$a,$b,$key,$mode);
			$mode="list";
			break;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$title);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function SendMail($mid, $mid2 = null, $contact_id = null, $template = "mail.txt")
{

	eval(globals());

	if($template == 'mail.txt') {
		$maildata = GetMailTemplate('金額提示');
	}
	else {
		$maildata = GetMailTemplate('評価依頼');
	}

$status =  str_replace('STATUS:', '', $FieldValue[5]);
	

	// 相手のメールアドレスを取得
	$tbl = 'DAT_M' . ($_SESSION['MATT'] == '1' ? '2' : '1');

	$StrSQL="SELECT EMAIL FROM $tbl where MID='".$mid."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	//$fp=$template;
	//$MailBody=@file_get_contents($fp) . $item['EMAIL'];
	$MailBody = $maildata['BODY'];

	$MailBody=str_replace("[STATUS]",$status,$MailBody);
	$MailBody=str_replace("[MSG]",$_POST['MSG'],$MailBody);
	$MailBody=str_replace("[PRICE]",number_format($_POST['PRICE']),$MailBody);
	$MailBody=str_replace("[DATE]",$FieldValue[7],$MailBody);

	$subject = $maildata['TITLE'];
	$subject=str_replace("[STATUS]",$status,$subject);

	//$subject = "【[WEBSITE_NAME】[" . $status . "]が" . $mode . "されました";
	if($template == 'mail_eval.txt') {
		//$subject = "【Scientist3】評価のお願い";
		$MailBody=str_replace("[MID1]",$mid,$MailBody);
		$MailBody=str_replace("[MID2]",$mid2,$MailBody);
		$MailBody=str_replace("[CONTACT_ID]",$contact_id,$MailBody);
	}

	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	//mb_send_mail($item['EMAIL'], $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3事務局","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 
	//mb_send_mail('info@msc-dev.com', $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3事務局","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 
	//mb_send_mail('197583@gmail.com', $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding("Scientist3事務局","ISO-2022-JP","AUTO"))."<info@msc-dev.com>"); 

	mb_send_mail($item['EMAIL'], $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
	mb_send_mail("info@msc-dev.com", $subject, $MailBody, "From:".mb_encode_mimeheader(mb_convert_encoding(SENDER_NAME,"ISO-2022-JP","AUTO"))."<".SENDER_EMAIL.">"); 
}

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$title)
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
		$str=str_replace("[ETC02]",$_GET['etc02'],$str);
		$str=str_replace("[SIDE]",$_GET['side'],$str);
		$str=str_replace("[RID]",$_GET['rid'],$str);

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

			// 2020.12.02 yamamoto ステータス固定のケース
			if(isset($_GET['contact_status'])) {
				$str=str_replace("[CONTACT_STATUS]",$_GET['contact_status'],$str);
				$str=str_replace("[ID]",$_GET['contact_id'],$str);
				if($_GET['contact_status'] == '1') {
					$str=str_replace("[OPT-STATUS]",'申込<input id="STATUS" name="STATUS" type="hidden" value="STATUS:申込"/>',$str);
				}
				else if($_GET['contact_status'] == '2') {
					$str=str_replace("[OPT-STATUS]",'金額変更<input id="STATUS" name="STATUS" type="hidden" value="STATUS:金額変更"/>',$str);
				}
			}
			else {
				$str=str_replace("[CONTACT_STATUS]",'',$str);
			}

			// 2021.01.07 yammaoto ステータス提示を追加
			if($_GET['contact_status'] == '4') {
				if($_SESSION['MATT'] == "1"){
					$FieldParam[5]="商談開始::納品完了";
				} else {
					$FieldParam[5]="商談開始::見積依頼::納品完了";
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
					// 2020.12.02 yamamoto ステータス選択の絞り込み
					if(isset($_GET['contact_status'])) {
						if($_GET['contact_status'] == '3') {
							if($j <= 1) {
								continue;
							}
						}
					}
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
		$str=str_replace("[TITLE]",$title,$str);

		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);
		$str=str_replace("[ETC02]",$_GET['etc02'],$str);

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
		$StrSQL="SELECT * FROM ".$TableName." ".ListSql(mysqli_real_escape_string(ConnDB(),$sort),mysqli_real_escape_string(ConnDB(),$word)).";";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item=="") {
			$pagestr="";
			$strMain="<tr class=tableset__list><td align=center colspan=7>対象データがありません。</td></tr>";
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

				$str=$strM;

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

		$str = MakeHTML($str,1,$lid);
		$str=str_replace("[ETC02]",$_GET['etc02'],$str);

		$str=str_replace("[PAGING]",$pagestr,$str);
		$str=str_replace("[SORT]",$sort,$str);
		$str=str_replace("[WORD]",$word,$str);
		$str=str_replace("[PAGE]",$page,$str);
		$str=str_replace("[KEY]",$key,$str);
		$str=str_replace("[LID]",$lid,$str);
		$str=str_replace("[TITLE]",$title,$str);

		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);
		$str=str_replace("[ETC02]",$_GET['etc02'],$str);

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

	//  2021.01.14 yamamoto 買い手側もステータス提示できるようにする
  //exit(var_dump($FieldValue));
	if($_GET['side'] == '2') {
		$FieldValue[1] = $_GET['rid'];
	}

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
//名前 タブ区切りデータのエクスポート処理
//機能 タブ区切りテキストデータ（UTF-8→ShiftJIS）のエクスポート処理
//引数 なし
//戻値 なし
//=========================================================================================================
function ExportData()
{
	eval(globals());

	$csv_data = "";

	$StrSQL="SELECT * FROM ".$TableName." order by ID";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item<>"") {
		$str="ID	FIELD01	FIELD02	FIELD03	FIELD04	FIELD05	FIELD06	FIELD07	FIELD08	FIELD09	FIELD10"."\r\n";
		$csv_data .= $str;
		while ($item = mysqli_fetch_assoc($rs)) {
			$str="";
			for ($i=0; $i<=$FieldMax; $i=$i+1){
				if ($i!=0){
					$str=$str."\t";
				}
				$str=$str.$item[$FieldName[$i]];
			}

			$str=str_replace("\r\n","",$str);
			$str=str_replace("\r","",$str);
			$str=str_replace("\n","",$str)."\r\n";
			$csv_data .= $str;
		} 
		$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=data.csv");
		echo($csv_data);
	} 

	return $function_ret;
} 

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

	// SQLインジェクション対策
	while (!feof($fp)) {
		$txt = fgets($fp);
		$txt=str_replace("\"","",$txt);
		$cols=explode("\t",$txt);
		if($cols[0]<>""){
			$StrSQL="SELECT * FROM ".$TableName." where ID='".mysqli_real_escape_string(ConnDB(),$cols[0])."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_num_rows($rs);
			if($item=="") {
				$StrSQL="INSERT INTO ".$TableName." (";
				for ($j=1; $j<=$FieldMax; $j++){
					if ($j!=1){
						$StrSQL.=",";
					} 
					$StrSQL.="`".$FieldName[$j]."`";
				}
				$StrSQL.=") values (";
				for ($j=1; $j<=$FieldMax; $j++){
					if ($j!=1){
						$StrSQL.=",";
					} 
					$StrSQL.="'".str_replace("'","''",$cols[$j])."'";
				}
				$StrSQL.=")";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			} else {
				if ($cols[1]!="delete"){
					$StrSQL="UPDATE ".$TableName." SET ";
					for ($j=1; $j<=$FieldMax; $j++) {
						if ($j!=1){
							$StrSQL.=",";
						} 
						$StrSQL.="`".$FieldName[$j]."`='".str_replace("'","''",$cols[$j])."'";
					}
					$StrSQL.=" WHERE ".$FieldName[$FieldKey]."='".$cols[0]."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} else {
					$StrSQL="DELETE FROM ".$TableName." WHERE ID='".$cols[0]."'";
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
