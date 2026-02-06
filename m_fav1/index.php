<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_o2/config.php';

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
		$url=BASE_URL . "/login1/";
		header("Location: {$url}");
		exit;
	}

	if ($mode==""){
		$mode="list";
	}

	// 2021.03.15 yamamoto 全いいねに既読フラグをたてる
	$StrSQL="UPDATE DAT_IINE SET ETC01 = '既読' WHERE MIDT='".$_SESSION['MID']."' and (ETC01 is null or ETC01 != 1)";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}


	if ($page==""){
		$page=1;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token);

	return $function_ret;
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

	$filename="../common/template/listm2.html";
	$fp=$DOCUMENT_ROOT.$filename;
	$strM=@file_get_contents($fp);

	// SQLインジェクション対策
	$StrSQL="SELECT DAT_M2.* FROM DAT_M2 inner join DAT_IINE on DAT_IINE.MID=DAT_M2.MID and DAT_IINE.MIDT='".$_SESSION['MID']."' ";
	// 2021.03.15 yamamoto ENABLE:公開中のみ表示
	$StrSQL.=" and DAT_M2.ENABLE = 'ENABLE:公開中'";
	//2020/12/28 gaosan ADD START
	$StrSQL .= " and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_M2.MID) ";
	//2020/12/28 gaosan ADD END

	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item=="") {
		$reccount=0;
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
				$str=$str." <a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\" class=\"inactive\">".$i."</a>";
			} 
		}
		$pagestr=$str;

		$CurrentRecord=1;
		$strMain="";
		while ($item = mysqli_fetch_assoc($rs)) {

			$str=$strM;

			$str=DispM2($item, $str);

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
	$str=str_replace("[RECCOUNT]",$reccount,$str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

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
