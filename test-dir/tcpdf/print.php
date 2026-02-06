<?php

require_once('../../TCPDF/tcpdf.php');

$str = '';
$font_size = 12;

$pdf = new TCPDF('P');// 横向き
//$pdf ->SetFont('notosansjpvariablefont_wght', '', 12);// 英語日本語フォント(NotoSansJP-Thin)
//$pdf ->SetFont('kozgopromedium', '', 12);// KozGoPro-Medium-Acro
$pdf ->SetFont('genshingothic', '', 12);// GenShinGothic-Regular
$pdf ->AddFont('caveatvariablefont_wght');//caveat(全ウェイト、CSS上の名称 Caveat-Regular)
$pdf ->AddFont('kozminpro');//kozminpro(全ウェイト、CSS上の名称 KozMinPro-Regular-Acro, Kozuka Mincho Pro)
$pdf ->AddFont('genshingothic');// GenShinGothic(全ウェイト、CSS上の名称 GenShinGothic-Regular)
$pdf ->SetFont('genshingothic', '', $font_size);// GenShinGothic(bodyにfontプロパティ設定とほぼ同義)


//kozgopromedium.php
//KozGoPro-Medium-Acro
//Kozuka Gothic Pro (Japanese Sans-Serif)

//kozminproregular.php
//KozMinPro-Regular-Acro
//Kozuka Mincho Pro (Japanese Serif)


//(GenShinGothic-Regular)
//'genshingothic'

//(GenShinGothic-Bold)
//'genshingothicb'

//(GenShinGothic-ExtraLight)
//'genshingothicextralight'

//(GenShinGothic-Heavy)
//'genshingothicheavy'

//(GenShinGothic-Light)
//'genshingothiclight'

//(GenShinGothic-Medium)
//'genshingothicmedium'

//(GenShinGothic-Normal)
//'genshingothicnormal'

$str = file_get_contents('print.html');

$pdf ->SetMargins(12,0,12);// ページマージン（left,top,right）
$pdf ->setPrintHeader(false);// ページヘッダー（なし）
$pdf ->setPrintFooter(false);// ページフッター（なし）
$pdf ->setListIndentWidth(4);// リストタグのインデント調整
$pdf ->addPage();

// 日本語の場合禁則処理を挟む
if($_GET['param'] == 'Agreements2j') {
	$str = getKinsokuHTML($str);
}

$pdf ->writeHTML($str);

ob_end_clean();
$pdf->Output(date('Ymd')."_".$_SESSION['MID'].".pdf", "I");
//$pdf->Output(date('Ymd')."_".$_SESSION['MID'].".pdf", "D");

?>
