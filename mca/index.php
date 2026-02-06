<?php

ini_set('display_errors', 0);

Main();//メイン処理

//=========================================================================================================
//名前 
//機能\ 
//引数 
//戻値 
//=========================================================================================================
function Main()
{

//  eval(globals());

  $FilePass="../";

  $mode=str_replace("\'", "'", $_POST['mode']);

  print "<head>";
  print "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />";
  print "</head>";
  print "<body>";

  switch ($mode){
    case "read":
      $str=str_replace("\'", "'", $_POST['sqlfile']);
      $strline=explode("\r\n",$str);

      $str=str_replace("\'", "'", $_POST['jfile']);
      $strj=explode("\r\n",$str);

      $log=str_replace("\'", "'", $_POST['log']);
      $lf=explode("\n",$log);

      if ($log<>""){
        $l1=explode("\t",$lf[10]);
        $l2=explode("\t",$lf[11]);
        $l3=explode("\t",$lf[12]);
        $l4=explode("\t",$lf[13]);
        $l5=explode("\t",$lf[14]);
        $l6=explode("\t",$lf[15]);
        $l7=explode("\t",$lf[16]);
        $l8=explode("\t",$lf[17]);
        $l9=explode("\t",$lf[18]);
        $l10=explode("\t",$lf[19]);
      }

      $sTBName=$strline[0];
      $sTBName=str_replace(" ","",$sTBName);
      $sTBName=str_replace("\t","",$sTBName);
      $sTBName=str_replace("CREATETABLE","",$sTBName);

      $cnt=0;
      for ($i=1; $i<=count($strline); $i=$i+1){
        if ($strline[$i]==")"){
          break;
        }
        if(strpos($strline[$i],"varchar")>0){
          $sFAtt[$cnt]="varchar";
        }
        if(strpos($strline[$i],"text")>0){
          $sFAtt[$cnt]="text";
        }
        if(strpos($strline[$i],"int")>0){
          $sFAtt[$cnt]="int";
        }

        $sFname[$cnt]=$strline[$i];
        $sFname[$cnt]=str_replace(" ","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace(",","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace("`","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace("(","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace(")","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace("\t","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace("varchar255","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace("text","",$sFname[$cnt]);
        $sFname[$cnt]=str_replace("intauto_incrementprimarykey","",$sFname[$cnt]);

        $cnt=$cnt+1;
      }

      print "<form method='post' action='./' enctype='multipart/form-data'>";
      print "<input type='hidden' name='mode' value='write'>";
      print "<input type='hidden' name='fcnt' value='".$cnt."'>";
      print "<input type='hidden' name='table' value='".$sTBName."'>";
      for ($i=0; $i<=$cnt-1; $i=$i+1){
        print "<input type='hidden' name='fname[]' value='".$sFname[$i]."'>";
      }

      for ($i=0; $i<=$cnt-1; $i=$i+1){
        print "<input type='hidden' name='fatt[]' value='".$sFAtt[$i]."'>";
      }

      print "設定を入力してください。<br><br>";
      print "<table border='1'>";
      if($log==""){
        if ($_SESSION['db1']==""){
          print "<tr><td>DBサーバ</td><td><input type='text' name='db1' size='90' value=\"'サーバ','ユーザ','パスワード'\"></td></tr>";
        } else {
          print "<tr><td>DBサーバ</td><td><input type='text' name='db1' size='90' value=\"".$_SESSION['db1']."\"></td></tr>";
        }
        if ($_SESSION['db2']==""){
          print "<tr><td>データベース</td><td><input type='text' name='db2' size='90' value=\"'データベース'\"></td></tr>";
        } else {
          print "<tr><td>データベース</td><td><input type='text' name='db2' size='90' value=\"".$_SESSION['db2']."\"></td></tr>";
        }

        print "<tr><td>テーブル名(項目数)</td><td>".$sTBName."(".$cnt.")</td></tr>";
        print "<tr><td>フォルダ名</td><td><input type='text' name='filepath' size='20'>「/」や「\\」は不可</td></tr>";
        if ($_SESSION['filepath2']==""){
          print "<tr><td>BASEファイルWEB絶対パス</td><td><input type='text' name='filepath2' size='20' value='../base_a.php'> 「/」から入れる<br>";
        } else {
          print "<tr><td>BASEファイルWEB絶対パス</td><td><input type='text' name='filepath2' size='20' value='".$_SESSION['filepath2']."'> 「/」から入れる<br>";
        }

        if ($_SESSION['baset']==""){
          print "<tr><td>ベーステンプレート<br>（公開画面のみ）</td><td><textarea name='baset' rows='5' cols='80'></textarea></td></tr>";
        } else {
          print "<tr><td>ベーステンプレート<br>（公開画面のみ）</td><td><textarea name='baset' rows='5' cols='80'>".$_SESSION['baset']."</textarea></td></tr>";
        }


        print "<tr><td>BASEファイル認証</td><td><input type='radio' name='nmode' value='0'>認証なし <input type='radio' name='nmode' value='1'>認証あり ※なしの場合「in.html」が呼ばれる<br>";
        print "<tr><td>ページタイトル</td><td><input type='text' name='title' size='50' value=''> ※○○編集などは「○○」のみ</td></tr>";
      } else {
        print "<tr><td>DBサーバ</td><td><input type='text' name='db1' size='90' value=\"".$lf[3]."\"></td></tr>";
        print "<tr><td>データベース</td><td><input type='text' name='db2' size='90' value=\"".$lf[4]."\"></td></tr>";
        print "<tr><td>テーブル名(項目数)</td><td>".$sTBName."(".$cnt.")</td></tr>";
        print "<tr><td>フォルダ名</td><td><input type='text' name='filepath' size='20' value='".$lf[1]."'>「/」や「\\」は不可</td></tr>";
        print "<tr><td>BASEファイルパス</td><td><input type='text' name='filepath2' size='20' value='".$lf[2]."'> 「/」から入れる<br>";

        if ($_SESSION['baset']==""){
          print "<tr><td>ベーステンプレート<br>（公開画面のみ）</td><td><textarea name='baset' rows='5' cols='80'></textarea></td></tr>";
        } else {
          print "<tr><td>ベーステンプレート<br>（公開画面のみ）</td><td><textarea name='baset' rows='5' cols='80'>".$_SESSION['baset']."</textarea></td></tr>";
        }

        if ($lf[5]=="0"){
          print "<tr><td>BASEファイル認証</td><td><input type='radio' name='nmode' value='0' checked>認証なし <input type='radio' name='nmode' value='1'>認証あり ※なしの場合「in.html」が呼ばれる<br>";
        } else {
          print "<tr><td>BASEファイル認証</td><td><input type='radio' name='nmode' value='0'>認証なし <input type='radio' name='nmode' value='1' checked>認証あり ※なしの場合「in.html」が呼ばれる<br>";
        }
        print "<tr><td>ページタイトル</td><td><input type='text' name='title' size='50' value='".$lf[0]."'> ※○○編集などは「○○」のみ</td></tr>";
      }

      print "</table><br>";
      print "■フィールド設定<br>";
      print "<table border='1'>";
      print "<tr><td>フィールド名</td><td>データ型</td><td>隠し</td><td>日本語名</td><td>入力形式</td><td>文字数(横)</td><td>文字数(縦)</td><td>選択肢(::区切)</td><td>初期値</td><td>必須</td><td>文字型</td><td>文字数</td></tr>";
      for ($i=0; $i<=$cnt-1; $i=$i+1){
        if ($log==""){
          print "<tr>";
          print "<td>".$sFname[$i]."</td>";
          print "<td>".$sFAtt[$i]."</td>";
          if(strstr($sFname[$i], "ETC0")==true || strstr($sFname[$i], "ETC1")==true || strstr($sFname[$i], "ETC2")==true || strstr($sFname[$i], "ETC3")==true || strstr($sFname[$i], "ETC4")==true || strstr($sFname[$i], "NEWDATE")==true || strstr($sFname[$i], "EDITDATE")==true || strstr($sFname[$i], "ENABLE")==true || $sFname[$i]=="ID"){
            print "<td><select name='fhidden[]'><option value='0'>--</option><option value='1' selected>HIDDEN</option></select></td>";
          } else {
            print "<td><select name='fhidden[]'><option value='0'>--</option><option value='1'>HIDDEN</option></select></td>";
          }
          if($strj[$i-1]!=""){
            print "<td><input type='text' name='fnname[]' size='20' value='".$strj[$i-1]."'></td>";
          } else {
            print "<td><input type='text' name='fnname[]' size='20' value='".$sFname[$i]."'></td>";
          }
          print "<td><select name='fsel[]'><option value='0'>text</option><option value='1'>select</option><option value='2'>radio</option><option value='3'>checkbox</option><option value='4'>file</option></select></td>";
          if($sFAtt[$i]=="text"){
            print "<td><input type='text' name='fcols[]' size='3' value='70'></td>";
          } else {
            print "<td><input type='text' name='fcols[]' size='3' value='90'></td>";
          }
          print "<td><input type='text' name='frows[]' size='3' value='5'></td>";
          print "<td><input type='text' name='fopt[]' size='20' value='なし'></td>";
          print "<td><input type='text' name='fdefault[]' size='10' value='なし'></td>";
          print "<td><select name='fhissu[]'><option value='0'>--</option><option value='1'>必須</option></select></td>";
          print "<td><select name='ferr[]'><option value='0'>通常</option><option value='1'>数字</option><option value='2'>カナ</option><option value='3'>E-Mail</option></select></td>";
          print "<td><input type='text' name='flen[]' size='3' value=''></td>";
          print "</tr>";
        } else {
          print "<tr>";
          print "<td>".$sFname[$i]."</td>";
          print "<td>".$sFAtt[$i]."</td>";
          if ($l1[$i]=="0"){

            print "<td><select name='fhidden[]'><option value='0' selected>--</option><option value='1'>HIDDEN</option></select></td>";
          } else {
            print "<td><select name='fhidden[]'><option value='0'>--</option><option value='1' selected>HIDDEN</option></select></td>";
          }

          print "<td><input type='text' name='fnname[]' size='20' value='".$l2[$i]."'></td>";
          $tmp="<td><select name='fsel[]'><option value='0' [0]>text</option><option value='1' [1]>select</option><option value='2' [2]>radio</option><option value='3' [3]>checkbox</option><option value='4' [4]>file</option></select></td>";
          $tmp=str_replace("[".$l3[$i]."]","selected",$tmp);
          for ($j=0; $j<=4; $j=$j+1) {
            $tmp=str_replace("[".$j."]","",$tmp);
          }

          print $tmp;
          print "<td><input type='text' name='fcols[]' size='3' value='".$l4[$i]."'></td>";
          print "<td><input type='text' name='frows[]' size='3' value='".$l5[$i]."'></td>";
          print "<td><input type='text' name='fopt[]' size='20' value='".$l6[$i]."'></td>";
          print "<td><input type='text' name='fdefault[]' size='10' value='".$l7[$i]."'></td>";
          if ($l8[$i]=="0"){
            print "<td><select name='fhissu[]'><option value='0' selected>--</option><option value='1'>必須</option></select></td>";
          } else {
            print "<td><select name='fhissu[]'><option value='0'>--</option><option value='1' selected>必須</option></select></td>";
          }

          switch ($l9[$i]){
            case "0":
              print "<td><select name='ferr[]'><option value='0' selected>通常</option><option value='1'>数字</option><option value='2'>カナ</option><option value='3'>E-Mail</option></select></td>";
              break;
            case "1":
              print "<td><select name='ferr[]'><option value='0'>通常</option><option value='1' selected>数字</option><option value='2'>カナ</option><option value='3'>E-Mail</option></select></td>";
              break;
            case "2":
              print "<td><select name='ferr[]'><option value='0'>通常</option><option value='1'>数字</option><option value='2' selected>カナ</option><option value='3'>E-Mail</option></select></td>";
              break;
            case "3":
              print "<td><select name='ferr[]'><option value='0'>通常</option><option value='1'>数字</option><option value='2'>カナ</option><option value='3' selected>E-Mail</option></select></td>";
              break;
          }
          print "<td><input type='text' name='flen[]' size='3' value='".$l10[$i]."'></td>";
          print "</tr>";
        }
      }
      print "</table><br>";
      print "<input type='submit' value='次へ'>";
      print "</form>";
      break;

    case "write":
      $title=str_replace("\'", "'", $_POST['title']);
      $filepath=str_replace("\'", "'", $_POST['filepath']);
      $filepath2=str_replace("\'", "'", $_POST['filepath2']);
      $db1=str_replace("\'", "'", $_POST['db1']);
      $db2=str_replace("\'", "'", $_POST['db2']);
      $baset=str_replace("\'", "'", $_POST['baset']);
      $nmode=str_replace("\'", "'", $_POST['nmode']);
      $fcnt=str_replace("\'", "'", $_POST['fcnt']);
      $table=str_replace("\'", "'", $_POST['table']);
      $fname=str_replace("\'", "'", $_POST['fname']);
      $fatt=str_replace("\'", "'", $_POST['fatt']);
      $fhidden=str_replace("\'", "'", $_POST['fhidden']);
      $fnname=str_replace("\'", "'", $_POST['fnname']);
      $fsel=str_replace("\'", "'", $_POST['fsel']);
      $fcols=str_replace("\'", "'", $_POST['fcols']);
      $frows=str_replace("\'", "'", $_POST['frows']);
      $fopt=str_replace("\'", "'", $_POST['fopt']);
      $fdefault=str_replace("\'", "'", $_POST['fdefault']);
      $fhissu=str_replace("\'", "'", $_POST['fhissu']);
      $ferr=str_replace("\'", "'", $_POST['ferr']);
      $flen=str_replace("\'", "'", $_POST['flen']);

      $_SESSION['db1']=$db1;
      $_SESSION['db2']=$db2;
      $_SESSION['filepath2']=$filepath2;
      $_SESSION['baset']=$baset;

      if($baset==""){
		$baset="[MAIN]";
      } else {
		$baset=str_replace("[PAGE_TITLE]", $title, $baset);
      }

      $log="";
      $log=$log.$title."\r\n";
      $log=$log.$filepath."\r\n";
      $log=$log.$filepath2."\r\n";
      $log=$log.$db1."\r\n";
      $log=$log.$db2."\r\n";
      $log=$log.$nmode."\r\n";
      $log=$log.$fcnt."\r\n";
      $log=$log.$table."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fname[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fatt[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fhidden[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fnname[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fsel[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fcols[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$frows[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fopt[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fdefault[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$fhissu[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$ferr[$i]."\t";
      }
      $log=$log."\r\n";

      for ($i=0; $i<=$fcnt-1; $i=$i+1){
      	$log=$log.$flen[$i]."\t";
      }
      $log=$log."\r\n";

print $mode."!";
      if ($filepath!=""){
        if (file_exists($FilePass.$filepath)==false){
          mkdir($FilePass.$filepath,0755);
        }
        if (file_exists($FilePass.$filepath."/data")==false){
          mkdir($FilePass.$filepath."/data",0755);
        }

print $FilePass.$filepath."/data";

//-------------------------------------------------------------------------------------------------
//filectrl.inc
//        copy($FilePass."mc\\dataedit\\filectrl.inc");
//-------------------------------------------------------------------------------------------------
//s.gif
        copy("./dataedit/data/s.gif", $FilePass.$filepath."/data/s.gif");
//-------------------------------------------------------------------------------------------------
//index.asp
        $fp="./dataedit/index.php";
	$str=@file_get_contents($fp);

        $str=str_replace("[FILEPATH]",$filepath,$str);
        $str=str_replace("[BASEFILE]",$filepath2,$str);
        $str=str_replace("[NMODE]",$nmode,$str);
        $str=str_replace("[TITLE]",$title,$str);
        $str=str_replace("[DATABASE1]",$db1,$str);
        $str=str_replace("[DATABASE2]",$db2,$str);

        $tmp="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          if ($i!=0){
            $tmp=$tmp."\t";
          }
          $tmp=$tmp.$fnname[$i];
        }
        $str=str_replace("[DATAHEADER]",$tmp,$str);

        $tso=fopen($FilePass.$filepath."/index.php","w");
        fputs($tso,$str);
        fclose($tso);
//-------------------------------------------------------------------------------------------------
//config.asp
        $fp="./dataedit/config.php";
	$str=@file_get_contents($fp);

        $str=str_replace("[TABLENAME]",$table,$str);
        $str=str_replace("[FILEPATH]",$filepath,$str);
        $str=str_replace("[FIELDCOUNT]",$fcnt-1,$str);
        $tmp="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          $tmp=$tmp."  "."\$FieldName[".$i."]=\"".$fname[$i]."\";"."\r\n";
        }
        $str=str_replace("[FIELDNAME]",$tmp,$str);

        $tmp="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          $tmp=$tmp."  "."\$FieldAtt[".$i."]=\"".$fsel[$i]."\";"."\r\n";
        }
        $str=str_replace("[FIELDATT]",$tmp,$str);

        $tmp="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          if ($fopt[$i]!="なし"){
            $tmp=$tmp."  "."\$FieldParam[".$i."]=\"".$fopt[$i]."\";"."\r\n";
          } else {
            $tmp=$tmp."  "."\$FieldParam[".$i."]=\"\";"."\r\n";
          }
        }
        $str=str_replace("[FIELDPARAM]",$tmp,$str);

        $tmp="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          if ($fdefault[$i]!="なし"){
            switch ($fsel[$i]){
              case "0":
                $tmp=$tmp."  "."\$FieldValue[".$i."]=\"".$fdefault[$i]."\";"."\r\n";
                break;
              case "1":
                $tmp=$tmp."  "."\$FieldValue[".$i."]=\"".$fname[$i].":".$fdefault[$i]."\";"."\r\n";
                break;
              case "2":
                $tmp=$tmp."  "."\$FieldValue[".$i."]=\"".$fname[$i].":".$fdefault[$i]."\";"."\r\n";
                break;
              case "3":
                $tmp=$tmp."  "."\$FieldValue[".$i."]=\"".$fname[$i].":".$fdefault[$i]."\";"."\r\n";
                break;
              case "4":
                $tmp=$tmp."  "."\$FieldValue[".$i."]=\"".$fdefault[$i]."\";"."\r\n";
                break;
            }
          } else {
            $tmp=$tmp."  "."\$FieldValue[".$i."]=\"\";"."\r\n";
          }
        }
        $str=str_replace("[FIELDVALUE]",$tmp,$str);

        $tmp="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          if ($fhissu[$i]=="1"){
            $tmp=$tmp."  "."if(\$FieldValue[".$i."]==\"\"){"."\r\n";
            $tmp=$tmp."  "."  "."\$function_ret = \$function_ret.\"<li><i class=\"icon-check2 mr05 fc_red\"></i>".$fnname[$i]."は必須項目です</li>\";"."\r\n";
            $tmp=$tmp."  "."}"."\r\n";
          }

          if ($ferr[$i]=="1"){
            $tmp=$tmp."  "."if(CheckNumber(\$FieldValue[".$i."])==False){"."\r\n";
            $tmp=$tmp."  "."  "."\$function_ret = \$function_ret.\"<li><i class=\"icon-check2 mr05 fc_red\"></i>".$fnname[$i]."は数字で入力して下さい</li>\";"."\r\n";
            $tmp=$tmp."  "."}"."\r\n";
          }

          if ($ferr[$i]=="2"){
            $tmp=$tmp."  "."if(CheckKana(\$FieldValue[".$i."])==False){"."\r\n";
            $tmp=$tmp."  "."  "."\$function_ret = \$function_ret.\"<li><i class=\"icon-check2 mr05 fc_red\"></i>".$fnname[$i]."はカナで入力して下さい</li>\";"."\r\n";
            $tmp=$tmp."  "."}"."\r\n";
          }

          if ($ferr[$i]=="3"){
            $tmp=$tmp."  "."if(CheckEmail(\$FieldValue[".$i."])==False){"."\r\n";
            $tmp=$tmp."  "."  "."\$function_ret = \$function_ret.\"<li><i class=\"icon-check2 mr05 fc_red\"></i>".$fnname[$i]."がEmailアドレスではありません</li>\";"."\r\n";
            $tmp=$tmp."  "."}"."\r\n";
          }

          if ($flen[$i]!=""){
            $tmp=$tmp."  "."if(strlen(\$FieldValue[".$i."])>=".$flen[$i]."){"."\r\n";
            $tmp=$tmp."  "."  "."\$function_ret = \$function_ret.\"<li><i class=\"icon-check2 mr05 fc_red\"></i>".$fnname[$i]."は".$flen[$i]."文字以内で入力して下さい</li>\";"."\r\n";
            $tmp=$tmp."  "."}"."\r\n";
          }

        }
        $str=str_replace("[ERRORCHECK]",$tmp,$str);

        $tso=fopen($FilePass.$filepath."/config.php","w");
        fputs($tso,$str);
        fclose($tso);
//-------------------------------------------------------------------------------------------------
//edit.html
        $fp="./dataedit/edit.html";
	$str=@file_get_contents($fp);

        $str=str_replace("[TITLE]",$title,$str);
        $str=str_replace("[FILEPATH]",$filepath,$str);

        $tmp="";
        $tmph="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          if ($fhidden[$i]=="1"){
            $tmph=$tmph."<input type=\"hidden\" name=\"".$fname[$i]."\" value=\"[".$fname[$i]."]\">"."\r\n";
          } else {
            if ($fhissu[$i]=="1"){
              $tmph2="<img class=\"em\" src=\"/html_a/img/em.jpg\" width=\"31\" height=\"16\" alt=\"必須\" />";
            } else {
              $tmph2="";
            }

            switch ($fsel[$i]){
              case "0":
                if ($fatt[$i]!="text"){
                  $tmp=$tmp."<tr><th>".$fnname[$i].$tmph2."</th><td><input type=\"text\" name=\"".$fname[$i]."\" class=\"input_w10 form-control\" value=\"[".$fname[$i]."]\" size=\"".$fcols[$i]."\"></td></tr>"."\r\n";
                } else {
                  $tmp=$tmp."<tr><th>".$fnname[$i].$tmph2."</th><td><textarea name=\"".$fname[$i]."\" class=\"textarea_w10 form-control\" cols=\"".$fcols[$i]."\" rows=\"".$frows[$i]."\">[".$fname[$i]."]</textarea></td></tr>"."\r\n";
                }
                break;
              case "1":
                $tmp=$tmp."<tr><th>".$fnname[$i].$tmph2."</th><td><select name=\"".$fname[$i]."\" class=\"input_select form-control\">[OPT-".$fname[$i]."]</select></td></tr>"."\r\n";
                break;
              case "2":
                $tmp=$tmp."<tr><th>".$fnname[$i].$tmph2."</th><td><ul>[OPT-".$fname[$i]."]</ul></td></tr>"."\r\n";
                break;
              case "3":
                $tmp=$tmp."<tr><th>".$fnname[$i].$tmph2."</th><td><ul>[OPT-".$fname[$i]."]</ul></td></tr>"."\r\n";
                break;
              case "4":
                $tmp=$tmp."[S-NEWDATA]<tr><th>".$fnname[$i].$tmph2."</th><td><input type=\"file\" name=\"EP_".$fname[$i]."\" value=\"[".$fname[$i]."]\" size=\"".$fcols[$i]."\"><input type=\"hidden\" name=\"".$fname[$i]."\" value=\"[".$fname[$i]."]\"></td></tr>[E-NEWDATA]"."\r\n";
                $tmp=$tmp."[S-EDITDATA]<tr><th>".$fnname[$i].$tmph2."</th><td><img src=\"[".$fname[$i]."]\" width=\"200\"> 削除 <input type=\"checkbox\" name=\"DEL_IMAGE_".$fname[$i]."\"><br><input type=\"file\" name=\"EP_".$fname[$i]."\" value=\"[".$fname[$i]."]\" size=\"".$fcols[$i]."\"><input type=\"hidden\" name=\"".$fname[$i]."\" value=\"[".$fname[$i]."]\"></td></tr>[E-EDITDATA]"."\r\n";
                break;
            }
          }
        }
        $str=str_replace("[INPUT-NORMAL]",$tmp,$str);
        $str=str_replace("[INPUT-HIDDEN]",$tmph,$str);

        $str=str_replace("[MAIN]",$str,$baset);

        $tso=fopen($FilePass.$filepath."/edit.html","w");
        fputs($tso,$str);
        fclose($tso);

//-------------------------------------------------------------------------------------------------
//list.html
        $fp="./dataedit/list.html";
	$str=@file_get_contents($fp);

        $str=str_replace("[TITLE]",$title,$str);

        $tmp="";
        $tmph="";
        for ($i=0; $i<=$fcnt-1; $i=$i+1){
          if ($fhidden[$i]!="1"){
            $tmph=$tmph."  <th>".$fnname[$i]."</th>"."\r\n";
            switch ($fsel[$i]){
              case "4":
                $tmp=$tmp."  <td><img src=\"[".$fname[$i]."]\" width=\"80\"></td>"."\r\n";
                break;
              default:
                $tmp=$tmp."  <td>[D-".$fname[$i]."]</td>"."\r\n";
                break;
            }
          }
        }

        $str=str_replace("[DISP-HEAD]",$tmph,$str);
        $str=str_replace("[DISP-NORMAL]",$tmp,$str);
        $str=str_replace("[DISP-KEY]",$fname[0],$str);

        $str=str_replace("[MAIN]",$str,$baset);

        $tso=fopen($FilePass.$filepath."/list.html","w");
        fputs($tso,$str);
        fclose($tso);
//-------------------------------------------------------------------------------------------------
        print $title."の生成が完了しました。<br><br>";
        print "■設定ファイル<br>";
        print "<textarea rows='15' cols='80'>".$log."</textarea><br><br>";
      }
      break;
    default:
      print "設定をセットしてください。　【注意】先頭カラムは必ず「ID」（主キー）であること<br><br>";
      print "<form method='post' action='./'>";
      print "<input type='hidden' name='mode' value='read'>";
      print "■CREATE SQL文<br>";
      print "<textarea name='sqlfile' rows='15' cols='80'></textarea><br><br>";
      print "■日本語見出し<br>";
      print "<textarea name='jfile' rows='15' cols='80'></textarea><br><br>";
      print "■設定ファイル（２回目以降）<br>";
      print "<textarea name='log' rows='15' cols='80'></textarea><br><br>";
      print "<input type='submit' value='次へ'>";
      print "</form>";
      break;
  }

  print "</body>";

  return $function_ret;
}
?>
