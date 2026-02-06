<?php

//http不可。httpsのみ許可。
function checkURL_crawl($url){
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


function checkUrlStatus($url){
	$ch = curl_init();
	//最大リダイレクト回数
	//サーバがセーフモードでもリダイレクトできるように
	$limit=10;
	do{
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$html = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
	}while($code === 301 || $code === 302 and $limit--);
	
	curl_close($ch);

	return $code;
}


function file_get_by_curl($url){
	$ch = curl_init();
	//最大リダイレクト回数
	//サーバがセーフモードでもリダイレクトできるように
	$limit=10;
	do{
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$html = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
	}while($code === 301 || $code === 302 and $limit--);
	
	curl_close($ch);

	return $html;
}



/**
 * PHP5.5.0以前でarray_column()を使えるように。
 * 指定したキーの値を取得する。2次元配列のみ対応
 * @param target_data 値を取り出したい多次元配列
 * @param column_key  値を返したいカラム
 * @param index_key   返す配列のインデックスとして使うカラム
 * return array       入力配列の単一のカラムを表す値の配列を返し
 */
function array_column ($target_data, $column_key, $index_key = null) {

	if (is_array($target_data) === FALSE || count($target_data) === 0) return array();

	$result = array();
	foreach ($target_data as $array) {
		if (array_key_exists($column_key, $array) === FALSE) continue;
		if (is_null($index_key) === FALSE && array_key_exists($index_key, $array) === TRUE) {
			$result[$array[$index_key]] = $array[$column_key];
			continue;
		}
		$result[] = $array[$column_key];
	}

	if (count($result) === 0) return array();
	return $result;

}


function get_all_url($url, $url_list){
	$str_html=file_get_by_curl($url);
	$html=str_get_html($str_html);

	if(empty($html)){
		return $url_list;
	}

	foreach($html->find("a") as $obj){
		$link=$obj->href;
		$link=trim_url($link);
		
		if($link=="" || $link==NULL){
			continue;
		}



		$ary_col_url_list_0=array_column( $url_list, 0 );
		//var_dump($url_list);
		//var_dump($ary_col_url_list_0);

		if( !in_array( $link, $ary_col_url_list_0) ){
			$url_list[]=array($link,"new");
		}
		
	}

	$html->clear();
	unset($str_html);

	return $url_list;
}



function trim_url($url){
	
	$tmp=$url;
	$tmp=trim($tmp);

	//例：works.html#portal
	$tmp=preg_replace('/#.*/', '', $tmp);
	
	//例：tel:123456789
	$tmp=preg_replace('/^tel:[0-9]*/', '', $tmp);

	//相対パスの削除
	// ./ ../ ../../等
	$tmp=preg_replace('/^\..*/', '', $tmp);
	// /
	$tmp=preg_replace('/^\/$/', '', $tmp);

	if( preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $tmp) ){
		//絶対パスの処理
		//外部urlの削除
		$pos=strpos($tmp, DOC_ROOT);
		if(false === $pos || $pos!=0){
			$tmp="";
		}

	}else if($tmp!=""){
		//サイトルート相対パス（ドキュメントルート絶対パス）
		$tmp=preg_replace('/^\//', '', $tmp);
		$tmp=DOC_ROOT.$tmp;
	}

	//ラストがディレクトリなのに「/」がない場合に付与する
	//例：https://hoge.com/hoge
	//例：https://hoge.com
	if( substr($tmp, -1)!="/" and $tmp!="" ){
		$exploded_tmp=explode("/", $tmp);
		$last=end( $exploded_tmp );

		if("https://".$last."/" != DOC_ROOT){
			if( false===strpos($last, '.') ){
				$tmp=$tmp."/";
			}
		}else{
			$tmp=$tmp."/";
		}
	}


	return $tmp;
}



function get_info($url){
	$str_html=file_get_by_curl($url);
	$html=str_get_html($str_html);

	if(empty($html)){
		$info=array(
			"url"=>$url,
			"title"=>"",
			"description"=>"",
			"keywords"=>"",
			"html"=>"",
		);
		
		return $info;
	}

	$info=array();
	$info["url"]=$url;
	$info["title"]=isset($html->find("title",0)->plaintext) ? $html->find("title",0)->plaintext : "";
	$info["description"]=isset($html->find("meta[name=description]",0)->content) ? $html->find("meta[name=description]",0)->content : "";
	$info["keywords"]=isset($html->find("meta[name=keywords]",0)->content) ? $html->find("meta[name=keywords]",0)->content : "";
	$info["html"]=isset($html->find("html",0)->plaintext) ? $html->find("html",0)->plaintext : "";

	$html->clear();
	unset($str_html);

	return $info;
}


function make_filename(){
	$dir="tmp";
	my_mkdir($dir);
	$name="";
	$name=$dir."/".date("Y_md_Hi_s")."-".rand().".dat";

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







//function get_all_url($url){
//	//$html=file_get_html($url);
//	
//	$str_html=file_get_by_curl($url);
//	$html=str_get_html($str_html);
//
//	$url_list=array();
//	foreach($html->find("a") as $obj){
//		$link=$obj->href;
//		$link=trim_url($link);
//		//debug
//		$url_list[]=array($link,"");
//	}
//
//	$html->clear();
//
//	//debug
//	//$url_list[]=trim_url("https://ms123.co.jp/?hoge=%E3%81%AB%E3%81%BB%E3%82%93%E3%81%94");
//	//$url_list[]=trim_url("./aaa");
//
//	return $url_list;
//}


//function get_all_url($url){
//	//$html=file_get_html($url);
//	$context = stream_context_create(
//		[
//			"http"=>
//			[
//				"ignore_errors"=>true
//			]
//		]
//	);
//	
//	$str_html=file_get_contents($url, false, $context);
//	$pos=strpos($http_response_header[0], "200");
//	echo "http status:".$http_response_header[0]."<br><br>";
//	//echo $str_html."<br><br>";
//
//	if($pos === false){
//		return null;
//	}
//
//	$html=str_get_html($str_html);
//
//
//	$url_list="";
//	foreach($html->find("a") as $obj){
//		$url_list.="\n".$obj->href;
//	}
//
//	$html->clear();
//
//	return $url_list;
//}


////
////今回は使ってない
////配列の空の要素を削除
////$target:2次元配列
////
//function remove_empty_elm($target){
//	$ary=array();
//
//	foreach($target as $val){
//		if($val[0]==""){
//			continue;
//		}else{
//			$ary[]=$val;
//		}
//	}
//
//	return  $ary;
//}
//
//
////
////今回は使ってない
////2次元配列の重複する要素を削除（各行の第1インデックスの値だけみる）
////
//function remove_overlap_elm($target){
//	$ary=array();
//	$idx=0;
//	foreach($target as $val){
//		if( !in_array( $val[$idx], array_column( $ary, $idx ) ) ){
//			$ary[]=$val;
//		}
//	}
//	
//	return $ary;
//}



//function file_get_by_curl($url){
//	$ch = curl_init();
//	curl_setopt($ch, CURLOPT_URL, $url);
//	curl_setopt($ch, CURLOPT_HEADER, false);
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
//	
//	$html = curl_exec($ch);
//	curl_close($ch);
//
//	return $html;
//}

?>