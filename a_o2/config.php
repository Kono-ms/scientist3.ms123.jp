<?php

//テーブル名の指定
  $TableName="DAT_O2";

//フィールド名の指定（0番目はオートナンバー型）
  $FieldName[0]="ID";
  $FieldName[1]="OID";
  $FieldName[2]="MID";
  $FieldName[3]="O2_DVAL01";
  $FieldName[4]="O2_DVAL02";
  $FieldName[5]="O2_DVAL03";
  $FieldName[6]="O2_DVAL04";
  $FieldName[7]="O2_DVAL05";
  $FieldName[8]="O2_DVAL06";
  $FieldName[9]="O2_DVAL07";
  $FieldName[10]="O2_DVAL08";
  $FieldName[11]="O2_DVAL09";
  $FieldName[12]="O2_DVAL10";
  $FieldName[13]="O2_DTXT01";
  $FieldName[14]="O2_DTXT02";
  $FieldName[15]="O2_DTXT03";
  $FieldName[16]="O2_DTXT04";
  $FieldName[17]="O2_DTXT05";
  $FieldName[18]="O2_DTXT06";
  $FieldName[19]="O2_DTXT07";
  $FieldName[20]="O2_DTXT08";
  $FieldName[21]="O2_DTXT09";
  $FieldName[22]="O2_DTXT10";
  $FieldName[23]="O2_DSEL01";
  $FieldName[24]="O2_DSEL02";
  $FieldName[25]="O2_DSEL03";
  $FieldName[26]="O2_DSEL04";
  $FieldName[27]="O2_DSEL05";
  $FieldName[28]="O2_DSEL06";
  $FieldName[29]="O2_DSEL07";
  $FieldName[30]="O2_DSEL08";
  $FieldName[31]="O2_DSEL09";
  $FieldName[32]="O2_DSEL10";
  $FieldName[33]="O2_DRDO01";
  $FieldName[34]="O2_DRDO02";
  $FieldName[35]="O2_DRDO03";
  $FieldName[36]="O2_DRDO04";
  $FieldName[37]="O2_DRDO05";
  $FieldName[38]="O2_DRDO06";
  $FieldName[39]="O2_DRDO07";
  $FieldName[40]="O2_DRDO08";
  $FieldName[41]="O2_DRDO09";
  $FieldName[42]="O2_DRDO10";
  $FieldName[43]="O2_DCHK01";
  $FieldName[44]="O2_DCHK02";
  $FieldName[45]="O2_DCHK03";
  $FieldName[46]="O2_DCHK04";
  $FieldName[47]="O2_DCHK05";
  $FieldName[48]="O2_DCHK06";
  $FieldName[49]="O2_DCHK07";
  $FieldName[50]="O2_DCHK08";
  $FieldName[51]="O2_DCHK09";
  $FieldName[52]="O2_DCHK10";
  $FieldName[53]="O2_DFIL01";
  $FieldName[54]="O2_DFIL02";
  $FieldName[55]="O2_DFIL03";
  $FieldName[56]="O2_DFIL04";
  $FieldName[57]="O2_DFIL05";
  $FieldName[58]="O2_DFIL06";
  $FieldName[59]="O2_DFIL07";
  $FieldName[60]="O2_DFIL08";
  $FieldName[61]="O2_DFIL09";
  $FieldName[62]="O2_DFIL10";
  $FieldName[63]="O2_MSEL01";
  $FieldName[64]="O2_MSEL02";
  $FieldName[65]="O2_MSEL03";
  $FieldName[66]="O2_MSEL04";
  $FieldName[67]="O2_MSEL05";
  $FieldName[68]="O2_MSEL06";
  $FieldName[69]="O2_MSEL07";
  $FieldName[70]="O2_MSEL08";
  $FieldName[71]="O2_MSEL09";
  $FieldName[72]="O2_MSEL10";
  $FieldName[73]="O2_MRDO01";
  $FieldName[74]="O2_MRDO02";
  $FieldName[75]="O2_MRDO03";
  $FieldName[76]="O2_MRDO04";
  $FieldName[77]="O2_MRDO05";
  $FieldName[78]="O2_MRDO06";
  $FieldName[79]="O2_MRDO07";
  $FieldName[80]="O2_MRDO08";
  $FieldName[81]="O2_MRDO09";
  $FieldName[82]="O2_MRDO10";
  $FieldName[83]="O2_MCHK01";
  $FieldName[84]="O2_MCHK02";
  $FieldName[85]="O2_MCHK03";
  $FieldName[86]="O2_MCHK04";
  $FieldName[87]="O2_MCHK05";
  $FieldName[88]="O2_MCHK06";
  $FieldName[89]="O2_MCHK07";
  $FieldName[90]="O2_MCHK08";
  $FieldName[91]="O2_MCHK09";
  $FieldName[92]="O2_MCHK10";
  $FieldName[93]="ENABLE";
  $FieldName[94]="NEWDATE";
  $FieldName[95]="EDITDATE";
  $FieldName[96]="O2_ETC01";
  $FieldName[97]="O2_ETC02";
  $FieldName[98]="O2_ETC03";
  $FieldName[99]="O2_ETC04";
  $FieldName[100]="O2_ETC05";
  $FieldName[101]="O2_ETC06";
  $FieldName[102]="O2_ETC07";
  $FieldName[103]="O2_ETC08";
  $FieldName[104]="O2_ETC09";
  $FieldName[105]="O2_ETC10";
  $FieldName[106]="O2_ETC11";
  $FieldName[107]="O2_ETC12";
  $FieldName[108]="O2_ETC13";
  $FieldName[109]="O2_ETC14";
  $FieldName[110]="O2_ETC15";
  $FieldName[111]="O2_ETC16";
  $FieldName[112]="O2_ETC17";
  $FieldName[113]="O2_ETC18";
  $FieldName[114]="O2_ETC19";
  $FieldName[115]="O2_ETC20";
  $FieldName[116]="O2_DVAL11";
  $FieldName[117]="O2_DVAL12";
  $FieldName[118]="O2_DVAL13";
  $FieldName[119]="O2_DVAL14";
  $FieldName[120]="O2_DVAL15";
  $FieldName[121]="O2_DVAL16";
  $FieldName[122]="O2_DVAL17";
  $FieldName[123]="O2_DVAL18";
  $FieldName[124]="O2_DVAL19";
  $FieldName[125]="O2_DVAL20";
  $FieldName[126]="O2_DVAL21";
  $FieldName[127]="O2_DVAL22";
  $FieldName[128]="O2_DVAL23";
  $FieldName[129]="O2_DVAL24";
  $FieldName[130]="O2_DVAL25";
  $FieldName[131]="O2_DVAL26";
  $FieldName[132]="O2_DVAL27";
  $FieldName[133]="O2_DVAL28";
  $FieldName[134]="O2_DVAL29";
  $FieldName[135]="O2_DVAL30";
  $FieldName[136]="O2_DTXT11";
  $FieldName[137]="O2_DTXT12";
  $FieldName[138]="O2_DTXT13";
  $FieldName[139]="O2_DTXT14";
  $FieldName[140]="O2_DTXT15";
  $FieldName[141]="O2_DTXT16";
  $FieldName[142]="O2_DTXT17";
  $FieldName[143]="O2_DTXT18";
  $FieldName[144]="O2_DTXT19";
  $FieldName[145]="O2_DTXT20";
  $FieldName[146]="O2_DTXT21";
  $FieldName[147]="O2_DTXT22";
  $FieldName[148]="O2_DTXT23";
  $FieldName[149]="O2_DTXT24";
  $FieldName[150]="O2_DTXT25";
  $FieldName[151]="O2_DTXT26";
  $FieldName[152]="O2_DTXT27";
  $FieldName[153]="O2_DTXT28";
  $FieldName[154]="O2_DTXT29";
  $FieldName[155]="O2_DTXT30";
  

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
  $FieldAtt[116]="0";
  $FieldAtt[117]="0";
  $FieldAtt[118]="0";
  $FieldAtt[119]="0";
  $FieldAtt[120]="0";
  $FieldAtt[121]="0";
  $FieldAtt[122]="0";
  $FieldAtt[123]="0";
  $FieldAtt[124]="0";
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
  $FieldParam[23]="O2_DSEL01-SEL";
  $FieldParam[24]="O2_DSEL02-SEL";
  $FieldParam[25]="O2_DSEL03-SEL";
  $FieldParam[26]="O2_DSEL04-SEL";
  $FieldParam[27]="O2_DSEL05-SEL";
  $FieldParam[28]="O2_DSEL06-SEL";
  $FieldParam[29]="O2_DSEL07-SEL";
  $FieldParam[30]="O2_DSEL08-SEL";
  $FieldParam[31]="O2_DSEL09-SEL";
  $FieldParam[32]="O2_DSEL10-SEL";
  $FieldParam[33]="O2_DRDO01-SEL";
  $FieldParam[34]="O2_DRDO02-SEL";
  $FieldParam[35]="O2_DRDO03-SEL";
  $FieldParam[36]="O2_DRDO04-SEL";
  $FieldParam[37]="O2_DRDO05-SEL";
  $FieldParam[38]="O2_DRDO06-SEL";
  $FieldParam[39]="O2_DRDO07-SEL";
  $FieldParam[40]="O2_DRDO08-SEL";
  $FieldParam[41]="O2_DRDO09-SEL";
  $FieldParam[42]="O2_DRDO10-SEL";
  $FieldParam[43]="O2_DCHK01-SEL";
  $FieldParam[44]="O2_DCHK02-SEL";
  $FieldParam[45]="O2_DCHK03-SEL";
  $FieldParam[46]="O2_DCHK04-SEL";
  $FieldParam[47]="O2_DCHK05-SEL";
  $FieldParam[48]="O2_DCHK06-SEL";
  $FieldParam[49]="O2_DCHK07-SEL";
  $FieldParam[50]="O2_DCHK08-SEL";
  $FieldParam[51]="O2_DCHK09-SEL";
  $FieldParam[52]="O2_DCHK10-SEL";
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
  $FieldParam[63]="カテゴリー１::カテゴリー２::カテゴリー３";
  $FieldParam[64]="O2_MSEL02-SEL";
  $FieldParam[65]="O2_MSEL03-SEL";
  $FieldParam[66]="O2_MSEL04-SEL";
  $FieldParam[67]="O2_MSEL05-SEL";
  $FieldParam[68]="O2_MSEL06-SEL";
  $FieldParam[69]="O2_MSEL07-SEL";
  $FieldParam[70]="O2_MSEL08-SEL";
  $FieldParam[71]="O2_MSEL09-SEL";
  $FieldParam[72]="O2_MSEL10-SEL";
  $FieldParam[73]="O2_MRDO01-SEL";
  $FieldParam[74]="O2_MRDO02-SEL";
  $FieldParam[75]="O2_MRDO03-SEL";
  $FieldParam[76]="O2_MRDO04-SEL";
  $FieldParam[77]="O2_MRDO05-SEL";
  $FieldParam[78]="O2_MRDO06-SEL";
  $FieldParam[79]="O2_MRDO07-SEL";
  $FieldParam[80]="O2_MRDO08-SEL";
  $FieldParam[81]="O2_MRDO09-SEL";
  $FieldParam[82]="O2_MRDO10-SEL";
  $FieldParam[83]="O2_MCHK01-SEL";
  $FieldParam[84]="O2_MCHK02-SEL";
  $FieldParam[85]="O2_MCHK03-SEL";
  $FieldParam[86]="O2_MCHK04-SEL";
  $FieldParam[87]="O2_MCHK05-SEL";
  $FieldParam[88]="O2_MCHK06-SEL";
  $FieldParam[89]="O2_MCHK07-SEL";
  $FieldParam[90]="O2_MCHK08-SEL";
  $FieldParam[91]="O2_MCHK09-SEL";
  $FieldParam[92]="O2_MCHK10-SEL";
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
  $FieldParam[116]="";
  $FieldParam[117]="";
  $FieldParam[118]="";
  $FieldParam[119]="";
  $FieldParam[120]="";
  $FieldParam[121]="";
  $FieldParam[122]="";
  $FieldParam[123]="";
  $FieldParam[124]="";
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
  

//全フィールド数
	$FieldMax=155;

//キーフィールドの設定
	$FieldKey=0;

//リスト行数
	$PageSize=20;

//ASPファイル名
	$aspname="index.php";

//FILE アップロードパス(WEB絶対パス)
	$filepath1="/a_o2/data/";

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
function ListSQLSearch($sort,$word,$sel1,$sel2,$word2)
{
	//extract($GLOBALS);
	eval(globals());

	$str="DAT_O2.ID>0";

	if ($word!=""){
		if(strstr($word, "MID:")==true){
			$str.=" AND (DAT_M2.MID='".str_replace("MID:", "", $word)."')";
		} else {
			$tmp1=explode("::","DAT_M2.M2_DVAL01::DAT_O2.O2_DVAL01::DAT_M2.M2_DTXT01::DAT_O2.O2_DTXT01");
			$tmp2=explode("\t",str_replace(" ", "\t", str_replace("　", " ", $word))."\t");
			$tmp3="";
			for ($j=0; $j<count($tmp2); $j++) {
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
			}
			if($tmp3!=""){
				$str.=" AND (".$tmp3.")";
			} 
		} 
	} 

  if($word2!=""){
    $str=$str." AND (";
    
    $str=$str."    DAT_O2.O2_DVAL01 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DVAL02 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT01 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT02 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT03 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT04 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT05 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT06 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT07 like '%".$word2."%'";
    $str=$str." OR DAT_O2.O2_DTXT08 like '%".$word2."%'";
    $str=$str." OR DAT_M2.EMAIL like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL01 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL02 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL03 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL04 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL05 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL06 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL07 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL08 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL09 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL10 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DVAL11 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DTXT01 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DTXT02 like '%".$word2."%'";
    $str=$str." OR DAT_M2.M2_DSEL01 like '%".$word2."%'";
    
    $str=$str." ) ";
  }


	if ($sel1!=""){
		$str=$str." AND DAT_O2.O2_MSEL01 like '%".$sel1."%'";
	} 

	if ($sel2!=""){
		$str=$str." AND DAT_O2.O2_MSEL02 like '%".$sel2."%'";
	} 

	if ($sort=="3"){
		$str=$str." AND cast(DAT_MATCH.POINT as SIGNED)>=50";
	} 

	$str=$str." group by DAT_O2.ID ";

	switch ($sort){
	case "1":
		$str=$str." ORDER BY cast(DAT_MATCH.POINT as SIGNED) desc";
		break;
	case "2":
		$str=$str." ORDER BY DAT_O2.NEWDATE desc";
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
