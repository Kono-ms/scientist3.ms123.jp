<?php

session_start();

require "../config.php";
require "../base.php";
//require './config.php';

set_time_limit(7200);

// ---------------------------------------------
// デバッグセット
// ---------------------------------------------
require_once(__dir__ . '/../handler.php');
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_error_handler('cms_error_handler', E_ALL);
register_shutdown_function('cms_shutdown_handler');
// ---------------------------------------------

//InitSub();//データベースデータの読み込み
ConnDB();//データベース接続

echo "test<br>";

/*
$SCNo_yy="";
$SCNo_mm="";
$SCNo_dd="";
$SCNo_cnt="";
$SCNo_else1="";
$SCNo_else2="";
*/


$SCNo=array(
	"SCNo_yy" => "", 
	"SCNo_mm" => "", 
	"SCNo_dd" => "", 
	"SCNo_cnt" => "", 
	"SCNo_else1" => "", 
	"SCNo_else2" => "", 
);

$now=strtotime("now");
echo "now:".date("Y/m/d H:i:s",$now)."<br>";

$SCNo["SCNo_yy"]=date("y",$now);
$SCNo["SCNo_mm"]=date("m",$now);
$SCNo["SCNo_dd"]=date("d",$now);



$StrSQL="SELECT MAX(CAST(`SCNo_cnt` AS SIGNED)) as max_scno_cnt from DAT_FILESTATUS ";
$StrSQL.=" where SCNo_yy ='".$SCNo["SCNo_yy"]."' ";
$StrSQL.=" and SCNo_mm='".$SCNo["SCNo_mm"]."' ";
$StrSQL.=" and SCNo_dd='".$SCNo["SCNo_dd"]."' ";
$scno_rs=mysqli_query(ConnDB(),$StrSQL);
$scno_item=mysqli_fetch_assoc($scno_rs);
$SCNo["SCNo_cnt"]=sprintf("%05d", $scno_item["max_scno_cnt"]+1);





//以下の関数をbase.phpに新規作成した
/*

//数字からアルファベットのIDに変換
function generateAlphabetId($number) {
	if ($number < 1) {
		return null; // 無効な入力
	}

	$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$base = 26; // アルファベットの数
	$id = '';

	// 数値をアルファベットIDに変換
	while ($number > 0) {
		$number--; // 0ベースに調整（A=1, B=2, ..., Z=26）
		$remainder = $number % $base; // 余り
		$id = $alphabet[$remainder] . $id; // アルファベットを前に追加
		$number = (int)($number / $base); // 次の桁へ
	}

	return $id;
}


//アルファベットのIDから、数字に変換
function getAlphabetNumber($alphabet) {
	if (empty($alphabet) || !preg_match('/^[A-Z]+$/', $alphabet)) {
		return null; // 無効な入力（空またはアルファベット以外）
	}

	$base = 26; // アルファベットの数
	$number = 0;
	$alphabet = strtoupper($alphabet); // 大文字に統一
	$alphabetMap = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ')); // アルファベットをインデックスに変換

	// アルファベットIDを数値に変換
	for ($i = 0; $i < strlen($alphabet); $i++) {
		$number = $number * $base + ($alphabetMap[$alphabet[$i]] + 1); // 1ベース（A=1, B=2, ...)
	}

	return $number;
}


//配列から「Scientist3 control No.」を作成。
function formatAlphabetId($SCNo_ary){
	$SCNo_str="";
	$i=0;
	foreach ($SCNo_ary as $key => $val) {
		if($i==0) $SCNo_str.="SC";
		if($key=="SCNo_else1" || $key=="SCNo_else2"){
			if($val!="" && !is_null($val)){
				$SCNo_str.="-".$val;
			}
		}else{
			$SCNo_str.=$val;
		}
		$i++;
	}

	return $SCNo_str;
}

*/


/*
// 使用例
for ($i = 1; $i <= 30; $i++) {
	echo "Number $i: " . generateAlphabetId($i) . "<br>";
}

echo "A:".getAlphabetNumber("A")."<br>";
echo "Z:".getAlphabetNumber("Z")."<br>";
echo "AA:".getAlphabetNumber("AA")."<br>";
echo "ZZ:".getAlphabetNumber("ZZ")."<br>";
*/


$SCNo["SCNo_else1"]=generateAlphabetId(1);
$SCNo["SCNo_else2"]=1;

//$SCNo["SCNo_else1"]="";
//$SCNo["SCNo_else2"]="1";




echo "<pre>";
var_dump($SCNo);
echo "</pre>";

echo "formated SCNo:".formatAlphabetId($SCNo);



////////////////////
//輸出代行費用
$StrSQL="SELECT * FROM DAT_AGENCY_SETTING";
$exp_fee_rs=mysqli_query(ConnDB(),$StrSQL);
$exp_fee_ary=array();
while($exp_fee_item= mysqli_fetch_assoc($exp_fee_rs)){
	$as_keyword_currency="";
	$as_keyword_currency=str_replace("AS_KEYWORD_CURRENCY:", "", $exp_fee_item["AS_KEYWORD_CURRENCY"]);
	if($as_keyword_currency!=""){
		$exp_fee_ary[$as_keyword_currency]=$exp_fee_item["AS_EXPORT_FEE"];
	}
}
echo "<pre>";
var_dump($exp_fee_ary);
echo "</pre>";

$m2_export_fee_table=json_encode($exp_fee_ary);
echo "json:$m2_export_fee_table<br>";

//必要ないのに関数通した場合どうなるか
echo "htmlspecialchars_decode:".htmlspecialchars_decode($m2_export_fee_table,ENT_QUOTES);




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