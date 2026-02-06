<?php

//テーブル名の指定
  $TableName="DAT_BLOG1";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="OID";
  $FieldName[2]="MID";
  $FieldName[3]="O1_DVAL01";
  $FieldName[4]="O1_DVAL02";
  $FieldName[5]="O1_DVAL03";
  $FieldName[6]="O1_DVAL04";
  $FieldName[7]="O1_DVAL05";
  $FieldName[8]="O1_DVAL06";
  $FieldName[9]="O1_DVAL07";
  $FieldName[10]="O1_DVAL08";
  $FieldName[11]="O1_DVAL09";
  $FieldName[12]="O1_DVAL10";
  $FieldName[13]="O1_DTXT01";
  $FieldName[14]="O1_DTXT02";
  $FieldName[15]="O1_DTXT03";
  $FieldName[16]="O1_DTXT04";
  $FieldName[17]="O1_DTXT05";
  $FieldName[18]="O1_DTXT06";
  $FieldName[19]="O1_DTXT07";
  $FieldName[20]="O1_DTXT08";
  $FieldName[21]="O1_DTXT09";
  $FieldName[22]="O1_DTXT10";
  $FieldName[23]="O1_DSEL01";
  $FieldName[24]="O1_DSEL02";
  $FieldName[25]="O1_DSEL03";
  $FieldName[26]="O1_DSEL04";
  $FieldName[27]="O1_DSEL05";
  $FieldName[28]="O1_DSEL06";
  $FieldName[29]="O1_DSEL07";
  $FieldName[30]="O1_DSEL08";
  $FieldName[31]="O1_DSEL09";
  $FieldName[32]="O1_DSEL10";
  $FieldName[33]="O1_DRDO01";
  $FieldName[34]="O1_DRDO02";
  $FieldName[35]="O1_DRDO03";
  $FieldName[36]="O1_DRDO04";
  $FieldName[37]="O1_DRDO05";
  $FieldName[38]="O1_DRDO06";
  $FieldName[39]="O1_DRDO07";
  $FieldName[40]="O1_DRDO08";
  $FieldName[41]="O1_DRDO09";
  $FieldName[42]="O1_DRDO10";
  $FieldName[43]="O1_DCHK01";
  $FieldName[44]="O1_DCHK02";
  $FieldName[45]="O1_DCHK03";
  $FieldName[46]="O1_DCHK04";
  $FieldName[47]="O1_DCHK05";
  $FieldName[48]="O1_DCHK06";
  $FieldName[49]="O1_DCHK07";
  $FieldName[50]="O1_DCHK08";
  $FieldName[51]="O1_DCHK09";
  $FieldName[52]="O1_DCHK10";
  $FieldName[53]="O1_DFIL01";
  $FieldName[54]="O1_DFIL02";
  $FieldName[55]="O1_DFIL03";
  $FieldName[56]="O1_DFIL04";
  $FieldName[57]="O1_DFIL05";
  $FieldName[58]="O1_DFIL06";
  $FieldName[59]="O1_DFIL07";
  $FieldName[60]="O1_DFIL08";
  $FieldName[61]="O1_DFIL09";
  $FieldName[62]="O1_DFIL10";
  $FieldName[63]="O1_MSEL01";
  $FieldName[64]="O1_MSEL02";
  $FieldName[65]="O1_MSEL03";
  $FieldName[66]="O1_MSEL04";
  $FieldName[67]="O1_MSEL05";
  $FieldName[68]="O1_MSEL06";
  $FieldName[69]="O1_MSEL07";
  $FieldName[70]="O1_MSEL08";
  $FieldName[71]="O1_MSEL09";
  $FieldName[72]="O1_MSEL10";
  $FieldName[73]="O1_MRDO01";
  $FieldName[74]="O1_MRDO02";
  $FieldName[75]="O1_MRDO03";
  $FieldName[76]="O1_MRDO04";
  $FieldName[77]="O1_MRDO05";
  $FieldName[78]="O1_MRDO06";
  $FieldName[79]="O1_MRDO07";
  $FieldName[80]="O1_MRDO08";
  $FieldName[81]="O1_MRDO09";
  $FieldName[82]="O1_MRDO10";
  $FieldName[83]="O1_MCHK01";
  $FieldName[84]="O1_MCHK02";
  $FieldName[85]="O1_MCHK03";
  $FieldName[86]="O1_MCHK04";
  $FieldName[87]="O1_MCHK05";
  $FieldName[88]="O1_MCHK06";
  $FieldName[89]="O1_MCHK07";
  $FieldName[90]="O1_MCHK08";
  $FieldName[91]="O1_MCHK09";
  $FieldName[92]="O1_MCHK10";
  $FieldName[93]="ENABLE";
  $FieldName[94]="NEWDATE";
  $FieldName[95]="EDITDATE";
  $FieldName[96]="O1_ETC01";
  $FieldName[97]="O1_ETC02";
  $FieldName[98]="O1_ETC03";
  $FieldName[99]="O1_ETC04";
  $FieldName[100]="O1_ETC05";
  $FieldName[101]="O1_ETC06";
  $FieldName[102]="O1_ETC07";
  $FieldName[103]="O1_ETC08";
  $FieldName[104]="O1_ETC09";
  $FieldName[105]="O1_ETC10";
  $FieldName[106]="O1_ETC11";
  $FieldName[107]="O1_ETC12";
  $FieldName[108]="O1_ETC13";
  $FieldName[109]="O1_ETC14";
  $FieldName[110]="O1_ETC15";
  $FieldName[111]="O1_ETC16";
  $FieldName[112]="O1_ETC17";
  $FieldName[113]="O1_ETC18";
  $FieldName[114]="O1_ETC19";
  $FieldName[115]="O1_ETC20";

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
  $FieldValue[91]="";
  $FieldValue[92]="";
  $FieldValue[93]="";
  $FieldValue[94]="";
  $FieldValue[95]="";
  $FieldValue[96]="";
  $FieldValue[97]="";
  $FieldValue[98]="";
  $FieldValue[99]="";
  $FieldValue[100]="";
  $FieldValue[101]="";
  $FieldValue[102]="";
  $FieldValue[103]="";
  $FieldValue[104]="";
  $FieldValue[105]="";
  $FieldValue[106]="";
  $FieldValue[107]="";
  $FieldValue[108]="";
  $FieldValue[109]="";
  $FieldValue[110]="";
  $FieldValue[111]="";
  $FieldValue[112]="";
  $FieldValue[113]="";
  $FieldValue[114]="";
  $FieldValue[115]="";

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
  $FieldAtt[9]="0";
  $FieldAtt[10]="0";
  $FieldAtt[11]="0";
  $FieldAtt[12]="0";
  $FieldAtt[13]="0";
  $FieldAtt[14]="0";
  $FieldAtt[15]="0";
  $FieldAtt[16]="0";
  $FieldAtt[17]="0";
  $FieldAtt[18]="0";
  $FieldAtt[19]="0";
  $FieldAtt[20]="0";
  $FieldAtt[21]="0";
  $FieldAtt[22]="0";
  $FieldAtt[23]="1";
  $FieldAtt[24]="1";
  $FieldAtt[25]="1";
  $FieldAtt[26]="1";
  $FieldAtt[27]="1";
  $FieldAtt[28]="1";
  $FieldAtt[29]="1";
  $FieldAtt[30]="1";
  $FieldAtt[31]="1";
  $FieldAtt[32]="1";
  $FieldAtt[33]="2";
  $FieldAtt[34]="2";
  $FieldAtt[35]="2";
  $FieldAtt[36]="2";
  $FieldAtt[37]="2";
  $FieldAtt[38]="2";
  $FieldAtt[39]="2";
  $FieldAtt[40]="2";
  $FieldAtt[41]="2";
  $FieldAtt[42]="2";
  $FieldAtt[43]="3";
  $FieldAtt[44]="3";
  $FieldAtt[45]="3";
  $FieldAtt[46]="3";
  $FieldAtt[47]="3";
  $FieldAtt[48]="3";
  $FieldAtt[49]="3";
  $FieldAtt[50]="3";
  $FieldAtt[51]="3";
  $FieldAtt[52]="3";
  $FieldAtt[53]="4";
  $FieldAtt[54]="4";
  $FieldAtt[55]="4";
  $FieldAtt[56]="4";
  $FieldAtt[57]="4";
  $FieldAtt[58]="4";
  $FieldAtt[59]="4";
  $FieldAtt[60]="4";
  $FieldAtt[61]="4";
  $FieldAtt[62]="4";
  $FieldAtt[63]="1";
  $FieldAtt[64]="1";
  $FieldAtt[65]="1";
  $FieldAtt[66]="1";
  $FieldAtt[67]="1";
  $FieldAtt[68]="1";
  $FieldAtt[69]="1";
  $FieldAtt[70]="1";
  $FieldAtt[71]="1";
  $FieldAtt[72]="1";
  $FieldAtt[73]="2";
  $FieldAtt[74]="2";
  $FieldAtt[75]="2";
  $FieldAtt[76]="2";
  $FieldAtt[77]="2";
  $FieldAtt[78]="2";
  $FieldAtt[79]="2";
  $FieldAtt[80]="2";
  $FieldAtt[81]="2";
  $FieldAtt[82]="2";
  $FieldAtt[83]="3";
  $FieldAtt[84]="3";
  $FieldAtt[85]="3";
  $FieldAtt[86]="3";
  $FieldAtt[87]="3";
  $FieldAtt[88]="3";
  $FieldAtt[89]="3";
  $FieldAtt[90]="3";
  $FieldAtt[91]="3";
  $FieldAtt[92]="3";
  $FieldAtt[93]="2";
  $FieldAtt[94]="0";
  $FieldAtt[95]="0";
  $FieldAtt[96]="0";
  $FieldAtt[97]="0";
  $FieldAtt[98]="0";
  $FieldAtt[99]="0";
  $FieldAtt[100]="0";
  $FieldAtt[101]="0";
  $FieldAtt[102]="0";
  $FieldAtt[103]="0";
  $FieldAtt[104]="0";
  $FieldAtt[105]="0";
  $FieldAtt[106]="0";
  $FieldAtt[107]="0";
  $FieldAtt[108]="0";
  $FieldAtt[109]="0";
  $FieldAtt[110]="0";
  $FieldAtt[111]="0";
  $FieldAtt[112]="0";
  $FieldAtt[113]="0";
  $FieldAtt[114]="0";
  $FieldAtt[115]="0";

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
  $FieldParam[15]="";
  $FieldParam[16]="";
  $FieldParam[17]="";
  $FieldParam[18]="";
  $FieldParam[19]="";
  $FieldParam[20]="";
  $FieldParam[21]="";
  $FieldParam[22]="";
  $FieldParam[23]="O1_DSEL01-SEL";
  $FieldParam[24]="O1_DSEL02-SEL";
  $FieldParam[25]="O1_DSEL03-SEL";
  $FieldParam[26]="O1_DSEL04-SEL";
  $FieldParam[27]="O1_DSEL05-SEL";
  $FieldParam[28]="O1_DSEL06-SEL";
  $FieldParam[29]="O1_DSEL07-SEL";
  $FieldParam[30]="O1_DSEL08-SEL";
  $FieldParam[31]="O1_DSEL09-SEL";
  $FieldParam[32]="O1_DSEL10-SEL";
  $FieldParam[33]="O1_DCHK01-SEL";
  $FieldParam[34]="O1_DCHK02-SEL";
  $FieldParam[35]="O1_DCHK03-SEL";
  $FieldParam[36]="O1_DCHK04-SEL";
  $FieldParam[37]="O1_DCHK05-SEL";
  $FieldParam[38]="O1_DCHK06-SEL";
  $FieldParam[39]="O1_DCHK07-SEL";
  $FieldParam[40]="O1_DCHK08-SEL";
  $FieldParam[41]="O1_DCHK09-SEL";
  $FieldParam[42]="O1_DCHK10-SEL";
  $FieldParam[43]="O1_DCHK01-SEL";
  $FieldParam[44]="O1_DCHK02-SEL";
  $FieldParam[45]="O1_DCHK03-SEL";
  $FieldParam[46]="O1_DCHK04-SEL";
  $FieldParam[47]="O1_DCHK05-SEL";
  $FieldParam[48]="O1_DCHK06-SEL";
  $FieldParam[49]="O1_DCHK07-SEL";
  $FieldParam[50]="O1_DCHK08-SEL";
  $FieldParam[51]="O1_DCHK09-SEL";
  $FieldParam[52]="O1_DCHK10-SEL";
  $FieldParam[53]="";
  $FieldParam[54]="";
  $FieldParam[55]="";
  $FieldParam[56]="";
  $FieldParam[57]="";
  $FieldParam[58]="";
  $FieldParam[59]="";
  $FieldParam[60]="";
  $FieldParam[61]="";
  $FieldParam[62]="";
  $FieldParam[63]="1950::1951::1952::1953::1954::1955::1956::1957::1958::1959::1960::1961::1962::1963::1964::1965::1966::1967::1968::1969::1970::1971::1972::1973::1974::1975::1976::1977::1978::1979::1980::1981::1982::1983::1984::1985::1986::1987::1988::1989::1990::1991::1992::1993::1994::1995::1996::1997::1998::1999::2000::2001::2002::2003::2004::2005::2006::2007::2008::2009::2010";
  $FieldParam[64]="北海道::青森県::岩手県::宮城県::秋田県::山形県::福島県::茨城県::栃木県::群馬県::埼玉県::千葉県::東京都::神奈川県::新潟県::富山県::石川県::福井県::山梨県::長野県::岐阜県::静岡県::愛知県::三重県::滋賀県::京都府::大阪府::兵庫県::奈良県::和歌山県::鳥取県::島根県::岡山県::広島県::山口県::徳島県::香川県::愛媛県::高知県::福岡県::佐賀県::長崎県::熊本県::大分県::宮崎県::鹿児島県::沖縄県::海外";
  $FieldParam[65]="野球(硬式)部::野球(軟式)部::ソフトボール部::サッカー部::テニス(硬式)部::ソフトテニス(軟式テニス) 部::ラグビー部::バスケットボール部::バレーボール部::ゴルフ部::ハンドボール部::水球部::アメリカンフットボール部::卓球部::スカッシュ部::ラクロス部::フットサル部::スキー部::山岳部::ワンダーフォーゲル部::剣道部::柔道部::空手部::少林寺拳法部::合気道部::太極拳部::弓道部::アーチェリー部::体操部::新体操部::水泳部::陸上部::バドミントン部::チアリーディング部::応援団部::バトン部::ボクシング部::フェンシング部::グラウンドホッケー部::ダンス部::バレエ部::アイスホッケー部::スケート(スピード、フィギュア)部::射撃部::重量挙部::釣り部::相撲部::レスリング部::自転車部::ボート部::馬術部::チアダンス部::アウトドア部::自動車部::吹奏楽部::ブラスバンド部::管弦楽部::器楽部::合奏弦楽器部::軽音楽部::琴部::筝曲部::コーラス部::和太鼓部::フォークソング部::音楽部::ゴスペル部::ハンドベル部::グリー部::数学部::物理部::工学部::化学部::地学部::天文部::生物部::自然科学部::科学部::美術部::写真部::伝統芸能部::茶道部::華道部::書道部::手芸部::料理部::家庭科部::園芸部::演劇部::ミュージカル部::文芸部::落語部::将棋部::囲碁部::チェス部::漫画部::イラスト部::パソコン部::新聞部::放送部::映画研究部::鉄道研究部::ボランティア部::英会話部::アニメ部::英語部::歴史部::社会部::クイズ部::かるた部::奇術(マジック)部::ディベート部::国際交流部::地理研究部::ESS部::コンピュータ部::インターアクト部::JRC（青少年赤十字）部::その他";
  $FieldParam[66]="O1_MSEL04-SEL";
  $FieldParam[67]="O1_MSEL05-SEL";
  $FieldParam[68]="O1_MSEL06-SEL";
  $FieldParam[69]="O1_MSEL07-SEL";
  $FieldParam[70]="O1_MSEL08-SEL";
  $FieldParam[71]="O1_MSEL09-SEL";
  $FieldParam[72]="O1_MSEL10-SEL";
  $FieldParam[73]="O1_MRDO01-SEL";
  $FieldParam[74]="O1_MRDO02-SEL";
  $FieldParam[75]="O1_MRDO03-SEL";
  $FieldParam[76]="O1_MRDO04-SEL";
  $FieldParam[77]="O1_MRDO05-SEL";
  $FieldParam[78]="O1_MRDO06-SEL";
  $FieldParam[79]="O1_MRDO07-SEL";
  $FieldParam[80]="O1_MRDO08-SEL";
  $FieldParam[81]="O1_MRDO09-SEL";
  $FieldParam[82]="O1_MRDO10-SEL";
  $FieldParam[83]="連絡が取りたい::同窓会募集::一緒に飲もう・遊ぼう::その他";
  $FieldParam[84]="O1_MCHK02-SEL";
  $FieldParam[85]="O1_MCHK03-SEL";
  $FieldParam[86]="O1_MCHK04-SEL";
  $FieldParam[87]="O1_MCHK05-SEL";
  $FieldParam[88]="O1_MCHK06-SEL";
  $FieldParam[89]="O1_MCHK07-SEL";
  $FieldParam[90]="O1_MCHK08-SEL";
  $FieldParam[91]="O1_MCHK09-SEL";
  $FieldParam[92]="O1_MCHK10-SEL";
  $FieldParam[93]="公開中::非公開";
  $FieldParam[94]="";
  $FieldParam[95]="";
  $FieldParam[96]="";
  $FieldParam[97]="";
  $FieldParam[98]="";
  $FieldParam[99]="";
  $FieldParam[100]="";
  $FieldParam[101]="";
  $FieldParam[102]="";
  $FieldParam[103]="";
  $FieldParam[104]="";
  $FieldParam[105]="";
  $FieldParam[106]="";
  $FieldParam[107]="";
  $FieldParam[108]="";
  $FieldParam[109]="";
  $FieldParam[110]="";
  $FieldParam[111]="";
  $FieldParam[112]="";
  $FieldParam[113]="";
  $FieldParam[114]="";
  $FieldParam[115]="";

//全フィールド数
	$FieldMax=115;

//キーフィールドの設定
	$FieldKey=0;

//リスト行数
	$PageSize=20;

//ASPファイル名
	$aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
	$filepath1="/a_blog1/data/";

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
  $FieldValue[91]="";
  $FieldValue[92]="";
  $FieldValue[93]="";
  $FieldValue[94]="";
  $FieldValue[95]="";
  $FieldValue[96]="";
  $FieldValue[97]="";
  $FieldValue[98]="";
  $FieldValue[99]="";
  $FieldValue[100]="";
  $FieldValue[101]="";
  $FieldValue[102]="";
  $FieldValue[103]="";
  $FieldValue[104]="";
  $FieldValue[105]="";
  $FieldValue[106]="";
  $FieldValue[107]="";
  $FieldValue[108]="";
  $FieldValue[109]="";
  $FieldValue[110]="";
  $FieldValue[111]="";
  $FieldValue[112]="";
  $FieldValue[113]="";
  $FieldValue[114]="";
  $FieldValue[115]="";

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

	$str="WHERE ID>0 ";

	if ($word!=""){
		$str=$str."AND MID like '%".$word."%' ";
	} 

	$str=$str."ORDER BY ID desc";

	$function_ret=$str;

	return $function_ret;
} 

//=========================================================================================================
//名前 SQL条件（WHERE ･･･ ORDER BY･･･）
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function ListSQLSearch($sort,$word,$sel1,$sel2)
{
	//extract($GLOBALS);
	eval(globals());

	$str="DAT_O1.ID>0";

	if ($word!=""){
		$p=0;
		$ltmp2="";
		$tmp1=explode("\t",$word);
		for ($i=0; $i<count($tmp1); $i++) {
			$tmp2=explode(":",$tmp1[$i]);
			if($tmp2[0]!=$ltmp2){
				$p++;
				$tmp3[$p]=$tmp2[0]." like \"%".$tmp1[$i]."%\"";
				$ltmp2=$tmp2[0];
			} else {
				if($tmp3[$p]!=""){
					$tmp3[$p].=" or ";
				}
				$tmp3[$p].=$tmp2[0]." like \"%".$tmp1[$i]."%\"";
			}
		}
		$tmp="";
		for ($i=0; $i<=$p; $i++) {
			if($tmp3[$i]!=""){
				if($tmp!=""){
					$tmp.=" and ";
				}
				$tmp.="(".$tmp3[$i].")";
			}
		}
		$str=$str." AND ".$tmp;
	} 
	$str=str_replace("%MID:", "%", $str);
	$str=str_replace("(MID", "(DAT_O1.MID", $str);

	if ($sel1!=""){
		$str=$str." AND DAT_O1.O1_MSEL01 like '%".$sel1."%'";
	} 

	if ($sel2!=""){
		$str=$str." AND DAT_O1.O1_MSEL02 like '%".$sel2."%'";
	} 

	if ($sort=="3"){
		$str=$str." AND cast(DAT_MATCH.POINT as SIGNED)>=50";
	} 

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
