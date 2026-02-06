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


$url_top=isset($argv[1]) ? $argv[1] : "";
$doc_root=isset($argv[2]) ? $argv[2] : "";
$tmp_filename=isset($argv[3]) ? $argv[3] : "";


define("DOC_ROOT", $doc_root);


$disp_str="<html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title></title></head>";
$disp_str.="※処理が長い場合このページをリロードすることで進捗が確認できます。※<br><br>処理を開始しました。<br>\n";
//echo $disp_str;
file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

$disp_str="DOC_ROOT:".DOC_ROOT."<br><br>\n\n";
//echo $disp_str."<br>\n";
file_put_contents($tmp_filename, $disp_str, FILE_APPEND);
//@ob_flush();
//@flush();


$code=checkUrlStatus($url_top);
$disp_str="HTTPステータスコード: ".$code."<br>";
file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

if($code==200){
	$disp_str="正常にトップページ($url_top)にアクセスできました。<br><br>";
}else{
	$disp_str="トップページ($url_top)にアクセスできませんでした。以下の原因が考えられます。<br><br>";
	$disp_str.="・ ロボット対策等が施されている場合があります。ブラウザで閲覧できるにもかかわらず、400台のエラーの場合に該当する可能性が高くなります。<br>";
	$disp_str.="・ 先方のサーバが一時的にアクセス不能の状態に陥ってる。500台のエラーの場合に該当する可能性が高くなります。<br>";
	$disp_str.="・ リンクの出力がJavaScriptによってされている場合はそのリンクを取得できません。<br><br>";

}
file_put_contents($tmp_filename, $disp_str, FILE_APPEND);



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
	file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

	//@ob_flush();
	//@flush();
}

$disp_str="<br><br>\n\n";
file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

//echo "\n\n";
//@ob_flush();
//@flush();



$cou=1;
foreach($url_list as $val){
	$info=get_info($val[0]);

	$disp_str='"'.$cou.'","'.$info["url"].'","'.$info["title"].'","'.$info["description"].'","'.$info["keywords"].'"'."<br>\n";
	//echo $disp_str;
	file_put_contents($tmp_filename, $disp_str, FILE_APPEND);

	//echo '"'.$cou.'","'.$info["url"].'","'.$info["title"].'","'.$info["description"].'","'.$info["keywords"].'"'."\n";
	//@ob_flush();
	//@flush();

	if($cou>=MAX_ELM) break;
	unset($info);
	$cou++;
}



//echo "</pre>";

$disp_str="\n\n<br><br>処理を終了しました。<br>\n";
//echo $disp_str;
file_put_contents($tmp_filename, $disp_str, FILE_APPEND);



?>