<?php

require_once('../TCPDF/tcpdf.php');

$str = '';
$font_size = 12;

$pdf = new TCPDF('P');// 横向き

// 書体の設定
// NOTE: 書体をPDFで利用可能にする設定と、書体を文字に適用する設定
//$pdf ->SetFont('notosansjpvariablefont_wght', '', 12);// 英語日本語フォント(NotoSansJP-Thin)
//$pdf ->SetFont('kozgopromedium', '', 12);// KozGoPro-Medium-Acro
$pdf ->SetFont('genshingothic', '', 12);// GenShinGothic-Regular
$pdf ->AddFont('caveatvariablefont_wght');//caveat(全ウェイト、CSS上の名称 Caveat-Regular)
$pdf ->AddFont('kozminpro');//kozminpro(全ウェイト、CSS上の名称 KozMinPro-Regular-Acro, Kozuka Mincho Pro)
$pdf ->AddFont('genshingothic');// GenShinGothic(全ウェイト、CSS上の名称 GenShinGothic-Regular)
$pdf ->SetFont('genshingothic', '', $font_size);// GenShinGothic(bodyにfontプロパティ設定とほぼ同義)

$pdf ->SetMargins(12,0,12);// ページマージン（left,top,right）
$pdf ->setPrintHeader(false);// ページヘッダー（なし）
$pdf ->setPrintFooter(false);// ページフッター（なし）
$pdf ->setListIndentWidth(4);// リストタグのインデント調整
$pdf ->addPage();


// 背景画像の設定
// 現在の改ページ余白を取得します
$bMargin = $pdf->getBreakMargin();
// 現在の自動改ページモードを取得しておきます（最後に、挿入位置を元に戻すため）
$auto_page_break = $pdf->getAutoPageBreak();
// 自動改ページを無効にする（第2引数は動作しない）
$pdf->SetAutoPageBreak(false, 0);
// 背景画像の大きさの指定
// NOTE: 用紙はA4サイズを想定（単位は1cm）
$pdf_w = 210;
$pdf_h = 297;
// 画像のパス
// NOTE: 透明な余白付きの画像を作る必要がある（photoshopなどで、用紙に合わせた比率と配置位置を画像にあらかじめ描画しておく）
$img_src = $_SERVER['DOCUMENT_ROOT'].'common/images/logo-s3-bg-has-210x297-bounding-box.png';
// 画像の設定
$pdf->Image($img_src, 0, 0, $pdf_w, $pdf_h, '', '', '', false, 300, '', false, false, 0, true);
// 自動改ページ状態に戻します
$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
// ページコンテンツの開始点を設定します
$pdf->setPageMark();

// 文章の挿入
$filename = 'print-en.html';
if(isset($_GET['lang'])){
	if($_GET['lang']=='ja') $filename = 'print.html';
}
$str = file_get_contents($filename);
$pdf ->writeHTML($str);

// new TCPDFで発生した ob_startを解除
ob_end_clean();


// 出力
$pdf->Output(date('Ymd')."_".$_SESSION['MID'].".pdf", "I");
//$pdf->Output(date('Ymd')."_".$_SESSION['MID'].".pdf", "D");

?>
