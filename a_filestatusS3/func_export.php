<?php
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ExportData($kbn,$expo,$date1,$date2,$price1,$price2,$currency,$customer_name)
{
	eval(globals());

	$csv_data = "";

	$StrSQL="  SELECT ";
	$StrSQL.=" DAT_FILESTATUS.ID  ";
	$StrSQL.=" ,DAT_FILESTATUS.SHODAN_ID  ";
	$StrSQL.=" ,DAT_FILESTATUS.M2_NOHIN_TYPE  ";
	$StrSQL.=" ,DAT_FILESTATUS.M2_SPECIAL_NOTE  ";
	$StrSQL.=" ,DAT_FILESTATUS.MID1  ";
	$StrSQL.=" ,DAT_FILESTATUS.MID2  ";
	$StrSQL.=" ,M1_DVAL01  ";
	$StrSQL.=" ,M1_DVAL04  ";
	$StrSQL.=" ,M2_DVAL01  ";
	$StrSQL.=" ,M2_DVAL03  ";
	$StrSQL.=" ,M2_DVAL16  ";
	$StrSQL.=" ,M2_DVAL17  ";
	$StrSQL.=" ,M2_DVAL18  ";
	$StrSQL.=" ,M2_DVAL19  ";

	$StrSQL.=" ,DAT_FILESTATUS.SCNo_yy  ";
	$StrSQL.=" ,DAT_FILESTATUS.SCNo_mm  ";
	$StrSQL.=" ,DAT_FILESTATUS.SCNo_dd  ";
	$StrSQL.=" ,DAT_FILESTATUS.SCNo_cnt  ";
	$StrSQL.=" ,DAT_FILESTATUS.SCNo_else1  ";
	$StrSQL.=" ,DAT_FILESTATUS.SCNo_else2  ";
	
	$StrSQL.=" ,DAT_FILESTATUS.M2_CURRENCY  ";

	$StrSQL.=" ,DAT_FILESTATUS_DETAIL.M2_DETAIL_ITEM  ";
	$StrSQL.=" ,DAT_FILESTATUS_DETAIL.M2_DETAIL_DESCRIPTION  ";
	$StrSQL.=" ,DAT_FILESTATUS_DETAIL.M2_DETAIL_QUANTITY  ";
	$StrSQL.=" ,DAT_FILESTATUS_DETAIL.M2_DETAIL_UNIT_PRICE  ";
	$StrSQL.=" ,DAT_FILESTATUS_DETAIL.M2_DETAIL_PRICE  ";
	$StrSQL.=" ,DAT_FILESTATUS_DETAIL.M2_DETAIL_NOTE  ";

	$StrSQL.=" ,DAT_FILESTATUS.M_STATUS  ";
	$StrSQL.=" ,DAT_FILESTATUS.H_M2_ID  ";
	$StrSQL.=" ,DAT_FILESTATUS.H_COMMENT  ";

	$StrSQL.="  ,ifnull(";
	$StrSQL.="  cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3)) ";
	$StrSQL.="   ,0) as MITSUMORISYO_SUBTOTAL1 "; //小計1

	$StrSQL.=" ,CASE WHEN M1_DVAL04 = 'M1_DVAL04:Japan' THEN 10 ELSE 0 END MITSUMORISYO_TAX_RATE1"; //消費税率

	$StrSQL.="  ,ifnull(";
	$StrSQL.=" ((cast(CASE WHEN M1_DVAL04 = 'M1_DVAL04:Japan' THEN 10 ELSE 0 END as DECIMAL(20,3))";
	$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3)))) / 100) ";
	$StrSQL.="   ,0) as MITSUMORISYO_TAX_BILL1 "; //消費税1

	$StrSQL.=" ,M2_ETC02"; //PF手数料率

	$StrSQL.="  ,ifnull(";
	$StrSQL.="  ((cast(M2_ETC02 as DECIMAL(20,3))";
	$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3)))) / 100) ";
	$StrSQL.="   ,0) as MITSUMORISYO_PF_FEE "; //PF手数料

	$StrSQL.=" ,cast(M2_IMPORT_FEE as DECIMAL(20,3)) as M2_IMPORT_FEE"; //輸入代行費用
	
	$StrSQL.=" ,MITSUMORISYO_EXPORT_FEE"; //輸出代行費用
	$StrSQL.=" ,cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) M2_MANAGE_DISCOUNT"; //特別値引き

	 //小計2:PF手数料+輸入代行費用+輸出代行費用-特別値引き（運営）
	$StrSQL.="  ,ifnull(";
	$StrSQL.="   (cast(M2_ETC02 as DECIMAL(20,3))";
	$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
	$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ";
	$StrSQL.="   ,0) as MITSUMORISYO_SUBTOTAL2 ";

	$StrSQL.=" ,cast(DAT_FILESTATUS.M2_TAX_RATE2 as DECIMAL(20,3)) as M2_TAX_RATE2  "; //税率2

	//消費税2(小計2*税率2)
	$StrSQL.="  , ifnull(";
	$StrSQL.="  ((cast(M2_ETC02 as DECIMAL(20,3))";
	$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
	$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ) ";
	$StrSQL.="  * cast(DAT_FILESTATUS.M2_TAX_RATE2 as DECIMAL(20,3)) / 100 "; 
	$StrSQL.="   ,0) as MITSUMORISYO_TAX_BILL2 ";//消費税2

	//合計金額(小計1+消費税1+小計2+消費税2)
	//小計1
	$StrSQL.="  ,ifnull(";
	$StrSQL.="  cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))";
	$StrSQL.="   ,0)";
	//消費税1
	$StrSQL.=" + ";
	$StrSQL.="   ifnull(";
	$StrSQL.="  ((cast(CASE WHEN M1_DVAL04 = 'M1_DVAL04:Japan' THEN 10 ELSE 0 END as DECIMAL(20,3))";
	$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3)))) / 100) ";
	$StrSQL.="   ,0)";
	//小計2
	$StrSQL.=" + ";
	$StrSQL.="   ifnull(";
	$StrSQL.="   (cast(M2_ETC02 as DECIMAL(20,3))";
	$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
	$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ";
	$StrSQL.="   ,0)";
	//消費税2
	$StrSQL.=" + ";
	$StrSQL.="   ifnull(";
	$StrSQL.="   (((cast(M2_ETC02 as DECIMAL(20,3))";
	$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
	$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
	$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
	$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
	$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ) ";
	$StrSQL.="  * cast(DAT_FILESTATUS.M2_TAX_RATE2 as DECIMAL(20,3)) / 100) ";
	$StrSQL.="   ,0)";
	$StrSQL.=" as MITSUMORISYO_ALL_CHARGE ";

	$StrSQL.=" FROM DAT_FILESTATUS  ";
	$StrSQL.=" LEFT JOIN ";
	$StrSQL.=" (SELECT FILESTATUS_ID ";
	$StrSQL.=" ,ifnull(SUM(M2_DETAIL_HANDLING_FEE),0) as M2_DETAIL_HANDLING_FEE ";
	$StrSQL.=" ,ifnull(SUM(M2_DETAIL_PRICE),0) as M2_DETAIL_PRICE ";
	$StrSQL.=" FROM DAT_FILESTATUS_DETAIL";
	$StrSQL.=" GROUP BY FILESTATUS_ID ) as FILESTATUS_DETAIL";
	if($kbn=="1"){
		$StrSQL.=" ON DAT_FILESTATUS.ID = FILESTATUS_DETAIL.FILESTATUS_ID";
	} else {
		$StrSQL.=" ON DAT_FILESTATUS.H_M2_ID = FILESTATUS_DETAIL.FILESTATUS_ID";
	}
	

	$StrSQL.=" LEFT JOIN DAT_FILESTATUS_DETAIL ";
	if($kbn=="1"){
		$StrSQL.=" ON DAT_FILESTATUS.ID = DAT_FILESTATUS_DETAIL.FILESTATUS_ID";
	} else {
		$StrSQL.=" ON DAT_FILESTATUS.H_M2_ID = DAT_FILESTATUS_DETAIL.FILESTATUS_ID";
	}
	

	// $StrSQL.=" AND (";
	// $StrSQL.="     ifnull(DAT_FILESTATUS_DETAIL.M2_DETAIL_ITEM,'') !='' ";
	// $StrSQL.=" OR ifnull(DAT_FILESTATUS_DETAIL.M2_DETAIL_DESCRIPTION,'') !=''  ";
	// $StrSQL.=" OR ifnull(DAT_FILESTATUS_DETAIL.M2_DETAIL_QUANTITY,'') !=''  ";
	// $StrSQL.=" OR ifnull(DAT_FILESTATUS_DETAIL.M2_DETAIL_UNIT_PRICE,'') !=''  ";
	// $StrSQL.=" OR ifnull(DAT_FILESTATUS_DETAIL.M2_DETAIL_PRICE,'') !=''  ";
	// $StrSQL.=" OR ifnull(DAT_FILESTATUS_DETAIL.M2_DETAIL_NOTE,'') !=''  ";
	// $StrSQL.=" ) ";


	$StrSQL.=" LEFT JOIN DAT_M1";
	$StrSQL.=" ON DAT_FILESTATUS.MID1= DAT_M1.MID";
	$StrSQL.=" LEFT JOIN DAT_M2";
	$StrSQL.=" ON DAT_FILESTATUS.MID2= DAT_M2.MID";

	//ダミーでMITSUMORISYO_EXPORT_FEEを用意しています、後で調整してください。※No1269で実装する値を使用（更新日時などの条件あり）
	$StrSQL.=" LEFT JOIN (SELECT 0 as MITSUMORISYO_EXPORT_FEE ) as DAT_MITSUMORISYO_EXPORT_FEE ON 1=1";


	if($kbn=="1"){
		$StrSQL.=" WHERE DAT_FILESTATUS.STATUS='見積り送付' ";
	} else{
		$StrSQL.=" WHERE DAT_FILESTATUS.STATUS='発注依頼' ";
	}



	$tmps=explode(",",$_SESSION['a_filestatus_expo_ids']);
	if($_SESSION['a_filestatus_expo_ids']!=""){
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
	

	if($date1!=""){
		$StrSQL.="  AND DATE_FORMAT(STR_TO_DATE(DAT_FILESTATUS.NEWDATE, '%Y/%m/%d %H:%i:%s'), '%Y%m%d%H%i%s') ";
		$StrSQL.="  >= DATE_FORMAT(STR_TO_DATE('".$date1."', '%Y-%m-%dT%H:%i:%s'), '%Y%m%d%H%i%s') ";
	}
	if($date2!=""){
		$StrSQL.="  AND DATE_FORMAT(STR_TO_DATE(DAT_FILESTATUS.NEWDATE, '%Y/%m/%d %H:%i:%s'), '%Y%m%d%H%i%s') ";
		$StrSQL.="  <= DATE_FORMAT(STR_TO_DATE('".$date2."', '%Y-%m-%dT%H:%i:%s'), '%Y%m%d%H%i%s') ";
	}
	if($price1!=""){
		// $StrSQL.="  AND CAST(MITSUMORISYO_ALL_CHARGE as signed) >= CAST('".$price1."' as signed) ";

		//合計金額(小計1+消費税1+小計2+消費税2)
		//小計1
		$StrSQL.="  AND CAST(";
		$StrSQL.="  ifnull(";
		$StrSQL.="  cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))";
		$StrSQL.="   ,0)";
		//消費税1
		$StrSQL.=" + ";
		$StrSQL.="   ifnull(";
		$StrSQL.="  ((cast(CASE WHEN M1_DVAL04 = 'M1_DVAL04:Japan' THEN 10 ELSE 0 END as DECIMAL(20,3))";
		$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3)))) / 100) ";
		$StrSQL.="   ,0)";
		//小計2
		$StrSQL.=" + ";
		$StrSQL.="   ifnull(";
		$StrSQL.="   (cast(M2_ETC02 as DECIMAL(20,3))";
		$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
		$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ";
		$StrSQL.="   ,0)";
		//消費税2
		$StrSQL.=" + ";
		$StrSQL.="   ifnull(";
		$StrSQL.="   (((cast(M2_ETC02 as DECIMAL(20,3))";
		$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
		$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ) ";
		$StrSQL.="  * cast(DAT_FILESTATUS.M2_TAX_RATE2 as DECIMAL(20,3)) / 100) ";
		$StrSQL.="   ,0)";
		$StrSQL.="   as signed) ";
		$StrSQL.="  >= CAST('".$price1."' as signed) ";

	}

	if($currency!=""){
		$StrSQL.="  AND DAT_FILESTATUS.M2_CURRENCY = '".$currency."'";
	}
	if($price2!=""){
		// $StrSQL.="  AND CAST(MITSUMORISYO_ALL_CHARGE as signed) <= CAST('".$price2."' as signed) ";
		//合計金額(小計1+消費税1+小計2+消費税2)
		//小計1
		$StrSQL.="  AND CAST(";
		$StrSQL.="  ifnull(";
		$StrSQL.="  cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))";
		$StrSQL.="   ,0)";
		//消費税1
		$StrSQL.=" + ";
		$StrSQL.="   ifnull(";
		$StrSQL.="  ((cast(CASE WHEN M1_DVAL04 = 'M1_DVAL04:Japan' THEN 10 ELSE 0 END as DECIMAL(20,3))";
		$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3)))) / 100) ";
		$StrSQL.="   ,0)";
		//小計2
		$StrSQL.=" + ";
		$StrSQL.="   ifnull(";
		$StrSQL.="   (cast(M2_ETC02 as DECIMAL(20,3))";
		$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
		$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ";
		$StrSQL.="   ,0)";
		//消費税2
		$StrSQL.=" + ";
		$StrSQL.="   ifnull(";
		$StrSQL.="   (((cast(M2_ETC02 as DECIMAL(20,3))";
		$StrSQL.="  * (cast(FILESTATUS_DETAIL.M2_DETAIL_HANDLING_FEE as DECIMAL(20,3))";
		$StrSQL.="  + cast(FILESTATUS_DETAIL.M2_DETAIL_PRICE as DECIMAL(20,3))  ";
		$StrSQL.="  + cast(DAT_FILESTATUS.M2_SPECIAL_DISCOUNT as DECIMAL(20,3))) / 100) ";
		$StrSQL.="  + cast(M2_IMPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  + cast(MITSUMORISYO_EXPORT_FEE as DECIMAL(20,3)) ";
		$StrSQL.="  - cast(M2_MANAGE_DISCOUNT as DECIMAL(20,3)) ) ";
		$StrSQL.="  * cast(DAT_FILESTATUS.M2_TAX_RATE2 as DECIMAL(20,3)) / 100) ";
		$StrSQL.="   ,0)";
		$StrSQL.="   as signed) ";
		$StrSQL.="  <= CAST('".$price1."' as signed) ";
	}

	if($customer_name!=""){
		// No255
		// ※顧客名の検索対象は以下の項目でお願いいたします
		// M1のサプライヤー名　M1_DVAL01
		// M2の組織名　M2_DVAL03
		// M2のニックネーム　M2_DVAL01
		// M2の氏名（姓）　M2_DVAL16
		// M2の氏名（名）　M2_DVAL17
		// M2の氏名（FirstName） M2_DVAL18
		// M2の氏名（LastName）  M2_DVAL19
		$StrSQL.="  AND ( ";
		$StrSQL.="           M1_DVAL01 LIKE '%".$customer_name."%' ";
		$StrSQL.="        OR M2_DVAL03 LIKE '%".$customer_name."%' ";
		$StrSQL.="        OR M2_DVAL01 LIKE '%".$customer_name."%' ";
		$StrSQL.="        OR M2_DVAL16 LIKE '%".$customer_name."%' ";
		$StrSQL.="        OR M2_DVAL17 LIKE '%".$customer_name."%' ";
		$StrSQL.="        OR M2_DVAL18 LIKE '%".$customer_name."%' ";
		$StrSQL.="        OR M2_DVAL19 LIKE '%".$customer_name."%' ";
		$StrSQL.="  ) ";
	}

// ini_set( 'display_errors', 1 );

	$StrSQL.="  order by cast(SHODAN_ID as signed) asc,ID asc";
// echo $StrSQL;
// exit;

	$rs=mysqli_query(ConnDB(),$StrSQL);
	$record_cnt=mysqli_num_rows($rs);

	
	header("Content-Type: application/octet-stream");
	if($kbn=="1"){
		$output_filename="m_filestatus_".date('YmdHis').".csv";
	} else {
		$output_filename="h_filestatus_".date('YmdHis').".csv";
	}
	header("Content-Disposition: attachment; filename=".$output_filename."");

	$str="";


	// $exports=EXPORT_LIST; //抽出項目
	$labels=LABLE_LIST; //画面項目
	$vals=VALUE_LIST; //DB項目
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
	if($record_cnt>0) {
		//明細
		$before_no="";
		while ($item = mysqli_fetch_assoc($rs)) {

			//IDが異なるレコードに移った場合
			$first_id_recoed_flag="";
			if($before_no=="" || $before_no!=$item["ID"]){
				$first_id_recoed_flag="1";
				$before_no=$item["ID"];
			}
			// $first_id_recoed_flag="1";//test

			$str="";
			//LABLE_LISTでループ
			for ($j=0; $j<=count($export_array); $j++) {
				$label=$export_array[$j];

				
				if($label!=""){
					// $idx = array_search($label, $label_array);
					$idx =$j;
					$val=$val_array[$idx];
			
					if ($j!=0){
						$str=$str.",";
					}

					$data=$item[$val];
					switch($val){
						case "SHODAN_ID":
							$data=$item[$val];
							break;

						case "Control_No":
							 $SCNo=array(
								"SCNo_yy" => $item["SCNo_yy"], 
								"SCNo_mm" => $item["SCNo_mm"], 
								"SCNo_dd" => $item["SCNo_dd"], 
								"SCNo_cnt" => $item["SCNo_cnt"], 
								"SCNo_else1" => $item["SCNo_else1"], 
								"SCNo_else2" => $item["SCNo_else2"], 
							);

							//成形された「Scientist3 control No.」
							$data=formatAlphabetId($SCNo);
							if($first_id_recoed_flag==""){
								$data="";
							}
							break;
						case "M2_DETAIL_ITEM":
						case "M2_DETAIL_DESCRIPTION":
						case "M2_DETAIL_QUANTITY":
						case "M2_DETAIL_UNIT_PRICE":
						case "M2_DETAIL_PRICE":
						case "M2_DETAIL_NOTE":
							//DAT_FILESTATUS_DETAILの項目
							$data=$item[$val];
							break;
						case "不明":
							$data="";
							break;
						default:
							$data=$item[$val];
							if($first_id_recoed_flag==""){
								$data="";
							}
							break;
					}
					
	

					$str=$str."\"".str_replace("\r\n", "[rn]", str_replace("\r", "[r]", str_replace("\n", "[n]", str_replace("\t", "[t]", $data))))."\"";
				}
			}
			
			$csv_data = $str."\r\n";
			$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
			echo($csv_data);
		} 
	} else {
		//データなし
		echo "111aa";
	}

	return $function_ret;
} 



?>