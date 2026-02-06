<?php
	session_start();

$VAL_M1="";
$VAL_M2="";
$VAL_O1="";
$VAL_O2="";

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispPoint1($oid1, $str)
{

	eval(globals());

	$StrSQL="SELECT POINT FROM DAT_MATCH where OID1='".$oid1."' and MID2='".$_SESSION['MID']."';";
	$rsp=mysqli_query(ConnDB(),$StrSQL);
	$itemp = mysqli_fetch_assoc($rsp);
	if($itemp!=""){
		$str=DispParam($str, "POINT");
		$str=str_replace("[POINT]",$itemp['POINT'],$str);
	} else {
		$str=DispParamNone($str, "POINT");
	} 

	return $str;
}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispPoint2($oid2, $str)
{

	eval(globals());

	$StrSQL="SELECT POINT FROM DAT_MATCH where OID2='".$oid2."' and MID1='".$_SESSION['MID']."';";
	$rsp=mysqli_query(ConnDB(),$StrSQL);
	$itemp = mysqli_fetch_assoc($rsp);
	if($itemp!=""){
		$str=DispParam($str, "POINT");
		$str=str_replace("[POINT]",$itemp['POINT'],$str);
	} else {
		$str=DispParamNone($str, "POINT");
	} 

	return $str;
}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CulcSingleM($param, $val1, $val2)
{

	eval(globals());

	if($val1==""){
		return "0/0";
	} else {
		if($val1=="こだわりなし"){

			if($VAL_M2!=""){
				$VAL_M2.="::";
			}
			$VAL_M2.=$param;

			if($VAL_O2!=""){
				$VAL_O2.="::";
			}
			$VAL_O2.=$param;

			return "1/1";
		} else {
			if($val1==$val2){

				if($VAL_M1!=""){
					$VAL_M1.="::";
				}
				$VAL_M1.=$val1;

				if($VAL_O1!=""){
					$VAL_O1.="::";
				}
				$VAL_O1.=$val2;

				return "1/1";
			} else {
				return "0/1";
			}
		}
	}
}

function CulcMultiM($param, $val1, $val2)
{

	eval(globals());

	$val1=trim($val1);
	$val2=trim($val2);
	$v1=explode("\t", $val1);
	$v2=explode("\t", $val2);

	if($val1==""){
		return "0/0";
	} else {
		if(strstr($val1, "こだわりなし")==true){
			if($val2!=""){

				if($VAL_M2!=""){
					$VAL_M2.="::";
				}
				$VAL_M2.=$param;

				if($VAL_O2!=""){
					$VAL_O2.="::";
				}
				$VAL_O2.=$param;

				return count($v2)."/".count($v2);
			} else {
				return "0/0";
			}
		} else {
			$v=0;
			// 2021.04.22 yamamoto 外科と〇〇外科がマッチしてしまう問題の対応
			/*
			for($i=0; $i<count($v2); $i++){
				if(strstr($val1, $v2[$i])==true){

					if($VAL_M1!=""){
						$VAL_M1.="::";
						$VAL_M2.="::";
					}
					$VAL_M1.=$v2[$i];
					$VAL_M2.=$v2[$i];

					if($VAL_O1!=""){
						$VAL_O1.="::";
						$VAL_O2.="::";
					}
					$VAL_O1.=$v2[$i];
					$VAL_O2.=$v2[$i];

					$v++;
				}
			}
			*/
			for($i = 0; $i < count($v1); $i++) {
				for($j = 0; $j < count($v2); $j++) {
					if($v1[$i] == $v2[$j]) {
						$VAL_M1 .= ($VAL_M1 != '' ? '::' : '') . $v1[$i];
						$VAL_M2 .= ($VAL_M2 != '' ? '::' : '') . $v1[$i];
						$VAL_O1 .= ($VAL_O1 != '' ? '::' : '') . $v1[$i];
						$VAL_O2 .= ($VAL_O2 != '' ? '::' : '') . $v1[$i];
						$v++;
					}
				}
			}
			return $v."/".count($v1);
		}
	}
}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function CulcMatching($oid1, $oid2, $mode)
{
	eval(globals());

//MSEL01::MSEL02::MSEL03::MSEL04::MSEL05::MSEL06::MSEL07::MSEL08::MSEL09::MSEL10::MRDO01::MRDO02::MRDO03::MRDO04::MRDO05::MRDO06::MRDO07::MRDO08::MRDO09::MRDO10::MCHK01::MCHK02::MCHK03::MCHK04::MCHK05::MCHK06::MCHK07::MCHK08::MCHK09::MCHK10
	$cnames1="カテゴリー::O1_MSEL02-TITLE::O1_MSEL03-TITLE::O1_MSEL04-TITLE::O1_MSEL05-TITLE::O1_MSEL06-TITLE::O1_MSEL07-TITLE::O1_MSEL08-TITLE::O1_MSEL09-TITLE::O1_MSEL10-TITLE";
	$cnames2="O1_MRDO01-TITLE::O1_MRDO02-TITLE::O1_MRDO03-TITLE::O1_MRDO04-TITLE::O1_MRDO05-TITLE::O1_MRDO06-TITLE::O1_MRDO07-TITLE::O1_MRDO08-TITLE::O1_MRDO09-TITLE::O1_MRDO10-TITLE";
	$cnames3="O1_MCHK01-TITLE::O1_MCHK02-TITLE::O1_MCHK03-TITLE::O1_MCHK04-TITLE::O1_MCHK05-TITLE::O1_MCHK06-TITLE::O1_MCHK07-TITLE::O1_MCHK08-TITLE::O1_MCHK09-TITLE::O1_MCHK10-TITLE";
	$cn1=explode("::", $cnames1);
	$cn2=explode("::", $cnames2);
	$cn3=explode("::", $cnames3);

	$StrSQL="SELECT * FROM DAT_O1 where OID='".$oid1."';";
	$rs1=mysqli_query(ConnDB(),$StrSQL);
	$item1 = mysqli_fetch_assoc($rs1);
	$mid1=$item1['MID'];

	$StrSQL="SELECT * FROM DAT_O2 where OID='".$oid2."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2 = mysqli_fetch_assoc($rs2);
	$mid2=$item2['MID'];

	$tmp="";
	$v0=0;
	$v1=0;

	$VAL_O1="";
	$VAL_O2="";
	$VAL_M1="";
	$VAL_M2="";

	for($i=1; $i<=10; $i++){
		$p="MSEL".sprintf("%02d", $i);
		$cn="O1_MSEL".sprintf("%02d", $i)."-TITLE";
		$c1="O1_MSEL".sprintf("%02d", $i);
		$c2="O2_MSEL".sprintf("%02d", $i);
//		if($cn!=$cn1[$i-1]){
			$vals=CulcSingleM($p, str_replace($c1.":", "", $item1[$c1]), str_replace($c2.":", "", $item2[$c2]));
			$val=explode("/", $vals);
			$v0=$v0+$val[0];
			$v1=$v1+$val[1];
			$tmp.="<tr><td>".$cn1[$i-1]."</td><td>".str_replace($c1.":", "", $item1[$c1])."</td><td>".str_replace($c2.":", "", $item2[$c2])."</td><td>".$val[0]."</td><td>".$val[1]."</td></tr>";
//		}
	}

	for($i=1; $i<=10; $i++){
		$p="MRDO".sprintf("%02d", $i);
		$cn="O1_MRDO".sprintf("%02d", $i)."-TITLE";
		$c1="O1_MRDO".sprintf("%02d", $i);
		$c2="O2_MRDO".sprintf("%02d", $i);
//		if($cn!=$cn2[$i-1]){
			$vals=CulcSingleM($p, str_replace($c1.":", "", $item1[$c1]), str_replace($c2.":", "", $item2[$c2]));
			$val=explode("/", $vals);
			$v0=$v0+$val[0];
			$v1=$v1+$val[1];
			$tmp.="<tr><td>".$cn2[$i-1]."</td><td>".str_replace($c1.":", "", $item1[$c1])."</td><td>".str_replace($c2.":", "", $item2[$c2])."</td><td>".$val[0]."</td><td>".$val[1]."</td></tr>";
//		}
	}

	for($i=1; $i<=10; $i++){
		$p="MRDO".sprintf("%02d", $i);
		$cn="O1_MCHK".sprintf("%02d", $i)."-TITLE";
		$c1="O1_MCHK".sprintf("%02d", $i);
		$c2="O2_MCHK".sprintf("%02d", $i);
//		if($cn!=$cn3[$i-1]){
			$vals=CulcMultiM($p, str_replace($c1.":", "", $item1[$c1]), str_replace($c2.":", "", $item2[$c2]));
			$val=explode("/", $vals);
			$v0=$v0+$val[0];
			$v1=$v1+$val[1];
			$tmp.="<tr><td>".$cn3[$i-1]."</td><td>".str_replace($c1.":", "", $item1[$c1])."</td><td>".str_replace($c2.":", "", $item2[$c2])."</td><td>".$val[0]."</td><td>".$val[1]."</td></tr>";
//		}
	}

	if($mode==1){
		print "<html><head><meta charset=\"UTF-8\" /></head><body>";
		print "<style>th,td {border:solid 1px;}table {border-collapse:  collapse;}</style>";
		print "<table>";
		print "<tr><td>ID</td><td>OID1（".$oid1."）</td><td>OID2（".$oid2."）</td><td>分子</td><td>分母</td></tr>";
		print $tmp;
		print "<tr><td colspan=\"3\">合計</td><td>".$v0."</td><td>".$v1."</td></tr>";
		print "<tr><td colspan=\"3\">パーセント</td><td colspan=\"2\">".intval($v0/$v1*100)."%</td></tr>";
		print "</table>";
		print "VAL_O1:".$VAL_O1."<br />";
		print "VAL_O2:".$VAL_O2."<br />";
		print "VAL_M1:".$VAL_M1."<br />";
		print "VAL_M2:".$VAL_M2."<br />";
		print "</body></html>";
	}

	if($mode==0){
		$StrSQL="INSERT INTO DAT_MATCH (NEWDATE, MID1, MID2, OID1, OID2, POINT, VAL_M1, VAL_M2, VAL_O1, VAL_O2) values ('".date("Y/m/d H:i:s")."', '".$mid1."', '".$mid2."', '".$oid1."', '".$oid2."', '".intval($v0/$v1*100)."', '".$VAL_M1."', '".$VAL_M2."', '".$VAL_O1."', '".$VAL_O2."')";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispM1($item, $str)
{
	eval(globals());

  $Param="M1";

  $M1FieldName[0]="ID";
  $M1FieldName[1]="MID";
  $M1FieldName[2]="EMAIL";
  $M1FieldName[3]="PASS";
  $M1FieldName[4]="SOCIALID";
  $M1FieldName[5]="M1_DVAL01";
  $M1FieldName[6]="M1_DVAL02";
  $M1FieldName[7]="M1_DVAL03";
  $M1FieldName[8]="M1_DVAL04";
  $M1FieldName[9]="M1_DVAL05";
  $M1FieldName[10]="M1_DVAL06";
  $M1FieldName[11]="M1_DVAL07";
  $M1FieldName[12]="M1_DVAL08";
  $M1FieldName[13]="M1_DVAL09";
  $M1FieldName[14]="M1_DVAL10";
  $M1FieldName[15]="M1_DTXT01";
  $M1FieldName[16]="M1_DTXT02";
  $M1FieldName[17]="M1_DTXT03";
  $M1FieldName[18]="M1_DTXT04";
  $M1FieldName[19]="M1_DTXT05";
  $M1FieldName[20]="M1_DTXT06";
  $M1FieldName[21]="M1_DTXT07";
  $M1FieldName[22]="M1_DTXT08";
  $M1FieldName[23]="M1_DTXT09";
  $M1FieldName[24]="M1_DTXT10";
  $M1FieldName[25]="M1_DSEL01";
  $M1FieldName[26]="M1_DSEL02";
  $M1FieldName[27]="M1_DSEL03";
  $M1FieldName[28]="M1_DSEL04";
  $M1FieldName[29]="M1_DSEL05";
  $M1FieldName[30]="M1_DSEL06";
  $M1FieldName[31]="M1_DSEL07";
  $M1FieldName[32]="M1_DSEL08";
  $M1FieldName[33]="M1_DSEL09";
  $M1FieldName[34]="M1_DSEL10";
  $M1FieldName[35]="M1_DRDO01";
  $M1FieldName[36]="M1_DRDO02";
  $M1FieldName[37]="M1_DRDO03";
  $M1FieldName[38]="M1_DRDO04";
  $M1FieldName[39]="M1_DRDO05";
  $M1FieldName[40]="M1_DRDO06";
  $M1FieldName[41]="M1_DRDO07";
  $M1FieldName[42]="M1_DRDO08";
  $M1FieldName[43]="M1_DRDO09";
  $M1FieldName[44]="M1_DRDO10";
  $M1FieldName[45]="M1_DCHK01";
  $M1FieldName[46]="M1_DCHK02";
  $M1FieldName[47]="M1_DCHK03";
  $M1FieldName[48]="M1_DCHK04";
  $M1FieldName[49]="M1_DCHK05";
  $M1FieldName[50]="M1_DCHK06";
  $M1FieldName[51]="M1_DCHK07";
  $M1FieldName[52]="M1_DCHK08";
  $M1FieldName[53]="M1_DCHK09";
  $M1FieldName[54]="M1_DCHK10";
  $M1FieldName[55]="M1_DFIL01";
  $M1FieldName[56]="M1_DFIL02";
  $M1FieldName[57]="M1_DFIL03";
  $M1FieldName[58]="M1_DFIL04";
  $M1FieldName[59]="M1_DFIL05";
  $M1FieldName[60]="M1_DFIL06";
  $M1FieldName[61]="M1_DFIL07";
  $M1FieldName[62]="M1_DFIL08";
  $M1FieldName[63]="M1_DFIL09";
  $M1FieldName[64]="M1_DFIL10";
  $M1FieldName[65]="M1_MSEL01";
  $M1FieldName[66]="M1_MSEL02";
  $M1FieldName[67]="M1_MSEL03";
  $M1FieldName[68]="M1_MSEL04";
  $M1FieldName[69]="M1_MSEL05";
  $M1FieldName[70]="M1_MSEL06";
  $M1FieldName[71]="M1_MSEL07";
  $M1FieldName[72]="M1_MSEL08";
  $M1FieldName[73]="M1_MSEL09";
  $M1FieldName[74]="M1_MSEL10";
  $M1FieldName[75]="M1_MRDO01";
  $M1FieldName[76]="M1_MRDO02";
  $M1FieldName[77]="M1_MRDO03";
  $M1FieldName[78]="M1_MRDO04";
  $M1FieldName[79]="M1_MRDO05";
  $M1FieldName[80]="M1_MRDO06";
  $M1FieldName[81]="M1_MRDO07";
  $M1FieldName[82]="M1_MRDO08";
  $M1FieldName[83]="M1_MRDO09";
  $M1FieldName[84]="M1_MRDO10";
  $M1FieldName[85]="M1_MCHK01";
  $M1FieldName[86]="M1_MCHK02";
  $M1FieldName[87]="M1_MCHK03";
  $M1FieldName[88]="M1_MCHK04";
  $M1FieldName[89]="M1_MCHK05";
  $M1FieldName[90]="M1_MCHK06";
  $M1FieldName[91]="M1_MCHK07";
  $M1FieldName[92]="M1_MCHK08";
  $M1FieldName[93]="M1_MCHK09";
  $M1FieldName[94]="M1_MCHK10";
  $M1FieldName[95]="ENABLE";
  $M1FieldName[96]="NEWDATE";
  $M1FieldName[97]="EDITDATE";
  $M1FieldName[98]="M1_ETC01";
  $M1FieldName[99]="M1_ETC02";
  $M1FieldName[100]="M1_ETC03";
  $M1FieldName[101]="M1_ETC04";
  $M1FieldName[102]="M1_ETC05";
  $M1FieldName[103]="M1_ETC06";
  $M1FieldName[104]="M1_ETC07";
  $M1FieldName[105]="M1_ETC08";
  $M1FieldName[106]="M1_ETC09";
  $M1FieldName[107]="M1_ETC10";
  $M1FieldName[108]="M1_ETC11";
  $M1FieldName[109]="M1_ETC12";
  $M1FieldName[110]="M1_ETC13";
  $M1FieldName[111]="M1_ETC14";
  $M1FieldName[112]="M1_ETC15";
  $M1FieldName[113]="M1_ETC16";
  $M1FieldName[114]="M1_ETC17";
  $M1FieldName[115]="M1_ETC18";
  $M1FieldName[116]="M1_ETC19";
  $M1FieldName[117]="M1_ETC20";
  $M1FieldName[118]="M1_DVAL11";
  $M1FieldName[119]="M1_DVAL12";
  $M1FieldName[120]="M1_DVAL13";
  $M1FieldName[121]="M1_DVAL14";
  $M1FieldName[122]="M1_DVAL15";
  $M1FieldName[123]="M1_DVAL16";
  $M1FieldName[124]="M1_DVAL17";
  $M1FieldName[125]="M1_DVAL18";
  $M1FieldName[126]="M1_DVAL19";
  $M1FieldName[127]="M1_DVAL20";
  $M1FieldName[128]="M1_DVAL21";
  $M1FieldName[129]="M1_DVAL22";
  $M1FieldName[130]="M1_DVAL23";
  $M1FieldName[131]="M1_DVAL24";
  $M1FieldName[132]="M1_DVAL25";
  $M1FieldName[133]="M1_DVAL26";
  $M1FieldName[134]="M1_DVAL27";
  $M1FieldName[135]="M1_DVAL28";
  $M1FieldName[136]="M1_DVAL29";
  $M1FieldName[137]="M1_DVAL30";
  $M1FieldName[138]="M1_DTXT11";
  $M1FieldName[139]="M1_DTXT12";
  $M1FieldName[140]="M1_DTXT13";
  $M1FieldName[141]="M1_DTXT14";
  $M1FieldName[142]="M1_DTXT15";
  $M1FieldName[143]="M1_DTXT16";
  $M1FieldName[144]="M1_DTXT17";
  $M1FieldName[145]="M1_DTXT18";
  $M1FieldName[146]="M1_DTXT19";
  $M1FieldName[147]="M1_DTXT20";
  $M1FieldName[148]="M1_DTXT21";
  $M1FieldName[149]="M1_DTXT22";
  $M1FieldName[150]="M1_DTXT23";
  $M1FieldName[151]="M1_DTXT24";
  $M1FieldName[152]="M1_DTXT25";
  $M1FieldName[153]="M1_DTXT26";
  $M1FieldName[154]="M1_DTXT27";
  $M1FieldName[155]="M1_DTXT28";
  $M1FieldName[156]="M1_DTXT29";
  $M1FieldName[157]="M1_DTXT30";

  $M1FieldName[158]="M1_ETC21";
  $M1FieldName[159]="M1_ETC22";
  $M1FieldName[160]="M1_ETC23";
  $M1FieldName[161]="M1_ETC24";
  $M1FieldName[162]="M1_ETC25";
  $M1FieldName[163]="M1_ETC26";
  $M1FieldName[164]="M1_ETC27";
  $M1FieldName[165]="M1_ETC28";
  $M1FieldName[166]="M1_ETC29";
  $M1FieldName[167]="M1_ETC30";
  $M1FieldName[168]="M1_ETC31";
  $M1FieldName[169]="M1_ETC32";
  $M1FieldName[170]="M1_ETC33";
  $M1FieldName[171]="M1_ETC34";
  $M1FieldName[172]="M1_ETC35";
  $M1FieldName[173]="M1_ETC36";
  $M1FieldName[174]="M1_ETC37";
  $M1FieldName[175]="M1_ETC38";
  $M1FieldName[176]="M1_ETC39";
  $M1FieldName[177]="M1_ETC40";
  $M1FieldName[178]="M1_ETC41";
  $M1FieldName[179]="M1_ETC42";
  $M1FieldName[180]="M1_ETC43";
  $M1FieldName[181]="M1_ETC44";
  $M1FieldName[182]="M1_ETC45";
  $M1FieldName[183]="M1_ETC46";
  $M1FieldName[184]="M1_ETC47";
  $M1FieldName[185]="M1_ETC48";
  $M1FieldName[186]="M1_ETC49";
  $M1FieldName[187]="M1_ETC50";
  $M1FieldName[188]="M1_ETC51";
  $M1FieldName[189]="M1_ETC52";
  $M1FieldName[190]="M1_ETC53";
  $M1FieldName[191]="M1_ETC54";
  $M1FieldName[192]="M1_ETC55";
  $M1FieldName[193]="M1_ETC56";
  $M1FieldName[194]="M1_ETC57";
  $M1FieldName[195]="M1_ETC58";
  $M1FieldName[196]="M1_ETC59";
  $M1FieldName[197]="M1_ETC60";
  $M1FieldName[198]="M1_ETC61";
  $M1FieldName[199]="M1_ETC62";
  $M1FieldName[200]="M1_ETC63";
  $M1FieldName[201]="M1_ETC64";
  $M1FieldName[202]="M1_ETC65";
  $M1FieldName[203]="M1_ETC66";
  $M1FieldName[204]="M1_ETC67";
  $M1FieldName[205]="M1_ETC68";
  $M1FieldName[206]="M1_ETC69";
  $M1FieldName[207]="M1_ETC70";
  $M1FieldName[208]="M1_ETC71";
  $M1FieldName[209]="M1_ETC72";
  $M1FieldName[210]="M1_ETC73";
  $M1FieldName[211]="M1_ETC74";
  $M1FieldName[212]="M1_ETC75";
  $M1FieldName[213]="M1_ETC76";
  $M1FieldName[214]="M1_ETC77";
  $M1FieldName[215]="M1_ETC78";
  $M1FieldName[216]="M1_ETC79";
  $M1FieldName[217]="M1_ETC80";
  $M1FieldName[218]="M1_ETC81";
  $M1FieldName[219]="M1_ETC82";
  $M1FieldName[220]="M1_ETC83";
  $M1FieldName[221]="M1_ETC84";
  $M1FieldName[222]="M1_ETC85";
  $M1FieldName[223]="M1_ETC86";
  $M1FieldName[224]="M1_ETC87";
  $M1FieldName[225]="M1_ETC88";
  $M1FieldName[226]="M1_ETC89";
  $M1FieldName[227]="M1_ETC90";
  $M1FieldName[228]="M1_ETC91";
  $M1FieldName[229]="M1_ETC92";
  $M1FieldName[230]="M1_ETC93";
  $M1FieldName[231]="M1_ETC94";
  $M1FieldName[232]="M1_ETC95";
  $M1FieldName[233]="M1_ETC96";
  $M1FieldName[234]="M1_ETC97";
  $M1FieldName[235]="M1_ETC98";
  $M1FieldName[236]="M1_ETC99";

  $M1FieldName[237]="M1_ETC100"; // Bank branch
  $M1FieldName[238]="M1_ETC101"; // Bank address
  $M1FieldName[239]="M1_ETC102"; // Intermediary Bank name
  $M1FieldName[240]="M1_ETC103"; // Intermediary Bank branch
  $M1FieldName[241]="M1_ETC104"; // Intermediary Bank address
  $M1FieldName[242]="M1_ETC105"; // 適格請求書発行事業者登録番号
  $M1FieldName[243]="M1_ETC106"; // Banking DetailsのBilling Address 2 Organization Name
  $M1FieldName[244]="M1_ETC107"; // Banking DetailsのBilling Address 2 Name
  $M1FieldName[245]="M1_ETC108"; // Banking DetailsのBilling Address 2 Attention
  $M1FieldName[246]="M1_ETC109"; // Banking DetailsのBilling Address 2 Address Line1
  $M1FieldName[247]="M1_ETC110"; // Banking DetailsのBilling Address 2 Address Line2
  $M1FieldName[248]="M1_ETC111"; // Banking DetailsのBilling Address 2 City/Region
  $M1FieldName[249]="M1_ETC112"; // Banking DetailsのBilling Address 2 State/Province
  $M1FieldName[250]="M1_ETC113"; // Banking DetailsのBilling Address 2 Zip/Postal Code
  $M1FieldName[251]="M1_ETC114"; // Banking DetailsのBilling Address 2 Country
  $M1FieldName[252]="M1_ETC115"; // Banking DetailsのBilling Address 3 Organization Name
  $M1FieldName[253]="M1_ETC116"; // Banking DetailsのBilling Address 3 Name
  $M1FieldName[254]="M1_ETC117"; // Banking DetailsのBilling Address 3 Attention
  $M1FieldName[255]="M1_ETC118"; // Banking DetailsのBilling Address 3 Address Line1
  $M1FieldName[256]="M1_ETC119"; // Banking DetailsのBilling Address 3 Address Line2
  $M1FieldName[257]="M1_ETC120"; // Banking DetailsのBilling Address 3 City/Region
  $M1FieldName[258]="M1_ETC121"; // Banking DetailsのBilling Address 3 State/Province
  $M1FieldName[259]="M1_ETC122"; // Banking DetailsのBilling Address 3 Ziop/Postal Code
  $M1FieldName[260]="M1_ETC123"; // Banking DetailsのBilling Address 3 Country
  $M1FieldName[261]="M1_ETC124"; // 初回ポップアップ制御フラグ(1:未表示、3:表示済)
  $M1FieldName[262]="M1_ETC125"; // SHIP TO 1 Explanation
  $M1FieldName[263]="M1_ETC126"; // SHIP TO 2 Explanation
  $M1FieldName[264]="M1_ETC127"; // SHIP TO 3 Explanation
  $M1FieldName[265]="M1_ETC128"; // Head Office Location Organization Name
  $M1FieldName[266]="M1_ETC129"; // Head Office Location Address Line1
  $M1FieldName[267]="M1_ETC130"; // Head Office Location Address Line2
  $M1FieldName[268]="M1_ETC131"; // Head Office Location City/Province
  $M1FieldName[269]="M1_ETC132"; // Head Office Location ZIP/Postal Code
  $M1FieldName[270]="M1_ETC133"; // Head Office Location Country
  $M1FieldName[271]="M1_ETC134";
  $M1FieldName[272]="M1_ETC135";
  $M1FieldName[273]="M1_ETC136";
  $M1FieldName[274]="M1_ETC137";
  $M1FieldName[275]="M1_ETC138";
  $M1FieldName[276]="M1_ETC139";
  $M1FieldName[277]="M1_ETC140";
  $M1FieldName[278]="M1_ETC141";
  $M1FieldName[279]="M1_ETC142";
  $M1FieldName[280]="M1_ETC143";
  $M1FieldName[281]="M1_ETC144";
  $M1FieldName[282]="M1_ETC145";
  $M1FieldName[283]="M1_ETC146";
  $M1FieldName[284]="M1_ETC147";
  $M1FieldName[285]="M1_ETC148";
  $M1FieldName[286]="M1_ETC149";
  $M1FieldName[287]="M1_ETC150";


  $M1FieldAtt[0]="0";
  $M1FieldAtt[1]="0";
  $M1FieldAtt[2]="0";
  $M1FieldAtt[3]="0";
  $M1FieldAtt[4]="0";
  $M1FieldAtt[5]="0";
  $M1FieldAtt[6]="0";
  $M1FieldAtt[7]="0";
  $M1FieldAtt[8]="0";
  $M1FieldAtt[9]="0";
  $M1FieldAtt[10]="0";
  $M1FieldAtt[11]="0";
  $M1FieldAtt[12]="0";
  $M1FieldAtt[13]="0";
  $M1FieldAtt[14]="0";
  $M1FieldAtt[15]="0";
  $M1FieldAtt[16]="0";
  $M1FieldAtt[17]="0";
  $M1FieldAtt[18]="0";
  $M1FieldAtt[19]="0";
  $M1FieldAtt[20]="0";
  $M1FieldAtt[21]="0";
  $M1FieldAtt[22]="0";
  $M1FieldAtt[23]="0";
  $M1FieldAtt[24]="0";
  $M1FieldAtt[25]="1";
  $M1FieldAtt[26]="1";
  $M1FieldAtt[27]="1";
  $M1FieldAtt[28]="1";
  $M1FieldAtt[29]="1";
  $M1FieldAtt[30]="1";
  $M1FieldAtt[31]="1";
  $M1FieldAtt[32]="1";
  $M1FieldAtt[33]="1";
  $M1FieldAtt[34]="1";
  $M1FieldAtt[35]="2";
  $M1FieldAtt[36]="2";
  $M1FieldAtt[37]="2";
  $M1FieldAtt[38]="2";
  $M1FieldAtt[39]="2";
  $M1FieldAtt[40]="2";
  $M1FieldAtt[41]="2";
  $M1FieldAtt[42]="2";
  $M1FieldAtt[43]="2";
  $M1FieldAtt[44]="2";
  $M1FieldAtt[45]="3";
  $M1FieldAtt[46]="3";
  $M1FieldAtt[47]="3";
  $M1FieldAtt[48]="3";
  $M1FieldAtt[49]="3";
  $M1FieldAtt[50]="3";
  $M1FieldAtt[51]="3";
  $M1FieldAtt[52]="3";
  $M1FieldAtt[53]="3";
  $M1FieldAtt[54]="3";
  $M1FieldAtt[55]="4";
  $M1FieldAtt[56]="4";
  $M1FieldAtt[57]="4";
  $M1FieldAtt[58]="4";
  $M1FieldAtt[59]="4";
  $M1FieldAtt[60]="4";
  $M1FieldAtt[61]="4";
  $M1FieldAtt[62]="4";
  $M1FieldAtt[63]="4";
  $M1FieldAtt[64]="4";
  $M1FieldAtt[65]="1";
  $M1FieldAtt[66]="1";
  $M1FieldAtt[67]="1";
  $M1FieldAtt[68]="1";
  $M1FieldAtt[69]="1";
  $M1FieldAtt[70]="1";
  $M1FieldAtt[71]="1";
  $M1FieldAtt[72]="1";
  $M1FieldAtt[73]="1";
  $M1FieldAtt[74]="1";
  $M1FieldAtt[75]="2";
  $M1FieldAtt[76]="2";
  $M1FieldAtt[77]="2";
  $M1FieldAtt[78]="2";
  $M1FieldAtt[79]="2";
  $M1FieldAtt[80]="2";
  $M1FieldAtt[81]="2";
  $M1FieldAtt[82]="2";
  $M1FieldAtt[83]="2";
  $M1FieldAtt[84]="2";
  $M1FieldAtt[85]="3";
  $M1FieldAtt[86]="3";
  $M1FieldAtt[87]="3";
  $M1FieldAtt[88]="3";
  $M1FieldAtt[89]="3";
  $M1FieldAtt[90]="3";
  $M1FieldAtt[91]="3";
  $M1FieldAtt[92]="3";
  $M1FieldAtt[93]="3";
  $M1FieldAtt[94]="3";
  $M1FieldAtt[95]="2";
  $M1FieldAtt[96]="0";
  $M1FieldAtt[97]="0";
  $M1FieldAtt[98]="3";
  $M1FieldAtt[99]="0";
  $M1FieldAtt[100]="0";
  $M1FieldAtt[101]="0";
  $M1FieldAtt[102]="0";
  $M1FieldAtt[103]="0";
  $M1FieldAtt[104]="0";
  $M1FieldAtt[105]="0";
  $M1FieldAtt[106]="0";
  $M1FieldAtt[107]="0";
  $M1FieldAtt[108]="0";
  $M1FieldAtt[109]="0";
  $M1FieldAtt[110]="0";
  $M1FieldAtt[111]="0";
  $M1FieldAtt[112]="0";
  $M1FieldAtt[113]="0";
  $M1FieldAtt[114]="0";
  $M1FieldAtt[115]="0";
  $M1FieldAtt[116]="0";
  $M1FieldAtt[117]="0";
  $M1FieldAtt[118]="0";
  $M1FieldAtt[119]="0";
  $M1FieldAtt[120]="0";
  $M1FieldAtt[121]="0";
  $M1FieldAtt[122]="0";
  $M1FieldAtt[123]="0";
  $M1FieldAtt[124]="0";
  $M1FieldAtt[125]="0";
  $M1FieldAtt[126]="0";
  $M1FieldAtt[127]="0";
  $M1FieldAtt[128]="0";
  $M1FieldAtt[129]="0";
  $M1FieldAtt[130]="0";
  $M1FieldAtt[131]="0";
  $M1FieldAtt[132]="0";
  $M1FieldAtt[133]="0";
  $M1FieldAtt[134]="0";
  $M1FieldAtt[135]="0";
  $M1FieldAtt[136]="0";
  $M1FieldAtt[137]="0";
  $M1FieldAtt[138]="0";
  $M1FieldAtt[139]="0";
  $M1FieldAtt[140]="0";
  $M1FieldAtt[141]="0";
  $M1FieldAtt[142]="0";
  $M1FieldAtt[143]="0";
  $M1FieldAtt[144]="0";
  $M1FieldAtt[145]="0";
  $M1FieldAtt[146]="0";
  $M1FieldAtt[147]="0";
  $M1FieldAtt[148]="0";
  $M1FieldAtt[149]="0";
  $M1FieldAtt[150]="0";
  $M1FieldAtt[151]="0";
  $M1FieldAtt[152]="0";
  $M1FieldAtt[153]="0";
  $M1FieldAtt[154]="0";
  $M1FieldAtt[155]="0";
  $M1FieldAtt[156]="0";
  $M1FieldAtt[157]="0";
  $M1FieldAtt[158]="0";
  $M1FieldAtt[159]="0";
  $M1FieldAtt[160]="0";
  $M1FieldAtt[161]="0";
  $M1FieldAtt[162]="0";
  $M1FieldAtt[163]="0";
  $M1FieldAtt[164]="0";
  $M1FieldAtt[165]="0";
  $M1FieldAtt[166]="0";
  $M1FieldAtt[167]="0";
  $M1FieldAtt[168]="0";
  $M1FieldAtt[169]="0";
  $M1FieldAtt[170]="0";
  $M1FieldAtt[171]="0";
  $M1FieldAtt[172]="0";
  $M1FieldAtt[173]="0";
  $M1FieldAtt[174]="0";
  $M1FieldAtt[175]="0";
  $M1FieldAtt[176]="0";
  $M1FieldAtt[177]="0";
  $M1FieldAtt[178]="0";
  $M1FieldAtt[179]="0";
  $M1FieldAtt[180]="0";
  $M1FieldAtt[181]="0";
  $M1FieldAtt[182]="0";
  $M1FieldAtt[183]="4";
  $M1FieldAtt[184]="0";
  $M1FieldAtt[185]="0";
  $M1FieldAtt[186]="0";
  $M1FieldAtt[187]="2";
  $M1FieldAtt[188]="2";
  $M1FieldAtt[189]="2";
  $M1FieldAtt[190]="2";
  $M1FieldAtt[191]="2";
  $M1FieldAtt[192]="2";
  $M1FieldAtt[193]="2";
  $M1FieldAtt[194]="2";
  $M1FieldAtt[195]="2";
  $M1FieldAtt[196]="2";
  $M1FieldAtt[197]="2";
  $M1FieldAtt[198]="2";
  $M1FieldAtt[199]="2";
  $M1FieldAtt[200]="2";
  $M1FieldAtt[201]="2";
  $M1FieldAtt[202]="2";
  $M1FieldAtt[203]="2";
  $M1FieldAtt[204]="2";
  $M1FieldAtt[205]="2";
  $M1FieldAtt[206]="2";
  $M1FieldAtt[207]="2";
  $M1FieldAtt[208]="2";
  $M1FieldAtt[209]="2";
  $M1FieldAtt[210]="2";
  $M1FieldAtt[211]="2";
  $M1FieldAtt[212]="2";
  $M1FieldAtt[213]="2";
  $M1FieldAtt[214]="3";
  $M1FieldAtt[215]="3";
  $M1FieldAtt[216]="0";
  $M1FieldAtt[217]="0";
  $M1FieldAtt[218]="0";
  $M1FieldAtt[219]="0";
  $M1FieldAtt[220]="0";
  $M1FieldAtt[221]="0";
  $M1FieldAtt[222]="0";
  $M1FieldAtt[223]="0";
  $M1FieldAtt[224]="0";
  $M1FieldAtt[225]="0";
  $M1FieldAtt[226]="0";
  $M1FieldAtt[227]="0";
  $M1FieldAtt[228]="0";
  $M1FieldAtt[229]="0";
  $M1FieldAtt[230]="0";
  $M1FieldAtt[231]="0";
  $M1FieldAtt[232]="0";
  $M1FieldAtt[233]="0";
  $M1FieldAtt[234]="0";
  $M1FieldAtt[235]="0";
  $M1FieldAtt[236]="0";

  $M1FieldAtt[237]="0";
  $M1FieldAtt[238]="0";
  $M1FieldAtt[239]="0";
  $M1FieldAtt[240]="0";
  $M1FieldAtt[241]="0";
  $M1FieldAtt[242]="0";
  $M1FieldAtt[243]="0";
  $M1FieldAtt[244]="0";
  $M1FieldAtt[245]="0";
  $M1FieldAtt[246]="0";
  $M1FieldAtt[247]="0";
  $M1FieldAtt[248]="0";
  $M1FieldAtt[249]="0";
  $M1FieldAtt[250]="0";
  $M1FieldAtt[251]="1";
  $M1FieldAtt[252]="0";
  $M1FieldAtt[253]="0";
  $M1FieldAtt[254]="0";
  $M1FieldAtt[255]="0";
  $M1FieldAtt[256]="0";
  $M1FieldAtt[257]="0";
  $M1FieldAtt[258]="0";
  $M1FieldAtt[259]="0";
  $M1FieldAtt[260]="1";
  $M1FieldAtt[261]="0";
  $M1FieldAtt[262]="0";
  $M1FieldAtt[263]="0";
  $M1FieldAtt[264]="0";
  $M1FieldAtt[265]="0";
  $M1FieldAtt[266]="0";
  $M1FieldAtt[267]="0";
  $M1FieldAtt[268]="0";
  $M1FieldAtt[269]="0";
  $M1FieldAtt[270]="1";
  $M1FieldAtt[271]="0";
  $M1FieldAtt[272]="0";
  $M1FieldAtt[273]="0";
  $M1FieldAtt[274]="0";
  $M1FieldAtt[275]="0";
  $M1FieldAtt[276]="0";
  $M1FieldAtt[277]="0";
  $M1FieldAtt[278]="0";
  $M1FieldAtt[279]="0";
  $M1FieldAtt[280]="0";
  $M1FieldAtt[281]="0";
  $M1FieldAtt[282]="0";
  $M1FieldAtt[283]="0";
  $M1FieldAtt[284]="0";
  $M1FieldAtt[285]="0";
  $M1FieldAtt[286]="0";
  $M1FieldAtt[287]="0";

  
  $M1FieldMax=287;

	for ($i=0; $i<=$M1FieldMax; $i=$i+1) {
		if ($M1FieldAtt[$i]==4) {
			if ($item[$M1FieldName[$i]]=="") {
				$str=str_replace("[".$M1FieldName[$i]."]",$filepath1."s.gif",$str);
				$str=str_replace("[D-".$M1FieldName[$i]."]",$filepath1."s.gif",$str);
			} 
			if (strstr($item[$M1FieldName[$i]], "s.gif")==true){
				$str=DispParamNone($str, $Param."-".$M1FieldName[$i]);
			} else {
				$str=DispParam($str, $Param."-".$M1FieldName[$i]);
			} 
		} 
		// HTMLエスケープ処理（一覧表示系）
		$str=str_replace("[".$Param."-".$M1FieldName[$i]."]",htmlspecialchars($item[$M1FieldName[$i]]),$str);
		$str=str_replace("[D-".$Param."-".$M1FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($M1FieldName[$i].":","",htmlspecialchars($item[$M1FieldName[$i]]))),$str);
		
    //そのまま出力
    $str=str_replace("[DD-".$Param."-".$M1FieldName[$i]."]",$item[$M1FieldName[$i]],$str);


    if (is_numeric($item[$M1FieldName[$i]])) {
			$str=str_replace("[N-".$Param."-".$M1FieldName[$i]."]",number_format($item[$M1FieldName[$i]],0),$str);
		} else {
			$str=str_replace("[N-".$Param."-".$M1FieldName[$i]."]","",$str);
		} 
		if ($item[$M1FieldName[$i]]==""){
			$str=DispParamNone($str, $Param."-".$M1FieldName[$i]);
		} else {
			$str=DispParam($str, $Param."-".$M1FieldName[$i]);
		} 
	}

	$StrSQL="SELECT ID FROM DAT_IINE where MID='".$_SESSION['MID']."' and MIDT='".$item['MID']."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2=mysqli_num_rows($rs2);
	if($item2>0){
		$str=DispParam($str, "IINE-BTN-ON");
		$str=DispParamNone($str, "IINE-BTN-OFF");
	} else {
		$str=DispParamNone($str, "IINE-BTN-ON");
		$str=DispParam($str, "IINE-BTN-OFF");
	}

  //youtube動画
  if($item["M1_ETC15"]=="M1_ETC15:公開する"){
    $str=DispParam($str, $Param."-"."M1_ETC15_OK");
  } else {
    $str=DispParamNone($str, $Param."-"."M1_ETC15_OK");
  }


	// 2021.01.18 yamamoto 平均評価
	$eval_avg = CalcEvalAvg($item['MID']);
	$str=str_replace("[D-".$Param."_EVAL_AVG]",$eval_avg,$str);

	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispM2($item, $str)
{
	eval(globals());

  $Param="M2";

  $M2FieldName[0]="ID";
  $M2FieldName[1]="MID";
  $M2FieldName[2]="EMAIL";
  $M2FieldName[3]="PASS";
  $M2FieldName[4]="SOCIALID";
  $M2FieldName[5]="M2_DVAL01";
  $M2FieldName[6]="M2_DVAL02";
  $M2FieldName[7]="M2_DVAL03";
  $M2FieldName[8]="M2_DVAL04";
  $M2FieldName[9]="M2_DVAL05";
  $M2FieldName[10]="M2_DVAL06";
  $M2FieldName[11]="M2_DVAL07";
  $M2FieldName[12]="M2_DVAL08";
  $M2FieldName[13]="M2_DVAL09";
  $M2FieldName[14]="M2_DVAL10";
  $M2FieldName[15]="M2_DTXT01";
  $M2FieldName[16]="M2_DTXT02";
  $M2FieldName[17]="M2_DTXT03";
  $M2FieldName[18]="M2_DTXT04";
  $M2FieldName[19]="M2_DTXT05";
  $M2FieldName[20]="M2_DTXT06";
  $M2FieldName[21]="M2_DTXT07";
  $M2FieldName[22]="M2_DTXT08";
  $M2FieldName[23]="M2_DTXT09";
  $M2FieldName[24]="M2_DTXT10";
  $M2FieldName[25]="M2_DSEL01";
  $M2FieldName[26]="M2_DSEL02";
  $M2FieldName[27]="M2_DSEL03";
  $M2FieldName[28]="M2_DSEL04";
  $M2FieldName[29]="M2_DSEL05";
  $M2FieldName[30]="M2_DSEL06";
  $M2FieldName[31]="M2_DSEL07";
  $M2FieldName[32]="M2_DSEL08";
  $M2FieldName[33]="M2_DSEL09";
  $M2FieldName[34]="M2_DSEL10";
  $M2FieldName[35]="M2_DRDO01";
  $M2FieldName[36]="M2_DRDO02";
  $M2FieldName[37]="M2_DRDO03";
  $M2FieldName[38]="M2_DRDO04";
  $M2FieldName[39]="M2_DRDO05";
  $M2FieldName[40]="M2_DRDO06";
  $M2FieldName[41]="M2_DRDO07";
  $M2FieldName[42]="M2_DRDO08";
  $M2FieldName[43]="M2_DRDO09";
  $M2FieldName[44]="M2_DRDO10";
  $M2FieldName[45]="M2_DCHK01";
  $M2FieldName[46]="M2_DCHK02";
  $M2FieldName[47]="M2_DCHK03";
  $M2FieldName[48]="M2_DCHK04";
  $M2FieldName[49]="M2_DCHK05";
  $M2FieldName[50]="M2_DCHK06";
  $M2FieldName[51]="M2_DCHK07";
  $M2FieldName[52]="M2_DCHK08";
  $M2FieldName[53]="M2_DCHK09";
  $M2FieldName[54]="M2_DCHK10";
  $M2FieldName[55]="M2_DFIL01";
  $M2FieldName[56]="M2_DFIL02";
  $M2FieldName[57]="M2_DFIL03";
  $M2FieldName[58]="M2_DFIL04";
  $M2FieldName[59]="M2_DFIL05";
  $M2FieldName[60]="M2_DFIL06";
  $M2FieldName[61]="M2_DFIL07";
  $M2FieldName[62]="M2_DFIL08";
  $M2FieldName[63]="M2_DFIL09";
  $M2FieldName[64]="M2_DFIL10";
  $M2FieldName[65]="M2_MSEL01";
  $M2FieldName[66]="M2_MSEL02";
  $M2FieldName[67]="M2_MSEL03";
  $M2FieldName[68]="M2_MSEL04";
  $M2FieldName[69]="M2_MSEL05";
  $M2FieldName[70]="M2_MSEL06";
  $M2FieldName[71]="M2_MSEL07";
  $M2FieldName[72]="M2_MSEL08";
  $M2FieldName[73]="M2_MSEL09";
  $M2FieldName[74]="M2_MSEL10";
  $M2FieldName[75]="M2_MRDO01";
  $M2FieldName[76]="M2_MRDO02";
  $M2FieldName[77]="M2_MRDO03";
  $M2FieldName[78]="M2_MRDO04";
  $M2FieldName[79]="M2_MRDO05";
  $M2FieldName[80]="M2_MRDO06";
  $M2FieldName[81]="M2_MRDO07";
  $M2FieldName[82]="M2_MRDO08";
  $M2FieldName[83]="M2_MRDO09";
  $M2FieldName[84]="M2_MRDO10";
  $M2FieldName[85]="M2_MCHK01";
  $M2FieldName[86]="M2_MCHK02";
  $M2FieldName[87]="M2_MCHK03";
  $M2FieldName[88]="M2_MCHK04";
  $M2FieldName[89]="M2_MCHK05";
  $M2FieldName[90]="M2_MCHK06";
  $M2FieldName[91]="M2_MCHK07";
  $M2FieldName[92]="M2_MCHK08";
  $M2FieldName[93]="M2_MCHK09";
  $M2FieldName[94]="M2_MCHK10";
  $M2FieldName[95]="ENABLE";
  $M2FieldName[96]="NEWDATE";
  $M2FieldName[97]="EDITDATE";
  $M2FieldName[98]="M2_ETC01";
  $M2FieldName[99]="M2_ETC02";
  $M2FieldName[100]="M2_ETC03";
  $M2FieldName[101]="M2_ETC04";
  $M2FieldName[102]="M2_ETC05";
  $M2FieldName[103]="M2_ETC06";
  $M2FieldName[104]="M2_ETC07";
  $M2FieldName[105]="M2_ETC08";
  $M2FieldName[106]="M2_ETC09";
  $M2FieldName[107]="M2_ETC10";
  $M2FieldName[108]="M2_ETC11";
  $M2FieldName[109]="M2_ETC12";
  $M2FieldName[110]="M2_ETC13";
  $M2FieldName[111]="M2_ETC14";
  $M2FieldName[112]="M2_ETC15";
  $M2FieldName[113]="M2_ETC16";
  $M2FieldName[114]="M2_ETC17";
  $M2FieldName[115]="M2_ETC18";
  $M2FieldName[116]="M2_ETC19";
  $M2FieldName[117]="M2_ETC20";
  $M2FieldName[118]="M2_DVAL11";
  $M2FieldName[119]="M2_DVAL12";
  $M2FieldName[120]="M2_DVAL13";
  $M2FieldName[121]="M2_DVAL14";
  $M2FieldName[122]="M2_DVAL15";
  $M2FieldName[123]="M2_DVAL16";
  $M2FieldName[124]="M2_DVAL17";
  $M2FieldName[125]="M2_DVAL18";
  $M2FieldName[126]="M2_DVAL19";
  $M2FieldName[127]="M2_DVAL20";
  $M2FieldName[128]="M2_DVAL21";
  $M2FieldName[129]="M2_DVAL22";
  $M2FieldName[130]="M2_DVAL23";
  $M2FieldName[131]="M2_DVAL24";
  $M2FieldName[132]="M2_DVAL25";
  $M2FieldName[133]="M2_DVAL26";
  $M2FieldName[134]="M2_DVAL27";
  $M2FieldName[135]="M2_DVAL28";
  $M2FieldName[136]="M2_DVAL29";
  $M2FieldName[137]="M2_DVAL30";
  $M2FieldName[138]="M2_DTXT11";
  $M2FieldName[139]="M2_DTXT12";
  $M2FieldName[140]="M2_DTXT13";
  $M2FieldName[141]="M2_DTXT14";
  $M2FieldName[142]="M2_DTXT15";
  $M2FieldName[143]="M2_DTXT16";
  $M2FieldName[144]="M2_DTXT17";
  $M2FieldName[145]="M2_DTXT18";
  $M2FieldName[146]="M2_DTXT19";
  $M2FieldName[147]="M2_DTXT20";
  $M2FieldName[148]="M2_DTXT21";
  $M2FieldName[149]="M2_DTXT22";
  $M2FieldName[150]="M2_DTXT23";
  $M2FieldName[151]="M2_DTXT24";
  $M2FieldName[152]="M2_DTXT25";
  $M2FieldName[153]="M2_DTXT26";
  $M2FieldName[154]="M2_DTXT27";
  $M2FieldName[155]="M2_DTXT28";
  $M2FieldName[156]="M2_DTXT29";
  $M2FieldName[157]="M2_DTXT30";
  

  $M2FieldAtt[0]="0";
  $M2FieldAtt[1]="0";
  $M2FieldAtt[2]="0";
  $M2FieldAtt[3]="0";
  $M2FieldAtt[4]="0";
  $M2FieldAtt[5]="0";
  $M2FieldAtt[6]="0";
  $M2FieldAtt[7]="0";
  $M2FieldAtt[8]="0";
  $M2FieldAtt[9]="0";
  $M2FieldAtt[10]="0";
  $M2FieldAtt[11]="0";
  $M2FieldAtt[12]="0";
  $M2FieldAtt[13]="0";
  $M2FieldAtt[14]="0";
  $M2FieldAtt[15]="0";
  $M2FieldAtt[16]="0";
  $M2FieldAtt[17]="0";
  $M2FieldAtt[18]="0";
  $M2FieldAtt[19]="0";
  $M2FieldAtt[20]="0";
  $M2FieldAtt[21]="0";
  $M2FieldAtt[22]="0";
  $M2FieldAtt[23]="0";
  $M2FieldAtt[24]="0";
  $M2FieldAtt[25]="1";
  $M2FieldAtt[26]="1";
  $M2FieldAtt[27]="1";
  $M2FieldAtt[28]="1";
  $M2FieldAtt[29]="1";
  $M2FieldAtt[30]="1";
  $M2FieldAtt[31]="1";
  $M2FieldAtt[32]="1";
  $M2FieldAtt[33]="1";
  $M2FieldAtt[34]="1";
  $M2FieldAtt[35]="2";
  $M2FieldAtt[36]="2";
  $M2FieldAtt[37]="2";
  $M2FieldAtt[38]="2";
  $M2FieldAtt[39]="2";
  $M2FieldAtt[40]="2";
  $M2FieldAtt[41]="2";
  $M2FieldAtt[42]="2";
  $M2FieldAtt[43]="2";
  $M2FieldAtt[44]="2";
  $M2FieldAtt[45]="3";
  $M2FieldAtt[46]="3";
  $M2FieldAtt[47]="3";
  $M2FieldAtt[48]="3";
  $M2FieldAtt[49]="3";
  $M2FieldAtt[50]="3";
  $M2FieldAtt[51]="3";
  $M2FieldAtt[52]="3";
  $M2FieldAtt[53]="3";
  $M2FieldAtt[54]="3";
  $M2FieldAtt[55]="4";
  $M2FieldAtt[56]="4";
  $M2FieldAtt[57]="4";
  $M2FieldAtt[58]="4";
  $M2FieldAtt[59]="4";
  $M2FieldAtt[60]="4";
  $M2FieldAtt[61]="4";
  $M2FieldAtt[62]="4";
  $M2FieldAtt[63]="4";
  $M2FieldAtt[64]="4";
  $M2FieldAtt[65]="1";
  $M2FieldAtt[66]="1";
  $M2FieldAtt[67]="1";
  $M2FieldAtt[68]="1";
  $M2FieldAtt[69]="1";
  $M2FieldAtt[70]="1";
  $M2FieldAtt[71]="1";
  $M2FieldAtt[72]="1";
  $M2FieldAtt[73]="1";
  $M2FieldAtt[74]="1";
  $M2FieldAtt[75]="2";
  $M2FieldAtt[76]="2";
  $M2FieldAtt[77]="2";
  $M2FieldAtt[78]="2";
  $M2FieldAtt[79]="2";
  $M2FieldAtt[80]="2";
  $M2FieldAtt[81]="2";
  $M2FieldAtt[82]="2";
  $M2FieldAtt[83]="2";
  $M2FieldAtt[84]="2";
  $M2FieldAtt[85]="3";
  $M2FieldAtt[86]="3";
  $M2FieldAtt[87]="3";
  $M2FieldAtt[88]="3";
  $M2FieldAtt[89]="3";
  $M2FieldAtt[90]="3";
  $M2FieldAtt[91]="3";
  $M2FieldAtt[92]="3";
  $M2FieldAtt[93]="3";
  $M2FieldAtt[94]="3";
  $M2FieldAtt[95]="2";
  $M2FieldAtt[96]="0";
  $M2FieldAtt[97]="0";
  $M2FieldAtt[98]="3";
  $M2FieldAtt[99]="0";
  $M2FieldAtt[100]="0";
  $M2FieldAtt[101]="0";
  $M2FieldAtt[102]="0";
  $M2FieldAtt[103]="0";
  $M2FieldAtt[104]="0";
  $M2FieldAtt[105]="0";
  $M2FieldAtt[106]="0";
  $M2FieldAtt[107]="0";
  $M2FieldAtt[108]="0";
  $M2FieldAtt[109]="0";
  $M2FieldAtt[110]="0";
  $M2FieldAtt[111]="0";
  $M2FieldAtt[112]="0";
  $M2FieldAtt[113]="0";
  $M2FieldAtt[114]="0";
  $M2FieldAtt[115]="0";
  $M2FieldAtt[116]="0";
  $M2FieldAtt[117]="0";
  $M2FieldAtt[118]="0";
  $M2FieldAtt[119]="0";
  $M2FieldAtt[120]="0";
  $M2FieldAtt[121]="0";
  $M2FieldAtt[122]="0";
  $M2FieldAtt[123]="0";
  $M2FieldAtt[124]="0";
  $M2FieldAtt[125]="0";
  $M2FieldAtt[126]="0";
  $M2FieldAtt[127]="0";
  $M2FieldAtt[128]="0";
  $M2FieldAtt[129]="0";
  $M2FieldAtt[130]="0";
  $M2FieldAtt[131]="0";
  $M2FieldAtt[132]="0";
  $M2FieldAtt[133]="0";
  $M2FieldAtt[134]="0";
  $M2FieldAtt[135]="0";
  $M2FieldAtt[136]="0";
  $M2FieldAtt[137]="0";
  $M2FieldAtt[138]="0";
  $M2FieldAtt[139]="0";
  $M2FieldAtt[140]="0";
  $M2FieldAtt[141]="0";
  $M2FieldAtt[142]="0";
  $M2FieldAtt[143]="0";
  $M2FieldAtt[144]="0";
  $M2FieldAtt[145]="0";
  $M2FieldAtt[146]="0";
  $M2FieldAtt[147]="0";
  $M2FieldAtt[148]="0";
  $M2FieldAtt[149]="0";
  $M2FieldAtt[150]="0";
  $M2FieldAtt[151]="0";
  $M2FieldAtt[152]="0";
  $M2FieldAtt[153]="0";
  $M2FieldAtt[154]="0";
  $M2FieldAtt[155]="0";
  $M2FieldAtt[156]="0";
  $M2FieldAtt[157]="0";
  

  $M2FieldMax=157;

	for ($i=0; $i<=$M2FieldMax; $i=$i+1) {
		if ($M2FieldAtt[$i]==4) {
			if ($item[$M2FieldName[$i]]=="") {
				$str=str_replace("[".$M2FieldName[$i]."]",$filepath1."s.gif",$str);
				$str=str_replace("[D-".$M2FieldName[$i]."]",$filepath1."s.gif",$str);
			} 
			if (strstr($item[$M2FieldName[$i]], "s.gif")==true){
				$str=DispParamNone($str, $Param."-".$M2FieldName[$i]);
			} else {
				$str=DispParam($str, $Param."-".$M2FieldName[$i]);
			} 
		} 
		// HTMLエスケープ処理（一覧表示系）
		$str=str_replace("[".$Param."-".$M2FieldName[$i]."]",htmlspecialchars($item[$M2FieldName[$i]]),$str);
		$str=str_replace("[D-".$Param."-".$M2FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($M2FieldName[$i].":","",htmlspecialchars($item[$M2FieldName[$i]]))),$str);
		if (is_numeric($item[$M2FieldName[$i]])) {
			$str=str_replace("[N-".$Param."-".$M2FieldName[$i]."]",number_format($item[$M2FieldName[$i]],0),$str);
		} else {
			$str=str_replace("[N-".$Param."-".$M2FieldName[$i]."]","",$str);
		} 
		if ($item[$M2FieldName[$i]]==""){
			$str=DispParamNone($str, $Param."-".$M2FieldName[$i]);
		} else {
			$str=DispParam($str, $Param."-".$M2FieldName[$i]);
		} 
	}

	$StrSQL="SELECT ID FROM DAT_IINE where MID='".$_SESSION['MID']."' and MIDT='".$item['MID']."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2=mysqli_num_rows($rs2);
	if($item2>0){
		$str=DispParam($str, "IINE-BTN-ON");
		$str=DispParamNone($str, "IINE-BTN-OFF");
	} else {
		$str=DispParamNone($str, "IINE-BTN-ON");
		$str=DispParam($str, "IINE-BTN-OFF");
	}

	// 2021.01.18 yamamoto 平均評価
	$eval_avg = CalcEvalAvg($item['MID']);
	$str=str_replace("[D-".$Param."_EVAL_AVG]",$eval_avg,$str);

	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispO1($item, $str)
{
	eval(globals());

  $Param="O1";

  $O1FieldName[0]="ID";
  $O1FieldName[1]="OID";
  $O1FieldName[2]="MID";
  $O1FieldName[3]="O1_DVAL01";
  $O1FieldName[4]="O1_DVAL02";
  $O1FieldName[5]="O1_DVAL03";
  $O1FieldName[6]="O1_DVAL04";
  $O1FieldName[7]="O1_DVAL05";
  $O1FieldName[8]="O1_DVAL06";
  $O1FieldName[9]="O1_DVAL07";
  $O1FieldName[10]="O1_DVAL08";
  $O1FieldName[11]="O1_DVAL09";
  $O1FieldName[12]="O1_DVAL10";
  $O1FieldName[13]="O1_DTXT01";
  $O1FieldName[14]="O1_DTXT02";
  $O1FieldName[15]="O1_DTXT03";
  $O1FieldName[16]="O1_DTXT04";
  $O1FieldName[17]="O1_DTXT05";
  $O1FieldName[18]="O1_DTXT06";
  $O1FieldName[19]="O1_DTXT07";
  $O1FieldName[20]="O1_DTXT08";
  $O1FieldName[21]="O1_DTXT09";
  $O1FieldName[22]="O1_DTXT10";
  $O1FieldName[23]="O1_DSEL01";
  $O1FieldName[24]="O1_DSEL02";
  $O1FieldName[25]="O1_DSEL03";
  $O1FieldName[26]="O1_DSEL04";
  $O1FieldName[27]="O1_DSEL05";
  $O1FieldName[28]="O1_DSEL06";
  $O1FieldName[29]="O1_DSEL07";
  $O1FieldName[30]="O1_DSEL08";
  $O1FieldName[31]="O1_DSEL09";
  $O1FieldName[32]="O1_DSEL10";
  $O1FieldName[33]="O1_DRDO01";
  $O1FieldName[34]="O1_DRDO02";
  $O1FieldName[35]="O1_DRDO03";
  $O1FieldName[36]="O1_DRDO04";
  $O1FieldName[37]="O1_DRDO05";
  $O1FieldName[38]="O1_DRDO06";
  $O1FieldName[39]="O1_DRDO07";
  $O1FieldName[40]="O1_DRDO08";
  $O1FieldName[41]="O1_DRDO09";
  $O1FieldName[42]="O1_DRDO10";
  $O1FieldName[43]="O1_DCHK01";
  $O1FieldName[44]="O1_DCHK02";
  $O1FieldName[45]="O1_DCHK03";
  $O1FieldName[46]="O1_DCHK04";
  $O1FieldName[47]="O1_DCHK05";
  $O1FieldName[48]="O1_DCHK06";
  $O1FieldName[49]="O1_DCHK07";
  $O1FieldName[50]="O1_DCHK08";
  $O1FieldName[51]="O1_DCHK09";
  $O1FieldName[52]="O1_DCHK10";
  $O1FieldName[53]="O1_DFIL01";
  $O1FieldName[54]="O1_DFIL02";
  $O1FieldName[55]="O1_DFIL03";
  $O1FieldName[56]="O1_DFIL04";
  $O1FieldName[57]="O1_DFIL05";
  $O1FieldName[58]="O1_DFIL06";
  $O1FieldName[59]="O1_DFIL07";
  $O1FieldName[60]="O1_DFIL08";
  $O1FieldName[61]="O1_DFIL09";
  $O1FieldName[62]="O1_DFIL10";
  $O1FieldName[63]="O1_MSEL01";
  $O1FieldName[64]="O1_MSEL02";
  $O1FieldName[65]="O1_MSEL03";
  $O1FieldName[66]="O1_MSEL04";
  $O1FieldName[67]="O1_MSEL05";
  $O1FieldName[68]="O1_MSEL06";
  $O1FieldName[69]="O1_MSEL07";
  $O1FieldName[70]="O1_MSEL08";
  $O1FieldName[71]="O1_MSEL09";
  $O1FieldName[72]="O1_MSEL10";
  $O1FieldName[73]="O1_MRDO01";
  $O1FieldName[74]="O1_MRDO02";
  $O1FieldName[75]="O1_MRDO03";
  $O1FieldName[76]="O1_MRDO04";
  $O1FieldName[77]="O1_MRDO05";
  $O1FieldName[78]="O1_MRDO06";
  $O1FieldName[79]="O1_MRDO07";
  $O1FieldName[80]="O1_MRDO08";
  $O1FieldName[81]="O1_MRDO09";
  $O1FieldName[82]="O1_MRDO10";
  $O1FieldName[83]="O1_MCHK01";
  $O1FieldName[84]="O1_MCHK02";
  $O1FieldName[85]="O1_MCHK03";
  $O1FieldName[86]="O1_MCHK04";
  $O1FieldName[87]="O1_MCHK05";
  $O1FieldName[88]="O1_MCHK06";
  $O1FieldName[89]="O1_MCHK07";
  $O1FieldName[90]="O1_MCHK08";
  $O1FieldName[91]="O1_MCHK09";
  $O1FieldName[92]="O1_MCHK10";
  $O1FieldName[93]="ENABLE";
  $O1FieldName[94]="NEWDATE";
  $O1FieldName[95]="EDITDATE";
  $O1FieldName[96]="O1_ETC01";
  $O1FieldName[97]="O1_ETC02";
  $O1FieldName[98]="O1_ETC03";
  $O1FieldName[99]="O1_ETC04";
  $O1FieldName[100]="O1_ETC05";
  $O1FieldName[101]="O1_ETC06";
  $O1FieldName[102]="O1_ETC07";
  $O1FieldName[103]="O1_ETC08";
  $O1FieldName[104]="O1_ETC09";
  $O1FieldName[105]="O1_ETC10";
  $O1FieldName[106]="O1_ETC11";
  $O1FieldName[107]="O1_ETC12";
  $O1FieldName[108]="O1_ETC13";
  $O1FieldName[109]="O1_ETC14";
  $O1FieldName[110]="O1_ETC15";
  $O1FieldName[111]="O1_ETC16";
  $O1FieldName[112]="O1_ETC17";
  $O1FieldName[113]="O1_ETC18";
  $O1FieldName[114]="O1_ETC19";
  $O1FieldName[115]="O1_ETC20";
  $O1FieldName[116]="O1_DVAL11";
  $O1FieldName[117]="O1_DVAL12";
  $O1FieldName[118]="O1_DVAL13";
  $O1FieldName[119]="O1_DVAL14";
  $O1FieldName[120]="O1_DVAL15";
  $O1FieldName[121]="O1_DVAL16";
  $O1FieldName[122]="O1_DVAL17";
  $O1FieldName[123]="O1_DVAL18";
  $O1FieldName[124]="O1_DVAL19";
  $O1FieldName[125]="O1_DVAL20";
  $O1FieldName[126]="O1_DVAL21";
  $O1FieldName[127]="O1_DVAL22";
  $O1FieldName[128]="O1_DVAL23";
  $O1FieldName[129]="O1_DVAL24";
  $O1FieldName[130]="O1_DVAL25";
  $O1FieldName[131]="O1_DVAL26";
  $O1FieldName[132]="O1_DVAL27";
  $O1FieldName[133]="O1_DVAL28";
  $O1FieldName[134]="O1_DVAL29";
  $O1FieldName[135]="O1_DVAL30";
  $O1FieldName[136]="O1_DTXT11";
  $O1FieldName[137]="O1_DTXT12";
  $O1FieldName[138]="O1_DTXT13";
  $O1FieldName[139]="O1_DTXT14";
  $O1FieldName[140]="O1_DTXT15";
  $O1FieldName[141]="O1_DTXT16";
  $O1FieldName[142]="O1_DTXT17";
  $O1FieldName[143]="O1_DTXT18";
  $O1FieldName[144]="O1_DTXT19";
  $O1FieldName[145]="O1_DTXT20";
  $O1FieldName[146]="O1_DTXT21";
  $O1FieldName[147]="O1_DTXT22";
  $O1FieldName[148]="O1_DTXT23";
  $O1FieldName[149]="O1_DTXT24";
  $O1FieldName[150]="O1_DTXT25";
  $O1FieldName[151]="O1_DTXT26";
  $O1FieldName[152]="O1_DTXT27";
  $O1FieldName[153]="O1_DTXT28";
  $O1FieldName[154]="O1_DTXT29";
  $O1FieldName[155]="O1_DTXT30";
  

  $O1FieldAtt[0]="0";
  $O1FieldAtt[1]="0";
  $O1FieldAtt[2]="0";
  $O1FieldAtt[3]="0";
  $O1FieldAtt[4]="0";
  $O1FieldAtt[5]="0";
  $O1FieldAtt[6]="0";
  $O1FieldAtt[7]="0";
  $O1FieldAtt[8]="0";
  $O1FieldAtt[9]="0";
  $O1FieldAtt[10]="0";
  $O1FieldAtt[11]="0";
  $O1FieldAtt[12]="0";
  $O1FieldAtt[13]="0";
  $O1FieldAtt[14]="0";
  $O1FieldAtt[15]="0";
  $O1FieldAtt[16]="0";
  $O1FieldAtt[17]="0";
  $O1FieldAtt[18]="0";
  $O1FieldAtt[19]="0";
  $O1FieldAtt[20]="0";
  $O1FieldAtt[21]="0";
  $O1FieldAtt[22]="0";
  $O1FieldAtt[23]="1";
  $O1FieldAtt[24]="1";
  $O1FieldAtt[25]="1";
  $O1FieldAtt[26]="1";
  $O1FieldAtt[27]="1";
  $O1FieldAtt[28]="1";
  $O1FieldAtt[29]="1";
  $O1FieldAtt[30]="1";
  $O1FieldAtt[31]="1";
  $O1FieldAtt[32]="1";
  $O1FieldAtt[33]="2";
  $O1FieldAtt[34]="2";
  $O1FieldAtt[35]="2";
  $O1FieldAtt[36]="2";
  $O1FieldAtt[37]="2";
  $O1FieldAtt[38]="2";
  $O1FieldAtt[39]="2";
  $O1FieldAtt[40]="2";
  $O1FieldAtt[41]="2";
  $O1FieldAtt[42]="2";
  $O1FieldAtt[43]="3";
  $O1FieldAtt[44]="3";
  $O1FieldAtt[45]="3";
  $O1FieldAtt[46]="3";
  $O1FieldAtt[47]="3";
  $O1FieldAtt[48]="3";
  $O1FieldAtt[49]="3";
  $O1FieldAtt[50]="3";
  $O1FieldAtt[51]="3";
  $O1FieldAtt[52]="3";
  $O1FieldAtt[53]="4";
  $O1FieldAtt[54]="4";
  $O1FieldAtt[55]="4";
  $O1FieldAtt[56]="4";
  $O1FieldAtt[57]="4";
  $O1FieldAtt[58]="4";
  $O1FieldAtt[59]="4";
  $O1FieldAtt[60]="4";
  $O1FieldAtt[61]="4";
  $O1FieldAtt[62]="4";
  $O1FieldAtt[63]="1";
  $O1FieldAtt[64]="1";
  $O1FieldAtt[65]="1";
  $O1FieldAtt[66]="1";
  $O1FieldAtt[67]="1";
  $O1FieldAtt[68]="1";
  $O1FieldAtt[69]="1";
  $O1FieldAtt[70]="1";
  $O1FieldAtt[71]="1";
  $O1FieldAtt[72]="1";
  $O1FieldAtt[73]="2";
  $O1FieldAtt[74]="2";
  $O1FieldAtt[75]="2";
  $O1FieldAtt[76]="2";
  $O1FieldAtt[77]="2";
  $O1FieldAtt[78]="2";
  $O1FieldAtt[79]="2";
  $O1FieldAtt[80]="2";
  $O1FieldAtt[81]="2";
  $O1FieldAtt[82]="2";
  $O1FieldAtt[83]="3";
  $O1FieldAtt[84]="3";
  $O1FieldAtt[85]="3";
  $O1FieldAtt[86]="3";
  $O1FieldAtt[87]="3";
  $O1FieldAtt[88]="3";
  $O1FieldAtt[89]="3";
  $O1FieldAtt[90]="3";
  $O1FieldAtt[91]="3";
  $O1FieldAtt[92]="3";
  $O1FieldAtt[93]="2";
  $O1FieldAtt[94]="0";
  $O1FieldAtt[95]="0";
  $O1FieldAtt[96]="0";
  $O1FieldAtt[97]="0";
  $O1FieldAtt[98]="0";
  $O1FieldAtt[99]="0";
  $O1FieldAtt[100]="0";
  $O1FieldAtt[101]="0";
  $O1FieldAtt[102]="0";
  $O1FieldAtt[103]="0";
  $O1FieldAtt[104]="0";
  $O1FieldAtt[105]="0";
  $O1FieldAtt[106]="0";
  $O1FieldAtt[107]="0";
  $O1FieldAtt[108]="0";
  $O1FieldAtt[109]="0";
  $O1FieldAtt[110]="0";
  $O1FieldAtt[111]="0";
  $O1FieldAtt[112]="0";
  $O1FieldAtt[113]="0";
  $O1FieldAtt[114]="0";
  $O1FieldAtt[115]="0";
  $O1FieldAtt[116]="0";
  $O1FieldAtt[117]="0";
  $O1FieldAtt[118]="0";
  $O1FieldAtt[119]="0";
  $O1FieldAtt[120]="0";
  $O1FieldAtt[121]="0";
  $O1FieldAtt[122]="0";
  $O1FieldAtt[123]="0";
  $O1FieldAtt[124]="0";
  $O1FieldAtt[125]="0";
  $O1FieldAtt[126]="0";
  $O1FieldAtt[127]="0";
  $O1FieldAtt[128]="0";
  $O1FieldAtt[129]="0";
  $O1FieldAtt[130]="0";
  $O1FieldAtt[131]="0";
  $O1FieldAtt[132]="0";
  $O1FieldAtt[133]="0";
  $O1FieldAtt[134]="0";
  $O1FieldAtt[135]="0";
  $O1FieldAtt[136]="0";
  $O1FieldAtt[137]="0";
  $O1FieldAtt[138]="0";
  $O1FieldAtt[139]="0";
  $O1FieldAtt[140]="0";
  $O1FieldAtt[141]="0";
  $O1FieldAtt[142]="0";
  $O1FieldAtt[143]="0";
  $O1FieldAtt[144]="0";
  $O1FieldAtt[145]="0";
  $O1FieldAtt[146]="0";
  $O1FieldAtt[147]="0";
  $O1FieldAtt[148]="0";
  $O1FieldAtt[149]="0";
  $O1FieldAtt[150]="0";
  $O1FieldAtt[151]="0";
  $O1FieldAtt[152]="0";
  $O1FieldAtt[153]="0";
  $O1FieldAtt[154]="0";
  $O1FieldAtt[155]="0";
  

  $O1FieldMax=155;

	for ($i=0; $i<=$O1FieldMax; $i=$i+1) {
		if ($O1FieldAtt[$i]==4) {
			if ($item[$O1FieldName[$i]]=="") {
				$str=str_replace("[".$O1FieldName[$i]."]",$filepath1."s.gif",$str);
				$str=str_replace("[D-".$O1FieldName[$i]."]",$filepath1."s.gif",$str);
			} 
			if (strstr($item[$O1FieldName[$i]], "s.gif")==true){
				$str=DispParamNone($str, $Param."-".$O1FieldName[$i]);
			} else {
				$str=DispParam($str, $Param."-".$O1FieldName[$i]);
			} 
		} 
		// HTMLエスケープ処理（一覧表示系）
		$str=str_replace("[".$Param."-".$O1FieldName[$i]."]",htmlspecialchars($item[$O1FieldName[$i]]),$str);
		$str=str_replace("[D-".$Param."-".$O1FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($O1FieldName[$i].":","",htmlspecialchars($item[$O1FieldName[$i]]))),$str);
		if (is_numeric($item[$O1FieldName[$i]])) {
			$str=str_replace("[N-".$Param."-".$O1FieldName[$i]."]",number_format($item[$O1FieldName[$i]],0),$str);
		} else {
			$str=str_replace("[N-".$Param."-".$O1FieldName[$i]."]","",$str);
		} 
		if ($item[$O1FieldName[$i]]==""){
			$str=DispParamNone($str, $Param."-".$O1FieldName[$i]);
		} else {
			$str=DispParam($str, $Param."-".$O1FieldName[$i]);
		} 
	}

	// 2021.01.18 yamamoto 平均評価
	$eval_avg = CalcEvalAvg($item['MID']);
	$str=str_replace("[D-".$Param."_EVAL_AVG]",$eval_avg,$str);

	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispO2($item, $str)
{
	eval(globals());

  $Param="O2";

  $O2FieldName[0]="ID";
  $O2FieldName[1]="OID";
  $O2FieldName[2]="MID";
  $O2FieldName[3]="O2_DVAL01";
  $O2FieldName[4]="O2_DVAL02";
  $O2FieldName[5]="O2_DVAL03";
  $O2FieldName[6]="O2_DVAL04";
  $O2FieldName[7]="O2_DVAL05";
  $O2FieldName[8]="O2_DVAL06";
  $O2FieldName[9]="O2_DVAL07";
  $O2FieldName[10]="O2_DVAL08";
  $O2FieldName[11]="O2_DVAL09";
  $O2FieldName[12]="O2_DVAL10";
  $O2FieldName[13]="O2_DTXT01";
  $O2FieldName[14]="O2_DTXT02";
  $O2FieldName[15]="O2_DTXT03";
  $O2FieldName[16]="O2_DTXT04";
  $O2FieldName[17]="O2_DTXT05";
  $O2FieldName[18]="O2_DTXT06";
  $O2FieldName[19]="O2_DTXT07";
  $O2FieldName[20]="O2_DTXT08";
  $O2FieldName[21]="O2_DTXT09";
  $O2FieldName[22]="O2_DTXT10";
  $O2FieldName[23]="O2_DSEL01";
  $O2FieldName[24]="O2_DSEL02";
  $O2FieldName[25]="O2_DSEL03";
  $O2FieldName[26]="O2_DSEL04";
  $O2FieldName[27]="O2_DSEL05";
  $O2FieldName[28]="O2_DSEL06";
  $O2FieldName[29]="O2_DSEL07";
  $O2FieldName[30]="O2_DSEL08";
  $O2FieldName[31]="O2_DSEL09";
  $O2FieldName[32]="O2_DSEL10";
  $O2FieldName[33]="O2_DRDO01";
  $O2FieldName[34]="O2_DRDO02";
  $O2FieldName[35]="O2_DRDO03";
  $O2FieldName[36]="O2_DRDO04";
  $O2FieldName[37]="O2_DRDO05";
  $O2FieldName[38]="O2_DRDO06";
  $O2FieldName[39]="O2_DRDO07";
  $O2FieldName[40]="O2_DRDO08";
  $O2FieldName[41]="O2_DRDO09";
  $O2FieldName[42]="O2_DRDO10";
  $O2FieldName[43]="O2_DCHK01";
  $O2FieldName[44]="O2_DCHK02";
  $O2FieldName[45]="O2_DCHK03";
  $O2FieldName[46]="O2_DCHK04";
  $O2FieldName[47]="O2_DCHK05";
  $O2FieldName[48]="O2_DCHK06";
  $O2FieldName[49]="O2_DCHK07";
  $O2FieldName[50]="O2_DCHK08";
  $O2FieldName[51]="O2_DCHK09";
  $O2FieldName[52]="O2_DCHK10";
  $O2FieldName[53]="O2_DFIL01";
  $O2FieldName[54]="O2_DFIL02";
  $O2FieldName[55]="O2_DFIL03";
  $O2FieldName[56]="O2_DFIL04";
  $O2FieldName[57]="O2_DFIL05";
  $O2FieldName[58]="O2_DFIL06";
  $O2FieldName[59]="O2_DFIL07";
  $O2FieldName[60]="O2_DFIL08";
  $O2FieldName[61]="O2_DFIL09";
  $O2FieldName[62]="O2_DFIL10";
  $O2FieldName[63]="O2_MSEL01";
  $O2FieldName[64]="O2_MSEL02";
  $O2FieldName[65]="O2_MSEL03";
  $O2FieldName[66]="O2_MSEL04";
  $O2FieldName[67]="O2_MSEL05";
  $O2FieldName[68]="O2_MSEL06";
  $O2FieldName[69]="O2_MSEL07";
  $O2FieldName[70]="O2_MSEL08";
  $O2FieldName[71]="O2_MSEL09";
  $O2FieldName[72]="O2_MSEL10";
  $O2FieldName[73]="O2_MRDO01";
  $O2FieldName[74]="O2_MRDO02";
  $O2FieldName[75]="O2_MRDO03";
  $O2FieldName[76]="O2_MRDO04";
  $O2FieldName[77]="O2_MRDO05";
  $O2FieldName[78]="O2_MRDO06";
  $O2FieldName[79]="O2_MRDO07";
  $O2FieldName[80]="O2_MRDO08";
  $O2FieldName[81]="O2_MRDO09";
  $O2FieldName[82]="O2_MRDO10";
  $O2FieldName[83]="O2_MCHK01";
  $O2FieldName[84]="O2_MCHK02";
  $O2FieldName[85]="O2_MCHK03";
  $O2FieldName[86]="O2_MCHK04";
  $O2FieldName[87]="O2_MCHK05";
  $O2FieldName[88]="O2_MCHK06";
  $O2FieldName[89]="O2_MCHK07";
  $O2FieldName[90]="O2_MCHK08";
  $O2FieldName[91]="O2_MCHK09";
  $O2FieldName[92]="O2_MCHK10";
  $O2FieldName[93]="ENABLE";
  $O2FieldName[94]="NEWDATE";
  $O2FieldName[95]="EDITDATE";
  $O2FieldName[96]="O2_ETC01";
  $O2FieldName[97]="O2_ETC02";
  $O2FieldName[98]="O2_ETC03";
  $O2FieldName[99]="O2_ETC04";
  $O2FieldName[100]="O2_ETC05";
  $O2FieldName[101]="O2_ETC06";
  $O2FieldName[102]="O2_ETC07";
  $O2FieldName[103]="O2_ETC08";
  $O2FieldName[104]="O2_ETC09";
  $O2FieldName[105]="O2_ETC10";
  $O2FieldName[106]="O2_ETC11";
  $O2FieldName[107]="O2_ETC12";
  $O2FieldName[108]="O2_ETC13";
  $O2FieldName[109]="O2_ETC14";
  $O2FieldName[110]="O2_ETC15";
  $O2FieldName[111]="O2_ETC16";
  $O2FieldName[112]="O2_ETC17";
  $O2FieldName[113]="O2_ETC18";
  $O2FieldName[114]="O2_ETC19";
  $O2FieldName[115]="O2_ETC20";
  $O2FieldName[116]="O2_DVAL11";
  $O2FieldName[117]="O2_DVAL12";
  $O2FieldName[118]="O2_DVAL13";
  $O2FieldName[119]="O2_DVAL14";
  $O2FieldName[120]="O2_DVAL15";
  $O2FieldName[121]="O2_DVAL16";
  $O2FieldName[122]="O2_DVAL17";
  $O2FieldName[123]="O2_DVAL18";
  $O2FieldName[124]="O2_DVAL19";
  $O2FieldName[125]="O2_DVAL20";
  $O2FieldName[126]="O2_DVAL21";
  $O2FieldName[127]="O2_DVAL22";
  $O2FieldName[128]="O2_DVAL23";
  $O2FieldName[129]="O2_DVAL24";
  $O2FieldName[130]="O2_DVAL25";
  $O2FieldName[131]="O2_DVAL26";
  $O2FieldName[132]="O2_DVAL27";
  $O2FieldName[133]="O2_DVAL28";
  $O2FieldName[134]="O2_DVAL29";
  $O2FieldName[135]="O2_DVAL30";
  $O2FieldName[136]="O2_DTXT11";
  $O2FieldName[137]="O2_DTXT12";
  $O2FieldName[138]="O2_DTXT13";
  $O2FieldName[139]="O2_DTXT14";
  $O2FieldName[140]="O2_DTXT15";
  $O2FieldName[141]="O2_DTXT16";
  $O2FieldName[142]="O2_DTXT17";
  $O2FieldName[143]="O2_DTXT18";
  $O2FieldName[144]="O2_DTXT19";
  $O2FieldName[145]="O2_DTXT20";
  $O2FieldName[146]="O2_DTXT21";
  $O2FieldName[147]="O2_DTXT22";
  $O2FieldName[148]="O2_DTXT23";
  $O2FieldName[149]="O2_DTXT24";
  $O2FieldName[150]="O2_DTXT25";
  $O2FieldName[151]="O2_DTXT26";
  $O2FieldName[152]="O2_DTXT27";
  $O2FieldName[153]="O2_DTXT28";
  $O2FieldName[154]="O2_DTXT29";
  $O2FieldName[155]="O2_DTXT30";
  

  $O2FieldAtt[0]="0";
  $O2FieldAtt[1]="0";
  $O2FieldAtt[2]="0";
  $O2FieldAtt[3]="0";
  $O2FieldAtt[4]="0";
  $O2FieldAtt[5]="0";
  $O2FieldAtt[6]="0";
  $O2FieldAtt[7]="0";
  $O2FieldAtt[8]="0";
  $O2FieldAtt[9]="0";
  $O2FieldAtt[10]="0";
  $O2FieldAtt[11]="0";
  $O2FieldAtt[12]="0";
  $O2FieldAtt[13]="0";
  $O2FieldAtt[14]="0";
  $O2FieldAtt[15]="0";
  $O2FieldAtt[16]="0";
  $O2FieldAtt[17]="0";
  $O2FieldAtt[18]="0";
  $O2FieldAtt[19]="0";
  $O2FieldAtt[20]="0";
  $O2FieldAtt[21]="0";
  $O2FieldAtt[22]="0";
  $O2FieldAtt[23]="1";
  $O2FieldAtt[24]="1";
  $O2FieldAtt[25]="1";
  $O2FieldAtt[26]="1";
  $O2FieldAtt[27]="1";
  $O2FieldAtt[28]="1";
  $O2FieldAtt[29]="1";
  $O2FieldAtt[30]="1";
  $O2FieldAtt[31]="1";
  $O2FieldAtt[32]="1";
  $O2FieldAtt[33]="2";
  $O2FieldAtt[34]="2";
  $O2FieldAtt[35]="2";
  $O2FieldAtt[36]="2";
  $O2FieldAtt[37]="2";
  $O2FieldAtt[38]="2";
  $O2FieldAtt[39]="2";
  $O2FieldAtt[40]="2";
  $O2FieldAtt[41]="2";
  $O2FieldAtt[42]="2";
  $O2FieldAtt[43]="3";
  $O2FieldAtt[44]="3";
  $O2FieldAtt[45]="3";
  $O2FieldAtt[46]="3";
  $O2FieldAtt[47]="3";
  $O2FieldAtt[48]="3";
  $O2FieldAtt[49]="3";
  $O2FieldAtt[50]="3";
  $O2FieldAtt[51]="3";
  $O2FieldAtt[52]="3";
  $O2FieldAtt[53]="4";
  $O2FieldAtt[54]="4";
  $O2FieldAtt[55]="4";
  $O2FieldAtt[56]="4";
  $O2FieldAtt[57]="4";
  $O2FieldAtt[58]="4";
  $O2FieldAtt[59]="4";
  $O2FieldAtt[60]="4";
  $O2FieldAtt[61]="4";
  $O2FieldAtt[62]="4";
  $O2FieldAtt[63]="1";
  $O2FieldAtt[64]="1";
  $O2FieldAtt[65]="1";
  $O2FieldAtt[66]="1";
  $O2FieldAtt[67]="1";
  $O2FieldAtt[68]="1";
  $O2FieldAtt[69]="1";
  $O2FieldAtt[70]="1";
  $O2FieldAtt[71]="1";
  $O2FieldAtt[72]="1";
  $O2FieldAtt[73]="2";
  $O2FieldAtt[74]="2";
  $O2FieldAtt[75]="2";
  $O2FieldAtt[76]="2";
  $O2FieldAtt[77]="2";
  $O2FieldAtt[78]="2";
  $O2FieldAtt[79]="2";
  $O2FieldAtt[80]="2";
  $O2FieldAtt[81]="2";
  $O2FieldAtt[82]="2";
  $O2FieldAtt[83]="3";
  $O2FieldAtt[84]="3";
  $O2FieldAtt[85]="3";
  $O2FieldAtt[86]="3";
  $O2FieldAtt[87]="3";
  $O2FieldAtt[88]="3";
  $O2FieldAtt[89]="3";
  $O2FieldAtt[90]="3";
  $O2FieldAtt[91]="3";
  $O2FieldAtt[92]="3";
  $O2FieldAtt[93]="2";
  $O2FieldAtt[94]="0";
  $O2FieldAtt[95]="0";
  $O2FieldAtt[96]="0";
  $O2FieldAtt[97]="0";
  $O2FieldAtt[98]="0";
  $O2FieldAtt[99]="0";
  $O2FieldAtt[100]="0";
  $O2FieldAtt[101]="0";
  $O2FieldAtt[102]="0";
  $O2FieldAtt[103]="0";
  $O2FieldAtt[104]="0";
  $O2FieldAtt[105]="0";
  $O2FieldAtt[106]="0";
  $O2FieldAtt[107]="0";
  $O2FieldAtt[108]="0";
  $O2FieldAtt[109]="0";
  $O2FieldAtt[110]="0";
  $O2FieldAtt[111]="0";
  $O2FieldAtt[112]="0";
  $O2FieldAtt[113]="0";
  $O2FieldAtt[114]="0";
  $O2FieldAtt[115]="0";
  $O2FieldAtt[116]="0";
  $O2FieldAtt[117]="0";
  $O2FieldAtt[118]="0";
  $O2FieldAtt[119]="0";
  $O2FieldAtt[120]="0";
  $O2FieldAtt[121]="0";
  $O2FieldAtt[122]="0";
  $O2FieldAtt[123]="0";
  $O2FieldAtt[124]="0";
  $O2FieldAtt[125]="0";
  $O2FieldAtt[126]="0";
  $O2FieldAtt[127]="0";
  $O2FieldAtt[128]="0";
  $O2FieldAtt[129]="0";
  $O2FieldAtt[130]="0";
  $O2FieldAtt[131]="0";
  $O2FieldAtt[132]="0";
  $O2FieldAtt[133]="0";
  $O2FieldAtt[134]="0";
  $O2FieldAtt[135]="0";
  $O2FieldAtt[136]="0";
  $O2FieldAtt[137]="0";
  $O2FieldAtt[138]="0";
  $O2FieldAtt[139]="0";
  $O2FieldAtt[140]="0";
  $O2FieldAtt[141]="0";
  $O2FieldAtt[142]="0";
  $O2FieldAtt[143]="0";
  $O2FieldAtt[144]="0";
  $O2FieldAtt[145]="0";
  $O2FieldAtt[146]="0";
  $O2FieldAtt[147]="0";
  $O2FieldAtt[148]="0";
  $O2FieldAtt[149]="0";
  $O2FieldAtt[150]="0";
  $O2FieldAtt[151]="0";
  $O2FieldAtt[152]="0";
  $O2FieldAtt[153]="0";
  $O2FieldAtt[154]="0";
  $O2FieldAtt[155]="0";
  

  $O2FieldMax=155;

	for ($i=0; $i<=$O2FieldMax; $i=$i+1) {
		if ($O2FieldAtt[$i]==4) {
			if ($item[$O2FieldName[$i]]=="") {
				$str=str_replace("[".$O2FieldName[$i]."]",$filepath1."s.gif",$str);
				$str=str_replace("[D-".$O2FieldName[$i]."]",$filepath1."s.gif",$str);
			} 
			if (strstr($item[$O2FieldName[$i]], "s.gif")==true){
				$str=DispParamNone($str, $Param."-".$O2FieldName[$i]);
			} else {
				$str=DispParam($str, $Param."-".$O2FieldName[$i]);
			} 
		} 
		// HTMLエスケープ処理（一覧表示系）
		$str=str_replace("[".$Param."-".$O2FieldName[$i]."]",htmlspecialchars($item[$O2FieldName[$i]]),$str);
		$str=str_replace("[D-".$Param."-".$O2FieldName[$i]."]",str_replace("\r\n","<br>",str_replace($O2FieldName[$i].":","",htmlspecialchars($item[$O2FieldName[$i]]))),$str);
		if (is_numeric($item[$O2FieldName[$i]])) {
			$str=str_replace("[N-".$Param."-".$O2FieldName[$i]."]",number_format($item[$O2FieldName[$i]],0),$str);
		} else {
			$str=str_replace("[N-".$Param."-".$O2FieldName[$i]."]","",$str);
		} 
		if ($item[$O2FieldName[$i]]==""){
			$str=DispParamNone($str, $Param."-".$O2FieldName[$i]);
		} else {
			$str=DispParam($str, $Param."-".$O2FieldName[$i]);
		} 
	}

	// 2021.01.18 yamamoto 平均評価
	$eval_avg = CalcEvalAvg($item['MID']);
	$str=str_replace("[D-".$Param."_EVAL_AVG]",$eval_avg,$str);

	return $str;

}

/*
//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function MessageList2($mid, $str)
{
	eval(globals());

	$StrSQL="SELECT * FROM DAT_M2 where MID='".$mid."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		$item = mysqli_fetch_assoc($rs);

		$str=DispM2($item, $str);
		$str=str_replace("[D-ID]", $item['ID'], $str);

		$StrSQL="SELECT * FROM DAT_MATCH where HID='".$_SESSION['HID']."' and MID='".$mid."' order by POINT desc LIMIT 0,1;";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);
		if ($item2['POINT']!="0"){
			$str=str_replace("[D-POINT]", $item2['POINT'], $str);
			$str=DispParam($str, "POINT");
		} else {
			$str=DispParamNone($str, "POINT");
		} 

		$StrSQL="SELECT ID FROM DAT_CHAT where AID='".$_SESSION['HID']."-".$mid."' and RID<>'".$_SESSION['HID']."' and (ETC01 is null or ETC01='');";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2=mysqli_num_rows($rs2);
		if($item2>0){
			$str=DispParam($str, "MIDOKU");
		} else {
			$str=DispParamNone($str, "MIDOKU");
		}

//		$str=str_replace("[MPIC]", MemberPic($mid), $str);

//		$str=str_replace("[ICONI]", IconI($_SESSION['HID'], $mid), $str);
//		$str=str_replace("[ICONO]", IconO2($_SESSION['HID'], $mid), $str);

		$str=str_replace("[HID]", $_SESSION['HID'], $str);
		$str=str_replace("[MID]", $mid, $str);

	}
	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function MessageListH($hid, $str)
{
	eval(globals());

	$StrSQL="SELECT * FROM DAT_HOSPITAL where HID='".$hid."'";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2=mysqli_num_rows($rs2);
	if($item2>0){
//		$item2 = mysqli_fetch_assoc($rs2);
//		$str=Hospital($item2, $str);
		$StrSQL="SELECT * FROM DAT_MATCH inner join DAT_JOB on DAT_JOB.JID=DAT_MATCH.JID and DAT_MATCH.MID='".$_SESSION['MID']."' and DAT_MATCH.HID='".$hid."' order by cast(DAT_MATCH.POINT as signed) desc;";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);
		$jid=$item2['JID'];
		if ($item2['POINT']!="0"){
			$str=str_replace("[D-POINT]", $item2['POINT'], $str);
			$str=DispParam($str, "POINT");
		} else {
			$str=DispParamNone($str, "POINT");
		} 

		$StrSQL="SELECT ID FROM DAT_CHAT where AID='".$hid."-".$_SESSION['MID']."' and RID<>'".$_SESSION['MID']."' and (ETC01 is null or ETC01='');";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2=mysqli_num_rows($rs2);
		if($item2>0){
			$str=DispParam($str, "MIDOKU");
		} else {
			$str=DispParamNone($str, "MIDOKU");
		}

//		$jid=$item2['JID'];

		$StrSQL="SELECT * FROM DAT_JOB where JID='".$jid."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$str=str_replace("[D-ID]", $item['ID'], $str);

		$StrSQL="SELECT * FROM DAT_HOSPITAL where HID='".$hid."'";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);
		$str=Hospital($item2, $str);

		$str=str_replace("[HPIC]", HospitalPic($hid), $str);
		$str=str_replace("[ICONI]", IconI($hid, $_SESSION['MID']), $str);
		$str=str_replace("[ICONO]", IconO($jid, $_SESSION['MID']), $str);

		$str=str_replace("[HID]", $hid, $str);
		$str=str_replace("[MID]", $_SESSION['MID'], $str);
	}

	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function JobList($item, $str)
{
	eval(globals());

	if($item['NENSYU1']==""){
		$str=str_replace("年収:[D-JOB-NENSYU1]～[D-JOB-NENSYU2]万円", "時給:[D-JOB-JIKYU1]～[D-JOB-JIKYU2]円", $str);
	}
	$str=str_replace("[ICONI]", IconI($item['HID'], $_SESSION['MID']), $str);
	$str=str_replace("[ICONO]", IconO($item['JID'], $_SESSION['MID']), $str);

	$str=Job($item, $str);

	$StrSQL="SELECT * FROM DAT_HOSPITAL where HID='".$item['HID']."'";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2 = mysqli_fetch_assoc($rs2);
	$str=Hospital($item2, $str);

	$StrSQL="SELECT * FROM DAT_MATCH where MID='".$_SESSION['MID']."' and JID='".$item['JID']."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2 = mysqli_fetch_assoc($rs2);
	if ($item2['POINT']!="0"){
		$str=str_replace("[D-POINT]", $item2['POINT'], $str);
		$str=DispParam($str, "POINT");
	} else {
		$str=DispParamNone($str, "POINT");
	} 

	$str=str_replace("[HPIC]", HospitalPic($item['HID']), $str);

	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function MemberList($item, $str)
{
	eval(globals());

	if($item['KOYOU']=="KOYOU:パート"){
		$str=str_replace("年収:[D-MEMBER-NENSYU]万円", "時給:[D-MEMBER-JIKYU]円", $str);
	}
	if($item['KOYOU']=="KOYOU:新卒"){
		$str=str_replace("臨床年数：[D-MEMBER-RNENSU]", "卒業予定年:[D-MEMBER-SOTSUNEN]年", $str);
	}
	if($item['GYOUSYU']!="GYOUSYU:動物病院"){
		$str=str_replace("臨床年数：[D-MEMBER-RNENSU]", "社会人年数:[D-MEMBER-SNENSU]年", $str);
	}

	$str=str_replace("[ICONI1]", IconI1($_SESSION['HID'], $item['MID']), $str);
	$str=str_replace("[ICONI2]", IconI2($_SESSION['HID'], $item['MID']), $str);

	$str=Member($item, $str);

	$StrSQL="SELECT JID FROM DAT_JOB where HID='".$_SESSION['HID']."' and KOYOU='".$item['KOYOU']."';";
	$rs3=mysqli_query(ConnDB(),$StrSQL);
	$item3 = mysqli_fetch_assoc($rs3);
	$StrSQL="SELECT * FROM DAT_MATCH where JID='".$item3['JID']."' and MID='".$item["MID"]."' order by POINT desc LIMIT 0,1;";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2 = mysqli_fetch_assoc($rs2);
	if ($item2['POINT']!="0"){
		$str=str_replace("[D-POINT]", $item2['POINT'], $str);
		$str=DispParam($str, "POINT");
	} else {
		$str=DispParamNone($str, "POINT");
	} 

//	$str=str_replace("[MPIC]", MemberPic($item['MID']), $str);


	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function Member($item, $str)
{
	eval(globals());

	//テーブル名の指定
	$ParamName="MEMBER";
	$MFieldMax=80;

  $MFieldName[0]="ID";
  $MFieldName[1]="MID";
  $MFieldName[2]="NAME1";
  $MFieldName[3]="NAME2";
  $MFieldName[4]="KANA1";
  $MFieldName[5]="KANA2";
  $MFieldName[6]="PREF";
  $MFieldName[7]="SEX";
  $MFieldName[8]="BIRTHY";
  $MFieldName[9]="BIRTHM";
  $MFieldName[10]="BIRTHD";
  $MFieldName[11]="TEL";
  $MFieldName[12]="EMAIL";
  $MFieldName[13]="PASS";
  $MFieldName[14]="JOKYO";
  $MFieldName[15]="SNENSU";
  $MFieldName[16]="RNENSU";
  $MFieldName[17]="KOYOU";
  $MFieldName[18]="GYOUSYU";
  $MFieldName[19]="AGE";
  $MFieldName[20]="DAIGAKU";
  $MFieldName[21]="SOTSUNEN";
  $MFieldName[22]="PICPTN";
  $MFieldName[23]="KT1";
  $MFieldName[24]="KT2";
  $MFieldName[25]="KT3S";
  $MFieldName[26]="KT3E";
  $MFieldName[27]="KT4S";
  $MFieldName[28]="KT4E";
  $MFieldName[29]="KT5";
  $MFieldName[30]="KT6";
  $MFieldName[31]="NENSYU";
  $MFieldName[32]="JIKYU";
  $MFieldName[33]="KINMUCHI";
  $MFieldName[34]="KYUUKA";
  $MFieldName[35]="TSUUKIN";
  $MFieldName[36]="SHIEN";
  $MFieldName[37]="TAIGU";
  $MFieldName[38]="FUKURI";
  $MFieldName[39]="KANKYO";
  $MFieldName[40]="SYU1";
  $MFieldName[41]="SYU2";
  $MFieldName[42]="SYU3";
  $MFieldName[43]="SYU4";
  $MFieldName[44]="JITSU1";
  $MFieldName[45]="JITSU2";
  $MFieldName[46]="JITSU3";
  $MFieldName[47]="JITSU4";
  $MFieldName[48]="ENG";
  $MFieldName[49]="SYUE";
  $MFieldName[50]="JITSUE";
  $MFieldName[51]="SEIKAKU";
  $MFieldName[52]="SEIKAKUE";
  $MFieldName[53]="PR";
  $MFieldName[54]="LINEID";
  $MFieldName[55]="LINEPASS";
  $MFieldName[56]="IINESU";
  $MFieldName[57]="FAVSU";
  $MFieldName[58]="ENABLE";
  $MFieldName[59]="NEWDATE";
  $MFieldName[60]="EDITDATE";
  $MFieldName[61]="ETC01";
  $MFieldName[62]="ETC02";
  $MFieldName[63]="ETC03";
  $MFieldName[64]="ETC04";
  $MFieldName[65]="ETC05";
  $MFieldName[66]="ETC06";
  $MFieldName[67]="ETC07";
  $MFieldName[68]="ETC08";
  $MFieldName[69]="ETC09";
  $MFieldName[70]="ETC10";
  $MFieldName[71]="ETC11";
  $MFieldName[72]="ETC12";
  $MFieldName[73]="ETC13";
  $MFieldName[74]="ETC14";
  $MFieldName[75]="ETC15";
  $MFieldName[76]="ETC16";
  $MFieldName[77]="ETC17";
  $MFieldName[78]="ETC18";
  $MFieldName[79]="ETC19";
  $MFieldName[80]="ETC20";

  $MFieldAtt[0]="0";
  $MFieldAtt[1]="0";
  $MFieldAtt[2]="0";
  $MFieldAtt[3]="0";
  $MFieldAtt[4]="0";
  $MFieldAtt[5]="0";
  $MFieldAtt[6]="1";
  $MFieldAtt[7]="2";
  $MFieldAtt[8]="1";
  $MFieldAtt[9]="1";
  $MFieldAtt[10]="1";
  $MFieldAtt[11]="0";
  $MFieldAtt[12]="0";
  $MFieldAtt[13]="0";
  $MFieldAtt[14]="2";
  $MFieldAtt[15]="1";
  $MFieldAtt[16]="1";
  $MFieldAtt[17]="2";
  $MFieldAtt[18]="2";
  $MFieldAtt[19]="0";
  $MFieldAtt[20]="1";
  $MFieldAtt[21]="1";
  $MFieldAtt[22]="2";
  $MFieldAtt[23]="2";
  $MFieldAtt[24]="2";
  $MFieldAtt[25]="1";
  $MFieldAtt[26]="1";
  $MFieldAtt[27]="1";
  $MFieldAtt[28]="1";
  $MFieldAtt[29]="2";
  $MFieldAtt[30]="2";
  $MFieldAtt[31]="1";
  $MFieldAtt[32]="1";
  $MFieldAtt[33]="3";
  $MFieldAtt[34]="3";
  $MFieldAtt[35]="3";
  $MFieldAtt[36]="3";
  $MFieldAtt[37]="3";
  $MFieldAtt[38]="3";
  $MFieldAtt[39]="2";
  $MFieldAtt[40]="3";
  $MFieldAtt[41]="3";
  $MFieldAtt[42]="3";
  $MFieldAtt[43]="3";
  $MFieldAtt[44]="3";
  $MFieldAtt[45]="3";
  $MFieldAtt[46]="3";
  $MFieldAtt[47]="3";
  $MFieldAtt[48]="2";
  $MFieldAtt[49]="0";
  $MFieldAtt[50]="0";
  $MFieldAtt[51]="3";
  $MFieldAtt[52]="0";
  $MFieldAtt[53]="0";
  $MFieldAtt[54]="0";
  $MFieldAtt[55]="0";
  $MFieldAtt[56]="0";
  $MFieldAtt[57]="0";
  $MFieldAtt[58]="2";
  $MFieldAtt[59]="0";
  $MFieldAtt[60]="0";
  $MFieldAtt[61]="0";
  $MFieldAtt[62]="0";
  $MFieldAtt[63]="0";
  $MFieldAtt[64]="0";
  $MFieldAtt[65]="0";
  $MFieldAtt[66]="0";
  $MFieldAtt[67]="0";
  $MFieldAtt[68]="0";
  $MFieldAtt[69]="0";
  $MFieldAtt[70]="0";
  $MFieldAtt[71]="0";
  $MFieldAtt[72]="0";
  $MFieldAtt[73]="0";
  $MFieldAtt[74]="0";
  $MFieldAtt[75]="0";
  $MFieldAtt[76]="0";
  $MFieldAtt[77]="0";
  $MFieldAtt[78]="0";
  $MFieldAtt[79]="0";
  $MFieldAtt[80]="0";

	for ($i=0; $i<=$MFieldMax; $i=$i+1) {
		if ($MFieldAtt[$i]==4) {
			if ($item[$MFieldName[$i]]=="") {
				$str=str_replace("[".$ParamName."-".$MFieldName[$i]."]",$filepath1."s.gif",$str);
				$str=str_replace("[D-".$ParamName."-".$MFieldName[$i]."]",$filepath1."s.gif",$str);
			} 
		} 
		// HTMLエスケープ処理（一覧表示系）
		$str=str_replace("[".$ParamName."-".$MFieldName[$i]."]",htmlspecialchars($item[$MFieldName[$i]]),$str);
		$str=str_replace("[D-".$ParamName."-".$MFieldName[$i]."]",str_replace("\n","<br>",str_replace("\r\n","<br>",str_replace($MFieldName[$i].":","",htmlspecialchars($item[$MFieldName[$i]])))),$str);
		if (is_numeric($item[$MFieldName[$i]])) {
			$str=str_replace("[N-".$ParamName."-".$MFieldName[$i]."]",number_format($item[$MFieldName[$i]],0),$str);
		} else {
			$str=str_replace("[N-".$ParamName."-".$MFieldName[$i]."]","",$str);
		} 
		if ($item[$MFieldName[$i]]==""){
			$str=DispParamNone($str, $ParamName."-".$MFieldName[$i]);
		} else {
			$str=DispParam($str, $ParamName."-".$MFieldName[$i]);
		} 
	}

	$a=explode("\t", str_replace("KINMUCHI:", "", trim($item['KINMUCHI'])));
	if(count($a)<=1){
		$str=str_replace("[D-".$ParamName."-KINMUCHI1]",str_replace("KINMUCHI:", "", $item['KINMUCHI']),$str);
	} else {
		$str=str_replace("[D-".$ParamName."-KINMUCHI1]",$a[0]."、他",$str);
	}

	if($item['SEX']=='SEX:男性'){
		$str=str_replace("[D-MEMBER-PIC]","/common/images/img-user-m1.jpg",$str);
	}
	if($item['SEX']=='SEX:女性'){
		$str=str_replace("[D-MEMBER-PIC]","/common/images/img-user-w1.jpg",$str);
	}

	$y1=intval((date('Y')-str_replace("BIRTHY:", "", $item['BIRTHY']))/10)*10;
	$d=date('Y')-str_replace("BIRTHY:", "", $item['BIRTHY'])-$y1;
	if($d>=5){
		$y2="後半";
	} else {
		$y2="前半";
	}
	$str=str_replace("[D-MEMBER-AGES]",$y1."代".$y2,$str);

	if($item['POINT']!=""){
		$str=str_replace("[D-POINT]", $item['POINT'], $str);
		$str=DispParam($str, "POINT");
	} else {
		$StrSQL="SELECT JID FROM DAT_JOB where HID='".$_SESSION['HID']."' and KOYOU='".$item['KOYOU']."';";
		$rs3=mysqli_query(ConnDB(),$StrSQL);
		$item3 = mysqli_fetch_assoc($rs3);
		$StrSQL="SELECT * FROM DAT_MATCH where JID='".$item3['JID']."' and MID='".$item["MID"]."' order by POINT desc LIMIT 0,1;";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);
		if ($item2['POINT']!="0"){
			$str=str_replace("[D-POINT]", $item2['POINT'], $str);
			$str=DispParam($str, "POINT");
		} else {
			$str=DispParamNone($str, "POINT");
		} 
	}

	$str=str_replace("[D-MEMBER-PR-LINE]", mb_substr($item['PR'], 0, 65, "UTF-8"), $str);

	$str=str_replace("[MPIC]", MemberPic($item['MID']), $str);

	$str=str_replace("[ICONI1]", IconI1D($_SESSION['HID'], $item['MID']), $str);
	$str=str_replace("[ICONI2]", IconI2D($_SESSION['HID'], $item['MID']), $str);

	$StrSQL="SELECT ID FROM DAT_IINE where HID='".$_SESSION['HID']."' and MID='".$item['MID']."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2=mysqli_num_rows($rs2);
	if($item2>0){
		$str=DispParam($str, "IINE-BTN-ON");
		$str=DispParamNone($str, "IINE-BTN-OFF");
	} else {
		$str=DispParamNone($str, "IINE-BTN-ON");
		$str=DispParam($str, "IINE-BTN-OFF");
	}

	$StrSQL="SELECT ID FROM DAT_SCOUT where HID='".$_SESSION['HID']."' and MID='".$item['MID']."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2=mysqli_num_rows($rs2);
	if($item2>0){
		$str=DispParam($str, "SCOUT-BTN-ON");
		$str=DispParamNone($str, "SCOUT-BTN-OFF");
	} else {
		$str=DispParamNone($str, "SCOUT-BTN-ON");
		$str=DispParam($str, "SCOUT-BTN-OFF");
	}

	$str=str_replace("[HID]", $_SESSION['HID'], $str);
	$str=str_replace("[MID]", $item['MID'], $str);

	return $str;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function Hospital($item, $str)
{
	eval(globals());

	//テーブル名の指定
	$ParamName="HOSPITAL";
	$HFieldMax=47;

  $HFieldName[0]="ID";
  $HFieldName[1]="HID";
  $HFieldName[2]="HNAME";
  $HFieldName[3]="POSTAGE";
  $HFieldName[4]="PREF";
  $HFieldName[5]="ADDRESS";
  $HFieldName[6]="TEL";
  $HFieldName[7]="URL";
  $HFieldName[8]="PR";
  $HFieldName[9]="RINEN";
  $HFieldName[10]="OPEN";
  $HFieldName[11]="SU";
  $HFieldName[12]="JSU";
  $HFieldName[13]="PIC01";
  $HFieldName[14]="PIC02";
  $HFieldName[15]="PIC03";
  $HFieldName[16]="PIC04";
  $HFieldName[17]="PIC05";
  $HFieldName[18]="PICPTN01";
  $HFieldName[19]="PICPTN02";
  $HFieldName[20]="PICPTN03";
  $HFieldName[21]="PICPTN04";
  $HFieldName[22]="PICPTN05";
  $HFieldName[23]="IINESU";
  $HFieldName[24]="PLAN";
  $HFieldName[25]="ENABLE";
  $HFieldName[26]="NEWDATE";
  $HFieldName[27]="EDITDATE";
  $HFieldName[28]="ETC01";	// メールアドレス
  $HFieldName[29]="ETC02";	// パスワード
  $HFieldName[30]="ETC03";
  $HFieldName[31]="ETC04";
  $HFieldName[32]="ETC05";
  $HFieldName[33]="ETC06";
  $HFieldName[34]="ETC07";
  $HFieldName[35]="ETC08";
  $HFieldName[36]="ETC09";
  $HFieldName[37]="ETC10";
  $HFieldName[38]="ETC11";
  $HFieldName[39]="ETC12";
  $HFieldName[40]="ETC13";
  $HFieldName[41]="ETC14";
  $HFieldName[42]="ETC15";
  $HFieldName[43]="ETC16";
  $HFieldName[44]="ETC17";
  $HFieldName[45]="ETC18";
  $HFieldName[46]="ETC19";
  $HFieldName[47]="ETC20";

  $HFieldAtt[0]="0";
  $HFieldAtt[1]="0";
  $HFieldAtt[2]="0";
  $HFieldAtt[3]="0";
  $HFieldAtt[4]="1";
  $HFieldAtt[5]="0";
  $HFieldAtt[6]="0";
  $HFieldAtt[7]="0";
  $HFieldAtt[8]="0";
  $HFieldAtt[9]="0";
  $HFieldAtt[10]="0";
  $HFieldAtt[11]="1";
  $HFieldAtt[12]="1";
  $HFieldAtt[13]="4";
  $HFieldAtt[14]="4";
  $HFieldAtt[15]="4";
  $HFieldAtt[16]="4";
  $HFieldAtt[17]="4";
  $HFieldAtt[18]="2";
  $HFieldAtt[19]="2";
  $HFieldAtt[20]="2";
  $HFieldAtt[21]="2";
  $HFieldAtt[22]="2";
  $HFieldAtt[23]="0";
  $HFieldAtt[24]="2";
  $HFieldAtt[25]="2";
  $HFieldAtt[26]="0";
  $HFieldAtt[27]="0";
  $HFieldAtt[28]="0";
  $HFieldAtt[29]="0";
  $HFieldAtt[30]="0";
  $HFieldAtt[31]="0";
  $HFieldAtt[32]="0";
  $HFieldAtt[33]="0";
  $HFieldAtt[34]="0";
  $HFieldAtt[35]="0";
  $HFieldAtt[36]="0";
  $HFieldAtt[37]="0";
  $HFieldAtt[38]="0";
  $HFieldAtt[39]="0";
  $HFieldAtt[40]="0";
  $HFieldAtt[41]="0";
  $HFieldAtt[42]="0";
  $HFieldAtt[43]="0";
  $HFieldAtt[44]="0";
  $HFieldAtt[45]="0";
  $HFieldAtt[46]="0";
  $HFieldAtt[47]="0";

	for ($i=0; $i<=$HFieldMax; $i=$i+1) {
		if ($HFieldAtt[$i]==4) {
			if ($item[$HFieldName[$i]]=="") {
				$str=str_replace("[".$ParamName."-".$HFieldName[$i]."]",$filepath1."s.gif",$str);
				$str=str_replace("[D-".$ParamName."-".$HFieldName[$i]."]",$filepath1."s.gif",$str);
			} 
		} 
		// HTMLエスケープ処理（一覧表示系）
		$str=str_replace("[".$ParamName."-".$HFieldName[$i]."]",htmlspecialchars($item[$HFieldName[$i]]),$str);
		$str=str_replace("[D-".$ParamName."-".$HFieldName[$i]."]",str_replace("\n","<br>",str_replace("\r\n","<br>",str_replace($HFieldName[$i].":","",htmlspecialchars($item[$HFieldName[$i]])))),$str);
		if (is_numeric($item[$HFieldName[$i]])) {
			$str=str_replace("[N-".$ParamName."-".$HFieldName[$i]."]",number_format($item[$HFieldName[$i]],0),$str);
		} else {
			$str=str_replace("[N-".$ParamName."-".$HFieldName[$i]."]","",$str);
		} 
		if ($item[$HFieldName[$i]]==""){
			$str=DispParamNone($str, $ParamName."-".$HFieldName[$i]);
		} else {
			$str=DispParam($str, $ParamName."-".$HFieldName[$i]);
		} 
	}

	return $str;
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function Job($item, $str)
{
	eval(globals());

	//テーブル名の指定
	$ParamName="JOB";
	$JFieldMax=65;

  $JFieldName[0]="ID";
  $JFieldName[1]="JID";
  $JFieldName[2]="HID";
  $JFieldName[3]="KTITLE";
  $JFieldName[4]="KNAIYO";
  $JFieldName[5]="RNENSU";
  $JFieldName[6]="KOYOU";
  $JFieldName[7]="GYOUSYU";
  $JFieldName[8]="DAIGAKU";
  $JFieldName[9]="SOTSUNEN";
  $JFieldName[10]="KT1";
  $JFieldName[11]="KT2";
  $JFieldName[12]="KT3S";
  $JFieldName[13]="KT3E";
  $JFieldName[14]="KT4S";
  $JFieldName[15]="KT4E";
  $JFieldName[16]="KT5";
  $JFieldName[17]="KT6";
  $JFieldName[18]="KTIME";
  $JFieldName[19]="NENSYU1";
  $JFieldName[20]="NENSYU2";
  $JFieldName[21]="JIKYU1";
  $JFieldName[22]="JIKYU2";
  $JFieldName[23]="KINMUCHI";
  $JFieldName[24]="KYUUKA";
  $JFieldName[25]="TSUUKIN";
  $JFieldName[26]="SHIEN";
  $JFieldName[27]="TAIGU";
  $JFieldName[28]="FUKURI";
  $JFieldName[29]="ETC";
  $JFieldName[30]="SYU1";
  $JFieldName[31]="SYU2";
  $JFieldName[32]="SYU3";
  $JFieldName[33]="SYU4";
  $JFieldName[34]="JITSU1";
  $JFieldName[35]="JITSU2";
  $JFieldName[36]="JITSU3";
  $JFieldName[37]="JITSU4";
  $JFieldName[38]="SEIKAKU";
  $JFieldName[39]="ENG";
  $JFieldName[40]="SYUE";
  $JFieldName[41]="JITSUE";
  $JFieldName[42]="FAVSU";
  $JFieldName[43]="ENABLE";
  $JFieldName[44]="NEWDATE";
  $JFieldName[45]="EDITDATE";
  $JFieldName[46]="ETC01";
  $JFieldName[47]="ETC02";
  $JFieldName[48]="ETC03";
  $JFieldName[49]="ETC04";
  $JFieldName[50]="ETC05";
  $JFieldName[51]="ETC06";
  $JFieldName[52]="ETC07";
  $JFieldName[53]="ETC08";
  $JFieldName[54]="ETC09";
  $JFieldName[55]="ETC10";
  $JFieldName[56]="ETC11";
  $JFieldName[57]="ETC12";
  $JFieldName[58]="ETC13";
  $JFieldName[59]="ETC14";
  $JFieldName[60]="ETC15";
  $JFieldName[61]="ETC16";
  $JFieldName[62]="ETC17";
  $JFieldName[63]="ETC18";
  $JFieldName[64]="ETC19";
  $JFieldName[65]="ETC20";

  $JFieldAtt[0]="0";
  $JFieldAtt[1]="0";
  $JFieldAtt[2]="0";
  $JFieldAtt[3]="0";
  $JFieldAtt[4]="0";
  $JFieldAtt[5]="3";
  $JFieldAtt[6]="2";
  $JFieldAtt[7]="3";
  $JFieldAtt[8]="3";
  $JFieldAtt[9]="3";
  $JFieldAtt[10]="3";
  $JFieldAtt[11]="3";
  $JFieldAtt[12]="1";
  $JFieldAtt[13]="1";
  $JFieldAtt[14]="1";
  $JFieldAtt[15]="1";
  $JFieldAtt[16]="2";
  $JFieldAtt[17]="2";
  $JFieldAtt[18]="0";
  $JFieldAtt[19]="1";
  $JFieldAtt[20]="1";
  $JFieldAtt[21]="1";
  $JFieldAtt[22]="1";
  $JFieldAtt[23]="3";
  $JFieldAtt[24]="3";
  $JFieldAtt[25]="3";
  $JFieldAtt[26]="3";
  $JFieldAtt[27]="3";
  $JFieldAtt[28]="3";
  $JFieldAtt[29]="0";
  $JFieldAtt[30]="3";
  $JFieldAtt[31]="3";
  $JFieldAtt[32]="3";
  $JFieldAtt[33]="3";
  $JFieldAtt[34]="3";
  $JFieldAtt[35]="3";
  $JFieldAtt[36]="3";
  $JFieldAtt[37]="3";
  $JFieldAtt[38]="3";
  $JFieldAtt[39]="2";
  $JFieldAtt[40]="0";
  $JFieldAtt[41]="0";
  $JFieldAtt[42]="0";
  $JFieldAtt[43]="2";
  $JFieldAtt[44]="0";
  $JFieldAtt[45]="0";
  $JFieldAtt[46]="0";
  $JFieldAtt[47]="0";
  $JFieldAtt[48]="0";
  $JFieldAtt[49]="0";
  $JFieldAtt[50]="0";
  $JFieldAtt[51]="0";
  $JFieldAtt[52]="0";
  $JFieldAtt[53]="0";
  $JFieldAtt[54]="0";
  $JFieldAtt[55]="0";
  $JFieldAtt[56]="0";
  $JFieldAtt[57]="0";
  $JFieldAtt[58]="0";
  $JFieldAtt[59]="0";
  $JFieldAtt[60]="0";
  $JFieldAtt[61]="0";
  $JFieldAtt[62]="0";
  $JFieldAtt[63]="0";
  $JFieldAtt[64]="0";
  $JFieldAtt[65]="0";

	for ($i=0; $i<=$JFieldMax; $i=$i+1) {
		if ($JFieldAtt[$i]==4) {
			if ($item[$JFieldName[$i]]=="") {
				$str=str_replace("[".$ParamName."-".$JFieldName[$i]."]",$filepath1."s.gif",$str);
				$str=str_replace("[D-".$ParamName."-".$JFieldName[$i]."]",$filepath1."s.gif",$str);
			} 
		} 
		// HTMLエスケープ処理（一覧表示系）
		$str=str_replace("[".$ParamName."-".$JFieldName[$i]."]",htmlspecialchars($item[$JFieldName[$i]]),$str);
		$str=str_replace("[D-".$ParamName."-".$JFieldName[$i]."]",str_replace("\n","<br>",str_replace("\r\n","<br>",str_replace($JFieldName[$i].":","",htmlspecialchars($item[$JFieldName[$i]])))),$str);
		if (is_numeric($item[$JFieldName[$i]])) {
			$str=str_replace("[N-".$ParamName."-".$JFieldName[$i]."]",number_format($item[$JFieldName[$i]],0),$str);
		} else {
			$str=str_replace("[N-".$ParamName."-".$JFieldName[$i]."]","",$str);
		} 
		if ($item[$JFieldName[$i]]==""){
			$str=DispParamNone($str, $ParamName."-".$JFieldName[$i]);
		} else {
			$str=DispParam($str, $ParamName."-".$JFieldName[$i]);
		} 
	}

	$a=explode("\t", str_replace("KINMUCHI:", "", trim($item['KINMUCHI'])));
	if(count($a)<=1){
		$str=str_replace("[D-".$ParamName."-KINMUCHI1]",str_replace("KINMUCHI:", "", $item['KINMUCHI']),$str);
	} else {
		$str=str_replace("[D-".$ParamName."-KINMUCHI1]",$a[0]."、他",$str);
	}

	$StrSQL="SELECT * FROM DAT_HOSPITAL where HID='".$item['HID']."'";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2 = mysqli_fetch_assoc($rs2);
	$str=Hospital($item2, $str);

	$str=str_replace("[HPIC]", HospitalPic($item['HID']), $str);

	$p=0;
	if(strstr($item2['PIC01'], "s.gif")==false && $item2['PIC01']!=""){
		$pic[$p]=$item2['PIC01'];
		$p++;
	}
	if(strstr($item2['PIC02'], "s.gif")==false && $item2['PIC02']!=""){
		$pic[$p]=$item2['PIC02'];
		$p++;
	}
	if(strstr($item2['PIC03'], "s.gif")==false && $item2['PIC03']!=""){
		$pic[$p]=$item2['PIC03'];
		$p++;
	}
	if(strstr($item2['PIC04'], "s.gif")==false && $item2['PIC04']!=""){
		$pic[$p]=$item2['PIC04'];
		$p++;
	}
	if(strstr($item2['PIC05'], "s.gif")==false && $item2['PIC05']!=""){
		$pic[$p]=$item2['PIC05'];
		$p++;
	}
	if(strstr($item2['PIC06'], "s.gif")==false && $item2['PIC06']!=""){
		$pic[$p]=$item2['PIC06'];
		$p++;
	}
	$cols=explode("\t",$item2['PICPTN01']);
	for ($i=0; $i<count($cols); $i++) {
		switch ($cols[$i]){
		case "PICPTN01:パターン１":
			$pic[$p]="/common/images/img-p01.jpg";
			$p++;
			break;
		case "PICPTN01:パターン２":
			$pic[$p]="/common/images/img-p02.jpg";
			$p++;
			break;
		case "PICPTN01:パターン３":
			$pic[$p]="/common/images/img-p03.jpg";
			$p++;
			break;
		case "PICPTN01:パターン４":
			$pic[$p]="/common/images/img-p04.jpg";
			$p++;
			break;
		case "PICPTN01:パターン５":
			$pic[$p]="/common/images/img-p05.jpg";
			$p++;
			break;
		case "PICPTN01:パターン６":
			$pic[$p]="/common/images/img-p06.jpg";
			$p++;
			break;
		}
	}
	$tmp="";
	for ($i=0; $i<$p; $i++) {
		$tmp.="<div><img src=\"".$pic[$i]."\" alt=\"".$item2['HNAME']."のメインイメージ".($i+1)."\" /></div>\n";
	}
	$str=str_replace("[HPICS]", $tmp, $str);

	$StrSQL="SELECT * FROM DAT_MATCH where MID='".$_SESSION['MID']."' and JID='".$item['JID']."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2 = mysqli_fetch_assoc($rs2);
	if ($item2['POINT']!="0"){
		$str=str_replace("[D-POINT]", $item2['POINT'], $str);
		$str=DispParam($str, "POINT");
	} else {
		$str=DispParamNone($str, "POINT");
	} 

	$str=str_replace("[ICONI]", IconID($item['HID'], $_SESSION['MID']), $str);
	$str=str_replace("[ICONO]", IconOD($item['JID'], $_SESSION['MID']), $str);

	$StrSQL="SELECT ID FROM DAT_FAV where MID='".$_SESSION['MID']."' and JID='".$item['JID']."';";
	$rs2=mysqli_query(ConnDB(),$StrSQL);
	$item2=mysqli_num_rows($rs2);
	if($item2>0){
		$str=DispParam($str, "LIKE-BTN-ON");
		$str=DispParamNone($str, "LIKE-BTN-OFF");
	} else {
		$str=DispParamNone($str, "LIKE-BTN-ON");
		$str=DispParam($str, "LIKE-BTN-OFF");
	}

	$str=str_replace("[MID]", $_SESSION['MID'], $str);
	$str=str_replace("[HID]", $item['HID'], $str);

	return $str;

} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconI($hid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_IINE where MID='".$mid."' and HID='".$hid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<a href=\"#\"><img src=\"/common/images/icon-iine2-w.svg\" alt=\"\" /></a>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconID($hid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_IINE where MID='".$mid."' and HID='".$hid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<div class=\"status-iine icon\"><a href=\"#\"><img src=\"/common/images/icon-iine2-w.svg\" alt=\"\" /></a><span>いいねされた</span></div>";
//		return "<a href=\"#\"><img src=\"/common/images/icon-iine-w.svg\" alt=\"\" /></a>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconI1($hid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_IINE where MID='".$mid."' and HID='".$hid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<div class=\"status-iine icon\"><a href=\"#\"><img src=\"/common/images/icon-iine-w.svg\" alt=\"\" /></a><!--span>いいねした</span--></div>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconI2($hid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_FAV where MID='".$mid."' and HID='".$hid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<div class=\"status-iine icon\"><a href=\"#\"><img src=\"/common/images/icon-iine2-w.svg\" alt=\"\" /></a><!--span>いいねされた</span--></div>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconI1D($hid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_IINE where MID='".$mid."' and HID='".$hid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<div class=\"status-iine icon\"><a href=\"#\"><img src=\"/common/images/icon-iine-w.svg\" alt=\"\" /></a><span>いいねした</span></div>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconI2D($hid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_FAV where MID='".$mid."' and HID='".$hid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<div class=\"status-iine icon\"><a href=\"#\"><img src=\"/common/images/icon-iine2-w.svg\" alt=\"\" /></a><span>いいねされた</span></div>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconOD($jid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_FAV where MID='".$mid."' and JID='".$jid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<div class=\"status-iine icon\"><a href=\"#\"><img src=\"/common/images/icon-iine-w.svg\" alt=\"\" /></a><span>いいねした</span></div>";
//		return "<a href=\"#\"><img src=\"/common/images/icon-like-w.svg\" alt=\"\" /></a>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconO($jid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_FAV where MID='".$mid."' and JID='".$jid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<a href=\"#\"><img src=\"/common/images/icon-iine-w.svg\" alt=\"\" /></a>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function IconO2($hid, $mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT ID FROM DAT_FAV where MID='".$mid."' and HID='".$hid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item>0){
		return "<a href=\"#\"><img src=\"/common/images/icon-iine-w.svg\" alt=\"\" /></a>";
	} else {
		return "";
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function HospitalPic($hid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT PIC01, PICPTN01 FROM DAT_HOSPITAL where HID='".$hid."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	if(strstr($item['PIC01'], "s.gif")==false){
		return $item['PIC01'];
	} else {
		$cols=explode("\t",$item['PICPTN01']);
		switch ($cols[0]){
		case "PICPTN01:パターン１":
			return "/common/images/img-p01.jpg";
			break;
		case "PICPTN01:パターン２":
			return "/common/images/img-p02.jpg";
			break;
		case "PICPTN01:パターン３":
			return "/common/images/img-p03.jpg";
			break;
		case "PICPTN01:パターン４":
			return "/common/images/img-p04.jpg";
			break;
		case "PICPTN01:パターン５":
			return "/common/images/img-p05.jpg";
			break;
		case "PICPTN01:パターン６":
			return "/common/images/img-p06.jpg";
			break;
		}
	}
} 

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function MemberPic($mid)
{
	//extract($GLOBALS);
	eval(globals());

	$StrSQL="SELECT PICPTN FROM DAT_MEMBER where MID='".$mid."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);

	switch ($item['PICPTN']){
	case "PICPTN:パターン１":
		return "/common/images/img-user-m1.jpg";
		break;
	case "PICPTN:パターン２":
		return "/common/images/img-user-w1.jpg";
		break;
	case "PICPTN:パターン３":
		return "/common/images/img-user-m2.jpg";
		break;
	case "PICPTN:パターン４":
		return "/common/images/img-user-w2.jpg";
		break;
	case "PICPTN:パターン５":
		return "/common/images/img-user-m3.jpg";
		break;
	case "PICPTN:パターン６":
		return "/common/images/img-user-w3.jpg";
		break;
	case "PICPTN:パターン７":
		return "/common/images/img-user-m4.jpg";
		break;
	case "PICPTN:パターン８":
		return "/common/images/img-user-w4.jpg";
		break;
	case "PICPTN:パターン９":
		return "/common/images/img-user-m5.jpg";
		break;
	case "PICPTN:パターン１０":
		return "/common/images/img-user-w5.jpg";
		break;
	}
} 
*/

//=========================================================================================================
//名前 
//機能\ 平均評価を返す
//引数 
//戻値 
//=========================================================================================================
function CalcEvalAvg($mid)
{

	eval(globals());

	$StrSQL="SELECT EVAL FROM DAT_EVAL where MID='".$mid."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$sum = 0;
	$cnt = 0;
	while ($item = mysqli_fetch_assoc($rs)) {
		$sum += intval(str_replace('EVAL:', '', $item['EVAL']));
		$cnt++;
	} 
	if($cnt == 0) {
		return 'なし';
	}
  return '' . sprintf('%.1f', round($sum / $cnt, 1));
}

//=========================================================================================================
//名前 
//機能\ 評価一覧を返す
//引数 
//戻値 
//=========================================================================================================
function GetEvalList($mid)
{

	eval(globals());

	// class名を入れていますがcss側未設定です。

	$StrSQL="SELECT date_format(NEWDATE,'%Y年%c月%e日') as NEWDATE,EVAL,COMMENT FROM DAT_EVAL where MID='".$mid."' and ENABLE='ENABLE:公開中' order by NEWDATE desc;";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$html = '<ul class="eval_list">';
	$star_list = '★★★★★';
	$cnt = 0;
	while ($item = mysqli_fetch_assoc($rs)) {
		$cnt++;
		$eval_val = intval(str_replace('EVAL:', '', $item['EVAL']));
		$star = mb_substr($star_list, 5 - $eval_val);
		$html .= ''
			. '<li>'
			. '<div class="eval_star">' . $star . '</div>'
			. '<div class="eval_date">' . $item['NEWDATE'] . '</div>'
			. '<div class="eval_comment">' . $item['COMMENT'] . '</div>'
			. '</li>'
			. '';
	} 
	$html .= '</ul>';
	if($cnt == 0) {
		$html = '評価はまだありません';
	}
  return $html;
}


?>
