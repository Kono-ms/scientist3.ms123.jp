<?php

session_start();

require "../config.php";
require "../base.php";
require "../common.php";
require '../a_estimates/config.php';

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
		$mid1=$_GET['mid1'];
		$mid2=$_GET['mid2'];
		$eid=$_GET['eid'];
		$itemdata=$_GET['ITEM'];
		$quantity=$_GET['QUANTITY'];
		$price=$_GET['PRICE'];
		$etc=$_GET['ETC'];
		$title=$_GET['title'];
		$msgid=$_GET['etc02'];
			

	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$mid1=$_POST['mid1'];
		$mid2=$_POST['mid2'];
		$eid=$_POST['eid'];
		$itemdata=$_POST['ITEM'];
		$quantity=$_POST['QUANTITY'];
		$price=$_POST['PRICE'];
		$etc=$_POST['ETC'];
		$title=$_POST['title'];
		$msgid=$_POST['etc02'];
	}

	if ($mode==""){
		$mode="list";
	}

	switch ($mode){
		case "new":
			InitData();

			$m1name = "";
			$StrSQL=" SELECT * FROM DAT_M1";
			$StrSQL.=" WHERE MID = '".$mid1."'";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);
			$m1name = $item['M1_DVAL01'];

			$m2name = "";
			$StrSQL=" SELECT * FROM DAT_M2";
			$StrSQL.=" WHERE MID = '".$mid2."'";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);
			$m2name = $item['M2_DVAL01'];

			//タイトルをメッセージから取得
			$StrSQL="SELECT COMMENT FROM DAT_MESSAGE where AID='".$word."' and ETC02='".$msgid."' and COMMENT like '[Change title]%' order by ID desc;";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			if ($rs==true) {
				$item = mysqli_fetch_assoc($rs);
				$preg = preg_match_all('/タイトル「.+?」/i', $item['COMMENT'], $match);
				for($i = 0; $i < count($match[0]); $i++) {
					$tmp = str_replace('タイトル「', '', $match[0][$i]);
					$tmp = str_replace('」', '', $tmp);
					if($tmp!=""){
						$title=$tmp;
					}
				} 
			}

			$msStr = substr(explode(".", (microtime(true) . ""))[1], 0, 3);

			$eid = "E".date("YmdHis").$msStr; //自動発番（E+YMDHIS+ミリ秒）
			$FieldValue[1] = $eid;
			$FieldValue[2] = $mid1;
			$FieldValue[3] = $mid2;
			$FieldValue[4] = $m1name;
			$FieldValue[5] = $m2name;
			$FieldValue[6] = $title;
			$FieldValue[7] = ""; //コメント
			$FieldValue[14]="STATUS:見積依頼中";
			$FieldValue[16]=date("Y/m/d H:i:s");
			$FieldValue[19]=$msgid;



			$mode="edit";

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

					//スタータス 見積依頼中 → 見積提示中
					if ($FieldValue[14]=="STATUS:見積依頼中") {
						$FieldValue[14]="STATUS:見積提示中";
						$FieldValue[9]=date("Y/m/d H:i:s");
						$FieldValue[13]="ISFLG:なし"; //初期値
					}

					SaveData($key,$itemdata,$quantity,$price,$etc);

					if($FieldValue[14]=="STATUS:見積提示中"){
						$mid1 = $FieldValue[2];
						$mid2 = $FieldValue[3];
						$eid = $FieldValue[1];
						$etc02=$FieldValue[19];

						$id="";
						$StrSQL=" SELECT ID FROM DAT_ESTIMATES";
						$StrSQL.=" WHERE EID = '".$eid."'";
						$StrSQL.=" AND M1ID = '".$mid1."'";
						$StrSQL.=" AND M2ID = '".$mid2."'";

						$rs2=mysqli_query(ConnDB(),$StrSQL);
						$item2 = mysqli_fetch_assoc($rs2);
						$id = $item2['ID'];



						$comment= '[見積書]が送信されました。' . "\n";
						$comment.="<a target=\"_blank\" href=\"/m_estimates/?mode=disp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$eid."&key=".$id."&etc02=".$etc02."\">見積書</a>"; //見積書

						//メッセージ
						$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
						$StrSQL.="'".$word."',";
						$StrSQL.="'".$mid1."',";
						$StrSQL.="'ENABLE:公開中',";
						$StrSQL.="'".date("Y/m/d H:i:s")."',";
						$StrSQL.="'".$comment."',";
						$StrSQL.="'".$etc02."'";
						$StrSQL.=")";
						if (!(mysqli_query(ConnDB(),$StrSQL))) {
							die;
						}


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
		case "hacchudisp":
		case "nouhindisp":
			LoadData($key);
			break;
		case "hacchu":
			LoadData($key);
			break;
		case "hacchusave":
			// CSRFチェック OKならDB書き込み
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);

				$FieldValue[10]=date("Y/m/d H:i:s");
				$FieldValue[14]="STATUS:発注済";
	
				SaveData($key,$itemdata,$quantity,$price,$etc);

				$id = $FieldValue[0];
				$mid1 = $FieldValue[2];
				$mid2 = $FieldValue[3];
				$eid = $FieldValue[1];
				$etc02=$FieldValue[19];
				$comment= '[Purchase Order]が送信されました。' . "\n";
				$comment.="<a target=\"_blank\" href=\"/m_estimates/?mode=hacchudisp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$eid."&key=".$id."&etc02=".$etc02."\">Purchase Order</a>"; //Purchase Order

				//メッセージ
				$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
				$StrSQL.="'".$word."',";
				$StrSQL.="'".$mid1."',";
				$StrSQL.="'ENABLE:公開中',";
				$StrSQL.="'".date("Y/m/d H:i:s")."',";
				$StrSQL.="'".$comment."',";
				$StrSQL.="'".$etc02."'";
				$StrSQL.=")";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}





			}
			break;
		case "nouhin":
			LoadData($key);
			break;
		case "nouhinsave":
			// CSRFチェック OKならDB書き込み
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);

				$FieldValue[11]=date("Y/m/d H:i:s");
				$FieldValue[14]="STATUS:納品済";
	
				SaveData($key,$itemdata,$quantity,$price,$etc);

				$id = $FieldValue[0];
				$mid1 = $FieldValue[2];
				$mid2 = $FieldValue[3];
				$eid = $FieldValue[1];
				$etc02=$FieldValue[19];
				$comment= '[Delivery note]が送信されました。' . "\n";
				$comment.="<a target=\"_blank\" href=\"/m_estimates/?mode=nouhindisp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$eid."&key=".$id."&etc02=".$etc02."\">Delivery note</a>"; //Delivery note

				//メッセージ
				$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
				$StrSQL.="'".$word."',";
				$StrSQL.="'".$mid1."',";
				$StrSQL.="'ENABLE:公開中',";
				$StrSQL.="'".date("Y/m/d H:i:s")."',";
				$StrSQL.="'".$comment."',";
				$StrSQL.="'".$etc02."'";
				$StrSQL.=")";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}
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

		case "cancel":
			LoadData($key);
			break;
		case "cancelsave":
			// CSRFチェック OKならDB書き込み
			if ($_SESSION['token']==$token) {
				LoadData($key);
				RequestData($obj,$a,$b,$key,$mode);

				$FieldValue[10]=date("Y/m/d H:i:s");
				$FieldValue[14]="STATUS:キャンセル";
	
				SaveData($key,$itemdata,$quantity,$price,$etc);

				$id = $FieldValue[0];
				$mid1 = $FieldValue[2];
				$mid2 = $FieldValue[3];
				$eid = $FieldValue[1];
				$etc02=$FieldValue[19];
				$comment= '[キャンセル]が送信されました。' . "\n";
				//$comment.="<a target=\"_blank\" href=\"/m_estimates/?mode=hacchudisp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$eid."&key=".$id."&etc02=".$etc02."\">Purchase Order</a>"; //Purchase Order

				//メッセージ
				$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
				$StrSQL.="'".$word."',";
				$StrSQL.="'".$mid1."',";
				$StrSQL.="'ENABLE:公開中',";
				$StrSQL.="'".date("Y/m/d H:i:s")."',";
				$StrSQL.="'".$comment."',";
				$StrSQL.="'".$etc02."'";
				$StrSQL.=")";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}

			}
			break;
	} 


	DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2,$eid,$itemdata,$quantity,$price,$etc);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2,$eid,$itemdata,$quantity,$price,$etc)
{

	eval(globals());

	//各テンプレートファイル名
	$htmlnew = "edit.html";
	$htmledit = "edit.html";
	$htmlconf = "conf.html";
	$htmlend = "end.html";
	$htmldisp = "disp.html";
	$htmlhacchu = "hacchu.html";
	$htmlnouhin = "nouhin.html";
	$htmlhacchudisp = "hacchudisp.html";
	$htmlnouhindisp = "nouhindisp.html";
	$htmlerr = "edit.html";
	$htmllist = "list.html";

	$htmlcancel = "cancel.html";

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
					$mode = "edit";
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
			case "hacchusave":
			case "save":
			case "nouhinsave":
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
			case "hacchudisp":
				$filename=$htmlhacchudisp;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "nouhindisp":
				$filename=$htmlnouhindisp;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "hacchu":
				$filename=$htmlhacchu;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
			case "nouhin":
				$filename=$htmlnouhin;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;

			case "cancel":
				$filename=$htmlcancel;
				$msg01="";
				$msg02="";
				$errmsg="";
				break;
				
		} 

		$fp=$DOCUMENT_ROOT.$filename;
		$str=@file_get_contents($fp);

		$str = MakeHTML($str,1,$lid);
		// $str=str_replace("[ETC02]",$_GET['etc02'],$str);
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

			

			// HTMLエスケープ処理（詳細表示系、HIDDEN値）
			$str=str_replace("[T-".$FieldName[$i]."]",$FieldValue[$i],$str);
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


		if($mode=="hacchu"){
			$str=str_replace("[SCREEN_TITLE]","Purchase Order",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","発注の確認をしてください",$str);

			// $url="/m_chat2_list/?mode=hacchu&word=".$word."&mid1=".$FieldValue[2]."&mid2=".$FieldValue[3]."&eid=".$FieldValue[1]."&key=".$FieldValue[0]."";
			

			// $str=str_replace("[URL]",$url,$str);

		} else 
		if($mode=="cancel"){
			$str=str_replace("[SCREEN_TITLE]","案件の取り下げ",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","以下の案件を取り下げしてよろしいでしょうか？",$str);

			// $url="/m_chat1_list/?mode=delivery&word=".$word."&mid1=".$FieldValue[2]."&mid2=".$FieldValue[3]."&eid=".$FieldValue[1]."&key=".$FieldValue[0]."";
			

			// $str=str_replace("[URL]",$url,$str);

		} else 
		if($mode=="nouhin"){
			$str=str_replace("[SCREEN_TITLE]","Delivery note",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","納品の確認をしてください",$str);

			// $url="/m_chat1_list/?mode=delivery&word=".$word."&mid1=".$FieldValue[2]."&mid2=".$FieldValue[3]."&eid=".$FieldValue[1]."&key=".$FieldValue[0]."";
			

			// $str=str_replace("[URL]",$url,$str);

		} else 
		if($mode=="hacchusave"){
			$str=str_replace("[SCREEN_TITLE]","Purchase Order",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);
			$str=str_replace("[END_MSG]","Purchase Orderを作成しました。",$str);

			$str=str_replace("[BACKURL]","https://scientist3.ms123.jp/m_chat2_list/?mode=list&word=".$word."&mid1=".$FieldValue[2]."&mid2=".$FieldValue[3]."",$str);

		} else 
		if($mode=="cancelsave"){
			$str=str_replace("[SCREEN_TITLE]","案件の取り下げ",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);
			$str=str_replace("[END_MSG]","案件を取り下げました。",$str);

			$str=str_replace("[BACKURL]","https://scientist3.ms123.jp/m_chat2_list/?mode=list&word=".$word."&mid1=".$FieldValue[2]."&mid2=".$FieldValue[3]."",$str);

		} else 
		if($mode=="nouhinsave"){
			$str=str_replace("[SCREEN_TITLE]","Delivery note",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);
			$str=str_replace("[END_MSG]","Delivery noteを作成しました。",$str);

			$str=str_replace("[BACKURL]","https://scientist3.ms123.jp/m_chat1_list/?mode=list&word=".$word."&mid1=".$FieldValue[2]."&mid2=".$FieldValue[3]."",$str);


		} else 
		if($mode=="hacchudisp"){
			$str=str_replace("[SCREEN_TITLE]","Purchase Order",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);
	
		} else 
		if($mode=="nouhindisp"){
			$str=str_replace("[SCREEN_TITLE]","Delivery note",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);


		} else 

        if ($FieldValue[14]=="STATUS:見積依頼中") {
			$str=str_replace("[SCREEN_TITLE]","見積書",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","見積書を作成してください。",$str);
        }
        else if ($FieldValue[14]=="STATUS:見積提示中") {
			$str=str_replace("[SCREEN_TITLE]","見積書",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);
			$str=str_replace("[END_MSG]","見積書を作成しました。",$str);

			$str=str_replace("[BACKURL]","https://scientist3.ms123.jp/m_chat1_list/?mode=list&word=".$word."&mid1=".$FieldValue[2]."&mid2=".$FieldValue[3]."",$str);
			

        }
		else if ($FieldValue[14]=="STATUS:発注済") {
			$str=str_replace("[SCREEN_TITLE]","Purchase Order",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);
        } else {
			$str=str_replace("[SCREEN_TITLE]","見積書",$str);
			$str=str_replace("[SCREEN_TITLE_DETAIL]","",$str);
		}

		//詳細データ
		$tmp = "";
        if ($mode=="edit" ) {

            if (is_array($itemdata)) {

				for ($i=0; $i<count($itemdata); $i=$i+1) {
					$tmp.="<div class=\"formset__item\" style=\"margin-bottom:0px;\">";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"ITEM[]\" value=\"".$itemdata[$i]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"QUANTITY[]\" class=\"QUANTITY inputnum\" style=\"text-align:right;\" value=\"".$quantity[$i]."\" ></div>";
					//$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"PRICE[]\" readonly class=\"PRICE\" style=\"text-align:right;\" value=\"".$price[$i]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"ETC[]\" value=\"".$etc[$i]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><div class=\"head-btn head-btn-color2\"><a class=\"rowDel\" href=\"javascript:void(0)\">削除</a></div></div>";
					$tmp.="</div>\n";
				}
            } else {
				$rowCnt = 0;
				$StrSQL=" SELECT * FROM DAT_ESTIMATES_DETAIL ";
				$StrSQL.=" WHERE EID ='".$eid."'";
				$StrSQL.=" order by id ";
				$rs=mysqli_query(ConnDB(), $StrSQL);
				while ($item = mysqli_fetch_assoc($rs)) {
					$tmp.="<div class=\"formset__item\" style=\"margin-bottom:0px;\">";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"ITEM[]\" value=\"".$item["ITEM"]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"QUANTITY[]\" class=\"QUANTITY inputnum\" style=\"text-align:right;\" value=\"".$item["QUANTITY"]."\" ></div>";
					//$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"PRICE[]\" readonly class=\"PRICE\" style=\"text-align:right;\" value=\"".$item["PRICE"]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"ETC[]\" value=\"".$item["ETC"]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><div class=\"head-btn head-btn-color2\"><a class=\"rowDel\" href=\"javascript:void(0)\">削除</a></div></div>";
					$tmp.="</div>\n";
					$rowCnt = $rowCnt + 1;
				}
				//1行に満たない場合
				for ($i=$rowCnt; $i<1; $i=$i+1) {
					$tmp.="<div class=\"formset__item\" style=\"margin-bottom:0px;\">";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"ITEM[]\" value=\"\" ></div>";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"QUANTITY[]\" class=\"QUANTITY inputnum\" style=\"text-align:right;\" value=\"\" ></div>";
					//$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"PRICE[]\" class=\"PRICE\" readonly style=\"text-align:right;\" value=\"\" ></div>";
					$tmp.="<div class=\"formset__input\"><input type=\"text\" name=\"ETC[]\" value=\"\" ></div>";
					$tmp.="<div class=\"formset__input\"><div class=\"head-btn head-btn-color2\"><a class=\"rowDel\" href=\"javascript:void(0)\">削除</a></div></div>";
					$tmp.="</div>\n";
				}
			}
            
        } else if($mode=="saveconf" ){
			if(is_array($itemdata)){
				for ($i=0; $i<count($itemdata); $i=$i+1) {
					$tmp.="<div class=\"formset__item\" style=\"margin-bottom:0px;\">";
					$tmp.="<div class=\"formset__input\"><input readonly type=\"text\" name=\"ITEM[]\" value=\"".$itemdata[$i]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><input readonly type=\"text\" name=\"QUANTITY[]\" class=\"QUANTITY inputnum\" style=\"text-align:right;\" value=\"".$quantity[$i]."\" ></div>";
					//$tmp.="<div class=\"formset__input\"><input readonly type=\"text\" name=\"PRICE[]\" class=\"PRICE\" style=\"text-align:right;\" value=\"".$price[$i]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><input readonly type=\"text\" name=\"ETC[]\" value=\"".$etc[$i]."\" ></div>";
					$tmp.="</div>\n";
				}
			}
		} else { 
				$StrSQL=" SELECT * FROM DAT_ESTIMATES_DETAIL ";
				$StrSQL.=" WHERE EID ='".$eid."'";
				$StrSQL.=" order by id ";
				$rs=mysqli_query(ConnDB(), $StrSQL);
				while ($item = mysqli_fetch_assoc($rs)) {
					$tmp.="<div class=\"formset__item\" style=\"margin-bottom:0px;\">";
					$tmp.="<div class=\"formset__input\"><input readonly type=\"text\" name=\"ITEM[]\" value=\"".$item["ITEM"]."\" ></div>";
					$tmp.="<div class=\"formset__input \"><input readonly type=\"text\" name=\"QUANTITY[]\" class=\"QUANTITY inputnum\" style=\"text-align:right;\" value=\"".$item["QUANTITY"]."\" ></div>";
					//$tmp.="<div class=\"formset__input\"><input readonly type=\"text\" name=\"PRICE[]\" readonly class=\"PRICE\" style=\"text-align:right;\" value=\"".$item["PRICE"]."\" ></div>";
					$tmp.="<div class=\"formset__input\"><input readonly type=\"text\" name=\"ETC[]\" value=\"".$item["ETC"]."\" ></div>";
					$tmp.="</div>\n";
				}
		}
		$str=str_replace("[DETAIL]",$tmp,$str);


		//PF手数料（％）
		$rate = 0;
		if($_SESSION['MATT'] == "2"){
			$StrSQL="SELECT M2_ETC02 FROM DAT_M2 WHERE ID='".$_SESSION['M-ID']."'";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);

			$rate = $item["M2_ETC02"];

			$str=DispParam($str, "PF_FEE");
		} else {
			$str=DispParamNone($str, "PF_FEE");
		}

		$StrSQL="SELECT * FROM DAT_M1 where MID='".$FieldValue[2]."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_fetch_assoc($rs);
		$str=DispM1($item, $str);
		$unit = $item["M1_ETC03"]; //通貨単位

		$StrSQL="SELECT * FROM DAT_M2 where MID='".$FieldValue[3]."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_fetch_assoc($rs);
		$str=DispM2($item, $str);

		//発注作成ボタンは、ログイン中かつステータスが「STATUS:見積提示中」のとき
		if($_SESSION['MATT']=="2" && $FieldValue[14]=="STATUS:見積提示中"){
			$str=DispParam($str, "HACCHU_SAKUSEI");
		} else {
			$str=DispParamNone($str, "HACCHU_SAKUSEI");
		}
		
		

		$str=str_replace("[RATE]",$rate,$str);


		//通貨レート
		$StrSQL="SELECT * FROM DAT_CURRENCY where UNIT='".explode(":",$unit)[1]."'";

		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_fetch_assoc($rs);
		$currency_rate=$item["RATE"];
		$currency_unit=$item["UNIT"];
		if($currency_rate==""){
			$currency_rate="1";
		}

		$str=str_replace("[CURRENCY_UNIT]",$currency_unit,$str);
		$str=str_replace("[CURRENCY_RATE]",$currency_rate,$str);
		



		// CSRFトークン生成
		if($token==""){
			$token=htmlspecialchars(session_id());
			$_SESSION['token'] = $token;
		}
		$str=str_replace("[TOKEN]",$token,$str);
		// $str=str_replace("[ETC02]",$_GET['etc02'],$str);
 
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

				if($CurrentRecord%2==0){
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
		// $str=str_replace("[ETC02]",$_GET['etc02'],$str);

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
			//$FieldValue[$i]=htmlspecialchars(str_replace("\\","",$_POST[$FieldName[$i]]));
			$FieldValue[$i]=(str_replace("\\","",$_POST[$FieldName[$i]]));
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
			//$FieldValue[$i]=htmlspecialchars($item[$FieldName[$i]]);
			$FieldValue[$i]=($item[$FieldName[$i]]);
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
function SaveData($key,$itemdata,$quantity,$price,$etc)

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
			//$StrSQL.="'".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";
			$StrSQL.="'".str_replace("'","''",($FieldValue[$i]))."'";
		}
		$StrSQL=$StrSQL.")";
	} else {
		$StrSQL="UPDATE ".$TableName." SET ";
		for ($i=1; $i<=$FieldMax; $i++) {
			if($i>1){
				$StrSQL.=",";
			}
			//$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",htmlspecialchars($FieldValue[$i]))."'";
			$StrSQL.="`".$FieldName[$i]."`='".str_replace("'","''",($FieldValue[$i]))."'";
		}
		$StrSQL=$StrSQL." WHERE ".$FieldName[$FieldKey]."='".$key."'";
	} 

	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}
 
	// 通貨単位の取得
	$unit = 0;
	$rate = 0;
	$StrSQL="SELECT UNIT,RATE FROM DAT_CURRENCY WHERE ID=1";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$unit = $item["UNIT"];
	$rate = $item["RATE"];

	//詳細データの登録(DELETE&INSERT)
	//DELETE
	$StrSQL="DELETE FROM DAT_ESTIMATES_DETAIL WHERE EID='".$FieldValue[1]."';";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	//INSERT
	if(is_array($itemdata)){
		for ($i=0; $i<count($itemdata); $i=$i+1) {

			// 空欄行は登録しない
			if($itemdata[$i]!="" || $quantity[$i]!="" || $etc[$i]!=""){

				$calc = 0;
				if (is_numeric($rate) && is_numeric($price[$i])) {
					$calc = floatval($rate) * floatval($price[$i]);
				}

				$StrSQL="INSERT INTO DAT_ESTIMATES_DETAIL (";
				$StrSQL.="EID,";
				$StrSQL.="ITEM,";
				$StrSQL.="QUANTITY,";
				$StrSQL.="UNIT,";
				$StrSQL.="PRICE,";
				$StrSQL.="ETC ";
				$StrSQL.=") VALUES (";
				$StrSQL.="'".$FieldValue[1]."',";
				$StrSQL.="'".$itemdata[$i]."',";
				$StrSQL.="'".$quantity[$i]."',";
				$StrSQL.="'".$unit."',";
				$StrSQL.="'".$calc."',";
				$StrSQL.="'".$etc[$i]."' ";
				$StrSQL.=")";

				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}
		}
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
