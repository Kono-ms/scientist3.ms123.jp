<? 
//====================
//グローバル変数宣言
//====================

//=========================================================================================================
//名前 基本データのセット
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function InitSub()
{
  extract($GLOBALS);


//DBへの接続モード
  $DBParam="Driver={SQL Server};Server=M-SERVER01;UID=;PWD=;DATABASE=min";

//テーブル名の指定
  $TableName="DAT_SHOP";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="CATE";
  $FieldName[2]="NAME";
  $FieldName[3]="KANA";
  $FieldName[4]="POSTAGE";
  $FieldName[5]="PREF";
  $FieldName[6]="ADD1";
  $FieldName[7]="ADD2";
  $FieldName[8]="EMAIL";
  $FieldName[9]="URL";
  $FieldName[10]="TEL";
  $FieldName[11]="FAX";
  $FieldName[12]="TANTO";
  $FieldName[13]="TIMECOMMENT1";
  $FieldName[14]="TIMECOMMENT2";
  $FieldName[15]="SDATE";
  $FieldName[16]="EDATE";
  $FieldName[17]="OFFWEEK";
  $FieldName[18]="MONEY";
  $FieldName[19]="ADDRESS";
  $FieldName[20]="COMMENT1";
  $FieldName[21]="COMMENT2";
  $FieldName[22]="COMMENT3";
  $FieldName[23]="PIC1";
  $FieldName[24]="PIC2";
  $FieldName[25]="PIC3";
  $FieldName[26]="PIC4";
  $FieldName[27]="PIC5";
  $FieldName[28]="MAP";
  $FieldName[29]="ENABLE";

//入力フィールドの書式　0-TEXT, 1-SELECT, 2-RADIO, 3-CHECKBOX, 4-FILE
  $FieldAtt[0]=0;
  $FieldAtt[1]=1;
  $FieldAtt[2]=0;
  $FieldAtt[3]=0;
  $FieldAtt[4]=0;
  $FieldAtt[5]=0;
  $FieldAtt[6]=0;
  $FieldAtt[7]=0;
  $FieldAtt[8]=0;
  $FieldAtt[9]=0;
  $FieldAtt[10]=0;
  $FieldAtt[11]=0;
  $FieldAtt[12]=0;
  $FieldAtt[13]=0;
  $FieldAtt[14]=0;
  $FieldAtt[15]=0;
  $FieldAtt[16]=0;
  $FieldAtt[17]=0;
  $FieldAtt[18]=0;
  $FieldAtt[19]=0;
  $FieldAtt[20]=0;
  $FieldAtt[21]=0;
  $FieldAtt[22]=0;
  $FieldAtt[23]=4;
  $FieldAtt[24]=4;
  $FieldAtt[25]=4;
  $FieldAtt[26]=4;
  $FieldAtt[27]=4;
  $FieldAtt[28]=0;
  $FieldAtt[29]=1;

//全フィールド数
  $FieldMax=29;

//キーフィールドの設定
  $FieldKey=0;

//リスト行数
  $PageSize=20;

//各テンプレートファイル名
  $htmlnew="new.html";
  $htmledit="edit.html";
  $htmlconf="conf.html";
  $htmlend="end.html";
  $htmldisp="disp.html";
  $htmlerr="err.html";
  $htmllist="list.html";

//ASPファイル名
  $aspname="dataedit.asp";

//FILE アップロードパス(WEB絶対パス)
  $filepath1="/shop/data/";

//FILE アップロードパス(サーバ絶対パス)
  $filepath2="C:\\Inetpub\\vhosts\\b-draft.jp\\httpdocs\\shop\\data\\";

  return $function_ret;
} 

//=========================================================================================================
//名前 新規作成時の初期値設定
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function InitData()
{
  extract($GLOBALS);


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
  extract($GLOBALS);


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
  extract($GLOBALS);



  $str="";

  if ($word!="")
  {

    $str=$str."WHERE ".$FieldName[$FieldKey]." like '%".$word."%'";
  } 

  $str=$str." ORDER BY ID desc";

  $function_ret=$str;

  return $function_ret;
} 

?>

