<?php
	session_start();
	require "../config.php";
require "../base.php";
	require '../a_m1/config.php';

ini_set( 'display_errors', 0 );
set_time_limit(7200);

//InitSub();//データベースデータの読み込み
//ConnDB();//データベース接続
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

	if($_SESSION['MATT'] == "2"){

		$cliendId = $_COOKIE['cliendId'];
		//ログインデータ削除
		$StrSQL=" DELETE FROM DAT_SESSION ";
		$StrSQL.=" WHERE MID = '".$_SESSION['MID']."'";
		$StrSQL.=" AND CLIENT_ID = '".$cliendId."'";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

		setcookie('cliendId', "", time()-60); //クッキー削除
	}
	
	$_SESSION['MATT'] = "";
	$_SESSION['M-ID'] = "";
	$_SESSION['MID'] = "";
	$_SESSION['MNAME'] = "";
	$_SESSION['EMAIL'] = "";

	$url=BASE_URL . "/";
	header("Location: {$url}");


	exit;

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
