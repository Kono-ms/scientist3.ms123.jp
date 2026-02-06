<?php
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_m1/config.php';


//MIDのM以降の文字列
//$StrSQL="SELECT substring(MID,instr(MID, 'M')+2) from DAT_M1";

//$StrSQL="SELECT substring(MID,instr(MID, 'M')+2) + 1 AS open_unique_number FROM DAT_M1 WHERE (substring(MID,instr(MID, 'M')+2) + 1) NOT IN ( SELECT substring(MID,instr(MID, 'M')+2) FROM DAT_M1 ) AND (substring(MID,instr(MID, 'M')+2) + 1) <= substring('".M1_SYSTEM_MID."',instr('".M1_SYSTEM_MID."', 'M')+2) ORDER BY open_unique_number ASC limit 1";


$StrSQL="SELECT substring(MID,instr(MID, 'M')+2) + 1 AS open_unique_number FROM DAT_M1 WHERE (substring(MID,instr(MID, 'M')+2) + 1) NOT IN ( SELECT substring(MID,instr(MID, 'M')+2) FROM DAT_M1 ) AND (substring(MID,instr(MID, 'M')+2) + 1) <= substring('".M1_SYSTEM_MID."',instr('".M1_SYSTEM_MID."', 'M')+2) ORDER BY open_unique_number ASC limit 1";
$rs=mysqli_query(ConnDB(),$StrSQL);
$item = mysqli_fetch_assoc($rs);
var_dump($item);


$mid=sprintf("%05d", $item["open_unique_number"]);
$mid="M1".$mid;
echo "<br>mid: $mid<br>";

function ConnDB()
{
	eval(globals());

	$ConnDB=mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);

	return $ConnDB;
} 