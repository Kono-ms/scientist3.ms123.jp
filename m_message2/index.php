<?php

session_start();
require "../config.php";
require "../base.php";
require "../common.php";
require '../a_o1/config.php';

set_time_limit(7200);

//データベース接続
//ConnDB();
//メイン処理
Main();

//=========================================================================================================
//名前 Main関数
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function Main()
{

	eval(globals());

	if($_SESSION['MID']==""){
		$url=BASE_URL . "/login2/";
		header("Location: {$url}");
		exit;
	}

	if($_POST['mode']==""){
		$mode=$_GET['mode'];
		$sort=$_GET['sort'];
		$word=$_GET['word'];
		$word2=$_GET['word2'];
		$key=$_GET['key'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
		$token=$_GET['token'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$word2=$_POST['word2'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
	}
	
	if ($mode==""){
		$mode="list";
	}

	if ($page==""){
		$page=1;
	} 

	DispData($mode,$sort,$word,$word2,$key,$page,$lid,$token);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$word2,$key,$page,$lid,$token)
{

	eval(globals());

	$htmllist = "list.html";

	$filename=$htmllist;

	$fp=$filename;
	$tso=@fopen($fp,"r");

	while( $line = fgets($tso,1024) ){
		if(strstr($line,"LIST-START") == true){
			break;
		}
		$strU=$strU.$line.chr(13);
	}
	while( $line = fgets($tso,1024) ){
		if(strstr($line,"LIST-END") == true){
			break;
		}
		$strM=$strM.$line.chr(13);
	}
	while( $line = fgets($tso,1024) ){
		$strD=$strD.$line.chr(13);
	}
	fclose($tso);

	// $filename="../common/template/listmm1.html";
	// $fp=$DOCUMENT_ROOT.$filename;
	// $strM=@file_get_contents($fp);

	//アカウント情報取得
	$accunt = "";
	$kigyoid = "";
	$StrSQL="SELECT M2_DVAL12,M2_DVAL13 FROM DAT_M2 where MID ='".$_SESSION['MID']."' and M2_DVAL13 !=''";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$accunt = $item["M2_DVAL12"];
	$kigyoid = $item["M2_DVAL13"];

	$strWhere = "";
	/*
	if($accunt=="M2_DVAL12:企業"){
		$StrSQL="SELECT MID FROM DAT_M2 where M2_DVAL13 ='".$kigyoid."' AND M2_DVAL12='M2_DVAL12:企業' ";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		
		while ($item = mysqli_fetch_assoc($rs)) {
			if($strWhere!=""){
				$strWhere.=" OR ";
			} else {
				$strWhere.=" where  (";
			}
			$strWhere.=" DAT_MESSAGE.AID like '%".$item["MID"]."%' ";
		}
		$strWhere.=" ) ";
	} else {
		$strWhere.=" where  DAT_MESSAGE.AID like '%".$_SESSION['MID']."%' ";
	}
	*/

	if(is_array($word)==true){
    $strWhere .= " AND ( 1 = 2 ";
		foreach($word as $word_row) {
			if($word_row == '1') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = '問い合わせ'";
			}
			if($word_row == '2') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = '見積り'";
			}
			if($word_row == '3') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = '発注'";
			}
			if($word_row == '4') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = '納品'";
			}
			if($word_row == '5') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = '請求'";
			}
			if($word_row == '6') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = '完了'";
			}
			if($word_row == '7') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = 'キャンセル'";
			}
			if($word_row == '8') {
	    	$strWhere=$strWhere." OR DAT_SHODAN.C_STATUS = '辞退'";
			}
		}
    $strWhere=$strWhere." ) ";
	}

  if($word2!=""){
    $strWhere .= " AND (";
    
    $strWhere=$strWhere."    DAT_SHODAN.TITLE like '%".$word2."%'";
    $strWhere=$strWhere." OR DAT_SHODAN.KEYWORD like '%".$word2."%'";
    $strWhere=$strWhere." OR DAT_SHODAN.CATEGORY like '%".$word2."%'";
    $strWhere=$strWhere." OR DAT_SHODAN.COMMENT like '%".$word2."%'";
    
    $strWhere=$strWhere." ) ";
  }
	 
	/*
	$hid="";
	// SQLインジェクション対策
	// 2021.03.15 yamamoto ENABLE:公開中のみ表示
	$StrSQL="SELECT DAT_MESSAGE.AID, max(DAT_MESSAGE.NEWDATE) as ldate,DAT_M1.* from DAT_MESSAGE ";
	$StrSQL.=" inner join DAT_M1 on DAT_M1.MID = SUBSTR(AID,1,7) ";

	$StrSQL.=" left join DAT_ESTIMATES on DAT_M1.MID = DAT_ESTIMATES.M1ID ";

	$StrSQL.=$strWhere;
	//$StrSQL.=" where  DAT_MESSAGE.AID like '%".$_SESSION['MID']."%' ";
	//2020/12/28 gaosan ADD START
	$StrSQL .= " AND NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = SUBSTRING(DAT_MESSAGE.AID,1,7)) ";
	$StrSQL .= " AND NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = SUBSTRING(DAT_MESSAGE.AID,9,7)) ";
	//2020/12/28 gaosan ADD END
	$StrSQL .= " group by AID ";
	$StrSQL.=" order by ";
	switch ($sort) {
		case 1:
			$StrSQL.=" DAT_M1.M1_DVAL01 asc, ";
			break;
		case 2:
			$StrSQL.=" DAT_M1.M1_DVAL01 desc, ";
			break;
		case 3:
			$StrSQL.=" DAT_M1.M1_DVAL02 asc, ";
			break;
		case 4:
			$StrSQL.=" DAT_M1.M1_DVAL02 desc, ";
			break;
		case 5:
			$StrSQL.=" DAT_M1.M1_DVAL03 asc, ";
			break;
		case 6:
			$StrSQL.=" DAT_M1.M1_DVAL03 desc, ";
			break;
		default:
			break;
	}
	$StrSQL .= " ldate desc;";
	*/
	$StrSQL = "
		SELECT
			DAT_SHODAN.*
		FROM
			DAT_SHODAN
		WHERE
			DAT_SHODAN.MID2 = '".$_SESSION['MID']."'
			and DAT_SHODAN.C_STATUS != '下書き'
			".$strWhere."
		ORDER BY
	";

	//運営アカウントを最上位
	$StrSQL.=" CASE WHEN DAT_SHODAN.MID1_LIST LIKE '%".M1_SYSTEM_MID."%' THEN 0 ELSE 1 END asc,";
	$StrSQL.=" CASE WHEN DAT_SHODAN.MID2 = '".M2_SYSTEM_MID."' THEN 0 ELSE 1 END asc,";
	switch ($sort) {
		case 1:
			$StrSQL.=" DAT_SHODAN.EDITDATE asc ";
			break;
		case 2:
			$StrSQL.=" DAT_SHODAN.EDITDATE desc ";
			break;
		case 3:
			$StrSQL.=" DAT_SHODAN.STATUS_SORT asc ";
			break;
		case 4:
			$StrSQL.=" DAT_SHODAN.STATUS_SORT desc ";
			break;
		case 5:
			$StrSQL.=" DAT_SHODAN.NEWDATE asc ";
			break;
		case 6:
			$StrSQL.=" DAT_SHODAN.NEWDATE desc ";
			break;
		default:
			$StrSQL.=" DAT_SHODAN.EDITDATE desc ";
			break;
	}

//  var_dump($StrSQL); 
	echo '<!--main:'.$StrSQL.'-->';
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item=="") {
		$reccount=0;
		$pagestr="";
		$strMain="There is no message exchange yet.";
	} else {
		//================================================================================================
		//ページング処理
		//================================================================================================

		// TODO
		//$PageSize = 2;

		$reccount=mysqli_num_rows($rs);
		$pagecount=intval(($reccount-1)/$PageSize+1);
		mysqli_data_seek($rs, $PageSize*($page-1));

		// $str="";
		// $s=$page-5;
		// if ($s<1) {
		// 	$s=1;
		// } 
		// $e=$s+9;
		// if ($e>$pagecount) {
		// 	$e=$pagecount;
		// } 
		// for ($i=$s; $i<=$e; $i=$i+1) {
		// 	if ($i==intval($page)) {
		// 		$str=$str."<span class=\"current\">".$i."</span>";
		// 	} else {
		// 		$str=$str." <a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\" class=\"inactive\">".$i."</a>";
		// 	} 
		// }
		$str="";
		$str.="<div class=\"paging\"><div class=\"row\">";
		$str.="<div class=\"col-sm-5\"><div class=\"dataTables_info\" id=\"table_summary_info\" role=\"status\" aria-live=\"polite\">対象件数(".$reccount."件)</div></div>";
		$str.="<div class=\"col-sm-7\"><div class=\"dataTables_paginate paging_simple_numbers\" id=\"table_summary_paginate\"><ul class=\"pagination\">";

		if (intval($page)>1) {
			$str.="<li class=\"paginate_button previous disabled\" id=\"table_summary_previous\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page-1)."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">前の".$PageSize."件</a></li>";
		}

		$s=$page-5;
		if ($s<1) {
			$s=1;
		} 
		$e=$s+9;
		if ($e>$pagecount) {
			$e=$pagecount;
		} 
		for ($i=$s; $i<=$e; $i=$i+1) {
			if ($i==intval($page)) {
				$str.="<li class=\"paginate_button active\"><span>".$i."</span></li>";
			} else {
				$str.="<li class=\"paginate_button\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".$i."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">".$i."</a></li>";
			} 
		}
		if (intval($page)<$pagecount) {
			$str.="<li class=\"paginate_button next\" id=\"table_summary_next\"><a href=\"".$aspname."?mode=list&lid=".$lid."&sort=".$sort."&word=".$word."&page=".($page+1)."\" aria-controls=\"table_summary\" data-dt-idx=\"\" tabindex=\"0\">次の".$PageSize."件</a></li>";
		} 

		$str.="</ul></div></div>";
		$str.="</div></div>";
		$pagestr=$str;

		$CurrentRecord=1;
		$strMain="";
		while ($item = mysqli_fetch_assoc($rs)) {

			$str=$strM;


			// echo "<!--MID2:".$item["MID2"]."-->";




			// 未読
			//M1からM1へ送ってる場合、RID=="M1xxxxx",ETC03=="M1xxxxx"になるので、RIDだけみてるとM2がわで間違って既読がつくべきでないときについてしまう。
			//ETC03もみる用に変更
			$StrSQL="SELECT ID FROM DAT_MESSAGE where ETC02='".$item['ID']."' and RID<>'".$_SESSION['MID']."' ";
			$StrSQL.=" and ETC03 = '".$_SESSION['MID']."' ";
			$StrSQL.=" and (NOREAD is null or NOREAD='')";
			//$StrSQL="SELECT ID FROM DAT_MESSAGE where ETC02='".$item['ID']."' and RID<>'".$_SESSION['MID']."' and (NOREAD is null or NOREAD='');";
			//echo('<!--'.$StrSQL.'-->');
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			$item2=mysqli_num_rows($rs2);
			if($item2>0){
				$str=DispParam($str, "SHODAN-MIDOKU");
			} else {
				$str=DispParamNone($str, "SHODAN-MIDOKU");
			}

			$str=str_replace("[SHODAN_ID]",$item['ID'],$str);

			// 案件名
			$str=str_replace("[TITLE]",$item['TITLE'],$str);

			// 最終更新日
			$str=str_replace("[EDITDATE]",substr($item['EDITDATE'], 0, 10),$str);

			// ステータス
			$status=$item['STATUS'];
			if($status=="見積り依頼" || $status=="再見積り依頼" || $status=="見積り送付"
			 || $status=='発注依頼' || $status=='決済者発注承認' || $status=='運営手数料追加'){
				$status="見積り";
			}

			if($status=="受注承認"  || 
				$status=="キャンセル依頼" || $status=="サプライヤーキャンセル承認" || 
				$status=="サプライヤーキャンセル承認（追加見積り）" || $status=="サプライヤーキャンセル否認" ||
				$status=="受注承認(一括前払い)" || $status=="請求書送付(一括前払い)"){
				$status="実施中";
			}
			if($status=="データ納品" || $status=="物品納品" || $status=="サプライヤーが納品(一括前払い)" || $status=="研究者が納品承認(一括前払い)"){
				$status="納品";
			}
			if($status=="キャンセル承認"){
				$status="キャンセル";
			}
			// 受領ボタン（納品承認のことと思われる）を押した時点で「請求」にするように変更とのこと
			// なので「納品確認」というステータスはなくなった
			if($status=="納品確認" || $status=="キャンセル承認（請求あり）"){
				$status="請求";
			}

			if($status=="完了後対応"){
				$status="完了";
			}

			$str=str_replace("[STATUS]",$status,$str);

			if($item['C_STATUS'] != '問い合わせ' && $item['C_STATUS'] != '見積り') {
				$str=str_replace("[STATUS_HIDE]",'display:none;',$str);
			}
			else {
				$str=str_replace("[STATUS_HIDE]",'',$str);
			}

			//「見積り依頼」ボタンの表示非表示
			if($item['STATUS']=="問い合わせ"){
				$str=DispParam($str,"MITSUMORI_IRAI");
			}else{
				$str=DispParamNone($str,"MITSUMORI_IRAI");
			}


			// 検索条件
			$str=str_replace("[KEYWORD]",$item['KEYWORD'],$str);
			$cate0 = explode(',', $item['CATEGORY']);
			$category = '';
			foreach($cate0 as $cate1) {
				if($cate1 == '') {
					continue;
				}
				$category .= '<span>' . str_replace('O1_MSEL01:', '', $cate1) . '</span>';
			}
			$str=str_replace("[CATEGORY]",$category,$str);

			// echo "<!--MID1_LIST:".$item['MID1_LIST']."-->";
			if(strpos($item['MID1_LIST'],M1_SYSTEM_MID)!==false){
				$str=DispParam($str, "M1_SYSTEM_MID");
				$str=DispParamNone($str, "NOT_M1_SYSTEM_MID");
			} else {
				$str=DispParamNone($str, "M1_SYSTEM_MID");
				$str=DispParam($str, "NOT_M1_SYSTEM_MID");
			}

			// サプライヤー（複数）
			$mid1s = explode(',', $item['MID1_LIST']);
			$m1_link = '';
			foreach($mid1s as $mid1) {
				$StrSQL="SELECT * FROM DAT_M1 where MID='".$mid1."'";
				$rs2=mysqli_query(ConnDB(),$StrSQL);
				$item_m1=mysqli_fetch_assoc($rs2);

				// 企業ごとに未読チェック
				$StrSQL="SELECT ID FROM DAT_MESSAGE where ETC02='".$item['ID']."' and RID<>'".$_SESSION['MID']."' ";
				$StrSQL.=" and ETC03 = '".$_SESSION['MID']."' ";
				$StrSQL.=" and (NOREAD is null or NOREAD='') and AID like '%".$mid1."%';";
				//$StrSQL="SELECT ID FROM DAT_MESSAGE where ETC02='".$item['ID']."' and RID<>'".$_SESSION['MID']."' and (NOREAD is null or NOREAD='') and AID like '%".$mid1."%';";
				//echo('<!--'.$StrSQL.'-->');
				$rs_m1_midoku=mysqli_query(ConnDB(),$StrSQL);
				$item_m1_midoku=mysqli_num_rows($rs_m1_midoku);
				$m1_midoku = '';
				if($item_m1_midoku>0){
					$m1_midoku = '<span class="item--new">未読</span>';
				}
				

				$m1_link .= '
					<div>
						<a href="javascript:chat_open(' . $item['ID'] . ',\'' . $mid1 . '-' . $_SESSION['MID'] . '\',\'' . $mid1 . '\',\'' . $_SESSION['MID'] . '\');">' . $item_m1['M1_DVAL01'] . '</a>&nbsp;
						'.$m1_midoku.'
					</div>';

//				$m1_link .= '
//					<div>
//						<input type="checkbox" name="shodan_' . $item['ID'] . '" value="' . $item_m1['MID'] .'"/>
//						<a href="javascript:chat_open(' . $item['ID'] . ',\'' . $mid1 . '-' . $_SESSION['MID'] . '\',\'' . $mid1 . '\',\'' . $_SESSION['MID'] . '\');">' . $item_m1['M1_DVAL01'] . '</a>&nbsp;
//						'.$m1_midoku.'
//					</div>';
			}
			$str=str_replace("[M1_LIST]",$m1_link,$str);




			$strMain=$strMain.$str.chr(13);

			$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

			if ($CurrentRecord>$PageSize){
				break;
			}
		} 
	} 

	$str=$strU.$strMain.$strD;

	$str = MakeHTML($str,0,$lid);

	for($i = 1; $i <= 6; $i++) {
		if($i == $sort) {
			$str=str_replace("[SORT_SELECT_".$i."]",' selected ',$str);
		}
		else {
			$str=str_replace("[SORT_SELECT_".$i."]",'',$str);
		}
	}

	if(is_array($word)==true){
		foreach($word as $word_row) {
			$str=str_replace("[WORD_CHECK_".$word_row."]",' checked ',$str);
		}
	}

	$str=str_replace("[PAGING]",$pagestr,$str);
	$str=str_replace("[SORT]",$sort,$str);
	$str=str_replace("[WORD]",$word,$str);
	$str=str_replace("[WORD2]",$word2,$str);
	$str=str_replace("[PAGE]",$page,$str);
	$str=str_replace("[KEY]",$key,$str);
	$str=str_replace("[LID]",$lid,$str);
	$str=str_replace("[RECCOUNT]",$reccount,$str);
	$str=str_replace("[START]",((($page - 1) * $PageSize) + 1),$str);
	$str=str_replace("[END]",($reccount < $page * $PageSize ? $reccount : $page * $PageSize),$str);

	//ソートリンクの整理
	switch ($sort) {
		case 1:
			$str=DispParamNone($str, "S_M1_DVAL01");
			$str=DispParam($str, "S_M1_DVAL02");
			$str=DispParam($str, "S_M1_DVAL03");
			$str=DispParam($str, "S_M1_DVAL01_A");
			$str=DispParamNone($str, "S_M1_DVAL02_A");
			$str=DispParamNone($str, "S_M1_DVAL03_A");
			$str=DispParamNone($str, "S_M1_DVAL01_D");
			$str=DispParamNone($str, "S_M1_DVAL02_D");
			$str=DispParamNone($str, "S_M1_DVAL03_D");
			break;
		case 2:
			$str=DispParamNone($str, "S_M1_DVAL01");
			$str=DispParam($str, "S_M1_DVAL02");
			$str=DispParam($str, "S_M1_DVAL03");
			$str=DispParamNone($str, "S_M1_DVAL01_A");
			$str=DispParamNone($str, "S_M1_DVAL02_A");
			$str=DispParamNone($str, "S_M1_DVAL03_A");
			$str=DispParam($str, "S_M1_DVAL01_D");
			$str=DispParamNone($str, "S_M1_DVAL02_D");
			$str=DispParamNone($str, "S_M1_DVAL03_D");
			break;
		case 3:
			$str=DispParam($str, "S_M1_DVAL01");
			$str=DispParamNone($str, "S_M1_DVAL02");
			$str=DispParam($str, "S_M1_DVAL03");
			$str=DispParamNone($str, "S_M1_DVAL01_A");
			$str=DispParam($str, "S_M1_DVAL02_A");
			$str=DispParamNone($str, "S_M1_DVAL03_A");
			$str=DispParamNone($str, "S_M1_DVAL01_D");
			$str=DispParamNone($str, "S_M1_DVAL02_D");
			$str=DispParamNone($str, "S_M1_DVAL03_D");
			break;
		case 4:
			$str=DispParam($str, "S_M1_DVAL01");
			$str=DispParamNone($str, "S_M1_DVAL02");
			$str=DispParam($str, "S_M1_DVAL03");
			$str=DispParamNone($str, "S_M1_DVAL01_A");
			$str=DispParamNone($str, "S_M1_DVAL02_A");
			$str=DispParamNone($str, "S_M1_DVAL03_A");
			$str=DispParamNone($str, "S_M1_DVAL01_D");
			$str=DispParam($str, "S_M1_DVAL02_D");
			$str=DispParamNone($str, "S_M1_DVAL03_D");
			break;
		case 5:
			$str=DispParam($str, "S_M1_DVAL01");
			$str=DispParam($str, "S_M1_DVAL02");
			$str=DispParamNone($str, "S_M1_DVAL03");
			$str=DispParamNone($str, "S_M1_DVAL01_A");
			$str=DispParamNone($str, "S_M1_DVAL02_A");
			$str=DispParam($str, "S_M1_DVAL03_A");
			$str=DispParamNone($str, "S_M1_DVAL01_D");
			$str=DispParamNone($str, "S_M1_DVAL02_D");
			$str=DispParamNone($str, "S_M1_DVAL03_D");
			break;
		case 6:
			$str=DispParam($str, "S_M1_DVAL01");
			$str=DispParam($str, "S_M1_DVAL02");
			$str=DispParamNone($str, "S_M1_DVAL03");
			$str=DispParamNone($str, "S_M1_DVAL01_A");
			$str=DispParamNone($str, "S_M1_DVAL02_A");
			$str=DispParamNone($str, "S_M1_DVAL03_A");
			$str=DispParamNone($str, "S_M1_DVAL01_D");
			$str=DispParamNone($str, "S_M1_DVAL02_D");
			$str=DispParam($str, "S_M1_DVAL03_D");
			break;
		default:
			$str=DispParam($str, "S_M1_DVAL01");
			$str=DispParam($str, "S_M1_DVAL02");
			$str=DispParam($str, "S_M1_DVAL03");
			$str=DispParamNone($str, "S_M1_DVAL01_A");
			$str=DispParamNone($str, "S_M1_DVAL02_A");
			$str=DispParamNone($str, "S_M1_DVAL03_A");
			$str=DispParamNone($str, "S_M1_DVAL01_D");
			$str=DispParamNone($str, "S_M1_DVAL02_D");
			$str=DispParamNone($str, "S_M1_DVAL03_D");

			break;
	}

	// CSRFトークン生成
	if($token==""){
		$token=htmlspecialchars(session_id());
		$_SESSION['token'] = $token;
	}
	$str=str_replace("[TOKEN]",$token,$str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);

	if($_SESSION['MID'] == 'M299999') {
		$str=DispParam($str, "ADMIN-ON");
		$str=DispParamNone($str, "ADMIN-OFF");
	}
	else {
		$str=DispParamNone($str, "ADMIN-ON");
		$str=DispParam($str, "ADMIN-OFF");
	}


	print $str;

	return $function_ret;
} 

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
