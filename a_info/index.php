<?php
	session_start();
	require "../config.php";
require "../base_a.php";
	require './config.php';

set_time_limit(7200);

//InitSub();//データベースデータの読み込み
//ConnDB();//データベース接続
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
		$mode="list";
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
			LoadData($key);
			RequestData($obj,$a,$b,$key,$mode);
			SaveData($key);

			$mode="list";
			if ($page==""){
				$page=1;
			} 
			break;
		case "delete":
			RequestData($obj,$a,$b,$key,$mode);
			DeleteData($key);

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

	DispData($mode,$sort,$word,$key,$page,$lid);

	return $function_ret;
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

//各テンプレートファイル名
  $htmlnew="edit.html";
  $htmledit="edit.html";
  $htmlconf="conf.html";
  $htmlend="end.html";
  $htmldisp="disp.html";
  $htmlerr="edit.html";
  $htmllist="list.html";

	$FieldParam[4]="";
	$StrSQL="SELECT * FROM DAT_CCATE order by ID";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	while ($item = mysqli_fetch_assoc($rs)) {
		if($FieldParam[4]!=""){
			$FieldParam[4].="::";
		}
		$FieldParam[4].=$item['CNAME'];
	}

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

		$str = MakeHTML($str,1,$lid);

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
				$strtmp=$strtmp."<option value=''>▼選択して下さい</option>";
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
				$strtmp=$strtmp."<ul class='mlist25p'>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input type='radio' name='".$FieldName[$i]."' value='".$FieldName[$i].":".$tmp[$j]."'>&nbsp;".$tmp[$j]."</li>";
				}
				$strtmp=$strtmp."</ul>";
				$str=str_replace("[OPT-".$FieldName[$i]."]",$strtmp,$str);
				if (($filename==$htmlerr || $mode=="new" || $mode=="edit") && $FieldValue[$i]!="") {
					$str=str_replace("'".$FieldValue[$i]."'","'".$FieldValue[$i]."' checked",$str);
				} 
			} 

			if ($FieldAtt[$i]=="3"){
				$strtmp="";
				$tmp=explode("::",$FieldParam[$i]);
				$strtmp=$strtmp."<ul class='mlist25p'>";
				for ($j=0; $j<count($tmp); $j=$j+1) {
					$strtmp=$strtmp."<li><input type='checkbox' name='".$FieldName[$i]."[]' value='".$FieldName[$i].":".$tmp[$j]."'>&nbsp;".$tmp[$j]."</li>";
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

		$str = MakeHTML($str,1,$lid);

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
function ExportData()
{
	eval(globals());

	$csv_data = "";

	$StrSQL="SELECT * FROM ".$TableName." order by ID";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item<>"") {
		$str="ID	"."\r\n";
		$csv_data .= $str;
		while ($item = mysqli_fetch_assoc($rs)) {
			$str="";
			for ($i=0; $i<=$FieldMax; $i=$i+1){
				if ($i!=0){
					$str=$str."\t";
				}
				$str=$str.str_replace("\t", "[t]", $item[$FieldName[$i]]);
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
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ImportData($obj,$a,$b,$key,$mode)
{
	eval(globals());

	$fp = fopen($_FILES['importfile']['tmp_name'], "r");
	$txt = fgets($fp);

//	$StrSQL="DELETE FROM ".$TableName.";";
//	if (!(mysqli_query(ConnDB(),$StrSQL))) {
//		die;
//	}

	while (!feof($fp)) {
		$txt = fgets($fp);
		$txt=str_replace("\"","",$txt);
		$cols=explode("\t",$txt);
		if($cols[0]<>""){
			$StrSQL="SELECT * FROM ".$TableName." where ID='".$cols[0]."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_num_rows($rs);
			if($item=="") {
				$StrSQL="INSERT INTO ".$TableName." (";
				for ($j=0; $j<=$FieldMax; $j=$j+1){
					$StrSQL=$StrSQL."`".$FieldName[$j]."`";
					if ($j!=$FieldMax){
						$StrSQL=$StrSQL.",";
					} 
				}
				$StrSQL=$StrSQL.") values (";
				for ($j=0; $j<=$FieldMax; $j=$j+1){
					$StrSQL=$StrSQL."'".str_replace("[t]","\t",str_replace("'","''",$cols[$j]))."'";
					if ($j!=$FieldMax){
						$StrSQL=$StrSQL.",";
					} 
				}
				$StrSQL=$StrSQL.")";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			} else {
				if ($cols[1]!="delete"){
					$StrSQL="UPDATE ".$TableName." SET ";
					for ($j=1; $j<=$FieldMax-1; $j=$j+1) {
						$StrSQL=$StrSQL."`".$FieldName[$j]."`='".str_replace("[t]","\t",str_replace("'","''",$cols[$j]))."',";
					}
					$StrSQL=$StrSQL."`".$FieldName[$FieldMax]."`='".str_replace("'","''",$cols[$FieldMax])."' ";
					$StrSQL=$StrSQL."WHERE ".$FieldName[$FieldKey]."='".$cols[0]."'";
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
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function LoadData($key)
{
	eval(globals());

	$StrSQL="";
	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".$key."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);

	if ($rs==true) {
		$item = mysqli_fetch_assoc($rs);
		for ($i=0; $i<=$FieldMax; $i=$i+1) {
			$FieldValue[$i]=$item[$FieldName[$i]];
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
		$StrSQL=$StrSQL."WHERE ".$FieldName[$FieldKey]."='".$key."'";
	} 


	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DeleteData($key)
{
	eval(globals());

	$StrSQL="";
	$StrSQL="DELETE FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".$key."';";

	if (!(mysqli_query(ConnDB(),$StrSQL))) {
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
//【関数名】	:ConnDB()
//【機能\】	:データベースへの接続
//【引数】	:なし
//【戻り値】	:なし
//【備考】	:DB接続
//=========================================================================================================
function ConnDB()
{
	eval(globals());

	$ConnDB=mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);

	return $ConnDB;
} 

?>
