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
		$key=$_GET['key'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
		$token=$_GET['token'];
		$mid1=$_GET['mid1'];
		$mid2=$_GET['mid2'];
		$word2=$_GET['word2'];
		$word3=$_GET['word3'];
		$eid=$_GET['eid'];
		$msgid=$_GET['msgid'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
		$mid1=$_POST['mid1'];
		$mid2=$_POST['mid2'];
		$word2=$_POST['word2'];
		$word3=$_POST['word3'];
		$eid=$_POST['eid'];
		$msgid=$_POST['msgid'];
	}

	if ($mode=="star"){
		$StrSQL="UPDATE DAT_MESSAGE SET ";
		$StrSQL=$StrSQL." ETC05 ='ETC05:対象'";
		$StrSQL=$StrSQL." WHERE ETC02 ='".$msgid."'";
		$StrSQL=$StrSQL." AND AID ='".$word."'";
		// var_dump($StrSQL);
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

		$mode="list";
	}

	if ($mode=="notstar"){
		$StrSQL="UPDATE DAT_MESSAGE SET ";
		$StrSQL=$StrSQL." ETC05 ='ETC05:非対象'";
		$StrSQL=$StrSQL." WHERE ETC02 ='".$msgid."'";
		$StrSQL=$StrSQL." AND AID ='".$word."'";
		// var_dump($StrSQL);
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

		$mode="list";
	}



	if ($mode=="cancel"){
		//不成立日時をセット、ステータスをキャンセルに変更
		$StrSQL="UPDATE DAT_ESTIMATES SET ";
		$StrSQL=$StrSQL." CDATE ='".date("Y/m/d H:i:s")."',";
		$StrSQL=$StrSQL." STATUS ='STATUS:キャンセル'";
		$StrSQL=$StrSQL." WHERE EID ='".$eid."'";
		
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

		$StrSQL=" SELECT * FROM DAT_ESTIMATES";
		$StrSQL.=" WHERE EID = '".$eid."'";
		$StrSQL.=" AND M1ID = '".$mid1."'";
		$StrSQL.=" AND M2ID = '".$mid2."'";
		$rs2=mysqli_query(ConnDB(),$StrSQL);
		$item2 = mysqli_fetch_assoc($rs2);

		$comment= '[キャンセル]が送信されました。' . "\n";
		
		//メッセージ
		$StrSQL="INSERT INTO DAT_MESSAGE (AID, RID, ENABLE, NEWDATE, COMMENT, ETC02) values (";
		$StrSQL.="'".$word."',";
		$StrSQL.="'".$_SESSION['MID']."',";
		$StrSQL.="'ENABLE:公開中',";
		$StrSQL.="'".date("Y/m/d H:i:s")."',";
		$StrSQL.="'".$comment."',";
		$StrSQL.="'".$item2['ETC02']."'";
		$StrSQL.=")";
		if (!(mysqli_query(ConnDB(),$StrSQL))) {
			die;
		}

		$mode="list";
	}

	if ($mode==""){
		$mode="list";
	}

	if ($page==""){
		$page=1;
	} 

	DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2,$word2,$word3);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token,$mid1,$mid2,$word2,$word3)
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

	// $filename="../common/template/chat2_list_part.html";
	// $fp=$DOCUMENT_ROOT.$filename;
	// $strM=@file_get_contents($fp);

	$hid="";
	// SQLインジェクション対策
	// 2020.12.23 yamamoto ETC02を案件IDとし、案件ごとにグループ化
	$StrSQL=" SELECT MSG.*,ifnull(MSG_NOREAD.NOREAD,'zzz') as NOREAD,DAT_M1.ID as M1KEY,DAT_M1.M1_DVAL01 as M1NAME, ";
	$StrSQL.=" CASE WHEN ESTIMATES.MAXDATE > MSG.ldate THEN ESTIMATES.MAXDATE ELSE MSG.ldate END as MAXDATE, ";
	$StrSQL.=" MSG_TITLE.TITLE as DETAIL_TITLE, ";
	$StrSQL.=" MSG.MSG as DETAIL_MSG FROM";
	$StrSQL.=" (SELECT MSG1.ETC05 as star,MSG1.ETC02 as msgid,DAT_MESSAGE.AID, DAT_MESSAGE.NEWDATE as ldate, DAT_MESSAGE.COMMENT as MSG from DAT_MESSAGE INNER JOIN ";
	$StrSQL.="    (SELECT ETC02,max(ifnull(ETC05, '')) as ETC05,AID,max(NEWDATE) as ldate from DAT_MESSAGE where AID = '".$_GET['word']."' and ETC02 is not null and ETC02 != '' group by AID,ETC02 ) MSG1 ";
	$StrSQL.="  ON DAT_MESSAGE.AID = MSG1.AID AND DAT_MESSAGE.NEWDATE = MSG1.ldate AND DAT_MESSAGE.ETC02 = MSG1.ETC02 ) MSG ";
	
	//NOREAD用
	$StrSQL.=" LEFT JOIN  ";
	$StrSQL.=" (SELECT ifnull(NOREAD,'') as NOREAD,ETC02 FROM DAT_MESSAGE   ";
	$StrSQL.="  where AID = '".$_GET['word']."' and RID<>'".$_SESSION['MID']."' and ifnull(NOREAD,'') = ''  ";
	$StrSQL.="  group by ifnull(NOREAD,''),ETC02) MSG_NOREAD ";
	$StrSQL.=" ON MSG.msgid = MSG_NOREAD.ETC02 ";
	
	//タイトル用
	$StrSQL.=" LEFT JOIN  (";
	$StrSQL.=" SELECT DAT_MESSAGE.ETC02 as msgid,DAT_MESSAGE.AID,  ";
	$StrSQL.=" replace(replace(DAT_MESSAGE.COMMENT,'[Change title]<br>「',''),'」 has been entered.','') as TITLE ";
	$StrSQL.=" from DAT_MESSAGE INNER JOIN ";
	$StrSQL.="   (SELECT ETC02,AID,max(NEWDATE) as ldate from DAT_MESSAGE ";
	$StrSQL.="    where AID = '".$_GET['word']."' and ETC02 is not null and ETC02 != '' and COMMENT like '[Change title]%' ";
	$StrSQL.="    group by AID,ETC02) MSG2 ";
	$StrSQL.=" ON DAT_MESSAGE.AID = MSG2.AID AND DAT_MESSAGE.NEWDATE = MSG2.ldate AND DAT_MESSAGE.ETC02 = MSG2.ETC02 ";
	$StrSQL.=" ) MSG_TITLE ";
	$StrSQL.=" ON MSG.msgid = MSG_TITLE.msgid ";
	$StrSQL.=" AND MSG.AID = MSG_TITLE.AID ";

	//日付用
	$StrSQL.=" LEFT JOIN  (";
	$StrSQL.=" SELECT max(";
	$StrSQL.=" CASE WHEN DAT_請求書.CDATE != '' THEN DAT_請求書.CDATE ";
	$StrSQL.="      WHEN DAT_請求書.PDATE != '' THEN DAT_請求書.PDATE ";
	$StrSQL.=" 	 WHEN DAT_請求書.IDATE != '' THEN DAT_請求書.IDATE ";
	$StrSQL.=" 	 WHEN DAT_ESTIMATES.CDATE != '' THEN DAT_ESTIMATES.CDATE ";
	$StrSQL.=" 	 WHEN DAT_ESTIMATES.NDATE != '' THEN DAT_ESTIMATES.NDATE ";
	$StrSQL.=" 	 WHEN DAT_ESTIMATES.JDAETE != '' THEN DAT_ESTIMATES.JDAETE ";
	$StrSQL.=" 	 WHEN DAT_ESTIMATES.MDATE != '' THEN DAT_ESTIMATES.MDATE ";
	$StrSQL.=" 	 ELSE '' END) as MAXDATE,DAT_ESTIMATES.M1ID,DAT_ESTIMATES.M2ID,DAT_ESTIMATES.ETC02 ";
	$StrSQL.=" FROM DAT_ESTIMATES LEFT JOIN DAT_請求書";
	$StrSQL.=" ON DAT_ESTIMATES.M1ID = DAT_請求書.M1ID AND DAT_ESTIMATES.EID = DAT_請求書.EID";
	$StrSQL.=" GROUP BY DAT_ESTIMATES.M1ID,DAT_ESTIMATES.M2ID,DAT_ESTIMATES.ETC02";
	$StrSQL.=" ) ESTIMATES ";
	$StrSQL.=" ON ESTIMATES.M1ID = SUBSTR(MSG.AID,1,7)";
	$StrSQL.=" AND ESTIMATES.M2ID = SUBSTR(MSG.AID,9)";
	$StrSQL.=" AND ESTIMATES.ETC02 = MSG.msgid ";

	 
	$StrSQL.=" LEFT JOIN DAT_M1 ON SUBSTR(MSG.AID,1,7) = DAT_M1.MID ";
	if($word2!=""){
		$StrSQL.=" where DAT_M1.M1_DVAL01 like '%".$word2."%' ";
	}
	$StrSQL.=" order by ";
	switch ($sort) {
		case 1:
			$StrSQL.=" CASE WHEN ESTIMATES.MAXDATE > MSG.ldate THEN ESTIMATES.MAXDATE ELSE MSG.ldate END asc, ";
			break;
		case 2:
			$StrSQL.=" CASE WHEN ESTIMATES.MAXDATE > MSG.ldate THEN ESTIMATES.MAXDATE ELSE MSG.ldate END desc, ";
			break;
		case 3:
			$StrSQL.=" MSG_TITLE.TITLE asc, ";
			break;
		case 4:
			$StrSQL.=" MSG_TITLE.TITLE desc, ";
			break;
		case 5:
			$StrSQL.=" DAT_M1.M1_DVAL01 asc, ";
			break;
		case 6:
			$StrSQL.=" DAT_M1.M1_DVAL01 desc, ";
			break;
		default:
			break;
	}
	$StrSQL.=" CASE WHEN ifnull(MSG_NOREAD.NOREAD,'zzz') ='' THEN 1 ELSE 0 END desc,CASE WHEN ifnull(star,'') ='ETC05:対象' THEN 1 ELSE 0 END desc,MAXDATE desc;";

	// var_dump($StrSQL); 
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);


	$syodan_cnt = 0; //商談中
	$mirai_cnt = 0; //見積依頼中
	$mteiji_cnt = 0; //見積提示中
	$hacchu_cnt = 0; //発注済
	$nohin_cnt = 0; //納品済
	$seikyu_cnt = 0; //請求提示中
	$nyukin_cnt = 0; //入金済
	$cancel_cnt = 0; //キャンセル

	$ids = array();
	$reccount=0;

	if($item=="") {
		$reccount=0;
		$pagestr="";
		$strMain="There is no message exchange yet.";
	} else {
		//================================================================================================
		//カウントアップ
		//================================================================================================
		while ($item = mysqli_fetch_assoc($rs)) {
			$status="";



			// if($status!="見積依頼否認"){

			
			$title="";
			//見積・請求データ抽出
			$StrSQL=" SELECT DAT_ESTIMATES.ID,DAT_ESTIMATES.EID,";
			$StrSQL.=" DAT_請求書.ID as ID2,DAT_請求書.IID,";
			$StrSQL.=" DAT_ESTIMATES.MDATE,"; //見積日時
			$StrSQL.=" DAT_ESTIMATES.MDATE,"; //発注日時
			$StrSQL.=" DAT_ESTIMATES.NDATE,"; //納品日時
			$StrSQL.=" DAT_ESTIMATES.CDATE,"; //不成立日時
			$StrSQL.=" DAT_ESTIMATES.STATUS,"; //ステータス(見積)
			$StrSQL.=" DAT_請求書.SDATE,"; //支払日
			$StrSQL.=" DAT_請求書.IDATE,"; //請求日時
			$StrSQL.=" DAT_請求書.PDATE,"; //入金日時
			$StrSQL.=" DAT_請求書.CDATE as CDATE2,"; //キャンセル日時
			$StrSQL.=" DAT_請求書.STATUS as STATUS2,"; //ステータス(請求)
			$StrSQL.=" IFNULL(DAT_請求書.TITLE,DAT_ESTIMATES.TITLE) as DETAIL_TITLE,";
			$StrSQL.=" CASE WHEN DAT_請求書.CDATE != '' THEN DAT_請求書.CDATE ";
			$StrSQL.="      WHEN DAT_請求書.PDATE != '' THEN DAT_請求書.PDATE ";
			$StrSQL.=" 	 WHEN DAT_請求書.IDATE != '' THEN DAT_請求書.IDATE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.CDATE != '' THEN DAT_ESTIMATES.CDATE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.NDATE != '' THEN DAT_ESTIMATES.NDATE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.JDAETE != '' THEN DAT_ESTIMATES.JDAETE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.MDATE != '' THEN DAT_ESTIMATES.MDATE ";
			$StrSQL.=" 	 ELSE '".$item['MAXDATE']."' END  as MAXDATE ";
			$StrSQL.=" FROM DAT_ESTIMATES LEFT JOIN DAT_請求書";
			$StrSQL.=" ON DAT_ESTIMATES.M1ID = DAT_請求書.M1ID AND DAT_ESTIMATES.EID = DAT_請求書.EID";
			$StrSQL.=" WHERE DAT_ESTIMATES.M1ID = SUBSTR('".$item["AID"]."',1,7)";
			$StrSQL.=" AND DAT_ESTIMATES.ETC02 = '".$item["msgid"]."'";
			$StrSQL.=" ORDER BY DAT_ESTIMATES.ID,DAT_ESTIMATES.EID ";


// var_dump($StrSQL);
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			while ($item2 = mysqli_fetch_assoc($rs2)) {

				$title = $item2['DETAIL_TITLE'];


				if($status!=""){
					$status.="<br/>";
				}

				$status.="●".$item2["EID"]."　"; //見積番号
				
				if($item2['STATUS']=="STATUS:見積依頼中"){
					$status.="見積依頼中"; 
					$mirai_cnt = $mirai_cnt + 1;

				} else if($item2['STATUS']=="STATUS:見積提示中"){
					$status.="<a target=\"_blank\" href=\"/m_estimates/?mode=disp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&key=".$item2['ID']."\">見積書</a>"; //見積書
					$status.="　<a href=\"/m_estimates/?mode=hacchu&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&key=".$item2['ID']."\">発注</a>"; //発注
					$status.="　<a onclick=\"var ok=confirm('キャンセルしてもよろしいですか？');if (ok) this.form.submit(); return false;\" href=\"./?mode=cancel&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&key=".$item2['ID']."\">キャンセル</a>"; //キャンセル
					
					$mteiji_cnt = $mteiji_cnt + 1;
				
				} else if($item2['STATUS']=="STATUS:発注済"){	
					$status.="発注済"; 
					$hacchu_cnt = $hacchu_cnt + 1;
				} else if($item2['STATUS']=="STATUS:納品済"){
					$status.="納品済"; 
					$nohin_cnt = $nohin_cnt + 1;
				} else if($item2['STATUS']=="STATUS:請求提示中"){
					$status.="<a target=\"_blank\" href=\"/m_請求書/?mode=disp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&iid=".$item2['IID']."&key=".$item2['ID2']."\">請求書</a>"; //請求書
					$seikyu_cnt = $seikyu_cnt + 1;
				} else if($item2['STATUS']=="STATUS:入金済"){
					$status.="入金確認済み"; 
					$nyukin_cnt = $nyukin_cnt + 1;
				} else if($item2['STATUS']=="STATUS:キャンセル"){
					$status.="キャンセル"; 
					$cancel_cnt = $cancel_cnt + 1;
				}

			}
			// }

			//未読
			if ($item['NOREAD']=="") {
				$midoku_cnt = $midoku_cnt + 1;
			}

			if($status==""){
				$status = "商談中";
				$syodan_cnt = $syodan_cnt + 1;
			} else if($word3=="商談中"){
				//商談中以外は、読み飛ばす
				continue;
			}


			if($word3=="見積依頼中"){
				if (strpos($status, "見積依頼中") === false)
				{
					continue;
				}
			}
			if($word3=="見積提示中"){
				if (strpos($status, "発注<") === false)
				{
					continue;
				}
			}
			if($word3=="発注済"){
				if (strpos($status, "発注済") === false)
				{
					continue;
				}
			}
			if($word3=="納品済"){
				if (strpos($status, "納品済") === false)
				{
					continue;
				}
			}
			if($word3=="請求提示中"){
				if (strpos($status, "請求書<") === false)
				{
					continue;
				}
			}
			if($word3=="入金済"){
				if (strpos($status, "入金確認済み") === false)
				{
					continue;
				}
			}
			if($word3=="キャンセル"){
				if (strpos($status, "キャンセル ") === false )
				{
					continue;
				}
			}


			
			$reccount=$reccount+1;

			$ids[] = $item['msgid']; //IDを入れる
		
		} 

		//================================================================================================
		//ページング処理
		//================================================================================================
		$maxreccount=mysqli_num_rows($rs);

		$pagecount=intval(($reccount-1)/$PageSize+1);
		
		mysqli_data_seek($rs, 0);

		//位置を求める
		$postion=0;
		$id = $ids[$PageSize*($page-1)];
		while ($item = mysqli_fetch_assoc($rs)) {
			if($id==$item["msgid"]){
				break;	
			}
			$postion=$postion+1;
		}
		mysqli_data_seek($rs, $PageSize*($page-1));

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
			$status="";

			$str=$strM;

			if ($item['star'] =="ETC05:対象") {
				$str=str_replace("[STAR]","<a href=\"./?mode=notstar&sort=".$sort."&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&msgid=".$item['msgid']."&eid=".$item['EID']."\">★</a>",$str);
			} else {
				$str=str_replace("[STAR]","<a href=\"./?mode=star&sort=".$sort."&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&msgid=".$item['msgid']."&eid=".$item['EID']."\">☆</a>",$str);
			}
			


			$str=str_replace("[M1KEY]",$item['M1KEY'],$str);
			$str=str_replace("[M1NAME]",$item['M1NAME'],$str);
			
			$str=str_replace("[MSGID]",$item['msgid'],$str);

			$str=str_replace("[AID]",$word,$str);
			$str=str_replace("[MID1]",$mid1,$str);
			$str=str_replace("[MID2]",$mid2,$str);
			


			// if($status!="見積依頼否認"){

		
			$title="";
			//見積・請求データ抽出
			$StrSQL=" SELECT DAT_ESTIMATES.ID,DAT_ESTIMATES.EID,";
			$StrSQL.=" DAT_請求書.ID as ID2,DAT_請求書.IID,";
			$StrSQL.=" DAT_ESTIMATES.MDATE,"; //見積日時
			$StrSQL.=" DAT_ESTIMATES.MDATE,"; //発注日時
			$StrSQL.=" DAT_ESTIMATES.NDATE,"; //納品日時
			$StrSQL.=" DAT_ESTIMATES.CDATE,"; //不成立日時
			$StrSQL.=" DAT_ESTIMATES.STATUS,"; //ステータス(見積)
			$StrSQL.=" DAT_請求書.SDATE,"; //支払日
			$StrSQL.=" DAT_請求書.IDATE,"; //請求日時
			$StrSQL.=" DAT_請求書.PDATE,"; //入金日時
			$StrSQL.=" DAT_請求書.CDATE as CDATE2,"; //キャンセル日時
			$StrSQL.=" DAT_請求書.STATUS as STATUS2,"; //ステータス(請求)
			$StrSQL.=" IFNULL(DAT_請求書.TITLE,DAT_ESTIMATES.TITLE) as DETAIL_TITLE,";
			$StrSQL.=" CASE WHEN DAT_請求書.CDATE != '' THEN DAT_請求書.CDATE ";
			$StrSQL.="      WHEN DAT_請求書.PDATE != '' THEN DAT_請求書.PDATE ";
			$StrSQL.=" 	 WHEN DAT_請求書.IDATE != '' THEN DAT_請求書.IDATE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.CDATE != '' THEN DAT_ESTIMATES.CDATE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.NDATE != '' THEN DAT_ESTIMATES.NDATE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.JDAETE != '' THEN DAT_ESTIMATES.JDAETE ";
			$StrSQL.=" 	 WHEN DAT_ESTIMATES.MDATE != '' THEN DAT_ESTIMATES.MDATE ";
			$StrSQL.=" 	 ELSE '".$item['MAXDATE']."' END  as MAXDATE ";
			$StrSQL.=" FROM DAT_ESTIMATES LEFT JOIN DAT_請求書";
			$StrSQL.=" ON DAT_ESTIMATES.M1ID = DAT_請求書.M1ID AND DAT_ESTIMATES.EID = DAT_請求書.EID";
			$StrSQL.=" WHERE DAT_ESTIMATES.M1ID = SUBSTR('".$item["AID"]."',1,7)";
			$StrSQL.=" AND DAT_ESTIMATES.ETC02 = '".$item["msgid"]."'";
			$StrSQL.=" ORDER BY DAT_ESTIMATES.ID,DAT_ESTIMATES.EID ";


// var_dump($StrSQL);
			$rs2=mysqli_query(ConnDB(),$StrSQL);
			while ($item2 = mysqli_fetch_assoc($rs2)) {

				$title = $item2['DETAIL_TITLE'];


				if($status!=""){
					$status.="<br/>";
				}

				$status.="●".$item2["EID"]."　"; //見積番号
				
				if($item2['STATUS']=="STATUS:見積依頼中"){
					$status.="見積依頼中"; 
					// $mirai_cnt = $mirai_cnt + 1;

				} else if($item2['STATUS']=="STATUS:見積提示中"){
					$status.="<a target=\"_blank\" href=\"/m_estimates/?mode=disp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&key=".$item2['ID']."\">見積書</a>"; //見積書
					$status.="　<a href=\"/m_estimates/?mode=hacchu&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&key=".$item2['ID']."\">発注</a>"; //発注
					$status.="　<a onclick=\"var ok=confirm('キャンセルしてもよろしいですか？');if (ok) this.form.submit(); return false;\" href=\"./?mode=cancel&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&key=".$item2['ID']."\">キャンセル</a>"; //キャンセル
					
					// $mteiji_cnt = $mteiji_cnt + 1;
				
				} else if($item2['STATUS']=="STATUS:発注済"){	
					$status.="発注済"; 
					// $hacchu_cnt = $hacchu_cnt + 1;
				} else if($item2['STATUS']=="STATUS:納品済"){
					$status.="納品済"; 
					$status.="　<a target=\"_blank\" href=\"/m_estimates/?mode=nouhindisp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&key=".$item2['ID']."\">納品書</a>"; //納品書
					// $nohin_cnt = $nohin_cnt + 1;
				} else if($item2['STATUS']=="STATUS:請求提示中"){
					$status.="<a target=\"_blank\" href=\"/m_請求書/?mode=disp&word=".$word."&mid1=".$mid1."&mid2=".$mid2."&eid=".$item2['EID']."&iid=".$item2['IID']."&key=".$item2['ID2']."\">請求書</a>"; //請求書
					// $seikyu_cnt = $seikyu_cnt + 1;
				} else if($item2['STATUS']=="STATUS:入金済"){
					$status.="入金確認済み"; 
					// $nyukin_cnt = $nyukin_cnt + 1;
				} else if($item2['STATUS']=="STATUS:キャンセル"){
					$status.="キャンセル "; 
					// $cancel_cnt = $cancel_cnt + 1;
				}


			}
			// }

			if($status==""){
				$status = "商談中";
				// $syodan_cnt = $syodan_cnt + 1;
			} else if($word3=="商談中"){
				//商談中以外は、読み飛ばす
				continue;
			}

			//タイトル取得
			if($item["DETAIL_TITLE"]==""){
				$TITLE = '(タイトルが入ります)';
				$str=str_replace("[TITLE]",$TITLE,$str);
			} else {
				$str=str_replace("[TITLE]",$item["DETAIL_TITLE"],$str);
			}


			$str=str_replace("[STATUS]",$status,$str);
			$str=str_replace("[NEWDATE]",$item['MAXDATE'],$str);

			$msg = preg_replace('/<a .*?>(.*?)<\/a>/', "", $item['DETAIL_MSG']);
			$str=str_replace("[COMMENT]",$msg ,$str);


			if ($item['NOREAD']=="") {
				$str=DispParam($str, "MIDOKU");
			} else {
				$str=DispParamNone($str, "MIDOKU");
			}

			if($word3=="見積依頼中"){
				if (strpos($status, "見積依頼中") === false)
				{
					continue;
				}
			}
			if($word3=="見積提示中"){
				if (strpos($status, "発注<") === false)
				{
					continue;
				}
			}
			if($word3=="発注済"){
				if (strpos($status, "発注済") === false)
				{
					continue;
				}
			}
			if($word3=="納品済"){
				if (strpos($status, "納品済") === false)
				{
					continue;
				}
			}
			if($word3=="請求提示中"){
				if (strpos($status, "請求書<") === false)
				{
					continue;
				}
			}
			if($word3=="入金済"){
				if (strpos($status, "入金確認済み") === false)
				{
					continue;
				}
			}
			if($word3=="キャンセル"){
				if (strpos($status, "キャンセル ") === false )
				{
					continue;
				}
			}



			$strMain=$strMain.$str.chr(13);

			$CurrentRecord=$CurrentRecord+1; //CurrentRecordの更新

			if ($CurrentRecord>$PageSize){
				break;
			}
		} 
	} 

	$str=$strU.$strMain.$strD;

	$str = MakeHTML($str,0,$lid);

	$StrSQL="SELECT * FROM DAT_M1 where MID='".$_GET['mid1']."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	$str=DispM1($item, $str);


	// 2020.12.23 yamamoto 新規案件用IDの発番
	$StrSQL_max="SELECT max(ifnull(cast(ETC02 AS SIGNED), 0)) as max_id FROM DAT_MESSAGE where AID = '".$_GET['word']."'";
	$rs_max=mysqli_query(ConnDB(),$StrSQL_max);
	$item_max = mysqli_fetch_assoc($rs_max);
	$next_id = intval($item_max['max_id']) + 1;
	
	$str=str_replace("[NEXT_ID]",$next_id,$str);

	$str=str_replace("[PAGING]",$pagestr,$str);
	$str=str_replace("[SORT]",$sort,$str);
	$str=str_replace("[WORD]",$word,$str);
	$str=str_replace("[WORD2]",$word2,$str);
	$str=str_replace("[WORD3]",$word3,$str);
	$str=str_replace("[MID1]",$mid1,$str);
	$str=str_replace("[MID2]",$mid2,$str);
	$str=str_replace("[PAGE]",$page,$str);
	$str=str_replace("[KEY]",$key,$str);
	$str=str_replace("[LID]",$lid,$str);
	$str=str_replace("[RECCOUNT]",$reccount,$str);

	$str=str_replace("[SYODAN_CNT]",$syodan_cnt,$str); //商談中
	$str=str_replace("[MIRAI_CNT]",$mirai_cnt,$str); //見積依頼中
	$str=str_replace("[MTEIJI_CNT]",$mteiji_cnt,$str); //見積提示中
	$str=str_replace("[HACCHU_CNT]",$hacchu_cnt,$str); //発注済
	$str=str_replace("[NOHIN_CNT]",$nohin_cnt,$str); //納品済
	$str=str_replace("[SEIKYU_CNT]",$seikyu_cnt,$str); //請求提示中
	$str=str_replace("[NYUKIN_CNT]",$nyukin_cnt,$str); //入金済
	$str=str_replace("[CANCEL_CNT]",$cancel_cnt,$str); //キャンセル

	//ソートリンクの整理
	switch ($sort) {
		case 1:
			$str=DispParamNone($str, "S_DATE");
			$str=DispParam($str, "S_MATTER");
			$str=DispParam($str, "S_NAME");
			$str=DispParam($str, "S_DATE_A");
			$str=DispParamNone($str, "S_MATTER_A");
			$str=DispParamNone($str, "S_NAME_A");
			$str=DispParamNone($str, "S_DATE_D");
			$str=DispParamNone($str, "S_MATTER_D");
			$str=DispParamNone($str, "S_NAME_D");
			break;
		case 2:
			$str=DispParamNone($str, "S_DATE");
			$str=DispParam($str, "S_MATTER");
			$str=DispParam($str, "S_NAME");
			$str=DispParamNone($str, "S_DATE_A");
			$str=DispParamNone($str, "S_MATTER_A");
			$str=DispParamNone($str, "S_NAME_A");
			$str=DispParam($str, "S_DATE_D");
			$str=DispParamNone($str, "S_MATTER_D");
			$str=DispParamNone($str, "S_NAME_D");
			break;
		case 3:
			$str=DispParam($str, "S_DATE");
			$str=DispParamNone($str, "S_MATTER");
			$str=DispParam($str, "S_NAME");
			$str=DispParamNone($str, "S_DATE_A");
			$str=DispParam($str, "S_MATTER_A");
			$str=DispParamNone($str, "S_NAME_A");
			$str=DispParamNone($str, "S_DATE_D");
			$str=DispParamNone($str, "S_MATTER_D");
			$str=DispParamNone($str, "S_NAME_D");
			break;
		case 4:
			$str=DispParam($str, "S_DATE");
			$str=DispParamNone($str, "S_MATTER");
			$str=DispParam($str, "S_NAME");
			$str=DispParamNone($str, "S_DATE_A");
			$str=DispParamNone($str, "S_MATTER_A");
			$str=DispParamNone($str, "S_NAME_A");
			$str=DispParamNone($str, "S_DATE_D");
			$str=DispParam($str, "S_MATTER_D");
			$str=DispParamNone($str, "S_NAME_D");
			break;
		case 5:
			$str=DispParam($str, "S_DATE");
			$str=DispParam($str, "S_MATTER");
			$str=DispParamNone($str, "S_NAME");
			$str=DispParamNone($str, "S_DATE_A");
			$str=DispParamNone($str, "S_MATTER_A");
			$str=DispParam($str, "S_NAME_A");
			$str=DispParamNone($str, "S_DATE_D");
			$str=DispParamNone($str, "S_MATTER_D");
			$str=DispParamNone($str, "S_NAME_D");
			break;
		case 6:
			$str=DispParam($str, "S_DATE");
			$str=DispParam($str, "S_MATTER");
			$str=DispParamNone($str, "S_NAME");
			$str=DispParamNone($str, "S_DATE_A");
			$str=DispParamNone($str, "S_MATTER_A");
			$str=DispParamNone($str, "S_NAME_A");
			$str=DispParamNone($str, "S_DATE_D");
			$str=DispParamNone($str, "S_MATTER_D");
			$str=DispParam($str, "S_NAME_D");
			break;
		default:
			$str=DispParam($str, "S_DATE");
			$str=DispParam($str, "S_MATTER");
			$str=DispParam($str, "S_NAME");
			$str=DispParamNone($str, "S_DATE_A");
			$str=DispParamNone($str, "S_MATTER_A");
			$str=DispParamNone($str, "S_NAME_A");
			$str=DispParamNone($str, "S_DATE_D");
			$str=DispParamNone($str, "S_MATTER_D");
			$str=DispParamNone($str, "S_NAME_D");

			break;
	}
		// 2020.12.22 yamamoto スレッド一覧用パラメータ
	// $str=str_replace("[THREAD_WORD]",$_GET['word'],$str);
	// $str=str_replace("[THREAD_MID1]",$_GET['mid1'],$str);
	// $str=str_replace("[THREAD_MID2]",$_GET['mid2'],$str);
	// $str=str_replace("[NEXT_ID]",$next_id,$str);

	// CSRFトークン生成
	if($token==""){
		$token=htmlspecialchars(session_id());
		$_SESSION['token'] = $token;
	}
	$str=str_replace("[TOKEN]",$token,$str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);
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
