<?php
//echo等の出力のバッファリングを無効にし則出力
ini_set('max_execution_time', 0);
set_time_limit(0);
//ini_set('memory_limit', '1G');
ini_set('memory_limit', '-1');

//@ob_end_flush();
//@ob_implicit_flush();

require("simple_html_dom.php");
require("func1.php");

define("MAX_ELM", 500);

$url_top=$_POST["url_top"] ?? "";
$url_top=trim($url_top);

//debug
//$url_top="https://www.minnanokaigo.com/";
//$url_top="https://zenn.dev/sa2kiryo/articles/87471a5935f40d559cba";

if( !checkURL_crawl($url_top) ){
	die("不正なurlです");
}

$doc_root=make_docRoot($url_top);
if($doc_root==""){
	die("不正なurlです");
}
define("DOC_ROOT", $doc_root);


$tmp_filename=make_filename();
//echo "tmp_filename:".$tmp_filename."<br>\n";


$exec_file=__DIR__."/crawl_proc.php";
$cmd = "nohup php -c '' '$exec_file' '$url_top' '$doc_root' '$tmp_filename' > nohup.dat &";
exec($cmd);

$disp_path="https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/".$tmp_filename;

echo "処理の依頼が完了しました。<br><br>";
echo "以下のurlから進捗状況と結果が確認できます。<br><br>※このurlは一度しか表示されません。urlの保存を忘れずにしてください。<br>";
echo "<a href='".$disp_path."' target='_blank'>".$disp_path."</a>";



?>