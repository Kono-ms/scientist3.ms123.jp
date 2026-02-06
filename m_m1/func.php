<?php
//=========================================================================================================
//名前 
//機能\ 禁則処理を施したhtml文字列を返します
//引数 $html:String
//戻値 $html:String
//=========================================================================================================
function getKinsokuHTML($html)
{
	// 編集個所をbodyに限定
	preg_match('/<body[\s\S]+<\/body>/',$html,$m);
	$body = $m[0];
	$html = preg_replace('/<body[\s\S]+<\/body>/','%%BODY%%',$html);

	// ホワイトスペース削除
	// NOTE: 半角スペースは自動改行をされる場合があるので無駄な半角スペースの削除が必要
	$body = preg_replace_callback('/<(\w+?)([\s\S]+?)>/u', function ($matches) {
		$name = $matches[1];
		$attr = $matches[2];
		$attr = preg_replace('/\s{2,}|[\n\r\t]{1,}/',' ',$attr);// タグ中改行削除
		$result = '<'.$name.$attr.'>';
		return $result;
	}, $body);
	$body = preg_replace('/(\d{1,2})\.(\s+?)(\S)/u','$1.&nbsp;$3',$body);// 1. 2. ホワイトスペース削除
	$body = preg_replace('/(\d{1,2})\)(\s+?)(\S)/u','$1)&nbsp;$3',$body);// (1) (2) ホワイトスペース削除
	$body = preg_replace('/([^>])\n\s+?<\/li>/u','$1</li>',$body);// </li>前のホワイトスペース削除
	$body = preg_replace('/([^>])\n\s+?<\/p>/u','$1</p>',$body);// </p>前のホワイトスペース削除
	$body = preg_replace('/<(.+?)>[ \t]+/u','<$1>',$body);// タグ直後のホワイトスペース削除
	$body = preg_replace('/([\p{Hiragana}\p{Katakana}\p{Han}])(\s{2,}|[\n\r\t]{1,})([\p{Hiragana}\p{Katakana}\p{Han}])/u','$1$3',$body);// 日本語と日本語の間のホワイトスペース削除
	$body = preg_replace_callback('/<p(.*?)>([\s\S]+?)<\/p>/u', function ($matches) {
		$attr = $matches[1];
		$text = $matches[2];
		if(!preg_match('/<(.+?)>/',$text)) $text = preg_replace('/[\n\r\t]/u','',$text);
		$result = '<p'.$attr.'>'.$text.'</p>';
		return $result;
	}, $body);
	$body = preg_replace_callback('/<li(.*?)>([\s\S]+?)<\/li>/u', function ($matches) {
		$attr = $matches[1];
		$text = $matches[2];
		if(!preg_match('/<(.+?)>/',$text)) $text = preg_replace('/[\n\r\t]/u','',$text);
		$result = '<li'.$attr.'>'.$text.'</li>';
		return $result;
	}, $body);

	// 処理1 禁則したい個所を指定
	$body = preg_replace_callback('/(.)([？！。、」』】はがのをにへとで])/u', function ($matches) {
		$str1 = $matches[1];
		$str2 = $matches[2];
		$result = '%%KINSOKU-START%%'.$str1.$str2.'%%KINSOKU-END%%';
		return $result;
	}, $body);

	// 処理2 </strong%%KINSOKU-START%%>,  </a%%KINSOKU-START%%> 対応
	$body = preg_replace_callback('/(<\/(?:[\w\d="" \-]+?)%%KINSOKU-START%%>)/u', function ($matches) {
		$str = $matches[0];
		$str = str_replace('%%KINSOKU-START%%','',$str);
		$str = $str.'%%KINSOKU-START%%';
		$result = $str;
		return $result;
	}, $body);

	// 処理3 <span nobr="true"></span> で囲う
	$body = preg_replace('/%%KINSOKU-START%%([\s|\S]+?)%%KINSOKU-END%%/u','<span nobr="true">$1</span>',$body);

	// 処理4 句読点
	$body = preg_replace('/<\/span>([？！。、」』】])/u','$1</span>',$body);

	// 処理5 不要タグ結合
	// NOTE: spanなどのインライン要素とインライン要素の間でも自動改行が入るので減らす
	$body = preg_replace('/<span nobr="true">([\s|\S]+?)<\/span><span nobr="true">/u','<span nobr="true">$1',$body);

	$html = str_replace('%%BODY%%',$body,$html);
	return $html;
}
?>

