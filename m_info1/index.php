<?php
	session_start();
	require "../config.php";
require "../base.php";
	require '../a_info/config.php';

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

	if($_SESSION['MATT'] != "1"){
		$url=BASE_URL . "/login1/";
		header("Location: {$url}");
		exit;
	}

	if($mode=="disp"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".BASE_URL . "/m_info1/".sprintf("%04d", $key)."/");
		exit();
	}

	if ($word==""){
		$mode="list";
	} else {
		if(is_numeric($word)){
			$mode="disp";
			$key=intval($word);
		} else {
			$mode="list";
		}
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
			RequestData($obj,$a,$b,$key,$mode);
			SaveData($key);
			break;
		case "delete":
			RequestData($obj,$a,$b,$key,$mode);
			DeleteData($key);
			break;
		case "back":
			RequestData($obj,$a,$b,$key,$mode);
			$mode="edit";
			break;
		case "disp":
			LoadData($key);
			if($FieldValue[0]==""){
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: "."/m_info1/");
				exit();
			}
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

	if ($mode!="list"){
		$filename="disp.html";
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

		$str=$strU."".$strD;
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

		$str=str_replace("@P1","<div class='image'><img src='".$FieldValue[10]."' style='width:100%;'></div>",$str);
		$str=str_replace("@P2","<div class='image'><img src='".$FieldValue[11]."' style='width:100%;'></div>",$str);
		$str=str_replace("@P3","<div class='image'><img src='".$FieldValue[12]."' style='width:100%;'></div>",$str);

		$str=str_replace("@PP1","<img src='".$FieldValue[10]."'>",$str);
		$str=str_replace("@PP2","<img src='".$FieldValue[11]."'>",$str);
		$str=str_replace("@PP3","<img src='".$FieldValue[12]."'>",$str);

		$cate=$FieldValue[4];

		$str=str_replace("<br /><br />","<br />",$str);

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
		$str=str_replace("[URLID]",sprintf("%04d", $key),$str);

		if($FieldValue[4]!=""){
			$StrSQL="SELECT * FROM DAT_CCATE where CNAME='".str_replace("CCATE:", "", $FieldValue[4])."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_fetch_assoc($rs);
			$str=str_replace("[PAN]", "<li class=\"breadcrumb__item\"><a href=\"/m_info1/".$item['URL']."/\">".str_replace("CCATE:", "", $FieldValue[4])."</a>&nbsp;&gt;&nbsp;</li>", $str);
			$str=str_replace("[CATE]", "<li class=\"breadcrumb__item\"><a href=\"/m_info1/".$item['URL']."/\">".str_replace("CCATE:", "", $FieldValue[4])."</a></li>", $str);
		} else {
			$str=str_replace("[PAN]", "", $str);
			$str=str_replace("[CATE]", "", $str);
		}

	}
		else
	{

		if($word!=""){
			$filename="list.html";
		} else {
			$filename="top_list.html";
		}

		$url=$word;
		if($word!=""){
			$StrSQL="SELECT * FROM DAT_CCATE where URL='".$word."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_fetch_assoc($rs);
			$word=$item['CNAME'];
			if($word==""){
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: "."/m_info1/");
				exit();
			}
		}

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

		$StrSQL="";
		$StrSQL="SELECT * FROM ".$TableName." ".ListSqlS2($sort,$word)." LIMIT 0, 100;";
		echo('<!--'.$StrSQL.'-->');
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item=="") {
			$pagestr="";
			$strMain="<tr class=tableset__list><td align=center colspan=7>対象データがありません。</td></tr>";
		} else {
			//================================================================================================
			//ページング処理
			//================================================================================================
			$PageSize=10;
			$reccount=mysqli_num_rows($rs);
			$pagecount=intval($reccount/$PageSize+0.9);
			mysqli_data_seek($rs, $PageSize*($page-1));

			$str="";
			$s=$page-5;
			if ($s<1) {
				$s=1;
			} 
			$e=$s+9;
			if ($e>$pagecount) {
				$e=$pagecount;
			} 
			if($e==0){
				$e=1;
			}
			for ($i=$s; $i<=$e; $i=$i+1) {
				if ($i==intval($page)) {
					$str=$str."<li><span class=\"current\">".$i."</span></li>";
				} else {
					$str=$str."<li><a href=\"/m_info1/?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\">".$i."</a></li>";
				} 
			}

			$pagestr=$str;
			$CurrentRecord=1;
			$strMain="";
			while ($item = mysqli_fetch_assoc($rs)) {

				$str=$strM;

				$str=str_replace("[INO]",sprintf("%04d", $item['ID']),$str);

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
				$str=str_replace("[L-COMMENT]", mb_substr(strip_tags($item['COMMENT']),0,60,"UTF-8"), $str);

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
		$str=str_replace("[URL]",$url,$str);

	} 

	if($word==""){
		$tmp="";
		$StrSQL="SELECT * FROM DAT_CCATE where CNAME<>'サイトオリジナル' order by ID";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		while ($item = mysqli_fetch_assoc($rs)) {
			$tmp.="<li class=\"breadcrumb__item\"><a href=\"/m_info1/".$item['URL']."/\">".$item['CNAME']."</a></li>";
//				$tmp.="<li style=\"padding:10px 0px;border-bottom:0px;\"><div class=\"info_lst\"><p class=\"info_lst_fl\"><a href=\"/m_info1/".$item['URL']."/\"><img src=\"".$item['CPIC']."\" alt=\"".$item['CNAME']."\" width=\"160\" /></a></p><p class=\"info_lst_fr\"><a href=\"/m_info1/".$item['URL']."/\">".$item['CCOMMENT']."</a></p></div></li>";
		} 
		$str=str_replace("[CLIST]",$tmp,$str);

		$str=str_replace("[H2_1]", "新着記事一覧", $str);
		$str=str_replace("[H2_2]", "カテゴリ一覧", $str);

		$str=str_replace("[PAN]", "<li class=\"breadcrumb__item\">Announcement</li>", $str);

		$str=str_replace("[METAWORD1]", "", $str);
		$str=str_replace("[METAWORD2]", "", $str);
		$str=str_replace("[METAWORD3]", "コラム,記事,", $str);
		$str=str_replace("[METAWORD4]", "キャリアコンサルタント", $str);

	} else {
		$tmp="";
//		$StrSQL="SELECT * FROM DAT_CCATE where CNAME='".$word."' order by ID desc";
		$StrSQL="SELECT * FROM DAT_CCATE where CNAME<>'サイトオリジナル' order by ID";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		while ($item = mysqli_fetch_assoc($rs)) {
			$tmp.="<li class=\"breadcrumb__item\"><a href=\"/m_info1/".$item['URL']."/\" class=\"catebutton\">".$item['CNAME']."</a></li>";
//<li style=\"padding:10px 0px;border-bottom:0px;\"><div class=\"info_lst\"><p class=\"info_lst_fl\"><a href=\"/m_info1/".$item['URL']."/\"><img src=\"".$item['CPIC']."\" alt=\"".$item['CNAME']."\" width=\"160\" /></a></p><p class=\"info_lst_fr\"><a href=\"/m_info1/".$item['URL']."/\">".$item['CCOMMENT']."</a></p></div></li>";
		} 
		$str=str_replace("[CLIST]",$tmp,$str);

		$str=str_replace("[H2_1]", $word."の新着記事一覧", $str);
		$str=str_replace("[H2_2]", "その他のカテゴリ一覧", $str);

		$str=str_replace("[PAN]", "<li class=\"breadcrumb__item\" itemprop=\"itemListElement\" itemscope itemtype=\"http://schema.org/Listitem\"><a href=\"/m_info1/\" itemprop=\"item\"><span itemprop=\"name\">記事コンテンツ</span></a>&nbsp;&gt;&nbsp;</li><li>".$word."</li>", $str);

		$str=str_replace("[METAWORD1]", $word."｜", $str);
		$str=str_replace("[METAWORD2]", $word."に関する記事一覧ページです。", $str);
		$str=str_replace("[METAWORD3]", $word.",", $str);
		$str=str_replace("[METAWORD4]", $word, $str);
	}

	$tmp="";
	$StrSQL="SELECT * FROM DAT_INFO where ETC04 like '%ETC04:Supplier向け%' AND CCATE like '".mb_substr($cate,0,10,"UTF-8")."%' and ETC12='ETC12:公開中' order by DATE desc, ID desc LIMIT 0, 8;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	while ($item = mysqli_fetch_assoc($rs)) {
		$tmp.="<a href=\"/m_info1/[INO]/\" class=\"similar_column\"><div><div class=\"similar_column_img\"> <img src=\"[PIC]\" alt=\"\"> </div><div class=\"similar_column_TITLE\"><p>[D-TITLE]</p><p>[COMMENT]</p></div><div class=\"similar_column_detail\"><p>[COMMENT]</p></div></div></a> ";
		$tmp=str_replace("[D-DATE]",$item['DATE'],$tmp);
		$tmp=str_replace("[D-CCATE]",str_replace("CCATE:", "", $item['CCATE']),$tmp);
		$tmp=str_replace("[PIC]",$item['PIC'],$tmp);
		$tmp=str_replace("[D-TITLE]",$item['TITLE'],$tmp);
		$tmp=str_replace("[COMMENT]",LeftText(strip_tags($item['COMMENT']), 30),$tmp);
		$tmp=str_replace("[INO]",sprintf("%04d", $item['ID']),$tmp);
		$tmp=str_replace("[D-AUTHOR]",$item['AUTHOR'],$tmp);
	} 
	if($tmp!=""){
		$str=str_replace("[NEW_LIST]", $tmp, $str);
		$str=DispParam($str, "NEW_LIST");
	} else {
		$str=DispParamNone($str, "NEW_LIST");
	}

	$cnt=1;
	$tmp="";
	$StrSQL="SELECT * FROM DAT_INFO where ETC04 like '%ETC04:Supplier向け%' AND CCATE<>'CCATE:特集' and ETC12='ETC12:公開中' order by ETC12, DATE desc, ID desc LIMIT 0, 10;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	while ($item = mysqli_fetch_assoc($rs)) {
		$tmp.="<a href=\"/m_info1/[INO]/\" class=\"ranking_column\"><div><div class=\"ranking_column_img\"> <img src=\"[PIC]\" alt=\"\"> <img src=\"/common/images/side_rank_[CNT].png\" alt=\"\"> </div><div class=\"ranking_column_TITLE\"> <img src=\"/common/images/side_rank_[CNT].png\" alt=\"\"><p>[D-TITLE]</p><p>[COMMENT]</p></div></div></a> ";
		$tmp=str_replace("[D-DATE]",$item['DATE'],$tmp);
		$tmp=str_replace("[D-CCATE]",str_replace("CCATE:", "", $item['CCATE']),$tmp);
		$tmp=str_replace("[PIC]",$item['PIC'],$tmp);
		$tmp=str_replace("[D-TITLE]",$item['TITLE'],$tmp);
		$tmp=str_replace("[COMMENT]",LeftText(strip_tags($item['COMMENT']), 30),$tmp);
		$tmp=str_replace("[INO]",sprintf("%04d", $item['ID']),$tmp);
		$tmp=str_replace("[D-AUTHOR]",$item['AUTHOR'],$tmp);
		$tmp=str_replace("[CNT]",sprintf("%02d", $cnt),$tmp);
		$cnt++;
	} 
	if($tmp!=""){
		$str=str_replace("[NEW_LIST2]", $tmp, $str);
		$str=DispParam($str, "NEW_LIST2");
	} else {
		$str=DispParamNone($str, "NEW_LIST2");
	}

	$cnt=1;
	$tmp="";
	$StrSQL="SELECT * FROM DAT_INFO where ETC04 like '%ETC04:Supplier向け%' AND CCATE<>'CCATE:特集' and ETC12='ETC12:公開中' order by ETC12, DATE desc, ID desc LIMIT 0, 10;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	while ($item = mysqli_fetch_assoc($rs)) {
		$tmp.="<li><a href=\"/m_info1/[INO]/\" class=\"ranking_column\"><div><div class=\"ranking_column_img\"> <img src=\"[PIC]\" alt=\"\"> <img src=\"/common/images/side_rank_[CNT].png\" alt=\"\"> </div><div class=\"ranking_column_TITLE\"> <img src=\"/common/images/side_rank_[CNT].png\" alt=\"\"><p>[D-TITLE]</p><p>[COMMENT]</p></div></div></a></li> ";
		$tmp=str_replace("[D-DATE]",$item['DATE'],$tmp);
		$tmp=str_replace("[D-CCATE]",str_replace("CCATE:", "", $item['CCATE']),$tmp);
		$tmp=str_replace("[PIC]",$item['PIC'],$tmp);
		$tmp=str_replace("[D-TITLE]",$item['TITLE'],$tmp);
		$tmp=str_replace("[COMMENT]",LeftText(strip_tags($item['COMMENT']), 30),$tmp);
		$tmp=str_replace("[INO]",sprintf("%04d", $item['ID']),$tmp);
		$tmp=str_replace("[D-AUTHOR]",$item['AUTHOR'],$tmp);
		$tmp=str_replace("[CNT]",sprintf("%02d", $cnt),$tmp);
		$cnt++;
	} 
	if($tmp!=""){
		$str=str_replace("[NEW_LIST2_SP]", $tmp, $str);
		$str=DispParam($str, "NEW_LIST2_SP");
	} else {
		$str=DispParamNone($str, "NEW_LIST2_SP");
	}

	$str=str_replace("<span class=\"catelabel\"></span>","",$str);

	$str=str_replace("[BANNER]", "", $str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

	return $function_ret;
} 
//=========================================================================================================
//名前 SQL条件（WHERE ･･･ ORDER BY･･･）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ListSqlS2($sort,$word)
{
  //extract($GLOBALS);
  eval(globals());

  //$str="WHERE ETC12='ETC12:公開中' AND ETC04 like '%ETC04:Supplier向け%'";
  $str="WHERE ETC12='ETC12:公開中' AND ETC04 like '%ETC04:サプライヤー向け%'";

  if ($word!=""){
    $str=$str." AND CCATE like '%".$word."%'";
  } 

  $str=$str." ORDER BY DATE desc, ID desc";

  $function_ret=$str;

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
			$exts = split("[/\\.]", $_FILES["EP_".$FieldName[$i]]['name']);
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
		$str="ID	人材担当ユーザID	人材担当	状況	チェック項目	社内データメモ	分類	登録経路	認知経路	PANO.	管理NO.	氏名	フリガナ	住所:〒	住所:都道府県	住所:住所	最寄沿線1	最寄駅1	最寄沿線2	最寄駅2	最寄沿線3	最寄駅3	連絡先種別1	連絡先名称1	連絡先1	連絡先種別2	連絡先名称2	連絡先2	その他連絡先	メール	携帯メール	生年月日	年齢	性別	婚姻	扶養家族の有無	連絡希望事項	入学年月:年1	入学年月:月1	卒業年月:年1	卒業年月:月1	卒退区分1	学歴区分1	文理区分1	学校名/学部名/学科名1	学歴メモ1	入学年月:年2	入学年月:月2	卒業年月:年2	卒業年月:月2	卒退区分2	学歴区分2	文理区分2	学校名/学部名/学科名2	学歴メモ2	その他学歴	現在の状況	勤務歴:勤務期間:開始年1	勤務歴:勤務期間:開始月1	勤務歴:勤務期間:終了年1	勤務歴:勤務期間:終了月1	勤務歴:会社名1	勤務歴:部署名1	勤務歴:役職名1	勤務歴:事業内容1	勤務歴:従業員数1	勤務歴:雇用形態1	勤務歴:担当職務1	勤務歴:備考1	勤務歴:勤務期間:開始年2	勤務歴:勤務期間:開始月2	勤務歴:勤務期間:終了年2	勤務歴:勤務期間:終了月2	勤務歴:会社名2	勤務歴:部署名2	勤務歴:役職名2	勤務歴:事業内容2	勤務歴:従業員数2	勤務歴:雇用形態2	勤務歴:担当職務2	勤務歴:備考2	その他勤務歴	転職回数	勤務歴メモ	勤務歴:業種1	勤務歴:職種カテゴリー1	勤務歴:職種1	勤務歴:経験年数1	勤務歴:業種2	勤務歴:職種カテゴリー2	勤務歴:職種2	勤務歴:経験年数2	勤務歴:業種3	勤務歴:職種カテゴリー3	勤務歴:職種3	勤務歴:経験年数3	勤務歴:業種4	勤務歴:職種カテゴリー4	勤務歴:職種4	勤務歴:経験年数4	勤務歴:業種5	勤務歴:職種カテゴリー5	勤務歴:職種5	勤務歴:経験年数5	勤務歴:業種6	勤務歴:職種カテゴリー6	勤務歴:職種6	勤務歴:経験年数6	英語:語学名称	英語:総合	英語:会話	英語:読解	英語:文章	英語:TOEIC	英語:TOEFL	語学力メモ	免許・資格	免許・資格メモ	スキル	スキルメモ	現在の年収	希望の年収	年収メモ	希望業種1	希望職種カテゴリー1	希望職種1	希望業種2	希望職種カテゴリー2	希望職種2	希望業種3	希望職種カテゴリー3	希望職種3	希望業種4	希望職種カテゴリー4	希望職種4	希望業種5	希望職種カテゴリー5	希望職種5	希望業種6	希望職種カテゴリー6	希望職種6	勤務地	転職時期	転居	休日	条件メモ	レジュメ名称	自己ＰＲ	特筆すべき事項	評価1	評価1メモ	評価2	評価2メモ	評価3	評価3メモ	メモ	登録日	登録者ユーザID	登録者	更新日	更新者ユーザID	更新者	OpenP	自由設定項目（セレクトボックス）	自由設定項目（チェックボックス）	自由設定項目（ラジオボタン）	自由設定項目（一行数値テキスト）	自由設定項目（複数行メモ）	登録日	更新日	NEWEID	更新者ID	企業CD	メール確認	希望雇用形態	転職理由	転職希望メモ"."\r\n";
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
				for ($j=1; $j<=$FieldMax; $j=$j+1){
					$StrSQL=$StrSQL."`".$FieldName[$j]."`";
					if ($j!=$FieldMax){
						$StrSQL=$StrSQL.",";
					} 
				}
				$StrSQL=$StrSQL.") values (";
				for ($j=1; $j<=$FieldMax; $j=$j+1){
					$StrSQL=$StrSQL."'".str_replace("'","''",$cols[$j])."'";
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
						$StrSQL=$StrSQL."`".$FieldName[$j]."`='".str_replace("'","''",$cols[$j])."',";
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
