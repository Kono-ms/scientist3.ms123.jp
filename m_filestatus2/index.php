<?php

session_start();

require "../config.php";
require "../base.php";
require '../a_mcontact/config.php';

set_time_limit(7200);

//データベース接続
ConnDB();
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

	global $TITLE;

	eval(globals());

	if($_POST['mode']==""){
		$mode=$_GET['mode'];
		$sort=$_GET['sort'];
		$word=$_GET['word'];
		$key=$_GET['key'];
		$page=$_GET['page'];
		$lid=$_GET['lid'];
		$token=$_GET['token'];
	} else {
		$mode=$_POST['mode'];
		$sort=$_POST['sort'];
		$word=$_POST['word'];
		$key=$_POST['key'];
		$page=$_POST['page'];
		$lid=$_POST['lid'];
		$token=$_POST['token'];
	}

	if ($mode==""){
		$mode="list";
	}

	DispData($mode,$sort,$word,$key,$page,$lid,$token);

	return $function_ret;
} 

//=========================================================================================================
//名前 画面表示処理
//機能 Modeによって画面表示
//引数 $mode,$sort,$word,$key,$page,$lid
//戻値 なし
//=========================================================================================================
function DispData($mode,$sort,$word,$key,$page,$lid,$token)
{

	eval(globals());

	if($word != '') {
		$m1_mid0 = explode('-', $word);
		$m1_mid = $m1_mid0[0];
	}
	else {
		$m1_mid = '';
	}

	//各テンプレートファイル名
	$htmllist = "list.html";

	$filename=$htmllist;
	$fp=$DOCUMENT_ROOT.$filename;
	$str=@file_get_contents($fp);
	$str = MakeHTML($str,1,$lid);

	// 商談データ
	$StrSQL=" select * from DAT_SHODAN where ID = '" . $_GET['etc02'] . "'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$shodan_item = mysqli_fetch_assoc($rs);

	// 当該商談のファイル・ステータスを取得
	if($m1_mid != '') {
  	$StrSQL=" select * from DAT_FILESTATUS where SHODAN_ID = '" . $_GET['etc02'] . "' and MID1 = '" . $m1_mid . "' order by ID asc";
	}
	else {
  	$StrSQL=" select * from DAT_FILESTATUS where SHODAN_ID = '" . $_GET['etc02'] . "' order by ID asc";
	}
	//echo($StrSQL);
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$strMain = '';
	$category = '';
	$part0_key=array();
	while ($item = mysqli_fetch_assoc($rs)) {
		$tmp = '';

		// M1データ
		$StrSQL=" select * from DAT_M1 where MID = '" . $item['MID1'] . "'";
		$m1_rs=mysqli_query(ConnDB(),$StrSQL);
		$m1_item = mysqli_fetch_assoc($m1_rs);
		$m1_mid = $m1_item['MID'];

		// サプライヤーからの請求書は表示しない
		if($item['STATUS'] == '請求' && $item['S2_FILE'] == '') {
			continue;
		}

		if($category != $item['CATEGORY']) {
			$tmp .= '
				<div class="chat__status" style="background:' . showStatusColor($item['CATEGORY']) . ';">
					<p>ステータス：' . $item['CATEGORY'] . '</p>
				</div>
				<div style="clear:both;"></div>
			';
		}
		$category = $item['CATEGORY'];

		//分割払い対応
		$div_id=$item["DIV_ID"];
		$div_id_tmp="";
		$div_id_tmp=explode("-", $div_id);
		echo "<!--";
		var_dump($div_id_tmp);
		echo "-->";
		$part="";
		$disp_part="";
		$div_id_2part="";
		if($item["M2_PAY_TYPE"]!='Once' && count($div_id_tmp)==3){
			$part=$div_id_tmp[2];
			//$disp_part="分割 ".$part;
			echo "<!--part:$part-->";
			$div_id_2part=$div_id_tmp[0]."-".$div_id_tmp[1];
		}
		//echo "<!--disp_part:$disp_part-->";


		if($item["STATUS"]=="見積り送付"){
			if($item["M2_PAY_TYPE"]=="Milestone" && $part=="Part0"){
				//マイルストーンの場合はPart0はユーザには表示しない
				$part0_key[$div_id_2part]=$item["ID"];
				continue;
			}

			if($item["M2_PAY_TYPE"]=="Split" && $part!="Part0"){
				//2回払いの場合はPart0はユーザには表示しない
				continue;
			}
		}


		//「Scientist3 control No.」が設定されていたら整形
			$SCNo_ary=array(
				"SCNo_yy" => "", 
				"SCNo_mm" => "", 
				"SCNo_dd" => "", 
				"SCNo_cnt" => "", 
				"SCNo_else1" => "", 
				"SCNo_else2" => "", 
			);
			$m2_quote_no="";

			$SCNo_ary["SCNo_yy"]=$item["SCNo_yy"];
			$SCNo_ary["SCNo_mm"]=$item["SCNo_mm"];
			$SCNo_ary["SCNo_dd"]=$item["SCNo_dd"];
			$SCNo_ary["SCNo_cnt"]=$item["SCNo_cnt"];
			$SCNo_ary["SCNo_else1"]=$item["SCNo_else1"];
			$SCNo_ary["SCNo_else2"]=$item["SCNo_else2"];
			$SCNo_str=formatAlphabetId($SCNo_ary);
			$m2_quote_no=$item["M2_QUOTE_NO"];
		
		//マイルストーン払いの場合に、Item名も表示。
			$item_name="";
			if($item["M2_PAY_TYPE"]=='Milestone'){
				$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL where FILESTATUS_ID='".$item["ID"]."' order by ID desc;";
				//echo('<!--'.$StrSQL.'-->');
				$rs_dmile=mysqli_query(ConnDB(),$StrSQL);
				$item_dmile = mysqli_fetch_assoc($rs_dmile);
				$item_name=$item_dmile["M2_DETAIL_ITEM"];
			}

		//メッセージ用のサプライヤーデータ
			$StrSQL="SELECT MID,M1_DVAL01 FROM DAT_M1 WHERE MID='".$item["MID1"]."'";
			$sup_rs=mysqli_query(ConnDB(),$StrSQL);
			$sup_item=mysqli_fetch_assoc($sup_rs);

		switch($item['STATUS']) {
			case '問い合わせ':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . 'への問い合わせ
	  		      <br><br>
    		      <div>
								' . ($word != '' ? "
	    		      <a href='javascript:window.parent.open_mcontact2(\"/m_contact2/?type=問い合わせ&mode=disp_frame&key=".$item['ID']."\");'>問い合わせを確認</a>
								" : '
	    		      <a href="/m_contact2/?type=問い合わせ&mode=disp&key='.$item['ID'].'">問い合わせを確認</a>
								') . '
	     	    	</div>
 	     	  	</div>
	  	    </div>
				';
				break;
			case '見積り依頼':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . 'への見積り依頼
	  		      <br><br>
    		      <div>
								' . ($word != '' ? " 
	    		      <a href='javascript:window.parent.open_mcontact2(\"/m_contact2/?type=見積り依頼&mode=disp_frame&key=".$item['ID']."\");'>見積り依頼内容を確認する</a>
								" : '
	    		      <a href="/m_contact2/?type=見積り依頼&mode=disp&key='.$item['ID'].'">見積り依頼内容を確認する</a>
								') . '
	     	    	</div>
 	     	  	</div>
	  	    </div>
				';
				//$tmp .= '
 		    //	<div class="filestatus_content">
				//		<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		  //    <div>' . $m1_item['M1_DVAL01'] . 'への見積り依頼
	  		//      <br><br>
    		//      <div>
				//				' . ($word != '' ? " 
	    	//	      <a href='javascript:window.parent.open_mcontact2(\"/m_contact2/?type=見積り依頼&mode=disp_frame&key=".$item['ID']."\");'>Confirm 見積り書 請求</a>
				//				" : '
	    	//	      <a href="/m_contact2/?type=見積り依頼&mode=disp&key='.$item['ID'].'">Confirm 見積り書 請求</a>
				//				') . '
	     	//    	</div>
 	     	//  	</div>
	  	  //  </div>
				//';
				break;
			case '再見積り依頼':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . 'への再見積り依頼
	  		      <br><br>
    		      <div>
								' . ($word != '' ? " 
	    		      <a href='javascript:window.parent.open_mcontact2(\"/m_contact2/?type=再見積り依頼&mode=disp_frame&key=".$item['ID']."\");'>再見積り依頼内容を確認</a>
								" : '
	    		      <a href="/m_contact2/?type=再見積り依頼&mode=disp&key='.$item['ID'].'">再見積り依頼内容を確認</a>
								') . '
	     	    	</div>
 	     	  	</div>
	  	    </div>
				';
				break;
			case '見積り送付':

				if($item["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part!="Part1"){
						//マイルストーン払いの場合に、Item名も表示。
						//Part1以外にはRevise Quotationボタンを非表示
						$tmp .= '
 		    		<div class="filestatus_content">
							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
	  		      	<br><br>
    		      	<div>
	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>".$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']."-".$item_name." ".$disp_part."</a>
	     	    		</div>
 	     	  		</div>
	  	    	</div>
					";
						
					}else if($item["M2_PAY_TYPE"]=='Milestone' && $part!="" && $item_name!="" && $part=="Part1"){
						//マイルストーン払いの場合に、Item名も表示。
						//Part1にはRevise Quotationボタンを表示
						//Revise QuotationボタンにはPart0のkeyを使う
						if( isset($part0_key[$div_id_2part]) ){
							$str_part0_key=$part0_key[$div_id_2part];
						}else{
							break;
						}

						$tmp .= '
 		    		<div class="filestatus_content">
							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
	  		      	<br><br>
    		      	<div>
	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>".$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']."-".$item_name." ".$disp_part."</a>
	    		      　<a href='/m_contact2/?type=発注依頼&mode=new&key=".$str_part0_key."&m1_mid=".$m1_mid."&upd_mode=1' target='_top'>発注する</a>
	     	    		</div>
 	     	  		</div>
	  	    	</div>
					";
	
					}else if($item["M2_PAY_TYPE"]=='Split' && $part=="Part0"){
						//2回払いでPart0以外は上でcontinueしてる。
						//2回払いの場合はPart0しか表示しない。
						//2回払いの場合に、Part0にRevise Quotationボタンを表示
						$tmp .= '
 		    		<div class="filestatus_content">
							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
	  		      	<br><br>
    		      	<div>
	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>".$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']." ".$disp_part."</a>
	    		      　<a href='/m_contact2/?type=発注依頼&mode=new&key=".$item['ID']."&m1_mid=".$m1_mid."&upd_mode=1' target='_top'>発注する</a>
	     	    		</div>
 	     	  		</div>
	  	    	</div>
					";
					
					}else if($item["M2_PAY_TYPE"]=='Once'){
						//1回払いの場合に、Revise Quotationボタンを表示
						$tmp .= '
 		    		<div class="filestatus_content">
							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
	  		      	<br><br>
    		      	<div>
	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>".$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']." ".$disp_part."</a>
	    		      　<a href='/m_contact2/?type=発注依頼&mode=new&key=".$item['ID']."&m1_mid=".$m1_mid."&upd_mode=1' target='_top'>発注する</a>
	     	    		</div>
 	     	  		</div>
	  	    	</div>
					";
	
					}else{
						//例外があったら表示のみ
						$tmp .= '
 		    		<div class="filestatus_content">
							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
	  		      	<br><br>
    		      	<div>
	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>".$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']." ".$disp_part."</a>
	     	    		</div>
 	     	  		</div>
	  	    	</div>
					";
					}



/*
				if($item_name!="" && $part!="Part0"){
						$tmp .= '
 		    		<div class="filestatus_content">
							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
	  		      	<br><br>
    		      	<div>
	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>".$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']."-".$item_name." ".$disp_part."</a>
	    		      　<a href='/m_contact2/?type=発注依頼&mode=new&key=".$item['ID']."&upd_mode=1' target='_top'>発注する</a>
	     	    		</div>
 	     	  		</div>
	  	    	</div>
					";
				}else{
						$tmp .= '
 		    		<div class="filestatus_content">
							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
	  		      	<br><br>
    		      	<div>
	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>".$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']." ".$disp_part."</a>
	    		      　<a href='/m_contact2/?type=発注依頼&mode=new&key=".$item['ID']."&upd_mode=1' target='_top'>発注する</a>
	     	    		</div>
 	     	  		</div>
	  	    	</div>
					";

				}*/

//				if($item_name!="" && $part!="Part0"){
//						$tmp .= '
// 		    		<div class="filestatus_content">
//							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
//  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
//	  		      	<br><br>
//    		      	<div>
//	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>"
//	    		      	.$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']."-".$item_name." ".$disp_part."</a>
//	    		      　<a href='/m_contact2/?type=発注依頼&mode=new&key=".$item['ID']."&upd_mode=1' target='_top'>発注する</a>
//	     	    		</div>
// 	     	  		</div>
//	  	    	</div>
//					";
//				}else{
//						$tmp .= '
// 		    		<div class="filestatus_content">
//							<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
//  		      	<div>' . $m1_item['M1_DVAL01'] . "から見積りが届きました
//	  		      	<br><br>
//    		      	<div>
//	    		      	<a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=見積り送付&mode=disp_frame&key=".$item['ID']."\");'>"
//	    		      	.$m2_quote_no."(".$SCNo_str.") バージョン".$item['M2_VERSION']." ".$disp_part."</a>
//	    		      　<a href='/m_contact2/?type=発注依頼&mode=new&key=".$item['ID']."&upd_mode=1' target='_top'>発注する</a>
//	     	    		</div>
// 	     	  		</div>
//	  	    	</div>
//					";
//
//				}
				
				
				break;
			case '発注依頼':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . "に発注依頼を出しました
	  		      <br><br>
    		      <div>
	    		      <a href='javascript:window.parent.open_mcontact2(\"/m_contact2/?type=発注依頼&mode=disp_frame&key=".$item['ID']."\");'>発注書を確認</a>
	     	    	</div>
 	     	  	</div>
	  	    </div>
				";
				break;
			case '発注承認':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>決済者により発注が承認されました
 	     	  	</div>
	  	    </div>
				';
				break;
			case '発注否認':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>決済者により発注が否認されました
	  		      <br><br>
    		      <div>
	    		    	<a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$m1_mid.'" target="_top">見積り依頼を追加する場合はこちら</a>
	     	    	</div>
 	     	  	</div>
	  	    </div>
				';
				break;
			case '受注承認':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . 'より受注が承認されました
 	     	  	</div>
	  	    </div>
				';
				break;
			case 'データ納品':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . "から納品がありました
	  		      <br><br>
    		      <div>
	    		      <a href='javascript:window.parent.open_mcontact2(\"/m_contact1/?type=データ納品&mode=disp_frame&key=".$item['ID']."\");'>納品データを確認</a>
	     	    	</div>
 	     	  	</div>
	  	    </div>
				";
				break;
			case '物品納品':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . 'から納品（物品納品）がありました
 	     	  	</div>
	  	    </div>
				';
				break;
			case '納品確認':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $m1_item['M1_DVAL01'] . 'からの納品を確認しました
 	     	  	</div>
	  	    </div>
				';
				break;
			case '請求':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>' . $item['S2_MESSAGE'] . "
	  		      <br><br>
    		      <div>
	    		      <a href='javascript:window.parent.open_mcontact2(\"/m_contact2/?type=請求&mode=disp_frame&key=".$item['ID']."\");'>Confirm 請求書</a>
	     	    	</div>
 	     	  	</div>
	  	    </div>
				";
				break;
			case '完了':
				$tmp .= '
 		    	<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      <div>お振込みが確認できました
          	<br>
						この度はScientist3をご利用いただき誠にありがとうございました
 	     	  	</div>
	  	    </div>
				';
				break;
			case 'キャンセル依頼':
				$tmp.='
					<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>
  		      		発注キャンセル依頼中です。
 	     	  	</div>
	  	    </div>
			';
				break;
			case 'サプライヤーキャンセル承認':
				$tmp.='
					<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>
  		      		'.$sup_item["M1_DVAL01"].'が発注のキャンセル依頼を承認しました。キャンセル手続きが完了するまでお待ちください。
 	     	  	</div>
	  	    </div>
			';
				break;
			case 'サプライヤーキャンセル否認':
				$tmp.='
					<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>
  		      		'.$sup_item["M1_DVAL01"].'が発注のキャンセル依頼を否認しました。
 	     	  	</div>
	  	    </div>
			';
				break;
			case 'キャンセル承認':
				$tmp.='
					<div class="filestatus_content">
						<div class="filestatus_datetime">' . substr($item['NEWDATE'], 0, 16) . '</div>
  		      	<div>
  		      		発注はキャンセルされました。誤ってキャンセルした場合は、<a href="/contact/" target="_blank">管理者</a>にご連絡ください。
 	     	  	</div>
	  	    </div>
			';
				break;
		}

		$strMain=$strMain.$tmp.chr(13);

	} 

	if($word != '') {

	// ステータスが変わったら表示されないメッセージはこちら
	switch($shodan_item['STATUS']) {
		case '問い合わせ':
			$strMain .= '
        <div class="filestatus_content">
          <div>' . $m1_item['M1_DVAL01'] . 'に見積り依頼を送る場合はこちら
            <br><br>
            <div>
	    		    <a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$m1_mid.'" target="_top">見積り依頼を作成</a>
            </div>
          </div>
        </div>
			';
			break;
		case '再見積り依頼':
		case '見積り依頼':
			$strMain .= '
        <div class="filestatus_content">
          <div>' . $m1_item['M1_DVAL01'] . 'からの見積りをお待ちください
            <br><br>
            <div>
	    		    <a href="/m_contact2/?type=見積り依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$m1_mid.'" target="_top">見積り依頼を追加する場合はこちら</a>
            </div>
          </div>
        </div>
			';
			break;
		case '見積り送付':
			$strMain .= '
        <div class="filestatus_content">
          <div>' . $m1_item['M1_DVAL01'] . 'へのご発注はこちら
            <br><br>
            <div>
	    		    <a href="/m_contact2/?type=発注依頼&mode=new&shodan_id='.$_GET['etc02'].'&m1_mid='.$m1_mid.'" target="_top">発注する</a>
            </div>
          </div>
        </div>
			';
			break;
		case '発注依頼':
			$strMain .= '
        <div class="filestatus_content">
          <div>決済者の発注承認待ちです
          </div>
        </div>
			';
			break;
		case '決済者発注承認':
			$strMain .= '
        <div class="filestatus_content">
          <div>' . $m1_item['M1_DVAL01'] . 'からの受注承認をお待ちください
          </div>
        </div>
			';
			break;
		case '受注承認':
			$strMain .= '
        <div class="filestatus_content">
          <div>受注が承認されました。' . $m1_item['M1_DVAL01'] . 'からの納品をお待ちください。
          </div>
        </div>
			';
			break;
		case 'データ納品':
		case '物品納品':
			$strMain .= '
        <div class="filestatus_content">
          <div>' . $m1_item['M1_DVAL01'] . 'からの納品を承認してください
						<br>
						※月末日は納品承認を行えません
            <br><br>
            <div>
							<a href="javascript:parent.shodan_nohin('.$_GET['etc02'].');">納品を承認する</a>
            </div>
          </div>
        </div>
			';
			break;
		case '納品確認':
			$strMain .= '
        <div class="filestatus_content">
          <div>請求書発行の準備をしております。しばらくお待ちください
          </div>
        </div>
			';
			break;
		case '請求':
			$strMain .= '
        <div class="filestatus_content">
          <div>お振込みをお願いいたします
          </div>
        </div>
			';
			break;
		case '完了':
			$strMain .= '
        <div class="filestatus_content">
          <div>
            <a href="/m_contact2/?mode=new&m1_id=' . $m1_item['ID'] . '" target="_top">' . $m1_item['M1_DVAL01'] . 'への新規のご相談はこちらから</a>
          </div>
        </div>
			';
			break;
	}

	} // word != '' (サプライヤー指定)　のif

	$str=str_replace("[FILESTATUS_LIST]",$strMain,$str);

	$str=str_replace("[KEY]",$key,$str);
	$str=str_replace("[LID]",$lid,$str);

	// CSRFトークン生成
	if($token==""){
		$token=htmlspecialchars(session_id());
		$_SESSION['token'] = $token;
	}
	$str=str_replace("[TOKEN]",$token,$str);
	$str=str_replace("[ETC02]",$_GET['etc02'],$str);

	$str=str_replace("[BASE_URL]",BASE_URL,$str);

	if(isset($_GET['frame']) && $_GET['frame'] == 'on'){
		$str=DispParam($str, "FRAME-ON");
		$str=DispParamNone($str, "FRAME-OFF");
	} else {
		$str=DispParamNone($str, "FRAME-ON");
		$str=DispParam($str, "FRAME-OFF");
	}

	print $str;

	return $function_ret;
} 

//=========================================================================================================
//名前 データリクエストパラメータ処理
//機能 データリクエストパラメータの処理と画像の保存
//引数 $obj,$a,$b,$key,$mode
//戻値 $function_ret;
//=========================================================================================================
function RequestData($obj,$a,$b,$key,$mode)
{
	eval(globals());

	// HTMLエスケープ処理（リクエストパラメータ）
	// クロスサイトスクリプティング対策
	for ($i=0; $i<=$FieldMax; $i=$i+1) {
		if ($FieldAtt[$i]==3) {
			if(strstr($_POST[$FieldName[$i]],"\t") == true) {
				$FieldValue[$i]=htmlspecialchars($_POST[$FieldName[$i]]);
			} else {
				$FieldValue[$i]="";
				for ($j=0; $j<count($_POST[$FieldName[$i]]); $j=$j+1) {
					if ($j!=0) {
						$FieldValue[$i]=$FieldValue[$i]."\t";
					}
					$FieldValue[$i]=$FieldValue[$i].$_POST[$FieldName[$i]][$j];
				}
			}
		} else {
			$FieldValue[$i]=htmlspecialchars(str_replace("\\","",$_POST[$FieldName[$i]]));
		}
		if ($FieldAtt[$i]==4 && $mode=="save") {
			$exts = explode("[/\\.]", $_FILES["EP_".$FieldName[$i]]['name']);
			$n = count($exts) - 1;
			$extention = $exts[$n];
			if ($extention=="jpeg") {
				$extention="jpg";
			} 

			if ($extention!="" && !!isset($extention)) {
				$filename=$FieldName[$i]."-".date("YmdHis").".".$extention;
				$FieldValue[$i]=$filepath1.$filename;
			} else {
				if ($FieldValue[$i]=="" || !isset($FieldValue[$i])) {
					$filename="s.gif";
					$FieldValue[$i]=$filepath1.$filename;
				} 
			} 
			if ($_POST["DEL_IMAGE_".$FieldName[$i]]=="on") {
				$filename="s.gif";
				$FieldValue[$i]=$filepath1.$filename;
			}
			if ($filename!="s.gif" && ($extention!="" && !!isset($extention))) {
				move_uploaded_file($_FILES["EP_".$FieldName[$i]]["tmp_name"], "data/".$filename);
				pic_resize("data/".$filename, 800,800);
			} 
		} 
	}

	return $function_ret;
} 

//=========================================================================================================
//名前 DB読み込み
//機能 DBからレコードを取得
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function LoadData($key)
{
	eval(globals());

	// SQLインジェクション対策
	// HTMLエスケープ処理（SQL読み込み）
	$StrSQL="SELECT * FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);

	if ($rs==true) {
		$item = mysqli_fetch_assoc($rs);
		for ($i=0; $i<=$FieldMax; $i=$i+1) {
			$FieldValue[$i]=htmlspecialchars($item[$FieldName[$i]]);
		}
	} 

	return $function_ret;
} 

//=========================================================================================================
//名前 DB削除
//機能 DBからレコードを削除
//引数 $key
//戻値 $function_ret
//=========================================================================================================
function DeleteData($key)
{
	eval(globals());

	// SQLインジェクション対策
	$StrSQL="DELETE FROM ".$TableName." WHERE ".$FieldName[$FieldKey]."='".mysqli_real_escape_string(ConnDB(),$key)."';";
	if (!(mysqli_query(ConnDB(),$StrSQL))) {
		die;
	}

	return $function_ret;
} 

//=========================================================================================================
//名前 タブ区切りデータのエクスポート処理
//機能 タブ区切りテキストデータ（UTF-8→ShiftJIS）のエクスポート処理
//引数 なし
//戻値 なし
//=========================================================================================================
function ExportData()
{
	eval(globals());

	$csv_data = "";

	$StrSQL="SELECT * FROM ".$TableName." order by ID";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item=mysqli_num_rows($rs);
	if($item<>"") {
		$str="ID	FIELD01	FIELD02	FIELD03	FIELD04	FIELD05	FIELD06	FIELD07	FIELD08	FIELD09	FIELD10"."\r\n";
		$csv_data .= $str;
		while ($item = mysqli_fetch_assoc($rs)) {
			$str="";
			for ($i=0; $i<=$FieldMax; $i=$i+1){
				if ($i!=0){
					$str=$str."\t";
				}
				$str=$str.$item[$FieldName[$i]];
			}

			$str=str_replace("\r\n","",$str);
			$str=str_replace("\r","",$str);
			$str=str_replace("\n","",$str)."\r\n";
			$csv_data .= $str;
		} 
		$csv_data = mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=data.csv");
		echo($csv_data);
	} 

	return $function_ret;
} 

//=========================================================================================================
//名前 タブ区切りデータのインポート処理
//機能 タブ区切りテキストデータ（ShiftJIS→UTF-8）のエクスポート処理
//引数 なし
//戻値 なし
//=========================================================================================================
function ImportData($obj,$a,$b,$key,$mode)
{
	eval(globals());

	$fp = fopen($_FILES['importfile']['tmp_name'], "r");
	$txt = fgets($fp);

	// SQLインジェクション対策
	while (!feof($fp)) {
		$txt = fgets($fp);
		$txt=str_replace("\"","",$txt);
		$cols=explode("\t",$txt);
		if($cols[0]<>""){
			$StrSQL="SELECT * FROM ".$TableName." where ID='".mysqli_real_escape_string(ConnDB(),$cols[0])."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item=mysqli_num_rows($rs);
			if($item=="") {
				$StrSQL="INSERT INTO ".$TableName." (";
				for ($j=1; $j<=$FieldMax; $j++){
					if ($j!=1){
						$StrSQL.=",";
					} 
					$StrSQL.="`".$FieldName[$j]."`";
				}
				$StrSQL.=") values (";
				for ($j=1; $j<=$FieldMax; $j++){
					if ($j!=1){
						$StrSQL.=",";
					} 
					$StrSQL.="'".str_replace("'","''",$cols[$j])."'";
				}
				$StrSQL.=")";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			} else {
				if ($cols[1]!="delete"){
					$StrSQL="UPDATE ".$TableName." SET ";
					for ($j=1; $j<=$FieldMax; $j++) {
						if ($j!=1){
							$StrSQL.=",";
						} 
						$StrSQL.="`".$FieldName[$j]."`='".str_replace("'","''",$cols[$j])."'";
					}
					$StrSQL.=" WHERE ".$FieldName[$FieldKey]."='".$cols[0]."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} else {
					$StrSQL="DELETE FROM ".$TableName." WHERE ID='".$cols[0]."'";
					if (!(mysqli_query(ConnDB(),$StrSQL))) {
						die;
					}
				} 
			} 
		}
	}

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
