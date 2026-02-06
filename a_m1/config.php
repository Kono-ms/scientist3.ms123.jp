<?php

//テーブル名の指定
  $TableName="DAT_M1";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="MID";
  $FieldName[2]="EMAIL";
  $FieldName[3]="PASS";
  $FieldName[4]="SOCIALID";
  $FieldName[5]="M1_DVAL01";
  $FieldName[6]="M1_DVAL02";
  $FieldName[7]="M1_DVAL03";
  $FieldName[8]="M1_DVAL04";
  $FieldName[9]="M1_DVAL05";
  $FieldName[10]="M1_DVAL06";
  $FieldName[11]="M1_DVAL07";
  $FieldName[12]="M1_DVAL08";
  $FieldName[13]="M1_DVAL09";
  $FieldName[14]="M1_DVAL10";
  $FieldName[15]="M1_DTXT01";
  $FieldName[16]="M1_DTXT02";
  $FieldName[17]="M1_DTXT03";
  $FieldName[18]="M1_DTXT04";
  $FieldName[19]="M1_DTXT05";
  $FieldName[20]="M1_DTXT06";
  $FieldName[21]="M1_DTXT07";
  $FieldName[22]="M1_DTXT08";
  $FieldName[23]="M1_DTXT09";
  $FieldName[24]="M1_DTXT10";
  $FieldName[25]="M1_DSEL01";
  $FieldName[26]="M1_DSEL02";
  $FieldName[27]="M1_DSEL03"; // タイムゾーンをプルダウンからdatalistに変更
  $FieldName[28]="M1_DSEL04";
  $FieldName[29]="M1_DSEL05";
  $FieldName[30]="M1_DSEL06";
  $FieldName[31]="M1_DSEL07";
  $FieldName[32]="M1_DSEL08";
  $FieldName[33]="M1_DSEL09";
  $FieldName[34]="M1_DSEL10";
  $FieldName[35]="M1_DRDO01";
  $FieldName[36]="M1_DRDO02";
  $FieldName[37]="M1_DRDO03";
  $FieldName[38]="M1_DRDO04";
  $FieldName[39]="M1_DRDO05";
  $FieldName[40]="M1_DRDO06";
  $FieldName[41]="M1_DRDO07";
  $FieldName[42]="M1_DRDO08";
  $FieldName[43]="M1_DRDO09";
  $FieldName[44]="M1_DRDO10";
  $FieldName[45]="M1_DCHK01";
  $FieldName[46]="M1_DCHK02";
  $FieldName[47]="M1_DCHK03";
  $FieldName[48]="M1_DCHK04";
  $FieldName[49]="M1_DCHK05";
  $FieldName[50]="M1_DCHK06";
  $FieldName[51]="M1_DCHK07";
  $FieldName[52]="M1_DCHK08";
  $FieldName[53]="M1_DCHK09";
  $FieldName[54]="M1_DCHK10";
  $FieldName[55]="M1_DFIL01";
  $FieldName[56]="M1_DFIL02";
  $FieldName[57]="M1_DFIL03";
  $FieldName[58]="M1_DFIL04";
  $FieldName[59]="M1_DFIL05";
  $FieldName[60]="M1_DFIL06";
  $FieldName[61]="M1_DFIL07";
  $FieldName[62]="M1_DFIL08";
  $FieldName[63]="M1_DFIL09";
  $FieldName[64]="M1_DFIL10";
  $FieldName[65]="M1_MSEL01";
  $FieldName[66]="M1_MSEL02";
  $FieldName[67]="M1_MSEL03";
  $FieldName[68]="M1_MSEL04";
  $FieldName[69]="M1_MSEL05";
  $FieldName[70]="M1_MSEL06";
  $FieldName[71]="M1_MSEL07";
  $FieldName[72]="M1_MSEL08";
  $FieldName[73]="M1_MSEL09";
  $FieldName[74]="M1_MSEL10";
  $FieldName[75]="M1_MRDO01";
  $FieldName[76]="M1_MRDO02";
  $FieldName[77]="M1_MRDO03";
  $FieldName[78]="M1_MRDO04";
  $FieldName[79]="M1_MRDO05";
  $FieldName[80]="M1_MRDO06";
  $FieldName[81]="M1_MRDO07";
  $FieldName[82]="M1_MRDO08";
  $FieldName[83]="M1_MRDO09";
  $FieldName[84]="M1_MRDO10";
  $FieldName[85]="M1_MCHK01";
  $FieldName[86]="M1_MCHK02";
  $FieldName[87]="M1_MCHK03";
  $FieldName[88]="M1_MCHK04";
  $FieldName[89]="M1_MCHK05";
  $FieldName[90]="M1_MCHK06";
  $FieldName[91]="M1_MCHK07";
  $FieldName[92]="M1_MCHK08";
  $FieldName[93]="M1_MCHK09";
  $FieldName[94]="M1_MCHK10";
  $FieldName[95]="ENABLE";
  $FieldName[96]="NEWDATE";
  $FieldName[97]="EDITDATE";
  $FieldName[98]="M1_ETC01";	// 規約に同意
  $FieldName[99]="M1_ETC02";
  $FieldName[100]="M1_ETC03";
  $FieldName[101]="M1_ETC04";
  $FieldName[102]="M1_ETC05";
  $FieldName[103]="M1_ETC06";
  $FieldName[104]="M1_ETC07"; //I have read and agree to the policies and procedures outlined by scientist3
  $FieldName[105]="M1_ETC08";
  $FieldName[106]="M1_ETC09";
  $FieldName[107]="M1_ETC10";
  $FieldName[108]="M1_ETC11";
  $FieldName[109]="M1_ETC12";
  $FieldName[110]="M1_ETC13";
  $FieldName[111]="M1_ETC14";
  $FieldName[112]="M1_ETC15"; // Youtube動画 の公開する
  $FieldName[113]="M1_ETC16"; // Banking Details
  $FieldName[114]="M1_ETC17"; // カテゴリー第1階層
  $FieldName[115]="M1_ETC18"; // カテゴリー第2階層
  $FieldName[116]="M1_ETC19"; // カテゴリー第3階層
  $FieldName[117]="M1_ETC20"; // カテゴリー第4階層
  $FieldName[118]="M1_DVAL11";
  $FieldName[119]="M1_DVAL12";
  $FieldName[120]="M1_DVAL13";
  $FieldName[121]="M1_DVAL14";
  $FieldName[122]="M1_DVAL15";
  $FieldName[123]="M1_DVAL16";
  $FieldName[124]="M1_DVAL17";
  $FieldName[125]="M1_DVAL18";
  $FieldName[126]="M1_DVAL19";
  $FieldName[127]="M1_DVAL20";
  $FieldName[128]="M1_DVAL21";
  $FieldName[129]="M1_DVAL22";
  $FieldName[130]="M1_DVAL23";
  $FieldName[131]="M1_DVAL24";
  $FieldName[132]="M1_DVAL25";
  $FieldName[133]="M1_DVAL26";
  $FieldName[134]="M1_DVAL27";
  $FieldName[135]="M1_DVAL28";
  $FieldName[136]="M1_DVAL29";
  $FieldName[137]="M1_DVAL30";
  $FieldName[138]="M1_DTXT11";
  $FieldName[139]="M1_DTXT12";
  $FieldName[140]="M1_DTXT13";
  $FieldName[141]="M1_DTXT14";
  $FieldName[142]="M1_DTXT15";
  $FieldName[143]="M1_DTXT16";
  $FieldName[144]="M1_DTXT17";
  $FieldName[145]="M1_DTXT18";
  $FieldName[146]="M1_DTXT19";
  $FieldName[147]="M1_DTXT20";
  $FieldName[148]="M1_DTXT21";
  $FieldName[149]="M1_DTXT22";
  $FieldName[150]="M1_DTXT23";
  $FieldName[151]="M1_DTXT24";
  $FieldName[152]="M1_DTXT25";
  $FieldName[153]="M1_DTXT26";
  $FieldName[154]="M1_DTXT27";
  $FieldName[155]="M1_DTXT28";
  $FieldName[156]="M1_DTXT29";
  $FieldName[157]="M1_DTXT30";
  $FieldName[158]="M1_ETC21";
  $FieldName[159]="M1_ETC22";
  $FieldName[160]="M1_ETC23";
  $FieldName[161]="M1_ETC24";
  $FieldName[162]="M1_ETC25";
  $FieldName[163]="M1_ETC26";
  $FieldName[164]="M1_ETC27";
  $FieldName[165]="M1_ETC28";
  $FieldName[166]="M1_ETC29";
  $FieldName[167]="M1_ETC30";
  $FieldName[168]="M1_ETC31";
  $FieldName[169]="M1_ETC32";
  $FieldName[170]="M1_ETC33";
  $FieldName[171]="M1_ETC34";
  $FieldName[172]="M1_ETC35";
  $FieldName[173]="M1_ETC36";
  $FieldName[174]="M1_ETC37";
  $FieldName[175]="M1_ETC38";
  $FieldName[176]="M1_ETC39";
  $FieldName[177]="M1_ETC40";
  $FieldName[178]="M1_ETC41";
  $FieldName[179]="M1_ETC42";
  $FieldName[180]="M1_ETC43";
  $FieldName[181]="M1_ETC44";
  $FieldName[182]="M1_ETC45";
  $FieldName[183]="M1_ETC46";
  $FieldName[184]="M1_ETC47";
  $FieldName[185]="M1_ETC48";
  $FieldName[186]="M1_ETC49";
  $FieldName[187]="M1_ETC50";
  $FieldName[188]="M1_ETC51";
  $FieldName[189]="M1_ETC52";
  $FieldName[190]="M1_ETC53";
  $FieldName[191]="M1_ETC54";
  $FieldName[192]="M1_ETC55";
  $FieldName[193]="M1_ETC56";
  $FieldName[194]="M1_ETC57";
  $FieldName[195]="M1_ETC58";
  $FieldName[196]="M1_ETC59";
  $FieldName[197]="M1_ETC60";
  $FieldName[198]="M1_ETC61";
  $FieldName[199]="M1_ETC62";
  $FieldName[200]="M1_ETC63";
  $FieldName[201]="M1_ETC64";
  $FieldName[202]="M1_ETC65";
  $FieldName[203]="M1_ETC66";
  $FieldName[204]="M1_ETC67";
  $FieldName[205]="M1_ETC68";
  $FieldName[206]="M1_ETC69";
  $FieldName[207]="M1_ETC70";
  $FieldName[208]="M1_ETC71";
  $FieldName[209]="M1_ETC72";
  $FieldName[210]="M1_ETC73";
  $FieldName[211]="M1_ETC74";
  $FieldName[212]="M1_ETC75";
  $FieldName[213]="M1_ETC76";
  $FieldName[214]="M1_ETC77";
  $FieldName[215]="M1_ETC78";
  $FieldName[216]="M1_ETC79";
  $FieldName[217]="M1_ETC80";
  $FieldName[218]="M1_ETC81";
  $FieldName[219]="M1_ETC82";
  $FieldName[220]="M1_ETC83";
  $FieldName[221]="M1_ETC84";
  $FieldName[222]="M1_ETC85";
  $FieldName[223]="M1_ETC86";
  $FieldName[224]="M1_ETC87";
  $FieldName[225]="M1_ETC88";
  $FieldName[226]="M1_ETC89";
  $FieldName[227]="M1_ETC90";
  $FieldName[228]="M1_ETC91";
  $FieldName[229]="M1_ETC92";
  $FieldName[230]="M1_ETC93";
  $FieldName[231]="M1_ETC94";
  $FieldName[232]="M1_ETC95";
  $FieldName[233]="M1_ETC96";
  $FieldName[234]="M1_ETC97"; // 電話番号の国コード
  $FieldName[235]="M1_ETC98"; // アカウント情報（社内メモ）
  $FieldName[236]="M1_ETC99"; //CEO/Exective Director

  $FieldName[237]="M1_ETC100"; // Bank branch
  $FieldName[238]="M1_ETC101"; // Bank address
  $FieldName[239]="M1_ETC102"; // Intermediary Bank name
  $FieldName[240]="M1_ETC103"; // Intermediary Bank branch
  $FieldName[241]="M1_ETC104"; // Intermediary Bank address
  $FieldName[242]="M1_ETC105"; // 適格請求書発行事業者登録番号
  $FieldName[243]="M1_ETC106"; // Banking DetailsのBilling Address 2 Organization Name
  $FieldName[244]="M1_ETC107"; // Banking DetailsのBilling Address 2 Name
  $FieldName[245]="M1_ETC108"; // Banking DetailsのBilling Address 2 Attention
  $FieldName[246]="M1_ETC109"; // Banking DetailsのBilling Address 2 Address Line1
  $FieldName[247]="M1_ETC110"; // Banking DetailsのBilling Address 2 Address Line2
  $FieldName[248]="M1_ETC111"; // Banking DetailsのBilling Address 2 City/Region
  $FieldName[249]="M1_ETC112"; // Banking DetailsのBilling Address 2 State/Province
  $FieldName[250]="M1_ETC113"; // Banking DetailsのBilling Address 2 Zip/Postal Code
  $FieldName[251]="M1_ETC114"; // Banking DetailsのBilling Address 2 Country
  $FieldName[252]="M1_ETC115"; // Banking DetailsのBilling Address 3 Organization Name
  $FieldName[253]="M1_ETC116"; // Banking DetailsのBilling Address 3 Name
  $FieldName[254]="M1_ETC117"; // Banking DetailsのBilling Address 3 Attention
  $FieldName[255]="M1_ETC118"; // Banking DetailsのBilling Address 3 Address Line1
  $FieldName[256]="M1_ETC119"; // Banking DetailsのBilling Address 3 Address Line2
  $FieldName[257]="M1_ETC120"; // Banking DetailsのBilling Address 3 City/Region
  $FieldName[258]="M1_ETC121"; // Banking DetailsのBilling Address 3 State/Province
  $FieldName[259]="M1_ETC122"; // Banking DetailsのBilling Address 3 Ziop/Postal Code
  $FieldName[260]="M1_ETC123"; // Banking DetailsのBilling Address 3 Country
  $FieldName[261]="M1_ETC124"; // 初回ポップアップ制御フラグ(1:未表示、3:表示済)
  $FieldName[262]="M1_ETC125"; // SHIP TO 1 Explanation
  $FieldName[263]="M1_ETC126"; // SHIP TO 2 Explanation
  $FieldName[264]="M1_ETC127"; // SHIP TO 3 Explanation
  $FieldName[265]="M1_ETC128"; // Head Office Location Organization Name
  $FieldName[266]="M1_ETC129"; // Head Office Location Address Line1
  $FieldName[267]="M1_ETC130"; // Head Office Location Address Line2
  $FieldName[268]="M1_ETC131"; // Head Office Location City/Province
  $FieldName[269]="M1_ETC132"; // Head Office Location ZIP/Postal Code
  $FieldName[270]="M1_ETC133"; // Head Office Location Country
  $FieldName[271]="M1_ETC134";
  $FieldName[272]="M1_ETC135";
  $FieldName[273]="M1_ETC136";
  $FieldName[274]="M1_ETC137";
  $FieldName[275]="M1_ETC138";
  $FieldName[276]="M1_ETC139";
  $FieldName[277]="M1_ETC140";
  $FieldName[278]="M1_ETC141";
  $FieldName[279]="M1_ETC142";
  $FieldName[280]="M1_ETC143";
  $FieldName[281]="M1_ETC144";
  $FieldName[282]="M1_ETC145";
  $FieldName[283]="M1_ETC146";
  $FieldName[284]="M1_ETC147";
  $FieldName[285]="M1_ETC148";
  $FieldName[286]="M1_ETC149";
  $FieldName[287]="M1_ETC150";


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
  $FieldValue[116]="";
  $FieldValue[117]="";
  $FieldValue[118]="企業";
  $FieldValue[119]="";
  $FieldValue[120]="";
  $FieldValue[121]="";
  $FieldValue[122]="";
  $FieldValue[123]="";
  $FieldValue[124]="";
  $FieldValue[125]="";
  $FieldValue[126]="";
  $FieldValue[127]="";
  $FieldValue[128]="";
  $FieldValue[129]="";
  $FieldValue[130]="";
  $FieldValue[131]="";
  $FieldValue[132]="";
  $FieldValue[133]="";
  $FieldValue[134]="";
  $FieldValue[135]="";
  $FieldValue[136]="";
  $FieldValue[137]="";
  $FieldValue[138]="";
  $FieldValue[139]="";
  $FieldValue[140]="";
  $FieldValue[141]="";
  $FieldValue[142]="";
  $FieldValue[143]="";
  $FieldValue[144]="";
  $FieldValue[145]="";
  $FieldValue[146]="";
  $FieldValue[147]="";
  $FieldValue[148]="";
  $FieldValue[149]="";
  $FieldValue[150]="";
  $FieldValue[151]="";
  $FieldValue[152]="";
  $FieldValue[153]="";
  $FieldValue[154]="";
  $FieldValue[155]="";
  $FieldValue[156]="";
  $FieldValue[157]="";
  $FieldValue[158]="";
  $FieldValue[159]="";
  $FieldValue[160]="";
  $FieldValue[161]="";
  $FieldValue[162]="";
  $FieldValue[163]="";
  $FieldValue[164]="";
  $FieldValue[165]="";
  $FieldValue[166]="";
  $FieldValue[167]="";
  $FieldValue[168]="";
  $FieldValue[169]="";
  $FieldValue[170]="";
  $FieldValue[171]="";
  $FieldValue[172]="";
  $FieldValue[173]="";
  $FieldValue[174]="";
  $FieldValue[175]="";
  $FieldValue[176]="";
  $FieldValue[177]="";
  $FieldValue[178]="";
  $FieldValue[179]="";
  $FieldValue[180]="";
  $FieldValue[181]="";
  $FieldValue[182]="";
  $FieldValue[183]="";
  $FieldValue[184]="";
  $FieldValue[185]="";
  $FieldValue[186]="";
$FieldValue[187]="";
$FieldValue[188]="";
$FieldValue[189]="";
$FieldValue[190]="";
$FieldValue[191]="";
$FieldValue[192]="";
$FieldValue[193]="";
$FieldValue[194]="";
$FieldValue[195]="";
$FieldValue[196]="";
$FieldValue[197]="";
$FieldValue[198]="";
$FieldValue[199]="";
$FieldValue[200]="";
$FieldValue[201]="";
$FieldValue[202]="";
$FieldValue[203]="";
$FieldValue[204]="";
$FieldValue[205]="";
$FieldValue[206]="";
$FieldValue[207]="";
$FieldValue[208]="";
$FieldValue[209]="";
$FieldValue[210]="";
$FieldValue[211]="";
$FieldValue[212]="";
$FieldValue[213]="";
$FieldValue[214]="";
$FieldValue[215]="";
$FieldValue[216]="";
$FieldValue[217]="";
$FieldValue[218]="";
$FieldValue[219]="";
$FieldValue[220]="";
$FieldValue[221]="";
$FieldValue[222]="";
$FieldValue[223]="";
$FieldValue[224]="";
$FieldValue[225]="";
$FieldValue[226]="";
$FieldValue[227]="";
  $FieldValue[228]="";
  $FieldValue[229]="";
  $FieldValue[230]="";
  $FieldValue[231]="";
  $FieldValue[232]="";
  $FieldValue[233]="";
  $FieldValue[234]="";
  $FieldValue[235]="";
  $FieldValue[236]="";

  $FieldValue[237]="";
  $FieldValue[238]="";
  $FieldValue[239]="";
  $FieldValue[240]="";
  $FieldValue[241]="";
  $FieldValue[242]="";
  $FieldValue[243]="";
  $FieldValue[244]="";
  $FieldValue[245]="";
  $FieldValue[246]="";
  $FieldValue[247]="";
  $FieldValue[248]="";
  $FieldValue[249]="";
  $FieldValue[250]="";
  $FieldValue[251]="";
  $FieldValue[252]="";
  $FieldValue[253]="";
  $FieldValue[254]="";
  $FieldValue[255]="";
  $FieldValue[256]="";
  $FieldValue[257]="";
  $FieldValue[258]="";
  $FieldValue[259]="";
  $FieldValue[260]="";
  $FieldValue[261]="";
  $FieldValue[262]="";
  $FieldValue[263]="";
  $FieldValue[264]="";
  $FieldValue[265]="";
  $FieldValue[266]="";
  $FieldValue[267]="";
  $FieldValue[268]="";
  $FieldValue[269]="";
  $FieldValue[270]="";
  $FieldValue[271]="";
  $FieldValue[272]="";
  $FieldValue[273]="";
  $FieldValue[274]="";
  $FieldValue[275]="";
  $FieldValue[276]="";
  $FieldValue[277]="";
  $FieldValue[278]="";
  $FieldValue[279]="";
  $FieldValue[280]="";
  $FieldValue[281]="";
  $FieldValue[282]="";
  $FieldValue[283]="";
  $FieldValue[284]="";
  $FieldValue[285]="";
  $FieldValue[286]="";
  $FieldValue[287]="";

//入力フィールドの書式　0-TEXT, 1-SELECT, 2-RADIO, 3-CHECKBOX, 4-FILE
  $FieldAtt[0]="0";
  $FieldAtt[1]="0";
  $FieldAtt[2]="0";
  $FieldAtt[3]="0";
  $FieldAtt[4]="0";
  $FieldAtt[5]="0";
  $FieldAtt[6]="0";
  $FieldAtt[7]="0";
  $FieldAtt[8]="1";
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
  $FieldAtt[23]="0";
  $FieldAtt[24]="0";
  $FieldAtt[25]="1";
  $FieldAtt[26]="1";

  $FieldAtt[27]="1"; // プルダウンからdatalistに変更

  $FieldAtt[28]="1";
  $FieldAtt[29]="1";
  $FieldAtt[30]="1";
  $FieldAtt[31]="1";
  $FieldAtt[32]="1";
  $FieldAtt[33]="1";
  $FieldAtt[34]="1";
  $FieldAtt[35]="2";
  $FieldAtt[36]="0";
  $FieldAtt[37]="0";
  $FieldAtt[38]="0";
  $FieldAtt[39]="0";
  $FieldAtt[40]="4";
  $FieldAtt[41]="0";
  $FieldAtt[42]="2";
  $FieldAtt[43]="1";
  $FieldAtt[44]="1";
  $FieldAtt[45]="3";
  $FieldAtt[46]="1";
  $FieldAtt[47]="1";
  $FieldAtt[48]="1";
  $FieldAtt[49]="1";
  $FieldAtt[50]="1";
  $FieldAtt[51]="1";
  $FieldAtt[52]="1";
  $FieldAtt[53]="1";
  $FieldAtt[54]="1";
  $FieldAtt[55]="4";
  $FieldAtt[56]="4";
  $FieldAtt[57]="4";
  $FieldAtt[58]="4";
  $FieldAtt[59]="4";
  $FieldAtt[60]="4";
  $FieldAtt[61]="4";
  $FieldAtt[62]="4";
  $FieldAtt[63]="4";
  $FieldAtt[64]="4";
  $FieldAtt[65]="0";
  $FieldAtt[66]="0";
  $FieldAtt[67]="0";
  $FieldAtt[68]="0";
  $FieldAtt[69]="0";
  $FieldAtt[70]="0";
  $FieldAtt[71]="2";
  $FieldAtt[72]="2";
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
  $FieldAtt[83]="2";
  $FieldAtt[84]="2";
  $FieldAtt[85]="3";
  $FieldAtt[86]="3";
  $FieldAtt[87]="3";
  $FieldAtt[88]="3";
  $FieldAtt[89]="3";
  $FieldAtt[90]="3";
  $FieldAtt[91]="3";
  $FieldAtt[92]="3";
  $FieldAtt[93]="3";
  $FieldAtt[94]="3";
  $FieldAtt[95]="2";
  $FieldAtt[96]="0";
  $FieldAtt[97]="0";
  $FieldAtt[98]="3";
  $FieldAtt[99]="0";
  $FieldAtt[100]="2";
  $FieldAtt[101]="4";
  $FieldAtt[102]="4";
  $FieldAtt[103]="0";
  $FieldAtt[104]="3";
  $FieldAtt[105]="0";
  $FieldAtt[106]="0";
  $FieldAtt[107]="0";
  $FieldAtt[108]="0";
  $FieldAtt[109]="0";
  $FieldAtt[110]="1";
  $FieldAtt[111]="0";
  $FieldAtt[112]="3";
  $FieldAtt[113]="0";
  $FieldAtt[114]="1";
  $FieldAtt[115]="1";
  $FieldAtt[116]="1";
  $FieldAtt[117]="1";
  $FieldAtt[118]="2";
  $FieldAtt[119]="0";
  $FieldAtt[120]="2";
  $FieldAtt[121]="0";
  $FieldAtt[122]="0";
  $FieldAtt[123]="0";
  $FieldAtt[124]="1";
  $FieldAtt[125]="0";
  $FieldAtt[126]="0";
  $FieldAtt[127]="0";
  $FieldAtt[128]="0";
  $FieldAtt[129]="0";
  $FieldAtt[130]="0";
  $FieldAtt[131]="0";
  $FieldAtt[132]="0";
  $FieldAtt[133]="0";
  $FieldAtt[134]="0";
  $FieldAtt[135]="0";
  $FieldAtt[136]="0";
  $FieldAtt[137]="0";
  $FieldAtt[138]="0";
  $FieldAtt[139]="0";
  $FieldAtt[140]="0";
  $FieldAtt[141]="0";
  $FieldAtt[142]="0";
  $FieldAtt[143]="0";
  $FieldAtt[144]="0";
  $FieldAtt[145]="0";
  $FieldAtt[146]="0";
  $FieldAtt[147]="0";
  $FieldAtt[148]="0";
  $FieldAtt[149]="0";
  $FieldAtt[150]="0";
  $FieldAtt[151]="0";
  $FieldAtt[152]="0";
  $FieldAtt[153]="0";
  $FieldAtt[154]="0";
  $FieldAtt[155]="0";
  $FieldAtt[156]="0";
  $FieldAtt[157]="0";
  $FieldAtt[158]="0";
  $FieldAtt[159]="0";
  $FieldAtt[160]="0";
  $FieldAtt[161]="0";
  $FieldAtt[162]="0";
  $FieldAtt[163]="0";
  $FieldAtt[164]="0";
  $FieldAtt[165]="0";
  $FieldAtt[166]="0";
  $FieldAtt[167]="0";
  $FieldAtt[168]="0";
  $FieldAtt[169]="0";
  $FieldAtt[170]="0";
  $FieldAtt[171]="0";
  $FieldAtt[172]="0";
  $FieldAtt[173]="0";
  $FieldAtt[174]="0";
  $FieldAtt[175]="0";
  $FieldAtt[176]="1";
  $FieldAtt[177]="1";
  $FieldAtt[178]="0";
  $FieldAtt[179]="0";
  $FieldAtt[180]="0";
  $FieldAtt[181]="0";
  $FieldAtt[182]="0";
  $FieldAtt[183]="4";
  $FieldAtt[184]="0";
  $FieldAtt[185]="0";
  $FieldAtt[186]="0";
  $FieldAtt[187]="2";
  $FieldAtt[188]="2";
  $FieldAtt[189]="2";
  $FieldAtt[190]="2";
  $FieldAtt[191]="2";
  $FieldAtt[192]="2";
  $FieldAtt[193]="2";
  $FieldAtt[194]="2";
  $FieldAtt[195]="2";
  $FieldAtt[196]="2";
  $FieldAtt[197]="2";
  $FieldAtt[198]="2";
  $FieldAtt[199]="2";
  $FieldAtt[200]="2";
  $FieldAtt[201]="2";
  $FieldAtt[202]="2";
  $FieldAtt[203]="2";
  $FieldAtt[204]="2";
  $FieldAtt[205]="2";
  $FieldAtt[206]="2";
  $FieldAtt[207]="2";
  $FieldAtt[208]="2";
  $FieldAtt[209]="2";
  $FieldAtt[210]="2";
  $FieldAtt[211]="2";
  $FieldAtt[212]="2";
  $FieldAtt[213]="2";
  $FieldAtt[214]="3";
  $FieldAtt[215]="3";
  $FieldAtt[216]="0";
  $FieldAtt[217]="0";
  $FieldAtt[218]="0";
  $FieldAtt[219]="0";
  $FieldAtt[220]="0";
  $FieldAtt[221]="0";
  $FieldAtt[222]="0";
  $FieldAtt[223]="0";
  $FieldAtt[224]="0";
  $FieldAtt[225]="0";
  $FieldAtt[226]="0";
  $FieldAtt[227]="0";
  $FieldAtt[228]="0";
  $FieldAtt[229]="0";
  $FieldAtt[230]="0";
  $FieldAtt[231]="0";
  $FieldAtt[232]="0";
  $FieldAtt[233]="0";
  $FieldAtt[234]="0";
  $FieldAtt[235]="0";
  $FieldAtt[236]="0";

  $FieldAtt[237]="0";
  $FieldAtt[238]="0";
  $FieldAtt[239]="0";
  $FieldAtt[240]="0";
  $FieldAtt[241]="0";
  $FieldAtt[242]="0";
  $FieldAtt[243]="0";
  $FieldAtt[244]="0";
  $FieldAtt[245]="0";
  $FieldAtt[246]="0";
  $FieldAtt[247]="0";
  $FieldAtt[248]="0";
  $FieldAtt[249]="0";
  $FieldAtt[250]="0";
  $FieldAtt[251]="1";
  $FieldAtt[252]="0";
  $FieldAtt[253]="0";
  $FieldAtt[254]="0";
  $FieldAtt[255]="0";
  $FieldAtt[256]="0";
  $FieldAtt[257]="0";
  $FieldAtt[258]="0";
  $FieldAtt[259]="0";
  $FieldAtt[260]="1";
  $FieldAtt[261]="0";
  $FieldAtt[262]="0";
  $FieldAtt[263]="0";
  $FieldAtt[264]="0";
  $FieldAtt[265]="0";
  $FieldAtt[266]="0";
  $FieldAtt[267]="0";
  $FieldAtt[268]="0";
  $FieldAtt[269]="0";
  $FieldAtt[270]="1";
  $FieldAtt[271]="0";
  $FieldAtt[272]="0";
  $FieldAtt[273]="0";
  $FieldAtt[274]="0";
  $FieldAtt[275]="0";
  $FieldAtt[276]="0";
  $FieldAtt[277]="0";
  $FieldAtt[278]="0";
  $FieldAtt[279]="0";
  $FieldAtt[280]="0";
  $FieldAtt[281]="0";
  $FieldAtt[282]="0";
  $FieldAtt[283]="0";
  $FieldAtt[284]="0";
  $FieldAtt[285]="0";
  $FieldAtt[286]="0";
  $FieldAtt[287]="0";

//SELECT, RADIO, CHECKBOX時の値群
  $FieldParam[0]="";
  $FieldParam[1]="";
  $FieldParam[2]="";
  $FieldParam[3]="";
  $FieldParam[4]="";
  $FieldParam[5]="";
  $FieldParam[6]="";
  $FieldParam[7]="";
  $FieldParam[8]=COUNTRY_LIST;
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
  $FieldParam[23]="";
  $FieldParam[24]="";
  $FieldParam[25]="北海道::青森県::岩手県::宮城県::秋田県::山形県::福島県::茨城県::栃木県::群馬県::埼玉県::千葉県::東京都::神奈川県::新潟県::富山県::石川県::福井県::山梨県::長野県::岐阜県::静岡県::愛知県::三重県::滋賀県::京都府::大阪府::兵庫県::奈良県::和歌山県::鳥取県::島根県::岡山県::広島県::山口県::徳島県::香川県::愛媛県::高知県::福岡県::佐賀県::長崎県::熊本県::大分県::宮崎県::鹿児島県::沖縄県";
  $FieldParam[26]="業種1::業種2";

  // プルダウンからdatalistに変更
  $FieldParam[27]="Africa/Johannesburg::Africa/Lagos::Africa/Windhoek::America/Adak::America/Anchorage::America/Argentina/Buenos_Aires::America/Bogota::America/Caracas::America/Chicago::America/Denver::America/Godthab::America/Guatemala::America/Halifax::America/Los_Angeles::America/Montevideo::America/New_York::America/Noronha::America/Phoenix::America/Santiago::America/Santo_Domingo::America/St_Johns::Asia/Baghdad::Asia/Baku::Asia/Beirut::Asia/Dhaka::Asia/Dubai::Asia/Irkutsk::Asia/Jakarta::Asia/Kabul::Asia/Kamchatka::Asia/Karachi::Asia/Kathmandu::Asia/Kolkata::Asia/Krasnoyarsk::Asia/Omsk::Asia/Rangoon::Asia/Shanghai::Asia/Tehran::Asia/Tokyo::Asia/Vladivostok::Asia/Yakutsk::Asia/Yekaterinburg::Atlantic/Azores::Atlantic/Cape_Verde::Australia/Adelaide::Australia/Brisbane::Australia/Darwin::Australia/Eucla::Australia/Lord_Howe::Australia/Sydney::Etc/GMT+12::Europe/Berlin::Europe/London::Europe/Moscow::Pacific/Apia::Pacific/Auckland::Pacific/Chatham::Pacific/Easter::Pacific/Gambier::Pacific/Honolulu::Pacific/Kiritimati::Pacific/Majuro::Pacific/Marquesas::Pacific/Norfolk::Pacific/Noumea::Pacific/Pago_Pago::Pacific/Pitcairn::Pacific/Tongatapu::UTC";

  $FieldParam[28]=COUNTRY_LIST;
  $FieldParam[29]=COUNTRY_LIST;
  $FieldParam[30]=COUNTRY_LIST;
  $FieldParam[31]=COUNTRY_LIST;
  $FieldParam[32]=COUNTRY_LIST;
  $FieldParam[33]=COUNTRY_LIST;
  $FieldParam[34]=COUNTRY_LIST;
  $FieldParam[35]="仮登録中::審査依頼::要再審査::本登録::本登録不可::登録変更審査中";
  $FieldParam[36]="M1_DRDO02-SEL";
  $FieldParam[37]="M1_DRDO03-SEL";
  $FieldParam[38]="M1_DRDO04-SEL";
  $FieldParam[39]="M1_DRDO05-SEL";
  $FieldParam[40]="M1_DRDO06-SEL";
  $FieldParam[41]="M1_DRDO07-SEL";
  $FieldParam[42]="W-9::1099";
  $FieldParam[43]="M1_DRDO09-SEL";
  $FieldParam[44]="M1_DRDO10-SEL";
  $FieldParam[45]="Use different contact information for purchase orders";
  $FieldParam[46]="M1_DCHK02-SEL";
  $FieldParam[47]="M1_DCHK03-SEL";
  $FieldParam[48]="M1_DCHK04-SEL";
  $FieldParam[49]="M1_DCHK05-SEL";
  $FieldParam[50]="M1_DCHK06-SEL";
  $FieldParam[51]="M1_DCHK07-SEL";
  $FieldParam[52]="M1_DCHK08-SEL";
  $FieldParam[53]="M1_DCHK09-SEL";
  $FieldParam[54]="M1_DCHK10-SEL";
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
  $FieldParam[65]="M1_MSEL01-SEL";
  $FieldParam[66]="M1_MSEL02-SEL";
  $FieldParam[67]="M1_MSEL03-SEL";
  $FieldParam[68]="M1_MSEL04-SEL";
  $FieldParam[69]="M1_MSEL05-SEL";
  $FieldParam[70]="M1_MSEL06-SEL";
  $FieldParam[71]="Yes::No";
  $FieldParam[72]="Yes::No";
  $FieldParam[73]="Yes::No";
  $FieldParam[74]="Yes::No";
  $FieldParam[75]="Yes::No";
  $FieldParam[76]="Yes::No";
  $FieldParam[77]="Yes::No";
  $FieldParam[78]="Yes::No";
  $FieldParam[79]="Yes::No";
  $FieldParam[80]="Yes::No";
  $FieldParam[81]="Yes::No";
  $FieldParam[82]="Yes::No";
  $FieldParam[83]="Yes::No";
  $FieldParam[84]="Yes::No";
  $FieldParam[85]="公開する";
  $FieldParam[86]="公開する";
  $FieldParam[87]="公開する";
  $FieldParam[88]="公開する";
  $FieldParam[89]="公開する";
  $FieldParam[90]="公開する";
  $FieldParam[91]="公開する";
  $FieldParam[92]="公開する";
  $FieldParam[93]="公開する";
  $FieldParam[94]="公開する";
  $FieldParam[95]="公開中::非公開";
  $FieldParam[96]="";
  $FieldParam[97]="";
  $FieldParam[98]="Agree to the Scientist3 Terms of Use";
  $FieldParam[99]="";
  $FieldParam[100]="";
  $FieldParam[101]="";
  $FieldParam[102]="";
  $FieldParam[103]="";
  $FieldParam[104]="agree";
  $FieldParam[105]="";
  $FieldParam[106]="";
  $FieldParam[107]="";
  $FieldParam[108]="";
  $FieldParam[109]="";
  $FieldParam[110]="";
  $FieldParam[111]="";
  $FieldParam[112]="公開する";
  $FieldParam[113]="";
  $FieldParam[114]="第1階層";
  $FieldParam[115]="第2階層";
  $FieldParam[116]="第3階層";
  $FieldParam[117]="第4階層";
  $FieldParam[118]="企業::個人";
  $FieldParam[119]="";
  $FieldParam[120]="Yes::No";
  $FieldParam[121]="";
  $FieldParam[122]="";
  $FieldParam[123]="";
  $FieldParam[124]="New startup(0-1M USD TTM Revenue)::Small business(1-10M USD TTM Revenue)::Mid-Sized business(10-100M USD TTM Revenue)::Large-Sized business(>100M USD TTM Revenue)";
  $FieldParam[125]="";
  $FieldParam[126]="";
  $FieldParam[127]="";
  $FieldParam[128]="";
  $FieldParam[129]="";
  $FieldParam[130]="";
  $FieldParam[131]="";
  $FieldParam[132]="";
  $FieldParam[133]="";
  $FieldParam[134]="";
  $FieldParam[135]="";
  $FieldParam[136]="";
  $FieldParam[137]="";
  $FieldParam[138]="";
  $FieldParam[139]="";
  $FieldParam[140]="";
  $FieldParam[141]="";
  $FieldParam[142]="";
  $FieldParam[143]="";
  $FieldParam[144]="";
  $FieldParam[145]="";
  $FieldParam[146]="";
  $FieldParam[147]="";
  $FieldParam[148]="";
  $FieldParam[149]="";
  $FieldParam[150]="";
  $FieldParam[151]="";
  $FieldParam[152]="";
  $FieldParam[153]="";
  $FieldParam[154]="";
  $FieldParam[155]="";
  $FieldParam[156]="";
  $FieldParam[157]="";
  $FieldParam[158]="";
  $FieldParam[159]="";
  $FieldParam[160]="";
  $FieldParam[161]="";
  $FieldParam[162]="";
  $FieldParam[163]="";
  $FieldParam[164]="";
  $FieldParam[165]="";
  $FieldParam[166]="";
  $FieldParam[167]="";
  $FieldParam[168]="";
  $FieldParam[169]="";
  $FieldParam[170]="";
  $FieldParam[171]="";
  $FieldParam[172]="";
  $FieldParam[173]="";
  $FieldParam[174]="";
  $FieldParam[175]="";
  $FieldParam[176]=COUNTRY_LIST;
  $FieldParam[177]="US Dollar::British Pound::Euro::Japanese Yen";
  $FieldParam[178]="";
  $FieldParam[179]="";
  $FieldParam[180]="";
  $FieldParam[181]="";
  $FieldParam[182]="";
  $FieldParam[183]="";
  $FieldParam[184]="";
  $FieldParam[185]="";
  $FieldParam[186]="";
$FieldParam[187]="Yes::No";
$FieldParam[188]="Yes::No";
$FieldParam[189]="Yes::No";
$FieldParam[190]="Yes::No";
$FieldParam[191]="Yes::No";
$FieldParam[192]="Yes::No";
$FieldParam[193]="Yes::No";
$FieldParam[194]="Yes::No";
$FieldParam[195]="Yes::No";
$FieldParam[196]="Yes::No";
$FieldParam[197]="Yes::No";
$FieldParam[198]="Yes::No";
$FieldParam[199]="Yes::No";
$FieldParam[200]="Yes::No";
$FieldParam[201]="Yes::No";
$FieldParam[202]="Yes::No";
$FieldParam[203]="Yes::No";
$FieldParam[204]="Yes::No";
$FieldParam[205]="Yes::No";
$FieldParam[206]="Yes::No";
$FieldParam[207]="Yes::No";
$FieldParam[208]="Yes::No";
$FieldParam[209]="Yes::No";
$FieldParam[210]="Yes::No";
$FieldParam[211]="Yes::No";
$FieldParam[212]="Yes::No";
$FieldParam[213]="Yes::No";

// echo "<!--param:".$_GET['param']."-->";
if($_GET['param'] == 'Agreements2e') {
  //$FieldParam[214]="I have read and to the volue,Evidence and Access Service Agreement";
  $FieldParam[214]="I have read Terms 1) and 2), fully understand their contents, and agree to these Terms.";
  $FieldParam[215]="I hereby confirm that I have the authority to enter into a binding agreement on behalf of [M1_DVAL01]";
}
else {
  $url=$_SERVER['REQUEST_URI'];
  if(strpos($url, "/a_m1/")!==false){
    $FieldParam[214]="I have read Terms 1) and 2), fully understand their contents, and agree to these Terms.::私は、本規約1)および2)を読み、その内容を完全に理解し、本規約に同意します。";
    $FieldParam[215]="I hereby confirm that I have the authority to enter into a binding agreement on behalf of [M1_DVAL01]::私は、[M1_DVAL01]を代表して拘束力のある契約を締結する権限を有することをここに確認します。";
  }else{
    $FieldParam[214]="私は、本規約1)および2)を読み、その内容を完全に理解し、本規約に同意します。";
    $FieldParam[215]="私は、[M1_DVAL01]を代表して拘束力のある契約を締結する権限を有することをここに確認します。";
  }
  
}


// echo "<!--FieldParam[214]:".$FieldParam[214]."-->";
$FieldParam[216]="";
$FieldParam[217]="";
$FieldParam[218]="";
$FieldParam[219]="";
$FieldParam[220]="";
$FieldParam[221]="";
$FieldParam[222]="";
$FieldParam[223]="";
$FieldParam[224]="";
$FieldParam[225]="";
$FieldParam[226]="";
$FieldParam[227]="";
  $FieldParam[228]="";
  $FieldParam[229]="";
  $FieldParam[230]="";
  $FieldParam[231]="";
  $FieldParam[232]="";
  $FieldParam[233]="";
  $FieldParam[234]="";
  $FieldParam[235]="";
  $FieldParam[236]="";

  $FieldParam[237]="";
  $FieldParam[238]="";
  $FieldParam[239]="";
  $FieldParam[240]="";
  $FieldParam[241]="";
  $FieldParam[242]="";
  $FieldParam[243]="";
  $FieldParam[244]="";
  $FieldParam[245]="";
  $FieldParam[246]="";
  $FieldParam[247]="";
  $FieldParam[248]="";
  $FieldParam[249]="";
  $FieldParam[250]="";
  $FieldParam[251]=COUNTRY_LIST;
  $FieldParam[252]="";
  $FieldParam[253]="";
  $FieldParam[254]="";
  $FieldParam[255]="";
  $FieldParam[256]="";
  $FieldParam[257]="";
  $FieldParam[258]="";
  $FieldParam[259]="";
  $FieldParam[260]=COUNTRY_LIST;
  $FieldParam[261]="";
  $FieldParam[262]="";
  $FieldParam[263]="";
  $FieldParam[264]="";
  $FieldParam[265]="";
  $FieldParam[266]="";
  $FieldParam[267]="";
  $FieldParam[268]="";
  $FieldParam[269]="";
  $FieldParam[270]=COUNTRY_LIST;
  $FieldParam[271]="";
  $FieldParam[272]="";
  $FieldParam[273]="";
  $FieldParam[274]="";
  $FieldParam[275]="";
  $FieldParam[276]="";
  $FieldParam[277]="";
  $FieldParam[278]="";
  $FieldParam[279]="";
  $FieldParam[280]="";
  $FieldParam[281]="";
  $FieldParam[282]="";
  $FieldParam[283]="";
  $FieldParam[284]="";
  $FieldParam[285]="";
  $FieldParam[286]="";
  $FieldParam[287]="";

//全フィールド数
	$FieldMax=287;

//キーフィールドの設定
	$FieldKey=0;

//リスト行数
	$PageSize=10;

//ASPファイル名
	$aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
	$filepath1="/a_m1/data/";

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
  $FieldValue[95]="ENABLE:非公開";
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
  $FieldValue[116]="";
  $FieldValue[117]="";
  $FieldValue[118]="";
  $FieldValue[119]="";
  $FieldValue[120]="";
  $FieldValue[121]="";
  $FieldValue[122]="";
  $FieldValue[123]="";
  $FieldValue[124]="";
  $FieldValue[125]="";
  $FieldValue[126]="";
  $FieldValue[127]="";
  $FieldValue[128]="";
  $FieldValue[129]="";
  $FieldValue[130]="";
  $FieldValue[131]="";
  $FieldValue[132]="";
  $FieldValue[133]="";
  $FieldValue[134]="";
  $FieldValue[135]="";
  $FieldValue[136]="";
  $FieldValue[137]="";
  $FieldValue[138]="";
  $FieldValue[139]="";
  $FieldValue[140]="";
  $FieldValue[141]="";
  $FieldValue[142]="";
  $FieldValue[143]="";
  $FieldValue[144]="";
  $FieldValue[145]="";
  $FieldValue[146]="";
  $FieldValue[147]="";
  $FieldValue[148]="";
  $FieldValue[149]="";
  $FieldValue[150]="";
  $FieldValue[151]="";
  $FieldValue[152]="";
  $FieldValue[153]="";
  $FieldValue[154]="";
  $FieldValue[155]="";
  $FieldValue[156]="";
  $FieldValue[157]="";
  
  


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
    $str=$str." AND (";
    for ($i=0; $i<=$FieldMax; $i=$i+1) {
      if($i!=0){
        $str=$str." OR ";
      }
      $str=$str." ".$FieldName[$i]." like '%".$word."%' ";
    }
    $str=$str." ) ";
		
	} 

	switch($sort){
		case "1":
			//会員ID（昇順）
			$str=$str."ORDER BY MID asc";
			break;
		case "2":
			//会員ID（降順）
			$str=$str."ORDER BY MID desc";
			break;
		case "3":
			//会社名（昇順）
			$str=$str."ORDER BY M1_DVAL01 asc";
			break;
		case "4":
			//会社名（降順）
			$str=$str."ORDER BY M1_DVAL01 desc";
			break;
		case "5":
			//部署名（昇順）
			$str=$str."ORDER BY M1_DVAL02 asc";
			break;
		case "6":
			//部署名（降順）
			$str=$str."ORDER BY M1_DVAL02 desc";
			break;
		case "7":
			//登録状態（昇順）
			$str=$str."ORDER BY M1_DRDO01 asc";
			break;
		case "8":
			//登録状態（降順）
			$str=$str."ORDER BY M1_DRDO01 desc";
			break;
		case "9":
			//公開フラグ（昇順）
			$str=$str."ORDER BY ENABLE asc";
			break;
		case "10":
			//公開フラグ（降順）
			$str=$str."ORDER BY ENABLE desc";
			break;
		case "11":
			//更新日時（昇順）
			$str=$str."ORDER BY EDITDATE asc";
			break;
		case "12":
			//更新日時（降順）
			$str=$str."ORDER BY EDITDATE desc";
			break;
		default:
			$str=$str."ORDER BY ID desc";
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

	$str="DAT_M1.ID>0";

	if ($word!=""){
		if(strstr($word, "MID:")==true){
			$str.=" AND (DAT_M1.MID='".str_replace("MID:", "", $word)."')";
		} else {
			//$tmp1=explode("::","DAT_M1.M1_DVAL01::DAT_O1.O1_DVAL01::DAT_M1.M1_DTXT01::DAT_O1.O1_DTXT01");
			$tmp2=explode("\t",str_replace(" ", "\t", str_replace("　", " ", $word))."\t");
			//$tmp3="";
			for ($j=0; $j<count($tmp2); $j++) {

        // カテゴリー
        // ※検索条件の仕様不明
        /*
        if(strpos($tmp2[$j], 'O1_MSEL01:') !== false) {
				  $str.=" AND DAT_O1.O1_MSEL01 = '".$tmp2[$j]."' ";
        }
        // 国、GLP、〇〇については仕様不明
        else if(strpos($tmp2[$j], 'O1_???:') !== false) {
				  $str.=" AND DAT_O1.O1_??? = '".$tmp2[$j]."' ";
        }
        */

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
    //$str=$str." AND ( ";
    $str=$str." AND (1 = 2 ";
    
    /*
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
    */
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

    $str=$str." OR DAT_M1.M1_ETC09 like '%".$word2."%'"; //検索ワード

    //Short Description
    $str=$str." OR DAT_M1.M1_DTXT03 like '%".$word2."%'";
    //Keywords (comma separated)
    $str=$str." OR DAT_M1.M1_DTXT04 like '%".$word2."%'";
    //Service Website
    $str=$str." OR DAT_M1.M1_ETC08 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC27 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC28 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC29 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC30 like '%".$word2."%'";
    //Service introduction
    $str=$str." OR DAT_M1.M1_ETC91 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC92 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC93 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC94 like '%".$word2."%'";
    $str=$str." OR DAT_M1.M1_ETC95 like '%".$word2."%'";

    
    $str=$str." ) ";
  }

  // 新カテゴリー
  if(isset($_GET['M1_ETC17']) && $_GET['M1_ETC17'] != '') {
    $str.=" AND (DAT_M1.M1_ETC17 = '".$_GET['M1_ETC17']."'";
    $str.=" OR DAT_M1.M1_DRDO09 = '".$_GET['M1_ETC17']."' ";
    $str.=" OR DAT_M1.M1_DCHK04 = '".$_GET['M1_ETC17']."' ";
    $str.=" OR DAT_M1.M1_DCHK08 = '".$_GET['M1_ETC17']."') ";
  }
  if(isset($_GET['M1_ETC18']) && $_GET['M1_ETC18'] != '') {
    $str.=" AND (DAT_M1.M1_ETC18 = '".$_GET['M1_ETC18']."' ";
    $str.=" OR DAT_M1.M1_DRDO10 = '".$_GET['M1_ETC18']."' ";
    $str.=" OR DAT_M1.M1_DCHK05 = '".$_GET['M1_ETC18']."' ";
    $str.=" OR DAT_M1.M1_DCHK09 = '".$_GET['M1_ETC18']."') ";
  }
  if(isset($_GET['M1_ETC19']) && $_GET['M1_ETC19'] != '') {
    $str.=" AND (DAT_M1.M1_ETC19 = '".$_GET['M1_ETC19']."' ";
    $str.=" OR DAT_M1.M1_DCHK02 = '".$_GET['M1_ETC19']."' ";
    $str.=" OR DAT_M1.M1_DCHK06 = '".$_GET['M1_ETC19']."' ";
    $str.=" OR DAT_M1.M1_DCHK10 = '".$_GET['M1_ETC19']."') ";
  }
  if(isset($_GET['M1_ETC20']) && $_GET['M1_ETC20'] != '') {
    $str.=" AND (DAT_M1.M1_ETC20 = '".$_GET['M1_ETC20']."' ";
    $str.=" OR DAT_M1.M1_DCHK03 = '".$_GET['M1_ETC20']."' ";
    $str.=" OR DAT_M1.M1_DCHK07 = '".$_GET['M1_ETC20']."' ";
    $str.=" OR DAT_M1.M1_ETC13 = '".$_GET['M1_ETC20']."') ";
  }

  //if(isset($_GET['M1_ETC17']) && $_GET['M1_ETC17'] != '') {
	//  $str.=" AND DAT_M1.M1_ETC17 = '".$_GET['M1_ETC17']."' ";
  //}
  //if(isset($_GET['M1_ETC18']) && $_GET['M1_ETC18'] != '') {
	//  $str.=" AND DAT_M1.M1_ETC18 = '".$_GET['M1_ETC18']."' ";
  //}
  //if(isset($_GET['M1_ETC19']) && $_GET['M1_ETC19'] != '') {
	//  $str.=" AND DAT_M1.M1_ETC19 = '".$_GET['M1_ETC19']."' ";
  //}
  //if(isset($_GET['M1_ETC20']) && $_GET['M1_ETC20'] != '') {
	//  $str.=" AND DAT_M1.M1_ETC20 = '".$_GET['M1_ETC20']."' ";
  //}


  // 検索条件の仕様不明
  /*
	if ($sel1!=""){
		$str=$str." AND DAT_O1.O1_MSEL01 like '%".$sel1."%'";
	} 

	if ($sel2!=""){
		$str=$str." AND DAT_O1.O1_MSEL02 like '%".$sel2."%'";
	} 

	if ($sort=="3"){
		$str=$str." AND cast(DAT_MATCH.POINT as SIGNED)>=50";
	} 
  */

	// $str=$str." group by DAT_O1.ID ";

	switch ($sort){
	case "1":
    // 更新日順
		$str=$str." ORDER BY DAT_M1.EDITDATE desc";
		break;
	case "2":
    // 企業名順
		$str=$str." ORDER BY DAT_M1.M1_DVAL01 asc";
		break;
	default:
    // 更新日順
		$str=$str." ORDER BY DAT_M1.EDITDATE desc";
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
