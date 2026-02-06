<?php
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ExportData()
{
	eval(globals());

	$csv_data = "";

	$StrSQL="SELECT * FROM ".$TableName." order by ID";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item<>"") {
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=member".date('Ymd').".txt");

		$str="";
		for ($j=0; $j<=$FieldMax; $j=$j+1){
			$StrSQL=$StrSQL."`".$FieldName[$j]."`";
			if ($str!=""){
				$str=$str."\t";
			} 
			$str=$str.$FieldName[$j];
		}
		$str=$str."\r\n";
		$csv_data = $str;
		$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
		echo($csv_data);
		while ($item = mysqli_fetch_assoc($rs)) {
			$str="";
			for ($i=0; $i<=$FieldMax; $i=$i+1){
				if ($i!=0){
					$str=$str."\t";
				}
				$str=$str.str_replace("\r\n", "[rn]", str_replace("\r", "[r]", str_replace("\n", "[n]", str_replace("\t", "[t]", $item[$FieldName[$i]]))));
			}
			$csv_data = $str."\r\n";
			$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
			echo($csv_data);
		} 
	} 

	return $function_ret;
}


?>