<?php

session_start();
require "../config.php";
require "../base.php";
require '../a_m1/config.php';

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

	$filename="disp.html";
	// $filename = set_basic_authentication('disp.html','auth.html'); // 疑似BASIC認証
echo('<!--filename:' . $filename . '-->');
	$fp=$DOCUMENT_ROOT.$filename;
	$str=@file_get_contents($fp);

	$str = MakeHTML($str,0,$lid);

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
