<?php

//http不可。httpsのみ許可。
function checkURL($url){
	//if( preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url) ){
	if( preg_match('/^(https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url) ){

		return true;
	}

	return false;
}



function make_docRoot($url){
	$tmp=$url;

	//末尾に「/」がなかった場合は付与
	if( substr($tmp, -1)!="/" ){
		$tmp=$tmp."/";
	}

	//https://以降で最初の「/」までを抜き出す
	preg_match('/https:\/\/.*?\//',$tmp,$match);
	//$tmp=$match[0] ?? "";
	$tmp=isset($match[0]) ? $match[0] : "";

	return $tmp;
}


function make_filename(){
	$dir="tmp";
	my_mkdir($dir);
	$name="";
	$name=$dir."/".date("Y_md_Hi_s")."-".rand().".html";

	return $name;
}



//サーバによって「/hoge1/hoge2/hoge3」等の階層構造のmkdirで権限関係のエラーがでて使えない対策
function my_mkdir($dir){
	$sDir=explode("/", $dir);
	
	$num=count($sDir);
	$treeDir=array();
	for($i=0; $i<$num; $i++){
		$tmp="";
		for($j=0; $j<=$i; $j++){
			$tmp.=$sDir[$j]."/";
		}
		$treeDir[$i]=$tmp;
	}

	foreach ($treeDir as $val) {
		if(!file_exists($val)){
			mkdir($val);
		}
	}
}



?>