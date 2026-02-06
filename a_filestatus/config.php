<?php

//テーブル名の指定
  $TableName="DAT_FILESTATUS";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="SHODAN_ID";
  $FieldName[2]="MID1";
  $FieldName[3]="MID2";
  $FieldName[4]="T_COMMENT";
  $FieldName[5]="T_FILE";
  $FieldName[6]="T_ANSWERDATE";
  $FieldName[7]="M1_MESSAGE";
  $FieldName[8]="M1_TRANS_FLG";
  $FieldName[9]="M1_TRANS_TXT";
  $FieldName[10]="M1_PRICE";
  $FieldName[11]="M1_KIGEN";
  $FieldName[12]="M1_FILE";
  $FieldName[13]="M2_ID";
  $FieldName[14]="M2_VERSION";
  $FieldName[15]="M2_NOHIN_TYPE";
  $FieldName[16]="M2_PAY_TYPE";
  $FieldName[17]="M2_STUDY_CODE";
  $FieldName[18]="M2_DATE";
  $FieldName[19]="M2_QUOTE_VALID_UNTIL";
  $FieldName[20]="M2_DESCRIPTION";
  $FieldName[21]="M2_CURRENCY";
  $FieldName[22]="M2_SPECIAL_DISCOUNT";
  $FieldName[23]="M2_SPECIAL_NOTE";
  $FieldName[24]="H_M2_ID";
  $FieldName[25]="H_COMMENT";
  $FieldName[26]="N_FILE";
  $FieldName[27]="N_MESSAGE";
  $FieldName[28]="S_FILE";
  $FieldName[29]="S_MESSAGE";
  $FieldName[30]="S2_FILE";
  $FieldName[31]="S2_MESSAGE";
  $FieldName[32]="NEWDATE";
  $FieldName[33]="EDITDATE";
  $FieldName[34]="STATUS";
  $FieldName[35]="M1_M2_ID";
  $FieldName[36]="T_FILE2";
  $FieldName[37]="T_FILE3";
  $FieldName[38]="T_FILE4";
  $FieldName[39]="T_FILE5";
  $FieldName[40]="N_FILE2";
  $FieldName[41]="N_FILE3";
  $FieldName[42]="N_FILE4";
  $FieldName[43]="N_FILE5";
  $FieldName[44]="N_PDF";
  $FieldName[45]="N_SHUKKA";
  $FieldName[46]="N_TEMP1";
  $FieldName[47]="N_TEMP2";
  $FieldName[48]="N_AWB";
  $FieldName[49]="TEMPR";
  $FieldName[50]="SAMPLE";
  $FieldName[51]="ORIGIN";
  $FieldName[52]="LEGAL";
  $FieldName[53]="UNIT";
  $FieldName[54]="DIV_ID";
  $FieldName[55]="M2_SHIP_TO_SPT_1";
  $FieldName[56]="M2_SHIP_TO_SPT_2";
  $FieldName[57]="M2_SHIP_TO_SPT_3";
  $FieldName[58]="M2_SHIP_TO_SPT_4";
  $FieldName[59]="M2_SHIP_TO_SPT_5";
  $FieldName[60]="M2_SHIP_TO_SPT_6";
  $FieldName[61]="M_STATUS";
  $FieldName[62]="M2_IMPORT_FEE";
  $FieldName[63]="M2_MANAGE_DISCOUNT";
  $FieldName[64]="M2_TAX_RATE2";
  $FieldName[65]="M2_BILL_TO_SPT_1";
  $FieldName[66]="M2_BILL_TO_SPT_2";
  $FieldName[67]="M2_BILL_TO_SPT_3";
  $FieldName[68]="M2_BILL_TO_SPT_4";
  $FieldName[69]="M2_BILL_TO_SPT_5";
  $FieldName[70]="M2_BILL_TO_SPT_6";
  $FieldName[71]="M2_QUOTE_NO"; //当初から存在しているのになかったので追加
  $FieldName[72]="SCNo_yy"; 
  $FieldName[73]="SCNo_mm"; 
  $FieldName[74]="SCNo_dd"; 
  $FieldName[75]="SCNo_cnt"; 
  $FieldName[76]="SCNo_else1"; 
  $FieldName[77]="SCNo_else2"; 
  $FieldName[78]="M2_EXPORT_FEE_TABLE"; 
  $FieldName[79]="M2_TAX_RATE1"; 
  $FieldName[80]="T_COMMENT";
  $FieldName[81]="M2_EXPORT_FEE";
  $FieldName[82]="PONO";
  $FieldName[83]="H3A_MESSAGE";//当初から存在しているのになかったので追加
  $FieldName[84]="H3B_MESSAGE";//当初から存在しているのになかったので追加
  $FieldName[85]="PF_RATE";
  $FieldName[86]="CHKNOHIN_MESSAGE";
  $FieldName[87]="CHKNOHIN_ID";
  $FieldName[88]="S_STATUS";
  $FieldName[89]="S_ADD_CHARGE1";
  $FieldName[90]="S_ADD_CHARGE2";


  $FieldValue[0]="";
  $FieldValue[1]="";
  $FieldValue[2]="";
  $FieldValue[3]="";
  $FieldValue[4]="";
  $FieldValue[5]="";
  $FieldValue[6]="";
  $FieldValue[7]="";
  $FieldValue[8]="";
  $FieldValue[9]="";
  $FieldValue[10]="";
  $FieldValue[11]="";
  $FieldValue[12]="";
  $FieldValue[13]="";
  $FieldValue[14]="";
  $FieldValue[15]="";
  $FieldValue[16]="";
  $FieldValue[17]="";
  $FieldValue[17]="";
  $FieldValue[18]="";
  $FieldValue[20]="";
  $FieldValue[21]="";
  $FieldValue[22]="";
  $FieldValue[23]="";
  $FieldValue[24]="";
  $FieldValue[25]="";
  $FieldValue[26]="";
  $FieldValue[27]="";
  $FieldValue[28]="";
  $FieldValue[29]="";
  $FieldValue[30]="";
  $FieldValue[31]="";
  $FieldValue[32]="";
  $FieldValue[33]="";
  $FieldValue[34]="";
  $FieldValue[35]="";
  $FieldValue[36]="";
  $FieldValue[37]="";
  $FieldValue[38]="";
  $FieldValue[39]="";
  $FieldValue[40]="";
  $FieldValue[41]="";
  $FieldValue[42]="";
  $FieldValue[43]="";
  $FieldValue[44]="";
  $FieldValue[45]="";
  $FieldValue[46]="";
  $FieldValue[47]="";
  $FieldValue[48]="";
  $FieldValue[49]="";
  $FieldValue[50]="";
  $FieldValue[51]="";
  $FieldValue[52]="";
  $FieldValue[53]="";
  $FieldValue[54]="";
  $FieldValue[55]="";
  $FieldValue[56]="";
  $FieldValue[57]="";
  $FieldValue[58]="";
  $FieldValue[59]="";
  $FieldValue[60]="";
  $FieldValue[61]="";
  $FieldValue[62]="";
  $FieldValue[63]="";
  $FieldValue[64]="";
  $FieldValue[65]="";
  $FieldValue[66]="";
  $FieldValue[67]="";
  $FieldValue[68]="";
  $FieldValue[69]="";
  $FieldValue[70]="";
  $FieldValue[71]="";
  $FieldValue[72]="";
  $FieldValue[73]="";
  $FieldValue[74]="";
  $FieldValue[75]="";
  $FieldValue[76]="";
  $FieldValue[77]="";
  $FieldValue[78]="";
  $FieldValue[79]="";
  $FieldValue[80]="";
  $FieldValue[81]="";
  $FieldValue[82]="";
  $FieldValue[83]="";
  $FieldValue[84]="";
  $FieldValue[85]="";
  $FieldValue[86]="";
  $FieldValue[87]="";
  $FieldValue[88]="";
  $FieldValue[89]="";
  $FieldValue[90]="";

//入力フィールドの書式　0-TEXT, 1-SELECT, 2-RADIO, 3-CHECKBOX, 4-FILE
  $FieldAtt[0]="0";
  $FieldAtt[1]="0";
  $FieldAtt[2]="0";
  $FieldAtt[3]="0";
  $FieldAtt[4]="0";
  $FieldAtt[5]="4";
  $FieldAtt[6]="0";
  $FieldAtt[7]="0";
  $FieldAtt[8]="0";
  $FieldAtt[9]="0";
  $FieldAtt[10]="0";
  $FieldAtt[11]="0";
  $FieldAtt[12]="4";
  $FieldAtt[13]="0";
  $FieldAtt[14]="0";
  $FieldAtt[15]="3";
  $FieldAtt[16]="2";
  $FieldAtt[17]="0";
  $FieldAtt[17]="0";
  $FieldAtt[18]="0";
  $FieldAtt[19]="0";
  $FieldAtt[20]="0";
  $FieldAtt[21]="1";
  $FieldAtt[22]="0";
  $FieldAtt[23]="0";
  $FieldAtt[24]="0";
  $FieldAtt[25]="0";
  $FieldAtt[26]="4";
  $FieldAtt[27]="0";
  $FieldAtt[28]="4";
  $FieldAtt[29]="0";
  $FieldAtt[30]="4";
  $FieldAtt[31]="0";
  $FieldAtt[32]="0";
  $FieldAtt[33]="0";
  $FieldAtt[34]="0";
  $FieldAtt[35]="0";
  $FieldAtt[36]="4";
  $FieldAtt[37]="4";
  $FieldAtt[38]="4";
  $FieldAtt[39]="4";
  $FieldAtt[40]="4";
  $FieldAtt[41]="4";
  $FieldAtt[42]="4";
  $FieldAtt[43]="4";
  $FieldAtt[44]="4";
  $FieldAtt[45]="0";
  $FieldAtt[46]="0";
  $FieldAtt[47]="0";
  $FieldAtt[48]="0";
  $FieldAtt[49]="1";
  $FieldAtt[50]="0";
  $FieldAtt[51]="0";
  $FieldAtt[52]="0";
  $FieldAtt[53]="1";
  $FieldAtt[54]="0";
  $FieldAtt[55]="0";
  $FieldAtt[56]="0";
  $FieldAtt[57]="0";
  $FieldAtt[58]="0";
  $FieldAtt[59]="0";
  $FieldAtt[60]="0";
  $FieldAtt[61]="0";
  $FieldAtt[62]="0";
  $FieldAtt[63]="0";
  $FieldAtt[64]="0";
  $FieldAtt[65]="0";
  $FieldAtt[66]="0";
  $FieldAtt[67]="0";
  $FieldAtt[68]="0";
  $FieldAtt[69]="0";
  $FieldAtt[70]="0";
  $FieldAtt[71]="0";
  $FieldAtt[72]="0";
  $FieldAtt[73]="0";
  $FieldAtt[74]="0";
  $FieldAtt[75]="0";
  $FieldAtt[76]="0";
  $FieldAtt[77]="0";
  $FieldAtt[78]="0";
  $FieldAtt[79]="0";
  $FieldAtt[80]="0";
  $FieldAtt[81]="0";
  $FieldAtt[82]="0";
  $FieldAtt[83]="0";
  $FieldAtt[84]="0";
  $FieldAtt[85]="0";
  $FieldAtt[86]="0";
  $FieldAtt[87]="0";
  $FieldAtt[88]="0";
  $FieldAtt[89]="0";
  $FieldAtt[90]="0";

//SELECT, RADIO, CHECKBOX時の値群
  $FieldParam[0]="";
  $FieldParam[1]="";
  $FieldParam[2]="";
  $FieldParam[3]="";
  $FieldParam[4]="";
  $FieldParam[5]="";
  $FieldParam[6]="";
  $FieldParam[7]="";
  $FieldParam[8]="";
  $FieldParam[9]="";
  $FieldParam[10]="";
  $FieldParam[11]="";
  $FieldParam[12]="";
  $FieldParam[13]="";
  $FieldParam[14]="";
  $FieldParam[15]="data::Goods";
  $FieldParam[16]="Once::Split";
  $FieldParam[17]="";
  $FieldParam[18]="";
  $FieldParam[19]="";
  $FieldParam[20]="";
  $FieldParam[21]="JPY::USD::EUR::GBP";
  $FieldParam[22]="";
  $FieldParam[23]="";
  $FieldParam[24]="";
  $FieldParam[25]="";
  $FieldParam[26]="";
  $FieldParam[27]="";
  $FieldParam[28]="";
  $FieldParam[29]="";
  $FieldParam[30]="";
  $FieldParam[31]="";
  $FieldParam[32]="";
  $FieldParam[33]="";
  $FieldParam[34]="";
  $FieldParam[35]="";
  $FieldParam[36]="";
  $FieldParam[37]="";
  $FieldParam[38]="";
  $FieldParam[39]="";
  $FieldParam[40]="";
  $FieldParam[41]="";
  $FieldParam[42]="";
  $FieldParam[43]="";
  $FieldParam[44]="";
  $FieldParam[45]="";
  $FieldParam[46]="";
  $FieldParam[47]="";
  $FieldParam[48]="";
  $FieldParam[49]="常温::冷蔵::冷凍";
  $FieldParam[50]="";
  $FieldParam[51]="";
  $FieldParam[52]="";
  $FieldParam[53]="￥::$::€::￡";
  $FieldParam[54]="";
  $FieldParam[55]="";
  $FieldParam[56]="";
  $FieldParam[57]="";
  $FieldParam[58]="";
  $FieldParam[59]="";
  $FieldParam[60]="";
  $FieldParam[61]="";
  $FieldParam[62]="";
  $FieldParam[63]="";
  $FieldParam[64]="";
  $FieldParam[65]="";
  $FieldParam[66]="";
  $FieldParam[67]="";
  $FieldParam[68]="";
  $FieldParam[69]="";
  $FieldParam[70]="";
  $FieldParam[71]="";
  $FieldParam[72]="";
  $FieldParam[73]="";
  $FieldParam[74]="";
  $FieldParam[75]="";
  $FieldParam[76]="";
  $FieldParam[77]="";
  $FieldParam[78]="";
  $FieldParam[79]="";
  $FieldParam[80]="";
  $FieldParam[81]="";
  $FieldParam[82]="";
  $FieldParam[83]="";
  $FieldParam[84]="";
  $FieldParam[85]="";
  $FieldParam[86]="";
  $FieldParam[87]="";
  $FieldParam[88]="";
  $FieldParam[89]="";
  $FieldParam[90]="";


//全フィールド数
	$FieldMax=90;

//キーフィールドの設定
	$FieldKey=0;

//リスト行数
	$PageSize=20;

//ASPファイル名
	$aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
	$filepath1="/a_filestatus/data/";

//=========================================================================================================
//名前 新規作成時の初期値設定
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function InitData()
{
	//extract($GLOBALS);
	eval(globals());

//各フィールドの初期値
  $FieldValue[0]="";
  $FieldValue[1]="";
  $FieldValue[2]="";
  $FieldValue[3]="";
  $FieldValue[4]="";
  $FieldValue[5]="";
  $FieldValue[6]="";
  $FieldValue[7]="";
  $FieldValue[8]="";
  $FieldValue[9]="";
  $FieldValue[10]="";
  $FieldValue[11]="";
  $FieldValue[12]="";
  $FieldValue[13]="";
  $FieldValue[14]="";
  $FieldValue[15]="";
  $FieldValue[16]="";
  $FieldValue[17]="";
  $FieldValue[18]="";
  $FieldValue[19]="";
  $FieldValue[20]="";
  $FieldValue[21]="";
  $FieldValue[22]="";
  $FieldValue[23]="";
  $FieldValue[24]="";
  $FieldValue[25]="";
  $FieldValue[26]="";
  $FieldValue[27]="";
  $FieldValue[28]="";
  $FieldValue[29]="";
  $FieldValue[30]="";
  $FieldValue[31]="";
  $FieldValue[32]="";
  $FieldValue[33]="";
  $FieldValue[34]="";
  $FieldValue[35]="";
  $FieldValue[36]="";
  $FieldValue[37]="";
  $FieldValue[38]="";
  $FieldValue[39]="";
  $FieldValue[40]="";
  $FieldValue[41]="";
  $FieldValue[42]="";
  $FieldValue[43]="";
  $FieldValue[44]="";
  $FieldValue[45]="";
  $FieldValue[46]="";
  $FieldValue[47]="";
  $FieldValue[48]="";
  $FieldValue[49]="";
  $FieldValue[50]="";
  $FieldValue[51]="";
  $FieldValue[52]="";
  $FieldValue[53]="";

	return $function_ret;
} 

//=========================================================================================================
//名前 入力後のエラーチェック（エラーがない場合は空を指定）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ErrorCheck()
{
	//extract($GLOBALS);
	eval(globals());

	$function_ret="";



	return $function_ret;
} 

//=========================================================================================================
//名前 SQL条件（WHERE ･･･ ORDER BY･･･）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ListSql($sort,$word)
{
	//extract($GLOBALS);
	eval(globals());

	$str=" WHERE DAT_FILESTATUS.ID>0 ";

	//if ($word!=""){
	//	$str=$str." and ( 1 = 2 " . 
  //    " or DAT_FILESTATUS.MID2 like '%".$word."%' " . 
  //    " or DAT_FILESTATUS.STATUS like '%".$word."%' " . 
  //    " or DAT_FILESTATUS.MID1 like '%".$word."%' " . 
  //    " or DAT_M1.M1_DVAL01 like '%".$word."%' " . 
  //    " or concat('商談ID：', DAT_FILESTATUS.SHODAN_ID) like '%".$word."%' " . 
  //    " ) ";
	//}
  if ($word!="" && strpos($word, '商談ID：') === false){
    $word=trim($word);
    $str=$str." and ( 1 = 2 " . 
    " or DAT_FILESTATUS.MID2 like '%".$word."%' " . 
    " or DAT_FILESTATUS.STATUS like '%".$word."%' " . 
    " or DAT_FILESTATUS.MID1 like '%".$word."%' " . 
    " or DAT_M1.M1_DVAL01 like '%".$word."%' " . 

    // 商談ID仕様変更に対応
    //" or trim(concat('商談ID：', DAT_FILESTATUS.SHODAN_ID)) like '".$word."' " .
    //" or trim(concat('商談ID：', DAT_FILESTATUS.ID)) like '".$word."' " .

    " or trim(concat('サプライヤー：', DAT_M1.M1_DVAL01)) like '".$word."' " . 
    " or trim(concat('ステータス：', DAT_FILESTATUS.STATUS)) like '".$word."' " . 
    " or trim(concat('更新日時：', DAT_FILESTATUS.EDITDATE)) like '".$word."' " . 
    //" or concat('商談ID：', DAT_FILESTATUS.SHODAN_ID) like '".$word."' " .
    //" or concat('サプライヤー：', DAT_M1.M1_DVAL01) like '".$word."' " . 
    //" or concat('ステータス：', DAT_FILESTATUS.STATUS) like '".$word."' " . 
    //" or concat('更新日時：', DAT_FILESTATUS.EDITDATE) like '".$word."' " . 
    " ) ";
  } 
  switch($sort){
		case "1":
			//商談ID（昇順）
			$str=$str."ORDER BY DAT_FILESTATUS.ID asc";
			break;
		case "2":
			//商談ID（降順）
			$str=$str."ORDER BY DAT_FILESTATUS.ID desc";
			break;
		case "3":
			//研究者（昇順）
			$str=$str."ORDER BY DAT_M2.M2_DVAL01 asc";
			break;
		case "4":
			//研究者（降順）
			$str=$str."ORDER BY DAT_M2.M2_DVAL01 desc";
			break;
		case "5":
			//サプライヤー（昇順）
			$str=$str."ORDER BY DAT_M1.M1_DVAL01 asc";
			break;
		case "6":
			//サプライヤー（降順）
			$str=$str."ORDER BY DAT_M1.M1_DVAL01 desc";
			break;
		// case "7":
		// 	//案件名（昇順）
		// 	$str=$str."ORDER BY DAT_FILESTATUS.TITLE asc";
		// 	break;
		// case "8":
		// 	//案件名（降順）
		// 	$str=$str."ORDER BY DAT_FILESTATUS.TITLE desc";
		// 	break;
		case "9":
			//ステータス（昇順）
			$str=$str."ORDER BY DAT_FILESTATUS.STATUS asc";
			break;
		case "10":
			//ステータス（降順）
			$str=$str."ORDER BY DAT_FILESTATUS.STATUS desc";
			break;
		case "11":
			//更新日時（昇順）
			$str=$str."ORDER BY DAT_FILESTATUS.EDITDATE asc";
			break;
		case "12":
			//更新日時（降順）
			$str=$str."ORDER BY DAT_FILESTATUS.EDITDATE desc";
			break;
		default:
			$str=$str."ORDER BY DAT_FILESTATUS.ID desc";
			break;
	}

	// $str=$str."ORDER BY DAT_FILESTATUS.ID desc";

	$function_ret=$str;

	return $function_ret;
} 

//=========================================================================================================
//名前 SQL条件（WHERE ･･･ ORDER BY･･･）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ListSQLSearch($sort,$word,$sel1,$sel2,$word2)
{
	//extract($GLOBALS);
	eval(globals());

	$str="DAT_O1.ID>0";

	if ($word!=""){
		if(strstr($word, "MID:")==true){
			$str.=" AND (DAT_M1.MID='".str_replace("MID:", "", $word)."')";
		} else {
			//$tmp1=explode("::","DAT_M1.M1_DVAL01::DAT_O1.O1_DVAL01::DAT_M1.M1_DTXT01::DAT_O1.O1_DTXT01");
			$tmp2=explode("\t",str_replace(" ", "\t", str_replace("　", " ", $word))."\t");
			//$tmp3="";
			for ($j=0; $j<count($tmp2); $j++) {

        // カテゴリー
        if(strpos($tmp2[$j], 'O1_MSEL01:') !== false) {
				  $str.=" AND DAT_O1.O1_MSEL01 = '".$tmp2[$j]."' ";
        }
        // 国、GLP、〇〇については仕様不明
        else if(strpos($tmp2[$j], 'O1_???:') !== false) {
				  $str.=" AND DAT_O1.O1_??? = '".$tmp2[$j]."' ";
        }

        /*
				if($tmp2[$j]!=""){
					$tmp4="";
					for ($i=0; $i<count($tmp1); $i++) {
						if($tmp4!=""){
							$tmp4.=" or ";
						}
						$tmp4.=$tmp1[$i]." like \"%".$tmp2[$j]."%\"";
					}
					if($tmp3!=""){
						$tmp3.=" or ";
					}
					$tmp3.="(".$tmp4.")";
				}
        */
			}
      /*
			if($tmp3!=""){
				$str.=" AND (".$tmp3.")";
			} 
      */
		} 
	} 

  if($word2!=""){
    $str=$str." AND (";
    
    $str=$str."    DAT_O1.O1_DVAL01 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DVAL02 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT01 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT02 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT03 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT04 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT05 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT06 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT07 like '%".$word2."%'";
    $str=$str." OR DAT_O1.O1_DTXT08 like '%".$word2."%'";
    $str=$str." OR DAT_M1.EMAIL like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC02 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL01 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL02 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL03 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL04 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL05 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL06 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL07 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL08 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL09 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL10 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DVAL11 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DTXT01 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DTXT02 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_DSEL01 like '%".$word2."%'";
    
    $str=$str." ) ";
  }


	if ($sel1!=""){
		$str=$str." AND DAT_O1.O1_MSEL01 like '%".$sel1."%'";
	} 

	if ($sel2!=""){
		$str=$str." AND DAT_O1.O1_MSEL02 like '%".$sel2."%'";
	} 

	if ($sort=="3"){
		$str=$str." AND cast(DAT_MATCH.POINT as SIGNED)>=50";
	} 

	$str=$str." group by DAT_O1.ID ";

	switch ($sort){
	case "1":
		$str=$str." ORDER BY cast(DAT_MATCH.POINT as SIGNED) desc";
		break;
	case "2":
		$str=$str." ORDER BY DAT_O1.NEWDATE desc";
		break;
	case "3":
		$str=$str." ORDER BY cast(DAT_MATCH.POINT as SIGNED) desc";
		break;
	default:
		$str=$str." ORDER BY cast(DAT_MATCH.POINT as SIGNED) desc";
		break;
	}

	$function_ret=$str;

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckKana($str)
{
	//extract($GLOBALS);
	eval(globals());

	$strKana="アイウエオカキクケコサシスセソ\タチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンァィゥェォッャュョーガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポ";

	for ($i=1; $i<=strlen($str); $i=$i+1)
	{
		if ((strpos($strKana,substr($str,$i-1,1)) ? strpos($strKana,substr($str,$i-1,1))+1 : 0)<=0)
		{

			$function_ret=false;
			return $function_ret;

		} 


	}


	$function_ret=true;

	return $function_ret;
} 
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckURL($str)
{
	//extract($GLOBALS);
	eval(globals());

	$strUrl="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";

	for ($i=1; $i<=strlen($str); $i=$i+1)
	{
		if ((strpos($strUrl,substr($str,$i-1,1)) ? strpos($strUrl,substr($str,$i-1,1))+1 : 0)<=0)
		{

			$function_ret=false;
			return $function_ret;

		} 


	}


	$function_ret=true;

	return $function_ret;
} 
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckEmail($str)
{
	//extract($GLOBALS);
	eval(globals());

	if ((strpos($str,"@") ? strpos($str,"@")+1 : 0)<=0)
	{

		$function_ret=false;
		return $function_ret;

	} 

	if ((strpos($str,".") ? strpos($str,".")+1 : 0)<=0)
	{

		$function_ret=false;
		return $function_ret;

	} 


	$function_ret=true;

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CheckNumber($str)
{
	//extract($GLOBALS);
	eval(globals());

	if (!is_numeric($str))
	{

		$function_ret=false;
		return $function_ret;

	} 


	$function_ret=true;

	return $function_ret;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ConvertToHalfNum($hnum)
{
	//extract($GLOBALS);
	eval(globals());

	$function_ret="";

	if (strlen($hnum)==0)
	{

		return $function_ret;

	} 


	$returnString=$hnum;
	$returnString=str_replace("０","0",$returnString);
	$returnString=str_replace("１","1",$returnString);
	$returnString=str_replace("２","2",$returnString);
	$returnString=str_replace("３","3",$returnString);
	$returnString=str_replace("４","4",$returnString);
	$returnString=str_replace("５","5",$returnString);
	$returnString=str_replace("６","6",$returnString);
	$returnString=str_replace("７","7",$returnString);
	$returnString=str_replace("８","8",$returnString);
	$returnString=str_replace("９","9",$returnString);
	$function_ret=$returnString;

	return $function_ret;
} 

?>
