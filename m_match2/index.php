<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_o1/config.php';

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
		$sel1=$_GET['sel1'];
		$sel2=$_GET['sel2'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$sel1=$_POST['sel1'];
		$sel2=$_POST['sel2'];
	}

	if(is_array($word)==true){
		$tmp="";
		for($i=0; $i<count($word); $i++){
			if($tmp!=""){
				$tmp.="\t";
			}
			$tmp.=$word[$i];
		}
		$word=$tmp;
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
		$word=str_replace("\t\t", "\t", $word);
	}

	if($_SESSION['MID']==""){
		
		$url=BASE_URL . "/login2/";
		header("Location: {$url}");
		exit;
	}

	if ($mode==""){
		$mode="list";
	}
	if ($page==""){
		$page=1;
	} 
	if ($sort==""){
		$sort=3;
	}

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$sel1,$sel2);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$sel1,$sel2)
{

	eval(globals());

	//各テンプレートファイル名
	$htmllist = "list.html";

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

	$filename="../common/template/listo1.html";
	$fp=$DOCUMENT_ROOT.$filename;
	$strM=@file_get_contents($fp);

	// SQLインジェクション対策
	$StrSQL="SELECT DAT_O1.* FROM DAT_O1 inner join DAT_M1 on DAT_M1.MID=DAT_O1.MID inner join DAT_MATCH on DAT_MATCH.OID1=DAT_O1.OID and DAT_MATCH.MID2='".$_SESSION['MID']."' ";
	//2020/12/28 gaosan ADD START
	$StrSQL .= " and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_O1.MID) ";
	//2020/12/28 gaosan ADD END
	$StrSQL .= " and ".ListSQLSearch($sort,$word,$sel1,$sel2,"");

	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item=="") {
		$reccount=0;
		$pagestr="";
		$strMain="<div class='result__item'><p class='result__ttl'>No search results were found.<p></div><div class='result__item'><p class='result__txt'>Researchersの希望情報の登録はお済みでしょうか？検索結果の表示にはResearchersの希望情報の登録が必要になります。まだ未登録の場合は先にこちらから登録をお願いします。</p><div class='btn result__btn'><a href='/m_o2/?mode=new&sort=&word=[L-MID]&page=1'>Researchersの希望情報を作成する</a></div></div>";
	} else {
		//================================================================================================
		//ページング処理
		//================================================================================================
		$reccount=mysqli_num_rows($rs);
		$pagecount=intval(($reccount-1)/$PageSize+1);
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
		for ($i=$s; $i<=$e; $i=$i+1) {
			if ($i==intval($page)) {
				$str=$str."<span class=\"current\">".$i."</span>";
			} else {
				$str=$str." <a href=\"".MakeUrl($sort,$word,$i)."\" class=\"inactive\">".$i."</a>";
			} 
		}
		$pagestr=$str;

		$CurrentRecord=1;
		$strMain="";
		while ($item = mysqli_fetch_assoc($rs)) {

			$str=$strM;

			$str=DispO1($item, $str);
			$str=DispPoint1($item['OID'], $str);

			$StrSQL="SELECT * FROM DAT_M1 where MID='".$item['MID']."'";
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			$item2=mysqli_fetch_assoc($rs2);
			$str=DispM1($item2, $str);

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
	$str=str_replace("[SEL1]",$sel1,$str);
	$str=str_replace("[SEL2]",$sel2,$str);
	$str=str_replace("[KEY]",$key,$str);
	$str=str_replace("[LID]",$lid,$str);
	$str=str_replace("[RECCOUNT]",$reccount,$str);

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
function MakeUrl($sort,$word, $sel1, $sel2,$page)
{

	return "/m_search2/?mode=list&sort=".urlencode($sort)."&word=".urlencode($word)."&page=".urlencode($page)."&sel1=".urlencode($sel1)."&sel2=".urlencode($sel2);

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
