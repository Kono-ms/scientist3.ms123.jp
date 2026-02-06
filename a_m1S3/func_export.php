<?php
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ExportData($expo)
{
	eval(globals());

	$csv_data = "";

	$StrSQL="SELECT * FROM ".$TableName." WHERE 1=1 ";
	$tmps=explode(",",$_SESSION['a_m1_expo_ids']);

	// if(count($tmps)>0){
	if($_SESSION['a_m1_expo_ids']!=""){
		$StrSQL.=" AND ID IN (";
		for ($i=0; $i<=count($tmps); $i++) {
			$id=$tmps[$i];
			if($i>0){
				$StrSQL.=",";
			}
			$StrSQL.="'".$id."'";
		}
		$StrSQL.=" ) ";
	}
	$StrSQL.=" order by ID";
// echo $StrSQL;
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item<>"") {
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=m1".date('YmdHis').".csv");

		$str="";



		$labels=LABLE_EXPORT_LIST; //画面項目
		$vals=VALUE_EXPORT_LIST; //DB項目
		$vals_array=explode("::",$vals);
		// if($expo==""){
			$export_array=explode("::",$labels);
		// } else {
		// 	$export_array=explode("\t",str_replace("expo:","",$expo));
		// }

		$label_array=explode("::",$labels);
		$val_array=explode("::",$vals);

		for ($j=0; $j<=count($export_array); $j++) {
			$label=$export_array[$j];
			if($label!=""){
				if ($str!=""){
					$str.=",";
				} 
				$str.="\"".$label."\"";
			}
		}


		$str=$str."\r\n";
		$csv_data = $str;
		$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
		echo($csv_data);
		while ($item = mysqli_fetch_assoc($rs)) {
			$str="";
			for ($j=0; $j<=count($export_array); $j++) {
				$label=$export_array[$j];
				if($label!=""){
					// $idx = array_search($label, $label_array);
					// $val=$val_array[$idx];
					$val=$vals_array[$j];
					if ($j!=0){
						$str=$str.",";
					}
					$data=str_replace($val.":","",$item[$val]);
					$str=$str."\"".str_replace("\r\n", "[rn]", str_replace("\r", "[r]", str_replace("\n", "[n]", str_replace("\t", "[t]", $data))))."\"";

				}
			}
			
			$csv_data = $str."\r\n";
			$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
			echo($csv_data);
		} 
	} 

	return $function_ret;
} 


?>