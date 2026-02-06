<?php
session_start();
ini_set( 'display_errors', 0 );
date_default_timezone_set('Asia/Tokyo');

Main();

//=========================================================================================================
//名前 Main関数
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function Main()
{

	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$pass = isset($_POST['pass']) ? $_POST['pass'] : '';

	if($_SESSION['basic_authentication'] == 'lock') {
		exit('lock2');
	}
	else if($id == 'Scientist3CosmoADM.' && $pass == 'eZ7@L3zA') {
		$_SESSION['basic_authentication'] = 'ok';
		exit('ok');
	}
	else {
		$ba_cnt = intval($_SESSION['basic_authentication_cnt']);
		$_SESSION['basic_authentication_cnt'] = $ba_cnt + 1;
		if($ba_cnt == 2) {
			$_SESSION['basic_authentication'] = 'lock';
			$_SESSION['basic_authentication_dt'] = date('Y-m-d H:i:s');
		  exit('lock');
		}
		else {
			$_SESSION['basic_authentication'] = 'ng';
			exit('ng');
		}
	}

} 

?>
