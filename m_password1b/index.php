<?php
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

	// 最初にセッションチェック
	// if(!CheckSession(1)) {
	// 	$url=BASE_URL . "/login1/";
	// 	header("Location: {$url}");
	// 	exit;
	// }

	if($_POST['mode']==""){
		$pass1=mysqli_real_escape_string(ConnDB(),$_GET['pass1']);
		$pass2=mysqli_real_escape_string(ConnDB(),$_GET['pass2']);
		$pass3=mysqli_real_escape_string(ConnDB(),$_GET['pass3']);
		$mode=mysqli_real_escape_string(ConnDB(),$_GET['mode']);
		$email=mysqli_real_escape_string(ConnDB(),$_GET['email']);
		$date=mysqli_real_escape_string(ConnDB(),$_GET['date']);
	} else {
		$pass1=mysqli_real_escape_string(ConnDB(),$_POST['pass1']);
		$pass2=mysqli_real_escape_string(ConnDB(),$_POST['pass2']);
		$pass3=mysqli_real_escape_string(ConnDB(),$_POST['pass3']);
		$mode=mysqli_real_escape_string(ConnDB(),$_POST['mode']);
		$email=mysqli_real_escape_string(ConnDB(),$_POST['email']);
		$date=mysqli_real_escape_string(ConnDB(),$_POST['date']);

	}

	$errmsg="";
	if(strDecrypt($email)!=""){
		$email=strDecrypt($email);
	}

	// $dateはDBから取得
	$StrSQL="SELECT * from DAT_M1 where EMAIL='".$email."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$date = $item['EDITDATE'];
	$pass_old = $item['PASS'];

	if($date!=""){
		$dt1=strtotime($date . " +1 hour");
		$dt2=strtotime(date('Y/m/d H:i:s'));
		 //echo "<!--１時間後:".$dt1."-->";
		 //echo "<!--現在日時:".$dt2."-->";
		if($dt1<$dt2){
			print "期限切れです。";
			exit;
		}
	}

	
	if($mode=="save"){
		
		// 前回のパスワードと同じではだめ
		if($pass2 == $pass_old) {
			$errmsg.="<font style='color:#ff0000;'>You cannot use the same password as the last time.</font><br>";
		}

	}

	DispData($mode,$pass1,$pass2,$pass3,$errmsg,$email,$date);

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispData($mode,$pass1,$pass2,$pass3,$errmsg,$email,$date)
{

	eval(globals());

	//各テンプレートファイル名
	$htmlnew = "edit.html";
	$htmlend = "end.html";


	$filename=$htmlnew;

	if($mode=="save" && $errmsg==""){
		
		$StrSQL ="UPDATE DAT_M1 SET ";
		$StrSQL.=" PASS = '".pwd_hash($pass2)."'"; 
		$StrSQL.=" where EMAIL='".$email."'"; 
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}


		$filename=$htmlend;
		
	}
	


	$fp=$DOCUMENT_ROOT.$filename;
	$str=@file_get_contents($fp);

	$str = MakeHTML($str,0,$lid);

	$str=str_replace("[ERRMSG]",$errmsg,$str);
	if($errmsg<>""){
		$str=DispParam($str, "ERR");
	} else {
		$str=DispParamNone($str, "ERR");
	}
	$str=str_replace("[email]",$email,$str);
	$str=str_replace("[date]",$date,$str);
	$str=str_replace("[BASE_URL]",BASE_URL,$str);
	print $str;

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
