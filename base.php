<?php
	session_start();
	ini_set( 'display_errors', 0 );
        date_default_timezone_set('Asia/Tokyo');

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function MakeHTML($str,$mode,$lid)
{
	//extract($GLOBALS);
	eval(globals());

	if ($lid=="99999"){

		if($_SESSION['MATT'] == "2"){

			$cliendId = $_COOKIE['cliendId'];
			//ログインデータ削除
			$StrSQL=" DELETE FROM DAT_SESSION ";
			$StrSQL.=" WHERE MID = '".$_SESSION['MID']."'";
			$StrSQL.=" AND CLIENT_ID = '".$cliendId."'";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

			setcookie('cliendId', "", time()-60); //クッキー削除
		}

		$_SESSION['MATT'] = "";
		$_SESSION['M-ID']="";
		$_SESSION['HID']="";
		$_SESSION['MID']="";
		$lid="";
	}
 
	// Researchersでログイン中の場合、セッション管理
	$loginerr = 0;

	if($_SESSION['MATT'] == "2"){


		$cliendId = $_COOKIE['cliendId'];
		// var_dump("cliendId:".$cliendId);

		//セッション情報の取得(タイムアウトは１時間とする)
		$StrSQL="SELECT CLIENT_ID,str_to_date(LIST_ACCESS_TIME ,'%Y/%m/%d %H:%i:%s') as DT ";
		$StrSQL.=" FROM DAT_SESSION where MID='".$_SESSION['MID']."' ";
		//$StrSQL.=" and DATEDIFF(str_to_date(LIST_ACCESS_TIME ,'%Y/%m/%d %H:%i:%s'), CURRENT_DATE() ) < 1 ";
		$StrSQL.=" and TIMESTAMPDIFF( MINUTE, str_to_date(LIST_ACCESS_TIME, '%Y/%m/%d %H:%i:%s'), CURRENT_TIMESTAMP ()) < 60";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$cnt=mysqli_num_rows($rs);
		if($cnt>0){
			$item=mysqli_fetch_assoc($rs);

			//クライアントIDが異なる場合、異なるユーザ
			if($cliendId!=$item["CLIENT_ID"]){
				// TODO 一時的に無効
				// $loginerr = 1;
				// $filename = "../loginerr.html";
				// $fp=$filename;
				// $str=@file_get_contents($fp);
				// $str=str_replace("[BASE_URL]",BASE_URL,$str);
				// //セッションクリア
				// $_SESSION['MATT'] = "";
				// $_SESSION['M-ID']="";
				// $_SESSION['HID']="";
				// $_SESSION['MID']="";
				// $lid="";
			} else {
				// 日時を更新
				$StrSQL=" UPDATE DAT_SESSION ";
				$StrSQL.=" SET LIST_ACCESS_TIME = '".date('Y/m/d H:i:s')."'";
				$StrSQL.=" ,   SESSION_ID = '".session_id()."'";
				$StrSQL.=" WHERE MID = '".$_SESSION['MID']."'";
				$StrSQL.=" AND CLIENT_ID = '".$cliendId."'";
				if (!(mysqli_query(ConnDB(),$StrSQL))) {
					die;
				}
			}
		} else {
			 
			// クッキー未作成の場合
			if($cliendId==""){
				$cliendId = date('YmdHis').rand();
				setcookie('cliendId',$cliendId,time()+60*60*24, "/"); //有効期限は一日
			}

			//ログインデータ削除
			$StrSQL=" DELETE FROM DAT_SESSION ";
			$StrSQL.=" WHERE MID = '".$_SESSION['MID']."'";
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

			//初回ログインの場合
			$StrSQL="INSERT ";
			$StrSQL.="INTO DAT_SESSION (  ";
			$StrSQL.="	  SESSION_ID ";
			$StrSQL.="	, MID ";
			$StrSQL.="	, CLIENT_ID ";
			$StrSQL.="	, LIST_ACCESS_TIME ";
			$StrSQL.=")  ";
			$StrSQL.="VALUES (  ";
			$StrSQL.="	  '".session_id()."' ";
			$StrSQL.="	, '".$_SESSION['MID']."' ";
			$StrSQL.="	, '".$cliendId."' ";
			$StrSQL.="	, '".date('Y/m/d H:i:s')."' ";
			$StrSQL.=") ";
			
			if (!(mysqli_query(ConnDB(),$StrSQL))) {
				die;
			}

		}
	}

// プラン関連
/*
	if($_SESSION['HID']!="" && $_SESSION['PLAN']==""){
		$url=$_SERVER['REQUEST_URI'];
		if($url=="/v_iine/" || $url=="/v_search/" || $url=="/searchv/" || $url=="/v_fav/" || $url=="/v_message/" || $url=="/v_editj/"){
			$StrSQL="SELECT ETC06 from DAT_HOSPITAL where HID='".$_SESSION['HID']."';";
			$rs=mysqli_query(ConnDB(),$StrSQL);
			$item = mysqli_fetch_assoc($rs);
			if($item['ETC06']==""){
				$fp="../planerr0.html";
			} else {
				$fp="../planerr1.html";
			}
			$str=@file_get_contents($fp);
		}
	}
*/
// プラン関連



//$_SESSION['MATT']でテンプレートを制御

	$cnt=0;

	if($_SESSION['MATT']!=""){
		$str=str_replace("header.html", "header".$_SESSION['MATT'].".html", $str);
	}

	if($_SESSION['MATT'] == "1"){
		$str=str_replace("head.html", "head1.html", $str);
	}
	else {
		$str=str_replace("head.html", "head2.html", $str);
	}
	

	while(strpos($str, "<!--template=")>0 && $cnt<20){
		$tmpl=GetTemplateFileName($str, strpos($str, "<!--template="));

		$url = explode("/", $_SERVER["REQUEST_URI"]);

		if(count($url)==2){
			$fp=str_replace("/common/", "./common/", $tmpl);
		} elseif(count($url)==3){
			$fp=str_replace("/common/", "../common/", $tmpl);
		} elseif(count($url)==4){
			$fp=str_replace("/common/", "../common/", $tmpl);
		} elseif(count($url)==5){
			$fp=str_replace("/common/", "../common/", $tmpl);
		} else {
			$fp=str_replace("/common/", "../../common/", $tmpl);
		}

		$tmp=@file_get_contents($fp);
		$str=str_replace("<!--template=".$tmpl."-->", $tmp, $str);
		$cnt=$cnt+1;
	}

	if($_SESSION['MID']==""){
		$str=str_replace("[LOGINM-S]","<!--",$str);
		$str=str_replace("[LOGINM-E]","-->",$str);
		$str=str_replace("[LOGOUTM-S]","",$str);
		$str=str_replace("[LOGOUTM-E]","",$str);
		$str=str_replace("[L-M-ID]","",$str);
		$str=str_replace("[L-MID]","",$str);
		$str=str_replace("[L-MNAME]","",$str);
		// $str=DispParamNone($str, "LOGINM");
		// $str=DispParam($str, "LOGOUTM");
	} else {
		$str=str_replace("[LOGINM-S]","",$str);
		$str=str_replace("[LOGINM-E]","",$str);
		$str=str_replace("[LOGOUTM-S]","<!--",$str);
		$str=str_replace("[LOGOUTM-E]","-->",$str);
		$str=str_replace("[L-M-ID]",$_SESSION['M-ID'],$str);
		$str=str_replace("[L-MID]",$_SESSION['MID'],$str);
		$str=str_replace("[L-MNAME]",$_SESSION['MNAME'],$str);
		// $str=DispParam($str, "LOGINM");
		// $str=DispParamNone($str, "LOGOUTM");

		$StrSQL="SELECT ID FROM DAT_MESSAGE where AID like '%".$_SESSION['MID']."%' and RID<>'".$_SESSION['MID']."' and (NOREAD is null or NOREAD='') ";
		//2020/12/28 gaosan ADD START
	    $StrSQL .= " AND NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = SUBSTRING(DAT_MESSAGE.AID,1,7)) ";
	    $StrSQL .= " AND NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = SUBSTRING(DAT_MESSAGE.AID,9,7)) ";
		//2020/12/28 gaosan ADD END
// var_dump($StrSQL);
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item>0){
			$str=str_replace("[MIDOKU]",$item,$str);
			$str=DispParam($str, "MIDOKU");
		} else {
			$str=str_replace("[MIDOKU]","",$str);
			$str=DispParamNone($str, "MIDOKU");
		}

		$StrSQL="SELECT ID FROM DAT_IINE where MIDT='".$_SESSION['MID']."' ";
		// 2021.03.15 yamamoto いいね既読フラグ導入
		$StrSQL .= " and (ETC01 is null or ETC01 != '既読') ";
		//2020/12/28 gaosan ADD START
		$StrSQL .= " and NOT EXISTS (SELECT * FROM DAT_BL WHERE DAT_BL.MID1 = '" . $_SESSION['MID'] . "' and DAT_BL.MID2 = DAT_IINE.MID); ";
		//2020/12/28 gaosan ADD END

		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		if($item>0){
			$str=str_replace("[IINECNT]",$item,$str);
			$str=DispParam($str, "IINECNT");
		} else {
			$str=str_replace("[IINECNT]","",$str);
			$str=DispParamNone($str, "IINECNT");
		}
	}


	//カテゴリー情報
	$catetags="";
	$big="";
	$smalls="";
	$StrSQL="SELECT * FROM DAT_CCATE2 ORDER BY BIG,cast(sort as SIGNED )";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	while ($item = mysqli_fetch_assoc($rs)) {
		if($big==""){
			$big=$item["BIG"];
		}

		if($big!=$item["BIG"]){
			$catetags.="<input type=\"hidden\" class=\"categorytags\" name=\"".$big."\" value=\"".$smalls."\" >\n";
			$smalls="";
			$big=$item["BIG"];
		}

		if($smalls!=""){
			$smalls.="::";
		}
		$smalls.=$item["SMALL"];
	}

	if($big!=""){
		$catetags.="<input type=\"hidden\" class=\"categorytags\" name=\"".$big."\" value=\"".$smalls."\" >\n";
	}


	$str=str_replace("[CATEGORY_TAGLIST]",$catetags,$str);


	//ライトプランの場合、使用不可
	$standardFlag = 0;
	if($_SESSION['MATT'] == "1"){
		$StrSQL="SELECT M1_DVAL13 from DAT_M1 where MID='".$_SESSION['MID']."' and ENABLE='ENABLE:公開中';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		if($item["M1_DVAL13"]=="M1_DVAL13:スタンダード"){
			$standardFlag = 1;
		}
	} else if($_SESSION['MATT'] == "2"){
		$StrSQL="SELECT M2_DVAL14 from DAT_M2 where MID='".$_SESSION['MID']."' and ENABLE='ENABLE:公開中';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		if($item["M2_DVAL14"]=="M2_DVAL14:スタンダード"){
			$standardFlag = 1;
		}
	}

	$str=DispParam($str, "STANDARD");
	// if($standardFlag==1){
	// 	$str=DispParam($str, "STANDARD");
	// } else {
	// 	$str=DispParamNone($str, "STANDARD");
	// }

	/*
	// yamamoto
	// Supplierは登録状態によって異なる
	if($_SESSION['MATT'] == '1') {
		// $StrSQL="SELECT M1_DRDO01 from DAT_M1 where MID='".$_SESSION['MID']."' and ENABLE='ENABLE:公開中';";
		$StrSQL="SELECT M1_DRDO01 from DAT_M1 where MID='".$_SESSION['MID']."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item = mysqli_fetch_assoc($rs);
		$hname = '';
		switch($item['M1_DRDO01']) {
			case 'M1_DRDO01:仮登録中':
				$hname = '仮登録中';
				break;
			case 'M1_DRDO01:審査依頼':
				$hname = '審査中';
				break;
			case 'M1_DRDO01:要再審査':
				$hname = '審査中';
				break;
			case 'M1_DRDO01:本登録不可':
				$hname = '本登録不可';
				break;
			case 'M1_DRDO01:本登録':
				$hname = $_SESSION['MNAME'] . '様';
				break;
		}
		$str=str_replace("[L-HNAME]",$hname,$str);
	}
	else {
		$str=str_replace("[L-HNAME]",$_SESSION['MNAME'],$str);
	}
	*/
	//$str=str_replace("[L-HNAME]",$_SESSION['MID'],$str);
	$str=str_replace("[L-HNAME]",convert_mid($_SESSION['MID']),$str);


	//ヘッダーの契約フラグ
	if($_SESSION['MATT'] == "2"){
		$StrSQL="SELECT M2_DSEL02 from DAT_M2 where MID='".$_SESSION['MID']."' and ENABLE='ENABLE:公開中';";
		$rs_keyaku=mysqli_query(ConnDB(),$StrSQL);
		$item_keyaku = mysqli_fetch_assoc($rs_keyaku);
		$tmp_kenkyu=str_replace("M2_DSEL02:","",$item_keyaku["M2_DSEL02"]);
		$str=str_replace("[D-M2-M2_DSEL02]", $tmp_kenkyu,$str);

	}
	


// プラン関連
/*
	if(strstr($_SESSION['PLAN'], "Basic")==true){
		$str=DispParam($str, "PLANB");
		$str=DispParamNone($str, "PLANP");

		$su1=0;
		$su2=0;
	} else {
		$str=DispParamNone($str, "PLANB");
		$str=DispParam($str, "PLANP");

		$d1=date("d");
		$d2=substr($_SESSION['LDATE'],-2,2);
		if(intval($d1)>=intval($d2)){
			$ds=date("Y/m")."/".$d2;
			$de=date("Y/m", strtotime('+1 month'))."/".$d2;
		} else {
			$ds=date("Y/m", strtotime('-1 month'))."/".$d2;
			$de=date("Y/m")."/".$d2;
		}
		$StrSQL="SELECT ID FROM DAT_IINE where HID='".$_SESSION['HID']."' and NEWDATE>='".$ds."' and NEWDATE<'".$de."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		$su1=30-$item;

		$StrSQL="SELECT ID FROM DAT_SCOUT where HID='".$_SESSION['HID']."' and NEWDATE>='".$ds."' and NEWDATE<'".$de."';";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$item=mysqli_num_rows($rs);
		$su2=10-$item;

	}
	$str=str_replace("[IINEZAN]",$su1,$str);
	$str=str_replace("[SCOUTZAN]",$su2,$str);

	$str=str_replace("<li><a href=\"/v_search/\" class=\"active\">残りいいね数 30回（更新日毎月日）</a></li>","",$str);
	$str=str_replace("<li><a href=\"/v_search/\" class=\"active\">有効期限 </a></li>","",$str);
*/
// プラン関連

	if($_SESSION['MATT'] == "1"){
		$str=DispParam($str, "BASE_MATT_1");
	} else if($_SESSION['MATT'] == "2"){
		$str=DispParam($str, "BASE_MATT_2");
	} else {
		$str=DispParam($str, "BASE_MATT_NONE");
	}
	$str=DispParamNone($str, "BASE_MATT_1");
	$str=DispParamNone($str, "BASE_MATT_2");
	$str=DispParamNone($str, "BASE_MATT_NONE");


	//ログインエラーの場合
	if($loginerr == 1){
		print $str;
		exit;
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
function ChatText($str)
{
	//extract($GLOBALS);
	eval(globals());

	$text = htmlspecialchars($str);
//	$text = str_replace("\r\n","",$text);
	$text = str_replace("\n","<br />",$str);
//	$text = mb_ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $text);
//	$text = mb_ereg_replace("[[:alnum:]]+@[[:alnum:]]+.+[[:alnum:]]", "<a href=\"mailto:\\0\">\\0</a>", $text);

	return $text;
} 

//=========================================================================================================
//名前
//機能
//引数
//戻値
//=========================================================================================================
function GetTemplateFileName($tmp, $pos){

	$pos=$pos+13;

	$str="";
	for($i=0; $i<99; $i++){
		$s = substr($tmp, $pos+$i, 1);
		$s2 = substr($tmp, $pos+$i+1, 1);
		if($s!="-" || $s2!="-"){
			$str = $str.$s;
		} else {
			break;
		}
	}

	$function_ret=$str;

	return $function_ret;

}

//=========================================================================================================
//名前
//機能
//引数
//戻値
//=========================================================================================================
function globals(){

	$vars = array();
	foreach($GLOBALS as $k => $v){
		$vars[] = "$".$k;
	}
	return "global ".join(",", $vars).";";
}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function LeftText($str, $n)
{
	if(mb_strlen($str,"UTF-8")>$n){
		return mb_substr($str, 0, $n,"UTF-8")."…";
	} else {
		return $str;
	}

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispParam($str, $tn)
{

	$str=str_replace("[S-".$tn."]","",$str);
	$str=str_replace("[E-".$tn."]","",$str);

	return $str;

}

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function DispParamNone($str, $tn)
{

	$cnt=0;
	while($cnt<20){
		if(strstr($str, "[S-".$tn."]")==true){
			$a=mb_strpos($str, "[S-".$tn."]");
			$b=mb_strpos($str, "[E-".$tn."]");
			$l=mb_strlen($str);
			$t=mb_strlen($tn);
			if(is_numeric($a) && is_numeric($b)){
				$str=mb_substr($str, 0, $a).mb_substr($str, $b+$t+4, $l);
			}
		}
		$cnt++;
	}
	return $str;

}


function pic_resize($orig_file, $resize_width, $resize_height)
{
	// GDライブラリがインストールされているか
	if (!extension_loaded('gd')) {
		// エラー処理
		return false;
	}

	// 画像情報取得
	$result = getimagesize($orig_file);
	list($orig_width, $orig_height, $image_type) = $result;

	// 画像をコピー
	switch ($image_type) {
		// 1 IMAGETYPE_GIF
		// 2 IMAGETYPE_JPEG
		// 3 IMAGETYPE_PNG
		case 1: $im = imagecreatefromgif($orig_file); break;
		case 2: $im = imagecreatefromjpeg($orig_file);  break;
		case 3: $im = imagecreatefrompng($orig_file); break;
		default: //エラー処理
			return false;
	}

	if($orig_width>$orig_height){
		$resize_height=$orig_height*($resize_width/$orig_width);
	} else {
		$resize_width=$orig_width*($resize_height/$orig_height);
	}

	// コピー先となる空の画像作成
	$new_image = imagecreatetruecolor($resize_width, $resize_height);
	if (!$new_image) {
		// エラー処理
		// 不要な画像リソースを保持するメモリを解放する
		imagedestroy($im);
		return false;
	}

	// GIF、PNGの場合、透過処理の対応を行う
	if (($image_type == 1) OR ($image_type==3)) {
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
		$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
		imagefilledrectangle($new_image, 0, 0, $resize_width, $resize_height, $transparent);
	}

	// コピー画像を指定サイズで作成
	if (!imagecopyresampled($new_image, $im, 0, 0, 0, 0, $resize_width, $resize_height, $orig_width, $orig_height)) {
		// エラー処理
		// 不要な画像リソースを保持するメモリを解放する
		imagedestroy($im);
		imagedestroy($new_image);
		return false;
	}

	ImageJPEG($new_image, $orig_file);
	// コピー画像を保存
	// $new_image : 画像データ
	// $new_fname : 保存先と画像名
	// クオリティ

	switch ($image_type) {
		// 1 IMAGETYPE_GIF
		// 2 IMAGETYPE_JPEG
		// 3 IMAGETYPE_PNG
//            case 1: $result = imagegif($new_image, $new_fname, $quality); break;
		//          case 2: $result = imagejpeg($new_image, $new_fname, $quality); break;
		//        case 3: $result = imagepng($new_image, $new_fname, $quality); break;
		default: //エラー処理
			return false;
	}

	if (!$result) {
		// エラー処理
		// 不要な画像リソースを保持するメモリを解放する
		imagedestroy($im);
		imagedestroy($new_image);
		return false;
	}

	// 不要になった画像データ削除
	imagedestroy($im);
	imagedestroy($new_image);

}

//=========================================================================================================
//名前 
//機能 メールテンプレートを取得して定数を埋め込む
//引数 
//戻値 
//=========================================================================================================
function GetMailTemplate($mailname)
{
	eval(globals());

  $maildata = array();

	$StrSQL="SELECT TITLE,BODY from DAT_MAIL where MAILNAME='MAILNAME:".$mailname."';";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	if($item>0){
		$maildata['TITLE'] = $item['TITLE'];
		$maildata['BODY'] = $item['BODY'];

		$maildata['BODY']=str_replace("[BASE_URL]",BASE_URL,$maildata['BODY']);
		$maildata['BODY']=str_replace("[SENDER_EMAIL]",SENDER_EMAIL,$maildata['BODY']);
		$maildata['BODY']=str_replace("[SENDER_NAME]",SENDER_NAME,$maildata['BODY']);
		$maildata['BODY']=str_replace("[WEBSITE_NAME]",WEBSITE_NAME,$maildata['BODY']);
		$maildata['BODY']=str_replace("[COMPANY_NAME]",COMPANY_NAME,$maildata['BODY']);
		$maildata['BODY']=str_replace("[M1_CAPTION]",M1_CAPTION,$maildata['BODY']);
		$maildata['BODY']=str_replace("[M2_CAPTION]",M2_CAPTION,$maildata['BODY']);

		$maildata['TITLE']=str_replace("[WEBSITE_NAME]",WEBSITE_NAME,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[O1_CAPTION]",O1_CAPTION,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[O2_CAPTION]",O2_CAPTION,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[M1_CAPTION]",M1_CAPTION,$maildata['TITLE']);
		$maildata['TITLE']=str_replace("[M2_CAPTION]",M2_CAPTION,$maildata['TITLE']);
	}

	return $maildata;
} 
//=========================================================================================================
//名前 ステータスをログインユーザーに応じて日本語→英語に変換
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function showStatus($val){
	$key = str_replace("STATUS:","",$val);
	$status=$key;
	if($_SESSION['MATT']=="1"){
		switch($key){
			case "問い合わせ":
				$status="Inquiry";
				break;
			case "見積り":
				$status="Estimate";
				break;
			case "発注":
				$status="Order placement";
				break;
			case "納品":
				$status="Delivery";
				break;
			case "請求":
				$status="Invoice";
				break;
			case "完了":
				$status="Done.";
				break;
			case "キャンセル":
			case "サプライヤーキャンセル承認":
			case "キャンセル承認":
			case "キャンセル承認（請求あり）":
				$status="Cancellation";
				break;
			case "辞退":
				$status="Declination";
				break;
			case "データ納品":
				//$status="Data delivery";
				$status="Delivery";
				break;
			case "物品納品":
				//$status="Delivery of goods";
				$status="Delivery";
				break;
			case "見積り依頼":
				$status="Request for quotation";
				break;
			case "再見積り依頼":
				$status="Request for Re-quotation";
				break;
			case "運営手数料追加":
				$status="Estimate";
				break;
			case "見積り送付":
				$status="Estimate";
				break;
			case "発注否認":
				$status="Order rejection";
				break;
			case "納品確認":
				$status="Delivery";
				break;
			case "実施中":
			case "キャンセル依頼":
			case "サプライヤーキャンセル承認（追加見積り）":
			case "サプライヤーキャンセル否認":
				$status="In progress";
				break;
			case "完了後対応":
				$status="Done";
				break;
		}
	} else {

	}

	return $status;



}

//=========================================================================================================
//名前 テキストに含まれるステータスをすべてログインユーザーに応じて日本語→英語に変換
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function showStatusAll($txt){
	if($_SESSION['MATT']=="1"){
		$list = array(
			/*
			"問い合わせ" => "Inquiry",
			"完了" => "Done.",
			"キャンセル" => "Cancellation",
			"辞退" => "Declination",
			"データ納品" => "Data delivery",
			"物品納品" => "Delivery of goods",
			"見積り依頼" => "Request for quotation",
			"請求書" => "Invoice",
			"納品" => "Delivery",
			"見積り" => "Estimate",
			"発注" => "Order placement",
			"請求" => "Request",
			"データ" => "Data",
			"物品" => "Goods",
			"一括" => "All at once",
			"分割" => "Split",
			*/
		);
	}

	foreach($list as $key => $val) {
		$txt = str_replace($key, $val, $txt);
	}

	//$txt = str_replace('書', '', $txt);

	return $txt;

}

//=========================================================================================================
//名前 テキストに含まれる文章をすべてログインユーザーに応じて日本語→英語に変換
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function showStatusAllLong($txt){
	if($_SESSION['MATT']=="1"){
		$list = array(
			"Quotationを送付しました" => "I have sent the quotation",
		);
	}

	foreach($list as $key => $val) {
		$txt = str_replace($key, $val, $txt);
	}

	$txt = str_replace('書', '', $txt);

	return $txt;

}



//=========================================================================================================
//名前 ステータスをログインユーザーに応じて日本語→英語に変換
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function showStatusColor($val){
	$key = $val;
	$color='#f0f0f0';
	if($_SESSION['MATT']=="1"){
		switch($key){
			case "問い合わせ":
				$color="#d08080";
				break;
			case "見積り":
				$color="#d0d080";
				break;
			case "発注":
				$color="#80d080";
				break;
			case "納品":
				$color="#80d0d0";
				break;
			case "請求":
				$color="#8080d0";
				break;
			case "完了":
				$color="#8080d0";
				$color="Done.";
				break;
			case "キャンセル":
				$color="#8080d0";
				break;
			case "辞退":
				$color="#8080d0";
				break;
			case "データ納品":
				$color="#8080d0";
				break;
			case "物品納品":
				$color="#8080d0";
				break;
			case "見積り依頼":
				$color="#8080d0";
				break;
		}
	} else {
		switch($key){
			case "問い合わせ":
				$color="#d08080";
				break;
			case "見積り":
				$color="#d0d080";
				break;
			case "発注":
				$color="#80d080";
				break;
			case "納品":
				$color="#80d0d0";
				break;
			case "請求":
				$color="#8080d0";
				break;
			case "完了":
				$color="#8080d0";
				$color="Done.";
				break;
			case "キャンセル":
				$color="#8080d0";
				break;
			case "辞退":
				$color="#8080d0";
				break;
			case "データ納品":
				$color="#8080d0";
				break;
			case "物品納品":
				$color="#8080d0";
				break;
			case "見積り依頼":
				$color="#8080d0";
				break;
		}

	}

	return $color;



}


//=========================================================================================================
//名前 文字列暗号化
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function strEncrypt($cid)
{
	// 暗号化キー
	$key = 'ms123_aes';
	// 暗号化方式
	$method = 'aes-128-cbc';
	// OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
	$options = 0;
	// IV
	$iv_string = "fq5ctJw8ZJfGrpOFYABw5w==";
	$iv = base64_decode($iv_string);

	$encrypted = bin2hex(openssl_encrypt($cid, $method, $key, OPENSSL_RAW_DATA, $iv));

	return $encrypted;
}
//=========================================================================================================
//名前 文字列複合化
//機能 プログラムのメイン関数
//引数 なし
//戻値 なし
//=========================================================================================================
function strDecrypt($encrypted)
{
	// 暗号化キー
	$key = 'ms123_aes';
	// 暗号化方式
	$method = 'aes-128-cbc';
	// OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
	$options = 0;
	// IV
	$iv_string = "fq5ctJw8ZJfGrpOFYABw5w==";
	$iv = base64_decode($iv_string);

	$decrypted = openssl_decrypt(hex2bin($encrypted), $method , $key, OPENSSL_RAW_DATA, $iv);

	return $decrypted;
}
//=========================================================================================================
//名前 パスワードをハッシュ化する
//機能 
//引数 
//戻値 
//=========================================================================================================
function pwd_hash($pwd){
	return $pwd;
	// return password_hash($pwd, PASSWORD_DEFAULT);
}

//=========================================================================================================
//名前 MIDをサプライヤーID、顧客IDに変換
//機能
//引数
//戻値
//=========================================================================================================
function convert_mid($mid){

	if(strpos($mid, 'M1') !== false) {
		return str_replace('M1', 'SS', $mid);
	}
	if(strpos($mid, 'M2') !== false) {
		// M2だけ1000倍しなければならない
		//return str_replace('M2', 'SR', $mid);
		$tmp = intval(str_replace('M2', '', $mid)) + 1000;
		return 'SR' . sprintf('%05d', $tmp);
	}
	if(strpos($mid, 'M3') !== false) {
		return str_replace('M3', 'SR', $mid);
	}
	return $mid;
}

//=========================================================================================================
//名前 疑似BASIC認証
//機能
//引数
//戻値
//=========================================================================================================
function set_basic_authentication($html_ok, $html_ng){

	// テスト用
	// unset($_SESSION['basic_authentication']);
	// unset($_SESSION['basic_authentication_cnt']);
	// unset($_SESSION['basic_authentication_dt']);

	echo('<!--basic_authentication:' . $_SESSION['basic_authentication'] . '-->');
	echo('<!--basic_authentication_cnt:' . $_SESSION['basic_authentication_cnt'] . '-->');
	echo('<!--basic_authentication_dt:' . $_SESSION['basic_authentication_dt'] . '-->');
	// 疑似BASIC認証
	$basic_authentication = '';
	if(isset($_SESSION['basic_authentication'])) {
	  if($_SESSION['basic_authentication'] == 'lock') {
			$cur_dt = strtotime(date('Y-m-d H:i:s'));
			$lock_dt = strtotime($_SESSION['basic_authentication_dt']);
			echo('<!--cur_dt:' . $cur_dt . '-->');
			echo('<!--lock_dt:' . ($lock_dt + 60 * 30) . '-->');
			if($cur_dt > $lock_dt + 60 * 30) {
				$_SESSION['basic_authentication'] = 'ng';
				$_SESSION['basic_authentication_cnt'] = '0';
				$_SESSION['basic_authentication_dt'] = '';
			}
		}
		$basic_authentication = $_SESSION['basic_authentication'];
	}
	else {
		$_SESSION['basic_authentication'] = 'ng';
		$_SESSION['basic_authentication_cnt'] = '0';
		$_SESSION['basic_authentication_dt'] = '';
		$basic_authentication = $_SESSION['basic_authentication'];
	}
	//$str=str_replace("[BASIC_AUTHENTICATION]",$basic_authentication,$str);
	if($basic_authentication == 'ok') {
		return $html_ok;
	}
	else {
		return $html_ng;
	}

}

//=========================================================================================================
//名前 
//機能 セッションチェック（空かどうかではなくセッションのMIDが有効かどうか）
//引数 
//戻値 
//=========================================================================================================
function CheckSession ($side)
{
	eval(globals());

	$StrSQL="SELECT ID from DAT_M".$side." where MID='".$_SESSION['MID']."';";

	$rs=mysqli_query(ConnDB(),$StrSQL);
	$item = mysqli_fetch_assoc($rs);
	if($item>0){
		return true;
	}
	else {
		return false;
	}

} 

//=========================================================================================================
//名前 
//機能 div_idが使えるかどうか確認(見積り発行後のフローで、分割支払いで登録されているかを確認)
//引数 div_id　文字列
//戻値 分割扱いになっていればdiv_idをそのまま返し、なっていなければ空を返す。
//=========================================================================================================
function checkDIV_ID($div_id){

	if($div_id=="" || !isset($div_id)){
		return "";
	}

	$tmp=explode("-", $div_id);
	if(count($tmp)==3){
		$StrSQL="SELECT * FROM DAT_SHODAN_DIV WHERE DIV_ID='".$div_id."'";
		$rs=mysqli_query(ConnDB(),$StrSQL);
		$num=mysqli_num_rows($rs);

		if($num>=1){
			return $div_id;
		}else{
			return "";
		}

	}else{
		return "";
	}
}


//=========================================================================================================
//名前 
//機能 
//発注以降のフェーズで使用可能
//shodan_idとMID1だけ情報が渡ってきた場合に（本来div_idがいるところでそれがなかった場合）、その商談内の状況を分析して、支払い形態を判定する。
//・最新の発注依頼をとってきて、その発注依頼の対象の見積り送付データをとってきて、1括払いかどうかの判断。
//・発注は1商談内に1つしか存在しない仕様と決定したが、念のため最新の発注依頼をとってくるようにしている。

//引数 shodan_id　文字列
//戻値 支払い形態を返す(Once,Split,Milstone)。それぞれ、1活払い、2回払い、マイルストーン払い
//=========================================================================================================
function check_M2_PAY_TYPE($shodan_id,$mid1){

	if($shodan_id=="" || $mid1==""){
		return "";
	}

	$StrSQL="SELECT ID,SHODAN_ID,MID1,STATUS,H_M2_ID FROM DAT_FILESTATUS where SHODAN_ID='".$shodan_id."' ";
	$StrSQL.=" and MID1='".$mid1."' ";
	$StrSQL.=" and STATUS='発注依頼' order by ID desc ";
	$h_rs=mysqli_query(ConnDB(),$StrSQL);
	$h_item= mysqli_fetch_assoc($h_rs);
	echo "<!--$StrSQL:[1]:\n";
	var_dump($h_item);
	echo "-->";
	$StrSQL="SELECT ID,SHODAN_ID,MID1,STATUS,M2_PAY_TYPE FROM DAT_FILESTATUS WHERE ID=".$h_item["H_M2_ID"]." ";
	$StrSQL.=" and MID1='".$mid1."' ";
	$rs_chk=mysqli_query(ConnDB(),$StrSQL);
	$item_chk = mysqli_fetch_assoc($rs_chk);
	echo "<!--$StrSQL:[2]:\n";
	var_dump($item_chk);
	echo "-->";
	
	$m2_pay_type = (isset($item_chk["M2_PAY_TYPE"]) && $item_chk["M2_PAY_TYPE"]!="") ? $item_chk["M2_PAY_TYPE"] : "";

	return $m2_pay_type;
}



//=========================================================================================================
//名前 
//機能 2回払いの発注依頼はPart0のデータが送られてくるが、Part1に発注をかけるため変数おきかえる必要有り。このため、発注依頼をしていいかどうか調べる関数。
//引数 div_id　状態をしりたい取引のdiv_id（Part0がくるはずだが、Part0じゃなくても機能する）
//戻値 発注依頼可能な状態の場合、Part1のDIV_IDを返す
//=========================================================================================================
function check_split_progress_hatyu($shodan_id,$div_id){
	$tmp="";
	$tmp=explode("-", $div_id);
	//echo "<!--";
	//var_dump($tmp);
	//echo "-->";
	$pre_part="";
	$div_id_part1="";
	$div_id_part2="";
	if(count($tmp)==3){
		$pre_part=$tmp[0]."-".$tmp[1];
		$div_id_part1=$pre_part."-Part1";
		$div_id_part2=$pre_part."-Part2";
	}

	//念のためDAT_SHODAN_DIVで発注依頼の前の何もしてない状態かチェック
	$StrSQL="SELECT * FROM DAT_SHODAN_DIV WHERE SHODAN_ID='".$shodan_id."'";
	$StrSQL.=" AND DIV_ID='".$div_id_part1."' ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$num1=0;
	while( $item = mysqli_fetch_assoc($rs) ){
		//echo "<!--";
		//var_dump($item);
		//echo "-->";
		if($item["DIV_ID"]=="" || is_null($item["DIV_ID"])){
			continue;
		}

		if($item["DIV_ID"]==$div_id_part1 && $item["STATUS"]=="見積り送付"){
			$num1=1;

		}
	}
	$StrSQL="SELECT * FROM DAT_SHODAN_DIV WHERE SHODAN_ID='".$shodan_id."'";
	$StrSQL.=" AND DIV_ID='".$div_id_part2."' ";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$num2=0;
	while( $item = mysqli_fetch_assoc($rs) ){
		//echo "<!--";
		//var_dump($item);
		//echo "-->";
		if($item["DIV_ID"]=="" || is_null($item["DIV_ID"])){
			continue;
		}

		if($item["DIV_ID"]==$div_id_part2 && $item["STATUS"]=="見積り送付"){
			$num2=1;

		}
	}

	$h_div_id="";
	if($num1==1 && $num2==1){
		$h_div_id=$div_id_part1;
	}


//	//念のためDAT_SHODAN_DIVで発注依頼の前の何もしてない状態かチェック
//	$StrSQL="SELECT * FROM DAT_SHODAN_DIV WHERE SHODAN_ID='".$shodan_id."'";
//	$StrSQL.=" AND (DIV_ID='".$div_id_part1."' || DIV_ID='".$div_id_part2."') ";
//	$rs=mysqli_query(ConnDB(),$StrSQL);
//	$num=0;
//	while( $item = mysqli_fetch_assoc($rs) ){
//		echo "<!--";
//		var_dump($item);
//		echo "-->";
//		if($item["DIV_ID"]=="" || is_null($item["DIV_ID"])){
//			continue;
//		}
//
//		if($item["DIV_ID"]==$div_id_part1 && $item["STATUS"]=="見積り送付"){
//			$num++;
//
//		}else if($item["DIV_ID"]==$div_id_part2 && $item["STATUS"]=="見積り送付"){
//			$num++;
//
//		}
//	}
//
//	$h_div_id="";
//	if($num==2){
//		$h_div_id=$div_id_part1;
//	}

	return $h_div_id;

}




//=========================================================================================================
//名前 
//機能 「Scientist3 control No.」用。数字からアルファベットのIDに変換。
//引数 1,2,3,...,N
//戻値 A,B,C,...,Z,AA,AB,AC,...,AZ,...
//=========================================================================================================
function generateAlphabetId($number) {
	if ($number < 1) {
		return null; // 無効な入力
	}

	$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$base = 26; // アルファベットの数
	$id = '';

	// 数値をアルファベットIDに変換
	while ($number > 0) {
		$number--; // 0ベースに調整（A=1, B=2, ..., Z=26）
		$remainder = $number % $base; // 余り
		$id = $alphabet[$remainder] . $id; // アルファベットを前に追加
		$number = (int)($number / $base); // 次の桁へ
	}

	return $id;
}
//=========================================================================================================
//名前 
//機能 「Scientist3 control No.」用。アルファベットのIDから、数字に変換。
//引数 A,B,C,...,Z,AA,AB,AC,...,AZ,...
//戻値 1,2,3,...,N
//=========================================================================================================
function getAlphabetNumber($alphabet) {
	if (empty($alphabet) || !preg_match('/^[A-Z]+$/', $alphabet)) {
		return null; // 無効な入力（空またはアルファベット以外）
	}

	$base = 26; // アルファベットの数
	$number = 0;
	$alphabet = strtoupper($alphabet); // 大文字に統一
	$alphabetMap = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ')); // アルファベットをインデックスに変換

	// アルファベットIDを数値に変換
	for ($i = 0; $i < strlen($alphabet); $i++) {
		$number = $number * $base + ($alphabetMap[$alphabet[$i]] + 1); // 1ベース（A=1, B=2, ...)
	}

	return $number;
}
//=========================================================================================================
//名前 
//機能 配列から「Scientist3 control No.」を作成。
//引数 Scientist3 control Noの各項目が設定された配列
//$SCNo=array(
//	"SCNo_yy" => "", 
//	"SCNo_mm" => "", 
//	"SCNo_dd" => "", 
//	"SCNo_cnt" => "", 
//	"SCNo_else1" => "", 
//	"SCNo_else2" => "", 
//);
//戻値 整形されたScientist3 control No.
//=========================================================================================================
function formatAlphabetId($SCNo_ary){
	$SCNo_str="";
	$i=0;
	foreach ($SCNo_ary as $key => $val) {
		if($i==0) $SCNo_str.="SC";
		if($key=="SCNo_else1" || $key=="SCNo_else2"){
			if($val!="" && !is_null($val)){
				$SCNo_str.="-".$val;
			}
		}else{
			$SCNo_str.=$val;
		}
		$i++;
	}

	if($SCNo_str=="SC") return "";
	return $SCNo_str;
}



//=========================================================================================================
//名前 
//機能 サービス費用エリアの表示や計算等の出力。
//a_filestatusのサービスエリア費用計算部分ををもとにしているので、ここで不要な記述もあり。
//引数 $id:見積り書のID, $str:置換したい文章。
//戻値 文章
//=========================================================================================================
function makeServiceArea($id,$str){
	$StrSQL="SELECT * FROM DAT_FILESTATUS WHERE ID='".$id."'";
	$rs=mysqli_query(ConnDB(),$StrSQL);
	$target_item=mysqli_fetch_assoc($rs);

	$tmp="";
	$tmp=explode("-", $target_item["DIV_ID"]);
	$part="";
	$pre_part="";
	if(count($tmp)==3){
		$part=$tmp[2];
		$pre_part=$tmp[0]."-".$tmp[1];
	}
	echo "<!--サービス費用エリア：part:$part-->";
	echo "<!--サービス費用エリア：pre_part:$pre_part-->";
	echo "<!--target_item:\n";
	var_dump($target_item);
	echo "-->";

	//小計1
	//※先方の要望により、
	//スペシャルディスカウント(M2_SPECIAL_DISCOUNT)の仕様変更で、各アイテムごとにスペシャルディスカウントを設定するようにした。
	//=該当分割分のアイテムのM2_DETAIL_PRICEの合計+M2_DETAIL_HANDLING_FEEの合計
	//※注意：M2_DETAIL_PRICE=M2_DETAIL_QUANTITY*M2_DETAIL_UNIT_PRICE-M2_DETAIL_SP_DISCOUNTに変更になった。
	//元はM2_DETAIL_QUANTITY*M2_DETAIL_UNIT_PRICEだった
	$StrSQL="SELECT * FROM DAT_FILESTATUS_DETAIL WHERE FILESTATUS_ID='".$target_item["ID"]."' order by NEWDATE";
	//echo "<!--SQL:".$StrSQL."-->";
	$rs_detail=mysqli_query(ConnDB(),$StrSQL);
	$syoke1=0;
	$sum_m2_detail_sp_discount=0;
	while($item_detail = mysqli_fetch_assoc($rs_detail)){
		if(is_numeric($item_detail["M2_DETAIL_HANDLING_FEE"])){
			$m2_detail_handling_fee=$item_detail["M2_DETAIL_HANDLING_FEE"];
		}else{
			$m2_detail_handling_fee=0;
		}
		if(is_numeric($item_detail["M2_DETAIL_PRICE"])){
			$m2_detail_price=$item_detail["M2_DETAIL_PRICE"];
		}else{
			$m2_detail_price=0;
		}
		if(is_numeric($item_detail["M2_DETAIL_SP_DISCOUNT"])){
			$m2_detail_sp_discount=$item_detail["M2_DETAIL_SP_DISCOUNT"];
		}else{
			$m2_detail_sp_discount=0;
		}
		$sum_m2_detail_sp_discount=$sum_m2_detail_sp_discount+$m2_detail_sp_discount;
		$syoke1=$syoke1+$m2_detail_price+$m2_detail_handling_fee;
		echo "<!--m2_detail_handling_fee:$m2_detail_handling_fee-->";
		echo "<!--m2_detail_sp_discount:$m2_detail_sp_discount-->";
		echo "<!--M2_DETAIL_PRICE:".$item_detail["M2_DETAIL_PRICE"]."-->";
	}
	
	echo "<!--M2_detail_SP_DISCOUNTの合計:$sum_m2_detail_sp_discount-->";
	echo "<!--syoke1:$syoke1-->";
	$str=str_replace("[SUM_M2_DETAIL_SP_DISCOUNT]", $sum_m2_detail_sp_discount, $str); //PDF対応
	$str=str_replace("[MITSUMORISYO_SUBTOTAL1]", $syoke1, $str);

	//税率1
	//国内サプライヤー10%,海外サプライヤー0%。
	//編集もしたいということで、
	//DBに値がなかったら自動入力し、常に編集できる状態に仕様変更
	$StrSQL="SELECT * FROM DAT_M1 WHERE MID='".$target_item["MID1"]."';";
	$rsM1=mysqli_query(ConnDB(),$StrSQL);
	$itemM1 = mysqli_fetch_assoc($rsM1);
	$tax_rate1=0;
	if($target_item["M2_TAX_RATE1"]!="" && !is_null($target_item["M2_TAX_RATE1"])){
		$tax_rate1=$target_item["M2_TAX_RATE1"];
	}else{
		if($itemM1["M1_DVAL04"]=="M1_DVAL04:Japan"){
			$tax_rate1=10;
		}else{
			$tax_rate1=0;
		}
	}
	$str=str_replace("[MITSUMORISYO_TAX_RATE1]",$tax_rate1,$str);


	//消費税
	$tax_bill1=$tax_rate1*$syoke1/100;
	$str=str_replace("[MITSUMORISYO_TAX_BILL1]",$tax_bill1,$str);


	//PDF対応
	//PDF用の表示
	$pdf_total1=$syoke1+$tax_bill1;
	$str=str_replace("[PDF_TOTAL1]", $pdf_total1, $str);


	//PF手数料
	//研究者管理で入力したPF手数料率/100を使用
	$StrSQL="SELECT * FROM DAT_M2 WHERE MID='".$target_item["MID2"]."';";
	$rsM2=mysqli_query(ConnDB(),$StrSQL);
	$itemM2 = mysqli_fetch_assoc($rsM2);
	if(is_numeric($itemM2["M2_ETC02"])){
		$pf_fee=$syoke1*$itemM2["M2_ETC02"]/100;
	}else{
		$pf_fee=0;
	}
	$str=str_replace("[MITSUMORISYO_PF_FEE]",$pf_fee,$str);

	echo "<!--M2_ETC02:".$itemM2["M2_ETC02"]."-->";



	//輸入代行費用
	//初期値はない。管理画面から手動で入力。
	//「手数料追加（前払い）＆2回払い＆(2回払いの2回目の支払い書 or フロント用)」or
	//「マイルストーンの場合」or
	//「1活払いの場合」
	//の場合、入力可能なインプットにする
	//M_STATUS,M2_PAY_TYPE

	//仕様変更１
	//一括の場合はインプットにする。
	//2回払い、マイルストーン払いの場合は、ラストの見積り送付（PartN）データのみインプットにし、
	//Part0はPartNの値を表示のみ、その他（Part1~PartN-1）は何も表示しない。

	//仕様変更2
	//マイルストーンの場合は、納品物がDataかGoodsかにかかわらず、輸出代行費用も輸入代行費用もInput形式。
	//Part0はPart1~PartNの合算値を表示のみ。

	if( $target_item["M2_PAY_TYPE"]=="Once"){

		if(is_numeric($target_item["M2_IMPORT_FEE"])){
			$import_fee=$target_item["M2_IMPORT_FEE"];
		}else{
			$import_fee=0;
		}

	}else if($target_item["M2_PAY_TYPE"]=="Split"){
		//分割されてナンバリングされた見積り送付のデータの最後のデータ（PartLAST）をとってくる
		$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_IMPORT_FEE FROM DAT_FILESTATUS WHERE ";
		$StrSQL.=" SHODAN_ID='".$target_item["SHODAN_ID"]."' ";
		$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
		$StrSQL.=" AND STATUS='".$target_item["STATUS"]."' ";
		$StrSQL.=" ORDER BY CAST(SUBSTRING_INDEX(DIV_ID, 'Part', -1) AS UNSIGNED) DESC;";
		$partLAST_rs=mysqli_query(ConnDB(),$StrSQL);
		$partLAST_item = mysqli_fetch_assoc($partLAST_rs);
		echo "<!--サービス費用エリア(輸入)：partLAST:";
		echo "$StrSQL\n";
		var_dump($partLAST_item);
		echo "-->";
		echo "<!--54:".$target_item["DIV_ID"].", prepart合体：".$pre_part."-Part0"."-->";
		if($target_item["DIV_ID"]==$pre_part."-Part0"){
			//Part0だったら
			if(is_numeric($partLAST_item["M2_IMPORT_FEE"])){
				$import_fee=$partLAST_item["M2_IMPORT_FEE"];
			}else{
				$import_fee=0;
			}

		}else if($target_item["DIV_ID"]==$partLAST_item["DIV_ID"]){
			//PartNだったら
			if(is_numeric($target_item["M2_IMPORT_FEE"])){
				$import_fee=$target_item["M2_IMPORT_FEE"];
			}else{
				$import_fee=0;
			}

		}else{
			$import_fee=0;
		}

	}else if($target_item["M2_PAY_TYPE"]=="Milestone"){
		echo "<!--54:".$target_item["DIV_ID"].", prepart合体：".$pre_part."-Part0"."-->";
		if($target_item["DIV_ID"]==$pre_part."-Part0"){
			//Part0だったら
			//分割されてナンバリングされた見積り送付のデータ（PartN）をとってくる
			$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_IMPORT_FEE FROM DAT_FILESTATUS WHERE ";
			$StrSQL.=" SHODAN_ID='".$target_item["SHODAN_ID"]."' ";
			$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
			$StrSQL.=" AND STATUS='".$target_item["STATUS"]."' ";
			$partN_rs=mysqli_query(ConnDB(),$StrSQL);

			echo "<!--サービス費用エリア(輸入)：partN:";
			echo "$StrSQL\n";
			var_dump($partN_item);
			echo "-->";
			$import_fee=0;
			while( $partN_item = mysqli_fetch_assoc($partN_rs) ){
				if($partN_item["DIV_ID"]==$pre_part."-Part0"){
					continue;
				}

				if(is_numeric($partN_item["M2_IMPORT_FEE"])){
					$import_fee+=$partN_item["M2_IMPORT_FEE"];
				}

			}

		}else{
			//Part0以外
			if(is_numeric($target_item["M2_IMPORT_FEE"])){
				$import_fee=$target_item["M2_IMPORT_FEE"];
			}else{
				$import_fee=0;
			}

		}

	}else{
		$import_fee=0;

	}
	
	$str=str_replace("[INPUT-M2_IMPORT_FEE]",$import_fee,$str);



	//輸出代行費用
	///管理画面/a_agency_setting/の各通貨にたいするその時点の手数料の値が、
	//「見積り送付」時に、DAT_FILESTATUSの、カラムM2_EXPORT_FEE_TABLEに、json形式で保存される。
	//カラムM2_CURRENCYに設定されている取引に使用される通貨に対応する値を、
	//カラムM2_EXPORT_FEE_TABLEからさがし、「輸出代行費用」として設定する。
	//関連カラム：M2_EXPORT_FEE_TABLE,M2_CURRENCY,M2_PAY_TYPE,SHODAN_ID,M1_TRANS_FLG,NEWDATE
	//例：M2_EXPORT_FEE_TABLE:{"USD":"200","EUR":"300","GBP":"400","JPY":"100"}
	
	//M2_CURRENCY=="M2_CURRENCY:USD"の場合、export_fee="200"
	//仕様変更：
	//※分割支払いの場合はPart1の見積り送付データで輸出代行費用を計上する。
	//※輸出代行費用が発生するのは、研究者が見積り依頼時に「輸出代行　あり」を選択した場合のみ。
	//Part1~PartNで、Part1は値が表示され、Part1以外はこの値を0にする。
	//Part0は、Part1の値。Part1の値を参照してるだけなので表示のみ。
	
	//仕様変更２：
	//先方の要望でM2_CURRENCYを変更できないようにした。
	//輸出代行費用用に新規カラム作成。M2_EXPORT_FEE
	//inputで値を上書きできるようにする。

	//仕様変更3:
	//マイルストーンの場合は、納品物がDataかGoodsかにかかわらず、輸出代行費用も輸入代行費用もInput形式。
	//Part0はPart1~PartNの合算値を表示のみ。
	
	$export_fee=0;
	$str_export_fee="";
	if( ($target_item["M2_PAY_TYPE"]=="Split" && $part=="Part0") || 
		($target_item["M2_PAY_TYPE"]=="Milestone" && $part=="Part0") ){
		//partNのデータとってくる
		$StrSQL="SELECT ID,DIV_ID,STATUS,M2_CURRENCY,M2_EXPORT_FEE FROM DAT_FILESTATUS WHERE ";
		$StrSQL.=" SHODAN_ID='".$target_item["SHODAN_ID"]."' ";
		$StrSQL.=" AND DIV_ID LIKE '".$pre_part."-%' ";
		$StrSQL.=" AND STATUS='".$target_item["STATUS"]."' ";
		$partN_rs=mysqli_query(ConnDB(),$StrSQL);

		$export_fee=0;
		while($partN_item = mysqli_fetch_assoc($partN_rs)){
			echo "<!--サービス費用エリア：part0:";
			var_dump($partN_item);
			echo "-->";
			if($partN_item["DIV_ID"]==$pre_part."-Part0"){
				continue;
			}
			$export_fee += is_numeric($partN_item["M2_EXPORT_FEE"]) ? $partN_item["M2_EXPORT_FEE"] : 0;
		};
		echo "<!--export_fee1:$export_fee-->";

		$str=DispParam($str, "Part0_EXP");
		$str=DispParamNone($str, "PartN_EXP");
		//$str=DispParamNone($str, "ELSE_EXP");

	}else if( ($target_item["M2_PAY_TYPE"]=="Split" && $part=="Part1") || 
		$target_item["M2_PAY_TYPE"]=="Milestone" || 
		$target_item["M2_PAY_TYPE"]=="Once"){
		//自分のデータを使う
		$export_fee = is_numeric($target_item["M2_EXPORT_FEE"]) ? $target_item["M2_EXPORT_FEE"] : 0;
		echo "<!--export_fee2:$export_fee-->";
		echo "<!--target_item[\"M2_EXPORT_FEE\"]:".$target_item["M2_EXPORT_FEE"]."-->";

		$str=DispParamNone($str, "Part0_EXP");
		$str=DispParam($str, "PartN_EXP");
		//$str=DispParamNone($str, "ELSE_EXP");

	}else{
		$export_fee=0;
		$str=DispParamNone($str, "Part0_EXP");
		$str=DispParamNone($str, "PartN_EXP");
		//$str=DispParam($str, "ELSE_EXP");
	}
	//輸出代行費用が発生するのは、研究者が見積り依頼時に「輸出代行　あり」を選択した場合のみ。
	//見積り送付や運営手数料追加のNEWDATEの値より前の日付けに送信した、
	//「見積り依頼」データの「M1_TRANS_FLG」が「なし」の場合は強制的に「0」
	$StrSQL="SELECT ID,NEWDATE,STATUS,M1_TRANS_FLG FROM DAT_FILESTATUS WHERE ";
	$StrSQL.=" SHODAN_ID='".$target_item["SHODAN_ID"]."' ";
	$StrSQL.=" AND STATUS='見積り依頼' ";
	$StrSQL.=" AND NEWDATE<'".$target_item["NEWDATE"]."' ";
	$StrSQL.=" ORDER BY NEWDATE DESC ";
	$irai_rs=mysqli_query(ConnDB(),$StrSQL);
	$irai_item = mysqli_fetch_assoc($irai_rs);
	echo "<!--サービス合計エリア irai_item:";
	var_dump($irai_item);
	echo "-->";
	if($irai_item["M1_TRANS_FLG"]=="なし"){
		$str=DispParam($str, "M1_TRANS_FLG_EXP");
		//データはそもそもこの場合保存されてないが念のためここでも0に設定する。
		$export_fee=0;
		//inputフォームなしで表示のみ
	}else{
		$str=DispParamNone($str, "M1_TRANS_FLG_EXP");
	}
	$str=str_replace("[INPUT-MITSUMORISYO_EXPORT_FEE]",$export_fee,$str);



	//特別値引き（運営）
	if($target_item["M_STATUS"]=="直接送付" ||
		$target_item["M_STATUS"]=="直接送付(前払い)"){
		$mng_discount=0;
		$str=DispParam($str,"HIDDEN_M2_MANAGE_DISCOUNT");

	}else{
		$mng_discount=$target_item["M2_MANAGE_DISCOUNT"];
		$str=DispParamNone($str,"HIDDEN_M2_MANAGE_DISCOUNT");

	}
	$str=str_replace("[INPUT-M2_MANAGE_DISCOUNT]",$mng_discount,$str);


	//小計2
	$syoke2=$pf_fee+$import_fee+$export_fee-$mng_discount;
	echo "<!--syoke2:$syoke2=$pf_fee+$import_fee+$export_fee-$mng_discount-->";
	$str=str_replace("[MITSUMORISYO_SUBTOTAL2]",$syoke2,$str);


	//税率2
	$tax_rate2=$target_item["M2_TAX_RATE2"];
	$str=str_replace("[MITSUMORISYO_TAX_RATE2]",$tax_rate2,$str);

	

	//消費税率2
	$tax_bill2=$syoke2*$tax_rate2/100;
	echo "<!--tax_bill2:$tax_bill2=$syoke2*$tax_rate2/100;-->";
	$str=str_replace("[MITSUMORISYO_TAX_BILL2]",$tax_bill2,$str);


	//合計金額
	//M2_CURRENCY
	$all_charge=$syoke1+$tax_bill1+$syoke2+$tax_bill2;
	echo "<!--all_charge:$all_charge=$syoke1+$tax_bill1+$syoke2+$tax_bill2-->";
	if($target_item["M2_CURRENCY"]=="M2_CURRENCY:JPY"){
		$rounded_all_charge=round($all_charge);
	}else{
		$rounded_all_charge=round($all_charge,1);
	}
	$str=str_replace("[MITSUMORISYO_ALL_CHARGE]",$all_charge,$str);
	$str=str_replace("[R_MITSUMORISYO_ALL_CHARGE]",$rounded_all_charge,$str);


	//SHIP TO
	//BILL TO
	$ship_to=$target_item["M2_SHIP_TO_SPT_1"].", ".$target_item["M2_SHIP_TO_SPT_2"].", ".$target_item["M2_SHIP_TO_SPT_3"];
	$ship_to.=" ".$target_item["M2_SHIP_TO_SPT_4"].", ".$target_item["M2_SHIP_TO_SPT_5"].", ".$target_item["M2_SHIP_TO_SPT_6"];

	$bill_to=$target_item["M2_BILL_TO_SPT_1"].", ".$target_item["M2_BILL_TO_SPT_2"].", ".$target_item["M2_BILL_TO_SPT_3"];
	$bill_to.=" ".$target_item["M2_BILL_TO_SPT_4"].", ".$target_item["M2_BILL_TO_SPT_5"].", ".$target_item["M2_BILL_TO_SPT_6"];

	$str=str_replace("[VIEW-SHIP_TO]",$ship_to,$str);
	$str=str_replace("[VIEW-BILL_TO]",$bill_to,$str);


	//その他の一括変換
	foreach ($target_item as $idx => $val) {
		$str=str_replace("[".$idx."]",$val,$str);
		$str=str_replace("[D-".$idx."]",str_replace($idx.":", "", $val),$str);
	}


	return $str;
}

?>