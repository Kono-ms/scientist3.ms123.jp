<?php
//echo等の出力のバッファリングを無効にし則出力
ini_set('max_execution_time', 0);
set_time_limit(0);
//ini_set('memory_limit', '1G');
ini_set('memory_limit', '-1');

//@ob_end_flush();
//@ob_implicit_flush();

require("simple_html_dom.php");
require("func2.php");

define("MAX_ELM", 500);

define('DB_HOST', 'mysql203.xbiz.ne.jp');
define('DB_USERNAME', 'ms123_scientist3');
define('DB_PASSWD', 'x7WYr3a9');
define('DB_DBNAME', 'ms123_scientist3');

// テスト用↓
// require("../crawl/simple_html_dom.php");
// require("func1.php");

// //echo等の出力のバッファリングを無効にし則出力
// ini_set('max_execution_time', 0);
// set_time_limit(0);
// //ini_set('memory_limit', '1G');
// ini_set('memory_limit', '-1');

// テスト用↑


$url_top=isset($argv[1]) ? $argv[1] : "";
$doc_root=isset($argv[2]) ? $argv[2] : "";
$tmp_filename=isset($argv[3]) ? $argv[3] : "";
$mid=isset($argv[4]) ? $argv[4] : ""; //M1のMID

// テスト用↓
// $url_top="https://ms123.co.jp/";
// $doc_root="https://ms123.co.jp/";
// $tmp_filename="tmp/2024_0206_1229_04-1983592151.html";
// $mid="M100001";
// テスト用↑

define("DOC_ROOT", $doc_root);

$disp_str="<html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title></title></head>";
$disp_str.="※処理が長い場合このページをリロードすることで進捗が確認できます。※<br><br>処理を開始しました。<br>\n";
// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

$disp_str="DOC_ROOT:".DOC_ROOT."<br><br>\n\n";
// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);


$code=checkUrlStatus($url_top);
$disp_str="HTTPステータスコード: ".$code."<br>";
// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

if($code==200){
	$disp_str="正常にトップページ($url_top)にアクセスできました。<br><br>";
}else{
	$disp_str="トップページ($url_top)にアクセスできませんでした。以下の原因が考えられます。<br><br>";
	$disp_str.="・ ロボット対策等が施されている場合があります。ブラウザで閲覧できるにもかかわらず、400台のエラーの場合に該当する可能性が高くなります。<br>";
	$disp_str.="・ 先方のサーバが一時的にアクセス不能の状態に陥ってる。500台のエラーの場合に該当する可能性が高くなります。<br>";
	$disp_str.="・ リンクの出力がJavaScriptによってされている場合はそのリンクを取得できません。<br><br>";

	$StrSQL=" UPDATE DAT_M1 SET M1_ETC09 = '".str_replace("'","''",$disp_str)."'";
	$StrSQL.=" WHERE MID = '".$mid."'";
	if (!(mysqli_query(ConnDB_crawl(),$StrSQL))) {
		file_put_contents($tmp_filename, "UPDATErr1:".$StrSQL, FILE_APPEND);
	}
}
// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);


// file_put_contents($tmp_filename, "探索開始", FILE_APPEND);

//echo "<pre>";
$url_list=array();
$url_list[]=array($url_top, "new");
//$url_list=get_all_url($url_top, $url_list);


for($idx=0;;$idx++){
	if( count( array_column($url_list, 0) ) >= MAX_ELM ) break;
	if( !isset($url_list[$idx]) ) break;

	$url_list=get_all_url($url_list[$idx][0], $url_list);
	$url_list[$idx][1]="done";
	
	$disp_str=($idx+1)."ページ目の探索が終わりました。<br>\n";
	//echo $disp_str;
	// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

	//@ob_flush();
	//@flush();
}

$disp_str="<br><br>\n\n";
// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

//echo "\n\n";
//@ob_flush();
//@flush();

// file_put_contents($tmp_filename, "探索終了", FILE_APPEND);

$carwl_str="";
$cou=1;
foreach($url_list as $val){
	$info=get_info($val[0]);

	// $disp_str='"'.$cou.'","'.$info["url"].'","'.$info["title"].'","'.$info["description"].'","'.$info["keywords"].'"'."<br>\n";
	$disp_str='"'.$cou.'","'.$info["url"].'","'.$info["html"].'"'."<br>\n";
	
	
	
	
	//echo $disp_str;
	// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

	// $val=$disp_str;
	// $StrSQL=" UPDATE DAT_M1 SET M1_ETC09 = concat(M1_ETC09,'".str_replace("'","''",$val)."')";
	// $StrSQL.=" WHERE MID = '".$mid."'";
	// if (!(mysqli_query(ConnDB_crawl(),$StrSQL))) {
	// 	// var_dump("UPDATErr2:".$StrSQL);
	// 	file_put_contents($tmp_filename, "UPDATErr2:".$StrSQL, FILE_APPEND);
	// 	// die;
	// }
	// // file_put_contents($tmp_filename, "UPDAT:".$StrSQL, FILE_APPEND);

	$carwl_str.=$disp_str;
	//echo '"'.$cou.'","'.$info["url"].'","'.$info["title"].'","'.$info["description"].'","'.$info["keywords"].'"'."\n";
	//@ob_flush();
	//@flush();

	if($cou>=MAX_ELM) break;
	unset($info);
	$cou++;
}


$StrSQL=" UPDATE DAT_M1 SET M1_ETC09 = '".str_replace("'","''",$carwl_str)."'";
$StrSQL.=" WHERE MID = '".$mid."'";
if (!(mysqli_query(ConnDB_crawl(),$StrSQL))) {
	// var_dump("UPDATErr2:".$StrSQL);
	file_put_contents($tmp_filename, "UPDATErr2:".$StrSQL, FILE_APPEND);
	// die;
}
// file_put_contents($tmp_filename, "UPDAT:".$StrSQL, FILE_APPEND);


//echo "</pre>";

$disp_str="\n\n<br><br>処理を終了しました。<br>\n";
//echo $disp_str;
// file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

//=========================================================================================================
//名前 DB初期化
//機能 DBとの接続を確立する
//引数 なし
//戻値 $function_ret
//=========================================================================================================
function ConnDB_crawl()
{
	// eval(globals());

	$ConnDB=mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);

	mysqli_set_charset($ConnDB, "utf8");
	return $ConnDB;
} 


?>