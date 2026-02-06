<?php

//テーブル名の指定
  $TableName="DAT_SHODAN";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="MID2";
  $FieldName[2]="TITLE";
  $FieldName[3]="MID1_LIST";
  $FieldName[4]="CATEGORY";
  $FieldName[5]="KEYWORD";
  $FieldName[6]="NEWDATE";
  $FieldName[7]="EDITDATE";
  $FieldName[8]="STATUS_SORT";
  $FieldName[9]="STATUS";
  $FieldName[10]="C_STATUS";
  
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

//入力フィールドの書式　0-TEXT, 1-SELECT, 2-RADIO, 3-CHECKBOX, 4-FILE
  $FieldAtt[0]="0";
  $FieldAtt[1]="0";
  $FieldAtt[2]="0";
  $FieldAtt[3]="0";
  $FieldAtt[4]="0";
  $FieldAtt[5]="0";
  $FieldAtt[6]="0";
  $FieldAtt[7]="0";
  $FieldAtt[8]="0";
  $FieldAtt[9]="1";
  $FieldAtt[10]="0";

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
  $FieldParam[9]="問い合わせ::見積り依頼::再見積り依頼::見積り送付::発注依頼::決済者発注承認::受注承認::データ納品::物品納品::納品確認::請求::完了::辞退::キャンセル";
  $FieldParam[10]="";

//全フィールド数
	$FieldMax=10;

//キーフィールドの設定
	$FieldKey=0;

//リスト行数
	$PageSize=20;

//ASPファイル名
	$aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
	$filepath1="/a_shodan/data/";

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

	$str="WHERE DAT_SHODAN.ID>0 ";

	if ($word!=""){
	  $str=$str." and (( " . 
      "    DAT_SHODAN.MID2 like '%".$word."%' " . 
      " or DAT_SHODAN.TITLE like '%".$word."%' " . 
      " or DAT_SHODAN.MID1_LIST like '%".$word."%' " . 
      " or DAT_SHODAN.STATUS like '%".$word."%' " . 
      " or DAT_M2.M2_DVAL03 like '%".$word."%' " . 
      " or DAT_SHODAN.EDITDATE like '%".$word."%' " . 
      " or trim(concat('商談ID：', DAT_SHODAN.ID)) like '".$word."' " . 
      " or trim(concat('研究者：', DAT_M2.M2_DVAL03)) like '".$word."' " . 
      " or trim(concat('サプライヤー：', M1.M1_DVAL01)) like '".$word."' " . 
      " or trim(concat('案件名：', DAT_SHODAN.TITLE)) like '".$word."' " . 
      " or trim(concat('ステータス：', DAT_SHODAN.STATUS)) like '".$word."' " . 
      " or trim(concat('更新日時：', DAT_SHODAN.EDITDATE)) like '".$word."' " .
      " ) ";
		$str=$str." or CASE WHEN STATUS IN ('問い合わせ','見積り依頼','再見積もり依頼') THEN M1.M1_DVAL01 ELSE FILESTATUS.M1_DVAL01 END like '%".$word."%' " ;
		// $str=$str." or FILESTATUS.M1_DVAL01 like '%".$word."%' " ;
		$str=$str." ) ";
		
	} 

	switch($sort){
		// TODO
		/*
		case "1":
			//商談ID（昇順）
			$str=$str."ORDER BY DAT_SHODAN.ID asc";
			break;
		case "2":
			//商談ID（降順）
			$str=$str."ORDER BY DAT_SHODAN.ID desc";
			break;
		*/
		case "1":
			//商談ID（昇順）
			$str=$str."ORDER BY FILESTATUS.FILESTATUS_ID asc";
			break;
		case "2":
			//商談ID（降順）
			$str=$str."ORDER BY FILESTATUS.FILESTATUS_ID desc";
			break;
		case "101":
			//商談ID（昇順）
			$str=$str."ORDER BY DAT_SHODAN.ID asc";
			break;
		case "102":
			//商談ID（降順）
			$str=$str."ORDER BY DAT_SHODAN.ID desc";
			break;
		case "21":
			//問合せID（昇順）
			$str=$str."ORDER BY concat(DAT_M2.MID, DAT_SHODAN.NEWDATE) asc";
			break;
		case "22":
			//問合せID（降順）
			$str=$str."ORDER BY concat(DAT_M2.MID, DAT_SHODAN.NEWDATE) desc";
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
			$str=$str."ORDER BY CASE WHEN STATUS='問い合わせ' OR STATUS='見積り依頼' OR STATUS='再見積もり依頼' THEN M1.M1_DVAL01 ELSE FILESTATUS.M1_DVAL01 END asc";
			break;
		case "6":
			//サプライヤー（降順）
			$str=$str."ORDER BY CASE WHEN STATUS='問い合わせ' OR STATUS='見積り依頼' OR STATUS='再見積もり依頼' THEN M1.M1_DVAL01 ELSE FILESTATUS.M1_DVAL01 END desc";
			break;
		case "7":
			//案件名（昇順）
			$str=$str."ORDER BY DAT_SHODAN.TITLE asc";
			break;
		case "8":
			//案件名（降順）
			$str=$str."ORDER BY DAT_SHODAN.TITLE desc";
			break;
		case "9":
			//ステータス（昇順）
			$str=$str."ORDER BY DAT_SHODAN.STATUS asc";
			break;
		case "10":
			//ステータス（降順）
			$str=$str."ORDER BY DAT_SHODAN.STATUS desc";
			break;
		case "11":
			//更新日時（昇順）
			$str=$str."ORDER BY DAT_SHODAN.EDITDATE asc";
			break;
		case "12":
			//更新日時（降順）
			$str=$str."ORDER BY DAT_SHODAN.EDITDATE desc";
			break;
		default:
			$str=$str."ORDER BY DAT_SHODAN.ID desc";
			break;
	}

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
